<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all templates default template for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

HTMLHelper::_('behavior.multiselect');
?>

<div id="bwp_view_lists">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=templates'); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-12">
				<div id="j-main-container" class="j-main-container table-responsive">
					<?php
					// Search tools bar
					echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
					?>

				<table id="main-table" class="table">
					<caption id="captionTable" class="sr-only">
						<?php echo Text::_('COM_BWPOSTMAN_TPL_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
					</caption>
					<thead>
						<tr>
							<th class="text-center w-1">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</th>
							<th class="w-20" scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							</th>
							<th class="d-none d-lg-table-cell w-10" scope="col">
								<?php echo Text::_('COM_BWPOSTMAN_TPL_THUMBNAIL'); ?>
							</th>
							<th class="w-5" scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_FORMAT', 'a.tpl_id', $listDirn, $listOrder); ?>
								</th>
							<th class="w-5" scope="col">
								<?php echo Text::_('COM_BWPOSTMAN_TPL_SET_DEFAULT'); ?>
							</th>
							<th class="d-none d-lg-table-cell w-5" scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th class="d-none d-lg-table-cell w-20" scope="col">
								<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
							</th>
							<th class="w-1" scope="col" aria-sort="ascending">
								<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (count($this->items) > 0)
						{
							foreach ($this->items as $i => $item) :
								$canEdit = BwPostmanHelper::canEdit('template', $item);
								$canEditState = BwPostmanHelper::canEditState('template', $item->id);
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
									<td>
									<?php
									if ($item->checked_out)
									{
										echo HTMLHelper::_(
											'jgrid.checkedout',
											$i,
											$item->editor,
											$item->checked_out_time,
											'templates.',
											BwPostmanHelper::canCheckin('template', $item->checked_out)
										);
									}

									if ($canEdit)
									{ ?>
										<a href="<?php echo Route::_('index.php?option=com_bwpostman&task=template.edit&id=' . $item->id); ?>">
											<?php echo $this->escape($item->title); ?>
										</a><?php
									}
									else
									{
										echo $this->escape($item->title);
									} ?>
									</td>
									<td class="d-none d-lg-table-cell text-center">
										<?php
										if ($item->thumbnail)
											{
											if ($canEdit)
											{ ?>
												<a href="<?php
												echo Route::_('index.php?option=com_bwpostman&task=template.edit&id=' . $item->id);
												?>">
												<img src="<?php echo Uri::root(true) . '/' . $item->thumbnail; ?>" style="width: 100px;" alt="Template Thumbnail" />
												</a><?php
											}
											else
											{ ?>
												<img src="<?php echo Uri::root(true) . '/' . $item->thumbnail; ?>" style="width: 100px;" alt="Template Thumbnail" /><?php
											}
										} ?>
									</td>
									<td class="text-center">
										<?php
										if (($item->tpl_id == 998) || ($item->tpl_id > 999))
										{
											echo 'TEXT';
										}
										else
										{
											echo 'HTML';
										}?>
									</td>
									<td class="text-center">
										<?php echo HTMLHelper::_(
											'jgrid.isdefault',
											($item->standard != '0' && !empty($item->standard)),
											$i,
											'template.',
											$canEditState && $item->standard != '1'
										);?>
									</td>
									<td class="d-none d-lg-table-cell text-center">
										<?php echo HTMLHelper::_(
											'jgrid.published',
											$item->published,
											$i,
											'templates.',
											$canEditState,
											'cb'
										); ?>
									<td class="d-none d-lg-table-cell text-center">
									<?php echo nl2br($item->description); ?>
								</td>
									<td><?php echo $item->id; ?></td>
								</tr><?php
							endforeach;
						}
						else { ?>
							<tr class="row1">
								<td colspan="8"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong>
								</td>
							</tr><?php
						}
						?>
					</tbody>
				</table>
			</div>

			<div class="pagination">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
			</div>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<input type="hidden" id="archiveText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_CONFIRM_ARCHIVE', true); ?>" />
		</div>
	</form>
</div>
