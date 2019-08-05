<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman order list layout.
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

$data	= $displayData;
$layout	= $data['tab'];

// Load the form list fields
$fieldset	= $data['view']->filterForm->getFieldset($layout);
$list = array();

foreach ($fieldset as $fieldName => $field)
{
	if (strpos($fieldName,  'list_') !== false)
	{
		$list[$fieldName] = $field;
	}
}

?>
<?php if ($list) : ?>
	<div class="ordering-select">
		<?php foreach ($list as $fieldName => $field) : ?>
			<div class="js-stools-field-list">
				<span class="sr-only"><?php echo $field->label; ?></span>
				<?php echo $field->input; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
