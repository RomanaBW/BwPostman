<?php
/**
 * BwTimeControl Plugin for BwPostman Newsletter Component
 *
 * BwTimeControl automailing sendmail queue table for backend.
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
defined ('_JEXEC') or die ('Restricted access');

/**
 * #__bwpostman_tc_sendmailqueue table handler
 * Table for storing the recipients to whom a newsletter shall be send
 *
 * @package		BwPostman BwTimeControl Plugin
 *
 * @since   1.2.0
 */
class BwPostmanTableTc_Sendmailqueue extends JTable
{
	/**
	 * @var int Primary Key
	 *
	 * @since   1.2.0
	 */
	var $id = null;

	/**
	 * @var int AM-Content-ID --> from the am_sendmailcontent-Table
	 *
	 * @since   1.2.0
	 */
	var $tc_content_id = null;

	/**
	 * @var int Campaign-ID
	 *
	 * @since   1.2.0
	 */
	var $campaign_id = null;

	/**
	 * @var int mail number
	 *
	 * @since   1.2.0
	 */
	var $mail_number = null;

	/**
	 * @var datetime date and time of planned sending the mail
	 *
	 * @since   1.2.0
	 */
	var $sending_planned = null;

	/**
	 * @var int suspended
	 *
	 * @since   1.2.0
	 */
	var $suspended = null;

	/**
	 * @var datetime date and time of realized sending the mail
	 *
	 * @since   1.2.0
	 */
	var $sent_time = null;

	/**
	 * @var int
	 *
	 * @since   1.2.0
	 */
	var $trial = null;

	/**
	 * @var string Recipient email
	 *
	 * @since   1.2.0
	 */
	var $email = null;

	/**
	 * @var int Mode --> 0 = Text, 1 = HTML
	 *
	 * @since   1.2.0
	 */
	var $mode = null;

	/**
	 * @var string Recipient name
	 *
	 * @since   1.2.0
	 */
	var $name = null;

	/**
	 * @var string Recipient firstname
	 *
	 * @since   1.2.0
	 */
	var $firstname = null;

