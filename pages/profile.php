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
require_once(ROOT_PATH."includes/classes/bbCode.php");
class Profile extends forumPage{
	private $escapedId;
	private $noUser = false;
	private $currentUser;
	private $userName2Display;
	public function __construct(){
		parent::__construct();
		if (isset($_GET['id']) && $_GET['id'] > 0){
			$id = $_GET['id'];
		}else {
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
			die();
		}
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);
		$this->escapedId = $id;
		$userSelect = "SELECT u.*, g.color, g.name FROM ".TBL_PREFIX."users AS u
				INNER JOIN ".TBL_PREFIX."groups AS g
				ON u.group_id =g.id
				WHERE u.id=".$this->escapedId;
		$userSelect = $GLOBALS['super']->db->query($userSelect);
		if ($GLOBALS['super']->db->getRowCount($userSelect) == 0){
			$this->noUser = true;
			return;
		}
		$this->currentUser = $GLOBALS['super']->db->fetch_assoc($userSelect);
		$this->userName2Display = $GLOBALS['super']->functions->user2Display($this->currentUser['username'], $this->currentUser['displayname']);
		$this->setName($this->userName2Display);
	}
	public function getName(){
		return "Viewing ".$this->pageName."'s Profile";
	}
	public function getJS(){
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/profile.js");
		return $js;
	}
	public function display(){
		ob_start();
		$bbCode = new bbCode();
		$tpl = new tpl(ROOT_PATH.'themes/Default/templates/profile.php');
		$posts = "SELECT count(`id`) FROM ".TBL_PREFIX."posts WHERE `user_id`=".$this->currentUser['id'];
		$posts = $GLOBALS['super']->db->query($posts);
		$posts = $GLOBALS['super']->db->fetch_result($posts);
		if (!$posts){
			$tpl->add("noActivity", true);
		}else{
			$tpl->add("noActivity",false);
			$topics = "SELECT count(`id`) FROM ".TBL_PREFIX."topics WHERE `user_id`=".$this->currentUser['id'];
			$topics = $GLOBALS['super']->db->query($topics);
			$topics = $GLOBALS['super']->db->fetch_result($topics);
			$tpl->add("TopicCount", $topics);
			$totalTopics = $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query("SELECT count(`id`) FROM ".TBL_PREFIX."topics"));
			$topicPercent = round(($topics/$totalTopics)*100,2);
			$tpl->add("TopicPercent", $topicPercent);
			$numPosts = $posts - $topics;
			$tpl->add("PostCount", $numPosts);
			$totalPosts = $GLOBALS['super']->db->fetch_result($GLOBALS['super']->db->query("SELECT count(`id`) FROM ".TBL_PREFIX."posts")) - $totalTopics;
			$totalPosts = max(1, $totalPosts);
			$postPercent = round(($posts/$totalPosts)*100,2);
			$tpl->add("PostPercent", $postPercent);
			$daysSinceRegister = time()-$this->currentUser['time_added'];
			$daysSinceRegister = $daysSinceRegister / (60 * 60 * 24);
			$daysSinceRegister = ceil($daysSinceRegister);
			$postsPerDay = round($posts/$daysSinceRegister,1);
			$tpl->add("PostsPerDay", $postsPerDay);
			$topicsPerDay = round($topics/$daysSinceRegister,1);
			$tpl->add("TopicsPerDay", $topicsPerDay);
			$favoriteTopic = 'SELECT count(p.id) as Count, p.topic_id, t.name, t.user_id FROM '.TBL_PREFIX.'posts AS p INNER JOIN '.TBL_PREFIX.'topics AS t ON p.topic_id=t.id WHERE p.user_id='.$this->currentUser['id'].' GROUP BY p.topic_id ORDER BY Count DESC LIMIT 1';
			$favoriteTopic = $GLOBALS['super']->db->query($favoriteTopic);
			$favoriteTopic = $GLOBALS['super']->db->fetch_assoc($favoriteTopic);
			$tpl->add("FavoriteTopic", $favoriteTopic);
			$biggestTopic = 'SELECT * FROM '.TBL_PREFIX.'topics WHERE `user_id`='.$this->currentUser['id'].' ORDER BY `post_count` DESC LIMIT 1';
			$biggestTopic = $GLOBALS['super']->db->query($biggestTopic);
			$biggestTopic = $GLOBALS['super']->db->fetch_assoc($biggestTopic);
			$tpl->add("BiggestTopic", $biggestTopic);
			$biggestCount = $biggestTopic['post_count']+1;
			$tpl->add("BiggestCount", $biggestCount);
			$lastPost = 'SELECT p.*, t.name AS topicName FROM '.TBL_PREFIX.'posts AS p INNER JOIN '.TBL_PREFIX.'topics AS t ON p.topic_id=t.id WHERE p.user_id='.$this->currentUser['id'].' ORDER BY p.time_added DESC LIMIT 1';
			$lastPost = $GLOBALS['super']->db->query($lastPost);
			$lastPost = $GLOBALS['super']->db->fetch_assoc($lastPost);
			$tpl->add("LastPost", $lastPost);
			$lastPostDate = $GLOBALS['super']->functions->formatDate($lastPost['time_added']);
			$tpl->add("LastPostDate", $lastPostDate);
		}
		$avatar = $GLOBALS['super']->functions->getImage($this->currentUser['avatar']);
		$signature = $bbCode->parse($this->currentUser['signature']);
		$online = "SELECT `page` FROM ".TBL_PREFIX."online WHERE `userid`=".$this->currentUser['id'];
		$online = $GLOBALS['super']->db->query($online);
		$onlinecount = $GLOBALS['super']->db->getRowCount($online);
		$fname = $this->currentUser['firstname'];
		$lname = $this->currentUser['lastname'];
		$gender = $this->currentUser['gender'];
		$country = $this->currentUser['country'];
		$bday = $this->currentUser['birth_day'];
		$bmonth = $this->currentUser['birth_month'];
		$byear = $this->currentUser['birth_year'];
		$website = $this->currentUser['website'];
		$email = $this->currentUser['email'];
		$name = "";
		if ($fname){
			$name .= $fname;
			if ($lname)
				$name .= " ".$lname;
		}
		$personal = array();
		if ($name)
			$personal[] = array("Real Name", $name);
		if ($gender == "m")
			$gender = "Male";
		elseif ($gender == "f")
			$gender = "Female";
		if ($gender)	
			$personal[] = array("Gender", $gender);
		if ($country)
			$personal[] = array("Country", $country);
		if ($website)
			$personal[] = array("Website", '<a href="'.$website.'">'.$website.'</a>');
		if ($email)
			$personal[] = array("Email", 'mailto:'.$email);
		if ($bday && $bmonth && $byear){
			$timestamp = mktime(null, null, null, $bmonth, $bday, $byear);
			$personal[] = array("Birth Day", date("F jS, Y", $timestamp));
		}
		if($onlinecount > 0)
			$page = $GLOBALS['super']->db->fetch_result($online);	
		else 
			$page = "Offline";
		$tpl->add("UserName", $this->userName2Display);
		$tpl->add("Avatar", $avatar);
		$tpl->add("currentUser", $this->currentUser);
		$tpl->add("Page", $page);
		$tpl->add("Personal", $personal);
		$tpl->add("Signature", $signature);
		$tpl->parse();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>