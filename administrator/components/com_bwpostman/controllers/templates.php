<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates controller for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Import CONTROLLER and Helper object class
jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Templates Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Templates
 *
 * @since       1.1.0
 */
class BwPostmanControllerTemplates extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 * @since	2.0.0
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_TPLS';

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @since	1.1.0
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

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
	 * @return	JModelLegacy

	 * @since	1.1.0
	 */
	public function getModel($name = 'Template', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
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
	 * @return  BwPostmanControllerTemplates		This object to support chaining.
	 *
	 * @since	1.1.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!BwPostmanHelper::canView('template'))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		$jinput		= JFactory::getApplication()->input;

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
	 * Method to (un)publish a template
	 *
	 * @access	public
	 *
	 * @return	boolean
	 *
	 * @since	1.1.0
	 */
	public function publish()
	{
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Get the selected template(s)
		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		// Access check
		if (!BwPostmanHelper::canEditState('template', $cid))
		{
			return false;
		}

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		// count selected standard templates
		$query->select($db->quoteName('standard'));
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('id')." IN (".implode(",", $cid).")");
		$query->where($db->quoteName('standard')." = ".$db->quote(1));

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}
		$count_std = $db->getNumRows();

		// unpublish only, if no standard template is selected
		if ($count_std > 0 && $this->getTask() == 'unpublish')
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CANNOT_UNPUBLISH_STD_TPL'), 'error');
			$link = JRoute::_('index.php?option=com_bwpostman&view=templates',false);
			$this->setRedirect($link, JText::_('COM_BWPOSTMAN_CANNOT_UNPUBLISH_STD_TPL'), 'error');
		}
		else
		{
			parent::publish();
		}
	}

	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @access	public
	 *
	 * @since       1.1.0
	 */
	public function uploadtpl()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Access check.
		if (!BwPostmanHelper::canAdd('template'))
		{
			return false;
		}

		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		// Get file details from uploaded file
		$file = $jinput->files->get('uploadfile', null, 'raw');
		$app->setUserState('com_bwpostman.templates.uploadfile', $file);
		$model	= $this->getModel('templates');

		$msg = $model->uploadTplFiles($file);

		if ($msg)
		{
			$link	= JRoute::_('index.php?option=com_bwpostman&view=templates', false);
			$this->setRedirect($link, $msg, 'error');
		}
		else
		{
			$link	= JRoute::_('index.php?option=com_bwpostman&view=templates&layout=installtpl', false);
			$this->setRedirect($link);
		}
	}
}
