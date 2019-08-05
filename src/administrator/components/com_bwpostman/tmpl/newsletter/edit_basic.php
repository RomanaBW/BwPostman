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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$image = '<i class="icon-info"></i>';

$checkContentArgs	 = "document.adminForm['jform_selected_content'], ";
$checkContentArgs	.= "document.getElementById('selected_content_old'), ";
$checkContentArgs	.= "document.getElementById('content_exists'), ";
$checkContentArgs	.= "document.getElementsByName('jform[template_id]'), ";
$checkContentArgs	.= "document.getElementsByName('jform[text_template_id]'), ";
$checkContentArgs	.= "document.getElementById('template_id_old'), ";
$checkContentArgs	.= "document.getElementById('text_template_id_old'),";
$checkContentArgs	.= "'" . JText::_('COM_BWPOSTMAN_NL_CONFIRM_ADD_CONTENT', true) . "', ";
$checkContentArgs	.= "'" . JText::_('COM_BWPOSTMAN_NL_CONFIRM_TEMPLATE_ID', true) . "', ";
$checkContentArgs	.= "'" . JText::_('COM_BWPOSTMAN_NL_CONFIRM_TEXT_TEMPLATE_ID', true) . "'";

$checkRecipientArgs	 = "document.getElementById('jform[campaign_id'), ";
$checkRecipientArgs	 = "document.getElementsByName('jform[ml_available][]'), ";
$checkRecipientArgs	.= "document.getElementsByName('jform[ml_unavailable][]'), ";
$checkRecipientArgs	.= "document.getElementsByName('jform[ml_intern][]'), ";
$checkRecipientArgs	.= "document.getElementsByName('jform[usergroups][]'), ";
$checkRecipientArgs	.= "'" . JText::_('COM_BWPOSTMAN_NL_ERROR_NO_RECIPIENTS_SELECTED', true) . "'";
?>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

