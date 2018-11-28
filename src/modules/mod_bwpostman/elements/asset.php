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

jimport('joomla.form.formfield');

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
	 * @since
	 */
	protected function getInput()
	{
		JHtml::_('jquery.framework');

		$text = JText::_('MOD_BWPOSTMAN_FIELD_OBLIGATION');

		$doc 		= JFactory::getDocument();
		$js = "
			window.onload=display_yes_no;
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
					css_Style = 'none';
				} 
				else 
				{
					css_Style = 'block';
				}
				document.getElementById('jform_params_disclaimer_menuitem').style.display=css_Style;
				document.getElementById('jform_params_article_id_name').parentNode.parentNode.style.display=css_Style;
				var mod_set = document.getElementsByClassName('mod_set');
				var length1 = mod_set.length;
				for (var i = 0; i < length1; i++) 
				{
					mod_set[i].style.display=css_Style;
				}
				if (document.getElementById('jform_params_disclaimer_menuitem_chzn'))
				{
					document.getElementById('jform_params_disclaimer_menuitem_chzn').style.display=css_Style;
				}
				else 
				{
					document.getElementById('jform_params_disclaimer_menuitem').style.display=css_Style;
					document.getElementById('jform_params_disclaimer_menuitem_chzn').style.display=css_Style;
				}
			}
			jQuery(document).ready(function()
			{
				// monitors obligation fields
				jQuery('#attrib-reg_settings').on('change', '.bwpcheck :radio', function(){
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
					var value1 = jQuery('input[name=\"jform[params]['+click_fields[a]+']\"]:checked').val();
					var value2 = jQuery('input[name=\"jform[params]['+check_fields[a]+']\"]:checked').val();
					var text = '$text';

					if (value1 == 0 && value2 == 1)
					{
						jQuery('#jform_params_'+click_fields[a]).after(text);
						jQuery('#jform_params_'+click_fields[a]).next('.obligation').slideDown(800);
					}
					else
					{
						jQuery('#jform_params_'+click_fields[a]).next('.obligation').slideUp(800);
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

		return null;
	}
}
