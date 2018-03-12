<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglist controller for backend.
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
defined('_JEXEC') or die('Restricted access');

// Import CONTROLLER and Helper object class
jimport('joomla.application.component.controllerform');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

/**
 * BwPostman Mailinglist Controller
 *
 * @since		1.0.1
 * @package 	BwPostman-Admin
 * @subpackage	Mailinglists
 */
class BwPostmanControllerMailinglist extends JControllerForm
{
	/**
	 * @var		string  The prefix to use with controller messages.

	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_ML';

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Constructor.
	 *
	 * @param 	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1

	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		parent::__construct($config);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerMailinglist		This object to support chaining.
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!$this->permissions['view']['mailinglist'])
		{
			$this->setRedirect(JRoute::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		parent::display();
		return $this;
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowAdd($data = array())
	{
		return $this->permissions['mailinglist']['create'];
	}

	/**
	 * Method override to check if you can edit a record.
	 *
	 * @param	array	$data		An array of input data.
	 * @param	string	$key		The name of the key for the primary key.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return BwPostmanHelper::canEdit('mailinglist', $data);
	}

	/**
	 * Method to check if you can archive records
	 *
	 * @param	array 	$recordIds		an array of items to check permission for
	 *
	 * @return	boolean
	 *
	 * @since	2.0.0
	 */
	protected function allowArchive($recordIds = array())
	{
		foreach ($recordIds as $recordId)
		{
			$allowed = BwPostmanHelper::canArchive('mailinglist', 0, $recordId);

			if (!$allowed)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Override method to edit an existing record, based on Joomla method.
	 * We need an override, because we want to handle state a bit different than Joomla at this point
	 *
	 * @param	string	$key		The name of the primary key of the URL variable.
	 * @param	string	$urlVar		The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return	boolean		True if access level check and checkout passes, false otherwise.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$jinput		= JFactory::getApplication()->input;
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= $jinput->post->get('cid', array(), 'array');
		$context	= "$this->option.edit.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : $jinput->getInt($urlVar));
		$checkin = property_exists($table, 'checked_out');

		// Access check.
		if ($recordId == 0)
		{
			$allowed    = $this->allowAdd();
		}
		else
		{
			$allowed    = $this->allowEdit(array('id' => $recordId), 'id');
		}

		if (!$allowed)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_EDIT_NO_PERMISSION'), 'error');
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(),
					false
				)
			);
			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice…
			JFactory::getApplication()->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');

			// …and do not allow the user to see the record.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return true;
		}
	}

	/**
	 * Method to archive one or more mailinglists
	 * --> mailinglists-table: archive_flag = 1, set archive_date
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function archive()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			jexit(JText::_('JINVALID_TOKEN'));
		}

		// Get the selected mailinglist(s)
		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		// Access check.
		if (!$this->allowArchive($cid))
		{
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(),
					false
				)
			);
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ERROR_ARCHIVE_NO_PERMISSION'), 'error');

			return;
		}

		$n = count($cid);

		$model = $this->getModel('mailinglist');
		if(!$model->archive($cid, 1))
		{
			if ($n > 1)
			{
				echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_MLS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_ML_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
			}
		}
		else
		{
			if ($n > 1)
			{
				$msg = JText::_('COM_BWPOSTMAN_MLS_ARCHIVED');
			}
			else
			{
				$msg = JText::_('COM_BWPOSTMAN_ML_ARCHIVED');
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=mailinglists', false);

			$this->setRedirect($link, $msg);
		}
	}
}
