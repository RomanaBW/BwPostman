<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber import 2 template for backend.
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

$jinput	= Factory::getApplication()->input;

// Split the result array into three arrays which contains errors and warnings which occurred during the import process
if (isset($this->result['mail_err']))
{
	$mail_err = $this->result['mail_err'];
}

if (isset($this->result['import_err']))
{
	$import_err = $this->result['import_err'];
}

if (isset($this->result['import_warn']))
{
	$import_warn = $this->result['import_warn'];
}

if (isset($this->result['import_success']))
{
	$import_success = $this->result['import_success'];
}

$option			= $jinput->getCmd('option');
$fileformat		= Factory::getApplication()->getUserState('com_bwpostman.subscriber.fileformat', '');

if ($fileformat == 'xml')
{
	$row_text 	= Text::_('COM_BWPOSTMAN_XML_ROW');
}
else
{
	$row_text 	= Text::_('COM_BWPOSTMAN_CSV_ROW');
}
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
	<?php
	if ((empty($mail_err)) && (empty($import_err)) && (empty($import_warn)))
	{
		echo '<div id="import-success" class="alert alert-success">' . Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT_SUCCESS') . '</div>';
	}

	// Email error
	if (!empty($mail_err))
	{ // The subscribers were imported but the confirmation email couldn't be sent ?>
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT_ERROR_CONFIRMEMAIL'); ?></legend>
			<table class="adminlist table table-bordered">
				<thead>
					<tr class="error">
						<th><?php echo $row_text; ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_ERROR_MSG'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($mail_err AS $mailing){ ?>
					<tr>
						<td><?php echo $mailing['row']; ?></td>
						<td><?php echo $mailing['email']; ?></td>
						<td><?php echo $mailing['msg']; ?></td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
		</fieldset>
	<?php
	}

	// Import error
	if (!empty($import_err))
	{
		// Subscriber couldn't be imported ?>
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT_ERROR'); ?></legend>
			<table class="adminlist table table-bordered">
				<thead>
					<tr>
						<th><?php echo $row_text; ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_ERROR_MSG'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($import_err AS $error){ ?>
					<tr class="error">
						<td><?php echo $error['row']; ?></td>
						<td><?php echo $error['email']; ?></td>
						<td>
							<?php
							echo $error['msg'];
							if (isset($error['id']))
							{
								echo " (ID: " . $error['id'] . ")";
							} ?>
						</td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
		</fieldset>
	<?php
	}

	// Import warning
	if (!empty($import_warn))
	{
		// The subscriber was imported but some data were changed ?>
		<fieldset class="adminform"><legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT_WARNING'); ?></legend>
			<table class="adminlist table table-bordered">
				<thead>
					<tr>
						<th><?php echo $row_text; ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?></th>
						<th><?php echo Text::_('COM_BWPOSTMAN_NOTES'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($import_warn AS $warning){ ?>
					<tr class="warning">
						<td><?php echo $warning['row']; ?></td>
						<td><?php echo $warning['email']; ?></td>
						<td><?php echo $warning['msg'];  ?></td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
		</fieldset>
	<?php
	}


	// Import success
	if (!empty($import_success))
	{
		// The subscriber was imported but some data were changed ?>
		<fieldset class="adminform"><legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT_SUCCESS_SUBSCRIBERS'); ?></legend>
			<table class="adminlist table table-bordered">
				<thead>
				<tr>
					<th><?php echo $row_text; ?></th>
					<th><?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?></th>
					<th><?php echo Text::_('COM_BWPOSTMAN_NOTES'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($import_success AS $success){ ?>
					<tr class="success">
						<td><?php echo $success['row']; ?></td>
						<td><?php echo $success['email']; ?></td>
						<td><?php echo $success['msg'];  ?></td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
		</fieldset>
		<?php
	}
	// Import warning ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
