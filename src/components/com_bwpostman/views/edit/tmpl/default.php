<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit default template for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidator');

?>

<script type="text/javascript">
/* <![CDATA[ */
	function submitbutton(pressbutton)
	{
		var form	= document.bwp_com_form;
		var fault	= false;

		form.edit.value = pressbutton;

		// Validate input fields only, if unsubscribe is not selected
		if (form.unsubscribe.checked == false)
		{
			if (document.bwp_com_form.name)
			{
				if (form.name_field_obligation.value == 1)
				{
					if (form.name.value == "")
					{
						alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_NAME', true); ?>");
						fault = true;
					}
				}
			}

			if (document.bwp_com_form.firstname)
			{
				if (form.firstname_field_obligation.value == 1)
				{
					if (form.firstname.value == "")
					{
						alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_FIRSTNAME', true); ?>");
						fault = true;
					}
				}
			}
			if (document.bwp_com_form.special)
			{
				if (form.special_field_obligation.value == 1)
				{
					if (form.special.value == "")
					{
						alert('<?php echo JText::sprintf("COM_BWPOSTMAN_SUB_ERROR_SPECIAL", JText::_($this->params->get("special_label"))); ?>');
						fault = true;
					}
				}
			}
			if (form.email.value== "")
			{
				alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL', true); ?>");
				fault	= true;
			}
			if (checkNlBoxes()== false)
			{
				alert ("<?php echo JText::_('COM_BWPOSTMAN_ERROR_NL_CHECK', true); ?>");
				fault	= true;
			}
		}
		if (fault == false)
		{
			form.submit();
		}
		function checkNlBoxes()
		{
			var arrCB = form.elements['mailinglists[]'];
			var n =	arrCB.length;
			var check = 0;
			var i = 0;
			if (n > 1)
			{
				for (i = 0; i < n; i++)
				{
					if (arrCB[i].checked == true)
					{
						check++;
					}
				}
			}
			else
			{
				check++;
			}
			if (check == 0)
			{
				return false;
			}
		}
	}
/* ]]> */
</script>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo JText::_('WARNING'); ?></h4>
			<div>
				<p><?php echo JText::_('COM_BWPOSTMAN_JAVASCRIPTWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman">
	<div id="bwp_com_edit_subscription">
		<?php if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>

		<div class="content_inner">
			<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
					name="bwp_com_form" class="form-validate form-inline">
				<?php
				echo JLayoutHelper::render(
					'default',
					array('subscriber' => $this->subscriber, 'params' => $this->params, 'lists' => $this->lists),
					$basePath = JPATH_COMPONENT . '/layouts/subscriber'
				);
				?>

				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<p class="edit_unsubscribe">
						<input title="unsubscribe" type="checkbox" id="unsubscribe" name="unsubscribe" value="1" />
						<span class="edit_unsubscribe_text"><?php echo JText::_('COM_BWPOSTMAN_UNSUBSCRIBE') ?></span>
					</p>
				</div>

				<div class="maindivider<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"></div>

				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<p class="edit_required">
						<?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</p>
				</div>

				<button class="button validate save btn" type="button" onclick="return submitbutton('submit');">
					<?php echo JText::_('COM_BWPOSTMAN_BUTTON_EDIT'); ?>
				</button>
				<?php if (JFactory::getUser()->get('guest')):   ?>
					<button class="button validate leave btn" type="button" onclick="return submitbutton('submitleave');">
						<?php echo JText::_('COM_BWPOSTMAN_BUTTON_LEAVEEDIT'); ?>
					</button>
				<?php endif; ?>

				<input type="hidden" name="option" value="com_bwpostman" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="view" value="edit" />
				<input type="hidden" name="edit" value="" />
				<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" />
				<input type="hidden" name="name_field_obligation" value="<?php echo $this->params->get('name_field_obligation'); ?>" />
				<input type="hidden" name="firstname_field_obligation" value="<?php echo $this->params->get('firstname_field_obligation'); ?>" />
				<input type="hidden" name="special_field_obligation" value="<?php echo $this->params->get('special_field_obligation'); ?>" />
				<input type="hidden" name="show_name_field" value="<?php echo $this->params->get('show_name_field'); ?>" />
				<input type="hidden" name="show_firstname_field" value="<?php echo $this->params->get('show_firstname_field'); ?>" />
				<input type="hidden" name="show_special" value="<?php echo $this->params->get('show_special'); ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</form>

			<?php
			if ($this->params->get('show_boldt_link') === '1')
			{ ?>
				<p class="bwpm_copyright"><?php echo BwPostman::footer(); ?></p>
			<?php
			} ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		// Turn radios into btn-group
		jQuery('.radio.btn-group label').addClass('btn');
		jQuery(".btn-group label:not(.active)").click(function()
		{
			var label = jQuery(this);
			var input = jQuery('#' + label.attr('for'));

			if (!input.prop('checked'))
			{
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '')
				{
					label.addClass('active btn-primary');
				}
				else if (input.val() == 0)
				{
					label.addClass('active btn-danger');
				}
				else
				{
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		jQuery(".btn-group input[checked=checked]").each(function()
		{
			if (jQuery(this).val() == '')
			{
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
			}
			else if (jQuery(this).val() == 0)
			{
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
			}
			else
			{
				jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	})
</script>
