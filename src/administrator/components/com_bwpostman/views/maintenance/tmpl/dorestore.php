<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance restoreTables template for backend.
 *
 * @version %%version_number%% build %%build_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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

JHtml::_('behavior.modal');
JHtml::_('behavior.framework', true);
$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
JHtml::_('script', 'system/modal' . $uncompressed . '.js', true, true);
JHtml::_('stylesheet', 'media/system/css/modal.css');

$model		= $this->getModel();
$token      = JSession::getFormToken();
?>

<div id="restoreResult" class="row-fluid">
	<div class="span6 inner well">
		<h2><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES'); ?></h2>
		<p id="step1" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_1'); ?></p>
		<p id="step2" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_2'); ?></p>
		<p id="step3" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_3'); ?></p>
		<p id="step4" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_4'); ?></p>
		<p id="step5" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_5'); ?></p>
		<p id="step6" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_6'); ?></p>
		<p id="step7" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_7'); ?></p>
		<p id="step8" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_8'); ?></p>
		<p id="step9" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_9'); ?></p>
		<p id="step10" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_10'); ?></p>
		<p id="step11" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_11'); ?></p>
	</div>
	<div class="span6 well well-small resultSet">
		<h2><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESULT'); ?></h2>
		<div id="loading2"></div>
		<div id="error"></div>
		<div id="result"></div>
	</div>
</div>

<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

<script type="text/javascript">
	function doAjax(data, successCallback)
	{
		var structure =
		{
			success: function(data)
			{
				// Call the callback function
				successCallback(data);
			},
			error: function(req) {
				var message = '<p class="bw_tablecheck_error">AJAX Loading Error: '+req.statusText+'</p>';
				jQuery('div#loading2').css({display:'none'});
				jQuery('p#'+data.step).removeClass('alert-info').addClass('alert-error');
				jQuery('div#result').html(message);
				jQuery('div.resultSet').css('background-color', '#f2dede');
				jQuery('div.resultSet').css('border-color', '#eed3d7');
				jQuery('div#toolbar').find('button').removeAttr('disabled');
			}
		};

		structure.url = starturl;
		structure.data = data;
		structure.type = 'POST',
		structure.dataType = 'json',
		jQuery.ajax(structure);
	}

	function processUpdateStep(data)
	{
		jQuery('p#step'+(data.step-1)).removeClass('alert-info').addClass('alert-'+data.aClass);
		jQuery('p#step'+data.step).addClass('alert alert-info');
		// Do AJAX post
		post = {step : 'step'+data.step};
		doAjax(post, function(data)
		{
			if(data.ready != "1")
			{
				jQuery('div#result').html(data.result);
				jQuery('div#error').html(data.error);
				processUpdateStep(data);
			}
			else
			{
				jQuery('p#step'+(data.step-1)).removeClass('alert-info').addClass('alert alert-'+data.aClass);
				jQuery('div#loading2').css({display:'none'});
				jQuery('div#result').html(data.result);
				if (data.error != '')
				{
					jQuery('div.resultSet').css('background-color', '#f2dede');
					jQuery('div.resultSet').css('border-color', '#eed3d7');
				}
				else
				{
					jQuery('div.resultSet').css('background-color', '#dff0d8');
					jQuery('div.resultSet').css('border-color', '#d6e9c6');
				}
				jQuery('div#error').html(data.error);
				jQuery('div#toolbar').find('button').removeAttr('disabled');
			}
		});
	}
	jQuery('div#toolbar').find('button').attr("disabled","disabled");
	var starturl = 'index.php?option=com_bwpostman&task=maintenance.tRestore&format=json&<?php echo JSession::getFormToken(); ?>=1';
	var data = {step: "1"};
	processUpdateStep(data);
</script>
