<?php
/**
 * BwPostman Overview Module
 *
 * BwPostman main part of module.
 *
 * @version 1.3.1 bwpm
 * @package BwPostman-Rchive-Module
 * @author Romana Boldt
 * @copyright (C) 2015 - 2016 Boldt Webservice <forum@boldt-webservice.de>
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

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$app		= JFactory::getApplication();
$document	= JFactory::getDocument();

// Get document object, set document title and add css
$templateName	= $app->getTemplate();
$css_filename	= '/templates/' . $templateName . '/css/mod_bwpostman_overview.css';

$document->addStyleSheet(JURI::root(true) . '/modules/mod_bwpostman_overview/assets/css/bwpostman_overview.css');
if (file_exists(JPATH_BASE . $css_filename)) {
	$document->addStyleSheet(JURI::root(true) . $css_filename);
}

$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$list				= modBwPostmanOverviewHelper::getList($params, $module->id);

require JModuleHelper::getLayoutPath('mod_bwpostman_overview', $params->get('layout', 'default'));
