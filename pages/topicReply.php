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
class topicReply extends tDisplay{
	public function __construct(){
		parent::__construct();
		$this->setName("Post Reply");
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
	function getJS(){
		$highlighterRoot = "includes/packages/Syntax Highlighter/";
		$js = parent::getJS();
		$js[] = array("path" => $highlighterRoot."scripts/shCore.js");
		$syntaxScripts = scandir($highlighterRoot."scripts");
		foreach($syntaxScripts as $syntax){
			if (!is_dir($syntax)){
				$js[] = array("path" => $highlighterRoot."scripts/".$syntax);
			}
		}
		$js[] = array("path" => "includes/js/syntaxHighlighter.js");
		return $js;
	}
	function getCSS(){
		$highlighterRoot = "includes/packages/Syntax Highlighter/";
		$css = parent::getCSS();
		$css[] = array("path" => $highlighterRoot."styles/shCore.css");
		$css[] = array("path" => $highlighterRoot."styles/shThemeDefault.css");
		return $css;
	}
	public function display(){
		ob_start();
		$bbCode = new bbCode();
		$errorArray = array();
		if (!$GLOBALS['super']->user->can("ViewBoard") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "Reply")
		){
			echo $GLOBALS['super']->user->noPerm();
			return;			
		}elseif ($this->noTopic){
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}
		if (isset($_POST['formsent']) && $_POST['formsent'] == "1"){
			if (isSecureForm("topicReply")){
				$message = $_POST['message'];
				if (strlen($message) <= 10){
					$errorArray[] = "The message must be greater than 10 characters";
				}
				if (!count($errorArray)){
					$topicId = $GLOBALS['super']->db->escape(intval($_GET['id']));
					$message = $GLOBALS['super']->db->escape(strip_tags($message));
					$posterid = $GLOBALS['super']->user->id; 
					$addPostSql = "INSERT INTO ".TBL_PREFIX."posts (
								`topic_id`,
								`user_id`,
								`time_added`,
								`message`
								)
								VALUES (
								'".$topicId."', '".$posterid."',".time().", '".$message."'
								);
								";
					$GLOBALS['super']->db->query($addPostSql);
					$postid = $GLOBALS['super']->db->fetch_lastid();
					$updateUsersSQL = "UPDATE ".TBL_PREFIX."users SET `total_posts` = `total_posts`+1 WHERE `id`='".$posterid."'";
					$GLOBALS['super']->db->query($updateUsersSQL);
					$updateTopicSQL = "UPDATE ".TBL_PREFIX."topics SET `post_count` = `post_count`+1, `time_modified`=".time().", `last_user_id`=".$posterid." WHERE `id`='".$topicId."'";
					$GLOBALS['super']->db->query($updateTopicSQL);
					$forumId = "SELECT `forum_id` FROM ".TBL_PREFIX."topics WHERE `id`=".$topicId;
					$forumId = $GLOBALS['super']->db->query($forumId);
					$forumId = $GLOBALS['super']->db->fetch_result($forumId);
					$updateTopicSQL = "UPDATE ".TBL_PREFIX."forums SET `post_count` = `post_count`+1, `last_post_id`=".$postid." WHERE `id`='".$forumId."'";
					$GLOBALS['super']->db->query($updateTopicSQL);
					$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
					$success->add("message","Reply made successfully!");
					$success->add("url","index.php?act=tdisplay&id=".$topicId.'&page=last');
					echo $success->parse();
				}
			}else{
				$errorArray[] = "You cannot attempt to make a different post after this form has been opened.";
			}
		}
		if (!isset($_POST['formsent']) || count($errorArray)){
			if (isset($errorArray) && count($errorArray) > 0){
			?>
			<div class="error">
				<strong>There Were Errors in Your Post</strong>
				<ul>
				<?php
				foreach($errorArray as $error){
					echo '<li>'.$error.'<li>';
				}
				?>
				</ul>
			</div>
			<?php
			}
			?>
			<div class="catrow">
				Post Reply
			</div>
			<div class="contentbox">
				<div class="postBox">
					<form action="?act=topicReply&amp;id=<?php echo $_GET['id']?>" method="post" >
						<p>
							<?php secureForm("topicReply"); ?>
							<input type="hidden" name="formsent" value="1" />
							Message:<br />
							<div class="postArea">
								<textarea name="message" class="post" rows="40" cols="20"><?php if (isset($message)) echo $message ?></textarea>
								<br />
								<div class="buttonwrapper">
									<input type="submit" class="button" name="submit" value="Post Reply" />
								</div>
							</div>
						</p>
					</form>
				</div>
			</div>
			<?php
			$review = new tpl(ROOT_PATH.'themes/Default/templates/postreply_review.php');
			$topicId = $GLOBALS['super']->db->escape(intval($_GET['id']));
			$last10 = "SELECT * FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$topicId." ORDER BY `time_added` DESC LIMIT 10";
			$last10 = $GLOBALS['super']->db->query($last10);
			while($postMess = $GLOBALS['super']->db->fetch_assoc($last10)){
				$user = $GLOBALS['super']->functions->getUser($postMess['user_id'], true);
				$postMessages[] = array(
					'date' => $GLOBALS['super']->functions->formatDate($postMess['time_added']),
					'user' => $user,
					'usercolor' => $user[1],
					'message' => $bbCode->parse($postMess['message']),
				);
			}
			$review->add("topicurl", '?act=tdisplay&id='.$topicId);
			$review->add("POSTS", $postMessages);
			echo $review->parse();
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}	
}
?>