<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register default template for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');

// Depends on jQuery UI
JHtml::_('jquery.ui', array('core'));

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');

global $arguments;

?>

<script type="text/javascript">
/* <![CDATA[ */

function checkRegisterForm() {

	var form = document.bwp_com_form;
	var errStr = "";
	var arrCB = document.bwp_com_form.elements['mailinglists[]'];
	var n =	arrCB.length;
	var check = 0;

	// Valdiate input fields
  // firstname
  if (((document.bwp_com_form.getElementById("firstname").value == "" || (document.bwp_com_form.getElementById("firstname").value == "<?php echo JText::_('COM_BWPOSTMAN_FIRSTNAME'); ?>"))) && (document.bwp_com_form.getElementById("firstname_field_obligation").value == 1)){
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_FIRSTNAME', true); ?>\n";
	}
  // name
	if (((document.bwp_com_form.getElementById("name").value == "") || (document.bwp_com_form.getElementById("name").value == "<?php echo JText::_('COM_BWPOSTMAN_NAME'); ?>")) && (document.bwp_com_form.getElementById("name_field_obligation").value == 1)){
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_NAME', true); ?>\n";
	}
	// additional field
	if (((document.bwp_com_form.getElementById("special").value == "") || (document.bwp_com_form.getElementById("special").value == "<?php echo JText::_($this->params->get('special_label')); ?>")) && (document.bwp_com_form.getElementById("special_field_obligation").value == 1)){
		errStr += "<?php echo JText::sprintf('COM_BWPOSTMAN_SUB_ERROR_SPECIAL', JText::_($this->params->get('special_label'))); ?>\n";
	}
  // email
  var email = document.bwp_com_form.getElementById("email").value;
	if (email == "" || (email == "<?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>")){
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL', true); ?>\n";
	} else {
  var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(email)) {
		  errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL_INVALID', true); ?>\n";
      email.focus;
    }
  }
  // mailinglist

  	if (n > 1) {
		for (i = 0; i < n; i++) {
			if (arrCB[i].checked == true) {
				check++;
			}
		}
	}
	else {
		check++;
	}

	if (check == 0) {
		errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_NL_CHECK'); ?>\n";
	}
	// disclaimer
	if (document.bwp_com_form.agreecheck) {
		if (document.bwp_com_form.agreecheck.checked == false) {
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_DISCLAIMER_CHECK'); ?>\n";
		}
	  }
	  // captcha
	if (document.bwp_com_form.stringCaptcha) {
		if (document.bwp_com_form.stringCaptcha.value == '') {
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_CAPTCHA_CHECK'); ?>\n";
		}
	}
	// question
	if (document.bwp_com_form.stringQuestion) {
		if (document.bwp_com_form.stringQuestion.value == '') {
			errStr += "<?php echo JText::_('COM_BWPOSTMAN_ERROR_CAPTCHA_CHECK'); ?>\n";
		}
	}
  if ( errStr !== "" ) {
		alert( errStr );
		return false;
	} else {
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
		if ($this->available_mailinglists) {
		?>

			<?php if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) : ?>
				<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			<?php endif; ?>

			<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=register'); ?>" method="post" id="bwp_com_form" name="bwp_com_form" class="form-validate form-inline" onsubmit="return checkRegisterForm();">
				<?php // Spamcheck 1 - Input-field: class="user_hightlight" style="position: absolute; top: -5000px;" ?>
					<p class="user_hightlight">
						<label for="falle"><strong><?php echo addslashes(JText::_('COM_BWPOSTMAN_SPAMCHECK')); ?></strong></label>
						<input type="text" name="falle" id="falle" size="20"  title="<?php echo addslashes(JText::_('COM_BWPOSTMAN_SPAMCHECK')); ?>" maxlength="50" />
					</p>
				<?php // End Spamcheck ?>

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php if ($this->params->get('pretext')) :
						// Show pretext only if set in basic parameters ?>
						<p class="pre_text"><?php echo nl2br($this->params->get('pretext')); ?></p>
					<?php endif;
						// End: Show pretext only if set in basic parameters ?>

					<?php if ($this->user->get('guest')) :
						// Show editlink only if the user is not logged in ?>
						<p class="user_edit"><a href="<?php echo JRoute::_('index.php?option=com_bwpostman&amp;view=edit'); ?>"><?php echo JText::_('COM_BWPOSTMAN_LINK_TO_EDITLINKFORM'); ?></a></p>
					<?php endif;
						// End: Show editlink only if the user is not logged in ?>

					<?php if ($this->params->get('show_gender') == 1) { // Show formfield gender only if enabled in basic parameters ?>
						<div class="edit_gender">
							<label id="gendermsg"> <?php echo JText::_('COM_BWPOSTMAN_GENDER'); ?>:</label>
							<?php echo $this->lists['gender']; ?>
						</div>
					<?php } // End gender ?>

					<?php if ($this->params->get('show_firstname_field') || $this->params->get('firstname_field_obligation')) :
						// Show first name-field only if set in basic parameters ?>
						<p class="user_firstname input<?php echo ($this->params->get('firstname_field_obligation')) ? '-append' : ''?>">
							<label id="firstnamemsg" for="firstname" <?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) echo "class=\"invalid\""; ?>>
								<?php echo JText::_('COM_BWPOSTMAN_FIRSTNAME'); ?>: </label>
							<?php if ($this->params->get('firstname_field_obligation')) : {
								// Is filling out the firstname field obligating ?>
								<input type="text" name="firstname" id="firstname" size="40" value="<?php if (!empty($this->subscriber->firstname)) { echo $this->subscriber->firstname; } ?>"
									class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
									maxlength="50" />  <span class="append-area"><i class="icon-star"></i></span> <?php } else : { ?> <input type="text" name="firstname" id="firstname" size="40" value="<?php if (!empty($this->subscriber->firstname)) { echo $this->subscriber->firstname; } ?>"
									class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
									maxlength="50" />
							<?php } endif;
								// End: Is filling out the firstname field obligating ?>
						</p>
					<?php endif;
					// End: Show first name-field only if set in basic parameters ?>

					<?php if ($this->params->get('show_name_field') || $this->params->get('name_field_obligation')) :
						// Show name-field only if set in basic parameters ?>
						<p class="user_name input<?php echo ($this->params->get('name_field_obligation')) ? '-append' : ''?>">
							<label id="namemsg" for="name" <?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) echo "class=\"invalid\""; ?>>
								<?php echo JText::_('COM_BWPOSTMAN_NAME'); ?>: </label>
							<?php if ($this->params->get('name_field_obligation')) : {
								// Is filling out the name field obligating ?>
								<input type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
									class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
									maxlength="50" />  <span class="append-area"><i class="icon-star"></i></span> <?php } else : { ?> <input type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
								class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
								maxlength="50" /> <?php } endif;
							// End: Is filling out the name field obligating ?>
						</p>
					<?php endif;
					// End: Show name-fields only if set in basic parameters ?>

					<?php if ($this->params->get('show_special') || $this->params->get('special_field_obligation')) : // Show special only if set in basic parameters or required
						if($this->params->get('special_desc') != '')
						{
							$tip    =  JText::_($this->params->get('special_desc'));
						}
						else
						{
							$tip    =  JText::_('COM_BWPOSTMAN_SPECIAL');
						} ?>

						<p class="edit_special input<?php echo ($this->params->get('special_field_obligation')) ? '-append' : ''?>">
							<label id="specialmsg hasTooltip" title="<?php echo JHtml::tooltipText($tip); ?>" for="special"
								<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) echo "class=\"invalid\""; ?>>
								<?php
								if($this->params->get('special_label') != '')
								{
									echo JText::_($this->params->get('special_label'));
								}
								else
								{
									echo JText::_('COM_BWPOSTMAN_SPECIAL');
								}
								?>:
							</label>
							<?php if ($this->params->get('special_field_obligation')) : { // Is filling out the special field obligating ?>
								<input	type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
									class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
									maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
							<?php }
							else : { ?>
								<input	type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
									class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
									maxlength="50" />
							<?php } endif; // End: Is filling out the special field obligating ?>
						</p>
					<?php endif; // End: Show special field only if set in basic parameters ?>

					<p class="user_email input-append">
						<label id="emailmsg" for="email"
							<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1)) echo "class=\"invalid\""; ?>>
							<?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>:
						</label>
						<input type="text" id="email" name="email" size="40" value="<?php echo $this->subscriber->email; ?>"
							class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
							maxlength="100" />  <span class="append-area"><i class="icon-star"></i></span>
					</p>
					<?php if ($this->params->get('show_emailformat') == 1) {
						// Show formfield email format only if enabled in basic parameters ?>
						<div class="user_mailformat">
							<label id="emailformatmsg"> <?php echo JText::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>: </label>
							<?php echo $this->lists['emailformat']; ?>
						</div>
				 	<?php }
					else {
						// hidden field with the default emailformat
						?>
						<input type="hidden" name="emailformat" value="<?php echo $this->params->get('default_emailformat'); ?>" />
					<?php }
					// End emailformat
				?>
				</div>

				<?php if ($this->available_mailinglists) :
				// Show available mailinglists
				?>
				<div class="maindivider<?php echo $this->params->get('pageclass_sfx'); ?>"></div>

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php
						$n	= count($this->available_mailinglists);
						if ($n == 1) { ?>
							<input type="checkbox" style="display: none;" id="<?php echo "mailinglists0"; ?>" name="<?php echo "mailinglists[]"; ?>" value="<?php echo $this->available_mailinglists[0]->id; ?>" checked="checked" />
						<?php }
						else { ?>
							<p class="mail_available">
								<?php echo JText::_('COM_BWPOSTMAN_MAILINGLISTS'); ?>
							</p>
							<?php foreach ($this->available_mailinglists as $i => $item) : ?>
								<p class="mail_available_list">
									<input type="checkbox" id="<?php echo "mailinglists$i"; ?>" name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
										<?php if ((is_array($this->selected_mailinglists)) && (in_array((int)$item->id, $this->selected_mailinglists))) echo "checked=\"checked\""; ?> />
									<span class="mail_available_list_title"><?php echo "$item->title: "; ?></span><?php echo "$item->description"; ?>
								</p>
							<?php endforeach; ?>
							<div class="maindivider<?php echo $this->params->get('pageclass_sfx'); ?>"></div>
						<?php } ?>
					</div>

				<?php endif;
				// End Mailinglists ?>

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php if ($this->params->get('disclaimer')) :
						// Show Disclaimer only if enabled in basic parameters ?>
						<p class="agree_check">
							<input type="checkbox" id="agreecheck" name="agreecheck" />
							<?php if ($this->params->get('disclaimer_selection') == 1 && $this->params->get('article_id') > 0) {
							// Disclaimer article and target_blank or not
							?>
							<span><?php $disclaimer_link = JRoute::_(ContentHelperRoute::getArticleRoute($this->params->get('article_id'))); echo '<a href="'.$disclaimer_link.'"'; if ($this->params->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('COM_BWPOSTMAN_DISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
							<?php }
							elseif ($this->params->get('disclaimer_selection') == 2 && $this->params->get('disclaimer_menuitem') > 0) {
							// Disclaimer menu item and target_blank or not
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
						<p class="security_question_lbl"><img src="<?php echo JURI::base();?>index.php?option=com_bwpostman&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" /></p>
						<p class="captcha-result input-append">
							<label id="captcha" for="stringCaptcha"><?php echo JText::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringCaptcha" id="stringCaptcha" size="40" maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
					<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
				<?php endif; // End captcha ?>

				<p class="button-register text-right"><button class="button validate btn text-right" type="submit"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_REGISTER'); ?></button></p>

				<input type="hidden" name="option" value="com_bwpostman" />
				<input type="hidden" name="task" value="register_save" />
				<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" />
				<input type="hidden" name="bwp-<?php echo $this->captcha; ?>" value="1" />
				<input type="hidden" name="name_field_obligation" id="name_field_obligation" value="<?php echo $this->params->get('name_field_obligation'); ?>" />
				<input type="hidden" name="firstname_field_obligation" id="firstname_field_obligation" value="<?php echo $this->params->get('firstname_field_obligation'); ?>" />
				<input type="hidden" name="special_field_obligation" id="special_field_obligation" value="<?php echo $this->params->get('special_field_obligation'); ?>" />
				<input type="hidden" name="show_name_field" value="<?php echo $this->params->get('show_name_field'); ?>" />
				<input type="hidden" name="show_firstname_field" value="<?php echo $this->params->get('show_firstname_field'); ?>" />
				<input type="hidden" name="show_special" value="<?php echo $this->params->get('show_special'); ?>" />
				<input type="hidden" name="registration_ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
				<?php echo JHTML::_('form.token'); ?>
			</form>

			<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
		<?php } else {
			echo JText::_('COM_BWPOSTMAN_MESSAGE_NO_AVAILIBLE_MAILINGLIST');
		}	?>
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

		if (!input.prop('checked')) {
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if (input.val() == '') {
				label.addClass('active btn-primary');
			} else if (input.val() == 0) {
				label.addClass('active btn-danger');
			} else {
				label.addClass('active btn-success');
			}
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function()
	{
		if (jQuery(this).val() == '') {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		} else if (jQuery(this).val() == 0) {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		} else {
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
})
</script>
