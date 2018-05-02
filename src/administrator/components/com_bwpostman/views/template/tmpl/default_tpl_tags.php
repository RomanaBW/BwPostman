<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template tpl_tags for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

// No direct access.
defined('_JEXEC') or die;

echo JHtml::_('tabs.start', 'tpl_tags', array('startOffset' => 0));
echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_TAGS_HEAD_LABEL'), 'panel1');
echo JText::_('COM_BWPOSTMAN_TPL_HEAD_DESC');
?>
<fieldset class="panelform">
	<ul class="adminformlist unstyled">
		<li>
			<?php echo $this->form->getLabel('tpl_tags_head'); ?>
			<div class="controls"><?php echo $this->form->getInput('tpl_tags_head'); ?></div>
		</li>
		<li>
			<p><label><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?></label></p>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->headTag)); ?></div>
		</li>
		<li>
			<p><?php echo $this->form->getLabel('tpl_tags_head_advanced'); ?></p>
			<?php echo $this->form->getInput('tpl_tags_head_advanced'); ?>
		</li>
	</ul>
</fieldset>
<?php
echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_TAGS_BODY_LABEL'), 'panel2');
echo JText::_('COM_BWPOSTMAN_TPL_HEAD_DESC');
?>
<fieldset class="panelform">
	<ul class="adminformlist unstyled">
		<li>
			<?php echo $this->form->getLabel('tpl_tags_body'); ?>
			<div class="controls"><?php echo $this->form->getInput('tpl_tags_body'); ?></div>
		</li>
		<li>
			<p><label><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?></label></p>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->bodyTag)); ?></div>
		</li>
		<li>
			<p><?php echo $this->form->getLabel('tpl_tags_body_advanced'); ?></p>
			<?php echo $this->form->getInput('tpl_tags_body_advanced'); ?>
		</li>
	</ul>
</fieldset>
<?php
echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_LABEL'), 'panel3');
echo JText::_('COM_BWPOSTMAN_TPL_ARTICLE_DESC');
?>
<fieldset class="panelform">
	<ul class="adminformlist unstyled">
		<li>
			<?php echo $this->form->getLabel('tpl_tags_article'); ?>
			<div class="controls"><?php echo $this->form->getInput('tpl_tags_article'); ?></div>
		</li>
		<li>
			<p><label><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?></label></p>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->articleTagBegin)); ?></div>
		</li>
		<li>
			<p><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_INFO'); ?></p>
		</li>
		<li>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->articleTagEnd)); ?></div>
		</li>
		<li>
			<p><?php echo $this->form->getLabel('tpl_tags_article_advanced_b'); ?></p>
			<?php echo $this->form->getInput('tpl_tags_article_advanced_b'); ?>
		</li>
		<li>
			<p><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_ARTICLE_INFO'); ?></p>
		</li>
		<li>
			<?php echo $this->form->getInput('tpl_tags_article_advanced_e'); ?>
		</li>
	</ul>
</fieldset>
<?php
echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_TAGS_READON_LABEL'), 'panel4');
echo JText::_('COM_BWPOSTMAN_TPL_READON_DESC');
?>
<fieldset class="panelform">
	<ul class="adminformlist unstyled">
		<li>
			<?php echo $this->form->getLabel('tpl_tags_readon'); ?>
			<div class="controls"><?php echo $this->form->getInput('tpl_tags_readon'); ?></div>
		</li>
		<li>
			<p><label><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?></label></p>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->readonTag)); ?></div>
		</li>
		<li>
			<p><?php echo $this->form->getLabel('tpl_tags_readon_advanced'); ?></p>
			<?php echo $this->form->getInput('tpl_tags_readon_advanced'); ?>
		</li>
	</ul>
</fieldset>
<?php
echo JHtml::_('tabs.panel', JText::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_LABEL'), 'panel5');
echo JText::_('COM_BWPOSTMAN_TPL_LEGAL_DESC');
?>
<fieldset class="panelform">
	<ul class="adminformlist unstyled">
		<li>
			<?php echo $this->form->getLabel('tpl_tags_legal'); ?>
			<div class="controls"><?php echo $this->form->getInput('tpl_tags_legal'); ?></div>
		</li>
		<li>
			<p><label><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_STANDARD_LABEL'); ?></label></p>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->legalTagBegin)); ?></div>
		</li>
		<li>
			<p><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_INFO'); ?></p>
		</li>
		<li>
			<div class="textarea inputbox"><?php echo nl2br(htmlentities($this->legalTagEnd)); ?></div>
		</li>
		<li>
			<p><?php echo $this->form->getLabel('tpl_tags_legal_advanced_b'); ?></p>
			<?php echo $this->form->getInput('tpl_tags_legal_advanced_b'); ?>
		</li>
		<li>
			<p><?php echo JText::_('COM_BWPOSTMAN_TPL_TAGS_LEGAL_INFO'); ?></p>
		</li>
		<li>
			<?php echo $this->form->getInput('tpl_tags_legal_advanced_e'); ?>
		</li>
	</ul>
</fieldset>

<?php
echo JHtml::_('tabs.end');

