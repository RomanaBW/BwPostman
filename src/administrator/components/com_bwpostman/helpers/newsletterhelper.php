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

defined ('_JEXEC') or die ();

use Joomla\CMS\Factory;

/**
 * Class BwPostmanNewsletterHelper
 *
 * @since
 */
abstract class BwPostmanNewsletterHelper {
	/**
	 * Method to get the campaign id of a specific newsletter
	 *
	 * @param int $nlId
	 *
	 * @return 	integer
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	static public function getCampaignId($nlId)
	{
		$campaignId = -1;

		$db	= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('campaign_id'));
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($nlId));

		$db->setQuery($query);

		try
		{
			$campaignId = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return (int)$campaignId;
	}

	/**
	 * Method to get the number of newsletters depending on provided sending and archive state
	 *
	 * @param boolean $sent
	 * @param boolean $archived
	 *
	 * @return 	integer|boolean number of newsletters or false
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	static public function getNbrOfNewsletters($sent, $archived)
	{
		$archiveFlag = 0;
		$mailingDateOperator = "=";

		if ($sent)
		{
			$mailingDateOperator = "!=";
		}

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_newsletters'));

		if (!$archived)
		{
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote('0000-00-00 00:00:00'));
		}

		$query->where($db->quoteName('archive_flag') . ' = ' . $archiveFlag);

		$db->setQuery($query);

		try
		{
			return $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return false;
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
	 * @since 2.4.0 here
	 */
	static public function getSelectedNewslettersOfCampaign($camId, $sent, $all)
	{
		$newsletters = array();
		$archiveFlag = 0;
		$mailingDateOperator = "=";

		if ($sent)
		{
			$mailingDateOperator = "!=";
		}

		if ($all)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.*');
		$query->select($db->quoteName('v') . '.' . $db->quoteName('name') . ' AS author');
		$query->from($db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin(
			$db->quoteName('#__users') . ' AS ' . $db->quoteName('v')
			. ' ON ' . $db->quoteName('v') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('created_by')
		);
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $camId));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int)0);

		if (!$archiveFlag)
		{
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote('0000-00-00 00:00:00'));
		}

		$db->setQuery($query);

		try
		{
			$newsletters = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $newsletters;
	}
}
