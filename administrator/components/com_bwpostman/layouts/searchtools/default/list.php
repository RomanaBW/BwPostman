<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
