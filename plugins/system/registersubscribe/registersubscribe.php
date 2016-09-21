<?php
/**
 * BwPostman RegisterSubscribe Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman RegisterSubscribe Plugin main file for BwPostman.
 *
 * @version 2.0.0 bwpmprs
 * @package			BwPostman RegisterSubscribe Plugin
 * @author			Romana Boldt
 * @copyright		(C) 2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
 * @license			GNU/GPL v3, see LICENSE.txt
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

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

/**
 * Class RegisterSubscribe
 *
 * @since  2.0.0
 */
class PlgSystemRegisterSubscribe extends JPlugin
{
	/**
	 * Load the language file on instantiation (for Joomla! 3.X only)
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Definition of which contexts to allow in this plugin
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	protected $allowedContext = array(
		'com_users.registration',
	);

	/**
	 * Event method onUserAfterSave
	 *
	 * @param   array   $data       User data
	 * @param   bool    $isNew      true on new user
	 * @param   bool    $result     result of saving user
	 * @param   string  $error      error message translated by JText()
	 *
	 * @return  bool
	 *
	 * @since  2.0.0
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if ($result == false)
		{
			return false;
		}

		// Sanitize data
		$user_mail   = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id     = ArrayHelper::getValue($data, 'id', 0, 'int');
		$user_name   = ArrayHelper::getValue($data, 'name', '', 'string');
		$mailformat  = ArrayHelper::getValue($data, 'registerSubscribe_selected_mailformat', 1, 'int');

		if ($isNew)
		{
			if ($this->_hasSubscription($user_mail))
			{
				$this->_updateUserIdAtSubscribers($user_mail, $user_id);
				return true;
			}

			$res = $this->_subscribeToBwPostman($user_mail, $user_id, $user_name, $mailformat);

			return $res;
		}

		if (ArrayHelper::getValue($data, 'activation', '', 'string') != '')
		{
			$res    = $this->_activateSubscription($user_id);

			return $res;
		}

		if ($this->params->get('auto_update_email_option') === true)
		{
			$subscriber = $this->_getSubscriptionData($user_id);

			if (is_array($subscriber) && ($subscriber['email']) != $user_mail)
			{
				$subscriber['email']    = $user_mail;
				$res                    = $this->_updateEmailOfSubscription($subscriber);
				return $res;
			}
		}

		return true;
	}

	/**
	 * Event method onContentPrepareForm
	 *
	 * @param   mixed  $form  JForm instance
	 * @param   array  $data  Form values
	 *
	 * @return  bool
	 *
	 * @since  2.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		$context = $form->getName();

		if (!in_array($context, $this->allowedContext))
		{
			return true;
		}

		JForm::addFormPath(__DIR__ . '/form');
		$form->loadFile('form', false);

		if (!$this->params->get('show_format_selection_option'))
		{
			$form->setFieldAttribute('registerSubscribe_selected_mailformat', 'type', 'hidden', 'registerSubscribe');
		}

		if ($this->params->get('register_message_option') != '')
		{
			$form->setFieldAttribute('registerSubscribe', 'description', $this->params->get('register_message_option'), 'registerSubscribe');
		}

		return true;
	}

	/**
	 * Event method onUserAfterDelete
	 *
	 * @param   array   $data     Data that was being deleted
	 * @param   bool    $success  Flag to indicate whether deletion was successful
	 * @param   string  $msg      Message after deletion
	 *
	 * @return  null
	 *
	 * @since  2.0.0
	 */
	public function onUserAfterDelete($data, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$res    = true;

		if ($this->params->get('auto_delete_option') === true)
		{
			$res = $this->_deleteSubscription(ArrayHelper::getValue($data, 'id', 0, 'int'));
		}

		return $res;
	}

