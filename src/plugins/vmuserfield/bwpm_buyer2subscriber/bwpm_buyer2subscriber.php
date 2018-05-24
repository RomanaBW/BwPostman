<?php
/**
 * BwPostman Buyer2Subscriber Plugin
 *
 * Plugin to automated subscription at VirtueMart registration
 *
 * BwPostman Buyer2Subscriber Plugin main file for BwPostman.
 *
 * @version 2.0.2 bwpmpbs
 * @package			BwPostman Buyer2Subscriber Plugin
 * @author			Romana Boldt
 * @copyright		(C) 2016-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/forum/bwpostman.html
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

require_once(JPATH_PLUGINS . '/system/bwpm_user2subscriber/helpers/bwpm_user2subscriberhelper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

if (!class_exists('vmUserfieldPlugin'))
{
	require(JPATH_VM_PLUGINS . '/vmuserfieldtypeplugin.php');
}

if(!include_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php'))
{
	// For some reason, J3.3 does not load the language file otherwise
	$language = JFactory::getLanguage();
	$language->load('plg_vmcustom_bwpm_buyer2subscriber');
	JFactory::getApplication()->enqueueMessage(JText::_('VMCUSTOM_BWPOSTMAN_COMPONENT_NEEDED'), 'error');
	return;
}

/**
 * @package     BwPostman Buyer2Subscriber Plugin
 *
 * @since       2.0.0
 */
