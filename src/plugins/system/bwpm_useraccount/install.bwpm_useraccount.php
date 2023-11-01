<?php
/**
 * BwPostman UserAccount Plugin
 *
 * Plugin to automated alignment of Joomla users with BwPostman subscribers
 *
 * BwPostman UserAccount Plugin installation file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman UserAccount Plugin
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
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

/**
 * Installation script for the plugin
 *
 * @since   4.1.0
 */
class PlgSystemBwPm_UserAccountInstallerScript
{
	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	protected $min_bwpostman_version    = '4.0';

	/**
	 * Called before any type of action
	 *
	 * @param string $type Which action is happening (install|uninstall|discover_install|update)
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       4.1.0
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
					Text::sprintf('PLG_BWPOSTMAN_PLUGIN_USERACCOUNT_BWPOSTMAN_NEEDED', $this->min_bwpostman_version),
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
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since     4.1.0
	 */
	public function postflight(string $type)
	{
		// We only need to perform this if the extension is being installed, not update
		if ($type == 'install')
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);

			$fields = array(
				$db->quoteName('enabled') . ' = ' . 1,
				$db->quoteName('ordering') . ' = ' . 9998
			);

			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('bwpm_useraccount'),
				$db->quoteName('folder') . ' = ' . $db->quote('system'),
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
	 * Method to get component version
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
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
}
