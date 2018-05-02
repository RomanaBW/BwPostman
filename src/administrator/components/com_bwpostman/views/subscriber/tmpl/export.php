<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber export template for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
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
JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
//JHtml::_('formbehavior.chosen', 'select');

?>

<script type="text/javascript">
/* <![CDATA[ */
//-----------------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-----------------------------------------------------------------------------
	function selectAllOptions(obj)
	{
		for (var i=0; i<obj.options.length; i++)
		{
			obj.options[i].selected = true;
		}
	}


//-----------------------------------------------------------------------------
// Like: http://www.plus2net.com/javascript_tutorial/list-remove.php
//-----------------------------------------------------------------------------
	function removeOptions(selectbox)
	{
		var i;

		for(i=selectbox.options.length-1;i>=0;i--)
		{
			if(selectbox.options[i].selected)
			{
				selectbox.remove(i);
			}
		}
	}

//-----------------------------------------------------------------------------
//http://javascript.internet.com/forms/select-box-with-options.html
//-----------------------------------------------------------------------------
	function moveUp(element) // Method to move an item up
	{
		for(i = 0; i < element.options.length; i++)
		{
			if(element.options[i].selected == true)
			{
				if(i != 0)
				{
					var temp    = new Option(element.options[i-1].text,element.options[i-1].value);
					var temp2   = new Option(element.options[i].text,element.options[i].value);
					element.options[i-1] = temp2;
					element.options[i-1].selected = true;
					element.options[i] = temp;
				}
			}
		}
	}

	function moveDown(element) // Method to move an item down
	{
		for(i = (element.options.length - 1); i >= 0; i--)
		{
			if(element.options[i].selected == true)
			{
				if(i != (element.options.length - 1))
				{
					var temp    = new Option(element.options[i+1].text,element.options[i+1].value);
					var temp2   = new Option(element.options[i].text,element.options[i].value);
					element.options[i+1] = temp2;
					element.options[i+1].selected = true;
					element.options[i] = temp;
				}
			}
		}
	}

	function check() // Method to check if the user didn't delete all items in the select box
	{
		var count_export_fields = document.getElementById('export_fields').length;

		if (count_export_fields <= 0)
		{
		alert ("<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ERROR_NO_EXPORTFIELDS', true); ?>");
		return 0;
		}
		return 1;
	}
 /* ]]> */
</script>

<?php
	$jinput	= JFactory::getApplication()->input;
	$image	= '<i class="icon-info"></i>';
	$option	= $jinput->getCmd('option');
?>

<form action="<?php echo $this->request_url_raw; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'); ?></legend>
		<div class="well well-small">
			<table class="admintable export">
				<tr class="bwptable fileformat">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_SUB_FILEFORMAT'); ?></span>
						<span class="editlinktip hasTip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_FILEFORMAT_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td class="bwptable"><div class="bwpmailformat"><?php echo $this->lists['fileformat']; ?></div></td>
				</tr>
				<tr class="bwptable delimiter">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_SUB_DELIMITER'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_DELIMITER_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td><?php echo $this->lists['delimiter'];?></td>
				</tr>
				<tr class="bwptable enclosure">
					<td align="right" class="key">
						<span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td><?php echo $this->lists['enclosure'];?></td>
				</tr>
				<tr class="bwptable exportgroups">
					<td align="right" class="key"><span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS'); ?></span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_GROUPS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td class="bwptable mailformat">
						<div class="bwpmailformat">
							<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_STATUS'); ?>
							<p class="state"><input type="checkbox" id="status1" name="status" title="status" value="1" />
								<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_CONFIRMED'); ?>
							</p>
							<p class="state"><input type="checkbox" id="status0" name="status" title="status" value="1" />
								<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_UNCONFIRMED'); ?>
							</p>
							<p class="state"><input type="checkbox" id="status9" name="status" title="status" value="1" />
								<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_TEST'); ?>
							</p>
							<br />
							<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVE'); ?><br />
							<p class="archive"><input type="checkbox" id="archive0" name="archive" title="archive" value="1" />
								<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_UNARCHIVED'); ?>
							</p>
							<p class="archive"><input type="checkbox" id="archive1" name="archive" title="archive" value="1" />
								<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_ARCHIVED'); ?>
							</p>
						</div>
					</td>
				</tr>
				<tr class="exportfields">
					<td width="150" align="right" class="key"><?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS'); ?><br />
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
					</td>
					<td valign="top" width="280"><?php echo $this->lists['export_fields']; ?><br />
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_MOVE_UP_NOTE');?>">
							<input class="btn btn-small" type="button" name="upbutton" onclick="moveUp(document.getElementById('export_fields'));"
									value="<?php echo JText::_('COM_BWPOSTMAN_SUB_MOVE_UP'); ?>" />
						</span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_MOVE_DOWN_NOTE');?>">
							<input class="btn btn-small" type="button" name="downbutton" onclick="moveDown(document.getElementById('export_fields'));"
									value="<?php echo JText::_('COM_BWPOSTMAN_SUB_MOVE_DOWN'); ?>" />
						</span>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED_NOTE');?>">
							<input class="btn btn-small" type="button" name="removebutton" onclick="removeOptions(export_fields);"
									value="<?php echo JText::_('COM_BWPOSTMAN_SUB_REMOVE_SELECTED'); ?>" />
						</span>
					</td>
					<td>&nbsp;</td>
					<td valign="top"><?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_FIELDS_ANNOTATION'); ?></td>
				</tr>
				<tr class="button">
					<td width="150" align="center" class="key">
						<input class="btn btn-success" type="button" name="submitbutton"
								onclick="if(check()){selectAllOptions(document.adminForm['export_fields[]']);Joomla.submitbutton('subscribers.export');}"
								value="<?php echo JText::_('COM_BWPOSTMAN_SUB_EXPORT_BUTTON'); ?>"
						/>
					</td>
				</tr>
			</table>
		</div>
	</fieldset>

	<input type="hidden" name="task" value="export" />
	<input type="hidden" name="controller" value="subscribers" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

function extCheck()
{
	var format		= $j("input[name='fileformat']:checked").val();

	switch (format)
	{
		case 'xml':
				$j( ".exportgroups" ).show();
				$j( ".exportfields" ).show();
			break;
		case 'csv':
				$j( ".exportgroups" ).show();
				$j( ".exportfields" ).show();
				$j( ".delimiter" ).show();
				$j( ".enclosure" ).show();
				$j( ".caption" ).show();
			break;
	}
}

$j(document).ready(function()
{
	$j( ".delimiter" ).hide();
	$j( ".enclosure" ).hide();
	$j( ".caption" ).hide();
	$j( ".exportgroups" ).hide();
	$j( ".exportfields" ).hide();
	$j( ".button" ).hide();
});

$j("input[name='fileformat']").on("change", function()
{
	$j( ".delimiter" ).hide();
	$j( ".enclosure" ).hide();
	extCheck();
});

$j(".state input[type='checkbox']").on("change", function()
{
	if ($j( ".archive input:checked" ).length)
	{
		$j( ".button" ).show();
	}
	if ($j( ".state input:checked" ).length == 0)
	{
		$j( ".button" ).hide();
	}
});

$j(".archive input[type='checkbox']").on("change", function()
{
	if ($j( ".state input:checked" ).length)
	{
		$j( ".button" ).show();
	}
	if ($j( ".archive input:checked" ).length == 0)
	{
		$j( ".button" ).hide();
	}
});
/* ]]> */
</script>

