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
		submitform(pressbutton);
		return;
	}

	if (pressbutton == 'newsletter.back')
	{
		form.task.value = 'back';
		submitform(pressbutton);
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
					submitform(pressbutton);
					return true;
				}
			}
			else
			{
				submitform(pressbutton);
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
					submitform(pressbutton);
					return true;
				}
			}
			else
			{
				submitform(pressbutton);
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
				<?php if (BwPostmanHelper::canSend((int) $this->item->id)) { ?>
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
			<div class="form-horizontal">
				<fieldset class="adminform">
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
					<div class="well well-small">
						<div class="width-50 fltlft span6 control-group">
							<ul class="adminformlist unstyled">
								<?php foreach($this->form->getFieldset('basic_1') as $field): ?>
									<?php if ($field->hidden): ?>
										<?php echo $field->input; ?>
									<?php else: ?>
										<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div></li>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php foreach($this->form->getFieldset('campaigns') as $field): ?>
									<?php if ($field->hidden): ?>
										<?php echo $field->input; ?>
									<?php else: ?>
										<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div></li>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php foreach($this->form->getFieldset('edit_publish') as $field): ?>
									<?php if ($field->hidden): ?>
										<?php echo $field->input; ?>
									<?php else: ?>
										<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div></li>
									<?php endif; ?>
								<?php endforeach; ?>
								</ul>
						</div>

						<div class="width-50 fltlft span6 control-group">
							<ul class="adminformlist unstyled">
								<?php foreach($this->form->getFieldset('basic_2') as $field): ?>
									<?php if ($field->hidden): ?>
										<li><?php echo $field->input; ?></li>
									<?php else: ?>
										<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div></li>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php if (isset($this->substitute) && $this->substitute === true): ?>
									<li><?php echo $this->form->getLabel('substitute_links') ?>
										<div class="controls"><?php echo $this->form->getInput('substitute_links'); ?></div></li>
								<?php endif; ?>
							</ul>
						</div>
						<div class="clr clearfix"></div>
						<p><span class="required_description"><?php echo JText::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
					</div>
				</fieldset>
			</div>
			<fieldset class="adminform">
				<legend>
					<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_NL_TEMPLATES_NOTE'); ?>">
						<?php echo $image; ?>
					</span>
					<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_TEMPLATES'); ?></span>
				</legend>
				<div class="well">
					<?php foreach($this->form->getFieldset('templates') as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
					<div class="width-33 fltlft span4">
						<div class="well-small well-white">
							<fieldset class="adminform templates">
								<legend><?php echo $field->label; ?></legend>
								<div class="row-fluid clearfix">
									<?php echo $field->input; ?>
								</div>
							</fieldset>
						</div>
					</div>
					<?php endif; ?>
					<?php endforeach; ?>
					<div class="clr clearfix"></div>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<div class="row-fluid">
					<fieldset class="adminform" id="recipients">
						<legend class="required"><?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_RECIPIENTS'); ?> *</legend>
						<div class="well">
							<div class="width-75 fltlft span9">
								<div class="well-white well-small">
									<fieldset class="adminform">
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
										<?php foreach($this->form->getFieldset('mailinglists') as $field): ?>
											<?php if ($field->hidden): ?>
												<?php echo $field->input; ?>
											<?php else: ?>
												<div class="width-33 fltlft span4">
													<div class="well well-small">
														<fieldset class="adminform">
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
																	echo '<div class="width-50 fltlft span6">
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
									</fieldset>
								</div>
							</div>

							<div class="width-25 fltlft span3">
								<div class="well-white well-small">
									<fieldset class="adminform usergroups">
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
									</fieldset>
								</div>
							</div>
							<div class="clr clearfix"></div>
						</div>
					</fieldset>
				</div>

				<div class="row-fluid">
					<div class="well-small">
						<fieldset class="adminform">
							<legend>
								<span class="editlinktip hasTip hasTooltip" title="<?php echo JText::_('COM_BWPOSTMAN_NL_ADD_CONTENT_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<span>&nbsp;<?php echo JText::_('COM_BWPOSTMAN_NL_ASSIGNMENTS_CONTENTS'); ?></span>
							</legend>
							<div class="well well-small">
								<div class="width-40 fltlft span4">
									<ul class="adminformlist unstyled">
										<?php foreach($this->form->getFieldset('selected_content') as $field): ?>
											<?php if ($field->hidden): ?>
												<li><?php echo $field->input; ?></li>
											<?php else: ?>
												<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
													<?php echo $field->input; ?></li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>

								<div class="width-20 fltlft span3">
									<input style="width: 50px;" type="button" name="left" class="btn-left" value="&lt;"
										onclick="moveSelectedOptions(document.adminForm['jform_available_content'],
										document.adminForm['jform_selected_content'])" />
									<input style="width: 50px;" type="button" name="right" class="btn-right" value="&gt;"
										onclick="moveSelectedOptions(document.adminForm['jform_selected_content'],
										document.adminForm['jform_available_content'])" />
								</div>

								<div class="width-40 fltlft span4">
									<ul class="adminformlist unstyled">
										<?php foreach($this->form->getFieldset('available_content') as $field): ?>
											<?php if ($field->hidden): ?>
												<li><?php echo $field->input; ?></li>
											<?php else: ?>
												<li <?php echo 'class="' . $field->name . '"'; ?>><?php echo $field->label; ?>
													<?php echo $field->input; ?></li>
											<?php endif; ?>
										<?php endforeach; ?>
									</ul>
								</div>
								<div class="clr clearfix"></div>
							</div>
						</fieldset>
					</div>
				</div>
			</fieldset>
			<fieldset class="adminform">
				<?php
				?>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_BWPOSTMAN_NL_FIELDSET_RULES'); ?></legend>
				<?php
				if ($this->permissions['com']['admin'] || $this->permissions['admin']['newsletter']): ?>
					<div class="fltlft">
						<div class="well well-small">
							<fieldset class="panelform">
								<?php echo $this->form->getInput('rules'); ?>
							</fieldset>
						</div>
					</div>
				<?php endif; ?>

			</fieldset>
		</div>
		<div class="clearfix"></div>
</div>

		<?php
		foreach($this->form->getFieldset('html_version_hidden') as $field)
		{
			echo $field->input;
		}

		foreach($this->form->getFieldset('text_version_hidden') as $field)
		{
			echo $field->input;
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
