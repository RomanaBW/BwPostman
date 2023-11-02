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
use Joomla\CMS\Form\FormHelper;
use Joomla\Database\DatabaseInterface;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper');

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
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	protected function getInput(): string
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '	    <div class="well well-small">';
		$html[] = '			<table class="adminlist table">';
		$html[] = '				<thead>';
		$html[] = '					<tr>';
		$html[] = '						<th nowrap="nowrap">' . Text::_('JGRID_HEADING_ID') . '</th>';
		$html[] = '						<th nowrap="nowrap">
						<input type="checkbox" name="checkall-toggle" value="" title="'
							. Text::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this)" />
						</th>';
		$html[] = '						<th nowrap="nowrap">' . Text::_('JGLOBAL_TITLE') . '</th>';
		$html[] = '						<th nowrap="nowrap">' . Text::_('JGLOBAL_DESCRIPTION') . '</th>';
		$html[] = '						<th nowrap="nowrap">' . Text::_('MOD_BWPOSTMAN_PUBLISHED') . '</th>';
		$html[] = '						<th nowrap="nowrap">' . Text::_('JFIELD_ACCESS_LABEL') . '</th>';
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
				$html[] = '							 <td>' . Text::_($option->value) . '</td>';
				$html[] = '			<td>
										<input type="checkbox" id="cb' . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT) . '" ' . $checked . $class . $onclick . $disabled . '/></td>';
				$html[] = '							 <td>' . Text::_($option->text) . '</td>';
				$html[] = '							 <td>' . Text::_($option->description) . '</td>';
				$html[] = '							 <td>'
					. HtmlHelper::_('jgrid.published', $option->published, $i, 'mailinglists.', '', '') . '</td>';
				$html[] = '							 <td>' . Text::_($option->access_level) . '</td>';
				$html[] = '						  </tr>';
			}
		}
		else
		{
				$html[] = '							<tr class="row1">';
				$html[] = '								<td colspan="6"><strong>' . Text::_('MOD_BWPOSTMAN_NO_ML') . '</strong></td>';
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
	 * Here all mailing lists are selected, which are not archived
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	protected function getOptions(): array
	{
		$options = null;

		// prepare query
		$_db		= Factory::getContainer()->get(DatabaseInterface::class);
		$query		= $_db->getQuery(true);

		$query->select("a.id AS value, a.title AS text, a.description as description, a.access AS access, a.published AS published");
		$query->from('#__bwpostman_mailinglists AS a');
		$query->where($_db->quoteName('a.archive_flag') . ' = ' . 0);

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		$query->order($_db->quoteName('published') . 'DESC, ' . $_db->quoteName('access_level') . 'ASC, ' . $_db->quoteName('text') . 'ASC');

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
		return array_merge(parent::getOptions(), $options);
	}
}
