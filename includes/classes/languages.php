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
class lang{
	public $language;
	public $main=array();
	public $text=array();
	function __construct(){
	}
	function init(){
		$string = ROOT_PATH."languages/".$this->language.".php";
		if (file_exists($string)){
   			require_once($string);
			$this->text = $main;
		}else{
			$GLOBALS['super']->err->errno = 4;
			$GLOBALS['super']->err->throwerr();
		}
	}	
}
?>