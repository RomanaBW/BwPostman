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
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
 * @since       11.1
 */
class JFormFieldComCam extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'ComCam';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html	= array();
		$stub	= "'cb'";

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '		<div class="well well-small">';
		$html[] = '			<table class="adminlist table">';
		$html[] = '				<thead>';
		$html[] = '					<tr>';
		$html[] = '						<th width="30" nowrap="nowrap">' . JText::_('JGRID_HEADING_ID') . '</th>';
		$html[] = '						<th width="30" nowrap="nowrap"><input type="checkbox" name="checkall-toggle" value=""
				title="' . JText::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this, ' . $stub . ')" /></th>';
		$html[] = '						<th width="70" nowrap="nowrap">' . JText::_('COM_BWPOSTMAN_ARCHIVED') . '</th>';
		$html[] = '						<th width="200" nowrap="nowrap">' . JText::_('JGLOBAL_TITLE') . '</th>';
		$html[] = '						<th nowrap="nowrap">' . JText::_('JGLOBAL_DESCRIPTION') . '</th>';
		$html[] = '						<th width="80" nowrap="nowrap">' . JText::_('COM_BWPOSTMAN_CAM_NL_NUM') . '</th>';
		$html[] = '					</tr>';
		$html[] = '				</thead>';
		$html[] = '				<tbody>';

		if (count($options) > 0) {
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked	= (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
				$class		= !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';
				$archived	= ($option->archived) ? '<i class="icon-archive"></i>' : '';

				// Initialize some JavaScript option attributes.
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

				$html[] = '							<tr class="row' . $i % 2 . '">';
				$html[] = '								<td align="center">' . JText::_($option->value) . '</td>';
				$html[] = '								<td><input type="checkbox" id="cb' . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '" ' . $checked . $class . $onclick . $disabled . ' /></td>';
				$html[] = '								<td style="text-align: center;">' . $archived . '</td>';
				$html[] = '								<td>' . JText::_($option->text) . '</td>';
				$html[] = '								<td>' . JText::_($option->description) . '</td>';
				$html[] = '								<td>' . JText::_($option->newsletters) . '</td>';
				$html[] = '							</tr>';
			}
		}
		else
		{
				$html[] = '							<tr class="row1">';
				$html[] = '								<td colspan="6"><strong>' . JText::_('COM_BWPOSTMAN_NO_CAM') . '</strong></td>';
				$html[] = '							</tr>';
		}

		$html[] = '				</tbody>';
		$html[] = '		</table>';
		$html[] = '	</div>';

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
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$app	    = JFactory::getApplication();
		$options    = null;

		// prepare query
		$_db		= JFactory::getDbo();
		$nullDate	= $_db->getNullDate();
		$query		= $_db->getQuery(true);
		$sub_query	= $_db->getQuery(true);

		// Build sub query which counts the newsletters of each campaign and query
		$sub_query->select('COUNT(' . $_db->quoteName('b') . '.' . $_db->quoteName('id') . ') AS ' . $_db->quoteName('newsletters'));
		$sub_query->from($_db->quoteName('#__bwpostman_newsletters') . 'AS ' . $_db->quoteName('b'));
		$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('mailing_date') . ' != "' . $nullDate . '"');
		$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('campaign_id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('id'));

		$query->select(
			"a.id AS value, a.title AS text, a.description as description, a.archive_flag AS archived" . ', (' . $sub_query . ') AS newsletters'
		);
		$query->from('#__bwpostman_campaigns AS a');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		$query->order($_db->quoteName('text') . 'ASC');

		try
		{
			$_db->setQuery($query);
			$options = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
