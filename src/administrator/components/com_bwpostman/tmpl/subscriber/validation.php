<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber validation template for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

	// Keep session alive while editing
	HTMLHelper::_('behavior.keepalive');
?>

<?php
$jinput	= Factory::getApplication()->input;
$image_pos = HTMLHelper::_('image', 'administrator/images/tick.png', Text::_('COM_BWPOSTMAN_NOTES'));
$image_neg = HTMLHelper::_('image', 'administrator/images/publish_x.png', Text::_('COM_BWPOSTMAN_NOTES'));
$option = $jinput->getCmd('option');

?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_VALIDATION_RESULT'); ?></legend>
		<table class="adminlist">
			<tr>
				<th width="30" align="center"><?php echo Text::_('ID'); ?></th>
				<th align="center"><?php echo Text::_('COM_BWPOSTMAN_SUB_NAME'); ?></th>
				<th align="center"><?php echo Text::_('COM_BWPOSTMAN_SUB_FIRSTNAME'); ?></th>
				<th align="center"><?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?></th>
				<th align="center"><?php echo Text::_('COM_BWPOSTMAN_SUB_VALIDATION_RESULT_TEXT'); ?></th>
			</tr>
			<?php foreach ($this->item AS $res_row) {?>
			<tr>
				<td align="center"><?php echo $res_row['id']; ?></td>
				<td><?php echo $res_row['name']; ?></td>
				<td><?php echo $res_row['firstname']; ?></td>
				<td><?php echo $res_row['email']; ?></td>
				<td><?php
				if ($res_row['result'] == 1)
				{
					echo "$image_pos ";
				}
				else
				{
					echo "$image_neg ";
				}

				echo $res_row['result_txt'];
				?></td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>


	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="finishValidation" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
