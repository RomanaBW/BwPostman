<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element to select a single newsletter for a view in frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

/**
 * Renders a newsletter element
 *
 * @package BwPostman-Admin
 *
 * @since       1.0.8
 */

class JFormFieldbuttons extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 *
	 * @var		string
	 *
	 * @since   1.0.8
	*/
	var	$_name = 'Subject';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.8
	 */
	protected function getinput()
	{
		$doc 		= JFactory::getDocument();
		$fieldName	= $this->name;

		$newsletter = JTable::getInstance('newsletters', 'BwPostmanTable');
		if ($this->value)
		{
			$newsletter->load($this->value);
		}
		else
		{
			$newsletter->subject = JText::_('COM_BWPOSTMAN_SELECT_BUTTON_LABEL');
		}

		$js = "
			function SelectNewsletter(field, id, subject) {
				document.getElementById('a_'+field).value = id;
				document.getElementById('b_'+field).value = subject;
				var btn = window.parent.document.getElementById('sbox-btn-close');
				btn.fireEvent('click');
			}";

		$link = 'index.php?option=com_bwpostman&amp;view=newsletterelement&amp;tmpl=component&amp;field='.$fieldName;
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal', 'a.modal');

		// The active newsletter id field.
		if (0 == (int)$this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html  = '<span class="input-append">';
		$html .= '<input type="text" class="input-medium" id="b_'.$fieldName.'" value="' . $newsletter->subject . '" disabled="disabled" size="35" />';
		$html .= '<a class="modal btn hasTooltip" title="' . JHtml::tooltipText('COM_BWPOSTMAN_SELECT_BUTTON_LABEL') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . JText::_('JSELECT') . '</a>';
		$html .= "\n<input type=\"hidden\" id=\"a_$fieldName\" $class name=\"$fieldName\" value=\"$value\" />";

		return $html;
	}
}

