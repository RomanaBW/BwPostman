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
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

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
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	protected function getOptions()
	{
		$options    = null;
		$db	    = Factory::getDbo();
		$query	    = $db->getQuery(true);

		$query->select($db->quoteName('id') . ' AS value');
		$query->select($db->quoteName('title') . ' AS text');
		$query->from($db->quoteName('#__menu'));
		$query->where(
			$db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_bwpostman&view=Archive')
			. ' OR ' . $db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_bwpostman&view=Newsletters')
		);
		$query->where($db->quoteName('client_id') . ' = ' . 0);
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
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
