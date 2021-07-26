<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive subscribers template for backend.
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
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

// Load the bootstrap tooltip for the notes
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

//Set context and layout state for filters
$this->context	= 'Archive.subscribers';
$tab			= Factory::getApplication()->setUserState($this->context . '.tab', 'subscribers');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->registerAndUseScript('com_bwpostman.admin-bwpm_tabshelper.js', 'com_bwpostman/admin-bwpm_tabshelper.js');

//
/**
 * BwPostman Archived Subscribers Layout
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
							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('newsletter', 1))
							{
							?>
								<li class="nav-item"><!-- We need to use the setAttribute-function because of the IE -->
									<a href="javascript:void(0);" data-layout="newsletters" class="nav-link bwpm-arc-tab">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_NLS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('subscriber', 1))
							{
							?>
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="subscribers" class="nav-link active bwpm-arc-tab">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_SUBS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('campaign', 1))
							{
							?>
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="campaigns" class="nav-link bwpm-arc-tab">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_CAMS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('mailinglist', 1))
							{
							?>
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="mailinglists" class="nav-link bwpm-arc-tab">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_MLS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('template', 1))
							{
							?>
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="templates" class="nav-link bwpm-arc-tab">
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
									<?php echo Text::_('COM_BWPOSTMAN_ARC_SUBS'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
									<tr>
										<th class="text-center" style="width: 1%;">
											<input type="checkbox" name="checkall-toggle" value=""
													title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
										</th>
										<th style="min-width: 100px;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_SUB_NAME',
												'a.name',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th style="min-width: 80px;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_SUB_FIRSTNAME',
												'a.firstname',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th style="min-width: 150px;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_EMAIL',
												'a.email',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell" style="width: 5%;" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'JSTATUS', 'a.status', $listDirn, $listOrder); ?></th>
										<th class="d-none d-xl-table-cell" style="width: 7%;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_EMAILFORMAT',
												'a.emailformat',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell" style="width: 7%;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_SUB_ML_NUM',
												'mailinglists',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell" style="width: 10%;" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_ARCHIVE_DATE',
												'a.archive_date',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell" style="width: 3%;" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
										</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									if (count($this->items) > 0) {
										foreach ($this->items as $i => $item) :
											$linkSub = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=print&format=raw&task=insideModal&id=' . $item->id);
											$titleSub = Text::_('COM_BWPOSTMAN_SUB_DATA_TITLE');
											?>
											<tr class="row<?php echo $i % 2; ?>">
												<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
												<td>
													<a class="iframe btn btn-outline-info btn-sm hasTooltip text-decoration-none" href="javascript:void(0);"
															aria-describedby="tip-sub-<?php echo $i; ?>"
															data-title="<?php echo $titleSub;?>" data-bs-title="<?php echo $titleSub;?>" data-bs-frame="myIframeSub" data-bs-src="<?php echo $linkSub;?>" data-bs-toggle="modal" data-bs-target="#bwp-modal">
														<?php
														$itemName = Text::_('COM_BWPOSTMAN_SUB_NONAME');

														if ($item->name)
														{
															$itemName = $item->name;
														}
														echo $itemName;
														?>
														<?php echo HTMLHelper::_('bootstrap.renderModal','modal');?>
													</a>
													<div role="tooltip" id="tip-sub-<?php echo $i; ?>">
														<?php echo Text::_('COM_BWPOSTMAN_ARC_SHOW_SUB') . '<br />' . $this->escape($item->firstname) . '&nbsp;' . $this->escape($item->name) . '<br />' . $this->escape($item->email); ?>
													</div>
												</td>
												<td><?php echo $item->firstname; ?></td>
												<td><?php echo $item->email; ?></td>
												<td class="d-none d-lg-table-cell text-center"><?php
													switch ($item->status) {
														case "0": echo Text::_('COM_BWPOSTMAN_ARC_SUB_UNCONFIRMED');
														break;
														case "1": echo Text::_('COM_BWPOSTMAN_ARC_SUB_CONFIRMED');
														break;
														case "9": echo Text::_('COM_BWPOSTMAN_ARC_SUB_TEST');
														break;
													} ?>
												</td>
												<td class="d-none d-lg-table-cell text-center"><?php echo $item->emailformat; ?></td>
												<td class="d-none d-lg-table-cell text-center"><?php echo $item->mailinglists; ?></td>
												<td class="d-none d-lg-table-cell text-center">
													<?php echo HTMLHelper::date($item->archive_date, Text::_('BW_DATE_FORMAT_LC5')); ?>
												</td>
												<td class="d-none d-lg-table-cell text-center"><?php echo $item->id; ?></td>
											</tr>
										<?php endforeach;
									}
									else { ?>
										<tr class="row1">
											<td colspan="9"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
										</tr><?php
									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" id="layout" name="layout" value="subscribers" /><!-- value can change if one clicks on another tab -->
					<input type="hidden" name="tab" value="subscribers" /><!-- value never changes -->
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
				<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
			</div>
		</div>
	</form>
</div>
<!-- Modal -->
<div id="bwp-modal" class="modal fade" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">&nbsp;</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JTOOLBAR_CLOSE'); ?>"></button>
			</div>
			<div class="modal-body p-3">
				<iframe class="modal-frame" width="100%"></iframe>
			</div>
			<div class="modal-footer">
				<button class="btn btn-dark btn-sm" data-bs-dismiss="modal" type="button" title="<?php echo Text::_('JTOOLBAR_CLOSE'); ?>"><?php echo Text::_('JTOOLBAR_CLOSE'); ?></button>
			</div>
		</div>
	</div>
</div>
