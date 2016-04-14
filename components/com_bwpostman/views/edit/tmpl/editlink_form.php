<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman editlink form template for frontend.
 *
 * @version 2.0.0 bwpm
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

// Form to order the editlink
// --> the editlink is needed to modify the subscriber account if the subscriber is not logged into the website
// --> the editlink will be send with email
?>

<div id="bwpostman">
	<div id="bwp_com_getedit_link">
		<?php if ($this->params->def('show_page_title', 1)) { ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></h1>
		<?php } ?>

		<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form" name="bwp_com_form" class="form-validate">
			<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
				<p class="getlink_text">
					<?php echo JText::_('COM_BWPOSTMAN_EDITLINK_MSG'); ?>
				</p>
				<p class="getlink_email">
					<span><label id="emailmsg" for="email"> <?php echo JText::_('COM_BWPOSTMAN_EMAIL'); ?>:</label></span>
					<span><input type="text" id="email" name="email" size="40" value="<?php echo $this->subscriber->email; ?>" class="inputbox required validate-email" maxlength="100" /></span>
				</p>
			</div>

			<button class="button validate btn" type="submit"><?php echo JText::_('COM_BWPOSTMAN_BUTTON_SENDEDITLINK'); ?></button>

			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="task" value="sendEditlink" />
			<?php echo JHTML::_('form.token'); ?>
		</form>

		<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
	</div>
</div>
