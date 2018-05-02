<?php
/**
 * BwPostman Newsletter QuickTimeControl Plugin
 *
 * BwPostman TimeControl Plugin cron handler for BwPostman.
 *
 * @version 2.0.0 bwplgtc
 * @package BwPostman TimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
 * Cron handler
 *
 * @since  1.2.0
 */

/* Get the Joomla framework */
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', substr(str_ireplace('plugins/bwpostman/bwtimecontrol/helpers/cron.php', '', str_ireplace('\\', '/', __FILE__)), 0, -1));
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_BASE.'/administrator/components/com_bwpostman');
define('JPATH_COMPONENT', JPATH_COMPONENT_ADMINISTRATOR);

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
$_argv = $argv;
$_SERVER['HTTP_HOST'] = $argv[1];

// Set error reporting to file to redirect errors, so that cronjob has not to mail messages
error_reporting  (E_ALL);
ini_set ('display_errors', 'ON');
ini_set ('error_log', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/log/cron_errors.log');

// Require files for the framework
require_once (JPATH_BASE.'/includes/framework.php');

// Load the cron details
$bwpostmancron = new BwPostmanCron();

/* Create the Application */
$app = JFactory::getApplication('site');
$app->initialise();

// Load the language file
$language = JFactory::getLanguage();
$language->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR);

// Load the plugin system
JPluginHelper::importPlugin('system');

// trigger the onAfterInitialise events
$app->triggerEvent('onAfterInitialise');

// Run the cron job
$bwpostmancron->runCron();


/**
 * Handles all cron requests
 *
 * @package BwPostman
 *
 * @since	1.2.0
 */
class BwPostmanCron {

	/**
	 * @var $basepath string the base of the installation
	 *
	 * @since	1.2.0
	 */
	var $basepath = '';

	/**
	 * @var $_variables array of user set variables to override template settings
	 *
	 * @since	1.2.0
	 */
	protected $_variables = array();

	/**
	 * Initialise the cron
	 *
	 * @copyright
	 * @author 		Romana Boldt
	 * @access 		public
	 * @param
	 * @return
	 * @since 		1.2.0
	 */
	public function __construct() {


		$this->CollectVariables();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('Username') . ' = ' . $db->quote($this->_variables['username']));
		$db->setQuery($query);
		$uid	= $db->loadResult();

		// Merge the default translation with the current translation
		$lang		= JFactory::getLanguage();
		$user		= JFactory::getUser($uid);

		$user_lang	= $user->getParam('admin_language');
		$user_lang	= $user->getParam('language');
		if (($user_lang != NULL) && ($user_lang != '')) {
			$def_lang	= $lang->setLanguage($user_lang);
		}
		else {
			$def_lang	= $lang->setLanguage($lang->getTag());
		}

		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR, $lang->getTag(), true, true);
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR, $def_lang, true, true);

		// Get the domain name
		$domainname = $this->_variables['domain'];
		// Check for the trailing slash at the domain name
		if (substr($domainname, -1) == '/') $domainname = substr($domainname, 0, -1);

		// Fill the server global with necessary information
		$_SERVER['REQUEST_METHOD']	= 'post';
		$_SERVER['HTTP_HOST']		= $domainname;
		$_SERVER['REMOTE_ADDR']		= gethostbyname('localhost');
		$_SERVER['SERVER_PORT']		= '';
		$_SERVER['HTTP_USER_AGENT']	= 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0) Gecko/20100101 Firefox/4.0';
		$_SERVER['REQUEST_URI']		= '/administrator/index.php';
		$_SERVER['QUERY_STRING']	= '';
		$_SERVER['PHP_SELF']		= '/index.php';
		$_SERVER['SCRIPT_NAME']		= '/index.php';
	}

	/**
	 * Initialise some settings
	 *
	 * @since  1.2.0
	 */
	public function runCron() {
		// Start the clock
		$starttime = time();

		// First check if we deal with a valid user
		if ($this->Login()) {
			// Set some global values

			// Check if we are running cron mode and set some necessary variables
			$_SERVER['SERVER_ADDR'] = $_SERVER['HTTP_HOST'];
			$_SERVER['SCRIPT_NAME'] = '/index.php';
			$_SERVER['REQUEST_URI'] = '/';
			$_SERVER['PHP_SELF'] = '/index.php';

			$this->ExecuteJob();

			echo sprintf(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_PROCESSING_FINISHED'), date('jS F Y, g:i a'))."\n";
			$duration = time() - $starttime;
			if ($duration < 60) echo JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_PROCESSING_SECONDS', $duration)."\n";
			else echo JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_PROCESSING_MINUTES', (number_format($duration/60, 2)))."\n";

			// Done, lets log the user out
			$this->UserLogout();
		}
		else {
			$error = JError::getError();
//			echo $error->message."\n";
		}
	}

	/**
	 * Collect the variables
	 *
	 * Running from the command line, values needed to run the functions to send due mails must be get from plugin options
	 * Here we get them from options, put them in $this->_variables so that they are available to the script
	 *
	 * @since  1.2.0
	 */
	private function CollectVariables() {
		$plugin = JPluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
		$params = new Registry($plugin->params);

//										  $params->get('bwtimecontrol_minute_intval');
		$this->_variables['username']	= $params->get('bwtimecontrol_username',null);
		$this->_variables['password']	= $params->get('bwtimecontrol_passwd',null);
		$this->_variables['domain']		= $params->get('bwtimecontrol_domain',null);
//var_dump ($this->_variables);
	}

	/**
	 * Check if the user exists
	 *
	 * @since  1.2.0
	 */
	private function Login() {
		global $app;

		$app = JFactory::getApplication();
		$jfilter = new JFilterInput();
		$credentials['username'] = $jfilter->clean($this->_variables['username'], 'username');
		$credentials['password'] = $jfilter->clean($this->_variables['password']);

		$result = $app->login($credentials, array('entry_url' => ''));

		if (!JError::isError($result)) {
			return true;
		}
		else return false;
	}

	/**
	 * Process the requested job
	 *
	 * @since  1.2.0
	 */
	private function ExecuteJob() {
		$jinput = JFactory::getApplication()->input;
		$jinput->set ('option', 'com_bwpostman');
		$jinput->set ('view', 'campaigns');
		$jinput->set ('layout', 'raw');
		$jinput->set ('task', 'campaign.dueSend');
		$jinput->set ('dueSend', true);

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/bwpostman.php');
	}

	/**
	 * Log the user out
	 *
	 * @since  1.2.0
	 */
	private function UserLogout() {
		global $app;
		ob_start();
		$error = $app->logout();

		if(JError::isError($error)) {
			ob_end_clean();
			echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_PROBLEM_LOGOUT_USER');
		}
		else {
			ob_end_clean();
			echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_USER_LOGGED_OUT') . "\n";
		}
	}
}

