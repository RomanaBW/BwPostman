<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers confirmed template for backend.
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

HTMLHelper::_('behavior.multiselect');

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$colNum = 8;

?>

<div id="bwp_view_lists">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=subscribers'); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
					// Search tools bar
					echo LayoutHelper::render(
						'tabbed',
						array('view' => $this, 'tab' => 'confirmed'),
						$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
					);
					?>

					<div class="bwp-subscribers">
						<ul class="nav nav-tabs bwp-tabs">
							<li class="nav-item"><!-- We need to use the setAttribute-function because of the IE -->
								<a id="tab-confirmed" href="#" onclick="changeTab('confirmed');Joomla.submitbutton();" class="nav-link active">
									<?php echo Text::_('COM_BWPOSTMAN_SUB_CONFIRMED'); ?>
								</a>
							</li>
							<li class="nav-item">
								<a id="tab-unconfirmed" href="#" onclick="changeTab('unconfirmed');Joomla.submitbutton();" class="nav-link">
									<?php echo Text::_('COM_BWPOSTMAN_SUB_UNCONFIRMED'); ?>
								</a>
							</li>
							<li class="nav-item">
								<a id="tab-testrecipients" href="#" onclick="changeTab('testrecipients');Joomla.submitbutton();" class="nav-link">
									<?php echo Text::_('COM_BWPOSTMAN_TEST'); ?>
								</a>
							</li>
						</ul>

						<div class="bwp-table">
							<table id="main-table-bw-confirmed" class="table bw-confirmed">
								<caption id="captionTable" class="sr-only">
									<?php echo Text::_('COM_BWPOSTMAN_SUBS_CONFIRMED_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
								</caption>
								<thead>
								<tr>
									<th class="text-center w-1">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</th>
									<th class="w-10" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_NAME', 'a.name', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-lg-table-cell w-10" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?>
									</th>
									<?php
									if($this->params->get('show_gender', '1'))
									{ ?>
										<th class="d-none d-lg-table-cell w-5" scope="col">
											<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_GENDER', 'a.gender', $listDirn, $listOrder); ?>
										</th>
										<?php
									} ?>
									<th scope="col">
										<?php echo HTMLHelper::_('searchtools.sort', 'COM_BWPOSTMAN_EMAIL', 'a.email', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-lg-table-cell w-5" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_EMAILFORMAT', 'a.emailformat', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-lg-table-cell w-5" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_JOOMLA_USERID', 'a.user_id', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-lg-table-cell w-5" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_ML_NUM', 'mailinglists', $listDirn, $listOrder); ?>
									</th>
									<th class="d-none d-lg-table-cell w-1" scope="col">
										<?php echo HTMLHelper::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
									</th>
								</tr>
								</thead>
								<tbody>
								<?php
								if (count($this->items))
								{
									foreach ($this->items as $i => $item) :
										$ordering	= ($listOrder == 'a.ordering');
										$name		= ($item->name) ?: Text::_('COM_BWPOSTMAN_SUB_NONAME');
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
													'subscribers.',
													BwPostmanHelper::canCheckin('subscriber', $item->checked_out),
													'cb'
												);
											}
											if (BwPostmanHelper::canEdit('subscriber', $item))
											{ ?>
												<a href="<?php echo Route::_('index.php?option=com_bwpostman&task=subscriber.edit&id=' . $item->id); ?>">
													<?php echo $this->escape($name); ?>
												</a>
												<?php
											}
											else
											{
												echo $this->escape($name);
											} ?>
										</td>
										<td class="d-none d-lg-table-cell"><?php echo $item->firstname; ?></td>
										<?php if($this->params->get('show_gender', '1'))
										{
											$colNum = 9;
											?>
											<td>
												<?php
												if ($item->gender === 1)
												{
													echo Text::_('COM_BWPOSTMAN_FEMALE');
												}
												elseif ($item->gender === 0)
												{
													echo Text::_('COM_BWPOSTMAN_MALE');
												}
												else
												{
													echo Text::_('COM_BWPOSTMAN_NO_GENDER');
												}
												?>
											</td>
										<?php } ?>
										<td><?php echo $item->email; ?></td>
										<td class="d-none d-lg-table-cell text-center"><?php echo ($item->emailformat) ? Text::_('COM_BWPOSTMAN_HTML') : Text::_('COM_BWPOSTMAN_TEXT')?></td>
										<td class="d-none d-lg-table-cell text-center"><?php echo ($item->user_id) ?: ''; ?></td>
										<td class="d-none d-lg-table-cell text-center"><?php echo $item->mailinglists; ?></td>
										<td class="d-none d-lg-table-cell text-center"><?php echo $item->id; ?></td>
										</tr><?php
									endforeach;
								}
								else
								{
									// if no data ?>
									<tr class="row1">
									<td colspan="<?php echo $colNum; ?>"><strong><?php echo Text::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
									</tr><?php
								}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
					<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

					<?php // Load the batch processing form. ?>
					<?php echo HTMLHelper::_(
						'bootstrap.renderModal',
						'collapseModal',
						array(
							'title'  => Text::_('COM_BWPOSTMAN_SUB_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer'),
						),
						$this->loadTemplate('batch_body')
					); ?>

					<input type="hidden" name="task" value="" />
					<input type="hidden" id="tab" name="tab" value="confirmed" />
					<!--				<input type="hidden" name="layout" value="confirmed" />-->
					<input type="hidden" name="tpl" value="confirmed" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" id="mlToExport" name="mlToExport" value="" />
					<?php echo HTMLHelper::_('form.token'); ?>

					<input type="hidden" id="currentTab" value="default_confirmed" />
					<input type="hidden" id="archiveText" value="<?php echo Text::_('COM_BWPOSTMAN_SUB_CONFIRM_ARCHIVE', true); ?>" />
					<input type="hidden" id="exportMl" value="<?php echo $this->filterMl; ?>" />
				</div>
			</div>
		</div>
	</form>
</div>

