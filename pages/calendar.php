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
class Calendar extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Calendar");
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	public function display(){
		ob_start();
		$actualMonth = date('m');
		$actualYear = date('Y');
		$actualDay = date('d');
		if (isset($_GET['m']))
			$curMonth = intval($_GET['m']);
		else
			$curMonth = $actualMonth;
		if (isset($_GET['y']))
			$curYear = intval($_GET['y']);
		else
			$curYear = $actualYear;
		$firstDayOfMonth = mktime(0,0,0,$curMonth, 1, $curYear); 
		$monthName = date('F', $firstDayOfMonth); 
		$firstDayName = date('D', $firstDayOfMonth);
		$curDay = mktime(0,0,0,$curMonth, 1, $curYear); 
		switch($firstDayName){
			case "Sun": $blank = 0; break;
			case "Mon": $blank = 1; break;
			case "Tue": $blank = 2; break;
			case "Wed": $blank = 3; break;
			case "Thu": $blank = 4; break;
			case "Fri": $blank = 5; break;
			case "Sat": $blank = 6; break;
		}
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $curMonth, $curYear);
		$lastDayOfMonth = mktime(0,0,0,$curMonth, $daysInMonth, $curYear); 
		if ($curMonth == 1){
			$prevMonth = 12;
			$prevYear = $curYear - 1;
		}else{
			$prevMonth = $curMonth-1;
			$prevYear = $curYear;
		}
		$daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
		$dayCounter = 1;
		$day_num = 1;
		if ($curMonth == 1){
			$linkPrevM = 12;
			$linkPrevY = $curYear - 1;
		}else{
			$linkPrevM = $curMonth - 1;
			$linkPrevY = $curYear;
		}
		if ($curMonth == 12){
			$linkNextM = 1;
			$linkNextY = $curYear + 1;
		}else{
			$linkNextM = $curMonth + 1;
			$linkNextY = $curYear;
		}
		?>
		<table class="calendar" border="0">
			<caption class="catrow"><a href="index.php?act=calendar&amp;m=<?php echo $linkPrevM?>&amp;y=<?php echo $linkPrevY?>">&laquo;</a> <?php echo $monthName?> <a href="index.php?act=calendar&amp;m=<?php echo $linkNextM?>&amp;y=<?php echo $linkNextY?>">&raquo;</a></caption>
			<tr>
				<th>Sun</th>
				<th>Mon</th>
				<th>Tue</th>
				<th>Wed</th>
				<th>Thu</th>
				<th>Fri</th>
				<th>Sat</th>
			</tr>
		<?php
		echo "<tr>";
		for ($i = $blank-1; $i >= 0; $i--){
			echo '<td class="inactive">
			'.($daysInPrevMonth-$i).'
			</td>';
			$dayCounter++;
		} 
		$GLOBALS['super']->db->query("SET time_zone = '".date_default_timezone_get()."'");
        $start = mktime(0,0,0,$curMonth,0,$curYear);
        $end = mktime(0,0,0,$curMonth,$daysInPrevMonth,$curYear);
		$topicsQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS topicCount
		FROM ".TBL_PREFIX."topics
		WHERE time_added BETWEEN '".$start."' AND '".$end."'
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$topicsQuery = $GLOBALS['super']->db->query($topicsQuery);
		$postsQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS postCount
		FROM ".TBL_PREFIX."posts
		WHERE time_added BETWEEN '".$start."' AND '".$end."'                      
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$postsQuery = $GLOBALS['super']->db->query($postsQuery);
		$usersQuery = "
		SELECT DATE(FROM_UNIXTIME(time_added)) AS date , count(*) AS userCount
		FROM ".TBL_PREFIX."users
		WHERE time_added BETWEEN '".$start."' AND '".$end."'                      
		GROUP BY DATE(FROM_UNIXTIME(time_added))
		ORDER BY DATE(FROM_UNIXTIME(time_added))";
		$usersQuery = $GLOBALS['super']->db->query($usersQuery);
		$perDayArray = array();
		while($day = $GLOBALS['super']->db->fetch_assoc($topicsQuery)){
			$perDayArray[$day['date']]["Topics"] = $day['topicCount'];
		}
		while($day = $GLOBALS['super']->db->fetch_assoc($postsQuery)){
			if (isset($perDayArray[$day['date']]["Topics"]))
				$daysTopics = $perDayArray[$day['date']]["Topics"];
			else 
				$daysTopics = 0;
			$perDayArray[$day['date']]["Reply"] = $day['postCount']-$daysTopics;
		}
		while($day = $GLOBALS['super']->db->fetch_assoc($usersQuery)){
			$perDayArray[$day['date']]["Users"] = $day['userCount'];
		}
		while ($day_num <= $daysInMonth){
			$mysqlFormat = date('Y-m-d', $curDay);
			if (key_exists($mysqlFormat, $perDayArray)){
				if(isset($perDayArray[$mysqlFormat]["Topics"]) && $perDayArray[$mysqlFormat]["Topics"] > 0){
					$plural = plural($perDayArray[$mysqlFormat]["Topics"]);
					$topicsToday = "<li>".$perDayArray[$mysqlFormat]["Topics"]." Topic".$plural."</li>";
				}else
					$topicsToday = "";
				if(isset($perDayArray[$mysqlFormat]["Reply"]) && $perDayArray[$mysqlFormat]["Reply"] > 0){
					$plural = plural($perDayArray[$mysqlFormat]["Reply"],true);
					$postsToday = "<li>".$perDayArray[$mysqlFormat]["Reply"]." Repl".$plural."</li>";
				}else
					$postsToday = "";
				if(isset($perDayArray[$mysqlFormat]["Users"]) &&  $perDayArray[$mysqlFormat]["Users"] > 0){
					$plural = plural($perDayArray[$mysqlFormat]["Users"]);
					$usersToday = "<li>".$perDayArray[$mysqlFormat]["Users"]." User".$plural."</li>";
				}else
					$usersToday = "";
				
			}else{
					$topicsToday = "";
					$postsToday = "";
					$usersToday = "";
			}
			$curDay = mktime(null, null, null, $curMonth, $day_num+1, $curYear);
			$class = "";
			if ($actualMonth == $curMonth && $actualYear == $curYear && $day_num == $actualDay){
				$class = ' class="current"';
			}
			echo '<td'.$class.'>
				<ul>
				<li class="daynum">'.$day_num.'</li>
				'.$topicsToday.
				$postsToday.
				$usersToday.'
				</ul>
				</td>';
			$day_num++;
			$dayCounter++;
			if ($dayCounter > 7){
				echo "</tr><tr>";
				$dayCounter = 1;
			}
		}
		$day_num = 1;
		while($dayCounter >1 && $dayCounter <= 7){
			echo '<td class="inactive">'. $day_num.'</td>';
			$day_num++;
			$dayCounter++;
		}
		echo "</tr>";
		echo "</table>";
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>