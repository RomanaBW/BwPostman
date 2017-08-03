<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register success message template for frontend.
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

// Success message - will be shown if
// 1. the newsletter account has been successfully activated --> show a link to the edit mode
// 2. the registration was successful and the confirmation email has been successfully sent
// 3. the activation code email has been successfully sent
// 4. the editlink email has been successfully sent

?>

<div id="bwpostman">
	<div id="bwp_com_register_success">
		<?php if ($this->params->def('show_page_title', 1)) { ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->escape($this->params->get('page_title')); ?></h1>
		<?php } ?>

		<div class="content_inner">
			<?php
			if (property_exists($this->success, 'editlink')) { // Case 1
				if (JFactory::getUser()->get('guest'))
				{
					if (is_null($this->success->itemid))
					{
						$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit&amp;editlink={$this->success->editlink}");
					}
					else
					{
						$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;Itemid={$this->success->itemid}&amp;view=edit&amp;editlink={$this->success->editlink}");
					}
				}
				else
				{
					if (is_null($this->success->itemid))
					{
						$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;view=edit") ;
					}
					else
					{
						$link = JRoute::_(JUri::root() . "index.php?option=com_bwpostman&amp;Itemid={$this->success->itemid}&amp;view=edit") ;
					}
				}

				$msg = '<div class="success-message">' . JText::sprintf($this->success->success_msg, $link) . '</div>';

				echo $msg;
			}
			else
			{ 	// Case 2, 3, 4
				if (property_exists($this->success, 'success_msg'))
					echo '<div class="success-message">' . JText::_($this->success->success_msg) . '</div>';
			}
			?>

			<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
		</div>
	</div>
</div>
