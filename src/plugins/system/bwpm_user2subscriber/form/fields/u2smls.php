<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * BwPostman form field class selectable mailinglists for plugin.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
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
use Joomla\CMS\Form\Field\CheckboxesField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\FormHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;

FormHelper::loadFieldClass('checkboxes');

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormFieldCheckbox
 * @since
 */
class JFormFieldU2sMls extends CheckboxesField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since
	 */
	protected $type = 'u2smls';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
//	protected function getInput()
//	{
//		// Initialize variables.
//		$session	= Factory::getApplication()->getSession();
//
//		// Initialize some field attributes.
//		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
//
//		// Start the checkbox field output.
//		$html = '<fieldset id="' . $this->id . '"' . $class . '>';
//
//		// Get the field options.
//		$options = $this->getOptions();
//
//		// Prepare needed options properties test and checked
//		$show_desc  = $session->get('plg_bwpm_user2subscriber.show_desc', 'true');
//		$descLength = $session->get('plg_bwpm_user2subscriber.desc_length', '150');
//
//		foreach ($options as $i => $option)
//		{
//			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
//
//			$html .= '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '[] " value="'
//				. $option->value . '" ' .  $class . '/>';
//
//			$html .= '<span class="plg-u2s-ml-title">';
//			$html .= $option->text;
//			$html .= '</span>';
//
//			if ($show_desc)
//			{
//				$html .= '<br />';
//				$html .= '<span class="plg-u2s-ml-description">';
//				$html .= substr(Text::_($option->description), 0, $descLength);
//
//				if (strlen(Text::_($option->description)) > $descLength)
//				{
//					$html .= '... ';
//					$html .= HTMLHelper::tooltip(Text::_($option->description), $option->title, 'tooltip.png', '', '');
//				}
//				$html .= '</span>';
//				$html .= '<br />';
//			}
//		}
//
////		 End the checkbox field output.
//		$html .= '</fieldset>';
//
//		return $html;
//	}

	protected function getInput()
	{
		// Initialize variables.
		$html	= array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '	    <div class="bwp-field well well-small table-responsive">';
		$html[] = '			<table class="adminlist table table-striped">';
		$html[] = '				<tbody>';

		if (count($options) > 0)
		{
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked	= (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
				$class		= !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';
//				$archived	= ($option->archived) ? '<i class="icon-archive"></i>' : '';
//				$published	= ($option->published) ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>';

				// Initialize some JavaScript option attributes.
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

				$html[] = '							<tr class="row' . $i % 2 . '">';
				$html[] = '								<td class="text-center"><input type="checkbox" id="mb' . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" ' . $checked . $class . $onclick . $disabled . ' /></td>';
				$html[] = '								<td>' . Text::_($option->text) . '</td>';
				$html[] = '								<td class="d-none d-lg-table-cell">' . Text::_($option->description) . '</td>';
				$html[] = '						  </tr>';
			}
		}
		else
		{
			$html[] = '							<tr class="row1">';
			$html[] = '								<td colspan="7"><strong>' . Text::_('COM_BWPOSTMAN_NO_ML') . '</strong></td>';
			$html[] = '							</tr>';
		}

		$html[] = '				</tbody>';
		$html[] = '			</table>';
		$html[] = '		</div>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}


	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$session	= Factory::getApplication()->getSession();
		$availableMailinglists = array();

		$mailinglists	= $session->get('plg_bwpm_user2subscriber.ml_available', array());

		if (!is_array($mailinglists))
		{
			$availableMailinglists[] = $mailinglists;
		}
		else {
			$availableMailinglists = $mailinglists;
		}

		// prepare query
		$_db		= Factory::getDbo();
		$query		= $_db->getQuery(true);

		$query->select("a.id AS value, a.title AS text, a.description");
		$query->from('#__bwpostman_mailinglists AS a');
		$query->where($_db->quoteName('a.archive_flag') . ' = ' . (int) 0);

		if (count($availableMailinglists))
		{
			$query->where($_db->quoteName('a.id') . ' IN (' . implode(',', $availableMailinglists) . ')');
		}

		$options = array();

		try
		{
			$_db->setQuery($query);

			$options = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
