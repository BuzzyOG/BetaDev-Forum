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
class user{
	private $super;
	private $cookiename = "sarologin";
	private $currentUser;
	private $guestUser;
	private $perm;
	private $viewedTopics = array();
	private $viewedForums = array();
	function __construct(&$super){
		$this->super = $super;
		if (isset($_SESSION['logged']) && $_SESSION['logged']){
			$user_id = $this->super->db->escape($_SESSION['user_id']);
			$sessionid = $this->super->db->escape(session_id());
			$user = "SELECT * FROM ".TBL_PREFIX."users WHERE (`id` = ".$user_id.") AND (`session_id` = '".$sessionid."')";
			$user = $this->super->db->query($user);
			if($this->super->db->getRowCount($user) > 0){
				$result = $this->super->db->fetch_assoc($user);
				$this->setUser($result);
			}else{
				$this->logout();
			}
		}
		elseif(isset($_COOKIE[$this->cookiename])){
			if (get_magic_quotes_gpc())
			{
				list($user_id, $cookie) = unserialize(stripslashes($_COOKIE[$this->cookiename]));
			}else{
				list($user_id, $cookie) = unserialize(stripslashes($_COOKIE[$this->cookiename]));
			}
			if (!$user_id || !$cookie)
				return;
			$user_id = $this->super->db->escape($user_id);
			$cookie = $this->super->db->escape($cookie);
			$user = "SELECT * FROM ".TBL_PREFIX."users WHERE (`id` = '".$user_id."') AND (`cookie` = '".$cookie."')";
			$user = $this->super->db->query($user);
			if($this->super->db->getRowCount($user) > 0){
				$user = $this->super->db->fetch_assoc($user);
				$this->setUser($user);
			}else
				$this->logout();
		}else{
			$this->logout();
		}
		$this->guestUser['username'] = "Guest";
		$this->guestUser['group_id'] = "3";
		$this->guestUser['theme_id'] = $this->super->config->defaultTheme;
		$this->guestUser['date_format'] = $this->super->config->dateFormat;
		$this->guestUser['time_format'] = $this->super->config->timeFormat;
		$this->guestUser['time_zone'] = $this->super->config->timeZone;
		$this->guestUser['posts_per_page'] = $this->super->config->postsPerPage;
		$this->guestUser['topics_per_page'] = $this->super->config->topicsPerPage;
		$this->guestUser['read_timt'] = "0";
		$this->guestUser['read_topics'] = "";
		$perm = "SELECT * FROM ".TBL_PREFIX."permissions WHERE `group_id`=".$this->group_id;
		$perm = $this->super->db->query($perm);
		while($perms = $this->super->db->fetch_assoc($perm)){
			if (strpos($perms["value"], ",") !== false){
				$subarray = array();
				$parts = explode(",", $perms["value"]);
				foreach($parts as $part){
					if ($part != ""){
						$permparts = explode(":",$part);
						$val = false;
						if ($permparts[1] == "true"){
							$val = true;
						}
						
						$subarray[$permparts[0]] = $val;
					}
				}
				$this->perm[$perms["name"]] = $subarray;	
			}else{
				$val = false;
				if ($perms["value"] == "true"){
					$val = true;
				}
				$this->perm[$perms["name"]] = $val;
			}
		}
		$topics = explode(",",$this->read_topics);
		array_pop($topics);
		foreach($topics as $topic){
			if (!in_array($topic, $this->viewedTopics))
				$this->viewedTopics[] = $topic;
		}
		$forums = explode(",",$this->read_forums);
		array_pop($forums);
		foreach($forums as $forum){
			if (!in_array($forum, $this->viewedForums))
				$this->viewedForums[] = $forum;
		}
	}
	function setUser($user){
		$this->currentUser = $user;
		$_SESSION['logged'] = true;
		$_SESSION['user_id'] = $this->currentUser['id'];
		$sessionid = $this->super->db->escape(session_id());
		if ($this->currentUser['session_id'] != $sessionid){
			$sql = "UPDATE ".TBL_PREFIX."users SET `session_id` = '".$sessionid."' WHERE `id` = ".$this->currentUser['id'];
			$this->super->db->query($sql);
		}
	}
	function logout(){
		$_SESSION['logged'] = false;
		$_SESSION['user_id'] = 0;
		setcookie($this->cookiename, serialize(""), time()-3600);
		$this->currentUser = null;
	}
	function checkLogin($name, $pass, $remember){
		$name = $this->super->db->escape($name);
		$pass = encrypt_password($pass);
		$pass = $this->super->db->escape($pass);
		$existsSql = "SELECT * FROM ".TBL_PREFIX."users WHERE `username` = '".$name."' AND `password` = '".$pass."'";
		$existsSql = $this->super->db->query($existsSql);
		if ($this->super->db->getRowCount($existsSql) > 0){
			$user = $this->super->db->fetch_assoc($existsSql);
			$this->setUser($user);
			$time = encrypt_password(time());
			if ($remember){
				$sql = "UPDATE ".TBL_PREFIX."users SET `cookie` = '".$time."' WHERE `id` = ".$this->currentUser['id'];
				$this->super->db->query($sql);
				$cookie = serialize( array($this->currentUser['id'], $time) );
				setcookie($this->cookiename, $cookie, time() + 60*60*24*30);
			}
			return true;
		}else{
			$this->logout();
			return false;
		}
	}
	function isLogged(){
		return is_array($this->currentUser) && count($this->currentUser) > 0;
	}
	public function __get($key){
		if (is_array($this->currentUser) && key_exists($key, $this->currentUser) && $this->currentUser[$key] != ""){
			return $this->currentUser[$key];
		}elseif (key_exists($key, $this->guestUser)){
			return $this->guestUser[$key];
		}else{
			return false;
		}
	}
	public function __set($key, $val){
		if ($key == "id")
			return false;
		if ($this->isLogged() && key_exists($key, $this->currentUser)){
			$this->currentUser[$key] = $val;
			$sql = "UPDATE ".TBL_PREFIX."users SET `".$GLOBALS['super']->db->escape($key)."`= '".$GLOBALS['super']->db->escape($val)."' WHERE `id`=".$this->currentUser['id'];
			$GLOBALS['super']->db->query($sql);
		}else{
			return false;
		}
	}
	function updateActive($pageClass){
		if ($this->isLogged() && $this->currentUser["show_online"] == 0)
			return;
		$ip = long2ip(ip2long($_SERVER['REMOTE_ADDR']));
		$ip = $this->super->db->escape($ip);
		$id = $this->id;
		if ($id == false)
			$id = 0;
		$time = time();
		$page = $pageClass->onlineName();
		$page = $this->super->db->escape($page);
		$latestTime = time() - 60*$this->super->config->usersOnlineOffset;
		$sessionid = $this->super->db->escape(session_id());
		$userInsert = "INSERT INTO ".TBL_PREFIX."online (`userid`, `ip`, `sessionid`, `time_modified`, `page`) VALUES
					(".$id.", '".$ip."', '".$sessionid."', ".$time.", '".$page."');";
		$removal = "DELETE FROM ".TBL_PREFIX."online WHERE `userid` = ".$id." OR `sessionid` = '".$sessionid."' OR `sessionid`=' ' OR `time_modified` < ".$latestTime;
		$this->super->db->query($removal);
		$this->last_active = time();
			$this->super->db->query($userInsert);
	}
	function can($key, $subkey = null){
		if (key_exists($key, $this->perm)){
			if (is_array($this->perm[$key])){
				if (key_exists($subkey, $this->perm[$key]) && $subkey != null){	
					return $this->perm[$key][$subkey];
				}else{
					return false;
				}
			}else
				return $this->perm[$key];
		}
		return false;
	}
	function noPerm(){
		$tpl = new tpl(ROOT_PATH."themes/Default/templates/error.php");
		$tpl->add("error_message", "You don't have permission to view this page.");
		return $tpl->parse(true);
	}
	function viewed_topic($topicid){
		if (!$this->isLogged())
			return;
		$t = intval($topicid);
		$query = 'UPDATE '.TBL_PREFIX.'users
					SET `read_topics` = CONCAT(`read_topics`,"'.$t.',")
					WHERE `id`="'.$this->currentUser['id'].'"
					AND NOT FIND_IN_SET("'.$t.'", `read_topics`)
				';
		$this->super->db->query($query);
	}
	function has_viewed_topic($topicid){
		return in_array($topicid, $this->viewedTopics);
	}
}
?>