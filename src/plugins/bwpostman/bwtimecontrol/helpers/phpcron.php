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

use Joomla\Registry\Registry as Registry;

/**
 * Handles the cron jobs by php
 *
 * @package BwPostman
 *
 * @since	2.3.0
 */
class BwPostmanPhpCron {

	/**
	 * @var $app
	 *
	 * @since	2.3.0
	 */
	protected $app;

	/**
	 * @var $basepath string the base of the installation
	 *
	 * @since	2.3.0
	 */
	var $basepath = '';

	/**
	 * @var $_variables array of user set variables to override template settings
	 *
	 * @since	2.3.0
	 */
	protected $_variables = array();

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
	 * Property to path for stop file
	 *
	 * @var    string
	 *
	 * @since  2.3.0
	 */
	public $startFile  = '/bwpostman/bwtimecontrol/helpers/startFile.txt';

	/**
	 * Property to path for stop file
	 *
	 * @var    string
	 *
	 * @since  2.3.0
	 */
	public $stopFile  = '/bwpostman/bwtimecontrol/helpers/stopFile.txt';

	/**
	 * Initialise the cron
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 		2.3.0
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
		$this->logger = new BwLogger($log_options);

		$plugin = JPluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
		$params = new Registry($plugin->params);

		$this->_variables['username']	= $params->get('bwtimecontrol_username',null);
		$this->_variables['password']	= $params->get('bwtimecontrol_passwd',null);
		$this->_variables['domain']		= $params->get('bwtimecontrol_domain',null);
	}


	/**
	 * Method to run a job at an infinite loop
	 *
	 * @return	void
	 *
	 * @throws \Exception
	 *
	 * @since  2.3.0
	 */
	public function runCronServer()
	{
		// Remove stop file
		if (JFile::exists(JPATH_PLUGINS . $this->stopFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->stopFile);
		}

		// Create start file
		if (!JFile::exists(JPATH_PLUGINS . $this->startFile))
		{
			file_put_contents(JPATH_PLUGINS . $this->startFile, 'start');

			$jobUrl = JUri::root() . 'index.php?option=com_bwpostman&task=doCron';

			$curlDefaults = array(
				CURLOPT_HEADER         => 0,
				CURLOPT_URL            => $jobUrl,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT        => 20,
			);

			$ch = curl_init();
			curl_setopt_array($ch, ($curlDefaults));

			if (!$curlRes = curl_exec($ch))
			{
				trigger_error(curl_error($ch));
			}

			curl_close($ch);
			$this->logger->addEntry(new JLogEntry('Cron server started', JLog::INFO, 'BwPm_TC'));
		}
		else
		{
			$this->logger->addEntry(new JLogEntry('Cron server already started', JLog::INFO, 'BwPm_TC'));
		}
	}

