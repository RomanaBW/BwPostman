<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field selected content class.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.0.1
 */
class JFormFieldSelectedContent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.1
	 */
	public $type = 'SelectedContent';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.1
	 */
	public function getLabel()
	{
		$return = '<label for="' . $this->id . '" class="selected_content_label">' . JText::_($this->element['label']) . '</label>';
		return $return;
	}

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getInput()
	{
		// Initialize variables.
		$html	= array();
		$attr	= '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		$attr .= $this->element['ondblclick'] ? ' ondblclick="' . (string) $this->element['ondblclick'] . '"' : '';

		// Get the field options.
		$options	= (array) $this->getOptions();

		// Create a regular list.
		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', '', $this->id);

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getOptions()
	{
		// Initialize variables.
		$user_id		= null;

		// prepare query
		$_db		= JFactory::getDbo();
		$query_user	= $_db->getQuery(true);

		// get user_ids if exists
		$query_user->select($_db->quoteName('user_id'));
		$query_user->from($_db->quoteName('#__bwpostman_subscribers'));
		$query_user->where($_db->quoteName('id') . ' = ' . (int) $this->_id);

		$_db->setQuery($query_user);
		try
		{
			$user_id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$options = $this->getSelectedContent();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to get the available content items which can be used to compose a newsletter
	 *
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	private function getSelectedContent()
	{
		$app				= JFactory::getApplication();
		$_db				= JFactory::getDbo();
		$options			= array();
		$selected_content	= '';

		if (is_object($app->getUserState('com_bwpostman.edit.newsletter.data')))
		{
			$selected_content	= $app->getUserState('com_bwpostman.edit.newsletter.data')->selected_content;
		}

		if ($selected_content)
		{
			if (!is_array($selected_content))
			{
				$selected_content = explode(',', $selected_content);
			}

			// We do a foreach to protect our ordering
			foreach ($selected_content as $value)
			{
				$subquery	= $_db->getQuery(true);
				$subquery->select($_db->quoteName('cc') . '.' . $_db->quoteName('path'));
				$subquery->from($_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc'));
				$subquery->where($_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('c') . '.' . $_db->quoteName('catid'));

				$query	= $_db->getQuery(true);
				$query->select($_db->quoteName('c') . '.' . $_db->quoteName('id') . 'AS value');
				$query->select(
					'CONCAT((' . $subquery . '), " = ",' . $_db->quoteName('c') . '.' . $_db->quoteName('title') . ') AS '
					. $_db->quoteName('text')
				);
				$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('c'));
				$query->where($_db->quoteName('c') . '.' . $_db->quoteName('id') . ' = ' . (int) $value);

				$_db->setQuery($query);

				try
				{
					$options[] = $_db->loadAssoc();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		return $options;
	}
}
