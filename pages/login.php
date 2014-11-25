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
class Login extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Login");
		if (isset($_GET['do']) && $_GET['do'] == "login" && isset($_POST['username']) && isset($_POST['password'])){
			$username = $_POST['username'];
			$password = $_POST['password'];
			$remember = (isset($_POST['remember']) && $_POST['remember'] == "1");
			$GLOBALS['super']->user->checkLogin($username, $password, $remember);
			if ($GLOBALS['super']->user->isLogged()){
				$updatequery = "UPDATE ".TBL_PREFIX."users SET `last_login` = ".time()." WHERE `id` = ".$GLOBALS['super']->user->id;
				$GLOBALS['super']->db->query($updatequery);
			}
		}elseif (isset($_GET['do']) && $_GET['do'] == "out"){
			$GLOBALS['super']->user->logout();
		}
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	public function display(){
		ob_start();
		$display = true;
		if (isset($_GET['do']) && $_GET['do'] == "login"){
			if ($GLOBALS['super']->user->isLogged()){
				$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
				$success->add("message", "Logged In Successfully");
				if (!isset($_SESSION['loginredirect']))
					$redirect = "index.php";
				else
					$redirect = $_SESSION['loginredirect'];
				$success->add("url", $redirect);
				$display = false;
				echo $success->parse();	
				unset($_SESSION['loginredirect']);
			}else{
				$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
				$error->add("error_message", "Incorrect Username or Password");
				echo $error->parse();
			}
		}elseif(isset($_GET['do']) && $_GET['do'] == "out"){
			$display = false;
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message", "Logged Out Successfully");
			$success->add("url", "index.php");
			echo $success->parse();
		}
		if ($display){
			if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['loginredirect'])){
				$_SESSION['loginredirect'] = $_SERVER['HTTP_REFERER'];
			}
			$login = new tpl(ROOT_PATH.'themes/Default/templates/login.php');
			echo $login->parse();
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>