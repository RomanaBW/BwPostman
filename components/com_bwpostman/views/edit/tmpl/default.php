<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit default template for frontend.
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
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidator');

?>

<script type="text/javascript">
/* <![CDATA[ */
	function submitbutton(pressbutton) {

		var form	= document.bwp_com_form;
		var fault	= false;

		form.edit.value = pressbutton;

		// Valdiate input fields
		if (document.bwp_com_form.name) {
			if (form.name_field_obligation.value == 1) {
				if (form.name.value == "") {
					alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_NAME', true); ?>");
					fault = true;
				}
			}
		}
		if (document.bwp_com_form.firstname) {
			if (form.firstname_field_obligation.value == 1) {
				if (form.firstname.value == "") {
					alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_FIRSTNAME', true); ?>");
					fault = true;
				}
			}
		}
		if (document.bwp_com_form.special) {
			if (form.special_field_obligation.value == 1) {
				if (form.special.value == "") {
					alert("<?php echo JText::sprintf('COM_BWPOSTMAN_SUB_ERROR_SPECIAL', JText::_($this->params->get('special_label'))); ?>");
					fault = true;
				}
			}
		}
		if (form.email.value== "") {
			alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_EMAIL', true); ?>");
			fault	= true;
		}
		if (checkNlBoxes()== false) {
			alert ("<?php echo JText::_('COM_BWPOSTMAN_ERROR_NL_CHECK', true); ?>");
			fault	= true;
		}
		if (fault == false) {
			form.submit();
		}
		function checkNlBoxes() {
			var arrCB = form.elements['mailinglists[]'];
			var n =	arrCB.length;
			var check = 0;
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
			if (check == 0 && form.unsubscribe.checked == false) {
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
				<p><?php echo JText::_('COM_BWPOSTMAN_JAVAWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman">
	<div id="bwp_com_edit_subscription">
		<?php if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		<?php endif; ?>

		<div class="content_inner">
			<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&task=save'); ?>" method="post" id="bwp_com_form" name="bwp_com_form" class="form-validate form-inline">
				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<?php
						if ($this->params->get('pretext')) : // Show pretext only if set in basic parameters			?>
							<p class="bwp_com_form_pretext"><?php echo nl2br($this->params->get('pretext')); ?></p>
					<?php
						endif; // End: Show pretext only if set in basic parameters
					?>
					<?php if ($this->params->get('show_gender') == 1) { // Show formfield gender only if enabled in basic parameters ?>
						<div class="edit_gender">
							<label id="gendermsg"> <?php echo JText::_('COM_BWPOSTMAN_GENDER'); ?>:</label>
							<?php echo $this->lists['gender']; ?>
						</div>
					<?php } // End gender ?>

					<?php if ($this->params->get('show_firstname_field') || $this->params->get('firstname_field_obligation')) : // Show firstname-field only if set in basic parameters or required ?>
						<p class="edit_firstname input<?php echo ($this->params->get('firstname_field_obligation')) ? '-append' : ''?>">
							<label id="firstnamemsg" for="firstname"
								<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) echo "class=\"invalid\""; ?>>
								<?php echo JText::_('COM_BWPOSTMAN_FIRSTNAME'); ?>:
							</label>
							<?php if ($this->params->get('firstname_field_obligation')) : { // Is filling out the firstname field obligating ?>
								<input	type="text" name="firstname" id="firstname" size="40"
										value="<?php echo $this->subscriber->firstname; ?>"
										class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox required";} ?>"
										maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
							<?php }
							else : { ?>
								<input	type="text" name="firstname" id="firstname" size="40"
										value="<?php echo $this->subscriber->firstname; ?>"
										class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
										maxlength="50" />
							<?php } endif; // End: Is filling out the firstname field obligating ?>
						</p>
					<?php
					endif;
					if ($this->params->get('show_name_field') || $this->params->get('name_field_obligation')) : // Show name-field only if set in basic parameters or required ?>
						<p class="edit_name input<?php echo ($this->params->get('name_field_obligation')) ? '-append' : ''?>">
							<label id="namemsg" for="name"
								<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) echo "class=\"invalid\""; ?>>
								<?php echo JText::_('COM_BWPOSTMAN_NAME'); ?>:
							</label>
							<?php if ($this->params->get('name_field_obligation')) : { // Is filling out the name field obligating ?>
								<input	type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
										class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox required";} ?>"
										maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
							<?php }
							else : { ?>
								<input	type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
										class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
										maxlength="50" />
							<?php } endif; // End: Is filling out the name field obligating ?>
						</p>
					<?php endif; // End: Show name-field only if set in basic parameters ?>

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
								class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox required";} ?>"
								maxlength="50" /> <span class="append-area"><i class="icon-star"></i></span>
						<?php }
						else : { ?>
							<input	type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
								class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) { echo "invalid"; } else { echo "inputbox";} ?>"
								maxlength="50" />
						<?php } endif; // End: Is filling out the special field obligating ?>
					</p>
					<?php endif; // End: Show special field only if set in basic parameters ?>
					<p class="edit_email input-append">
						<label id="emailmsg" for="email"
							<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1)) echo "class=\"invalid\""; ?>>
							<?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>:
						</label>
						<input	type="text" id="email" name="email" size="40" value="<?php echo $this->subscriber->email; ?>"
								class="<?php if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1)) { echo "invalid"; } else { echo "inputbox required validate-email";} ?>"
								maxlength="100" /> <span class="append-area"><i class="icon-star"></i></span>
					</p>
					<?php if ($this->params->get('show_emailformat') == 1) { // Show formfield emailformat only if enabled in basic parameters ?>
						<div class="edit_emailformat">
							<label id="emailformatmsg"> <?php echo JText::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>:</label>
							<?php echo $this->lists['emailformat']; ?>
						</div>
					<?php } // End emailformat ?>
				</div>

				<?php if ($this->available_mailinglists) : // Show available mailinglists ?>
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
								<p class="mail_available_list <?php echo "mailinglists$i"; ?>">
									<input type="checkbox" id="<?php echo "mailinglists$i"; ?>" name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
										<?php if ((is_array($this->selected_mailinglists)) && (in_array((int)$item->id, $this->selected_mailinglists))) echo "checked=\"checked\""; ?> />
									<span class="mail_available_list_title"><?php echo "$item->title: "; ?></span><?php echo "$item->description"; ?>
								</p>
							<?php endforeach; ?>
							<div class="maindivider<?php echo $this->params->get('pageclass_sfx'); ?>"></div>
						<?php } ?>
					</div>

				<?php endif; // End Mailinglists ?>

				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<p class="edit_unsubscribe">
						<input type="checkbox" id="unsubscribe" name="unsubscribe" value="1" />
						<span class="edit_unsubscribe_text"><?php echo JText::_('COM_BWPOSTMAN_UNSUBSCRIBE') ?></span>
					</p>
				</div>

				<div class="maindivider<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"></div>

				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
					<p class="edit_required">
						<?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</p>
				</div>

				<button class="button validate save btn" type="button" onclick="return submitbutton('submit');"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_EDIT'); ?></button>
				<?php if ($this->user->get('guest')):   ?>
					<button class="button validate leave btn" type="button" onclick="return submitbutton('submitleave');"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_LEAVEEDIT'); ?></button>
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
				<?php echo JHTML::_('form.token'); ?>
			</form>

			<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
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
