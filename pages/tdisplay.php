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
class tDisplay extends forumPage{
	protected $noTopic;
	protected $currentTopic;
	protected $currentForum;
	private $theme;
	public function __construct($tid = null){
		$this->noTopic = false;
		$this->noForum = false;
		parent::__construct();
		if ($tid)
			$id = $tid;
		else{
			if (!isset($_GET['id'])){
				$this->noTopic = true;
				return;
			}else
				$id = $_GET['id'];
		}
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);
		$topic_sql = "SELECT * FROM ".TBL_PREFIX."topics WHERE `id`=".$id;
		$topic = $GLOBALS['super']->db->query($topic_sql);
		if ($GLOBALS['super']->db->getRowCount($topic) == 0 || !isset($_GET['id'])){
			$this->noTopic = true;
			return;
		}
		$this->currentTopic = $GLOBALS['super']->db->fetch_assoc($topic);
		$forum_sql = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`=".$this->currentTopic['forum_id'];
		$forum = $GLOBALS['super']->db->query($forum_sql);
		if ($GLOBALS['super']->db->getRowCount($forum) == 0){
			$this->noForum = true;
			return;
		}
		$this->currentForum = $GLOBALS['super']->db->fetch_assoc($forum);
		$this->setName($this->currentTopic['name']);
		$this->theme = $GLOBALS['super']->functions->getTheme();
	}
	public function onlineName(){
		return "Viewing Topic: ".$this->getName();
	}
	public function breadCrumb(){
		$parentCrumb = parent::breadCrumb();
		if ($this->noTopic)
			return $parentCrumb;
		$parentArray = explode(",",$this->currentForum['parent_ids']);
		$breadcrumbs = "";
		$breadcrumbs .= $this->recurseNames($parentArray);
		$breadcrumbs .= " ".$GLOBALS['super']->config->crumbSeperator.' <a href="?act=tdisplay&amp;id='.$this->currentTopic['id'].'">'.$this->currentTopic['name']."</a>";
		return $parentCrumb." ".
			$breadcrumbs;
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
		if (!$GLOBALS['super']->user->can("ViewBoard") || !$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View")){
			echo $GLOBALS['super']->user->noPerm();
		}elseif ($this->noTopic){
			$error = new tpl(ROOT_PATH."themes/{$this->theme}/templates/error.php");
			$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
			echo $error->parse();
		}else{
			$updateViews = "UPDATE ".TBL_PREFIX."topics SET `view_count`=`view_count`+1 WHERE `id`=".$this->currentTopic['id'];
			$GLOBALS['super']->db->query($updateViews);
			$GLOBALS['super']->user->viewed_topic($this->currentTopic['id']);
			$topBar = new tpl(ROOT_PATH."themes/{$this->theme}/templates/forum_infobar.php");
			$topBar->add("forum_name",$this->currentTopic['name']);
			$topBar->add("newTopicLink", '');
			$topBar->add("hasDescription", "true");
			$topBar->add("forum_description", "By: ".$GLOBALS['super']->functions->getUser($this->currentTopic['user_id'], true));
			$topBar->add("topic_count", $this->currentTopic['post_count']." Replies");
			$topBar->add("post_count", $this->currentTopic['view_count']+1 ." Views");
			$forumActions = array();
			if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'],"Reply")){
				$forumActions[] = array(
						'url' =>	FORUM_ROOT."index.php?act=topicReply&amp;id=".$this->currentTopic['id'],
						'name'	=>	"Post Reply"
					);
			}
			$topBar->add("LINKS", $forumActions);
			$totalPages = "SELECT count(id) FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$this->currentTopic['id'];
			$totalPages = $GLOBALS['super']->db->query($totalPages);
			$totalPages = $GLOBALS['super']->db->fetch_result($totalPages);
			$totalPages = max(1, $totalPages);
			$postsPerPage = $GLOBALS['super']->user->posts_per_page;
			$pageCount = ceil($totalPages/$postsPerPage);
				if (!isset($_GET['page']))
					$curPage = 1;
				else if($_GET['page'] == "last"){
					$curPage = $pageCount;
				}else{
					$curPage = max(1,intval($_GET['page']));
				}
				$paginationLinks = 'index.php?act=tdisplay&amp;id='.$this->currentTopic['id'].'&amp;page=';
				$topBar->add("page_count", $pageCount);
				list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $postsPerPage);
				$topBar->add("PAGINATION", $paginationArray['PAGES']);
				$topBar->add("curpage", $curPage);
			echo $topBar->parse();
			$bbCode = new bbCode();
			$post_sql = "SELECT * FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$this->currentTopic['id']." ORDER BY `time_added` ASC LIMIT ".$startPost.", ".$postsPerPage;
			$posts = $GLOBALS['super']->db->query($post_sql);
			if ($GLOBALS['super']->db->getRowCount($posts) > 0){
				$topicRow = new tpl(ROOT_PATH."themes/{$this->theme}/templates/postview.php");
				while($post = $GLOBALS['super']->db->fetch_assoc($posts)){
					$poster = $GLOBALS['super']->functions->getUser($post['user_id']);
					$posterName = $poster['formatted'];
					$joinDate = $GLOBALS['super']->functions->formatDate($poster['time_added'], true);
					$postDate = $GLOBALS['super']->functions->formatDate($post['time_added']);
					$editDate = 0;
					if ($post['last_edited'] != 0)
						$editDate = $GLOBALS['super']->functions->formatDate($post['last_edited']);
					$canEdit = false;
					if (($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditOthers") && $GLOBALS['super']->user->id != $post['user_id'])||
						($post['user_id'] == $GLOBALS['super']->user->id && $GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "EditSelf"))){
						$canEdit = true;
					}
					$canDelete = false;
					if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") ||
						($post['user_id'] == $GLOBALS['super']->user->id && $GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf"))){
						$canDelete = true;
					}
					$structure_array['POST'][] = array(
						'id'		=>	$post['id'],
						'avatar'	=>	$GLOBALS['super']->functions->getImage($poster['avatar']),
						'signature'	=>	$bbCode->parse($poster['signature']),
						'poster'	=>	$posterName,
						'postdate'	=>	$postDate,
						'group'		=>	$poster['groupname'],
						'groupcolor'	=>	$poster['color'],
						'joindate'		=>	$joinDate,
						'postcount'	=>	$poster['total_posts']+$poster['total_topics'],
						'message'	=>	$bbCode->parse($post['message']),
						'canEdit'	=>	$canEdit,
						'canDelete'	=>	$canDelete,
						'editDate'	=>	$editDate
						);
				}
				$topicRow->add("POSTS", $structure_array['POST']);
				echo $topicRow->parse();
			}else{
				$error = new tpl(ROOT_PATH."themes/{$this->theme}/templates/error.php");
				$error->add("error_message", "An error has occurred.");
				echo $error->parse();
			}
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>