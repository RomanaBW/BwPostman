<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance model for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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
jimport('joomla.application.component.model');

// Require helper classes
require_once (JPATH_ADMINISTRATOR.'/components/com_bwpostman/helpers/tablehelper.php');

/**
 * BwPostman maintenance page model
 *
 * @package		BwPostman-Admin
 * @subpackage	MaintenancePage
 */
class BwPostmanModelMaintenance extends JModelLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Method to checkTables
	 *
	 * @access 	public
	 * @return 	array success message with type of message
	 */
	public function checkTables()
	{
		$app	= JFactory::getApplication();
		$msg	= array();
		
		if (!BwPostmanTableHelper::checkTables()) {
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR');
			$msg['type']	= 'error';
		}
		else {
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OK');
			$msg['type']	= 'message';
		}
		return $msg;
	}

	/**
	 * Method to save tables
	 *
	 * @access 	public
	 * @return 	
	 */
	public function saveTables($update = false)
	{
		$res	= BwPostmanTableHelper::saveTables($update);
//dump ($res, 'Model SaveTables Result');
		return $res;
	}

	/**
	 * Method to restore tables
	 *
	 * @access 	public
	 * @return 	
	 */
	public function restoreTables($file	= '')
	{
		$app	= JFactory::getApplication();
		
		$msg	= array();
		
		if ($file == '') {
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_ERROR_NO_FILE');
			$msg['type']	= 'error';
		}
		
		if (!BwPostmanTableHelper::restoreTables($file)) {
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_ERROR');
			$msg['type']	= 'error';
		}
		else {
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OK');
			$msg['type']	= 'message';
		}
		return $msg;
	}
}