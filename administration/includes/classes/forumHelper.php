<?php
/**
 * BetaDev Forum Software 2010
 * 
 * This file is part of BetaDev Forum.
 * 
 * DevBoard is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * DevBoard is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with DevBoard.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class fHelper{
	public $forum_list=array();
	public $structure_array=array();
	function __construct(){
	}
	function get_forum_list(){
		$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
		$cat_query = $GLOBALS['super']->db->query($cat_sql);
		$this->forum_list = array();
		if (isset($_GET['fid'])){
			$fid = intval($_GET['fid']);
			$parentID = "SELECT `parent_id` FROM ".TBL_PREFIX."forums WHERE `id`=".$GLOBALS['super']->db->escape($fid);
			$parentID = $GLOBALS['super']->db->query($parentID);
			$parentID = $GLOBALS['super']->db->fetch_assoc($parentID);
			$parentID = $parentID['parent_id'];
		}else{
			$fid = -1;
			$parentID = -2;
		}
		while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query)){
			if ($cat['id'] == $parentID){
				$selected = "true";
			}else{
				$selected = "false";
			}	
			$this->forum_list[] = array('id' => $cat['id'], 'name' => $cat['name'], 'selected' => $selected);
			$this->get_forums($cat['id'], $parentID);
		}
		return $this->forum_list;
	}
	function get_forums($parent_id, $parentID=0){
		static $counter = 0;
		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`='".$parent_id."' ORDER BY `sort_order` ASC";
		$result = $GLOBALS['super']->db->query($query);
		while($row = $GLOBALS['super']->db->fetch_assoc($result)){
			$counter++;	
			$dashes = str_repeat("|--",$counter);
			if ($row['id'] == $parentID){
				$selected = "true";
			}else
				$selected = "false";
			$this->forum_list[] = array('id' => $row['id'], 'name' => $dashes.$row['name'], 'selected' => $selected);
			$this->get_forums($row['id'], $parentID);
			$counter--;
		}
	}
	function get_structurelist(){
		$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
		$cat_query = $GLOBALS['super']->db->query($cat_sql);
		while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query)){	
			$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`='".$cat['id']."'";
			$result = $GLOBALS['super']->db->query($query);
			$row = $GLOBALS['super']->db->fetch_assoc($result);
			$sortorder = $row['sort_order'];
			$this->structure_array['Structure'][] = array('id' => $row['id'], 'name' => $row['name'], 'sort_order' => $sortorder, 'indent' => "", 'class' => 'admin_class');		
			$this->get_structuredforums($cat['id'], true);
		}
	}
	function get_structuredforums($parent_id){
		static $counter = 1;
		static $class = 'admin_level1';
		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `parent_id`='".$parent_id."' ORDER BY `sort_order` ASC";
		$result = $GLOBALS['super']->db->query($query);
		while($row = $GLOBALS['super']->db->fetch_assoc($result)){
			if($class == "admin_line1"){
				$class = "admin_line2";
			}else{
				$class = "admin_line1";
			}
			$dashes = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$counter*2);
			$sortorder = $row['sort_order'];
			$this->structure_array['Structure'][] = array('id' => $row['id'], 'name' => $row['name'], 'sort_order' => $sortorder, 'indent' => $dashes, 'class' => $class);
			$counter++;
			$this->get_structuredforums($row['id']);
			$counter--;
		}
	}
	function add(){
		$is_cat = $GLOBALS['super']->db->escape($_POST['is_cat'], true);
		if ($is_cat == "'1'")
			$parent_id = 0;
		else
			$parent_id = intval($_POST['parent_id']);
		$escapedParentId = $GLOBALS['super']->db->escape($parent_id, true);
		$forum_name = $GLOBALS['super']->db->escape($_POST['forum_name'], true);
		$forum_description = $GLOBALS['super']->db->escape($_POST['forum_description'], true);
		$active = $GLOBALS['super']->db->escape($_POST['active'], true);
		$theme = $GLOBALS['super']->db->escape($_POST['theme'], true);
		$date = $GLOBALS['super']->db->escape(date("Y-m-d H:i:s"), true);
		$isRedirect = $GLOBALS['super']->db->escape(intval($_POST['redirect']), true);
		$url = $GLOBALS['super']->db->escape($_POST['redirecturl'], true);
		$sortOrder = 'SELECT `sort_order` FROM '.TBL_PREFIX.'forums WHERE `parent_id`='.$escapedParentId.' ORDER BY `sort_order` DESC LIMIT 1';
		$sortOrder = $GLOBALS['super']->db->query($sortOrder);
		if ($GLOBALS['super']->db->getRowCount($sortOrder) == 0){
			$sortOrder = 1;
		}else{
			$sortOrder = $GLOBALS['super']->db->fetch_assoc($sortOrder);
			$sortOrder = $sortOrder['sort_order'];
			$sortOrder++;
			$sortOrder = $GLOBALS['super']->db->escape($sortOrder);
		}
		$GLOBALS['super']->db->query("INSERT INTO ".TBL_PREFIX."forums (name, description, is_cat, sort_order, parent_id, active, theme_id, isRedirect, redirectURL, mod_user_ids, mod_group_ids, time_added)
			VALUES (".$forum_name.",".$forum_description.",".$is_cat.", ".$sortOrder.",".$parent_id.",".$active.",".$theme.",".$isRedirect.",".$url.", '', '', ".time().")");
		$forum_id = $GLOBALS['super']->db->fetch_lastid();
		$parent_list = $forum_id.",".$this->get_parent_list($parent_id);
		$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET parent_ids = '".$parent_list."' WHERE id = '".$forum_id."'");
		$groupids = array();
		$groups = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."groups");
		while($group = $GLOBALS['super']->db->fetch_assoc($groups)){
			$groupids[] = $group['id'];
		}
		foreach($groupids as $id){
			if (isset($_POST['perm'][$id])){
				$perm = "";
				foreach($_POST['perm'][$id] as $name => $value){
					if ($value == "on")
						$value = "true";
					else{
						$value = $GLOBALS['super']->db->escape($value);
					}
					$name = $GLOBALS['super']->db->escape($name);
					$perm .= $name.":".$value.",";
				}
				$sql = "INSERT INTO ".TBL_PREFIX."permissions
				(`group_id`,`name`,`value`) VALUES
				(".$id.", 'Forum".$forum_id."', '".$perm."')";
				$GLOBALS['super']->db->query($sql);
			}
		}
		return true;
	}
	function delete(){
		$GLOBALS['super']->db->query("DELETE FROM ".TBL_PREFIX."forums WHERE id = '".$_GET['fid']."'");
		return true;
	}
	function edit(){
		$curForum = $GLOBALS['super']->db->escape(intval($_GET['fid']));
		$is_cat = intval($_POST['is_cat']);
		if ($is_cat == 1)
			$parent_id = $GLOBALS['super']->db->escape("0");
		else
			$parent_id = $GLOBALS['super']->db->escape($_POST['parent_id']);
		$is_cat = $GLOBALS['super']->db->escape($is_cat, true);
		$forum_name = $GLOBALS['super']->db->escape($_POST['forum_name'], true);
		$forum_description = $GLOBALS['super']->db->escape($_POST['forum_description'], true);
		$active = $GLOBALS['super']->db->escape(intval($_POST['active']), true);
		$theme = $GLOBALS['super']->db->escape($_POST['theme'], true);
		$isRedirect = $GLOBALS['super']->db->escape(intval($_POST['redirect']), true);
		$url = $GLOBALS['super']->db->escape($_POST['redirecturl'], true);
		if ($curForum != $parent_id){
			$sortOrder = 'SELECT `sort_order` FROM '.TBL_PREFIX.'forums WHERE `parent_id`='.$parent_id.' ORDER BY `sort_order` DESC LIMIT 1';
			$sortOrder = $GLOBALS['super']->db->query($sortOrder);
			if ($GLOBALS['super']->db->getRowCount($sortOrder) == 0){
				$sortOrder = 1;
			}else{
				$sortOrder = $GLOBALS['super']->db->fetch_assoc($sortOrder);
				$sortOrder = $sortOrder['sort_order'];
				$sortOrder++;
				$sortOrder = $GLOBALS['super']->db->escape($sortOrder);
			}
		}
		$updateQuery = "UPDATE ".TBL_PREFIX."forums
						SET `name`=".$forum_name.",
						`is_cat` = ".$is_cat.",
						`description`=".$forum_description.",
						`active`=".$active.",
						`theme_id`=".$theme.",
						`isRedirect`=".$isRedirect.",
						`redirectURL`=".$url.",
						`parent_id` = ".$parent_id."
						WHERE `id` =".$curForum;
		$GLOBALS['super']->db->query($updateQuery);
		$forum_id = $GLOBALS['super']->db->fetch_lastid();
		$query = $GLOBALS['super']->db->query("SELECT id FROM ".TBL_PREFIX."forums WHERE FIND_IN_SET('".$forum_id."', parent_ids)");
		while($row = $GLOBALS['super']->db->fetch_assoc($query)){
			$parent_list = $row['id'].",".$this->get_parent_list($row['parent_id']);
			$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET parent_ids = '".$parent_list."' WHERE id = '".$row['id']."'");
		}
		$groupids = array();
		$groups = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."groups");
		while($group = $GLOBALS['super']->db->fetch_assoc($groups)){
			$groupids[] = $group['id'];
		}
		foreach($groupids as $id){
			$perm = "";
			if (isset($_POST['perm'][$id])){
				foreach($_POST['perm'][$id] as $name => $value){
					if ($value == "on")
						$value = "true";
					else{
						$value = $GLOBALS['super']->db->escape($value);
					}
					$name = $GLOBALS['super']->db->escape($name);
					$perm .= $name.":".$value.",";
				}
			}
			$checkExist = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."permissions WHERE `name`='Forum".$curForum."' AND `group_id`=".$id);
			if ($GLOBALS['super']->db->getRowCount($checkExist) > 0){
				$sql = "UPDATE ".TBL_PREFIX."permissions SET
				`value` = '".$perm."' WHERE `name`='Forum".$curForum."' AND `group_id`=".$id;
			}else{
				$sql = "INSERT INTO ".TBL_PREFIX."permissions
				(`group_id`,`name`,`value`) VALUES
				(".$id.", 'Forum".$curForum."', '".$perm."')";
			}
			$GLOBALS['super']->db->query($sql);
		}
		return true;
	}
	function get_parent_list($forum_id){
		if($forum_id == '-1' || $forum_id == "") return '-1';
		$forum = $this->get_parent($forum_id);
		$parent_list = $forum['parent_ids'];
		if (substr($parent_list, -3) != ',-1'){
			$parent_list .= ',-1';
		}
		return $parent_list;
	}
	function get_parent($parent = '-1'){
		if($parent == '-1' || $parent == "") return false;
		$parent = $GLOBALS['super']->db->escape($parent);
		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE id = '".$parent."'";
		$result = $GLOBALS['super']->db->query($query);
		if($GLOBALS['super']->db->getRowCount($result) > 0) {
			$row = $GLOBALS['super']->db->fetch_assoc($result);
			return $row;
		}
		return false;
	}
	function build_parent_lists($forum_id){
		$query = $GLOBALS['super']->db->query("SELECT id, (CHAR_LENGTH(parent_ids) - CHAR_LENGTH(REPLACE(parent_ids, ',', ''))) AS parents FROM ".TBL_PREFIX."forums WHERE FIND_IN_SET('$forum_id', parent_ids) ORDER BY parents ASC");
		while($row = $GLOBALS['super']->db->fetch_assoc($query)){
			$parent_list = $this->get_parent_list($row['id']);
			$GLOBALS['super']->db->query("UPDATE forums SET parent_ids = '".addslashes($parent_list)."' WHERE id = '".$row['id']."'");
		}
	}
}
?>