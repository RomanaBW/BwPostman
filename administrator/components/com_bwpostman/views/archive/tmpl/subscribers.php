<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive subscribers template for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

// Load the modal behavior for the subscriber preview
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
//jimport ('joomla.html.html.bootstrap');
JHTML::_('behavior.modal');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

//Set context and layout state for filters
$this->context	= 'archive.subscribers';
$tab			= JFactory::getApplication()->setUserState($this->context . '.tab', 'subscribers');

/**
 * BwPostman Archived Subscribers Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 */
?>

<div id="bwp_view_lists">
	<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
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
				echo JLayoutHelper::render('default', array('view' => $this, 'tab' => $tab), $basePath = JPATH_ADMINISTRATOR .'/components/com_bwpostman/layouts/searchtools');
			?>

			<div class="row-fluid">
				<table class="adminlist table table-striped">
					<tbody>
						<tr>
							<td valign="top" width="90%">
								<ul class="bwp_tabs">
									<li class="closed"><!-- We need to use the setAttribute-function because of the IE -->
										<button onclick="layout.setAttribute('value','newsletters');this.form.submit();" class="buttonAsLink"><?php echo JText::_('COM_BWPOSTMAN_ARC_NLS'); ?></button>
									</li>
									<li class="open">
										<button onclick="layout.setAttribute('value','subscribers');this.form.submit();" class="buttonAsLink_open"><?php echo JText::_('COM_BWPOSTMAN_ARC_SUBS'); ?></button>
									</li>
									<li class="closed">
										<button onclick="layout.setAttribute('value','campaigns');this.form.submit();" class="buttonAsLink"><?php echo JText::_('COM_BWPOSTMAN_ARC_CAMS'); ?></button>
									</li>
									<li class="closed">
										<button onclick="layout.setAttribute('value','mailinglists');this.form.submit();" class="buttonAsLink"><?php echo JText::_('COM_BWPOSTMAN_ARC_MLS'); ?></button>
									</li>
									<li class="closed">
										<button onclick="layout.setAttribute('value','templates');this.form.submit();" class="buttonAsLink"><?php echo JText::_('COM_BWPOSTMAN_ARC_TPLS'); ?></button>
									</li>
								</ul>

								<div class="current">
									<table class="adminlist">
										<thead>
											<tr>
												<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
												<th width="200"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_NAME', 'a.name', $listDirn, $listOrder); ?></th>
												<th width="150"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?></th>
												<th><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_EMAIL', 'a.email', $listDirn, $listOrder); ?></th>
												<th width="100"><?php echo JHTML::_('searchtools.sort',  'State', 'a.status', $listDirn, $listOrder); ?></th>
												<th width="100"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_EMAILFORMAT', 'a.emailformat', $listDirn, $listOrder); ?></th>
												<th width="100"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_ML_NUM', 'mailinglists', $listDirn, $listOrder); ?></th>
												<th width="120"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_ARC_ARCHIVE_DATE', 'a.archive_date', $listDirn, $listOrder); ?></th>
												<th width="30" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
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
													$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
													$canEdit	= $user->authorise('core.edit',			'com_bwpostman.newsletter.'.$item->id);
													$canChange	= $user->authorise('core.edit.state',	'com_bwpostman.newsletter.'.$item->id) && $canCheckin;
													?>
													<tr class="row<?php echo $i % 2; ?>">
														<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
														<td>
															<span class="editlinktip hasTip" title="<?php echo JText::_('COM_BWPOSTMAN_ARC_SHOW_SUB');?>::<?php echo $this->escape($item->name); ?>">
																<a class="modal" href="<?php echo JRoute::_('index.php?option=com_bwpostman&view=archive&format=raw&layout=subscriber_modal&sub_id='. $item->id); ?>" rel="{handler: 'iframe', size: {x: 600, y: 400}}"> <?php
																if ($item->name) {
																	echo $item->name;
																}
																else {
																	echo JText::_('COM_BWPOSTMAN_SUB_NONAME');
																}
																?> </a>&nbsp;
															</span>
														</td>
														<td><?php echo $item->firstname; ?></td>
														<td><?php echo $item->email; ?></td>
														<td align="center"><?php
															switch ($item->status) {
																case "0": echo JText::_('COM_BWPOSTMAN_ARC_SUB_UNCONFIRMED');
																break;
																case "1": echo JText::_('COM_BWPOSTMAN_ARC_SUB_CONFIRMED');
																break;
																case "9": echo JText::_('COM_BWPOSTMAN_ARC_SUB_TEST');
																break;
															} ?>
														</td>
														<td align="center"><?php echo $item->emailformat; ?></td>
														<td align="center"><?php echo $item->mailinglists; ?></td>
														<td align="center"><?php echo JHtml::date($item->archive_date, JText::_('BW_DATE_FORMAT_LC5')); ?></td>
														<td align="center"><?php echo $item->id; ?></td>
													</tr>
												<?php endforeach;
											}
											else { ?>
												<tr class="row1">
													<td colspan="9"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
												</tr><?php
											}
										?>
										</tbody>
									</table>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="layout" value="subscribers" /><!-- value can change if one clicks on another tab -->
				<input type="hidden" name="tab" value="subscribers" /><!-- value never changes -->
				<?php echo JHTML::_('form.token'); ?>
			</div>

			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
		</div>
	</form>
</div>
