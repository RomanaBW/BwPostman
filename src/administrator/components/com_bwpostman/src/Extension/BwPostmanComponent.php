<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman component class
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use JLoader;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use BoldtWebservice\Component\BwPostman\Administrator\Service\Html\BwPostman;
use Joomla\CMS\MVC\Model\DatabaseAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_bwpostman
 *
 * @since  4.0.0
 */
class BwPostmanComponent extends MVCComponent implements BootableExtensionInterface
{
	use HTMLRegistryAwareTrait;
	use DatabaseAwareTrait;

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

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Extension', BWPM_ADMINISTRATOR . '/src/Extension', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', BWPM_ADMINISTRATOR . '/Helper', false, false);
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Field', BWPM_ADMINISTRATOR . '/src/Field', false, false);
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
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Field', BWPM_SITE . '/src/Field', false, false);
	}
}
