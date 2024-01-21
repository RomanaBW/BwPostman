<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance controller for api.
 *
 * @version %%version_number%%
 * @package BwPostman-Api
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

namespace BoldtWebservice\Component\BwPostman\API\Controller;

use BoldtWebservice\Plugin\Bwpostman\Bwtimecontrol\Helper\BwPostmanPhpCron;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * The styles controller
 *
 * @since  4.2.7
 */
class MaintenanceController extends ApiController
{
    /**
     * The content type of the item.
     *
     * @var    string
     * @since  4.2.7
     */
    protected $contentType = '';

    /**
     * The default view for the display method.
     *
     * @var    string
     *
     * @since  4.2.7
     */
    protected $default_view = '';

    /**
     * Constructor
     *
     * @param	array	$config		An optional associative array of configuration settings.
     *
     * @return void
     *
     * @throws Exception
     *
     * @since	1.0.1
     *
     * @see		JController
     */
    public function __construct($config = array())
    {
        $this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

        parent::__construct($config, $this->factory);

        // Register Extra tasks
        $this->registerTask('doCron', 'doCron');
    }

    /**
     * Own method to start the cron server.
     *
     * @throws Exception
     *
     * @since   4.2.7
     */
    public function doCron(): void
    {
        \JLoader::registerNamespace('BoldtWebservice\\Plugin\\Bwpostman\\Bwtimecontrol\\Helper', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers');

        $plugin = PluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
        $pluginParams = new Registry();
        $pluginParams->loadString($plugin->params);
        $pluginPw   = (string) $pluginParams->get('bwtimecontrol_passwd', '');
        $pluginUser = (string) $pluginParams->get('bwtimecontrol_username', '');

        if ($pluginUser === "" || $pluginPw === "")
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_TC_NO_CREDENTIALS'), 'error');
        }

        $bwpostmancron = new BwPostmanPhpCron();

        $bwpostmancron->doCronJob();
    }
}
