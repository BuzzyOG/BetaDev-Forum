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
require_once('init.php');
define("INCLUDED", 1);
require_once(ROOT_PATH."includes/classes/pageClass.php");
if (isset($_GET['act']))
	$current_page = $_GET['act'];
else
	$current_page = "index";
$pageClass = new forumPage();	
switch($current_page){
	case "postTopic":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/postTopic.php');
		$pageClass = new postTopic();
		break;
	case "topicReply":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/topicReply.php');
		$pageClass = new topicReply();
		break;
	case "tdisplay":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/tdisplay.php');
		$pageClass = new tDisplay();
		break;
	case "fdisplay":
		require_once(ROOT_PATH.'pages/fdisplay.php');
		$pageClass = new fDisplay();
		break;
	case "calendar":
		require_once(ROOT_PATH.'pages/calendar.php');
		$pageClass = new Calendar();
		break;
	case "member":
		require_once(ROOT_PATH.'pages/profile.php');
		$pageClass = new Profile();
		break;
	case "memberlist":
		require_once(ROOT_PATH.'pages/memberlist.php');
		$pageClass = new memberList();
		break;
	case "login":
		require_once(ROOT_PATH.'pages/login.php');
		$pageClass = new Login();
		break;
	case "register":
		require_once(ROOT_PATH.'pages/register.php');
		$pageClass = new Register();
		break;
	case "search":
		require_once(ROOT_PATH.'pages/search.php');
		$pageClass = new Search();
		break;
	case "online":
		require_once(ROOT_PATH.'pages/online.php');
		require_once(ROOT_PATH."includes/classes/online.php");
		$onlineHelper = new onlineClass();
		$pageClass = new Online();
		break;
	case "markread":
		$GLOBALS['super']->user->read_time = time();
		$GLOBALS['super']->user->read_topics = "";
		header("Location: ".FORUM_ROOT);
		die();
		break;
	case "editPost":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/editPost.php');
		$pageClass = new editPost(); 
		break;
	case "deletePost":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/deletePost.php');
		$pageClass = new deletePost(); 
		break;
	case "deleteTopic":
		require_once(ROOT_PATH.'pages/tdisplay.php');
		require_once(ROOT_PATH.'pages/fdisplay.php');
		require_once(ROOT_PATH.'pages/deleteTopic.php');
		$pageClass = new deleteTopic(); 
		break;
	case "controlPanel":
		require_once(ROOT_PATH.'pages/controlpanel.php');
		$pageClass = new ControlPanel();
		break;
	default:
		require_once(ROOT_PATH.'pages/main.php');
		$pageClass = new Main();
		break;
}
$super->user->updateActive($pageClass);
$styleSheet = $super->functions->getTheme();
$styleDir = 'themes/'.$styleSheet;
$header = new tpl(ROOT_PATH.$styleDir.'/templates/header.php');
$header->add("TITLE", $pageClass->getTitle());
$isIphone = false;
$isIpod = false;
if (isset($_SERVER['HTTP_USER_AGENT'])){
	$isIphone = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone") !== false;
	$isIpod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod") !== false;
}
$css = $pageClass->getCSS();
if (isset($_SERVER['HTTP_USER_AGENT']) && ($isIpod || $isIphone) && file_exists($styleDir.'/iPhone.css')){
	$css[] = array('path' => $styleDir.'/iPhone.css');
}
$header->add("STYLESHEETS", $css);
$header->add("STYLESHEET", $styleSheet);
$js = $pageClass->getJS();
$header->add("SCRIPTS", $js);
echo $header->parse();
$nav = new tpl(ROOT_PATH.$styleDir.'/templates/navbar.php');
$nav->add("act", $current_page);
$menu[] = array("act" => "home", "name" => "Home", "url" => "index.php");
if ($super->user->can("ViewAdmin")){
	$menu[] = array("act" => "admin", "name" => "Admin", "url" => "administration/index.php");
}
$menu[] = array("act" => "search", "name" => "Search", "url" => "index.php?act=search");
$menu[] = array("act" => "calendar", "name" => "Calendar", "url" => "index.php?act=calendar");
if (!$super->user->isLogged()){
	$menu[] = array("act" => "login", "name" => "Login", "url" => "index.php?act=login");
	$menu[] = array("act" => "register", "name" => "Register", "url" => "index.php?act=register");
}else
	$menu[] = array("act" => "logout", "name" => "Logout", "url" => "index.php?act=login&amp;do=out");
$nav->add("MENU", $menu);
echo $nav->parse();
$userbar = new tpl(ROOT_PATH.$styleDir.'/templates/userbar.php');
$username = $super->user->username;
$displayname = $super->user->displayname;
if (isset($displayname) && $displayname != "")
	$user_display = $username." (".$displayname.")";
else
	$user_display = $username;
$userbar->add("BREADCRUMBS", $pageClass->breadCrumb());
$userbar->add("LOGGED", $super->user->isLogged());
$userbar->add("USERID", $super->user->id);
$userbar->add("USERNAME_DISPLAYNAME", $user_display);
echo $userbar->parse();
echo $pageClass->display();
$footer = new tpl(ROOT_PATH.$styleDir.'/templates/footer.php');
$curTime = date("g:i A");
$footer->add("time", $curTime);
$usersOnPage = "SELECT * FROM ".TBL_PREFIX."online WHERE page='".$GLOBALS['super']->db->escape($pageClass->onlineName())."' AND userid != 0";
$usersOnPage = $super->db->query($usersOnPage);
$guestsOnPage = "SELECT count(*) as count FROM ".TBL_PREFIX."online WHERE `page`='".$GLOBALS['super']->db->escape($pageClass->onlineName())."' AND `userid`=0";
$guestsOnPage = $super->db->query($guestsOnPage);
$guestsOnPage = $super->db->fetch_assoc($guestsOnPage);
$pageUsers = array();
while($user = $super->db->fetch_assoc($usersOnPage)){
	$pageUsers[] = $super->functions->getUser($user['userid'], true);
}
$footer->add("UsersOnPage", $pageUsers);
$footer->add("GuestsOnPage", $guestsOnPage['count']);
$footer->add("VERSION", $super->config->forumVersion);
echo $footer->parse();
require_once('debug.php');
?>