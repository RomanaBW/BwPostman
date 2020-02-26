<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit template sub-template intro for backend.
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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

$fieldSets = $this->form->getFieldsets('intro');
foreach ($fieldSets as $name => $fieldSet) :
	?>
	<fieldset class="panelform options-grid-form options-grid-form-full">
		<legend><?php echo $this->escape(Text::_($fieldSet->label)); ?></legend>
		<div>
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<?php echo $field->renderField(); ?>
			<?php endforeach; ?>
			<div class="clr clearfix"></div>
			<div><?php echo Text::_('COM_BWPOSTMAN_TPL_INTRO_TEXT_DESC'); ?></div>
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

			foreach ($linktexts as $key => $linktext)
			{
				echo "                    <div class=\"clearfix mb-2\">";
				echo "                    	<span class=\"btn btn-info btn-sm\" onclick=\"InsertAtCaret('" . $linktext . "');\">" . $linktext . "</span>";
				echo '                    	<span>&nbsp;' . Text::_('COM_BWPOSTMAN_TPL_HTML_DESC' . $key) . '</span>';
				echo '                    </div>';
			}

			if(PluginHelper::isEnabled('bwpostman', 'personalize'))
			{
				echo Text::_('COM_BWPOSTMAN_TPL_HTML_DESC_PERSONALIZE');
			}
			?>
		</div>
		<div class="clr clearfix"></div>
	</fieldset>
<?php endforeach;
