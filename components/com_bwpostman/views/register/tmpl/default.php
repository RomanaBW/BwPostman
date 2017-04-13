<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register default template for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

// Depends on jQuery UI
JHtml::_('jquery.ui', array('core'));

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');

$remote_ip  = JFactory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
?>

<script type="text/javascript">
/* <![CDATA[ */

function checkRegisterForm() {

	var form = document.bwp_com_form;
	var errStr = "";
	var arrCB = document.bwp_com_form.elements['mailinglists[]'];
	var n =	arrCB.length;
	var check = 0;

	// Validate input fields
  // firstname
	if (document.bwp_com_form.firstname)
	{
		if (((document.bwp_com_form.getElementById("firstname").value == "" || (document.bwp_com_form.getElementById("firstname").value == "<?php echo JText::_('COM_BWPOSTMAN_FIRSTNAME'); ?>")))
			&& (document.bwp_com_form.getElementById("firstname_field_obligation").value == 1))
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_FIRSTNAME', true); ?>\n";
		}
	}
  // name
	if (document.bwp_com_form.name)
	{
		if (((document.bwp_com_form.getElementById("name").value == "") || (document.bwp_com_form.getElementById("name").value == "<?php echo JText::_('COM_BWPOSTMAN_NAME'); ?>"))
			&& (document.bwp_com_form.getElementById("name_field_obligation").value == 1))
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_NAME', true); ?>\n";
		}
	}
	// additional field
	if (document.bwp_com_form.special)
	{
		if (((document.bwp_com_form.getElementById("special").value == "") || (document.bwp_com_form.getElementById("special").value == "<?php echo JText::_($this->params->get('special_label')); ?>"))
			&& (document.bwp_com_form.getElementById("special_field_obligation").value == 1))
		{
			errStr += "<?php echo JText::sprintf('COM_BWPOSTMAN_SUB_ERROR_SPECIAL', JText::_($this->params->get('special_label'))); ?>\n";
		}
	}
  // email
  var email = document.bwp_com_form.getElementById("email").value;
	if (email == "" || (email == "<?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>"))
	{
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL', true); ?>\n";
	}
	else
	{
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!filter.test(email))
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL_INVALID', true); ?>\n";
			email.focus;
		}
	}
  // mailinglist
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
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_NL_CHECK'); ?>\n";
	}
	// disclaimer
	if (document.bwp_com_form.agreecheck)
	{
		if (document.bwp_com_form.agreecheck.checked == false)
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_DISCLAIMER_CHECK'); ?>\n";
		}
	  }
	  // captcha
	if (document.bwp_com_form.stringCaptcha)
	{
		if (document.bwp_com_form.stringCaptcha.value == '')
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_CAPTCHA_CHECK'); ?>\n";
		}
	}
	// question
	if (document.bwp_com_form.stringQuestion)
	{
		if (document.bwp_com_form.stringQuestion.value == '')
		{
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_CAPTCHA_CHECK'); ?>\n";
		}
	}
	if ( errStr !== "" )
	{
		alert( errStr );
		return false;
	}
	else
	{
		form.submit();
	}
}
	/* ]]> */
