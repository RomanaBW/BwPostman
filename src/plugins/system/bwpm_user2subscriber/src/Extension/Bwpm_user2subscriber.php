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

namespace BoldtWebservice\Plugin\System\Bwpm_user2subscriber\Extension;

defined('_JEXEC') or die('Restricted access');

use ContentHelperRoute;
use Exception;
use JLoader;
use Joomla\CMS\Event\Model\PrepareFormEvent;
use Joomla\CMS\Event\User\AfterDeleteEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Event\User\BeforeSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\Form;
use Joomla\CMS\User\UserFactoryAwareTrait;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Site\Model\RegisterModel;
use BoldtWebservice\Plugin\BwPostman\System\U2S\Helper\BwpmUser2SubscriberHelper;
use RuntimeException;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', JPATH_ADMINISTRATOR.'/components/com_bwpostman/libraries');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Model', JPATH_SITE.'/components/com_bwpostman/src/Model');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Field', JPATH_SITE.'/components/com_bwpostman/src/Field');
JLoader::registerNamespace('BoldtWebservice\\Plugin\\BwPostman\\System\\U2S\\Field', JPATH_PLUGINS . '/system/bwpm_user2subscriber/form/fields');
JLoader::registerNamespace('BoldtWebservice\\Plugin\\BwPostman\\System\\U2S\\Helper', JPATH_PLUGINS . '/system/bwpm_user2subscriber/helpers');

/**
 * Class User2Subscriber
 *
 * @since  2.0.0
 */