	/**
	 * Method to Subscribe to BwPostman while Joomla registration
	 *
	 * @param   string  $user_mail      User mail address
	 * @param   int     $user_id        Joomla User ID
	 * @param   string  $user_name      Joomla User name
	 * @param   int     $mailformat     selected mail format
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _subscribeToBwPostman($user_mail, $user_id, $user_name, $mailformat)
	{
		$subscriber = $this->_createSubscriberData($user_mail, $user_id, $user_name, $mailformat);

		$subscriber_id  = $this->_saveSubscriber($subscriber);
		if (!$subscriber_id)
		{
			return false;
		}

		if (!$this->_saveSubscribersMailinglists($subscriber_id))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to activate subscription when Joomla account is confirmed
	 *
	 * @param   int   $user_id       Joomla User ID
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _activateSubscription($user_id)
	{
		// Is it a valid user to activate?
		if ($user_id == 0)
		{
			return false;
		}

		$activation_ip	= $_SERVER['REMOTE_ADDR'];

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$date   = JFactory::getDate();
		$time   = $date->toSql();

		$query->update($_db->quoteName('#__bwpostman_subscribers'));
		$query->set($_db->quoteName('status') . ' = ' . (int) 1);
		$query->set($_db->quoteName('activation') . ' = ' . $_db->quote(''));
		$query->set($_db->quoteName('confirmation_date') . ' = ' . $_db->quote($time, false));
		$query->set($_db->quoteName('confirmed_by') . ' = ' . (int) 0);
		$query->set($_db->quoteName('confirmation_ip') . ' = ' . $_db->quote($activation_ip));
		$query->where($_db->quoteName('id') . ' = ' . (int) $user_id);

		$_db->setQuery($query);
		try
		{
			$res = $_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		$params 		= JComponentHelper::getParams('com_bwpostman');
		$send_mail		= $params->get('activation_to_webmaster');

		if ($send_mail && $res)
		{
			$subscriber = $this->_getSubscriptionData($user_id);
			$model      = JModelLegacy::getInstance('Register', 'BwPostmanModel');
			$model->sendActivationNotification($subscriber['id']);
		}

		return true;
	}

	/**
	 * Method to update email of subscription, if email of Joomla account changes
	 *
	 * @param   array  $subscriber  Subscriber ID and mail address
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _updateEmailOfSubscription($subscriber)
	{
		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('email') . " = " . $_db->quote($subscriber['email']));
			$query->where($_db->quoteName('id') . ' = ' . $_db->quote($subscriber['id']));

			$_db->setQuery($query);

			$result  = $_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		return $result;
	}

	/**
	 * Method to delete subscription, if Joomla account is deleted
	 *
	 * @param   int  $user_id  User ID
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _deleteSubscription($user_id)
	{
		$subscriber                 = $this->_getSubscriptionData($user_id);
		$res                        = false;
		$res_delete_mailinglists    = false;
		$res_delete_subscriber      = false;

		if (!is_array($subscriber))
		{
			return true;
		}

		if ($subscriber['id'] != 0)
		{
			try
			{
				$res_delete_subscriber      = $this->_deleteSubscriber($subscriber['id']);
				$res_delete_mailinglists    = $this->_deleteSubscribedMailinglists($subscriber['id']);
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}
		}

		if ($res_delete_mailinglists || $res_delete_subscriber)
		{
			$res    = true;
		}
		return $res;
	}

	/**
	 * Method to check if user has a subscription
	 *
	 * @param   string  $user_mail   User email address
	 *
	 * @return  bool     true if subscription present
	 *
	 * @since  2.0.0
	 */
	protected function _hasSubscription($user_mail)
	{
		if ($user_mail == '')
		{
			return false;
		}

		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('email'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

			$_db->setQuery($query);

			$result  = $_db->loadResult();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to update user ID in table subscribers
	 *
	 * @param   string  $user_mail   User email address
	 * @param   int     $user_id   User ID
	 *
	 * @return  bool     true if subscription present
	 *
	 * @since  2.0.0
	 */
	protected function _updateUserIdAtSubscribers($user_mail, $user_id)
	{
		if ($user_id == 0)
		{
			return false;
		}

		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('user_id') . " = " . $_db->quote($user_id));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user_mail));

			$_db->setQuery($query);

