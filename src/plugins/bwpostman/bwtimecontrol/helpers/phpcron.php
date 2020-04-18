<?php
/**
 * BwPostman Newsletter TimeControl Plugin
 *
 * BwPostman TimeControl Plugin cron handler for BwPostman.
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

use Joomla\CMS\Crypt\Cipher\SodiumCipher;
use \Joomla\CMS\Crypt\Key;

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

/**
 * Handles the cron jobs by php
 *
 * @package BwPostman
 *
 * @since	0.9.0
 */
class BwPostmanPhpCron {

	/**
	 * @var $app
	 *
	 * @since	0.9.0
	 */
	protected $app;

	/**
	 * @var $basepath string the base of the installation
	 *
	 * @since	0.9.0
	 */
	var $basepath = '';

	/**
	 *  property to hold ID of this extension
	 *
	 * @var $extensionId
	 *
	 * @since	0.9.2
	 */
	var $extensionId = null;

	/**
	 * Property to hold plugin params
	 *
	 * @var $_variables array
	 *
	 * @since	0.9.0
	 */
	protected $_variables = array();

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
	private $log_cat  = 'BwPm_TC';

	/**
	 * Property to path for start file
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	public $startFile  = '/bwpostman/bwtimecontrol/helpers/startFile.txt';

	/**
	 * Property to path for started file
	 *
	 * @var    string
	 *
	 * @since  0.9.2
	 */
	public $startedFile  = '/bwpostman/bwtimecontrol/helpers/startedFile.txt';

	/**
	 * Property to path for stop file
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	public $stopFile  = '/bwpostman/bwtimecontrol/helpers/stopFile.txt';

	/**
	 * Property to path for stopped file
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	public $stoppedFile  = '/bwpostman/bwtimecontrol/helpers/stoppedFile.txt';

	/**
	 * Property to to hold current error
	 *
	 * @var    string
	 *
	 * @since  0.9.0
	 */
	public $error  = null;

	/**
	 * Initialise the cron
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 		0.9.0
	 */
	public function __construct()
	{
		/* Create the Application */
		$app = JFactory::getApplication('site');
		$app->initialise();
		$this->app = $app;

		// Load the language file
		$language = JFactory::getLanguage();
		$language->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR);

		// Load the plugin system
		JPluginHelper::importPlugin('system');

		// trigger the onAfterInitialise events
		$app->triggerEvent('onAfterInitialise');

		$log_options  = array('text_file' => 'bwpostman/BwPmTimecontrol.log');
		$this->logger = BwLogger::getInstance($log_options);

		$task = $app->input->get('task', null);
		$extensionId = $app->input->get('extension_id', null);

		if ($extensionId === null)
		{
			$plugin = JPluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
			$extensionId = $plugin->id;
		}

		$this->extensionId = $extensionId;

