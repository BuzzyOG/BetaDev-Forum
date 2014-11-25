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
if (DEBUG == 'true'){
	echo "<div>";
	$time_start = explode(' ', PAGE_PARSE_START_TIME);
	$time_end = explode(' ', microtime());
	$parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
	echo '<div align="center"><span class="smallText">Current Parse Time: <b>' . $parse_time . ' s</b> with <b>' . sizeof($debug['QUERIES']) . ' queries</b></span></div>';
	echo '<b>QUERY DEBUG:</b> ';
	$super->functions->print_array($debug['QUERIES']);
	$super->functions->print_array($debug['TIME']);
	echo '<hr>';
	echo '<b>SESSION:</b> ';
	$super->functions->print_array($_SESSION);
	echo '<hr>';
	echo '<b>COOKIE:</b> ';
	$super->functions->print_array($_COOKIE);
	echo '<b>POST:</b> ';
	$super->functions->print_array($_POST);
	echo '<hr>';
	echo '<b>GET:</b> ';
	$super->functions->print_array($_GET);
	echo '<hr>';
	echo '<b>MEMORY:</b> ';
	$super->functions->print_array($debug['memory']);
	echo "</div>";
}
unset($debug);
?>