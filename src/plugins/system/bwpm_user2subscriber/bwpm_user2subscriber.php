<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\Form;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Log\LogEntry;

jimport('joomla.plugin.plugin');

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');
require_once(JPATH_PLUGINS . '/system/bwpm_user2subscriber/helpers/bwpm_user2subscriberhelper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

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
	 * Property to hold group
	 *
	 * @var    object
	 *
	 * @since  3.0.0
	 */
	protected $group;

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
	 * @param DispatcherInterface $subject
	 * @param array  $config
	 *
	 * @since   2.0.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		FormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/fields');
		FormHelper::addFieldPath(JPATH_PLUGINS . '/system/bwpm_user2subscriber/form/fields');

		$log_options    = array();
		$this->logger   = BwLogger::getInstance($log_options);
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
		$_db        = Factory::getDbo();
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
				$this->logger->addEntry(new LogEntry(sprintf('Component is enabled: %s', $enabled), BwLogger::BW_DEVELOPMENT, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$this->logger->addEntry(new LogEntry($e->getMessage(), BwLogger::BW_ERROR, $this->log_cat));
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
		$_db        = Factory::getDbo();
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
				$this->logger->addEntry(new LogEntry(sprintf('Component version is: %s', $manifest['version']), BwLogger::BW_DEVELOPMENT, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentVersion = '0.0.0';
			$this->logger->addEntry(new LogEntry($e->getMessage(), BwLogger::BW_ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to load further language files
	 *
	 * @since 2.0.0
	 */
	protected function loadLanguageFiles()
	{
		$lang = Factory::getLanguage();

		//Load first english file of component
		$lang->load('com_bwpostman', JPATH_SITE, 'en-GB', true);

		//load specific language of component
		$lang->load('com_bwpostman', JPATH_SITE, null, true);

		//Load specified other language files in english
		$lang->load('plg_system_bwpm_user2subscriber', JPATH_ADMINISTRATOR, 'en-GB', true);

		// and other language
		$lang->load('plg_system_bwpm_user2subscriber', JPATH_ADMINISTRATOR, null, true);
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
		$this->logger->addEntry(new LogEntry('onContentPrepareForm reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		$context = $form->getName();

		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry(sprintf('Context is: %s', $context), BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!in_array($context, $this->allowedContext))
		{
			return true;
		}

		$this->loadLanguageFiles();

		$mailinglists   = $this->params->get('ml_available', array());
		$session = Factory::getSession();
		$session->set('plg_bwpm_user2subscriber.ml_available', $mailinglists);
		$session->set('plg_bwpm_user2subscriber.show_desc', $this->params->get('show_desc', 'true'));
		$session->set('plg_bwpm_user2subscriber.desc_length', $this->params->get('desc_length', '150'));

		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry(sprintf('Count mailinglists is: %s', count($mailinglists)), BwLogger::BW_DEBUG, $this->log_cat));
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
			$this->logger->addEntry(new LogEntry(sprintf('Array data_helper is empty: %s', !empty($data_helper)), BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!empty($data_helper))
		{
			$this->logger->addEntry(new LogEntry('Array is not okay'));
			return true;
		}

		$this->form = $form;

		Form::addFormPath(JPATH_PLUGINS . '/system/bwpm_user2subscriber/form');

		$this->form->loadFile('form3', false);
		$this->group = 'bwpm_user2subscriber';

		if (!($this->form instanceof Form))
		{
			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry('Form is not an instance of JForm', BwLogger::BW_DEVELOPMENT, $this->log_cat));
			}

			return false;
		}

		$this->logger->addEntry(new LogEntry('Form U2S is instance'));

		// Add CSS and JS for the radio fields
		$doc = Factory::getDocument();

		$css_file   = Uri::base(true) . '/plugins/system/bwpm_user2subscriber/assets/css/bwpm_user2subscriber.css';
		$doc->addStyleSheet($css_file);

		// makes sure that jQuery is loaded first
		HTMLHelper::_('jquery.framework');
		$js_file = Uri::base(true) . '/plugins/system/bwpm_user2subscriber/assets/js/bwpm_user2subscriber.js';

		$doc->addScript($js_file);

		// Get disclaimer link if disclaimer enabled at component and plugin
		$disclaimer_link = $this->getDisclaimerLink();

		// Add JS for disclaimer modal box
		if ($disclaimer_link)
		{
			$disclaimer_script =	'	var dc_src = "' . $disclaimer_link . '";' . "\n";
			$disclaimer_script .=	'	jQuery(document).ready(function(){' . "\n";
			$disclaimer_script .=	'		setPlgModal()' . "\n";
			$disclaimer_script .=	'	});' . "\n";

			$doc->addScriptDeclaration($disclaimer_script);
			$this->form->setValue('bwpdisclaimer_required', $this->group, 1);
		}
		else
		{
			$this->removeDisclaimerField();
		}

		$this->logger->addEntry(new LogEntry('Script and CSS added'));

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
				$this->logger->addEntry(new LogEntry(sprintf('Component version not met!'), BwLogger::BW_ERROR, $this->log_cat));
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
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if (!$com_params->get('show_gender'))
		{
			$this->form->removeField('gender', $this->group);
		}
	}

	/**
	 * Method to prepare input field last name
	 *
	 * @since 2.0.0
	 */
	protected function processLastnameField()
	{
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('name_field_obligation'))
		{
			$this->form->setValue('name_required', $this->group, 1);
		}

		if (!$com_params->get('show_name_field') && !$com_params->get('name_field_obligation'))
		{
			$this->form->removeField('bwpm_name', $this->group);
		}
	}

	/**
	 * Method to prepare input field first name
	 *
	 * @since 2.0.0
	 */
	protected function processFirstnameField()
	{
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('firstname_field_obligation'))
		{
			$this->form->setValue('firstname_required', $this->group, 1);
		}

		if (!$com_params->get('show_firstname_field') && !$com_params->get('firstname_field_obligation'))
		{
			$this->form->removeField('firstname', $this->group);
		}
	}

	/**
	 *
	 *
	 * @since 2.0.0
	 */
	protected function processAdditionalField()
	{
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('special_field_obligation'))
		{
			$this->form->setValue('additional_required', $this->group, 1);
		}

		if (!$com_params->get('show_special') && !$com_params->get('special_field_obligation'))
		{
			$this->form->removeField('special', $this->group);
		}
		else
		{
			$special_label = $com_params->get('special_label');
			$special_desc  = $com_params->get('special_desc');

			if ($special_label != '')
			{
				$this->form->setFieldAttribute('special', 'label', Text::_($special_label), $this->group);
			}

			if ($special_desc != '')
			{
				$this->form->setFieldAttribute('special', 'description', Text::_($special_desc), $this->group);
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
		$com_params = ComponentHelper::getParams('com_bwpostman');

		$this->form->setFieldAttribute('emailformat', 'default', $com_params->get('default_emailformat'), $this->group);

		if ($com_params->get('show_emailformat'))
		{
			$this->form->setFieldAttribute('emailformat', 'required', 'true', $this->group);
		}
		else
		{
			$this->form->setFieldAttribute('emailformat_show', 'default', $com_params->get('show_emailformat'), $this->group);
		}
	}

	/**
	 * Method to prepare input field mailinglists
	 *
	 * @since 2.0.0
	 */
	protected function processSelectedMailinglists()
	{
		$this->form->setValue('mailinglists_required', $this->group, 1);
		$mailinglists = $this->form->getInput('mailinglists');
		$this->form->setValue('mailinglists', $this->group, $mailinglists);

		// bwpm_user2subscriber_mailinglists is not the name but the id
		$this->form->setFieldAttribute('mailinglists', 'required', 'false', $this->group);
	}

	/**
	 * Method to prepare input field captcha
	 *
	 * @since 2.0.0
	 */
	protected function processCaptchaField()
	{
		$captcha = BwPostmanHelper::getCaptcha(1);
		$this->form->setFieldAttribute('bw_captcha', 'name', 'bwp-' . $captcha, $this->group);

		$session = Factory::getSession();
		$session->set('plg_bwpm_user2subscriber.captcha', $captcha);
	}

	/**
	 * Method to check if disclaimer enabled and prepare the disclaimer link
	 *
	 * @return  string
	 *
	 * @since 2.1.0
	 */
	protected function getDisclaimerLink()
	{
		$com_params = ComponentHelper::getParams('com_bwpostman');
		$disclaimer_link = '';

		if ($this->params->get('disclaimer') && $com_params->get('disclaimer'))
		{
			// Extends the disclaimer link with '&tmpl=component' to see only the content
			$tpl_com = '&amp;tmpl=component';
			// Disclaimer article and target_blank or not
			if ($com_params->get('disclaimer_selection') == 1 && $com_params->get('article_id') > 0)
			{
				JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');
				$disclaimer_link = Route::_(ContentHelperRoute::getArticleRoute($com_params->get('article_id'))) . $tpl_com;
			}
			// Disclaimer menu item and target_blank or not
			elseif ($com_params->get('disclaimer_selection') == 2 && $com_params->get('disclaimer_menuitem') > 0)
			{
				$disclaimer_link = Route::_('index.php?Itemid=' . $com_params->get('disclaimer_menuitem')) . $tpl_com;
			}
			// Disclaimer url and target_blank or not
			else
			{
				$disclaimer_link = $com_params->get('disclaimer_link');
			}
		}

		return $disclaimer_link;
	}

	/**
	 * Method to remove input field disclaimer
	 *
	 * @since 2.1.0
	 */
	protected function removeDisclaimerField()
	{
		$this->form->removeField('bwpdisclaimer', $this->group);
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
			$this->logger->addEntry(new LogEntry('onUserBeforeSave reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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

		$session = Factory::getSession();
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
			$this->logger->addEntry(new LogEntry('onUserAfterSave reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		if ($result == false)
		{
			return false;
		}

		$session = Factory::getSession();

		$session->set('plg_bwpm_user2subscriber.form_prepared', false);

		$subscription_data  = $session->get('plg_bwpm_buyer2subscriber.subscription_data', array());
		$session->clear('plg_bwpm_buyer2subscriber');

		if (is_array($subscription_data) && count($subscription_data) > 0)
		{
			$data['bwpm_user2subscriber']   = $subscription_data;
		}

		// Get and sanitize data
		$captcha = $session->get('plg_bwpm_user2subscriber.captcha', '');

		$user_mail = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id   = ArrayHelper::getValue($data, 'id', 0, 'int');

		$subscriber_data = array();
		if (isset($data['bwpm_user2subscriber']) && is_array($data['bwpm_user2subscriber']))
		{
			$dataRaw = $data['bwpm_user2subscriber'];
			$dataRaw['bwpm_name'] = $dataRaw['name'];
		}
		else
		{
			$dataRaw = $data;
		}
		$subscriber_data['bwpm_user2subscriber']	= ArrayHelper::getValue($dataRaw, 'bwpm_user2subscriber', 0, 'int');
		$subscriber_data['gender']					= ArrayHelper::getValue($dataRaw, 'gender', 2, 'int');
		$subscriber_data['name']					= ArrayHelper::getValue($dataRaw, 'bwpm_name', '', 'string');
		$subscriber_data['firstname']				= ArrayHelper::getValue($dataRaw, 'firstname', '', 'string');
		$subscriber_data['special']					= ArrayHelper::getValue($dataRaw, 'special', '', 'string');
		$subscriber_data['emailformat']				= ArrayHelper::getValue($dataRaw, 'emailformat', 1, 'int');
		$subscriber_data['mailinglists']			= ArrayHelper::getValue($dataRaw, 'mailinglists', array(), 'array');
		$subscriber_data['bwpmdisclaimer']			= ArrayHelper::getValue($dataRaw, 'bwpmdisclaimer', 0, 'int');
		$subscriber_data['bwpm-' . $captcha]		= ArrayHelper::getValue($dataRaw, 'bwpm-' . $captcha, '', 'string');
		$subscriber_data['name_required']			= ArrayHelper::getValue($dataRaw, 'name_required', '', 'string');
		$subscriber_data['firstname_required']		= ArrayHelper::getValue($dataRaw, 'firstname_required', '', 'string');
		$subscriber_data['emailformat_required']	= ArrayHelper::getValue($dataRaw, 'emailformat_required', '', 'string');
		$subscriber_data['mailinglists_required']	= ArrayHelper::getValue($dataRaw, 'mailinglists_required', '', 'string');
		$subscriber_data['additional_required']		= ArrayHelper::getValue($dataRaw, 'additional_required', '', 'string');
		$subscriber_data['bwpmdisclaimer_required']	= ArrayHelper::getValue($dataRaw, 'bwpmdisclaimer_required', '', 'string');

		if ($isNew)
		{
			$newUser_result = $this->processNewUser($user_mail, $user_id, $subscriber_data);

			return $newUser_result;
		}

		$activation     = $session->get('plg_bwpm_user2subscriber.activation');
		$changeMail     = $session->get('plg_bwpm_user2subscriber.changeMail');
		$task           = Factory::getApplication()->input->get->get('task', '', 'string');
		$token          = Factory::getApplication()->input->get->get('token', '', 'string');
		$session->clear('plg_bwpm_user2subscriber');

		$this->stored_subscriber_data = BWPM_User2SubscriberHelper::getSubscriptionData($user_id);
		$subscriber_id                = BWPM_User2SubscriberHelper::hasSubscription($user_mail);
		$subscriber_is_to_activate    = BWPM_User2SubscriberHelper::isToActivate($user_mail);

		if (($task == 'registration.activate' && $token == $activation) || (Factory::getApplication()->isClient('administrator') && $activation != ''))
		{
			if ($subscriber_is_to_activate)
			{
				$activate_result = $this->activateSubscription($user_mail);

				return $activate_result;
			}
		}

		if (!$subscriber_is_to_activate)
		{
//			$new_mailinglists       = json_decode($subscriber_data['mailinglists']);
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
			$this->logger->addEntry(new LogEntry('process new user', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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

				$new_mailinglists       = $subscriber_data['mailinglists'];
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
			$this->logger->addEntry(new LogEntry('subscribe to BwPostman', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$mailinglist_ids    = $subscriber_data['mailinglists'];

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
			$this->_subject->$e->getMessage();
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
			$this->logger->addEntry(new LogEntry('activate subscription reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if ($user_mail == '')
		{
			return false;
		}

		$activation_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
		$subscriber_id  = BWPM_User2SubscriberHelper::getSubscriberIdByEmail($user_mail);

		$_db	= Factory::getDbo();
		$query	= $_db->getQuery(true);

		$date   = Factory::getDate();
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

			$params    = ComponentHelper::getParams('com_bwpostman');
			$send_mail = $params->get('activation_to_webmaster');

			// @ToDo: How could I get here with no object $this->stored_subscriber_data
			if ($send_mail && $res && $subscriber_id)
			{
				require_once(JPATH_SITE . '/components/com_bwpostman/models/register.php');
				$model = new BwPostmanModelRegister();

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
			$this->logger->addEntry(new LogEntry('update mail address', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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
		$_db	= Factory::getDbo();
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
			$this->logger->addEntry(new LogEntry('onUserAfterDelete reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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
			$this->logger->addEntry(new LogEntry('delete subscription', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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
			$this->logger->addEntry(new LogEntry('delete subscriber', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$_db	= Factory::getDbo();
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
			$this->logger->addEntry(new LogEntry('delete mailinglists', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$_db	= Factory::getDbo();
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
			$this->logger->addEntry(new LogEntry('remove UserID from subscription', BwLogger::BW_DEVELOPMENT, $this->log_cat));
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
				$_db	= Factory::getDbo();
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

		Factory::getUser();

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
		$session = Factory::getSession();
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
