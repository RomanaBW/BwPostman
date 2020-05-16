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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

// Load the tooltip behavior for the notes
HtmlHelper::_('behavior.tooltip');

// Keep session alive while editing
HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('bootstrap.tooltip');

$document = Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_subscriber_import.js');

$jinput	= Factory::getApplication()->input;
$image	= '<i class="icon-info"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP1'); ?></legend>
		<div class="well well-small">
		  <table class="admintable bwptable import">
				<tr>
					<td width="250" align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE'); ?></td>
					<td><?php echo $this->import['filename']; ?></td>
				</tr>

				<?php if ($this->import['fileformat'] == 'csv')
				{
					// Show delimiter, enclosure and caption
					?>
					<tr>
						<td align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?></td>
						<td>
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
							} ?></td>
					</tr>
					<tr>
						<td align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE'); ?></td>
						<td>
							<?php
							if ($this->import['enclosure'] == '')
							{
								echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOSEPARATION');
							}
							else
							{
								echo $this->import['enclosure'];
							} ?>
						</td>
					</tr>
					<tr>
						<td align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION'); ?></td>
						<td>
							<?php
							if (empty($this->import['caption']))
							{
								echo Text::_('COM_BWPOSTMAN_NO');
							}
							else
							{
								echo Text::_('COM_BWPOSTMAN_YES');
							} ?>
						</td>
					</tr>
				<?php
				}
				// End CSV format
				?>
			</table>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP2'); ?></legend>
			<div class="well well-small">
				<div class="well well-small">
					<table class="admintable import">
						<tr>
							<td width="150" align="right" class="key" rowspan="2"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS'); ?>
								<br />
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
							</td>
							<td><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DB_FIELDS'); ?>
								&nbsp;
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DB_FIELDS_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
							</td>
							<td>&nbsp;</td>
							<td><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_FIELDS'); ?>
								&nbsp;
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_FIELDS_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td valign="top" width="180"><?php echo $this->lists['db_fields']; ?>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
								<input class="btn btn-small" type="button" onclick="removeOptions(db_fields);"
										value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
								</span>
							</td>
							<td width="20" align="center"><strong>=</strong></td>
							<td valign="top" width="280"><?php echo $this->lists['import_fields']; ?>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP_NOTE');?>">
									<input class="btn btn-small" type="button" onclick="moveUp(document.getElementById('import_fields'));"
											value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_UP'); ?>" />
								</span>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN_NOTE');?>">
									<input class="btn btn-small" type="button" onclick="moveDown(document.getElementById('import_fields'));"
											value="<?php echo Text::_('COM_BWPOSTMAN_SUB_MOVE_DOWN'); ?>" />
								</span>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
									<input class="btn btn-small" type="button" onclick="removeOptions(import_fields);"
											value="<?php echo Text::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
								</span>
							</td>
							<td>&nbsp;</td>
							<td valign="top"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_MATCH_FIELDS_ANNOTATION'); ?></td>
						</tr>
					</table>
				</div>

				<div class="width-100 fltlft row-fluid">
					<fieldset class="adminform">
						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip"
												title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>">
											<?php echo $image; ?>
										</span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_available'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
										$ml_available	= $this->form->getInput('ml_available');
										if (!empty($ml_available))
										{
											echo $this->form->getInput('ml_available');
										}
										else
										{ ?>
											<div class="width-50 fltlft span6">
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
											</div><?php
										}
										?>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip"
												title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>">
											<?php echo $image; ?>
										</span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_unavailable'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
										$ml_unavailable	= $this->form->getInput('ml_unavailable');

										if (!empty($ml_unavailable))
										{
											echo $this->form->getInput('ml_unavailable');
										}
										else
										{ ?>
											<div class="width-50 fltlft span6">
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
											</div><?php
										}
										?>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip"
												title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>">
											<?php echo $image; ?>
										</span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_intern'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
										$ml_intern	= $this->form->getInput('ml_intern');
										if (!empty($ml_intern))
										{
											echo $this->form->getInput('ml_intern');
										}
										else
										{ ?>
											<div class="width-50 fltlft span6">
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
											</div><?php
										}
										?>
									</div>
								</fieldset>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="well well-small">
					<table class="admintable bwptable import">
						<tr>
							<td width="250" align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_EMAILFORMAT'); ?>
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_EMAILFORMAT_NOTE');?>">
									<?php echo $image; ?>
								</span>
							</td>
							<td><?php echo $this->lists['emailformat']; ?></td>
						</tr>
					</table>
					<table class="admintable bwptable import">
						<tr>
							<td width="250" align="right" class="key"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CONFIRM'); ?>
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CONFIRM_NOTE');?>">
									<?php echo $image; ?>
								</span>
							</td>
							<td><input type="checkbox" id="confirm" name="confirm" title="confirm" value="1" /></td>
						</tr>
					</table>
				</div>

			<table class="admintable bwptable import">
				<tr>
					<td width="250" align="center" class="key"><input type="button" class="btn btn-success"
						onclick="if(check())
						{
							selectAllOptions(document.adminForm['db_fields[]']);
							selectAllOptions(document.adminForm['import_fields[]']);
							Joomla.submitbutton('subscribers.import');
						}"
						 value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_BUTTON1'); ?>" />
					</td>
				</tr>
			</table>
		</div>
	</fieldset>


	<input type="hidden" name="task" value="import" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HtmlHelper::_('form.token'); ?>

	<input type="hidden" id="importAlertEmail" value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_REMOVING_EMAIL', true); ?>" />
	<input type="hidden" id="importAlertFields" value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_MATCH_FIELDS', true); ?>" />
</form>

<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
