<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register error account general template for frontend.
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

// Displays a link to the registration form or the editlink form
// Will be shown if
// 1. the unsubscribe process failed --> the editlink or email address is wrong
// 2. someone ordered an editlink but the email address doesn't exist in the subscribers-table
// 3. the registration process failed because the account already exists
// 4. the unsubscribe process failed --> the account couldn't be removed --> find a better solution for that
?>

<div id="bwpostman">
	<div id="bwp_com_error_account_general">
		<?php
		if ($this->params->def('show_page_title', 1)) { ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
				<?php echo $this->escape($this->params->get('page_title')); ?>
			</h1>
		<?php
		}

		echo '<p class="bwp-error">' . JText::_('COM_BWPOSTMAN_ERROR') . '</p>';

		if (is_null($this->error->err_code))
		{
			if ($this->error->err_msg == 'COM_BWPOSTMAN_ERROR_UNSUBSCRIBE')
			{ // Case 4
				echo '<p class="error-message">' . JText::_($this->error->err_msg) . '</p>';

				$admin_email = $this->params->def('default_from_email', JFactory::getConfig()->get('mailfrom'));

				$msg1 = '<p class="contact-admin">' . JText::sprintf('COM_BWPOSTMAN_ERROR_CONTACTADMIN', $admin_email) . '</p>';
				echo JHtml::_('content.prepare', $msg1);
			}
			else
			{
				// Case 1
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit");
				}
				else
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit&amp;Itemid={$this->error->err_itemid}");
				}

				$msg = '<p class="error-message">' . JText::sprintf($this->error->err_msg, $link) . '</p>';
			}
		}
		else
		{
			if ($this->error->err_code == 0) {
				// Case 2
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=register");
				}
				else
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=register&amp;Itemid={$this->error->err_itemid}");
				}
			}
			else
			{
				// Case 3
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit");
				}
				else
				{
					$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit&amp;Itemid={$this->error->err_itemid}");
				}
			}

			$msg = '<p class="error-message">' . JText::sprintf($this->error->err_msg, $this->error->err_email, $link) . '</p>';
		}

		echo $msg;

		if ($this->params->get('show_boldt_link') === '1')
		{ ?>
			<p class="bwpm_copyright"><?php echo BwPostman::footer(); ?></p>
		<?php
		} ?>
	</div>
</div>
