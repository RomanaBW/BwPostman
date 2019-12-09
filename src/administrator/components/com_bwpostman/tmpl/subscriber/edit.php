<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber template for backend.
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

$modalParams = array();
$modalParams['modalWidth'] = 80;
$modalParams['bodyHeight'] = 70;

// declare image for tooltip
$image = '';

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

$image = '<i class="icon-info"></i>';

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.keepalive');

$new_test	= Factory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', $this->item->status);
?>

<div id="bwp_editform">
	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&layout=edit&id='.(int) $this->item->id); ?>"
			method="post" name="adminForm" id="item-form">
		<div class="tab-wrapper-bwp">
			<?php echo HTMLHelper::_('uitab.startTabSet', 'subscriber_tabs', array('active' => 'details')); ?>
			<?php echo HTMLHelper::_(
				'uitab.addTab',
				'subscriber_tabs',
				'details',
				is_null($this->item->id) ? Text::_('COM_BWPOSTMAN_NEW_SUB') : Text::sprintf('COM_BWPOSTMAN_EDIT_SUB', $this->item->id)
			); ?>
			<div class="row well">
				<div class="col-md-6">
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('gender'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('gender'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('firstname'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('firstname'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('name'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('name'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('email'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('email'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('emailformat'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('emailformat'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('special'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('special'); ?>
						</div>
					</div>

					<?php if ($new_test != '9') { ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('status'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('status'); ?>
							</div>
						</div>
					<?php } else { ?>
							<input id="jform_status" type="hidden" value="9" name="jform[status]">
					<?php } ?>
				</div>

				<div class="col-md-6">
					<?php
					$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=print&format=raw&task=insideModal&id=' . (int) $this->item->id);
					$title = Text::_('COM_BWPOSTMAN_PRINT_SUB_DAT');
					$modalParams['url'] = $link;
					$modalParams['title'] = $title;
					?>

					<button type="button" data-target="#subsData" class="btn btn-info" data-toggle="modal">
						<?php echo Text::_('COM_BWPOSTMAN_PRINT_SUB_DAT');?>
					</button>
					<?php echo HTMLHelper::_('bootstrap.renderModal','subsData', $modalParams); ?>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('confirmation_date'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('confirmation_date'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
					<?php echo $this->form->getLabel('confirmed_by'); ?>
						</div>
						<div class="controls">
					<?php echo $this->form->getInput('confirmed_by'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('confirmation_ip'); ?>
						</div>
					<div class="controls">
						<?php echo $this->form->getInput('confirmation_ip'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registration_date'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registration_date'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registered_by'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registered_by'); ?>
					</div>
				</div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('registration_ip'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('registration_ip'); ?>
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
				<div class="clr clearfix"></div>
				<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
			</div>
			</div>

			<?php if ($new_test != '9') : ?>
				<div class="row well subs-mailinglists">
					<legend>
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE_NOTE'); ?>">
							<?php echo $image; ?>
						</span>
						<span>&nbsp;<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE'); ?></span>
					</legend>
					<div class="col-md-4 subs-mailinglists well-white">
						<div class="well-small" id="ml_available">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>">
										<?php echo $image; ?>
									</span>
									<span>&nbsp;<?php echo $this->form->getLabel('ml_available'); ?></span>
								</legend>
								<div class="row-fluid clearfix">
									<?php
									$ml_available	= $this->form->getInput('ml_available');

									if (!empty($ml_available))
									{
										echo $this->form->getInput('ml_available');
									}
									else
									{ ?>
										<div class="width-50 fltlft span6">
											<label class="mailinglist_label noclear checkbox">
												<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="col-md-4 subs-mailinglists well-white">
						<div class="well-small" id="ml_unavailable">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>">
										<?php echo $image; ?>
									</span>
									<span>&nbsp;<?php echo $this->form->getLabel('ml_unavailable'); ?></span>
								</legend>
								<div class="row-fluid clearfix">
									<?php
									$ml_unavailable	= $this->form->getInput('ml_unavailable');

									if (!empty($ml_unavailable))
									{
										echo $this->form->getInput('ml_unavailable');
									}
									else
									{ ?>
										<div class="width-50 fltlft span6">
											<label class="mailinglist_label noclear checkbox">
												<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>

					<div class="col-md-4 subs-mailinglists well-white">
						<div class="well-small" id="ml_intern">
							<fieldset class="adminform">
								<legend>
									<span class="editlinktip hasTip hasTooltip"
											title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>">
										<?php echo $image; ?>
									</span>
									<span>&nbsp;<?php echo $this->form->getLabel('ml_intern'); ?></span>
								</legend>
								<div class="row-fluid clearfix">
									<?php
									$ml_intern	= $this->form->getInput('ml_intern');

									if (!empty($ml_intern))
									{
										echo $this->form->getInput('ml_intern');
									}
									else
									{ ?>
										<div class="width-50 fltlft span6">
											<label class="mailinglist_label noclear checkbox">
												<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
											</label>
										</div><?php
									}
									?>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
			<?php  endif ?>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php
			if (BwPostmanHelper::canAdmin('subscriber')): ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'subscriber_tabs', 'permissions', Text::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES')); ?>
					<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
						<legend><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES'); ?></legend>
						<div>
							<?php echo $this->form->getInput('rules'); ?>
						</div>
					</fieldset>

				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php endif
			?>
			<div class="clearfix"></div>
			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<?php
				$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

				$this->form->setValue('ip', '', $remote_ip);
				echo $this->form->getInput('ip');
			?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="jform[name_field_obligation]" value="<?php echo $this->obligation['name']; ?>" />
			<input type="hidden" name="jform[firstname_field_obligation]" value="<?php echo $this->obligation['firstname']; ?>" />
			<input type="hidden" name="jform[special_field_obligation]" value="<?php echo $this->obligation['special']; ?>" />
			<input type="hidden" id="jform_title" name="jform[title]" value="<?php echo $this->form->getValue('title') ?>">
			<?php echo $this->form->getInput('asset_id'); ?>
			<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
