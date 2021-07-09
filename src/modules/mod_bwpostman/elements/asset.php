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
use Joomla\CMS\HTML\HTMLHelper;

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
		HtmlHelper::_('jquery.framework');

		$text = Text::_('MOD_BWPOSTMAN_FIELD_OBLIGATION');

		$doc 		= Factory::getApplication()->getDocument();
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
					css_Style = 'hidden';
				} 
				else 
				{
					css_Style = 'visible';
				}
				jQuery( '.bwpmod.field-spacer' ).nextAll().css( 'visibility', css_Style );
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

				// set view to one column
				jQuery('#fieldset-ml_available .column-count-md-2').attr('class', 'column-count-1');

				// trigger click on checkbox
				jQuery('#jform_params_mod_ml_available tr').click(function(event) {
					if (event.target.type !== 'checkbox') {
						jQuery(':checkbox', this).trigger('click');
					}
				});
			});
		";
		$doc->addScriptDeclaration($js);

		return null;
	}
}
