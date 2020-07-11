<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main component for frontend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

// Required class
require_once(JPATH_COMPONENT_SITE . '/classes/bwpostman.class.php');

// Set the table directory
Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

// Require the base controller
require_once(JPATH_COMPONENT . '/controller.php');

// Require specific controller
$jinput = Factory::getApplication()->input;
$view   = $jinput->getCmd('view', '');

if ($view)
{
	$path = JPATH_COMPONENT . '/controllers/' . $view . '.php';

	if (file_exists($path))
	{
		include_once $path;
	}
}

// Create the controller
$classname    = 'BwPostmanController' . ucfirst($view);
$controller   = new $classname;

// Perform the Request task
$controller->execute($jinput->getCmd('task', 'display'));

// Redirect if set by the controller
$controller->redirect();
