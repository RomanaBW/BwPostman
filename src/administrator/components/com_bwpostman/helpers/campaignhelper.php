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
abstract class BwPostmanCampaignHelper {
	/**
	 * Method to get the number of campaigns depending on provided archive state
	 *
	 * @param boolean $archived
	 *
	 * @return 	integer|boolean number of campaigns or false
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	static public function getNbrOfCampaigns($archived)
	{
		$archiveFlag = 0;

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
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
}
