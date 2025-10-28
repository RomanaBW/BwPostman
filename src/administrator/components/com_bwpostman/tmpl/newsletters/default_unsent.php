<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters unsent template for backend.
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

use Joomla\CMS\Button\ActionButton;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\Database\DatabaseInterface;

HTMLHelper::_('behavior.multiselect');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

// Configure featured button renderer.
$isTemplateButton = (new ActionButton(['tip_title' => Text::_('COM_BWPOSTMAN_NL_FILTER_IS_TEMPLATE')]))
	->addState(0, 'newsletter.changeIsTemplate', 'unpublish', Text::_('COM_BWPOSTMAN_NL_FILTER_IS_TEMPLATE_SET_TITLE'))
	->addState(1, 'newsletter.changeIsTemplate', 'publish', Text::_('COM_BWPOSTMAN_NL_FILTER_IS_TEMPLATE_UNSET_TITLE'));
?>

<div id="bwp_view_lists">
	<?php
	// Open modalbox if task == startsending --> we will show the sending process in the modalbox
	$jinput	= Factory::getApplication()->input;
	$task	= $jinput->get->get('task');

	if ($task != 'startsending')
	{
		if ($this->queueEntries)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
		}
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
								<a id="tab-unsent" href="javascript:void(0);" onclick="changeTab('unsent');Joomla.submitbutton();" data-layout="unsent" class="nav-link active">
									<?php echo Text::_('COM_BWPOSTMAN_NL_UNSENT'); ?>
								</a>
							</li>
							<li class="nav-item">
								<a id="tab-sent" href="javascript:void(0);" onclick="changeTab('sent');Joomla.submitbutton();" class="nav-link">
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
									<?php echo Text::_('COM_BWPOSTMAN_NL_UNSENT_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
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
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell w-20" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-xxl-table-cell w-7" scope="col">
											<?php echo HTMLHelper::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_NL_LAST_MODIFICATION_DATE',
												'a.modified_time',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-xxl-table-cell w-7" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
										</th>
										<th class="d-none d-lg-table-cell w-10" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_NAME', 'campaign_id', $listDirn, $listOrder); ?>
										</th>
										<th class="w-5" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_NL_IS_TEMPLATE', 'is_template', $listDirn, $listOrder); ?>
										</th>
										<th class="w-1" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if (count($this->items))
									{
										foreach ($this->items as $i => $item) :
											$canEdit = BwPostmanHelper::canEdit('newsletter', $item);
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
													<?php if ($canEdit) : ?>
														<a href="
														<?php
														echo Route::_(
															'index.php?option=com_bwpostman&view=newsletter&layout=edit_basic&task=newsletter.edit&id='
															. $item->id . '&referrer=newsletters'
														);?>">
															<?php echo $this->escape($item->subject); ?>
														</a>
													<?php else : ?>
														<?php echo $this->escape($item->subject); ?>
													<?php endif; ?>
												</td>
											<td class="d-none d-lg-table-cell"><?php echo $this->escape($item->description); ?></td>
											<td class="d-none d-xxl-table-cell">
												<?php
												if (property_exists($item, 'modified_time') && $item->modified_time !== Factory::getContainer()->get(DatabaseInterface::class)->getNullDate() && $item->modified_time !== null)
												{
													echo HTMLHelper::date($item->modified_time, Text::_('BW_DATE_FORMAT_LC5'));
												} ?>
											</td>
											<td class="d-none d-xxl-table-cell"><?php echo $item->authors; ?></td>
											<td class="d-none d-lg-table-cell text-center"><?php echo $item->campaign_id; ?></td>
											<td class="text-center">
												<?php echo $isTemplateButton->render($item->is_template, $i, array('disabled' => !$canEdit, 'id' => $item->id)); ?>
											</td>
											<td class="text-center"><?php echo $item->id; ?></td>
										</tr><?php
										endforeach;
									}
									else
									{
										// if no data ?>
										<tr class="row1">
											<td colspan="9"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
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
			<input type="hidden" id="tab" name="tab" value="unsent" />
			<input type="hidden" name="layout" value="default" />
			<input type="hidden" name="tpl" value="unsent" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<input type="hidden" id="currentTab" value="default_unsent" />
			<input type="hidden" id="archiveText" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_ARCHIVE', true); ?>" />
		</div>
	</form>
</div>
