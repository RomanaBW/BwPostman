<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman concrete web application class.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Libraries;

use Joomla\CMS\Application\WebApplication;

defined('_JEXEC') or die('Restricted access');

/**
 * Concrete web application class
 *
 * @since       2.4.0
 */
class BwWebApp extends WebApplication
{
	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   2.4.0
	 */
	public function doExecute()
	{
		parent::execute();
	}
}
