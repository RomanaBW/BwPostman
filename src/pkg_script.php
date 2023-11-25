<?php
/**
 * BwPostman Newsletter Package
 *
 * BwPostman package installer.
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
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Installer\Administrator\Model\UpdatesitesModel;

/**
 * Class Pkg_BwPostmanInstallerScript
 *
 * @since       2.2.1
 */
class Pkg_BwPostmanInstallerScript
{
	/**
	 * @var string release
	 *
	 * @since       2.2.1
	 */
	private $release = null;

	/**
	 * Called on installation
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.2.1
	 */

	public function install($installer)
	{
		sleep(5);
		$session = Factory::getApplication()->getSession();
		$session->set('update', false, 'bwpostman');

		// Get component manifest file version
		$parent = $installer->getParent();

		$manifest = $parent->getManifest();
		$this->release = (string)$manifest->version;

		// override existing message for update by installing manually
		$this->showFinished(false);
  }

	/**
	 * Called on update
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.2.1
	 */

	public function update($installer)
	{
        $app     = Factory::getApplication();
		$session = $app->getSession();
		$session->set('update', true, 'bwpostman');

		// Get component manifest file version
		$parent = $installer->getParent();

		$manifest = $parent->getManifest();
		$this->release = (string)$manifest->version;

        // Set redirect path to do table check on update by Joomla!
        $app->setUserState('com_installer.redirect_url', 'index.php?option=com_bwpostman&view=bwpostman');

        // override existing message for update by installing manually
		$this->showFinished(true);
	}

	/**
	 * Called before any type of action
	 *
	 * @param string           $type Which action is happening (install|uninstall|discover_install|update)
	 * @param InstallerAdapter $installer
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       2.2.1
	 */

	public function preflight(string $type, InstallerAdapter $installer): bool
	{
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param string $type Which action is happening (install|uninstall|discover_install)
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       2.2.1
	 */

	public function postflight(string $type, $installer): bool
	{
		if ($type == 'update')
		{
			$oldRelease	= Factory::getApplication()->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '4.2.6', 'lt'))
			{
				// rebuild update servers
				$installerModel = new UpdatesitesModel();
				$installerModel->rebuild();
			}

            $session = Factory::getApplication()->getSession();
            $session->set('com_bwpostman.extension_message', $installer->extensionMessage);
        }

        if ($type !== 'uninstall')
        {
            $manifest = $installer->getParent()->getManifest();

            if (is_object($manifest))
            {
                $this->release = $manifest->version;
            }

            // remove obsolete
            $this->removeObsoleteExtensions($type, $installer);
        }

