<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance restoreTables template for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Load the tooltip behavior for the notes
HTMLHelper::_('bootstrap.tooltip');
?>

<?php
$jinput	= Factory::getApplication()->input;
$image	= '<i class="fa fa-info-circle fa-lg"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_SELECT_UPLOAD_FILE'); ?></legend>
		<div class="card card-body mb-3"><?php echo Text::_('COM_BWPOSTMAN_TPL_UPLOAD_USER_MESSAGE')?></div>
		<?php
		if (BwPostmanHelper::canAdd('template'))
		{
			?>
			<div class="card card-body">
				<div class="row">
					<div class="admintable bwptable uploadtpl col-12">
						<div class="form-group row">
							<label class="key col-md-6 text-md-right">
								<?php echo Text::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE'); ?>
								<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
							</label>
							<div class="col-md-6">
								<input type="file" class="form-control-file" name="uploadfile" id="uploadfile" />
							</div>
						</div>
						<div class="button form-group row mt-3">
							<div class="key col-12 text-center">
								<input type="button" class="btn btn-success" name="submitbutton"
									onclick="Joomla.submitbutton('templates.uploadtpl'); document.getElementById('loading').style.display = 'block';"
									value="<?php echo Text::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE_BUTTON'); ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</fieldset>
	<input type="hidden" name="task" value="uploadtpl" />
	<input type="hidden" name="controller" value="templates" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div id="loading" style="display: none;"></div>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
