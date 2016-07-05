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

require_once (JPATH_COMPONENT . '/helpers/subscriberhelper.php');


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

		if ($user->guest)
		{ // Subscriber is guest
			$session				= JFactory::getSession();
			$session_subscriberid	= $session->get('session_subscriberid');

			if(isset($session_subscriberid) && is_array($session_subscriberid))
			{ // Session contains subscriber ID
				$id	= $session_subscriberid['id'];
			}
		}
		else
		{ // Subscriber is user
			$id	= BwPostmanSubscriberHelper::getSubscriberId($user->get('id')); // Get the subscriber ID from the subscribers-table
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
		$jinput	= JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $jinput->getInt('id');
		$this->setState('subscriber.id', $pk);

		$offset = $jinput->getUint('limitstart');
		$this->setState('list.offset', $offset);

		// TODO: Tune these values based on other permissions.
		$user	= JFactory::getUser();
		if ((!$user->authorise('bwpm.edit.state', 'com_bwpostman')) &&  (!$user->authorise('bwpm.edit', 'com_bwpostman')))
		{
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
		JForm::addFieldPath('JPATH_COMPONENT/models/fields');

		// Get the form.
		$form = $this->loadForm('com_bwpostman.subscriber', 'subscriber', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput	= JFactory::getApplication()->input;
		$id		= $jinput->get('id', 0);
		$user	= JFactory::getUser();

		// Check for existing subscriber.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('bwpm.subscriber.edit.state', 'com_bwpostman.subscriber.'.(int) $id))
				|| ($id == 0 && !$user->authorise('bwpm.edit.state', 'com_bwpostman'))
			)
		{
			// Disable fields for display.
			$form->setFieldAttribute('status', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an subscriber you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Check for required name
		if (!$form->getValue('name_field_obligation'))
		{
			$form->setFieldAttribute('name', 'required', false);
		}

		// Check for required first name
		if ($form->getValue('firstname_field_obligation'))
		{
			$form->setFieldAttribute('firstname', 'required', true);
		}

		// Check to show confirmation data or checkbox
		$c_date	= strtotime($form->getValue('confirmation_date'));
		if (empty($c_date))
		{
			$form->setFieldAttribute('confirmation_date', 'type', 'hidden');
			$form->setFieldAttribute('confirmed_by', 'type', 'hidden');
			$form->setFieldAttribute('confirmation_ip', 'type', 'hidden');
		}
		else
		{
			$form->setFieldAttribute('status', 'type', 'hidden');
		}

		// Check to show registration data
		$r_date	= $form->getValue('registration_date');
		if (empty($r_date))
		{
			$form->setFieldAttribute('registration_date', 'type', 'hidden');
			$form->setFieldAttribute('registered_by', 'type', 'hidden');
			$form->setFieldAttribute('registration_ip', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

		return $form;
	}

	/**
	 * Method to reset the subscriber ID, view level and the subscriber data
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
	 * Method to get subscriber data.
	 *
	 * @param	int     $pk 	The id of the subscriber.
	 *
	 * @return	mixed	Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$app	        = JFactory::getApplication();
		$list_id_values = null;
		$_db	        = $this->_db;
		$query	        = $_db->getQuery(true);

		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $app->getUserState('subscriber.id');

		// Get subscriber data from subscribers table
		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $pk);

		try
		{
			$_db->setQuery($query);
			$this->_data	= $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// if no data get, take default values
		if (!is_object($this->_data))
		{
			$this->_data	= BwPostmanSubscriberHelper::fillVoidSubscriber();
		}

		// set id and mailinglists property
		$this->_id  = $pk;
		$this->_data->mailinglists  = $this->_getMailinglistsOfSubscriber($pk);

		return $this->_data;
	}

	/**
	 * Method to get the mail address of a subscriber from the subscribers-table depending on the subscriber ID
	 *
	 * @access 	public
	 *
	 * @param 	int		$id     subscriber ID
	 *
	 * @return 	string	user ID
	 */
	public function getEmailaddress($id)
	{
		$emailaddress   = null;
		$_db	        = $this->_db;
		$query	        = $_db->getQuery(true);

		$query->select($_db->quoteName('email'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);

		try
		{
			$_db->setQuery($query);
			$emailaddress = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

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
		$newActivation      = true;
		$existingActivation = true;
		$_db	            = $this->_db;
		$query	            = $_db->getQuery(true);

		// Create the activation and check if the sting doesn't exist twice or more
		$match_activation = true;
		while ($match_activation)
		{
			$newActivation = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			$query->clear();
			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->quote($newActivation));

			try
			{
				$_db->setQuery($query);
				$existingActivation = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (!$existingActivation == $newActivation) {
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
		$itemid = null;
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=edit'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		try
		{
			$_db->setQuery($query);
			$itemid = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (empty($itemid))
		{
			$query->clear();

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			try
			{
				$_db->setQuery($query);
				$itemid = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
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
		if ($editlink === null)
			return 0;

		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('editlink') . ' = ' . $_db->quote($editlink));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		try
		{
			$_db->setQuery($query);
			$id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

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
		parent::save($data);

		// Get the subscriber id
		$subscriber_id = $data['id'];

		// Delete all mailinglist entries for the subscriber_id from newsletters_mailinglists-table
		BwPostmanSubscriberHelper::deleteMailinglistsOfSubscriber($subscriber_id);

		// Store subscribed mailinglists in newsletters_mailinglists-table
		if (isset($data['mailinglists']))
		{
			if (($data['mailinglists']) != '') {
				BwPostmanSubscriberHelper::storeMailinglistsOfSubscriber($subscriber_id, $data['mailinglists']);
			}
		}
		return true;
	}

	/**
	 * Method to get associated mailing lists
	 *
	 * @param $pk
	 *
	 * @return mixed
	 */
	private function _getMailinglistsOfSubscriber($pk)
	{
		$list_id_values = null;
		$_db    = $this->_db;
		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pk);

		try
		{
			$_db->setQuery($query);

			$list_id_values = $_db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return $list_id_values;
	}
}
