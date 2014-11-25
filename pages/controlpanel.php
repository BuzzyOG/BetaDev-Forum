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
class ControlPanel extends forumPage {
	public function __construct(){
		parent::__construct();
		$this->setName("User Control Panel");
	}
	public function breadCrumb()
	{
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	
	function getJS()
	{
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/controlPanel.js");
		return $js;
	}
	public function display()
	{
		ob_start();
		
		if (!$GLOBALS['super']->user->can("ViewBoard") || !$GLOBALS['super']->user->isLogged())
		{
			echo $GLOBALS['super']->user->noPerm();
		}
		else 
		{
			if (!isset($_GET['page']))
				$action = "acct";
			else
				$action = $_GET['page'];
			$errors = null;
			if (isset($_POST['submit']) && $_POST['submit'] == "Submit" && isSecureForm("controlPanel"))
			{
				$errors = $this->validateForm();
			}
			$navitems[] = array("name"=>"Account Settings", "act"=>"acct");
			$navitems[] = array("name"=>"Personal Settings", "act"=>"personal");
			$navitems[] = array("name"=>"Display Settings", "act"=>"display");
			$navitems[] = array("name"=>"Privacy Settings", "act"=>"privacy");
			$content = new tpl(ROOT_PATH.'themes/Default/templates/controlpanel.php');
			$content->add("ERRORS", $errors);
				
			$content->add("NAV", $navitems);
			$content->add("ACT", $action);
				
			$content->add("USERINFO", $GLOBALS['super']->user);
				
			$content->parse();
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	private function validateForm(){
		$errors = array();
		if ($_POST['action'] == "acct"){
			$displayname = (isset($_POST['displayname'])) ? $_POST['displayname'] : "";
			$email = (isset($_POST['email'])) ? $_POST['email'] : "";
			$signature = (isset($_POST['signature'])) ? $_POST['signature'] : "";
			$oldpass = (isset($_POST['old_paw'])) ? $_POST['old_paw'] : "";
			$newpass = (isset($_POST['new_paw'])) ? $_POST['new_paw'] : "";
			if ($displayname != $GLOBALS['super']->user->displayname){
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `displayname`='".$displayname."'");
				if ($GLOBALS['super']->db->getRowCount($query) > 0){
					$errors[] = "That display name is taken.";
				}
			}
			if ($email != $GLOBALS['super']->user->email){
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `email`='".$email."'");
				if ($GLOBALS['super']->db->getRowCount($query) > 0){
					$errors[] = "That E-mail is already binded to another account.";
				}
			}
			$changePass = false;
			$pass = encrypt_password($oldpass);
			$newpass = encrypt_password($newpass);
			if ($oldpass != ""){
				$username = $GLOBALS['super']->db->escape($GLOBALS['super']->user->username);
				$query = $GLOBALS['super']->db->query("SELECT `id` FROM ".TBL_PREFIX."users WHERE `username`='".$username."' AND `password`='".$pass."'");
				$id = $GLOBALS['super']->db->fetch_assoc($query);
				if ($id['id'] != $GLOBALS['super']->user->id)
					$errors[] = "To change your password, you must provide your old password.";
				elseif (strlen($newpass) <= 5)
					$errors[] = "Your new password must be greater than 5 characters.";
				else{
					$changePass = true;
				}
			}
			$newAvatar = false;
			$resizeAvatar = false;
			if (isset($_FILES["new_avatar"]) && $_FILES['new_avatar']['name'] != "" && is_uploaded_file($_FILES['new_avatar']['tmp_name'])){
				$name = $_FILES['new_avatar']['name'];
				$path = pathinfo($name);
				$size = filesize($_FILES['new_avatar']['tmp_name']);
				$info = getimagesize($_FILES['new_avatar']['tmp_name']);
				if (!in_array($path["extension"], array("jpg","jpeg","gif","png"))){
					$errors[] = "Avatars may only have the extension .jpg, .jpeg, .gif, or .png";
				}elseif ($size >= 10000*1024){
					$errors[] = "Avatars must be less than 100KB.";
				}else{
					$newAvatar = true;
				}
				if ($info[0] > 60 || $info[1] > 60){
					$resizeAvatar = true;
				}
			}
			if (count($errors) == 0){
				$GLOBALS['super']->user->displayname = $displayname;
				$GLOBALS['super']->user->email = $email;
				$GLOBALS['super']->user->signature = $signature;
				if ($changePass){
					$GLOBALS['super']->user->password = $newpass;
				}
				if ($newAvatar){
					$info = pathinfo($_FILES['new_avatar']['name']);
					$newPath =  "images/avatar_".$GLOBALS['super']->user->id.".".$info["extension"];
					move_uploaded_file($_FILES['new_avatar']['tmp_name'], ROOT_PATH.$newPath);
					$GLOBALS['super']->db->query("INSERT INTO ".TBL_PREFIX."images (`name`, `url`) VALUES ('Avatar', '{$newPath}')");
					$id = $GLOBALS['super']->db->fetch_lastid();
					$GLOBALS['super']->user->avatar = $id;
					if ($resizeAvatar) {
						require_once("includes/functions/smart_resize.php");
						smart_resize_image($newPath, null, 60, 60, false, $newPath, false, false, 100);
					}
				}
			}
		}elseif ($_POST['action'] == "personal"){
			$fname = (isset($_POST['fname'])) ? $_POST['fname'] : "";
			$lname = (isset($_POST['lname'])) ? $_POST['lname'] : "";
			$gender = (isset($_POST['gender'])) ? $_POST['gender'] : "";
			$country = (isset($_POST['country'])) ? $_POST['country'] : "";
			$birth_month = (isset($_POST['birth_month'])) ? $_POST['birth_month'] : 0;
			$birth_day = (isset($_POST['birth_day'])) ? $_POST['birth_day'] : 0;
			$birth_year = (isset($_POST['birth_year'])) ? $_POST['birth_year'] : 0;
			if (!in_array($gender, array("m", "f"))){
				$errors[] = "You have specified an incorrect gender.";
			}
			if (!($birth_month >= 1 && $birth_month <= 12)){
				$errors[] = "You have specified an incorrect birth month.";
			}
			if (!($birth_year >= 1930 && $birth_year <= intval(date("Y")))){
				$errors[] = "You have specified an incorrect birth year.";
			}
			if (!($birth_day >= 1 && $birth_day <= cal_days_in_month(CAL_GREGORIAN, $birth_month, $birth_year))){
				$errors[] = "You have specified an incorrect birth day.";
			}
			if (count($errors) == 0){
				$GLOBALS['super']->user->firstname = $fname;
				$GLOBALS['super']->user->lastname = $lname;
				$GLOBALS['super']->user->gender = $gender;
				$GLOBALS['super']->user->country = $country;
				$GLOBALS['super']->user->birth_day = $birth_day;
				$GLOBALS['super']->user->birth_month = $birth_month;
				$GLOBALS['super']->user->birth_year = $birth_year;
			}
		}elseif ($_POST['action'] == "display"){
			$dateformat = (isset($_POST['dateformat'])) ? $_POST['dateformat'] : "";
			$timeformat = (isset($_POST['timeformat'])) ? $_POST['timeformat'] : "";
			$timezone = (isset($_POST['timezone'])) ? $_POST['timezone'] : "";
			$postsperpage = (isset($_POST['postsperpage'])) ? intval($_POST['postsperpage']) : 0;
			$topicsperpage = (isset($_POST['topicsperpage'])) ? intval($_POST['topicsperpage']) : 0;
			if (!in_array($timezone, DateTimeZone::listIdentifiers())){
				$errors[] = "You have specified an invalid timezone.";
			}
			if ($postsperpage <= 0)
				$errors[] = "You must show at least 1 post per page.";
			if ($topicsperpage <= 0)
				$errors[] = "You must show at least 1 post per page.";
			if (count($errors) == 0){
				$GLOBALS['super']->user->date_format = $dateformat;
				$GLOBALS['super']->user->time_format = $timeformat;
				$GLOBALS['super']->user->time_zone = $timezone;
				$GLOBALS['super']->user->posts_per_page = $postsperpage;
				$GLOBALS['super']->user->topics_per_page = $topicsperpage;
			}
		}elseif ($_POST['action'] == "privacy"){
			$showonline = (isset($_POST['show_online'])) ? $_POST['show_online'] : 1;
			$senddigests = (isset($_POST['send_digests'])) ? $_POST['send_digests'] : 0;
			if (!in_array($showonline, array(1, 0)))
				$errors[] = "You must choose whether to show yourself on the online list.";
			if (!in_array($senddigests, array(1, 0)))
				$errors[] = "You must choose whether to recieve digests.";
			if (count($errors) == 0){
				$GLOBALS['super']->user->show_online = $showonline;
				$GLOBALS['super']->user->send_digests = $senddigests;
			}
		}
		if (count($errors) == 0){
			$success = new tpl(ROOT_PATH.'themes/Default/templates/success_redir.php');
			$success->add("message","Profile updated successfully!");
			$success->add("url","index.php?act=controlPanel&page=".$_POST['action']);
			echo $success->parse();
		}
		return $errors;
	}
}
?>