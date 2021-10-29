<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class JFormFieldAsset
 *
 * @since
 */
class JFormFieldAsset extends JFormField
{
	/**
	 * property to hold type
	 *
	 * @var string
	 *
	 * @since
	 */
	protected $type = 'Asset';

	/**
	 * Method to get asset input field
	 *
	 * @return null
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	protected function getInput()
	{

		$text = Text::_('MOD_BWPOSTMAN_FIELD_OBLIGATION');

		$doc 		= Factory::getApplication()->getDocument();

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
			function display_yes_no()
			{
				var radios = document.getElementsByName('jform[params][com_params]');
				for (var i = 0, length = radios.length; i < length; i++) 
				{
					if (radios[i].checked) 
					{
						value = (radios[i].value);
						break
					}
				}
				if (value == 1) 
				{
					toggleVisibility('visible', 'invisible');
				}
				else 
				{
					toggleVisibility('invisible', 'visible');
				}
			}

			function toggleVisibility(remove, add)
			{
				var el = document.querySelector('.bwpmod.field-spacer');
				while (el = el.nextSibling)
				{
					if (typeof el.classList != 'undefined')
					{
						el.classList.remove(remove);
						el.classList.add(add);
					}
				}
			}

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
				var value1 = document.querySelector('input[name=\"jform[params]['+click_fields[a]+']\"]:checked').value;
				var value2 = document.querySelector('input[name=\"jform[params]['+check_fields[a]+']\"]:checked').value;
				var elem = document.getElementById('jform_params_'+click_fields[a]);

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

			(function() {

				document.addEventListener('DOMContentLoaded', function() {
					display_yes_no();
					// check obligation fields after page rendering
					for (a = 0; a < 3; a++)
	      			{
						bwpcheck(a, 1);
	      			}
				});

			})();
		";
		$doc->getWebassetManager()->addInlineScript($js);

		return null;
	}
}
