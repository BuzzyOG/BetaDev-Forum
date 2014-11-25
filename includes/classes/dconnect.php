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
class dconnect{
	private $database = null;
	private $dbHostName = null;
	private $dbName = null;
	private $dbOptions = null;
	private $userName=null;
	private $userPassword=null;
	static function &getInstance(){
			static $dbm;
			static $count;
			static $old_count;
			if(!isset($dbm)){
				$count++;
				$dbm = new dconnect();
				$dbm->resetSettings();
				$dbm->count_id = $count;
				$dbm->references = 0;
			}else{
				$old_count++;
				$dbm->references = $old_count;
			}
			return $dbm;
	}
	function setDieOnError($value){
		$this->dieOnError = $value;
	}
	function setUserName($name){
		$this->userName = $name;
	}
	function setOption($name, $value){
		if(isset($this->dbOptions)){
			$this->dbOptions[$name] = $value;
		}
		if(isset($this->database)){
			$this->database->setOption($name, $value);
		}
	}
	function setUserPassword($pass){
		$this->userPassword = $pass;
	}
	function setDatabaseName($db){
		$this->dbName = $db;
	}
	function setDatabaseHost($host){
		$this->dbHostName = $host;
	}
	function checkConnection(){
		if(!isset($this->database)) $this->connect();
	}
	function query($sql){
		$this->checkConnection();
		$result = $this->database->query($sql);
		if($result == false) {
			try {
				throw new Exception("MySQL error {$result->error} <br> Query:<br> {$string}", $result->errno); 
				echo "<h1><b>Please report this to Cblair91 immediately!</b></h1>";   
			} catch(Exception $e) {
				echo "Error No: {$e->getCode()} - {$e->getMessage()}<br >";
				echo nl2br($e->getTraceAsString());
				echo "<h1><b>Please report this to Cblair91 immediately!</b></h1>";
			}
		} else {
			return $result;
		}
	}
	function getRowCount(&$result){
		return $result->num_rows;
	}
	function fetch_lastid(){
		return $this->database->insert_id;
	}
    function fetch_assoc(&$result){
		return $result->fetch_assoc();
	}
	function getLastId() {
		return $this->database->insert_id;
	}
	function connect() {
		$this->database = new mysqli($this->dbHostName, $this->userName, $this->userPassword, $this->dbName);
		if($this->database->connect_errno > 0) {
			die("Unable to connect to site database [{$this->database->connect_error}]");
		}
	}
	function resetSettings(){
		global $INFO;
		$this->disconnect();
		$this->setUserName($INFO['username']);
		$this->setUserPassword($INFO['password']);
		$this->setDatabaseHost($INFO['host']);
		$this->setDatabaseName($INFO['database']);
	}
    function escape($string, $encapsulate=false){
		$this->checkConnection();
		if(get_magic_quotes_gpc()){
			$string = stripslashes($string);
		}     
		if(function_exists('mysql_real_escape_string')){
        	$string = $this->database->real_escape_string($string);
		}else{
			$string = $this->database->escape_string($string);
		}
        if ($encapsulate){
        	$string = "'".$string."'";
        }
        return $string; 
    }
    function disconnect() {    
		if(isset($this->database)){
			mysql_close($this->database);
			unset($this->database);
        }
    }   
}
?>