<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter helper class for backend.
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
use RuntimeException;

/**
 * Class BwPostmanCampaignHelper
 *
 * @since 3.0.0
 */
abstract class BwPostmanCampaignHelper
{
	/**
	 * Method to get the data of a single campaign for raw view
	 *
	 * @access    public
	 *
	 * @param int|null $cam_id Campaign ID
	 *
	 * @return    mixed|null Campaign
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public static function getSingleCampaign(int $cam_id = null): ?object
	{
		$campaign = null;

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
		$query->where($db->quoteName('id') . ' = ' . (int) $cam_id);

		try
		{
			$db->setQuery($query);

			$campaign = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get all assigned newsletters
		// --> we offer to unarchive not only the campaign but also the assigned newsletters,
		// that's why we have to show also the archived newsletters
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('subject'));
		$query->select($db->quoteName('campaign_id'));
		$query->select($db->quoteName('archive_flag'));
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('campaign_id') . ' = ' . (int) $cam_id);

		try
		{
			$db->setQuery($query);

			$campaign->newsletters = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $campaign;
	}

	/**
	 * Method to get the newsletters of a specific campaign depending on provided campaign id, sending and archive state
	 *
	 * @param integer $camId
	 * @param boolean $sent
	 * @param boolean $all
	 *
	 * @return 	array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public static function getSelectedNewslettersOfCampaign(int $camId, bool $sent, bool $all): array
	{
		$newsletters = array();
		$archiveFlag = 0;
		$mailingDateOperator = "=";
		$nullDateOperator    = ' IS NULL';

		if ($sent)
		{
			$mailingDateOperator = "!=";
			$nullDateOperator    = 'IS NOT NULL';
		}

		if ($all)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.*');
		$query->select($db->quoteName('v') . '.' . $db->quoteName('name') . ' AS author');
		$query->from($db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin(
			$db->quoteName('#__users') . ' AS ' . $db->quoteName('v')
			. ' ON ' . $db->quoteName('v') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('created_by')
		);
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote($camId));
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		if (!$archiveFlag)
		{
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote($db->getNullDate())
				. ' OR ' . $db->quoteName('mailing_date') . $nullDateOperator);
		}

		try
		{
			$db->setQuery($query);

			$newsletters = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $newsletters;
	}

	/**
	 * Method to get the options for the form fields comcam and comcam_noarc
	 *
	 * @param boolean $hasMailingdate
	 * @param boolean $archiveMatters
	 *
	 * @return 	array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public static function getCampaignsFieldlistOptions(bool $hasMailingdate = false, bool $archiveMatters = false): array
	{
		$options   = array();
		$db        = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate  = $db->getNullDate();
		$query     = $db->getQuery(true);
		$sub_query = $db->getQuery(true);

		// Build sub query which counts the newsletters of each campaign and query
		$sub_query->select('COUNT(' . $db->quoteName('b') . '.' . $db->quoteName('id') . ') AS ' . $db->quoteName('newsletters'));
		$sub_query->from($db->quoteName('#__bwpostman_newsletters') . 'AS ' . $db->quoteName('b'));

		if ($hasMailingdate)
		{
			$sub_query->where($db->quoteName('b') . '.' . $db->quoteName('mailing_date') . ' != "' . $nullDate . '"'
			. ' AND ' . $db->quoteName('b') . '.' . $db->quoteName('mailing_date') . ' IS NOT NULL');
		}

		if ($archiveMatters)
		{
			$sub_query->where($db->quoteName('b') . '.' . $db->quoteName('archive_flag') . ' = ' . 0);
		}
		$sub_query->where($db->quoteName('b') . '.' . $db->quoteName('campaign_id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('id'));

		$query->select($db->quoteName('a') . '.' . $db->quoteName('id')  . ' AS value');
		$query->select($db->quoteName('a') . '.' . $db->quoteName('title')  . ' AS text');
		$query->select($db->quoteName('a') . '.' . $db->quoteName('description'));
		$query->select($db->quoteName('a') . '.' . $db->quoteName('archive_flag')  . ' AS archived');
		$query->select('(' . $sub_query . ') AS ' . $db->quoteName('newsletters'));
		$query->from($db->quoteName('#__bwpostman_campaigns') . ' AS ' . $db->quoteName('a'));

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

			$options = (array)$db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $options;
	}
}
