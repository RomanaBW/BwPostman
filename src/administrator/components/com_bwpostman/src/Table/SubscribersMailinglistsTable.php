<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers-lists table for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Table;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use Exception;
use JAccessRules;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * #__bwpostman_subscribers_mailinglists table handler
 * Table for storing the subscriber data
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class SubscribersMailinglistsTable extends Table
{
	/**
	 * @var int Primary Key subscriber-id
	 *
	 * @since       0.9.1
	 */
	public $subscriber_id = null;

	/**
	 * @var int Primary Key list-id
	 *
	 * @since       0.9.1
	 */
	public $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_subscribers_mailinglists', 'subscriber_id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return boolean
	 *
	 * @throws  BwException
	 *
	 * @since       0.9.1
	 */
	public function bind($src, $ignore=''): bool
	{
		// Bind the rules.
		if (is_object($src))
		{
			if (property_exists($src, 'rules') && is_array($src->rules))
			{
				$rules = new JAccessRules($src->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($src))
		{
			if (array_key_exists('rules', $src) && is_array($src['rules']))
			{
				$rules = new JAccessRules($src['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Method to get the subscribers of a specific mailinglist
	 *
	 * @param 	array|integer $ids id of mailinglist
	 *
	 * @return 	array       $subscribers of this mailinglist
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0 (here, before since 2.2.0 at mailinglist helper)
	 */
	public function getSubscribersOfMailinglist($ids): ?array
	{
		$subscribersOfMailinglist = null;

		if (!is_array($ids))
		{
			$ids = array((int)$ids);
		}

		$ids = ArrayHelper::toInteger($ids);

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('subscriber_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('mailinglist_id') . ' IN (' . implode(',', $ids) . ')');

		try
		{
			$db->setQuery($query);

			$subscribersOfMailinglist = $db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}


		return $subscribersOfMailinglist;
	}

	/**
	 * Method to delete all or selected mailinglist entries for the subscriber_id from subscribers_mailinglists-table
	 *
	 * @param integer $subscriber_id
	 * @param array|null
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function deleteMailinglistsOfSubscriber(int $subscriber_id, $mailinglists = null): bool
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->delete($db->quoteName($this->_tbl));
		$query->where($db->quoteName('subscriber_id') . ' =  ' . $subscriber_id);

		if (!is_null($mailinglists))
		{
			$query->where($db->quoteName('mailinglist_id') . ' IN (' . (implode('.', $mailinglists)) . ')');
		}

		try
		{
			$db->setQuery($query);
			$db->execute();

			return true;
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Method to store subscribed mailinglists in newsletters_mailinglists table
	 *
	 * @param integer $subscriber_id
	 * @param array   $mailinglist_ids
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function storeMailinglistsOfSubscriber(int $subscriber_id, array $mailinglist_ids): bool
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->columns(
			array(
				$db->quoteName('subscriber_id'),
				$db->quoteName('mailinglist_id')
			)
		);

		foreach ($mailinglist_ids AS $list_id)
		{
			$query->insert($db->quoteName($this->_tbl));
			$query->values(
				$subscriber_id . ',' .
				(int) $list_id
			);
		}

		try
		{
			$db->setQuery($query);
			$db->execute();

			return  true;
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			return false;
		}
	}

	/**
	 * Method to check if a subscriber has a subscription to a specific mailinglist
	 *
	 * @param integer $subscriberId  ID of subscriber to check
	 * @param integer $mailinglistId ID of mailinglist to check
	 *
	 * @return boolean|integer
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public function hasSubscriptionForMailinglist(int $subscriberId, int $mailinglistId)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('subscriber_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('subscriber_id') . ' = ' . $subscriberId);
		$query->where($db->quoteName('mailinglist_id') . ' = ' . $mailinglistId);

		try
		{
			$db->setQuery($query);

			$subsIdExists = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			return -1;
		}

		if ($subsIdExists === null)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to remove the mailinglist from the cross table #__bwpostman_subscribers_mailinglists
	 *
	 * @param integer $id
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0 (here, before since 2.0.0 at mailinglist model)
	 */
	public function deleteMailinglistSubscribers(int $id): bool
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->delete($db->quoteName($this->_tbl));
		$query->where($db->quoteName('mailinglist_id') . ' =  ' . $db->quote($id));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			return false;
		}

		return true;
	}

	/**
	 * Method to get the mailinglist ids which a subscriber is subscribed to
	 *
	 * @param integer $sub_id
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public function getMailinglistIdsOfSubscriber(int $sub_id): array
	{
		$mailinglist_ids = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('mailinglist_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('subscriber_id') . ' = ' . $sub_id);

		try
		{
			$db->setQuery($query);

			$mailinglist_ids = $db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubsMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return ArrayHelper::toInteger($mailinglist_ids);
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  3.0.0
	 */
	public function getId()
	{
		$key = $this->getKeyName();

		return $this->$key;
	}

	/**
	 * Check if the record has a property (applying a column alias if it exists)
	 *
	 * @param string $key key to be checked
	 *
	 * @return  boolean
	 *
	 * @since   3.0.0
	 */
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
