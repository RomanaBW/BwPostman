<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit publish template for backend.
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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$image = '<i class="icon-info"></i>';
?>

<script type="text/javascript">
/* <![CDATA[ */
Joomla.submitbutton = function (pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'newsletter.cancel')
	{
		Joomla.submitform(pressbutton, form);
		return;
	}

	if (pressbutton == 'newsletter.publish_save')
	{
		form.task.setAttribute('value','newsletter.publish_save');
		Joomla.submitform(pressbutton, form);
	}

	if (pressbutton == 'newsletter.publish_apply')
	{
		form.task.setAttribute('value','newsletter.publish_apply');
		Joomla.submitform(pressbutton, form);
	}
};
/* ]]> */
</script>

<div id="bwp_view_single">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=newsletter'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="form-horizontal">
			<fieldset class="adminform">
				<legend>
					<?php echo JText::sprintf('COM_BWPOSTMAN_NL_EDIT_PUBLISHED', $this->item->id); ?>
				</legend>
				<div class="well well-small">
					<div class="width-50 fltlft span6 control-group">
						<ul class="adminformlist unstyled">
							<?php foreach($this->form->getFieldset('edit_publish') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
										<div class="controls"><?php echo $field->input; ?></div></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="width-50 fltlft span6 control-group">
						<ul class="adminformlist unstyled">
							<?php foreach($this->form->getFieldset('basic_2') as $field): ?>
								<?php if ($field->hidden): ?>
									<li><?php echo $field->input; ?></li>
								<?php else: ?>
									<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
										<div class="controls"><?php echo $field->input; ?></div></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="clr clearfix"></div>
					<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
				</div>
			</fieldset>
		</div>

		<?php
		foreach($this->form->getFieldset('basic_1_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('selected_content_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('available_content_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('html_version_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('text_version_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('templates_hidden') as $field)
		{
			echo $field->input;
		}
		?>

		<div class="clr clearfix"></div>

		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="publish_save" />
		<input type="hidden" id="layout" name="layout" value="edit_publish" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_publish" /><!-- value can change if one clicks on another tab -->
		<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
		<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
		<input type="hidden" name="add_content" value="" />
		<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
		<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
