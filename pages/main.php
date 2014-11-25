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
class Main extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName($GLOBALS['super']->config->siteName);
	}
	public function onlineName(){
		return "Board Index";
	}
	public function display(){
		ob_start();
		if ($GLOBALS['super']->user->can("ViewBoard")){
			$cat_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `is_cat`='1' ORDER BY `sort_order` ASC";
			$cat_query = $GLOBALS['super']->db->query($cat_sql);
			while ($cat = $GLOBALS['super']->db->fetch_assoc($cat_query)){
				require_once(ROOT_PATH.'includes/classes/forum_display.php');
				$displayForum = new showForums($cat['id'], $cat['name']);
				$displayForum->display(false);
			}
		}else {
			echo $GLOBALS['super']->user->noPerm();
		}
		$memberCount = "SELECT COUNT(id) as count FROM ".TBL_PREFIX."users";
		$memberCount = $GLOBALS['super']->db->query($memberCount);
		$memberCount = $GLOBALS['super']->db->fetch_assoc($memberCount);
		$topicCount = "SELECT COUNT(id) as count FROM ".TBL_PREFIX."topics";
		$topicCount = $GLOBALS['super']->db->query($topicCount);
		$topicCount = $GLOBALS['super']->db->fetch_assoc($topicCount);
		$postCount = "SELECT COUNT(id) as count FROM ".TBL_PREFIX."posts";
		$postCount = $GLOBALS['super']->db->query($postCount);
		$postCount = $GLOBALS['super']->db->fetch_assoc($postCount);
		$newestMember = "SELECT id
			FROM ".TBL_PREFIX."users 
			ORDER BY time_added DESC LIMIT 1";
		$newestMember = $GLOBALS['super']->db->query($newestMember);
		$newestMember = $GLOBALS['super']->db->fetch_assoc($newestMember);
		$newestMember = $GLOBALS['super']->functions->getUser($newestMember['id'], true);
		$stats = new tpl(ROOT_PATH.'themes/Default/templates/stats.php');
		$stats->add("MEMBERS", $memberCount['count']);
		$stats->add("TOPICS", $topicCount['count']);
		$stats->add("POSTS", $postCount['count']-$topicCount['count']);
		$stats->add("NEWESTMEMBER", $newestMember);
		$guestsOnline = "SELECT COUNT(*) as count FROM ".TBL_PREFIX."online WHERE `userid`=0";
		$guestsOnline = $GLOBALS['super']->db->query($guestsOnline);
		$guestsOnline = $GLOBALS['super']->db->fetch_assoc($guestsOnline);
		$stats->add("GUESTSONLINE", $guestsOnline['count']);
		$usersOnline = "SELECT userid
			 FROM ".TBL_PREFIX."online
			 WHERE userid !=0 ORDER BY `time_modified` DESC";
		$usersOnline = $GLOBALS['super']->db->query($usersOnline);
		$numUsersOnline = $GLOBALS['super']->db->getRowCount($usersOnline);
		$stats->add("NUMUSERSONLINE", $numUsersOnline);
		$users = array();
		while($user = $GLOBALS['super']->db->fetch_assoc($usersOnline)){
				$users[] = $GLOBALS['super']->functions->getUser($user['userid'], true);
		}
		$stats->add("USERSONLINE", $users);
		echo $stats->parse();
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>