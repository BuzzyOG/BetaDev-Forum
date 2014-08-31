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
class postTopic extends fDisplay{
	public function __construct(){
		parent::__construct();
		$this->setName("New Topic");
	}
	public function getName(){
		return $this->currentForum['name']." / ".$this->pageName;
	}
	public function onlineName(){
		return $this->pageName.": ".$this->currentForum['name'];
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->pageName;
	}
	public function display(){
		ob_start();
		$errorArray = array();
		if (!$GLOBALS['super']->user->can("ViewBoard") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View") ||
			!$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "NewTopic")
		){
			echo $GLOBALS['super']->user->noPerm();
			return;
		}
		if(isset($_POST['formsent']) && $_POST['formsent'] == "1"){
			if (isSecureForm("postTopic")){
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				if (strlen($subject) <= 4 || strlen($subject) > 35){
					$errorArray[] = "The subject must be greater than 4 characters and less than 35";
				}
				if (strlen($message) <= 10){
					$errorArray[] = "The message must be greater than 10 characters";
				}
				if (!count($errorArray)){
					$id = $GLOBALS['super']->db->escape(intval($_GET['id']));
					$subject = $GLOBALS['super']->db->escape($subject);
					$message = $GLOBALS['super']->db->escape($message);
					$posterid = $GLOBALS['super']->user->id;
					$addTopicSql = "INSERT INTO ".TBL_PREFIX."topics (
								`id` ,
								`name` ,
								`forum_id` ,
								`user_id` ,
								`time_added` ,
								`time_modified`,
								`last_user_id`
								)
								VALUES (
								NULL , '".$subject."', '".$id."','".$posterid."', ".time().", ".time().", '".$posterid."'
								);
								";
					$GLOBALS['super']->db->query($addTopicSql);
					$topicId = $GLOBALS['super']->db->escape($GLOBALS['super']->db->fetch_lastid());
					$addPostSql = "INSERT INTO ".TBL_PREFIX."posts (
								`id` ,
								`topic_id` ,
								`user_id` ,
								`time_added` ,
								`message`
								)
								VALUES (
								NULL , '".$topicId."', '".$posterid."', ".time().", '".$message."')";
					$GLOBALS['super']->db->query($addPostSql);
					$postid = $GLOBALS['super']->db->fetch_lastid();
					$updateForumCountSQL = "UPDATE ".TBL_PREFIX."forums SET `topic_count` = `topic_count`+1, `last_post_id` = '".$postid."' WHERE `id` ='".$id."' LIMIT 1";
					$GLOBALS['super']->db->query($updateForumCountSQL);
					$updateUsersSQL = "UPDATE ".TBL_PREFIX."users SET `total_topics` = `total_topics`+1 WHERE `id`='".$posterid."'";
					$GLOBALS['super']->db->query($updateUsersSQL);
					$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
					$success->add("message","Topic made successfully!");
					$success->add("url","index.php?act=fdisplay&id=".$id);
					echo $success->parse();
				}
			}else{
				$errorArray[] = "You cannot attempt to make a different post after this form has been opened.";
			}
		}
		if (!isset($_POST['formsent']) || count($errorArray)){
			if (isset($errorArray) && count($errorArray)){
			?>
			<div class="error">
				<strong>There Were Errors in Your Topic</strong>
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
				Add Topic
			</div>
			<div class="contentbox">
				<div class="postBox">
					<form action="?act=postTopic&amp;id=<?php echo $_GET['id']?>" method="post" >
						<p>
							<?php secureForm("postTopic"); ?>
							<input type="hidden" name="formsent" value="1" />
							Subject:<br />
							<input class="text" type="text" name="subject" value="<?php if (isset($subject)) echo $subject ?>" size="40" /><br /><br />
							Message:<br />
							<div class="postArea">
								<textarea name="message" class="post" rows="40" cols="20"><?php if (isset($message)) echo $message ?></textarea>
								<br />
								<div class="buttonwrapper">
									<input type="submit" class="button" name="submit" value="Create Thread" />
								</div>
							</div>
						</p>
					</form>
				</div>
			</div>
			<?php
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>