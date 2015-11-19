<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman sendmail content table for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

/**
 * #__bwpostman_sendmailcontent table handler
 * Table for storing the prepared data for sending a newsletter
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanTableSendmailcontent extends JTable
{
	/** @var int Primary Key --> every ID exists twice (once for mode text, once for mode html */
	var $id = null;

	/** @var tinyint Primary Key --> 0 = Text, 1 = HTML */
	var $mode = null;

	/** @var int Newsletter-ID */
	var $nl_id = null;
		
	/** @var string Sender name */
	var $from_name = null;

	/** @var string Sender email */
	var $from_email = null;

	/** @var string Subject */
	var $subject = null;

	/** @var String Email-body */
	var $body = null;

	/** @var string CC email*/
	var $cc_email = null;

	/** @var string BCC email */
	var $bcc_email = null;

	/** @var string Attachment */
	var $attachment = null;

	/** @var string Reply-to email */
	var $reply_email = null;

	/** @var string Reply-to name */
	var $reply_name = null;

	/**
	 * Constructor
	 *
	 * @param db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_sendmailcontent', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 * @param object Named array
	 * @param string Space separated list of fields not to bind
	 * @return boolean
	 */
	public function bind($data, $ignore='')
	{
		// Bind the rules.
		if (is_object($data)) {
			if (property_exists($data, 'rules') && is_array($data->rules))
			{
				$rules = new JAccessRules($data->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($data)) {
			if (array_key_exists('rules', $data) && is_array($data['rules']))
			{
				$rules = new JAccessRules($data['rules']);
				$this->setRules($rules);
			}
		}
		else {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
			$this->setError($e);
			return false;
		}
				
		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($data, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True
	 */
	public function check()
	{
		return true;
	}


	/**
	 * Overloaded store method
	 *
	 * @access 	public
	 * @param	boolean True to update fields even if they are null.
	 * @return 	boolean
	 */
	public function store($updateNulls = false)
	{
		$k		= $this->_tbl_key;
		$query	= $this->_db->getQuery(true);

			if (!$this->$k) {
			// Find the next possible id and insert
			$query->select('IFNULL(MAX(id)+1,1) AS ' . $this->_db->quoteName('id'));
			$query->from($this->_db->quoteName($this->_tbl));
			$this->_db->setQuery($query);

			$res = $this->_db->loadResult();
			if ($res) $this->$k = $res;
		}
		
		if ($this->$k) {
			// An id value is set
			$ret = $this->_db->insertObject($this->_tbl, $this);
		} else {
			$ret = 0;
		}

		if (!$ret) {
			$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Overloaded load method
	 *
	 * @access	public
	 * @param 	int		ID
	 * @param 	tinyint	Mode (0 = Text, 1 = HTML)
	 */
	public function load($keys = null, $reset = true){
		if (!$keys) return 0;
		// If (empty($mode)) return 0;
		$app	= JFactory::getApplication();
		$mode	= $app->getUserState('com_bwpostman.newsletter.send.mode', 1);
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		$this->reset();
		
		$query->select('*');
		$query->from($_db->quoteName($this->_tbl));
		$query->where($_db->quoteName('id') . ' = ' . (int) $keys);
		$query->where($_db->quoteName('mode') . ' = ' . (int) $mode);
				
		$_db->setQuery($query);
		
		if ($result = $_db->loadAssoc()) {
			return $this->bind($result);
		}
		else{
			$this->setError($db->getErrorMsg());
			return false;
		}
	}
}