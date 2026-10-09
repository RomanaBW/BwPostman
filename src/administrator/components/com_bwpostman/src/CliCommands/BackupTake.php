<?php
/**
 * BwPostman Console Command BackupTake
 *
 * BwPostman console command to take a backup
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
 * BwPostman CLI Command BackupTake
 *
 * @package      BwPostman-Admin
 *
 * @subpackage    CLICommands
 *
 * @since       4.4.0
 *
 * @noinspection PhpUnused
 */
class BackupTake extends AbstractCommand
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
    protected static $defaultName = 'bwpostman:backup:take';

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
        $title = Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_UNCOMPRESSED_HEAD');

        if ($this->cliInput->getOption('compressed'))
        {
            $title = Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_COMPRESSED_HEAD');
        }

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

        $session->set('plg_bwpm_console_backup.compressed', $this->cliInput->getOption('compressed'));
        $session->set('plg_bwpm_console_backup.filename', $this->cliInput->getOption('filename') ?? null);
//        $session->set('plg_bwpm_console_backup.backup_path', $this->cliInput->getOption('backup_path') ?? null);
        $session->set('plg_bwpm_console_backup.errorMsg', '');

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

        $saveResult = $model->saveTables($this->cliInput->getOption('filename'));

        // Output backup result
        if ($saveResult === false)
        {
            $errorMsg = $session->get('plg_bwpm_console_backup.errorMsg', '');
            $this->ioStyle->error(Text::sprintf('COM_BWPOSTMAN_CLI_BACKUP_ERR_UNABLE_TO_BACKUP', $errorMsg));

            return 1;
        }
        else
        {
            $resultParts = explode('/', $saveResult);
            $fileName = end($resultParts);
            $filePath = implode('/', array_slice($resultParts, 0, -1));
            $outputString = "Filepath: " . $filePath . "\n\n" . "Filename: " . $fileName;

            $this->ioStyle->success(Text::sprintf('COM_BWPOSTMAN_CLI_BACKUP_TAKE_LBL_COMPLETE', $outputString));

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
        // Set default values
        $compressed = ComponentHelper::getParams('com_bwpostman')->get('compress_backup', true);

        // Set actual options
        $this->addOption('compressed', null, InputOption::VALUE_OPTIONAL, Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_OPT_COMPRESSED'), $compressed);
        $this->addOption('filename', null, InputOption::VALUE_OPTIONAL, Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_OPT_FILENAME'), null);
//        $this->addOption('backup_path', null, InputOption::VALUE_OPTIONAL, Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_OPT_BACKUP_PATH'), null);
        $this->setDescription(Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_DESC'));
        $this->setHelp(Text::_('COM_BWPOSTMAN_CLI_BACKUP_TAKE_HELP'));
    }
}
