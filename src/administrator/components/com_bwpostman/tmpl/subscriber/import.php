<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber import 0 template for backend.
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

// Keep session alive while importing
HTMLHelper::_('behavior.keepalive');
//HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('bootstrap.tooltip');

$document = Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_subscriber_import.js');

$jinput	= Factory::getApplication()->input;
$image	= '<i class="fa fa-info-circle fa-lg"></i>';
$option	= $jinput->getCmd('option');

?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP1'); ?></legend>
		<div class="card card-body">
			<div class="row">
				<div class="admintable bwptable import col-12">
					<fieldset class="bwptable form-group">
						<div class="key row">
							<label class="col-form-label col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_FILEFORMAT'); ?>
								<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILEFORMAT_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
							</label>
							<div class="col-md-6">
								<?php echo $this->lists['fileformat']; ?>
							</div>
						</div>
					</fieldset>
					<div class="importfile form-group row">
						<label class="key col-md-6 text-md-right">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE'); ?>
							<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</label>
						<div class="col-md-6">
							<input type="file" class="form-control-file" name="importfile" id="importfile"
								<?php //if (empty($this->import['fileformat'])) echo ' disabled="disabled"'; ?> />
						</div>
					</div>
					<div class="delimiter form-group row">
						<label class="key col-md-6 text-md-right">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?>
							<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DELIMITER_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</label>
						<div class="col-md-6">
							<?php echo $this->lists['delimiter'];?>
						</div>
					</div>
					<div class="enclosure form-group row">
						<label class="key col-md-6 text-md-right">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE'); ?>
							<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</label>
						<div class="col-md-6">
							<?php echo $this->lists['enclosure'];?>
						</div>
					</div>
					<div class="caption form-group row">
						<label class="key col-md-6 text-md-right">
							<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION'); ?>
							<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</label>
						<div class="col-md-6">
							<div class="form-check">
								<input type="checkbox" class="form-check-input" id="caption" name="caption" title="caption"
									<?php
									if (isset($this->import['caption']))
									{
										if ($this->import['caption'] == 1)
										{
											echo "checked";
										}
									} ?>
								/>
							</div>
						</div>
					</div>
					<div class="button form-group row mt-3">
						<div class="key col-12 text-center">
							<input type="button" class="btn btn-success" name="submitbutton" id="further"
								<?php //if (empty($this->import['fileformat'])) echo ' disabled="disabled"'; ?>
									onclick="Joomla.submitbutton('subscribers.prepareImport');"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_BUTTON'); ?>"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="prepareImport" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<input type="hidden" id="importAlertFileFormat" value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILEFORMAT', true); ?>" />
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

