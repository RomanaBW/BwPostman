<?php
/**
 * BwPostman Newsletter Module
 *
 * BwPostman link to component template for module.
 *
 * @version 4.3.1
 * @package BwPostman-Module
 * @author Romana Boldt
 * @copyright (C) 2024 Boldt Webservice <forum@boldt-webservice.de>
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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;

$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');
?>

<div id="bwp_mod_link_to_edit">
	<p id="linktoeditform">
		<a class="btn btn-default btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_bwpostman&amp;view=edit&amp;Itemid=' . $itemid); ?>"
				title="<?php echo Text::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?>">
			<?php echo Text::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?>
		</a>
	</p>
</div>
