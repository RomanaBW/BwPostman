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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$data = $displayData;
$layout	= $data['tab'];

// Receive overrideable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

$noResultsText     = '';
$hideActiveFilters = false;
$showFilterButton  = false;
$showSelector      = false;
$selectorFieldName = $data['options']['selectorFieldName'] ?? 'client_id';

// If a filter form exists.
if (isset($data['view']->filterForm) && !empty($data['view']->filterForm))
{
	// Checks if a selector (e.g. client_id) exists.
	if ($selectorField = $data['view']->filterForm->getField($selectorFieldName))
	{
		$showSelector = $selectorField->getAttribute('filtermode', '') === 'selector' ? true : $showSelector;

		// Checks if a selector should be shown in the current layout.
		if (isset($data['view']->layout))
		{
			$showSelector = $selectorField->getAttribute('layout', 'default') != $data['view']->layout ? false : $showSelector;
		}

		// Unset the selector field from active filters group.
		unset($data['view']->activeFilters[$selectorFieldName]);
	}

	// Checks if the filters button should exist.
	$fieldset	= $data['view']->filterForm->getFieldset($layout);
	$filters = array();

	foreach ($fieldset as $fieldName => $field)
	{
		if (strpos($fieldName,  'filter_') !== false)
		{
			$filters[$fieldName] = $field;
		}
	}

	$showFilterButton = isset($filters['filter_search']) && count($filters) === 1 ? false : true;

	// Checks if it should show the be hidden.
	$hideActiveFilters = empty($data['view']->activeFilters);

	// Check if the no results message should appear.
	if (isset($data['view']->total) && (int) $data['view']->total === 0)
	{
		$noResults = $data['view']->filterForm->getFieldAttribute('search', 'noresults', '', 'filter');
		if (!empty($noResults))
		{
			$noResultsText = Text::_($noResults);
		}
	}
}

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) && $data['options']['filtersHidden'] ? $data['options']['filtersHidden'] : $hideActiveFilters,
	'defaultLimit'        => $data['options']['defaultLimit'] ?? Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering',
	'filterButton'        => isset($data['options']['filterButton']) && $data['options']['filterButton'] ? $data['options']['filterButton'] : $showFilterButton,
	'selectorFieldName'   => $selectorFieldName,
	'showSelector'        => $showSelector,
	'showNoResults'       => !empty($noResultsText) ? true : false,
	'noResultsText'       => !empty($noResultsText) ? $noResultsText : '',
	'formSelector'        => !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm',
);

$data['options'] = array_merge($customOptions, $data['options']);

// Add class to hide the active filters if needed.
$filtersActiveClass = $hideActiveFilters ? '' : ' js-stools-container-filters-visible';

// Load search tools
HTMLHelper::_('searchtools.form', $data['options']['formSelector'], $data['options']);
?>

<div class="js-stools" role="search">
	<?php if ($data['options']['showSelector']) : ?>
	<div class="js-stools-container-selector">
		<?php echo JLayoutHelper::render('tabbed.selector', $data, $basePath = JPATH_COMPONENT_ADMINISTRATOR .'/layouts/searchtools'); ?>
	</div>
	<?php endif; ?>
	<div class="js-stools-container-bar">
		<?php echo $this->sublayout('bar', $data, $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools/tabbed'); ?>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container-filters clearfix<?php echo $filtersActiveClass; ?>">
		<?php echo $this->sublayout('list', $data, $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools/tabbed'); ?>
		<?php //echo JLayoutHelper::render('tabbed.list', $data, $basePath = JPATH_COMPONENT_ADMINISTRATOR .'/layouts/searchtools'); ?>
		<?php if ($data['options']['filterButton']) : ?>
		<?php echo $this->sublayout('filters', $data, $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools/tabbed'); ?>
		<?php //echo JLayoutHelper::render('tabbed.filters', $data, $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools'); ?>
	<?php endif; ?>
	</div>
</div>

<?php if ($data['options']['showNoResults']) : ?>
	<?php echo $this->sublayout('noitems', $data); ?>
<?php endif; ?>
