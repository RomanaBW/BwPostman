<?php
/**
 * BwPostman Newsletter TimeControl Plugin
 *
 * BwPostman TimeControl Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman TimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;

/**
 * @package     BwPostman TimeControl Plugin
 *
 * @since       0.9.0
 */
class plgBwPostmanBwTimeControl extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    bool Plugin enabled?
	 *
	 * @since       0.9.0
	 */
	protected $_enabled;

	/**
	 * @var string
	 *
	 * @since 0.9.0
	 */
	protected $minBwpostmanVersion = '2.3.1';

	/**
	 * Property to hold form
	 *
	 * @var    object
	 *
	 * @since  0.9.0
	 */
	protected $form;

	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 *
	 * @since  0.9.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Property to hold component enabled status
	 *
	 * @var    boolean
	 *
	 * @since  0.9.0
	 */
	protected $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	protected $BwPostmanComponentVersion = '0.0.0';

	/**
	 * Property to hold logger
	 *
	 * @var    object
	 *
	 * @since  0.9.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	private $log_cat = 'BwPm_TC';

	/**
	 * Property to hold debug
	 *
	 * @var    boolean
	 *
	 * @since  0.9.0
	 */
	private $debug = false;

	/**
	 * Definition of which contexts to allow in this plugin
	 *
	 * @var    array
	 *
	 * @since  0.9.0
	 */
	protected $allowedContext = array(
		'com_bwpostman.newsletter',
	);

	/**
	 * plgBwPostmanBwTimeControl constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since       0.9.0
	 */
	function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_enabled = false;

		$log_options  = array('text_file' => 'bwpostman/BwPmTimecontrol.log');
		$this->logger = BwLogger::getInstance($log_options);
		$this->debug  = false;

		// Do not load if BwPostman version is not supported or BwPostman isn't detected
		$this->setBwPostmanComponentStatus();
		$this->setBwPostmanComponentVersion();
		$this->loadLanguage();
		// @ToDo check licence!