		if (($task === 'startCron' || $task === 'doCron')
			&& (count($this->_variables) === 0
				|| $this->_variables['username'] === ''
				|| $this->_variables['password'] === ''
			))
		{
			$this->getPluginParams($extensionId);
		}
	}

	/**
	 * Method to get plugin params and set them as properties
	 *
	 * @param string  $extensionId
	 *
	 * @since  0.9.2
	 */
	public function getPluginParams($extensionId)
	{
		$params = $this->getStoredParams($extensionId);

		$this->_variables['username'] = $params->bwtimecontrol_username;
		$encryptedPassword = base64_decode($params->bwtimecontrol_passwd);

		if ($encryptedPassword !== '')
		{
			$rawPassword = $this->decryptPassword($encryptedPassword);
			$this->_variables['password'] = $rawPassword;
		}
	}

	/**
	 * Method to get stored params
	 *
	 * @param integer $extensionId  ID of this plugin
	 *
	 * @return  object
	 *
	 * @since   0.9.2
	 */
	protected function getStoredParams($extensionId)
	{
		$storedParams = array();
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('extension_id') . ' = ' . $db->Quote($extensionId));

		$db->setQuery($query);

		try
		{
			$storedParams = $db->loadResult();
		}
		catch (Exception $e)
		{
			$this->_subject->setError($e->getMessage());
			$this->BwPostmanComponentEnabled = false;
			$message                         = 'Database error while getting stored params, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		return json_decode($storedParams);
	}

	/**
	 * Method to decrypt password
	 *
	 * @param string $encryptedPasswd       the encrypted password
	 *
	 * @return  string
	 *
	 * @since   0.9.2
	 */
	public function decryptPassword($encryptedPasswd)
	{
		$key    = $this->getKeyFromDatabase();
		$nonce  = $this->getNonceFromDatabase();

		$cipher = new SodiumCipher;

		$cipher->setNonce($nonce);

		$rawPasswd = $cipher->decrypt($encryptedPasswd, $key);

		return $rawPasswd;
	}

	/**
	 * Method to get encryption key from database
	 *
	 * @return Key  the encryption key
	 *
	 * @since  0.9.2
	 */
	public function getKeyFromDatabase()
	{
		$keyValues = new stdClass();
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('priv'));
		$query->select($db->quoteName('pub'));
		$query->from($db->quoteName('#__bwpostman_tc_settings'));
		$query->where($db->quoteName('type') . '= ' . $db->quote('sodium'));

		$db->setQuery($query);

		try
		{
			$keyValues = $db->loadObject();
		}
		catch (Exception $e)
		{
			$message = 'Database error while getting encryption values, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		$key = new Key('sodium');

		if ($keyValues !== null)
		{
			$key->public = $keyValues->pub;
			$key->private = $keyValues->priv;
		}

		return $key;
	}

	/**
	 * Method to get previously used password
	 *
	 * @param integer  $extensionId
	 *
	 * @return string
	 *
	 * @since  0.9.2
	 */
	public function getOldPassword($extensionId)
	{
		$oldPassword = null;
		$params      = null;
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('extension_id') . '= ' . $db->quote($extensionId));

		$db->setQuery($query);

		try
		{
			$params = $db->loadResult();
		}
		catch (Exception $e)
		{
			$message = 'Database error while getting previously used password, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		if ($params !== '')
		{
			$oldPassword = json_decode($params)->bwtimecontrol_passwd;
		}

		return base64_decode($oldPassword);
	}

	/**
	 * Method to get nonce from database
	 *
	 * @return string
	 *
	 * @since  0.9.2
	 */
	public function getNonceFromDatabase()
	{
		$nonce = null;
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('nonce'));
		$query->from($db->quoteName('#__bwpostman_tc_settings'));
		$query->where($db->quoteName('type') . '= ' . $db->quote('sodium'));

		$db->setQuery($query);

		try
		{
			$nonce = $db->loadResult();
		}
		catch (Exception $e)
		{
			$message = 'Database error while getting nonce, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		return $nonce;
	}

	/**
	 * Method to run a job at an infinite loop
	 *
	 * @return	boolean|string
	 *
	 * @throws Exception
	 *
	 * @since  0.9.0
	 */
	public function runCronServer()
	{
		if ($this->_variables['username'] === '' || $this->_variables['password'] === '')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_BWTIMECONTROL_NO_CREDENTIALS'), 'error');
			return false;
		}

		if (!extension_loaded('curl'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_BWTIMECONTROL_CURL_NOT_INSTALLED'), 'error');
			return false;
		}

		// Remove stop file
		if (JFile::exists(JPATH_PLUGINS . $this->stopFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->stopFile);
		}

		// Remove stopped file
		if (JFile::exists(JPATH_PLUGINS . $this->stoppedFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->stoppedFile);
		}

		// Create start file
		if (!JFile::exists(JPATH_PLUGINS . $this->startFile))
		{
			file_put_contents(JPATH_PLUGINS . $this->startFile, 'start');

			$jobUrl = JUri::root() . 'index.php?option=com_bwpostman&task=doCron&lang=en';

			$curlDefaults = array(
				CURLOPT_HEADER         => 0,
				CURLOPT_URL            => $jobUrl,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT        => 120,
			);

			$ch = curl_init();
			curl_setopt_array($ch, ($curlDefaults));

			$curlRes =  curl_exec($ch);
			$err     = curl_errno( $ch );
			$errMsg  = curl_error( $ch );

			preg_match('/error/i', $curlRes, $matches);

			$this->logger->addEntry(new JLogEntry('JobURL: ' . $jobUrl, BwLogger::BW_INFO, 'BwPm_TC'));
			$this->logger->addEntry(new JLogEntry('cURL err: ' . $err, BwLogger::BW_ERROR, 'BwPm_TC'));
			$this->logger->addEntry(new JLogEntry('cURL errMsg: ' . $errMsg, BwLogger::BW_ERROR, 'BwPm_TC'));
			$this->logger->addEntry(new JLogEntry('cURL curlRes: ' . $curlRes, BwLogger::BW_INFO, 'BwPm_TC'));
			$this->logger->addEntry(new JLogEntry('counted matches for error: ' . count($matches), BwLogger::BW_INFO, 'BwPm_TC'));

			if ($err === 28 && JFile::exists(JPATH_PLUGINS . $this->startedFile))
			{
				// Remove start file
				if (JFile::exists(JPATH_PLUGINS . $this->startFile))
				{
					JFile::delete(JPATH_PLUGINS . $this->startFile);
				}
			}

			if ($err !== 0 || count($matches) > 0)
			{
				$this->logger->addEntry(new JLogEntry('cURL errMsg: ' . $errMsg, BwLogger::BW_ERROR, 'BwPm_TC'));

				if (!$err === 28 || count($matches) > 0)
				{
					// @Todo: Is this really the only reason for timeout?
					$errMsg = JText::_('PLG_BWTIMECONTROL_WRONG_CREDENTIALS');
				}

				if ($err === 28)
				{
					$errMsg = JText::_('PLG_BWTIMECONTROL_CURL_TIMEOUT');
				}

				$this->error = $errMsg;

				// Remove start file
				if (JFile::exists(JPATH_PLUGINS . $this->startFile))
				{
					JFile::delete(JPATH_PLUGINS . $this->startFile);
				}

				// Create stopped file
				if (!JFile::exists(JPATH_PLUGINS . $this->stoppedFile))
				{
					file_put_contents(JPATH_PLUGINS . $this->stoppedFile, 'stopped');
				}

				return $errMsg;
			}

			curl_close($ch);
			$this->logger->addEntry(new JLogEntry('Cron server started', BwLogger::BW_INFO, 'BwPm_TC'));
		}
		else
		{
			$this->logger->addEntry(new JLogEntry('Cron server already started', BwLogger::BW_INFO, 'BwPm_TC'));
		}

		return true;
	}

	/**
	 * Method to stop infinite loop
	 *
	 * @return	void
	 *
	 * @since  0.9.0
	 */
	public function stopCronServer()
	{
		// Remove start file
		if (JFile::exists(JPATH_PLUGINS . $this->startFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->startFile);
		}

		// Remove started file
		if (JFile::exists(JPATH_PLUGINS . $this->startedFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->startedFile);
		}

		// Create stop file
		if (!JFile::exists(JPATH_PLUGINS . $this->stopFile))
		{
			file_put_contents(JPATH_PLUGINS . $this->stopFile, 'stop');
		}
	}

	/**
	 * Method to run a job at an infinite loop
	 *
	 * @return	boolean|string
	 *
	 * @throws Exception
	 *
	 * @since  0.9.0
	 */
	public function doCronJob()
	{
		$loginResult = $this->Login();

		if ($loginResult === true)
		{
			ob_end_clean();
			ignore_user_abort(true);
			ob_start();
			header("Connection: close");
			header("Content-Length: " . ob_get_length());
			ob_end_flush();
			flush();

			// from here the response has been sent. One can now wait as long as one want and do some stuff
			$this->logger->addEntry(new JLogEntry('Cron job started', BwLogger::BW_INFO, 'BwPm_TC'));
			$doRun = true;

			do
			{
				// Check and stop cron server if plugin is disabled
				$pluginState = $this->getPluginState($this->extensionId);

				if ($pluginState !== '1')
				{
					$this->stopCronServer();
				}

				// Quit loop if desired
				if (JFile::exists(JPATH_PLUGINS . $this->stopFile))
				{
					$doRun = false;
					$this->logger->addEntry(new JLogEntry('Stopping cron server', BwLogger::BW_INFO, 'BwPm_TC'));
				}

				// Do every X minutes
				$params = $this->getStoredParams($this->extensionId);
				$interval = (int) $params->bwtimecontrol_cron_intval * 60;

				$startTime = time();
				// Only go on, if quit loop not desired
				if ($doRun)
				{
					// get newsletters for the interval since last loop and now
					$nlsToSend = $this->getNextNewslettersToSend();

					// Send newsletter, if necessary
					if (is_array($nlsToSend) && count($nlsToSend))
					{
						$this->logger->addEntry(new JLogEntry(JText::sprintf('%s newsletter(s) to send', count($nlsToSend)), BwLogger::BW_INFO, 'BwPm_TC'));

						foreach ($nlsToSend as $nlToSend)
						{
							if ($this->sendCronNewsletter($nlToSend['id']))
							{
								$this->sendCronMail('', $nlToSend['id'], 'cronFinished');
							}
							else
							{
								$this->sendCronMail('', $nlToSend['id'], 'sendCron');
							}
						}
					}
//					else
//					{
//						$this->logger->addEntry(new JLogEntry('No newsletters to send', BwLogger::BW_DEBUG, 'BwPm_TC'));
//					}
				}

				// If interval is greater than needed time for sending, wait for remaining time, else loop anew immediately
				$usedTime = time() - $startTime;
				$remainingTime = $interval - $usedTime;

				if ($doRun && $remainingTime > 0)
				{
					sleep($remainingTime);
				}
			}
			while ($doRun);

			$user = JFactory::getUser();
			$this->UserLogout($user->id);
		}
		else
		{
			$this->logger->addEntry(new JLogEntry(('Credentials error while sending'), BwLogger::BW_ERROR, 'BwPm_TC'));
			return $loginResult;
		}
		return true;
	}

	/**
	 * Check if the user exists
	 *
	 * @throws Exception
	 *
	 * @since  0.9.0
	 */
	private function Login() {

		$jfilter = new JFilterInput();
		$app = JFactory::getApplication();

		$credentials['username'] = $jfilter->clean($this->_variables['username'], 'username');
		$credentials['password'] = $jfilter->clean($this->_variables['password']);

		if ($credentials['username'] === '' || $credentials['password'] === '')
		{
			$error = JText::_('PLG_BWTIMECONTROL_NO_CREDENTIALS');
			$app->enqueueMessage($error, 'error');
			return $error;
		}

		$result = $this->app->login($credentials, array('entry_url' => '\JUri::base() . \'administrator\index.php?option=com_users&task=user.login'));

		if (!$result) {
			$error = JText::_('PLG_BWTIMECONTROL_WRONG_CREDENTIALS');
			$app->enqueueMessage($error, 'error');
			throw new Exception($result, 500);
//			return $error;
		}

		// Remove start file
		if (JFile::exists(JPATH_PLUGINS . $this->startFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->startFile);
		}

		// Create started file
		if (!JFile::exists(JPATH_PLUGINS . $this->startedFile))
		{
			file_put_contents(JPATH_PLUGINS . $this->startedFile, 'started');
		}

		return true;
	}

	/**
	 * Log out the user
	 *
	 * @param int $uid  user to log out
	 *
	 * @since  0.9.0
	 */
	private function UserLogout($uid) {
		ob_start();
		$loggedOut = $this->app->logout($uid);

		if(!$loggedOut) {
			ob_end_clean();
			echo JText::_('PLG_BWTIMECONTROL_PROBLEM_LOGOUT_USER');
		}
		else
		{
			ob_end_clean();
			echo JText::_('PLG_BWTIMECONTROL_USER_LOGGED_OUT') . "\n";

			// Remove stop file
			if (JFile::exists(JPATH_PLUGINS . $this->stopFile))
			{
				JFile::delete(JPATH_PLUGINS . $this->stopFile);
			}

			// Create stopped file
			if (!JFile::exists(JPATH_PLUGINS . $this->stoppedFile))
			{
				file_put_contents(JPATH_PLUGINS . $this->stoppedFile, 'stopped');
			}
		}
	}

	/**
	 * Method to get stored params
	 *
	 * @param integer $extensionId  ID of this plugin
	 *
	 * @return  boolean
	 *
	 * @since   0.9.2
	 */
	protected function getPluginState($extensionId)
	{
		$pluginStateOld = 0;

		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('enabled'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('extension_id') . ' = ' . $db->Quote($extensionId));

		$db->setQuery($query);

		try
		{
			$pluginStateOld = $db->loadResult();
		}
		catch (Exception $e)
		{
			$message = 'Database error while getting plugin state, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, $this->log_cat));
		}

		return $pluginStateOld;
	}

	/**
	 * Method to get the next newsletter(s) to send
	 *
	 * @return	array|boolean
	 *
	 * @since  0.9.0
	 */
	public function getNextNewslettersToSend()
	{
		$currentTime = date('Y-m-d H:i:s', time());

		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('newsletter_id'));
		$query->from($db->quoteName('#__bwpostman_tc_schedule'));
		$query->where($db->quoteName('sent') . ' = ' . $db->Quote('0'));
		$query->where($db->quoteName('scheduled_date') . ' < ' . $db->quote($currentTime));
		$query->where($db->quoteName('ready_to_send') . ' = ' . $db->quote('1'));
		$query->order($db->quoteName('scheduled_date') . ' ASC');

		$db->setQuery($query);

		try
		{
			$nlsToSend = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$message = 'Database error while getting next newsletters to send, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, 'BwPm_TC'));

			return false;
		}

		if (is_array($nlsToSend) && count($nlsToSend))
		{
			$nlsToSend = $this->checkMailingDate($nlsToSend);
		}

		return $nlsToSend;
	}

	/**
	 * Method to check if newsletters are already sent (have mailing date)
	 *
	 * @param array  $scheduledNls
	 *
	 * @return	array|boolean
	 *
	 * @since  0.9.0
	 */
	public function checkMailingDate($scheduledNls)
	{
		$db	= JFactory::getDbo();

		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('id') . ' IN (' . implode (',', $scheduledNls) . ')');
		$query->where($db->quoteName('mailing_date') . ' = ' . $db->quote('0000-00-00 00:00:00'));

		$db->setQuery($query);

		try
		{
			$nlsToSend = $db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			$message = 'Database error while check mailing date, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, 'BwPm_TC'));

			return false;
		}

		return $nlsToSend;
	}

	/**
	 * Send the newsletters
	 *
	 * @param integer $nlToSend
	 *
	 * @return boolean true on success
	 *
	 * @since 0.9.0
	 *
	 * @throws Exception
	 */
	private function sendCronNewsletter($nlToSend)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/newsletter.php');
		require_once(JPATH_ADMINISTRATOR . '/components//com_bwpostman/helpers/newsletterhelper.php');
		$nlModel = JModelLegacy::getInstance('Newsletter', 'BwPostmanModel');

		$error = array();
		$ret = 1;
		$ret_msg = '';
		$params			= JComponentHelper::getParams('com_bwpostman');
		$mails_per_step	= (int) $params->get('default_mails_per_pageload');
		// We have to divide by 1000, because sending by component is made by JS and therefore the sleep is stored in ms
		$delay			= (int) $params->get('mails_per_pageload_delay') * (int) $params->get('mails_per_pageload_delay_unit') / 1000;

		if (count($error))
		{
			$this->sendCronMail($error, $nlToSend, 'preSendChecks');
			return false;
		}

		$campaignId = BwPostmanNewsletterHelper::getCampaignId($nlToSend);

		if (!$nlModel->checkRecipients($ret_msg, $nlToSend, 0, $campaignId))
		{
			$this->sendCronMail($ret_msg, $nlToSend, 'checkRecipients');
			return false;
		}

		if (!$nlModel->sendNewsletter($ret_msg, 'recipients', $nlToSend, 0, $campaignId))
		{
			$this->sendCronMail($ret_msg, $nlToSend, 'sendNewsletter');
			return false;
		}

		while ($ret)
		{
			$ret = $nlModel->sendMailsFromQueue($mails_per_step, false);

			if ($ret === 2)
			{
				$this->sendCronMail('', $nlToSend, 'processQueue');
				return false;
			}

			if ($ret)
			{
				$this->setSentStatus($nlToSend);
				sleep($delay);
			}
		}

		return true;
	}

	/**
	 * Set status sent of the current send out newsletter
	 *
	 * @param string|array $messages
	 * @param integer      $nlToSend
	 * @param string       $context
	 *
	 * @return void
	 *
	 * @since 0.9.0
	 *
	 * @throws Exception
	 */
	public function sendCronMail($messages, $nlToSend, $context)
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_PLUGINS . '/bwpostman/bwtimecontrol');

		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		$mailer		= JFactory::getMailer();
		$sender		= array();

		$sender[0]	= $config->get('mailfrom');
		$sender[1]	= $config->get('fromname');

		$subject = JText::_('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_SUBJECT');
		$body = "";

		switch ($context)
		{
			case 'checkRecipients':
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_CHECK_RECIPIENTS', $nlToSend, JText::_($messages));
				break;
			case 'sendNewsletter':
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_SEND_NEWSLETTER', $nlToSend, JText::_($messages));
				break;
			case 'preSendChecks':
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_PRE_CHECK', $nlToSend);
				foreach ($messages as $message)
				{
					$body .=  "\n" . JText::_($message);
				}
				break;
			case 'sendCron';
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_GENERAL', $nlToSend);
				break;
			case 'processQueue';
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_QUEUE', $nlToSend);
				break;
			case 'cronFinished';
				$subject = JText::_('PLG_BWTIMECONTROL_SCHEDULE_SEND_SUCCESS_SUBJECT');
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_FINISHED', $nlToSend);
				break;
			default:
				$body .= JText::sprintf('PLG_BWTIMECONTROL_SCHEDULE_SEND_ERROR_OTHERS', $nlToSend);
				break;
		}

		$mailer->setSubject($subject);
		$mailer->setSender($sender);
		$mailer->addReplyTo($config->get('replyto'), $config->get('replytoname'));
		$mailer->addRecipient($user->email);
		$mailer->setBody($body);

		$mailer->Send();
		$this->logger->addEntry(new JLogEntry(JText::_('Scheduled sending of newsletter with ID %s finished', $nlToSend), BwLogger::BW_ERROR, 'BwPm_TC'));

	}

	/**
	 * Set status sent of the current send out newsletter
	 *
	 * @param $nlToSend
	 *
	 * @return boolean true on success
	 *
	 * @since 0.9.0
	 *
	 * @throws Exception
	 */
	public function setSentStatus($nlToSend)
	{
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_tc_schedule'));
		$query->set($db->quoteName('sent') . " = " . $db->Quote('1'));
		$query->where($db->quoteName('newsletter_id') . ' = ' . $db->Quote((int)$nlToSend));

		try
		{
			$db->setQuery($query);

			$db->execute();

			return true;
		}
		catch (RuntimeException $e)
		{
			$message = 'Database error while storing sent status, error message is ' . $e->getMessage();
			$this->logger->addEntry(new JLogEntry($message, BwLogger::BW_ERROR, 'BwPm_TC'));

			return false;
		}
	}
}

