<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit model for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
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

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

/**
 * Class BwPostmanModelEdit
 */
class BwPostmanModelEdit extends JModelAdmin
{

	/**
	 * Subscriber ID
	 *
	 * @var int
	 */
	var $_id;

	/**
	 * User ID in subscriber-table
	 *
	 * @var int
	 */
	var $_userid;

	/**
	 * Subscriber data
	 *
	 * @var array
	 */
	var $_data;

	/**
	 * Constructor
	 * Builds object, determines the subscriber ID and the viewlevel
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$user		= JFactory::getUser();
		$id			= 0;

		if ($user->guest) { // Subscriber is guest
			$session				= JFactory::getSession();
			$session_subscriberid	= $session->get('session_subscriberid');

			if(isset($session_subscriberid) && is_array($session_subscriberid)){ // Session contains subscriber ID
				$id	= $session_subscriberid['id'];
			}
		}
		else { // Subscriber is user
			$id	= $this->getSubscriberId($user->get('id')); // Get the subscriber ID from the subscribers-table
		}
		$this->setData($id);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string      $type       The table type to instantiate
	 * @param	string	    $prefix     A prefix for the table class name. Optional.
	 * @param	array	    $config     Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.0.1
	*/
	public function getTable($type = 'Subscribers', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.0.1
	 */
	protected function populateState()
	{
		$app	= JFactory::getApplication('site');
		$jinput	= JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $jinput->getInt('id');
		$this->setState('subscriber.id', $pk);

		$offset = $jinput->getUint('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
		if ((!$user->authorise('bwpm.edit.state', 'com_bwpostman')) &&  (!$user->authorise('bwpm.edit', 'com_bwpostman'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
	}

	/**
	 * Method to reset the subscriber ID, viewlevel and the subscriber data
	 *
	 * @access	public
	 * @param	int $id     subcriber ID
	 */
	protected function setData($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get article data.
	 *
	 * @param	int     $pk 	The id of the article.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $app->getUserState('subscriber.id');

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $pk);

//		echo nl2br(str_replace('#__','jos_',$query));
		$_db->setQuery($query);
		$this->_data	= $_db->loadObject();

		if (!is_object($this->_data)) $this->_data	= $this->fillVoidSubscriber();
		$this->_id		= $pk;

		$query->clear();
		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pk);

		$_db->setQuery($query);
		$list_id_values = $_db->loadColumn();
		$this->_data->mailinglists = $list_id_values;

		return $this->_data;
	}

	/**
	 * Method to get all mailinglists which the user is authorized to see
	 *
	 * @access 	public
	 * @return 	object Mailinglists
	 */
	public function getMailinglists()
	{
		$app		= JFactory::getApplication();
		$user_id	= self::getUserId($this->_id);
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		// get authorized viewlevels
			$accesslevels	= JAccess::getAuthorisedViewLevels($user_id);

		if (!in_array('3', $accesslevels)) {
			// A user shall only see mailinglists which are public or - if registered - accessible for his viewlevel and published
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->order($_db->quoteName('title') . 'ASC');
		}
		else {
			// A user with a super user status shall see all mailinglists
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->order($_db->quoteName('title') . 'ASC');
		}

		$_db->setQuery ($query);

		$mailinglists = $_db->loadObjectList();

		// Does the subscriber has internal mailinglists?
		$selected	= $app->getUserState('com_bwpostman.subscriber.selected_lists', '');

		if (is_array($selected)) {
			$ml_ids		= array();
			$add_mls	= array();

			// compare available mailinglists with selected mailinglists, get difference
			foreach ($mailinglists as $value) $ml_ids[]	= $value->id;
			$get_mls	= array_diff ($selected, $ml_ids);

			// if there are internal mailinglists selected, get them ...
			if (!empty($get_mls)) {
				$query->clear();
				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('id') . ' IN (' .implode(',', $get_mls) . ')');
				$query->order($_db->quoteName('title') . 'ASC');

				$_db->setQuery ($query);

				$add_mls = $_db->loadObjectList();
			}
		}
		// ...and add them to the mailinglists array
		if (!empty($add_mls)) $mailinglists	= array_merge($mailinglists, $add_mls);

		return $mailinglists;
	}

	/**
	 * Method to get the subscriber ID of a user from the subscribers-table depending on the user ID
	 * --> is needed for the construct
	 *
	 * @access 	public
	 *
	 * @param 	int     $uid    user ID
	 *
	 * @return 	int subscriber ID
	 */
	public function getSubscriberId($uid)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $uid);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$id = $_db->loadResult();

		if (empty($id)) $id = 0;

		return $id;
	}

