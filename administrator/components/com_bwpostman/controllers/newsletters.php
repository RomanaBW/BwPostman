<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters controller for backend.
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

// Import CONTROLLER and Helper object class
jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Newsletters Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanControllerNewsletters extends JControllerAdmin
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_NLS';

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
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('sendtestmail', 'sendmail');
		$this->registerTask('sendmailandpublish', 'sendmail');
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerNewsletters		This object to support chaining.
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!BwPostmanHelper::canView('newsletter'))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
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
	 * @return	JModelLegacy

	 * @since	1.0.1
	 */
	public function getModel($name = 'Newsletter', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to copy one or more newsletters
	 *
	 * @access	public
	 *
	 * @return 	bool
	 *
	 * @since       0.9.1
	 */
	public function copy()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Access check
		if (!BwPostmanHelper::canAdd('newsletter'))
		{
			return false;
		}

		$jinput	= JFactory::getApplication()->input;

		// Which tab are we in?
		$layout = $jinput->get('tab', 'unsent');

		// Get the selected newsletter(s)
		$cid = $jinput->get('cid', array(0), 'post', 'array');
		ArrayHelper::toInteger($cid);

		$n = count ($cid);
		$model = $this->getModel('newsletter');

		if(!$model->copy($cid))
		{ // Couldn't copy
			if ($n > 1)
			{
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NLS_ERROR_COPYING', true)."'); window.history.go(-1); </script>\n";
			}
			else
			{
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NL_ERROR_COPYING', true)."'); window.history.go(-1); </script>\n";
			}
		}
		else
		{ // Copied successfully
			if ($n > 1)
			{
				$msg = JText::_('COM_BWPOSTMAN_NLS_COPIED');
			}
			else
			{
				$msg = JText::_('COM_BWPOSTMAN_NL_COPIED');
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&layout='.$layout, false);
			$this->setRedirect($link, $msg);
		}
		return true;
	}

	/**
	 * Method to publish a list of newsletters.
	 *
	 * @return	bool
	 *
	 * @since	1.0.1
	 */
	function publish()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Get the selected newsletters(s)
		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		// Access check
		if (!BwPostmanHelper::canEditState('newsletter', $cid))
		{
			return false;
		}

		// Which tab are we in?
		$layout = $jinput->get('tab', 'sent');

		// From which view do we come?
		$view = $jinput->get('view', 'newsletters');

		parent::publish();

		if ($view == 'archive')
		{
			$this->setRedirect('index.php?option=com_bwpostman&view=archive&layout=newsletters');
		}
		else
		{
			$this->setRedirect('index.php?option=com_bwpostman&view=newsletters&layout='.$layout);
		}
		return true;
	}

	/**
	 * Method to set the tab state while changing tabs, used for building the appropriate toolbar
	 *
	 * @access	public
	 *
	 * @since	1.0.1
	 */
	public function changeTab()
	{
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$tab	= $jinput->get('tab', 'unsent');

		$app->setUserState('com_bwpostman.newsletters.tab', $tab);

		$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters', false);

		$this->setRedirect($link);
	}


	/**
	 * Method to add selected content items to the newsletter
	 *
	 * @access	public
	 *
	 * @return 	string  $insert_contents    the content of the newsletter
	 *
	 * @since       0.9.1
	 */
	public function addContent()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');

		// Insert the contents into the newsletter
		$insert_contents = $model->composeNl();

		return $insert_contents;
	}

	/**
	 * Method to remove all entries from the sendmailqueue-table
	 *
	 * @access	public
	 *
	 * @return 	bool
	 *
	 * @since       0.9.1
	 */
	public function clear_queue()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Access check
		if (!BwPostmanHelper::canClearQueue())
		{
			return false;
		}

		$model = $this->getModel('newsletter');
		if(!$model->delete_queue())
		{ // Couldn't clear queue
			echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NL_ERROR_CLEARING_QUEUE', true)."'); window.history.go(-1); </script>\n";
		}
		else
		{ // Cleared queue successfully
			$msg = JText::_('COM_BWPOSTMAN_NL_CLEARED_QUEUE');

			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters',false);
			$this->setRedirect($link, $msg);
		}
		return true;
	}

	/**
	 * Method to reset the count of delivery attempts in sendmailqueue back to 0.
	 *
	 * @return bool
	 *
	 * @since       0.9.1
	 */
	public function resetSendAttempts()
	{
		// Access check
		if (!BwPostmanHelper::canResetQueue())
		{
			return false;
		}

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');
		$model->resetSendAttempts();
		$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&tab=queue', false);
		$this->setRedirect($link);
	}
}
