<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main component for backend.
 *
 * @version 1.3.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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

// Component name and database prefix
define ('BWPOSTMAN_COMPONENT_NAME', basename (dirname (__FILE__)));
define ('BWPOSTMAN_NAME', substr (BWPOSTMAN_COMPONENT_NAME, 4));

// Component location
define ('BWPOSTMAN_COMPONENT_LOCATION', basename (dirname (dirname (__FILE__))));

// Component paths
define ('BWPOSTMAN_PATH_COMPONENT_RELATIVE', BWPOSTMAN_COMPONENT_LOCATION . '/' . BWPOSTMAN_COMPONENT_NAME);
define ('BWPOSTMAN_PATH_SITE', JPATH_ROOT .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
define ('BWPOSTMAN_PATH_ADMIN', JPATH_ADMINISTRATOR .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
define ('BWPOSTMAN_PATH_MEDIA', JPATH_ROOT . '/media/' . BWPOSTMAN_NAME);

// URLs
define ('BWPOSTMAN_URL_COMPONENT', 'index.php?option=' . BWPOSTMAN_COMPONENT_NAME);
define ('BWPOSTMAN_URL_SITE', JURI::Root () . BWPOSTMAN_PATH_COMPONENT_RELATIVE . '/');
define ('BWPOSTMAN_URL_MEDIA', JURI::Root () . 'media/' . BWPOSTMAN_NAME . '/');

// Miscellaneous
define ('BWPOSTMAN_LOG_MEM', 0);

// import joomla controller library
jimport('joomla.application.component.controller');

// Require class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/admin.class.php');

// Get the user object
$user	= JFactory::getUser();
$app	= JFactory::getApplication();
// Access check.
if ((!$user->authorise('core.manage', 'com_bwpostman'))) return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');


// Get an instance of the controller
$controller	= JControllerLegacy::getInstance('BwPostman');

// Perform the Request task
$jinput = JFactory::getApplication()->input;
$controller->execute($jinput->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
