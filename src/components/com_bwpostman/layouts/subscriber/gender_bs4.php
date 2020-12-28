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

// replace for - function buildGenderList($gender_selected = '2', $name = 'gender', $class = '', $idPrefix = '')
$gender_selected = $displayData['gender_selected'] ? $displayData['gender_selected'] : '2';
$name = $displayData['name'] ? $displayData['name'] : 'gender';
$class = $displayData['class'] ? ' class="' . $displayData['class'] . '"' : '';
$genderId = $displayData['idPrefix'] ? $displayData['idPrefix'] . 'gender' : 'gender';
?>
				<select id="<?php echo $genderId . '"' . $class; ?> name="<?php echo $name ?>">
					<option value="2"<?php echo $gender_selected == '2' ? ' selected="selected"' : ''; ?>>
			            <?php echo Text::_('COM_BWPOSTMAN_NO_GENDER'); ?>
					</option>
					<option value="0"<?php echo $gender_selected == '0' ? ' selected="selected"' : ''; ?>>
			            <?php echo Text::_('COM_BWPOSTMAN_MALE'); ?>
					</option>
					<option value="1"<?php echo $gender_selected == '1' ? ' selected="selected"' : ''; ?>>
			            <?php echo Text::_('COM_BWPOSTMAN_FEMALE'); ?>
					</option>
				</select>