//		$this->setDownloadId();
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @since 0.9.0
	 */
	protected function setBwPostmanComponentStatus()
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('enabled'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('com_bwpostman'));


		try
		{
			$_db->setQuery($query);

			$enabled                         = $_db->loadResult();
			$this->BwPostmanComponentEnabled = $enabled;
			$this->_enabled                  = true;

			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry(sprintf('Component is enabled: %s', $enabled), BwLogger::BW_DEBUG,
					$this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$message                         = 'Database error while getting component status, error message is ' . $e->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to set component version property
	 *
	 * @return void
	 *
	 * @since 0.9.0
	 */
	protected function setBwPostmanComponentVersion()
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));

		try
		{
			$_db->setQuery($query);

			$manifest                        = json_decode($_db->loadResult(), true);
			$this->BwPostmanComponentVersion = $manifest['version'];

			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry(sprintf('Component version is: %s', $manifest['version']),
					BwLogger::BW_DEBUG, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentVersion = '0.0.0';
			$message                         = 'Database error while getting component version, error message is ' . $e->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to load further language files
	 *
	 * @since 0.9.0
	 */
	protected function loadLanguageFiles()
	{
		$lang = Factory::getApplication()->getLanguage();

		//Load first english file of component
		$lang->load('com_bwpostman', JPATH_SITE, 'en_GB', true);

		//load specific language of component
		$lang->load('com_bwpostman', JPATH_SITE, null, true);

		//Load specified other language files in english
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR, 'en_GB', true);

		// and other language
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR, null, true);
	}

	/**
	 * Event method onContentPrepareForm
	 *
	 * @param mixed  $form JForm instance
	 * @param object $data Form values
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since  0.9.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onContentPrepareForm reached', BwLogger::BW_DEBUG, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		$context = $form->getName();

		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry(sprintf('Context is: %s', $context), BwLogger::BW_DEBUG, $this->log_cat));
		}

		if (!in_array($context, $this->allowedContext))
		{
			return true;
		}

		$scheduledXml = $this->createFieldsetScheduled();
		$form->setField($scheduledXml);

		if (is_object($data) && property_exists($data, 'id'))
		{
			$scheduledData = $this->getItem((int) $data->id);

			if (is_array($scheduledData))
			{
				$form->setValue('scheduled_date', 'scheduled', $scheduledData['scheduled_date']);
				$form->setValue('ready_to_send', 'scheduled', $scheduledData['ready_to_send']);
			}
		}

		return true;
	}

	/**
	 * Method to check if prerequisites are fulfilled
	 *
	 * @return  bool
	 *
	 * @since   0.9.0
	 */
	protected function prerequisitesFulfilled()
	{
		if (!$this->BwPostmanComponentEnabled)
		{
			return false;
		}

		if (version_compare($this->BwPostmanComponentVersion, $this->minBwpostmanVersion, 'lt'))
		{
			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry(sprintf('Component version not met!'), BwLogger::BW_ERROR,
					$this->log_cat));
			}

			return false;
		}

		return true;
	}

	/**
	 * Method to manipulate form before validation
	 *
	 * @param object $form
	 *
	 * @return    bool    true on success
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanBeforeNewsletterControllerValidate(&$form)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		$scheduledXml = $this->createFieldsetScheduled();
		$form->setField($scheduledXml);

		return true;
	}

	/**
	 * Method to manipulate form before validation
	 *
	 * @param array $properties
	 *
	 * @return    bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanAfterNewsletterModelGetProperties(&$properties)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		$scheduledDate                = $this->getItem($properties['id']);
		$properties['scheduled_date'] = $scheduledDate['scheduled_date'];
		$properties['ready_to_send']  = $scheduledDate['ready_to_send'];

		return true;
	}

	/**
	 * Method to manipulate form before validation
	 *
	 * @return    bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanMaintenanceStartCron()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		require_once(JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers/phpcron.php');

		$bwpostmancron = new BwPostmanPhpCron();

		$result= $bwpostmancron->runCronServer();

		return $result;
	}

	/**
	 * Method to manipulate form before validation
	 *
	 * @return    bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanMaintenanceStopCron()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		require_once(JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers/phpcron.php');

		$bwpostmancron = new BwPostmanPhpCron();

		$bwpostmancron->stopCronServer();

		return true;
	}

	/**
	 * Method to prepare toolbar buttons for BwTimeControl at campaigns
	 *
	 * @return    boolean    true on success
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanMaintenanceRenderLayout()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		if ($permissions['view']['maintenance'])
		{
			$link = 'index.php?option=com_bwpostman&view=maintenance&task=maintenance.startCron';
			BwPostmanHTMLHelper::quickiconButton(
				$link,
				'icon-48-cron-start.png',
				Text::_("PLG_BWTIMECONTROL_MAINTENANCE_START_CRON"),
				0,
				0
			);

			$link = 'index.php?option=com_bwpostman&view=maintenance&task=maintenance.stopCron';
			BwPostmanHTMLHelper::quickiconButton(
				$link,
				'icon-48-cron-stop.png',
				Text::_("PLG_BWTIMECONTROL_MAINTENANCE_STOP_CRON"),
				0,
				0
			);
		}

		$message  = Text::_("PLG_BWTIMECONTROL_MAINTENANCE_STARTING_CRON");
		$document = Factory::getApplication()->getDocument();
		$document->addScriptDeclaration("let message = '$message'");
		$document->addScript(Uri::root(true) . '/plugins/bwpostman/bwtimecontrol/assets/js/bwtimecontrol.js');

		return true;
	}

	/**
	 * Method to save scheduled date to table
	 *
	 * @param array $data
	 *
	 * @return    bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	public function onBwPostmanAfterNewsletterModelSave(&$data)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		if (!key_exists('scheduled_date', $data))
		{
			$data['scheduled_date'] = '';
		}

		if (!key_exists('ready_to_send', $data))
		{
			$data['ready_to_send'] = 0;
		}

		$scheduledData = array(
			'newsletter_id'  => $data['id'],
			'scheduled_date' => $data['scheduled_date'],
			'ready_to_send'  => $data['ready_to_send'],
		);

		require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/newsletter.php');
		$nlModel = JModelLegacy::getInstance('Newsletter', 'BwPostmanModel');
		$error   = array();

		$data = $nlModel->preSendChecks($error, $data['id'], true);

		if (count($error))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_PRE_CHECK',
				$data['id']), 'error');

			return false;
		}

		$this->saveItem($scheduledData);

		return true;
	}

	/**
	 * Method to get schedule data from table
	 *
	 * @param integer $nl_id
	 *
	 * @return    array
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	private function getItem($nl_id)
	{
		$db             = Factory::getDbo();
		$scheduled_date = $db->getNullDate();

		if ($nl_id !== 0)
		{
			$query = $db->getQuery(true);

			$query->select($db->quoteName('scheduled_date'));
			$query->select($db->quoteName('ready_to_send'));
			$query->from($db->quoteName('#__bwpostman_tc_schedule'));
			$query->where($db->quoteName('newsletter_id') . ' = ' . $db->Quote($nl_id));

			try
			{
				$db->setQuery($query);

				$scheduled_date = $db->loadAssoc();
			}
			catch (RuntimeException $e)
			{
				$message = 'Database error while getting itemId at TC, error message is ' . $e->getMessage();
				Factory::getApplication()->enqueueMessage($message, 'error');
			}
		}

		return $scheduled_date;
	}

	/**
	 * Method to manipulate schedule data at table
	 *
	 * @param array $scheduledDate
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 *
	 * @since    0.9.0
	 */
	private function saveItem($scheduledDate)
	{
		$savedDate = $this->getItem($scheduledDate['newsletter_id']);

		if ($savedDate['scheduled_date'] === null && $scheduledDate['scheduled_date'] === ''
			|| $savedDate === null && $scheduledDate['scheduled_date'] === null)
		{
			return true;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// New scheduled date
		if ($savedDate['scheduled_date'] === null && $scheduledDate['scheduled_date'] !== '')
		{
			$query->insert($db->quoteName('#__bwpostman_tc_schedule'));
			$query->columns(array(
				$db->quoteName('scheduled_date'),
				$db->quoteName('newsletter_id'),
				$db->quoteName('ready_to_send'),
			));
			$query->values(
				$db->Quote($scheduledDate['scheduled_date']) . ',' .
				$db->Quote($scheduledDate['newsletter_id']) . ',' .
				$db->Quote($scheduledDate['ready_to_send'])
			);
		}
		// Update scheduled date
		elseif ($savedDate['scheduled_date'] !== null && $scheduledDate['scheduled_date'] !== '')
		{
			$query->update($db->quoteName('#__bwpostman_tc_schedule'));
			$query->set($db->quoteName('scheduled_date') . " = " . $db->Quote($scheduledDate['scheduled_date']));
			$query->set($db->quoteName('ready_to_send') . " = " . $db->Quote($scheduledDate['ready_to_send']));
			$query->where($db->quoteName('newsletter_id') . ' = ' . $db->Quote($scheduledDate['newsletter_id']));
		}
		else
		{
			$query->delete($db->quoteName('#__bwpostman_tc_schedule'));
			$query->where($db->quoteName('newsletter_id') . ' = ' . $db->Quote($scheduledDate['newsletter_id']));
		}

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$message = 'Database error while saving item at TC, error message is ' . $e->getMessage();
			Factory::getApplication()->enqueueMessage($message, 'error');
		}

		return true;
	}

	/**
	 *
	 * @return SimpleXMLElement
	 *
	 * @since 0.9.0
	 */
	private function createFieldsetScheduled()
	{
		$scheduledXml = new SimpleXMLElement(
			'<fieldset name="scheduled">
				<field 
				name="scheduled_date" 
				type="calendar"
				default=""
				label="PLG_BWTIMECONTROL_SCHEDULE_DATE_LABEL"
				labelclass="control-label"
				description="PLG_BWTIMECONTROL_SCHEDULE_DATE_DESC"
				format="%Y-%m-%d %H:%M:%S"
				size="22"
				filter="user_utc"
				class="inputbox"
				/>
				<field 
				name="ready_to_send" 
				type="list"
				default="0"
				label="PLG_BWTIMECONTROL_READY_TO_SEND_LABEL"
				labelclass="control-label"
				description="PLG_BWTIMECONTROL_READY_TO_SEND_DESC"
				class="chzn-color-state"
				filter="intval"
				>
					<option value="1">COM_BWPOSTMAN_YES</option>
					<option value="0">COM_BWPOSTMAN_NO</option>
				</field>
				</fieldset>');

		return $scheduledXml;
	}

	/**
	 * Enhance where clause of query to sendmailqueue to get only items of manual or automated sending
	 *
	 * This method will be used at sendmailqueue pop() to get the correct entries to send as also for counting number of
	 * mails to send.
	 *
	 * @param   object      $query           query to manipulate
	 * @param   boolean     $fromComponent   do we come from component or from plugin
	 *
	 * @return    bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since     0.9.0
	 */
	public function onBwPostmanGetAdditionalQueueWhere(&$query, $fromComponent)
	{
		Factory::getApplication()->getUserState('com_bwpostman.newsletter.idToSend', 0);

		// Get content ids of automated newsletters
		$allAutomatedContentIds = $this->getAutomatedContentIds();

		// If we come from component, content id **must not** be in list of automated content ids,
		// if we come from automation, content id **must** be in this list
		if (count($allAutomatedContentIds))
		{
			if ($fromComponent)
			{
				$query->where('content_id' . ' NOT IN (' . implode(',', $allAutomatedContentIds) . ')');
			}
			else
			{
				$query->where('content_id' . ' IN (' . implode(',', $allAutomatedContentIds) . ')');
			}
		}

		return true;
	}

	/**
	 * Method to get all ids fo newsletters, which are in the table tc_schedule
	 *
	 * @return    array
	 *
	 * @throws Exception
	 *
	 * @since     0.9.0
	 */
	public function getAutomatedNlIds()
	{
		$db	= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('newsletter_id'));
		$query->from($db->quoteName('#__bwpostman_tc_schedule'));

		try
		{
			$db->setQuery($query);

			$allAutomatedNlsIds = $db->loadColumn();

			return $allAutomatedNlsIds;
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$message                         = 'Database error while getting all automated nl ids, error message is ' . $e->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		return array();
	}

	/**
	 * Method to get all content ids for newsletters, which are in the table tc_schedule
	 *
	 * @return    array
	 *
	 * @throws Exception
	 *
	 * @since     0.9.0
	 */
	public function getAutomatedContentIds()
	{
		// Get ids of newsletters, which are marked as automated
		$automatedNlIds = $this->getAutomatedNlIds();

		if (count($automatedNlIds))
		{
			$db	= Factory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('DISTINCT ' . $db->quoteName('id'));
			$query->from($db->quoteName('#__bwpostman_sendmailcontent'));
			$query->where('nl_id' . ' IN ('  . implode(',', $automatedNlIds) . ')');

			try
			{
				$db->setQuery($query);

				$allAutomatedContentIds = $db->loadColumn();

				return $allAutomatedContentIds;
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				$this->BwPostmanComponentEnabled = false;
				$message                         = 'Database error while getting all automated nl ids, error message is ' . $e->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
			}
		}

		return array();
	}
}
