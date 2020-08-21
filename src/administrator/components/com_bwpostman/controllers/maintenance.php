<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance controller for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/maintenancehelper.php');

/**
 * BwPostman Maintenance Controller
 *
 * @package		BwPostman-Admin
 * @subpackage	Maintenance
 *
 * @since       1.0.1
 */
class BwPostmanControllerMaintenance extends JControllerLegacy
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_MAINTENANCE';

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return void
	 *
	 * @since	1.0.1
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('checkTables', 'checkTables');
		$this->registerTask('saveTables', 'saveTables');
		$this->registerTask('restoreTables', 'restoreTables');
		$this->registerTask('updateCheckSave', 'updateCheckSave');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	JModelLegacy
	 *
	 * @since	1.0.1
	 */
	public function getModel($name = 'Maintenance', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerMaintenance		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!BwPostmanHelper::canView('maintenance'))
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		parent::display();

		return $this;
	}

	/**
	 * Method to call the view for the save tables process
	 * --> we will take the raw-view which calls the saveTables-function in the model
	 *
	 * @return 	void
	 *
	 * @since	1.3.0
	 */
	public function updateCheckSave()
	{
		$model = $this->getModel();

		ob_start();

		// first save all tables
		echo '<br /><br /><div class="well">';
		echo '<h2>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES') . '</h2>';
		$model->saveTables(null, true);
		ob_flush();
		flush();

		// then make the checks (function repairs tables automatically)
		echo '<br /><br /><h2>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES') . '</h2>';
		$model->checkTables();
		echo '</div>';
		ob_flush();
		flush();

		$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=checkTables', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the view for the save tables process
	 * --> we will take the raw-view which calls the saveTables-function in the model
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function saveTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin('maintenance'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$jinput   = Factory::getApplication()->input;
		$document = Factory::getDocument();

		$jinput->set('view', 'subscriber');

		$document->setType('raw');

		$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=saveTables&format=raw', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the check tables process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function checkTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin('maintenance'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=checkTables', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the restore tables process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function restoreTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin('maintenance'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the restore tables process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function doRestore()
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Access check.
		if (!BwPostmanHelper::canAdmin('maintenance'))
		{
			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Retrieve file details from uploaded file, sent from upload form
		$file = $jinput->files->get('restorefile');

		jimport('joomla.filesystem.file');

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = File::makeSafe($file['name']);

		// Set up the source and destination of the file
		$src = $file['tmp_name'];

		// If the file isn't okay, redirect to restoretables.php
		if ($file['error'] > 0)
		{
			//http://de.php.net/features.file-upload.errors
			$msg = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD');

			switch ($file['error'])
			{
				case '1':
				case '2':
					$msg .= Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD_SIZE');
					break;
				case '3':
					$msg .= Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD_PART');
					break;
				case '4':
					$msg .= Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_NO_FILE');
					break;
			}

			$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
			$this->setRedirect($link, $msg, 'error');

		}
		else
		{ // The file is okay
			// Check if the file has the right extension, we need xml
			// --> if the extension is wrong, redirect to restoretables.php
			$fileExt = File::getExt($filename);
			$dest    = Factory::getConfig()->get('tmp_path') . '/tmp_bwpostman_tablesav.' . $fileExt;

			if ($fileExt !== 'xml' && $fileExt !== 'zip')
			{
				$msg = Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_TYPE');
				$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
				$this->setRedirect($link, $msg, 'error');

				// Check if the extension is identical to the selected file format
				// --> if not, redirect to import.php
			}
			else
			{ // Everything is fine
				if (File::upload($src, $dest) === false)
				{
					$msg	= Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_FILE');
					$link	= Route::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
					$this->setRedirect($link, $msg, 'error');
				}
				else
				{
					if ($fileExt === 'zip')
					{
						$dest = BwPostmanMaintenanceHelper::decompressBackupFile($dest, $filename);
					}

					$app->setUserState('com_bwpostman.maintenance.dest', $dest);

					$link = Route::_('index.php?option=com_bwpostman&view=maintenance&layout=doRestore', false);
				}
			}
		}

		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to start cron server
	 *
	 * @return boolean
	 *
	 * @throws  Exception
	 *
	 * @since       2.3.0
	 */
	public function startCron()
	{
		$lang = Factory::getLanguage();
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_PLUGINS . '/bwpostman/bwtimecontrol');

		$plugin = PluginHelper::getPlugin('bwpostman', 'bwtimecontrol');

		$pluginParams = new JRegistry();
		$pluginParams->loadString($plugin->params);

		$pluginUser = $pluginParams->get('bwtimecontrol_username',null);
		$pluginPw   = $pluginParams->get('bwtimecontrol_passwd', null);

		if ($pluginUser === null || $pluginPw === null)
		{
			Factory::getApplication()->enqueueMessage(Text::_('PLG_BWTIMECONTROL_NO_CREDENTIALS'), 'error');

			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);

			return false;
		}
		else
		{
//			PluginHelper::importPlugin('bwpostman', 'bwtimecontrol');
			$results = Factory::getApplication()->triggerEvent('onBwPostmanMaintenanceStartCron', array());

			if ($results[0] !== true)
			{
				$error = '';

				foreach ($results as $result)
				{
					$error .= $result . '<br />';
				}

				$error .= Text::_('PLG_BWTIMECONTROL_MAINTENANCE_ERROR_CRON');
				Factory::getApplication()->enqueueMessage($error, 'error');
			}

			$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return true;
		}
	}

	/**
	 * Method to stop cron server
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	public function stopCron()
	{
		$lang = Factory::getLanguage();
		$lang->load('plg_bwpostman_bwtimecontrol', JPATH_ADMINISTRATOR);

		PluginHelper::importPlugin('bwpostman', 'bwtimecontrol');
		Factory::getApplication()->triggerEvent('onBwPostmanMaintenanceStopCron', array());

		$link = Route::_('index.php?option=com_bwpostman&view=maintenance', false);
		$this->setRedirect($link);
		return true;
	}
}
