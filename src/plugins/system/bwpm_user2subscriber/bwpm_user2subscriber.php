<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin main file for BwPostman.
 *
 * @version 2.0.2 bwpmpu2s
 * @package			BwPostman User2Subscriber Plugin
 * @author			Romana Boldt
 * @copyright		(C) 2016-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');
require_once(JPATH_PLUGINS . '/system/bwpm_user2subscriber/helpers/bwpm_user2subscriberhelper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

/**
 * Class User2Subscriber
 *
 * @since  2.0.0
 */
class PlgSystemBWPM_User2Subscriber extends JPlugin
{
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected $min_bwpostman_version    = '1.3.2';

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
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	protected $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected $BwPostmanComponentVersion = '0.0.0';

	/**
	 * Property to hold form
	 *
	 * @var    object
	 *
	 * @since  2.0.0
	 */
	protected $form;

	/**
	 * Property to hold app
	 *
	 * @var    object
	 *
	 * @since  2.0.0
	 */
	protected $app;

	/**
	 * Property to hold subscriber data stored at component
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	protected $stored_subscriber_data = array();


	/**
	 * Property to hold logger
	 *
	 * @var    object
	 *
	 * @since  2.0.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    object
	 *
	 * @since  2.0.0
	 */
	private $log_cat  = 'Plg_U2S';

	/**
	 * Property to hold debug
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	private $debug    = false;

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
		JFormHelper::addFieldPath(JPATH_PLUGINS . '/system/bwpm_user2subscriber/form/fields');

		$log_options    = array();
		$this->logger   = new BwLogger($log_options);
		$this->debug    = $this->params->get('debug_option');

		$this->setBwPostmanComponentStatus();
		$this->setBwPostmanComponentVersion();
		$this->loadLanguageFiles();
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setBwPostmanComponentStatus()
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
			$this->BwPostmanComponentEnabled = $enabled;

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Component is enabled: %s', $enabled), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$this->logger->addEntry(new JLogEntry($e->getMessage(), JLog::ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to set component version property
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setBwPostmanComponentVersion()
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
			$this->BwPostmanComponentVersion = $manifest['version'];

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Component version is: %s', $manifest['version']), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentVersion = '0.0.0';
			$this->logger->addEntry(new JLogEntry($e->getMessage(), JLog::ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to load further language files
	 *
	 * @since 2.0.0
	 */
	protected function loadLanguageFiles()
	{
		$lang = JFactory::getLanguage();

		//Load first english file of component
		$lang->load('com_bwpostman', JPATH_SITE, 'en_GB', true);

		//load specific language of component
		$lang->load('com_bwpostman', JPATH_SITE, null, true);

		//Load specified other language files in english
		$lang->load('plg_vmuserfield_bwpm_buyer2subscriber', JPATH_ADMINISTRATOR, 'en_GB', true);

		// and other language
		$lang->load('plg_vmuserfield_bwpm_buyer2subscriber', JPATH_ADMINISTRATOR, null, true);
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
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('onContentPrepareForm reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		$context = $form->getName();

		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry(sprintf('Context is: %s', $context), JLog::DEBUG, $this->log_cat));
		}

		if (!in_array($context, $this->allowedContext))
		{
			return true;
		}

		$mailinglists   = $this->params->get('ml_available', array());

		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry(sprintf('Count mailinglists is: %s', count($mailinglists)), JLog::DEBUG, $this->log_cat));
		}

		if (!count($mailinglists))
		{
			return true;
		}

		$data_helper = $data;
		$data_helper = (array) $data_helper;
		if (isset($data_helper['language']))
		{
			unset($data_helper['language']);
		}

		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry(sprintf('Array is empty: %s', !empty($data_helper)), JLog::DEBUG, $this->log_cat));
		}

		if (!empty($data_helper))
		{
			$this->logger->addEntry(new JLogEntry('Array is not okay'));
			return true;
		}

		$this->form = $form;

		JForm::addFormPath(JPATH_PLUGINS . '/system/bwpm_user2subscriber/form');
		$this->form->loadFile('form', false);

		if (!($this->form instanceof JForm))
		{
			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry('Form is not an instance of JForm', JLog::DEBUG, $this->log_cat));
			}

			return false;
		}

		$this->logger->addEntry(new JLogEntry('Form is instance'));

		// Add CSS and JS for the radio fields
		$doc = JFactory::getDocument();

		$css_file   = JUri::base(true) . '/plugins/system/bwpm_user2subscriber/assets/css/bwpm_user2subscriber.css';
		$doc->addStyleSheet($css_file);

		// makes sure that jQuery is loaded first
		JHtml::_('jquery.framework');
		$js_file = JUri::base(true) . '/plugins/system/bwpm_user2subscriber/assets/js/bwpm_user2subscriber.js';

		$doc->addScript($js_file);
		$this->logger->addEntry(new JLogEntry('Script and CSS added'));

		$this->processGenderField();
		$this->processLastnameField();
		$this->processFirstnameField();
		$this->processAdditionalField();
		$this->processNewsletterFormatField();
		$this->processSelectedMailinglists();
		$this->processCaptchaField();

		$form   = $this->form;

		return true;
	}

	/**
	 * Method to check if prerequisites are fulfilled
	 *
	 * @return  bool
	 *
	 * @since   2.0.0
	 */
	protected function prerequisitesFulfilled()
	{
		if (!$this->BwPostmanComponentEnabled)
		{
			return false;
		}

		if (version_compare($this->BwPostmanComponentVersion, $this->min_bwpostman_version, 'lt'))
		{
			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Component version not met!'), JLog::ERROR, $this->log_cat));
			}

			return false;
		}

		return true;
	}

	/**
	 * Method to prepare input field gender
	 *
	 * @since 2.0.0
	 */
	protected function processGenderField()
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if (!$com_params->get('show_gender'))
		{
			$this->form->removeField('gender', 'bwpm_user2subscriber');
		}
	}

	/**
	 * Method to prepare input field last name
	 *
	 * @since 2.0.0
	 */
	protected function processLastnameField()
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('name_field_obligation'))
		{
			$com_params->set('show_name_field', '1');
			$this->form->setValue('name_required', 'bwpm_user2subscriber', 1);
		}

		if (!$com_params->get('show_name_field'))
		{
			$this->form->removeField('name', 'bwpm_user2subscriber');
		}
	}

	/**
	 * Method to prepare input field first name
	 *
	 * @since 2.0.0
	 */
	protected function processFirstnameField()
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('firstname_field_obligation'))
		{
			$com_params->set('show_firstname_field', '1');
			$this->form->setValue('firstname_required', 'bwpm_user2subscriber', 1);
		}

		if (!$com_params->get('show_firstname_field'))
		{
			$this->form->removeField('firstname', 'bwpm_user2subscriber');
		}
	}

	/**
	 *
	 *
	 * @since 2.0.0
	 */
	protected function processAdditionalField()
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('special_field_obligation'))
		{
			$com_params->set('show_special', '1');
			$this->form->setValue('additional_required', 'bwpm_user2subscriber', 1);
		}

		if (!$com_params->get('show_special'))
		{
			$this->form->removeField('special', 'bwpm_user2subscriber');
		}
		else
		{
			$special_label = $com_params->get('special_label');
			$special_desc  = $com_params->get('special_desc');

			if ($special_label != '')
			{
				$this->form->setFieldAttribute('special', 'label', JText::_($special_label), 'bwpm_user2subscriber');
			}

			if ($special_desc != '')
			{
				$this->form->setFieldAttribute('special', 'description', JText::_($special_desc), 'bwpm_user2subscriber');
			}
		}
	}

	/**
	 * Method to prepare input field additional
	 *
	 * @since 2.0.0
	 */
	protected function processNewsletterFormatField()
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		$this->form->setFieldAttribute('emailformat', 'default', $com_params->get('default_emailformat'), 'bwpm_user2subscriber');

		if ($com_params->get('show_emailformat'))
		{
			$this->form->setFieldAttribute('emailformat', 'required', 'true', 'bwpm_user2subscriber');
		}
		else
		{
			$this->form->setFieldAttribute('emailformat_show', 'default', $com_params->get('show_emailformat'), 'bwpm_user2subscriber');
		}
	}

	/**
	 * Method to prepare input field mailinglists
	 *
	 * @since 2.0.0
	 */
	protected function processSelectedMailinglists()
	{
		$this->form->setValue('mailinglists', 'bwpm_user2subscriber', json_encode($this->params->get('ml_available', array())));
	}

	/**
	 * Method to prepare input field captcha
	 *
	 * @since 2.0.0
	 */
	protected function processCaptchaField()
	{
		$this->form->setFieldAttribute('bw_captcha', 'name', 'bwp-' . BwPostmanHelper::getCaptcha(1), 'bwpm_user2subscriber');
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
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('onUserBeforeSave reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		// Sanitize data
		$old_activation	= ArrayHelper::getValue($oldUser, 'activation', '', 'string');
		$new_activation	= ArrayHelper::getValue($newUser, 'activation', '', 'string');
		$oldMail		= ArrayHelper::getValue($oldUser, 'email', '', 'string');
		$newMail		= ArrayHelper::getValue($newUser, 'email', '', 'string');
		$user_id        = ArrayHelper::getValue($oldUser, 'id', 0, 'int');
		$changeMail		= false;

		if ($oldMail != $newMail)
		{
			$changeMail = true;
		}

		$session = JFactory::getSession();
		$session->set('plg_bwpm_user2subscriber.changeMail', $changeMail);

		if ($old_activation != '' && ($old_activation != $new_activation))
		{
			$session->set('plg_bwpm_user2subscriber.userid', $user_id);
			$session->set('plg_bwpm_user2subscriber.activation', $old_activation);
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
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('onUserAfterSave reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		if ($result == false)
		{
			return false;
		}

		$session = JFactory::getSession();

		$session->set('plg_bwpm_user2subscriber.form_prepared', false);

		$subscription_data  = $session->get('plg_bwpm_buyer2subscriber.subscription_data', array());
		$session->clear('plg_bwpm_buyer2subscriber');

		if (is_array($subscription_data) && count($subscription_data) > 0)
		{
			$data['bwpm_user2subscriber']   = $subscription_data;
		}

		// Get and sanitize data
		$user_mail              = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id                = ArrayHelper::getValue($data, 'id', 0, 'int');
		$subscriber_data        = ArrayHelper::getValue($data, 'bwpm_user2subscriber', array(), 'array');

		if ($isNew)
		{
			$newUser_result = $this->processNewUser($user_mail, $user_id, $subscriber_data);

			return $newUser_result;
		}

		$activation     = $session->get('plg_bwpm_user2subscriber.activation');
		$changeMail     = $session->get('plg_bwpm_user2subscriber.changeMail');
		$task           = JFactory::getApplication()->input->get->get('task', '', 'string');
		$token          = JFactory::getApplication()->input->get->get('token', '', 'string');
		$session->clear('plg_bwpm_user2subscriber');

		$this->stored_subscriber_data = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);
		$subscriber_id                = BWPM_User2SubscriberHelper::hasSubscription($user_mail);
		$subscriber_is_to_activate    = BWPM_User2SubscriberHelper::isToActivate($user_mail);

		if (($task == 'registration.activate' && $token == $activation) || (JFactory::getApplication()->isAdmin() && $activation != ''))
		{
			if ($subscriber_is_to_activate)
			{
				$activate_result = $this->activateSubscription($user_mail);

				return $activate_result;
			}
		}

		if (!$subscriber_is_to_activate)
		{
			$new_mailinglists           = $this->params->get('ml_available', array());
			$updateMailinglists_result  = BWPM_User2SubscriberHelper::updateSubscribedMailinglists($subscriber_id, $new_mailinglists);

			if (!$updateMailinglists_result)
			{
				return false;
			}
		}

		if ($this->params->get('auto_update_email_option') && $changeMail)
		{
			$email_update_result  = $this->updateMailaddress($user_mail);

			return $email_update_result;
		}

		return true;
	}

	/**
	 * @param string    $user_mail
	 * @param int       $user_id
	 * @param array     $subscriber_data
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function processNewUser($user_mail, $user_id, $subscriber_data)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('process new user', JLog::DEBUG, $this->log_cat));
		}

		$subscription_wanted    = ArrayHelper::getValue($subscriber_data, 'bwpm_user2subscriber', 0, 'int');

		if (!$subscription_wanted)
		{
			return false;
		}

		try
		{
			$subscriber_id  = BWPM_User2SubscriberHelper::hasSubscription($user_mail);

			if ($subscriber_id)
			{
				$update_userid_result   = false;

				if ($user_id)
				{
					$update_userid_result = BWPM_User2SubscriberHelper::updateUserIdAtSubscriber($user_mail, $user_id);
				}

				$update_subscriberdata_result = BWPM_User2SubscriberHelper::updateSubscriberData($subscriber_id, $subscriber_data);

				$new_mailinglists       = json_decode($subscriber_data['mailinglists']);
				$update_mailinglists    = BWPM_User2SubscriberHelper::updateSubscribedMailinglists($subscriber_id, $new_mailinglists);

				return ($update_mailinglists && $update_userid_result);
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
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('subscribe to BwPostman', JLog::DEBUG, $this->log_cat));
		}

		try
		{
			$mailinglist_ids    = json_decode($subscriber_data['mailinglists']);

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

		return true;
	}

	/**
	 * Method to activate subscription when Joomla account is confirmed or order in VM is confirmed
	 *
	 * @param   string   $user_mail       mail address of new subscriber
	 *
	 * @return  bool        True on success
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	protected function activateSubscription($user_mail)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('activate subscription reached', JLog::DEBUG, $this->log_cat));
		}

		if ($user_mail == '')
		{
			return false;
		}

		$activation_ip  = JFactory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
		$subscriber_id  = BWPM_User2SubscriberHelper::getSubscriberIdByEmail($user_mail);

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
		$query->where($_db->quoteName('email') . ' = "' . (string) $user_mail . '"');

		$_db->setQuery($query);
		try
		{
			$res = $_db->execute();

			$params    = JComponentHelper::getParams('com_bwpostman');
			$send_mail = $params->get('activation_to_webmaster');

			// @ToDo: How could I get here with no object $this->stored_subscriber_data
			if ($send_mail && $res && $subscriber_id)
			{
				$model  = JModelLegacy::getInstance('Register', 'BwPostmanModel');
				$model->sendActivationNotification($this->stored_subscriber_data['id']);
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
	 * @param $user_mail
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function updateMailaddress($user_mail)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('update mail address', JLog::DEBUG, $this->log_cat));
		}

		$update_email_result    = false;

		try
		{
			if (is_array($this->stored_subscriber_data) && ($this->stored_subscriber_data['email'] != $user_mail))
			{
				$this->stored_subscriber_data['email'] = $user_mail;

				$update_email_result = $this->updateEmailOfSubscription();
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		return $update_email_result;
	}

	/**
	 * Method to update email of subscription, if email of Joomla account changes
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function updateEmailOfSubscription()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_subscribers'));
		$query->set($_db->quoteName('email') . " = " . $_db->quote($this->stored_subscriber_data['email']));
		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($this->stored_subscriber_data['id']));

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
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('onUserAfterDelete reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->BwPostmanComponentEnabled)
		{
			return false;
		}

		if (!$success)
		{
			return false;
		}

		$user_id    = ArrayHelper::getValue($data, 'id', 0, 'int');

		$this->stored_subscriber_data = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);

		if ($this->params->get('auto_delete_option'))
		{
			$delete_result = $this->deleteSubscription();
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
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function deleteSubscription()
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('delete subscription', JLog::DEBUG, $this->log_cat));
		}

		try
		{
			$res                        = false;
			$res_delete_mailinglists    = false;
			$res_delete_subscriber      = false;

			if (!is_array($this->stored_subscriber_data))
			{
				return true;
			}

			if ($this->stored_subscriber_data['id'] != 0)
			{
				$res_delete_subscriber      = $this->deleteSubscriber();
				$res_delete_mailinglists    = $this->deleteSubscribedMailinglists();
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
	 * Method to delete subscriber from subscribers table
	 *
	 * @return  bool                    true on success
	 *
	 * @since  2.0.0
	 */
	protected function deleteSubscriber()
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('delete subscriber', JLog::DEBUG, $this->log_cat));
		}

		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->delete($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('id') . ' =  ' . $_db->quote($this->stored_subscriber_data['id']));

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
	 * @return  bool        true on success
	 *
	 * @since  2.0.0
	 */
	protected function deleteSubscribedMailinglists()
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('delete mailinglists', JLog::DEBUG, $this->log_cat));
		}

		try
		{
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($_db->quoteName('subscriber_id') . ' =  ' . $_db->quote($this->stored_subscriber_data['id']));

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
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('remove UserID from subscription', JLog::DEBUG, $this->log_cat));
		}

		try
		{
			$res_update_subscriber      = false;

			if (!is_array($this->stored_subscriber_data))
			{
				return true;
			}

			if ($this->stored_subscriber_data['id'] != 0)
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

		JFactory::getUser();

		return $res_update_subscriber;
	}

	/**
	 * Event method onAfterRender
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public function onAfterRender()
	{
		$session = JFactory::getSession();
		$jinput  = $this->app->input;

		$confirm             = (int) $jinput->get('confirm', 0);
		$subscription_data   = $session->get('plg_bwpm_buyer2subscriber.subscription_data', array());
		$session->clear('plg_bwpm_buyer2subscriber.subscription_data');

		if (count($subscription_data))
		{
			if ($confirm)
			{
				$subscription_success   = $this->processNewUser($subscription_data['email'], 0, $subscription_data);

				if ($subscription_success)
				{
					$this->activateSubscription($subscription_data['email']);
				}
			}
		}
	}
}
