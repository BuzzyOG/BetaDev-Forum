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
require_once(ROOT_PATH.'includes/classes/recaptchalib.php');
class Register extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Register");
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	public function display(){
		ob_start();
		$display = true;
		if (isset($_GET['do']) && $_GET['do'] == "register" && $_POST['formsent'] == "1"){
			$errors = array();
			$username = $_POST['username'];
			$username= $GLOBALS['super']->db->escape($username);
			if (!ctype_alnum($username))
				$errors[] = "Usernames must contain only alphanumerics.";
			if (strlen($username) < 5 || strlen($username) > 20)
				$errors[] = "Usernames must be 5-20 characters.";
			$checkUser = "SELECT * FROM ".TBL_PREFIX."users WHERE `username`='".$username."'";
			$checkUser = $GLOBALS['super']->db->query($checkUser);
			$checkUser = $GLOBALS['super']->db->getRowCount($checkUser);
			if ($checkUser != 0)
				$errors[] = "That username is already taken";
			$displayname = $_POST['displayname'];
			$displayname=$GLOBALS['super']->db->escape($displayname);
			if (!ctype_print($displayname))
				$errors[] = "Displaynames may not contain special characters.";
			$checkDisplay = "SELECT * FROM ".TBL_PREFIX."users WHERE `displayname`='".$displayname."'";
			$checkDisplay = $GLOBALS['super']->db->query($checkDisplay);
			$checkDisplay = $GLOBALS['super']->db->getRowCount($checkDisplay);
			if ($checkDisplay != 0)
				$errors[] = "That displayname is already taken";
			$pass1 = $_POST['password'];
			$pass2 = $_POST['password2'];
			if ($pass1 != $pass2)
				$errors[] = "Your passwords do not match";
			elseif (strlen($pass1) <= 5)
				$errors[] = "Your password must be greater than 5 characters";
			$email = $_POST['email'];
			if (!preg_match("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/",$email))
				$errors[] = "You must enter a valid email address";
			$resp = recaptcha_check_answer ($_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
			if (!$resp->is_valid)
				$errors[] ="The CAPTCHA wasn't entered correctly";
			if (count($errors) == 0){
				$display = false;
				$pass = encrypt_password($pass1);
				$pass = $GLOBALS['super']->db->escape($pass);
				$email = $GLOBALS['super']->db->escape($email);
				$addUserSql = 
				"INSERT INTO ".TBL_PREFIX."users (
					`username`,
					`displayname`,
					`password`,
					`email`,
					`time_added`,
					`group_id`,
					`theme_id`,
					`posts_per_page`,
					`topics_per_page`,
					`read_time`,
					`show_online`
					)
					VALUES (
					'".$username."',
					'".$displayname."',
					'".$pass."',
					'".$email."',
					'".time()."',
					'2',
					'1',
					'".$GLOBALS['super']->config->postsPerPage."',
					'".$GLOBALS['super']->config->topicsPerPage."',
					'".time()."',
					'1'
					);
					";
				$GLOBALS['super']->db->query($addUserSql);
				$error = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
				$str = "Registered Successfully!";
				$extra = "You may now login with your new account".
				$error->add("message", $str);
				$error->add("extramessage", $extra);
				$error->add("url", FORUM_ROOT);
				$error->parse();
			}
		}
		if (isset($errors) && count($errors) > 0){
			$error = new tpl(ROOT_PATH.'themes/Default/templates/error.php');
			$error->add("error_message", $errors);
			$error->parse();
		}
		if ($display){
			$register = new tpl(ROOT_PATH.'themes/Default/templates/register.php');
			$register->add("CAPTCHAHTML", recaptcha_get_html());
			echo $register->parse();
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
?>