		return true;
  }

	/**
	 * Method to remove obsolete extensions
	 *
	 * @param string           $type Which action is happening (install|uninstall|discover_install|update)
	 * @param InstallerAdapter $parent
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function removeObsoleteExtensions(string $type, InstallerAdapter $parent)
	{
		if ($type == 'update')
		{
			$obsoleteExtensions = array('bwpm_mediaoverride');
			$obsoleteFolders = array('bwpm_mediaoverride' => array(
				'/plugins/system/bwpm_mediaoverride',
			));
			$oldRelease = Factory::getApplication()->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '4.0.0', 'lt'))
			{
				foreach ($obsoleteExtensions as $obsoleteExtension)
				{
					$extId = $this->getExtensionId(0, $obsoleteExtension);

					// Remove extension files
					if ($extId)
					{
						// Remove extension from extension table
						$this->removeFromExtensionsTable($obsoleteExtension);

						foreach ($obsoleteFolders[$obsoleteExtension] as $folder)
						{
							$this->removeFilesAndFoldersRecursive(JPATH_ROOT . $folder);
						}
					}
				}
			}
		}
	}

	/**
	 * Get id of installed extension
	 *
	 * @param integer $clientId
	 * @param string  $extensionName
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private function getExtensionId(int $clientId, string $extensionName = 'com_bwpostman')
	{
		$db    = Factory::getDbo();
		$result = 0;

		$query = $db->getQuery(true);
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote($extensionName));
		$query->where($db->quoteName('client_id') . ' = ' . $db->quote($clientId));

		try
		{
			$db->setQuery($query);

			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Remove extension from extension table
	 *
	 * @param string  $extensionName
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private function removeFromExtensionsTable(string $extensionName): bool
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$result = false;

		$query->delete($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote($extensionName));

		try
		{
			$db->setQuery($query);
			$result = $db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to remove obsolete files and folders
	 *
	 * @param string $path  can be file or folder
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function removeFilesAndFoldersRecursive(string $path): bool
	{
		if (is_dir($path) === true)
		{
			$files = array_diff(scandir($path), array('.', '..'));

			foreach ($files as $file)
			{
				$this->removeFilesAndFoldersRecursive(realpath($path) . '/' . $file);
			}

			return rmdir($path);
		}
		elseif (is_file($path) === true || is_file(realpath($path)))
		{
			return unlink($path);
		}

		return false;
	}

	/**
	 * shows the HTML after installation/update
	 *
	 * @param boolean $update
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function showFinished(bool $update)
	{
		$lang = Factory::getApplication()->getLanguage();
		//Load first english files
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);

		//load specific language
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, null, true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$show_update = false;
		$show_right  = false;
		$lang_ver    = substr($lang->getTag(), 0, 2);

		if ($lang_ver != 'de')
		{
			$forum  = "https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html";
			$manual = "https://www.boldt-webservice.de/en/forum-en/manuals/bwpostman-manual.html";
		}
		else
		{
			$forum  = "https://www.boldt-webservice.de/de/forum/forum/bwpostman.html";
			$manual = "https://www.boldt-webservice.de/index.php/de/forum/handb%C3%BCcher/handbuch-zu-bwpostman.html";
		}


		if ($update)
		{
			$string_special = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else
		{
			$string_special = Text::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}

		$string_new         = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix      = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
		{
			$show_update = true;
		}
		if ($show_update || $string_special != '')
		{
			$show_right = true;
		}

		$asset_path = 'media/com_bwpostman';
		$image_path = 'media/com_bwpostman/images';
		?>

		<link rel="stylesheet" href="<?php echo Uri::root() . $asset_path . '/css/install.css'; ?>" type="text/css" />

		<div id="com_bwp_install_header" class="text-center">
			<a href="https://www.boldt-webservice.de" target="_blank">
				<img class="img-fluid border-0" src="<?php echo Uri::root() . $asset_path . '/images/bw_header.png'; ?>" alt="Boldt Webservice" />
			</a>
		</div>
		<div class="top_line"></div>

		<div id="com_bwp_install_outer" class="row">
			<div class="col-lg-12 text-center p-2 mt-2">
				<h1><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_WELCOME') ?></h1>
			</div>
			<div id="com_bwp_install_left" class="col-lg-6 mb-2">
				<div class="com_bwp_install_welcome">
					<p><?php echo Text::_('COM_BWPOSTMAN_DESCRIPTION') ?></p>
				</div>
				<div class="com_bwp_install_finished text-center">
					<h2>
						<?php
						if($update)
						{
							echo Text::sprintf('COM_BWPOSTMAN_UPGRADE_SUCCESSFUL', $this->release);
						}
						else
						{
							echo Text::sprintf('COM_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $this->release);
						}
						?>
					</h2>
				</div>
				<?php
				if ($show_right)
				{ ?>
					<div class="cpanel text-center mb-3">
						<div class="icon btn" >
							<a href="<?php echo Route::_('index.php?option=com_bwpostman'); ?>">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $manual; ?>" target="_blank">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-manual.png',
									Text::_('COM_BWPOSTMAN_INSTALL_MANUAL')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $forum; ?>" target="_blank">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-forum.png',
									Text::_('COM_BWPOSTMAN_INSTALL_FORUM')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
							</a>
						</div>
					</div>
					<?php
				} ?>
			</div>

			<div id="com_bwp_install_right" class="col-lg-6">
				<?php
				if ($show_right)
				{
					if ($string_special != '')
					{ ?>
						<div class="com_bwp_install_specialnote p-3">
							<h2><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
							<p class="urgent"><?php echo $string_special; ?></p>
						</div>
						<?php
					}?>

					<?php
					if ($show_update)
					{ ?>
						<div class="com_bwp_install_updateinfo mb-3 p-3">
							<h2 class="mb-3"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATEINFO') ?></h2>
							<?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_CHANGELOG_INFO'); ?>
							<?php if ($string_new != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
								<p><?php echo $string_new; ?></p>
							<?php }?>
							<?php if ($string_improvement != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
								<p><?php echo $string_improvement; ?></p>
							<?php }?>
							<?php if ($string_bugfix != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
								<p><?php echo $string_bugfix; ?></p>
							<?php }?>
						</div>
						<?php
					}
				}
				else
				{ ?>
					<div class="cpanel text-center mb-3">
						<div class="icon btn" >
							<a href="<?php echo Route::_('index.php?option=com_bwpostman&token=' . Session::getFormToken()); ?>">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $manual; ?>" target="_blank">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_MANUAL')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $forum; ?>" target="_blank">
								<?php echo HTMLHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_FORUM')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
							</a>
						</div>
					</div>
					<?php
				} ?>
			</div>
			<div class="clr clearfix"></div>

			<div class="com_bwp_install_footer col-12 text-center my-3">
				<p class="small">
					<?php echo Text::_('&copy; 2012-'); echo date(" Y")?> by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>
				</p>
			</div>
		</div>

		<?php
		// check all tables of BwPostman
		// Let Ajax client redirect
		if ($update)
		{
			$modal = $this->getModal();
			echo $modal;
		}
	}

	/**
	 * Get the HTML-String for popup modal
	 *
	 * @return	string
	 *
	 * @since	2.2.0 (since 3.1.4 here)
	 */
	private function getModal(): string
	{
		$url = Uri::root() . '/administrator/index.php?option=com_bwpostman&view=maintenance&tmpl=component&layout=updateCheckSave';

		$html    = '
		<div id="bwp_Modal" class="bwp_modal">
			<div id="bwp_modal-content">
				<div id="bwp_modal-header"><span class="bwp_close" style="display:none;">&times;</span></div>
				<div id="bwp_wrapper"></div>
			</div>
		</div>
	';
		$css     = "#bwpostman .bwp_modal{display:none;position:fixed;z-index:99999;padding-top:10px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,0.4)}#bwpostman #bwp_modal-content{position:relative;background-color:#fefefe;margin:auto;border:1px solid #888;border-radius:6px;box-shadow:0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);height:100%;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;pointer-events:auto;outline:0;padding:15px}#bwpostman #bwp_modal-header{height:35px}#bwpostman #bwp_wrapper{position:relative;-ms-flex:1 1 auto;flex:1 1 auto}#bwpostman .bwp_close{color:#aaa;float:right;font-size:28px;font-weight:700;line-height:28px;-webkit-appearance:non}#bwpostman .bwp_close:hover,#bwpostman .bwp_close:focus{color:#000;text-decoration:none;cursor:pointer}";
		$percent = 0.10;

		$js = "
			var css = '$css',
				head = document.head || document.getElementsByTagName('head')[0],
				style = document.createElement('style');

			style.type = 'text/css';
			if (style.styleSheet){
				// This is required for IE8 and below.
				style.styleSheet.cssText = css;
			} else {
				style.appendChild(document.createTextNode(css));
			}
			head.appendChild(style);

			function setModal() {
				// Set the modal height and width 90%
				if (typeof window.innerWidth != 'undefined')
				{
					viewportwidth = window.innerWidth,
						viewportheight = window.innerHeight
				}
				else if (typeof document.documentElement != 'undefined'
					&& typeof document.documentElement.clientWidth !=
					'undefined' && document.documentElement.clientWidth != 0)
				{
					viewportwidth = document.documentElement.clientWidth,
						viewportheight = document.documentElement.clientHeight
				}
				else
				{
					viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
						viewportheight = document.getElementsByTagName('body')[0].clientHeight
				}
				var modalcontent = document.getElementById('bwp_modal-content');
				modalcontent.style.height = viewportheight-(viewportheight*$percent)+'px';
				";

		$js .= "
								modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';
						";

		$js .= "

				// Get the modal
				var modal = document.getElementById('bwp_Modal');

				// Get the Iframe-Wrapper and set Iframe
				var wrapper = document.getElementById('bwp_wrapper');
				var html = '<iframe id=\"iFrame\" name=\"iFrame\" src=\"$url\" style=\"width:100%; height:100%;\"></iframe>';

				// Open the modal
					wrapper.innerHTML = html;
                    wrapper.style.opacity = '1';
					modal.style.display = 'block';

			}
			setModal();

		";

		return <<<EOS
		<div id="bwpostman">$html</div><script>$js</script>
EOS;
	}

}
