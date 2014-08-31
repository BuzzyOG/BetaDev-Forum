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
class Search extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Search");
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	public function display(){
		ob_start();
		if (!isset($_GET['q']) || $_GET['q'] == ""){
			$searchForm = new tpl(ROOT_PATH.'themes/Default/templates/search_form.php');
			$searchForm->parse();
		}else{
			$query = $GLOBALS['super']->db->escape($_GET['q']);
			$results = array();
			function getSearch($query, $startlimit=false, $endlimit=false){	
				$extra = "";
				if ($startlimit!==false && $endlimit!==false){
					$extra = "LIMIT ".$startlimit.", ".$endlimit;
				}
				return "SELECT lower('topic') AS type,
				`id`,`name`, '' AS displayname, `user_id`,`post_count`,`time_added`, '' AS message, `time_modified`, `last_user_id`,
				MATCH(name) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."topics
				WHERE MATCH(name) AGAINST('".$query."')
				UNION
				SELECT lower('post') AS type,
					p.id, t.name, '', p.user_id, p.topic_id, p.time_added, p.message AS message, '', '',
				MATCH(message) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."posts AS p
				INNER JOIN ".TBL_PREFIX."topics AS t
				ON p.topic_id=t.id
				WHERE MATCH(message) AGAINST('".$query."')
				UNION
				SELECT lower('user') AS type,
					`id`, `username`, `displayname`, `avatar`, '', `time_added`, '', '', '',
				MATCH(username) AGAINST('".$query."') AS score
				FROM ".TBL_PREFIX."users
				WHERE MATCH(username) AGAINST('".$query."')         
				ORDER BY score DESC ".$extra;
			}
			$numRows = $GLOBALS['super']->db->getRowCount($GLOBALS['super']->db->query(getSearch($query)));
			$totalPages = max(1, $numRows);
			if (!isset($_GET['page']))
				$curPage = 1;
			else
				$curPage = max(1,intval($_GET['page']));
			$paginationLinks = 'index.php?act=search&amp;q='.urlencode($query).'&amp;page=';
			$topicsPerPage = $GLOBALS['super']->user->topics_per_page;
			list($paginationArray, $startPost) = $GLOBALS['super']->functions->doPagination($totalPages, $curPage, $paginationLinks, $topicsPerPage);
			$matches = $GLOBALS['super']->db->query(getSearch($query, $startPost, $topicsPerPage));
			if ($GLOBALS['super']->db->getRowCount($matches) > 0){
				while($match = $GLOBALS['super']->db->fetch_assoc($matches)){
					$results[] = $match;
				}
				$searchResults = new tpl(ROOT_PATH.'themes/Default/templates/searchresults.php');
				$searchResults->add("numResults",$numRows);
				$searchResults->add("QUERY", stripslashes($query));
				$searchResults->add("RESULTS", $results);
				$searchResults->add("PAGINATION", $paginationArray['PAGES']);
				$searchResults->add("curpage", $curPage);
				$searchResults->add("page_count", ceil($totalPages/$topicsPerPage));
				$searchResults->parse();
			}else{
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "No results found for your search keywords.<br /> Please broaden your search, or if your search is too broad, you may need to be more specific.");
				echo $error->parse();
			}
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>