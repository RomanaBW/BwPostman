<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglist form template for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
defined ('_JEXEC') or die ('Restricted access');

// Load the tooltip behavior for the notes
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton)
	{

		var form = document.adminForm;

		if (pressbutton == 'mailinglist.cancel')
		{
			submitform(pressbutton);
			return;
		}

		// Validate input fields
		if (form.jform_title.value == "")
		{
			alert("<?php echo JText::_('COM_BWPOSTMAN_ML_ERROR_TITLE', true); ?>");
		}
		else if (form.jform_description.value== "")
		{
			alert("<?php echo JText::_('COM_BWPOSTMAN_ML_ERROR_DESCRIPTION', true); ?>");
		}
		else
		{
			submitform(pressbutton);
		}
	};
/* ]]> */
</script>

<div id="bwp_editform">
	<?php
		if ($this->queueEntries)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
 		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&task=edit.save'); ?>"
			method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<div class="tab-wrapper-bwp">
			<fieldset class="adminform">
				<legend><?php echo empty($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_ML') : JText::sprintf('COM_BWPOSTMAN_EDIT_ML', $this->item->id); ?></legend>
				<div class="well well-small">
					<div class="width-60 fltlft span8 control-group">
						<ul class="adminformlist unstyled">
							<?php if (isset($this->item->err_code)) if (($this->item->err_code == 201) || ($this->item->err_code == 203)) ?>
							<li>
								<?php echo $this->form->getLabel('title'); ?>
								<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
							</li>

							<?php if (isset($this->item->err_code)) if ($this->item->err_code == 202) // echo "class=\"invalid\""; ?>
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
					<div class="clearfix"></div>
					<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
				</div>
			</fieldset>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

			<?php echo $this->form->getInput('id'); ?>
			<?php echo $this->form->getInput('asset_id'); ?>
			<?php echo $this->form->getInput('checked_out'); ?>
			<?php echo $this->form->getInput('archive_flag'); ?>
			<?php echo $this->form->getInput('archive_time'); ?>
			<?php echo JHtml::_('form.token'); ?>

			<?php if (BwPostmanHelper::canAdmin()): ?>
				<div class="fltlft">
					<?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
					<?php echo JHtml::_('sliders.panel', JText::_('COM_BWPOSTMAN_ML_FIELDSET_RULES'), 'access-rules'); ?>
				<div class="well well-small">
						<fieldset class="panelform">
							<?php echo $this->form->getLabel('rules'); ?>
							<?php echo $this->form->getInput('rules');  ?>
						</fieldset>
				</div>
					<?php echo JHtml::_('sliders.end'); ?>
				</div>
			<?php endif ?>
			<div class="clearfix"></div>
		</div>
		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
	</form>
