<?php
/**
 * BwPostman Console Plugin Command Factory Interface
 *
 * BwPostman command factory interface for cli.
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

namespace BoldtWebservice\Plugin\Console\BwPostman\Command;

defined('_JEXEC') || die;

use Joomla\Console\Command\AbstractCommand;

interface CommandFactoryInterface
{
    public function getCLICommand(string $commandName): AbstractCommand;
}
