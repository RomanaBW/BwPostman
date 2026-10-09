<?php
/**
 * BwPostman Console Plugin Extension
 *
 * BwPostman console extension for cli plugin.
 *
 * @version %%version_number%%
 * @package BwPostman-CLI
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public Licence as published by
 * the Free Software Foundation, either version 3 of the Licence, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public Licence for more details.
 *
 * You should have received a copy of the GNU General Public Licence
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace BoldtWebservice\Plugin\Console\BwPostman\Extension;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Plugin\Console\BwPostman\Command\CommandFactoryInterface;
use Exception;
use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationEvent;
use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Event\SubscriberInterface;
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Throwable;

/**
 * Class BwPostman Console Plugin extension
 *
 * @since       4.4.0
 */
final class BwPostman extends CMSPlugin implements SubscriberInterface
{
    use MVCFactoryAwareTrait;

    /**
     * Load the language file on instantiation.
     *
     * @var    bool
     * @since  4.4.0
     */
    protected $autoloadLanguage = true;

    /**
     * BwPostman CLI Command Factory object instance.
     *
     * @var   CommandFactoryInterface
     *
     * @since 4.4.0
     */
    protected CommandFactoryInterface $commandFactory;

    /**
     * Sets the CLI command factory
     *
     * @param   CommandFactoryInterface  $factory
     *
     * @since       4.4.0
     */
    public function setCLICommandFactory(CommandFactoryInterface $factory): void
    {
        $this->commandFactory = $factory;
    }

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since 4.4.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ApplicationEvents::BEFORE_EXECUTE => 'registerCLICommands',
        ];
    }

    /**
     * Registers command classes to the CLI application.
     *
     * This is an event handled for the ApplicationEvents::BEFORE_EXECUTE event.
     *
     * @param   ApplicationEvent  $event  The before_execute application event being handled
     *
     * @since        4.4.0
     * @noinspection PhpUnused
     */
    public function registerCLICommands(ApplicationEvent $event): void
    {
        /** @var ConsoleApplication $app */
        $app = $event->getApplication();

        // Load the component language files
        $lang = $app->getLanguage();
        $lang->load('com_bwpostman', JPATH_ADMINISTRATOR);
        $lang->load('com_bwpostman', JPATH_SITE);

        // Only register CLI commands if BwPostman is installed and enabled
        try
        {
            if (!ComponentHelper::isEnabled('com_bwpostman'))
            {
                return;
            }
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        catch (Throwable $e)
        {
            return;
        }

        // Try to find all commands in the CliCommands directory of the component
        try
        {
            $files = Folder::files(JPATH_ADMINISTRATOR . '/components/com_bwpostman/src/CliCommands', '.php');
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        catch (Exception $e)
        {
            $files = [];
        }

        $files = is_array($files) ? $files : [];

        foreach ($files as $file)
        {
            /**
             * Try to instantiate and register each command object, going through the BwPostman CLI command factory.
             *
             * The try/catch block has a rationale behind it. We get the command name by removing the .php extension
             * from the base name of the file. This is combined with the root namespace of CLI commands to construct the
             * class FQN we will be trying to instantiate.
             *
             * However, some hosts create copies of the files e.g. copying or renaming FooBar.php to FooBar.01.php or
             * even FooBar_01.php. This is something we've seen and documented since 2013, mostly attributed to some
             * hosts' really broken file scanners. These files would create invalid class names. Since these class names
             * do not exist, trying to instantiate them will fail with a RuntimeException from the factory. We catch
             * this and move on, ignoring the bad file.
             *
             * Further to that, it's possible that a different combination of unforeseen mistakes by the host and / or
             * Joomla (e.g. not all files copied correctly on update) causing one or more CLI command classes to error
             * out. This is why we are catching Throwable instead of just RuntimeException.
             */
            try
            {
                $commandFactory = $this->commandFactory;
                $cliCommand     = $commandFactory->getCLICommand(basename($file, '.php'));

                $app->addCommand($cliCommand);
            }
            catch (Throwable $e)
            {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $errorMessage = $e->getMessage();
            }
        }
    }
}
