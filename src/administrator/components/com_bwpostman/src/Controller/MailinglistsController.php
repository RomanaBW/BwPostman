<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglists controller for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Mailinglists Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Mailinglists
 *
 * @since       0.9.1
 */
class MailinglistsController extends AdminController
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
	public array $permissions;

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
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return bool|BaseDatabaseModel
	 *
	 * @since	1.0.1
	 */
	public function getModel($name = 'Mailinglist', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   boolean  $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  MailinglistsController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($cachable = false, $urlparams = false): MailinglistsController
	{
		if (!$this->permissions['view']['mailinglist'])
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput = Factory::getApplication()->input;

		// Show the layout depending on the task
		switch($this->getTask())
		{
			case 'add':
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
	 * @param array $recordIds an array of items to check permission for
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.0.0
	 */
	protected function allowPublish(array $recordIds = array()): bool
	{
		foreach ($recordIds as $recordId)
		{
			$allowed = BwPostmanHelper::canEditState('mailinglist', (int) $recordId);

			if (!$allowed)
			{
				$link = Route::_('index.php?option=com_bwpostman&view=mailinglists', false);
				$this->setRedirect($link, Text::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');

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
	public function publish(): bool
	{
		$jinput	= Factory::getApplication()->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Get the selected mailinglists(s)
		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		// Access check
		if (!$this->allowPublish($cid))
		{
			return false;
		}

		parent::publish();

		return true;
	}
}
