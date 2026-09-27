<?php

/**
 * BwPostman Console Plugin Provider
 *
 * BwPostman console plugin provider for cli.
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
defined('_JEXEC') || die;

use BoldtWebservice\Plugin\Console\BwPostman\Command\CommandFactoryInterface;
use BoldtWebservice\Plugin\Console\BwPostman\Command\CommandFactoryProvider;
use BoldtWebservice\Plugin\Console\BwPostman\Extension\BwPostman;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

// Make sure that Joomla has registered the namespace for the plugin
if (!class_exists('\BoldtWebservice\Plugin\Console\BwPostman\Extension\BwPostman'))
{
    JLoader::registerNamespace('\BoldtWebservice\Plugin\Console\BwPostman', realpath(__DIR__ . '/../src'));
}

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param Container $container The DI container.
     *
     * @return  void
     *
     * @since   4.3.6
     */
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('BoldtWebservice\\Component\\BwPostman'));
        $container->registerServiceProvider(new CommandFactoryProvider());

        $container->set(
            PluginInterface::class,
            function (Container $container)
            {
                $config     = (array)PluginHelper::getPlugin('console', 'bwpostman');
                $subject    = $container->get(DispatcherInterface::class);

                $plugin     = new BwPostman($subject, $config);

                $plugin->setApplication(Factory::getApplication());
                $plugin->setCLICommandFactory($container->get(CommandFactoryInterface::class));

                return $plugin;
            }
        );
    }
};
