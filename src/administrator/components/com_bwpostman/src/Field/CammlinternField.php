<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field intern mailinglists class.
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
use Joomla\CMS\Form\Field\RadioField;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use RuntimeException;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		BwPostman.Administrator
 *
 * @since		1.0.1
 */
class CammlinternField extends RadioField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  1.0.1
	 */
	public $type = 'Cammlintern';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.1
	 */
	public function getLabel()
	{
		  $return = Text::_($this->element['label']);

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
		$app       = Factory::getApplication();

		// Get item and selected mailinglists
		$item    = $app->getUserState('com_bwpostman.edit.campaign.data');
		$cam_id  = $app->getUserState('com_bwpostman.edit.campaign.id', null);
		$options = (array) $this->getOptions();

		if (is_object($item))
		{
			(property_exists($item, 'ml_intern')) ? $ml_select	= $item->ml_intern : $ml_select = '';
		}

		$db        = Factory::getDbo();
		$query     = $db->getQuery(true);
		$ml_select = array();
		$selected  = '';

		$disabled   = $this->element['disabled'] == 'true' ? true : false;
		$readonly   = $this->element['readonly'] == 'true' ? true : false;
		$attributes = ' ';
		$return     = '';

		$type = 'checkbox';
		$v    = $this->element['class'];

		if ($v)
		{
			$attributes .= 'class="' . $v . '" ';
		}
		else
		{
			$attributes .= 'class="inputbox" ';
		}

		$m = $this->element['multiple'];

		if ($m)
		{
			$type = 'checkbox';
		}

		$value = $this->value;

		if (!is_array($value))
		{
			// Convert the selections field to an array.
			$registry = new Registry;
			$registry->loadString($value);
		}

		if ($disabled || $readonly)
		{
			$attributes .= 'disabled="disabled"';
		}

		$query->select("m.mailinglist_id AS selected");
		$query->from($db->quoteName('#__bwpostman_campaigns_mailinglists') . ' AS m');
		$query->where($db->quoteName('m.campaign_id') . ' = ' . $db->quote((int)$cam_id));

		try
		{
			$db->setQuery($query);

			$ml_select = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		$i = 0;

		foreach ($options as $option)
		{
			if (is_array($ml_select))
			{
				$selected = (in_array($option->value, $ml_select) ? ' checked="checked"' : '');
			}

			$i++;
			$return	.= '<p class="mllabel"><label for="' . $this->id . '_' . $i . '" class="mailinglist_label noclear checkbox">';
			$return	.= '<input type="' . $type . '" id="' . $this->id . '_' . $i . '" name="' . $this->name . '[]" ';
			$return	.= 'value="' . $option->value . '"' . $attributes . $selected . ' />';
			$return	.= '<span class="editlinktip hasTip hasTooltip" title="' . $option->text . '">' . $option->title . '</span></label></p>';
		}

		return $return;
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
		$options        = array();

		// prepare query
		$db		= Factory::getDbo();
		$query		= $db->getQuery(true);

		$query->select("id AS value, title, description AS text");
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('published') . ' = ' . 0);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);
		$query->order('title ASC');

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
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
