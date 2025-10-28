<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit basic template for backend.
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

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.keepalive');

Text::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');

$image = '<i class="fa fa-lg fa-info-circle"></i>';

$checkContentArgs	= "'" . Text::_('COM_BWPOSTMAN_NL_CONFIRM_ADD_CONTENT', true) . "', ";
$checkContentArgs	.= "'" . Text::_('COM_BWPOSTMAN_NL_CONFIRM_TEMPLATE_ID', true) . "', ";
$checkContentArgs	.= "'" . Text::_('COM_BWPOSTMAN_NL_CONFIRM_TEXT_TEMPLATE_ID', true) . "', ";
$checkContentArgs	.= "'" . Text::_('COM_BWPOSTMAN_NO_HTML_TEMPLATE_SELECTED', true) . "', ";
$checkContentArgs	.= "'" . Text::_('COM_BWPOSTMAN_NO_TEXT_TEMPLATE_SELECTED', true) . "'";

$checkRecipientArgs	= "'" . Text::_('COM_BWPOSTMAN_NL_ERROR_NO_RECIPIENTS_SELECTED', true) . "'";

if (isset($this->substitute))
{
	$substitute = $this->substitute;
}
else
{
	$substitute = false;
}

$currentTab = 'edit_basic';
?>

<div id="bwp_view_single">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=newsletter'); ?>" method="post" name="adminForm" id="item-form">
		<?php
			if ($this->item->is_template)
			{
				Factory::$application->enqueueMessage(Text::_("COM_BWPOSTMAN_NL_IS_TEMPLATE_INFO"), "Notice");
			}
		?>
		<div class="bwp-newsletter">
			<ul class="nav nav-tabs bwp-tabs">
				<li class="nav-item">
					<a id="tab-edit_basic" href="#" onclick="changeTab('edit_basic', '<?php echo $currentTab; ?>', <?php echo $checkContentArgs; ?>, <?php echo $checkRecipientArgs; ?>);Joomla.submitbutton();" class="nav-link active">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP1'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_html" href="#" onclick="changeTab('edit_html', '<?php echo $currentTab; ?>', <?php echo $checkContentArgs; ?>, <?php echo $checkRecipientArgs; ?>);Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP2'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_text" href="#" onclick="changeTab('edit_text', '<?php echo $currentTab; ?>', <?php echo $checkContentArgs; ?>, <?php echo $checkRecipientArgs; ?>);Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_preview" href="#" onclick="changeTab('edit_preview', '<?php echo $currentTab; ?>', <?php echo $checkContentArgs; ?>, <?php echo $checkRecipientArgs; ?>);Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</a>
				</li>
				<?php if (BwPostmanHelper::canSend((int) $this->item->id) && !$this->item->is_template) { ?>
					<li class="nav-item">
						<a id="tab-edit_send" href="#" onclick="changeTab('edit_send', '<?php echo $currentTab; ?>', <?php echo $checkContentArgs; ?>, <?php echo $checkRecipientArgs; ?>);Joomla.submitbutton();" class="nav-link">
							<?php echo Text::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>

			<div class="tab-wrapper">
				<div class="card card-body mb-2">
					<div class="row nl-generals">
						<div class="col-12 mb-2">
							<div class="h3" id="bw_nl_edit_generals">
								<?php
								$title = Text::_('COM_BWPOSTMAN_NL_GENERAL');

								if ($this->item->id)
								{
									$title = Text::sprintf('COM_BWPOSTMAN_NL_GENERAL_EDIT', $this->item->id);
								}

								echo $title;
								?>
							</div>
						</div>
						<div class="col-lg-6">
							<?php foreach($this->form->getFieldset('basic_1') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="control-group">
										<div aria-describedby="<?php echo $field->name; ?>-desc">
											<?php echo $field->label; ?>
										</div>
										<div role="tooltip" id="<?php echo $field->name; ?>-desc">
											<?php echo Text::_($field->description); ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php if (count($this->form->getFieldset('scheduled')))
							{
								foreach ($this->form->getFieldset('scheduled') as $field): ?>
									<?php if ($field->hidden): ?>
										<?php echo $field->input; ?>
									<?php else: ?>
										<div class="control-group">
											<div aria-describedby="<?php echo $field->name; ?>-desc">
												<?php echo $field->label; ?>
											</div>
											<div role="tooltip" id="<?php echo $field->name; ?>-desc">
												<?php echo Text::_($field->description); ?>
											</div>
											<div class="controls">
												<?php echo $field->input; ?>
											</div>
										</div>
									<?php endif; ?>
								<?php endforeach;
							}?>

							<?php foreach($this->form->getFieldset('campaigns') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="control-group">
										<div aria-describedby="<?php echo $field->name; ?>-desc">
											<?php echo $field->label; ?>
										</div>
										<div role="tooltip" id="<?php echo $field->name; ?>-desc">
											<?php echo Text::_($field->description); ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php foreach($this->form->getFieldset('edit_publish') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="control-group">
										<div aria-describedby="<?php echo $field->name; ?>-desc">
											<?php echo $field->label; ?>
										</div>
										<div role="tooltip" id="<?php echo $field->name; ?>-desc">
											<?php echo Text::_($field->description); ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
						<div class="col-lg-6">
							<?php foreach($this->form->getFieldset('basic_2') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="control-group">
										<div aria-describedby="<?php echo $field->name; ?>-desc">
											<?php echo $field->label; ?>
										</div>
										<div role="tooltip" id="<?php echo $field->name; ?>-desc">
											<?php echo Text::_($field->description); ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php if (isset($this->substitute) && $this->substitute === true): ?>
								<?php echo $this->form->getInput('substitute_links'); ?>
							<?php endif; ?>
						</div>
						<div class="col-12">
							<span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span>
						</div>
					</div>
				</div>

				<div class="card card-body mb-2">
					<div class="row nl-template">
						<div class="col-12 mb-2">
							<div class="h4" id="bw_nl_edit_tpl">
								<div aria-describedby="tip-nl-tpl">
									<?php echo $image; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_TEMPLATES'); ?>
								</div>
								<div role="tooltip" id="tip-nl-tpl"><?php echo Text::_('COM_BWPOSTMAN_NL_TEMPLATES_NOTE'); ?></div>
							</div>
						</div>
						<?php foreach($this->form->getFieldset('templates') as $field): ?>
							<?php if ($field->hidden): ?>
								<?php echo $field->input; ?>
							<?php else: ?>
								<div class="col-md-6" id="<?php echo $field->title ?>">
									<div class="border p-3">
										<div aria-describedby="<?php echo $field->name; ?>-desc">
											<?php echo $field->label; ?>
										</div>
										<div role="tooltip" id="<?php echo $field->name; ?>-desc">
											<?php echo Text::_($field->description); ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>

				<div id="recipients" class="card card-body mb-2">
					<div class="row nl-recipients">
						<div class="col-12 mb-2">
							<div class="h3 required" id="bw_nl_edit_subs">
								<?php echo Text::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *
							</div>
						</div>
						<div class="col-xl-8 nl-mailinglists">
							<div class="h4 mb-3">
								<div aria-describedby="tip-nl-subs">
									<?php echo $image; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS'); ?>
								</div>
								<div role="tooltip" id="tip-nl-subs"><?php echo Text::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?></div>
							</div>
							<div class="row">
								<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
									<?php if ($field->hidden): ?>
										<?php echo $field->input; ?>
									<?php else: ?>
										<div class="col-lg-4 nl-mailinglists mb-3">
											<div class="h5">
												<div aria-describedby="tip-<?php echo Text::_($field->fieldname); ?>">
													<?php echo $image; ?>
													<?php echo $field->label; ?>
												</div>
												<div role="tooltip" id="tip-<?php echo Text::_($field->fieldname); ?>"><?php echo Text::_($field->description); ?></div>
											</div>
											<div class="clearfix">
												<?php
												$input_field	= trim($field->input);
												if (!empty($input_field))
												{
													echo $field->input;
												}
												else
												{
													echo '<div>' . Text::_('COM_BWPOSTMAN_NO_DATA') . '</div>';
												}
												?>
											</div>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>

						<div class="col-xl-4 nl-usergroups break-word">
							<div class="h4 mb-3">
								<div aria-describedby="tip-nl-usergroups">
									<?php echo $image; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_LABEL'); ?>
								</div>
								<div role="tooltip" id="tip-nl-usergroups"><?php echo Text::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_DESC'); ?></div>
							</div>
							<?php foreach($this->form->getFieldset('usergroups') as $field): ?>
								<?php echo $field->input; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="card card-body mb-2">
					<div class="row nl-content">
						<div class="col-12 mb-2">
							<div id="bw_nl_edit_content" class="h4 mb-3">
								<div aria-describedby="tip-nl-content">
									<?php echo $image; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_CONTENTS'); ?>
								</div>
								<div role="tooltip" id="tip-nl-content"><?php echo Text::_('COM_BWPOSTMAN_NL_ADD_CONTENT_NOTE'); ?></div>
							</div>
						</div>

						<div class="col-lg-5">
							<div class="row">
								<div class="col-lg-9">
									<?php foreach($this->form->getFieldset('selected_content') as $field): ?>
										<?php if ($field->hidden): ?>
											<?php echo $field->input; ?>
										<?php else: ?>
											<div class="control-label">
												<div aria-describedby="tip-nl-content">
													<?php echo $image; ?>
													<?php echo $field->label; ?>
												</div>
												<div role="tooltip" id="tip-nl-content"><?php echo Text::_('COM_BWPOSTMAN_NL_REMOVE_CONTENT_NOTE'); ?></div>
											</div>
											<div class="controls">
												<?php echo $field->input; ?>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
								<div class="col-lg-2 nl-content-mover my-3">
									<div class="control-label">
										<label>&nbsp;</label>
									</div>
									<div class="w-100 text-center mt-2">
										<button type="button" name="up" class="btn-up btn btn-outline-secondary mr-2" onclick="sortSelectedOptions('up')">
											<span class="icon-chevron-up" aria-hidden="true"></span>
										</button>
										<br class="my-3">
										<button type="button" name="down" class="btn-down btn btn-outline-secondary" onclick="sortSelectedOptions('down')">
											<span class="icon-chevron-down" aria-hidden="true"></span>
										</button>
									</div>
								</div>
							</div>
						</div>


						<div class="col-lg-2 nl-content-mover text-center my-3">
							<div class="control-label">
								<label>&nbsp;</label>
							</div>
							<div class="pt-lg-4">
								<button type="button" name="left" class="btn-left btn btn-primary mr-2" onclick="moveSelectedOptions(form['jform_available_content'], form['jform_selected_content'])">
									<span class="icon-chevron-left" aria-hidden="true"></span>
								</button>
								<button type="button" name="right" class="btn-right btn btn-primary" onclick="moveSelectedOptions(form['jform_selected_content'], form['jform_available_content'])">
									<span class="icon-chevron-right" aria-hidden="true"></span>
								</button>
							</div>
						</div>

						<div class="col-lg-5">
							<div aria-describedby="tip-nl-content">
								<div id="available_content_label" class="mb-3">
									<?php echo $image; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_AVAILABLE_CONTENT'); ?>
								</div>
							</div>
							<div role="tooltip" id="tip-nl-content"><?php echo Text::_('COM_BWPOSTMAN_NL_ADD_CONTENT_NOTE'); ?></div>
							<?php foreach($this->form->getFieldset('available_content') as $field): ?>
								<?php if ($field->hidden): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<div class="control-label">
										<?php if ($field->id == 'jform_ac_id') { ?>
											<?php echo $image; ?>
											<?php echo $field->label; ?>
											<div role="tooltip" id="tip-nl-content"><?php echo Text::_('COM_BWPOSTMAN_NL_ADD_CONTENT_POPUP_NOTE'); ?></div>
										<?php } ?>
										<?php if ($field->id == 'jform_available_content') { ?>
											<?php echo $image; ?>
											<?php echo $field->label; ?>
											<div role="tooltip" id="tip-nl-content"><?php echo Text::_('COM_BWPOSTMAN_NL_ADD_CONTENT_LIST_NOTE'); ?></div>
										<?php } ?>
									</div>
									<div class="controls">
										<?php if ($field->fieldname == 'ac_id') { ?>
										<div class="row g-0 my-3">
											<div class="col-2 text-end">
												<button type="button" name="ac-left" class="btn btn-outline-info btn-left" onclick="moveArticle()" >
													<span class="icon-chevron-left" aria-hidden="true"></span>
												</button>
											</div>
											<div class="col-10">
												<?php } ?>
												<?php echo $field->input; ?>
												<?php if ($field->fieldname == 'ac_id') { ?>
											</div>
										</div>
									<?php } ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<?php if ($this->permissions['com']['admin'] || $this->permissions['admin']['newsletter']): ?>
					<div class="card card-body mb-3 com_config">
						<div class="row">
							<div class="col-12 mb-2">
								<div class="h3" id="bw_nl_edit_rules">
									<?php echo Text::_('COM_BWPOSTMAN_NL_FIELDSET_RULES'); ?>
								</div>
								<section id="rules" aria-labelledby="tab-rules" role="tabpanel">
									<?php echo $this->form->getInput('rules'); ?>
								</section>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$hiddenFieldsets = array(
				'html_version_hidden',
				'text_version_hidden',
			);
			foreach ($hiddenFieldsets as $hiddenFieldset)
			{
				foreach($this->form->getFieldset($hiddenFieldset) as $field)
				{
					echo $field->input;
				}
			}
			?>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" id="layout" name="layout" value="edit_basic" /><!-- value never changes -->
			<input type="hidden" name="tab" value="edit_basic" /><!-- value can change if one clicks on another tab -->
			<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
			<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
			<input type="hidden" id="add_content" name="add_content" value="" />
			<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
			<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<input type="hidden" id="checkContentArgs1" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_ADD_CONTENT', true); ?>" />
			<input type="hidden" id="checkContentArgs2" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_TEMPLATE_ID', true); ?>" />
			<input type="hidden" id="checkContentArgs3" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_TEXT_TEMPLATE_ID', true); ?>" />
			<input type="hidden" id="checkContentArgs4" value="<?php echo Text::_('COM_BWPOSTMAN_NO_HTML_TEMPLATE_SELECTED', true); ?>" />
			<input type="hidden" id="checkContentArgs5" value="<?php echo Text::_('COM_BWPOSTMAN_NO_TEXT_TEMPLATE_SELECTED', true); ?>" />
			<input type="hidden" id="checkRecipientArgs" value="<?php echo Text::_('COM_BWPOSTMAN_NL_ERROR_NO_RECIPIENTS_SELECTED', true); ?>" />
			<input type="hidden" id="substituteLinks" value="<?php echo $substitute; ?>" />
			<input type="hidden" id="currentTab" value="<?php echo $currentTab; ?>" />
		</div>
	</form>
</div>
