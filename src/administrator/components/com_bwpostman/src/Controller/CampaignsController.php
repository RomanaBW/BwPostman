<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns controller for backend.
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
 * BwPostman Campaigns Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 *
 * @since       0.9.1
 */
class CampaignsController extends AdminController
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
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

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
	 * @return bool|BaseDatabaseModel
	 *
	 * @since	1.0.1
	 */
	public function getModel($name = 'Campaign', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  CampaignsController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($cachable = false, $urlparams = array()): CampaignsController
	{
		if (!$this->permissions['view']['campaign'])
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput	= Factory::getApplication()->input;

		switch($this->getTask())
		{
			case 'add':
			case 'edit':
				$jinput->set('hidemainmenu', 1);
				$jinput->set('layout', 'form');
				$jinput->set('view', 'campaign');
				break;
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
	public function checkin(): bool
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = Factory::getApplication()->input->post->get('cid', array(), 'array');
		$ids = ArrayHelper::toInteger($ids);
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
				Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');
				$this->setRedirect(Route::_('index.php?option=com_bwpostman&view=campaigns', false));
				return false;
			}
		}

		return $res;
	}
}
