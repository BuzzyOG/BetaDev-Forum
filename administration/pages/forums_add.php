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
class ForumsAdd extends adminSub{
	public $fhelper;
	public function __construct(){
		parent::__construct();
		$this->setName("Add");
		require_once(ADMIN_PATH."includes/classes/forumHelper.php");
		$this->fhelper = new fHelper();
	}
	function getJS(){
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/forum_set.js");
		return $js;
	}
	public function display(){
		ob_start();
		if(isset($_POST['post']) && $_POST['post'] == 'form'){
			if(isSecureForm("addEditForum") && $this->fhelper->add()){
				$success = new tpl(ROOT_PATH.'administration/display/templates/success_redir.php');
				$success->add("message","Forum Added Successfully!");
				$success->add("url","index.php?act=forums&sub=structure");
				echo $success->parse();
			}else{
				echo "Insecure!";
			}
		}
		$content = new tpl(ADMIN_PATH.'display/templates/forums_add.php');
		$this->fhelper->get_forum_list();
		$content->add("PARENT_LIST", $this->fhelper->forum_list);
		$content->add("title", "Add New Forum:");
		$content->add("name", "");
		$content->add("description", "");
		$content->add("active", "1");
		$content->add("is_cat", "0");
		$content->add("redirect", "0");
		$content->add("url", "");
		$theme_sql = "SELECT * FROM ".TBL_PREFIX."themes";
		$theme_query = $GLOBALS['super']->db->query($theme_sql);
		while ($row = $GLOBALS['super']->db->fetch_assoc($theme_query)){
			$themes[] = array(
				"id"	=>	$row['id'],
				"displayname" => $row['display_name']
			);
		}
		$content->add("THEME_LIST", $themes);
		$permgroups = array();
		$groups = $GLOBALS['super']->db->query("SELECT * FROM ".TBL_PREFIX."groups");
		while($group = $GLOBALS['super']->db->fetch_assoc($groups)){
			$permgroups[] = array("name" => $group['name'], "id" => $group['id']);
		}
		$content->add("groups", $permgroups);
		echo $content->parse();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>