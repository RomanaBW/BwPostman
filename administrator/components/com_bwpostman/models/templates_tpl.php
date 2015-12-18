<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template tpl model for backend.
 *
 * @version 1.3.0 bwpm
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
jimport('joomla.application.component.helper');

/**
 * BwPostman campaign model
 * Provides methodes to add and edit campaigns
 *
 * @package		BwPostman-Admin
 * @subpackage	Campaigns
 */
class BwPostmanModelTemplates_Tpl extends JModelAdmin
{
	/**
	 * Alias Constructor
	 *
	 * @since 1.1.0
	 */
	public function __construct()
	{
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.1.0
	*/
	public function getTable($type = 'Templates_Tpl', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.1.0
	 */

	public function getItem($pk = null)
	{
		$item	= parent::getItem($pk);

		// convert header_tpl string to array
		if (is_string($item->header_tpl)){
			$registry = new JRegistry;
			$registry->loadString($item->header_tpl);
			$item->header_tpl = $registry->toArray();
		}

		return $item;
	}

	/**
	 * Alias Method
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.template', 'Template', array('control' => 'jform', 'load_data' => $loadData));

		return $form;
	}

}
