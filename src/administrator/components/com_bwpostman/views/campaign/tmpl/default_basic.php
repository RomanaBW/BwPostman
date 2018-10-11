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
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

<fieldset class="adminform">
	<legend>
		<?php echo empty($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_CAM') : JText::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id); ?>
	</legend>
	<div class="well well-small">
		<div class="width-60 fltlft span8 control-group">
			<ul class="adminformlist unstyled">
				<?php
				if (isset($this->item->err_code))
				{
					if (($this->item->err_code == 101) || ($this->item->err_code == 102))
					{

					}
				}
				?>
				<li>
					<?php echo $this->form->getLabel('title'); ?>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
				</li>

				<?php
				if (isset($this->item->err_code))
				{
					if (($this->item->err_code == 101) || ($this->item->err_code == 102))
					{

					}
				}?>
				<li>
					<?php echo $this->form->getLabel('description'); ?>
					<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('access'); ?>
					<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('published'); ?>
					<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
				</li>
			</ul>
		</div>

		<div class="width-40 fltrt span4 control-group">
			<ul class="adminformlist width_50 unstyled">
				<?php
				if (empty($this->item->campaign_id))
				{
					$this->form->setFieldAttribute('campaign_id', 'type', 'hidden');
				}

				?>
				<li>
					<?php echo $this->form->getLabel('campaign_id'); ?>
					<div class="controls"><?php echo $this->form->getInput('campaign_id'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('created_by'); ?>
					<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('created_date'); ?>
					<div class="controls"><?php echo $this->form->getInput('created_date'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('modified_by'); ?>
					<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
				</li>

				<li>
					<?php echo $this->form->getLabel('modified_time'); ?>
					<div class="controls"><?php echo $this->form->getInput('modified_time'); ?></div>
				</li>
			</ul>
		</div>
		<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
		<div class="clearfix"></div>
	</div>
</fieldset>

<fieldset class="adminform">
	<div class="row-fluid">
		<fieldset class="adminform">
			<legend class="required"><?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *</legend>
			<div class="well">
				<div class="width-75 fltlft span9">
					<div class="well-white well-small">
						<fieldset class="adminform">
							<legend>
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span class="editlinktip hasTip hasTooltip"
										title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">&nbsp;
									<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS'); ?>
								</span>
							</legend>
							<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="width-33 fltlft span4">
										<div class="well well-small">
											<fieldset class="adminform">
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
														echo '<div class="width-50 fltlft span6">
															<label class="mailinglist_label noclear checkbox">' . JText::_('COM_BWPOSTMAN_NO_DATA') . '
															</label>
															</div>';
													}
													?>
												</div>
											</fieldset>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</fieldset>
					</div>
				</div>

				<div class="width-25 fltlft span3">
					<div class="well-white well-small">
						<fieldset class="adminform usergroups">
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
						</fieldset>
					</div>
				</div>
				<div class="clr clearfix"></div>
			</div>
		</fieldset>
	</div>
</fieldset>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

Joomla.submitbutton = function (pressbutton)
{
	if (pressbutton == 'campaign.cancel')
	{
		submitform(pressbutton);
		return;
	}

	if ((pressbutton == 'campaign.apply') || (pressbutton == 'campaign.save') || (pressbutton == 'campaign.save2new') || (pressbutton == 'campaign.save2copy'))
	{
		if ($j("input[type=checkbox]:checked").length)
		{
			submitform(pressbutton);
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

