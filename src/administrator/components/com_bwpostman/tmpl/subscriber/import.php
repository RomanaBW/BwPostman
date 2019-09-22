<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber import 0 template for backend.
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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.tooltip');

// Keep session alive while importing
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('bootstrap.tooltip');

$jinput	= Factory::getApplication()->input;
$image	= '<i class="icon-info"></i>';
$option	= $jinput->getCmd('option');

?>

<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_STP1'); ?></legend>
		<div class="well well-small">
			<div class="row-fluid">
				<table class="admintable bwptable import">
					<tr class="bwptable">
						<td width="250" align="right" class="key">
							<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_FILEFORMAT'); ?></span>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILEFORMAT_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td class="bwptable">
							<div class="bwpmailformat">
								<?php echo $this->lists['fileformat']; ?>
							</div>
						</td>
					</tr>
					<tr class="importfile">
						<td align="right" class="key">
							<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE'); ?></span>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_FILE_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td>
							<input type="file" name="importfile" id="importfile"
								<?php //if (empty($this->import['fileformat'])) echo ' disabled="disabled"'; ?> />
						</td>
					</tr>
					<tr class="delimiter">
						<td align="right" class="key">
							<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?></span>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_DELIMITER_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td><?php echo $this->lists['delimiter'];?></td>
					</tr>
					<tr class="enclosure">
						<td align="right" class="key">
							<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE'); ?></span>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_ENCLOSURE_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td><?php echo $this->lists['enclosure'];?></td>
					</tr>
					<tr class="caption">
						<td align="right" class="key">
							<span class="bwplabel"><?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION'); ?></span>
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_CAPTION_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td>
							<input type="checkbox" id="caption" name="caption" title="caption"
								<?php
								if (isset($this->import['caption']))
								{
									if ($this->import['caption'] == 1)
									{
										echo "checked";
									}
								} ?>
							/>
						</td>
					</tr>
					<tr class="button">
						<td width="250" align="center" class="key">
							<input type="button" class="btn btn-success" name="submitbutton" id=""
								<?php //if (empty($this->import['fileformat'])) echo ' disabled="disabled"'; ?>
										onclick="Joomla.submitbutton('subscribers.prepareImport');"
										value="<?php echo Text::_('COM_BWPOSTMAN_SUB_IMPORT_BUTTON'); ?>"
							/>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="prepareImport" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

function extCheck()
{
	// get the file name, possibly with path (depends on browser)
	var filename	= $j("#importfile").val();
	var format		= $j("input[name='fileformat']:checked").val();


	// Use a regular expression to trim everything before final dot
	var extension = filename.replace(/^.*\./, '');

	// If there is no dot anywhere in filename, we would have extension == filename,
	// so we account for this possibility now
	if (extension == filename)
	{
		extension = '';
	}
	else
	{
		// if there is an extension, we convert to lower case
		// (N.B. this conversion will not effect the value of the extension
		// on the file upload.)
		extension = extension.toLowerCase();
	}
	switch (extension)
	{
		case 'xml':
				if (format == 'xml')
				{
					$j( ".button" ).show();
				}
				else
				{
					alert ('<?php echo Text::_("COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILEFORMAT"); ?>');
					$j( "#importfile" ).val('');
				}
			break;
		case 'csv':
			if (format == 'csv')
			{
				$j( ".button" ).show();
				$j( ".delimiter" ).show();
				$j( ".enclosure" ).show();
				$j( ".caption" ).show();
			}
			else
			{
				alert ('<?php echo Text::_("COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILEFORMAT"); ?>');
				$j( "#importfile" ).val('');
			}
			break;
		default:
			alert ('<?php echo Text::_("COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILEFORMAT"); ?>');
		$j( "#importfile" ).val('');
		break;
	}
}

$j(document).ready(function()
{
	var format	= $j("input[name='fileformat']:checked").val();

	$j( ".delimiter" ).hide();
	$j( ".enclosure" ).hide();
	$j( ".caption" ).hide();
	$j( ".button" ).hide();

	if (typeof (format) == 'undefined')
	{
		$j( ".importfile" ).hide();
	}
	else
	{
		$j( ".importfile" ).show();
		if ($j( "#importfile" ).val() != '')
		{
			extCheck();
		}
	}
});

$j("input[name='fileformat']").on("change", function()
{
	$j( ".importfile" ).show();
	$j( ".delimiter" ).hide();
	$j( ".enclosure" ).hide();
	$j( ".caption" ).hide();
	$j( ".button" ).hide();
	$j( "#importfile" ).val('');
});

$j("#importfile").on("change", function()
{
	if ($j( "#importfile" ).val() != '')
	{
		extCheck();
	}
});
/* ]]> */
</script>
