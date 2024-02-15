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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

defined ('_JEXEC') or die ();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * Class BwPostmanMailinglistHelper
 *
 * @since 2.2.0
 */
class BwPostmanMailinglistHelper {
	/**
	 * Method to get the mailinglist ids for a single campaign
	 *
	 * @param array $mailinglists list of mailinglists
	 *
	 * @return array
	 *
	 * @since 3.0.0
	 */
	public static function extractAssociatedUsergroups(array $mailinglists = array()): array
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
	 * @param array $data list of mailinglists
	 *
	 * @since 3.0.0
	 */
	public static function mergeMailinglists(array &$data = array())
	{
		if (isset($data['ml_available']))
		{
			foreach ($data['ml_available'] as $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (!empty($data['usergroups']))
		{
			foreach ($data['usergroups'] as $value)
			{
				$data['mailinglists'][] = '-' . $value;
			}
		}
	}

	/**
	 * Method to merge the parts of the mailinglists (available, unavailable, internal) to one single array
	 *
	 * @param array $data list of mailinglists
	 *
	 * @return array
	 *
	 * @since 3.0.0
	 */
	public static function mergeMailinglistsOnly(array $data = array()): array
	{
		$mailinglists = array();

		if (isset($data['ml_available']))
		{
			foreach ($data['ml_available'] as $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $value)
			{
				$mailinglists[] 	= $value;
			}
		}

		return ArrayHelper::toInteger($mailinglists);
	}

	/**
	 * Method to get the data of a single Mailinglist for raw view
	 *
	 * @param int|null $ml_id Mailinglist ID
	 *
	 * @return 	object Mailinglist
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public static function getSingleMailinglist(int $ml_id = null): ?object
	{
		$mailinglist = null;

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
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

		try
		{
			$db->setQuery($query);

			$mailinglist = $db->loadObject();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistHelper BE');

			Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $mailinglist;
	}

	/**
	 * Method to get the options for the form fields comcam and comcam_noarc
	 *
	 * @param boolean $archiveMatters
	 *
	 * @return 	array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public static function getMailinglistsFieldlistOptions(bool $archiveMatters = false): ?array
	{
		$options   = null;
		$db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query     = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.' . $db->quoteName('id')  . ' AS value');
		$query->select($db->quoteName('a') . '.' . $db->quoteName('title')  . ' AS text');
		$query->select($db->quoteName('a') . '.' . $db->quoteName('description'));
		$query->select($db->quoteName('a') . '.' . $db->quoteName('access'));
		$query->select($db->quoteName('a') . '.' . $db->quoteName('published'));
		$query->select($db->quoteName('a') . '.' . $db->quoteName('archive_flag')  . ' AS archived');
		$query->from($db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $db->quoteName('a'));

		if ($archiveMatters)
		{
			$query->where($db->quoteName('a') . '.' . $db->quoteName('archive_flag') . ' = ' . 0);
		}

		// Join over the asset groups.
		$query->select($db->quoteName('ag') . '.' . $db->quoteName('title')  . ' AS access_level');
		$query->join(
			'LEFT',
			$db->quoteName('#__viewlevels') .
			' AS ' . $db->quoteName('ag') .
			' ON ' . $db->quoteName('ag') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('access')
		);
		$query->order($db->quoteName('text') . 'ASC');

		try
		{
			$db->setQuery($query);

			$options = $db->loadObjectList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistHelper BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $options;
	}
}
