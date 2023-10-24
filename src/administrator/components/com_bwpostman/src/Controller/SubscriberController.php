<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber controller for backend.
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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Subscriber Controller
 *
 * @since		1.0.1
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class SubscriberController extends FormController
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
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('add_test', 'add_test');
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  SubscriberController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array()): SubscriberController
	{
		if (!$this->permissions['view']['subscriber'])
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
	 * @param string $name   The name of the model.
	 * @param string $prefix The prefix for the PHP class name.
	 * @param array  $config An optional associative array of configuration settings.
	 *
	 * @return bool|BaseDatabaseModel
	 *
	 * @throws Exception
	 *
	 * @since    4.0.0
	 */
	public function getModel($name = 'Subscriber', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method-override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function allowAdd($data = array()): bool
	{
		return BwPostmanHelper::canAdd('subscriber');
	}

	/**
	 * Method-override to check if you can edit a record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function allowEdit($data = array(), $key = 'id'): bool
	{
		return BwPostmanHelper::canEdit('subscriber', $data);
	}

	/**
	 * Method to check if you can archive records
	 *
	 * @param array $recordIds an array of items to check permission for
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.0.0
	 */
	protected function allowArchive(array $recordIds = array()): bool
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
	 * We need an override, because we want to handle state a bit different from Joomla at this point
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
	public function edit($key = null, $urlVar = null): bool
	{
		// Initialise variables.
		$app     = Factory::getApplication();
		$jinput  = $app->input;
		$model   = $this->getModel();
		$table   = $model->getTable();
		$cid     = $jinput->post->get('cid', array(), 'array');
		$cid     = ArrayHelper::toInteger($cid);
		$context = "$this->option.edit.$this->context";

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
		if ($recordId === 0)
		{
			$allowed    = $this->allowAdd();
		}
		else
		{
			$allowed    = $this->allowEdit(array('id' => $recordId));
		}

		if (!$allowed)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_EDIT_NO_PERMISSION'), 'error');
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(),
					false
				)
			);
			return false;
		}

		// Attempt to check out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice…
			$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');

			// …and do not allow the user to see the record.
			$this->setRedirect(
				Route::_(
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
				Route::_(
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
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function add()
	{
		// set state for normal subscriber…
		Factory::getApplication()->setUserState('com_bwpostman.subscriber.new_test', '0');

		parent::add();
	}

	/**
	 * Overwrite for method to add a new record for a test-recipient
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function add_test()
	{
		// set state for test-recipient…
		Factory::getApplication()->setUserState('com_bwpostman.subscriber.new_test', '9');

		parent::add();
	}

	/**
	 * Override method to save a subscriber
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 *
	 */
	public function save($key = null, $urlVar = null)
	{

		parent::save();

		PluginHelper::importPlugin('bwpostman');
		Factory::getApplication()->triggerEvent('onBwPostmanAfterSubscriberControllerSave', array());

		$task = $this->getTask();

		switch ($task)
		{
			case 'save':
			case 'save2new':
				Factory::getApplication()->setUserState('subscriber.id', null);
				Factory::getApplication()->setUserState('com_bwpostman.edit.subscriber.mailinglists', null);
				break;
		}
	}

	/**
	 * Override method to cancel edit of a subscriber
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0
	 *
	 */
	public function cancel($key = null)
	{

		parent::cancel();

		Factory::getApplication()->setUserState('subscriber.id', null);
		Factory::getApplication()->setUserState('com_bwpostman.edit.subscriber.mailinglists', null);
	}

	/**
	 * Method to send the activation link again
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       4.0.0
	 */
	public function sendconfirmmail()
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$cid      = $jinput->post->get('cid', array(), 'array');
		$cid      = ArrayHelper::toInteger($cid);

		$model = $this->getModel('subscriber');
		$res = $model->sendconfirmmail($cid);

		foreach ($res['success'] as $mail)
		{
				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_SUB_SEND_CONFIRMMAIL_SUCCESS', $mail), 'message');
		}
		foreach ($res['error'] as $mail)
		{
				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_SUB_SEND_CONFIRMMAIL_ERROR', $mail), 'error');
		}

		$app->setUserState('com_bwpostman.subscribers.layout', 'confirmed');
		$this->setRedirect(Route::_('index.php?option=com_bwpostman&view=subscribers', false));

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
	public function archive(): bool
	{
		$jinput	= Factory::getApplication()->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Get the selected campaign(s)
		$cid = $jinput->get('cid', array(0), 'post');
		$cid = ArrayHelper::toInteger($cid);

		// Access check.
		if (!$this->allowArchive($cid))
		{
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(),
					false
				)
			);
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_ARCHIVE_NO_PERMISSION'), 'error');

			return false;
		}

		$n = count($cid);

		$model = $this->getModel('subscriber');

		if(!$model->archive($cid, 1))
		{ // Couldn't archive
			if ($layout == 'testrecipients')
			{
				if ($n > 1)
				{
					echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_TESTS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_TEST_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
			}
			else
			{
				if ($n > 1)
				{
					echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_SUBS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_SUB_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
				}
			}
		}
		else
		{ // Archived successfully
			if ($layout == 'testrecipients')
			{
				if ($n > 1)
				{
					$msg = Text::_('COM_BWPOSTMAN_TESTS_ARCHIVED');
				}
				else
				{
					$msg = Text::_('COM_BWPOSTMAN_TEST_ARCHIVED');
				}
			}
			else
			{
				if ($n > 1)
				{
					$msg = Text::_('COM_BWPOSTMAN_SUBS_ARCHIVED');
				}
				else
				{
					$msg = Text::_('COM_BWPOSTMAN_SUB_ARCHIVED');
				}
			}

			$link = Route::_('index.php?option=com_bwpostman&view=subscribers', false);
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
	public function batch($model = null): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$vars     = $jinput->post->get('batch', array(), 'array');
		$cid      = $jinput->post->get('cid', array(), 'array');
		$cid      = ArrayHelper::toInteger($cid);
		$old_list = $app->getSession()->get('com_bwpostman.subscriber.batch_filter_mailinglist', null);
		$message  = '';

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

		// Set the model and some variables
		$model = $this->getModel('Subscriber', '', array());

		// run the batch operation.
		$results = $model->batch($vars, $cid, $contexts);

		// Check results.
		if (is_array($results))
		{
			foreach ($results as $result)
			{
				if ($result['task']	== 'subscribe')
				{
					$sub_text = Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE', $vars['mailinglist_id']);
					$message  = Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					$message .= ' ' . Text::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE_N_ITEMS', $result['done']);

					if ($result['skipped'] > 0)
					{
						$message .= ' ' . Text::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_SUBSCRIBE_SKIPPED_N_ITEMS', $result['skipped']);
					}
				}

				if ($result['task']	== 'unsubscribe')
				{
					if ($message == '') {
						$sub_text = Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE', $vars['mailinglist_id']);
						$message  = Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					}
					else
					{
						$sub_text = Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE', $old_list);
						$message  .= '<br />' . Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_FINISHED', $sub_text);
					}

					$message .= ' ' . Text::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE_N_ITEMS', $result['done']);

					if ($result['skipped'] > 0)
					{
						$message .= ' ' . Text::plural('COM_BWPOSTMAN_SUB_BATCH_RESULT_UNSUBSCRIBE_SKIPPED_N_ITEMS', $result['skipped']);
					}
				}

				$this->setMessage($message);
			}
		}
		elseif ($results < 0)
		{
			$this->setMessage(Text::sprintf('COM_BWPOSTMAN_SUB_BATCH_RESULT_NOTHING_TO_MOVE', $old_list), 'warning');
		}
		else
		{
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()), 'warning');
		}

		// Set the redirect
		$this->setRedirect(Route::_('index.php?option=com_bwpostman&view=subscribers' . $this->getRedirectToListAppend(), false));

		return true;
	}
}
