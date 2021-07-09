<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field dcmessage class.
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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class JFormFieldBwpSpacer
 * Helper form field to show a message
 *
 * @since   2.0.0
 */
class JFormFieldDcMessage extends JFormField
{
	/**
	 * The form field type.
	 * Type have to be 'Spacer', because we need not the word '(optional)'
	 *
	 * @var string  $type
	 *
	 * @since   2.0.0
	 */
	protected $type = 'DcMessage';

	/**
	 * Method to get the field input markup for bwpspacer.
	 * The bwpspacer shows the value of the message field of the plugin options.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.0.0
	 */
	protected function getInput(): string
	{

		$com_params = ComponentHelper::getParams('com_bwpostman');

		$html = array();
		$html[] = '<div class="dc_message">';
		$html[] = '	<p>' . Text::_('PLG_BWPOSTMAN_PLUGIN_USER2SUBSCRIBER_DISCLAIMER_MESSAGE') . '</p>';
		$html[] = '	<p class="invalid">' . $com_params->get('disclaimer') ? '' : Text::_('PLG_BWPOSTMAN_PLUGIN_USER2SUBSCRIBER_DISCLAIMER_COM_DISABLED') . '</p>';
		$html[] = '</div>';

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
	protected function getLabel(): string
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
	protected function getTitle(): string
	{
		return ' ';
	}
}
