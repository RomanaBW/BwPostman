<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber template for backend.
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

use Joomla\CMS\HTML\HTMLHelper;

// declare image for tooltip
$image = '';

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidator');

$image = '<i class="icon-info"></i>';

// Load the tooltip behavior for the notes
//JHtml::_('behavior.modal');
JHtml::_('behavior.keepalive');

$new_test	= JFactory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', $this->item->status);
?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'subscriber.cancel')
		{
			Joomla.submitform(pressbutton, form);
			return;
		}
		else
		{
			var isValid=true;
			var action = pressbutton.split('.');

			if (action[1] != 'cancel' && action[1] != 'close')
			{
				var forms = jQuery('form.form-validate');
				for (var i = 0; i < forms.length; i++)
				{
					if (!document.formvalidator.isValid(forms[i]))
					{
						isValid = false;
						break;
					}
				}
			}

			if (isValid)
			{
				Joomla.submitform(pressbutton, form);
				return true;
			}
		}
	};

	// This function changes the layout-value if the checkbox 'confirm' exists and if it is not checked
	function checkConfirmBox() {

		var form = document.adminForm;

		cb = document.getElementById('confirm');

		// Does the checkbox 'confirm' exist?
		if(cb == null)
		{
			return;
		}

		if (form.jform_confirm.checked == false)
		{
			form.layout.value = 'unconfirmed';
		}
	}
/* ]]> */
</script>

<div id="bwp_editform">
	<?php
	if ($this->queueEntries)
	{
		\JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&layout=edit&id='.(int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<div class="tab-wrapper-bwp">
			<?php echo JHtml::_('uitab.startTabSet', 'subscriber_tabs', array('active' => 'details')); ?>
			<?php echo JHtml::_(
				'uitab.addTab',
				'subscriber_tabs',
				'details',
				is_null($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_SUB') : JText::sprintf('COM_BWPOSTMAN_EDIT_SUB', $this->item->id)
			); ?>
			<div class="row">
				<div class="col-md-6">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('gender'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('gender'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('firstname'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('firstname'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('name'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('name'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('email'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('email'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('emailformat'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('emailformat'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('special'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('special'); ?>
						</div>
					</div>

					<?php if ($new_test != '9') { ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('status'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('status'); ?>
							</div>
						</div>
					<?php } else { ?>
							<input id="jform_status" type="hidden" value="9" name="jform[status]">
					<?php } ?>
				</div>

				<div class="col-md-6">
					<a class="modal btn btn-info btn-block" href="
						<?php echo JRoute::_(
						'index.php?option=com_bwpostman&view=subscriber&layout=print&format=raw&task=insideModal&id='
						. (int) $this->item->id
					); ?>" rel="{handler: 'iframe', size: {x: 700, y: 500}, iframeOptions: {name: 'subsData'}}">
						<?php echo JText::_('COM_BWPOSTMAN_PRINT_SUB_DAT'); ?>
					</a>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('confirmation_date'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('confirmation_date'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
					<?php echo $this->form->getLabel('confirmed_by'); ?>
						</div>
						<div class="controls">
					<?php echo $this->form->getInput('confirmed_by'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('confirmation_ip'); ?>
						</div>
					<div class="controls">
						<?php echo $this->form->getInput('confirmation_ip'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registration_date'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registration_date'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registered_by'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registered_by'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registration_ip'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registration_ip'); ?>
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
				<div class="clr clearfix"></div>
				<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
			</div>

			<?php if ($new_test != '9') : ?>
				<div class="row">
					<legend>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
						<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE'); ?></span>
					</legend>
					<div class="col-md-4">
						<div class="well well-small">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>">
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
												<?php JText::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="col-md-4">
						<div class="well well-small">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>">
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
												<?php JText::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="col-md-4">
						<div class="well well-small">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>">
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
												<?php JText::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			<?php  endif ?>
			<?php echo JHtml::_('uitab.endTab'); ?>
			<?php
			if (BwPostmanHelper::canAdmin('subscriber')): ?>
				<?php echo JHtml::_('uitab.addTab', 'subscriber_tabs', 'permissions', JText::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES')); ?>
				<div class="row">
					<div class="col-md-12">
						<fieldset class="adminform">
							<?php echo $this->form->getInput('rules'); ?>
						</fieldset>
					</div>
				</div>
				<?php echo JHtml::_('uitab.endTab'); ?>
			<?php endif
			?>
			<div class="clearfix"></div>
			<?php echo JHtml::_('uitab.endTabSet'); ?>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<?php
				$remote_ip  = \JFactory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

				$this->form->setValue('ip', '', $remote_ip);
				echo $this->form->getInput('ip');
			?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="jform[name_field_obligation]" value="<?php echo $this->obligation['name']; ?>" />
			<input type="hidden" name="jform[firstname_field_obligation]" value="<?php echo $this->obligation['firstname']; ?>" />
			<input type="hidden" name="jform[special_field_obligation]" value="<?php echo $this->obligation['special']; ?>" />
			<input type="hidden" id="jform_title" name="jform[title]" value="<?php echo $this->form->getValue('title') ?>">
			<?php echo $this->form->getInput('asset_id'); ?>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
