<?php
/**
 * BwPostman Newsletter Module
 *
 * BwPostman link to component template for module.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Module
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

JHtml::_('behavior.tooltip');

?>

<div id="bwp_mod_link_to_edit">
	<p id="linktoeditform">
		<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&amp;view=edit&amp;Itemid=' . $itemid); ?>">
			<?php echo JText::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?>
		</a>
	</p>
</div>
