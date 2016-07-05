<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element template to select a singlenewsletter for a view in frontend.
 *
 * @version 2.0.0 bwpm
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
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
?>

<form id="adminForm" action="<?php JRoute::_('index.php?option=com_bwpostman&amp;view=newsletterelement&amp;tmpl=component'); ?>" method="post" name="adminForm">
	<table class="adminform">
		<tr>
			<td width="100%">
				<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom" style="margin-bottom:9px;">
					<span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="this.form.getElementById('search').setAttribute('value', '');this.form.submit();" style="margin-bottom:9px;">
					<span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</td>
		</tr>
	</table>
	<br />

	<table class="adminlist table">
		<thead>
			<tr>
				<th width="10"><?php echo JHtml::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th class="title"><?php echo JHtml::_('grid.sort', 'Subject', 'a.subject', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th class="title" style="text-align:center;"><?php echo JHtml::_('grid.sort', 'COM_BWPOSTMAN_NL_MAILING_DATE', 'a.mailing_date', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th class="title" style="text-align:center;"><?php echo JText::_('JPUBLISHED'); ?></th>
				<th class="title" style="text-align:center;"><?php echo JHtml::_('grid.sort', 'Archived', 'a.archive_flag', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (count($this->items) > 0) {
				foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="center"><?php echo $item->id; ?></td>
					<td>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_BWPOSTMAN_SELECT_NEWSLETTER' );?><?php echo "<br /><br /><strong>".$item->subject.":</strong><br />".$item->description; ?>">
							<a style="cursor:pointer;" onclick="window.parent.SelectNewsletter('<?php echo $item->id; ?>', '<?php echo str_replace( array("'", "\""), array("\\'", ""), $item->subject ); ?>');">
								<?php echo htmlspecialchars($item->subject, ENT_QUOTES, 'UTF-8'); ?>
							</a>
						</span>
					</td>
					<td align="center" style="text-align:center;"><?php echo $item->mailing_date; ?></td>
					<td align="center" style="text-align:center;"><?php echo JHtml::_('grid.published', $item, $i); ?></td>
					<td align="center" style="text-align:center;"><?php $archived = ($item->archive_flag == 0) ? '<i class="icon-archive"></i>' : ''; echo $archived; ?></td>
				</tr>
				<?php endforeach;
			}
			else { ?>
				<tr class="row1">
					<td colspan="5"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
				</tr><?php
			}
			?>
		</tbody>
	</table>

	<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
