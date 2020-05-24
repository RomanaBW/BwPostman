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


	/**
	 * Method to get the data of a single Mailinglist for raw view
	 *
	 * @access	public
	 *
	 * @param 	int $ml_id      Mailinglist ID
	 *
	 * @return 	object Mailinglist
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public static function getSingleMailinglist($ml_id = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.*');
		$query->from($db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $db->quoteName('a'));
		$query->where($db->quoteName('a') . '.' . $db->quoteName('id') . ' = ' . (int) $ml_id);
		// Join over the asset groups.
		$query->select($db->quoteName('ag') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('access_level'));
		$query->join(
			'LEFT',
			$db->quoteName('#__viewlevels') . ' AS ' . $db->quoteName('ag') . ' ON ' .
			$db->quoteName('ag') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('access')
		);

		$db->setQuery($query);
		try
		{
			$mailinglist = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglist;
	}

	/**
	 * Method to get the mailinglist ids for a single campaign
	 *
	 * @param int $cam_id Campaign ID
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public static function getCampaignMailinglists($cam_id = null)
	{
		$mailinglists = array();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('mailinglist_id'));
		$query->from($db->quoteName('#__bwpostman_campaigns_mailinglists'));
		$query->where($db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
		$db->setQuery($query);
		try
		{
			$mailinglists = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get the mailinglist ids for a single campaign
	 *
	 * @param array $mailinglists      list of mailinglists
	 *
	 * @return array
	 *
	 * @since 2.4.0
	 */
	public static function extractAssociatedUsergroups($mailinglists = array())
	{
		$usergroups	= array();

		foreach ($mailinglists as $mailinglist)
		{
			if ((int) $mailinglist < 0)
			{
				$usergroups[]	= -(int) $mailinglist;
			}
		}

		return $usergroups;
	}

	/**
	 * Method to merge the parts of the mailinglists (available, unavailable, internal, usergroups)
	 *
	 * @param array $data      list of mailinglists
	 *
	 * @since 2.4.0
	 */
	public static function mergeMailinglists(&$data = array())
	{
		if (isset($data['ml_available']))
		{
			foreach ($data['ml_available'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))
		{
			foreach ($data['usergroups'] as $key => $value)
			{
				$data['mailinglists'][] = '-' . $value;
			}
		}
	}

	/**
	 * Method to merge the parts of the mailinglists (available, unavailable, internal)
	 *
	 * @param array $data      list of mailinglists
	 *
	 * @return array
	 *
	 * @since 2.4.0
	 */
	public static function mergeMailinglistsOnly($data = array())
	{
		$mailinglists = array();

		if (isset($data['ml_available']))
		{
			foreach ($data['ml_available'] as $key => $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $key => $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $key => $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		return $mailinglists;
	}

	/**
	 * Method to get the subscribers of a specific mailinglist
	 *
	 * @param 	integer $id id of mailinglist
	 *
	 * @return 	array       $subscribers of this mailinglist
	 *
	 * @throws Exception
	 *
	 * @since       2.2.0
	 */
	public static function getSubscribersOfMailinglist($id)
	{
		$db	= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('subscriber_id'));
		$query->from($db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($db->quoteName('mailinglist_id') . ' = ' . $db->Quote($id));

		$db->setQuery($query);

		$subscribersOfMailinglist = $db->loadColumn();

		return $subscribersOfMailinglist;
	}

}
