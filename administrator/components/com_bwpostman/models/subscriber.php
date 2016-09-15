<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber model for backend.
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

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;
use Joomla\Registry\Registry as JRegistry;

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman subscriber model
 * Provides methods to add and edit subscribers/test-recipients
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class BwPostmanModelSubscriber extends JModelAdmin
{
	/**
	 * Subscriber/Test-recipient id
	 *
	 * @var int
	 *
	 * @since       0.9.1
	 */
	var $_id = null;

	/**
	 * Subscriber/Test-recipient data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	var $_data = null;

	/**
	 * Constructor
	 * Determines the subscriber/test-recipient ID
	 *
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '');
		$this->setId((int)$array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type	    The table type to instantiate
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
	 * Method to reset the subscriber/test-recipient ID and subscriber/test-recipient data
	 *
	 * @access	public
	 *
	 * @param	int     $id     Subscriber ID
	 *
	 * @since       0.9.1
	 */
	public function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since	2.0.0
	 */
	protected function canEditState($record)
	{
		$permission = BwPostmanHelper::canEditState('subscriber', $record->id);

		return $permission;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	$pk	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 *
	 * @since	1.0,1
	 */
	public function getItem($pk = null)
	{
		$app			= JFactory::getApplication();
		$cid			= $app->getUserState('com_bwpostman.edit.subscriber.id', 0);
		$data			= $app->getUserState('com_bwpostman.edit.subscriber.data', null);
		$mailinglists	= $app->getUserState('com_bwpostman.edit.subscriber.mailinglists', null);

		if (!$data)
		{
			// Initialise variables.
			if (is_array($cid))
			{
				if (!empty($cid))
				{
					$cid = $cid[0];
				}
				else
				{
					$cid = 0;
				}
			}
			if (empty($pk))
				$pk	= (int) $cid;

			$item	= parent::getItem($pk);

			// check permission
			if (!BwPostmanHelper::canEdit('subscriber', $item))
			{
				$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_EDIT_NO_PERMISSION'), 'error');
				return false;
			}

			$_db	= $this->_db;
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $item->id);

			$_db->setQuery($query);
			try
			{
				$item->list_id_values = $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		else
		{
			$item	= new stdClass();
			foreach ($data as $key => $value)
				$item->$key	= $value;
			if (is_array($mailinglists))
			{
				$item->list_id_values = $mailinglists;
			}
			else
			{
				$item->list_id_values = '';
			}
		}

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadJSON($item->params);
			$item->params = $registry->toArray();
		}
		$app->setUserState('com_bwpostman.edit.subscriber.data', null);
		$app->setUserState('com_bwpostman.edit.subscriber.mailinglists', null);

		return $item;
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
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_bwpostman/models/fields');

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
			|| ($id == 0 && !$user->authorise('bwpm.edit.state', 'com_bwpostman')))
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

		// check for new test-recipient
		if (JFactory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', '0') == '9') $form->setFieldAttribute('status', 'default', '1');

		return $form;
	}

	/**
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		// @todo XML-file will not be processed

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.subscriber.edit.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}

	/**
	 * Method to get the menu item ID which will be needed for the confirmation email links
	 *
	 * @access	public
	 *
	 * @return 	int menu item ID
	 *
	 * @since       0.9.1
	 */
	public function getItemid()
	{
		$itemid = '';
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id')  . ' AS ' . $_db->quote(''));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		$_db->setQuery($query);

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
	 * Method to create an editlink and to check whether the link already exists or not
	 *
	 * @access	public
	 *
	 * @return	string Editlink
	 *
	 * @since       0.9.1
	 */
	public function getEditlink()
	{
		jimport('joomla.user.helper');
		$_db		    = $this->_db;
		$editlink       = '';
		$new_editlink   = '';

		// Create the editlink and check if the string doesn't in the database
		$match_editlink = true;

		while ($match_editlink)
		{
			$new_editlink	= JApplicationHelper::getHash(JUserHelper::genRandomPassword());
			$query			= $_db->getQuery(true);

			$query->select($_db->quoteName('editlink'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('editlink') . ' = ' . $_db->quote($new_editlink));

			$_db->setQuery($query);

			try
			{
				$editlink = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (!($editlink == $new_editlink)) {
				$match_editlink = false;
			}
		}
		return $new_editlink;
	}

	/**
	 * Method to create an activation code and to check whether the code already exists or not
	 *
	 * @access	public
	 *
	 * @return	string Activation code
	 *
	 * @since       0.9.1
	 */
	public function getActivation()
	{
		$_db	= $this->_db;

		jimport('joomla.user.helper');

		// Create the activation and check if the string doesn't exist twice or more
		$match_activation   = true;
		$activation         = '';
		$new_activation     = '';

		while ($match_activation)
		{
			$new_activation = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
			$query		= $_db->getQuery(true);

			$query->select($_db->quoteName('activation'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('activation') . ' = ' . $_db->quote($new_activation));

			$_db->setQuery($query);
			try
			{
				$activation = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (!($activation == $new_activation)) {
				$match_activation = false;
			}
		}
		return $new_activation;
	}

	/**
	 * Method to store the subscriber data
	 *
	 * @access 	public
	 *
	 * @param 	array   $data   associative array of data to store
	 *
	 * @return 	boolean         True on success
	 *
	 * @since       0.9.1
	 */
	public function save ($data)
	{
		//initialize variables
		$app		= JFactory::getApplication();
		$date		= JFactory::getDate();
		$user		= JFactory::getUser();
		$result     = true;
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$subscriber	= new stdClass();

		try
		{
			// Get the user_id from the users-table
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__users'));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($data['email']));

			$_db->setQuery($query);
			$data['user_id'] = $_db->loadResult();

			// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
			if (isset($data['ml_available']))
			{
				foreach ($data['ml_available'] as $key => $value)
					$data['mailinglists'][] = $value;
			}
			if (isset($data['ml_unavailable']))
			{
				foreach ($data['ml_unavailable'] as $key => $value)
					$data['mailinglists'][] = $value;
			}
			if (isset($data['ml_intern']))
			{
				foreach ($data['ml_intern'] as $key => $value)
					$data['mailinglists'][] = $value;
			}

			// Admin creates a new subscriber?
			if (!$data['id'])
			{
				$data['editlink'] = $this->getEditlink();
			}

			// Admin creates a new subscriber and doesn't confirm the subscriber?
			if ((!array_key_exists('confirm', $data)) && (!$data['id']))
			{
				$data['activation'] = $this->getActivation();
			}

			$time      = $date->toSql();
			$confirmed = 0;

			// New subscriber
			if (empty($data['id']))
			{
				$data['registration_date'] = $time;
				$data['registered_by']     = $user->get('id');
				$data['registration_ip']   = $data['ip'];
				$new_subscriber            = true;

				// New subscriber is confirmed by administrator
				if ($data['status'] != '0')
				{
					$data['confirmation_date'] = $time;
					$data['confirmation_ip']   = $data['ip'];
					$data['confirmed_by']      = $user->get('id');
					$data['activation']        = '';
					$confirmed                 = 1;
				}
				else
				{
					$data['confirmed_by'] = -1;
					$confirmed            = 0;
				}
			}
			// Existing subscriber
			else
			{
				$new_subscriber = false;

				if ($data['status'] == '1')
				{ // Existing subscriber is confirmed by administrator  **** (2015-04-10) not possible, field confirmation is not displayed at existing subscribers
					//				$data['confirmation_date'] 	= $time;
					//				$data['confirmed_by']		= $user->get('id');
					//				$data['activation']			= '';
				}
			}

			if (parent::save($data))
			{
				// Get the subscriber ID
				$subscriber_id = $this->getState('subscriber.id');

				// Delete all entries of the subscriber from subscribers_mailinglists-Table
				$query = $_db->getQuery(true);

				$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
				$query->where($_db->quoteName('subscriber_id') . ' =  ' . (int) $subscriber_id);

				$_db->setQuery($query);
				$_db->execute();

				if (!empty($data['mailinglists']))
				{
					$list_id_values = $data['mailinglists'];

					// Store subscribed mailinglists in newsletters_mailinglists-table
					foreach ($list_id_values AS $list_id)
					{
						$query = $_db->getQuery(true);

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

				// New subscriber has to confirm the account by himself
				if (($new_subscriber) && (!$confirmed))
				{
					$subscriber->name       = $data['name'];
					$subscriber->firstname  = $data['firstname'];
					$subscriber->email      = $data['email'];
					$subscriber->activation = $data['activation'];

					// Send registration confirmation mail
					$itemid = '';//$this->getItemid();
					$res    = $this->_sendMail($subscriber, $itemid);

					if (!$res)
					{
						$app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_FAILED'), 'error');
						return false;
					}
				}
			}
			else
			{
				if (!empty($data['mailinglists']))
				{
					$app->setUserState('com_bwpostman.edit.subscriber.mailinglists', $data['mailinglists']);
				}
				$result = false;
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return $result;
	}


	/**
	 * Method to send an email
	 *
	 * @access	private
	 *
	 * @param 	object $subscriber      Subscriber
	 * @param	int $itemid             Menu item ID
	 *
	 * @return 	boolean True on success | error object
	 *
	 * @since       0.9.1
	 */
	private function _sendMail($subscriber, $itemid = null)
	{
		$app		= JFactory::getApplication();
		$mailer		= JFactory::getMailer();
		$siteURL	= JUri::root();

		$params 	= JComponentHelper::getParams('com_bwpostman');
		$sitename	= $app->get('sitename');
		$sender		= array();

		$sender[0]	= $params->get('default_from_email');
		$sender[1]	= $params->get('default_from_name');

		$reply		= array();
		$reply[0]	= $params->get('default_from_email');
		$reply[1]	= $params->get('default_from_name');


		$name 		= $subscriber->name;
		$firstname 	= $subscriber->firstname;
		if ($firstname != '')
			$name = $firstname . ' ' . $name; //Cat fo full name

		$subject 	= JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_SUBJECT', $sitename);

		if (is_null($itemid))
		{
			$body 	= JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG', $name, $siteURL, $siteURL."index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}");
		}
		else
		{
			$body 	= JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG', $name, $siteURL, $siteURL."index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}");
		}

		$subject	= html_entity_decode($subject, ENT_QUOTES);
		$body 		= html_entity_decode($body, ENT_QUOTES);

		// Fill in mailer data
		$mailer->setSender($sender);
		$mailer->addReplyTo($reply[0],$reply[1]);
		$mailer->addRecipient($subscriber->email);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->isHtml(false);

		$res = $mailer->Send();

		return $res;
	}

	/**
	 * Method to (un)archive a subscriber/test-recipient
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array   $cid        Subscriber/Test-recipient IDs
	 * @param	int     $archive    Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function archive($cid = array(), $archive = 1)
	{
		$app	= JFactory::getApplication();
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$_db	= $this->_db;

		if ($archive == 1)
		{
			$userid	= $user->get('id');

			// Access check.
			if (!BwPostmanHelper::canArchive('subscriber', $cid))
			{
				return false;
			}
		}
		else
		{ //
			$userid	= "-1";

			// Access check.
			if (!BwPostmanHelper::canRestore('subscriber', $cid)) {
				return false;
			}
		}

		if (count($cid))
		{
			ArrayHelper::toInteger($cid);
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('archive_flag') . " = " . (int) $archive);
			$query->set($_db->quoteName('archive_date') . " = " . $_db->quote($date));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $userid);
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');

			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		return true;
	}

	/**
	 * Method to remove one or more subscribers/test-recipients
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array $pks      Subscriber/Test-recipient IDs
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		// Access check.
		if (!BwPostmanHelper::canDelete('subscriber', $pks))
		{
			return false;
		}

		if (count($pks))
		{
			ArrayHelper::toInteger($pks);
			$_db	= $this->getDbo();

			// Delete subscriber from subscribers-table
			try
			{
				parent::delete($pks);
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// Delete subscribed mailinglists from subscribers_mailinglists-table
			$query = $_db->getQuery(true);
			$query->delete();
			$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where('subscriber_id IN (' .implode(',', $pks) . ')');
			$_db->setQuery($query);

			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());
			}
		}
		return true;
	}

	/**
	 * Method to validate one ore more email addresses
	 *
	 * @access	public
	 *
	 * @param	array   $cid            Subscriber/Test-recipient IDs
	 * @param   boolean $showProgress
	 *
	 * @return	array   $res            associative array of result data
	 *
	 * @since       0.9.1
	 */
	public function validate_mail($cid = array(0), $showProgress = false)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$config	= JFactory::getConfig();

		$validator = new emailValidation();

		$tmp = explode('@', $config->get('mailfrom'));
		$mailuser = $tmp[0];
		$mailserver = $tmp[1];

		$validator->timeout=5;
		$validator->data_timeout=0;
		$validator->localuser=$mailuser;
		$validator->localhost=$mailserver;
		$validator->debug=0;
		$validator->html_debug=1;
		$validator->exclude_address="";

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');
//		$query->order($_db->escape($listOrdering).' '.$listDirn);

		$_db->setQuery($query);
		$subscribers = $_db->loadObjectList();

		// After the validation process we want to show the results
		// --> therefore we store the results into an array
		$res = array();
		$i = 0;

		foreach($subscribers as $subscriber)
		{
			if ($showProgress)
			{
				echo "\n<br>{$subscriber->email} ... ";
				ob_flush();
				flush();
			}

			$res[$i]['id']			= $subscriber->id;
			$res[$i]['email']		= $subscriber->email;
			$res[$i]['name']		= $subscriber->name;
			$res[$i]['firstname']	= $subscriber->firstname;

			// Skip confirmed email address if they still passed the confirmation process and where identified as invalid
			if (strstr($subscriber->name, 'INVALID_'))
			{ // Skipped
				$res[$i]['result'] = 2;
				$res[$i]['result_txt'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATION_SKIPPED');
				$i++;
				if ($showProgress)
				{
					echo JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATION_SKIPPED');
					ob_flush();
					flush();
				}
				continue;
			}

			$result = $validator->ValidateEmailBox($subscriber->email);
			if($result === -1)
			{ // Unable to validate the address with this host
				$res[$i]['result'] = -1;
				$res[$i]['result_txt'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_HOST');
				$i++;
				if ($showProgress)
				{
					echo JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_HOST');
					ob_flush();
					flush();
				}

			}
			elseif($result)
			{ // The host is able to receive email. The address could be valid.
				$res[$i]['result'] = 1;
				$res[$i]['result_txt'] = JText::_('COM_BWPOSTMAN_SUB_VALIDATING_EMAIL');
				$i++;
				if ($showProgress)
				{
					echo JText::_('COM_BWPOSTMAN_SUB_VALIDATING_EMAIL');
					ob_flush();
					flush();
				}

				// Activate this account
				$date	= JFactory::getDate();
				$time	= $date->toSql();
				$user	= JFactory::getUser();
				$query	= $_db->getQuery(true);

				$query->update($_db->quoteName('#__bwpostman_subscribers'));
				$query->set($_db->quoteName('status') . " = " . (int) 1);
				$query->set($_db->quoteName('confirmation_date') . " = " . $_db->quote($time, false));
				$query->set($_db->quoteName('confirmed_by') . " = " . (int) $user->get('id'));
				$query->where($_db->quoteName('id') . ' = ' . (int) $subscriber->id);

				$_db->setQuery($query);

				try
				{
					$_db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage());
				}
			}
			else
			{ // The host can't receive email or this mailbox doesn't exist. The address is NOT valid.
				$res[$i]['result'] = 0;
				$res[$i]['result_txt'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL');
				$i++;
				if ($showProgress)
				{
					echo JText::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL');
					ob_flush();
					flush();
				}

				// Prepend an INVALID_ to the subscriber name
				$query	= $_db->getQuery(true);

				$query->update($_db->quoteName('#__bwpostman_subscribers'));
				$query->set($_db->quoteName('name') . " = " . $_db->quote('INVALID_'.$subscriber->name));
				$query->where($_db->quoteName('id') . ' = ' . (int) $subscriber->id);

				$_db->setQuery($query);

				try
				{
					$_db->execute();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage());
				}
			}
		}
		return $res;
	}

	/**
	 * Method to get the import data from the import file
	 *
	 * @access 	public
	 *
	 * @param 	array   $data           associative array of data which we need to prepare the storing to store
	 * @param	array   $ret_err        associative array of import error data
	 * @param	array   $ret_warn       associative array of import warning data
	 * @param 	array   $ret_maildata   associative array of subscriber email data --> we need this if the admin didn't confirm the accounts
	 *
	 * @return 	boolean true on success
	 *
	 * @since       0.9.1
	 */
	public function import($data, &$ret_err, &$ret_warn, &$ret_maildata)
	{
		// Access check
		if (!BwPostmanHelper::canAdd('subscriber'))
		{
			return false;
		}

		$app			= JFactory::getApplication();
		$session		= JFactory::getSession();
		$date			= JFactory::getDate();
		$time			= $date->toSql();
		$user			= JFactory::getUser();
		$mailinglists	= array();
		$ext            = '';
		$dest           = '';
		$delimiter      = '';
		$enclosure      = '"';
		$caption        = '';

		$import_general_data = $session->get('import_general_data');

		// Load the session data which are needed for import operation
		if(isset($import_general_data) && is_array($import_general_data))
		{
			if (isset ($import_general_data['caption']))	$caption	= stripcslashes($import_general_data['caption']);
			if (isset ($import_general_data['delimiter']))  $delimiter 	= stripcslashes($import_general_data['delimiter']);
			if (!empty ($import_general_data['enclosure'])) $enclosure	= stripcslashes($import_general_data['enclosure']);
			$dest 	= $import_general_data['dest'];
			$ext	= $import_general_data['ext'];
		}

		// Load the post data
		$import_fields 	= $data['import_fields'];
		$db_fields 		= $data['db_fields'];

		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		if (isset($data['jform']['ml_available']))
			foreach ($data['jform']['ml_available']		as $key => $value)
				$mailinglists[] 	= $value;
		if (isset($data['jform']['ml_unavailable']))
			foreach ($data['jform']['ml_unavailable']	as $key => $value)
				$mailinglists[] 	= $value;
		if (isset($data['jform']['ml_intern']))
			foreach ($data['jform']['ml_intern']		as $key => $value)
				$mailinglists[] 	= $value;

		if (isset($data['confirm']))
		{
			$confirm = true;
		}
		else
		{
			$confirm = false;
		}

		// We need some database-fields for subsequent checking of the values
		if (false === ($colEmail = array_search('email',$db_fields)))
		{
			// Couldn't find an email column --> return because we need the email for import
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_ERROR_NO_EMAIL_COLUMN'), 'error');
			return false;
		}

		if ($ext == 'csv')
		{
			// We only need the column numbers of the csv file which are coded like "column_#"
			for ($i = 0;$i < count($import_fields);$i++)
			{
				$tmp = explode("_",$import_fields[$i]);
				$import_fields[$i] = $tmp[1];
			}
		}

		// Create correlation of db fields and csv fields in form of $correlation table[csv_column_number] = db_column_name
		$colNumToDBName = array();
		for ($i = 0;$i < count($db_fields);$i++)
		{
			$colNumToDBName[$import_fields[$i]] = $db_fields[$i];
			$values[$db_fields[$i]] = 0;
		}

		// Add and initialize additional fields, depending of confirm-box value
		$values["id"]					= 0;
		$values["user_id"]				= 0;
		$values["registration_date"]	= $time;
		$values["registered_by"]		= $user->get('id');
		$values["registration_ip"]		= $_SERVER['REMOTE_ADDR'];
		$values["confirmation_date"]	= 0;
		$values["confirmation_ip"]		= 0;
		$values["confirmed_by"]			= -1;
		$values["editlink"]				= '';
		$values["activation"]			= '';
		$values["emailformat"]			= $data['emailformat'];
		if (isset ($data['confirm']))
		{
			$values["status"] = $data['confirm'];
		}
		else
		{
			$values["status"] = 0;
		}

		if (false === $fh = fopen($dest, 'r'))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_OPEN_FILE'), 'warning');
			return false;
		}
		else
		{
			// Error-Array --> 2dimensions: [err][csv_row], [err][email], [err][msg], [err][id]
			$err = array();
			// Warning-Array --> 2dimensions: [warn][csv_row], [warn][email], [warn][msg]
			$warn = array();
			// SendEmailActivation-Array --> 2dimensions [sendmail][csv_row], [sendmail][email]
			$mail = array();

			$row_nbr = 0;

			if ($ext == 'csv')
			{ // Format = csv
				JFactory::getApplication()->setUserState('com_bwpostman.subscriber.fileformat', 'csv');

				// Get data from the file and store them into an array
				while(($row = fgetcsv ($fh, '', $delimiter, $enclosure)) !== FALSE)
				{
					// Reset the import values. We should do this for every import row preventively.
					foreach($colNumToDBName as $key => $value)
						$values[$key] = 0;

					// Get the values from the csv
					foreach($colNumToDBName as $key => $value)
						$values[$value] = $row[$key];

					// Count CSV-file line numbers
					$row_nbr++;

					// If caption is set, don't read the first line of the csv-file
					if ($caption)
					{
						$caption = 0;
						continue;
					}

					// Save the row
					$this->save_import($values, $confirm, $row_nbr, $mailinglists, $ret_err, $ret_warn, $ret_maildata);

					// Push the error/mailing data into the arrays
					if ($ret_err)
					{
						$err[] = $ret_err;
					}
					if ($ret_warn)
					{
						foreach ($ret_warn AS $row_warn)
						{
							$warn[] = $row_warn;
						}
					}
					if ($ret_maildata)
					{
						$mail[] = $ret_maildata;
					}

				} // Endif format == csv

			}
			else
			{ // Format == xml
				JFactory::getApplication()->setUserState('com_bwpostman.subscriber.fileformat', 'xml');

				// Parse the XML
				$parser	= JFactory::getXml($dest);

				if ($parser->name()!= "subscribers")
				{
					// TODO: There is no bwpostman xml file! Perhaps one may proceed if there are appropriate fields
				}

				// Get all fields from the xml file for listing and selecting by the user
				$subscribers    = array();
				foreach ($parser->subscriber as $subscriber)
					$subscribers[]	= $subscriber;

				foreach ($subscribers as $subscriber)
				{
					$xml_fields	= get_object_vars($subscriber);

					// Reset the import values. We should do this for every import dataset preventively.
					foreach($colNumToDBName as $key => $value)
						$values[$key] = 0;

					// Get the values from the xml
					foreach($colNumToDBName as $key => $value)
						$values[$value] = $xml_fields[$key];

					// Count XML-dataset numbers
					$row_nbr++;

					// Save the data
					$this->save_import($values, $confirm, $row_nbr, $mailinglists, $ret_err, $ret_warn, $ret_maildata);

					// Push the error/mailing data into the arrays
					if ($ret_err)
					{
						$err[] = $ret_err;
					}
					if ($ret_warn)
					{
						foreach ($ret_warn AS $row_warn)
						{
							$warn[] = $row_warn;
						}
					}
					if ($ret_maildata)
					{
						$mail[] = $ret_maildata;
					}
				}
			}

			fclose($fh); // Close the file

			// Return the error/mailing data arrays
			$ret_err 	= $err;
			$ret_warn 	= $warn;
			$ret_maildata = $mail;

			return true;
		}
	}

	/**
	 * Method to save single import data set
	 *
	 * @access	public
	 *
	 * @param 	array   $values         associative array of data to store
	 * @param 	boolean $confirm        Confirm --> 0 = do not confirm, 1 = confirm
	 * @param 	int     $row            CSV row --> we will use this only if the format is csv
	 * @param	array   $mailinglists   array of mailinglist IDs
	 * @param	array   $ret_err        associative array of import error data
	 * @param	array   $ret_warn       associative array of import warning data
	 * @param	array   $ret_maildata   associative object of subscriber email data
	 *
	 * @return	Boolean true on success
	 *
	 * @since       0.9.1
	 */
	public function save_import($values, $confirm, $row, $mailinglists, &$ret_err, &$ret_warn, &$ret_maildata)
	{
		// Access check
		if (!BwPostmanHelper::canAdd('subscriber'))
		{
			return false;
		}

		jimport('joomla.mail.helper');
		$_db			= $this->_db;
		$query			= $_db->getQuery(true);
		$date			= JFactory::getDate();
		$time			= $date->toSql();
		$user			= JFactory::getUser();
		$ret_err		= '';
		$ret_warn		= '';
		$ret_maildata	= '';

		// Check if there is a valid email address
		if (!JMailHelper::isEmailAddress($values['email']))
		{
			$err['row'] = $row;
			$err['email'] = $values['email'];
			$err['msg'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_INVALID_EMAIL');
			$ret_err = $err;
			return false;
		}

		// We may set confirmation data if the confirm-box is checked and the import value does not stand against
		if ($confirm && $values['status'] != '0')
		{
			$values["confirmation_date"]	= $time;
			$values["confirmed_by"]			= $user->get('id');
			$values["confirmation_ip"]		= $_SERVER['REMOTE_ADDR'];
		}

		try
		{
			// Check if the email address exists in the database
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($values['email']));
			if ($values['status'] == '9')
			{
				$query->where($_db->quoteName('emailformat') . ' = ' . $_db->quote($values['emailformat']));
				$query->where($_db->quoteName('status') . ' = ' . (int) 9);
			}
			else
			{
				$query->where($_db->quoteName('status') . ' IN (0, 1)');
			}

			$_db->setQuery($query);
			$subscriber = $_db->loadObject();

			if (isset ($subscriber->id))
			{ // A recipient with this email address already exists
				if ($values['status'] != '9')
				{ // regular subscriber was found
					$err['row']   = $row;        // Get CSV row
					$err['email'] = $values['email'];
					if ($subscriber->archive_flag)
					{ // Subscriber already exists but is archived
						$err['msg'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTBLOCKED_BY_SYSTEM');
					}
					else
					{ // Subscriber already exists
						if ($subscriber->activation)
						{ // Account is not activated
							$err['msg'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTNOTACTIVATED');
						}
						else
						{
							$err['msg'] = JText::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTEXISTS');
						}
					}
					$err['id'] = $subscriber->id;
					$ret_err   = $err;

					return false;
				}
				else
				{ // a test-recipient with same emailformat was found
					// Check if the test-recipient in the database has the same emailformat like the one who shall be imported
					if ($subscriber->emailformat == $values['emailformat'])
					{
						$err['row']   = $row;        // Get CSV row
						$err['email'] = $values['email'];

						if ($subscriber->archive_flag == 1)
						{
							$err['msg'] = JText::_('COM_BWPOSTMAN_TEST_IMPORT_ERROR_ACCOUNTARCHIVED');
						}
						else
						{
							$err['msg'] = JText::_('COM_BWPOSTMAN_TEST_IMPORT_ERROR_ACCOUNTEXISTS');
						}

						$err['id'] = $subscriber->id;
						$ret_err   = $err;

						return false;
					}
				}
			}

			// Check for valid status value and set the status value
			if (($values['status'] != '0') && ($values['status'] != '1') && ($values['status'] != '9'))
			{ // Wrong status value
				$warn[0]['row']   = $row;
				$warn[0]['email'] = $values['email'];
				// Set the columns and values
				if ($confirm)
				{ // Status = 1
					if (empty($values['status']))
					{
						$warn[0]['msg'] = JText::_('COM_BWPOSTMAN_SUB_IMPORT_NO_STATUS_CONFIRMED');
					}
					else
					{
						$warn[0]['msg'] = JText::_('COM_BWPOSTMAN_SUB_IMPORT_INVALID_STATUS_CONFIRMED');
					}
				}
				else
				{ // Status = 0
					if (empty($values['status']))
					{
						$warn[0]['msg'] = JText::_('COM_BWPOSTMAN_SUB_IMPORT_NO_STATUS_UNCONFIRMED');
					}
					else
					{
						$warn[0]['msg'] = JText::_('COM_BWPOSTMAN_SUB_IMPORT_INVALID_STATUS_UNCONFIRMED');
					}
				}
				$values["status"] = $confirm;
			}

			if ($values['status'] == '0')
			{
				$values["activation"] = $this->getActivation();
			}

			// Check if the subscriber email address exists in the users-table
			$query = $_db->getQuery(true);

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__users'));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($values['email']));

			$_db->setQuery($query);
			$user_id = $_db->loadResult();

			$values["user_id"] = $user_id;
			if ($values["status"] != '9')
			{
				$values['editlink'] = $this->getEditlink();
			}

			if (parent::save($values))
			{
				$subscriber_id = $this->getState('subscriber.id');

				//Save Mailinglists if selected
				if ($mailinglists && ($values['status'] != '9'))
				{
					foreach ($mailinglists AS $list_id)
					{
						if (is_numeric($list_id))
						{ // We have to test this because IE doesn't accept the value "disabled"
							$query = $_db->getQuery(true);

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

				//Send Email, if confirmed is not set
				if ($values["status"] == 0)
				{
					$subscriber_emaildata = new stdClass();

					$subscriber_emaildata->row        = $row;
					$subscriber_emaildata->name       = $values["name"];
					$subscriber_emaildata->firstname  = $values["firstname"];
					$subscriber_emaildata->email      = $values["email"];
					$subscriber_emaildata->activation = $values["activation"];
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (isset($warn)) if ($warn)
		{
			$ret_warn = $warn;
		}
		else
		{
			$ret_warn = null;
		}

		if (isset($subscriber_emaildata)) if ($subscriber_emaildata)
		{
			$ret_maildata = $subscriber_emaildata;
		}
		else
		{
			$ret_maildata = null;
		}
		return true;
	}

	/**
	 * Method to export selected data
	 *
	 * @access	public
	 *
	 * @param 	array   $data       associative array of export option data
	 *
	 * @return 	string  $output     File content
	 *
	 * @since       0.9.1
	 */
	public function export($data)
	{
		// Access check
		if (!BwPostmanHelper::canManage())
		{
			return false;
		}

		$_db	= $this->_db;
		$output = '';

		$export_fields = $data['export_fields'];

		// Build the subQuery
		$subQuery = $this->_buildExportSubQuery(isset($data['status0']) ? $data['status0'] : '0', isset($data['status1']) ? $data['status1'] : '0', isset($data['status9']) ? $data['status9'] : '0', isset($data['archive0']) ? $data['archive0'] : '0', isset($data['archive1']) ? $data['archive1'] : '0');

		try
		{
			if ($data['fileformat'] == 'csv')
			{ // Fileformat = csv
				$delimiter = $data['delimiter'];
				$enclosure = $data['enclosure'];
				$newline   = "\n";

				$export_fields_tmp = array();
				foreach ($export_fields AS $export_field)
				{
					$export_fields_tmp[] = $enclosure . $export_field . $enclosure;
				}

				$output = implode($delimiter, $export_fields_tmp) . $newline;

				// Add DB-Quote to each export field
				foreach ($export_fields as $key => $value)
					$export_fields[$key] = $_db->quoteName($value);

				$export_fields_str = implode(",", $export_fields);

				// Build the query
				$query = $_db->getQuery(true);

				$query->select($export_fields_str);
				$query->from($_db->quoteName('#__bwpostman_subscribers'));
				$query .= $subQuery;

				$_db->setQuery($query);

				$query2 = "SELECT {$export_fields_str}
					FROM {$_db->quoteName('#__bwpostman_subscribers')}
					{$subQuery}";
				$_db->setQuery($query2);
				$subscribers_export = $_db->loadAssocList();

				foreach ($subscribers_export AS $subscriber)
				{
					$subscriber_export_tmp = array();
					foreach ($subscriber AS $subscriber_tmp)
					{
						// Insert enclosure
						$subscriber_export_tmp[] = $enclosure . $subscriber_tmp . $enclosure;
					}
					// Write file
					$output .= implode($delimiter, $subscriber_export_tmp) . $newline;
				}
			}
			else
			{ // Fileformat == xml
				$export_fields_str_xml = implode(", ", $export_fields);

				$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
							<!-- BwPostman export file -->
							<!DOCTYPE subscribers [
							<!ELEMENT subscribers (subscriber+)>
							<!ELEMENT subscriber (' . $export_fields_str_xml . ')>
							';

				foreach ($export_fields as $key => $value)
				{
					$output .= "<!ELEMENT {$value} (#PCDATA)>
								";
				}
				$output .= "]>\n<subscribers>\n";

				// Add DB-Quote to each export field
				$export_fields_tmp = array();
				foreach ($export_fields as $key => $value)
					$export_fields_tmp[$key] = $_db->quoteName($value);
				$export_fields_str = implode(",", $export_fields_tmp);

				// Build query
				$query = $_db->getQuery(true);

				$query->select($export_fields_str);
				$query->from($_db->quoteName('#__bwpostman_subscribers'));
				$query .= $subQuery;

				$_db->setQuery($query);

				$subscribers_export = $_db->loadAssocList();

				foreach ($subscribers_export AS $subscriber)
				{
					$output .= "	<subscriber>\n";
					foreach ($subscriber AS $key => $value)
					{
						$output .= "		<{$key}>{$value}</{$key}>\n";
					}
					$output .= "	</subscriber>\n";
				}
				$output .= "</subscribers>";
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return $output;
	}

	/**
	 * Method to build the WHERE-clause for the export function
	 *
	 * @access	private
	 *
	 * @param 	int     $status0    Status = 0 --> account is not confirmed
	 * @param 	int     $status1    Status = 1 --> account is confirmed
	 * @param 	int     $status9    Status = 9 --> subscriber is test-recipient
	 * @param 	int     $archive0   Archive_flag = 0 --> subscriber is not archived
	 * @param 	int     $archive1   Archive_flag = 1 --> subscriber is archived
	 *
	 * @return 	String  $subQuery   WHERE-clause
	 *
	 * @since       0.9.1
	 */
	private function _buildExportSubQuery($status0 = 0, $status1 = 0, $status9 = 0, $archive0 = 0, $archive1 = 0)
	{
		$_db		= $this->_db;
		$subQuery	= '';
		$where		= false;

		if ($status0 && $status1 && $status9)
		{
		}
		elseif ($status0 && $status1)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} != " . (int) 9;
			$where = true;
		}
		elseif ($status0 && $status9)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} != " . (int) 1;
			$where = true;
		}
		elseif ($status1 && $status9)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} != " . (int) 0;
			$where = true;
		}
		elseif ($status0)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} = " . (int) 0;
			$where = true;
		}
		elseif ($status1)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} = " . (int) 1;
			$where = true;
		}
		elseif ($status9)
		{
			$subQuery = " WHERE {$_db->quoteName('status')} = " . (int) 9;
			$where = true;
		}

		if ($archive0 && $archive1)
		{
		}
		elseif ($archive0)
		{
			if ($where)
			{
				$subQuery .= " AND {$_db->quoteName('archive_flag')} = " . (int) 0;
			}
			else
			{
				$subQuery = " WHERE {$_db->quoteName('archive_flag')} = " . (int) 0;
			}
		}
		elseif ($archive1)
		{
			if ($where)
			{
				$subQuery .= " AND {$_db->quoteName('archive_flag')} = " . (int) 1;
			}
			else
			{
				$subQuery = " WHERE {$_db->quoteName('archive_flag')} = " . (int) 1;
			}
		}
		return $subQuery;
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  		An array of commands to perform.
	 * @param   array  $pks       		An array of item ids.
	 * @param   array  $contexts		An array of contexts.
	 *
	 * @return  mixed  Returns array on success, false on failure.
	 *
	 * @since   1.0.8
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$old_list	= JFactory::getSession()->get('com_bwpostman.subscriber.batch_filter_mailinglist', null);
		$pks		= array_unique($pks);
		ArrayHelper::toInteger($pks);

		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JGLOBAL_NO_ITEM_SELECTED'), 'error');
			return false;
		}

		$done   = false;
		$result = array();

		if ($commands['mailinglist_id'] > 0)
		{
			$cmd = ArrayHelper::getValue($commands, 'batch-task', 'a');

			if ($cmd == 's')
			{
				$result[0] = $this->batchAdd($commands['mailinglist_id'], $pks);
				if (is_array($result)) {
					$done	= true;
				}
			}
			if ($cmd == 'u')
			{
				$result[0] = $this->batchRemove($commands['mailinglist_id'], $pks);
				if (is_array($result))
				{
					$done	= true;
				}
			}
			if ($cmd == 'm' && $old_list)
			{
				if ($commands['mailinglist_id'] != $old_list)
				{
					$result[0] = $this->batchAdd($commands['mailinglist_id'], $pks);
					$result[1] = $this->batchRemove($old_list, $pks);
					if (is_array($result[0]) && is_array($result[1]))
					{
						$done	= true;
					}
					elseif (is_array($result[0]))
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_BATCH_RESULT_ERROR_MOVE_REMOVE'), 'error');
						return false;
					}
					else
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_BATCH_RESULT_ERROR_MOVE_ADD'), 'error');
						return false;
					}
				}
				else
				{
					return (int) -$old_list;
				}
			}
		}

		if (!$done)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache unset Session variable
		$this->cleanCache();
		JFactory::getSession()->clear('com_bwpostman.subscriber.batch_filter_mailinglist');

		return $result;
	}

	/**
	 * Batch add subscribers to a new mailinglist.
	 *
	 * @param   integer  $mailinglist   The new mailinglist.
	 * @param   array    $pks       	An array of row IDs.
	 *
	 * @return  mixed  An array of result values on success, boolean false on failure.
	 *
	 * @since	1.0.8
	 */
	protected function batchAdd($mailinglist, $pks)
	{
		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		$_db		= $this->getDbo();
		$result_set	= array();
		$subscribed	= 0;
		$skipped	= 0;

		// Subscribers exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first id off the stack
			$pk     = array_shift($pks);
			$result = 0;

			// Check if subscriber has already subscribed to the desired mailinglist
			$query = $_db->getQuery(true);
			$query->select($_db->quoteName('subscriber_id'));
			$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pk);
			$query->where($_db->quoteName('mailinglist_id') . ' = ' . (int) $mailinglist);
			$_db->setQuery($query);

			try
			{
				$result = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// If no subscription to this mailinglist then subscribe, else only count
			if (!$result)
			{
				$query  = $_db->getQuery(true);
				$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
				$query->columns(array(
					$_db->quoteName('subscriber_id'),
					$_db->quoteName('mailinglist_id')
					));
					$query->values(
					(int) $pk . ',' .
					(int) $mailinglist
					);
				$_db->setQuery($query);

				try
				{
					$_db->execute();
					$subscribed++;
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
			else
			{
				$skipped++;
			}
		}
		$result_set['task']		= 'subscribe';
		$result_set['done']		= $subscribed;
		$result_set['skipped']	= $skipped;

		return $result_set;
	}

	/**
	 * Batch unsubscribe subscribers from a mailinglist.
	 *
	 * @param   integer  $mailinglist	The mailinglist ID.
	 * @param   array    $pks       	An array of row IDs.
	 *
	 * @return  array|bool
	 *
	 * @since   1.0.8
	 */
	protected function batchRemove($mailinglist, $pks)
	{
		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		$_db			= $this->getDbo();
		$result_set		= array();
		$unsubscribed	= 0;
		$skipped		= 0;

		// Subscribers exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first id off the stack
			$pk     = array_shift($pks);
			$result = 0;

			// Check if subscriber has already subscribed to the desired mailinglist
			$query = $_db->getQuery(true);
			$query->select($_db->quoteName('subscriber_id'));
			$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pk);
			$query->where($_db->quoteName('mailinglist_id') . ' = ' . (int) $mailinglist);
			$_db->setQuery($query);

			try
			{
				$result = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// If subscription to this mailinglist, then unsubscribe, else only count
			if ($result)
			{
				$query = $_db->getQuery(true);
				$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
				$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $pk);
				$query->where($_db->quoteName('mailinglist_id') . ' = ' . (int) $mailinglist);
				$_db->setQuery($query);

				try
				{
					$_db->execute();
					$unsubscribed++;
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
			else
			{
				$skipped++;
			}

		}
		$result_set['task']		= 'unsubscribe';
		$result_set['done']		= $unsubscribed;
		$result_set['skipped']	= $skipped;

		return $result_set;
	}
}

/**
 * Email Validation
 * Provides methods to validate the e-mail-addresses of subscriber
 * --> Carried over from the previous BwPostman version without any changes

 * @package		BwPostman-Admin
 *
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class emailValidation
{
	/**
	 * property to hold regular expressions for email check
	 *
	 * @var string $email_regular_expression
	 *
	 * @since       0.9.1
	 */
	var $email_regular_expression="^([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}\$";

	/**
	 * property to hold timeout
	 *
	 * @var int $timeout
	 *
	 * @since       0.9.1
	 */
	var $timeout=0;

	/**
	 * property to hold data timeout
	 *
	 * @var int $data_timeout
	 *
	 * @since       0.9.1
	 */
	var $data_timeout=0;

	/**
	 * property to hold localhost
	 *
	 * @var string  $localhost
	 *
	 * @since       0.9.1
	 */
	var $localhost="";

	/**
	 * property to hold local user
	 *
	 * @var string $localuser
	 *
	 * @since       0.9.1
	 */
	var $localuser="";

	/**
	 * property to hold debug mode
	 *
	 * @var int $debug
	 *
	 * @since       0.9.1
	 */
	var $debug=0;

	/**
	 * property to hold html debug mode
	 *
	 * @var int $html_debug
	 *
	 * @since       0.9.1
	 */
	var $html_debug=0;

	/**
	 * property to hold exclude mail address
	 *
	 * @var string
	 *
	 * @since       0.9.1
	 */
	var $exclude_address="";

	/**
	 * property to hold MXRR
	 *
	 * @var string  $getmxrr
	 *
	 * @since       0.9.1
	 */
	var $getmxrr="GetMXRR";

	/**
	 * property to hold next token
	 *
	 * @var string  $next_token
	 *
	 * @since       0.9.1
	 */
	var $next_token="";

	/**
	 * property to hold preg
	 *
	 * @var string  $preg
	 *
	 * @since       0.9.1
	 */
	var $preg;

	/**
	 * property to hold last code
	 *
	 * @var string  $last_code
	 *
	 * @since       0.9.1
	 */
	var $last_code="";

	/**
	 * Method to tokenize
	 *
	 * @param string    $string
	 * @param string    $separator
	 *
	 * @return string
	 *
	 * @since       0.9.1
	 */
	public function Tokenize($string, $separator="")
	{
		if(!strcmp($separator,""))
		{
			$separator=$string;
			$string=$this->next_token;
		}
		for($character=0;$character<strlen($separator);$character++)
		{
			if(gettype($position=strpos($string,$separator[$character]))=="integer")
				$found=(IsSet($found) ? min($found,$position) : $position);
		}
		if(IsSet($found))
		{
			$this->next_token=substr($string,$found+1);
			return(substr($string,0,$found));
		}
		else
		{
			$this->next_token="";
			return($string);
		}
	}

	/**
	 * Method to debug output
	 *
	 * @param string    $message
	 *
	 * @since       0.9.1
	 */
	public function OutputDebug($message)
	{
		$message.="\n";
		if($this->html_debug)
			$message=str_replace("\n","<br />\n",htmlentities($message));
		echo $message;
		flush();
	}

	/**
	 * Method to get line
	 *
	 * @param resource  $connection
	 *
	 * @return int|string
	 *
	 * @since       0.9.1
	 */
	public function GetLine($connection)
	{
		for($line="";;)
		{
			if(feof($connection))
				return(0);
			$line.=fgets($connection,100);
			$length=strlen($line);
			if($length>=2
				&& substr($line,$length-2,2)=="\r\n")
			{
				$line=substr($line,0,$length-2);
				if($this->debug)
					$this->OutputDebug("S $line");
				return($line);
			}
		}
		return(0);
	}

	/**
	 * Method to put line
	 *
	 * @param resource  $connection
	 * @param string    $line
	 *
	 * @return int
	 *
	 * @since       0.9.1
	 */
	public function PutLine($connection,$line)
	{
		if($this->debug)
			$this->OutputDebug("C $line");
		return(fputs($connection,"$line\r\n"));
	}

	/**
	 * Method to validate email address
	 *
	 * @param string    $email
	 *
	 * @return bool|int
	 *
	 * @since       0.9.1
	 */
	public function ValidateEmailAddress($email)
	{
		if(IsSet($this->preg))
		{
			if(strlen($this->preg))
				return(preg_match($this->preg,$email));
		}
		else
		{
			$this->preg=(function_exists("preg_match") ? "/".str_replace("/", "\\/", $this->email_regular_expression)."/" : "");
			return($this->ValidateEmailAddress($email));
		}
		return(preg_match("/$this->email_regular_expression/i",$email)!=0);
	}

	/**
	 * Method to validate email host
	 *
	 * @param string    $email
	 * @param string    $hosts
	 *
	 * @return bool|int
	 *
	 * @since       0.9.1
	 */
	public function ValidateEmailHost($email,&$hosts)
	{
		if(!$this->ValidateEmailAddress($email))
			return(0);
		$domain=$this->Tokenize("");
		$hosts=$weights=array();
		$getmxrr=$this->getmxrr;
		if(function_exists($getmxrr)
			&& $getmxrr($domain,$hosts,$weights))
		{
			$mxhosts=array();
			for($host=0;$host<count($hosts);$host++)
				$mxhosts[$weights[$host]]=$hosts[$host];
			ksort($mxhosts);
			for(reset($mxhosts),$host=0;$host<count($mxhosts);next($mxhosts),$host++)
				$hosts[$host]=$mxhosts[key($mxhosts)];
		}
		else
		{
			if(strcmp($ip=@gethostbyname($domain),$domain)
				&& (strlen($this->exclude_address)==0
				|| strcmp(@gethostbyname($this->exclude_address),$ip)))
				$hosts[]=$domain;
		}
		return(count($hosts)!=0);
	}

	/**
	 * Method to verify result
	 *
	 * @param resource  $connection
	 * @param string    $code
	 *
	 * @return int
	 *
	 * @since       0.9.1
	 */
	public function VerifyResultLines($connection,$code)
	{
		while(($line=$this->GetLine($connection)))
		{
			$this->last_code=$this->Tokenize($line," -");
			if(strcmp($this->last_code,$code))
				return(0);
			if(!strcmp(substr($line, strlen($this->last_code), 1)," "))
				return(1);
		}
		return(-1);
	}

	/**
	 * Method to validate email box
	 *
	 * @param string    $email
	 *
	 * @return bool|int
	 *
	 * @since       0.9.1
	 */
	public function ValidateEmailBox($email)
	{
		if(!$this->ValidateEmailHost($email,$hosts))
			return(0);
		if(!strcmp($localhost=$this->localhost,"")
			&& !strcmp($localhost=getenv("SERVER_NAME"),"")
			&& !strcmp($localhost=getenv("HOST"),""))
			$localhost="localhost";
		if(!strcmp($localuser=$this->localuser,"")
			&& !strcmp($localuser=getenv("USERNAME"),"")
			&& !strcmp($localuser=getenv("USER"),""))
			$localuser="root";
		for($host=0;$host<count($hosts);$host++)
		{
			$domain=$hosts[$host];
			if(preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i',$domain))
				$ip=$domain;
			else
			{
				if($this->debug)
					$this->OutputDebug("Resolving host name \"".$hosts[$host]."\"...");
				if(!strcmp($ip=@gethostbyname($domain),$domain))
				{
					if($this->debug)
						$this->OutputDebug("Could not resolve host name \"".$hosts[$host]."\".");
					continue;
				}
			}
			if(strlen($this->exclude_address)
				&& !strcmp(@gethostbyname($this->exclude_address),$ip))
			{
				if($this->debug)
					$this->OutputDebug("Host address of \"".$hosts[$host]."\" is the exclude address");
				continue;
			}
			if($this->debug)
				$this->OutputDebug("Connecting to host address \"".$ip."\"...");
			if(($connection=($this->timeout ? @fsockopen($ip,25,$errno,$error,$this->timeout) : @fsockopen($ip,25))))
			{
				$timeout=($this->data_timeout ? $this->data_timeout : $this->timeout);
				if($timeout
					&& function_exists("socket_set_timeout"))
					socket_set_timeout($connection,$timeout,0);
				if($this->debug)
					$this->OutputDebug("Connected.");
				if($this->VerifyResultLines($connection,"220")>0
					&& $this->PutLine($connection,"HELO $localhost")
					&& $this->VerifyResultLines($connection,"250")>0
					&& $this->PutLine($connection,"MAIL FROM: <$localuser@$localhost>")
					&& $this->VerifyResultLines($connection,"250")>0
					&& $this->PutLine($connection,"RCPT TO: <$email>")
					&& ($result=$this->VerifyResultLines($connection,"250"))>=0)
				{
					if($result)
					{
						if($this->PutLine($connection,"DATA"))
							$result=($this->VerifyResultLines($connection,"354")!=0);
					}
					else
					{
						if(strlen($this->last_code)
							&& !strcmp($this->last_code[0],"4"))
							$result=-1;
					}
					if($this->debug)
						$this->OutputDebug("This host states that the address is ".($result ? ($result>0 ? "valid" : "undetermined") : "not valid").".");
					fclose($connection);
					if($this->debug)
						$this->OutputDebug("Disconnected.");
					return($result);
				}
				if($this->debug)
					$this->OutputDebug("Unable to validate the address with this host.");
				fclose($connection);
				if($this->debug)
					$this->OutputDebug("Disconnected.");
			}
			else
			{
				if($this->debug)
					$this->OutputDebug("Failed.");
			}
		}
		return(-1);
	}
}
