<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers confirmed template for backend.
 *
 * @version 1.3.0 bwpm
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

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	Joomla.checkAll2 = function(checkbox, stub) {
	    if (!stub) {
	        stub = 'ub';
	    }
	    if (checkbox.form) {
	        var c = 0, i, e;
	        for (i = 0, n = checkbox.form.elements.length; i < n; i++) {
	            e = checkbox.form.elements[i];
	            if (e.type == checkbox.type) {
	                if ((stub && e.id.indexOf(stub) == 0) || !stub) {
	                    e.checked = checkbox.checked;
	                    c += (e.checked == true ? 1 : 0);
	                }
	            }
	        }
	        if (checkbox.form.boxchecked) {
	            checkbox.form.boxchecked.value = c;
	        }
	        return true;
	    }
	    return false;
	}
</script>

<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="30" nowrap="nowrap" align="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll2(this)" /></th>
			<th width="200" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_NAME', 'a.name', $listDirn, $listOrder); ?></th>
			<th width="150" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?></th>
			<th nowrap="nowrap"><?php echo JHTML::_('searchtools.sort', 'COM_BWPOSTMAN_EMAIL', 'a.email', $listDirn, $listOrder); ?></th>
			<th width="100" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_EMAILFORMAT', 'a.emailformat', $listDirn, $listOrder); ?></th>
			<th width="100" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_JOOMLA_USERID', 'a.user_id', $listDirn, $listOrder); ?></th>
			<th width="100" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'COM_BWPOSTMAN_SUB_ML_NUM', 'mailinglists', $listDirn, $listOrder); ?></th>
			<th width="30" nowrap="nowrap"><?php echo JHTML::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		if (count ($this->items)) {
			foreach ($this->items as $i => $item) :
				$ordering	= ($listOrder == 'a.ordering');
				$canCheckin	= $user->authorise('core.manage',	'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEdit	= $user->authorise('core.edit',		'com_bwpostman.subscriber.'.$item->id);
				$canEditOwn	= $user->authorise('core.edit.own',	'com_bwpostman.subscriber.'.$item->id) && $item->registered_by == $userId;
				$name		= ($item->name) ? $item->name : JText::_('COM_BWPOSTMAN_SUB_NONAME');
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id, 0, 'cid', 'ub'); ?></td>
					<td>
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'subscribers.', $canCheckin, 'ub'); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&task=subscriber.edit&id='. $item->id);?>">
								<?php echo $this->escape($name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($name); ?>
						<?php endif; ?>
					</td>
					<td><?php echo $item->firstname; ?></td>
					<td><?php echo $item->email; ?></td>
					<td align="center"><?php echo ($item->emailformat) ? JText::_('COM_BWPOSTMAN_HTML') : JText::_('COM_BWPOSTMAN_TEXT')?></td>
					<td align="center"><?php echo ($item->user_id) ? $item->user_id : ''; ?></td>
					<td align="center"><?php echo $item->mailinglists; ?></td>
					<td align="center"><?php echo $item->id; ?></td>
				</tr><?php
			endforeach;
		}
		else {
    	// if no data ?>
			<tr class="row1">
				<td colspan="8"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
			</tr><?php
		}
	?>
	</tbody>
</table>
<input type="hidden" name="tab" value="unconfirmed" />
