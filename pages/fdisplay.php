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
class fDisplay extends forumPage{
	protected $currentForum;
	protected $noForum;
	protected $escapedId;
	private $theme;
	public function __construct(){
		$this->noForum = false;
		parent::__construct();
		if (empty($this->currentForum)){
			$id = $_GET['id'];
		}else{
			$id = $this->currentForum['id'];
		}
		$id = intval($id);
		$id = $GLOBALS['super']->db->escape($id);
		$this->escapedId = $id;
		$query = "SELECT * FROM ".TBL_PREFIX."forums WHERE `id`=".$id." AND `active`='1'";
		$query = $GLOBALS['super']->db->query($query);
		if ($GLOBALS['super']->db->getRowCount($query) == 0){
			$this->noForum = true;
			return;
		}
		$this->currentForum = $GLOBALS['super']->db->fetch_assoc($query);
		$this->setName($this->currentForum['name']);
		if ($this->currentForum['isRedirect'] == "1"){
			$hits = "UPDATE ".TBL_PREFIX."forums SET `redirectHits`=`redirectHits`+1 WHERE `id` = ".$this->currentForum['id'];
			$GLOBALS['super']->db->query($hits);
			header('Location: '.$this->currentForum['redirectURL']);
		}
		$this->theme = $GLOBALS['super']->functions->getTheme();
	}
	public function getJS(){
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/hoverIntent.js");
		$js[] = array("path" => "includes/js/jquery.tools.min.js");
		$js[] = array("path" => "includes/js/viewforum.js");
		return $js;
	}
	public function onlineName(){
		return "Viewing Forum: ".$this->getName();
	}
	public function breadCrumb(){
		$parentCrumb = parent::breadCrumb();
		if ($this->noForum)
			return $parentCrumb;
		$parentArray = explode(",",$this->currentForum['parent_ids']);
		$breadcrumbs = "";
		$breadcrumbs .= $this->recurseNames($parentArray);
		return $parentCrumb." ".
			$breadcrumbs;
	}
	public function display(){
		ob_start();
		if (!$GLOBALS['super']->user->can("ViewBoard") || !$GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "View")){
			echo $GLOBALS['super']->user->noPerm();
			return;
		}else{
			if ($this->currentForum['is_cat'] == 1){
				$error = new tpl(ROOT_PATH."themes/{$this->theme}/templates/error.php");
				$error->add("error_message", "This forum is marked as a category and does not accept posts.");
				echo $error->parse();
			}elseif ($this->noForum){
				$error = new tpl(ROOT_PATH."themes/{$this->theme}/templates/error.php");
				$error->add("error_message", "You have reached this page in error.<br />Please go back and try again.");
				echo $error->parse();
			}
			require_once(ROOT_PATH.'includes/classes/forum_display.php');
			$displayForum = new showForums($this->escapedId, "Sub-Forums");
			$displayForum->display(false);
			$topBar = new tpl(ROOT_PATH."themes/{$this->theme}/templates/forum_infobar.php");
			$topBar->add("forum_name",$this->currentForum['name']);
			if ($this->currentForum['description'] != "")
				$hasDescription = "true";
			else
				$hasDescription = "false";
			$topBar->add("hasDescription", $hasDescription);
			$topBar->add("forum_description", $this->currentForum['description']);
			$topBar->add("topic_count", $this->currentForum['topic_count']." Topics");
			$topBar->add("post_count", $this->currentForum['post_count']." Replies");
			$forumActions = array();
			if ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'],"NewTopic")){
				$forumActions[] = array(
					'url' =>	"index.php?act=postTopic&amp;id=".$this->currentForum['id'],
					'name'	=>	"New Topic"
				);
			}
			$topBar->add("LINKS", $forumActions);
			$totalPages = "SELECT count(id) as count FROM ".TBL_PREFIX."topics WHERE `forum_id`=".$this->currentForum['id'];
			$totalPages = $GLOBALS['super']->db->query($totalPages);
			$totalPages = $GLOBALS['super']->db->fetch_assoc($totalPages);
			$totalPages = max(1, $totalPages['count']);
			if (!isset($_GET['page']))
				$curPage = 1;
			else
				$curPage = max(1,intval($_GET['page']));
			$paginationLinks = 'index.php?act=fdisplay&amp;id='.$this->currentForum['id'].'&amp;page=';
			$topicsPerPage = $GLOBALS['super']->user->topics_per_page;
			$topBar->add("page_count", ceil($totalPages/$topicsPerPage));
			list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $topicsPerPage);
			$topBar->add("PAGINATION", $paginationArray['PAGES']);
			$topBar->add("curpage", $curPage);
			echo $topBar->parse();
			$topic_sql = "SELECT * FROM ".TBL_PREFIX."topics WHERE `forum_id`=".$this->currentForum['id']." ORDER BY `time_modified` DESC LIMIT ".$startPost.", ".$topicsPerPage;
			$topics = $GLOBALS['super']->db->query($topic_sql);
			if ($GLOBALS['super']->db->getRowCount($topics) > 0){
				$topicRow = new tpl(ROOT_PATH."themes/{$this->theme}/templates/forum_topicrow.php");
				$canMod = false;
				while($topic = $GLOBALS['super']->db->fetch_assoc($topics)){
					$poster = $GLOBALS['super']->functions->getUser($topic['user_id'], true);
					$lastPostTime = $topic['time_modified'];
					$lastPostDate = $GLOBALS['super']->functions->formatDate($lastPostTime);
					$lastposter = $GLOBALS['super']->functions->getUser($topic['last_user_id'], true);
					$preview = $GLOBALS['super']->db->query("SELECT `message` FROM ".TBL_PREFIX."posts WHERE `topic_id`=".$topic['id']." ORDER BY `time_added` ASC LIMIT 1");
					$preview = $GLOBALS['super']->db->fetch_assoc($preview);
					$bbCode = new bbCode();
					$preview = str_replace("\n", " ", $preview['message']);
					$preview = $bbCode->stripBBCode($preview);
					$preview = dotdotdot($preview, 115);
					$image = "themes/{$this->theme}/images/icon_old.gif";
					if ($lastPostTime > $GLOBALS['super']->user->read_time && !$GLOBALS['super']->user->has_viewed_topic($topic['id']))
						$image = "themes/{$this->theme}/images/icon_new.gif";
					$canDelete = ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteOthers") && $topic['user_id'] != $GLOBALS['super']->user->id)
								|| ($GLOBALS['super']->user->can("Forum".$this->currentForum['id'], "DeleteSelf") && $topic['user_id'] == $GLOBALS['super']->user->id);
					if ($canDelete && !$canMod)
						$canMod = true;
					$structure_array['TOPICS'][] = array(
						'id'	=>	$topic['id'],
						'image'	=> $image,
						'link'	=>	'index.php?act=tdisplay&amp;id='.$topic['id'],
						'name'	=>	$topic['name'],
						'starter'	=>	$poster,
						'replies'	=>	$topic['post_count'],
						'views'		=>	$topic['view_count'],
						'lastpostdate'	=>	$lastPostDate,
						'lastposter'	=> $lastposter,
						'preview'		=> $preview,
						'canDelete'		=> $canDelete
						);
				}
				$topicRow->add("TOPICS", $structure_array['TOPICS']);
				$topicRow->add("canMod", $canMod);
				echo $topicRow->parse();
			}else{
				$error = new tpl(ROOT_PATH."themes/{$this->theme}/templates/error.php");
				$error->add("error_message", "This forum currently has no topics.");
				echo $error->parse();
			}
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>