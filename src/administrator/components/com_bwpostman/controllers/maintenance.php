<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance controller for backend.
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
defined ('_JEXEC') or die ('Restricted access');

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

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
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerMaintenance		This object to support chaining.
	 *
	 * @since   1.0.1
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!BwPostmanHelper::canView('maintenance'))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
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
	 * @access	public
	 *
	 * @since	1.3.0
	 */
	public function updateCheckSave()
	{
		$model	= $this->getModel();

		ob_start();

		// first save all tables
		echo '<br /><br /><div class="well">';
		echo '<h2>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES') . '</h2>';
		$model->saveTables(true);
		ob_flush();
		flush();

		// then make the checks (function repairs tables automatically)
		echo '<br /><br /><h2>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES') . '</h2>';
		$model->checkTables();
		echo '</div>';
		ob_flush();
		flush();

		$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=checkTables', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the view for the save tables process
	 * --> we will take the raw-view which calls the saveTables-function in the model
	 *
	 * @access	public
	 *
	 * @return boolean
	 *
	 * @since       1.0.1
	 */
	public function saveTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin())
		{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$jinput		= JFactory::getApplication()->input;
		$document	= JFactory::getDocument();

		$jinput->set('view', 'subscriber');

		$document->setType('raw');

		$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=saveTables&format=raw', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the check tables process
	 *
	 * @access	public
	 *
	 * @return boolean
	 *
	 * @since       1.0.1
	 */
	public function checkTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin())
		{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=checkTables', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the restore tables process
	 *
	 * @access	public
	 *
	 * @since       1.0.1
	 */
	public function restoreTables()
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin())
		{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MAINTENANCE_MISSING_RIGHTS'), 'warning');
			$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method to call the layout for the restore tables process
	 *
	 * @access	public
	 *
	 * @return boolean
	 *
	 * @since       1.0.1
	 */
	public function doRestore()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Access check.
		if (!BwPostmanHelper::canAdmin())
		{
			$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance', false);
			$this->setRedirect($link);
			return false;
		}

		$jinput	= JFactory::getApplication()->input;

		$app	= JFactory::getApplication();

		// Retrieve file details from uploaded file, sent from upload form
		$file = $jinput->files->get('restorefile');

		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);

		// Set up the source and destination of the file
		$src	= $file['tmp_name'];

		$ext	= JFile::getExt($filename);
		$dest	= JFactory::getConfig()->get('tmp_path') . '/tmp_bwpostman_tablesav.' . $ext;

		// If the file isn't okay, redirect to restoretables.php
		if ($file['error'] > 0)
		{
			//http://de.php.net/features.file-upload.errors
			$msg = JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD');

			switch ($file['error'])
			{
				case '1':
				case '2': $msg .= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD_SIZE');
				break;
				case '3': $msg .= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_UPLOAD_PART');
				break;
				case '4': $msg .= JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ERROR_NO_FILE');
				break;
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
			$this->setRedirect($link, $msg, 'error');

		}
		else
		{ // The file is okay
			// Check if the file has the right extension, we need xml
			// --> if the extension is wrong, redirect to restoretables.php
			if (strtolower(JFile::getExt($filename)) !== 'xml')
			{
				$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_TYPE');
				$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
				$this->setRedirect($link, $msg, 'error');

			// Check if the extension is identical to the selected file format
			// --> if not, redirect to import.php
			}
			else
			{ // Everything is fine
				if (JFile::upload($src, $dest) === false)
				{
					$msg	= JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_FILE');
					$link	= JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=restoreTables&task=restoreTables', false);
					$this->setRedirect($link, $msg, 'error');
				}
				else
				{
					// @ToDo: delete uploaded file after use
					$app->setUserState('com_bwpostman.maintenance.dest', $dest);

					$link = JRoute::_('index.php?option=com_bwpostman&view=maintenance&layout=doRestore', false);
				}
			}
		}
		$this->setRedirect($link);
		return true;
	}
}
