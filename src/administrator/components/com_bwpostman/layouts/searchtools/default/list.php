<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman order list layout.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Admin
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

defined('JPATH_BASE') or die;

$data	= $displayData;
$layout	= $data['tab'];

// Load the form list fields
$list	= $data['view']->filterForm->getFieldset($layout);

?>
<?php if ($list) : ?>
	<div class="ordering-select hidden-phone <?php echo $layout?>">
		<?php foreach ($list as $fieldName => $field) :
				if (stripos($fieldName, 'list_') !== false) : ?>
					<div class="js-stools-field-list">
						<?php echo $field->input; ?>
					</div>
				<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
