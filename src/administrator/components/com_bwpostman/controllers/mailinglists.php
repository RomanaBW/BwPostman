<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglists controller for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
defined('_JEXEC') or die('Restricted access');

// Import CONTROLLER object class
jimport('joomla.application.component.controlleradmin');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

/**
 * BwPostman Mailinglists Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Mailinglists
 *
 * @since       0.9.1
 */
class BwPostmanControllerMailinglists extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_MLS';

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 *
	 * @since	1.0.1
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	JModel
	 *
	 * @since	1.0.1
	 */
	public function getModel($name = 'Mailinglist', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerMailinglists		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		if (!$this->permissions['view']['mailinglist'])
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput		= JFactory::getApplication()->input;

		// Show the layout depending on the task
		switch($this->getTask())
		{
			case 'add':
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'mailinglist');
				break;

			case 'edit':
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
		return $this;
	}

	/**
	 * Method to check if you can publish/unpublish records
	 *
	 * @param	array 	$recordIds		an array of items to check permission for
	 *
	 * @return	boolean
	 *
	 * @since	2.0.0
	 */
	protected function allowPublish($recordIds = array())
	{
		foreach ($recordIds as $recordId)
		{
			$allowed = BwPostmanHelper::canEditState('mailinglist', 0, $recordId);

			if (!$allowed)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to (un)publish mailinglists
	 *
	 * @access	public
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function publish()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			jexit(JText::_('JINVALID_TOKEN'));
		}

		// Get the selected template(s)
		$cid = $jinput->get('cid', array(0), 'post');
		\Joomla\Utilities\ArrayHelper::toInteger($cid);

		// Access check
		if (!$this->allowPublish($cid))
		{
			return false;
		}

		parent::publish();

		return true;
	}
}
