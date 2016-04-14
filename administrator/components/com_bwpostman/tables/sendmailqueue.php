<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman sendmail queue table for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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
 * #__bwpostman_sendmailqueue table handler
 * Table for storing the recipients to whom a newsletter shall be send
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanTableSendmailqueue extends JTable
{
	/** @var int Primary Key */
	var $id = null;

	/** @var int Content-ID --> from the sendmailcontent-Table */
	var $content_id = null;

	/** @var string Recipient email */
	var $recipient = null;

	/** @var int Mode --> 0 = Text, 1 = HTML */
	var $mode = null;

	/** @var string Recipient name */
	var $name = null;

	/** @var string Recipient firstname */
	var $firstname = null;

	/** @var int Subscriber ID */
	var $subscriber_id = null;

	/** @var int Number of delivery attempts */
	var $trial = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_sendmailqueue', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 * @param array|object  $data       Named array
	 * @param string        $ignore     Space separated list of fields not to bind
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
	 * Method to get the first entry of this table
	 *
	 * @access 	public
	 * @param   int     $trial  Only pop entries with < trial
	 *
	 * @return 	int --> 0 if nothing was selected
	 */
	public function pop($trial = 2)
	{
		$this->reset();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName($this->_tbl));
		$query->where($_db->quoteName('trial') . ' < ' . (int) $trial);
		$query->order($_db->quoteName($this->_tbl_key).' ASC LIMIT 0,1');

		$_db->setQuery($query);

		if ($result = $_db->loadAssoc()) {
			if ($this->bind($result)){
				$this->_trackAssets = 0;
				$this->delete($this->id);

				return true;
			}
		}
		else{
			$this->setError($_db->getErrorMsg());
			return false;
		}
		return $_db->getAffectedRows();
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
	 * @return 	boolean
	 */
	public function push($content_id, $emailformat, $email, $name, $firstname, $subscriber_id, $trial = 0)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->insert($_db->quoteName($this->_tbl));
		$query->columns(array(
			$_db->quoteName('content_id'),
			$_db->quoteName('mode'),
			$_db->quoteName('recipient'),
			$_db->quoteName('name'),
			$_db->quoteName('firstname'),
			$_db->quoteName('subscriber_id'),
			$_db->quoteName('trial')
		));
		$query->values(
			(int) $content_id . ',' .
			(int) $emailformat . ',' .
			$_db->quote($email) . ',' .
			$_db->quote($name) . ',' .
			$_db->quote($firstname) . ',' .
			(int) $subscriber_id . ',' .
			(int) $trial
		);
		$_db->setQuery($query);

		if (!$_db->query()){
			$this->setError($_db->getErrorMsg());
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
	 * @param	int		$cam_id         campaign id
	 *
	 * @return 	boolean
	 */

	public function pushAllFromNlId($nl_id, $content_id, $status, $cam_id){
		if (!$content_id) return false;

		$_db		= $this->_db;
		$subQuery1	= $_db->getQuery(true);
		$subQuery2	= $_db->getQuery(true);
		$subQuery3	= $_db->getQuery(true);
		$query		= $_db->getQuery(true);

		if ($cam_id != '-1') {
			$subQuery3->select($_db->quoteName('c') . '.' . $_db->quoteName('mailinglist_id'));
			$subQuery3->from($_db->quoteName('#__bwpostman_campaigns_mailinglists', 'c'));
			$subQuery3->where($_db->quoteName('c') . '.' . $_db->quoteName('campaign_id') . ' = ' . $cam_id);
		}
		else {
			$subQuery3->select($_db->quoteName('c') . '.' . $_db->quoteName('mailinglist_id'));
			$subQuery3->from($_db->quoteName('#__bwpostman_newsletters_mailinglists', 'c'));
			$subQuery3->where($_db->quoteName('c') . '.' . $_db->quoteName('newsletter_id') . ' IN (' . $nl_id . ')');
		}

		$subQuery2->select('DISTINCT' . $_db->quoteName('b') . '.' . $_db->quoteName('subscriber_id'));
		$subQuery2->from($_db->quoteName('#__bwpostman_subscribers_mailinglists', 'b'));
		$subQuery2->where($_db->quoteName('b') . '.' . $_db->quoteName('mailinglist_id') . ' IN (' . $subQuery3 . ')');

		$subQuery1->select($_db->Quote($content_id) . ' AS content_id');
		$subQuery1->select($_db->quoteName('a') . '.' . $_db->quoteName('email') . ' AS ' . $_db->quoteName('recipient'));
		$subQuery1->select($_db->quoteName('a') . '.' . $_db->quoteName('emailformat') . ' AS ' . $_db->quoteName('mode'));
		$subQuery1->select($_db->quoteName('a') . '.' . $_db->quoteName('name') . ' AS ' . $_db->quoteName('name'));
		$subQuery1->select($_db->quoteName('a') . '.' . $_db->quoteName('firstname') . ' AS ' . $_db->quoteName('firstname'));
		$subQuery1->select($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' AS ' . $_db->quoteName('subscriber_id'));
		$subQuery1->from($_db->quoteName('#__bwpostman_subscribers', 'a'));
		$subQuery1->where($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' IN (' . $subQuery2 . ')');
		$subQuery1->where($_db->quoteName('a') . '.' . $_db->quoteName('status') . ' IN (' . $status . ')');
		$subQuery1->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$query->insert($this->_tbl);
		$query .= ' (' .
				$_db->quoteName('content_id') . ',' .
				$_db->quoteName('recipient') . ',' .
				$_db->quoteName('mode') . ',' .
				$_db->quoteName('name') . ',' .
				$_db->quoteName('firstname') . ',' .
				$_db->quoteName('subscriber_id') .
		')';
		$query .=$subQuery1;

		$_db->setQuery($query);
		$res	= $_db->query();

		if (!$res){
			$this->setError($_db->getErrorMsg());
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
	 */
	public function pushAllSubscribers($content_id, $status) {
		if (!$content_id) return false;

		$_db		= $this->_db;
		$subQuery	= $_db->getQuery(true);
		$query		= $_db->getQuery(true);

		$subQuery->select($_db->Quote($content_id) . ' AS content_id');
		$subQuery->select($_db->quoteName('email', 'recipient'));
		$subQuery->select($_db->quoteName('emailformat', 'mode'));
		$subQuery->select($_db->quoteName('name', 'name'));
		$subQuery->select($_db->quoteName('firstname', 'firstname'));
		$subQuery->select($_db->quoteName('id', 'subscriber_id'));
		$subQuery->from($_db->quoteName('#__bwpostman_subscribers'));
		$subQuery->where($_db->quoteName('status') . ' IN (' . $status . ')');
		$subQuery->where($_db->quoteName('archive_flag') . ' = ' . $_db->Quote('0'));

		$query->insert($this->_tbl);
		$query->columns(array(
			$_db->quoteName('content_id'),
			$_db->quoteName('recipient'),
			$_db->quoteName('mode'),
			$_db->quoteName('name'),
			$_db->quoteName('firstname'),
			$_db->quoteName('subscriber_id')
		));
		$query->values($subQuery);

		if (!$_db->query()){
			$this->setError($_db->getErrorMsg());
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
	 */
	public function pushJoomlaUser($content_id, $usergroups, $format = 0){
		if (!$content_id) return false;
		if (!is_array($usergroups)) return false;
		if (!count($usergroups)) return false;

		$_db		= $this->_db;

		$subQuery	= $_db->getQuery(true);
		$subQuery1	= $_db->getQuery(true);

		$subQuery1->select($_db->quoteName('g') . '.' . $_db->quoteName('user_id'));
		$subQuery1->from($_db->quoteName('#__user_usergroup_map') . ' AS ' . $_db->quoteName('g'));
		$subQuery1->where($_db->quoteName('g') . '.' . $_db->quoteName('group_id') . ' IN (' . implode(',', $usergroups) . ')' );

		$subQuery->select($_db->Quote($content_id) . ' AS content_id');
		$subQuery->select($_db->quoteName('email', 'recipient'));
		$subQuery->select($_db->Quote($format) . ' AS mode');
		$subQuery->select($_db->quoteName('name', 'name'));
		$subQuery->select((int) 0 . ' AS subscriber_id');
		$subQuery->from($_db->quoteName('#__users'));
		$subQuery->where($_db->quoteName('block') . ' = ' . (int) 0);
		$subQuery->where($_db->quoteName('activation') . " IN ('', '0')");
		$subQuery->where($_db->quoteName('id') . ' IN (' . $subQuery1 . ')');

		$_db->setQuery($subQuery);
		$sub_res	= $_db->loadObjectList();

		foreach ($sub_res as $result){
			$query		= $_db->getQuery(true);

			$query->insert($_db->quoteName($this->_tbl));
			$query->columns(array(
				$_db->quoteName('content_id'),
				$_db->quoteName('recipient'),
				$_db->quoteName('mode'),
				$_db->quoteName('name'),
				$_db->quoteName('subscriber_id')
			));
			$query->values(
					$_db->Quote($result->content_id) . ', ' .
					$_db->Quote($result->recipient) . ', ' .
					$_db->Quote($result->mode) . ', ' .
					$_db->Quote($result->name) . ', ' .
					$_db->Quote($result->subscriber_id)
			);

			$_db->setQuery($query);
			if (!$_db->query()){
				$this->setError($_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to reset sending trials
	 *
	 * @return bool
	 */
	public function resetTrials(){
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName($this->_tbl));
		$query->set($_db->quoteName('trial') . " = " . (int) 0);
		$query->where($_db->quoteName('trial') . ' > ' . (int) 0);

		$_db->setQuery($query);

		if (!$_db->query()){
			$this->setError($_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
