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
class BwPostmanComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface
{
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;

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

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', BWPM_ADMINISTRATOR . '/Helper', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Field', BWPM_ADMINISTRATOR . '/Field', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Classes', BWPM_ADMINISTRATOR . '/classes', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', BWPM_ADMINISTRATOR . '/libraries', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Model', BWPM_ADMINISTRATOR . '/src/Model', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Controller', BWPM_ADMINISTRATOR . '/src/Controller', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\View', BWPM_ADMINISTRATOR . '/src/View', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Table', BWPM_ADMINISTRATOR . '/src/Table', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service', BWPM_ADMINISTRATOR . '/src/Service', false, false, 'psr4');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service\\Html', BWPM_ADMINISTRATOR . '/src/Service/Html', false, false, 'psr4');

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Models', BWPM_SITE . '/models', false, false, 'psr4');
	}
}
