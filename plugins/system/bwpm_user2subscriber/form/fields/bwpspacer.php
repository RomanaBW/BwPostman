<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field bwpspacer class.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
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

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry as JRegistry;

/**
 * Class JFormFieldBwpSpacer
 * Helper form field to show a message
 *
 * @since   2.0.0
 */
class JFormFieldBwpSpacer extends JFormField
{
	/**
	 * The form field type.
	 * Type have to be 'Spacer', because we need not the word '(optional)'
	 *
	 * @var string  $type
	 *
	 * @since   2.0.0
	 */
	protected $type = 'Spacer';

	/**
	 * Method to get the field input markup for bwpspacer.
	 * The bwpspacer shows the value of the message field of the plugin options.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.0.0
	 */
	protected function getInput()
	{
		$plugin = JPluginHelper::getPlugin('system', 'bwpm_user2subscriber');
		$params = new JRegistry($plugin->params);

		$html = array();
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$html[] = '<p' . $class . '>';

		$text = $params->get('register_message_option') ? nl2br($params->get('register_message_option')) : '';

		$html[] = $text . '</p>';

		return implode('', $html);
	}

	/**
	 * Method to get the field label markup for a bwpspacer.
	 * The bwpspacer need no label.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.0.0
	 */
	protected function getLabel()
	{
		return ' ';
	}

	/**
	 * Method to get the field title.
	 * The bwpspacer need no field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   2.0.0
	 */
	protected function getTitle()
	{
		return ' ';
	}
}
