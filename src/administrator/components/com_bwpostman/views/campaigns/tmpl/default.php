<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all campaigns default template for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));


/**
 * BwPostman Campaigns Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */
?>

<script type="text/javascript">
/* <![CDATA[ */
	function confirmArchive(archive_value) // Get the selected value from modal box
	{
		document.adminForm.archive_nl.value = archive_value;
		Joomla.submitbutton('campaign.archive');
	}
/* ]]> */
</script>

<div id="bwp_view_lists">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=campaigns'); ?>"
			method="post" name="adminForm" id="adminForm" class="form-inline">
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
				<table id="main-table" class="adminlist table table-striped">
					<thead>
						<tr>
							<th width="30" nowrap="nowrap">
								<input type="checkbox" name="checkall-toggle" value=""
										title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_NL_NUM', 'newsletters', $listDirn, $listOrder); ?>
							</th>
							<?php
							if ($this->auto_nbr)
							{ ?>
								<th nowrap="nowrap"><?php echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOMATION'); ?></th>
								<th nowrap="nowrap"><?php echo JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVE'); ?></th>
							<?php
							}
							?>
							<th width="30" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if (count($this->items) > 0)
						{
							foreach ($this->items as $i => $item)
							{
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
									<td>
									<?php
									if ($item->checked_out)
									{ ?>
										<?php
										echo JHtml::_(
											'jgrid.checkedout',
											$i,
											$item->editor,
											$item->checked_out_time,
											'campaigns.',
											BwPostmanHelper::canCheckin('campaign', $item->checked_out)
										);
									} ?>
									<?php
									if (BwPostmanHelper::canEdit('campaign', $item))
									{ ?>
										<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&task=campaign.edit&id=' . $item->id); ?>">
											<?php echo $this->escape($item->title); ?>
										</a> <?php
									}
									else
									{
										echo $this->escape($item->title);
									} ?>
									</td>
									<td><?php echo $item->description; ?></td>
									<td align="center"><?php echo $item->newsletters; ?></td>
									<?php
									if ($this->auto_nbr)
									{
										$checked = '';
										if ($item->active)
										{
											$checked = 'checked="checked" ';
										} ?>
										<td align="center">
											<?php
											if ($item->auto)
											{
												echo JText::_('COM_BWPOSTMAN_YES');
											}
											?>
										</td>
										<td align="center">
											<?php
											if ($item->auto)
											{
												if ($item->active)
												{ ?>
													<a href="
													<?php echo
														JRoute::_(
															'index.php?option=com_bwpostman&view=campaign&task=campaign.activate&cid[0]=' . $item->id
														);
														?>"
														class="btn btn-micro active hasTooltip"><i class="icon-publish"></i>
													</a>
													<?php
												}
											} ?>
										</td>
										<?php
									} ?>
									<td align="center"><?php echo $item->id; ?></td>
								</tr>
							<?php
							}
						}
						else
						{ ?>
							<tr class="row1">
								<td colspan="5"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
							</tr><?php
						}
						?>
					</tbody>
				</table>
			</div>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="archive_nl" value="0" />
			<?php echo JHtml::_('form.token'); ?>

			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
		</div>
	</form>
</div>
