<?php
/**
 * Boldt Webservice LibRegister Plugin
 *
 * Plugin to register own libraries
 *
 * Boldt Webservice LibRegister Plugin main file.
 *
 * @version %%version_number%%
 * @package Boldt Webservice LibRegister Plugin
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

defined('_JEXEC') or die('Restricted access');

/**
 * Class LibRegister
 *
 * @since  2.0.0
 */
class PlgSystemBW_Libregister extends JPlugin
{
	/**
	 * Event method onAfterInitialise
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since  2.0.0
	 */
	public function onAfterInitialise()
	{
		JLoader::registerPrefix('J', JPATH_PLUGINS . '/system/bw_libregister/libraries/toolbar');
	}
}
