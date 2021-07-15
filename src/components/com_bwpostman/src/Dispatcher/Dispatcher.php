<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters dispatcher for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

namespace BoldtWebservice\Component\BwPostman\Site\Dispatcher;

defined('JPATH_PLATFORM') or die;

use BwPostmanPhpCron;
use Exception;
use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * ComponentDispatcher class for com_bwpostman
 *
 * @since  4.0.0
 */
class Dispatcher extends ComponentDispatcher
{
	/**
	 * Dispatch a controller task. Redirecting the user if appropriate.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function dispatch()
	{
		$input = Factory::getApplication()->input;
		$view  = $input->get('view', 'register');
		$input->set('controller', $view);

		parent::dispatch();
	}

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
		$pluginPw   = (string) $pluginParams->get('bwtimecontrol_passwd');
		$pluginUser = (string) $pluginParams->get('bwtimecontrol_username');

		if ($pluginUser === "" || $pluginPw === "")
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_TC_NO_CREDENTIALS'), 'error');
		}

		$bwpostmancron = new BwPostmanPhpCron();

		$bwpostmancron->doCronJob();

		return true;
	}
}