	/**
	 * @var int Subscriber ID
	 *
	 * @since       1.2.0
	 */
	var $subscriber_id = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       1.2.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_tc_sendmailqueue', 'id', $db);
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
	 * Method to get the first entry of this table
	 *
	 * @access 	public
	 *
	 * @param   int $trial      trial; Only pop entries with < trial
	 *
	 * @return 	int --> 0 if nothing was selected
	 *
	 * @since   1.2.0
	 */
	function pop($trial = 2)
	{
		$k		= $this->_tbl_key;
		$now	= JFactory::getDate('now')->toSql();
		$this->reset();
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from($this->_tbl);
		$query->where('trial < ' . $db->quote($trial));
		$query->where('suspended = ' . (int) 0);
		$query->where('sent_time = ' . $db->quote('0000-00-00 00:00:00'));
		$query->where('sending_planned <= ' . $db->quote($now));
		$query->order($k . ' ASC');
//		$query->limit(0, 1);
		$db->setQuery($query);

		$result = $db->loadObject();

		if ($result) {
			if ($this->bind($result)){
				$query->clear();
				$query->select($db->quoteName('status'));
				$query->from($db->quoteName('#__bwpostman_subscribers'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($result->subscriber_id));
				$db->setQuery((string) $query);
				$status = $db->loadResult();

				if ($status != 9) {
					// if not test mode, then set sent_time
					$this->_trackAssets = 0;
					$query->clear();
					$query->update($this->_tbl);
					$query->set('sent_time = ' . $db->quote($now));
					$query->where('id = ' . (int) $this->id);
					$db->setQuery((string) $query);
					if (!$db->execute()) {
						JError::raiseError(500, $db->getErrorMsg());
						return false;
					}
					return true;
				}
				else {
					$query->clear();
					$query->delete($this->_tbl);
					$query->where('id = ' . (int) $this->id);
					$db->setQuery((string) $query);

					if (!$db->query()) {
						JError::raiseError(500, $db->getErrorMsg());
						return FALSE;
					}
				}
			}
			else {
				$this->setError($db->getErrorMsg());
				return false;
			}
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Method to store a single recipient
	 *
	 * @access 	public
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
	 * @since   1.2.0
	 */
	function push($content_id, $emailformat, $email, $name, $firstname, $subscriber_id, $trial = 0)
	{
		$db = $this->getDbo();
		$query = "INSERT INTO {$this->_tbl} (tc_content_id,mode,email,name,firstname,subscriber_id,trial) VALUES ({$content_id},{$emailformat},'{$email}','{$name}','{$firstname}','{$subscriber_id}',{$trial})";
		$db->setQuery($query);
		if (!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to store all recipients when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param 	int     $nl_id          Newsletter-ID
	 * @param 	int     $content_id     Content ID --> from the sendmailcontent-Table
	 * @param 	int     $status         Status --> 0 = unconfirmed, 1 = confirmed
	 *
	 * @return 	boolean
	 *
	 * @since   1.2.0
	 */

	function pushAllFromNlId($nl_id, $content_id, $status){
		if (!$content_id) return false;
		$db =& $this->getDbo();
		$subQuery =  "SELECT {$content_id} AS content_id, a.email AS recipient, a.emailformat AS mode, a.name AS name, a.firstname AS firstname, a.id AS subscriber_id "
		." FROM {$db->quoteName('#__bwpostman_subscribers')} a "
		." WHERE a.id IN ("
		."               SELECT DISTINCT b.subscriber_id "
		."                 FROM {$db->quoteName('#__bwpostman_subscribers_mailinglists')} b"
		."                WHERE b.list_id IN ("
		."                                   SELECT c.list_id "
		."                                     FROM {$db->quoteName('#__bwpostman_newsletters_mailinglists')} c "
		."                                    WHERE c.newsletter_id IN ($nl_id) "
		."                                ) "
		."            ) "
		."   AND a.status IN ($status) "
		."   AND a.archive_flag = {$db->quote('0')}";
		$query = "INSERT INTO {$this->_tbl} (content_id,recipient,mode,name,firstname,subscriber_id) $subQuery";
		$db->setQuery($query);
		if (!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to store all subscribers when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param 	int     $content_id     Content ID --> --> from the sendmailcontent-Table
	 * @param 	int     $status         Status -->  0 = unconfirmed, 1 = confirmed, 9 = test-recipient
	 *
	 * @return 	boolean
	 *
	 * @since   1.2.0
	 */
	function pushAllSubscribers($content_id, $status){
		if (!$content_id) return false;
		$db =& $this->getDbo();
		$subQuery =  "SELECT {$content_id} AS content_id, email AS recipient, emailformat AS mode, name AS name, firstname AS firstname, id AS subscriber_id"
		."  FROM {$db->quoteName('#__bwpostman_subscribers')} "
		." WHERE {$db->quoteName('status')} IN ($status) "
		."   AND {$db->quoteName('archive_flag')} = {$db->quote('0')}";
		$query = "INSERT INTO {$this->_tbl} (content_id,recipient,mode,name,firstname,subscriber_id) $subQuery";
		$db->setQuery($query);
		if (!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to store all users when clicking the 'send' button
	 *
	 * @access	public
	 *
	 * @param 	int     $content_id         Content ID --> from the sendmailcontent-Table
	 * @param 	array   $users              Usergroups
	 * @param 	int     $format             Emailformat --> standard email format defined by BwPostman preferences
	 *
	 * @return 	boolean
	 *
	 * @since   1.2.0
	 */
	function pushJoomlaUser($content_id, $users, $format = 0){
		if (!$content_id) return false;
		if (!is_array($users)) return false;
		if (!count($users)) return false;
		$db =& $this->getDbo();

		foreach ($users as $user){
			$subQuery =  "SELECT {$content_id} AS content_id, email AS recipient, {$format} AS mode, name AS name, '0' AS subscriber_id"
			."  FROM {$db->quoteName('#__users')} "
			." WHERE {$db->quoteName('usertype')} like {$db->quote($user)} "
			."   AND {$db->quoteName('block')} = {$db->quote('0')} "
			."   AND {$db->quoteName('activation')} = {$db->quote('')} ";
			$query = "INSERT INTO {$this->_tbl} (content_id,recipient,mode,name,subscriber_id) $subQuery";
			$db->setQuery($query);
			if (!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Reset sending trials
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.2.0
	 */
	function resetTrials(){
		$db =& $this->getDbo();
		$query = "UPDATE {$this->_tbl} SET trial = 0 WHERE trial > 0";
		$db->setQuery($query);
		if (!$db->execute()){
			$this->setError($db->getErrorMsg());
			return false;
		}
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
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing mailing list
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New mailing list
			$this->created = $date->toSql();
			$this->created_by = $user->get('id');
		}
		$res	= parent::store($updateNulls);

		return $res;
	}
}
