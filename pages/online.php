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
class Online extends forumPage{
	public function __construct(){
		parent::__construct();
		$this->setName("Online List");
	}
	public function breadCrumb(){
		return parent::breadCrumb()." ".$GLOBALS['super']->config->crumbSeperator." ".$this->getName();
	}
	public function display(){
		ob_start();
		if (!$GLOBALS['super']->user->can("ViewBoard")){
			echo $GLOBALS['super']->user->noPerm();
		}else {
			global $onlineHelper;
			list($guestCount, $userCount, $info) = $onlineHelper->generate();
			$tpl = new tpl(ROOT_PATH.'themes/Default/templates/onlinelist.php');
			$tpl->add("online",$info);
			$tpl->add("guestcount",$guestCount);
			$tpl->add("usercount",$userCount);
			echo $tpl->parse();	
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	function getJS(){
		$js = parent::getJS();
		$js[] = array("path" => "includes/js/onlinelist.js");
		return $js;
	}
}
?>