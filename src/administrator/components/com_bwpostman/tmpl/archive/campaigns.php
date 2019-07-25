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

// Load the tooltip behavior for the notes
JHtml::_('behavior.tooltip');

// Load the modal behavior for the campaign preview
//JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

//Set context and layout state for filters
$this->context	= 'archive.campaigns';
$tab			= JFactory::getApplication()->setUserState($this->context . '.tab', 'campaigns');

/**
 * BwPostman Archived Campaigns Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 */
?>

<script type="text/javascript">
/* <![CDATA[ */
	function confirmUnarchive(unarchive_value)  // Get the selected value from modal box
	{
		document.adminForm.unarchive_nl.value = unarchive_value;
		Joomla.submitbutton('archive.unarchive');
	}

	function confirmDelete(delete_value)  // Get the selected value from modal box
	{
		document.adminForm.remove_nl.value = delete_value;
		Joomla.submitbutton('archive.delete');
	}
/* ]]> */
</script>

<div id="bwp_view_lists">
	<form action="<?php echo JRoute::_($this->request_url); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
						// Search tools bar
						echo JLayoutHelper::render(
							'default',
							array('view' => $this, 'tab' => $tab),
							$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
						);
					?>

					<div class="form-horizontal">
						<ul class="bwp_tabs">
							<?php
							if ($this->permissions['newsletter']['archive'] && BwPostmanHelper::canArchive('newsletter', 1, 0))
							{
								?>
								<li class="closed"><!-- We need to use the setAttribute-function because of the IE -->
									<button onclick="layout.setAttribute('value','newsletters');this.form.submit();" class="buttonAsLink">
										<?php echo JText::_('COM_BWPOSTMAN_ARC_NLS'); ?>
									</button>
								</li>
								<?php
							}

							if ($this->permissions['subscriber']['archive'] && BwPostmanHelper::canArchive('subscriber', 1, 0))
							{
								?>
								<li class="closed">
									<button onclick="layout.setAttribute('value','subscribers');this.form.submit();" class="buttonAsLink">
										<?php echo JText::_('COM_BWPOSTMAN_ARC_SUBS'); ?></button>
								</li>
								<?php
							}

							if ($this->permissions['campaign']['archive'] && BwPostmanHelper::canArchive('campaign', 1, 0))
							{
								?>
								<li class="open">
									<button onclick="layout.setAttribute('value','campaigns');this.form.submit();"
											class="buttonAsLink_open">
										<?php echo JText::_('COM_BWPOSTMAN_ARC_CAMS'); ?>
									</button>
								</li>
								<?php
							}

							if ($this->permissions['mailinglist']['archive'] && BwPostmanHelper::canArchive('mailinglist', 1, 0))
							{
								?>
								<li class="closed">
									<button onclick="layout.setAttribute('value','mailinglists');this.form.submit();"
											class="buttonAsLink">
										<?php echo JText::_('COM_BWPOSTMAN_ARC_MLS'); ?>
									</button>
								</li>
								<?php
							}

							if ($this->permissions['template']['archive'] && BwPostmanHelper::canArchive('template', 1, 0))
							{
								?>
								<li class="closed">
									<button onclick="layout.setAttribute('value','templates');this.form.submit();" class="buttonAsLink">
										<?php echo JText::_('COM_BWPOSTMAN_ARC_TPLS'); ?>
									</button>
								</li>
								<?php
							}
							?>
						</ul>

						<div class="current">
							<table id="main-table" class="table">
								<thead>
									<tr>
										<th style="width: 1%;" class="text-center">
											<input type="checkbox" name="checkall-toggle" value=""
													title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
													onclick="Joomla.checkAll(this)" />
										</th>
										<th class="d-none d-md-table-cell" style="min-width: 150px;" scope="col">
											<?php echo JHtml::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_CAM_TITLE',
												'a.title',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-md-table-cell" style="min-width: 150px;" scope="col">
											<?php echo JHtml::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_CAM_DESCRIPTION',
												'a.description',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
											<?php echo JHtml::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_CAM_NL_NUM',
												'newsletters',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
											<?php echo JHtml::_(
												'searchtools.sort',
												'COM_BWPOSTMAN_ARC_ARCHIVE_DATE',
												'a.archive_date',
												$listDirn,
												$listOrder
											); ?>
										</th>
										<th class="d-none d-md-table-cell" style="width: 3%;" scope="col">
											<?php echo JHtml::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
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
										$link = 'index.php?option=com_bwpostman&view=archive&format=raw&layout=campaign_modal&cam_id=';
										$link .= $item->id;
										?>
										<tr class="row<?php echo $i % 2; ?>">
											<td align="center">
												<?php echo JHtml::_('grid.id', $i, $item->id); ?>
											</td>
											<td>
												<?php echo $item->title;?>
											</td>
											<td><?php echo $item->description; ?>
											</td>
											<td align="center">
												<?php echo $item->newsletters; ?>
											</td>
											<td align="center">
												<?php echo JHtml::date($item->archive_date, JText::_('BW_DATE_FORMAT_LC5')); ?>
											</td>
											<td align="center">
												<?php echo $item->id; ?>
											</td>
										</tr>
									<?php endforeach;
								}
								else
								{ ?>
									<tr class="row1">
										<td colspan="6"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
									</tr><?php
								}
								?>
								</tbody>
							</table>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="unarchive_nl" value="0" />
						<input type="hidden" name="remove_nl" value="0" />
						<input type="hidden" name="layout" value="campaigns" /><!-- value can change if one clicks on another tab -->
						<input type="hidden" name="tab" value="campaigns" /><!-- value never changes -->
						<?php echo JHtml::_('form.token'); ?>
					</div>
					<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
				</div>
			</div>
		</div>
	</form>
</div>
