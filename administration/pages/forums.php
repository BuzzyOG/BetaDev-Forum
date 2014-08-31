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
if( ! defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}
if( ! defined("INCLUDED")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}
class Forums extends adminPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Forums");
	}
	public function children(){
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Structure', 'act' => 'structure','redirect' => 'No');
		$children[] = array('text' => 'Add', 'act' => 'add', 'redirect' => 'No');
		$children[] = array('text' => 'Edit', 'act' => 'edit', 'redirect' => 'No');
		$children[] = array('text' => 'Delete', 'act' => 'delete', 'redirect' => 'No');
		return $children;
	}
	public function setSub($sub){
		switch ($sub){
			case "Structure":
				require_once(ADMIN_PATH.'pages/forums_structure.php');
	    		$this->sub = new ForumsStruct();
				break;
			case "Add":
				require_once(ADMIN_PATH.'pages/forums_add.php');
	    		$this->sub = new ForumsAdd();
				break;
			case "Edit":
				require_once(ADMIN_PATH.'pages/forums_edit.php');
	    		$this->sub = new ForumsEdit();
				break;
			case "Delete":
				require_once(ADMIN_PATH.'pages/forums_delete.php');
				$this->sub = new ForumsDelete();
				break;
			default:
				require_once(ADMIN_PATH.'pages/forums_info.php');
	    		$this->sub = new ForumsInfo();
				break;
		}
		return $this->sub->getName();
	}
	public function display(){
		ob_start();
		echo $this->sub->display();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	public function getJS(){
		$js = parent::getJS();
		return array_merge($js, $this->sub->getJS());
	}
}
?>