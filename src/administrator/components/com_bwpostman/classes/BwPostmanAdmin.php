<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman admin class for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Classes;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;

/**
 * BwPostman Footer
 *
 * @package BwPostman-Admin
 *
 * @since   0.9.1
 */
class BwPostmanAdmin {

	/**
	 * Method to write the BwPostman footer
	 *
	 * @return string
	 *
	 * @since   0.9.1
	 *
	 * @throws Exception
	 */
	static public function footer()
	{
		$version = BwPostmanHelper::getInstalledBwPostmanVersion();

		return 'BwPostman version ' . $version . ' by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>';
	}
}
