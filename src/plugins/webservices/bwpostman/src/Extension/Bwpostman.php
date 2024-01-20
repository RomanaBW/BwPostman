<?php
/**
 * BwPostman Webservice Plugin
 *
 * Plugin to support webservices of Joomla
 *
 * BwPostman Webservice Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman Webservice Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

namespace BoldtWebservice\Plugin\Webservices\Bwpostman\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;
use Joomla\Router\Route;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Web Services adapter for com_bwpostman.
 *
 * @since  4.0.0
 */
final class Bwpostman extends CMSPlugin
{
    /**
     * Registers com_bwpostman's API's routes in the application
     *
     * @param ApiRouter  $router The API Routing object
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function onBeforeApiRoute(ApiRouter $router): void
    {
        $defaults    = [
            'component' => 'com_bwpostman',
            'task'      => 'doCron',
        ];
        $traceDefaults = array_merge(['public' => false], $defaults);

        $routes = [
            new Route(['GET'], 'v1/bwpostman/maintenance', 'maintenance.doCron', [], $traceDefaults),
        ];

        $router->addRoutes($routes);
    }
}
