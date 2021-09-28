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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.modal');

$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_tabshelper');

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
			<div class="main-card">
				<?php
				$detailText = $this->item->id ? Text::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id) : Text::_('COM_BWPOSTMAN_NEW_CAM');
				echo HTMLHelper::_('uitab.startTabSet', 'campaign_tabs', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]);

				echo HTMLHelper::_('uitab.addTab', 'campaign_tabs', 'details', $detailText);
				?>
				<div class="mb-3">
					<?php echo $this->loadTemplate('basic'); ?>
				</div>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					echo HTMLHelper::_('uitab.addTab', 'campaign_tabs', 'unsent', Text::_('COM_BWPOSTMAN_CAM_UNSENT_NLS'));
					?>
				<div class="card card-body mb-3 com_config">
					<?php echo $this->loadTemplate('unsent'); ?>
				</div>
				<?php
				echo HTMLHelper::_('uitab.endTab');

				echo HTMLHelper::_('uitab.addTab', 'campaign_tabs', 'sent', Text::_('COM_BWPOSTMAN_NL_SENT'));
				?>
				<div class="card card-body mb-3 com_config">
					<?php echo $this->loadTemplate('sent'); ?>
				</div>
				<?php
				echo HTMLHelper::_('uitab.endTab');

				if ($this->permissions['com']['admin'] || $this->permissions['admin']['mailinglist'])
				{
					echo HTMLHelper::_('uitab.addTab', 'campaign_tabs', 'rules', Text::_('COM_BWPOSTMAN_CAM_FIELDSET_RULES'));
					?>
					<div class="card card-body mb-3 com_config">
						<?php echo $this->form->getInput('rules'); ?>
					</div>
					<?php
					echo HTMLHelper::_('uitab.endTab');
				}
				echo HTMLHelper::_('uitab.endTabSet');
				?>
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
<div id="bwp-modal" class="joomla-modal modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center">&nbsp;</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JTOOLBAR_CLOSE'); ?>"></button>
			</div>
			<div class="modal-body p-3">
				<iframe class="modal-frame" width="100%"></iframe>
			</div>
			<div class="modal-footer">
				<button class="btn btn-dark btn-sm" data-bs-dismiss="modal" type="button" title="<?php echo Text::_('JTOOLBAR_CLOSE'); ?>"><?php echo Text::_('JTOOLBAR_CLOSE'); ?></button>
			</div>
		</div>
	</div>
</div>