final class Bwpm_user2subscriber extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use UserFactoryAwareTrait;
    use DatabaseAwareTrait;

    /**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected string $min_bwpostman_version    = '1.3.2';

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
	protected array $allowedContext = array(
		'com_users.registration',
	);

	/**
	 * Property to hold component enabled status
	 *
	 * @var    boolean
	 *
	 * @since  2.0.0
	 */
	protected bool $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	protected string $BwPostmanComponentVersion = '0.0.0';

	/**
	 * Property to hold form
     *
     * @var Form
	 *
	 * @since  2.0.0
	 */
	protected Form $form;

	/**
	 * Property to hold group
	 *
	 * @var    ?object
	 *
	 * @since  3.0.0
	 */
	protected ?object $group;

	/**
	 * Property to message
	 *
	 * @var    object
	 *
	 * @since  4.0.0
	 */
	protected object $_subject;

	/**
	 * Property to hold subscriber data stored at component
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	protected array $stored_subscriber_data = array();


	/**
	 * Property to hold logger
	 *
	 * @var    BwLogger
	 *
	 * @since  2.0.0
	 */
	private BwLogger $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	private string $log_cat  = 'Plg_U2S';

	/**
	 * Property to hold debug
	 *
	 * @var    bool
	 *
	 * @since  2.0.0
	 */
	private bool $debug;

    /**
     * Property to indicate use of legacy listeners
     *
     * @var    boolean
     *
     * @since  4.2.6
     */
    protected $allowLegacyListeners = false;

    /**
     * PlgSystemBWPM_User2Subscriber constructor.
     *
     * @param DispatcherInterface $dispatcher
     * @param array               $config
     *
     * @throws Exception
     * @since   2.0.0
     */
	public function __construct(DispatcherInterface $dispatcher, array $config = [])
	{
        $plugin = PluginHelper::getPlugin('system', 'bwpm_user2subscriber');

        $config['params'] = new Registry($plugin->params);

        parent::__construct($dispatcher, $config);

		$log_options    = array();
		$this->logger   = BwLogger::getInstance($log_options);
		$this->debug    = (bool)$this->params->get('debug_option', false);

		$this->setBwPostmanComponentStatus();
		$this->setBwPostmanComponentVersion();
	}

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since 4.2.6
     */
    public static function getSubscribedEvents(): array
    {
        // Only subscribe events if the component is installed and enabled
        if (!ComponentHelper::isEnabled('com_bwpostman'))
        {
            return [];
        }
        else
        {
            return [
                'onContentPrepareForm' => 'onContentPrepareForm',
                'onUserBeforeSave'     => 'onUserBeforeSave',
                'onUserAfterSave'      => 'onUserAfterSave',
                'onUserAfterDelete'    => 'onUserAfterDelete',
                'onAfterRender'        => 'onAfterRender',
            ];
        }
    }

    /**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function setBwPostmanComponentStatus(): void
    {
		$db        = $this->getDatabase();
		$query      = $db->getQuery(true);

		$query->select($db->quoteName('enabled'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);

			$enabled                = (bool)$db->loadResult();
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
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function setBwPostmanComponentVersion(): void
    {
		$db        = $this->getDatabase();
		$query      = $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);


			$result   = $db->loadResult();

			if ($result === null)
			{
				$result = '';
			}

			$manifest = json_decode($result, true);

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
     * Event method onContentPrepareForm
     *
     * @param PrepareFormEvent $event
     *
     * @return void
     *
     * @throws Exception
     *
     * @since  2.0.0
     */
	public function onContentPrepareForm(PrepareFormEvent $event): void
	{
        //		$this->logger->addEntry(new LogEntry('onContentPrepareForm reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));

		if (!$this->prerequisitesFulfilled())
		{
			return;
		}

        $form    = $event->getForm();
        $data    = $event->getData();
        $context = $form->getName();

        if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry(sprintf('Context is: %s', $context), BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

        if (!in_array($context, $this->allowedContext))
		{
			return;
		}

		$mailinglists   = $this->params->get('ml_available', array());
		$session = $this->getApplication()->getSession();
		$session->set('plg_bwpm_user2subscriber.ml_available', $mailinglists);
		$session->set('plg_bwpm_user2subscriber.show_desc', $this->params->get('show_desc', '1'));
		$session->set('plg_bwpm_user2subscriber.desc_length', $this->params->get('desc_length', '150'));

		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry(sprintf('Count mailinglists is: %s', count($mailinglists)), BwLogger::BW_DEBUG, $this->log_cat));
		}

		if (!count($mailinglists))
		{
			return;
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

		$this->form = $form;

		Form::addFormPath(JPATH_PLUGINS . '/system/bwpm_user2subscriber/form');

		$this->form->loadFile('form', false);
		$this->group = null;

        if ($this->debug)
        {
            $this->logger->addEntry(new LogEntry('Form is not an instance of JForm', BwLogger::BW_DEVELOPMENT, $this->log_cat));
        }

		$this->logger->addEntry(new LogEntry('Form U2S is instance'));

		// Add CSS and JS for the radio fields
		$doc = $this->getApplication()->getDocument();
		$wa = $doc->getWebAssetManager();
		$wr = $wa->getRegistry();
		$wr->addRegistryFile('media/plg_system_bwpm_user2subscriber/joomla.asset.json');

		$wa->useStyle('plg_system_bwpm_user2subscriber.bwpm_user2subscriber');

		$wa->useScript('plg_system_bwpm_user2subscriber.bwpm_user2subscriber');

		// Get disclaimer link if disclaimer enabled at component and plugin
		$disclaimer_link = $this->getDisclaimerLink();

		// Add JS for disclaimer modal box
		if ($disclaimer_link)
		{
			$disclaimer_script =	'	var dc_src = "' . $disclaimer_link . '";' . "\n";

			$wa->addInlineScript($disclaimer_script);
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
	}

	/**
	 * Method to check if prerequisites are fulfilled
	 *
	 * @return  bool
	 *
	 * @since   2.0.0
	 */
	protected function prerequisitesFulfilled(): bool
	{
		if (!$this->BwPostmanComponentEnabled)
		{
			return false;
		}

		if (version_compare($this->BwPostmanComponentVersion, $this->min_bwpostman_version, 'lt'))
		{
			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry('Component version not met!', BwLogger::BW_ERROR, $this->log_cat));
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
	protected function processGenderField(): void
    {
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if (!$com_params->get('show_gender', '1'))
		{
			$this->form->removeField('gender', $this->group);
		}
	}

	/**
	 * Method to prepare input field last name
	 *
	 * @since 2.0.0
	 */
	protected function processLastnameField(): void
    {
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('name_field_obligation', '1'))
		{
			$this->form->setValue('name_required', $this->group, 1);
		}

		if (!$com_params->get('show_name_field', '1') && !$com_params->get('name_field_obligation', '1'))
		{
			$this->form->removeField('bwpm_name', $this->group);
		}
	}

	/**
	 * Method to prepare input field first name
	 *
	 * @since 2.0.0
	 */
	protected function processFirstnameField(): void
    {
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('firstname_field_obligation', '1'))
		{
			$this->form->setValue('firstname_required', $this->group, 1);
		}

		if (!$com_params->get('show_firstname_field', '1') && !$com_params->get('firstname_field_obligation', '1'))
		{
			$this->form->removeField('firstname', $this->group);
		}
	}

	/**
	 * Method to prepare input field additional
	 *
	 * @since 2.0.0
	 */
	protected function processAdditionalField(): void
    {
		$com_params = ComponentHelper::getParams('com_bwpostman');

		if ($com_params->get('special_field_obligation', '0'))
		{
			$this->form->setValue('additional_required', $this->group, 1);
		}

		if (!$com_params->get('show_special', '1') && !$com_params->get('special_field_obligation', '0'))
		{
			$this->form->removeField('special', $this->group);
		}
		else
		{
			$special_label = $com_params->get('special_label', '');
			$special_desc  = $com_params->get('special_desc', '');

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
	 * Method to prepare input field emailformat
	 *
	 * @since 2.0.0
	 */
	protected function processNewsletterFormatField(): void
    {
		$com_params = ComponentHelper::getParams('com_bwpostman');

		$this->form->setFieldAttribute('emailformat', 'default', $com_params->get('default_emailformat', '1'), $this->group);

		if ($com_params->get('show_emailformat', '1'))
		{
			$this->form->setFieldAttribute('emailformat', 'required', 'true', $this->group);
		}
		else
		{
			$this->form->setFieldAttribute('emailformat_show', 'default', $com_params->get('show_emailformat', '1'), $this->group);
		}
	}

	/**
	 * Method to prepare input field mailinglists
	 *
	 * @since 2.0.0
	 */
	protected function processSelectedMailinglists(): void
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
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function processCaptchaField(): void
    {
		$captcha = BwPostmanHelper::getCaptcha();
		$this->form->setFieldAttribute('bw_captcha', 'name', 'bwp-' . $captcha, $this->group);

		$session = $this->getApplication()->getSession();
		$session->set('plg_bwpm_user2subscriber.captcha', $captcha);
	}

	/**
	 * Method to check if disclaimer enabled and prepare the disclaimer link
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since 2.1.0
	 */
	protected function getDisclaimerLink(): string
	{
		$com_params = ComponentHelper::getParams('com_bwpostman');
		$disclaimer_link = '';

		if ($this->params->get('disclaimer', '0') && $com_params->get('disclaimer', '0'))
		{
			// Extends the disclaimer link with '&tmpl=component' to see only the content
			$tpl_com = '&amp;tmpl=component';
			// Disclaimer article and target_blank or not
			if ($com_params->get('disclaimer_selection', '1') == 1 && $com_params->get('article_id', '0') > 0)
			{
				$disclaimer_link = Route::_(Uri::base() . ContentHelperRoute::getArticleRoute($com_params->get('article_id', '0') . $tpl_com));
			}
			// Disclaimer menu item and target_blank or not
			elseif ($com_params->get('disclaimer_selection', '1') == 2 && $com_params->get('disclaimer_menuitem', '0') > 0)
			{
				if (($this->getApplication()->get('sef') === '1' || $this->getApplication()->get('sef') === true))
				{
					$tpl_com = '?tmpl=component';
				}
				$disclaimer_link = Route::_("index.php?Itemid={$com_params->get('disclaimer_menuitem', '0')}") . $tpl_com;
			}
			// Disclaimer url and target_blank or not
			else
			{
				$disclaimer_link = $com_params->get('disclaimer_link', '');
			}
		}

		return $disclaimer_link;
	}

	/**
	 * Method to remove input field disclaimer
	 *
	 * @since 2.1.0
	 */
	protected function removeDisclaimerField(): void
    {
		$this->form->removeField('bwpdisclaimer', $this->group);
	}

    /**
     * Event method onUserBeforeSave
     *
     * @param BeforeSaveEvent $event
     *
     * @return void
     *
     * @since  2.0.0
     */
	public function onUserBeforeSave(BeforeSaveEvent $event): void
	{
//        new BeforeSaveEvent('onEventName', ['subject' => $oldUserArray, 'isNew' => $isNew, 'data' => $data]);

        if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserBeforeSave reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return;
		}

        $oldUser = $event->getArgument('subject');
        $newUser = $event->getArgument('data');

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

		$session = $this->getApplication()->getSession();
		$session->set('plg_bwpm_user2subscriber.changeMail', $changeMail);

		if ($old_activation != '' && ($old_activation != $new_activation))
		{
			$session->set('plg_bwpm_user2subscriber.userid', $user_id);
			$session->set('plg_bwpm_user2subscriber.activation', $old_activation);
		}
	}

    /**
     * Event method onUserAfterSave
     *
     * @param AfterSaveEvent $event
     *
     * @return void
     *
     * @throws Exception
     *
     * @since  2.0.0
     */
	public function onUserAfterSave(AfterSaveEvent $event): void
	{
//        new AfterSaveEvent('onEventName', ['subject' => $userArray, 'isNew' => $isNew, 'savingResult' => $result, 'errorMessage' => $errorStr]);

        if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserAfterSave reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return;
		}

        $data   = $event->getArgument('subject');
        $isNew  = $event->getArgument('isNew');
        $result = $event->getArgument('savingResult');

        if (!$result)
		{
			return;
		}

		$session = $this->getApplication()->getSession();

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
		$user_id   = (int)$data['id'];

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
			$this->processNewUser($user_mail, $user_id, $subscriber_data);

            return;
		}

		$activation     = $session->get('plg_bwpm_user2subscriber.activation');
		$changeMail     = $session->get('plg_bwpm_user2subscriber.changeMail');
		$task           = $this->getApplication()->input->get->get('task', '', 'string');
		$token          = $this->getApplication()->input->get->get('token', '', 'string');
		$session->clear('plg_bwpm_user2subscriber');

		$this->stored_subscriber_data = BwpmUser2SubscriberHelper::getSubscriptionData($user_id);
		$subscriber_id                = BwpmUser2SubscriberHelper::hasSubscription($user_mail);
		$subscriber_is_to_activate    = BwpmUser2SubscriberHelper::isToActivate($user_mail);

		if (($task == 'registration.activate' && $token == $activation) || ($this->getApplication()->isClient('administrator') && $activation != ''))
		{
			if ($subscriber_is_to_activate)
			{
				$this->activateSubscription($user_mail);

                return;
			}
		}

		if (!$subscriber_is_to_activate)
		{
			$new_mailinglists           = $this->params->get('ml_available', array());

			if (is_string($new_mailinglists) && $new_mailinglists !== '')
			{
				$new_mlArray[] = $new_mailinglists;
				$new_mailinglists = $new_mlArray;
			}

			$updateMailinglists_result  = BwpmUser2SubscriberHelper::updateSubscribedMailinglists($subscriber_id, $new_mailinglists);

			if (!$updateMailinglists_result)
			{
				return;
			}
		}

		if ($this->params->get('auto_update_email_option', '1') && $changeMail)
		{
			$this->updateMailaddress($user_mail);
		}
	}

	/**
	 * @param string $user_mail
	 * @param int    $user_id
	 * @param array  $subscriber_data
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function processNewUser(string $user_mail, int $user_id, array $subscriber_data): bool
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
			$subscriber_id  = BwpmUser2SubscriberHelper::hasSubscription($user_mail);

			if ($subscriber_id)
			{
				$update_userid_result   = false;

				if ($user_id)
				{
					$update_userid_result = BwpmUser2SubscriberHelper::updateUserIdAtSubscriber($user_mail, $user_id);
				}

				$new_mailinglists       = $subscriber_data['mailinglists'];
				$update_mailinglists    = BwpmUser2SubscriberHelper::updateSubscribedMailinglists($subscriber_id, $new_mailinglists);

				return ($update_mailinglists && $update_userid_result);
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());

			return false;
		}

		return $this->subscribeToBwPostman($user_mail, $user_id, $subscriber_data);
	}

	/**
	 * Method to Subscribe to BwPostman while Joomla registration
	 *
	 * @param string $user_mail       User mail address
	 * @param int    $user_id         Joomla User ID
	 * @param array  $subscriber_data subscriber date submitted by form
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function subscribeToBwPostman(string $user_mail, int $user_id, array $subscriber_data): bool
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

			$subscriber     = BwpmUser2SubscriberHelper::createSubscriberData($user_mail, $user_id, $subscriber_data, $mailinglist_ids);

			$subscriber_id  = BwpmUser2SubscriberHelper::saveSubscriber($subscriber);

			if (!$subscriber_id)
			{
				return false;
			}

			$ml_save_result     = BwpmUser2SubscriberHelper::saveSubscribersMailinglists($subscriber_id, $mailinglist_ids);
		}
		catch (Exception $e)
		{
			$this->_subject->$e->getMessage();
			return false;
		}

		return $ml_save_result;
	}

	/**
	 * Method to activate subscription when Joomla account is confirmed or order in VM is confirmed
	 *
	 * @param string $user_mail mail address of new subscriber
	 *
	 * @return  bool        True on success
	 *
	 * @throws Exception
	 * @since  2.0.0
	 */
	protected function activateSubscription(string $user_mail): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('activate subscription reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if ($user_mail == '')
		{
			return false;
		}

		$activation_ip  = $this->getApplication()->input->server->get('REMOTE_ADDR', '', '');
		$subscriber_id  = BwpmUser2SubscriberHelper::getSubscriberIdByEmail($user_mail);

		$db	= $this->getDatabase();
		$query	= $db->getQuery(true);

		$date   = Factory::getDate();
		$time   = $date->toSql();

		$query->update($db->quoteName('#__bwpostman_subscribers'));
		$query->set($db->quoteName('status') . ' = ' . 1);
		$query->set($db->quoteName('activation') . ' = ' . $db->quote(''));
		$query->set($db->quoteName('confirmation_date') . ' = ' . $db->quote($time, false));
		$query->set($db->quoteName('confirmed_by') . ' = ' . 0);
		$query->set($db->quoteName('confirmation_ip') . ' = ' . $db->quote($activation_ip));
		$query->where($db->quoteName('email') . ' = "' . $user_mail . '"');

		try
		{
			$db->setQuery($query);
			$res = $db->execute();

			$params    = ComponentHelper::getParams('com_bwpostman');
			$send_mail = $params->get('activation_to_webmaster', '0');

			// @ToDo: How could I get here with no object $this->stored_subscriber_data
			if ($send_mail && $res && $subscriber_id)
			{
//				require_once(JPATH_SITE . '/components/com_bwpostman/models/register.php');
				$model = new RegisterModel();

				$model->sendActivationNotification($this->stored_subscriber_data['id']);
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		return $res;
	}

	/**
	 * @param string $user_mail
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	protected function updateMailaddress(string $user_mail): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('update mail address', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		$update_email_result    = false;

		try
		{
			if (($this->stored_subscriber_data['email'] != $user_mail))
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
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	protected function updateEmailOfSubscription(): bool
	{
		$result = false;

		$db	= $this->getDatabase();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_subscribers'));
		$query->set($db->quoteName('email') . " = " . $db->quote($this->stored_subscriber_data['email']));
		$query->where($db->quoteName('id') . ' = ' . $db->quote((int)$this->stored_subscriber_data['id']));

		try
		{
			$db->setQuery($query);

			$result  = $db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

    /**
     * Event method onUserAfterDelete
     *
     * @param AfterDeleteEvent $event
     *
     * @return void
     *
     * @throws Exception
     *
     * @since  2.0.0
     */
	public function onUserAfterDelete(AfterDeleteEvent $event): void
	{
//        new AfterDeleteEvent('onEventName', ['subject' => $userArray, 'deletingResult' => $result, 'errorMessage' => $errorStr]);
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserAfterDelete reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->BwPostmanComponentEnabled)
		{
			return;
		}

        $data = $event->getArgument('subject');
        $success = $event->getArgument('deletingResult');

        if (!$success)
		{
			return;
		}

        $user_id    = (int)$data['id'];

		$this->stored_subscriber_data = BwpmUser2SubscriberHelper::getSubscriptionData($user_id);

		if ($this->params->get('auto_delete_option', '0'))
		{
			$this->deleteSubscription();
		}
		else
		{
			$this->removeUseridFromSubscription($user_id);
		}
	}

	/**
	 * Method to delete subscription, if Joomla account is deleted
	 *
	 * @return  bool        True on success
	 *
	 * @since  2.0.0
	 */
	protected function deleteSubscription(): bool
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

            if (key_exists('id', $this->stored_subscriber_data) && $this->stored_subscriber_data['id'] != 0)
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
	protected function deleteSubscriber(): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('delete subscriber', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$db	= $this->getDatabase();
			$query	= $db->getQuery(true);

			$query->delete($db->quoteName('#__bwpostman_subscribers'));
			$query->where($db->quoteName('id') . ' =  ' . $db->quote($this->stored_subscriber_data['id']));

			$db->setQuery($query);

			$res  = $db->execute();
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
	protected function deleteSubscribedMailinglists(): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('delete mailinglists', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$db	= $this->getDatabase();
			$query	= $db->getQuery(true);

			$query->delete($db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($db->quoteName('subscriber_id') . ' =  ' . $db->quote($this->stored_subscriber_data['id']));

			$db->setQuery($query);

			$res  = $db->execute();
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
	 * @param int $user_id User ID
	 *
	 * @return  bool        True on success
	 *
	 * @throws Exception
	 * @since  2.0.0
	 */
	protected function removeUseridFromSubscription(int $user_id): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('remove UserID from subscription', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		try
		{
			$res_update_subscriber      = false;

            if ($this->stored_subscriber_data['id'] != 0)
			{
				$db	= $this->getDatabase();
				$query	= $db->getQuery(true);

				$query->update($db->quoteName('#__bwpostman_subscribers'));
				$query->set($db->quoteName('user_id') . " = " . 0);
				$query->where($db->quoteName('user_id') . ' = ' . $db->quote($user_id));

				$db->setQuery($query);

				$res_update_subscriber  = $db->execute();
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			return false;
		}

		$this->getApplication()->getIdentity();

		return $res_update_subscriber;
	}

    /**
     * Event method onAfterRender
     *
     * @param Event $event
     *
     * @return  void
     *
     * @throws Exception
     *
     * @since  2.0.0
     */
	public function onAfterRender(Event $event): void
    {
//        $context = '';
//
//        if (!in_array($context, $this->allowedContext))
//        {
//            return;
//        }
		$session = $this->getApplication()->getSession();
		$jinput  = $this->getApplication()->input;

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
