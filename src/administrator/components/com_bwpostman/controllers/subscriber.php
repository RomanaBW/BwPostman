<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber controller for backend.
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
 * BwPostman Subscriber Controller
 *
 * @since		1.0.1
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class BwPostmanControllerSubscriber extends JControllerForm
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_SUB';

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
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 *
	 * @see		JController

	 * @since	1.0.1
	 */
	public function __construct($config = array())
	{
		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add_test', 'add_test');
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BwPostmanControllerSubscriber		This object to support chaining.
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if (!$this->permissions['view']['subscriber'])
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
		return BwPostmanHelper::canAdd('subscriber');
	}

	/**
	 * Method override to check if you can edit a record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return BwPostmanHelper::canEdit('subscriber', $data);
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
			$allowed = BwPostmanHelper::canArchive('subscriber', 0, (int) $recordId);

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
	 * @throws Exception
	 *
	 * @return	boolean		True if access level check and checkout passes, false otherwise.
	 *
	 * @since	1.0.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$jinput		= $app->input;
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
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');

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
	 * Overwrite for method to add a new record for a subscriber
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function add()
	{
		// set state for normal subscriber…
		JFactory::getApplication()->setUserState('com_bwpostman.subscriber.new_test', '0');

		parent::add();
	}

	/**
	 * Overwrite for method to add a new record for a test-recipient
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function add_test()
	{
		// set state for test-recipient…
		JFactory::getApplication()->setUserState('com_bwpostman.subscriber.new_test', '9');

		parent::add();
	}

	/**
	 * Method to archive one or more subscribers/test-recipients
	 * --> subscribers-table: archive_flag = 1, set archive_date
	 *
	 * @return 	bool    true on success
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

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Get the selected campaign(s)
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

			return false;
		}

		$n = count($cid);

		$model = $this->getModel('subscriber');
		if(!$model->archive($cid, 1)) { // Couldn't archive
			if ($layout == 'testrecipients')
			{
				if ($n > 1)
				{
					echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_TESTS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_TEST_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
			}
			else
			{
				if ($n > 1)
				{
					echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_SUBS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					echo "<script> alert ('" . JText::_('COM_BWPOSTMAN_SUB_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
			}
		}
		else
		{ // Archived successfully
			if ($layout == 'testrecipients')
			{
				if ($n > 1)
				{
					$msg = JText::_('COM_BWPOSTMAN_TESTS_ARCHIVED');
				}
				else
				{
					$msg = JText::_('COM_BWPOSTMAN_TEST_ARCHIVED');
				}
			}
			else
			{
				if ($n > 1)
				{
					$msg = JText::_('COM_BWPOSTMAN_SUBS_ARCHIVED');
				}
				else
				{
					$msg = JText::_('COM_BWPOSTMAN_SUB_ARCHIVED');
				}
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=subscribers', false);
			$this->setRedirect($link, $msg);
		}

		return true;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param	object	$model	The model.
	 *
	 * @return	boolean	True if successful, false otherwise and internal error is set.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.8
	 *
	 */
	public function batch($model = null)
	{
		// Check for request forgeries
		if (!JSession::checkToken())
		{
			jexit(JText::_('JINVALID_TOKEN'));
		}

		$app		= JFactory::getApplication();
		$jinput		= $app->input;
		$vars		= $jinput->post->get('batch', array(), 'array');
		$cid		= $jinput->post->get('cid', array(), 'array');
		$old_list	= JFactory::getSession()->get('com_bwpostman.subscriber.batch_filter_mailinglist', null);
		$message	= '';

		// Build an array of item contexts to check
		$contexts = array();
		foreach ($cid as $id)
		{
			// If we're coming from com_categories, we need to use extension vs. option
			if (isset($this->extension))
			{
				$option = $this->extension;
			}
			else
			{
				$option = $this->option;
			}

			$contexts[$id] = $option . '.' . $this->context . '.' . $id;
		}

		// Set the model and sone variables
		$model	= $this->getModel('Subscriber', '', array());

		// run the batch operation.
		$results	= $model->batch($vars, $cid, $contexts);

		// Check results.
		if (is_array($results))
		{
			foreach ($results as $result)
			{
				if ($result['task']	== 'subscribe')
				{
					$sub_text	= JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE', $vars['mailinglist_id']);
					$message	= JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					$message	.= ' ' . JText::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE_N_ITEMS', $result['done']);
					$message	.= ' ' . JText::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE_SKIPPED_N_ITEMS', $result['skipped']);
				}

				if ($result['task']	== 'unsubscribe')
				{
					if ($message == '') {
						$sub_text	= JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE', $vars['mailinglist_id']);
						$message	= JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					}
					else
					{
						$sub_text	= JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE', $old_list);
						$message	.= '<br />' . JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					}

					$message	.= ' ' . JText::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE_N_ITEMS', $result['done']);
					$message	.= ' ' . JText::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE_SKIPPED_N_ITEMS', $result['skipped']);
				}

				$this->setMessage($message);
			}
		}
		elseif ($results < 0)
		{
			$this->setMessage(JText::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_NOTHING_TO_MOVE', $old_list), 'warning');
		}
		else
		{
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
		}

		// Set the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=subscribers' . $this->getRedirectToListAppend(), false));

		return true;
	}
}
