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
class HomeInfo extends adminSub{
	public function __construct(){
		parent::__construct();
		$this->setName("Info");
	}
	public function display(){
		ob_start();
		$version_info[] = "BetaDev Board ".$this->super->config->forumVersion;
		$version_info[] = "&copy; Copyright 2010 - ".date("Y");
		$version_info[] = "Coded by Cblair91";
		$server_info[] = "Operating System: ".php_uname("s");
		$server_info[] = "HTTP Server: ".$_SERVER["SERVER_SOFTWARE"];
		$server_info[] = "PHP Version ".phpversion();
		$sql="SELECT VERSION()";
		$result = $this->super->db->query($sql, true);
		$result = $this->super->db->fetch_assoc($result);	
		$database_info[] = "MYSQL Version: ".$result['VERSION()'];
		$dbsize = 0;
		$numrows = 0;
		$rows = $this->super->db->query("SHOW table STATUS");
		while ($row = $this->super->db->fetch_assoc($rows)){
			$dbsize += $row['Data_length'] + $row['Index_length'];
			$numrows += $row['Rows'];
		}
		$dbsize = $this->super->functions->fileSizeFormat($dbsize);
		$database_info[] = "Rows: ".$numrows;
		$database_info[] = "Size: ".$dbsize['size']." ".$dbsize['type'];
		$infotext = "Welcome to BetaDev Board Adminstration!";
		$message = "The pages in this section will give you information about your server setup, license status, forum statistics as well as version update information.";
		$content = new tpl(ROOT_PATH.'administration/display/templates/home_info.php');
		$content->add("HOME_INFO_TEXT", $infotext);
		$content->add("HOME_MESSAGE", $message);
		$content->add("VERSION", $version_info);
		$content->add("SERVER", $server_info);
		$content->add("DATABASE", $database_info);
		echo $content->parse();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>