<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use JLoader;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use BoldtWebservice\Component\BwPostman\Administrator\Service\Html\BwPostman;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_banners
 *
 * @since  4.0.0
 */
class BwPostmanComponent extends MVCComponent implements BootableExtensionInterface
{
	use HTMLRegistryAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('bwpostman', new BwPostman);

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', BWPM_ADMINISTRATOR . '/Helper', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Field', BWPM_ADMINISTRATOR . '/Field', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Classes', BWPM_ADMINISTRATOR . '/classes', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', BWPM_ADMINISTRATOR . '/libraries', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Model', BWPM_ADMINISTRATOR . '/src/Model', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Controller', BWPM_ADMINISTRATOR . '/src/Controller', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\View', BWPM_ADMINISTRATOR . '/src/View', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Table', BWPM_ADMINISTRATOR . '/src/Table', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service', BWPM_ADMINISTRATOR . '/src/Service', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service\\Html', BWPM_ADMINISTRATOR . '/src/Service/Html', false, false);

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Dispatcher', BWPM_SITE . '/src/Dispatcher', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Service', BWPM_SITE . '/src/Service', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Controller', BWPM_SITE . '/src/Controller', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Model', BWPM_SITE . '/src/Model', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\View', BWPM_SITE . '/src/View', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Classes', BWPM_SITE . '/classes', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Field', BWPM_SITE . '/Field', false, false);
	}
}
