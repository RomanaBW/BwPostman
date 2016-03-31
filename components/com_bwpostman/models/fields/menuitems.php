<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
 *
 * @version 2.0.0 bwpm
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

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a select list.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormFieldList
 * @since       11.1
 */
class JFormFieldMenuItems extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.2.0
	 */
	protected $type = 'MenuItems';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.2.0
	 */
	protected function getOptions()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id') . ' AS value');
		$query->select($_db->quoteName('title') . ' AS text');
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=archive') . ' OR ' . $_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=newsletters'));
		$query->where($_db->quoteName('client_id') . ' = ' . (int) 0);
		$query->order($_db->quoteName('title').' ASC');

		$_db->setQuery($query);

		$options	= $_db->loadObjectList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
