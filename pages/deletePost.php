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
class deletePost extends tDisplay{
	private $currentPost;
	public function __construct(){
		$pid = $_GET['id'];
		$pid = intval($pid);
		$pid = $GLOBALS['super']->db->escape($pid);
		$post_sql = "SELECT * FROM ".TBL_PREFIX."posts WHERE `id`=".$pid;
		$post = $GLOBALS['super']->db->query($post_sql);
		if ($GLOBALS['super']->db->getRowCount($post) >= 0){
			$this->currentPost = $GLOBALS['super']->db->fetch_assoc($post);
			parent::__construct($this->currentPost['topic_id']);
		}
		$this->setName("Delete Post");
	}
	public function getName(){
		return $this->currentTopic['name']." / ".$this->pageName;
	}
	public function onlineName(){
		return $this->pageName.": ".$this->currentTopic['name'];
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->pageName;
	}
	public function display(){
		ob_start();
		if (!$GLOBALS['super']->user->can("ViewBoard") && (!($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") && $this->currentPost['user_id'] != $GLOBALS['super']->user->id) || !($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf") && $this->currentPost['user_id'] == $GLOBALS['super']->user->id))){
			echo $GLOBALS['super']->user->noPerm();
			return;
		}
		elseif ($this->noTopic){
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}else if (isSecureForm("deletePost") && isset($_POST['formsent']) && $_POST['formsent'] == "1"){
			$errorArray = array();
			$isFirst = false;
			$postId = $GLOBALS['super']->db->escape(intval($this->currentPost['id']));
			$query = $GLOBALS['super']->db->query("SELECT count(`id`) FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$this->currentTopic['id']);
			$result = $GLOBALS['super']->db->fetch_result($query);
			if ($result == 1){
				$isFirst = true;
				$query = $GLOBALS['super']->db->query("DELETE FROM ".TBL_PREFIX."topics WHERE `id`=".$this->currentTopic['id']);
				$lastPost = $GLOBALS['super']->db->query("SELECT p.id FROM ".TBL_PREFIX."posts AS p INNER JOIN ".TBL_PREFIX."topics AS t ON p.topic_id=t.id INNER JOIN ".TBL_PREFIX."forums AS f ON t.forum_id = f.id WHERE f.id=".$this->currentForum['id']." ORDER BY p.time_added DESC LIMIT 1");
				if ($GLOBALS['super']->db->getRowCount($lastPost) > 0)
					$lastPost = $GLOBALS['super']->db->fetch_result($lastPost);
				else{
					$lastPost = 0;
				}
				$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET `last_post_id`=".$lastPost.", `topic_count` = ".($this->currentForum['topic_count']-1) .", `post_count`=".($this->currentForum['post_count']-$this->currentTopic['post_count'])."  WHERE `id`=".$this->currentForum['id']);
			}else{
				$isFirst = false;
				$GLOBALS['super']->db->query("DELETE FROM ".TBL_PREFIX."posts WHERE `id`=".$this->currentPost['id']);
				$lastPost = $GLOBALS['super']->db->query("SELECT p.id FROM ".TBL_PREFIX."posts AS p INNER JOIN ".TBL_PREFIX."topics AS t ON p.topic_id=t.id INNER JOIN ".TBL_PREFIX."forums AS f ON t.forum_id = f.id WHERE f.id=".$this->currentForum['id']." ORDER BY p.time_added DESC LIMIT 1");
				$lastPost = $GLOBALS['super']->db->fetch_result($lastPost);
				$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."forums SET `last_post_id`=".$lastPost.", `post_count`=".($this->currentForum['post_count']-1) ." WHERE `id`=".$this->currentForum['id']);
				$lastUser = $GLOBALS['super']->db->query("SELECT u.id FROM ".TBL_PREFIX."users AS u INNER JOIN ".TBL_PREFIX."posts AS p ON p.user_id = u.id INNER JOIN ".TBL_PREFIX."topics AS t ON p.topic_id = t.id WHERE t.id=".$this->currentTopic['id']." ORDER BY p.time_added DESC LIMIT 1");
				$lastUser = $GLOBALS['super']->db->fetch_result($lastUser);
				$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."topics SET `last_user_id`=".$lastUser.", `post_count`=".($this->currentTopic['post_count']-1) ." WHERE `id`=".$this->currentTopic['id']);
			}
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message","Post Deleted Successfully!");
			if ($isFirst)
				$url = "index.php?act=fdisplay&id=".$this->currentTopic['forum_id'];
			else 
				$url = "index.php?act=tdisplay&id=".$this->currentPost['topic_id'];
			$success->add("url",$url);
			echo $success->parse();
		}
		if (!isset($_POST['formsent'])){
			$delete = new tpl(ROOT_PATH.'themes/Default/templates/deletepost.php');
			$delete->add("topicName", $this->currentTopic['name']);
			$delete->add("message", $this->currentPost['message']);
			echo $delete->parse();
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>