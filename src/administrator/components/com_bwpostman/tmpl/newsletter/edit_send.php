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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$image = '<i class="fa fa-lg fa-info-circle"></i>';

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
		<div class="bwp-newsletter">
			<ul class="nav nav-tabs bwp-tabs">
				<li class="nav-item">
					<a id="tab-edit_basic" href="#" onclick="changeTab('edit_basic', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP1'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_html" href="#" onclick="changeTab('edit_html', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP2'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_text" href="#" onclick="changeTab('edit_text', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</a>
				</li>
				<li class="nav-item">
					<a id="tab-edit_preview" href="#" onclick="changeTab('edit_preview', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</a>
				</li>
				<?php if (BwPostmanHelper::canSend((int) $this->item->id) && !$this->item->is_template) { ?>
					<li class="nav-item">
						<a id="tab-edit_send" href="#" onclick="changeTab('edit_send', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link active">
							<?php echo Text::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>

			<div class="tab-wrapper">
				<?php if (!property_exists($this->item, 'scheduled_date') || $this->item->scheduled_date === '' || $this->item->scheduled_date === null)
				{ ?>
					<div class="card card-body mb-2">
						<div class="row">
							<div class="col-12 mb-2">
								<div class=“h3“>
									<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL'); ?>
								</div>
								<div class="clearfix mb-3">
									<?php echo $image_newsletter; ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS'); ?>
								</div>
								<div class="clearfix mb-3">
									<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_RECIPIENTS_NOTE'); ?>
									<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_OPTIONS');?>
								</div>
								<div class="form-check form-check-inline clearfix mb-3">
									<input type="checkbox" id="send_to_unconfirmed" class="form-check-input" name="send_to_unconfirmed" />&nbsp;
									<label class="form-check-label form-control-plaintext" for="send_to_unconfirmed"><?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_UNCONFIRMED');?></label>
								</div>
								<div class="form-inline mb-3">
									<input type="text" class="form-control inputbox mr-2" name="mails_per_pageload" id="mails_per_pageload" title="mails_per_pageload"
											size="4" maxlength="10" value="<?php echo $this->params->get('default_mails_per_pageload');?>" />
									<span class="hasTooltip"
											title="<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD_NOTE'); ?>">
									<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_MAILS_PER_PAGELOAD'); ?>&nbsp;
									<?php echo $image; ?>
								</span>
								</div>
								<?php if (BwPostmanHelper::canSend((int) $this->item->id)) : ?>
									<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendmail');"
											value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_BUTTON'); ?>" />
									<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendmailandpublish');"
											value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>"
											title="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL_AND_PUBLISH_BUTTON'); ?>" />
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="card card-body mb-2">
					<div class="row">
						<div class="col-12 mb-2">
							<div class=“h3“>
								<?php echo Text::_('COM_BWPOSTMAN_NL_SENDTESTMAIL'); ?>
							</div>
							<div class="clearfix mb-3">
								<?php echo $image_testrecipients; ?>
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS'); ?>
							</div>
							<div class="clearfix mb-3">
								<?php echo Text::_('COM_BWPOSTMAN_NL_SEND_TO_TESTRECIPIENTS_NOTE'); ?>
							</div>
							<?php if (BwPostmanHelper::canSend((int) $this->item->id)) : ?>
								<input class="btn btn-info" type="button" onclick="Joomla.submitbutton('newsletter.sendtestmail');"
										value="<?php echo Text::_('COM_BWPOSTMAN_NL_SENDTESTMAIL_BUTTON'); ?>" />
							<?php endif; ?>
						</div>
					</div>
				</div>
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

			<input type="hidden" id="confirmSend" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING', true); ?>" />
			<input type="hidden" id="confirmSendPublish" value="<?php echo Text::_('COM_BWPOSTMAN_NL_CONFIRM_SENDING_AND_PUBLISH', true); ?>" />
		</div>
	</form>
</div>

