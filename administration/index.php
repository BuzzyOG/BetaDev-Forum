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
require_once('../init.php');
define("INCLUDED", 1);
if (!$super->user->can("ViewAdmin")){
	header("Location: ../");
	die();
}
define("ADMIN_PATH", dirname( __FILE__ ) ."/");
require_once(ADMIN_PATH."includes/classes/adminPage.php");
if (isset($_GET['act']))
	$curr_act = $_GET['act'];
else{
	$curr_act = "home";
}
switch ($curr_act){
	case "forums":
	    require_once(ADMIN_PATH.'pages/forums.php');
	    $pageClass = new Forums();
	    break;
	case "config":
	    require_once(ADMIN_PATH.'pages/configuration.php');
	    $pageClass = new Configuration();
	    break;
	case "users":
		require_once(ADMIN_PATH.'pages/users.php');
		$pageClass = new Users();
		break;
	case "groups":
		require_once(ADMIN_PATH.'pages/groups.php');
		$pageClass = new Groups();
		break;
	default:
	    require_once(ADMIN_PATH.'pages/home.php');
	    $pageClass = new Home();
	    break;
}
$super->user->updateActive($pageClass);
$curr_sub = "";
if (isset($_GET['sub']))
	$curr_sub = $_GET['sub'];
$curr_sub = ucwords($curr_sub);
$curr_sub = $pageClass->setSub($curr_sub);
$mainitems[] = array('text' => 'Home', 'act' => 'home', 'redirect' => 'No');
$mainitems[] = array('text' => 'Forums', 'act' => 'forums', 'redirect' => 'No');
//$mainitems[] = array('text' => 'Users', 'act' => 'users', 'redirect' => 'No');
//$mainitems[] = array('text' => 'Groups', 'act' => 'groups', 'redirect' => 'No');
$mainitems[] = array('text' => 'Configuration', 'act' => 'config', 'redirect' => 'No');
$subitems = $pageClass->children();
$header = new tpl(ADMIN_PATH.'display/templates/header.php');
$header->add("title", $pageClass->getTitle());
$header->add("stylesheets", $pageClass->getCSS());
$header->add("scripts", $pageClass->getJS());
$header->add("curact", $curr_act);
$header->add("cursub", $curr_sub);
$header->add("mainmenu", $mainitems);
$header->add("submenu", $subitems);
echo $header->parse();
echo $pageClass->display();
$footer = new tpl(ADMIN_PATH.'display/templates/footer.php');
echo $footer->parse();
?>