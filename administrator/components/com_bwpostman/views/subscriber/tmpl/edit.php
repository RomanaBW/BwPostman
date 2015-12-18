<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber template for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

// declare image for tooltip
$image = '';

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$image = '<i class="icon-info"></i>';

// Load the tooltip behavior for the notes
JHTML::_('behavior.keepalive');

$new_test	= JFactory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', $this->item->status);
?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton) {

		var form = document.adminForm;
		if (pressbutton == 'subscriber.cancel') {
			submitform(pressbutton);
			return;
		}
		// Valdiate input fields
		if ((form.jform_name.value == "") && (form.name_field_obligation.value == 1)){
			alert("<?php echo JText::_('COM_BWPOSTMAN_SUB_ERROR_NAME', true); ?>");
		} else if ((form.jform_firstname.value == "") && (form.firstname_field_obligation.value == 1)){
				alert("<?php echo JText::_('COM_BWPOSTMAN_SUB_ERROR_FIRSTNAME', true); ?>");
		} else if (form.jform_email.value== ""){
			alert("<?php echo JText::_('COM_BWPOSTMAN_SUB_ERROR_EMAIL', true); ?>");
		} else {
			submitform(pressbutton);
		}
	}

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
		if ($this->queueEntries) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
 		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<div class="tab-wrapper-bwp">
			<fieldset class="adminform">
				<legend><?php echo empty($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_SUB') : JText::sprintf('COM_BWPOSTMAN_EDIT_SUB', $this->item->id); ?></legend>
				<div class="well well-small">
					<div class="width-60 fltlft span8 control-group">
						<ul class="adminformlist unstyled">
							<li>
								<?php echo $this->form->getLabel('firstname'); ?>
								<div class="controls"><?php echo $this->form->getInput('firstname'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('name'); ?>
								<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('email'); ?>
								<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('emailformat'); ?>
								<div class="controls"><?php echo $this->form->getInput('emailformat'); ?></div>
							</li>

							<?php if ($new_test != '9') { ?>
								<li>
									<?php echo $this->form->getLabel('status'); ?>
									<div class="controls"><?php echo $this->form->getInput('status'); ?></div>
								</li>
							<?php } else { ?>
								<div class="controls"><input id="jform_status" type="hidden" value="9" name="jform[status]"></div>
							<?php } ?>
						</ul>
					</div>

					<div class="width-40 fltrt span4 control-group">
						<ul class="adminformlist width_50 unstyled">
							<li>
								<?php echo $this->form->getLabel('confirmation_date'); ?>
								<div class="controls"><?php echo $this->form->getInput('confirmation_date'); ?></div>
							</li>

						<li>
								<?php echo $this->form->getLabel('confirmed_by'); ?>
								<div class="controls"><?php echo $this->form->getInput('confirmed_by'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('confirmation_ip'); ?>
								<div class="controls"><?php echo $this->form->getInput('confirmation_ip'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('registration_date'); ?>
								<div class="controls"><?php echo $this->form->getInput('registration_date'); ?></div>
							</li>

						<li>
								<?php echo $this->form->getLabel('registered_by'); ?>
								<div class="controls"><?php echo $this->form->getInput('registered_by'); ?></div>
							</li>

							<li>
								<?php echo $this->form->getLabel('registration_ip'); ?>
								<div class="controls"><?php echo $this->form->getInput('registration_ip'); ?></div>
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
					<div class="clr clearfix"></div>
					<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
				</div>
			</fieldset>

			<?php if ($new_test != '9') : ?>
				<div class="width-100 fltlft row-fluid">
					<fieldset class="adminform">
						<legend>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE_NOTE'); ?>"><?php echo $image; ?></span>
							<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE'); ?></span>
						</legend>
						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>"><?php echo $image; ?></span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_available'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
											$ml_available	= $this->form->getInput('ml_available');

											if (!empty($ml_available)) echo $this->form->getInput('ml_available');
											else echo '<div class="width-50 fltlft span6"><label class="mailinglist_label noclear checkbox">'. JText::_('COM_BWPOSTMAN_NO_DATA') .'</label></div>';
										?>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>"><?php echo $image; ?></span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_unavailable'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
											$ml_unavailable	= $this->form->getInput('ml_unavailable');

											if (!empty($ml_unavailable)) { echo $this->form->getInput('ml_unavailable'); }
											else { echo '<div class="width-50 fltlft span6"><label class="mailinglist_label noclear checkbox">'. JText::_('COM_BWPOSTMAN_NO_DATA') .'</label></div>'; }
										?>
									</div>
								</fieldset>
							</div>
						</div>

						<div class="width-33 fltlft span4">
							<div class="well well-small">
								<fieldset class="adminform">
									<legend>
										<span class="editlinktip hasTip hasTooltip"	title="<?php echo JText::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>"><?php echo $image; ?></span>
										<span>&nbsp;<?php echo $this->form->getLabel('ml_intern'); ?></span>
									</legend>
									<div class="row-fluid clearfix">
										<?php
											$ml_intern	= $this->form->getInput('ml_intern');

											if (!empty($ml_intern)) { echo $this->form->getInput('ml_intern'); }
											else { echo '<div class="width-50 fltlft span6"><label class="mailinglist_label noclear checkbox">'. JText::_('COM_BWPOSTMAN_NO_DATA') .'</label></div>'; }
										?>
									</div>
								</fieldset>
							</div>
						</div>
					</fieldset>
				</div>
			<?php  endif ?>
			<?php if ($this->canDo->get('core.admin')): ?>
				<div class="fltlft">
					<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

						<?php echo JHtml::_('sliders.panel', JText::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES'), 'access-rules'); ?>
						<div class="well well-small">
							<fieldset class="panelform">
								<?php echo $this->form->getLabel('rules'); ?>
								<?php echo $this->form->getInput('rules'); ?>
							</fieldset>
						</div>
					<?php echo JHtml::_('sliders.end'); ?>
				</div>
			<?php endif ?>
		</div>
		<div class="clr"></div>
		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

		<?php
			$this->form->setValue('ip', '', $_SERVER['REMOTE_ADDR']);
			echo $this->form->getInput('ip');
		?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="name_field_obligation" value="<?php echo $this->obligation['name']; ?>" />
		<input type="hidden" name="firstname_field_obligation" value="<?php echo $this->obligation['firstname']; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
