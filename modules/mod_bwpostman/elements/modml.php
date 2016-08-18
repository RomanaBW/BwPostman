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

JFormHelper::loadFieldClass('checkboxes');

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
class JFormFieldModMl extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since
	 */
	protected $type = 'ModMl';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since
	 */
	protected function getLabel()
	{
		// Initialise variables.
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip hasTooltip' : '';
		$class = $this->required == true ? $class . ' required' : $class;
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
				trim($text, ':') . '<br />' . ($this->translateDescription ? JText::_($this->description) : $this->description),
				ENT_COMPAT, 'UTF-8'
			) . '"';
		}

		// Add the label text and closing tag.
		if ($this->required)
		{
			$label .= '>' . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label><br />';
		}

		return $label;
	}


	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '	    <div class="well well-small">';
		$html[] = '			<table class="adminlist table">';
		$html[] = '				<thead>';
		$html[] = '					<tr>';
		$html[] = '						<th width="30" nowrap="nowrap">'. JText::_('JGRID_HEADING_ID') . '</th>';
		$html[] = '						<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="' . JText::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this)" /></th>';
		$html[] = '						<th width="200" nowrap="nowrap">' . JText::_('JGLOBAL_TITLE') . '</th>';
		$html[] = '						<th nowrap="nowrap">' . JText::_('JGLOBAL_DESCRIPTION') . '</th>';
		$html[] = '						<th width="80" nowrap="nowrap">' . JText::_('MOD_BWPOSTMAN_PUBLISHED') . '</th>';
		$html[] = '						<th width="80" nowrap="nowrap">' . JText::_('JFIELD_ACCESS_LABEL') . '</th>';
		$html[] = '					</tr>';
		$html[] = '				</thead>';
	    $html[] = '				<tbody>';

	    if (count($options) > 0)
	    {
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
				$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

				// Initialize some JavaScript option attributes.
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

				$html[] = '							<tr class="row' . $i % 2 . '">';
				$html[] = '							 <td align="center">' . JText::_($option->value) . '</td>';
				$html[] = '              <td><input type="checkbox" id="cb'  . $i . '" name="' . $this->name . '" ' . ' value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" ' . $checked . $class . $onclick . $disabled . '/></td>';
				$html[] = '							 <td>' . JText::_($option->text) . '</td>';
				$html[] = '							 <td>' . JText::_($option->description) . '</td>';
				$html[] = '							 <td align="center">' . JHtml::_('jgrid.published', $option->published, $i, 'mailinglists.', '', '') . '</td>';
				$html[] = '							 <td>' . JText::_($option->access_level) . '</td>';
				$html[] = '						  </tr>';
			}
	    }
	    else
	    {
				$html[] = '							<tr class="row1">';
				$html[] = '								<td colspan="6"><strong>'. JText::_('MOD_BWPOSTMAN_NO_ML').'</strong></td>';
				$html[] = '							</tr>';
	    }
		$html[] = '				</tbody>';
		$html[] = '     </table>';
		$html[] = '    </div>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$app	= JFactory::getApplication();

		// prepare query
		$_db		= JFactory::getDbo();
		$query		= $_db->getQuery(true);

		$query->select("a.id AS value, a.title AS text, a.description as description, a.access AS access, a.published AS published");
		$query->from('#__bwpostman_mailinglists AS a');
		$query->where($_db->quoteName('a.archive_flag') . ' = ' . (int) 0);

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		$query->order($_db->quoteName('published')  . 'DESC, ' . $_db->quoteName('access_level'). 'ASC');

		$_db->setQuery($query);
		$options = $_db->loadObjectList();

		// Check for a database error.
		if ($_db->getErrorNum()) {
			$app->enqueueMessage($_db->getErrorMsg(), 'error');
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