	/**
	 * Method to stop infinite loop
	 *
	 * @return	void
	 *
	 * @since  2.3.0
	 */
	public function stopCronServer()
	{
		// Remove start file
		if (JFile::exists(JPATH_PLUGINS . $this->startFile))
		{
			JFile::delete(JPATH_PLUGINS . $this->startFile);
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
	 * @return	void
	 *
	 * @throws \Exception
	 *
	 * @since  2.3.0
	 */
	public function doCronJob()
	{
		if ($this->Login())
		{
			ob_end_clean();
			ignore_user_abort(true);
			ob_start();
			header("Connection: close");
			header("Content-Length: " . ob_get_length());
			ob_end_flush();
			flush();

			// from here the response has been sent. One can now wait as long as one want and do some stuff
			$this->logger->addEntry(new JLogEntry('Cron job started', JLog::INFO, 'BwPm_TC'));
			$doRun = true;

			// Do every X minutes
			$plugin = JPluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
			$pluginParams = new JRegistry();
			$pluginParams->loadString($plugin->params);
			$interval = (int) $pluginParams->get('bwtimecontrol_cron_intval') * 60;

			do
			{
				// Quit loop if desired
				if (JFile::exists(JPATH_PLUGINS . $this->stopFile))
				{
					$doRun = false;
					$this->logger->addEntry(new JLogEntry('Stopping cron server', JLog::INFO, 'BwPm_TC'));
				}

				// Only go on, if quit loop not desired
				if ($doRun)
				{
					$startTime = time();

					// get newsletters for the interval since last loop and now
					$nlsToSend = $this->getNextNewslettersToSend();

					// Send newsletter, if necessary
					if (is_array($nlsToSend) && count($nlsToSend))
					{
						$this->logger->addEntry(new JLogEntry(JText::sprintf('%s newsletter(s) to send', count($nlsToSend)), JLog::INFO, 'BwPm_TC'));

						foreach ($nlsToSend as $nlToSend)
						{
							if ($this->sendCronNewsletter($nlToSend))
							{
								$this->logger->addEntry(new JLogEntry(JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_FINISHED', count($nlsToSend)), JLog::INFO, 'BwPm_TC'));
								$this->sendCronMail('', $nlToSend, 'cronFinished');
							}
							else
							{
								$this->sendCronMail('', $nlToSend, 'sendCron');
							}
						}
					}
					else
					{
						$this->logger->addEntry(new JLogEntry('No newsletters to send', JLog::INFO, 'BwPm_TC'));
					}
				}

				// If interval is greater than needed time for sending, wait for remaining time, else loop anew immediately
				$usedTime = time() - $startTime;
				$remainingTime = $interval - $usedTime;

				if ($remainingTime > 0)
				{
					sleep($remainingTime);
				}
			}
			while ($doRun);

			$user = \JFactory::getUser();
			$this->UserLogout($user->id);
		}
		else
		{
			$this->logger->addEntry(new JLogEntry(('Credentials error while sending'), JLog::ERROR, 'BwPm_TC'));
		}
	}

	/**
	 * Check if the user exists
	 *
	 * @throws \Exception
	 *
	 * @since  2.3.0
	 */
	private function Login() {

		$jfilter = new JFilterInput();

		$credentials['username'] = $jfilter->clean($this->_variables['username'], 'username');
		$credentials['password'] = $jfilter->clean($this->_variables['password']);

		$result = $this->app->login($credentials, array('entry_url' => '\JUri::base() . \'administrator\index.php?option=com_users&task=user.login'));

		if (!JError::isError($result)) {
			return true;
		}
		else return false;
	}

	/**
	 * Log out the user
	 *
	 * @param int $uid  user to log out
	 *
	 * @since  2.3.0
	 */
	private function UserLogout($uid) {
		ob_start();
		$loggedOut = $this->app->logout($uid);

		if(!$loggedOut) {
			ob_end_clean();
			echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_PROBLEM_LOGOUT_USER');
		}
		else {
			ob_end_clean();
			echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_USER_LOGGED_OUT') . "\n";
		}
	}

	/**
	 * Method to get the next newsletter(s) to send
	 *
	 * @return	array
	 *
	 * @since  2.3.0
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

		$nlsToSend = $db->loadColumn();

		if (is_array($nlsToSend) && count($nlsToSend))
		{
			$this->checkMailingDate($nlsToSend);
		}

		return $nlsToSend;
	}

	/**
	 * Method to check if newsletters are already sent (have mailing date)
	 *
	 * @param array  $scheduledNls
	 *
	 * @return	array
	 *
	 * @since  2.3.0
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

		$nlsToSend = $db->loadAssocList();

	return $nlsToSend;
	}

	/**
	 * Send the newsletters
	 *
	 * @param integer $nlToSend
	 *
	 * @return boolean true on success
	 *
	 * @since 2.3.0
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

		// @ToDo: Move preSendChecks and evaluation to newsletter save specific task for automation at activation of ready_to_send
		$data = $nlModel->preSendChecks($error, $nlToSend, true);

		if (count($error))
		{
			// Send error mail?
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

		$this->setSentStatus($nlToSend);

		while ($ret)
		{
			$ret = $nlModel->sendMailsFromQueue($mails_per_step);

			if ($ret === 2)
			{
				$this->sendCronMail('', $nlToSend, 'processQueue');
				return false;
			}

			if ($ret)
			{
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
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	public function sendCronMail($messages, $nlToSend, $context)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$config = \JFactory::getConfig();
		$user = \JFactory::getUser();

		$mailer		= JFactory::getMailer();
		$mailer->SMTPDebug = true;
		$sender		= array();

		$sender[0]	= $config->get('mailfrom');
		$sender[1]	= $config->get('fromname');

		$body = '';

		switch ($context)
		{
			case 'checkRecipients':
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_CHECK_RECIPIENTS', $nlToSend, JText::_($messages));
				break;
			case 'sendNewsletter':
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_SEND_NEWSLETTER', $nlToSend, JText::_($messages));
				break;
			case 'preSendChecks':
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_PRE_CHECK', $nlToSend);
				foreach ($messages as $message)
				{
					$body .=  "\n" . JText::_($message);
				}
				break;
			case 'sendCron';
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_GENERAL', $nlToSend);
				break;
			case 'processQueue';
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_QUEUE', $nlToSend);
				break;
			case 'cronFinished';
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_FINISHED', $nlToSend);
				break;
			default:
				$body .= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_OTHERS', $nlToSend);
				break;
		}

		$mailer->setSender($sender);
		$mailer->addReplyTo($config->get('replyto'), $config->get('replytoname'));
		$mailer->addRecipient($user->email);
		$mailer->setSubject(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_SCHEDULE_SEND_ERROR_SUBJECT'));
		$mailer->setBody($body);

		$mailer->Send();
		$this->logger->addEntry(new JLogEntry($body, JLog::ERROR, 'BwPm_TC'));

	}

	/**
	 * Set status sent of the current send out newsletter
	 *
	 * @param $nlToSend
	 *
	 * @return boolean true on success
	 *
	 * @since 2.3.0
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
			// ToDo: We are in the plugin, here is no message queue visible!
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

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

