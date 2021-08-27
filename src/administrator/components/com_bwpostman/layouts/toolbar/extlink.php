<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman external link layout
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

defined('JPATH_BASE') or die;

$toolbarClass = '';
if (isset($displayData['options']['toolbar-class']))
{
	$toolbarClass  = ' class="' . $displayData['options']['toolbar-class'] . '"';
}
$buttonClass  = $displayData['options']['btnClass'];
$iconClass  = 'icon-' . $displayData['options']['icon-class'];
$id    = $displayData['options']['id'];
$url    = $displayData['options']['url'];
$text   = $displayData['options']['text'];

?>
<joomla-toolbar-button id="<?php echo $id; ?>" task="" <?php echo $toolbarClass; ?>>
	<button onclick="window.open('<?php echo $url; ?>', '_blank', '');" class="<?php echo $buttonClass; ?>" type="button">
		<span class="<?php echo $iconClass; ?>" aria-hidden="true"></span>
		<?php echo $text; ?>
	</button>
</joomla-toolbar-button>
