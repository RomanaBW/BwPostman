<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman sendmail content table for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
 * #__bwpostman_sendmailcontent table handler
 * Table for storing the prepared data for sending a newsletter
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanTableSendmailcontent extends JTable
{
	/**
	 * @var int Primary Key --> every ID exists twice (once for mode text, once for mode html
	 *
	 * @since       0.9.1
	 */
	public $id = null;

	/**
	 * @var int Primary Key --> 0 = Text, 1 = HTML
	 *
	 * @since       0.9.1
	 */
	public $mode = null;

	/**
	 * @var int Newsletter-ID
	 *
	 * @since       0.9.1
	 */
	public $nl_id = null;

	/**
	 * @var string Sender name
	 *
	 * @since       0.9.1
	 */
	public $from_name = null;

	/**
	 * @var string Sender email
	 *
	 * @since       0.9.1
	 */
	public $from_email = null;

	/**
	 * @var string Subject
	 *
	 * @since       0.9.1
	 */
	public $subject = null;

	/**
	 * @var String Email-body
	 *
	 * @since       0.9.1
	 */
	public $body = null;

	/**
	 * @var string CC email
	 *
	 * @since       0.9.1
	 */
	public $cc_email = null;

	/**
	 * @var string BCC email
	 *
	 * @since       0.9.1
	 */
	public $bcc_email = null;

	/**
	 * @var string Attachment
	 *
	 * @since       0.9.1
	 */
	public $attachment = null;

	/**
	 * @var string Reply-to email
	 *
	 * @since       0.9.1
	 */
	public $reply_email = null;

	/**
	 * @var string Reply-to name
	 *
	 * @since       0.9.1
	 */
	public $reply_name = null;

	/**
	 * @var int substitute links --> 0 = no, 1 = yes
	 *
	 * @since       2.0.0
	 */
	public $substitute_links = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_sendmailcontent', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param array|object  $data       Named array
	 * @param string        $ignore     Space separated list of fields not to bind
	 *
	 * @throws BwException
	 *
	 * @return boolean
	 *
	 * @since       0.9.1
	 */
	public function bind($data, $ignore='')
	{
		// Bind the rules.
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
			throw new BwException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($data, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 *
	 * @return boolean True
	 *
	 * @since       0.9.1
	 */
	public function check()
	{
		return true;
	}


	/**
	 * Overloaded store method
	 *
	 * @access 	public
	 *
	 * @param	boolean True to update fields even if they are null.
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function store($updateNulls = false)
	{
		$k		= $this->_tbl_key;
		$res    = 0;
		$query	= $this->_db->getQuery(true);

		if (!$this->$k)
		{
			// Find the next possible id and insert
			$query->select('IFNULL(MAX(id)+1,1) AS ' . $this->_db->quoteName('id'));
			$query->from($this->_db->quoteName($this->_tbl));
			$this->_db->setQuery($query);

			try
			{
				$res = $this->_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if ($res)
			{
				$this->$k = $res;
			}
		}

		if ($this->$k)
		{
			// An id value is set
			try
			{
				$this->_db->insertObject($this->_tbl, $this);
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(get_class($this) . '::store failed - ' . $e->getMessage());
			}
		}

		return true;
	}

	/**
	 * Overloaded load method
	 *
	 * @access	public
	 *
	 * @param 	int		    $keys       ID
	 * @param 	boolean	    $reset      Mode (0 = Text, 1 = HTML)
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function load($keys = null, $reset = true)
	{
		if (!$keys)
		{
			return 0;
		}

		// If (empty($mode)) return 0;
		$app	= JFactory::getApplication();
		$mode	= $app->getUserState('com_bwpostman.newsletter.send.mode', 1);
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		$result = array();

		$this->reset();

		$query->select('*');
		$query->from($_db->quoteName($this->_tbl));
		$query->where($_db->quoteName('id') . ' = ' . (int) $keys);
		$query->where($_db->quoteName('mode') . ' = ' . (int) $mode);

		$_db->setQuery($query);

		try
		{
			$result = $_db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $this->bind($result);
	}
}
