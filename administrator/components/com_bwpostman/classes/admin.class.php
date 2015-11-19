<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman admin class for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

/**
 * BwPostman Footer
 *
 * @package BwPostman-Admin
 */
class BwPostmanAdmin {

	/**
	 * Method to write the BwPostman footer
	 */
	static public function footer()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->SetQuery($query);
		
		$manifest = json_decode($db->loadResult(), true);

		echo 'BwPostman version ' . $manifest['version'] . ' by <a href="http://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>';
	}
}
