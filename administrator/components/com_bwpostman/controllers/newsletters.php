<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters controller for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
jimport('joomla.application.component.controlleradmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Newsletters Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 */
class BwPostmanControllerNewsletters extends JControllerAdmin
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_NLS';

	/**
	 * Constructor
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
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
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
	 * @return 	Redirect
	 */
	public function copy()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Which tab are we in?
		$layout = $jinput->get('tab', 'unsent');

		// Get the selected newsletter(s)
		$cid = $jinput->get('cid', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$n = count ($cid);
		$model = $this->getModel('newsletter');

		if(!$model->copy($cid)) { // Couldn't copy
			if ($n > 1) {
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NLS_ERROR_COPYING', true)."'); window.history.go(-1); </script>\n";
			}
			else {
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NL_ERROR_COPYING', true)."'); window.history.go(-1); </script>\n";
			}
		}
		else { // Copied successfully
			if ($n > 1) {
				$msg = JText::_('COM_BWPOSTMAN_NLS_COPIED');
			}
			else {
				$msg = JText::_('COM_BWPOSTMAN_NL_COPIED');
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&layout='.$layout, false);
			$this->setRedirect($link, $msg);
		}
	}

	/**
	 * Method to publish a list of newsletters.
	 *
	 * @return	void
	 * @since	1.0.1
	 */
	function publish()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Which tab are we in?
		$layout = $jinput->get('tab', 'sent');

		// From which view do we come ?
		$view = $jinput->get('view', 'newsletters');

		parent::publish();

		if ($view == 'archive') {
			$this->setRedirect('index.php?option=com_bwpostman&view=archive&layout=newsletters');
		}
		else {
			$this->setRedirect('index.php?option=com_bwpostman&view=newsletters&layout='.$layout);
		}
	}

	/**
	 * Method to set the tab state while changing tabs, used for building the appropriate toolbar
	 *
	 * @access	public
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
	 * Method to remove all entries from the sendmailqueue-table
	 *
	 * @access	public
	 * @return 	Redirect
	 */
	public function clear_queue()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');
		if(!$model->delete_queue()) { // Couldn't clear queue
			echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NL_ERROR_CLEARING_QUEUE', true)."'); window.history.go(-1); </script>\n";
		}
		else { // Cleared queue successfully
			$msg = JText::_('COM_BWPOSTMAN_NL_CLEARED_QUEUE');

			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters',false);
			$this->setRedirect($link, $msg);
		}
	}

	/**
	 * Method to add selected content items to the newsletter
	 *
	 * @access	public
	 * @return 	Redirect
	 */
	public function addContent()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');

		// Insert the contents into the newsletter
		$insert_contents = $model->composeNl();

		return $insert_contents;
	}

	/**
	 * Method to reset the count of delivery attempts in sendmailqueue back to 0.
	 *
	 * @return unknown_type
	 */
	public function resetSendAttempts()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('newsletter');
		$model->resetSendAttempts();
		$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&tab=queue', false);
		$this->setRedirect($link);
	}
}