</script>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo JText::_('WARNING'); ?></h4>
			<div>
				<p><?php echo JText::_('COM_BWPOSTMAN_JAVAWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman">
	<div id="bwp_com_register">
		<?php // displays a message if no availible mailinglist
		if ($this->lists['available_mailinglists'])
		{
			if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) : ?>
				<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->params->escape($this->params->get('page_heading')); ?></h1>
			<?php endif; ?>

		<div class="content_inner">
			<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=register'); ?>" method="post" id="bwp_com_form" name="bwp_com_form" class="form-validate form-inline" onsubmit="return checkRegisterForm();">
				<?php // Spamcheck 1 - Input-field: class="user_highlight" style="position: absolute; top: -5000px;" ?>
				<p class="user_hightlight">
					<label for="falle"><strong><?php echo addslashes(JText::_('COM_BWPOSTMAN_SPAMCHECK')); ?></strong></label>
					<input type="text" name="falle" id="falle" size="20"  title="<?php echo addslashes(JText::_('COM_BWPOSTMAN_SPAMCHECK')); ?>" maxlength="50" />
				</p>
				<?php // End Spamcheck

				echo JLayoutHelper::render('default', array('subscriber' => $this->subscriber, 'params' => $this->params, 'lists' => $this->lists), $basePath = JPATH_COMPONENT .'/layouts/subscriber');
				?>
				<?php // Question
				if ($this->params->get('use_captcha') == 1) : ?>
					<div class="question">
						<p class="question-text"><?php echo JText::_('COM_BWPOSTMAN_CAPTCHA'); ?></p>
						<p class="security_question_lbl"><?php echo $this->params->get('security_question'); ?></p>
						<p class="question-result input-append">
							<label id="question" for="stringQuestion"><?php echo JText::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringQuestion" id="stringQuestion" size="40" maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
				<?php endif; // End question ?>

				<?php // Captcha
				if ($this->params->get('use_captcha') == 2) :
					$codeCaptcha = md5(microtime());
					?>

					<div class="captcha">
						<p class="captcha-text"><?php echo JText::_('COM_BWPOSTMAN_CAPTCHA'); ?></p>
						<p class="security_question_lbl"><img src="<?php echo JUri::base();?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" /></p>
						<p class="captcha-result input-append">
							<label id="captcha" for="stringCaptcha"><?php echo JText::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringCaptcha" id="stringCaptcha" size="40" maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
					<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
				<?php endif; // End captcha ?>

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php // Show Disclaimer only if enabled in basic parameters
					if ($this->params->get('disclaimer')) :
						?>
						<p class="agree_check">
							<input title="agreecheck" type="checkbox" id="agreecheck" name="agreecheck" />
							<?php // Disclaimer article and target_blank or not
							if ($this->params->get('disclaimer_selection') == 1 && $this->params->get('article_id') > 0) {

								?>
								<span><?php $disclaimer_link = JRoute::_(ContentHelperRoute::getArticleRoute($this->params->get('article_id'))); echo '<a href="'.$disclaimer_link.'"'; if ($this->params->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('COM_BWPOSTMAN_DISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
								<?php
							}
							// Disclaimer menu item and target_blank or not
							elseif ($this->params->get('disclaimer_selection') == 2 && $this->params->get('disclaimer_menuitem') > 0)
							{
								?>
								<span><?php $disclaimer_link = JRoute::_('index.php?Itemid=' . $this->params->get('disclaimer_menuitem')); echo '<a href="'.$disclaimer_link.'"'; if ($this->params->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('COM_BWPOSTMAN_DISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
							<?php }
							else {
								// Disclaimer url and target_blank or not
								?>
								<span><?php echo '<a href="'. $this->params->get('disclaimer_link') . '"'; if ($this->params->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('COM_BWPOSTMAN_DISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
							<?php } ?>
						</p>
					<?php endif; // Show disclaimer ?>
					<p class="show_disclaimer">
						<?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</p>
				</div>

				<p class="button-register text-right"><button class="button validate btn text-right" type="submit"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_REGISTER'); ?></button></p>

			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="view" value="register" />
			<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" />
			<input type="hidden" name="bwp-<?php echo $this->captcha; ?>" value="1" />
			<input type="hidden" name="name_field_obligation" id="name_field_obligation" value="<?php echo $this->params->get('name_field_obligation'); ?>" />
			<input type="hidden" name="firstname_field_obligation" id="firstname_field_obligation" value="<?php echo $this->params->get('firstname_field_obligation'); ?>" />
			<input type="hidden" name="special_field_obligation" id="special_field_obligation" value="<?php echo $this->params->get('special_field_obligation'); ?>" />
			<input type="hidden" name="show_name_field" value="<?php echo $this->params->get('show_name_field'); ?>" />
			<input type="hidden" name="show_firstname_field" value="<?php echo $this->params->get('show_firstname_field'); ?>" />
			<input type="hidden" name="show_special" value="<?php echo $this->params->get('show_special'); ?>" />
			<input type="hidden" name="registration_ip" value="<?php echo $remote_ip; ?>" />
			<?php echo JHtml::_('form.token'); ?>
			</form>

			<?php
		}
		else
		{
			echo JText::_('COM_BWPOSTMAN_MESSAGE_NO_AVAILIBLE_MAILINGLIST');
		}
		?>
		<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
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
