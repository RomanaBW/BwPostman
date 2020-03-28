<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber import 1 template for backend.
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
//HTMLHelper::_('behavior.tooltip');

// Keep session alive while editing
HTMLHelper::_('behavior.keepalive');

$document = Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_subscriber_import.js');

HTMLHelper::_('bootstrap.tooltip');
$jinput	= Factory::getApplication()->input;
$image	= '<i class="fa fa-info-circle fa-lg"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform mb-4">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP1'); ?></legend>
		<div class="card card-body">
			<div class="admintable bwptable import">
				<div class="row">
					<div class="key col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE'); ?>:</div>
					<div class="col-md-6"><?php echo $this->import['filename']; ?></div>
				</div>

				<?php if ($this->import['fileformat'] == 'csv')
				{
					// Show delimiter, enclosure and caption
					?>
					<div class="row">
						<div class="key col-9 col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?>:</div>
						<div class="col-3 col-md-6">
							<?php
							if ($this->import['delimiter'] == '\t')
							{
								echo "Tabulator";
							}
							elseif ($this->import['delimiter'] == ' ')
							{
								echo "Space";
							}
							else
							{
								echo $this->import['delimiter'];
							} ?>
						</div>
					</div>
					<div class="row">
						<div class="key col-9 col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE'); ?>:</div>
						<div class="col-3 col-md-6">
							<?php
							if ($this->import['enclosure'] == '')
							{
								echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOSEPARATION');
							}
							else
							{
								echo $this->import['enclosure'];
							} ?>
						</div>
					</div>
					<div class="row">
						<div class="key col-9 col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION'); ?>:</div>
						<div class="col-3 col-md-6">
							<?php
							if (empty($this->import['caption']))
							{
								echo Text::_('COM_BWPOSTMAN_NO');
							}
							else
							{
								echo Text::_('COM_BWPOSTMAN_YES');
							} ?>
						</div>
					</div>
					<?php
				}
				// End CSV format
				?>
			</div>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP2'); ?></legend>
		<div>
			<div class="admintable import card card-body mb-2">
				<h4 class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS'); ?>
					<span class="hasTooltip"
							title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS_NOTE'); ?>">
						<?php echo $image; ?>
					</span>
				</h4>
				<div class="row">
					<div class="col-sm-6 text-sm-right mb-2"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DB_FIELDS'); ?>
						&nbsp;
						<span class="hasTooltip"
								title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DB_FIELDS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
						<div class="my-2">
							<?php echo $this->lists['db_fields']; ?>
						</div>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
							<input class="btn btn-danger btn-sm mb-2" type="button" onclick="removeOptions(db_fields);"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
						</span>
					</div>
					<div class="col-sm-6 mb-2"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_FIELDS'); ?>
						&nbsp;
						<span class="hasTooltip"
								title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_FIELDS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
						<div class="my-2">
							<?php echo $this->lists['import_fields']; ?>
						</div>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP_NOTE');?>">
							<input class="btn btn-outline-primary btn-sm mb-2" type="button" onclick="moveUp(document.getElementById('import_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP'); ?>" />
						</span>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN_NOTE');?>">
							<input class="btn btn-outline-success btn-sm mb-2" type="button" onclick="moveDown(document.getElementById('import_fields'));"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN'); ?>" />
						</span>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
							<input class="btn btn-danger btn-sm mb-2" type="button" onclick="removeOptions(import_fields);"
									value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
						</span>
					</div>
				</div>
				<div><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS_ANNOTATION'); ?></div>
			</div>

			<div class="card card-body mb-2">
				<fieldset class="adminform row">
					<div class="col-lg-4 mb-2">
						<fieldset class="adminform">
							<legend>
								<span class="hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span>&nbsp;<?php echo $this->form->getLabel('ml_available'); ?></span>
							</legend>
							<?php
							$ml_available	= $this->form->getInput('ml_available');
							if (!empty($ml_available))
							{
								echo $this->form->getInput('ml_available');
							}
							else
							{ ?>
								<label class="mailinglist_label noclear checkbox">
									<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
								</label>
								<?php
							}
							?>
						</fieldset>
					</div>

					<div class="col-lg-4 mb-2">
						<fieldset class="adminform">
							<legend>
								<span class="hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span>&nbsp;<?php echo $this->form->getLabel('ml_unavailable'); ?></span>
							</legend>
							<?php
							$ml_unavailable	= $this->form->getInput('ml_unavailable');
							if (!empty($ml_unavailable))
							{
								echo $this->form->getInput('ml_unavailable');
							}
							else
							{ ?>
								<label class="mailinglist_label noclear checkbox">
									<?php echo Text::_('COM_BWPOSTMAN_NO_DATA') ?>
								</label>
								<?php
							}
							?>
						</fieldset>
					</div>

					<div class="col-lg-4 mb-2">
						<fieldset class="adminform">
							<legend>
								<span class="hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span>&nbsp;<?php echo $this->form->getLabel('ml_intern'); ?></span>
							</legend>
							<?php
							$ml_intern	= $this->form->getInput('ml_intern');
							if (!empty($ml_intern))
							{
								echo $this->form->getInput('ml_intern');
							}
							else
							{ ?>
								<label class="mailinglist_label noclear checkbox">
									<?php echo Text::_('COM_BWPOSTMAN_NO_DATA') ?>
								</label>
								<?php
							}
							?>
						</fieldset>
					</div>
				</fieldset>
			</div>

			<div class="card card-body mb-2">
				<div class="admintable bwptable import">
					<div class="row">
						<div class="key col-8 col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_EMAILFORMAT'); ?>
							<span class="hasTooltip"
									title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_EMAILFORMAT_NOTE');?>">
								<?php echo $image; ?>
							</span>
						</div>
						<div class="col-4 col-md-6"><?php echo $this->lists['emailformat']; ?></div>
					</div>
				</div>
				<div class="admintable bwptable import">
					<div class="row">
						<div class="key col-8 col-md-6 text-md-right"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CONFIRM'); ?>
							<span class="hasTooltip"
									title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CONFIRM_NOTE');?>">
								<?php echo $image; ?>
							</span>
						</div>
						<div class="col-4 col-md-6"><input type="checkbox" id="confirm" name="confirm" title="confirm" value="1" /></div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<div class="admintable bwptable import mb-3">
		<div class="row">
			<div class="key col-12 text-center"><input type="button" class="btn btn-success"
						onclick="if(check())
				{
					selectAllOptions(document.adminForm['db_fields[]']);
					selectAllOptions(document.adminForm['import_fields[]']);
					Joomla.submitbutton('subscribers.import');
				}"
						value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_BUTTON1'); ?>" />
			</div>
		</div>
	</div>


	<input type="hidden" name="task" value="import" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<input type="hidden" id="importAlertEmail" value="<?php echo JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_REMOVING_EMAIL', true); ?>" />
	<input type="hidden" id="importAlertFields" value="<?php echo JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_MATCH_FIELDS', true); ?>" />
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

