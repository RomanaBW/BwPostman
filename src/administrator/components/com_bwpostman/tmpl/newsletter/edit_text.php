<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit text template for backend.
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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

$image = HTMLHelper::_('image', 'administrator/templates/' . $this->template . '/images/menu/icon-16-info.png', Text::_('COM_BWPOSTMAN_NOTES'));

$currentTab = 'edit_text';
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
				<li class="open">
					<button onclick="return changeTab('edit_text', '<?php echo $currentTab; ?>');" class="buttonAsLink_open">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP3'); ?>
					</button>
				</li>
				<li class="closed">
					<button onclick="return changeTab('edit_preview', '<?php echo $currentTab; ?>');" class="buttonAsLink">
						<?php echo Text::_('COM_BWPOSTMAN_NL_STP4'); ?>
					</button>
				</li>
				<?php if (BwPostmanHelper::canSend((int) $this->item->id) && !$this->item->is_template) { ?>
					<li class="closed">
						<button onclick="return changeTab('edit_send', '<?php echo $currentTab; ?>');" class="buttonAsLink">
							<?php echo Text::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</button>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="clr clearfix"></div>

		<div class="tab-wrapper-bwp">
			<fieldset class="adminform form-horizontal">
				<legend><?php echo Text::_('COM_BWPOSTMAN_NL_TEXT'); ?></legend>
				<div class="row">
					<div class="col-md-12">
						<?php echo '<div>' . Text::_('COM_BWPOSTMAN_NL_PERSONALISATION_NOTE') . '</div>'; ?>
						<?php
						foreach($this->form->getFieldset('text_version') as $field):
							// if old template - show no intro fields
							if (empty($this->item->intro_text_headline) && empty($this->item->intro_text_text))
							{
								$show = array("jform[text_version]");
							}
							else
							{
								$show = array("jform[text_version]", "jform[intro_text_headline]", "jform[intro_text_text]");
							}

							if (in_array($field->name, $show)) :
								if ($field->hidden) :
									echo $field->input;
								else :
									if ($field->name == 'jform[text_version]') { ?>
										<?php echo $field->label; ?>
										<div class="row"><?php echo $field->input; ?></div>
										<div class="row">
											<div class="col-md-12">
												<br />
												<?php
												$link = Uri::base() . '#';
												if(PluginHelper::isEnabled('bwpostman', 'personalize'))
												{
													$button_text = Text::_('COM_BWPOSTMAN_TPL_HTML_PERS_BUTTON');
													$linktexts = array('PERS' => $button_text, '[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]');
												}
												else
												{
													$linktexts = array('[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]');
												}

												foreach ($linktexts as $key => $linktext) {
													echo "                    <a class=\"btn btn-info btn-small pull-left\"
													 onclick=\"InsertAtCaret('" . $linktext . "');\">" . $linktext . "</a>";
													echo '                     <p>&nbsp;' . Text::_('COM_BWPOSTMAN_TPL_HTML_DESC' . $key) . '</p>';
												}

												if(PluginHelper::isEnabled('bwpostman', 'personalize'))
												{
													echo Text::_('COM_BWPOSTMAN_TPL_HTML_DESC_PERSONALIZE');
												}
												?>
											</div>
										</div>
										<?php
									}
									else
									{ ?>
										<div class="control-group">
											<div class="control-label">
												<?php echo $field->label; ?>
											</div>
											<div class="controls">
												<?php echo $field->input; ?>
											</div>
										</div>
									<?php
									}
								endif;
							endif;
						endforeach; ?>
					</div>
					<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
				</div>
				<div class="clr clearfix"></div>

				<?php
				$hiddenFieldsets = array(
					'basic_1_hidden',
					'basic_2_hidden',
					'html_version_hidden',
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

			</fieldset>
		</div>
		<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="layout" name="layout" value="edit_text" /><!-- value never changes -->
		<input type="hidden" name="tab" value="edit_text" /><!-- value can change if one clicks on another tab -->
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
				document.adminForm.task.setAttribute('value', 'newsletter.apply');
				Joomla.submitform(pressbutton, form);
				return;
			}

			if (pressbutton === 'newsletter.save' || pressbutton === 'newsletter.save2new' || pressbutton === 'newsletter.save2copy') {
				document.adminForm.task.setAttribute('value', 'newsletter.save');
				Joomla.submitform(pressbutton, form);
			}
		};
	}
	/* ]]> */
</script>
