<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists default template for backend.
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
	Joomla.submitbutton = function (pressbutton)
	{
		if (pressbutton == 'archive')
		{
			ConfirmArchive = confirm("<?php echo JText::_('COM_BWPOSTMAN_ML_CONFIRM_ARCHIVE' , true); ?>");
			if (ConfirmArchive == true)
			{
				submitform(pressbutton);
			}
		}
		else
		{
			submitform(pressbutton);
		}
	};
/* ]]> */
</script>

<div id="bwp_view_lists">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=mailinglists'); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
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
							<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
							<th width="250" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_ML_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
							<th nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_ML_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?></th>
							<th width="100" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'PUBLISHED', 'a.published', $listDirn, $listOrder); ?></th>
							<th width="100" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'ACCESS_LEVEL', 'a.access', $listDirn, $listOrder); ?></th>
							<th width="100" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_ML_SUB_NUM', 'subscribers', $listDirn, $listOrder); ?></th>
							<th width="30" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						if (count($this->items) > 0) {
							foreach ($this->items as $i => $item) :
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
								<td>
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'mailinglists.', BwPostmanHelper::canCheckin('mailinglist', $item->checked_out)); ?>
									<?php endif; ?>
									<?php if (BwPostmanHelper::canEdit('mailinglist', $item)) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&task=mailinglist.edit&id='. $item->id);?>"><?php echo $this->escape($item->title); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->title); ?>
									<?php endif; ?>
								</td>
								<td><?php echo $item->description; ?></td>
								<td align="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'mailinglists.', BwPostmanHelper::canEditState('mailinglist', $item->id), 'cb'); ?>
								<td><?php echo $this->escape($item->access_level); ?></td>
								<td align="center"><?php echo $item->subscribers; ?></td>
								<td align="center"><?php echo $item->id; ?></td>
							</tr>
							<?php endforeach;
						}
						else { ?>
							<tr class="row1">
								<td colspan="7"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
							</tr><?php
						}
						?>
					</tbody>
				</table>

				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
		</div>
	</form>
</div>
