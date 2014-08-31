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
	private  $dieOnError = false;
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
	function checkError($msg='', $dieOnError=false){
		if (mysql_errno()){
			$error = $msg."MySQL error ".mysql_errno().": ".mysql_error();
    		if($this->dieOnError || $dieOnError){	
    			die ($error);		
       		}else{
    			$this->last_error = $error;
    		}
        }
        return false;
	}
	function checkConnection(){
			$this->last_error = '';
			if(!isset($this->database)) $this->connect();
	}
	function query($sql, $dieOnError=true, $msg='Error: '){
		global $debug;
		$query_start = microtime();
		$this->checkConnection();
		$result = mysql_query($sql);
		$this->lastmysqlrow = -1; 
		$this->checkError($msg.' Query Failed:' . $sql . ' :: ', $dieOnError);
		$_start = explode(' ', $query_start);
		$_end = explode(' ', microtime());
		$_time = number_format(($_end[1] + $_end[0] - ($_start[1] + $_start[0])), 6);
		$debug['QUERIES'][] = trim($sql);
		$debug['TIME'][] = $_time;
		return $result;
	}
	function getRowCount(&$result){
		if(isset($result) && !empty($result))
				return mysql_num_rows($result);
		return 0;
	}
	function fetch_lastid(){
		return mysql_insert_id();
	}
    function fetch_assoc(&$result){
		$row = mysql_fetch_assoc($result);
		return $row;
	}
	function fetch_result($result, $row=0){
		$result = mysql_result($result, $row);
		return $result;
	}
	function getLastId() {
		return mysql_insert_id();
	}
	function connect()
	{
			$this->database = @mysql_connect($this->dbHostName,$this->userName,$this->userPassword) or die("Can't connect: ".mysql_error());
			@mysql_select_db($this->dbName) or die( "Unable to select database: " . mysql_error());
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
        	$string = mysql_real_escape_string($string);
		}else{
			$string = mysql_escape_string($string);
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