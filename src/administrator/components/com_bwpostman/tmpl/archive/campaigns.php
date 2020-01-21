<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive campaigns template for backend.
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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Load the bootstrap tooltip for the notes
HTMLHelper::_('bootstrap.tooltip');

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

//Set context and layout state for filters
$this->context	= 'archive.campaigns';
$tab			= Factory::getApplication()->setUserState($this->context . '.tab', 'campaigns');

$modalParams = array();
$modalParams['modalWidth'] = 80;
$modalParams['bodyHeight'] = 70;

//
/**
 * BwPostman Archived Campaigns Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 */
?>

<div id="bwp_view_lists">
	<form action="<?php echo Route::_($this->request_url); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
					// Search tools bar
					echo LayoutHelper::render(
						'tabbed',
						array('view' => $this, 'tab' => $tab),
						$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
					);
					?>

					<div class="bwp-archive">
						<ul class="nav nav-tabs bwp-tabs">
							<?php
							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('newsletter', 1, 0))
							{
							?>
								<li class="nav-item"><!-- We need to use the setAttribute-function because of the IE -->
									<a href="#" data-layout="newsletters" class="nav-link">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_NLS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('subscriber', 1, 0))
							{
							?>
								<li class="nav-item">
									<a href="#" data-layout="subscribers" class="nav-link">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_SUBS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('campaign', 1, 0))
							{
							?>
								<li class="nav-item">
									<a href="#" data-layout="campaigns" class="nav-link active">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_CAMS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('mailinglist', 1, 0))
							{
							?>
								<li class="nav-item">
									<a href="#" data-layout="mailinglists" class="nav-link">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_MLS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('template', 1, 0))
							{
							?>
								<li class="nav-item">
									<a href="#" data-layout="templates" class="nav-link">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_TPLS'); ?>
									</a>
								</li>
								<?php
							}
							?>
						</ul>

						<div class="bwp-table">
							<table id="main-table" class="table">
								<caption id="captionTable" class="sr-only">
									<?php echo Text::_('COM_BWPOSTMAN_ARC_CAMS'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
									<tr>
										<th style="width: 1%;" class="text-center">
											<input type="checkbox" name="checkall-toggle" value=""
													title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
													onclick="Joomla.checkAll(this)" />
										</th>
										<th style="min-width: 150px;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_CAM_TITLE',
												'a.title',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th style="min-width: 150px;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_CAM_DESCRIPTION',
												'a.description',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-lg-table-cell" style="width: 10%;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_CAM_NL_NUM',
												'newsletters',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-lg-table-cell" style="width: 10%;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_ARCHIVE_DATE',
												'a.archive_date',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th style="width: 3%;" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
										</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
									</tr>
								</tfoot>
								<tbody>
								<?php
								if (count($this->items) > 0)
								{
									foreach ($this->items as $i => $item) :
										$linkCam = Route::_('index.php?option=com_bwpostman&view=archive&format=raw&layout=campaign_modal&cam_id=' . $item->id);
										$frameCam = "FrameCam" . $item->id;
										?>
										<tr class="row<?php echo $i % 2; ?>">
											<td class="text-center">
												<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
											</td>
											<td>
												<span class="hasTooltip"
														title="<?php echo JText::_('COM_BWPOSTMAN_ARC_SHOW_CAM');?>::
														<?php echo '<br />'.$this->escape($item->title); ?>">
													<?php
													$modalParams['url'] = $linkCam;
													$modalParams['title'] = Text::_('COM_BWPOSTMAN_ARC_SHOW_CAM');
													?>
													<button type="button" data-target="#<?php echo $frameCam; ?>" class="btn btn-outline-info btn-sm" data-toggle="modal">
														<?php echo $item->title;?>
													</button>
												</span>
												<?php echo HTMLHelper::_('bootstrap.renderModal',$frameCam, $modalParams); ?>
											</td>
											<td><?php echo $item->description; ?>
											</td>
											<td class="d-none d-lg-table-cell text-center">
												<?php echo $item->newsletters; ?>
											</td>
											<td class="d-none d-lg-table-cell text-center">
												<?php echo HTMLHelper::date($item->archive_date, Text::_('BW_DATE_FORMAT_LC5')); ?>
											</td>
											<td class="text-center">
												<?php echo $item->id; ?>
											</td>
										</tr>
									<?php endforeach;
								}
								else
								{ ?>
									<tr class="row1">
										<td colspan="6"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
									</tr><?php
								}
								?>
								</tbody>
							</table>
						</div>
					</div>

					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="unarchive_nl" value="0" />
					<input type="hidden" name="remove_nl" value="0" />
					<input type="hidden" id="layout"  name="layout" value="campaigns" /><!-- value can change if one clicks on another tab -->
					<input type="hidden" name="tab" value="campaigns" /><!-- value never changes -->
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
				<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
			</div>
		</div>
	</form>
</div>
<?php
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_tabshelper.js');
?>
