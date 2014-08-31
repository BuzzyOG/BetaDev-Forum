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
class Home extends adminPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Home");
	}
	public function children(){
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Board', 'act' => 'board', 'redirect' => 'Yes', 'link' => FORUM_ROOT);
		return $children;
	}
	public function setSub($sub){
		switch ($sub){
			case "License":
				break;
			case "Statistics":
				break;
			default:
				require_once(ADMIN_PATH.'pages/home_info.php');
	    		$this->sub = new HomeInfo();
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
}
?>