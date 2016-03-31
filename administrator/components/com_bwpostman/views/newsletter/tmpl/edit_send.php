<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit send template for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHTML::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$image = '<i class="icon-info"></i>';

$image_testrecipients	= JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/send.png', JText::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS'));
$image_newsletter		= JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/send_f2.png', JText::_('COM_BWPOSTMAN_NL_SENDMAIL'));
?>

<script type="text/javascript">
/* <![CDATA[ */

// This function stay here instead of external JS file to get nearly free of parameters on buttons
function changeTab(tab, task){
	if (tab != 'edit_send') {
//		document.adminForm.layout.setAttribute('value',tab);
		document.adminForm.tab.setAttribute('value',tab);
		document.adminForm.task.setAttribute('value','newsletter.changeTab');
		return true;
	}
	else {
		return false;
	}
}

Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'newsletter.cancel') {
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'newsletter.back') {
		form.task.value = 'back';
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'newsletter.apply') {
		form.task.setAttribute('value','newsletter.apply');
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'newsletter.save') {
		form.task.setAttribute('value','newsletter.save');
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'newsletter.sendmail') {
		confirmSendNl = confirm("<?php echo JText::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING', true); ?>");
		if (confirmSendNl == true) {
			form.task.setAttribute('value','newsletter.sendmail');
			submitform(pressbutton);
		}
	}

	if (pressbutton == 'newsletter.sendmailandpublish') {
		confirmSendNl = confirm("<?php echo JText::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING_AND_PUBLISH', true); ?>");
		if (confirmSendNl == true) {
			form.task.setAttribute('value','newsletter.sendmail');
			submitform(pressbutton);
		}
	}

	if (pressbutton == 'newsletter.sendtestmail') {
		confirmSendNl = confirm("<?php echo JText::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING', true); ?>");
		if (confirmSendNl == true) {
			form.task.setAttribute('value','newsletter.sendmail');
			submitform(pressbutton);
		}
	}
};
/* ]]> */
</script>

<div id="bwp_view_single">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
		<div class="form-horizontal">
			<ul class="bwp_tabs">
				<li class="closed">
					<button onclick="return changeTab('edit_basic');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP1'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_html');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP2'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_text');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_preview');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</button>
				</li>
				<li class="open">
					<button onclick="return changeTab('edit_send');" class="buttonAsLink_open">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP5'); ?>
					</button>
				</li>
			</ul>
		</div>
		<div class="clr clearfix"></div>



		<div class="tab-wrapper-bwp">
			<fieldset class="adminform">
				<legend><?php echo JTEXT::_('COM_BWPOSTMAN_NL_SENDMAIL'); ?></legend>
				<div class="well well-small">
					<table class="admintable">
						<tr valign="top">
							<td width="40"><?php echo $image_newsletter; ?></td>
							<td>
								<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS'); ?>
								<br /><br />
								<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS_NOTE'); ?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><?php echo JText::_('COM_BWPOSTMAN_NL_SEND_OPTIONS');?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><label class="checkbox"><input type="checkbox" id="send_to_unconfirmed" name="send_to_unconfirmed" />&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_TO_UNCONFIRMED');?></label></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input class="input-mini inputbox" name="mails_per_pageload" id="mails_per_pageload" size="4" maxlength="10"
									value="<?php echo $this->params->get('default_mails_per_pageload');?>" />
								<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD'); ?>&nbsp;
								<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD_NOTE'); ?>"><?php echo $image; ?></span>
								<br /><br />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php if ($this->canDo->get('core.send')) : ?>
									<input class="btn" type="button" onclick="Joomla.submitbutton('newsletter.sendmail');"
										value="<?php echo JText::_('COM_BWPOSTMAN_NL_SENDMAIL_BUTTON'); ?>" />
									<input class="btn" type="button" onclick="Joomla.submitbutton('newsletter.sendmailandpublish');"
										value="<?php echo JText::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>" title="<?php echo JText::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>" />
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo JTEXT::_('COM_BWPOSTMAN_NL_SENDTESTMAIL'); ?></legend>
				<div class="well well-small">
					<table class="admintable">
						<tr valign="top">
							<td width="40"><?php echo $image_testrecipients; ?></td>
							<td><?php echo JText::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS'); ?>
								<br /><br />
								<?php echo JText::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS_NOTE'); ?>
								<br /><br />
								<input type="hidden" id="send_to_unconfirmed" name="send_to_unconfirmed" value="0" />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php if ($this->canDo->get('core.send')) : ?>
									<input class="btn" type="button" onclick="Joomla.submitbutton('newsletter.sendtestmail');"
										value="<?php echo JText::_('COM_BWPOSTMAN_NL_SENDTESTMAIL_BUTTON'); ?>" />
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>
			</fieldset>
		</div>

		<?php
			foreach($this->form->getFieldset('basic_1_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('basic_2_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('html_version_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('text_version_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('templates_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('selected_content_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('available_content_hidden') as $field) echo $field->input;
			foreach($this->form->getFieldset('publish_hidden') as $field) echo $field->input;
		?>

		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="layout" name="layout" value="edit_send" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_send" /><!-- value can change if one clicks on another tab -->
		<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
		<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
		<input type="hidden" name="add_content" value="" />
		<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
		<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
