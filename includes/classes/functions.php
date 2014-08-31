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
class functions{
	private $super;
	function __construct($super){
		$this->super = $super;
	}
	function print_array ($array, $exit = false){
		print "<pre>";
		print_r ($array);
		print "</pre>";
		if ($exit) exit();
	}
	function doPagination($totalPosts, $curPage, $link, $topicsPerPage, $horizonalDistance=2){
		$totalPages = ceil($totalPosts/$topicsPerPage);
		$startPost = ($curPage-1)*$topicsPerPage;
		$firstPaginationPage = max(1,$curPage - $horizonalDistance);
		$lastPaginationPage = min($totalPages,$curPage+$horizonalDistance);
		if ($firstPaginationPage > 1){
			$pagination['PAGES'][] = array('value' => '&laquo;', 'link' => $link."1");
		}
		if ($curPage != 1){
			$pagination['PAGES'][] = array('value' => '&lt;', 'link' => $link.($curPage-1));
		}
		if ($curPage - 10 > 1){
			$pagination['PAGES'][] = array('value' => $curPage - 10, 'link' => $link.($curPage-10));
		}
		for ($i = $firstPaginationPage; $i <= $lastPaginationPage; $i++){
			$pagination['PAGES'][] = array('value' => $i, 'link' => $link.$i);
		}
		if ($totalPages - 10 > $curPage){
			$pagination['PAGES'][] = array('value' => $curPage + 10, 'link' => $link.($curPage+10));
		}
		if ($curPage != $totalPages){
			$pagination['PAGES'][] = array('value' => '&gt;', 'link' => $link.($curPage+1));
		}
		if ($lastPaginationPage < $totalPages){
			$pagination['PAGES'][] = array('value' => '&raquo;', 'link' => $link.$totalPages);
		}
		return array($pagination,$startPost);
	}
	function formatDate($date, $short = false){
		$dateformat = $this->super->user->date_format;
		$timeformat = $this->super->user->time_format;
		$extra = "";
		$time = time();
		$diff = $time - $date;
		if($diff < 60){
			return "Less than 1 minute ago";
		}else if($diff < 60 * 60){
			$result = floor($diff/60);
			$plural = plural($result);
			if (!$short)
				$extra = " (".$result." minute".$plural." ago)";
			return date($timeformat,$date).$extra;
		}else if($diff < 60 * 60 * 24){
			$result = floor($diff/(60*60));
			$plural = plural($result);
			if (date($dateformat,$date) != date($dateformat,$time)){
				$text = date($dateformat,$date);
			}else{
				$text = date($timeformat,$date);
			}
			if (!$short)
				$extra = " (".$result." hour".$plural." ago)";
			return $text.$extra;
		}else if($diff < 60 * 60 * 24 * 30){
			$result = floor($diff/(60*60*24));
			$plural = plural($result);
			$extra = "";
			if (!$short)
				$extra = " ($result day$plural ago)";

			return date($dateformat,$date).$extra;
		}
		$result = floor($diff/(60*60*24*30));
		$plural = plural($result);
		if (!$short)
		$extra = " ($result month$plural ago)";
		return date($dateformat,$date).$extra;
	}
	function formatTime($date){
		$time = time();
		$diff = $time - $date;
		if ($diff == 0){
			return "An instant ago";
		}elseif($diff < 60){
			$plural = plural($diff);
			return $diff." second".$plural." ago";
		}else if($diff < 60 * 60){
			$result = floor($diff/60);
			$plural = plural($result);
			return $result." minute".$plural." ago";
		}else if($diff < 60 * 60 * 24){
			$result = floor($diff/(60*60));
			$plural = plural($result);
			return $result." hour".$plural." ago)";
		}
	}
	function fileSizeFormat($filesize){
		$bytes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
		if ($filesize < 1024)
			$filesize = 1;
		for ($i = 0; $filesize > 1024; $i++)
			$filesize /= 1024;
		$file_size_info['size'] = ceil($filesize);
		$file_size_info['type'] = $bytes[$i];
		return $file_size_info;
	}
	function userId2Display($id){
		$id = $this->super->db->escape($id);
		$query = "SELECT u.username, u.displayname, g.color, g.name FROM ".TBL_PREFIX."users AS u INNER JOIN ".TBL_PREFIX."groups AS g ON u.group_id=g.id WHERE u.id=".$id;
		$result = $this->super->db->query($query);
		$user = $this->super->db->fetch_assoc($result);
		$name = $this->user2display($user['username'], $user['displayname']);
		return array(
			$name,
			$user['color'],
			'<a title="'.$user['name'].'" href="?act=member&amp;id='.$id.'"><span style="color:'.$user["color"].'">'.$name.'</span></a>'
			);
	}
	function user2display($username, $displayname){
		if ($displayname != ""){
			return $displayname;
		}
		return $username;
	}
	function getImage($id){
		$id = $this->super->db->escape(intval($id));
		$query = "SELECT * FROM ".TBL_PREFIX."images WHERE `id` = ".$id;
		$query = $this->super->db->query($query);
		if ($this->super->db->getRowCount($query) > 0){
			$row = $this->super->db->fetch_assoc($query);
			$image = array(
				"name"	=>	$row['name'],
				"url"	=>	$row['url']
				);
			return $image;
		}else{
			return false;
		}
	}
	function formatUser($group, $color, $id, $username, $displayname, $both=false){
		if ($both){
			if (isset($displayname) && $displayname != "")
				$display = $username." (".$displayname.")";
			else
				$display = $username;
		}
		else
			$display = $this->user2display($username, $displayname);
		return '<a class="username" style="color:'.$color.'" title="'.$group.'" href="?act=member&amp;id='.$id.'">'.$display.'</a>';
	}
	private $usersDisplayed = array();
	function getUser($id, $formatted = false, $both = false){
		$id = intval($id);
		$id = $this->super->db->escape($id);
		if (!key_exists($id,$this->usersDisplayed)){
			$query = "SELECT u.*, g.name AS groupname, g.color
				FROM ".TBL_PREFIX."users AS u
				INNER JOIN ".TBL_PREFIX."groups AS g
				ON u.group_id=g.id
				WHERE u.id=".$id;
			$query = $this->super->db->query($query);
			if ($this->super->db->getRowCount($query)){
				$user = $this->super->db->fetch_assoc($query);
				$user['formatted'] = $this->formatUser($user['groupname'], $user["color"], $user['id'], $user['username'],$user['displayname'], $both);
			$this->usersDisplayed[$user['id']] = $user;
			}
		}
		if ($formatted)
			return $this->usersDisplayed[$id]["formatted"];
		else
			return $this->usersDisplayed[$id];
	}
}
?>