			$result  = $_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get subscription email from BwPostman
	 *
	 * @param   int  $user_id   User ID
	 *
	 * @return  array|bool     subscriber mailaddress and id, or false on error
	 *
	 * @since  2.0.0
	 */
	protected function _getSubscriptionData($user_id)
	{
		if ($user_id == 0)
		{
			return true;
		}

		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('email'));
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('user_id') . ' = ' . $_db->quote($user_id));

			$_db->setQuery($query);

			// @ToDo: What is the result on 'not found'?
			$subscriber  = $_db->loadAssoc();
		}
		catch (Exception $e)
	    {
		    $this->_subject->setError($e->getMessage());
		    return false;
	    }
		return $subscriber;
	}

	/**
	 * Method to delete subscriber from subscribers table
	 *
	 * @param   int  $subscriber_id     Subscriber ID
	 *
	 * @return  bool                    true on success
	 *
	 * @since  2.0.0
	 */
	protected function _deleteSubscriber($subscriber_id)
	{
		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->delete($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('id') . ' =  ' . $_db->quote($subscriber_id));

			$_db->setQuery($query);

			$res  = $_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		return $res;
	}

	/**
	 * Method to delete subscriber entries from subscribers mailinglists table
	 *
	 * @param   int  $subscriber_id     Subscriber ID
	 *
	 * @return  bool                    true on success
	 *
	 * @since  2.0.0
	 */
	protected function _deleteSubscribedMailinglists($subscriber_id)
	{
		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($_db->quoteName('subscriber_id') . ' =  ' . $_db->quote($subscriber_id));

			$_db->setQuery($query);

			$res  = $_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		return $res;
	}

	/**
	 * Method to create the activation and check if the sting does not exist twice or more
	 *
	 * @return string   $activation
	 *
	 * @since       2.0.0
	 */
	protected function _createActivation()
	{
		// @ToDo: When this method has moved to helper class, this one here is redundant
		$_db                = JFactory::getDbo();
		$query              = $_db->getQuery(true);
		$current_activation = null;
		$match_activation   = true;

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
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
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
	protected function _createEditlink()
	{
		// @ToDo: When this method has moved to helper class, this one here is redundant
		$_db                = JFactory::getDbo();
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
	/**
	 * Method to save subscriber data into table
	 *
	 * @param   array   $subscriber     subscriber data
	 *
	 * @return bool     true on success
	 *
	 * @since       2.0.0
	 */
	protected function _saveSubscriber($subscriber)
	{

		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		// @ToDo: Complete method
		$query->insert($_db->quoteName('#__bwpostman_subscribers'));
		$query->columns(array(
			$_db->quoteName(''),
			$_db->quoteName('')
		));
		$query->values(
			$_db->quote('') . ',' .
			$_db->quote('')
		);
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Method to save subscribed mailinglists
	 *
	 * @param   int   $subscriber_id     subscriber id
	 *
	 * @return bool     true on success
	 *
	 * @since       2.0.0
	 */
	protected function _saveSubscribersMailinglists($subscriber_id)
	{
		$mailinglist_id = $this->params->get('mailinglist_to_subscribe');

		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->columns(array(
			$_db->quoteName('subscriber_id'),
			$_db->quoteName('mailinglist_id')
		));
		$query->values(
			$_db->quote($subscriber_id) . ',' .
			$_db->quote($mailinglist_id)
		);
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Method to create user data array
	 *
	 * @param $user_mail
	 * @param $user_id
	 * @param $user_name
	 * @param $mailformat
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	protected function _createSubscriberData($user_mail, $user_id, $user_name, $mailformat)
	{
		$params = JComponentHelper::getParams('com_bwpostman');
		$date   = JFactory::getDate();
		$time   = $date->toSql();
		$jinput = JFactory::getApplication()->input;
		$ip     = $jinput->server->get('REMOTE_ADDR', '', '');

		$subscriber = array(
			'id'                => 0,
			'user_id'           => $user_id,
			'name'              => $user_name,
			'email'             => $user_mail,
			'emailformat'       => $mailformat,
			'activation'        => $this->_createActivation(),
			'editlink'          => $this->_createEditlink(),
			'status'            => 0,
			'registration_date' => $time,
			'registered_by'     => 0,
			'registration_ip'   => $ip,
			'confirmed_by'      => '-1',
			'archived_by'       => '-1',
		);

		if ($params->get('firstname_field_obligation'))
		{
			$subscriber['first_name']   = ' ';
		}

		if ($params->get('special_field_obligation'))
		{
			$subscriber['special']   = ' ';
		}

		return $subscriber;
	}

}
