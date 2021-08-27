<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman render field layout
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

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

/**
 * Layout variables
 * ---------------------
 * 	$options      : (array)  Optional parameters
 * 	$name         : (string) The id of the input this label is for
 * 	$label        : (string) The html code for the label (not required if $options['hiddenLabel'] is true)
 * 	$input        : (string) The input field html code
 * 	$description  : (string) An optional description to use in a tooltip
 */

if (!empty($options['showonEnabled']))
{
	HTMLHelper::_('script', 'system/showon.min.js', array('version' => 'auto', 'relative' => true));
}
$class = empty($options['class']) ? '' : ' ' . $options['class'];
$rel   = empty($options['rel']) ? '' : ' ' . $options['rel'];
$id    = $name . '-desc';

?>
<div class="control-group<?php echo $class; ?>"<?php echo $rel; ?>>
	<?php if (empty($options['hiddenLabel'])) : ?>
		<div class="control-label"><?php echo $label; ?></div>
	<?php endif; ?>
	<div class="controls">
		<?php echo $input; ?>
	</div>
	<?php if (!empty($description)) : ?>
		<div id="<?php echo $id; ?>">
			<small class="form-text text-muted">
				<?php echo htmlspecialchars(($description), ENT_COMPAT); ?>
			</small>
		</div>
	<?php endif; ?>
</div>
