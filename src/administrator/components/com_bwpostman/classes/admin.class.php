<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman admin class for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * BwPostman Footer
 *
 * @package BwPostman-Admin
 *
 * @since   0.9.1
 */
class BwPostmanAdmin {

	/**
	 * Method to write the BwPostman footer
	 *
	 * @return string
	 *
	 * @since   0.9.1
	 */
	static public function footer()
	{
		$db		    = JFactory::getDbo();
		$query	    = $db->getQuery(true);
		$manifest   = array();

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->setQuery($query);

		try
		{
			$manifest = json_decode($db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return 'BwPostman version ' . $manifest['version'] . ' by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>';
	}
}
