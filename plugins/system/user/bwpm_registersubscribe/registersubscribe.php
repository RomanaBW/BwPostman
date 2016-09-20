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
class PlgUserRegisterSubscribe extends JPlugin
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
//		'com_users.profile',
//		'com_users.user',
		'com_users.registration',
//		'com_admin.profile',
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
	public function onUserAfterSaveX($data, $isNew, $result, $error)
	{
		if ($result == false)
		{
			return false;
		}

		$user_mail   = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id     = ArrayHelper::getValue($data, 'id', 0, 'int');
		$user_name   = ArrayHelper::getValue($data, 'name', '', 'string');

		if ($isNew)
		{
			$res    = $this->_subscribeToBwPostman($user_mail, $user_id, $user_name);

			return $res;
		}

		if (ArrayHelper::getValue($data, 'activation', '', 'string') != '')
		{
			$res    = $this->_activateSubscription($data);

			return $res;
		}

		if ($this->params->get('auto_update_email_option') === true)
		{
			$subscriber = $this->_getSubscriptionData($user_id);

			if (is_array($subscriber) && ($user_mail != $subscriber['email']))
			{
				$res = $this->_updateEmailOfSubscription($subscriber);
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
	public function onContentPrepareFormX($form, $data)
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
	public function onUserAfterDeleteX($data, $success, $msg)
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
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _subscribeToBwPostman($user_mail, $user_id, $user_name)
	{
		return true;
	}

	/**
	 * Method to activate subscription when Joomla account is confirmed
	 *
	 * @param   int   $data       User data
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _activateSubscription($data)
	{
		return true;
	}

	/**
	 * Method to update email of subscription, if email of Joomla account changes
	 *
	 * @param   int  $user_id  User ID
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function _updateEmailOfSubscription($user_id)
	{
		return true;
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
}
