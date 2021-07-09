<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element template to select a single newsletter for a view in frontend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');
?>

<form id="adminForm" action="<?php Route::_('index.php?option=com_bwpostman&amp;view=newsletterelement&amp;tmpl=component'); ?>"
		method="post" name="adminForm">
	<div class="js-stools" role="search" tabindex="-1" id="ui-skip-2">
		<div class="js-stools-container-bar">
			<div class="btn-toolbar">
				<div class="lead mr-2"><?php echo Text::_('JSEARCH_FILTER_LABEL'); ?></div>
				<div class="input-group mr-2">
					<input type="text" name="search" title="search" id="search"
							value="<?php echo $this->lists['search']; ?>" class="form-control" onChange="document.adminForm.submit();" />
					<button onclick="this.form.submit();" class="btn btn-primary input-group-append" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"
							aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<span class="fa fa-search mr-2" aria-hidden="true"></span><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
					</button>
				</div>
				<button type="button" class="btn btn-outline-primary" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>"
						aria-label="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').setAttribute('value', '');this.form.submit();">
					<span class="fa icon-unpublish mr-2" aria-hidden="true"></span><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	</div>

	<table class="table table-sm">
		<thead>
			<tr>
				<th scope="col" class="text-center">
					<?php echo HTMLHelper::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th scope="col" class="title">
					<?php echo HTMLHelper::_('grid.sort', 'Subject', 'a.subject', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th scope="col" class="title text-center d-none d-sm-table-cell">
					<?php echo HTMLHelper::_(
						'grid.sort',
						'COM_BWPOSTMAN_NL_MAILING_DATE',
						'a.mailing_date',
						$this->lists['order_Dir'],
						$this->lists['order']
					); ?>
				</th>
				<th scope="col" class="text-center d-none d-md-table-cell">
					<?php echo Text::_('JPUBLISHED'); ?>
				</th>
				<th scope="col" class="text-center d-none d-md-table-cell">
					<?php echo HTMLHelper::_('grid.sort', 'Archived', 'a.archive_flag', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			if (count($this->items) > 0)
			{
				foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="small text-center"><?php echo $item->id; ?></td>
					<td>
						<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SELECT_NEWSLETTER');?>
								<?php echo "<br /><br /><strong>" . $item->subject . ":</strong><br />" . $item->description; ?>">
							<a class="" href="#" style="cursor:pointer;" onclick="window.parent.SelectNewsletter('<?php echo $item->id; ?>', '<?php echo str_replace( array("'", "\""), array("\\'", ""), $item->subject ); ?>');">
								<?php echo htmlspecialchars($item->subject, ENT_QUOTES); ?>
							</a>
						</span>
					</td>
					<td class="small text-center d-none d-sm-table-cell"><?php echo $item->mailing_date; ?></td>
					<td class="small text-center d-none d-md-table-cell text-success"><span class="fa fa-check-circle"></span></td>
					<td class="small text-center d-none d-md-table-cell">
						<?php
						$archived = ($item->archive_flag === 1) ? '<i class="icon-archive"></i>' : '';
						echo $archived; ?>
					</td>
				</tr>
				<?php endforeach;
			}
			else
			{ ?>
				<tr class="row1">
					<td colspan="5"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
				</tr><?php
			}
			?>
		</tbody>
	</table>

	<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
