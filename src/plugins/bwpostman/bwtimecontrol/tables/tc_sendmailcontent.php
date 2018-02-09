<?php
/**
 * BwTimeControl Plugin for BwPostman Newsletter Component
 *
 * BwTimeControl automailing sendmail content table for backend.
 *
 * @version 2.0.0 bwplgtc
 * @package BwPostman BwTimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014-2017 Boldt Webservice <forum@boldt-webservice.de>
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
 * #__bwpostman_tc_sendmailcontent table handler
 * Table for storing the prepared data for automatic/time-controlled sending a newsletter
 *
 * @package		BwPostman BwTimeControl Plugin
 *
 * @since   1.2.0
 */
class BwPostmanTableTc_Sendmailcontent extends JTable
{
	/**
	 * @var int Primary Key --> every ID exists twice (once for mode text, once for mode html
	 *
	 * @since   1.2.0
	 */
	var $id = null;

	/**
	 * @var int --> 0 = Text, 1 = HTML
	 *
	 * @since   1.2.0
	 */
	var $mode = null;

	/**
	 * @var int Newsletter-ID
	 *
	 * @since   1.2.0
	 */
	var $nl_id = null;

	/**
	 * @var int Campaign-ID
	 *
	 * @since   1.2.0
	 */
	var $campaign_id = null;

	/**
	 * @var int mail number for campaign
	 *
	 * @since   1.2.0
	 */
	var $mail_number = null;

	/**
	 * @var int --> 0 = not sent, 1 = sent
	 *
	 * @since   1.2.0
	 */
	var $sent = null;

	/**
	 * @var int --> 0 = actual, 1 = old
	 *
	 * @since   1.2.0
	 */
	var $old = null;

	/**
	 * @var string Sender name
	 *
	 * @since   1.2.0
	 */
	var $from_name = null;

	/**
	 * @var string Sender email
	 *
	 * @since   1.2.0
	 */
	var $from_email = null;

	/**
	 * @var string Subject
	 *
	 * @since   1.2.0
	 */
	var $subject = null;

	/**
	 * @var String Email-body
	 *
	 * @since   1.2.0
	 */
	var $body = null;

	/**
	 * @var string CC email
	 *
	 * @since   1.2.0
	 */
	var $cc_email = null;

	/**
	 * @var string BCC email
	 *
	 * @since   1.2.0
	 */
	var $bcc_email = null;

	/**
	 * @var string Attachment
	 *
	 * @since   1.2.0
	 */
	var $attachment = null;

	/**
	 * @var string Reply-to email
	 *
	 * @since   1.2.0
	 */
	var $reply_email = null;

	/**
	 * @var string Reply-to name
	 *
	 * @since   1.2.0
	 */
	var $reply_name = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       1.2.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_tc_sendmailcontent', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access 	public
	 *
	 * @param array|object  $data       Named array or object
	 * @param 	string $ignore          Space separated list of fields not to bind
	 *
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
	 *
	 * @param   int  $id    ID
	 *
	 * @return 	boolean
	 *
	 * @since   1.2.0
	 */
	public function setSent($id)
	{
		if (!$id) return 0;
dump ($id, 'SetSent ID');

		$db = $this->getDbo();
		$query	= $db->getQuery(true);

		$query->update($this->_tbl);
		$query->set($db->quoteName('sent').' = '.$db->quote(1));
		$query->where($db->quoteName('id').' = '.(int) $id);
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	/**
	 * Overloaded load method
	 *
	 * @access	public
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.
	 *                           If not set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  bool    true on success
	 *
	 * @since   1.2.0
	 */
	public function load($keys = null, $reset = true)
	{
		if (!$keys) return 0;

		$mode	= JFactory::getApplication()->getUserState('bwtimecontrol.mode', false);
		if ($mode === false) return 0;

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from($this->_tbl);
		$query->where('id = ' . $db->quote($keys));
		$query->where('mode = ' . $db->quote($mode));
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
		return true;
	}
}
