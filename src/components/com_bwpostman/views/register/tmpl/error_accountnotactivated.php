<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register error account not activated template for frontend.
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

// needed to validate email
JHtml::_('behavior.formvalidator');

// Displays a button to send the activation code
// Will be shown if
// 1. the editlink in the uri is correct but the account is not activated
// 2. the user has a newsletter account which is not activated
// 3. the newsletter account of a subscriber with a session-stored ID is not activated
// 4. the registration failed because a newsletter account with the input email already exists but is not activated
// 5. the activation code in the uri is empty or doesn't exist in the subscribers-table
?>

<div id="bwpostman">
	<div id="bwp_com_error_account_notactivated">
		<?php
		if ($this->params->def('show_page_title', 1)) { ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
				<?php echo $this->escape($this->params->get('page_title')); ?>
			</h1>
		<?php
		}

		echo '<p class="bwp-error">' . JText::_('COM_BWPOSTMAN_ERROR') . '</p>';
		?>

		<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
				name="bwp_com_form" class="form-validate">
			<?php
			if (property_exists($this->error, 'err_code'))
			{ // Case 1, 2, 3, 4
				if ($this->error->err_email)
				{ // Case 4
					$msg = '<p class="error-message">' . JText::sprintf($this->error->err_msg, $this->error->err_email) . '</p>';
					echo $msg;
				}
				else
				{ // Case 1, 2, 3
					echo '<p class="error-message">' . JText::_($this->error->err_msg) . '</p>';
				}
			}
			else
			{  // Case 5
				echo '<p class="error-message">' . JText::_($this->error->err_msg) . '</p>';
			?>

			<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
				<p class="activate">
						<label id="emailmsg" for="email"> <?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>:</label>
						<input type="text" id="email" name="email" size="40" value="" class="inputbox required validate-email" maxlength="100" />
				</p>
			</div>
				<?php
			}
			?>

			<button class="button validate btn" type="submit"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_SENDACTIVATION'); ?></button>
			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="view" value="register" />
			<input type="hidden" name="task" value="sendActivation" />
			<input type="hidden" name="id" value="<?php echo $this->error->err_id; ?>" />
			<input type="hidden" name="err_code" value="<?php echo (property_exists($this->error, 'err_code')) ? $this->error->err_code : ''; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>

		<?php if ($this->params->get('show_boldt_link') === '1')
		{ ?>
			<p class="bwpm_copyright"><?php echo BwPostman::footer(); ?></p>
		<?php } ?>
	</div>
</div>
