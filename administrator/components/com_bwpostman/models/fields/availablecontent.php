<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field available content class.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.0.1
 */
class JFormFieldAvailableContent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.1
	 */
	public $type = 'AvailableContent';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.1
	 */
	public function getLabel()
	{
		$return = '<label for="' . $this->id . '" class="available_content_label">' . JText::_($this->element['label']) . '</label>';
		return $return;
 	}

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.1
	 */
	public function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

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
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtmlSelect::genericlist($options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtmlSelect::genericlist($options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}


	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
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
		// @Todo: Why this query?
		$query_user->select($_db->quoteName('user_id'));
		$query_user->from($_db->quoteName('#__bwpostman_subscribers'));
		$query_user->where($_db->quoteName('id') . ' = ' . (int) $this->_id);

		$_db->setQuery($query_user);

		// get authorized viewlevels
		$options = $this->getAvailableContent();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
	/**
	 * Method to get the available content items which can be used to compose a newsletter
	 *
	 * @access	public
	 *
	 * @return	array
	 *
	 * @since       1.0.1
	 */
	private function getAvailableContent()
	{
		$app				= JFactory::getApplication();
		$_db				= JFactory::getDbo();
		$query				= $_db->getQuery(true);
		$options			= array();
		$selected_content	= '';
		$categories         = array();
		$rows_list_uncat    = array();

		if ($app->getUserState('com_bwpostman.edit.newsletter.data')) {
			$selected_content	= $app->getUserState('com_bwpostman.edit.newsletter.data')->selected_content;
		}

		if (is_array($selected_content))
			$selected_content	= implode(',',$selected_content);

		// Get available content which is categorized
		$query->select($_db->quoteName('c') . '.' . $_db->quoteName('id'));
		$query->select($_db->quoteName('c') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('category_name'));
		$query->select($_db->quoteName('c') . '.' . $_db->quoteName('parent_id') . ' AS ' . $_db->quoteName('parent'));
		$query->from($_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('c'));
		$query->where($_db->quoteName('c') . '.' . $_db->quoteName('parent_id') . ' > ' . $_db->quote('0'));
		$query->order($_db->quoteName('c') . '.' . $_db->quoteName('title') .' ASC');

		$_db->setQuery($query);

		try
		{
			$categories = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		foreach($categories as $category)
		{
			$rows_list  = array();

			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('c') . '.' . $_db->quoteName('id') . ' AS ' . $_db->quoteName('value'));
			$query->select('CONCAT(' . $_db->quoteName('cc') . '.' . $_db->quoteName('path') . ', " = ",' . $_db->quoteName('c') . '.' . $_db->quoteName('title') . ') AS ' . $_db->quoteName('text'));
			$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('c'));
			$query->from($_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc'));

			$query->where($_db->quoteName('c') . '.' . $_db->quoteName('state') . ' > ' . (int) 0);
			$query->where($_db->quoteName('c') . '.' . $_db->quoteName('catid') . ' = ' . $_db->quoteName('cc') . '.' . $_db->quoteName('id'));
			$query->where($_db->quoteName('c') . '.' . $_db->quoteName('catid') . ' = ' . (int) $category->id);
			$query->where($_db->quoteName('cc') . '.' . $_db->quoteName('parent_id') . ' = ' . (int) $category->parent);

			if ($selected_content)
				$query->where($_db->quoteName('c') . '.' . $_db->quoteName('id') . ' NOT IN ('.$selected_content.')');

			$query->order($_db->quoteName('cc') . '.' . $_db->quoteName('path').' ASC');
			$query->order($_db->quoteName('c') . '.' . $_db->quoteName('created').' DESC');
			$query->order($_db->quoteName('c') . '.' . $_db->quoteName('title').' ASC');

			$_db->setQuery($query);
			try
			{
				$rows_list = $_db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if(sizeof($rows_list) > 0)
				$options	= array_merge($options, $rows_list);
		}

		// Get available content which is uncategorized
		$query	= $_db->getQuery(true);
		$query->select($_db->quoteName('id') . ' AS ' . $_db->quoteName('value'));
		$query->select('CONCAT("' . JText::_('COM_BWPOSTMAN_NL_AVAILABLE_CONTENT_UNCATEGORIZED') . ' = ",' .  $_db->quoteName('title') . ') AS ' . $_db->quoteName('text'));
		$query->from($_db->quoteName('#__content'));

		$query->where($_db->quoteName('state') . ' > ' . (int) 0);
		$query->where($_db->quoteName('catid') . ' = ' . (int) 0);

		if ($selected_content)
			$query->where($_db->quoteName('id') . ' NOT IN ('.$selected_content.')');

		$query->order($_db->quoteName('created').' DESC');
		$query->order($_db->quoteName('title').' ASC');

		$_db->setQuery($query);
		try
		{
			$rows_list_uncat = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// @ToDo: must there not stand $options?
		if(sizeof($rows_list_uncat) > 0)	$options	= array_merge($rows_list, $rows_list_uncat);

		return $options;
	}
}
