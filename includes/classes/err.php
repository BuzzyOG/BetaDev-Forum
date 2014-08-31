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
if( ! defined("SEC")){
	echo "<p>You cannot access this page directly</p>";
	exit();
}
class err{
	public $error = array();
	public $errors = array();
	function __construct(){
		require_once(ROOT_PATH."includes/error_array.php");
		$this->error = $error_array;
	}
	function throwError($errorno){	
		if (key_exists($errorno, $this->error)){
			$this->errors[] = array('id' => $errorno, 'message' => $this->error[$errorno]);
		}
	}	
	function getNumErrors(){
		return count($this->errors);
	}
	function showErrors(){
		if (count($this->errors) > 0){
			return "There are ".count($this->errors)." errors on this page.";
		}
	}
}
?>