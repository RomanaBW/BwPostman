<?php
/**
 * BwPostman component
 *
 * Custom MVC factory with service injection.
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

namespace BoldtWebservice\Component\BwPostman\MVC;

use BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwUserStateServiceAwareInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerInterface;
use BoldtWebservice\Component\BwPostman\Administrator\Service\BwUserStateInterface;
use Exception;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Model\ModelInterface;
use Joomla\DI\Container;
use Psr\Log\LoggerInterface;

/**
 * Extended MVC factory with service injection.
 *
 * After creating an MVC object it checks which Aware interfaces the object
 * implements and injects the matching services from the component's
 * container. Adding a new service = one more if-line in injectServices().
 *
 * NOTE (verified): the parent constructor signature is
 * __construct($namespace, ?LoggerInterface $logger = null) – identical since
 * Joomla 4.0.0, so it works for Joomla 5 and 6 alike. See the constructor
 * DocBlock below.
 *
 * @since   4.4.0
 */
class BwPostmanMVCFactory extends MVCFactory
{
    /**
     * The component's DI container, used to resolve the services to inject.
     *
     * @var    Container
     * @since  4.4.0
     */
    private readonly Container $servicesContainer;

    /**
     * Constructor.
     *
     * Matches the parent signature ($namespace + optional PSR-3 logger,
     * identical since Joomla 4.0.0) and adds the component's DI container,
     * which is only used here to resolve the services to inject. We always
     * construct the factory ourselves in services/provider.php, so the extra
     * parameter is safe.
     *
     * @param   string                $namespace  The component namespace prefix.
     * @param   Container             $container  The component's DI container.
     * @param   LoggerInterface|null  $logger     Optional PSR-3 logger (passed to parent).
     *
     * @since   4.4.0
     */
    public function __construct(string $namespace, private readonly Container $container, ?LoggerInterface $logger = null)
    {
        parent::__construct($namespace, $logger);

        $this->servicesContainer = $container;
    }

    /**
     * Creates and returns a model object, with services injected.
     * *
     * * NOTE ON SIGNATURE: the parent MVCFactory deliberately declares no
     * * parameter types (maximising backwards compatibility). Adding parameter
     * * types here would violate contravariance and cause a fatal PHP error
     * * ("must be compatible with parent"). Adding the RETURN type, however,
     * * is allowed (covariant) and documents the contract for consumers.
     *
     * @param   string  $name    The model name.
     * @param   string  $prefix  The client prefix ('Administrator', 'Site', ...).
     * @param   array   $config  Configuration array for the model.
     *
     * @return  ModelInterface|null  The model object on success, null otherwise.
     *
     * @throws Exception
     *
     * @since   4.4.0
     */
    public function createModel($name, $prefix = '', array $config = []): ?ModelInterface
    {
        $model = parent::createModel($name, $prefix, $config);

        return $model !== null ? $this->injectServices($model) : null;
    }


    // Extend analogously for views/tables if they need services:
    // public function createView(string $name, ?string $client = null): ?object
    // public function createTable(string $name, ?string $client = null): ?object

    private function injectServices(object $object): object
    {
        if ($object instanceof BwFilesystemServiceAwareInterface) {
            $object->setFilesystem(
                $this->container->get(BwFilesystemInterface::class)
            );
        }

        if ($object instanceof BwUserStateServiceAwareInterface) {
            $object->setUserState(
                $this->container->get(BwUserStateInterface::class)
            );
        }

        if ($object instanceof BwLoggerServiceAwareInterface) {
            $object->setLogger(
                $this->container->get(BwLoggerInterface::class)
            );
        }

        return $object;
    }
}
