<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance restoreTables template for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
JHtml::_('bootstrap.tooltip');
?>

<?php
$jinput	= JFactory::getApplication()->input;
$image	= '<i class="icon-info"></i>';
$option	= $jinput->getCmd('option');
?>

<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_BWPOSTMAN_TPL_SELECT_UPLOAD_FILE'); ?></legend>
		<div class="well well-small"><?php echo JText::_('COM_BWPOSTMAN_TPL_UPLOAD_USER_MESSAGE')?></div>
		<?php
		if (BwPostmanHelper::canAdd('template'))
		{
			?>
			<div class="well well-small">
				<div class="row-fluid">
					<table class="admintable bwptable uploadtpl">
						<tr>
							<td align="right" class="key">
								<span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE'); ?></span>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE_NOTE'); ?>"><?php echo $image; ?></span>
							</td>
							<td>
								<input type="file" name="uploadfile" id="uploadfile" />
							</td>
						</tr>
						<tr>
							<td width="250" align="center" class="key">
								<input type="button" class="btn btn-success" name="submitbutton"
									onclick="Joomla.submitbutton('templates.uploadtpl'); document.getElementById('loading').style.display = 'block';" value="<?php echo JText::_('COM_BWPOSTMAN_TPL_UPLOAD_FILE_BUTTON'); ?>">
							</td>
						</tr>
					</table>
				</div>
			</div>
			<?php
		}
		?>
	</fieldset>
	<input type="hidden" name="task" value="uploadtpl" />
	<input type="hidden" name="controller" value="templates" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="loading" style="display: none;"></div>

<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
