<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit default template for frontend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Site
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

JHTML::_('behavior.tooltip');

?>

<script type="text/javascript">
/* <![CDATA[ */
	function submitbutton(pressbutton) {
	
		var form	= document.bwp_com_form;
		var fault	= false;
		
		form.edit.value = pressbutton;
		
		// Valdiate input fields
		if (form.name_field_obligation.value == 1) {
			if (form.name.value == "") {
				alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_NAME', true); ?>");
				fault	= true;
			}
		} 
		if (form.firstname_field_obligation.value == 1) {
			if (form.firstname.value == "") {
				alert("<?php echo JText::_('COM_BWPOSTMAN_ERROR_FIRSTNAME', true); ?>");
				fault	= true;
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
			<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form" name="bwp_com_form" class="form-validate form-inline">
				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php 
					if ($this->params->get('pretext')) : // Show pretext only if set in basic parameters			?>
						<p class="bwp_com_form_pretext"><?php echo nl2br($this->params->get('pretext')); ?></p>
				<?php 
					endif; // End: Show pretext only if set in basic parameters 
				?>
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
				<input type="hidden" name="edit" value="" /> 
				<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" /> 
				<input type="hidden" name="name_field_obligation" value="<?php echo $this->params->get('name_field_obligation'); ?>" /> 
				<input type="hidden" name="firstname_field_obligation" value="<?php echo $this->params->get('firstname_field_obligation'); ?>" /> 
				<?php echo JHTML::_('form.token'); ?>
			</form>
			
			<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
		</div>
	</div>
</div>