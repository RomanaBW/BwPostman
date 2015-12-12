<?php
/**
 * BwPostman Newsletter Module
 * 
 * BwPostman default template for module.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Module
 * @author Romana Boldt, Karl Klostermann
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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');

require_once (JPATH_SITE . '/components/com_content/helpers/route.php');

$n	= count($mailinglists);
?>

<?php 
// We cannot use the same form name and name for the disclaimer checkbox 
// because this will not work if the module and the component will be displayed 
// on the same page

?>

<script type="text/javascript">
/* <![CDATA[ */
function checkModRegisterForm() {
	
	var form = document.bwp_mod_form;
	var errStr = "";
	var arrCB = document.getElementsByName("mailinglists[]");
	var n =	arrCB.length;
	var check = 0;
	
	// Valdiate input fields
	// firstname
	if ((document.getElementById("a_firstname").value == "")  && (document.getElementById("firstname_field_obligation").value == 1)){
		errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_FIRSTNAME', true); ?>\n";
	}
	// name
	if ((document.getElementById("a_name").value == "") && (document.getElementById("name_field_obligation").value == 1)){
		errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_NAME', true); ?>\n";
	}
	// email
	var email = document.getElementById("a_email").value;
	if (email == ""){
		errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_EMAIL', true); ?>\n";			
	} else {
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!filter.test(email)) {
			errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_EMAIL_INVALID', true); ?>\n";
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
		errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_NL_CHECK'); ?>\n";
	}
	// disclaimer
	if (document.bwp_mod_form.agreecheck_mod) {
		if (document.bwp_mod_form.agreecheck_mod.checked == false) {
			errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_DISCLAIMER_CHECK'); ?>\n";
		}
	}
	// captcha  
	if (document.bwp_mod_form.stringCaptcha) {
		if (document.bwp_mod_form.stringCaptcha.value == '') {
			errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_CAPTCHA_CHECK'); ?>\n";
		}
	}
	// question
	if (document.bwp_mod_form.stringQuestion) {
		if (document.bwp_mod_form.stringQuestion.value == '') {
			errStr += "<?php echo JText::_('MOD_BWPOSTMANERROR_CAPTCHA_CHECK'); ?>\n";
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
				<p><?php echo JText::_('MOD_BWPOSTMAN_JAVAWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="mod_bwpostman">
	<?php 
	if ($n == 0) { 
		// Don't show registration form if no mailinglist is selectable ?>
		<p class="bwp_mod_error_no_mailinglists"><?php echo addslashes(JText::_('MOD_BWPOSTMANERROR_NO_MAILINGLIST_AVAILABLE')); ?></p> <?php
	} 
	else { 
		// Show registration form only if a mailinglist is selectable?>
		
		<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=register'); ?>" method="post" id="bwp_mod_form" name="bwp_mod_form" class="form-validate form-inline" onsubmit="return checkModRegisterForm();">
		
			<?php // Spamcheck 1 - Input-field: class="user_hightlight" style="position: absolute; top: -5000px;" ?>
				<p class="user_hightlight">
					<label for="a_falle"><strong><?php echo addslashes(JText::_('MOD_BWPOSTMANSPAMCHECK')); ?></strong></label>
					<input type="text" name="falle" id="a_falle" size="20"  title="<?php echo addslashes(JText::_('MOD_BWPOSTMANSPAMCHECK')); ?>" maxlength="50" />
				</p>
			<?php // End Spamcheck ?>
			<?php 
				if ($paramsComponent->get('pretext')) : // Show pretext only if set in basic parameters			?>
					<p id="bwp_mod_form_pretext"><?php echo nl2br($paramsComponent->get('pretext')); ?></p>
			<?php 
				endif; // End: Show pretext only if set in basic parameters 
		
				if ($paramsComponent->get('show_firstname_field') OR $paramsComponent->get('firstname_field_obligation')) : // Show firstname-field only if set in basic parameters 			?>	
					<p id="bwp_mod_form_firstnamefield" class="input<?php echo ($paramsComponent->get('firstname_field_obligation')) ? '-append' : '-xx'?>">
						<?php   
						// Is filling out the firstname field obligating 
						isset($subscriber->firstname) ? $sub_firstname = $subscriber->firstname : $sub_firstname = '';
						($paramsComponent->get('firstname_field_obligation')) ? $required = '<span class="append-area"><i class="icon-star"></i></span>' : $required = '';
						?>
							<input type="text" name="a_firstname" id="a_firstname" placeholder="<?php echo addslashes(JText::_('MOD_BWPOSTMANFIRSTNAME')); ?>" value="<?php echo $sub_firstname; ?>" class="inputbox input-small" maxlength="50" />
							<?php echo $required; ?>
					</p>
				<?php 
				endif; 
				if ($paramsComponent->get('show_name_field') OR $paramsComponent->get('name_field_obligation')) : // Show name-field only if set in basic parameters ?>	
					<p id="bwp_mod_form_namefield" class="input<?php echo ($paramsComponent->get('name_field_obligation')) ? '-append' : ''?>">
						<?php  // Is filling out the name field obligating 
							isset($subscriber->name) ? $sub_name = $subscriber->name : $sub_name = '';
							($paramsComponent->get('name_field_obligation')) ? $required = '<span class="append-area"><i class="icon-star"></i></span>' : $required = ''; ?> 			
							<input type="text" name="a_name" id="a_name" placeholder="<?php echo addslashes(JText::_('MOD_BWPOSTMANNAME')); ?>" value="<?php echo $sub_name; ?>" class="inputbox input-small" maxlength="50" />
							<?php echo $required; ?>
					</p>
				<?php 
				endif; // End: Show name-field only if set in basic parameters ?>	
				
				<?php isset($subscriber->email) ? $sub_email = $subscriber->email : $sub_email = ''; ?>
				<p id="bwp_mod_form_emailfield" class="input-append">
					<input type="text" id="a_email" name="email" placeholder="<?php echo addslashes(JText::_('MOD_BWPOSTMANEMAIL')); ?>" value="<?php echo $sub_email; ?>" class="inputbox input-small" maxlength="100" />
					<span class="append-area"><i class="icon-star"></i></span>		
				</p>
				<?php if ($paramsComponent->get('show_emailformat') == 1) {
					// Show formfield emailformat only if enabled in basic parameters ?>	
					<p id="bwp_mod_form_emailformat">
						<label id="emailformatmsg_mod">
							<?php echo JText::_('MOD_BWPOSTMANEMAILFORMAT'); ?>:
						</label>
					</p>
					<div id="bwp_mod_form_emailformatfield">
						<?php echo $lists['emailformat']; ?>
					</div>
				<?php 
				}
				else {
					// hidden field with the default emailformat
					?>
					<input type="hidden" name="emailformat" value="<?php echo $paramsComponent->get('default_emailformat'); ?>" />
				<?php }
				// End emailformat
				?>

			<?php // Show available mailinglists  
				$n	= count($mailinglists);
				if (($mailinglists) && ($n > 0)) :  

				if ($n == 1) { ?>
					<input type="checkbox" style="display: none;" id="a_<?php echo "mailinglists0"; ?>" name="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglists[0]->id; ?>" checked="checked" /><?php 
				} 
				else { ?>
					<p id="bwp_mod_form_lists" class="required">
						<?php  echo JText::_('MOD_BWPOSTMANLISTS').' <i class="icon-star"></i>'; ?>
					</p>
					<div id="bwp_mod_form_listsfield">
						<?php
							foreach ($mailinglists AS $i => $mailinglist){ ?>
								<p class="a_mailinglist_item_<?php echo $i; ?>">
									<input type="checkbox" id="a_<?php echo "mailinglists$i"; ?>" name="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglist->id; ?>" />
									<span class="mailinglist-title"><?php echo $mailinglist->title;
									if ($paramsComponent->get('show_desc') == 1) {
										?>:</strong><br /><?php echo substr($mailinglist->description,0,$paramsComponent->get('desc_lenght')); if (strlen($mailinglist->description) > $paramsComponent->get('desc_lenght')) echo '...';
									} 
									else {
										echo '</span>'; 
									} ?>
								</p>
							<?php
							}
						?>
					</div><?php 
				}
				
				endif; 
				// End Mailinglists 
		
				if ($paramsComponent->get('disclaimer')) :// Show Disclaimer only if enabled in basic parameters ?>	
					<p id="bwp_mod_form_disclaimer">
						<input type="checkbox" id="agreecheck_mod" name="agreecheck_mod" />&nbsp;
						<?php 
						if ($paramsComponent->get('disclaimer_selection') == 1 && $paramsComponent->get('article_id') > 0) {
							// Disclaimer article and target_blank or not
						?>	
							<span class="bwp_mod_disclaimer"><?php echo '<a href="'.JRoute::_(ContentHelperRoute::getArticleRoute($paramsComponent->get('article_id'))) .'"'; if ($paramsComponent->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('MOD_BWPOSTMANDISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
						<?php 
						} 
						elseif ($paramsComponent->get('disclaimer_selection') == 2 && $paramsComponent->get('disclaimer_menuitem') > 0) {
						// Disclaimer menu item and target_blank or not
						?>	
							<span class="bwp_mod_disclaimer"><?php $disclaimer_link = JRoute::_('index.php?Itemid=' . $paramsComponent->get('disclaimer_menuitem')); echo '<a href="'.$disclaimer_link.'"'; if ($paramsComponent->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('MOD_BWPOSTMANDISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
						<?php } 
						else { 
						// Disclaimer url and target_blank or not
						?>
							<span class="bwp_mod_disclaimer"><?php echo '<a href="'. $paramsComponent->get('disclaimer_link') . '"'; if ($paramsComponent->get('disclaimer_target') == 0) {echo ' target="_blank"';}; echo '>'. JText::_('MOD_BWPOSTMANDISCLAIMER').'</a> <i class="icon-star"></i>'; ?></span>
						<?php
						} ?>	
					</p>
				<?php 
				endif; // Show disclaimer ?>	
			
			<?php // Question 
				if ($paramsComponent->get('use_captcha') == 1) : ?>
				<div class="question">
					<p class="security_question_entry"><?php echo JText::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
					<p class="security_question_lbl"><?php echo $paramsComponent->get('security_question'); ?></p>
					<p class="question input-append">
						<label id="question_mod" for="a_stringQuestion"><?php echo JText::_('MOD_BWPOSTMANCAPTCHA_LABEL'); ?>:</label>
						<input type="text" name="stringQuestion" id="a_stringQuestion" placeholder="<?php echo addslashes(JText::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>" maxlength="50"  class="input-small" /> <span class="append-area"><i class="icon-star"></i></span>
					</p>
				</div>
				<?php 
				endif; // End question 
			?>
	
			<?php // Captcha 
				if ($paramsComponent->get('use_captcha') == 2) :
					$codeCaptcha = md5(microtime()); ?>
					<div class="captcha">
						<p class="security_question_entry"><?php echo JText::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
						<p class="security_question_lbl"><img src="<?php echo JURI::base();?>index.php?option=com_bwpostman&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" /></p>
						<p class="captcha input-append">
							<label id="a_captcha" for="a_stringCaptcha"><?php echo JText::_('MOD_BWPOSTMANCAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringCaptcha" id="a_stringCaptcha" placeholder="<?php echo addslashes(JText::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>" maxlength="50" class="input-small" /> <span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
					<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
				<?php 
				endif; // End captcha 
			?>
			<?php // End Spamcheck 2 ?>
			
			<p class="mod-button-register text-right"><button class="button validate btn" type="submit"><?php echo JText::_('MOD_BWPOSTMANBUTTON_REGISTER'); ?></button></p>
			
			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="task" value="register_save" />
			<input type="hidden" name="bwp-<?php echo $captcha; ?>" value="1" /> 
			
			<?php // TODO: muss hier subscriber->id stehen oder kann das leer bleiben? ?>
			<!-- <input type="hidden" name="id" value="<?php echo isset($subscriber->id); ?>" /> -->
			<input type="hidden" name="registration_ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
			<input type="hidden" name="name_field_obligation" id="name_field_obligation" value="<?php echo $paramsComponent->get('name_field_obligation'); ?>" /> 
			<input type="hidden" name="firstname_field_obligation" id="firstname_field_obligation" value="<?php echo $paramsComponent->get('firstname_field_obligation'); ?>" /> 
			<?php echo JHTML::_('form.token'); ?>
		</form>
	
		<p id="bwp_mod_form_required">(<i class="icon-star"></i>) <?php echo JText::_('MOD_BWPOSTMANREQUIRED'); ?></p>
		<p id="bwp_mod_form_editlink" class="text-right">
			<button class="button btn" onclick="location.href='<?php echo JRoute::_('index.php?option=com_bwpostman&amp;view=edit&amp;Itemid='.$itemid); ?>'"><?php echo JText::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?></button>
		</p>
	<?php 
	}; // End: Show registration form ?> 
</div>	
