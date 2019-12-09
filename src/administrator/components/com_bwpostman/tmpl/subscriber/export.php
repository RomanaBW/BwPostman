<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber export template for backend.
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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('bootstrap.tooltip');

Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_subscriber_export.js');

$jinput	= Factory::getApplication()->input;
$image	= '<i class="icon-info"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo $this->request_url_raw; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'); ?></legend>
		<div class="well well-small">
			<table class="admintable export">
				<tr class="bwptable fileformat">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_FILEFORMAT'); ?></span>
						<span class="editlinktip hasTip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FILEFORMAT_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td class="bwptable"><div class="bwpmailformat"><?php echo $this->lists['fileformat']; ?></div></td>
				</tr>
				<tr class="bwptable delimiter">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_DELIMITER_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td><?php echo $this->lists['delimiter'];?></td>
				</tr>
				<tr class="bwptable enclosure">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td><?php echo $this->lists['enclosure'];?></td>
				</tr>
				<tr class="bwptable exportgroups">
					<td align="right" class="key"><span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td class="bwptable mailformat">
						<div class="bwpmailformat">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_STATUS'); ?>
							<p class="state"><input type="checkbox" id="status1" name="status1" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_CONFIRMED'); ?>
							</p>
							<p class="state"><input type="checkbox" id="status0" name="status0" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_UNCONFIRMED'); ?>
							</p>
							<p class="state"><input type="checkbox" id="status9" name="status9" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_TEST'); ?>
							</p>
							<br />
							<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVE'); ?><br />
							<p class="archive"><input type="checkbox" id="archive0" name="archive0" title="archive" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_UNARCHIVED'); ?>
							</p>
							<p class="archive"><input type="checkbox" id="archive1" name="archive1" title="archive" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVED'); ?>
							</p>
						</div>
					</td>
				</tr>
				<tr class="exportfields">
					<td width="150" align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS'); ?><br />
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td valign="top" width="280"><?php echo $this->lists['export_fields']; ?><br />
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP_NOTE');?>">
							<input class="btn btn-small" type="button" name="upbutton" onclick="moveUp(document.getElementById('export_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP'); ?>" />
						</span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN_NOTE');?>">
							<input class="btn btn-small" type="button" name="downbutton" onclick="moveDown(document.getElementById('export_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN'); ?>" />
						</span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
							<input class="btn btn-small" type="button" name="removebutton" onclick="removeOptions(export_fields);"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
						</span>
					</td>
					<td>&nbsp;</td>
					<td valign="top"><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_ANNOTATION'); ?></td>
				</tr>
				<tr class="button">
					<td width="150" align="center" class="key">
						<input class="btn btn-success" type="button" name="submitbutton"
								onclick="if(check()){selectAllOptions(document.adminForm['export_fields[]']);Joomla.submitbutton('subscribers.export');}"
								value="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_BUTTON'); ?>"
						/>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="export" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<input type="hidden" id="exportAlertText" value="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ERROR_NO_EXPORTFIELDS', true); ?>" />
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
