<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman sendmail queue table for backend.
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
use Exception;
use JAccessRules;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use Joomla\Database\DatabaseDriver;
use RuntimeException;

/**
 * #__bwpostman_sendmailqueue table handler
 * Table for storing the recipients to whom a newsletter shall be send
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class SendmailqueueTable extends Table
{
	/**
	 * @var int Primary Key
	 *
	 * @since       0.9.1
	 */
	public $id = null;

	/**
	 * @var int Content-ID --> from the sendmailcontent-Table
	 *
	 * @since       0.9.1
	 */
	public $content_id = null;

	/**
	 * @var string Recipient email
	 *
	 * @since       0.9.1
	 */
	public $recipient = null;

	/**
	 * @var int Mode --> 0 = Text, 1 = HTML
	 *
	 * @since
	 */
	public $mode = null;

	/**
	 * @var string Recipient name
	 *
	 * @since       0.9.1
	 */
	public $name = null;

	/**
	 * @var string Recipient firstname
	 *
	 * @since       0.9.1
	 */
	public $firstname = null;

	/**
	 * @var int Subscriber ID
	 *
	 * @since       0.9.1
	 */
	public $subscriber_id = null;

	/**
	 * @var int Number of delivery attempts
	 *
	 * @since       0.9.1
	 */
	public $trial = null;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_sendmailqueue', 'id', $db);
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
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function bind($src, $ignore=''): bool
	{
		try
		{// Bind the rules.
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

			// Cast properties
			$this->id = (int) $this->id;
		}
		catch (BwException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public

	 * @return boolean True
	 *
	 * @since       0.9.1
	 */
	public function check(): bool
	{
		return true;
	}

	/**
	 * Method to get the first entry of this table and remove it
	 *
	 * @param integer $trial         Only pop entries with < trial
	 * @param boolean $fromComponent do we come from component or from plugin
	 *
	 * @return    bool --> 0 if nothing was selected
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function pop(int $trial = 2, bool $fromComponent = true): bool
	{
		$this->reset();
		$result = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('trial') . ' < ' . $trial);
		$query->order($db->quoteName($this->_tbl_key) . ' ASC LIMIT 0,1');

		PluginHelper::importPlugin('bwpostman');

		Factory::getApplication()->triggerEvent('onBwPostmanGetAdditionalQueueWhere', array(&$query, $fromComponent));

		try
		{
			$db->setQuery($query);

			$result = $db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($result !== null && count($result))
		{
			if ($this->bind($result))
			{
				$this->_trackAssets = 0;
				$this->delete($this->id);

				return true;
			}
		}

		return false;
	}

	/**
	 * Method to store a single recipient
	 *
	 * @access 	public
	 *
	 * @param int    $content_id    Content ID --> from the sendmailcontent-Table
	 * @param int    $emailformat   Emailformat --> 0 = Text, 1 = HTML
	 * @param string $email         Recipient email
	 * @param string $name          Recipient name
	 * @param string $firstname     Recipient first name
	 * @param int    $subscriber_id Subscriber ID
	 * @param int    $trial         Number of delivery attempts
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function push(int $content_id, int $emailformat, string $email, string $name, string $firstname, int $subscriber_id, int $trial = 0): bool
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->insert($db->quoteName($this->_tbl));
		$query->columns(
			array(
				$db->quoteName('content_id'),
				$db->quoteName('mode'),
				$db->quoteName('recipient'),
				$db->quoteName('name'),
				$db->quoteName('firstname'),
				$db->quoteName('subscriber_id'),
				$db->quoteName('trial'),
				)
		);
		$query->values(
			$content_id . ',' .
			$emailformat . ',' .
			$db->quote($email) . ',' .
			$db->quote($name) . ',' .
			$db->quote($firstname) . ',' .
			$subscriber_id . ',' .
			$trial
		);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to store all recipients when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param int    $content_id Content ID --> from the sendmailcontent-Table
	 * @param string $status     Status --> 0 = unconfirmed, 1 = confirmed
	 * @param int    $nl_id      Newsletter-ID
	 * @param int    $cam_id     campaign id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */

	public function pushSubscribers(int $content_id, string $status, int $nl_id, int $cam_id): bool
	{
		if (!$content_id)
		{
			return false;
		}

		$subscribers = array();
		$MvcFactory  = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		if ($nl_id)
		{
			if ($cam_id != '-1')
			{
				// Select mailinglist IDs from campaigns_mailinglists, if campaign ID is provided
				$camMlsTable = $MvcFactory->createTable('CampaignsMailinglists', 'Administrator');
				$mailinglists = $camMlsTable->getAssociatedMailinglistsByCampaign($cam_id);
			}
			else
			{
				// Select mailinglist IDs from newsletters_mailinglists, if no campaign ID is provided
				$nlsMlsTable = $MvcFactory->createTable('NewslettersMailinglists', 'Administrator');
				$mailinglists = $nlsMlsTable->getAssociatedMailinglistsByNewsletter($nl_id);
			}

			// Select unique subscriber IDs from subscribers_mailinglists of the calculated mailinglists
			$subsMlsTable = $MvcFactory->createTable('SubscribersMailinglists', 'Administrator');
			$subscribers = $subsMlsTable->getSubscribersOfMailinglist($mailinglists);

		}
		// Select subscribers data of the calculated subscriber IDs
		$subsTable = $MvcFactory->createTable('Subscriber', 'Administrator');
		$subscribersData = $subsTable->getSubscriberDataForSendmailqueue($content_id, $status, $subscribers);

		$data = array();

		foreach ($subscribersData as $subscribersDatum)
		{
			$quotedDatum = array();

			foreach ($subscribersDatum as $datum)
			{
				$quotedDatum[] = $db->quote($datum);
			}
			$data[] = implode(',', $quotedDatum);
		}

		// Insert queue data
		$query->insert($this->_tbl);
		$query->columns(
				$db->quoteName('content_id') . ',' .
				$db->quoteName('recipient') . ',' .
				$db->quoteName('mode') . ',' .
				$db->quoteName('name') . ',' .
				$db->quoteName('firstname') . ',' .
				$db->quoteName('subscriber_id')
		);
		$query->values($data);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to store all users when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param int   $content_id Content ID --> from the sendmailcontent-Table
	 * @param array $usergroups Usergroups
	 * @param int   $format     Emailformat --> standard email format defined by BwPostman preferences
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function pushJoomlaUser(int $content_id, array $usergroups, int $format = 0): bool
	{
		if (!$content_id)
		{
			return false;
		}

		if (!is_array($usergroups))
		{
			return false;
		}

		if (!count($usergroups))
		{
			return false;
		}

		$db      = $this->_db;
		$sub_res = array();

		$subQuery1 = $db->getQuery(true);

		$subQuery1->select($db->quoteName('g') . '.' . $db->quoteName('user_id'));
		$subQuery1->from($db->quoteName('#__user_usergroup_map') . ' AS ' . $db->quoteName('g'));
		$subQuery1->where($db->quoteName('g') . '.' . $db->quoteName('group_id') . ' IN (' . implode(',', $usergroups) . ')');

		$subQuery = $db->getQuery(true);
		$subQuery->select($db->quote($content_id) . ' AS content_id');
		$subQuery->select($db->quoteName('email', 'recipient'));
		$subQuery->select($db->quote($format) . ' AS mode');
		$subQuery->select($db->quoteName('name', 'name'));
		$subQuery->select(0 . ' AS subscriber_id');
		$subQuery->from($db->quoteName('#__users'));
		$subQuery->where($db->quoteName('block') . ' = ' . 0);
		$subQuery->where($db->quoteName('activation') . " IN ('', '0')");
		$subQuery->where($db->quoteName('id') . ' IN (' . $subQuery1 . ')');

		try
		{
			$db->setQuery($subQuery);

			$sub_res	= $db->loadRowList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		$data = array();

		foreach ($sub_res as $subscribersDatum)
		{
			$quotedDatum = array();

			foreach ($subscribersDatum as $datum)
			{
				$quotedDatum[] = $db->quote($datum);
			}
			$data[] = implode(',', $quotedDatum);
		}

		$query = $db->getQuery(true);

		$query->insert($db->quoteName($this->_tbl));
		$query->columns(
			array(
				$db->quoteName('content_id'),
				$db->quoteName('recipient'),
				$db->quoteName('mode'),
				$db->quoteName('name'),
				$db->quoteName('subscriber_id'),
			)
		);
		$query->values($data);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to reset sending trials
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function resetTrials(): bool
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('trial') . " = " . 0);
		$query->where($db->quoteName('trial') . ' > ' . 0);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to clear the queue
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function clearQueue(): bool
	{
		$db	= $this->_db;

		$query = "TRUNCATE TABLE $this->_tbl ";

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to check if there are entries. Depending on $count the result is true or a number of entries
	 *
	 * @param integer $trial number of sending trials
	 * @param integer $count 1: only count, 0: check for number of trials
	 *
	 * @return	bool|int	true if no entries or there are entries with number trials less than 2, otherwise false
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function checkTrials(int $trial = 2, int $count = 0)
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName($this->_tbl));

		try
		{
			$db->setQuery($query);

			$result = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		// returns only number of entries
		if ($count !== 0)
		{
			return $result;
		}

		// queue not empty
		if ($result != 0)
		{
			$query->where($db->quoteName('trial') . ' < ' . $trial);

			// all queue entries have trial number 2
			try
			{
				$db->setQuery($query);

				$result = $db->loadResult();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'SendmailQueueTable BE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}

			if ($result === 0)
			{
				return false;
			}
		}

		return true;
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
