<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all templates default template for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton) {
		if (pressbutton == 'template.archive') {
			ConfirmArchive = confirm("<?php echo JText::_('COM_BWPOSTMAN_TPL_CONFIRM_ARCHIVE' , true); ?>");
			if (ConfirmArchive == true) {
				submitform(pressbutton);
			} else {
				return;
			}
		} else {
			submitform(pressbutton);		
		} 
	}
/* ]]> */	
</script>

<div id="bwp_view_lists">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=templates'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
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
				echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
		
				<div class="row-fluid">
					<table class="adminlist table table-striped">
						<thead>
							<tr>
								<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
								<th width="250" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
								<th class="center" align="center" width="110" nowrap="nowrap"><?php echo JText::_('COM_BWPOSTMAN_TPL_THUMBNAIL'); ?></th>
								<th class="center" align="center" width="100" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_FORMAT', 'a.tpl_id', $listDirn, $listOrder); ?></th>
								<th class="center" align="center" width="60" nowrap="nowrap"><?php echo JText::_('COM_BWPOSTMAN_TPL_SET_DEFAULT'); ?></th>
								<th class="center" align="center" width="100" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'PUBLISHED', 'a.published', $listDirn, $listOrder); ?></th>
								<th nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_TPL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?></th>
								<th width="30" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
							</tr>
						</tfoot>
						<tbody>
						<?php
							if (count($this->items) > 0) { 
								foreach ($this->items as $i => $item) : 
									$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
									$canEdit	= $user->authorise('core.edit',			'com_bwpostman.template.'.$item->id);
									$canEditOwn	= $user->authorise('core.edit.own',		'com_bwpostman.template.'.$item->id) && $item->created_by == $userId;
									$canChange	= $user->authorise('core.edit.state',	'com_bwostman.template.'.$item->id) && $canCheckin;
									?>
									<tr class="row<?php echo $i % 2; ?>">
										<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
										<td>
										<?php if ($item->checked_out) : ?>
											<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'templates.', $canCheckin); ?>
										<?php endif; ?>
										<?php if ($canEdit || $canEditOwn) : ?>
												<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&task=template.edit&id='. $item->id);?>">
													<?php echo $this->escape($item->title); ?></a>
											<?php else : ?>
												<?php echo $this->escape($item->title); ?>
											<?php endif; ?>
										</td>
										<td class="center" align="center" >
											<?php if ($item->thumbnail) : ?>
												<?php if ($canEdit || $canEditOwn) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&task=template.edit&id='. $item->id);?>">
														<img src="<?php echo JURI::root( true ) . '/' . $item->thumbnail; ?>" style="width: 100px;" />
													</a>
												<?php else : ?>
													<img src="<?php echo JURI::root( true ) . '/' . $item->thumbnail; ?>" style="width: 100px;" />
												<?php endif; ?>
											<?php endif; ?>
										</td>
										<td class="center" align="center"><?php if (($item->tpl_id == 998) || ($item->tpl_id > 999)) { echo 'TEXT'; } else { echo 'HTML'; }?></td>
										<td class="center" align="center"><?php echo JHtml::_('jgrid.isdefault', ($item->standard != '0' && !empty($item->standard)), $i, 'template.', $canChange && $item->standard != '1');?></td>
										<td class="center" align="center"><?php echo JHTML::_('jgrid.published', $item->published, $i, 'templates.', $canChange, 'cb'); ?>
										<td><?php echo nl2br($item->description); ?></td>
										<td align="center"><?php echo $item->id; ?></td>
									</tr><?php 
								endforeach; 
							}
							else { ?>
								<tr class="row1">
									<td colspan="8"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
								</tr><?php
							} 
						?>
						</tbody>
					</table>
				</div>
		
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHTML::_('form.token'); ?>
		
				<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
			</div>
		</div>
	</form>
</div>	
