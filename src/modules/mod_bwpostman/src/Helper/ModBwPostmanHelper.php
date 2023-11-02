<?php
/**
 * BwPostman Module
 *
 * BwPostman helper class for module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
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

namespace BoldtWebservice\Module\BwPostman\Site\Helper;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use RuntimeException;
use stdClass;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

/**
 * Class ModBwPostmanHelper
 *
 * @since       0.9.1
 */
class ModBwPostmanHelper
{
	/**
	 * Method to get the subscriber ID of the current user
	 *
	 * @return    int     $subscriberid   id of the subscriber
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function getSubscriberID(): int
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		if ($user->get('guest'))
		{ // User is guest
			$session              = $app->getSession();
			$session_subscriberid = $session->get('session_subscriberid');

			if(isset($session_subscriberid) && is_array($session_subscriberid))
			{ // A session_subscriber id exists
				$subscriberid = (int)$session_subscriberid['id'];
			}
			else
			{ // No session_subscriber id exists
				$subscriberid = 0;
			}
		}
		else
		{ // User is logged in
			$subscriberid = self::getSubscriberIdFromUserID((int)$user->get('id'));
		}

		return $subscriberid;
	}

	/**
	 * Method to get all mailing lists which
	 * - the user is authorized to see
	 * - are not archived
	 * - are published
	 *
	 * @param array $accessTypes user type to get mailing lists for
	 * @param array $mod_mls     mailing lists to return, if set
	 *
	 * @return    array   $mailinglists   array of mailing lists objects
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function getMailinglists(array $accessTypes, array $mod_mls): array
	{
		$mailinglists = array();

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));

		// if mailinglists are selected at the module parameters use these
		if (isset($mod_mls) && count($mod_mls) && $mod_mls[0] !== "")
		{
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $mod_mls) . ')');
		}
		else
		{
			// else restrict by access level, state and archive state
			$query->where($db->quoteName('access') . ' IN (' . implode(',', $accessTypes) . ')');
			$query->where($db->quoteName('published') . ' = ' . 1);
			$query->where($db->quoteName('archive_flag') . ' = ' . 0);
		}

		$query->order($db->quoteName('title') . 'ASC');

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadObjectList();

			if ($mailinglists === null)
			{
				$mailinglists = array();
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to check if a user has a newsletter account
	 * --> gives back the id from the subscribers-table
	 *
	 * @param int $userid Joomla! user id
	 *
	 * @return    int     $subscriberid   id of subscriber
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function getSubscriberIdFromUserID(int $userid): int
	{
		$subscriberid = 0;

		$db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('user_id') . ' = ' . $userid);
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);

			$subscriberid = $db->loadResult();

			if ($subscriberid === null)
			{
				$subscriberid = 0;
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $subscriberid;
	}

	/**
	 * Method to get the data of a user who has no newsletter account
	 *
	 * @access           public
	 *
	 * @param int $userid Joomla! user id
	 *
	 * @return    object  $user       user data
	 *
	 * @throws Exception
	 *
	 * @since            0.9.1
	 */
	public static function getUserData(int $userid): ?object
	{
		$id	   = 0;
		$user  = new stdClass;
		$db	   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('name'));
		$query->select($db->quoteName('email'));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('id') . ' = ' . $userid);

		try
		{
			$db->setQuery($query);

			$user = $db->loadObject();

			if ($user === null)
			{
				$user = new stdClass;
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$user->user_id = $id;

		return $user;
	}

	/**
	 * Method to get the email format select list
	 *
	 * @param Registry $paramsComponent
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	public static function getMailformatSelectList(Registry $paramsComponent): string
	{
		$mailformat_selected = $paramsComponent->get('default_emailformat', '1');

		$emailformat = '<div id="edit_mailformat_m" class="radio btn-group">';
		$emailformat .= '<input type="radio" name="a_emailformat" id="formatTextMod" class="rounded-left" value="0"';
		if (!$mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatTextMod" class="rounded-left"><span>' . Text::_('COM_BWPOSTMAN_TEXT') . '</span></label>';
		$emailformat .= '<input type="radio" name="a_emailformat" id="formatHtmlMod" value="1"';
		if ($mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatHtmlMod" class="rounded-right"><span>' . Text::_('COM_BWPOSTMAN_HTML') . '</span></label>';
		$emailformat .= '</div>';

		return $emailformat;
	}
}
