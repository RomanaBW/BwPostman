<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
 *
 * @version 1.3.2 bwpm
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
 * @since       1.2.0
 */
class JFormFieldUserGroups extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.2.0
	 */
	protected $type = 'UserGroups';

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected static $options = array();

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   1.2.0
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
	 * @since   1.2.0
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html	= array();
		$stub	= "'ub'";

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
		$html[] = '						<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value="" title="' . JText::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this, ' . $stub . ')" /></th>';
		$html[] = '						<th nowrap="nowrap">' . JText::_('JGLOBAL_TITLE') . '</th>';
		$html[] = '					</tr>';
		$html[] = '				</thead>';
    $html[] = '				<tbody>';

    if (count($options) > 0) {

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
			$html[] = '              <td><input type="checkbox" id="ub'  . $i . '" name="' . $this->name . '"' . ' value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" ' . $checked . $class . $onclick . $disabled . '/></td>';
			$html[] = '							 <td>' . JText::_($option->text) . '</td>';
			$html[] = '						  </tr>';

		}

    } else {
			$html[] = '							<tr class="row1">';
			$html[] = '								<td colspan="3"><strong>'. JText::_('COM_BWPOSTMAN_NO_CAM').'</strong></td>';
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
	 * @since   1.2.0
	 */
	protected function getOptions()
	{
		// Hash for caching
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();

			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
			->select('CONCAT("-",a.id) AS value')
			->select('a.title AS text')
			->select('COUNT(DISTINCT b.id) AS level')
			->from('#__usergroups as a')
			->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');
			$db->setQuery($query);

			if ($options = $db->loadObjectList())
			{
				foreach ($options as &$option)
				{
					$option->text = str_repeat('- ', $option->level) . $option->text;
				}

				static::$options[$hash] = array_merge(static::$options[$hash], $options);
			}
		}

		return static::$options[$hash];
	}






}
