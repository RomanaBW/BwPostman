<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance updateCheckSave template for backend.
 *
 * @version 1.3.0 bwpm
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

//JHTML::_('behavior.framework',true);
JHTML::_('behavior.modal');
//$uncompressed = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
//JHTML::_('script','system/modal'.$uncompressed.'.js', true, true);
//JHTML::_('stylesheet','media/system/css/modal.css');

$model		= $this->getModel();

$session	= JFactory::getSession();
$update		= $session->get('update', false, 'bwpostman');
$release	= $session->get('release', null, 'bwpostman');

$lang = JFactory::getLanguage();
//Load first english files
$lang->load('com_bwpostman.sys',JPATH_ADMINISTRATOR,'en_GB',true);
$lang->load('com_bwpostman',JPATH_ADMINISTRATOR,'en_GB',true);

//load specific language
$lang->load('com_bwpostman.sys',JPATH_ADMINISTRATOR,null,true);
$lang->load('com_bwpostman',JPATH_ADMINISTRATOR,null,true);

$show_update	= false;
$show_right		= false;
$lang_ver		= substr($lang->getTag(), 0, 2);
if ($lang_ver != 'de') {
	$lang_ver = 'en';
	$forum	= "http://www.boldt-webservice.de/en/forum-en/bwpostman.html";
}
else {
	$forum	= "http://www.boldt-webservice.de/de/forum/bwpostman.html";
}
$manual	= "http://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

if ($update) {
	$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
}
else {
	$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
}
$string_new			= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
$string_improvement	= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
$string_bugfix		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update) {
	$show_update	= true;
}
if ($show_update || $string_special != '') {
	$show_right	= true;
}
?>

<link rel="stylesheet" href="components/com_bwpostman/assets/css/install.css" type="text/css" />

<div id="com_bwp_install_header">
	<a href="http://www.boldt-webservice.de" target="_blank">
		<img border="0" align="center" src="components/com_bwpostman/assets/images/bw_header.png" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="com_bwp_install_outer">
	<h1><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_WELCOME') ?></h1>
	<div id="com_bwp_install_left">
		<div class="com_bwp_install_welcome">
			<p><?php echo JText::_('COM_BWPOSTMAN_DESCRIPTION') ?></p>
		</div>
		<div class="com_bwp_install_finished">
			<h2>
			<?php
			if($update){
				echo JText::sprintf('COM_BWPOSTMAN_UPGRADE_SUCCESSFUL', $release);
				echo '<br />'.JText::_('COM_BWPOSTMAN_EXTENSION_UPGRADE_REMIND');
			} else {
				echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $release);
			}
			?>
			</h2>
		</div>
		<?php if ($show_right) { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JROUTE::_('index.php?option=com_bwpostman'); ?>"> <?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-manual.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-forum.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php }?>
	</div>

	<div id="com_bwp_install_right">
		<?php if ($show_right) { ?>
			<?php if ($string_special != '') { ?>
				<div class="com_bwp_install_specialnote">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
				</div>
			<?php }?>

			<?php if ($show_update) { ?>
				<div class="com_bwp_install_updateinfo">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATEINFO') ?></h2>
					<?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_CHANGELOG_INFO'); ?>
					<?php if ($string_new != '') { ?>
						<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
						<p><?php echo $string_new; ?></p>
					<?php }?>
					<?php if ($string_improvement != '') { ?>
					<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
						<p><?php echo $string_improvement; ?></p>
					<?php }?>
					<?php if ($string_bugfix != '') { ?>
						<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
						<p><?php echo $string_bugfix; ?></p>
					<?php }?>
				</div>
			<?php }?>
		<?php }
		else { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JROUTE::_('index.php?option=com_bwpostman&token='.JSession::getFormToken()); ?>"> <?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clr"></div>
<?php /*
	<div class="com_bwp_install_footer">
		<p class="small"><?php echo JText::_('&copy; 2012-'); echo date (" Y")?> by <a href="http://www.boldt-webservice.de" target="_blank">Boldt Webservice</a></p>
	</div>
</div>
*/?>

<?php
/*
switch ($this->check_res['type']) {
	case 'error':	$class	= "bw_tablecheck_error bw_maintenance_result err";
		break;
	case 'warn':	$class	= "bw_tablecheck_warn bw_maintenance_result";
		break;
	case 'message':	$class	= "bw_tablecheck_ok bw_maintenance_result ok";
		break;
}*/
?>

<?php /*
<div class="<?php echo $class; ?> modal"><?php echo $this->check_res['message']; ?></div>
<div id="checkResult">
*/?>
<div id="sbox" class="modal" rel="{size: {x: 700, y: 500}, handler='string'}">
	<?php
//		ob_start();
		echo '<div class="well">';
			echo JText::_('COM_BWPOSTMAN_INSTALL_FINISH_SAVE_TABLES');
			$this->check_res	= $model->saveTables(false);
			echo JText::_('COM_BWPOSTMAN_INSTALL_FINISH_TABLES_SAVED');
//			ob_flush();
//			flush();
			echo JText::_('COM_BWPOSTMAN_INSTALL_FINISH_CHECK_TABLES');
//			$this->check_res	= $model->checkTables();
			echo JText::_('COM_BWPOSTMAN_INSTALL_FINISH_TABLES_CHECKED');
//			ob_flush();
//			flush();
			echo '</div>';
//		ob_end_clean();
	?>
</div>
</div>
<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
