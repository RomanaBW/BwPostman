<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit send template for backend.
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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$image = '<i class="icon-info"></i>';

$image_testrecipients	= HTMLHelper::_(
	'image',
	'administrator/components/com_bwpostman/assets/images/send.png',
	Text::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS')
);
$image_newsletter		= HTMLHelper::_(
	'image',
	'administrator/components/com_bwpostman/assets/images/send_f2.png',
	Text::_('COM_BWPOSTMAN_NL_SENDMAIL')
);

$currentTab = 'edit_send';
?>

<div id="bwp_view_single">
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="item-form">
		<?php
		if ($this->item->is_template)
		{
			Factory::$application->enqueueMessage(Text::_("COM_BWPOSTMAN_NL_IS_TEMPLATE_INFO"), "Notice");
		}
		?>
		<div class="form-horizontal">
			<ul class="bwp_tabs">
				<li class="closed">
					<button onclick="return changeTab('edit_basic', '<?php echo $currentTab; ?>');" class="buttonAsLink">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP1'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_html', '<?php echo $currentTab; ?>');" class="buttonAsLink">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP2'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_text', '<?php echo $currentTab; ?>');" class="buttonAsLink">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_preview', '<?php echo $currentTab; ?>');" class="buttonAsLink">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</button>
				</li>
				<?php if (BwPostmanHelper::canSend((int) $this->item->id) && !$this->item->is_template) { ?>
					<li class="open">
						<button onclick="return changeTab('edit_send', '<?php echo $currentTab; ?>');" class="buttonAsLink_open">
							<?php echo Text::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</button>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="clr clearfix"></div>

		<div class="tab-wrapper-bwp">
			<?php if (!property_exists($this->item, 'scheduled_date') || $this->item->scheduled_date === '' || $this->item->scheduled_date === null)
				{ ?>
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL'); ?></legend>
				<div class="row">
					<div class="col-md-12">
						<table class="admintable">
						<tr valign="top">
							<td width="40"><?php echo $image_newsletter; ?></td>
							<td>
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS'); ?>
								<br /><br />
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS_NOTE'); ?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><?php echo Text::_('COM_BWPOSTMAN_NL_SEND_OPTIONS');?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<label class="checkbox"></label>
								<input type="checkbox" id="send_to_unconfirmed" name="send_to_unconfirmed" />&nbsp;
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_UNCONFIRMED');?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input class="input-mini inputbox" name="mails_per_pageload" id="mails_per_pageload" title="mails_per_pageload"
										size="4" maxlength="10" value="<?php echo $this->params->get('default_mails_per_pageload');?>" />
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD'); ?>&nbsp;
								<span class="hasTip"
										title="<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD_NOTE'); ?>">
									<?php echo $image; ?>
								</span>
								<br /><br />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php if (BwPostmanHelper::canSend((int) $this->item->id)) : ?>
									<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendmail');"
										value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_BUTTON'); ?>" />
									<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendmailandpublish');"
											value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>"
											title="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>" />
								<?php endif; ?>
							</td>
						</tr>
					</table>
					</div>
				</div>
			</fieldset>
			<?php } ?>

			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_BWPOSTMAN_NL_SENDTESTMAIL'); ?></legend>
				<div class="row">
					<div class="col-md-12">
						<table class="admintable">
						<tr valign="top">
							<td width="40"><?php echo $image_testrecipients; ?></td>
							<td><?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS'); ?>
								<br /><br />
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS_NOTE'); ?>
								<br /><br />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<?php if (BwPostmanHelper::canSend((int) $this->item->id)) : ?>
									<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendtestmail');"
										value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDTESTMAIL_BUTTON'); ?>" />
								<?php endif; ?>
							</td>
						</tr>
					</table>
					</div>
				</div>
			</fieldset>
		</div>

		<?php
		$hiddenFieldsets = array(
			'basic_1_hidden',
			'basic_2_hidden',
			'html_version_hidden',
			'text_version_hidden',
			'templates_hidden',
			'selected_content_hidden',
			'selected_content_hidden',
			'publish_hidden',
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
		<input type="hidden" id="layout" name="layout" value="edit_send" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_send" /><!-- value can change if one clicks on another tab -->
		<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
		<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
		<input type="hidden" name="add_content" value="" />
		<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
		<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>

<script type="text/javascript">
	/* <![CDATA[ */

	window.onload = function() {
// This function stay here instead of external JS file to get nearly free of parameters on buttons
		Joomla = window.Joomla || {};

		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton === 'newsletter.cancel') {
				Joomla.submitform(pressbutton, form);
				return;
			}

			if (pressbutton === 'newsletter.back') {
				form.task.value = 'back';
				Joomla.submitform(pressbutton, form);
				return;
			}

			if (pressbutton === 'newsletter.apply') {
				form.task.setAttribute('value', 'newsletter.apply');
				Joomla.submitform(pressbutton, form);
				return;
			}

			if (pressbutton === 'newsletter.save' || pressbutton === 'newsletter.apply' || pressbutton === 'newsletter.save2new' || pressbutton === 'newsletter.save2copy') {
				form.task.setAttribute('value', pressbutton);
				Joomla.submitform(pressbutton, form);
				return;
			}

			if (pressbutton === 'newsletter.sendmail') {
				confirmSendNl = confirm("<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING', true); ?>");
				if (confirmSendNl === true) {
					form.task.setAttribute('value', 'newsletter.sendmail');
					Joomla.submitform(pressbutton, form);
				}
			}

			if (pressbutton === 'newsletter.sendmailandpublish') {
				confirmSendNl = confirm("<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING_AND_PUBLISH', true); ?>");
				if (confirmSendNl === true) {
					form.task.setAttribute('value', 'newsletter.sendmail');
					Joomla.submitform(pressbutton, form);
				}
			}

			if (pressbutton === 'newsletter.sendtestmail') {
				confirmSendNl = confirm("<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING', true); ?>");
				if (confirmSendNl === true) {
					form.task.setAttribute('value', 'newsletter.sendmail');
					Joomla.submitform(pressbutton, form);
				}
			}
		};
	}
	/* ]]> */
</script>
