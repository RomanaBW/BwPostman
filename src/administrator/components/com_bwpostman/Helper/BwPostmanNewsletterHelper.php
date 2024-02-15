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
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * Class BwPostmanNewsletterHelper
 *
 * @since
 */
abstract class BwPostmanNewsletterHelper {
	/**
	 * @param array $usergroup
	 *
	 * @return string|null
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0 (since 3.0.0 here, before at BE newsletter model)
	 */
	public static function countUsersOfNewsletter(array $usergroup)
	{
		$count_users = 0;
		$usergroup   = ArrayHelper::toInteger($usergroup);

		$db       = Factory::getContainer()->get(DatabaseInterface::class);
		$sub_query = $db->getQuery(true);

		$sub_query->select($db->quoteName('g') . '.' . $db->quoteName('user_id'));
		$sub_query->from($db->quoteName('#__user_usergroup_map') . ' AS ' . $db->quoteName('g'));
		$sub_query->where($db->quoteName('g') . '.' . $db->quoteName('group_id') . ' IN (' . implode(',',
				$usergroup) . ')');

		$query     = $db->getQuery(true);
		$query->select('COUNT(' . $db->quoteName('u') . '.' . $db->quoteName('id') . ')');
		$query->from($db->quoteName('#__users') . ' AS ' . $db->quoteName('u'));
		$query->where($db->quoteName('u') . '.' . $db->quoteName('block') . ' = ' .  0);
		$query->where($db->quoteName('u') . '.' . $db->quoteName('activation') . ' = ' . $db->quote(''));
		$query->where($db->quoteName('u') . '.' . $db->quoteName('id') . ' IN (' . $sub_query . ')');

		try
		{
			$db->setQuery($query);

			$count_users = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NewsletterHelper BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $count_users;
	}

	/**
	 * @param array   $associatedMailinglists
	 * @param string  $status
	 * @param boolean $allSubscribers
	 *
	 * @return integer
	 *
	 * @throws Exception
	 * @throws RuntimeException
	 *
	 * @since 2.3.0 (since 3.0.0 here, before at BE newsletter model)
	 */
	public static function countSubscribersOfNewsletter(array $associatedMailinglists, string $status, bool $allSubscribers): int
	{
		$count_subscribers      = 0;
		$associatedMailinglists = ArrayHelper::toInteger($associatedMailinglists);

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName('#__bwpostman_subscribers'));

		if (!$allSubscribers)
		{
			$subQuery1 = $db->getQuery(true);
			$subQuery1->select('DISTINCT' . $db->quoteName('subscriber_id'));
			$subQuery1->from($db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$subQuery1->where($db->quoteName('mailinglist_id') . ' IN (' . implode(',', $associatedMailinglists) . ')');
			$query->where($db->quoteName('id') . ' IN (' . $subQuery1 . ')');
		}

		$query->where($db->quoteName('status') . ' IN (' . $status . ')');
		$query->where($db->quoteName('archive_flag') . ' = ' .  0);

		try
		{
			$db->setQuery($query);

			$count_subscribers = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NewsletterHelper BE');

			Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $count_subscribers;
	}

	/**
	 * Method to convert attachment JSON string (or old string version) to an array
	 *
	 * @param string $attachmentString
	 *
	 * @return array   Array of attachments
	 *
	 * @since 4.0.0
	 */
	public static function decodeAttachments(string $attachmentString): array
	{
		$attachments = array();

		if (!empty($attachmentString))
		{
//			If attachment is simple string (no JSON), convert it this way
			if (strpos($attachmentString, '{') === false)
			{
				$tmpAttach = explode(';', $attachmentString);

//				If array has only one tier, insert first tier and move existing to second tier
				if (!is_array($tmpAttach[0]))
				{
					$attachments = self::makeTwoTierAttachment($tmpAttach);
				}
				else
				{
					$attachments = $tmpAttach;
				}
			}
//			If attachment is JSON, convert it that way
			else
			{
				$attachments = json_decode($attachmentString, true);
			}
		}

		return $attachments;
	}

	/**
	 * Method to convert one tier attachment array to two tier attachment array
	 *
	 * @param array $oneTierAttachments
	 *
	 * @return array   Array of attachments with two tiers
	 *
	 * @since 4.0.0
	 */
	public static function makeTwoTierAttachment(array $oneTierAttachments): array
	{
		$twoTierAttachments = array();

		for ($i = 0; $i < count($oneTierAttachments); $i++)
		{
			$keyField                      = '__field' . ($i + 31);
			$twoTierAttachments[$keyField] = array('single_attachment' => $oneTierAttachments[$i]);
		}

		return $twoTierAttachments;
	}
}
