<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance restoreTables template for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.keepalive');

$jinput	= Factory::getApplication()->input;
$image = '<i class="fa fa-lg fa-info-circle"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform card card-body mb-2">
		<div class="h2"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_SELECT_RESTORE_FILE'); ?></div>
		<div class="alert alert-warning"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_USER_MESSAGE')?></div>
		<div>
			<div class="row-fluid">
				<table class="admintable bwptable restore">
					<tr>
						<td class="key">
							<div class="editlinktip me-2" aria-labelledby="tip-desc">
								<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_FILE'); ?>:</span>
								<?php echo $image; ?>&nbsp;&nbsp;
							</div>
                            <div role="tooltip" id="tip-desc"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_FILE_NOTE'); ?></div>
						</td>
						<td>
							<input type="file" name="restorefile" id="restorefile" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<input type="button" class="btn btn-success mt-3" name="submitbutton"
									onclick="Joomla.submitbutton('maintenance.doRestore');
										document.getElementById('loading').style.display = 'block';"
									value="<?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_BUTTON'); ?>">
						</td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>
	<input type="hidden" name="task" value="doRestore" />
	<input type="hidden" name="controller" value="maintenance" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div id="loading" style="display: none;"></div>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer');
