<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template text-std for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

// Load the tooltip behavior for the notes
HtmlHelper::_('bootstrap.tooltip');
HtmlHelper::_('behavior.keepalive');

$image = '<i class="icon-info"></i>';

$options = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
	'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<div id="bwp_view_lists" class="well well-small">
	<?php
	if ($this->queueEntries) {
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=template&layout=default&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<fieldset class="adminform">
			<legend>
				<?php
				$title = Text::_('COM_BWPOSTMAN_NEW_TPL_TEXT');
				if ($this->item->id)
				{
					$title = Text::sprintf('COM_BWPOSTMAN_EDIT_TPL_TEXT', $this->item->id);
				}

				echo $title;
				?>
			</legend>
			<div class="well well-small">
				<div class="fltlft width-40 span5 control-group">
					<?php
						echo HtmlHelper::_('tabs.start', 'template_tabs', $options);
						echo HtmlHelper::_('tabs.panel', Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'), 'panel1');
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<li>
									<?php echo $this->form->getLabel('title'); ?>
									<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
								</li>
								<li>
									<?php echo $this->form->getLabel('description'); ?>
									<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
								</li>
								<li>
									<?php echo $this->form->getLabel('thumbnail'); ?>
									<div class="controls"><?php echo $this->form->getInput('thumbnail'); ?></div>
								</li>
							</ul>
							<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
						</div>
					</fieldset>
					<?php
						echo HtmlHelper::_('tabs.panel', Text::_('COM_BWPOSTMAN_TPL_HEADER_LABEL'), 'panel2');
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_HEADER_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<?php
								foreach ($this->form->getFieldset('jheader') as $field) :
									$show = array("jform[header][firstline]", "jform[header][secondline]");
									if (in_array($field->name, $show)) : ?>
										<li><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div>
										</li>
										<?php
									endif;
								endforeach; ?>
							</ul>
						</div>
					</fieldset>
					<?php
						echo HtmlHelper::_('tabs.panel', Text::_('COM_BWPOSTMAN_TPL_INTRO_LABEL'), 'panel3');
						echo $this->loadTemplate('intro');
						echo HtmlHelper::_('tabs.panel', Text::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'), 'panel4');
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<?php
								foreach ($this->form->getFieldset('jarticle') as $field) :
									$show = array(
										"jform[article][divider]",
										"jform[article][show_title]",
										"jform[article][show_author]",
										"jform[article][show_createdate]",
										"jform[article][show_readon]"
									);
									if (in_array($field->name, $show)) : ?>
										<li><?php echo $field->label; ?>
											<div class="controls clearfix"><?php echo $field->input; ?></div>
										</li>
										<?php
									endif;
								endforeach; ?>
							</ul>
						</div>
					</fieldset>
					<?php
						echo HtmlHelper::_('tabs.panel', Text::_('COM_BWPOSTMAN_TPL_FOOTER_LABEL'), 'panel5');
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_FOOTER_LABEL'); ?></legend>
						<div class="well well-small">
							<ul class="adminformlist unstyled">
								<?php
								foreach ($this->form->getFieldset('jfooter') as $field) :
									$show = array(
											"jform[footer][show_impressum]",
										"jform[footer][spacer3]",
										"jform[footer][show_address]",
										"jform[footer][address_text]",
										"jform[footer][button_headline]"
									);
									if (in_array($field->name, $show)) : ?>
										<li><?php echo $field->label; ?>
											<div class="controls"><?php echo $field->input; ?></div>
										</li>
										<?php
									endif;
								endforeach;
								// begin footer buttons
								$i = 1;

								echo '  <li><div class="clr clearfix"></div>';
								echo HtmlHelper::_('tabs.start', 'buttons', array('startOffset' => 0));

								while ($i <= 5) :
									$fieldSets = $this->form->getFieldsets('button' . $i);
									foreach ($fieldSets as $name => $fieldSet) :
										echo HtmlHelper::_('tabs.panel', Text::_($fieldSet->label) . ' ' . $i, 'bpanel' . $i);
										?>
											<fieldset class="panelform">
												<legend><?php echo $this->escape(Text::_($fieldSet->label)) . ' ' . $i; ?></legend>
												<div class="well well-small">
													<ul class="adminformlist unstyled">
													<?php
													foreach ($this->form->getFieldset($name) as $field) :
														$show = array(
															"jform[button$i][show_button]",
															"jform[button$i][button_text]",
															"jform[button$i][button_href]"
														);
														if (in_array($field->name, $show)) : ?>
															<li><?php echo $field->label; ?>
																<div class="controls"><?php echo $field->input; ?></div>
															</li>
															<?php
														endif;
													endforeach; ?>
													</ul>
												</div>
											</fieldset>
											<?php
									endforeach;

									$i++;
								endwhile;
								echo HtmlHelper::_('tabs.end');
								echo '  </li>';
								?>
							</ul>
						</div>
					</fieldset>
					<?php
						echo HtmlHelper::_('tabs.end');
					?>
					<div class="clr clearfix"></div>
					<div class="well-note well-small"><?php echo Text::_('COM_BWPOSTMAN_TPL_USER_NOTE'); ?></div>
				</div>
				<div id="email_preview" class="fltlft span7">
					<p>
						<button class="btn btn-large btn-block btn-primary"
								type="submit"><?php echo Text::_('COM_BWPOSTMAN_TPL_REFRESH_PREVIEW'); ?></button>&nbsp;
					</p>
					<iframe id="myIframe" name="myIframeHtml"
							src="index.php?option=com_bwpostman&amp;view=template&amp;layout=template_preview&amp;format=raw&amp;id=<?php echo $this->item->id; ?>"
							width="100%" style="border: 1px solid #c2c2c2; min-height:200px;">
					</iframe>
				</div>
				<div class="clr clearfix"></div>
			</div>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('tpl_id'); ?>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo HtmlHelper::_('form.token'); ?>

		<input type="hidden" id="cancelText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_CONFIRM_CANCEL', true); ?>" />
		<input type="hidden" id="titleErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_TITLE', true); ?>" />
		<input type="hidden" id="descriptionErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION', true); ?>" />

		<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
	</form>
</div>

<?php
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template.js');
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template_base.js');
