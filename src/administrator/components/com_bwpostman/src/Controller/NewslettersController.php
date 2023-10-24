<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters controller for backend.
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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Newsletters Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class NewslettersController extends AdminController
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_NLS';

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
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('sendtestmail', 'sendmail');
		$this->registerTask('sendmailandpublish', 'sendmail');

		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  NewslettersController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array()): NewslettersController
	{
		if (!$this->permissions['view']['newsletter'])
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		parent::display();

		return $this;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	BaseDatabaseModel

	 * @since	1.0.1
	 */
	public function getModel($name = 'Newsletter', $prefix = 'Administrator', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method to copy one or more newsletters
	 *
	 * @return 	bool
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function copy(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Access check
		if (!$this->permissions['newsletter']['create'])
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;

		// Which tab are we in?
		$layout = $jinput->get('tab', 'unsent');

		// Get the selected newsletter(s)
		$cid = $jinput->get('cid', array(0), 'post');
		$cid = ArrayHelper::toInteger($cid);

		$n     = count($cid);
		$model = $this->getModel('newsletter');

		if(!$model->copy($cid[0]))
		{ // Couldn't copy
			if ($n > 1)
			{
				echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_NLS_ERROR_COPYING', true) . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_NL_ERROR_COPYING', true) . "'); window.history.go(-1); </script>\n";
			}
		}
		else
		{ // Copied successfully
			if ($n > 1)
			{
				$msg = Text::_('COM_BWPOSTMAN_NLS_COPIED');
			}
			else
			{
				$msg = Text::_('COM_BWPOSTMAN_NL_COPIED');
			}

			$link = Route::_('index.php?option=com_bwpostman&view=newsletters&layout=' . $layout, false);
			$this->setRedirect($link, $msg);
		}

		return true;
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
			$allowed = BwPostmanHelper::canEditState('newsletter', (int) $recordId);

			if (!$allowed)
			{
				$link = Route::_('index.php?option=com_bwpostman&view=newsletters', false);
				$this->setRedirect($link, Text::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to publish a list of newsletters.
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function publish(): bool
	{
		$jinput	= Factory::getApplication()->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Get the selected newsletters(s)
		$cid = $jinput->get('cid', array(0), 'post');
		$cid = ArrayHelper::toInteger($cid);

		// Access check
		if (!$this->allowPublish($cid))
		{
			return false;
		}

		// Which tab are we in?
		$tab = $jinput->get('tab', 'sent');

		// From which view do we come?
		$view = $jinput->get('view', 'newsletters');

		parent::publish();

		if ($view == 'archive')
		{
			$this->setRedirect('index.php?option=com_bwpostman&view=Archive&layout=newsletters');
		}
		else
		{
			$this->setRedirect('index.php?option=com_bwpostman&view=newsletters&tab=' . $tab);
		}

		return true;
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
			$allowed = BwPostmanHelper::canCheckin('newsletter', $item);

			// Access check.
			if ($allowed)
			{
				$res = parent::checkin();
			}
			else
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_EDITSTATE_NO_PERMISSION'), 'error');
				$this->setRedirect(Route::_('index.php?option=com_bwpostman&view=newsletters', false));
				return false;
			}
		}

		return $res;
	}

	/**
	 * Method to set the tab state while changing tabs, used for building the appropriate toolbar
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function changeTab()
	{
		$app	= Factory::getApplication();
		$jinput	= $app->input;
		$tab	= $jinput->get('tab', 'unsent');

		$app->setUserState('com_bwpostman.newsletters.tab', $tab);

		$link = Route::_('index.php?option=com_bwpostman&view=newsletters', false);

		$this->setRedirect($link);
	}

	/**
	 * Method to add selected content items to the newsletter
	 *
	 * @return 	string  $insert_contents    the content of the newsletter
	 *
	 * @since       0.9.1
	 */
	public function addContent(): string
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$model = $this->getModel('newsletter');

		// Insert the contents into the newsletter
		return $model->composeNl();
	}

	/**
	 * Method to remove all entries from the sendmailqueue-table
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function clear_queue(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Access check
		if (!BwPostmanHelper::canClearQueue())
		{
			return false;
		}

		$model = $this->getModel('newsletter');
		if(!$model->delete_queue())
		{ // Couldn't clear queue
			echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_NL_ERROR_CLEARING_QUEUE', true) . "'); window.history.go(-1); </script>\n";
		}
		else
		{ // Cleared queue successfully
			$msg = Text::_('COM_BWPOSTMAN_NL_CLEARED_QUEUE');

			$link = Route::_('index.php?option=com_bwpostman&view=newsletters&tab=unsent', false);
			$this->setRedirect($link, $msg);
		}

		return true;
	}

	/**
	 * Method to reset the count of delivery attempts in sendmailqueue back to 0.
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function resetSendAttempts(): bool
	{
		// Access check
		if (!BwPostmanHelper::canResetQueue())
		{
			return false;
		}

		// Check for request forgeries
		if (!Session::checkToken()) jexit(Text::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');
		$model->resetSendAttempts();
		$link = Route::_('index.php?option=com_bwpostman&view=newsletters&tab=queue', false);
		$this->setRedirect($link);

		return true;
	}
}
