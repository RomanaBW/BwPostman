<?php
/**
 * BwPostman Plugin Footer Used Mailinglists
 *
 * BwPostman Plugin Footer Used Mailinglists installer.
 *
 * @version %%version_number%%
 * @package BwPostman Plugin Footer Used Mailinglists
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Database\DatabaseInterface;

/**
 * Script file of BwPostman module
 *
 * @since       2.3.0
 */
class PlgBwPostmanFooterUsedMailinglistsInstallerScript
{
	/**
	 * @var string $minJoomlaRelease
	 *
	 * @since       2.3.0
	 */
	var $minJoomlaRelease;

	/**
	 * @var string $minPhpRelease
	 *
	 * @since       2.3.0
	 */
	var $minPhpRelease = '7.2.5';

	/**
	 * @var string minimum version of BwPostman
	 *
	 * @since       2.3.0
	 */
	var $bwpmMinRelease = '2.3.0';

	/**
	 * @var string release
	 *
	 * @since       2.3.0
	 */
	var $release = null;

	/**
	 * Method to install the extension
	 *
	 * @param object $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since       2.3.0
	 */
	public function install(object $parent)
	{
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	public function uninstall()
	{
//		Factory::getApplication()->enqueueMessage(Text::_('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_UNINSTALL_THANKYOU'), 'message');
	}

	/**
	 * Method to update the extension
	 *
	 * @param object $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since       2.3.0
	 */
	public function update(object $parent)
	{
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param string            $type   is the type of change (install, update or discover_install)
	 * @param  InstallerAdapter $parent is the class calling this method
	 *
	 * @return     bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	function preflight(string $type, InstallerAdapter $parent): bool
	{
		$app 		= Factory::getApplication ();

		// Get component manifest file version
		$manifest = $parent->getManifest();
		$this->release = $manifest->version;

		// Manifest file minimum Joomla version
		$this->minJoomlaRelease = $manifest->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare(JVERSION, $this->minJoomlaRelease, 'lt'))
		{
			$app->enqueueMessage(Text::sprintf('PLG_BWPOSTMAN_INSTALL_ERROR_JVERSION', $this->minJoomlaRelease), 'error');
			return false;
		}

		if(version_compare(phpversion(), $this->minPhpRelease, 'lt'))
		{
			$app->enqueueMessage(Text::_('PLG_BWPOSTMAN_INSTALL_ERROR_PHP7'), 'error');
			return false;
		}

		// Abort if BwPostman is not installed or not at least version 2.3.0
		if ($type == 'install')
		{
			JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

			$bwpmVersion = $this->getManifestVar('version', 'com_bwpostman');

			if ($bwpmVersion === false)
			{
				$app->enqueueMessage(Text::_('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_COMPONENT_NOT_INSTALLED'), 'error');
				return false;
			}

			if (version_compare($bwpmVersion, $this->bwpmMinRelease, 'lt')) {
				$app->enqueueMessage(Text::sprintf('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_COMPONENT_MIN_VERSION', $this->bwpmMinRelease), 'error');
				return false;
			}
		}

		// Abort if the extension being installed is older than the currently installed version
		if ($type == 'update')
		{
			JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

			$oldRelease = $this->getManifestVar('version', 'footerusedmailinglists');

			if (version_compare( $this->release, $oldRelease, 'lt')) {
				$app->enqueueMessage(Text::sprintf('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_OLD_VERSION', $oldRelease, $this->release), 'error');
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	public function postflight(string $type)
	{
		// We only need to perform this if the extension is being installed, not updated
		if ($type == 'install')
		{
			JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

			$db = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true);

			$fields = array(
				$db->quoteName('enabled') . ' = ' . 1,
				$db->quoteName('ordering') . ' = ' . 9998
			);

			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('footerusedmailinglists'),
				$db->quoteName('folder') . ' = ' . $db->quote('bwpostman'),
				$db->quoteName('type') . ' = ' . $db->quote('plugin')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Method to get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param string $name
	 * @param string $extension
	 *
	 * @return  bool|string
	 *
	 * @since       2.3.0
	 */
	private function getManifestVar(string $name, string $extension)
	{
		$db		= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote($extension));

		try
		{
			$db->setQuery($query);

			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		$manifest = json_decode($result, true);

		return $manifest[$name];
	}
}
