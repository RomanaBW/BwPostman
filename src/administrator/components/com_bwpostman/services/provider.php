<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// BwPostman Administration Component
const BWPM_ADMINISTRATOR = JPATH_ADMINISTRATOR . '/components/com_bwpostman';

// BwPostman Site Component
const BWPM_SITE = JPATH_SITE . '/components/com_bwpostman';

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
 * The bwpostman service provider.
 *
 * @since  4.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
		$container->registerServiceProvider(new MVCFactory('\\BoldtWebservice\\Component\\BwPostman'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\BoldtWebservice\\Component\\BwPostman'));
		$container->registerServiceProvider(new RouterFactory('\\BoldtWebservice\\Component\\BwPostman'));

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
