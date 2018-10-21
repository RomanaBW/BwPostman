<?php
/**
 * BwPostman Component
 *
 * BwPostman  form field color selector
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

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('color');
/**
 * Color Form Field class for the Joomla Platform.
 * This implementation is designed to be compatible with HTML5's <input type="color">
 *
 * @package     Joomla.Platform
 *
 * @subpackage  Form
 *
 * @link        http://www.w3.org/TR/html-markup/input.color.html
 *
 * @since       11.3
 */
class JFormFieldMyColor extends JFormFieldColor
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.3
	 */
	protected $type = 'MyColor';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.3
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size       = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$classes    = (string) $this->element['class'];
		$disabled   = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		if (!$disabled)
		{
			JHtml::_('behavior.colorpicker');
			$classes .= ' input-colorpicker';
		}

		if (empty($this->value))
		{
			// A color field can't be empty, we default to black. This is the same as the HTML5 spec.
			$this->value = '#000000';
		}

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		$onblur = $this->element['onblur'] ? ' onblur="' . (string) $this->element['onblur'] . '"' : '';

		$class = $classes ? ' class="' . trim($classes) . '"' : '';

		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $onchange . $onblur . '/>';
	}
}
