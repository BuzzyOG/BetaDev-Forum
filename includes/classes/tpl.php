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
class tpl{
	private $template_location = null;
	private $variables = array();
	private $functions = null;
	function __construct($templateFile){
		$this->functions =& $GLOBALS['super']->functions;
		$this->template_location = $templateFile;
		if (!is_file($this->template_location)){
			echo $this->template_location." does not exist";
		}
	}
	function __get($var){
		if (key_exists($var, $this->variables))
			return ($this->variables[$var]);
	}
	function parse($return = false){
		if ($this->template_location == null)
			return;
		ob_start();
		require($this->template_location);
		$content = ob_get_contents();
		ob_end_clean();
		if ($return)
			return $content;
		else
			echo $content;
	}
	function add($var, $value = null){
		$this->variables[$var] = $value;
	}
}	
?>