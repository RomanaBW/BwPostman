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
 * @license GNU/GPL, see LICENCE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public Licence as published by
 * the Free Software Foundation, either version 3 of the Licence or
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Extension;

defined('_JEXEC') or die;

use JLoader;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use BoldtWebservice\Component\BwPostman\Administrator\Service\Html\BwPostman;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\DI\Container;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_bwpostman
 *
 * @since  4.0.0
 */
class BwPostmanComponent extends MVCComponent implements BootableExtensionInterface, RouterServiceInterface
{
	use HTMLRegistryAwareTrait;
	use DatabaseAwareTrait;
	use RouterServiceTrait;

    /**
     * The container we were created with
     *
     * @var   Container
     *
     * @since 4.4.0
     */
    private Container $container;


    /**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial setup can be done from services of the container, e.g.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function boot(ContainerInterface $container): void
    {
		$this->getRegistry()->register('bwpostman', new BwPostman());

        JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman', BWPM_ADMINISTRATOR . '/src');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Extension', BWPM_ADMINISTRATOR . '/src/Extension');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', BWPM_ADMINISTRATOR . '/Helper');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Field', BWPM_ADMINISTRATOR . '/src/Field');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Classes', BWPM_ADMINISTRATOR . '/classes');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', BWPM_ADMINISTRATOR . '/Libraries');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Model', BWPM_ADMINISTRATOR . '/src/Model');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Controller', BWPM_ADMINISTRATOR . '/src/Controller');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\View', BWPM_ADMINISTRATOR . '/src/View');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Table', BWPM_ADMINISTRATOR . '/src/Table');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service', BWPM_ADMINISTRATOR . '/src/Service');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service\\Html', BWPM_ADMINISTRATOR . '/src/Service/Html');

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Dispatcher', BWPM_SITE . '/src/Dispatcher');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Service', BWPM_SITE . '/src/Service');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Controller', BWPM_SITE . '/src/Controller');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Model', BWPM_SITE . '/src/Model');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\View', BWPM_SITE . '/src/View');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Classes', BWPM_SITE . '/classes');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Site\\Field', BWPM_SITE . '/src/Field');
	}

    /**
     * Returns the Container the extension was created with.
     *
     * We are going to use it wherever we are not instantiated through the extension object, e.g. fields.
     *
     * @return  Container
     *
     * @since   4.4.0
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

}
