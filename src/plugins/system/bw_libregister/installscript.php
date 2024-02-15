<?php
/**
 * Boldt Webservice LibRegister Plugin
 *
 * Boldt Webservice LibRegister Plugin installer.
 *
 * @version %%version_number%%
 * @package Boldt Webservice LibRegister Plugin
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

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

/**
 * Script file of BwPostman module
 *
 * @since       2.3.0
 */
class PlgSystemBW_LibregisterInstallerScript
{
	/**
	 * Method to install the extension
	 *
	 *
	 * @return void
	 *
	 * @since       2.3.0
	 */
	public function install()
	{
		sleep(5);
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
		$oldLibPath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/toolbar/';
		if (Folder::exists($oldLibPath))
		{
			Folder::delete($oldLibPath);
		}

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
				$db->quoteName('element') . ' = ' . $db->quote('bw_libregister'),
				$db->quoteName('folder') . ' = ' . $db->quote('system'),
				$db->quoteName('type') . ' = ' . $db->quote('plugin')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'Plg LibRegister Install FE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}
		}
	}
}
