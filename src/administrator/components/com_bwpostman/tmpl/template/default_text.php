<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template text for backend.
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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.keepalive');

?>

<div id="bwp_view_lists">
	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=template&layout=default_text&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm">
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
			<div class="row">
				<div class="col-xl-6">
					<?php
					echo HTMLHelper::_('uitab.startTabSet', 'template_tabs', ['active' => 'panel1']);
					echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel1', Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'));
					?>
					<fieldset class="panelform options-grid-form options-grid-form-full">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_BASICS_LABEL'); ?></legend>
						<div>
							<?php echo $this->form->renderField('title'); ?>
							<?php echo $this->form->renderField('description'); ?>
							<?php echo $this->form->renderField('thumbnail'); ?>
						</div>
					</fieldset>

					<fieldset class="panelform options-grid-form options-grid-form-full">
						<legend><?php echo Text::_('COM_BWPOSTMAN_TPL_ARTICLE_LABEL'); ?></legend>
						<div>
							<?php
							foreach ($this->form->getFieldset('jarticle') as $field) :
								$show = array(
									"jform[article][show_title]",
									"jform[article][show_author]",
									"jform[article][show_createdate]",
									"jform[article][show_readon]"
								);
								if (in_array($field->name, $show)) : ?>
									<?php echo $field->renderField(); ?>
									<?php
								endif;
							endforeach; ?>
						</div>
					</fieldset>
					<p><span class="required_description"><?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?></span></p>
					<?php
					echo HTMLHelper::_('uitab.endTab');

					echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel2', Text::_('COM_BWPOSTMAN_TPL_TEXT_LABEL'));
					?>
					<fieldset class="panelform card card-body">
						<?php echo Text::_('COM_BWPOSTMAN_TPL_TEXT_DESC'); ?>
						<div>
							<div class="clearfix mb-3">
								<textarea id="jform_tpl_html" rows="20" cols="50" name="jform[tpl_html]" title="jform[tpl_html]"
									style="width: 95%;"><?php echo htmlspecialchars($this->item->tpl_html, ENT_COMPAT, 'UTF-8'); ?></textarea>
							</div>
							<?php
							$link = Uri::base() . '#';
							if(PluginHelper::isEnabled('bwpostman', 'personalize'))
							{
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

							foreach ($linktexts as $key => $linktext)
							{
								echo "                    <div class=\"clearfix mb-2\">";
								echo "                    <a class=\"btn btn-info btn-sm\"
									onclick=\"buttonClick4('" . $linktext . "','jform_tpl_html');
									return false;\" href=\"" . $link . "\">" . $linktext . "</a>";
								echo '                     <span>' . Text::_('COM_BWPOSTMAN_TPL_HTML_DESC' . $key) . '</span>';
								echo '                     </div>';
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

					if ($this->permissions['com']['admin'] || $this->permissions['admin']['template'])
					{
						echo HTMLHelper::_('uitab.addTab', 'template_tabs', 'panel3', Text::_('COM_BWPOSTMAN_TPL_FIELDSET_RULES')); ?>
						<div class="options-grid-form options-grid-form-full com_config">
							<?php echo $this->form->getInput('rules'); ?>
						</div>
						<?php
						echo HTMLHelper::_('uitab.endTab');
					}

					echo HTMLHelper::_('uitab.endTabSet');
					?>
					<div class="clr clearfix"></div>
					<div class="alert alert-danger"><?php echo Text::_('COM_BWPOSTMAN_TPL_USER_NOTE'); ?></div>
				</div>

				<div class="col-xl-6">
					<div id="email_preview" class="clearfix">
						<p>
							<button class="btn btn-large btn-block btn-primary" type="submit">
								<?php echo Text::_('COM_BWPOSTMAN_TPL_REFRESH_PREVIEW'); ?>
							</button>&nbsp;
						</p>
						<iframe id="myIframe" class="bg-white" name="myIframeHtml"
								src="index.php?option=com_bwpostman&amp;view=template&amp;layout=template_preview&amp;format=raw&amp;id=<?php echo $this->item->id; ?>"
								height="800" width="100%" style="border: 1px solid #c2c2c2;">
						</iframe>
					</div>
				</div>
			</div>
		</fieldset>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="nl_method" value="default_text" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('tpl_id', null, 998); ?>
		<?php echo $this->form->getInput('checked_out'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo HTMLHelper::_('form.token'); ?>
		<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

		<input type="hidden" id="cancelText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_CONFIRM_CANCEL', true); ?>" />
		<input type="hidden" id="titleErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_TITLE', true); ?>" />
		<input type="hidden" id="descriptionErrorText" value="<?php echo Text::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION', true); ?>" />
	</form>
</div>

<?php
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template_text_buttonClick.js');
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template_base.js');
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_template.js');
