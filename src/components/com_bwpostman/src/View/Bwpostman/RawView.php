<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman raw main view for frontend.
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

namespace BoldtWebservice\Component\BwPostman\Site\View\Bwpostman;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

\JLoader::registerNamespace('BoldtWebservice\\Plugin\\Bwpostman\\Bwtimecontrol\\Helper', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers');

use BoldtWebservice\Plugin\Bwpostman\Bwtimecontrol\Helper\BwPostmanPhpCron;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * BwPostman RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class RawView extends BaseHtmlView
{
	/**
	 * property to hold form data
	 *
	 * @var array   $form
	 *
	 * @since       4.0.0
	 */
	protected $form;

	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since       4.0.0
	 */
	protected object $item;

	/**
	 * property to hold subscriber data
	 *
	 * @var object   $sub
	 *
	 * @since       4.0.0
	 */
	protected object $sub;

	/**
	 * property to hold row object
	 *
	 * @var object   $row
	 *
	 * @since       0.9.1
	 */
	protected object $row;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public array $permissions;

    /**
     * Method to do the cron loop
     *
     * @return boolean
     *
     * @throws  Exception
     *
     * @since       2.3.0
     */
    public function doCron(): bool
    {
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

        return true;
    }
}
