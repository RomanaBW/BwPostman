<?php
/**
 * BwPostman User2Subscriber Plugin
 *
 * BwPostman form field class selectable mailinglists for plugin.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
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

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

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
class JFormFieldU2sMls extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since
	 */
	protected $type = 'u2smls';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$app	= JFactory::getApplication();
		$session	= JFactory::getSession();

		$mailinglists	= $session->get('plg_bwpm_user2subscriber.ml_available', array());

		// prepare query
		$_db		= JFactory::getDbo();
		$query		= $_db->getQuery(true);

		$query->select("a.id AS value, a.title AS title, a.description as description");
		$query->from('#__bwpostman_mailinglists AS a');
		$query->where($_db->quoteName('a.archive_flag') . ' = ' . (int) 0);
		if (count($mailinglists))
		{
			$query->where($_db->quoteName('a.id') . ' IN (' . implode(',', $mailinglists) . ')');
		}

		$_db->setQuery($query);
		$options = $_db->loadObjectList();

		// Check for a database error.
//		if ($_db->getErrorNum()) {
//			$app->enqueueMessage($_db->getErrorMsg(), 'error');
//		}

		// Prepare needed options properties test and checked
		$modified_options	= array();
		$show_desc			= $session->get('plg_bwpm_user2subscriber.show_desc', 'true');
		$descLength			= $session->get('plg_bwpm_user2subscriber.desc_length', '150');

		foreach ($options as $option)
		{
			$modified_option = new stdClass();
			$modified_option->checked = '';
			$modified_option->value = $option->value;

			$text  = '<span class="plg-u2s-ml-title">';
			$text .= $option->title;
			$text .= '</span>';

			if ($show_desc)
			{
				$text .= '<br />';
				$text .= '<span class="plg-u2s-ml-description">';
				$text .= substr(JText::_($option->description), 0, $descLength);

				if (strlen(JText::_($option->description)) > $descLength)
				{
					$text .= '... ';
					$text .= JHTML::tooltip(JText::_($option->description), $option->title, 'tooltip.png', '', '');
				}
				$text .= '</span>';
			}

			$modified_option->text = $text;

			$modified_options[] = $modified_option;
		}

		return $modified_options;
	}
}
