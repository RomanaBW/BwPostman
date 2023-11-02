<?php
/**
 * BwPostman Newsletter Overview Module
 *
 * BwPostman installer for module.
 *
 * @version %%version_number%%
 * @package BwPostman-Overview-Module
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

 // No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Database\DatabaseInterface;

/**
 * Script file of BwPostmanOverview module
 *
 * @since       1.2.0
 */
class Mod_BwPostman_OverviewInstallerScript
{
	/**
	 * @var InstallerAdapter $parentInstaller
	 *
	 * @since       1.2.0
	 */
	protected $parentInstaller;

	/**
	 * @var string $minimum_joomla_release
	 *
	 * @since       1.2.0
	 */
	protected $minimum_joomla_release;

	/**
	 * @var string release
	 *
	 * @since       1.2.0
	 */
	protected $release = null;

	/**
	 * Method to install the extension
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       1.2.0
	 */
	public function install()
	{
		$this->showFinished(false);
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       1.2.0
	 */
	public function uninstall()
	{
		Factory::getApplication()->enqueueMessage(Text::_('MOD_BWPOSTMAN_OVERVIEW_UNINSTALL_THANKYOU'), 'message');
	}

	/**
	 * Method to update the extension
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       1.2.0
	 */
	public function update()
	{
		$this->showFinished(true);
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param string           $type   is the type of change (install, update or discover_install)
	 * @param InstallerAdapter $parent is the class calling this method
	 *
	 * @return boolean            false if error occurs
	 *
	 * @throws Exception
	 * @since       1.2.0
	 */
	public function preflight(string $type, InstallerAdapter $parent): bool
	{
		$app 		= Factory::getApplication();

		// Get component manifest file version
		$manifest = $parent->getManifest();
		$this->release = $manifest->version;

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $manifest->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare(JVERSION, $this->minimum_joomla_release, 'lt'))
		{
			$app->enqueueMessage(Text::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(version_compare(phpversion(), '7.2.5', 'lt'))
		{
			$app->enqueueMessage(Text::_('MOD_BWPOSTMAN_OVERVIEW_USES_PHP7'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update')
		{
			JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('mod_bwpostman_overview.update.oldRelease', $oldRelease);

			if (version_compare($this->release, $oldRelease, 'lt'))
			{
				$app->enqueueMessage(
					Text::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release),
					'error'
				);
				return false;
			}
		}

		return true;
	}

	/**
	 * get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param string $name name of the manifest to get
	 *
	 * @return  string      $manifest the manifest for this module
	 *
	 * @throws Exception
	 *
	 * @since       1.2.0
	 */
	private function getManifestVar(string $name): string
	{
		$manifest = null;

		$db		= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('mod_bwpostman_overview'));

		try
		{
			$db->setQuery($query);

			$manifest = json_decode($db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $manifest[$name];
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string $type   is the type of change (install, update or discover_install)
	 * @param object $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since       1.2.0
	 */
	public function postflight(string $type, object $parent)
	{
		if ($type == 'update')
		{
			// remove obsolete files
			$this->removeObsoleteFilesAndFolders();
		}
	}

	/**
	 * Method to remove obsolete files and folders
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function removeObsoleteFilesAndFolders()
	{
		$feFilesArray = array(
			'helper.php',
			'css/bwpostman_overview.css',
			'css/install.css',
			'css/index.html',
			'images/bw_header.png',
			'images/icon-48-bwpostman.png',
			'images/icon-48-forum.png',
			'images/icon-48-manual.png',
			'images/verlauf_gelb_2.png',
			'images/index.html',
			'js/mod_archive.js',
		);

		foreach ($feFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/modules/mod_bwpostman_overview/' . $file))
			{
				File::delete(JPATH_ROOT . '/modules/mod_bwpostman_overview/' . $file);
			}
		}

		$feFoldersArray = array(
			'css',
			'images',
			'js',
		);

		foreach ($feFoldersArray as $folder)
		{
			if (Folder::exists(JPATH_ROOT . '/modules/mod_bwpostman_overview/' . $folder))
			{
				Folder::delete(JPATH_ROOT . '/modules/mod_bwpostman_overview/' . $folder);
			}
		}

		$mediaFilesArray = array(
			'js/mod_archive.js',
		);

		foreach ($mediaFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/media/mod_bwpostman_overview/' . $file))
			{
				File::delete(JPATH_ROOT . '/media/mod_bwpostman_overview/' . $file);
			}
		}

		$mediaFoldersArray = array(
				'js',
		);

		foreach ($mediaFoldersArray as $folder)
		{
			if (Folder::exists(JPATH_ROOT . '/media/mod_bwpostman_overview/' . $folder))
			{
				Folder::delete(JPATH_ROOT . '/media/mod_bwpostman_overview/' . $folder);
			}
		}
	}

	/**
	 * shows the HTML after installation/update
	 *
	 * @param boolean $update true if update
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       1.2.0
	 */
	public function showFinished(bool $update)
	{
		$lang = Factory::getApplication()->getLanguage();
		//Load first english files
		$lang->load('mod_bwpostman_overview.sys', JPATH_SITE, 'en_GB', true);
		$lang->load('mod_bwpostman_overview', JPATH_SITE, 'en_GB', true);

		//load specific language
		$lang->load('mod_bwpostman_overview.sys', JPATH_SITE, null, true);
		$lang->load('mod_bwpostman_overview', JPATH_SITE, null, true);

		$show_update	= false;
		$show_right		= false;
		$release		= str_replace('.', '-', $this->release);
		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$lang_ver = 'en';
			$forum	= "https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html";
		}
		else
		{
			$forum	= "https://www.boldt-webservice.de/de/forum/bwpostman.html";
		}

		$manual	= "https://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

		if ($update)
		{
			$string_special		= Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else
		{
			$string_special		= Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}

		$string_new			= Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement	= Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix		= Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
		{
			$show_update	= true;
		}

		if ($show_update || $string_special != '')
		{
			$show_right	= true;
		}

		$asset_path = 'media/mod_bwpostman_overview';
		?>

<link rel="stylesheet" href="<?php echo Route::_($asset_path . '/css/install.css'); ?>" type="text/css" />

<div id="mod_bwp_install_header">
	<a href="https://www.boldt-webservice.de" target="_blank">
		<img src="<?php echo Route::_($asset_path . '/images/bw_header.png'); ?>" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="mod_bwp_install_outer">
	<h1><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_WELCOME') ?></h1>
	<div id="mod_bwp_install_left">
		<div class="mod_bwp_install_welcome">
			<p><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_DESCRIPTION') ?></p>
		</div>
		<div class="mod_bwp_install_finished">
			<h2>
			<?php
			if($update)
			{
				echo Text::sprintf('MOD_BWPOSTMAN_OVERVIEW_UPGRADE_SUCCESSFUL', $this->release);
			}
			else
			{
				echo Text::sprintf('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_SUCCESSFUL', $this->release);
			}
			?>
			</h2>
		</div>
		<?php
		if ($show_right)
		{ ?>
			<div class="cpanel">
				<div class="icon" >
					<?php
					if ($update)
					{ ?>
						<a href="<?php echo Route::_('index.php?option=com_modules'); ?>">
					<?php
					}
					else
					{ ?>
						<a href="<?php echo Route::_('index.php?option=com_modules&amp;filter_search=bw'); ?>">
					<?php
					} ?>
						<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>"
								src="<?php echo Route::_($asset_path . '/images/icon-48-bwpostman.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?>"
								src="<?php echo Route::_($asset_path . '/images/icon-48-manual.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?>"
								src="<?php echo Route::_($asset_path . '/images/icon-48-forum.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>

	<div id="mod_bwp_install_right">
		<?php
		if ($show_right)
		{ ?>
			<?php
			if ($string_special != '')
			{ ?>
				<div class="mod_bwp_install_specialnote">
					<h2><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
					<div class="icon">
						<?php
						if ($update)
						{ ?>
							<a href="<?php echo Route::_('index.php?option=com_modules'); ?>">
						<?php
						}
						else
						{ ?>
							<a href="<?php echo Route::_('index.php?option=com_modules&amp;filter_search=bw'); ?>">
						<?php
						} ?>
								<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>"
										src="<?php echo Route::_($asset_path . '/images/icon-48-bwpostman.png'); ?>">
								<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
							</a>
					</div>
				</div>
			<?php
			}?>

			<?php
			if ($show_update)
			{ ?>
				<div class="mod_bwp_install_updateinfo">
					<h2><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATEINFO') ?></h2>
					<?php
					if ($string_new != '')
					{ ?>
						<h3><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
						<div><?php echo $string_new; ?></div>
					<?php
					}?>
					<?php
					if ($string_improvement != '')
					{ ?>
					<h3><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
						<div><?php echo $string_improvement; ?></div>
					<?php
					}?>
					<?php
					if ($string_bugfix != '')
					{ ?>
						<h3><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
						<div><?php echo $string_bugfix; ?></div>
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
					<a href="<?php echo Route::_('index.php?option=com_modules&amp;filter_search=bw&amp;token=' . (new Session)->getToken()); ?>">
			<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?>"
					src="<?php echo Route::_($asset_path . '/images/icon-48-bwpostman.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_GO_MODULES'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?>"
								src="<?php echo Route::_($asset_path . '/images/icon-48-manual.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<img alt="<?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?>"
								src="<?php echo Route::_($asset_path . '/images/icon-48-forum.png'); ?>">
						<span><?php echo Text::_('MOD_BWPOSTMAN_OVERVIEW_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>
	<div class="clr"></div>

	<div class="mod_bwp_install_footer">
		<p class="small"><?php echo Text::_('&copy; 2012-'); echo date(" Y")?> by <a href="https://www.boldt-webservice.de"
					target="_blank">Boldt Webservice</a></p>
	</div>
</div>
<br /><br /><br />
	<?php
	}
}
