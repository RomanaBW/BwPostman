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
JHtml::_('behavior.modal');
JHtml::_('behavior.multiselect');

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

// Load the modal behavior for the newsletter preview
JHtml::_('behavior.modal', 'a.popup');

//Load tabs behavior for the Tabs
jimport('joomla.html.html.tabs');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JFactory::getApplication()->setUserState($this->context . 'tab', 'sent');
?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.checkAll2 = function(checkbox, stub)
	{
		if (!stub)
		{
			stub = 'ub';
		}
		if (checkbox.form)
		{
			var c = 0, i, e;
			for (i = 0, n = checkbox.form.elements.length; i < n; i++)
			{
				e = checkbox.form.elements[i];
				if (e.type == checkbox.type)
				{
					if ((stub && e.id.indexOf(stub) == 0) || !stub)
					{
						e.checked = checkbox.checked;
						c += (e.checked == true ? 1 : 0);
					}
				}
			}
			if (checkbox.form.boxchecked)
			{
				checkbox.form.boxchecked.value = c;
			}
			return true;
		}
		return false;
	};

	function changeTab(tab)
	{
		if (tab != 'default_unsent')
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
		if (pressbutton == 'newsletters.archive')
		{
			ConfirmArchive = confirm("<?php echo JText::_('COM_BWPOSTMAN_NL_CONFIRM_ARCHIVE', true); ?>");
			if (ConfirmArchive == true)
			{
				Joomla.submitform(pressbutton, form);
			}
		}
		else
		{
			Joomla.submitform(pressbutton, form);
		}
	};
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
			echo JLayoutHelper::render(
				'default',
				array('view' => $this, 'tab' => 'sent'),
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
					<li class="open">
						<button onclick="return changeTab('sent');" class="buttonAsLink_open">
							<?php echo JText::_('COM_BWPOSTMAN_NL_SENT'); ?>
						</button>
					</li>
					<?php if ($this->count_queue && $this->permissions['newsletter']['send']) { ?>
						<li class="closed">
							<button onclick="return changeTab('queue');" class="buttonAsLink">
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
							<th width="30" nowrap="nowrap" align="center">
								<input type="checkbox" name="checkall-toggle" value=""
										title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll2(this)" />
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_ATTACHMENT', 'a.attachment', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
							</th>
							<th nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
							</th>
							<th width="150" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_MAILING_DATE', 'a.mailing_date', $listDirn, $listOrder); ?>
							</th>
							<th width="100" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
							</th>
							<th width="100" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_CAM_NAME', 'campaign_id', $listDirn, $listOrder); ?>
							</th>
							<th width="100" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort', 'Published', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th width="100" nowrap="nowrap">
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_UP', 'a.publish_up', $listDirn, $listOrder); ?>
								<br />
								<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_PUBLISH_DOWN', 'a.publish_down', $listDirn, $listOrder); ?>
								</th>
							<th width="30" nowrap="nowrap"><?php echo JHtml::_('searchtools.sort', 'NUM', 'a.id', $listDirn, $listOrder); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (count($this->items))
					{
						foreach ($this->items as $i => $item) :
							?>
							<tr class="row<?php echo $i % 2; ?>">
								<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id, 0, 'cid', 'ub'); ?></td>
								<td>
									<?php if (!empty($item->attachment)) { ?>
										<span class="icon_attachment" title="<?php echo JText::_('COM_BWPOSTMAN_ATTACHMENT'); ?>"></span>
									<?php } ?>
								</td>
								<td nowrap="nowrap">
									<?php
									if ($item->checked_out)
									{
										echo JHtml::_(
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
											<?php echo JRoute::_(
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
									<p class="editlinktip hasTip"
											title="<?php echo JText::_('COM_BWPOSTMAN_NL_SHOW_HTML');?>::
											<?php echo $this->escape($item->subject); ?>">
										<a class="modal" href="
										<?php echo JRoute::_(
											'index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_html_modal&task=insideModal&nl_id='
											. $item->id
										);?>" rel="{handler: 'iframe', size: {x: 650, y: 450}, iframeOptions: {id: 'htmlFrame'}}">
											<?php echo JText::_('COM_BWPOSTMAN_HTML_NL');?>
										</a>&nbsp;
									</p>
									<p class="editlinktip hasTip" title="<?php
										echo JText::_('COM_BWPOSTMAN_NL_SHOW_TEXT');?>::
										<?php echo $this->escape($item->subject); ?>">
										<a class="modal" href="
										<?php
											echo JRoute::_(
												'index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_text_modal&task=insideModal&nl_id='
												. $item->id
											);
											?>" rel="{handler: 'iframe', size: {x: 650, y: 450}, iframeOptions: {id: 'textFrame'}}">
											<?php echo JText::_('COM_BWPOSTMAN_TEXT_NL');?>
										</a>&nbsp;
									</p>
								</td>
								<td><?php echo $this->escape($item->description); ?></td>
								<td><?php echo JHtml::date($item->mailing_date, JText::_('BW_DATE_FORMAT_LC5')); ?></td>
								<td><?php echo $item->authors; ?></td>
								<td align="center"><?php echo $item->campaign_id; ?></td>
								<td align="center">
									<?php echo JHtml::_(
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
											? JHtml::date($item->publish_up, JText::_('BW_DATE_FORMAT_LC5'))
											: '-'; ?><br />
									</p>
									<p>
										<?php echo ($item->publish_down != '0000-00-00 00:00:00')
											? JHtml::date($item->publish_down, JText::_('BW_DATE_FORMAT_LC5'))
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
							<td colspan="10"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
						</tr><?php
					}
				?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="tab" value="sent" />
			<input type="hidden" name="layout" value="edit_publish" />
			<input type="hidden" name="tpl" value="sent" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
