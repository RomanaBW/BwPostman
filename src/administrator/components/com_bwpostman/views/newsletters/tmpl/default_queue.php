<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters queue template for backend.
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

// Load the modal behavior for the newsletter preview
JHtml::_('behavior.modal', 'a.popup');

//Load tabs behavior for the Tabs
jimport('joomla.html.html.tabs');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<script type="text/javascript">
/* <![CDATA[ */
	function changeTab(tab)
	{
		if (tab != 'default_unsent')
		{
			document.adminForm.tab.setAttribute('value',tab);
		}
	}
/* ]]> */
</script>

<div id="bwp_view_lists">
	<?php
	if ($this->queueEntries)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=newsletters'); ?>"
			method="post" name="adminForm" id="adminForm" class="form-inline">
		<?php
		if (property_exists($this, 'sidebar')) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
			<?php
		else : ?>
				<div id="j-main-container">
			<?php
		endif; ?>
			<?php
				// Search tools bar
				echo JLayoutHelper::render(
					'default',
					array('view' => $this, 'tab' => 'queue'),
					$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
				);
			?>

			<div class="form-horizontal">
				<ul class="bwp_tabs">
					<li class="closed">
						<button onclick="return changeTab('unsent');" class="buttonAsLink">
							<?php echo JText::_('COM_BWPOSTMAN_NL_UNSENT'); ?>
						</button>
					</li>
					<li class="closed">
						<button onclick="return changeTab('sent');" class="buttonAsLink">
							<?php echo JText::_('COM_BWPOSTMAN_NL_SENT'); ?>
						</button>
					</li>
					<?php if ((count($this->count_queue) > 0) && $this->permissions['newsletter']['send']) { ?>
						<li class="open">
							<button onclick="return changeTab('queue');" class="buttonAsLink_open">
								<?php echo JText::_('COM_BWPOSTMAN_NL_QUEUE'); ?>
							</button>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="clr clearfix"></div>

			<div class="row-fluid current">
				<table id="main-table" class="adminlist table table-striped">
					<thead>
						<tr>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_SUBJECT', 'sc.subject', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_DESCRIPTION', 'n.description', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
							</th>
							<th width="250" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_RECIPIENT', 'q.recipient', $listDirn, $listOrder); ?>
							</th>
							<th width="30" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_TRIAL', 'q.trial', $listDirn, $listOrder); ?>
							</th>
							<th width="30" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'NUM', 'q.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (count($this->items))
					{
						foreach ($this->items as $i => $item) :
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td><?php echo $this->escape($item->subject); ?></td>
								<td><?php echo $this->escape($item->description); ?></td>
								<td><?php echo $item->authors; ?></td>
								<td><?php echo $item->recipient; ?></td>
								<td><?php echo $item->trial; ?></td>
								<td align="center"><?php echo $item->id; ?></td>
							</tr><?php
						endforeach;
					}
					else
					{
						// if no data ?>
						<tr class="row1">
							<td colspan="8"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA_FOUND'); ?></strong></td>
						</tr><?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo $this->queuePagination->getListFooter(); ?></div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="tab" value="queue" />
			<input type="hidden" name="layout" value="default" />
			<input type="hidden" name="tpl" value="queue" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>

