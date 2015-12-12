<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit preview template for backend.
 *
 * @version 1.2.4 bwpm
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

JHtml::_('bootstrap.tooltip');
JHTML::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$image = JHTML::_('image', 'administrator/templates/'. $this->template .'/images/menu/icon-16-info.png', JText::_('COM_BWPOSTMAN_NOTES'));
?>

<script type="text/javascript">
/* <![CDATA[ */
function changeTab(tab){
	if (tab != 'edit_preview') {
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
		document.adminForm.task.setAttribute('value','newsletter.apply');
		submitform(pressbutton);
		return;
	} 

	if (pressbutton == 'newsletter.save') {
		document.adminForm.task.setAttribute('value','newsletter.save');
		submitform(pressbutton);
		return;
	} 
}
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
				<li class="open">
					<button onclick="return changeTab('edit_preview');" class="buttonAsLink_open">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_send');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP5'); ?>
					</button>
				</li>
			</ul>
		</div>
		<div class="clr clearfix"></div>
		
		<div class="tab-wrapper-bwp">
			<fieldset class="adminform">
				<legend><?php echo JTEXT::_('COM_BWPOSTMAN_NL_HEADER'); ?></legend>
				<div class="well well-small">
					<table class="admintable">
						<tr>
							<td align="right"><strong><?php echo JText::_('COM_BWPOSTMAN_NL_FROM_NAME'); echo ':'; ?></strong></td>
							<td><?php echo $this->item->from_name;?></td></tr>
						<tr>
							<td align="right"><strong><?php echo JText::_('COM_BWPOSTMAN_NL_FROM_EMAIL'); echo ':'; ?></strong></td>
							<td><?php echo $this->item->from_email;?></td>
						</tr>
						<tr>
							<td align="right"><strong><?php echo JText::_('COM_BWPOSTMAN_NL_REPLY_EMAIL'); echo ':'; ?></strong></td>
							<td><?php echo $this->item->reply_email;?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td align="right"><strong><?php echo JText::_('COM_BWPOSTMAN_NL_SUBJECT'); echo ':'; ?></strong></td>
							<td><?php echo $this->item->subject;?></td>
						</tr>
					</table>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JTEXT::_('COM_BWPOSTMAN_NL_PREVIEW_HTML'); ?></legend>
				<div class="well well-small">
					<div><iframe name="myIframeHtml"
						src="index.php?option=com_bwpostman&amp;view=newsletter&amp;layout=newsletter_html_preview&amp;format=raw&amp;task=previewHTML&amp;nl_id=<?php echo $this->item->id; ?>"
						height="500" width="100%" style="border: 1px solid #999999;"></iframe>
					</div>
				</div>
			</fieldset>
			
			<fieldset class="adminform">
				<legend><?php echo JTEXT::_('COM_BWPOSTMAN_NL_PREVIEW_TEXT'); ?></legend>
				<div class="well well-small">
					<div><iframe name="myIframeText"
						src="index.php?option=com_bwpostman&amp;view=newsletter&amp;layout=newsletter_text_preview&amp;format=raw&amp;task=previewText&amp;nl_id=<?php echo $this->item->id; ?>"
						height="400" width="100%" style="border: 1px solid #999999;"></iframe>
					</div>
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
		<input type="hidden" id="layout" name="layout" value="edit_preview" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_preview" /><!-- value can change if one clicks on another tab -->
		<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
		<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
		<input type="hidden" name="add_content" value="" />
		<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
		<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
		