	/**
	 * Method to get the user ID of a subsriber from the subscribers-table depending on the subscriber ID
	 * --> is needed for the constructor
	 *
	 * @access 	public
	 *
	 * @param 	int     $id     subscriber ID
	 *
	 * @return 	int user ID
	 */
	public function getUserId($id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('user_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$user_id = $_db->loadResult();

		return $user_id;
	}

	/**
	 * Method to get the mailaddress of a subsriber from the subscribers-table depending on the subscriber ID
	 *
	 * @access 	public
	 *
	 * @param 	int		$id     subscriber ID
	 *
	 * @return 	string	user ID
	 */
	public function getEmailaddress($id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('email'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);

		$_db->setQuery($query);

		$emailaddress = $_db->loadResult();

		return $emailaddress;
	}

	/**
	 * Method to get a unique activation string
	 *
	 * @access 	public
	 *
	 * @return 	string	$newActivation
	 */
	public function getActivation()
	{
		jimport('joomla.user.helper');
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		// Create the activation and check if the sting doesn't exist twice or more
		$match_activation = true;
		while ($match_activation) {
			$newActivation = JApplication::getHash(JUserHelper::genRandomPassword());

			$query->clear();
			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->Quote($newActivation));

			$_db->setQuery($query);
			$existingActivation = $_db->loadResult();

			if ($existingActivation == $newActivation) {
				$match_activation = true;
			} else {
				$match_activation = false;
			}
		}
		return $newActivation;
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @access	public
	 * @return 	int menu item ID
	 */
	public function getItemid()
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=edit'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		$_db->setQuery($query);
		$itemid = $_db->loadResult();

		if (empty($itemid)) {
			$query->clear();

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			$_db->setQuery($query);
			$itemid = $_db->loadResult();
		}
		return $itemid;
	}

	/**
	 * Checks if an editlink exists in the subscribers-table
	 *
	 * @access 	public
	 *
	 * @param 	string  $editlink   to edit the subscriber data
	 *
	 * @return 	int subscriber ID
	 */
	public function checkEditlink ($editlink)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('editlink') . ' = ' . $_db->Quote($editlink));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$id = $_db->loadResult();

		if (empty($id)) $id = 0;

		return $id;
	}

	/**
	 * Method to save the subscriber data
	 *
	 * @access 	public
	 *
	 * @param 	array   $data   associative array of data to store
	 *
	 * @return 	Boolean
	 *
	 * @since	1.0.1
	 */
	public function save($data)
	{
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		parent::save($data);

		// Get the subscriber id
		$subscriber_id = $data['id'];

		// Delete all mailinglist entries for the subscriber_id from newsletters_mailinglists-table
		$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' =  ' . (int) $subscriber_id);

		$_db->setQuery($query);
		$_db->execute();

		if (isset($data['mailinglists'])) {
			if (($data['mailinglists']) != '') {
				$list_id_values = $data['mailinglists'];

				// Store subscribed mailinglists in newsletters_mailinglists-table
				foreach ($list_id_values AS $list_id) {
					$query	= $_db->getQuery(true);

					$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
					$query->columns(array(
							$_db->quoteName('subscriber_id'),
							$_db->quoteName('mailinglist_id')
					));
					$query->values(
							(int) $subscriber_id . ',' .
							(int) $list_id
					);
					$_db->setQuery($query);
					$_db->execute();
				}
			}
		}
		return true;
	}

	/**
	 * Method to fill void data
	 * --> the subscriber data filled with default values
	 *
	 * @access	public
	 *
	 * @return 	object  $subscriber     subscriber object
	 */
	public function fillVoidSubscriber(){

		/* Load an empty subscriber */
		$subscriber = $this->getTable('subscribers', 'BwPostmanTable');
		$subscriber->load();

		return $subscriber;
	}
}
