<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

if (isset($displayData['options']))
{
	$doTask = $displayData['options']['doTask'];
	$class  = $displayData['options']['class'];
	$text   = $displayData['options']['text'];
}
else
{
	$doTask = $displayData['doTask'];
	$class  = $displayData['class'];
	$text   = $displayData['text'];
}

$id = 'toolbar-manual';

if ($class === 'icon-users')
{
	$id = 'toolbar-forum';
}

?>
<div id="<?php echo $id; ?>" class="btn-wrapper">
	<button onclick="window.open('<?php echo $doTask; ?>', '_blank', '');" class="btn btn-small">
		<span class="<?php echo $class; ?>" aria-hidden="true"></span>
		<?php echo $text; ?>
	</button>
</div>
