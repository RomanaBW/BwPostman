<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all campaigns default template for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('formbehavior.chosen', 'select');
HtmlHelper::_('behavior.multiselect');

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));


/**
 * BwPostman Campaigns Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */
?>

<div id="bwp_view_lists">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=campaigns'); ?>"
			method="post" name="adminForm" id="adminForm" class="form-inline">
		<?php if (property_exists($this, 'sidebar')) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else :  ?>
			<div id="j-main-container">
		<?php endif; ?>
			<?php
				// Search tools bar
				echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>

			<div class="row-fluid">
				<table id="main-table" class="adminlist table table-striped">
					<thead>
						<tr>
							<th width="30" nowrap="nowrap">
								<input type="checkbox" name="checkall-toggle" value=""
										title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th nowrap="nowrap">
								<?php echo HtmlHelper::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
							<th nowrap="nowrap">
								<?php echo HtmlHelper::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo HtmlHelper::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_NL_NUM', 'newsletters', $listDirn, $listOrder); ?>
							</th>
							<th width="30" nowrap="nowrap"><?php echo HtmlHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (count($this->items) > 0)
						{
							foreach ($this->items as $i => $item)
							{
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<td align="center"><?php echo HtmlHelper::_('grid.id', $i, $item->id); ?></td>
									<td>
									<?php
									if ($item->checked_out)
									{ ?>
										<?php
										echo HtmlHelper::_(
											'jgrid.checkedout',
											$i,
											$item->editor,
											$item->checked_out_time,
											'campaigns.',
											BwPostmanHelper::canCheckin('campaign', $item->checked_out)
										);
									} ?>
									<?php
									if (BwPostmanHelper::canEdit('campaign', $item))
									{ ?>
										<a href="<?php echo Route::_('index.php?option=com_bwpostman&task=campaign.edit&id=' . $item->id); ?>">
											<?php echo $this->escape($item->title); ?>
										</a> <?php
									}
									else
									{
										echo $this->escape($item->title);
									} ?>
									</td>
									<td><?php echo $item->description; ?></td>
									<td align="center"><?php echo $item->newsletters; ?></td>
									<td align="center"><?php echo $item->id; ?></td>
								</tr>
							<?php
							}
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
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="archive_nl" value="0" />
			<?php echo HtmlHelper::_('form.token'); ?>

		</div>
	</form>
</div>
