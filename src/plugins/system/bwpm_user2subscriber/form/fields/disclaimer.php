<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field disclaimer class.
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

JFormHelper::loadFieldClass('checkbox');

/**
 * Class JFormFieldDisclaimer
 * Helper form field to show a disclaimer checkbox
 *
 * @since   2.1.1
 */
class JFormFieldDisclaimer extends JFormFieldCheckbox
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.1.1
	 */
	protected $type = 'Disclaimer';

	/**
	 * The checked state of checkbox field.
	 *
	 * @var    boolean
	 * @since  2.1.1
	 */
	protected $checked = false;

	/**
	 * Method to get the field input markup.
	 * The checked element sets the field to selected.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.1.1
	 */
	protected function getInput()
	{
		// Extends the checkbox with the disclaimer link and modalbox
		$ext	=	"\n" . '<a id="bwp_plg_open">' . JText::_('COM_BWPOSTMAN_DISCLAIMER') . '</a></label>' . "\n";
		$ext	.=	'<div id="bwp_plg_Modal" class="bwp_plg_modal">' . "\n";
		$ext	.=	'	<div id="bwp_plg_modal-content">' . "\n";
		$ext	.=	'		<span class="bwp_plg_close">&times;</span>' . "\n";
		$ext	.=	'		<div id="bwp_plg_wrapper"></div>' . "\n";
		$ext	.=	'	</div>' . "\n";
		$ext	.=	'</div>' . "\n";

		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$value     = !empty($this->default) ? $this->default : '1';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$checked   = $this->checked || !empty($this->value) ? ' checked' : '';

		// Initialize JavaScript field attributes.
		$onclick  = !empty($this->onclick) ? ' onclick="' . $this->onclick . '"' : '';
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

		return '<label class="checkbox disclaimer"><input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . $disabled . $onclick . $onchange
			. $required . $autofocus . ' />' . $ext;
	}
}
