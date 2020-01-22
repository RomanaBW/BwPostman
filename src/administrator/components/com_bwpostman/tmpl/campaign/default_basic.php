<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns form template for backend.
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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

$image = '<i class="fa fa-lg fa-info-circle"></i>';
?>

<div class="card card-body mb-3">
	<div class="row">
		<div class="col-12 mb-2">
			<h3>
				<?php echo empty($this->item->id) ? Text::_('COM_BWPOSTMAN_NEW_CAM') : Text::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id); ?>
			</h3>
		</div>
		<div class="col-lg-6">
			<?php
			if (isset($this->item->err_code))
			{
				if (($this->item->err_code == 101) || ($this->item->err_code == 102))
				{

				}
			}
			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('title'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('title'); ?>
				</div>
			</div>

			<?php
			if (isset($this->item->err_code))
			{
				if (($this->item->err_code == 101) || ($this->item->err_code == 102))
				{

				}
			}?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('description'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('access'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('access'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<?php
			// Romana - es gibt hier keine campaign_id
			// @Karl - Ja, Du hast vollkommen Recht! :-(
			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('created_by'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('created_by'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('created_date'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('created_date'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('modified_by'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('modified_by'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('modified_time'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('modified_time'); ?>
				</div>
			</div>
		</div>
		<p class="col-12 clearfix"><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
	</div>
</div>

<div class="card card-body">
	<div class="row cam-recipients">
		<div class="col-12 mb-2">
			<h3 class="required">
				<?php echo Text::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *
			</h3>
		</div>
		<div class="col-xl-8 nl-mailinglists">
			<div class="card-header">
				<h4>
					<span class="hasTooltip"
							title="<?php echo Text::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
						<?php echo $image; ?>
						<?php echo Text::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS'); ?>
					</span>
				</h4>
				<div class="row">
					<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div class="col-lg-4 nl-mailinglists mb-3">
								<div class="card card-body">
									<fieldset>
										<h5>
											<span class="hasTooltip"
													title="<?php echo Text::_($field->description); ?>">
												<?php echo $image; ?>
												<?php echo $field->label; ?>
											</span>
										</h5>
										<div class="row-fluid clearfix">
											<?php
											$input_field	= trim($field->input);
											if (!empty($input_field))
											{
												echo $field->input;
											}
											else
											{
												echo '<div class="">' . Text::_('COM_BWPOSTMAN_NO_DATA') . '</div>';
											}
											?>
										</div>
									</fieldset>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="col-xl-4 cam-usergroups">
			<div class="card-header break-word">
				<h4>
				<span class="hasTooltip"
						title="<?php echo Text::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_DESC'); ?>">
					<?php echo $image; ?>
					<?php echo Text::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_LABEL'); ?>
				</span>
			</h4>
				<div class="card card-body">
				<?php foreach($this->form->getFieldset('usergroups') as $field): ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>


