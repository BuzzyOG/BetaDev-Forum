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
error_reporting(E_ALL);
ob_start();
if(get_magic_quotes_runtime()){
    set_magic_quotes_runtime(false);
}
define('DEBUG', 'false');
define('PAGE_PARSE_START_TIME', microtime());
$debug = array();
@session_start();
define("ROOT_PATH", dirname( __FILE__ ) ."/");
define("SEC", 1);
if (!file_exists(ROOT_PATH."config.php")){
	header("Location: install/index.php");
}
require_once(ROOT_PATH.'includes/functions/functions.php');
require_once(ROOT_PATH."includes/classes/super.php");
$super = new super();
date_default_timezone_set($super->user->time_zone);
require_once(ROOT_PATH."includes/classes/tpl.php");
if ($GLOBALS['super']->user->isLogged()){
	$GLOBALS['super']->db->query("UPDATE ".TBL_PREFIX."users SET `last_active` = ".time()." WHERE `id` = ".$super->user->id);	
	date_default_timezone_set($super->user->time_zone);
}
?>