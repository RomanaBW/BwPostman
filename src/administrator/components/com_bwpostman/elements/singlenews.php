<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element to select a single newsletter for a view in frontend.
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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

/**
 * Renders a newsletter element
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 *
 * @since       1.0.8
 */

class JFormFieldsinglenews extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 *
	 * @since       1.0.8
	*/
	var	$_name = 'Subject';

	/**
	 * Method to get form input field
	 *
	 * @return string
	 *
	 * @since       1.0.8
	 */
	protected function getinput()
	{
		$doc       = Factory::getDocument();
		$fieldName = $this->name;

		Table::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_bwpostman/tables');

		$newsletter = Table::getInstance('newsletters', 'BwPostmanTable');

		if ($this->value)
		{
			$newsletter->load($this->value);
		}
		else {
			$newsletter->subject = Text::_('COM_BWPOSTMAN_SELECT_NEWSLETTER');
		}

		// The active newsletter id field.
		if ((int)$this->value > 0)
		{
			$value = (int)$this->value;
		}
		else
		{
			$value = '';
		}

		$link = 'index.php?option=com_bwpostman&amp;view=newsletterelement&amp;tmpl=component';
		HTMLHelper::_('behavior.modal', 'a.modal');

		$js = "
			function SelectNewsletter(id, subject) {
				document.getElementById('a_id').value = id;
				document.getElementById('a_name').value = subject;
				var btn = window.parent.document.getElementById('sbox-btn-close');
				btn.fireEvent('click');
			};";

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html  = '<span class="input-append">';
		$html .= '<input type="text" class="input-medium" id="a_name" value="' . $newsletter->subject . '" disabled="disabled" size="35" />';
		$html .= '<a class="modal btn hasTooltip" title="' . HTMLHelper::tooltipText('COM_BWPOSTMAN_SELECT_NEWSLETTER') . '" href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}, iframeOptions: {id: \'nlsFrame\'}}"><i class="icon-file"></i> ' . Text::_('JSELECT') . '</a>';
		$html .= "\n<input type=\"hidden\" id=\"a_id\" $class name=\"$fieldName\" value=\"$value\" />";

		$doc->addScriptDeclaration($js);

		return $html;
	}
}

