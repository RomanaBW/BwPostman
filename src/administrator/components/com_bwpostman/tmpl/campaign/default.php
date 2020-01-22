<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns form template for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

/**
 * BwPostman Single Campaign Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */

?>

	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<div id="bwp_view_single">
		<form action="<?php echo Route::_('index.php?option=com_bwpostman&layout=default&id=' . (int) $this->item->id); ?>"
				method="post" name="adminForm" id="item-form">
			<div>
				<div id="campaign_tabs">
					<ul class="nav nav-tabs bwp-tabs">
						<li class="nav-item">
							<a class="nav-link active" id="tab-basic" data-toggle="tab" href="#basic" role="tab" aria-controls="basic" aria-selected="true">
								<?php echo $this->item->id ? Text::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id) : Text::_('COM_BWPOSTMAN_NEW_CAM'); ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-unsent" data-toggle="tab" href="#unsent" role="tab" aria-controls="unsent" aria-selected="true">
								<?php echo Text::_('COM_BWPOSTMAN_CAM_UNSENT_NLS'); ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-sent" data-toggle="tab" href="#sent" role="tab" aria-controls="sent" aria-selected="true">
								<?php echo Text::_('COM_BWPOSTMAN_NL_SENT'); ?>
							</a>
						</li>
						<?php if ($this->permissions['com']['admin'] || $this->permissions['admin']['campaign'])
						{ ?>
							<li class="nav-item">
								<a class="nav-link" id="tab-permissions" data-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="true">
									<?php echo Text::_('COM_BWPOSTMAN_CAM_FIELDSET_RULES'); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
					<div class="tab-content" id="campaignTabContent" role="tabpanel" aria-labelledby="basic-tab">
						<div class="tab-pane fade show active" id="basic">
							<?php echo $this->loadTemplate('basic'); ?>
						</div>
						<div class="tab-pane fade" id="unsent">
							<?php echo $this->loadTemplate('unsent'); ?>
						</div>
						<div class="tab-pane fade" id="sent">
							<?php echo $this->loadTemplate('sent'); ?>
						</div>
						<?php if ($this->permissions['com']['admin'] || $this->permissions['admin']['campaign'])
						{ ?>
							<div class="tab-pane fade" id="permissions">
								<?php echo $this->loadTemplate('rules'); ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<input type="hidden" name="task" value="" />

				<?php echo $this->form->getInput('id'); ?>
				<?php echo $this->form->getInput('asset_id'); ?>
				<?php echo $this->form->getInput('checked_out'); ?>
				<?php echo $this->form->getInput('archive_flag'); ?>
				<?php echo $this->form->getInput('archive_time'); ?>
				<?php echo HTMLHelper::_('form.token'); ?>

				<input type="hidden" id="alertTitle" value="<?php echo Text::_('COM_BWPOSTMAN_CAM_ERROR_TITLE', true); ?>" />
				<input type="hidden" id="alertRecipients" value="<?php echo Text::_('COM_BWPOSTMAN_CAM_ERROR_NO_RECIPIENTS_SELECTED'); ?>" />
			</div>
		</form>
	</div>

	<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
