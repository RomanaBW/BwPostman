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
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Class modBwPostmanHelper
 *
 * @since       0.9.1
 */
class ModBwPostmanHelper
{
	/**
	 * Method to get the subscriber ID of the user
	 *
	 * @access	public
	 *
	 * @return 	int     $subscriberid   id of the subscriber
	 *
	 * @since       0.9.1
	 */
	public static function getSubscriberID()
	{
		$user 	= JFactory::getUser();

		if ($user->get('guest'))
		{ // User is guest
			$session = JFactory::getSession();
			$session_subscriberid = $session->get('session_subscriberid');

			if(isset($session_subscriberid) && is_array($session_subscriberid))
			{ // A session_subscriber id exists
				$subscriberid = $session_subscriberid['id'];
			}
			else
			{ // No session_subscriber id exists
				$subscriberid = 0;
			}
		}
		else
		{ // User is logged in
			$subscriberid = self::getSubscriberIdFromUserID($user->get('id'));
		}

		return $subscriberid;
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @access	public
	 *
	 * @return 	int     $itemid     menu item ID
	 *
	 * @since       0.9.1
	 */
	public static function getItemid()
	{
		$_db = JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=edit'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		$_db->setQuery($query);
		$itemid = $_db->loadResult();

		if (empty($itemid))
		{
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$_db->setQuery($query);
			$itemid = $_db->loadResult();
		}

		return $itemid;
	}

	/**
	 * Method to get all mailing lists which the user is authorized to see
	 *
	 * @access 	public
	 *
	 * @param   string  $usertype   user type to get mailing lists for
	 * @param   array   $mod_mls    mailing lists to return, if set
	 *
	 * @return 	array   $mailinglists   array of mailing lists objects
	 *
	 * @since       0.9.1
	 */
	public static function getMailinglists($usertype, $mod_mls)
	{
		$_db = JFactory::getDbo();

		// if mailinglists are checked in the module parameters
		if (isset($mod_mls))
		{
			$query	= $_db->getQuery(true);

			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('id') . ' IN (' . implode(',', $mod_mls) . ')');
		}
		else
		{
			// no mailinglist is checked in the module parameters
			if (empty($usertype))
			{
				// A guest shall only see mailinglists which are public
				$query	= $_db->getQuery(true);

				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('access') . ' = ' . (int) 1);
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				$query->order($_db->quoteName('title') . 'ASC');
			}
			elseif ($usertype == 'Registered')
			{
				// A registered user shall only see mailinglists which are registered or public
				$query	= $_db->getQuery(true);

				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('access') . ' = ' . (int) 2);
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				$query->order($_db->quoteName('title') . 'ASC');
			}
			else
			{
				// A user with a higher status than registered shall see all mailinglists
				$query	= $_db->getQuery(true);

				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				$query->order($_db->quoteName('title') . 'ASC');
			}
		}

		$_db->setQuery($query);

		$mailinglists = $_db->loadObjectList();

		return $mailinglists;
	}

	/**
	 * Method to check if a user has a newsletter account
	 * --> gives back the id from the subscribers-table
	 *
	 * @access	public
	 *
	 * @param 	int     $userid         Joomla! user id
	 *
	 * @return 	int     $subscriberid   id of subscriber
	 *
	 * @since       0.9.1
	 */
	public static function getSubscriberIdFromUserID($userid)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $userid);
		$query->where($_db->quoteName('status') . ' = ' . (int) 9);

		$_db->setQuery($query);
		$subscriberid = $_db->loadResult();

		return $subscriberid;
	}

	/**
	 * Method to get the data of a user who has no newsletter account
	 *
	 * @access 	public
	 *
	 * @param	int     $userid     Joomla! user id
	 *
	 * @return 	object  $user       user data
	 *
	 * @since       0.9.1
	 */
	public static function getUserData($userid)
	{
		$_db	= JFactory::getDbo();
		$id		= 0;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('name'));
		$query->select($_db->quoteName('email'));
		$query->from($_db->quoteName('#__users'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $userid);

		$_db->setQuery($query);
		$user = $_db->loadObject();

		$user->user_id = $id;

		return $user;
	}

	/**
	 * Method to get the user type if a user is logged in
	 *
	 * @access	public
	 *
	 * @param	int     $userid     Joomla! user id
	 *
	 * @return string   $usertype   type of Joomla! user
	 *
	 * @since       0.9.1
	 */
	public static function getUsertype($userid)
	{
		$_db		= JFactory::getDbo();
		$usertype	= '';

		if ($userid)
		{
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('usertype'));
			$query->from($_db->quoteName('#__users'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $userid);

			$_db->setQuery($query);
			$usertype = $_db->loadResult();

			if (empty($usertype)) {
				$usertype = "Public";
			}
		}

		return $usertype;
	}
}
