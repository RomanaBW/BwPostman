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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;

/**
 * #__bwpostman_sendmailqueue table handler
 * Table for storing the recipients to whom a newsletter shall be send
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanTableSendmailqueue extends JTable
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
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_sendmailqueue', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param array|object  $data       Named array
	 * @param string        $ignore     Space separated list of fields not to bind
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 *
	 * @since       0.9.1
	 */
	public function bind($data, $ignore='')
	{
		try
		{// Bind the rules.
			if (is_object($data))
			{
				if (property_exists($data, 'rules') && is_array($data->rules))
				{
					$rules = new JAccessRules($data->rules);
					$this->setRules($rules);
				}
			}
			elseif (is_array($data))
			{
				if (array_key_exists('rules', $data) && is_array($data['rules']))
				{
					$rules = new JAccessRules($data['rules']);
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
		catch (BwException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return parent::bind($data, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public

	 * @return boolean True
	 *
	 * @since       0.9.1
	 */
	public function check()
	{
		return true;
	}

	/**
	 * Method to get the first entry of this table and remove it
	 *
	 * @param   integer     $trial           Only pop entries with < trial
	 * @param   boolean     $fromComponent   do we come from component or from plugin
	 *
	 * @return 	int --> 0 if nothing was selected
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function pop($trial = 2, $fromComponent = true)
	{
		$this->reset();
		$result = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('trial') . ' < ' . (int) $trial);
		$query->order($db->quoteName($this->_tbl_key) . ' ASC LIMIT 0,1');

		PluginHelper::importPlugin('bwpostman');

		Factory::getApplication()->triggerEvent('onBwPostmanGetAdditionalQueueWhere', array(&$query, $fromComponent));

		$db->setQuery($query);

		try
		{
			$result = $db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @param 	int     $content_id         Content ID --> from the sendmailcontent-Table
	 * @param 	int     $emailformat        Emailformat --> 0 = Text, 1 = HTML
	 * @param 	string  $email              Recipient email
	 * @param   string  $name               Recipient name
	 * @param   string  $firstname          Recipient first name
	 * @param   int     $subscriber_id      Subscriber ID
	 * @param   int     $trial              Number of delivery attempts
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function push($content_id, $emailformat, $email, $name, $firstname, $subscriber_id, $trial = 0)
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
			(int) $content_id . ',' .
			(int) $emailformat . ',' .
			$db->quote($email) . ',' .
			$db->quote($name) . ',' .
			$db->quote($firstname) . ',' .
			(int) $subscriber_id . ',' .
			(int) $trial
		);
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to store all recipients when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param 	int     $content_id     Content ID --> from the sendmailcontent-Table
	 * @param 	string  $status         Status --> 0 = unconfirmed, 1 = confirmed
	 * @param 	int     $nl_id          Newsletter-ID
	 * @param	int		$cam_id         campaign id
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */

	public function pushSubscribers($content_id, $status, $nl_id, $cam_id)
	{
		if (!$content_id)
		{
			return false;
		}

		$subscribers = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		if ($nl_id)
		{
			if ($cam_id != '-1')
			{
				// Select mailinglist IDs from campaigns_mailinglists, if campaign ID is provided
				$camMlsTable = Table::getInstance('Campaigns_Mailinglists', 'BwPostmanTable');
				$mailinglists = $camMlsTable->getAssociatedMailinglistsByCampaign($cam_id);
			}
			else
			{
				// Select mailinglist IDs from newsletters_mailinglists, if no campaign ID is provided
				$nlsMlsTable = Table::getInstance('Newsletters_Mailinglists', 'BwPostmanTable');
				$mailinglists = $nlsMlsTable->getAssociatedMailinglistsByNewsletter($nl_id);
			}

			// Select unique subscriber IDs from subscribers_mailinglists of the calculated mailinglists
			$subsMlsTable = Table::getInstance('Subscribers_Mailinglists', 'BwPostmanTable');
			$subscribers = $subsMlsTable->getSubscribersOfMailinglist($mailinglists);

		}
		// Select subscribers data of the calculated subscriber IDs
		$subsTable = Table::getInstance('Subscribers', 'BwPostmanTable');
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

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to store all users when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param 	int     $content_id     Content ID --> from the sendmailcontent-Table
	 * @param 	array   $usergroups     Usergroups
	 * @param 	int     $format         Emailformat --> standard email format defined by BwPostman preferences
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function pushJoomlaUser($content_id, $usergroups, $format = 0)
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
		$subQuery->select((int) 0 . ' AS subscriber_id');
		$subQuery->from($db->quoteName('#__users'));
		$subQuery->where($db->quoteName('block') . ' = ' . 0);
		$subQuery->where($db->quoteName('activation') . " IN ('', '0')");
		$subQuery->where($db->quoteName('id') . ' IN (' . $subQuery1 . ')');

		$db->setQuery($subQuery);
		try
		{
			$sub_res	= $db->loadRowList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	public function resetTrials()
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('trial') . " = " . (int) 0);
		$query->where($db->quoteName('trial') . ' > ' . (int) 0);

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @since       2.4.0
	 */
	public function clearQueue()
	{
		$db	= $this->_db;

		$query = "TRUNCATE TABLE {$this->_tbl} ";

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to check if there are entries. Depending on $count the result is true or a number of entries
	 *
	 * @param integer $trial  number of sending trials
	 * @param integer $count  1: only count, 0: check for number of trials
	 *
	 * @return	bool|int	true if no entries or there are entries with number trials less than 2, otherwise false
	 *
	 * @throws Exception
	 *
	 * @since       2.4.0
	 */
	public function checkTrials($trial = 2, $count = 0)
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName($this->_tbl));

		$db->setQuery($query);
		try
		{
			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

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
			$query->where($db->quoteName('trial') . ' < ' . (int) $trial);
			$db->setQuery($query);
			// all queue entries have trial number 2
			try
			{
				$result = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @since  2.4.0
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
	 * @since   2.4.0
	 */
	public function hasField($key)
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
