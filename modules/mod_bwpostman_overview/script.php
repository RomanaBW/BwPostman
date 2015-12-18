<?php
/**
 * BwPostman Newsletter Overview Modul
 *
 * BwPostman installer for module.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Overview-Module
 * @author Romana Boldt
 * @copyright (C) 2015 Boldt Webservice <forum@boldt-webservice.de>
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

 // No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of BwPostmanOverview module
 */
class mod_BwPostman_overviewInstallerScript
{
  /**
  * Method to install the extension
  * $parent is the class calling this method
  *
  * @return void
  */
  function install($parent)
  {
    $this->showFinished(false);
  }

  /**
  * Method to uninstall the extension
  * $parent is the class calling this method
  *
  * @return void
  */
  function uninstall($parent)
  {
		JFactory::getApplication()->enqueueMessage(JText::_('MOD_BWPOSTMAN_OVERVIEW_UNINSTALL_THANKYOU'), 'message');
  }

  /**
  * Method to update the extension
  * $parent is the class calling this method
  *
  * @return void
  */
  function update($parent)
  {
		$this->showFinished(true);
  }

  /**
  * Method to run before an install/update/uninstall method
  * $parent is the class calling this method
  * $type is the type of change (install, update or discover_install)
  *
  * @return void
  */
  function preflight($type, $parent)
  {
		$app 		= JFactory::getApplication ();
		$jversion	= new JVersion();
		$jInstall	= new JInstaller('mod_bwpostman_overview');

		// Get component manifest file version
		$this->release = $parent->get("manifest")->version;

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
			$app->enqueueMessage(JText::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(floatval(phpversion()) < 5)
		{
			$app->enqueueMessage(JText::_('MOD_BWPOSTMAN_OVERVIEW_USES_PHP5'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update') {
			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('mod_bwpostman_overview.update.oldRelease', $oldRelease);

			if (version_compare( $this->release, $oldRelease, 'lt')) {
				$app->enqueueMessage(JText::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}
		}
  }

	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	private function getManifestVar($name) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('mod_bwpostman_overview'));
		$db->SetQuery($query);

		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}


  /**
  * Method to run after an install/update/uninstall method
  * $parent is the class calling this method
  * $type is the type of change (install, update or discover_install)
  *
  * @return void
  */
  function postflight($type, $parent)
  {
  }

	/*
	 * shows the HTML after installation/update
	 */
	public function showFinished($update){

		$lang = JFactory::getLanguage();
		//Load first english files
		$lang->load('mod_bwpostman_overview.sys',JPATH_SITE,'en_GB',true);
		$lang->load('mod_bwpostman_overview',JPATH_SITE,'en_GB',true);

		//load specific language
		$lang->load('mod_bwpostman_overview.sys',JPATH_SITE,null,true);
		$lang->load('mod_bwpostman_overview',JPATH_SITE,null,true);

		$show_update	= false;
		$show_right		= false;
		$release		= str_replace('.', '-', $this->release);
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
			$string_special		= JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else {
			$string_special		= JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}
		$string_new			= JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement	= JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix		= JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update) {
			$show_update	= true;
		}
		if ($show_update || $string_special != '') {
			$show_right	= true;
		}
		?>

<link rel="stylesheet" href="../modules/mod_bwpostman_overview/assets/css/install.css" type="text/css" />

<div id="mod_bwp_install_header">
	<a href="http://www.boldt-webservice.de" target="_blank">
		<img border="0" align="center" src="../modules/mod_bwpostman_overview/images/bw_header.png" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="mod_bwp_install_outer">
	<h1><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_WELCOME') ?></h1>
	<div id="mod_bwp_install_left">
		<div class="mod_bwp_install_welcome">
			<p><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_DESCRIPTION') ?></p>
		</div>
		<div class="mod_bwp_install_finished">
			<h2>
			<?php
			if($update){
				echo JText::sprintf('MOD_BWPOSTMAN_OVERVIEW_UPGRADE_SUCCESSFUL', $this->release);
//				echo '<br />'.JText::_('MOD_BWPOSTMAN_OVERVIEW_EXTENSION_UPGRADE_REMIND');
			} else {
				echo JText::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_SUCCESSFUL', $this->release);
			}
			?>
			</h2>
		</div>
		<?php if ($show_right) { ?>
			<div class="cpanel">
				<div class="icon" >
					<?php if ($update) { ?>
						<a href="<?php echo JROUTE::_('index.php?option=com_modules'); ?>">
					<?php }
					else { ?>
						<a href="<?php echo JROUTE::_('index.php?option=com_modules&amp;filter_search=bw'); ?>">
					<?php } ?>
						<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-bwpostman.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-manual.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-forum.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php }?>
	</div>

	<div id="mod_bwp_install_right">
		<?php if ($show_right) { ?>
			<?php if ($string_special != '') { ?>
				<div class="mod_bwp_install_specialnote">
					<h2><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
					<div class="icon">
						 <?php if ($update) { ?>
							<a href="<?php echo JROUTE::_('index.php?option=com_modules'); ?>">
						<?php }
						else { ?>
							<a href="<?php echo JROUTE::_('index.php?option=com_modules&amp;filter_search=bw'); ?>">
						<?php } ?>
								<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-bwpostman.png">
								<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
							</a>
					</div>
				</div>
			<?php }?>

			<?php if ($show_update) { ?>
				<div class="mod_bwp_install_updateinfo">
					<h2><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATEINFO') ?></h2>
					<?php if ($string_new != '') { ?>
						<h3><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
						<div><?php echo $string_new; ?></div>
					<?php }?>
					<?php if ($string_improvement != '') { ?>
					<h3><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
						<div><?php echo $string_improvement; ?></div>
					<?php }?>
					<?php if ($string_bugfix != '') { ?>
						<h3><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
						<div><?php echo $string_bugfix; ?></div>
					<?php }?>
				</div>
			<?php }?>
		<?php }
		else { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JROUTE::_('index.php?option=com_modules&amp;filter_search=bw&amp;token='.JUtility::getToken()); ?>">
            <img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-bwpostman.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-manual.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?>" src="../modules/mod_bwpostman_overview/images/icon-48-forum.png">
						<span><?php echo JText::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clr"></div>

	<div class="mod_bwp_install_footer">
		<p class="small"><?php echo JText::_('&copy; 2012-'); echo date (" Y")?> by <a href="http://www.boldt-webservice.de" target="_blank">Boldt Webservice</a></p>
	</div>
</div>
<br /><br /><br />
	<?php
	}
}
