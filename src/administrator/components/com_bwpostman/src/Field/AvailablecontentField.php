<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field available content class.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

defined('JPATH_BASE') or die;

use Exception;
use JHtmlSelect;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.0.1
 */
class AvailablecontentField extends ListField
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
	public function getLabel(): string
	{
		return '<label for="' . $this->id . '" class="form-label available_content_label">' . Text::_($this->element['label']) . '</label>';
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
	public function getInput(): string
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . $this->element['onchange'] . '"' : '';
		$attr .= $this->element['ondblclick'] ? ' ondblclick="' . $this->element['ondblclick'] . '"' : '';

		// Get the field options.
		$options = $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtmlSelect::genericlist($options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT) . '"/>';
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
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getOptions(): array
	{
		// prepare query
		$db         = Factory::getContainer()->get(DatabaseInterface::class);
		$query_user = $db->getQuery(true);

		// get user_ids if exists
		// @Todo: Why this query?
		$query_user->select($db->quoteName('user_id'));
		$query_user->from($db->quoteName('#__bwpostman_subscribers'));
		$query_user->where($db->quoteName('id') . ' = ' . (int) $this->_id);

		try
		{
			$db->setQuery($query_user);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// get authorized viewlevels
		$options = $this->getAvailableContent();

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
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
	private function getAvailableContent(): array
	{
		$app        = Factory::getApplication();
		$db         = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $db->getQuery(true);
		$options    = array();
		$categories = array();
		$params     = ComponentHelper::getParams('com_bwpostman');
		$exc_cats   = $params->get('excluded_categories', '');

		$rows_list_uncat  = array();
		$selected_content = '';


		if ($app->getUserState('com_bwpostman.edit.newsletter.data'))
		{
			$selected_content = $app->getUserState('com_bwpostman.edit.newsletter.data')->selected_content;
		}

		if (is_array($selected_content))
		{
			$selected_content = implode(',', $selected_content);
		}

		// Get available content which is categorized
		$query->select($db->quoteName('c') . '.' . $db->quoteName('id'));
		$query->select($db->quoteName('c') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('category_name'));
		$query->select($db->quoteName('c') . '.' . $db->quoteName('parent_id') . ' AS ' . $db->quoteName('parent'));
		$query->from($db->quoteName('#__categories') . ' AS ' . $db->quoteName('c'));
		$query->where($db->quoteName('c') . '.' . $db->quoteName('parent_id') . ' > ' . $db->quote('0'));

		// params - get only not excluded categories
		if (is_array($exc_cats) && !empty($exc_cats))
		{
			$exc_cats = ArrayHelper::toInteger($exc_cats);
			$query->where(
				'(' . $db->quoteName('c') . '.' . $db->quoteName('id')
				. ' NOT IN (' . implode(',', $exc_cats) . ') AND ' . $db->quoteName('c') . '.' . $db->quoteName('parent_id')
				. ' NOT IN (' . implode(',', $exc_cats) . '))'
			);
		}

		$query->order($db->quoteName('c') . '.' . $db->quoteName('title') . ' ASC');

		try
		{
			$db->setQuery($query);

			$categories = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		$rows_list = array();

		foreach($categories as $category)
		{
			$rows_list = array();

			$query = $db->getQuery(true);
			$query->select($db->quoteName('c') . '.' . $db->quoteName('id') . ' AS ' . $db->quoteName('value'));
			$query->select(
				'CONCAT(' . $db->quoteName('cc') . '.' . $db->quoteName('path') . ', " = ",'
				. $db->quoteName('c') . '.' . $db->quoteName('title') . ') AS ' . $db->quoteName('text')
			);
			$query->from($db->quoteName('#__content') . ' AS ' . $db->quoteName('c'));
			$query->from($db->quoteName('#__categories') . ' AS ' . $db->quoteName('cc'));

			$query->where($db->quoteName('c') . '.' . $db->quoteName('state') . ' > ' . 0);
			$query->where($db->quoteName('c') . '.' . $db->quoteName('catid') . ' = ' . $db->quoteName('cc') . '.' . $db->quoteName('id'));
			$query->where($db->quoteName('c') . '.' . $db->quoteName('catid') . ' = ' . (int) $category->id);
			$query->where($db->quoteName('cc') . '.' . $db->quoteName('parent_id') . ' = ' . (int) $category->parent);

			if ($selected_content)
			{
				$query->where($db->quoteName('c') . '.' . $db->quoteName('id') . ' NOT IN (' . $selected_content . ')');
			}

			$query->order($db->quoteName('cc') . '.' . $db->quoteName('path') . ' ASC');
			$query->order($db->quoteName('c') . '.' . $db->quoteName('created') . ' DESC');
			$query->order($db->quoteName('c') . '.' . $db->quoteName('title') . ' ASC');

			try
			{
				$db->setQuery($query);

				$rows_list = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}

			if(count($rows_list) > 0)
			{
				$options = array_merge($options, $rows_list);
			}
		}

		// Get available content which is uncategorized
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('value'));
		$query->select(
			'CONCAT("' . Text::_('COM_BWPOSTMAN_NL_AVAILABLE_CONTENT_UNCATEGORIZED') . ' = ",'
			. $db->quoteName('title') . ') AS ' . $db->quoteName('text')
		);
		$query->from($db->quoteName('#__content'));

		$query->where($db->quoteName('state') . ' > ' . 0);
		$query->where($db->quoteName('catid') . ' = ' . 0);

		if ($selected_content)
		{
			$query->where($db->quoteName('id') . ' NOT IN (' . $selected_content . ')');
		}

		$query->order($db->quoteName('created') . ' DESC');
		$query->order($db->quoteName('title') . ' ASC');

		try
		{
			$db->setQuery($query);

			$rows_list_uncat = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if(count($rows_list_uncat) > 0)
		{
			$options = array_merge($rows_list, $rows_list_uncat);
		}

		return $options;
	}
}
