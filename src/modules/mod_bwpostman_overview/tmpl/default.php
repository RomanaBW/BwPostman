<?php
/**
 * BwPostman Newsletter Overview Module
 *
 * BwPostman default template for overview module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

?>
<div id="mod_bwpostman_overview">
	<?php if (count($list) > 0) { ?>
		<ul class="mod-bwpostman-overview-module<?php echo $moduleclass_sfx; ?>">
			<?php foreach ($list as $item) : ?>
			<li>
				<a href="<?php echo $item->link; ?>">
					<?php echo $item->text; ?>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php }
	else {
		echo Text::_('MOD_BWPOSTMAN_OVERVIEW_NO_NEWSLETTERS_FOUND');
	} ?>
</div>
