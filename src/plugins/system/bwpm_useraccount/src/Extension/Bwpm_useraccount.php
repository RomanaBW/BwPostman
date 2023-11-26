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

namespace BoldtWebservice\Plugin\System\Bwpm_useraccount\Extension;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use Exception;
use JLoader;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Result\ResultAwareInterface;
use Joomla\CMS\Event\User\AfterDeleteEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Log\LogEntry;
use RuntimeException;

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
final class Bwpm_useraccount extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	protected string $min_bwpostman_version    = '4.0';

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
	protected bool $BwPostmanComponentEnabled = false;

	/**
	 * Property to hold component version
	 *
	 * @var    string
	 *
	 * @since  4.1.0
	 */
	protected string $BwPostmanComponentVersion = '0.0.0';

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
     * @var BwLogger
	 *
	 * @since  4.1.0
	 */
	private BwLogger $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  4.1.0
	 */
	private string $log_cat  = 'Plg_UA';

	/**
	 * Property to hold debug
	 *
	 * @var    bool
	 *
	 * @since  4.1.0
	 */
	private bool $debug;

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
	public function __construct(DispatcherInterface $subject, array $config)
	{
        // Only do something if component is enabled
        if (!ComponentHelper::isEnabled('com_bwpostman'))
        {
            parent::__construct($subject, $config);

            $log_options  = array();
            $this->logger = BwLogger::getInstance($log_options);
            $this->debug  = (bool) $this->params->get('debug_option', false);

            $this->setDatabase(Factory::getContainer()->get(DatabaseInterface::class));
            $this->setBwPostmanComponentStatus();
            $this->setBwPostmanComponentVersion();
        }
	}

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since 4.2.6
     */
    public
    static function getSubscribedEvents(): array
    {
        // Only subscribe events if the component is installed and enabled
        if (!ComponentHelper::isEnabled('com_bwpostman'))
        {
            return [];
        }
        else
        {
            return [
                'onUserAfterSave'      => 'onUserAfterSave',
                'onUserAfterDelete'    => 'onUserAfterDelete',
            ];
        }
    }

    /**
     * Set the database.
     *
     * @param DatabaseInterface $db The database.
     *
     * @return  void
     *
     * @since   4.2.6
     */
    public function setDatabase(DatabaseInterface $db): void
    {
        $this->databaseAwareTraitDatabase = $db;
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
	protected function setBwPostmanComponentVersion(): void
    {
		$db   = $this->getDatabase();
		$query = $db->getQuery(true);

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
			$this->BwPostmanComponentVersion = '0.0.0';
			$this->logger->addEntry(new LogEntry($e->getMessage(), BwLogger::BW_ERROR, $this->log_cat));
		}
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
     * @param Event $event
     *
     * @return void
     *
     * @since  4.1.0
     */
	public function onUserAfterSave(Event $event): void
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserAfterSave reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return;
		}

        // If using a concrete event, do it the simple way
        if ($event instanceof AfterSaveEvent)
        {
            $data    = $event->getArgument('subject');
            $isNew = $event->getArgument('isNew');
            $result = $event->getArgument('savingResult');
        }
        // If using a generic event, do it the hard way
        else
        {
            [$data, $isNew, $result] = $event->getArguments();
        }

        if (!$result)
		{
			return;
		}

		// Get and sanitize data
		$user_mail = ArrayHelper::getValue($data, 'email', '', 'string');
		$user_id   = (int)$data['id'];

		if ($isNew)
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);

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
                $this->getApplication()->enqueueMessage('Error pluginUserAccount: ' . $e->getMessage() . '<br />', 'error');
			}
		}

        $result = [
            'subject'        => $data,
            'isNew'          => $isNew,
            'deletingResult' => $result,
        ];

        // Return the result
        $this->setResult($event, $result);
    }

    /**
     * Event method onUserAfterDelete
     *
     * Removes user ID of subscriber if account is deleted and subscriber with same email address exists
     *
     * @param Event $event
     *
     * @return void
     *
     * @since  4.1.0
     */
	public function onUserAfterDelete(Event $event): void
	{
		if ($this->debug)
		{
			$this->logger->addEntry(new LogEntry('onUserAfterDelete reached', BwLogger::BW_DEVELOPMENT, $this->log_cat));
		}

		if (!$this->prerequisitesFulfilled())
		{
			return;
		}

        // If using a concrete event, do it the simple way
        if ($event instanceof AfterDeleteEvent)
        {
            $data    = $event->getArgument('subject');
            $result = $event->getArgument('deletingResult');
        }
        // If using a generic event, do it the hard way
        else
        {
            [$data, $result, $errorMessage] = $event->getArguments();
        }

        if ($result !== true)
		{
			return;
		}

		// Get and sanitize data
		$user_mail = ArrayHelper::getValue($data, 'email', '', 'string');

		$db     = $this->getDatabase();
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
			$this->getApplication()->enqueueMessage('Error pluginUserAccount: ' . $e->getMessage() . '<br />', 'error');
		}

        $result = [
            'subject'        => $data,
            'deletingResult' => $result,
            'errorMessage'   => $errorMessage,
        ];

        // Return the result
        $this->setResult($event, $result);
    }

    /**
     * Method to set the event result
     *
     * @param Event $event
     * @param       $value
     *
     *
     * @since 4.2.6
     */
    private function setResult(Event $event, $value): void
    {
        if ($event instanceof ResultAwareInterface)
        {
            $event->addResult($value);

            return;
        }

        $result   = $event->getArgument('result', []) ?: [];
        $result   = is_array($result) ? $result : [];
        $result[] = $value;
        $event->setArgument('result', $result);
    }
}
