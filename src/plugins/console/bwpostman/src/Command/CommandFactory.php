<?php
/**
 * BwPostman Console Plugin Command Factory
 *
 * BwPostman command factory for cli.
 *
 * @version %%version_number%%
 * @package BwPostman-CLI
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

namespace BoldtWebservice\Plugin\Console\BwPostman\Command;

defined('_JEXEC') || die;

use Joomla\Application\ApplicationInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

class CommandFactory implements CommandFactoryInterface, DatabaseAwareInterface
{
    use MVCFactoryAwareTrait;
    use DatabaseAwareTrait;

    private ApplicationInterface $app;

    public function setApplication(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function getCLICommand(string $commandName): AbstractCommand
    {
        $classFQN = 'BoldtWebservice\\Component\\BwPostman\\Administrator\\CliCommands\\' . ucfirst($commandName);

        if (!class_exists($classFQN))
        {
            throw new \RuntimeException(sprintf('Unknown BwPostman CLI command class ‘%s’.', $commandName));
        }

        $classParents = class_parents($classFQN);

        if (!in_array(AbstractCommand::class, $classParents))
        {
            throw new \RuntimeException(sprintf('Invalid BwPostman CLI command object ‘%s’.', $commandName));
        }

        $o = new $classFQN;

        if (method_exists($classFQN, 'setMVCFactory'))
        {
            $o->setMVCFactory($this->getMVCFactory());
        }

        if ($o instanceof DatabaseAwareInterface)
        {
            $o->setDatabase($this->getDatabase());
        }

        if (method_exists($o, 'setApplication'))
        {
            $o->setApplication($this->getApplication());
        }

        return $o;
    }

    private function getApplication(): ApplicationInterface
    {
        return $this->app;
    }
}
