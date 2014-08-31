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
class ForumsInfo extends adminSub{
	public function __construct(){
		parent::__construct();
		$this->setName("Info");
	}
	public function display(){
		ob_start();
		$infotext = "Welcome to Forums!";
		$message = "Welcome to the BetaDev Board Administration Pages. These pages will help you control all aspects of your forums, ranging from ordering your forums, creating new forums, even to changing the default theme and adding moderators.";
		$content = new tpl(ROOT_PATH.'administration/display/templates/forums_info.php');
		$content->add("FORUM_INFO_TEXT", $infotext);
		$content->add("FORUM_MESSAGE", $message);
		echo $content->parse();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
?>