function changeTab(tab)
{
	if (tab != 'edit_basic')
	{
		document.adminForm.tab.setAttribute('value',tab);
		document.adminForm.task.setAttribute('value','newsletter.changeTab');
		checkSelectedContent(<?php echo $checkContentArgs; ?>);
		if ($j("#jform_campaign_id option:selected").val() == '-1')
		{
			res = checkSelectedRecipients(<?php echo $checkRecipientArgs; ?>);
			return res;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}

Joomla.submitbutton = function (pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'newsletter.cancel')
	{
		Joomla.submitform(pressbutton, form);
		return;
	}

	if (pressbutton == 'newsletter.back')
	{
		form.task.value = 'back';
		Joomla.submitform(pressbutton, document.adminForm);
		return;
	}

	if (pressbutton == 'newsletter.apply' || pressbutton == 'newsletter.save2new' || pressbutton == 'newsletter.save2copy')
	{
		if (checkSelectedContent(<?php echo $checkContentArgs; ?>)== true)
		{
			document.adminForm.task.setAttribute('value',pressbutton);
			if ($j("#jform_campaign_id option:selected").val() == '-1')
			{
				res = checkSelectedRecipients(<?php echo $checkRecipientArgs; ?>);
				if (res == false)
				{
					return false;
				}
				else
				{
					Joomla.submitform(pressbutton, document.adminForm);
					return true;
				}
			}
			else
			{
				Joomla.submitform(pressbutton, document.adminForm);
				return true;
			}
		}
	}

	if (pressbutton == 'newsletter.save')
	{
		if (checkSelectedContent(<?php echo $checkContentArgs; ?>)== true)
		{
			document.adminForm.task.setAttribute('value',pressbutton);
			if ($j("#jform_campaign_id option:selected").val() == '-1')
			{
				res = checkSelectedRecipients(<?php echo $checkRecipientArgs; ?>);
				if (res == false)
				{
					return false;
				}
				else
				{
					Joomla.submitform(pressbutton, document.adminForm);
					return true;
				}
			}
			else
			{
				Joomla.submitform(pressbutton, document.adminForm);
				return true;
			}
		}
	}
};
<?php if (isset($this->substitute) && $this->substitute === true): ?>
window.onload = function()
{
	var substitute =  document.getElementsByName("jform[substitute_links]");
	for (var i=0; i < substitute.length; i++)
	{
		substitute[i].onclick = function()
		{
			document.getElementById("add_content").value = "1";
			document.getElementById("template_id_old").value = "";
		};
	}
};

<?php endif; ?>
/* ]]> */
</script>

<div id="bwp_view_single">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=newsletter'); ?>" method="post" name="adminForm" id="adminForm">
		<?php
			if ($this->item->is_template)
			{
				JFactory::$application->enqueueMessage(JText::_("COM_BWPOSTMAN_NL_IS_TEMPLATE_INFO"), "Notice");
			}
		?>
		<div class="form-horizontal">
			<ul class="bwp_tabs">
				<li class="open">
					<button onclick="return changeTab('edit_basic');" class="buttonAsLink_open">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP1'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_html');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP2'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_text');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_preview');" class="buttonAsLink">
						<?php echo JText::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</button>
				</li>
				<?php if (BwPostmanHelper::canSend((int) $this->item->id) && !$this->item->is_template) { ?>
					<li class="closed">
						<button onclick="return changeTab('edit_send');" class="buttonAsLink">
							<?php echo JText::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</button>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="clr clearfix"></div>

		<div class="tab-wrapper-bwp">
			<div class="well row nl-generals">
				<legend>
					<?php
					$title = JText::_('COM_BWPOSTMAN_NL_GENERAL');

					if ($this->item->id)
					{
						$title = JText::sprintf('COM_BWPOSTMAN_NL_GENERAL_EDIT', $this->item->id);
					}

					echo $title;
					?>
				</legend>
				<div class="col-md-6">
					<?php foreach($this->form->getFieldset('basic_1') as $field): ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
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
									<div class="control-label">
										<?php echo $field->label; ?>
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
								<div class="control-label">
									<?php echo $field->label; ?>
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
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>

				<div class="col-md-6">
						<?php foreach($this->form->getFieldset('basic_2') as $field): ?>
							<?php if ($field->hidden): ?>
								<?php echo $field->input; ?>
							<?php else: ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $field->label; ?>
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
				<div class="clr clearfix"></div>
				<p>
					<span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span>
				</p>
			</div>

			<div class="row well nl-templates">
				<legend>
					<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_NL_TEMPLATES_NOTE'); ?>">
						<?php echo $image; ?>
					</span>
					<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_TEMPLATES'); ?></span>
				</legend>
				<?php foreach($this->form->getFieldset('templates') as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<div class="col-md-4" id="<?php echo $field->title ?>">
							<div class="well-small well-white">
								<?php echo $field->renderField(); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="clr clearfix"></div>

			<div class="row well nl-recipients">
				<legend class="required">
					<?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *
				</legend>
				<div class="col-md-9 nl-mailinglists">
					<div class="well-white well-small">
						<legend>
							<span class="editlinktip hasTip hasTooltip"
									title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
								<?php echo $image; ?>
							</span>
							<span class="editlinktip hasTip hasTooltip"
									title="<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS_NOTE'); ?>">
								<?php echo JText::_('COM_BWPOSTMAN_NL_COM_BWPOSTMAN_MAILINGLISTS'); ?>
							</span>
						</legend>
						<div class="row">
						<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
							<?php if ($field->hidden): ?>
								<?php echo $field->input; ?>
							<?php else: ?>
								<div class="col-md-4 nl-mailinglists">
									<div class="well well-small">
										<fieldset>
											<legend>
												<span class="editlinktip hasTip hasTooltip"
														title="<?php echo JText::_($field->description); ?>">
													<?php echo $image; ?>
												</span>
												<span class="editlinktip hasTip hasTooltip"
														title="<?php echo JText::_($field->description); ?>">
													<?php echo $field->label; ?>
												</span>
											</legend>
											<div class="row-fluid clearfix">
												<?php
												$input_field	= trim($field->input);
												if (!empty($input_field))
												{
													echo $field->input;
												}
												else
												{
													echo '<div class="width-50 fltlft col-md-6">
																<label class="mailinglist_label noclear checkbox">' .
																	JText::_('COM_BWPOSTMAN_NO_DATA') .
																'</label>
															</div>';
												}
												?>
											</div>
										</fieldset>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="col-md-3 nl-usergroups">
					<div class="well-white well-small">
						<legend>
							<span class="editlinktip hasTip hasTooltip"
									title="<?php echo JText::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_DESC'); ?>">
								<?php echo $image; ?>
							</span>
							<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_FIELD_USERGROUPS_LABEL'); ?></span>
						</legend>
						<?php foreach($this->form->getFieldset('usergroups') as $field): ?>
							<?php echo $field->input; ?>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="clr clearfix"></div>
			</div>

			<div class="row well nl-contents">
				<legend>
					<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_NL_ADD_CONTENT_NOTE'); ?>">
						<?php echo $image; ?>
					</span>
					<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_CONTENTS'); ?></span>
				</legend>
				<div class="col-md-5">
					<?php foreach($this->form->getFieldset('selected_content') as $field): ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>

				<div class="col-md-2 nl-content-mover">
					<div class="control-group">
						<div class="control-label">
							<label>&nbsp;</label>
						</div>
						<div class="controls">

							<input style="width: 50px;" type="button" name="left" class="btn-left" value="&lt;"
								onclick="moveSelectedOptions(document.adminForm['jform_available_content'],
								document.adminForm['jform_selected_content'])" />
							<input style="width: 50px;" type="button" name="right" class="btn-right" value="&gt;"
								onclick="moveSelectedOptions(document.adminForm['jform_selected_content'],
								document.adminForm['jform_available_content'])" />
						</div>
					</div>
				</div>

				<div class="col-md-5">
					<?php foreach($this->form->getFieldset('available_content') as $field): ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="clr clearfix"></div>
			</div>

			<div class="row well">
				<div class="col-md-12">
					<section id="rules" name="Newsletters permissions" aria-labelledby="tab-rules" role="tabpanel" active="">
						<legend><?php echo JText::_('COM_BWPOSTMAN_NL_FIELDSET_RULES'); ?></legend>
						<?php
						if ($this->permissions['com']['admin'] || $this->permissions['admin']['newsletter']): ?>
							<?php echo $this->form->getInput('rules'); ?>
						<?php endif; ?>
					</section>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>

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
		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="layout" name="layout" value="edit_basic" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_basic" /><!-- value can change if one clicks on another tab -->
		<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
		<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
		<input type="hidden" id="add_content" name="add_content" value="" />
		<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
		<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
		</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

$j(document).ready(function()
{
	if ($j("#jform_campaign_id option:selected").val() != '-1')
	{
		$j( "#recipients" ).hide();
	}
	else
	{
		$j( "#recipients" ).show();
	}
});

$j("#jform_campaign_id").on("change", function()
{
	if ($j("#jform_campaign_id option:selected").val() != '-1')
	{
		$j( "#recipients" ).hide();
	}
	else
	{
		$j( "#recipients" ).show();
	}
});
/* ]]> */
</script>
