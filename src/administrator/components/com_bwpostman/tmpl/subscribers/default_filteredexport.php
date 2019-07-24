<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers check for only filtered subscribers to export.
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

?>

<form name="popupForm" action="#" method="get">
	<fieldset>
		<table width="100%" id="modal-upload">
			<tr>
				<th height="30" align="center"><?php echo JText::_("COM_BWPOSTMAN_SUB_CONFIRM_EXPORT_FILTERED"); ?></th>
			</tr>
			<tr>
				<td height="30" align="center">
					<input type="button" name="submitbutton" onClick="window.parent.OnlyFiltered('1')" value="<?php echo JText::_("COM_BWPOSTMAN_YES");?>" />
					<input type="button" name="submitbutton" onClick="window.parent.OnlyFiltered('0')" value="<?php echo JText::_("COM_BWPOSTMAN_NO");?>" />
				</td>
			</tr>
		</table>
	</fieldset>
</form>
