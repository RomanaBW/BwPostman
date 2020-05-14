<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglist helper class for backend.
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

defined ('_JEXEC') or die ();

use Joomla\CMS\Factory;

/**
 * Class BwPostmanMailinglistHelper
 *
 * @since
 */
abstract class BwPostmanMailinglistHelper {
	/**
	 * Method to get the export fields list
	 *
	 * @param array     $mailinglists
	 * @param string    $condition
	 * @param integer   $archived
	 * @param boolean   $restricted
	 *
	 * @return array
	 *
	 * @throws Exception
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	static public function getMailinglistsByRestriction($mailinglists, $condition = 'available', $archived = 0, $restricted = true)
	{
		$mls   = null;
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) $archived);

		if ((int)$archived === 0)
		{
			switch ($condition)
			{
				case 'available':
					$query->where($db->quoteName('published') . ' = ' . (int) 1);
					$query->where($db->quoteName('access') . ' = ' . (int) 1);
					break;
				case 'unavailable':
					$query->where($db->quoteName('published') . ' = ' . (int) 1);
					$query->where($db->quoteName('access') . ' > ' . (int) 1);
					break;
				case 'internal':
					$query->where($db->quoteName('published') . ' = ' . (int) 0);
					break;
			}
		}

		$db->setQuery($query);

		try
		{
			$mls = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($restricted === true)
		{
			$resultingMls = array_intersect($mailinglists, $mls);
		}
		else
		{
			$resultingMls = $mls;
		}

		if (count($resultingMls) > 0)
		{
			$restrictedMls = $resultingMls;
		}
		else
		{
			$restrictedMls = array();
		}

		return $restrictedMls;
	}
}
