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
?>
<div class="mainheaderbox">
	BetaDev Board Installer - Installation Successful
</div>
Congratulations! BetaDev Board has now been installed successfully and your board has been set up with a default forum and topic! For security reasons you should now delete the install folder from your server directory and CHMOD config.php to 655. Click <a href="../index.php">here</a> to be taken to your brand new installation.
<?php
require_once('includes/footer.php');
?>