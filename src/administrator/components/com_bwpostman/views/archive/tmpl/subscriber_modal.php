<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive subscribers modal template for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
		<td><img src="<?php echo JUri::base() . 'components/com_bwpostman/assets/images/icon-48-subscribers.png'; ?>" /></td>
		<td><strong><?php echo JText::_('COM_BWPOSTMAN_ARC_SHOW_SUB') ?></strong></td>
	</tr>
</table>

<fieldset class="adminform">
	<table border="0" class="admintable">
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_NAME');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->name;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_EMAIL');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->email;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_EMAILFORMAT');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->emailformat;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_REGISTRATION_DATE');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->registration_date;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_REGISTERED_BY');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->registered_by;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_CONFIRMATION_DATE');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->confirmation_date;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_CONFIRMED_BY');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->confirmed_by;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_ARCHIVE_DATE');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->archive_date;?></td>
		</tr>
		<tr>
			<td align="right">
				<strong>
					<?php echo JText::_('COM_BWPOSTMAN_SUB_ARCHIVED_BY');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->sub->archived_by;?></td>
		</tr>
	</table>
</fieldset>

<fieldset class="adminform">
	<table width="100%" border="0">
		<tr>
			<td><strong><?php echo JText::_('COM_BWPOSTMAN_SUB_ML_SUBSCRIBED'); ?></strong></td>
		</tr>
		<tr>
			<td>
				<?php
				$lists = $this->sub->lists;

				if (!empty($lists))
				{
					echo "<ul>";
					foreach ($lists AS $list) {
						echo "<li><strong>{$list->title}:</strong> {$list->description}</li>";
					}

					echo "</ul>";
				}
				else
					{
					echo JText::_('COM_BWPOSTMAN_ARC_SUB_NO_ASSIGNED_ML');
				}
				?>
			</td>
		</tr>
	</table>
</fieldset>
