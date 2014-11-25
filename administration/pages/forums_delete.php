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
class ForumsDelete extends adminSub{
	public $fhelper;
	public function __construct(){
		parent::__construct();
		$this->setName("Delete");
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
		if(isset($_GET['fid'])){
			if($this->fhelper->delete()){
				$success = new tpl(ROOT_PATH.'administration/display/templates/success_redir.php');
				$success->add("message","Forum Deleted Successfully!");
				$success->add("url","index.php?act=forums&sub=structure");
				echo $success->parse();
			}
		}
		$content = new tpl(ADMIN_PATH.'display/templates/forums_delete.php');
		$this->fhelper->get_forum_list();
		$content->add("forum", $this->fhelper->forum_list);
		echo $content->parse();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>