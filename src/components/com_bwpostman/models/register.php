<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register model for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
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
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

require_once (JPATH_COMPONENT . '/helpers/subscriberhelper.php');


/**
 * Class BwPostmanModelRegister
 *
 * @since       0.9.1
 */
class BwPostmanModelRegister extends JModelAdmin
{
	/**
	 * Constructor
	 *
	 * @since       0.9.1
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
		$jinput	= JFactory::getApplication()->input;

		// Load state from the request.
		$pk = $jinput->getInt('id');
		$this->setState('subscriber.id', $pk);

		$offset = $jinput->getUint('limitstart');
		$this->setState('list.offset', $offset);

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_bwpostman')) &&  (!$user->authorise('core.edit', 'com_bwpostman')))
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
	 *
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @access	public
	 *
	 * @return 	int menu item ID
	 *
	 * @since       0.9.1
	 */
	public function getItemid()
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		$itemid = 0;

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);
		$_db->setQuery((string) $query);

		try
		{
			$itemid = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

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
	 *
	 * @since       0.9.1
	 */
	public function isRegUser ($email)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		$uid    = 0;

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__users'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($email));
		$_db->setQuery((string) $query);

		try
		{
			$uid = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
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
	 *
	 * @since       0.9.1
	 */
	public function isRegSubscriber ($email)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		$id     = 0;

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($email));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);
		$_db->setQuery($query);

		try
		{
			$id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
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

		// Create the editlink and check if the sting doesn't exist twice or more
		$data['editlink'] = $this->_createEditlink();

		// Create the activation and check if the sting doesn't exist twice or more
		$data['activation'] = $this->_createActivation();
		$app->setUserState('com_bwpostman.subscriber.activation', $data['activation']);

		if (parent::save($data))
		{
			// Get the subscriber id
			$subscriber_id	= $app->getUserState('com_bwpostman.subscriber.id');

			if (isset($data['mailinglists']))
			{
				if ($data['mailinglists'] != '')
				{
					BwPostmanSubscriberHelper::storeMailinglistsOfSubscriber($subscriber_id, $data['mailinglists']);
				}
			}

//			$data['activation'];

			return true;
		}
		else
			return false;
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
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks = null)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		if ($pks)
		{
			// delete subscriber from subscribers table
			$query->delete($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $pks);
			$_db->setQuery((string) $query);

			try
			{
				$_db->execute();
				// delete subscriber entries from subscribers-lists table
				BwPostmanSubscriberHelper::deleteMailinglistsOfSubscriber($pks);
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_DELETE_MAILINGLISTS'), 'warning');
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to activate the newsletter account of a subscriber
	 *
	 * @access 	public
	 *
	 * @param 	string  $activation     activation code for the newsletter account
	 * @param 	string $ret_err_msg     error message
	 * @param 	string $ret_editlink    editlink for editing the subscriber data
	 * @param 	string $activation_ip   IP used for activation
	 *
	 * @return 	Boolean
	 *
	 * @since       0.9.1
	 */
	public function activateSubscriber($activation, &$ret_err_msg, &$ret_editlink, $activation_ip)
	{
		$app	    = JFactory::getApplication();
		$subscriber = null;
		$this->addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('email'));
		$query->select($_db->quoteName('editlink'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('activation') . ' = ' . $_db->quote($activation));
		$query->where($_db->quoteName('status') . ' = ' . (int) 0);
		$query->where($_db->quoteName('confirmation_date') . ' = ' . $_db->quote('0000-00-00 00:00:00'));
		$query->where($_db->quoteName('confirmed_by') . ' = ' . (int) -1);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->where($_db->quoteName('archived_by') . ' = ' . (int) -1);

		try
		{
			$_db->setQuery($query);
			$subscriber = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if (isset($subscriber->editlink)) $ret_editlink = $subscriber->editlink;
		if (isset($subscriber->id)) $id = $subscriber->id;

		// Is it a valid user to activate?
		if (!empty($id))
		{
			$date = JFactory::getDate();
			$time = $date->toSql();

			$query->clear();
			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('status') . ' = ' . (int) 1);
			$query->set($_db->quoteName('activation') . ' = ' . $_db->quote(''));
			$query->set($_db->quoteName('confirmation_date') . ' = ' . $_db->quote($time, false));
			$query->set($_db->quoteName('confirmed_by') . ' = ' . (int) 0);
			$query->set($_db->quoteName('confirmation_ip') . ' = ' . $_db->quote($activation_ip));
			$query->where($_db->quoteName('id') . ' = ' . (int) $id);

			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		else
		{
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
	 *
	 * @return 	Boolean
	 *
	 * @since       0.9.1
	 */
	public function unsubscribe ($editlink, $email, &$ret_err_msg)
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		$id     = null;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->quote($email));
		$query->where($_db->quoteName('editlink') . ' = ' . $_db->quote($editlink));
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);
		$_db->setQuery((string) $query);

		try
		{
			$_db->setQuery($query);
			$id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($id)
		{
			if ($this->delete($id))
			{
				return true;
			}
			else
			{
				$ret_err_msg = 'COM_BWPOSTMAN_ERROR_UNSUBSCRIBE';
				return false;
			}
		}
		else
		{
			$ret_err_msg = 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
			return false;
		}
	}

	/**
	 * Method to send an information to webmaster, when a new subscriber activated the account
	 *
	 * @access 	public
	 *
	 * @param 	int		$subscriber_id      subscriber id
	 *
	 * @return 	Boolean
	 *
	 * @since       0.9.1
	 */
	public function sendActivationNotification($subscriber_id)
	{
		$app	    = JFactory::getApplication();
		$mail	    = JFactory::getMailer();
		$params     = JComponentHelper::getParams('com_bwpostman');
		$from	    = array();
		$subscriber = null;

		// set recipient and reply-to
		$from[0]	= JMailHelper::cleanAddress($params->get('default_from_email'));
		$from[1]	= $params->get('default_from_name');
		$mail->setSender($from);
		$mail->addReplyTo($from[0],$from[1]);

		// set recipient
		$recipient_mail	= JMailHelper::cleanAddress($params->get('activation_to_webmaster_email'));
		$recipient_name	= $params->get('activation_from_name');
		if (!is_string($recipient_mail)) $recipient_mail = $from[0];
		if (!is_string($recipient_name)) $recipient_name = $from[1];
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

		try
		{
			$_db->setQuery($query);
			$subscriber = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Set registered by name
		if ($subscriber->registered_by == 0)
		{
			if ($subscriber->name != '')
			{
				$subscriber->registered_by	= $subscriber->name;
				if ($subscriber->firstname != '')
				{
					$subscriber->registered_by	.= ", " . $subscriber->firstname;
				}
			}
			else
			{
				$subscriber->registered_by = "User";
			}
		}
		else
		{
			$query_reg	= $_db->getQuery(true);
			$query_reg->select('name');
			$query_reg->from($_db->quoteName('#__users'));
			$query_reg->where($_db->quoteName('id') . ' = ' . (int) $subscriber->registered_by);
			$_db->setQuery((string) $query_reg);

			try
			{
				$subscriber->registered_by = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}

		// Set confirmed by name
		if ($subscriber->confirmed_by == 0)
		{
			if ($subscriber->name != '')
			{
				$subscriber->confirmed_by	= $subscriber->name;
				if ($subscriber->firstname != '')
				{
					$subscriber->confirmed_by	.= ", " . $subscriber->firstname;
				}
			}
			else
			{
				$subscriber->confirmed_by = "User";
			}
		}
		else
		{
			$query_conf	= $_db->getQuery(true);
			$query_conf->select('name');
			$query_conf->from($_db->quoteName('#__users'));
			$query_conf->where($_db->quoteName('id') . ' = ' . (int) $subscriber->confirmed_by);
			$_db->setQuery((string) $query_conf);

			try
			{
				$subscriber->confirmed_by = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
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

	/**
	 * Method to create the activation and check if the sting does not exist twice or more
	 *
	 * @return string   $activation
	 *
	 * @since       0.9.1
	 */
	private function _createActivation()
	{
		// @ToDo: Move to helper class to get access by plugins
		$_db                = $this->_db;
		$query              = $_db->getQuery(true);
		$current_activation = null;
		$match_activation   = true;
		$activation         = '';

		while ($match_activation)
		{
			$current_activation = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->quote($current_activation));

			$_db->setQuery($query);

			try
			{
				$activation = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if ($activation == $current_activation)
			{
				$match_activation = true;
			}
			else
			{
				$match_activation = false;
			}
		}

		return $current_activation;
	}

	/**
	 * Method to create the editlink and check if the sting does not exist twice or more
	 *
	 * @return string   $editlink
	 *
	 * @since       0.9.1
	 */
	private function _createEditlink()
	{
		// @ToDo: Move to helper class to get access by plugins
		$_db                = $this->_db;
		$query              = $_db->getQuery(true);
		$current_editlink   = null;
		$match_editlink     = true;
		$editlink           = '';

		while ($match_editlink)
		{
			$current_editlink = JApplicationHelper::getHash(JUserHelper::genRandomPassword());

			$query->select($_db->quoteName('editlink'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('editlink') . ' = ' . $_db->quote($current_editlink));

			$_db->setQuery($query);

			try
			{
				$editlink = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if ($editlink == $current_editlink)
			{
				$match_editlink = true;
			}
			else
			{
				$match_editlink = false;
			}
		}

		return $current_editlink;
	}
}
