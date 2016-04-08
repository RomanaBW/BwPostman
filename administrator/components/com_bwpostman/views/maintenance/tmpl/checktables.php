<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance checkTables template for backend.
 *
 * @version 1.3.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

JHTML::_('behavior.modal');
JHTML::_('behavior.framework',true);
$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
JHTML::_('script','system/modal'.$uncompressed.'.js', true, true);
JHTML::_('stylesheet','media/system/css/modal.css');

$model		= $this->getModel();




/*
switch ($this->check_res['type']) {
	case 'error':	$class	= "bw_tablecheck_error bw_maintenance_result err";
		break;
	case 'warn':	$class	= "bw_tablecheck_warn bw_maintenance_result";
		break;
	case 'message':	$class	= "bw_tablecheck_ok bw_maintenance_result ok";
		break;
}
*/
?>
<div id="checkResult" class="row-fluid">
	<div class="span6 inner well">
		<h2><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES'); ?></h2>
		<p id="step1" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_1'); ?></p>
		<p id="step2" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_2'); ?></p>
		<p id="step3" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_3'); ?></p>
		<p id="step4" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_4'); ?></p>
		<p id="step5" class="well"><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_5'); ?></p>
	</div>
	<div class="span6 well well-small">
		<h2><?php echo JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_RESULT'); ?></h2>
		<div id="loading2"></div>
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
	doAjax(post, function(data){
		if(data.ready != "1"){
			processUpdateStep(data);
		} else {
			jQuery('p#step'+(data.step-1)).removeClass('alert-info').addClass('alert alert-'+data.aClass);
			jQuery('div#loading2').css({display:'none'});
			jQuery('div#result').html(data.result);
			jQuery('div#toolbar').find('button').removeAttr('disabled');
		}
	});
}
jQuery('div#toolbar').find('button').attr("disabled","disabled");
var starturl = 'index.php?option=com_bwpostman&task=maintenance.tCheck&format=json&<?php echo JSession::getFormToken(); ?>=1';
var data = {step: "1"};
processUpdateStep(data);
</script>
