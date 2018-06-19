<?php
/**
 * BwPostman Personalize Plugin
 *
 * BwPostman Personalize Plugin installer.
 *
 * @version %%version_number%%
 * @package BwPostman Personalize Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

/**
 * Script file of BwPostman module
 *
 * @since       2.0.0
 */
class PlgBwPostmanPersonalizeInstallerScript
{
	/**
	 * Method to install the extension
	 *
	 * @param object  $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since       2.0.0
	 */
	public function install($parent)
	{
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function uninstall()
	{
		JFactory::getApplication()->enqueueMessage(JText::_('PLG_BWPOSTMAN_PLUGIN_PERSONALIZE_UNINSTALL_THANKYOU'), 'message');
	}

	/**
	 * Method to update the extension
	 *
	 * @param object  $parent is the class calling this method
	 *
	 * @return void
	 *
	 * @since       2.0.0
	 */
	public function update($parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string  $type       is the type of change (install, update or discover_install)
	 *
	 * @return void
	 *
	 * @since       2.0.0
	 */
	public function postflight($type)
	{
		// We only need to perform this if the extension is being installed, not update
		if ($type == 'install')
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$fields = array(
				$db->quoteName('enabled') . ' = ' . (int) 1,
				$db->quoteName('ordering') . ' = ' . (int) 9999
			);

			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('personalize'),
				$db->quoteName('folder') . ' = ' . $db->quote('bwpostman'),
				$db->quoteName('type') . ' = ' . $db->quote('plugin')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

			$db->setQuery($query);
			$db->execute();
		}
	}
}
