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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Renders a custom form field to insert a javascript to bwpostman settings
 *
 * @package BwPostman-Admin
 *
 * @since       2.2.0
 */

class JFormFieldCustomscript extends JFormField
{

	/**
	 * Method to get the field label markup 
	 * We need no label.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.2.0
	 */
	protected function getLabel()
	{
		return ' ';
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.2.0
	 */
	protected function getinput()
	{
		JHtml::_('jquery.framework');

		$doc 		= JFactory::getDocument();
		$text = JText::_('COM_BWPOSTMAN_FIELD_OBLIGATION');
		$js = "
			jQuery(document).ready(function()
			{
				// monitors obligation fields
				jQuery('#com_bwpostman_registration_settings').on('change', '.bwpcheck :radio', function(){
					var ind = jQuery(this).index('.bwpcheck :radio');
					var a = Math.floor(ind/4);
					bwpcheck(a);
				});

				// Displays a tip
				function bwpcheck(a){
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
					var value1 = jQuery('input[name=\"jform['+click_fields[a]+']\"]:checked').val();
					var value2 = jQuery('input[name=\"jform['+check_fields[a]+']\"]:checked').val();
					var text = '$text';

					if (value1 == 0 && value2 == 1)
					{
						jQuery('#jform_'+click_fields[a]).after(text);
						jQuery('#jform_'+click_fields[a]).next('.obligation').slideDown(800);
					}
					else
					{
						jQuery('#jform_'+click_fields[a]).next('.obligation').slideUp(800);
					}
				}

				// check obligation fields after page rendering
				for (a = 0; a < 3; a++)
      			{
					bwpcheck(a);
      			}
			});
		";

		$doc->addScriptDeclaration($js);


		return;
	}
}

