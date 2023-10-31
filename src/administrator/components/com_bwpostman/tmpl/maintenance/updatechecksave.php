<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance updateCheckSave template for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('com_bwpostman.admin-bwpm_maintenance_doAjax');
$wa->useScript('com_bwpostman.admin-bwpm_update_checksave');

$model		= $this->getModel();

$session	= Factory::getApplication()->getSession();
$update		= $session->get('update', false, 'bwpostman');
$release	= $session->get('release', null, 'bwpostman');

$lang = Factory::getApplication()->getLanguage();
//Load first english files
$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, 'en_GB', true);
$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);

//load specific language
$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, null, true);
$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

$show_update	= false;
$show_right		= false;
$lang_ver		= substr($lang->getTag(), 0, 2);
$forum	        = BwPostmanHTMLHelper::getForumLink();

$manual	= "https://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

if ($update)
{
	$string_special		= Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
}
else
{
	$string_special		= Text::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
}
$string_new			= Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
$string_improvement	= Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
$string_bugfix		= Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
{
	$show_update	= true;
}
if ($show_update || $string_special != '')
{
	$show_right	= true;
}
?>

<div id="com_bwp_install_header">
	<a href="https://www.boldt-webservice.de" target="_blank">
		<img class="img-fluid" src="<?php echo Uri::root(); ?>media/com_bwpostman/images/bw_header.png" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="com_bwp_install_outer">
</div>
<div class="row">
	<div id="checkResult" class="col-12">
		<div id="warn" class="card alert-warning my-3 p-2">
			<?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE_WARNING'); ?>
		</div>
		<div id="info" class="card alert-info my-3 p-2" style="display:none;">
			<?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE_INFO'); ?>
		</div>
	</div>
</div>
<div id="checkResult" class="row">
	<div class="col-lg-6">
		<div class="card card-body">
			<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES'); ?></h2>
			<p id="step0" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_0'); ?></p>
			<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES'); ?></h2>
			<p id="step1" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_1'); ?></p>
			<p id="step2" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_2'); ?></p>
			<p id="step3" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_3'); ?></p>
			<p id="step4" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_4'); ?></p>
			<p id="step5" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_5'); ?></p>
			<p id="step6" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_6'); ?></p>
			<p id="step7" class="alert alert-secondary mt-0"><span class="fa fa-pulse me-2"></span><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_STEP_7'); ?></p>
		</div>
	</div>
	<div class="col-lg-6">
		<div id="resultSet" class="resultSet card card-body">
			<h2><?php echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_AND_REPAIR_RESULT'); ?></h2>
			<div id="loading2" class="text-center my-3"></div>
			<div id="result"></div>
		</div>
	</div>
</div>
<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

<input type="hidden" id="startUrl" value="index.php?option=com_bwpostman&task=maintenancejson.tCheck&format=json&<?php echo Session::getFormToken(); ?>=1" />
