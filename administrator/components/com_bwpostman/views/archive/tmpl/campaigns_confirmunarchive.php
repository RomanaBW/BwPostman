<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive campaigns confirm unarchive template for backend.
 *
 * @version 1.3.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

?>

<script type="text/javascript">
	/* <![CDATA[ */
		function check() // Checks if one or more campaigns are selected
		{
      var htmlText="";
      htmlText += '<form name="popupForm" action="#" method="get">';
      htmlText += '	<fieldset>';
      htmlText += '		<table width="100%">';
      htmlText += '			<tr>';
			if(window.parent.document.adminForm.boxchecked.value==0){
        htmlText += '				<th height="30" align="center"><?php echo JText::_("COM_BWPOSTMAN_NO_SELECTION"); ?></th>';
			} else {
        htmlText += '				<th height="30" align="center"><?php echo JText::_("COM_BWPOSTMAN_ARC_CONFIRM_UNARCHIVEING_CAM_NL"); ?></th>';
        htmlText += '			</tr>';
        htmlText += '			<tr>';
        htmlText += '				<td height="30" align="center">';
        htmlText += '					<input type="button" name="submitbutton" onClick="window.parent.confirmUnarchive(\'1\');"	value="<?php echo JText::_("COM_BWPOSTMAN_YES");?>" />';
        htmlText += '					<input type="button" name="submitbutton" onClick="window.parent.confirmUnarchive(\'0\');"	value="<?php echo JText::_("COM_BWPOSTMAN_NO");?>" />';
        htmlText += '				</td>';
      }
      htmlText += '			</tr>';
      htmlText += '		</table>';
      htmlText += '	</fieldset>';
      htmlText += '</form>';
      document.write(htmlText);
		}
		check();
	/* ]]> */
</script>
