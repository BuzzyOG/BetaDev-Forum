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
require_once(ADMIN_PATH."includes/classes/subPage.php");
abstract class adminPage{
	protected $pageName;
	protected $super;
	public $sub;
	public $isRedirect;
	public $active;
	public $link;
	public $default;
	public function __construct(){
		global $super;
		$this->super = $super;
		$this->setName("");
	}
	protected function setName($name){
		$this->pageName = $name;
	}
	public function getName(){
		return $this->pageName;
	}
	public function onlineName(){
		return "Admin Panel";
	}
	public function getTitle(){
		return "Admin: ".$this->getName()." - ".$this->sub->getName()." ".$this->getAddon();
	}
	protected function getAddon(){
		
		return "(".$this->super->config->siteName.")";
	}
	public function display(){
		ob_start();
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	public function getCSS(){
		$css[] = array("path" => "display/admin.css");
		return $css;
	}
	public function getJS(){
		$js[] = array("path" => "../includes/js/jquery-1.3.2.js");
		$js[] = array("path" => "includes/js/functions.js");
		return $js;
	}
	abstract function children();
}
?>