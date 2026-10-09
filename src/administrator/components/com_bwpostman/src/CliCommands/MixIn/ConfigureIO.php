<?php
/**
 * BwPostman Console Commands Mixin ConfigureIO
 *
 * BwPostman console command mixin to configure IO
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

namespace BoldtWebservice\Component\BwPostman\Administrator\CliCommands\MixIn;

defined('_JEXEC') || die;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Set up the Symfony I/O objects
 *
 * @since   4.4.0
 */
trait ConfigureIO
{
    /**
     * @var   SymfonyStyle
     *
     * @since 4.4.0
     */
    private SymfonyStyle $ioStyle;

    public const int MAX_LINE_LENGTH = 200;

    /**
     * @var   InputInterface
     *
     * @since 4.4.0
     */
    private InputInterface $cliInput;

    /**
     * Configure the IO.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  void
     *
     * @since   4.4.0
     */
    private function configureSymfonyIO(InputInterface $input, OutputInterface $output): void
    {
        $this->cliInput = $input;
        $this->ioStyle  = new SymfonyStyle($input, $output);
    }
}
