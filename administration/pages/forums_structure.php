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
class ForumsStruct extends adminSub{
	public $fhelper;
	public function __construct(){
		parent::__construct();
		$this->setName("Structure");
		require_once(ADMIN_PATH."includes/classes/forumHelper.php");
		$this->fhelper = new fHelper();
	}
	public function display(){
		ob_start();
		$content = new tpl(ADMIN_PATH.'display/templates/forums_structure.php');
		if(isSecureForm("structForum") && isset($_POST['post']) && $_POST['post'] == '1'){
			$update_string = "Forum information updated successfully.<br /><br />";
			$content->add("INFORMATION_UPDATED", $update_string);
			foreach($_POST['sortorder'] as $key=>$value){
				$sort = intval($value);
				$sort = $GLOBALS['super']->db->escape($sort, true);
				$id = intval($key);
				$id = $GLOBALS['super']->db->escape($id, true);
				$query = "UPDATE ".TBL_PREFIX."forums SET `sort_order`=".$sort." WHERE `id`=".$id;
				$GLOBALS['super']->db->query($query);
			}
		}else{
			$content->add("INFORMATION_UPDATED", "");
		}
		$this->fhelper->get_structurelist();
		$content->add("STRUCTUREDFORUMS", $this->fhelper->structure_array['Structure']);
		echo $content->parse();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>