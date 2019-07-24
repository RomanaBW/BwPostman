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
defined('_JEXEC') or die('Restricted access');

$image = '<i class="icon-info"></i>';
?>

<div class="row">
	<legend>
		<?php echo empty($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_CAM') : JText::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id); ?>
	</legend>
	<div class="col-md-6">
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

	<div class="col-md-6">
		<?php
		if (empty($this->item->campaign_id))
		{
			$this->form->setFieldAttribute('campaign_id', 'type', 'hidden');
		}
		?>

		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('campaign_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('campaign_id'); ?>
			</div>
		</div>

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
	<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
	<div class="clearfix"></div>
</div>

<div class="row well cam-recipients">
	<legend class="required">
		<?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *
	</legend>
	<div class="col-md-9 nl-mailinglists">
		<div class="well-white well-small">
			<legend>
				<span class="editlinktip hasTip hasTooltip"
						title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
					<?php echo $image; ?>
				</span>
				<span class="editlinktip hasTip hasTooltip"
						title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
								<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS'); ?>
				</span>
			</legend>
			<div class="row">
				<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<div class="col-md-4 nl-mailinglists">
							<div class="well well-small">
								<fieldset>
									<legend>
												<span class="editlinktip hasTip hasTooltip"
														title="<?php echo JText::_($field->description); ?>">
													<?php echo $image; ?>
												</span>
										<span class="editlinktip hasTip hasTooltip"
												title="<?php echo JText::_($field->description); ?>">
													<?php echo $field->label; ?>
												</span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
										$input_field	= trim($field->input);
										if (!empty($input_field))
										{
											echo $field->input;
										}
										else
										{
											echo '<div class="width-50 fltlft col-md-6">
																<label class="mailinglist_label noclear checkbox">' .
												JText::_('COM_BWPOSTMAN_NO_DATA') .
												'</label>
															</div>';
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

	<div class="col-md-3 cam-usergroups">
		<div class="well-white well-small">
			<legend>
				<span class="editlinktip hasTip hasTooltip"
						title="<?php echo JText::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_DESC'); ?>">
					<?php echo $image; ?>
				</span>
				<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_LABEL'); ?></span>
			</legend>
			<?php foreach($this->form->getFieldset('usergroups') as $field): ?>
				<?php echo $field->input; ?>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="clr clearfix"></div>
</div>



<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

Joomla.submitbutton = function (pressbutton)
{
	if (pressbutton == 'campaign.cancel')
	{
		Joomla.submitform(pressbutton, document.adminForm);
		return;
	}

	if ((pressbutton == 'campaign.apply') || (pressbutton == 'campaign.save') || (pressbutton == 'campaign.save2new') || (pressbutton == 'campaign.save2copy'))
	{
		if ($j("input[type=checkbox]:checked").length)
		{
			Joomla.submitform(pressbutton, document.adminForm);
			return true;
		}
		else
		{
			alert('<?php echo JText::_("COM_BWPOSTMAN_CAM_ERROR_NO_RECIPIENTS_SELECTED"); ?>');
			return false;
		}
	}
};

/* ]]> */
</script>

