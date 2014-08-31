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
class Configuration extends adminPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Configuration");
	}
	public function children(){
		$children[] = array('text' => 'Info', 'act' => 'info', 'redirect' => 'No');
		$children[] = array('text' => 'Edit', 'act' => 'edit', 'redirect' => 'No');
		return $children;
	}
	public function setSub($sub){
		switch ($sub){
			case "Edit":
				require_once(ADMIN_PATH.'pages/configuration_edit.php');
	    		$this->sub = new ConfigurationEdit();
				break;
			default:
				require_once(ADMIN_PATH.'pages/configuration_info.php');
	    		$this->sub = new ConfigurationInfo();
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