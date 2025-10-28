<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters sent template for backend.
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
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\Database\DatabaseInterface;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HtmlHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.modal');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$nullDate   = Factory::getContainer()->get(DatabaseInterface::class)->getNullDate();

$iconImage = Uri::getInstance()->base() . 'media/com_bwpostman/images/icon-48-newsletters.png';

$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_tabshelper');

Factory::getApplication()->setUserState($this->context . 'tab', 'sent');
?>

<div id="bwp_view_lists">
	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=newsletters'); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
					// Search tools bar
					echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
					?>

					<div class="bwp-newsletters">
						<ul class="nav nav-tabs bwp-tabs">
							<li class="nav-item">
								<a id="tab-unsent" href="javascript:void(0);" onclick="changeTab('unsent');Joomla.submitbutton();" class="nav-link">
									<?php echo Text::_('COM_BWPOSTMAN_NL_UNSENT'); ?>
								</a>
							</li>
							<li class="nav-item">
								<a id="tab-sent" href="javascript:void(0);" onclick="changeTab('sent');Joomla.submitbutton();" class="nav-link active">
									<?php echo Text::_('COM_BWPOSTMAN_NL_SENT'); ?>
								</a>
							</li>
							<?php if ($this->count_queue && $this->permissions['newsletter']['send']) { ?>
								<li class="nav-item">
									<a id="tab-queue" href="javascript:void(0);" onclick="changeTab('queue');Joomla.submitbutton();" class="nav-link">
										<?php echo Text::_('COM_BWPOSTMAN_NL_QUEUE'); ?>
									</a>
								</li>
							<?php } ?>
						</ul>

						<div class="bwp-table">
							<table id="main-table" class="table">
								<caption id="captionTable" class="sr-only">
									<?php echo Text::_('COM_BWPOSTMAN_NL_SENT_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
									<tr>
										<th class="text-center w-1">
											<?php echo HTMLHelper::_('grid.checkall'); ?>
										</th>
										<th class="d-none d-lg-table-cell w-7" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_ATTACHMENT', 'a.attachment', $listDirn, $listOrder); ?>
										</th>
										<th scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell w-20" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-xl-table-cell w-7" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_MAILING_DATE', 'a.mailing_date', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-xxl-table-cell w-7" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_CAM_NAME', 'campaign_id', $listDirn, $listOrder); ?>
										</th>
										<th class="w-5" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'Published', 'a.published', $listDirn, $listOrder); ?>
										</th>
										<th class="w-5" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?>
											<br />
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?>
										</th>
										<th class="w-1" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'NUM', 'a.id', $listDirn, $listOrder); ?>
										</th>
									</tr>
								</thead>
								<tbody>
								<?php
								if (count($this->items))
								{
									foreach ($this->items as $i => $item) :
										$linkHtml = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_html_modal&task=insideModal&nl_id=' . $item->id);
										$titleHtml = Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');

										$linkText = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_text_modal&task=insideModal&nl_id=' . $item->id);
										$titleText = Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');

										$canEditState = BwPostmanHelper::canEditState('newsletter', $item->id);
										?>
										<tr class="row<?php echo $i % 2; ?>">
											<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
											<td class="d-none d-lg-table-cell">
													<?php if (!empty($item->attachment)) { ?>
															<i class="fa fa-paperclip fa-lg"></i>
													<?php } ?>
											</td>
											<td>
												<?php
												if ($item->checked_out)
												{
													echo HTMLHelper::_(
														'jgrid.checkedout',
														$i,
														$item->editor,
														$item->checked_out_time,
														'newsletters.',
														BwPostmanHelper::canCheckin('newsletter', $item->checked_out)
													);
												} ?>
												<?php
												if (BwPostmanHelper::canEdit('newsletter', $item) || $canEditState) : ?>
													<a href="
													<?php echo Route::_(
														'index.php?option=com_bwpostman&view=newsletter&layout=edit_publish&task=newsletter.edit&id='
														. $item->id
													);
													?>">
													<?php echo $this->escape($item->subject); ?>
													</a>
												<?php else : ?>
													<?php echo $this->escape($item->subject); ?>
												<?php endif; ?>
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
											</td>
											<td class="d-none d-lg-table-cell"><?php echo $this->escape($item->description); ?></td>
											<td class="d-none d-xl-table-cell"><?php echo HTMLHelper::date($item->mailing_date, Text::_('BW_DATE_FORMAT_LC5')); ?></td>
											<td class="d-none d-xxl-table-cell"><?php echo $item->authors; ?></td>
											<td class="d-none d-lg-table-cell text-center"><?php echo $item->campaign_id; ?></td>
											<td class="text-center">
												<?php echo HTMLHelper::_(
													'jgrid.published',
													$item->published,
													$i,
													'newsletters.',
													$canEditState
												); ?>
											</td>
											<td class="text-center">
												<p>
													<?php echo ($item->publish_up !== $nullDate && $item->publish_up !== null)
														? HTMLHelper::date($item->publish_up, Text::_('BW_DATE_FORMAT_LC5'))
														: '-'; ?><br />
												</p>
												<p>
													<?php echo ($item->publish_down !== $nullDate && $item->publish_down !== null)
														? HTMLHelper::date($item->publish_down, Text::_('BW_DATE_FORMAT_LC5'))
														: '-'; ?>
												</p>
											</td>
											<td class="text-center"><?php echo $item->id; ?></td>
										</tr><?php
									endforeach;
								}
								else
								{
									// if no data ?>
									<tr class="row1">
										<td colspan="10"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
									</tr><?php
								}
							?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="tab" value="sent" />
			<input type="hidden" name="layout" value="edit_publish" />
			<input type="hidden" name="tpl" value="sent" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<input type="hidden" id="currentTab" value="default_sent" />
			<input type="hidden" id="archiveText" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_ARCHIVE', true); ?>" />
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
