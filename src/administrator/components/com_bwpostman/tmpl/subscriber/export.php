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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

// Load the tooltip behavior for the notes
HTMLHelper::_('bootstrap.tooltip');

$wa = $this->document->getWebAssetManager();
$wa->useScript('com_bwpostman.admin-bwpm_subscriber_export');

$jinput	= Factory::getApplication()->input;
$image	= '<i class="fa fa-info-circle fa-lg"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo $this->request_url_raw; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'); ?></legend>
		<div class="card card-body">
			<div class="admintable export">
				<div class="bwptable fileformat row">
					<label class="key col-form-label col-md-6 text-md-right">
						<?php echo Text::_('COM_BWPOSTMAN_SUB_FILEFORMAT'); ?>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FILEFORMAT_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</label>
					<div class="col-md-6"><?php echo $this->lists['fileformat']; ?></div>
				</div>
				<div id="delimiter_tr" class="delimiter form-group row">
					<label class="key col-md-6 text-md-right">
						<?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_DELIMITER_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</label>
					<div class="col-md-6"><?php echo $this->lists['delimiter'];?></div>
				</div>
				<div id="enclosure_tr" class="enclosure form-group row">
					<label class="key col-md-6 text-md-right">
						<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE'); ?>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</label>
					<div class="col-md-6"><?php echo $this->lists['enclosure'];?></div>
				</div>
				<div id="exportgroups_tr" class="exportgroups form-group row">
					<label class="key col-md-6 text-md-right">
						<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS'); ?>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</label>
					<div class="col-md-6 mailformat">
						<div class="bwpmailformat">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_STATUS'); ?>
							<p class="state"><label><input type="checkbox" id="status1" name="status1" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_CONFIRMED'); ?></label>
							</p>
							<p class="state"><label><input type="checkbox" id="status0" name="status0" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_UNCONFIRMED'); ?></label>
							</p>
							<p class="state"><label><input type="checkbox" id="status9" name="status9" title="status" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_TEST'); ?></label>
							</p>
							<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVE'); ?><br />
							<p class="archive"><label><input type="checkbox" id="archive0" name="archive0" title="archive" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_UNARCHIVED'); ?></label>
							</p>
							<p class="archive"><label><input type="checkbox" id="archive1" name="archive1" title="archive" value="1" />
								<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVED'); ?></label>
							</p>
						</div>
					</div>
				</div>
				<div id="exportfields_tr" class="exportfields form-group row">
					<label class="key col-md-6 text-md-right">
						<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS'); ?>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</label>
					<div class="col-md-6">
						<div class="mb-2">
							<?php echo $this->lists['export_fields']; ?>
						</div>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP_NOTE');?>">
							<input class="btn btn-outline-primary btn-sm mb-2" type="button" name="upbutton" onclick="moveUp(document.getElementById('export_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP'); ?>" />
						</span>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN_NOTE');?>">
							<input class="btn btn-outline-success btn-sm mb-2" type="button" name="downbutton" onclick="moveDown(document.getElementById('export_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN'); ?>" />
						</span>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
							<input class="btn btn-outline-danger btn-sm mb-2" type="button" name="removebutton" onclick="removeOptions(export_fields);"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
						</span>
					</div>
					<div><?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_ANNOTATION'); ?></div>
				</div>
				<div id="button_tr" class="button">
					<div class="key text-center">
						<input class="btn btn-success" type="button" name="submitbutton"
								onclick="if(check()){selectAllOptions(document.adminForm['export_fields[]']);Joomla.submitbutton('subscribers.export');}"
								value="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_BUTTON'); ?>"
						/>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="export" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<input type="hidden" id="exportAlertText" value="<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ERROR_NO_EXPORTFIELDS', true); ?>" />
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

