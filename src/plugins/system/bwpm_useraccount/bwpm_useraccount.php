<?php
/**
 * BwPostman UserAccount Plugin
 *
 * Plugin to automated alignment of Joomla users with BwPostman subscribers
 *
 * BwPostman UserAccount Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman UserAccount Plugin
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
use Joomla\Database\DatabaseInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Log\LogEntry;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', JPATH_ADMINISTRATOR.'/components/com_bwpostman/libraries');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper', true);
JLoader::registerAlias("BwLogger", "BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries\\BwLogger");
JLoader::registerAlias("BwPostmanHelper", "BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper\\BwPostmanHelper");
JLoader::loadByAlias("BwLogger");
JLoader::loadByAlias("BwPostmanHelper");

/**
 * Class UserAccount
 *
 * @since  4.1.0
 */
class PlgSystemBWPM_UserAccount extends JPlugin
{
	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	protected $min_bwpostman_version    = '4.0';

	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 *
	 * @since  4.1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Property to hold component enabled status
	 *
	 * @var    boolean
	 *
	 * @since  4.1.0
	 */
	protected $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  4.1.0
	 */
	protected $BwPostmanComponentVersion = '0.0.0';

	/**
	 * Property to hold app
	 *
	 * @var    object
	 *
	 * @since  4.1.0
	 */
	protected $app;
	/**
	 * Property to hold logger
	 *
	 * @var    object
	 *
	 * @since  4.1.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    object
	 *
	 * @since  4.1.0
	 */
	private $log_cat  = 'Plg_UA';

	/**
	 * Property to hold debug
	 *
	 * @var    boolean
	 *
	 * @since  4.1.0
	 */
	private $debug;

	/**
	 * PlgSystemBWPM_UserAccount constructor.
	 *
	 * @param DispatcherInterface $subject
	 * @param array               $config
	 *
	 * @throws Exception
	 *
	 * @since   4.1.0
	 */
	public function __construct(DispatcherInterface &$subject, array $config)
	{
		parent::__construct($subject, $config);

		$log_options    = array();
		$this->logger   = BwLogger::getInstance($log_options);
		$this->debug    = $this->params->get('debug_option', '0');

		$this->setBwPostmanComponentStatus();
		$this->setBwPostmanComponentVersion();
		$this->loadLanguageFiles();
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
	 */
	protected function setBwPostmanComponentStatus()
	{
		$_db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('enabled'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('com_bwpostman'));

		try
		{
			$_db->setQuery($query);

			$enabled                = (bool)$_db->loadResult();
			$this->BwPostmanComponentEnabled = $enabled;

			if ($this->debug)
			{
				$this->logger->addEntry(new LogEntry(sprintf('Component is enabled: %s', $enabled), BwLogger::BW_DEVELOPMENT, $this->log_cat));
			}
		}
		catch (Exception $e)
		{
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
	 * @since 4.1.0
	 */
	protected function setBwPostmanComponentVersion()
	{
		$_db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));

		try
		{
			$_db->setQuery($query);


			$result   = $_db->loadResult();

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
			$this->BwPostmanComponentVersion = '0.0.0';
			$this->logger->addEntry(new LogEntry($e->getMessage(), BwLogger::BW_ERROR, $this->log_cat));
		}
	}

	/**
	 * Method to load further language files
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
	 */
	protected function loadLanguageFiles()
	{
		$lang = $this->app->getLanguage();

		//Load first english file of component
		$lang->load('com_bwpostman', JPATH_SITE, 'en-GB', true);

		//load specific language of component
		$lang->load('com_bwpostman', JPATH_SITE, null, true);

		//Load specified other language files in english
		$lang->load('plg_system_bwpm_useraccount', JPATH_ADMINISTRATOR, 'en-GB', true);

		// and other language
		$lang->load('plg_system_bwpm_useraccount', JPATH_ADMINISTRATOR, null, true);
	}

	/**
	 * Method to check if prerequisites are fulfilled
	 *
	 * @return  bool
	 *
	 * @since   4.1.0
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
	 * Event method onUserAfterSave
	 *
	 * Writes user ID at subscriber if account is created and subscriber with same email address exists
	 *
	 * @param array  $data   User data
	 * @param bool   $isNew  true on new user
	 * @param bool   $result result of saving user
	 *
	 * @return  bool
	 *
	 * @throws Exception
	 * @since  4.1.0
	 */
	public function onUserAfterSave(array $data, bool $isNew, bool $result): bool
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

		// Get and sanitize data
		$user_mail = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id   = (int)$data['id'];

		if ($isNew)
		{
			$db        = Factory::getContainer()->get(DatabaseInterface::class);
			$query  = $db->getQuery(true);

			$query->update($db->quoteName('#__bwpostman_subscribers'));
			$query->set($db->quoteName('user_id') . " = " . $db->quote($user_id));
			$query->where("`email` = '" . $user_mail . "'");

			try
			{
				$db->setQuery($query);

				$db->execute();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage('Error pluginUserAccount: ' . $e->getMessage() . '<br />', 'error');
			}
		}

		return true;
	}

	/**
	 * Event method onUserAfterDelete
	 *
	 * Removes user ID of subscriber if account is deleted and subscriber with same email address exists
	 *
	 * @param array $data   User data
	 * @param bool  $result true on new user
	 *
	 * @return  bool
	 *
	 * @throws Exception
	 * @since  4.1.0
	 */
	public function onUserAfterDelete(array $data, bool $result): bool
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserAfterDelete reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return false;
		}

		if ($result !== true)
		{
			return false;
		}

		// Get and sanitize data
		$user_mail = ArrayHelper::getValue($data, 'email', '', 'string');

		$db     = Factory::getContainer()->get(DatabaseInterface::class);
		$query  = $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_subscribers'));
		$query->set($db->quoteName('user_id') . " = 0");
		$query->where("`email` = '" . $user_mail . "'");

		try
		{
			$db->setQuery($query);

			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage('Error pluginUserAccount: ' . $e->getMessage() . '<br />', 'error');
		}

		return true;
	}
}
