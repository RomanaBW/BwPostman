<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers confirmed template for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Get some states
$filter_id	= $this->escape($this->state->get('filter.mailinglist'));
$published	= $this->escape($this->state->get('filter.published'));

// Set session filter state for moving, needed in model
JFactory::getSession()->set('com_bwpostman.subscriber.batch_filter_mailinglist', $filter_id);

// Create the subscribe/unsubscribe/move options.
$options = array(
	JHtml::_('select.option', 's', JText::_('COM_BWPOSTMAN_SUB_HTML_BATCH_SUBSCRIBE')),
	JHtml::_('select.option', 'u', JText::_('COM_BWPOSTMAN_SUB_HTML_BATCH_UNSUBSCRIBE'))
);

if ($filter_id)
{
	$options[]	= JHtml::_('select.option', 'm', JText::_('COM_BWPOSTMAN_SUB_HTML_BATCH_MOVE'));
}

// Create the batch selector to change select the mailinglist by which to add or remove.
$batch_lists = '<label id="batch-choose-action-lbl" for="batch-choose-action">' . JText::_('COM_BWPOSTMAN_SUB_BATCH_MENU_LABEL') . '</label>'
				. '<div class="clr"></div>'
				. '<div id="batch-choose-action" class="control-group">'
				. '<select name="batch[mailinglist_id]" class="inputbox" id="batch-mailinglist-id">'
				. JHtml::_('select.options', $this->mailinglists, 'value', 'text', '', '', '')
				. '</select>'
				. '</div>'
				. '<div id="batch-task" class="control-group radio">'
				. JHtml::_('select.radiolist', $options, 'batch[batch-task]', '', 'value', 'text', 's')
				. '</div>';
?>

<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_BWPOSTMAN_SUB_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body modal-batch">
		<p><?php echo JText::_('COM_BWPOSTMAN_SUB_BATCH_TIP'); ?></p>
		<div class="row-fluid">
			<?php if ($published >= 0) : ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $batch_lists; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.getElementById('batch-mailinglist-id').value='';" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('subscriber.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>

	</div>
</div>
