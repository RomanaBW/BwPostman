<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman search tool layout.
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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

$data = $displayData;

// Receive overrideable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
HtmlHelper::_('searchtools.form', $formSelector, $data['options']);

?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<?php echo LayoutHelper::render('default.bar', $data, $basePath = JPATH_COMPONENT_ADMINISTRATOR .'/layouts/searchtools'); ?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php echo LayoutHelper::render('default.list', $data, $basePath = JPATH_COMPONENT_ADMINISTRATOR .'/layouts/searchtools'); ?>
		</div>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container-filters hidden-phone clearfix">
		<?php echo LayoutHelper::render('default.filters', $data, $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools'); ?>
	</div>
</div>
