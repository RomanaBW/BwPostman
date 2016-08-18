<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Module
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
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('
			window.onload=display_yes_no;
			function display_yes_no()
			{
				var radios = document.getElementsByName("jform[params][com_params]");
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
					css_Style = "none";
				} 
				else 
				{
					css_Style = "block";
				}
				document.getElementById("jform_params_disclaimer_menuitem").style.display=css_Style;
				document.getElementById("jform_params_article_id_name").parentNode.parentNode.style.display=css_Style;
				var mod_set = document.getElementsByClassName("mod_set");
				var length1 = mod_set.length;
				for (var i = 0; i < length1; i++) 
				{
					mod_set[i].style.display=css_Style;
				}
				if (document.getElementById("jform_params_disclaimer_menuitem_chzn")) 
				{
					document.getElementById("jform_params_disclaimer_menuitem_chzn").style.display=css_Style;
				}
				else 
				{
					document.getElementById("jform_params_disclaimer_menuitem").style.display=css_Style;
					document.getElementById("jform_params_disclaimer_menuitem_chzn").style.display=css_Style;
				}
			}
		');

	return null;
	}
}

