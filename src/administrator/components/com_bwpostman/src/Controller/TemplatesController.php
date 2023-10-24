<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates controller for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Templates Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Templates
 *
 * @since       1.1.0
 */
class TemplatesController extends AdminController
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 * @since	2.0.0
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_TPLS';

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold id
	 *
	 * @var integer $id
	 *
	 * @since       2.4.0
	 */
	public $id;

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('addhtml', 'addhtml');
		$this->registerTask('addtext', 'addtext');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	BaseDatabaseModel

	 * @since	1.1.0
	 */
	public function getModel($name = 'Template', $prefix = 'Administrator', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  TemplatesController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function display($cachable = false, $urlparams = array()): TemplatesController
	{
		if (!$this->permissions['view']['template'])
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput = Factory::getApplication()->input;

		// Show the layout depending on the task
		switch($this->getTask())
		{
			default:
					$jinput->set('hidemainmenu', 0);
					$jinput->set('view', 'templates');
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
			$allowed = BwPostmanHelper::canEditState('template', (int) $recordId);

			if (!$allowed)
			{
				$link = Route::_('index.php?option=com_bwpostman&view=templates', false);
				$this->setRedirect($link, Text::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to (un)publish a template
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function publish(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app    = Factory::getApplication();
		$jinput = Factory::getApplication()->input;

		// Get the selected template(s)
		$cid = $jinput->get('cid', array(0), 'post');
		$cid = ArrayHelper::toInteger($cid);

		// Access check
		if (!$this->allowPublish($cid))
		{
			return false;
		}

		$model     = $this->getModel('template');
		$tplTable  = $model->getTable();
		$count_std = $tplTable->getNumberOfStdTemplates($cid);

		// unpublish only, if no standard template is selected
		if ($count_std > 0 && $this->getTask() == 'unpublish')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_CANNOT_UNPUBLISH_STD_TPL'), 'error');
			$link = Route::_('index.php?option=com_bwpostman&view=templates', false);
			$this->setRedirect($link, Text::_('COM_BWPOSTMAN_CANNOT_UNPUBLISH_STD_TPL'), 'error');
		}
		else
		{
			parent::publish();
		}

		return true;
	}

	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	public function uploadtpl(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Access check.
		if (!BwPostmanHelper::canAdd('template'))
		{
			return false;
		}

		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Get file details from uploaded file
		$file = $jinput->files->get('uploadfile', null, 'raw');
		$app->setUserState('com_bwpostman.templates.uploadfile', $file);
		$model	= $this->getModel('templates');

		$msg = $model->uploadTplFiles($file);

		if ($msg)
		{
			$link = Route::_('index.php?option=com_bwpostman&view=templates', false);
			$this->setRedirect($link, $msg, 'error');
		}
		else
		{
			$link = Route::_('index.php?option=com_bwpostman&view=templates&layout=installtpl', false);
			$this->setRedirect($link);
		}

		return true;
	}

	/**
	 * Method to export a newsletter template
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.1.0
	 */
	public function exportTpl()
	{
		// get newsletter ID to send
		$cids     = $this->input->get('cid', array(), 'array');
		$this->id = (int)$cids[0];

		// redirect to raw view
		$this->setRedirect(
			Route::_(
				'index.php?option=' . $this->option . '&view=templates&task=export&id=' . $this->id, false
			)
		);
	}
}
