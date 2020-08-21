<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive campaigns confirm unarchive template for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-de" lang="de-de" dir="ltr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php echo Text::_('COM_BWPOSTMAN_UNARCHIVE'); ?></title>
	</head>
	<body>
		<form name="popupForm" action="#" method="get">
			<fieldset>
				<table width="100%" id="confirm-unarchive">
					<tr>
						<th height="30" align="center"><?php echo Text::_("COM_BWPOSTMAN_ARC_CONFIRM_UNARCHIVEING_CAM_NL"); ?></th>
					</tr>
					<tr>
						<td height="30" align="center">
							<input type="button" name="submitbutton" onClick="window.parent.confirmUnarchive('1');"
									value="<?php echo Text::_("COM_BWPOSTMAN_YES");?>" />
							<input type="button" name="submitbutton" onClick="window.parent.confirmUnarchive('0');"
									value="<?php echo Text::_("COM_BWPOSTMAN_NO");?>" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</body>
</html>
