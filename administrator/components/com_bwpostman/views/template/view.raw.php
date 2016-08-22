<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template view for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman template View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	template
 *
 * @since       1.1.0
 */
class BwPostmanViewTemplate extends JViewLegacy
{
	/**
	 * property to hold preview data
	 *
	 * @var string  $pre
	 *
	 * @since       1.1.0
	 */
	protected $pre;

	/**
	 * Display
	 *
	 * @access	public
	 *
	 * @param	string $tpl Template
	 *
	 * @since	1.1.0
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		// load template data and decode object
		$pre =	JFactory::getApplication()->getUserState('com_bwpostman.edit.template.tpldata');

		$this->pre	= $pre;

		// clear preview data
		JFactory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', null);

		// Call parent display
		parent::display($tpl);
	}
}
