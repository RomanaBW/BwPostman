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
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

$modalParams = array();
$modalParams['modalWidth'] = 80;
$modalParams['bodyHeight'] = 70;

// declare image for tooltip
$image = '';

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

$image = '<i class="fa fa-info-circle fa-lg"></i>';

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
		<div id="subscriber_tabs">
			<ul class="nav nav-tabs bwp-tabs">
				<li class="nav-item">
					<a class="nav-link active" id="tab-details" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">
						<?php echo is_null($this->item->id) ? Text::_('COM_BWPOSTMAN_NEW_SUB') : Text::sprintf('COM_BWPOSTMAN_EDIT_SUB', $this->item->id); ?>
					</a>
				</li>
				<?php if (BwPostmanHelper::canAdmin('subscriber'))
				{ ?>
					<li class="nav-item">
						<a class="nav-link" id="tab-permissions" data-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="true">
							<?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
			<div class="tab-content" id="subscriberTabContent" role="tabpanel" aria-labelledby="details-tab">
				<div class="tab-pane fade show active" id="details">
					<div class="card card-body mb-3">
						<div class="row">
							<div class="col-lg-6">
								<?php echo $this->form->renderField('gender'); ?>
								<?php echo $this->form->renderField('firstname'); ?>
								<?php echo $this->form->renderField('name'); ?>
								<?php echo $this->form->renderField('email'); ?>
								<?php echo $this->form->renderField('emailformat'); ?>
								<?php echo $this->form->renderField('special'); ?>
								<?php if ($new_test != '9') { ?>
									<?php echo $this->form->renderField('status'); ?>
								<?php } else { ?>
										<input id="jform_status" type="hidden" value="9" name="jform[status]">
								<?php } ?>
							</div>
							<div class="col-lg-6">
								<?php
								$title = Text::_('COM_BWPOSTMAN_PRINT_SUB_DAT');
								$modalParams['title'] = $title;
								$printLayout = LayoutHelper::render('print', array('form' => $this->form, 'subscriberId' => $this->item->id), JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/subscriber/');
								?>
								<div class="control-group">
									<div class="control-label">
									</div>
									<div class="controls">
										<button type="button" data-bs-target="#subsData" class="btn btn-primary" data-bs-toggle="modal">
											<?php echo Text::_('COM_BWPOSTMAN_PRINT_SUB_DAT');?>
										</button>
									</div>
								</div>

								<!-- Modal -->
								<div id="subsData" class="modal" tabindex="-1">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title"><?php echo $title; ?></h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<p><?php echo $printLayout; ?></p>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary btn-danger" data-bs-dismiss="modal"><?php echo Text::_('JTOOLBAR_CLOSE'); ?></button>
											</div>
										</div>
									</div>
								</div>


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
										<?php echo $this->form->getLabel('modified_time'); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput('modified_time'); ?>
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
							</div>
							<div class="col-12"><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></div>
						</div>
					</div>
					<?php if ($new_test != '9')
					{
						?>
						<div id="subs_mailinglists" class="card subs-mailinglists mb-3">
							<h4 class="card-header">
								<span class="hasTooltip" title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span>&nbsp;<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_AVAILABLE'); ?></span>
							</h4>
							<div class="row">
								<div class="col-lg-4 subs-mailinglists mb-3">
									<div class="card-body" id="ml_available">
										<fieldset class="adminform">
											<legend>
												<span class="hasTooltip"
													title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_AVAILABLE_NOTE'); ?>">
													<?php echo $image; ?>
												</span>
												<span>&nbsp;<?php echo $this->form->getLabel('ml_available'); ?></span>
											</legend>
											<?php
											$ml_available = $this->form->getInput('ml_available');

											if (!empty($ml_available))
											{
												echo $this->form->getInput('ml_available');
											}
											else
											{ ?>
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
											<?php
											}
											?>
										</fieldset>
									</div>
								</div>

								<div class="col-lg-4 subs-mailinglists mb-3">
									<div class="card-body" id="ml_unavailable">
										<fieldset class="adminform">
											<legend>
												<span class="hasTooltip"
													title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_PUBLISHED_UNAVAILABLE_NOTE'); ?>">
													<?php echo $image; ?>
												</span>
												<span>&nbsp;<?php echo $this->form->getLabel('ml_unavailable'); ?></span>
											</legend>
											<?php
											$ml_unavailable = $this->form->getInput('ml_unavailable');

											if (!empty($ml_unavailable))
											{
												echo $this->form->getInput('ml_unavailable');
											}
											else
											{ ?>
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
												<?php
											}
											?>
										</fieldset>
									</div>
								</div>

								<div class="col-lg-4 subs-mailinglists mb-3">
									<div class="card-body" id="ml_intern">
										<fieldset class="adminform">
											<legend>
												<span class="hasTooltip"
													title="<?php echo Text::_('COM_BWPOSTMAN_SUB_ML_INTERNAL_NOTE'); ?>">
													<?php echo $image; ?>
												</span>
												<span>&nbsp;<?php echo $this->form->getLabel('ml_intern'); ?></span>
											</legend>
											<?php
											$ml_intern = $this->form->getInput('ml_intern');

											if (!empty($ml_intern))
											{
												echo $this->form->getInput('ml_intern');
											}
											else
											{ ?>
												<label class="mailinglist_label noclear checkbox">
													<?php Text::_('COM_BWPOSTMAN_NO_DATA') ?>
												</label>
												<?php
											}
											?>
										</fieldset>
									</div>
								</div>
							</div>
						</div>
						<?php
					} ?>
				</div>
				<?php if (BwPostmanHelper::canAdmin('subscriber'))
				{
				?>
				<div class="tab-pane fade" id="permissions">
					<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
						<legend><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELDSET_RULES'); ?></legend>
						<div class="mb-3 com_config">
							<?php echo $this->form->getInput('rules'); ?>
						</div>
					</fieldset>
				</div>
				<?php
				}
				?>
				</div>
			</div>
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
