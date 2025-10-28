<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element to select a single newsletter for a view in frontend.
 *
 * @version 2.1.1 build 548
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Renders a custom form field to insert a javascript to bwpostman settings
 *
 * @package BwPostman-Admin
 *
 * @since       2.2.0
 */

class CustomscriptField extends FormField
{

	/**
	 * Method to get the field label markup
	 * We need no label.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.2.0
	 */
	protected function getLabel(): string
	{
		return ' ';
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	protected function getinput()
	{
		$doc  = Factory::getApplication()->getDocument();
		$text = Text::_('COM_BWPOSTMAN_FIELD_OBLIGATION');

		$css = "
			.obligation {
				color: red;
				opacity: 0;
				line-height: 0px;
				transition: all 0.5s linear;
			}
			.obligation.down {
				opacity: 1;
				line-height: 20px;
				transition: all 0.5s linear;
			}
		";
		$doc->getWebassetManager()->addInlineStyle($css);

		$js = "

			// Displays a tip
			function bwpcheck(a, init){
				var click_fields    = [
					'show_firstname_field',
					'show_name_field',
					'show_special'
				];
				var check_fields    = [
					'firstname_field_obligation',
					'name_field_obligation',
					'special_field_obligation'
				];
				var value1 = document.querySelector('input[name=\"jform['+click_fields[a]+']\"]:checked').value;
				var value2 = document.querySelector('input[name=\"jform['+check_fields[a]+']\"]:checked').value;
				var elem = document.getElementById('jform_'+click_fields[a]);

				if (init == 1)
				{
					var text = '$text';
					elem.insertAdjacentHTML('afterend',text);
				}

				if (value1 == 0 && value2 == 1)
				{
					upOrDown(elem, 'down');
				}
				else
				{
					upOrDown(elem, 'up');
				}
			}

			function upOrDown(elem, slide) {
				// Get the next sibling element
				var sibling = elem.nextElementSibling;

				// If the sibling matches our selector, use it
				// If not, jump to the next sibling and continue the loop
				while (sibling)
				{
					if (sibling.matches('.obligation'))
					{
						if (slide == 'up')
						{
							sibling.classList.remove('down');
						}
						else
						{
							sibling.classList.add('down');
						}
						return;
					}
					sibling = sibling.nextElementSibling
				}
			}

			function ready(callbackFunc) {
				if (document.readyState !== 'loading')
				{
					// Document is already ready, call the callback directly
					callbackFunc();
				}
				else if (document.addEventListener)
				{
					// All modern browsers to register DOMContentLoaded
					document.addEventListener('DOMContentLoaded', callbackFunc);
				}
				else
				{
					// Old IE browsers
					document.attachEvent('onreadystatechange', function() {
						if (document.readyState === 'complete')
						{
							callbackFunc();
						}
					});
				}
			}

			ready(function() {
				// check obligation fields after page rendering
				for (a = 0; a < 3; a++)
	      		{
					bwpcheck(a, 1);
	      		}
			});
		";

		$doc->getWebAssetManager()->addInlineScript($js);
	}

	/**
	 * Method to get a control group with label and input.
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   3.0.0
	 */
	public function renderField($options = array()): string
	{
		$options['class'] = empty($options['class']) ? 'hidden' : $options['class'] . ' hidden';

		return parent::renderField($options);
	}
}

