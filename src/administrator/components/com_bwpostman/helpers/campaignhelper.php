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
 * Class BwPostmanCampaignHelper
 *
 * @since 2.4.0
 */
abstract class BwPostmanCampaignHelper
{
	/**
	 * Method to get the data of a single campaign for raw view
	 *
	 * @access    public
	 *
	 * @param int $cam_id Campaign ID
	 *
	 * @return    object Campaign
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public static function getSingleCampaign($cam_id = null)
	{
		$db   = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
		$query->where($db->quoteName('id') . ' = ' . (int) $cam_id);
		$db->setQuery($query);

		$campaign = $db->loadObject();

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

		$db->setQuery($query);
		try
		{
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
	 * @since 2.4.0 here
	 */
	public static function getSelectedNewslettersOfCampaign($camId, $sent, $all)
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
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote($db->getNullDate()));
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
