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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

defined('JPATH_BASE') or die;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;
use RuntimeException;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.0.1
 */
class SelectedcontentField extends ListField
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
	public function getLabel(): string
	{
		return '<label for="' . $this->id . '" class="form-label selected_content_label">' . Text::_($this->element['label']) . '</label>';
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

		// Create a regular list.
		$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', '', $this->id);

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
		$options = $this->getSelectedContent();

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
	private function getSelectedContent(): array
	{
		$app              = Factory::getApplication();
		$db               = Factory::getContainer()->get(DatabaseInterface::class);
		$options          = array();
		$selected_content = '';

		if (is_object($app->getUserState('com_bwpostman.edit.newsletter.data')))
		{
			$selected_content = $app->getUserState('com_bwpostman.edit.newsletter.data')->selected_content;
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
				$subquery = $db->getQuery(true);
				$subquery->select($db->quoteName('cc') . '.' . $db->quoteName('path'));
				$subquery->from($db->quoteName('#__categories') . ' AS ' . $db->quoteName('cc'));
				$subquery->where($db->quoteName('cc') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('c') . '.' . $db->quoteName('catid'));

				$query = $db->getQuery(true);
				$query->select($db->quoteName('c') . '.' . $db->quoteName('id') . 'AS value');
				$query->select(
					'CONCAT((' . $subquery . '), " = ",' . $db->quoteName('c') . '.' . $db->quoteName('title') . ') AS '
					. $db->quoteName('text')
				);
				$query->from($db->quoteName('#__content') . ' AS ' . $db->quoteName('c'));
				$query->where($db->quoteName('c') . '.' . $db->quoteName('id') . ' = ' . (int) $value);

				try
				{
					$db->setQuery($query);

					$options[] = $db->loadAssoc();
				}
				catch (RuntimeException $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		return $options;
	}
}
