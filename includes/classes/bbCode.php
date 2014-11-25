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
class bbCode{
	protected function startCallback($string){
		$string = trim($string);
		return $string;
	}
	protected function bold($matches){
		$string = $this->startCallback($matches[1]);
		return "<strong>".$string."</strong>";
	}
	protected function underline($matches){
		$string = $this->startCallback($matches[1]);
		return '<span style="text-decoration: underline;">'.$string.'</span>';
	}
	protected function italic($matches){
		$string = $this->startCallback($matches[1]);
		return '<em>'.$string.'</em>';
	}
	protected function strike($matches){
		$string = $this->startCallback($matches[1]);
		return '<del>'.$string.'</del>';
	}
	protected function color($matches){
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		return '<span style="color: '.$m2.'">'.$m4.'</span>';
	}
	protected function size($matches){
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		$m2 = min(6, $m2);
		$m2 = max(1, $m2);
		return "<h".$m2.">".$m4."</h".$m2.">";
	}
	protected function image($matches){
		$string = $this->startCallback($matches[1]);
		return '<img src="'.$string.'" alt="" />';
	}
	protected function urlIn($matches){
		$string = $this->startCallback($matches[1]);
		return '<a href="'.$string.'">'.$string.'</a>';
	}
	protected function urlOut($matches){
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);

		return '<a href="'.$m2.'">'.$m4.'</a>';
	}
	protected function emailIn($matches){
		$string = $this->startCallback($matches[1]);
		return '<a href="mailto:'.$string.'">'.$string.'</a>';
	}
	protected function emailOut($matches){
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		return '<a href="mailto:'.$m2.'">'.$m4.'</a>';
	}
	protected function center($matches){
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: center">'.$string.'</div>';
	}
	protected function left($matches){
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: left">'.$string.'</div>';
	}
	protected function right($matches){
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: right">'.$string.'</div>';
	}
	protected function align($matches){
		$alignment = $this->startCallback($matches[1]);
		$content = $this->startCallback($matches[2]);
		$align = "none";
		if (in_array($alignment, array("left", "right")))
			$align = $alignment;
		return '<div style="float: '.$align.'">'.$content.'</div>';
	}
	protected function pre($matches){
		$string = $this->startCallback($matches[1]);
		return '<pre>'.$string.'</pre>';
	}
	protected function indent($matches){
		$string = $this->startCallback($matches[1]);
		return '<blockquote>'.$string.'</blockquote>';
	}
	protected function codeOut($matches){
		$m2 = $this->startCallback($matches[2]);
		$m4 = $this->startCallback($matches[4]);
		return '<pre class="bbcode_code brush:'.$m2.'">'.$m4.'</pre>';
	}
	protected function codeIn($matches){
		$string = $this->startCallback($matches[1]);
		return '<pre class="bbcode_code">'.$string.'</pre>';
	}
	protected function icodeOut($matches){
		$m2 = $this->startCallback($matches[2]);
		$m4 = $this->startCallback($matches[4]);
		return '<pre class="bbcode_code brush:'.$m2.' light:true">'.$m4.'</pre>';
	}
	protected function icodeIn($matches){
		$string = $this->startCallback($matches[1]);
		return '<pre style="display: inline;" class="bbcode_code brush:plain light:true">'.$string.'</pre>';
	}
	public function parse($string, $return = true){
		$string = htmlentities($string, ENT_NOQUOTES);
		$string = preg_replace_callback("/\[b\](.*?)\[\/b\]/is", array($this, 'bold') , $string);
		$string = preg_replace_callback("/\[u\](.*?)\[\/u\]/is", array($this, 'underline'), $string);
		$string = preg_replace_callback("/\[i\](.*?)\[\/i\]/is", array($this, 'italic'), $string);
		$string = preg_replace_callback("/\[strike\](.*?)\[\/strike\]/is", array($this, 'strike'), $string);
		$string = preg_replace_callback('/\[color=\"?(.*?)\"?\](.*?)\[\/color\]/is', array($this, 'color'), $string);
		$string = preg_replace_callback('/\[size=\"?(.*?)\"?\](.*?)\[\/size\]/is', array($this, 'size'),$string);
		$string = preg_replace_callback('/\[img\](.*?)\[\/img\]/is', array($this, 'image'),$string);
		$string = preg_replace_callback('/\[url\](.*?)\[\/url\]/is', array($this, 'urlIn'),$string);
		$string = preg_replace_callback('/\[url=\"?(.*?)\"?\](.*?)\[\/url\]/is', array($this, 'urlOut'),$string);
		$string = preg_replace_callback('/\[email\](.*?)\[\/email\]/is', array($this, 'emailIn'),$string);
		$string = preg_replace_callback('/\[email=\"?(.*?)\"?\](.*?)\[\/email\]/is', array($this, 'emailOut'),$string);
		$string = preg_replace_callback("/\[center\](.*?)\[\/center\]/is", array($this, 'center'), $string);
		$string = preg_replace_callback("/\[right\](.*?)\[\/right\]/is", array($this, 'right'), $string);
		$string = preg_replace_callback("/\[left\](.*?)\[\/left\]/is", array($this, 'left'), $string);
		$string = preg_replace_callback("/\[align=\"?(.*?)\"?](.*?)\[\/align\]/is", array($this, 'align'), $string);
		$string = preg_replace_callback("/\[pre\](.*?)\[\/pre\]/is", array($this, 'pre'), $string);
		$string = preg_replace_callback("/\[indent\](.*?)\[\/indent\]/is", array($this, 'indent'), $string);
		$string = preg_replace_callback('/\[code\](.*?)\[\/code\]/is', array($this, 'codeIn'),$string);
		$string = preg_replace_callback('/\[code=(&quot;|")?(.*?)(&quot;|")?\](.*?)\[\/code\]/is', array($this, 'codeOut'),$string);
		$string = preg_replace_callback('/\[icode\](.*?)\[\/icode\]/is', array($this, 'icodeIn'),$string);
		$string = preg_replace_callback('/\[icode=(&quot;|")?(.*?)(&quot;|")?\](.*?)\[\/icode\]/is', array($this, 'icodeOut'),$string);
		$string = nl2br($string);
		$string = str_replace("\t",str_repeat("&nbsp;",4), $string);
		if ($return)
			return $string;
		else
			echo $string;
	}
	public function stripBBCode($text){
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $text);
	}
}
?>