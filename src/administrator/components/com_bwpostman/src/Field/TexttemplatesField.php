<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field Text templates class.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

defined('JPATH_PLATFORM') or die;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use RuntimeException;

/**
 * Form Field class for the Joomla Platform.
 * Supports a nested check box field listing user groups.
 * Multi select is available by default.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.2.0
 */
class TexttemplatesField extends RadioField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.2.0
	 */
	protected $type = 'Texttemplates';

	/**
	 * Method to get the Text template field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	protected function getInput(): string
	{
		$item     = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data');
		$html     = array();
		$selected = '';

		// Initialize some field attributes.
		$readonly = $this->readonly;

		// Get the field options.
		$options = $this->getOptions();

		// Get selected template.
		if (is_object($item))
		{
			$selected = $item->text_template_id;
		}

		// note for old templates
		if ($selected < 1)
		{
			$html[] = Text::_('COM_BWPOSTMAN_NOTE_OLD_TEMPLATE');
		}

		if (count($options) > 0)
		{
			// Build the radio field output.
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked    = ((string) $option->value == (string) $selected) ? ' checked="checked"' : '';
				$lblclass   = ' class="mailinglists form-check-label"';
				$inputclass = ' class="mailinglists form-check-input"';

				$disabled   = !empty($option->disable) || ($readonly && !$checked);

				$disabled   = $disabled ? ' disabled' : '';

				// Initialize some JavaScript option attributes.
				$onclick    = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
				$onchange   = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

				$html[] = '<div class="form-check" aria-describedby="tip-' . $this->id . $i . '">';
				$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
							. htmlspecialchars($option->value, ENT_COMPAT) . '"' . $checked . $inputclass . $onclick
							. $onchange . $disabled . ' />';
				$html[] = '<label for="' . $this->id . $i . '"' . $lblclass . ' >';
				$html[] = $option->title . '</label>';

				$tooltip = '<strong>' . $option->description . '</strong><br /><br />'
					. '<div><img src="' . Uri::root() . $option->thumbnail . '" alt="' . $option->title . '"'
					.'style="max-width:160px; max-height:100px;" /></div>';
				$html[] = '<div role="tooltip" id="tip-' . $this->id . $i . '">'.$tooltip.'</div>';
				$html[] = '</div>';
			}
		}
		else
		{
			$html[] = Text::_('COM_BWPOSTMAN_NO_DATA');
		}

		// End the radio field output.
//		$html[]	= '</div>';

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	public function getOptions(): array
	{
		$app = Factory::getApplication();

		// Initialize variables.
		$item    = $app->getUserState('com_bwpostman.edit.newsletter.data');
		$options = array();

		// prepare query
		$db = BwPostmanHelper::getDbo();

		// Build the select list for the templates
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('value'));
		$query->select($db->quoteName('title') . ' AS ' . $db->quoteName('title'));
		$query->select($db->quoteName('description') . ' AS ' . $db->quoteName('description'));
		$query->select($db->quoteName('thumbnail') . ' AS ' . $db->quoteName('thumbnail'));
		$query->from($db->quoteName('#__bwpostman_templates'));

		// special for old newsletters with template_id < 1
		if (is_object($item))
		{
			if ($item->text_template_id < 1 && !is_null($item->text_template_id))
			{
				$query->where($db->quoteName('id') . ' >= ' . $db->quote('-2'));
			}
			else
			{
				$query->where($db->quoteName('id') . ' > ' . $db->quote('0'));
			}
		}

		$query->where($db->quoteName('archive_flag') . ' = ' . $db->quote('0'));
		$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));
		$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));
		$query->order($db->quoteName('title') . ' ASC');

		try
		{
			$db->setQuery($query);

			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
