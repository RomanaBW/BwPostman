<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register model for frontend.
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
 * Class BwPostmanModelRegister
 */
class BwPostmanModelRegister extends JModelAdmin
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type   	The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
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
		if ((!$user->authorise('core.edit.state', 'com_bwpostman')) &&  (!$user->authorise('core.edit', 'com_bwpostman'))){
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
	 * Method to get all mailinglists which the user is authorized to see
	 *
	 * @access 	public
	 *
	 * @return 	object  $mailinglists   mailinglists object
	 */
	public function getMailinglists()
	{
		$user 		= JFactory::getUser();
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		// get authorized viewlevels
		$accesslevels	= JAccess::getAuthorisedViewLevels($user->id);

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

		return $mailinglists;
	}

	/**
	 * Method to check by user ID if a user has a newsletter account (user = no guest)
	 *
	 * @access 	public
	 *
	 * @param 	int $uid    user ID
	 *
	 * @return 	int $id     subscriber ID
	 */
	public function getSubscriberID ($uid)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $uid);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery((string) $query);
		$id = $_db->loadResult();

		if (empty($id)) {
			$id = 0;
		}
		return $id;
	}

	/**
	 * Method to get the data of a subscriber who has a newsletter account from the subscribers-table
	 * because we need to know if his account is okay or archived or not activated (user = no guest)
	 *
	 * @access 	public
	 *
	 * @param 	int     $id         subscriber ID
	 *
	 * @return 	object  $subscriber subscriber object
	 */
	public function getSubscriberData ($id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$subscriber = $_db->loadObject();

		return $subscriber;
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
		$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=register'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		$_db->setQuery((string) $query);
		$itemid = $_db->loadResult();

		return $itemid;
	}

	/**
	 * Method to check by an input email address if a user has a newsletter account (user = no guest)
	 *
	 * @access 	public
	 *
	 * @param 	string $email   user email
	 *
	 * @return 	int     $uid    user ID
	 */
	public function isRegUser ($email)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__users'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->Quote($email));

		$_db->setQuery((string) $query);
		$uid = $_db->loadResult();

		if ($uid == NULL) $uid = 0;

		return $uid;
	}

	/**
	 * Method to check if an email address exists in the subscribers-table
	 *
	 * @access 	public
	 *
	 * @param 	string  $email  subscriber email
	 *
	 * @return 	int     $id     subscriber ID
	 */
	public function isRegSubscriber ($email)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->Quote($email));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$id = $_db->loadResult();

		return $id;
	}

	/**
	 * Method to save the subscriber data into the subscribers-table
	 * Sets editlink and activation code and checks if the data are valid
	 *
	 * @access 	public
	 *
	 * @param 	array   $data       associative array of data to store
	 *
	 * @return 	Boolean
	 *
	 * @since	1.0.1
	 */
	public function save($data)
	{
		jimport('joomla.user.helper');

		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		// Create the editlink and check if the sting doesn't exist twice or more
		$match_editlink = true;
		while ($match_editlink) {
			$data['editlink'] = JApplication::getHash(JUserHelper::genRandomPassword());

			$query->clear();
			$query->select($_db->quoteName('editlink'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('editlink') . ' = ' . $_db->Quote($data['editlink']));

			$_db->setQuery($query);

			$editlink = $_db->loadResult();

			if ($editlink == $data['editlink']) {
				$match_editlink = true;
			} else {
				$match_editlink = false;
			}
		}

		// Create the activation and check if the sting doesn't exist twice or more
		$match_activation = true;
		while ($match_activation) {
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());

			$query->clear();
			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->Quote($data['activation']));

			$_db->setQuery($query);

			$activation = $_db->loadResult();

			if ($activation == $data['activation']) {
				$match_activation = true;
			} else {
				$match_activation = false;
			}
		}

		$app->setUserState('com_bwpostman.subscriber.activation', $data['activation']);

		if (parent::save($data)) {

			// Get the subscriber id
			$subscriber_id	= $app->getUserState('com_bwpostman.subscriber.id');

			if (isset($data['mailinglists'])) if ($data['mailinglists'] != '') {
				$list_id_values = $data['mailinglists'];

			// Store subscribed mailinglists in subscribers_mailinglists-table
				foreach ($list_id_values AS $list_id) {
					$query->clear();
					$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
					$query->columns(array($_db->quoteName('subscriber_id'), $_db->quoteName('mailinglist_id')));
					$query->values($_db->Quote($subscriber_id) . ',' . (int) $list_id);

					$_db->setQuery((string) $query);

					if (!$_db->query()) {
						$app->enqueueMessage($_db->getErrorMsg(), 'error');
					}
				}
			}

			$data['activation'];

			return true;
		}
		else return false;
	}

	/**
	 * Method to delete a subscriber and the subscribed mailinglists
	 * --> is also called from the store method if a email is registered but archived by the user himself
	 *
	 * @access 	public
	 *
	 * @param 	int     $pks        subscriber ID
	 *
	 * @return 	Boolean
	 */
	public function delete(&$pks = null)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		if ($pks) {
			// delete subscriber from subscribers table
			$query->delete($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $pks);
			$_db->setQuery((string) $query);

			if ($_db->query()) {
				// delete subscriber entries from subscribers-lists table
				$query->clear();
				$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
				$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pks);
				$_db->setQuery($query);

				if (!$_db->query()) {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_DELETE_MAILINGLISTS'), 'warning');
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to activate the newsletteraccount of a subscriber
	 *
	 * @access 	public
	 *
	 * @param 	string  $activation     activation code for the newsletter account
	 * @param 	string $ret_err_msg     error message
	 * @param 	string $ret_editlink    editlink for editing the subscriber data
	 * @param 	string $activation_ip   IP used for activation
	 *
	 * @return 	Boolean
	 */
	public function activateSubscriber($activation, &$ret_err_msg, &$ret_editlink, $activation_ip)
	{
		$app	= JFactory::getApplication();
		$this->addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

		$_db			= $this->_db;
		$query			= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('email'));
		$query->select($_db->quoteName('editlink'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('activation') . ' = ' . $_db->Quote($activation));
		$query->where($_db->quoteName('status') . ' = ' . (int) 0);
		$query->where($_db->quoteName('confirmation_date') . ' = ' . $_db->Quote('0000-00-00 00:00:00'));
		$query->where($_db->quoteName('confirmed_by') . ' = ' . (int) -1);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->where($_db->quoteName('archived_by') . ' = ' . (int) -1);
		$_db->setQuery($query);

		if (!$_db->query()) {
			$app->enqueueMessage($_db->getErrorMsg(), 'error');
		}
		$subscriber = $_db->loadObject();

		if (isset($subscriber->editlink)) $ret_editlink = $subscriber->editlink;
		if (isset($subscriber->id)) $id = $subscriber->id;
//		if (isset($subscriber->email)) $email = $subscriber->email;

		// Is it a valid user to activate?
		if (!empty($id))
		{
			$date = JFactory::getDate();
			$time = $date->toSql();

			$query->clear();
			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('status') . ' = ' . (int) 1);
			$query->set($_db->quoteName('activation') . ' = ' . $_db->Quote(''));
			$query->set($_db->quoteName('confirmation_date') . ' = ' . $_db->Quote($time, false));
			$query->set($_db->quoteName('confirmed_by') . ' = ' . (int) 0);
			$query->set($_db->quoteName('confirmation_ip') . ' = ' . $_db->Quote($activation_ip));
			$query->where($_db->quoteName('id') . ' = ' . (int) $id);

			$_db->setQuery((string) $query);
			$_db->query();
		}
		else {
			// The activation code does not exist in the db
			$ret_err_msg = 'COM_BWPOSTMAN_ERROR_WRONGACTIVATIONCODE';
			return false;
		}

		return $subscriber->id;
	}

	/**
	 * Method to unsubscribe
	 * --> the subscriber data will be deleted
	 *
	 * @access	public
	 *
	 * @param 	string $editlink
	 * @param 	string $email
	 * @param 	string $ret_err_msg     error message
	 * @return 	Boolean
	 */
	public function unsubscribe ($editlink, $email, &$ret_err_msg)
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->Quote($email));
		$query->where($_db->quoteName('editlink') . ' = ' . $_db->Quote($editlink));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);
		$_db->setQuery((string) $query);

		if (!$_db->query()) {
			$app->enqueueMessage($_db->getErrorMsg(), 'error');
		}
		$id = $_db->loadResult();

		if ($id) {
			if ($this->delete($id)) {
				return true;
			}
			else {
				$ret_err_msg = 'COM_BWPOSTMAN_ERROR_UNSUBSCRIBE';
				return false;
			}
		}
		else {
			$ret_err_msg = 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
			return false;
		}
	}

	/**
	 * Method to fill void data
	 * --> the subscriber data filled with dafault values
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

	/**
	 * Method to send an information to webmaster, when a new subscriber activated the account
	 *
	 * @access 	public
	 *
	 * @param 	int		$subscriber_id      subscriber id
	 *
	 * @return 	Boolean
	 */
	public function sendActivationNotification($subscriber_id)
	{
		$app	= JFactory::getApplication();
		$mail	= JFactory::getMailer();
		$params = JComponentHelper::getParams('com_bwpostman');
		$from	= array();

		// set recipient and reply-to
		$from['mail']	= JMailHelper::cleanAddress($params->get('default_from_email'));
		$from['name']	= $params->get('default_from_name');
		$mail->setSender($from);
		$mail->AddReplyTo($from);

		// set recipient
		$recipient_mail	= JMailHelper::cleanAddress($params->get('activation_to_webmaster_email'));
		$recipient_name	= $params->get('activation_from_name');
		if (!is_string($recipient_mail)) $recipient_mail = $from['mail'];
		if (!is_string($recipient_name)) $recipient_name = $from['name'];
		$mail->addRecipient($recipient_mail, $recipient_name);

		// set subject
		$subject		= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION');
		$mail->setSubject($subject);

		// get body-data for mail and set body
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $subscriber_id);
		$_db->setQuery((string) $query);

		if (!$_db->query()) {
			$app->enqueueMessage($_db->getErrorMsg(), 'error');
		}
		$subscriber = $_db->loadObject();

		// Set registered by name
		if ($subscriber->registered_by == 0) {
			if ($subscriber->name != '') {
				$subscriber->registered_by	= $subscriber->name;
				if ($subscriber->firstname != '') {
					$subscriber->registered_by	.= ", " . $subscriber->firstname;
				}
			}
			else {
				$subscriber->registered_by = "User";
			}
		}
		else {
			$query_reg	= $_db->getQuery(true);
			$query_reg->select('name');
			$query_reg->from($_db->quoteName('#__users'));
			$query_reg->where($_db->quoteName('id') . ' = ' . (int) $subscriber->registered_by);
			$_db->setQuery((string) $query_reg);
			$subscriber->registered_by = $_db->loadResult();
		}

		// Set confirmed by name
		if ($subscriber->confirmed_by == 0) {
			if ($subscriber->name != '') {
				$subscriber->confirmed_by	= $subscriber->name;
				if ($subscriber->firstname != '') {
					$subscriber->confirmed_by	.= ", " . $subscriber->firstname;
				}
			}
			else {
				$subscriber->confirmed_by = "User";
			}
		}
		else {
			$query_conf	= $_db->getQuery(true);
			$query_conf->select('name');
			$query_conf->from($_db->quoteName('#__users'));
			$query_conf->where($_db->quoteName('id') . ' = ' . (int) $subscriber->confirmed_by);
			$_db->setQuery((string) $query_conf);
			$subscriber->confirmed_by = $_db->loadResult();
		}

		// Set body
		$body	= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT');
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_NAME') . $subscriber->name . "\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_FIRSTNAME') . $subscriber->firstname . "\n\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_EMAIL') . $subscriber->email . "\n\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_DATE') . $subscriber->registration_date . "\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_IP') . $subscriber->registration_ip . "\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_BY') . $subscriber->registered_by . "\n\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_DATE') . $subscriber->confirmation_date . "\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_IP') . $subscriber->confirmation_ip . "\n";
		$body	.= JText::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_BY') . $subscriber->confirmed_by . "\n";
		$mail->setBody($body);

		// Send the email
		$mail->Send();
	}
}
