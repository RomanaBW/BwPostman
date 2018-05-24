<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * BwPostman helper class for plugin.
 *
 * @version 2.0.2 bwpmpu2s
 * @package BwPostman User2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) 2016-2018 Boldt Webservice <forum@boldt-webservice.de>
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
 * Class BwPostmanHelper
 *
 * @since 2.0.0
 */
abstract class BWPM_User2SubscriberHelper
{
	/**
	 * Method to check if user has a subscription
	 *
	 * @param   string  $user_mail
	 *
	 * @return  mixed   $subscriber_id|false   subscription data or false
	 *
	 * @since  2.0.0
	 */
	public static function hasSubscription($user_mail)
	{
		if ($user_mail == '')
		{
			return false;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		$_db->setQuery($query);

		$subscriber_id  = $_db->loadResult();

		return $subscriber_id;
	}

	/**
	 * Method to check if user has a subscription
	 *
	 * @param   string  $user_mail
	 *
	 * @return  bool    subscriber is to activate or not
	 *
	 * @since  2.0.0
	 */
	public static function isToActivate($user_mail)
	{
		if ($user_mail == '')
		{
			return false;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('status'));
		$query->select($_db->quoteName('activation'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		$_db->setQuery($query);

		$result  = $_db->loadAssoc();

		if (!$result['status'] && $result['activation'] != '')
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to update user ID in table subscribers
	 *
	 * @param   string  $user_mail
	 * @param   int     $user_id
	 *
	 * @return  bool                true if subscription present and update okay
	 *
	 * @since  2.0.0
	 */
	public static function updateUserIdAtSubscriber($user_mail, $user_id)
	{
		if ($user_id == 0)
		{
			return false;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_subscribers'));
		$query->set($_db->quoteName('user_id') . " = " . $_db->quote($user_id));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		$_db->setQuery($query);

		$result  = $_db->execute();

		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to update user ID in table subscribers
	 *
	 * @param   int     $subscriber_id
	 * @param   array   $subscriber_data
	 *
	 * @return  bool                true if subscription present and update okay
	 *
	 * @since  2.0.0
	 */
	public static function updateSubscriberData($subscriber_id, $subscriber_data)
	{
		if ($subscriber_id == 0)
		{
			return false;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_subscribers'));

		if ($subscriber_data['name'] != '')
		{
			$query->set($_db->quoteName('name') . " = " . $_db->quote($subscriber_data['name']));
		}

		if ($subscriber_data['firstname'] != '')
		{
			$query->set($_db->quoteName('firstname') . " = " . $_db->quote($subscriber_data['firstname']));
		}

		if ($subscriber_data['special'] != '')
		{
			$query->set($_db->quoteName('special') . " = " . $_db->quote($subscriber_data['special']));
		}

		if ($subscriber_data['gender'] != '')
		{
			$query->set($_db->quoteName('gender') . " = " . $_db->quote($subscriber_data['gender']));
		}

		if ($subscriber_data['mailformat'] != '')
		{
			$query->set($_db->quoteName('mailformat') . " = " . $_db->quote($subscriber_data['mailformat']));
		}

		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($subscriber_id));

		$_db->setQuery($query);

		$result  = $_db->execute();

		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to update subscribed mailinglists in table
	 *
	 * @param   int     $subscriber_id
	 * @param   array   $new_mailinglists
	 *
	 * @return  bool    true if subscription present and update okay
	 *
	 * @since  2.0.0
	 */
	public static function updateSubscribedMailinglists($subscriber_id, $new_mailinglists)
	{
		$subscribed_mailinglists    = self::getSubscribedMailinglists($subscriber_id);

		if ((count($new_mailinglists) == 1) && ($new_mailinglists[0] == 0))
		{
			unset($new_mailinglists[0]);
		}

		if (empty($new_mailinglists))
		{
			return false;
		}

		$mailinglists_to_add    = array();

		foreach ($new_mailinglists as $new_id)
		{
			$found  = array_search($new_id, $subscribed_mailinglists);

			if ($found === false)
			{
				$mailinglists_to_add[]  = $new_id;
			}
		}

		if (count($mailinglists_to_add))
		{
			$save_mailinglists_result   = self::saveSubscribersMailinglists($subscriber_id, $mailinglists_to_add);

			return $save_mailinglists_result;
		}

		return true;
	}

	/**
	 * Method to get subscribed mailinglists
	 *
	 * @param   int     $subscriber_id
	 *
	 * @return  array   $subscribed_mailinglists
	 *
	 * @since       2.0.0
	 */
	public static function getSubscribedMailinglists($subscriber_id)
	{
		// @Todo: As from version 2.0.0 helper class of component may be used
		$_db = JFactory::getDbo();
		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' = ' . $_db->quote($subscriber_id));

		$_db->setQuery($query);

		$subscribed_mailinglists = $_db->loadColumn();

		return  $subscribed_mailinglists;
	}

	/**
	 * Method to get subscription email from BwPostman
	 *
	 * @param   int  $user_id
	 *
	 * @return  array|bool     subscriber mailaddress and id, or false on error
	 *
	 * @since  2.0.0
	 */
	public static  function getSubscriptionData($user_id)
	{
		if ($user_id == 0)
		{
			return false;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('email'));
		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . $_db->quote($user_id));

		$_db->setQuery($query);

		$subscriber  = $_db->loadAssoc();

		if (is_array($subscriber))
		{
			return $subscriber;
		}

		return false;
	}

	/**
	 * Method to create user data array
	 *
	 * @param string        $user_mail
	 * @param int           $user_id
	 * @param array         $subscriber_data
	 * @param array         $mailinglist_ids
	 *
	 * @return object       $subscriber
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public static function createSubscriberData($user_mail, $user_id, $subscriber_data, $mailinglist_ids)
	{
		$date   = JFactory::getDate();
		$time   = $date->toSql();

		// @Todo: For version 2.0.0 of component replace $_SERVER['REMOTE_ADDR'] with the following
		$remote_ip  = JFactory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

		$captcha    = 'bwp-' . BwPostmanHelper::getCaptcha(1);

		$subscriber = new stdClass();

		$subscriber->id                = 0;
		$subscriber->user_id           = $user_id;
		$subscriber->gender            = \Joomla\Utilities\ArrayHelper::getValue($subscriber_data, 'gender', '', 'string');
		$subscriber->name              = \Joomla\Utilities\ArrayHelper::getValue($subscriber_data, 'name', '', 'string');
		$subscriber->firstname         = \Joomla\Utilities\ArrayHelper::getValue($subscriber_data, 'firstname', '', 'string');
		$subscriber->special           = \Joomla\Utilities\ArrayHelper::getValue($subscriber_data, 'special', '', 'string');
		$subscriber->email             = $user_mail;
		$subscriber->emailformat       = \Joomla\Utilities\ArrayHelper::getValue($subscriber_data, 'emailformat', 1, 'int');
		$subscriber->mailinglists      = $mailinglist_ids;
		$subscriber->activation        = self::createActivation();
		$subscriber->editlink          = self::createEditlink();
		$subscriber->status            = 0;
		$subscriber->registration_date = $time;
		$subscriber->registered_by     = 0;
		$subscriber->registration_ip   = $remote_ip;
		$subscriber->confirmed_by      = 0;
		$subscriber->archived_by       = 0;
		$subscriber->{$captcha}        = '1';
		$subscriber->agreecheck        = '1';

		return $subscriber;
	}

	/**
	 * Method to create the activation and check if the sting does not exist twice or more
	 *
	 * @return string   $activation
	 *
	 * @since       2.0.0
	 */
	public static function createActivation()
	{
		// @ToDo: When this method has moved to helper class of component, this one here is redundant
		$_db                = JFactory::getDbo();
		$query              = $_db->getQuery(true);
		$current_activation = null;
		$match_activation   = true;

		while ($match_activation)
		{
			$current_activation = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->quote($current_activation));

			$_db->setQuery($query);

			$activation = $_db->loadResult();

			if ($activation == $current_activation)
			{
				$match_activation = true;
			}
			else
			{
				$match_activation = false;
			}
		}

		return $current_activation;
	}

	/**
	 * Method to create the editlink and check if the sting does not exist twice or more
	 *
	 * @return string   $editlink
	 *
	 * @since       0.9.1
	 */
	public static function createEditlink()
	{
		// @ToDo: When this method has moved to helper class of component, this one here is redundant
		$_db                = JFactory::getDbo();
		$query              = $_db->getQuery(true);
		$current_editlink   = null;
		$match_editlink     = true;

		while ($match_editlink)
		{
			$current_editlink = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			$query->select($_db->quoteName('editlink'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('editlink') . ' = ' . $_db->quote($current_editlink));

			$_db->setQuery($query);

			$editlink = $_db->loadResult();

			if ($editlink == $current_editlink)
			{
				$match_editlink = true;
			}
			else
			{
				$match_editlink = false;
			}
		}

		return $current_editlink;
	}

	/**
	 * Method to save subscriber data into table
	 *
	 * @param   object   $data              subscriber data
	 *
	 * @return  int      $subscriber_id     id of saved subscriber
	 *
	 * @throws exception
	 *
	 * @since       2.0.0
	 */
	public static function saveSubscriber($data)
	{
		// @Todo: As from version 2.0.0 BwPostmanModelRegister->save() may be used, depends on spam check solution
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/tables');
		$table  = JTable::getInstance('Subscribers', 'BwPostmanTable');

		// Bind the data.
		if (!$table->bind($data))
		{
			return false;
		}

		// Check the data.
		/*
		 * @ToDo: spam check as yet implemented is evil to implement in registration form of Joomla.
		 * Better solution would be a plugin for spam check to outsource spam check from table check.
		 * That would also open the possibility to use other spam check methods/plugins
		 */
		/*
		$check_data = \Joomla\Utilities\ArrayHelper::fromObject($data);
		JFactory::getApplication()->setUserState('com_bwpostman.subscriber.register.data', $check_data);
		if (!$table->check())
		{
			return false;
		}
		*/

		// Store the data.
		if (!$table->store())
		{
			// Allow an exception to be thrown.
			throw  new Exception($table->getError());
		}

		$subscriber_id = self::getSubscriberIdByEmail($data->email);

		return $subscriber_id;
	}

	/**
	 * Method to save subscribed mailinglists
	 *
	 * @param   int     $subscriber_id
	 * @param   array   $mailinglist_ids
	 *
	 * @return bool     true on success
	 *
	 * @since       2.0.0
	 */
	public static function saveSubscribersMailinglists($subscriber_id, $mailinglist_ids)
	{
		// @Todo: As from version 2.0.0 helper class of component may be used
		$_db   = JFactory::getDbo();

		foreach ($mailinglist_ids as $mailinglist_id)
		{
			$query = $_db->getQuery(true);

			$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->columns(
				array(
					$_db->quoteName('subscriber_id'),
					$_db->quoteName('mailinglist_id')
				)
			);
			$query->values(
				$_db->quote($subscriber_id) . ',' .
				$_db->quote($mailinglist_id)
			);
			$_db->setQuery($query);

			$_db->execute();
		}

		return true;
	}

	/**
	 * Method to get the subscriber id by email address
	 *
	 * @param   string  $email
	 *
	 * @return  int     $id
	 *
	 * @since       2.0.0
	 */
	public static function getSubscriberIdByEmail($email)
	{
		$_db    = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($email));

		$_db->setQuery($query);

		$id = $_db->loadResult();

		return  $id;
	}
}
