<?php
/**
 * BwTimeControl Plugin for BwPostman Newsletter Component
 *
 * BwTimeControl automailing sendmail content table for backend.
 *
 * @version 1.2.0 bwplgtc
 * @package BwPostman BwTimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014 Boldt Webservice <forum@boldt-webservice.de>
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
 * #__bwpostman_tc_sendmailcontent table handler
 * Table for storing the prepared data for automatic/time-controlled sending a newsletter
 *
 * @package		BwPostman BwTimeControl Plugin
 */
class BwPostmanTableTc_Sendmailcontent extends JTable
{
	/** @var int Primary Key --> every ID exists twice (once for mode text, once for mode html */
	var $id = null;

	/** @var tinyint --> 0 = Text, 1 = HTML */
	var $mode = null;

	/** @var int Newsletter-ID */
	var $nl_id = null;
		
	/** @var int Campaign-ID */
	var $campaign_id = null;
		
	/** @var int Mailnumber for Kampaign */
	var $mail_number = null;
		
	/** @var tinyint --> 0 = not sent, 1 = sent */
	var $sent = null;

	/** @var tinyint --> 0 = actual, 1 = old */
	var $old = null;

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
	function __construct($db)
	{
		parent::__construct('#__bwpostman_tc_sendmailcontent', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access 	public
	 * @param 	array Named array
	 * @param 	string Space separated list of fields not to bind
	 * @return 	boolean
	 *
	 * @since   1.2.0
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
	 *
	 * @since   1.2.0
	 */
	function check()
	{
		return true;
	}


	/**
	 * Overridden JTable::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.2.0
	 */
	public function store($updateNulls = false)
	{
		$res	= parent::store($updateNulls);

		return $res;
	}

	/**
	 * Set reminder "sent"
	 *
	 * @access 	public
	 * @param   int  ID
	 * @return 	boolean
	 *
	 * @since   1.2.0
	 */
	public function setSent($id)
	{
		if (!$id) return 0;
dump ($id, 'SetSent ID');

		$db = $this->getDBO();
		$query	= $db->getQuery(true);

		$query->update($this->_tbl);
		$query->set($db->quoteName('sent').' = '.$db->quote(1));
		$query->where($db->quoteName('id').' = '.(int) $id);
		$db->setQuery($query);
		$db->query();
			
		return true;
	}

	/**
	 * Overloaded load method
	 *
	 * @access	public
	 * 
	 * @param 	int 	ID
	 * @param 	tinyint Mode (0 = Text, 1 = HTML)
	 *
	 * @since   1.2.0
	 */
	public function load($keys = NULL, $reset = true)
	{
		if (!$keys) return 0;
		
		$mode	= JFactory::getApplication()->getUserState('bwtimecontrol.mode', false);
		if ($mode === false) return 0;

		$db		= $this->getDBO();
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from($this->_tbl);
		$query->where('id = ' . $db->Quote($keys));
		$query->where('mode = ' . $db->Quote($mode));
		$db->setQuery($query);

		$result = $db->loadObject();
		
		if ($result) {
			if ($this->bind($result)){
				return true;
			}
		}
		else {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}
}