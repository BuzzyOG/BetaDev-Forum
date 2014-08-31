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
class onlineClass{
	function __construct(){
	}
	function generate(){
		$users = "SELECT * FROM ".TBL_PREFIX."online ORDER BY `time_modified` DESC";
		$users = $GLOBALS['super']->db->query($users);
		$info = array();
		$guestCount = 0;
		$userCount = 0;
		while($user = $GLOBALS['super']->db->fetch_assoc($users)){
			$display = array();
			if ($user["userid"] == "0"){
				$display["name"] = "Guest";
				$guestCount++;
			}else {
				$user2display = $GLOBALS['super']->functions->getUser($user["userid"], true);
				$display["name"] = $user2display;
				$userCount++;
			}
			$display["page"] = $user["page"];
			$display["time"] = $GLOBALS['super']->functions->formatTime($user["time_modified"]);
			$info[] = $display;
		}
		return array($guestCount, $userCount, $info);
	}
}