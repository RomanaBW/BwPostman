<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register error get edit link template for frontend.
 *
 * @version 2.0.1 bwpm
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
defined('_JEXEC') or die('Restricted access');

// Displays a button to send the editlink
// Will be shown if
// 1. the editlink in the uri is empty
// 2. the editlink in the uri doesn't exist in the subscribers-table
?>

<div id="bwpostman">
	<div id="bwp_com_error_geteditlink">
		<?php
		if ($this->params->def('show_page_title', 1)) { ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
				<?php echo $this->escape($this->params->get('page_title')); ?>
			</h1>
		<?php
		}

		echo '<p class="bwp-error">' . JText::_('COM_BWPOSTMAN_ERROR') . '</p>';
		echo '<p class="error-message">' . JText::_($this->error->err_msg) . '</p>';
		?>

		<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
				name="bwp_com_form" class="form-validate">
			<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
				<p class="getlink">
					<label id="emailmsg" for="email"> <?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>:</label>
					<input type="text" id="email" name="email" size="40" value="" class="inputbox required validate-email" maxlength="100" />
				</p>
			</div>

			<button class="button validate btn" type="submit"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_SENDEDITLINK'); ?></button>
			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="view" value="edit" />
			<input type="hidden" name="task" value="sendEditlink" />
			<input type="hidden" name="id" value="<?php echo (property_exists($this->error, 'err_code')) ? $this->error->err_code : ''; ?>" />
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
