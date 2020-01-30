<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglist form template for backend.
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
HtmlHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.popover', '.hasPopover', array('placement' => 'bottom'));

?>

<div id="bwp_editform">
	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&task=edit.save'); ?>"
			method="post" name="adminForm" id="item-form" class="form-validate">
		<div id="mailinglist_tabs">
			<ul class="nav nav-tabs bwp-tabs">
				<li class="nav-item">
					<a class="nav-link active" id="tab-details" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">
						<?php echo $this->item->id ? Text::sprintf('COM_BWPOSTMAN_EDIT_ML', $this->item->id) : Text::_('COM_BWPOSTMAN_NEW_ML'); ?>
					</a>
				</li>
				<?php if ($this->permissions['com']['admin'] || $this->permissions['admin']['mailinglist'])
				{ ?>
					<li class="nav-item">
						<a class="nav-link" id="tab-rules" data-toggle="tab" href="#rules" role="tab" aria-controls="rules" aria-selected="true">
							<?php echo Text::_('COM_BWPOSTMAN_ML_FIELDSET_RULES'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
			<div class="tab-content" id="mailinglistTabContent" role="tabpanel" aria-labelledby="details-tab">
				<div class="tab-pane fade show active" id="details">
					<div class="card card-body mb-3">
						<div class="row">
							<div class="col-lg-6">
								<?php echo $this->form->renderField('title'); ?>
								<?php echo $this->form->renderField('description'); ?>
								<?php echo $this->form->renderField('access'); ?>
								<?php echo $this->form->renderField('published'); ?>
							</div>
							<div class="col-lg-6">
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('campaign_id'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('campaign_id'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('created_date'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('created_date'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('created_by'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('created_by'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified_by'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified_by'); ?>
									</div>
								</div>
								<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel('modified_time'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified_time'); ?>
									</div>
								</div>
							</div>
						</div>
						<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
					</div>
				</div>
				<?php if ($this->permissions['com']['admin'] || $this->permissions['admin']['mailinglist'])
				{ ?>
					<div class="tab-pane fade" id="rules">
						<div class="card card-body mb-3 com_config">
							<?php echo $this->form->getInput('rules'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo HTMLHelper::_('form.token'); ?>

		<input type="hidden" id="alertTitle" value="<?php echo Text::_('COM_BWPOSTMAN_ML_ERROR_TITLE', true); ?>" />
		<input type="hidden" id="alertDescription" value="<?php echo Text::_('COM_BWPOSTMAN_ML_ERROR_DESCRIPTION'); ?>" />
	</form>
</div>
<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

