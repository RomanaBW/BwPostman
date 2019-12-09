<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance checkTables template for backend.
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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$uncompressed = Factory::getConfig()->get('debug') ? '-uncompressed' : '';
HTMLHelper::_('script', 'system/modal' . $uncompressed . '.js', true, true);
HTMLHelper::_('stylesheet', 'media/system/css/modal.css');

$model	= $this->getModel();
$uri    = Uri::root();
?>

<div id="checkResult" class="row">
	<div class="col-md-6 well">
		<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES'); ?></h2>
		<p id="step1" class="well"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_1'); ?></p>
		<p id="step2" class="well"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_2'); ?></p>
		<p id="step3" class="well"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_3'); ?></p>
		<p id="step4" class="well"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_4'); ?></p>
		<p id="step5" class="well"><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_5'); ?></p>
	</div>
	<div class="col-md-6 well well-small">
		<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_RESULT'); ?></h2>
		<div id="loading2"></div>
		<div id="result"></div>
	</div>
</div>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

<input type="hidden" id="startUrl" value="index.php?option=com_bwpostman&task=maintenance.tCheck&format=json&<?php echo Session::getFormToken(); ?>=1" />

