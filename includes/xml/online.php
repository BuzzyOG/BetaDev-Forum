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
require_once('../../init.php');
define("INCLUDED", 1);
require_once(ROOT_PATH."includes/classes/online.php");
$onlineHelper = new onlineClass();
require_once(ROOT_PATH."includes/classes/pageClass.php");
require_once(ROOT_PATH.'pages/online.php');
$pageClass = new Online();
$super->user->updateActive($pageClass);
list($guestCount, $userCount, $info) = $onlineHelper->generate();
ob_end_clean();
$dom = new DOMDocument("1.0");
header("Content-Type: text/xml");
$root = $dom->createElement("onlinelist");
$dom->appendChild($root);
$guests = $dom->createElement("guestCount",$guestCount);
$root->appendChild($guests);
$users = $dom->createElement("userCount", $userCount);
$root->appendChild($users);
foreach($info as $user){
	$usernode = $dom->createElement("user");
	$root->appendChild($usernode);
	$name = $dom->createElement("name",$user["name"]);
	$usernode->appendChild($name);
	$page = $dom->createElement("page", $user["page"]);
	$usernode->appendChild($page);
	$time = $dom->createElement("time", $user["time"]);
	$usernode->appendChild($time);
}
echo $dom->saveXML();
?>