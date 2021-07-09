<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive campaign modal template for backend.
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
use Joomla\CMS\Uri\Uri;
?>

<table class="admintable">
	<tr>
		<td><img src="<?php echo Uri::base() . 'components/com_bwpostman/assets/images/icon-48-campaigns.png'; ?>" alt="Campaigns Icon" /></td>
		<td><strong><?php echo Text::_('COM_BWPOSTMAN_ARC_SHOW_CAM') ?></strong></td>
	</tr>
</table>
<br />
<fieldset class="adminform">
	<table class="admintable">
		<tr>
			<td style="text-align: right;">
				<strong>
					<?php echo Text::_('COM_BWPOSTMAN_TITLE');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->cam->title;?></td>
		</tr>
		<tr>
			<td style="text-align: right;">
				<strong>
					<?php echo Text::_('COM_BWPOSTMAN_DESC');
					echo ':'; ?>
				</strong>
			</td>
			<td><?php echo $this->cam->description;?></td>
		</tr>
	</table>
</fieldset>
<br />
<fieldset class="adminform">
	<table>
		<tr>
			<td><strong><?php echo Text::_('COM_BWPOSTMAN_CAM_ASSIGNED_NL'); ?></strong></td>
		</tr>
		<tr>
			<td><?php
			$newsletters = $this->cam->newsletters;

			if (!empty($newsletters))
			{
				echo "<ul>";

				foreach ($newsletters AS $newsletter) {
					if ($newsletter->archive_flag == 0) {
						echo "<li><strong>$newsletter->subject</strong> (ID: $newsletter->id)</li>";
					}
					else
					{
						echo "<li><strong>$newsletter->subject</strong> (ID: $newsletter->id, " . Text::_('COM_BWPOSTMAN_ARC_NL') . ")</li>";
					}
				}

				echo "</ul>";
			}
			else {
				echo Text::_('COM_BWPOSTMAN_ARC_CAM_NO_ASSIGNED_NL');
			}
			?></td>
		</tr>
	</table>
</fieldset>
