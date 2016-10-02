<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin main file for BwPostman.
 *
 * @version 2.0.0 bwpmprs
 * @package			BwPostman User2Subscriber Plugin
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

require_once (JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');
require_once (JPATH_PLUGINS . '/system/bwpm_user2subscriber/helpers/bwpm_user2subscriberhelper.php');
require_once (JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

/**
 * Class User2Subscriber
 *
 * @since  2.0.0
 */
class PlgSystemBWPM_User2Subscriber extends JPlugin
{
	/**
	 * Load the language file on instantiation
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
	 * Property to hold component enabled status
	 *
	 * @var    bool
	 *
	 * @since  2.0.0
	 */
	protected $componentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected $componentVersion = 0;

	/**
	 * PlgSystemBWPM_User2Subscriber constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since   2.0.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/fields');
		JFormHelper::addFieldPath(__DIR__ . '/form/fields');

		$this->setComponentStatus();
		$this->setComponentVersion();

		$lang   = JFactory::getLanguage();

		//Load first english file of component
		$lang->load('com_bwpostman',JPATH_SITE,'en_GB',true);

		//load specific language
		$lang->load('com_bwpostman',JPATH_SITE,null,true);
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setComponentStatus()
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('enabled'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('com_bwpostman'));

		$_db->setQuery($query);

		try
		{
			$enabled                = $_db->loadResult();
			$this->componentEnabled = $enabled;
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->componentEnabled = false;
		}
	}

	/**
	 * Method to set component version property
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setComponentVersion()
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$manifest               = json_decode($_db->loadResult(), true);
			$this->componentVersion = $manifest['version'];
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->componentVersion = 0;
		}
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
		if (!$this->componentEnabled)
		{
			return false;
		}

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

		if (!($form instanceof JForm))
		{
			return false;
		}

		// Add CSS for the radio fields
		$doc = JFactory::getDocument();

		$css = "
				.registerradio.radio.btn-group input[type=\"radio\"] {display: none;}
				.registerradio .btn {-moz-border-bottom-colors: none;-moz-border-left-colors: none;-moz-border-right-colors: none;-moz-border-top-colors: none;background-color: #f5f5f5;background-image: linear-gradient(to bottom, #fff, #e6e6e6);background-repeat: repeat-x;border-color: #bbb #bbb #a2a2a2;border-image: none;border-radius: 4px;border-style: solid;border-width: 1px;box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);color: #333;cursor: pointer;display: inline-block;font-size: 13px;line-height: 18px;margin-bottom: 0;padding: 4px 12px;text-align: center;text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);vertical-align: middle;}
				.registerradio .btn:hover, .registerradio .btn:focus, .registerradio .btn:active, .registerradio .btn.active, .registerradio .btn.disabled, .registerradio .btn[disabled] {background-color: #e6e6e6;color: #333;}
				.registerradio .btn.active, .registerradio .btn:active {background-image: none;box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15) inset, 0 1px 2px rgba(0, 0, 0, 0.05);outline: 0 none;}
				.registerradio .btn-primary.active, .registerradio .btn-warning.active, .registerradio .btn-danger.active, .registerradio .btn-success.active, .registerradio .btn-info.active, .registerradio .btn-inverse.active {color: rgba(255, 255, 255, 0.75);}
				.registerradio .btn-success {background-color: #409740;background-image: linear-gradient(to bottom, #46a546, #378137);background-repeat: repeat-x;border-color: #378137 #378137 #204b20;color: #fff;text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);}
				.registerradio .btn-success:hover, .registerradio .btn-success:focus, .registerradio .btn-success:active, .registerradio .btn-success.active, .registerradio .btn-success.disabled, .registerradio .btn-success[disabled] {background-color: #378137;color: #fff;}
				.registerradio .btn-danger:hover, .registerradio .btn-danger:focus, .registerradio .btn-danger:active, .registerradio .btn-danger.active, .registerradio .btn-danger.disabled, .registerradio .btn-danger[disabled] {background-color: #942a25;color: #fff;}
				.registerradio.btn-group > .btn {border-radius: 0;position: relative;}
				.registerradio.btn-group > .btn, .registerradio.btn-group > .dropdown-menu, .registerradio.btn-group > .popover {font-size: 13px;}
				.registerradio.btn-group > .btn:last-child, .registerradio.btn-group > .dropdown-toggle {border-bottom-right-radius: 4px;border-top-right-radius: 4px;}
				.registerradio.btn-group > .btn:hover, .registerradio.btn-group > .btn:focus, .registerradio.btn-group > .btn:active, .registerradio.btn-group > .btn.active {z-index: 2;}
				.registerradio.radio.btn-group > label:first-of-type {border-bottom-left-radius: 4px !important;border-top-left-radius: 4px !important;margin-left: 0;}
				.registerradio .btn-primary.active, .registerradio .btn-warning.active, .registerradio .btn-danger.active, .registerradio .btn-success.active, .registerradio .btn-info.active, .registerradio .btn-inverse.active {color: rgba(255, 255, 255, 0.95);}
				.registerradio.btn-group.btn-group-yesno > .btn {min-width: 84px;padding: 2px 12px;}
				";
		$doc->addStyleDeclaration($css);

		$com_params = JComponentHelper::getParams('com_bwpostman');

		if (!$com_params->get('show_gender'))
		{
			$form->removeField('gender', 'bwpm_user2subscriber');
		}

		if ($com_params->get('name_field_obligation'))
		{
			$com_params->set('show_name_field', '1');
			$form->setFieldAttribute('name', 'required', 'true', 'bwpm_user2subscriber');
		}

		if (!$com_params->get('show_name_field'))
		{
			$form->removeField('name', 'bwpm_user2subscriber');
		}

		if ($com_params->get('firstname_field_obligation'))
		{
			$com_params->set('show_firstname_field', '1');
			$form->setFieldAttribute('firstname', 'required', 'true', 'bwpm_user2subscriber');
		}

		if (!$com_params->get('show_firstname_field'))
		{
			$form->removeField('firstname', 'bwpm_user2subscriber');
		}

		if ($com_params->get('special_field_obligation'))
		{
			$com_params->set('show_special', '1');
			$form->setFieldAttribute('special', 'required', 'true', 'bwpm_user2subscriber');
		}

		if (!$com_params->get('show_special'))
		{
			$form->removeField('special', 'bwpm_user2subscriber');
		}
		else
		{
			$special_label  = $com_params->get('special_label');
			$special_desc   = $com_params->get('special_desc');

			if ($special_label != '')
			{
				$form->setFieldAttribute('special', 'label', JText::_($special_label), 'bwpm_user2subscriber');
			}

			if ($special_desc != '')
			{
				$form->setFieldAttribute('special', 'description', JText::_($special_desc), 'bwpm_user2subscriber');
			}
		}

		if ($com_params->get('show_emailformat'))
		{
			$form->setFieldAttribute('emailformat', 'required', 'true', 'bwpm_user2subscriber');
			$form->setFieldAttribute('emailformat', 'default', $com_params->get('default_emailformat'), 'bwpm_user2subscriber');
		}
		else
		{
			$form->removeField('emailformat', 'bwpm_user2subscriber');
		}

		$form->setFieldAttribute('bw_captcha', 'name', 'bwp-' . BwPostmanHelper::getCaptcha(1), 'bwpm_user2subscriber');

		return true;
	}

	/**
	 * Event method onUserBeforeSave
	 *
	 * @param   array  $oldUser User data before saving
	 * @param   bool   $isNew   true on new user
	 * @param   array  $newUser User data to save
	 *
	 * @return  bool
	 *
	 * @since  2.0.0
	 */
	public function onUserBeforeSave($oldUser, $isNew, $newUser)
	{
		if (!$this->componentEnabled)
		{
			return false;
		}

		// Sanitize data
		$activation     = ArrayHelper::getValue($oldUser, 'activation', '', 'string');
		$user_id        = ArrayHelper::getValue($oldUser, 'id', 0, 'int');

		if ($activation != '')
		{
			$session = JFactory::getSession();
			$session->set('plg_bwpm_user2subscriber.userid', $user_id);
			$session->set('plg_bwpm_user2subscriber.activation', $activation);
		}
		return true;
	}

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
/*

$log_options = array('test' => 'testtext', 'text_file' => 'bwpostman/BWPM_User2Subscriber.log');
$logger      = new BwLogger($log_options);
foreach ($data as $key => $value)
{
	$logger->addEntry(new JLogEntry(sprintf('Data Key: %s, value = %s ', $key, $value)));
}
*/
		if (!$this->componentEnabled)
		{
			return false;
		}

		if ($result == false)
		{
			return false;
		}

		// Get and sanitize data
		$user_mail              = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id                = ArrayHelper::getValue($data, 'id', 0, 'int');
		$subscriber_data        = ArrayHelper::getValue($data, 'bwpm_user2subscriber', array(), 'array');
		$subscription_wanted    = ArrayHelper::getValue($subscriber_data, 'bwpm_user2subscriber', 1, 'int');

		if ($isNew)
		{
			if (!$subscription_wanted)
			{
				return false;
			}

			try
			{
				if (BWPM_User2SubscriberHelper::hasSubscription($user_mail))
				{
					$update_userid_result = BWPM_User2SubscriberHelper::updateUserIdAtSubscriber($user_mail, $user_id);

					return $update_userid_result;
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}

			$create_result = $this->subscribeToBwPostman($user_mail, $user_id, $subscriber_data);

			return $create_result;
		}

		$session        = JFactory::getSession();
		$activation     = $session->get('plg_bwpm_user2subscriber.activation');
		$task           = JFactory::getApplication()->input->get->get('task', '', 'string');
		$token          = JFactory::getApplication()->input->get->get('token', '', 'string');

		if ($task == 'registration.activate' && $token == $activation)
		{
			$activate_result    = $this->activateSubscription($user_id);

			return $activate_result;
		}

		if ($this->params->get('auto_update_email_option'))
		{
			try
			{
				$subscriber = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);

				if (is_array($subscriber) && ($subscriber['email']) != $user_mail)
				{
					$subscriber['email'] = $user_mail;
					$update_email_result = $this->updateEmailOfSubscription($subscriber);

					return $update_email_result;
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to Subscribe to BwPostman while Joomla registration
	 *
	 * @param   string  $user_mail          User mail address
	 * @param   int     $user_id            Joomla User ID
	 * @param   array   $subscriber_data    subscriber date submitted by form
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function subscribeToBwPostman($user_mail, $user_id, $subscriber_data)
	{
		try
		{
			$mailinglist_ids    = $this->params->get('ml_available');

			if ((count($mailinglist_ids) == 1) && ($mailinglist_ids[0] == 0))
			{
				unset($mailinglist_ids[0]);
			}

			if (empty($mailinglist_ids))
			{
				return false;
			}

			$subscriber     = BWPM_User2SubscriberHelper::createSubscriberData($user_mail, $user_id, $subscriber_data, $mailinglist_ids);

			$subscriber_id  = BWPM_User2SubscriberHelper::saveSubscriber($subscriber);
			if (!$subscriber_id)
			{
				return false;
			}

			$ml_save_result     = BWPM_User2SubscriberHelper::saveSubscribersMailinglists($subscriber_id, $mailinglist_ids);

			if (!$ml_save_result)
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}
		catch (BwException $e)
		{
			$this->_subject->setError($e->getMessage());
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
	protected function activateSubscription($user_id)
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
		$query->set($_db->quoteName('confirmed_by') . ' = ' . 0);
		$query->set($_db->quoteName('confirmation_ip') . ' = ' . $_db->quote($activation_ip));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $user_id);

		$_db->setQuery($query);
		try
		{
			$res = $_db->execute();

			$params    = JComponentHelper::getParams('com_bwpostman');
			$send_mail = $params->get('activation_to_webmaster');

			if ($send_mail && $res)
			{
				$subscriber = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);
				$model      = JModelLegacy::getInstance('Register', 'BwPostmanModel');
				$model->sendActivationNotification($subscriber['id']);
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
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
	protected function updateEmailOfSubscription($subscriber)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_subscribers'));
		$query->set($_db->quoteName('email') . " = " . $_db->quote($subscriber['email']));
		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($subscriber['id']));

		$_db->setQuery($query);

		$result  = $_db->execute();

		return $result;
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
		if (!$this->componentEnabled)
		{
			return false;
		}

		if (!$success)
		{
			return false;
		}

		$user_id    = ArrayHelper::getValue($data, 'id', 0, 'int');

		if ($this->params->get('auto_delete_option'))
		{
			$delete_result = $this->deleteSubscription($user_id);
		}
		else
		{
			$delete_result = $this->removeUseridFromSubscription($user_id);
		}

		return $delete_result;
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
	protected function deleteSubscription($user_id)
	{
		try
		{
			$subscriber = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);

			$res                        = false;
			$res_delete_mailinglists    = false;
			$res_delete_subscriber      = false;

			if (!is_array($subscriber))
			{
				return true;
			}

			if ($subscriber['id'] != 0)
			{
				$res_delete_subscriber      = $this->deleteSubscriber($subscriber['id']);
				$res_delete_mailinglists    = $this->deleteSubscribedMailinglists($subscriber['id']);
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		if ($res_delete_mailinglists && $res_delete_subscriber)
		{
			$res    = true;
		}
		return $res;
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
	protected function removeUseridFromSubscription($user_id)
	{
		try
		{
			$subscriber = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);

			$res_update_subscriber      = false;

			if (!is_array($subscriber))
			{
				return true;
			}

			if ($subscriber['id'] != 0)
			{
				$_db	= JFactory::getDbo();
				$query	= $_db->getQuery(true);

				$query->update($_db->quoteName('#__bwpostman_subscribers'));
				$query->set($_db->quoteName('user_id') . " = " . (int) 0);
				$query->where($_db->quoteName('user_id') . ' = ' . $_db->quote($user_id));

				$_db->setQuery($query);

				$res_update_subscriber  = $_db->execute();
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		return $res_update_subscriber;
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
	protected function deleteSubscriber($subscriber_id)
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
	protected function deleteSubscribedMailinglists($subscriber_id)
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
