<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter edit html template for backend.
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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

HTMLHelper::_('behavior.keepalive');

$currentTab = 'edit_html';
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
					<a id="tab-edit_html" href="#" onclick="changeTab('edit_html', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link active">
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
						<a id="tab-edit_send" href="#" onclick="changeTab('edit_send', '<?php echo $currentTab; ?>');Joomla.submitbutton();" class="nav-link">
							<?php echo Text::_('COM_BWPOSTMAN_NL_STP5'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>

			<div class="tab-wrapper">
				<div class="card card-body mb-2">
					<div class="row">
						<div class="col-12 mb-2">
							<div class="h3">
								<?php echo Text::_('COM_BWPOSTMAN_NL_HTML'); ?>
							</div>
							<?php
							foreach($this->form->getFieldset('html_version') as $field):
								// if old template - show no intro fields
								if (empty($this->item->intro_headline) && empty($this->item->intro_text))
								{
									$show = array("jform[html_version]");
								}
								else
								{
									$show = array("jform[html_version]", "jform[intro_headline]", "jform[intro_text]");
								}

								if (in_array($field->name, $show)) : ?>
									<?php
									if ($field->hidden):
										echo $field->input;
									else:
										if ($field->name == 'jform[html_version]')
										{ ?>
											<?php echo $field->label; ?>
											<?php echo Text::_('COM_BWPOSTMAN_NL_PERSONALISATION_NOTE'); ?>
											<div class="row">
												<div class="col-12 mb-2">
													<?php echo $field->input; ?>
												</div>
											</div>
											<?php
											$link = '#';
											if(PluginHelper::isEnabled('bwpostman', 'personalize')) {
												$button_text = Text::_('COM_BWPOSTMAN_TPL_HTML_PERS_BUTTON');
												$linktexts = array('PERS' => $button_text, '[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]');
											}
											else
											{
												$linktexts = array('[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]');
											}
											foreach ($linktexts as $key => $linktext)
											{
												echo "                    <div class=\"clearfix mb-2\"><a class=\"btn btn-info btn-sm\"
												onclick=\"buttonClick('" . $linktext . "','jform_html_version');return false;\"
												href=\"" . $link . "\">" . $linktext . "</a>";
												echo '                     <span>&nbsp;' . Text::_('COM_BWPOSTMAN_TPL_HTML_DESC' . $key) . '</span></div>';
											}
											if(PluginHelper::isEnabled('bwpostman', 'personalize'))
											{
												echo Text::_('COM_BWPOSTMAN_TPL_HTML_DESC_PERSONALIZE');
											}
											?>

											<?php
										}
										else
										{
											echo $field->renderField();
										}
									endif;
								endif;
							endforeach; ?>
						<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>

						<?php
						$hiddenFieldsets = array(
							'basic_1_hidden',
							'basic_2_hidden',
							'text_version_hidden',
							'templates_hidden',
							'campaigns_hidden',
							'selected_content_hidden',
							'available_content_hidden',
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
						</div>
					</div>
				</div>
			</div>
			<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

			<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" id="layout" name="layout" value="edit_html" /><!-- value never changes -->
			<input type="hidden" name="tab" value="edit_html" /><!-- value can change if one clicks on another tab -->
			<input type="hidden" id="template_id_old" name="template_id_old" value="<?php echo $this->template_id_old; ?>" />
			<input type="hidden" id="text_template_id_old" name="text_template_id_old" value="<?php echo $this->text_template_id_old; ?>" />
			<input type="hidden" name="add_content" value="" />
			<input type="hidden" id="selected_content_old" name="selected_content_old" value="<?php echo $this->selected_content_old; ?>" />
			<input type="hidden" id="content_exists" name="content_exists" value="<?php echo $this->content_exists; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
