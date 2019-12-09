<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template html for backend.
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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.keepalive');

Factory::getDocument()->addScript(JUri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template_html.js');

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

<div id="bwp_view_lists">
	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=template&layout=default_html&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm">
		<fieldset class="adminform">
			<legend>
				<?php echo Text::_(
					empty($this->item->id) ? Text::_('COM_BWPOSTMAN_NEW_TPL_HTML') : Text::sprintf('COM_BWPOSTMAN_EDIT_TPL_HTML', $this->item->id)
				); ?>
			</legend>
			<div class="row">
				<div class="col-md-5">
					<?php
						echo HTMLHelper::_('uitab.startTabSet', 'template_tabs', $options);
						echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel1', Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'));
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'); ?></legend>
						<div class="well well-small">
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('title'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('title'); ?>
								</div>
							</div>

							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('description'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('description'); ?>
								</div>
							</div>

							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('thumbnail'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('thumbnail'); ?>
								</div>
							</div>
						</div>
					</fieldset>

					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'); ?></legend>
						<div class="well well-small">
							<?php
							foreach ($this->form->getFieldset('jarticle') as $field) :
								$show = array(
									"jform[article][show_title]",
									"jform[article][show_author]",
									"jform[article][show_createdate]",
									"jform[article][show_readon]"
								);
								if (in_array($field->name, $show)) : ?>
									<div class="control-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
									<?php
								endif;
							endforeach; ?>
						</div>
					</fieldset>
					<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel2', Text::_('COM_BWPOSTMAN_TPL_CSS_LABEL'));
					?>
					<fieldset class="panelform">
						<?php echo Text::_('COM_BWPOSTMAN_TPL_CSS_DESC'); ?>
						<?php echo $this->form->getInput('tpl_css'); ?>
					</fieldset>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel3', Text::_('COM_BWPOSTMAN_TPL_HTML_LABEL'));
					?>
					<fieldset class="panelform">
						<?php echo Text::_('COM_BWPOSTMAN_TPL_HTML_DESC'); ?>
							<div class="well well-small">
								<?php echo $this->form->getInput('tpl_html'); ?>
								<?php
								$link = Uri::base() . '#';
								if(PluginHelper::isEnabled('bwpostman', 'personalize')) {
									$button_text = Text::_('COM_BWPOSTMAN_TPL_HTML_PERS_BUTTON');
									$linktexts = array(
										'PERS' => $button_text,
										'[FIRSTNAME]',
										'[LASTNAME]',
										'[FULLNAME]',
										'[%content%]',
										'[%unsubscribe_link%]',
										'[%edit_link%]',
										'[%impressum%]'
									);
								}
								else
								{
									$linktexts = array(
										'[FIRSTNAME]',
										'[LASTNAME]',
										'[FULLNAME]',
										'[%content%]',
										'[%unsubscribe_link%]',
										'[%edit_link%]',
										'[%impressum%]'
									);
								}

								foreach ($linktexts as $key => $linktext) {
									echo "                    <a class=\"btn btn-info btn-small pull-left\"
										onclick=\"buttonClick('" . $linktext . "','jform_tpl_html');
										return false;\" href=\"" . $link . "\">" . $linktext . "</a>";
									echo '                     <p>&nbsp;' . Text::_('COM_BWPOSTMAN_TPL_HTML_DESC' . $key) . '</p>';
								}

								if(PluginHelper::isEnabled('bwpostman', 'personalize'))
								{
									echo Text::_('COM_BWPOSTMAN_TPL_HTML_DESC_PERSONALIZE');
								}
								?>
							</div>
						<div class="clr clearfix"></div>
					</fieldset>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel4', Text::_('COM_BWPOSTMAN_TPL_TAGS_LABEL'));
					?>
					<fieldset class="panelform">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_LABEL'); ?></legend>
						<div><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_DESC'); ?></div>
						<?php echo $this->loadTemplate('tpl_tags'); ?>
					</fieldset>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					if ($this->permissions['com']['admin'] || $this->permissions['admin']['template'])
					{
						echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel5', Text::_('COM_BWPOSTMAN_TPL_FIELDSET_RULES'));
						?>
						<div class="row">
							<div class="col-md-12">
								<fieldset class="adminform">
									<?php echo $this->form->getInput('rules'); ?>
								</fieldset>
							</div>
						</div>
						<?php
						echo HTMLHelper::_('uitab.endTab');
					}

					echo HTMLHelper::_('uitab.endTabSet');
					?>
					<div class="clr clearfix"></div>
					<div class="well-note well-small"><?php echo Text::_('COM_BWPOSTMAN_TPL_USER_NOTE'); ?></div>
				</div>

				<div class="col-md-7">
					<div id="email_preview">
						<p>
							<button class="btn btn-large btn-block btn-primary" type="submit">
								<?php echo Text::_('COM_BWPOSTMAN_TPL_REFRESH_PREVIEW'); ?>
							</button>&nbsp;
						</p>
						<iframe id="myIframe" name="myIframeHtml"
							src="index.php?option=com_bwpostman&amp;view=template&amp;layout=template_preview&amp;format=raw&amp;id=<?php echo $this->item->id; ?>"
							height="800" width="100%" style="border: 1px solid #c2c2c2;">
						</iframe>
						<div class="clr clearfix"></div>
					</div>
				</div>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="nl_method" value="default_html" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('tpl_id', null, 0); ?>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo $this->form->getInput('templates_table_id'); ?>
		<?php echo HTMLHelper::_('form.token'); ?>
		<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

		<input type="hidden" id="cancelText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_CONFIRM_CANCEL', true); ?>" />
		<input type="hidden" id="titleErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_TITLE', true); ?>" />
		<input type="hidden" id="descriptionErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION', true); ?>" />
	</form>
</div>
