<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman includes file main component for backend.
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

defined('_JEXEC') or die('Restricted access');

// load constants
//require_once __DIR__ . '/constants.php';

// BwPostman Administration Component
define('BWPM_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_bwpostman');

// BwPostman Site Component
define('BWPM_SITE', JPATH_SITE.'/components/com_bwpostman');

// register classes
JLoader::register('BwAccess', BWPM_ADMINISTRATOR . '/libraries/access/BwAccess.php');
JLoader::register('BwAccess', BWPM_ADMINISTRATOR . '/libraries/logging/BwLogger.php');


//JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Libraries', BWPM_ADMINISTRATOR . '/libraries', false, false, 'psr4');
//JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\BwAccess', BWPM_ADMINISTRATOR . '/libraries/access', false, false, 'psr4');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', BWPM_ADMINISTRATOR . '/Helper', false, false, 'psr4');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Field', BWPM_ADMINISTRATOR . '/Field', false, false, 'psr4');
JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Classes', BWPM_ADMINISTRATOR . '/classes', false, false, 'psr4');
