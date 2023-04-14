<?php
/**
 * BwPostman Overview Module
 *
 * BwPostman main part of module.
 *
 * @version %%version_number%%
 * @package BwPostman-Overview-Module
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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use BoldtWebservice\Module\BwPostmanOverview\Site\Helper\ModBwPostmanOverviewHelper;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');
JLoader::registerNamespace('BoldtWebservice\\Module\\BwPostmanOverview\\Site\\Helper', JPATH_SITE . '/modules/mod_bwpostman_overview/src/Helper');

$app      = Factory::getApplication();
$document = $app->getDocument();

// Get document object, set document title and add css
$templateName = $app->getTemplate();
$css_filename = '/templates/' . $templateName . '/css/mod_bwpostman_overview.css';

$wa = $document->getWebAssetManager();
$wr = $wa->getRegistry();
$wr->addRegistryFile('media/mod_bwpostman_overview/joomla.asset.json');

$wa->useStyle('mod_bwpostman_overview.bwpostman_overview');

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('mod_bwpostman_overview.bwpostman_overview_custom', Uri::root(true) . $css_filename);
//	$document->addStyleSheet(Uri::root(true) . $css_filename);
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));
$list            = ModBwPostmanOverviewHelper::getList($params, $module->id);

require ModuleHelper::getLayoutPath('mod_bwpostman_overview', $params->get('layout', 'default'));
