<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive newsletters template for backend.
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
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\Database\DatabaseInterface;

// Load the bootstrap tooltip for the notes
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.modal');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$nullDate   = Factory::getContainer()->get(DatabaseInterface::class)->getNullDate();

//Set context and layout state for filters
$this->context	= 'Archive.newsletters';
$tab			= Factory::getApplication()->setUserState($this->context . '.tab', 'newsletters');

$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_tabshelper');

//
/**
 * BwPostman Archived Newsletters Layout
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
					echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
					?>

					<div class="bwp-archive">
						<ul class="nav nav-tabs bwp-tabs">
							<?php
							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('newsletter', 1))
							{
							?>
								<!-- We need to use the setAttribute-function because of the IE -->
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="newsletters" class="nav-link active bwpm-arc-tab">
										<?php echo Text::_('COM_BWPOSTMAN_ARC_NLS'); ?>
									</a>
								</li>
								<?php
							}

							if ($this->permissions['view']['archive'] && BwPostmanHelper::canArchive('subscriber', 1))
							{
							?>
								<li class="nav-item">
									<a href="javascript:void(0);" data-layout="subscribers" class="nav-link bwpm-arc-tab">
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
									<?php echo Text::_('COM_BWPOSTMAN_ARC_NLS'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
									<tr>
										<th class="text-center w-1">
											<?php echo HTMLHelper::_('grid.checkall'); ?>
										</th>
										<th scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'Subject', 'a.subject', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_NL_DESCRIPTION',
												'a.description',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell w10" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_NL_MAILING_DATE',
												'a.mailing_date',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'Author', 'author', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell" nowrap="nowrap">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_CAM_NAME',
												'campaigns',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-lg-table-cell w-5" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_PUBLISHED',
												'a.published',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-lg-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_NL_PUBLISH_UP',
												'a.publish_up',
												$listDirn,
												$listOrder
											); ?>
											<br />
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_NL_PUBLISH_DOWN',
												'a.publish_down',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xl-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_ARCHIVE_DATE',
												'a.archive_date',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="w-1" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
										</th>
									</tr>
								</thead>
								<tbody>
								<?php
								if (count($this->items) > 0) {
									foreach ($this->items as $i => $item) :
										$linkHtml = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_html_modal&task=insideModal&nl_id=' . $item->id);
										$titleHtml = Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');

										$linkText = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_text_modal&task=insideModal&nl_id=' . $item->id);
										$titleText = Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');
										?>
										<tr class="row<?php echo $i % 2; ?>">
											<td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
											<td>
												<?php
												echo $item->subject;
												if ($item->mailing_date != Factory::getContainer()->get(DatabaseInterface::class)->getNullDate() && $item->mailing_date != null)
												{ ?>&nbsp;&nbsp;
													<div class="bw-btn">
														<div class="d-inline-block" aria-describedby="tip-html-<?php echo $i; ?>">
															<a class="iframe btn btn-info btn-sm text-decoration-none mt-1" href="javascript:void(0);"
																	data-title="<?php echo $titleHtml;?>" data-bs-title="<?php echo $titleHtml;?>" data-bs-frame="myIframeHtml" data-bs-src="<?php echo $linkHtml;?>" data-bs-toggle="modal" data-bs-target="#bwp-modal">
																<?php echo Text::_('COM_BWPOSTMAN_HTML_NL');?>
															</a>
														</div>
														<div role="tooltip" id="tip-html-<?php echo $i; ?>">
															<?php echo $titleHtml . '<br />' . $this->escape($item->subject); ?>
														</div>
														<div class="d-inline-block" aria-describedby="tip-text-<?php echo $i; ?>">
															<a class="iframe btn btn-info btn-sm text-decoration-none mt-1" href="javascript:void(0);"
																	data-title="<?php echo $titleText;?>" data-bs-title="<?php echo $titleText;?>" data-bs-frame="myIframeText" data-bs-src="<?php echo $linkText;?>" data-bs-toggle="modal" data-bs-target="#bwp-modal">
																<?php echo Text::_('COM_BWPOSTMAN_TEXT_NL');?>
															</a>
														</div>
														<div role="tooltip" id="tip-text-<?php echo $i; ?>">
															<?php echo $titleText . '<br />' . $this->escape($item->subject); ?>
														</div>
													</div>
												<?php } ?>
											</td>
											<td class="d-none d-lg-table-cell text-center"><?php echo $item->description; ?></td>
											<td class="d-none d-xl-table-cell text-center">
												<?php
												if ($item->mailing_date !== $nullDate && $item->mailing_date !== null)
												{
													echo HTMLHelper::date($item->mailing_date, Text::_('BW_DATE_FORMAT_LC5'));
												}
												?>&nbsp;
											</td>
											<td class="d-none d-xl-table-cell text-center">
												<?php echo $item->author; ?></td>
											<td class="d-none d-lg-table-cell text-center">
												<?php echo $item->campaigns;
												if ($item->campaign_archive_flag)
												{
													echo " (" . Text::_('ARCHIVED') . ")";
												}
												?>
											</td>
											<td class="d-none d-lg-table-cell text-center">
												<?php echo HTMLHelper::_(
													'jgrid.published',
													$item->published,
													$i,
													'newsletters.',
													BwPostmanHelper::canEditState('newsletter', (int) $item->id)
												); ?>
											</td>
											<td class="d-none d-lg-table-cell text-center">
												<p style="text-align: center;">
													<?php echo ($item->publish_up !== $nullDate && $item->publish_up !== null)
														? HTMLHelper::date($item->publish_up, Text::_('BW_DATE_FORMAT_LC5'))
														: '-'; ?>
													<br /></p>
												<p style="text-align: center;">
													<?php echo ($item->publish_down !== $nullDate && $item->publish_down !== null)
														? HTMLHelper::date($item->publish_down, Text::_('BW_DATE_FORMAT_LC5'))
														: '-'; ?>
												</p>
											</td>
											<td class="d-none d-xl-table-cell text-center">
												<?php echo HTMLHelper::date($item->archive_date, Text::_('BW_DATE_FORMAT_LC5')); ?>
											</td>
											<td class="text-center"><?php echo $item->id; ?></td>
										</tr>
									<?php endforeach;
								}
								else { ?>
									<tr class="row1">
										<td colspan="10"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
									</tr><?php
								}
								?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" id="layout" name="layout" value="newsletters" /><!-- value can change if one clicks on another tab -->
					<input type="hidden" name="tab" value="newsletters" /><!-- value never changes -->
					<input type="hidden" name="view" value="archive" />
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
				<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
			</div>
		</div>
	</form>
</div>

<div id="bwp-modal" class="joomla-modal modal fade" role="dialog" tabindex="-1">
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
