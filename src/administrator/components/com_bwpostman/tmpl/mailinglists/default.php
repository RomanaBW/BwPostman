<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists default template for backend.
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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

HTMLHelper::_('behavior.multiselect');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<div id="bwp_view_lists">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=mailinglists'); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
						// Search tools bar
						echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
					?>

					<table id="main-table" class="table">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_BWPOSTMAN_ML_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<th class="text-center w-1">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</th>
								<th scope="col" class="w-20">
									<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_ML_TITLE', 'a.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-lg-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_ML_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="w-10">
									<?php echo HTMLHelper::_('searchtools.sort',  'PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-lg-table-cell w-10">
									<?php echo HTMLHelper::_('searchtools.sort',  'ACCESS_LEVEL', 'a.access', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-lg-table-cell w-10">
									<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_ML_SUB_NUM', 'subscribers', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-lg-table-cell w-1" aria-sort="ascending">
									<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (count($this->items) > 0) {
								foreach ($this->items as $i => $item) :
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
									<td>
										<?php if ($item->checked_out) : ?>
											<?php echo HTMLHelper::_(
												'jgrid.checkedout',
												$i,
												$item->editor,
												$item->checked_out_time,
												'mailinglists.',
												BwPostmanHelper::canCheckin('mailinglist', $item->checked_out)
											); ?>
										<?php endif; ?>
										<?php if (BwPostmanHelper::canEdit('mailinglist', $item)) : ?>
											<a href="<?php echo Route::_('index.php?option=com_bwpostman&task=mailinglist.edit&id=' . $item->id);?>">
												<?php echo $this->escape($item->title); ?>
											</a>
										<?php else : ?>
											<?php echo $this->escape($item->title); ?>
										<?php endif; ?>
									</td>
									<td class="d-none d-lg-table-cell"><?php echo $item->description; ?></td>
									<td class="text-center">
										<?php echo HTMLHelper::_(
											'jgrid.published',
											$item->published,
											$i,
											'mailinglists.',
											BwPostmanHelper::canEditState('mailinglist', (int) $item->id),
											'cb'
										); ?>
									</td>
									<td class="d-none d-lg-table-cell"><?php echo $this->escape($item->access_level); ?></td>
									<td class="d-none d-lg-table-cell text-center"><?php echo $item->subscribers; ?></td>
									<td class="d-none d-lg-table-cell text-center"><?php echo $item->id; ?></td>
								</tr>
								<?php endforeach;
							}
							else { ?>
								<tr class="row1">
									<td colspan="7"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
								</tr><?php
							}
							?>
						</tbody>
					</table>
				</div>

				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo HTMLHelper::_('form.token'); ?>

				<input type="hidden" id="alertArchive" value="<?php echo Text::_('COM_BWPOSTMAN_ML_CONFIRM_ARCHIVE', true); ?>" />
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
		</div>
	</form>
</div>
