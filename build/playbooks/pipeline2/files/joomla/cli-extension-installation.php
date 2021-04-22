<?php
/**
 * Command-line extension installer. This file is meant to be copied into your Joomla! 3 site's cli directory.
 *
 * @copyright
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

// Define ourselves as a parent file
define('_JEXEC', 1);

// Required by the CMS
define('DS', DIRECTORY_SEPARATOR);
define('JDEBUG', 0);

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
{
	if (function_exists('error_reporting'))
	{
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if (empty($serverTimezone) || !is_string($serverTimezone))
	{
		$serverTimezone = 'UTC';
	}
	if (function_exists('error_reporting'))
	{
		error_reporting($oldLevel);
	}
	@date_default_timezone_set($serverTimezone);
}

// Load system defines
if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	$path = rtrim(__DIR__, DIRECTORY_SEPARATOR);
	$rpos = strrpos($path, DIRECTORY_SEPARATOR);
	$path = substr($path, 0, $rpos);
	define('JPATH_BASE', $path);
	require_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the framework include files
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php'))
{
	require_once JPATH_LIBRARIES . '/import.legacy.php';
}
else
{
	require_once JPATH_LIBRARIES . '/import.php';
}

require_once JPATH_LIBRARIES . '/cms.php';

// Load the JApplicationCli class
JLoader::import('joomla.application.cli');
JLoader::import('joomla.application.component.helper');
JLoader::import('cms.component.helper');

/**
 * Joomla extension installer cli
 * Installs an extension by command line interpreter
 *
 * This method will only work if the extension’s installation script does not rely on JSession, redirections,
 * AJAX or any JApplicationWeb/JApplicationAdmin methods.
 *
 * This file has to reside at the cli folder of the Joomla installation
 *
 * Usage: cd /path/to/site/cli php ./install-joomla-extension.php --package=/where/is/your/extension.zip
 *
 * Returns:
 * 0 - The extension was installed successfully.
 * 1 - The package file was not found.
 * 3 - The package file could not be extracted.
 * 250 - The extension could not be installed.
 *
 * See:
 * https://www.dionysopoulos.me/installing-joomla-extensions-from-the-command-line/
 * https://raw.githubusercontent.com/akeeba/vagrant/master/assets/joomla/install-joomla-extension.php
 *
 * @since
 */
class JoomlaExtensionInstallerCli extends JApplicationCli
{
	/**
	 * JApplicationCli didn't want to run on PHP CGI. I have my way of becoming
	 * VERY convincing. Now obey your true master, you petty class!
	 *
	 * @param JInputCli   $input
	 * @param JRegistry   $config
	 * @param JDispatcher $dispatcher
	 *
	 * @since
	 */
	public function __construct(JInputCli $input = null, JRegistry $config = null, JDispatcher $dispatcher = null)
	{
		// Close the application if we are not executed from the command line, Akeeba style (allow for PHP CGI)
		if (array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			die('You are not supposed to access this script from the web. You have to run it from the command line. If you don\'t understand what this means, you must not try to use this file before reading the documentation. Thank you.');
		}

		$cgiMode = false;

		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$cgiMode = true;
		}

		// If a input object is given use it.
		if ($input instanceof JInput)
		{
			$this->input = $input;
		}
		// Create the input based on the application logic.
		else
		{
			if (class_exists('JInput'))
			{
				if ($cgiMode)
				{
					$query = "";
					if (!empty($_GET))
					{
						foreach ($_GET as $k => $v)
						{
							$query .= " $k";
							if ($v != "")
							{
								$query .= "=$v";
							}
						}
					}
					$query = ltrim($query);
					$argv  = explode(' ', $query);
					$argc  = count($argv);

					$_SERVER['argv'] = $argv;
				}

				$this->input = new JInputCLI();
			}
		}

		// If a config object is given use it.
		if ($config instanceof JRegistry)
		{
			$this->config = $config;
		}
		// Instantiate a new configuration object.
		else
		{
			$this->config = new JRegistry;
		}

		// If a dispatcher object is given use it.
		if ($dispatcher instanceof JDispatcher)
		{
			$this->dispatcher = $dispatcher;
		}
		// Create the dispatcher based on the application logic.
		else
		{
			$this->loadDispatcher();
		}

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Set the current directory.
		$this->set('cwd', getcwd());
	}

	/**
	 *
	 * @return bool
	 *
	 * @since
	 */
	public function flushAssets()
	{
		// This is an empty function since JInstall will try to flush the assets even if we're in CLI (!!!)
		return true;
	}

	/**
	 *
	 * @return void
	 *
	 * @since
	 */
	public function execute()
	{
		JLoader::import('joomla.application.component.helper');
		JLoader::import('joomla.updater.update');
		JLoader::import('joomla.filesystem.file');
		JLoader::import('joomla.filesystem.folder');

		// Load the language files
		$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
		$jlang = JFactory::getApplication()->getLanguage();
		$jlang->load('lib_joomla', $paths[0], 'en-GB', true);

		$packageFile = $this->input->get('package', null, 'folder');

		if (!JFile::exists($packageFile))
		{
			$this->out("Package file $packageFile does not exist");
			$this->close(1);
		}

		// Attempt to use an infinite time limit, in case you are using the PHP CGI binary instead
		// of the PHP CLI binary. This will not work with Safe Mode, though.
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			@set_time_limit(0);
		}

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($packageFile);

		if (!$package)
		{
			$this->out("An error occurred while unpacking the file");
			$this->close(3);
		}

		$installer = new JInstaller;
		$installed = $installer->install($package['extractdir']);

		// Let's cleanup the downloaded archive and the temp folder
		if (JFolder::exists($package['extractdir']))
		{
			JFolder::delete($package['extractdir']);
		}

		if (JFile::exists($package['packagefile']))
		{
			JFile::delete($package['packagefile']);
		}

		if ($installed)
		{
			$this->out("Extension successfully installed");
			$this->close(0);
		}
		else
		{
			$this->out("Extension installation failed");
			$this->close(250);
		}
	}

	/**
	 * @param $params
	 *
	 * @return bool
	 *
	 * @since
	 */
	public function getTemplate($params = false)
	{
		return '';
	}

	/**
	 * @param $name
	 * @param $value
	 * @param $replace
	 *
	 * @return object
	 *
	 * @since
	 */
	public function setHeader($name, $value, $replace = false)
	{
		return $this;
	}

	/**
	 * @param $name
	 * @param $default
	 *
	 * @return string
	 *
	 * @since
	 */
	public function getCfg($name, $default = null)
	{
		return $this->get($name, $default);
	}

	/**
	 *
	 * @return bool
	 *
	 * @since
	 */
	public function getClientId()
	{
		return 1;
	}

	/**
	 * @param $identifier
	 *
	 * @return bool
	 *
	 * @since
	 */
	public function isClient($identifier)
	{
		return $identifier === 'administrator';
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return object|null
	 *
	 * @since
	 */
	public function setUserState($key, $value)
	{
		$session  = JFactory::getApplication()->getSession();
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->setValue($key, $value);
		}

		return null;
	}

	/**
	 *
	 * @return void
	 *
	 * @since
	 */
	public function doExecute()
	{
		$this->execute();
	}

	/**
	 *
	 * @return void
	 *
	 * @since
	 */
	public function getName()
	{

	}
}

$app                   = JApplicationCli::getInstance('JoomlaExtensionInstallerCli');
JFactory::$application = $app;
$app->execute();
