<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman toolbar layout.
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

use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

$data = $displayData;
$layout	= $data['tab'];

// Receive overrideable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

// Options
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');

$fieldset	= $data['view']->filterForm->getFieldset($layout);
$filters = array();

foreach ($fieldset as $fieldName => $field)
{
	if (strpos($fieldName,  'filter_') !== false)
	{
		$filters[$fieldName] = $field;
	}
}
?>

<?php if (!empty($filters['filter_search'])) : ?>
	<?php if ($searchButton) : ?>
		<div class="btn-toolbar">
			<div class="btn-group mr-2">
				<div class="input-group">
					<label for="filter_search" class="sr-only">
						<?php if (isset($filters['filter_search']->label)) : ?>
							<?php echo Text::_($filters['filter_search']->label); ?>
						<?php else : ?>
							<?php echo Text::_('JSEARCH_FILTER'); ?>
						<?php endif; ?>
					</label>
					<?php echo $filters['filter_search']->input; ?>
					<?php if ($filters['filter_search']->description) : ?>
						<div role="tooltip" id="<?php echo $filters['filter_search']->name . '-desc'; ?>">
							<?php echo htmlspecialchars(Text::_($filters['filter_search']->description), ENT_COMPAT, 'UTF-8'); ?>
						</div>
					<?php endif; ?>
					<span class="input-group-append">
						<button type="submit" class="btn btn-primary" aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
							<span class="fa fa-search" aria-hidden="true"></span>
						</button>
					</span>
				</div>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-primary hasTooltip js-stools-btn-filter">
					<?php echo Text::_('JFILTER_OPTIONS'); ?>
					<span class="fa fa-caret-down" aria-hidden="true"></span>
				</button>
				<button type="button" class="btn btn-primary js-stools-btn-clear mr-2">
					<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>
<?php endif;
