<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Get some states
$filter_id	= $this->escape($this->state->get('filter.mailinglist'));
$published	= $this->escape($this->state->get('filter.published'));

// Set session filter state for moving, needed in model
Factory::getSession()->set('com_bwpostman.subscriber.batch_filter_mailinglist', $filter_id);

// Create the subscribe/unsubscribe/move options.
$options = array(
	HTMLHelper::_('select.option', 's', Text::_('COM_BWPOSTMAN_SUB_HTML_BATCH_SUBSCRIBE')),
	HTMLHelper::_('select.option', 'u', Text::_('COM_BWPOSTMAN_SUB_HTML_BATCH_UNSUBSCRIBE'))
);

if ($filter_id)
{
	$options[]	= HTMLHelper::_('select.option', 'm', Text::_('COM_BWPOSTMAN_SUB_HTML_BATCH_MOVE'));
}

// Create the batch selector to change select the mailinglist by which to add or remove.
?>

<div class="container">
	<div class="row">
		<div class="form-group col-md-12">
			<?php //if ($published >= 0) : ?>

			<label id="batch-choose-action-lbl" for="batch-choose-action"><?php echo Text::_('COM_BWPOSTMAN_SUB_BATCH_MENU_LABEL'); ?></label>
			<div id="batch-choose-action" class="control-group">
				<select name="batch[mailinglist_id]" class="custom-select" id="batch-mailinglist-id">
					<?php echo HTMLHelper::_('select.options', $this->mailinglists, 'value', 'text', '', '', ''); ?>
				</select>
			</div>
			<div id="batch-task" class="control-group radio">
				<?php echo HTMLHelper::_('select.radiolist', $options, 'batch[batch-task]', '', 'value', 'text', 's'); ?>
			</div>
		</div>
	</div>
</div>
