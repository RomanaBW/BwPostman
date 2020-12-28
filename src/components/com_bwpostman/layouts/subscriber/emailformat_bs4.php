<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber data fields layout.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
 * @author Romana Boldt, Karl Klostermann
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

use Joomla\CMS\Language\Text;

$mailformat_selected = $displayData['mailformat_selected'];
$formclass = $displayData['formclass'];
?>
				<div id="edit_mailformat" class="btn-group btn-group-toggle<?php echo $formclass === "sm" ? ' btn-group-sm' : ''; ?>" data-toggle="buttons">
					<label class="btn btn-secondary<?php echo (!$mailformat_selected ? ' active' : ''); ?>" for="formatText">
						<input type="radio" name="emailformat" id="formatText" value="0"<?php echo (!$mailformat_selected ? ' checked="checked"' : ''); ?> />
						<span>&nbsp;&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_TEXT'); ?>&nbsp;&nbsp;&nbsp;</span>
					</label>
					<label class="btn btn-secondary<?php echo ($mailformat_selected ? ' active' : ''); ?>" for="formatHtml">
						<input type="radio" name="emailformat" id="formatHtml" value="1"<?php echo ($mailformat_selected ? ' checked="checked"' : ''); ?> />
						<span>&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_HTML'); ?>&nbsp;&nbsp;</span>
					</label>
				</div>
