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

class forumPage{
	protected $pageName;
	public function __construct(){
		$this->setName($GLOBALS['super']->config->siteName);
	}

	protected function setName($name){
		$this->pageName = $name;
	}
	
	public function getName(){
		return $this->pageName;
	}
	
	public function onlineName(){
		return $this->getName();
	}
	
	public function getTitle(){
		return $this->getName()." ".$this->getAddon();
	}
	
	protected function getAddon(){
		return " - BetaDev Forum Systems";
	}
	
	public function breadCrumb(){
		return '<a href="index.php">'.$GLOBALS['super']->config->siteName.'</a>';
	}

	public function display(){
		ob_start();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	public function getCSS(){
		$css[] = array("path" => "themes/{$GLOBALS['super']->functions->getTheme()}/main.css");
		return $css;
	}
	
	public function getJS(){
		$js[] = array("path" => "includes/js/jquery-1.3.2.js");
		$js[] = array("path" => "includes/js/ui.core.js");
		$js[] = array("path" => "includes/js/global_functions.js");
		return $js;
	}
	
	protected function recurseNames($parentArray){
		if ($parentArray[0] == "-1"  ){
			return "";
		}else{
			$sql = "SELECT `is_cat`, `name`, `id` FROM ".TBL_PREFIX."forums WHERE `id` = ".$parentArray[0];
			$result = $GLOBALS['super']->db->query($sql);
			$result = $GLOBALS['super']->db->fetch_assoc($result);
			$currentid = array_shift($parentArray);
			if ($result['is_cat'] != 1){
				$link = " ".$GLOBALS['super']->config->crumbSeperator.' <a href="index.php?act=fdisplay&amp;id='.$result['id'].'">'.$result['name'].'</a>';
				return $this->recurseNames($parentArray).$link;
			}else{
				return $this->recurseNames($parentArray);
			}
		}
	}
}
?>