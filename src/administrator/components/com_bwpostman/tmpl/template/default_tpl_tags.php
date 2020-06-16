<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template tpl_tags for backend.
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

// No direct access.
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

echo HTMLHelper::_('uitab.startTabSet', 'tpl_tags', array('startOffset' => 0));
echo HTMLHelper::_('uitab.addTab', 'tpl_tags', 'tpl_tag1', Text::_('COM_BWPOSTMAN_TPL_TAGS_HEAD_LABEL'));
?>
<fieldset class="panelform card-body">
	<?php echo Text::_('COM_BWPOSTMAN_TPL_HEAD_DESC'); ?>
	<?php echo $this->form->renderField('tpl_tags_head'); ?>

	<div class="control-group">
		<label>
			<?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?>
		</label>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->headTag)); ?></div>
	</div>

	<div class="control-group">
		<?php echo $this->form->getLabel('tpl_tags_head_advanced'); ?>
		<?php echo $this->form->getInput('tpl_tags_head_advanced'); ?>
	</div>
</fieldset>
<?php
echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.addTab', 'tpl_tags', 'tpl_tag2', Text::_('COM_BWPOSTMAN_TPL_TAGS_BODY_LABEL'));
?>
<fieldset class="panelform card-body">
	<?php echo Text::_('COM_BWPOSTMAN_TPL_HEAD_DESC'); ?>
	<?php echo $this->form->renderField('tpl_tags_body'); ?>

	<div class="control-group">
			<label>
				<?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?>
			</label>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->bodyTag)); ?></div>
	</div>

	<div class="control-group">
		<?php echo $this->form->getLabel('tpl_tags_body_advanced'); ?>
		<?php echo $this->form->getInput('tpl_tags_body_advanced'); ?>
	</div>
</fieldset>
<?php
echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.addTab', 'tpl_tags', 'tpl_tag3', Text::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_LABEL'));
?>
<fieldset class="panelform card-body">
	<?php echo Text::_('COM_BWPOSTMAN_TPL_ARTICLE_DESC'); ?>
	<?php echo $this->form->renderField('tpl_tags_article'); ?>

	<div class="control-group">
		<label>
			<?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?>
		</label>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->articleTagBegin)); ?></div>
		<div class="my-2"><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_INFO'); ?></div>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->articleTagEnd)); ?></div>
	</div>

	<div class="control-group">
		<?php echo $this->form->getLabel('tpl_tags_article_advanced_b'); ?>
		<?php echo $this->form->getInput('tpl_tags_article_advanced_b'); ?>
		<div class="my-2"><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_INFO'); ?></div>
		<?php echo $this->form->getInput('tpl_tags_article_advanced_e'); ?>
	</div>
</fieldset>
<?php
echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.addTab', 'tpl_tags', 'tpl_tag4', Text::_('COM_BWPOSTMAN_TPL_TAGS_READON_LABEL'));
?>
<fieldset class="panelform card-body">
	<?php echo Text::_('COM_BWPOSTMAN_TPL_READON_DESC'); ?>
	<?php echo $this->form->renderField('tpl_tags_readon'); ?>

	<div class="control-group">
		<label>
			<?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?>
		</label>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->readonTag)); ?></div>
	</div>

	<div class="control-group">
		<?php echo $this->form->getLabel('tpl_tags_readon_advanced'); ?>
		<?php echo $this->form->getInput('tpl_tags_readon_advanced'); ?>
	</div>
</fieldset>
<?php
echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.addTab', 'tpl_tags', 'tpl_tag5', Text::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_LABEL'));
?>
<fieldset class="panelform card-body">
	<?php echo Text::_('COM_BWPOSTMAN_TPL_LEGAL_DESC'); ?>
	<?php echo $this->form->renderField('tpl_tags_legal'); ?>

	<div class="control-group">
		<label>
			<?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?>
		</label>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->legalTagBegin)); ?></div>
		<div class="my-2"><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_INFO'); ?></div>
		<div class="textarea inputbox form-control h-auto readonly w-100"><?php echo nl2br(htmlentities($this->legalTagEnd)); ?></div>
	</div>

	<div class="control-group">
		<?php echo $this->form->getLabel('tpl_tags_legal_advanced_b'); ?>
			<?php echo $this->form->getInput('tpl_tags_legal_advanced_b'); ?>
		<div class="my-2"><?php echo Text::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_INFO'); ?></div>
		<?php echo $this->form->getInput('tpl_tags_legal_advanced_e'); ?>
	</div>
</fieldset>

<?php
echo HTMLHelper::_('uitab.endTab');

echo HTMLHelper::_('uitab.endTabSet');


