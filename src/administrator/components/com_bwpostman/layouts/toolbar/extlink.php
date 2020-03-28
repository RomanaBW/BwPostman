<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

if(version_compare(JVERSION, '3.999.999', 'le'))
{
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
	<?php
	}
else
{
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
	<?php
}
