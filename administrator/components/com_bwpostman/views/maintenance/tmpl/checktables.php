<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance checkTables template for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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





switch ($this->check_res['type']) {
	case 'error':	$class	= "bw_tablecheck_error bw_maintenance_result err";
		break;
	case 'warn':	$class	= "bw_tablecheck_warn bw_maintenance_result";
		break;
	case 'message':	$class	= "bw_tablecheck_ok bw_maintenance_result ok";
		break;
}
?>

<?php /*
<div class="<?php echo $class; ?> modal"><?php echo $this->check_res['message']; ?></div>
<div id="checkResult" class="modal" rel="{size: {x: 700, y: 500}, handler:'iframe', closable: true}">
*/?>
<div id="checkResult">
	<?php
		ob_start();
		echo '<div class="well">';
		$model->checkTables();
		echo '</div>';
		ob_flush();
		flush();
	?>
</div>
<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
