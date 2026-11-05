<?php
/**
 * BwPostman component
 *
 * DI service provider of the component (Site AND Admin).
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

defined('_JEXEC') or die;

// BwPostman Administration Component
const BWPM_ADMINISTRATOR = JPATH_ADMINISTRATOR . '/components/com_bwpostman';

// BwPostman Site Component
const BWPM_SITE = JPATH_SITE . '/components/com_bwpostman';

// BwPostman default path for Backups
if (!defined('BWPM_BACKUP')) define('BWPM_BACKUP', JPATH_SITE . '/images/com_bwpostman/backup_tables');

use BoldtWebservice\Component\BwPostman\Administrator\MVC\BwPostmanMVCFactory;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemService;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerService;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwUserStateInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwUserStateService;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Extension\BwPostmanComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The service provider of the component.
 *
 * It defines HOW each service is created – not WHO consumes it.
 * On boot Joomla creates a child container and runs this register() once.
 * The provider applies to Site AND Administrator (com_content does the same).
 *
 * @since  4.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
     * Registers the services of the component in the DI container.
     * *
     * * Services are always keyed by the interface, so the implementation
     * * can be swapped later without touching any consumer.
     *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container): void
    {
		$container->registerServiceProvider(new MVCFactory('\\BoldtWebservice\\Component\\BwPostman'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\BoldtWebservice\\Component\\BwPostman'));
		$container->registerServiceProvider(new RouterFactory('\\BoldtWebservice\\Component\\BwPostman'));

        $container->set(
            BwFilesystemInterface::class,
            static fn (Container $c) => new BwFilesystemService()
        );

        $container->set(
            BwUserStateInterface::class,
            static fn (Container $c) => new BwUserStateService()
        );

        $container->set(
            BwLoggerInterface::class,
            static fn (Container $c) => new BwLoggerService()
        );

        // Register our custom MVC factory instead of the core one,
        // so that models (Site and Admin!) get their services injected.
        $container->set(
            MVCFactoryInterface::class,
            static fn (Container $c) => new BwPostmanMVCFactory('\\BoldtWebservice\\Component\\BwPostman', $c)
        );

  		$container->set(
			ComponentInterface::class,
			function (Container $container)
			{
				$component = new BwPostmanComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