/* Get the Joomla framework */
define('DS', DIRECTORY_SEPARATOR);

$parts = explode(DS, JPATH_BASE);

if (!defined('JPATH_ROOT'))				define('JPATH_ROOT',			implode(DS, $parts));
if (!defined('JPATH_SITE'))				define('JPATH_SITE',			JPATH_ROOT);
if (!defined('JPATH_CONFIGURATION'))	define('JPATH_CONFIGURATION',	JPATH_ROOT);
if (!defined('JPATH_ADMINISTRATOR'))	define('JPATH_ADMINISTRATOR',	JPATH_ROOT . '/administrator');
if (!defined('JPATH_LIBRARIES'))		define('JPATH_LIBRARIES',		JPATH_ROOT . '/libraries');
if (!defined('JPATH_PLUGINS'))			define('JPATH_PLUGINS',			JPATH_ROOT . '/plugins');
if (!defined('JPATH_INSTALLATION'))		define('JPATH_INSTALLATION',	JPATH_ROOT . '/installation');
if (!defined('JPATH_THEMES'))			define('JPATH_THEMES',			JPATH_BASE . '/templates');
if (!defined('JPATH_CACHE'))			define('JPATH_CACHE',			JPATH_BASE . '/cache');
if (!defined('JPATH_MANIFESTS'))		define('JPATH_MANIFESTS',		JPATH_ADMINISTRATOR . '/manifests');

ob_start();
// Set error reporting to file to redirect errors, so that cronjob has not to mail messages
error_reporting  (E_ALL);
ini_set ('display_errors', 'ON');
ini_set ('error_log', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/log/cron_errors.log');

// Require files for the framework
require_once (JPATH_BASE.'/includes/framework.php');

