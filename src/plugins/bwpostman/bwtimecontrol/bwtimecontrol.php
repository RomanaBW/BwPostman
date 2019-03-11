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

/**
 * @package     BwPostman TimeControl Plugin
 *
 * @since       2.3.0
 */
class plgBwPostmanBwTimeControl extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    bool Plugin enabled?
	 *
	 * @since       2.3.0
	 */
	protected $_enabled;

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	protected $min_bwpostman_version = '2.2.1';

	/**
	 * Property to hold form
	 *
	 * @var    object
	 *
	 * @since  2.3.0
	 */
	protected $form;

	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 *
	 * @since  2.3.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Property to hold component enabled status
	 *
	 * @var    boolean
	 *
	 * @since  2.3.0
	 */
	protected $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  2.3.0
	 */
	protected $BwPostmanComponentVersion = '0.0.0';

	/**
	 * Property to hold logger
	 *
	 * @var    object
	 *
	 * @since  2.3.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  2.3.0
	 */
	private $log_cat  = 'BwPm_TC';

	/**
	 * Property to hold debug
	 *
	 * @var    boolean
	 *
	 * @since  2.3.0
	 */
	private $debug    = false;

	/**
	 * Definition of which contexts to allow in this plugin
	 *
	 * @var    array
	 *
	 * @since  2.3.0
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
	 * @since       2.3.0
	 */
	function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_enabled = false;

		$log_options    = array('text_file' => 'bwpostman/BwPmTimecontrol.log');
		$this->logger   = new BwLogger($log_options);
		$this->debug    = false;

		// Do not load if BwPostman version is not supported or BwPostman isn't detected
		$this->setBwPostmanComponentStatus();
		$this->setBwPostmanComponentVersion();
		$this->loadLanguage();
		// @ToDo check licence!
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @since 2.3.0
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
			$this->_enabled = true;

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
	 * @since 2.3.0
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
	 * @since 2.3.0
	 */
	protected function loadLanguageFiles()
	{
		$lang = JFactory::getLanguage();

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
	 * @param   mixed  $form  JForm instance
	 * @param   object $data  Form values
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since  2.3.0
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

		$scheduledXml = $this->createFieldsetScheduled();
		$form->setField($scheduledXml);

		if (is_object($data) && property_exists($data, 'id'))
		{
			$scheduledData = $this->getItem((int)$data->id);

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
	 * @since   2.3.0
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
	 * Method to manipulate form before validation
	 *
	 * @param 	object $form
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanBeforeNewsletterControllerValidate (&$form)
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
	 * @param 	array $properties
	 *
	 * @return 	bool	true on success
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanAfterNewsletterModelGetProperties (&$properties)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		$scheduledDate = $this->getItem($properties['id']);
		$properties['scheduled_date'] = $scheduledDate['scheduled_date'];
		$properties['ready_to_send'] = $scheduledDate['ready_to_send'];

		return true;
	}

	/**
	 * Method to manipulate form before validation
	 *
	 * @throws \Exception
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanMaintenanceStartCron ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		require_once(JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers/phpcron.php');

		$bwpostmancron = new BwPostmanPhpCron();

		$bwpostmancron->runCronServer();

		return true;
	}


	/**
	 * Method to manipulate form before validation
	 *
	 * @throws \Exception
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanMaintenanceStopCron ()
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
	 * Method to  test automation
	 *
	 * @access	public
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignsTaskAutoTest ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to activate automated campaign
	 *
	 * @access	public
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignsTaskActivate ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to prepare toolbar buttons for BwTimeControl at campaigns
	 *
	 * @access	public
	 *
	 * @return 	boolean 	true on success
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignsPrepareToolbar ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

//		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.autotest', 'question-circle', 'question-circle', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST'), true);
//		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.activate', 'publish', 'publish', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE'), true);
//		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.dueSend', 'broadcast', 'broadcast', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_DUESEND'), false);

		return true;
	}

	/**
	 * Method to prepare toolbar buttons for BwTimeControl at campaigns
	 *
	 * @return 	boolean 	true on success
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanMaintenanceRenderLayout ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$permissions	= JFactory::getApplication()->getUserState('com_bwpm.permissions');


		if ($permissions['view']['maintenance'])
		{
			$link = 'index.php?option=com_bwpostman&view=maintenance&task=maintenance.startCron';
			BwPostmanHTMLHelper::quickiconButton(
				$link,
				'icon-48-maintenance.png',
				JText::_("PLG_BWPOSTMAN_BWTIMECONTROL_MAINTENANCE_START_CRON"),
				0,
				0
			);

			$link = 'index.php?option=com_bwpostman&view=maintenance&task=maintenance.stopCron';
			BwPostmanHTMLHelper::quickiconButton(
				$link,
				'icon-48-maintenance.png',
				JText::_("PLG_BWPOSTMAN_BWTIMECONTROL_MAINTENANCE_STOP_CRON"),
				0,
				0
			);

		}

		return true;
	}

	/**
	 * Method to switch content table and queue table to BwTimeControl-tables
	 *
	 * @access	public
	 *
	 * @param   object      $table_name
	 * @param   object      $tblSendMailQueue
	 * @param   object      $tblSendMailContent
	 *
	 * @return 	boolean	    true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanBeforeNewsletterSend (&$table_name, &$tblSendMailQueue, &$tblSendMailContent)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to prepare content for listing BwTimeControl values
	 *
	 * @access	public
	 *
	 * @param 	object	$items  campaign list
	 *
	 * @return  boolean         true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignsPrepare (&$items)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$k	= 0;

		return $k;
	}

	/**
	 * Method to prepare content for editing BwTimeControl values
	 *
	 * @access	public
	 *
	 * @param 	object	$cam_data           campaign data
	 * @param 	object	$newsletters        newsletters lists
	 * @param 	object	$document           document
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignPrepare (&$cam_data, &$newsletters, &$document)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to suspend a queued newsletter from sending
	 *
	 * @access	public
	 *
	 * @param 	int		    $get_data
	 *
	 * @return  boolean     true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignTaskSuspendNewsletterFromSending (&$get_data)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to save BwTimeControl data of a campaign
	 *
	 * @access	public
	 *
	 * @param 	int		$campaign_id
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanCampaignSave ($campaign_id)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to redirect back to newsletters list after editing campaign, if newsletter changed his campaign
	 *
	 * @access	public
	 *
	 * @return 	boolean	    true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanAfterCampaignControllerSave ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to set state with newsletter data before it is edited (old data)
	 *
	 * @access	public
	 *
	 * @param   object $item
	 * @param   object      $referrer
	 *
	 * @return 	bool	    true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanBeforeNewsletterEdit (&$item, $referrer)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

//		$this->injectFormField();

		return true;
	}

	/**
	 * Method to save scheduled date to table
	 *
	 * @param array $data
	 *
	 * @return 	bool	true on success
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanAfterNewsletterModelSave (&$data)
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
			'newsletter_id' => $data['id'],
			'scheduled_date' => $data['scheduled_date'],
			'ready_to_send' => $data['ready_to_send'],
			);

		$this->saveItem($scheduledData);

		return  true;
	}

	/**
	 * Method to redirect to edit campaign, if newsletter changes his campaign at task cancel
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanAfterNewsletterCancel ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to copy a newsletter that belongs to a timecontrolled campaign
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanAfterNewsletterCopy ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to check, if archiving newsletters is possible. Newsletters of a timecontrolled campaign may not be archived
	 *
	 * @access	public
	 *
	 * @param 	array	$cid        Newsletter-IDs
	 * @param	string	$msg        return message
	 * @param 	bool	$res        result
	 *
	 * @return boolean              true on success
	 *
	 * @since	2.3.0
	 */
	public function onBwPostmanBeforeNewsletterArchive (&$cid, &$msg, &$res)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to update table tc_sendmail_content after a newsletter is edited
	 *
	 * @access	public
	 *
	 * @param   object  $item
	 *
	 * @return 	bool	true on success
	 *
	 * @since	2.3.0
	 */
//	public function onBwPostmanAfterNewsletterModelSave (&$item)
//	{
//		// Sanity check :)
//		if (!$this->_enabled)
//		{
//			return false;
//		}
//
//		return true;
//	}

	/**
	 * Method to get schedule data from table
	 *
	 * @param   integer  $nl_id
	 *
	 * @return 	array
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	private function getItem ($nl_id)
	{
		$scheduled_date = '0000-00-00 00:00:00';

		if ($nl_id !== 0)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select($db->quoteName('scheduled_date'));
			$query->select($db->quoteName('ready_to_send'));
			$query->from($db->quoteName('#__bwpostman_tc_schedule'));
			$query->where($db->quoteName('newsletter_id') . ' = ' . $db->Quote($nl_id));

			$db->setQuery($query);

			try
			{
				$scheduled_date = $db->loadAssoc();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $scheduled_date;
	}

	/**
	 * Method to manipulate schedule data at table
	 *
	 * @param   array  $scheduledDate
	 *
	 * @return 	boolean
	 *
	 * @throws \Exception
	 *
	 * @since	2.3.0
	 */
	private function saveItem ($scheduledDate)
	{
		$savedDate = $this->getItem($scheduledDate['newsletter_id']);

		if ($savedDate['scheduled_date'] === null && $scheduledDate['scheduled_date'] === '')
		{
			return true;
		}

		$db    = JFactory::getDbo();
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
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 *
	 * @return SimpleXMLElement
	 *
	 * @since version
	 */
	private function createFieldsetScheduled()
	{
		$scheduledXml = new \SimpleXMLElement(
			'<fieldset name="scheduled">
				<field 
				name="scheduled_date" 
				type="calendar"
				default=""
				label="PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_DATE_LABEL"
				labelclass="control-label"
				description="PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_DATE_DESC"
				format="%Y-%m-%d %H:%M:%S"
				size="22"
				filter="user_utc"
				class="inputbox"
				/>
				<field 
				name="ready_to_send" 
				type="list"
				default="0"
				label="PLG_BWPOSTMAN_BWTIMECONTROL_READY_TO_SEND_LABEL"
				labelclass="control-label"
				description="PLG_BWPOSTMAN_BWTIMECONTROL_READY_TO_SEND_DESC"
				class="chzn-color-state"
				filter="intval"
				>
					<option value="1">COM_BWPOSTMAN_YES</option>
					<option value="0">COM_BWPOSTMAN_NO</option>
				</field>
				</fieldset>');

		return $scheduledXml;
	}
}
