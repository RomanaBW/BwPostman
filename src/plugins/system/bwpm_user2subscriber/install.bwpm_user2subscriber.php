<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin installation script.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

/**
 * Installation script for the plugin
 *
 * @since   2.0.0
 */
class PlgSystemBwPm_User2SubscriberInstallerScript
{
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected $min_bwpostman_version    = '1.3.2';

	/**
	 * Called before any type of action
	 *
	 * @param string $type Which action is happening (install|uninstall|discover_install|update)
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function preflight(string $type): bool
	{
		if ($type == 'install')
		{
			// check prerequisites
			$BwPostmanComponentVersion = $this->getComponentVersion();

			if (version_compare($BwPostmanComponentVersion, $this->min_bwpostman_version, 'lt'))
			{
				Factory::getApplication()->enqueueMessage(
					Text::sprintf('PLG_BWPOSTMAN_PLUGIN_USER2SUBSCRIBER_COMPONENT_BWPOSTMAN_NEEDED', $this->min_bwpostman_version),
					'error'
				);
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string $type   is the type of change (install, update or discover_install)
	 * @param object $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since     0.9.8
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
	 * Method to get component version
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function getComponentVersion(): string
	{
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

		$version    = '0.0.0';
		$_db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$result = $_db->loadResult();

			if ($result === null)
			{
				$result = '';
			}

			$manifest   = json_decode($result, true);
			$version    = $manifest['version'];
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $version;
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
		$plgFilesArray = array(
			'helpers/bwpm_user2subscriberhelper.php',
		);

		foreach ($plgFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/plugins/system/bwpm_user2subscriber/' . $file))
			{
				File::delete(JPATH_ROOT . '/plugins/system/bwpm_user2subscriber/' . $file);
			}
		}

		$plgFoldersArray = array(
		);

		foreach ($plgFoldersArray as $folder)
		{
			if (Folder::exists(JPATH_ROOT . '/plugins/system/bwpm_user2subscriber/' . $folder))
			{
				Folder::delete(JPATH_ROOT . '/plugins/system/bwpm_user2subscriber/' . $folder);
			}
		}

	}
}
