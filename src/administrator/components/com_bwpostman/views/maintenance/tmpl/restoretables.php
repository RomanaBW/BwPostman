<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance restoreTables template for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
JHtml::_('bootstrap.tooltip');
?>

<script type="text/javascript">
/* <![CDATA[ */
//-------------------------------------------------------------------------------------
//http://www.easywayserver.com/blog/enable-disable-radio-button-text-box-in-javascript
//-------------------------------------------------------------------------------------

 function enable_disable()
 {

  // Initialize form with empty field
  document.adminForm.restorefile.disabled=false;
  document.adminForm.restorefile.value="";

  for(var i=0;i<document.adminForm.elements.length;i++)
  {
    if(document.adminForm.elements[i].restorefile.value!="")
    {
       if(document.adminForm.elements[i].checked==true){
        document.adminForm.restorefile.disabled=false;
        document.adminForm.submitbutton.disabled=false;
       }
     }
  }
 }
 /* ]]> */
</script>

<?php
	$jinput	= JFactory::getApplication()->input;
	$image	= '<i class="icon-info"></i>';
	$option	= $jinput->getCmd('option');
?>

<form action="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_SELECT_RESTORE_FILE'); ?></legend>
		<div class="well well-small warning"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_USER_MESSAGE')?></div>
		<div class="well well-small">
			<div class="row-fluid">
				<table class="admintable bwptable restore">
					<tr>
						<td align="right" class="key">
							<span class="bwplabel"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_FILE'); ?></span>
							<span class="editlinktip hasTip hasTooltip"
									title="<?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_FILE_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
						</td>
						<td>
							<input type="file" name="restorefile" id="restorefile" />
						</td>
					</tr>
					<tr>
						<td width="250" align="center" class="key">
							<input type="button" class="btn btn-success" name="submitbutton"
									onclick="Joomla.submitbutton('maintenance.doRestore');
										document.getElementById('loading').style.display = 'block';"
									value="<?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_BUTTON'); ?>">
						</td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>
	<input type="hidden" name="task" value="doRestore" />
	<input type="hidden" name="controller" value="maintenance" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="loading" style="display: none;"></div>

<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
