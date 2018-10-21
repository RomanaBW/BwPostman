<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive mailinglists modal template for backend.
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
<table>
	<tr>
		<td><img src="<?php echo JUri::base() . 'components/com_bwpostman/assets/images/icon-48-mailinglists.png'; ?>" /></td>
		<td><strong><?php echo JText::_('COM_BWPOSTMAN_ARC_SHOW_ML') ?></strong></td>
	</tr>
</table>

<fieldset class="adminform">
	<table border="0" class="admintable">
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_TITLE');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->ml->title;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_DESC');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->ml->description;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_ACCESS');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->ml->access_level; ?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('PUBLISHED');
					echo ':'; ?>
				</strong>
			</td>
			<td>
				<?php
				switch ($this->ml->published) {
					case "0":
						echo JText::_('COM_BWPOSTMAN_NO');
						break;
					case "1":
						echo JText::_('COM_BWPOSTMAN_YES');
				} ?>
			</td>
		</tr>
	</table>
</fieldset>
