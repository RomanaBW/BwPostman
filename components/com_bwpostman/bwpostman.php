<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main component for frontend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

// Required class
require_once (JPATH_COMPONENT_SITE.'/classes/bwpostman.class.php');

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bwpostman/tables');

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

// Require specific controller if requested
$jinput	= JFactory::getApplication()->input;
if($controller = $jinput->getWord('controller')) {
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname    = 'BwPostmanController'.ucfirst($controller);
$controller   = new $classname();

// Perform the Request task
$controller->execute($jinput->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
