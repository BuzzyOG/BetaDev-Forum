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
if(!defined("SEC")) {
	echo "<p>You cannot access this page directly</p>";
	exit();
}
require_once(ROOT_PATH."config.php");
require_once(ROOT_PATH."includes/classes/err.php");
require_once(ROOT_PATH."includes/classes/dconnect.php");
require_once(ROOT_PATH."includes/classes/languages.php");
require_once(ROOT_PATH."includes/classes/functions.php");
require_once(ROOT_PATH."includes/classes/user.php");
require_once(ROOT_PATH."includes/classes/config.php");
class super{
	public $db;
	public $err;
	public $lang;
	public $config;
	public $user;
	public $current_page;
	public $functions;
	function __construct(){
		$this->err = new err();
		$this->db = dconnect::getInstance();
		$this->config = new config($this);
		$this->functions = new functions($this);
		$this->user = new user($this);
	}	
	function __toString(){
		return "The Super Class. Fear it's power!";
	}
}
?>