class PlgVmUserfieldBwPm_Buyer2Subscriber extends vmUserfieldPlugin
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
		'user',
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
	 * Property to hold User2SubscriberPlugin enabled status
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	protected $BwPostmanUser2SubscriberPluginEnabled = false;

	/**
	 * Property to hold Buyer2SubscriberPlugin enabled status
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	protected $BwPostmanBuyer2SubscriberPluginEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected $BwPostmanComponentVersion = 0;

	/**
	 * Property to hold field ids to delete
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	protected $unset_array = array();

	/**
	 * Property to hold subject
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected $subject = '';

	/**
	 * Property to hold field ids of userfields used by plugin
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected $BwPostman_field_ids = array();

	/**
	 * Property to hold logger
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	private $logger;

	/**
	 * Property to hold logger ctegory
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	private $log_cat  = 'Plg_B2S';

	/**
	 * Property to hold debug switch
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	private $debug    = false;

	/**
	 * Property to hold number of userfields
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	private $count_userfields  = 0;
	/**
	 * plgVmCustomBwPm_Buyer2Subscriber constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since   2.0.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$log_options    = array();
		$this->logger   = new BwLogger($log_options);
		$this->debug    = $this->params->get('debug_option');

		$this->setComponentStatus();
		$this->setComponentVersion();
		$this->setUser2SubscriberPluginStatus();
		$this->setBuyer2SubscriberPluginStatus();
	}

	/**
	 * Method to set status of component activation (property)
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
			$enabled                         = $_db->loadResult();
			$this->BwPostmanComponentEnabled = $enabled;

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Component is enabled: %s', $enabled), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$this->logger->addEntry(new JLogEntry($e->getMessage(), JLog::ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to set component version (property)
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
			$manifest                        = json_decode($_db->loadResult(), true);
			$this->BwPostmanComponentVersion = $manifest['version'];

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Component version is: %s', $manifest['version']), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->subject->setError($e->getMessage());
			$this->BwPostmanComponentVersion = 0;
		}
	}

	/**
	 * Method to set status of User2Subscriber plugin activation (property)
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setUser2SubscriberPluginStatus()
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('enabled'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('bwpm_user2subscriber'));

		$_db->setQuery($query);

		try
		{
			$enabled    = $_db->loadResult();

			$this->BwPostmanUser2SubscriberPluginEnabled = $enabled;

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Plugin User2Subscriber is enabled: %s', $enabled), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->subject->setError($e->getMessage());
			$this->BwPostmanUser2SubscriberPluginEnabled = false;
		}
	}

	/**
	 * Method to set status of Buyer2Subscriber plugin activation (property)
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function setBuyer2SubscriberPluginStatus()
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('enabled'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('bwpm_buyer2subscriber'));

		$_db->setQuery($query);

		try
		{
			$enabled    = $_db->loadResult();

			$this->BwPostmanBuyer2SubscriberPluginEnabled = $enabled;

			if ($this->debug)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('Plugin Buyer2Subscriber is enabled: %s', $enabled), JLog::DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->subject->setError($e->getMessage());
			$this->BwPostmanBuyer2SubscriberPluginEnabled = false;
		}
	}

	/**
	 * Method to get IDs of userfields used by this plugin
	 *
	 * @param   array   $userFields
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	protected function getPluginUserfieldIds($userFields)
	{
		$field_names    = array(
			'bw_newsletter_message',
			'bw_newsletter_subscription',
			'bw_newsletter_format',
			'bw_gender',
			'bw_newsletter_additional',
			'bw_newsletter_additional_required',
			'last_name',
			'first_name'
		);
		$field_ids      = array();

		for ($i = 0; $i < count($userFields); $i++)
		{
			if (in_array($userFields[$i]->name, $field_names))
			{
				$field_ids[$userFields[$i]->name]   = $i;
			}
		}

		$this->BwPostman_field_ids  = $field_ids;
	}

	/**
	 * Method to preprocess userfields for display
	 *
	 * @param string	$type
	 * @param string	$name
	 * @param string	$render
	 *
	 * @since 2.0.0
	 */
	public function plgVmOnSelfCallFE($type, $name, &$render)
	{
		// Add CSS
		$doc = JFactory::getDocument();

		$css_file   = JUri::base(true) . '/plugins/vmuserfield/bwpm_buyer2subscriber/assets/css/bwpm_buyer2subscriber.css';
		$doc->addStyleSheet($css_file);
	}

	/**
	 * Method to preprocess userfields for display
	 *
	 * @param string    $type
	 * @param array     $userFields
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function plgVmOnGetUserfields($type, &$userFields)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('plgVmOnGetUserfields reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->allowedContext($type))
		{
			return false;
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		// Add JS for additional fields
		$doc = JFactory::getDocument();

		// makes sure that jQuery is loaded first
		JHtml::_('jquery.framework');
		$js_file = JUri::base(true) . '/plugins/vmuserfield/bwpm_buyer2subscriber/assets/js/bwpm_buyer2subscriber.js';

		$doc->addScript($js_file);

		$this->count_userfields = count($userFields);
		$this->getPluginUserfieldIds($userFields);
		$this->setBwUserfieldTypes($userFields);

		$this->unset_array  = array();

		$this->processMessageField($userFields);
		$this->processSubscriptionField($userFields);
		$this->processGenderField($userFields);
		$this->processLastnameField($userFields);
		$this->processFirstnameField($userFields);
		$this->processAdditionalField($userFields);
		$this->processNewsletterFormatField($userFields);

		if (count($this->unset_array))
		{
			foreach ($this->unset_array as $item)
			{
				unset($userFields[$item]);
			}
		}

		return true;
	}

	/**
	 * Method to check if the context is allowed
	 *
	 * @param   string  $type
	 *
	 * @return  bool
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	protected function allowedContext($type)
	{
		$view = JFactory::getApplication()->input->getCmd('view', '');
		if (!in_array($view, $this->allowedContext) || ($type != 'BT'))
		{
			return false;
		}

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

		if (version_compare($this->BwPostmanComponentVersion, '1.3.2', 'lt'))
		{
			return false;
		}

		if (!$this->BwPostmanUser2SubscriberPluginEnabled)
		{
			return false;
		}

		// @Todo: really needed? Do I get into this, if the plugin is not enabled?
		if (!$this->BwPostmanBuyer2SubscriberPluginEnabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function setBwUserfieldTypes(&$userFields)
	{
		$userFields[$this->BwPostman_field_ids['bw_newsletter_message']]->type      = 'delimiter';
		$userFields[$this->BwPostman_field_ids['bw_newsletter_subscription']]->type = 'select';
		$userFields[$this->BwPostman_field_ids['bw_gender']]->type                  = 'select';
		$userFields[$this->BwPostman_field_ids['bw_newsletter_additional']]->type   = 'text';
		$userFields[$this->BwPostman_field_ids['bw_newsletter_format']]->type       = 'select';
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processMessageField(&$userFields)
	{
		if ($this->params->get('bw_register_message_option') != '')
		{
			$userFields[$this->BwPostman_field_ids['bw_newsletter_message']]->title       = $this->params->get('bw_register_message_option');
			$userFields[$this->BwPostman_field_ids['bw_newsletter_message']]->description = $this->params->get('bw_register_message_option');
		}
		else
		{
			$userFields[$this->BwPostman_field_ids['bw_newsletter_message']]->published = 0;

			$this->unset_array[]    = $this->BwPostman_field_ids['bw_newsletter_message'];
		}
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processGenderField(&$userFields)
	{
		$new_field              = new stdClass();
		$new_field->name        = 'bw_newsletter_show_gender';
		$new_field->title       = 'bw_newsletter_show_gender';
		$new_field->type        = 'hidden';
		$new_field->required    = 0;
		$new_field->published   = 1;
		$new_field->hidden      = 1;
		$new_field->default     = 0;

		if (JComponentHelper::getParams('com_bwpostman')->get('show_gender'))
		{
			$userFields[$this->BwPostman_field_ids['bw_gender']]->description
				= JText::_('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBS_FIELD_GENDER_DESC');
			$new_field->default = 1;
		}
		else
		{
			$userFields[$this->BwPostman_field_ids['bw_gender']]->published = 0;

			$this->unset_array[]    = $this->BwPostman_field_ids['bw_gender'];
		}

		$userFields[]   = $new_field;
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processLastnameField(&$userFields)
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('name_field_obligation'))
		{
			$com_params->set('show_name_field', '1');
			$userFields[$this->BwPostman_field_ids['last_name']]->required = 1;
		}

		if ($com_params->get('show_name_field'))
		{
			$userFields[$this->BwPostman_field_ids['last_name']]->account      = 1;
			$userFields[$this->BwPostman_field_ids['last_name']]->registration = 1;
		}
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processFirstnameField(&$userFields)
	{
		$com_params = JComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('firstname_field_obligation'))
		{
			$com_params->set('show_firstname_field', '1');
			$userFields[$this->BwPostman_field_ids['first_name']]->required = 1;
		}

		if ($com_params->get('show_firstname_field'))
		{
			$userFields[$this->BwPostman_field_ids['first_name']]->account      = 1;
			$userFields[$this->BwPostman_field_ids['first_name']]->registration = 1;
		}
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processAdditionalField(&$userFields)
	{
		$new_required   = new stdClass();
		$new_show       = new stdClass();
		$com_params = JComponentHelper::getParams('com_bwpostman');

		$new_required->name        = 'bw_newsletter_additional_required';
		$new_required->title       = 'bw_newsletter_additional_required';
		$new_required->type        = 'hidden';
		$new_required->required    = 0;
		$new_required->published   = 1;
		$new_required->hidden      = 1;
		$new_required->default     = 0;

		$new_show->name        = 'bw_newsletter_additional_show';
		$new_show->title       = 'bw_newsletter_additional_show';
		$new_show->type        = 'hidden';
		$new_show->required    = 0;
		$new_show->published   = 1;
		$new_show->hidden      = 1;
		$new_show->default     = 0;

		if ($com_params->get('special_field_obligation'))
		{
			$com_params->set('show_special', '1');
			$userFields[$this->BwPostman_field_ids['bw_newsletter_additional']]->required = 1;
			$new_required->default  = 1;
			$new_show->default      = 1;
		}
		elseif ($com_params->get('show_special'))
		{
			$special_label = $com_params->get('special_label');
			$special_desc  = $com_params->get('special_desc');
			$new_show->default      = 1;

			if ($special_label == '')
			{
				$special_label = 'PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBS_FIELD_SPECIAL_LABEL';
			}

			$userFields[$this->BwPostman_field_ids['bw_newsletter_additional']]->title = JText::_($special_label);

			if ($special_desc == '')
			{
				$special_desc = 'PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBS_FIELD_SPECIAL_DESC';
			}

			$userFields[$this->BwPostman_field_ids['bw_newsletter_additional']]->description = JText::_($special_desc);
		}
		else
		{
			$userFields[$this->BwPostman_field_ids['bw_newsletter_additional']]->published = 0;

			$this->unset_array[]    = $this->BwPostman_field_ids['bw_newsletter_additional'];
		}

		$userFields[]   = $new_required;
		$userFields[]   = $new_show;
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processNewsletterFormatField(&$userFields)
	{
		$new_field              = new stdClass();
		$new_field->name        = 'bw_newsletter_show_format';
		$new_field->title       = 'bw_newsletter_show_format';
		$new_field->type        = 'hidden';
		$new_field->required    = 0;
		$new_field->published   = 1;
		$new_field->hidden      = 1;
		$new_field->default     = 1;
		$new_field->data        = 'yes';

		$com_params = JComponentHelper::getParams('com_bwpostman');

		$userFields[$this->BwPostman_field_ids['bw_newsletter_format']]->value       = $com_params->get('default_emailformat');
		$userFields[$this->BwPostman_field_ids['bw_newsletter_format']]->description
			= JText::_('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MAILFORMAT_DESC');
		if (!$com_params->get('show_emailformat'))
		{
			$userFields[$this->BwPostman_field_ids['bw_newsletter_format']]->type = 'hidden';
			$new_field->default     = 0;
		}

		$userFields[]   = $new_field;
	}

	/**
	 * @param $userFields
	 *
	 * @since 2.0.0
	 */
	protected function processSubscriptionField(&$userFields)
	{
		$userFields[$this->BwPostman_field_ids['bw_newsletter_subscription']]->description
			= JText::_('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBSCRIPTION_CHECKBOX_DESC');
	}

	/**
	 *
	 * @param   object      $data
	 *
	 * @return  bool
	 *
	 * @since   2.0.0
	 */
	public function plgVmOnUserOrder(&$data)
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new JLogEntry('plgVmOnUserOrder reached', JLog::DEBUG, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		$subscription_data['bwpm_user2subscriber']  = $data->bw_newsletter_subscription;
		$subscription_data['gender']                = $data->bw_gender;
		$subscription_data['name']                  = $data->last_name;
		$subscription_data['firstname']             = $data->first_name;
		$subscription_data['special']               = $data->bw_newsletter_additional;
		$subscription_data['email']                 = $data->email;
		$subscription_data['emailformat']           = $data->bw_newsletter_format;
		$subscription_data['mailinglists']          = json_encode($this->params->get('ml_available', array()));
		$subscription_data['id']                    = $this->getJoomlaUserId($data->email);

		$session = JFactory::getSession();
		$session->set('plg_bwpm_buyer2subscriber.subscription_data', $subscription_data);

		if ($this->debug)
		{
			foreach ($subscription_data as $key=>$value)
			{
				$this->logger->addEntry(new JLogEntry(sprintf('plgVmOnUserOrder submitted data, key %s value $s ', $key, $value), JLog::DEBUG, $this->log_cat));
			}
		}

		return true;
	}

	/**
	 *
	 * @param   string      $email
	 *
	 * @return  integer     $user_id
	 *
	 * @since   2.0.0
	 */
	private function getJoomlaUserId($email)
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__users'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->Quote($email));

		$_db->setQuery($query);

		$user_id   = $_db->loadResult();

		return $user_id;
	}
}
