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

/**
 * Class BwPostmanHTMLHelper
 *
 * @since
 */
abstract class BwPostmanNewsletterHelper {
	/**
	 * Method to get the export fields list
	 *
	 * @param int $nlId
	 *
	 * @return 	integer
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	static public function getCampaignId($nlId)
	{
		$campaignId = -1;

		$db	= JFactory::getDbo();
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
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return (int)$campaignId;
	}

}
