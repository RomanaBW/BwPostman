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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('com_bwpostman.admin-bwpm_do_restore');

$model		= $this->getModel();
$token      = Session::getFormToken();
?>

<div id="restoreResult" class="row">
	<div class="col-lg-6">
		<div class="card card-body">
			<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES'); ?></h2>
			<p id="step1" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_1'); ?></p>
			<p id="step2" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_2'); ?></p>
			<p id="step3" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_3'); ?></p>
			<p id="step4" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_4'); ?></p>
			<p id="step5" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_5'); ?></p>
			<p id="step6" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_6'); ?></p>
			<p id="step7" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_7'); ?></p>
			<p id="step8" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_8'); ?></p>
			<p id="step9" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_9'); ?></p>
			<p id="step10" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_10'); ?></p>
			<p id="step11" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_11'); ?></p>
			<p id="step12" class="alert alert-secondary mt-0"><span class="fa fa-pulse"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STEP_12'); ?></p>
		</div>
	</div>
	<div class="col-lg-6">
		<div id="resultSet" class="resultSet card card-body">
		<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESULT'); ?></h2>
			<div id="loading2" class="text-center my-3"><i class="fas fa-spinner fa-pulse fa-5x"></i></div>
			<div id="error"></div>
			<div id="result"></div>
		</div>
	</div>
</div>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

<input type="hidden" id="startUrl" value="index.php?option=com_bwpostman&task=maintenancejson.tRestore&format=json&<?php echo Session::getFormToken(); ?>=1" />
