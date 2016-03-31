<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template intro for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
$fieldSets = $this->form->getFieldsets('intro');
foreach ($fieldSets as $name => $fieldSet) :
	?>
	<fieldset class="panelform">
		<legend><?php echo $this->escape(JText::_($fieldSet->label)); ?></legend>
		<div class="well well-small">
			<ul class="adminformlist unstyled">
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<li><?php echo $field->label; ?>
						<div class="controls"><?php echo $field->input; ?></div>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="clr clearfix"></div>
			<div><?php echo JText::_('COM_BWPOSTMAN_TPL_INTRO_TEXT_DESC'); ?></div>
			<?php
				$link = JURI::base() . '#';
				$linktexts = array('[FIRSTNAME]', '[LASTNAME]', '[FULLNAME]');
				foreach ($linktexts as $key => $linktext) {
					echo "                    <a class=\"btn btn-small pull-left\" onclick=\"buttonClick('jform_intro_intro_text', '" . $linktext . "');return false;\" href=\"" . $link . "\">" . $linktext . "</a>";
					echo '                     <p>&nbsp;'.JText::_('COM_BWPOSTMAN_TPL_HTML_DESC'.$key).'</p>';
				}
			?>
		</div>
		<div class="clr clearfix"></div>
	</fieldset>
<?php endforeach;
?>
