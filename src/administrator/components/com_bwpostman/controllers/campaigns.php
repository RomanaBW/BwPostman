<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns controller for backend.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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
 * BwPostman Campaigns Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 *
 * @since       0.9.1
 */
class BwPostmanControllerCampaigns extends JControllerAdmin
{
	/**
	 * @var		string		The prefix to use with controller messages.

	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_CAMS';

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
	 * @throws Exception
	 *
	 * @since	1.0.1
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	JModelLegacy

	 * @since	1.0.1
	 */
	public function getModel($name = 'Campaign', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerCampaigns		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!$this->permissions['view']['campaign'])
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput	= JFactory::getApplication()->input;

		switch($this->getTask())
		{
			case 'add':
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'campaign');
				break;

			case 'edit':
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'campaign');
				break;
/*
			case 'activate':
				$jinput->set('hidemainmenu', 0);
				$jinput->set('layout', 'default');
				$jinput->set('view', 'campaigns');
				break;

			case 'autotest':
				$jinput->set('hidemainmenu', 0);
				$jinput->set('layout', 'default');
				$jinput->set('view', 'campaigns');
				break;

			case 'dueSend':
//echo "Test Task";
				$jinput->set('hidemainmenu', 0);
				$jinput->set('layout', 'default');
				$jinput->set('view', 'campaigns');
				break;
*/
			default:
				$jinput->set('hidemainmenu', 0);
				$jinput->set('view', 'campaigns');
				break;
		}
		parent::display();
		return $this;
	}

	/**
	 * Override method to checkin an existing record, based on Joomla method.
	 * We need an override, because we want to handle this a bit different than Joomla at this point
	 *
	 * @return	boolean		True if access level check and checkout passes, false otherwise.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function checkin()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		$res = true;

		foreach ($ids as $item)
		{
			$allowed = BwPostmanHelper::canCheckin('campaign', $item);

			// Access check.
			if ($allowed)
			{
				$res = parent::checkin();
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');
				$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=campaigns', false));
				return false;
			}
		}

		return $res;
	}
}
