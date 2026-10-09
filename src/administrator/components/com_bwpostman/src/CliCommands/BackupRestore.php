<?php
/**
 * BwPostman Console Command BackupRestore
 *
 * BwPostman console command to restore a backup
 *
 * @version %%version_number%%
 * @package BwPostman-CLI commands
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

namespace BoldtWebservice\Component\BwPostman\Administrator\CliCommands;

use BoldtWebservice\Component\BwPostman\Administrator\CliCommands\MixIn\ConfigureIO;
use BoldtWebservice\Component\BwPostman\Administrator\CliCommands\MixIn\InitialiseComponent;
use BoldtWebservice\Component\BwPostman\Administrator\Model\MaintenanceModel;
use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * BwPostman CLI Command BackupRestore
 *
 * @package      BwPostman-Admin
 *
 * @subpackage    CLICommands
 *
 * @since       4.4.0
 *
 * @noinspection PhpUnused
 */
class BackupRestore extends AbstractCommand
{
    use ConfigureIO;
    use InitialiseComponent;

    /**
     * The default command name
     *
     * @var    string
     *
     * @since  4.4.0
     */
    protected static $defaultName = 'bwpostman:backup:restore';

    /**
     * Internal function to execute the command.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  int  The command exit code
     *
     * @throws Exception
     *
     * @since   4.4.0
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $session = Factory::getApplication()->getSession();

        $this->configureSymfonyIO($input, $output);
        $title = Text::_('COM_BWPOSTMAN_CLI_BACKUP_RESTORE_HEAD');

        $this->ioStyle->title($title);

        $this->ioStyle->text(Text::_('COM_BWPOSTMAN_CLI_BACKUP_LOAD_BWPOSTMAN_COMPONENT'));

        try
        {
            $this->initialiseComponent($this->getApplication());
        }
        catch (Throwable $e)
        {
            $this->ioStyle->error([
                Text::_('COM_BWPOSTMAN_CLI_BACKUP_ERR_CANNOT_LOAD_BWPOSTMAN_COMPONENT'),
                $e->getMessage(),
            ]);

            return 255;
        }

        $session->set('plg_bwpm_console_restore.filename', $this->cliInput->getOption('filename') ?? null);
        $session->set('plg_bwpm_console_restore.errorMsg', '');

        $this->ioStyle->text(Text::_('COM_BWPOSTMAN_CLI_BACKUP_LOAD_MAINTENANCE_MODEL'));

        try
        {
            /** @var MaintenanceModel $model */
            $model = $this->componentObject->getMVCFactory()->createModel('Maintenance', 'Administrator');
        }
        catch (Throwable $e)
        {
            $this->ioStyle->error([
                Text::_('COM_BWPOSTMAN_CLI_BACKUP_ERR_CANNOT_LOAD_MAINTENANCE_MODEL'),
                $e->getMessage(),
            ]);

            return 254;
        }

//        $saveResult = $model->saveTables($this->cliInput->getOption('filename'));
        $saveResult = true;

        // Output backup result
        if ($saveResult === false)
        {
            $errorMsg = $session->get('plg_bwpm_console_restore.errorMsg', '');
            $this->ioStyle->error(Text::sprintf('COM_BWPOSTMAN_CLI_BACKUP_ERR_UNABLE_TO_RESTORE', $errorMsg));

            return 1;
        }
        else
        {
            $this->ioStyle->success(Text::_('COM_BWPOSTMAN_CLI_BACKUP_RESTORE_LBL_COMPLETE'));

            return 0;
        }
    }

    /**
     * Configure the command.
     *
     * @return  void
     *
     * @throws Exception
     *
     * @since   4.4.0
     */
    protected function configure(): void
    {
        // Set actual options
        $this->addOption('filename', null, InputOption::VALUE_REQUIRED, Text::_('COM_BWPOSTMAN_CLI_BACKUP_RESTORE_OPT_FILENAME'), null);
        $this->setDescription(Text::_('COM_BWPOSTMAN_CLI_BACKUP_RESTORE_DESC'));
        $this->setHelp(Text::_('COM_BWPOSTMAN_CLI_BACKUP_RESTORE_HELP'));
    }
}
