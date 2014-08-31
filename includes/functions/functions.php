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
function encrypt_password($password){
	$md5_pass = md5($password);
	if (defined("PASS_HASH"))
		$pass = PASS_HASH.$md5_pass;
	else
		$pass = "hash".$md5_pass;
	$hash_crypt_pass = hash("whirlpool",$pass);
	return $hash_crypt_pass;
}
function secureForm($formName, $return = false){
	$hash = encrypt_password(time());
	$hash = substr($hash, 4, 10);
	$input = '<input type="hidden" name="'.$formName.'Hash" value="'.$hash.'" />';
	$_SESSION[$formName.'Hash'] = $hash;
	if ($return)
		return $input;
	else
		echo $input;
}
function isSecureForm($formName){
	$return = false;
	if (!isset($_POST[$formName.'Hash']) || !isset($_SESSION[$formName.'Hash']))
		return false;
	if ($_POST[$formName.'Hash'] == $_SESSION[$formName.'Hash'])
		$return = true;
	unset ($_SESSION[$formName.'Hash']);
	return $return;
}
function dotdotdot($string, $length){
	if (strlen($string) > $length)
		$string = substr($string, 0, $length - 3)."...";
	return $string;
}
function plural($num, $endsInY = false){
	if ($num != 1) {
    	if ($endsInY)
    		return "ies";
    	else
    		return "s";
    }else{
    	if ($endsInY)
    		return "y";
    	else
    		return "";
    }
}
?>