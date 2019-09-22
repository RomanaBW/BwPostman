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

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
//HTMLHelper::_('behavior.modal');
HtmlHelper::_('behavior.multiselect');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

//Load tabs behavior for the Tabs
jimport('joomla.html.html.tabs');

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$iconImage = Uri::getInstance()->base() . 'components/com_bwpostman/assets/images/icon-48-newsletters.png';
$modalParams = array();
$modalParams['modalWidth'] = 80;
$modalParams['bodyHeight'] = 70;

Factory::getApplication()->setUserState($this->context . 'tab', 'sent');
?>

<script type="text/javascript">
/* <![CDATA[ */
	function changeTab(tab)
	{
		if (tab !== 'default_unsent')
		{
			document.adminForm.tab.setAttribute('value',tab);
		}
		else
		{
			return false;
		}
	}

	Joomla.submitbutton = function (pressbutton)
	{
		if (pressbutton === 'newsletters.archive')
		{
			ConfirmArchive = confirm("<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_ARCHIVE', true); ?>");
			if (ConfirmArchive === true)
			{
				Joomla.submitform(pressbutton, document.adminForm);
			}
		}
		else
		{
			Joomla.submitform(pressbutton, document.adminForm);
		}
	};
/* ]]> */
</script>

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
					echo LayoutHelper::render(
						'tabbed',
						array('view' => $this, 'tab' => 'sent'),
						$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
					);
					?>

					<div class="form-horizontal">
						<ul class="bwp_tabs">
							<li class="closed">
								<button onclick="return changeTab('unsent');" class="buttonAsLink">
									<?php echo Text::_('COM_BWPOSTMAN_NL_UNSENT'); ?>
								</button>
							</li>
							<li class="open">
								<button onclick="return changeTab('sent');" class="buttonAsLink_open">
									<?php echo Text::_('COM_BWPOSTMAN_NL_SENT'); ?>
								</button>
							</li>
							<?php if ($this->count_queue && $this->permissions['newsletter']['send']) { ?>
								<li class="closed">
									<button onclick="return changeTab('queue');" class="buttonAsLink">
										<?php echo Text::_('COM_BWPOSTMAN_NL_QUEUE'); ?>
									</button>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="clr clearfix"></div>

					<div class="current">
						<table id="main-table" class="table">
							<caption id="captionTable" class="sr-only">
								<?php echo Text::_('COM_BWPOSTMAN_NL_SENT_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
							</caption>
							<thead>
								<tr>
									<th style="width: 1%;" class="text-center">
										<input type="checkbox" name="checkall-toggle" value=""
												title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this, 'ub')" />
									</th>
									<th class="d-none d-md-table-cell" style="width: 7%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_ATTACHMENT', 'a.attachment', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="min-width: 100px;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="min-width: 100px;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_MAILING_DATE', 'a.mailing_date', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="width: 7%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_CAM_NAME', 'campaign_id', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="width: 5%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'Published', 'a.published', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?>
										<br />
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?>
										</th>
									<th class="d-none d-md-table-cell" style="width: 3%;" scope="col">
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
									$linkText = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_text_modal&task=insideModal&nl_id=' . $item->id);
									$titleHtml = Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');
									$titleText = Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');
//									$title = '<img src="' . $iconImage . '" alt="' . Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT') . '" /> ' . Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');

									$frameHtml = "htmlFrameSent" . $item->id;
									$frameText = "textFrameSent" . $item->id;

									?>
									<tr class="row<?php echo $i % 2; ?>">
										<td align="center"><?php echo HTMLHelper::_('grid.id', $i, $item->id, 0, 'cid', 'ub'); ?></td>
										<td>
											<?php if (!empty($item->attachment)) { ?>
												<span class="icon_attachment" title="<?php echo Text::_('COM_BWPOSTMAN_ATTACHMENT'); ?>"></span>
											<?php } ?>
										</td>
										<td nowrap="nowrap">
											<?php
											if ($item->checked_out)
											{
												echo HTMLHelper::_(
													'jgrid.checkedout',
													$i,
													$item->editor,
													$item->checked_out_time,
													'newsletters.',
													BwPostmanHelper::canCheckin('newsletter', $item->checked_out),
													'ub'
												);
											} ?>
											<?php
											if (BwPostmanHelper::canEdit('newsletter', $item) || BwPostmanHelper::canEditState('newsletter', (int) $item->id)) : ?>
												<p>
													<a href="
													<?php echo Route::_(
														'index.php?option=com_bwpostman&view=newsletter&layout=edit_publish&task=newsletter.edit&id='
														. $item->id
													);
													?>">
													<?php echo $this->escape($item->subject); ?>
													</a>
												</p>
											<?php else : ?>
												<p><?php echo $this->escape($item->subject); ?></p>
											<?php endif; ?>
											<div class="bw-btn">
												<span class="hasTip"
														title="<?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');?>
														<?php echo $this->escape($item->subject); ?>">
													<?php
													$modalParams['url'] = $linkHtml;
													$modalParams['title'] = $titleHtml;
													?>

													<button type="button" data-target="#<?php echo $frameHtml; ?>" class="btn btn-info" data-toggle="modal">
														<?php echo Text::_('COM_BWPOSTMAN_HTML_NL');?>
													</button>
													<?php echo HTMLHelper::_('bootstrap.renderModal',$frameHtml, $modalParams); ?>
												</span>

												<span class="hasTip" title="<?php
													echo Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');?>
													<?php echo $this->escape($item->subject); ?>">
													<?php
													$modalParams['url'] = $linkText;
													$modalParams['title'] = $titleText;
													?>

													<button type="button" data-target="#<?php echo $frameText; ?>" class="btn btn-info" data-toggle="modal">
														<?php echo Text::_('COM_BWPOSTMAN_TEXT_NL');?>
													</button>
													<?php echo HTMLHelper::_('bootstrap.renderModal',$frameText, $modalParams); ?>
												</span>
											</div>
										</td>
										<td><?php echo $this->escape($item->description); ?></td>
										<td><?php echo HTMLHelper::date($item->mailing_date, Text::_('BW_DATE_FORMAT_LC5')); ?></td>
										<td><?php echo $item->authors; ?></td>
										<td align="center"><?php echo $item->campaign_id; ?></td>
										<td align="center">
											<?php echo HTMLHelper::_(
												'jgrid.published',
												$item->published,
												$i,
												'newsletters.',
												BwPostmanHelper::canEditState('newsletter', (int) $item->id),
												'ub'
											); ?>
										</td>
										<td align="center">
											<p>
												<?php echo ($item->publish_up != '0000-00-00 00:00:00')
													? HTMLHelper::date($item->publish_up, Text::_('BW_DATE_FORMAT_LC5'))
													: '-'; ?><br />
											</p>
											<p>
												<?php echo ($item->publish_down != '0000-00-00 00:00:00')
													? HTMLHelper::date($item->publish_down, Text::_('BW_DATE_FORMAT_LC5'))
													: '-'; ?>
											</p>
										</td>
										<td align="center"><?php echo $item->id; ?></td>
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
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="tab" value="sent" />
			<input type="hidden" name="layout" value="edit_publish" />
			<input type="hidden" name="tpl" value="sent" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
