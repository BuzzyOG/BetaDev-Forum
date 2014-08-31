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
$page = "1";
require_once('includes/header.php');
require_once('install.php');
$errors = array();
if (isSecureForm("step1") && isset($_POST['formsent']) && $_POST['formsent'] == "1"){
	$install = new Installer();
	if (!$install->install()){
		$errors = $install->errors;
	}else{
		header('Location: page2.php');
	}
}
?>
<div class="mainheaderbox">
	BetaDev Board Installer - Configuration Options
</div>
<div class="mainheaderbox2">
	Complete these settings and click the install button to continue.
</div>
<?php
if (count($errors) > 0){
	?>
	<div class="error">
		<strong>Errors have occured.</strong><br />
		<ul class="errors">
		<?php
		foreach($errors as $error){
			?>
			<li><?php echo $error?></li>
			<?php
		}
		?>
		</ul>
	</div>
	<?php
}
?>
<div class="catrow">Board Settings</div>
<form action="" method="post">
	<div class="contentbox">
		<table class="info">
			<tr>
				<td class="info">
					<strong>MySQL Host</strong>
				</td>
				<td class="input">
					<?php secureForm("step1"); ?>
					<input type="hidden" name="formsent" value="1" />
					<input type="text" name="dbhost" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL User Name</strong>
				</td>
				<td class="input">
					<input type="text" name="dbusername" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL Password</strong>
				</td>
				<td class="input">
					<input type="text" name="dbpass" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>MySQL Database Name</strong>
				</td>
				<td class="input">
					<input type="text" name="dbname" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Table Prefix</strong><br />
					The prefix for the sql tables. This allows you to run multiple copies of the discussion board on the same database.
				</td>
				<td class="input">
					<input type="text" name="dbprefix" />
				</td>
			</tr>
			<tr class="divider">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Username</strong><br />
				</td>
				<td class="input">
					<input type="text" name="username" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Displayname</strong><br />
				</td>
				<td class="input">
					<input type="text" name="displayname" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Password</strong><br />
				</td>
				<td class="input">
					<input type="password" name="password" />
				</td>
			</tr>
			<tr>
				<td class="info">
					<strong>Admin Email</strong><br />
				</td>
				<td class="input">
					<input type="text" name="email" />
				</td>
			</tr>
			<tr class="divider">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="info">
					<strong>Forum Name</strong><br />
				</td>
				<td class="input">
					<input type="text" name="forumname" />
				</td>
			</tr
			><tr>
				<td class="info">
					<strong>Forum URL</strong><br />
					The full url to the forum folder with a trailing slash. Ex: http://betadev.com/forum/
				</td>
				<td class="input">
					<input type="text" name="root" value="" />
				</td>
			</tr>
		</table>
	</div>
	<input class="submit" type="submit" value="Install!" /> <input class="submit" type="reset" value="Reset" />
</form>
<?php
require_once('includes/footer.php');
?>