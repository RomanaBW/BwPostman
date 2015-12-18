<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglists controller for backend.
 *
 * @version 1.3.0 bwpm
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

// Import CONTROLLER object class
jimport('joomla.application.component.controlleradmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Mailinglists Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Mailinglists
 */
class BwPostmanControllerMailinglists extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_MLS';

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name		The name of the model.
	 * @param	string	$prefix		The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.0.1
	 */
	public function getModel($name = 'Mailinglist', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Display
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$jinput		= JFactory::getApplication()->input;
		$allowed	= FALSE;
		$user		= JFactory::getUser();

		// Show the layout depending on the task
		switch($this->getTask())
		{
			case 'add'     :
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'mailinglist');
				break;

			case 'edit'    :
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'mailinglist');

				break;
			default:
				$jinput->set('hidemainmenu', 0);
				$jinput->set('view', 'mailinglists');
				break;
		}
		parent::display();
	}
}
