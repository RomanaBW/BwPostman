<?php
/**
 * TimeControl Plugin for BwPostman Newsletter Component
 *
 * TimeControl installer for plugin.
 *
 * @version 2.0.0 bwplgtc
 * @package BwPostman TimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014-2017 Boldt Webservice <forum@boldt-webservice.de>
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

 // No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of BwTimeControl plugin
 *
 * @since       2.0.0
 */
class plgBwpostmanBwtimecontrolInstallerScript
{
	/**
	 * @var JAdapterInstance $parentInstaller
	 *
	 * @since       2.0.0
	 */
	var $parentInstaller;

	/**
	 * @var string $minimum_joomla_release
	 *
	 * @since       2.0.0
	 */
	var $minimum_joomla_release;

	/**
	 * @var string release
	 *
	 * @since       2.0.0
	 */
	var $release = null;

	/**
  * Method to install the extension
  *
  * @return void
   *
   * @since       2.0.0
  */
  function install()
  {
    $this->showFinished(false);
  }

  /**
  * Method to uninstall the extension
  *
  * @return void
   *
   * @since       2.0.0
  */
  function uninstall()
  {
		JFactory::getApplication()->enqueueMessage(JText::_('PLG_BWPOSTMAN_UNINSTALL_THANKYOU'), 'message');
  }

  /**
  * Method to update the extension
  *
  * @return void
  *
  * @since       2.0.0
  */
  function update()
  {
		$this->showFinished(true);
  }

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param  object      $parent     is the class calling this method
	 * @param  string      $type       is the type of change (install, update or discover_install)
	 *
	 * @return     bool    true on success
	 *
	 * @since       2.0.0
	 */
  function preflight($type, $parent)
  {
		$app 		= JFactory::getApplication ();
		$jversion	= new JVersion();

		// Get component manifest file version
		$this->release = $parent->get("manifest")->version;

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt'))
		{
			$app->enqueueMessage(JText::sprintf('PLG_BWPOSTMAN_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(version_compare(phpversion(), '5.3.10', 'lt'))
		{
			$app->enqueueMessage(JText::_('BWPOSTMAN_USES_PHP5'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update')
		{
			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('PLG_BWPOSTMAN.update.oldRelease', $oldRelease);

			if (version_compare( $this->release, $oldRelease, 'lt')) {
				$app->enqueueMessage(JText::sprintf('PLG_BWPOSTMAN_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}
		}
		return true;
  }

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param  object      $parent     is the class calling this method
	 * @param  string      $type       is the type of change (install, update or discover_install)
	 *
	 * @since       2.0.0
	 */
  function postflight($type, $parent)
  {
  }

	/**
	 * Method to get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param  string      $name
	 *
	 * @return     bool    true on success
	 *
	 * @since       2.0.0
	 */
	private function getManifestVar($name)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('PLG_BWPOSTMAN_BWTIMECONTROL'));
		$db->setQuery($query);

		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}

	/**
	 * Method to show the HTML after installation/update
	 *
	 * @param   bool    $update
	 *
	 * @since       2.0.0
	 */
	public function showFinished($update){

		$lang = JFactory::getLanguage();
		//Load first english files
		$lang->load('plg_bwpostman_bwtimecontrol.sys',JPATH_ADMINISTRATOR,'en_GB',true);
		$lang->load('plg_bwpostman_bwtimecontrol',JPATH_ADMINISTRATOR,'en_GB',true);

		//load specific language
		$lang->load('plg_bwpostman_bwtimecontrol.sys',JPATH_ADMINISTRATOR,null,true);
		$lang->load('plg_bwpostman_bwtimecontrol',JPATH_ADMINISTRATOR,null,true);

		$show_update	= false;
		$show_right		= false;
		$release		= str_replace('.', '-', $this->release);
		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$lang_ver = 'en';
			$forum	= "https://www.boldt-webservice.de/en/forum-en/bwpostman.html";
		}
		else
		{
			$forum	= "https://www.boldt-webservice.de/de/forum/bwpostman.html";
		}
		$manual	= "https://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

		if ($update)
		{
			$string_special		= JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else
		{
			$string_special		= JText::_('PLG_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}
		$string_new			= JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement	= JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix		= JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
		{
			$show_update	= true;
		}
		if ($show_update || $string_special != '')
		{
			$show_right	= true;
		}
		?>

<link rel="stylesheet" href="../plugins/bwpostman/bwtimecontrol/assets/css/install.css" type="text/css" />

<div id="plg_bwp_install_header">
	<a href="https://www.boldt-webservice.de" target="_blank">
		<img border="0" align="center" src="../plugins/bwpostman/bwtimecontrol/assets/images/bw_header.png" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="plg_bwp_install_outer">
	<h1><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_WELCOME') ?></h1>
	<div id="plg_bwp_install_left">
		<div class="plg_bwp_install_welcome">
			<p><?php echo JText::_('PLG_BWPOSTMAN_DESCRIPTION') ?></p>
		</div>
		<div class="plg_bwp_install_finished">
			<h2>
			<?php
			if($update){
				echo JText::sprintf('PLG_BWPOSTMAN_UPGRADE_SUCCESSFUL', $this->release);
//				echo '<br />'.JText::_('PLG_BWPOSTMAN_EXTENSION_UPGRADE_REMIND');
			} else {
				echo JText::sprintf('PLG_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $this->release);
			}
			?>
			</h2>
		</div>
		<?php if ($show_right) { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_plugins&amp;filter_search=timecontrol'); ?>">
            			<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-bwpostman.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_MANUAL'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-manual.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_FORUM'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-forum.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php }?>
	</div>

	<div id="plg_bwp_install_right">
		<?php if ($show_right)
		{ ?>
			<?php if ($string_special != '')
			{ ?>
				<div class="plg_bwp_install_specialnote">
					<h2><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<div class="urgent"><?php echo $string_special; ?></div>
					<div class="icon">
						<a href="<?php echo JRoute::_('index.php?option=com_plugins&amp;filter_search=timecontrol'); ?>">
							<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-bwpostman.png">
							<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?></span>
						</a>
					</div>
				</div>
			<?php
			}?>

			<?php if ($show_update)
			{ ?>
				<div class="plg_bwp_install_updateinfo">
					<h2><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATEINFO') ?></h2>
					<?php if ($string_new != '') { ?>
						<h3><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
						<p><?php echo $string_new; ?></p>
					<?php
					}?>
					<?php if ($string_improvement != '')
					{ ?>
					<h3><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
						<p><?php echo $string_improvement; ?></p>
					<?php
					}?>
					<?php if ($string_bugfix != '')
					{ ?>
						<h3><?php echo JText::_('PLG_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
						<p><?php echo $string_bugfix; ?></p>
					<?php
					}?>
				</div>
			<?php
			}?>
		<?php
		}
		else
		{ ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_plugins&amp;filter_search=timecontrol&amp;token='.JUtility::getToken()); ?>">
            <img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-bwpostman.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_GO_PLUGINS'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_MANUAL'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-manual.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo JText::_('PLG_BWPOSTMAN_INSTALL_FORUM'); ?>" src="../plugins/bwpostman/bwtimecontrol/assets/images/icon-48-forum.png">
						<span><?php echo JText::_('PLG_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>
	<div class="clr"></div>

	<div class="plg_bwp_install_footer">
		<p class="small"><?php echo JText::_('&copy; 2013-'); echo date (" Y")?> by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a></p>
	</div>
</div>
<br /><br /><br />
	<?php
	}
}
