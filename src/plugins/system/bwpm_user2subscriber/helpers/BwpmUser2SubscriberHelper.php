<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * BwPostman helper class for plugin.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
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

namespace BoldtWebservice\Plugin\BwPostman\System\U2S\Helper;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Table\SubscriberTable;
use Exception;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;
use stdClass;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Table', JPATH_ADMINISTRATOR.'/components/com_bwpostman/src/Table');


/**
 * Class BwpmUser2SubscriberHelper
 *
 * @since 2.0.0
 */
abstract class BwpmUser2SubscriberHelper
{
	/**
	 * Method to check if user has a subscription
	 *
	 * @param string $user_mail
	 *
	 * @return  integer   $subscriber_id   ID of subscription or 0
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static function hasSubscription(string $user_mail): int
	{
		$subscriber_id = 0;

		if ($user_mail == '')
		{
			return 0;
		}

		$_db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		try
		{
			$_db->setQuery($query);

			$subscriber_id  = (int)$_db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $subscriber_id;
	}

	/**
	 * Method to check if user has a subscription
	 *
	 * @param string $user_mail
	 *
	 * @return  bool    subscriber is to activate or not
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static function isToActivate(string $user_mail): bool
	{
		if ($user_mail == '')
		{
			return false;
		}

		$result = array();
		$_db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('status'));
		$query->select($_db->quoteName('activation'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		try
		{
			$_db->setQuery($query);

			$result  = $_db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if (is_array($result) && key_exists('status', $result) && key_exists('activation', $result)
			&& !$result['status'] && $result['activation'] != '')
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to update user ID in table subscribers
	 *
	 * @param string $user_mail
	 * @param int    $user_id
	 *
	 * @return  bool                true if subscription present and update okay
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static function updateUserIdAtSubscriber(string $user_mail, int $user_id): bool
	{
		if ($user_id == 0)
		{
			return false;
		}

		$result = false;
		$_db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_subscribers'));
		$query->set($_db->quoteName('user_id') . " = " . $_db->quote($user_id));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

		try
		{
			$_db->setQuery($query);

			$result  = $_db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to update user ID in table subscribers
	 *
	 * @param int   $subscriber_id
	 * @param array $subscriber_data
	 *
	 * @return  bool                true if subscription present and update okay
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static function updateSubscriberData(int $subscriber_id, array $subscriber_data): bool
	{
		if ($subscriber_id == 0)
		{
			return false;
		}

		$_db	= Factory::getContainer()->get(DatabaseInterface::class);
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

		if (isset($subscriber_data['gender']) && $subscriber_data['gender'] != '')
		{
			$query->set($_db->quoteName('gender') . " = " . $_db->quote($subscriber_data['gender']));
		}

		if (isset($subscriber_data['mailformat']) && $subscriber_data['mailformat'] != '')
		{
			$query->set($_db->quoteName('mailformat') . " = " . $_db->quote($subscriber_data['mailformat']));
		}

		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($subscriber_id));

		$result = false;

		try
		{
			$_db->setQuery($query);

			$result  = $_db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to update subscribed mailinglists in table
	 *
	 * @param int   $subscriber_id
	 * @param array $new_mailinglists
	 *
	 * @return  bool    true if subscription present and update okay
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static function updateSubscribedMailinglists(int $subscriber_id, array $new_mailinglists): bool
	{
		$subscribed_mailinglists    = self::getSubscribedMailinglists($subscriber_id);

		if (is_countable($new_mailinglists) && (count($new_mailinglists) == 1) && ($new_mailinglists[0] == 0))
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
			return self::saveSubscribersMailinglists($subscriber_id, $mailinglists_to_add);
		}

		return true;
	}

	/**
	 * Method to get subscribed mailinglists
	 *
	 * @param int $subscriber_id
	 *
	 * @return  array   $subscribed_mailinglists
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public static function getSubscribedMailinglists(int $subscriber_id): array
	{
		$subscribed_mailinglists = array();

		// @Todo: As from version 2.0.0 helper class of component may be used
		$_db = Factory::getContainer()->get(DatabaseInterface::class);
		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' = ' . $_db->quote($subscriber_id));

		try
		{
			$_db->setQuery($query);

			$subscribed_mailinglists = $_db->loadColumn();

			if ($subscribed_mailinglists === null)
			{
				$subscribed_mailinglists = array();
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return  $subscribed_mailinglists;
	}

	/**
	 * Method to get subscription email from BwPostman
	 *
	 * @param int $user_id
	 *
	 * @return  array     subscriber mailaddress and id, or false on error
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public static  function getSubscriptionData(int $user_id): array
	{
		$subscriber = array();

		if ($user_id == 0)
		{
			return $subscriber;
		}

		$_db	= Factory::getContainer()->get(DatabaseInterface::class);
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('email'));
		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . $_db->quote($user_id));

		try
		{
			$_db->setQuery($query);

			$subscriber  = $_db->loadAssoc();

			if ($subscriber === null)
			{
				$subscriber = array();
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $subscriber;
	}

	/**
	 * Method to create user data array
	 *
	 * @param string $user_mail
	 * @param int    $user_id
	 * @param array  $subscriber_data
	 * @param array  $mailinglist_ids
	 *
	 * @return object       $subscriber
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public static function createSubscriberData(string $user_mail, int $user_id, array $subscriber_data, array $mailinglist_ids): object
	{
		$date   = Factory::getDate();
		$time   = $date->toSql();

		$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

		$captcha    = 'bwp-' . BwPostmanHelper::getCaptcha();

		$db         = Factory::getContainer()->get(DatabaseInterface::class);
		$subsTable  = new SubscriberTable($db);
		$subscriber = new stdClass();

		$subscriber->id                = 0;
		$subscriber->user_id           = $user_id;
		$subscriber->gender            = ArrayHelper::getValue($subscriber_data, 'gender', '', 'string');
		$subscriber->name              = ArrayHelper::getValue($subscriber_data, 'name', '', 'string');
		$subscriber->firstname         = ArrayHelper::getValue($subscriber_data, 'firstname', '', 'string');
		$subscriber->special           = ArrayHelper::getValue($subscriber_data, 'special', '', 'string');
		$subscriber->email             = $user_mail;
		$subscriber->emailformat       = ArrayHelper::getValue($subscriber_data, 'emailformat', 1, 'int');
		$subscriber->mailinglists      = $mailinglist_ids;
		$subscriber->activation        = $subsTable->createActivation();
		$subscriber->editlink          = $subsTable->getEditlink();
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
	 * Method to save subscriber data into table
	 *
	 * @param object $data subscriber data
	 *
	 * @return  int      $subscriber_id     id of saved subscriber or 0
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public static function saveSubscriber(object $data): int
	{
		// @Todo: As from version 2.0.0 BwPostmanModelRegister->save() may be used, depends on spam check solution
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$table = new SubscriberTable($db);

		// Bind the data.
		if (!$table->bind($data))
		{
			return 0;
		}

		// Check the data.
		/*
		 * @ToDo: spam check as yet implemented is evil to implement in registration form of Joomla.
		 * Better solution would be a plugin for spam check to outsource spam check from table check.
		 * That would also open the possibility to use other spam check methods/plugins
		 */
		/*
		$check_data = ArrayHelper::fromObject($data);
		Factory::getApplication()->setUserState('com_bwpostman.subscriber.register.data', $check_data);
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

		return self::getSubscriberIdByEmail($data->email);
	}

	/**
	 * Method to save subscribed mailinglists
	 *
	 * @param int   $subscriber_id
	 * @param array $mailinglist_ids
	 *
	 * @return bool     true on success
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public static function saveSubscribersMailinglists(int $subscriber_id, array $mailinglist_ids): bool
	{
		// @Todo: As from version 2.0.0 helper class of component may be used
		$result = false;
		$_db    = Factory::getContainer()->get(DatabaseInterface::class);

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

			try
			{
				$_db->setQuery($query);

				$result = $_db->execute();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}
		}

		return $result;
	}

	/**
	 * Method to get the subscriber id by email address
	 *
	 * @param string $email
	 *
	 * @return  int     $id
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public static function getSubscriberIdByEmail(string $email): int
	{
		$id     = 0;
		$_db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($email));

		try
		{
			$_db->setQuery($query);

			$id = (int)$_db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg U2SHelper FE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return  $id;
	}
}
