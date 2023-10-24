<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter controller for backend.
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
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use stdClass;

/**
 * BwPostman Newsletter Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since       1.0.1
 */
class NewsletterController extends FormController
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_NL';

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public array $permissions;

	/**
	 * property to hold logger
	 *
	 * @var BwLogger $logger
	 *
	 * @since       2.4.0
	 */
	public BwLogger $bwLogger;

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
	 */
	public function __construct($config = array())
	{
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		$log_options  = array();
		$this->bwLogger = BwLogger::getInstance($log_options);

		//register extra tasks
		$this->registerTask('setContent', 'setContent');
		$this->registerTask('sendmail', 'sendmail');
		$this->registerTask('sendmailandpublish', 'sendmail');
		$this->registerTask('sendtestmail', 'sendmail');
		$this->registerTask('publish_apply', 'save');
		$this->registerTask('publish_save', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('changeTab', 'changeTab');
		$this->registerTask('changeIsTemplate', 'changeIsTemplate');

		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  NewsletterController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array()): NewsletterController
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
	 * Method override to check if you can add a new record.
	 *
	 * @param array $data An array of input data.
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 * @since    1.0.1
	 */
	protected function allowAdd($data = array()): bool
	{
		return BwPostmanHelper::canAdd('newsletter');
	}

	/**
	 * Method override to check if you can edit a record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function allowEdit($data = array(), $key = 'id'): bool
	{
		return BwPostmanHelper::canEdit('newsletter', $data);
	}

	/**
	 * Method to check if you can send a newsletter.
	 *
	 * @param array $data An array of input data.
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.0.0
	 */
	public static function allowSend(array $data = array()): bool
	{
		return BwPostmanHelper::canSend($data['id']);
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
			$allowed = BwPostmanHelper::canArchive('newsletter', 0, (int) $recordId);

			if (!$allowed)
			{
				return false;
			}
		}

		return true;
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
	public function getModel($name = 'Newsletter', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return $this->factory->createModel($name, $prefix, $config);
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
		$model   = $this->getModel();

		$table   = $model->getTable();
		$cid     = $this->input->post->get('cid', array(), 'array');
		$context = "$this->option.edit.$this->context";

		$cid = ArrayHelper::toInteger($cid);

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
		$recordId = (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
		$checkin  = property_exists($table, 'checked_out');

		// Access check.
		if ($recordId === 0)
		{
			$allowed = $this->allowAdd();
		}
		else
		{
			$allowed = $this->allowEdit(array('id' => $recordId));
		}

		if (!$allowed)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_EDIT_NO_PERMISSION'), 'error');
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(),
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
					'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);

			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return true;
		}
	}

	/**
	 * Method to set start tab 'basic' on cancel editing newsletter.
	 *
	 * @param	string	$key		The name of the key for the primary key.
	 *
	 * @access	public
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function cancel($key = null): bool
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$app     = Factory::getApplication();
		$model   = $this->getModel();
		$table   = $model->getTable();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";


		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = $app->input->getInt($key);

		// Attempt to check in the current record.
		if ($recordId)
		{
			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');

					$this->setRedirect(
						Route::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $key),
							false
						)
					);
					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);
		$app->setUserState('com_bwpostman.edit.newsletter.data', null);

		PluginHelper::importPlugin('bwpostman');

		$this->setRedirect(
			Route::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(),
				false
			)
		);

		return true;
	}

	/**
	 * Method to save a record and set start tab 'basic' on save newsletter.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @return	boolean
	 *
	 * @throws 	Exception
	 *
	 * @since	1.1.0
	 */
	public function save($key = null, $urlVar = null): bool
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model = $this->getModel();
		$table = $model->getTable();

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

		$recordId = $this->input->getInt($urlVar);

		// Access check.
		if ($recordId === 0)
		{
			$allowed = $this->allowAdd();
		}
		else
		{
			$allowed = $this->allowEdit(array('id' => $recordId));
		}

		$app = Factory::getApplication();

		if (!$allowed)
		{
			$app->setUserState('com_bwpostman.edit.newsletter.data', null);
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(),
					false
				)
			);
			return false;
		}

		$lang    = $app->getLanguage();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		$task    = $this->getTask();

		if (($task === 'save') || ($task === 'apply') || ($task === 'save2new')  || ($task === 'save2copy') || ($task === 'publish_save') || ($task === 'publish_apply'))
		{
			$this->changeTab();
		}

		$data = ArrayHelper::fromObject($app->getUserState('com_bwpostman.edit.newsletter.data', []));
		$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task === 'save2copy')
		{
			// Reset is_template on copy
			$data['is_template'] = 0;

			// Check-in the original row.
			if ($checkin && $model->checkin($data[$key]) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');

				$this->setRedirect(
					Route::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar),
						false
					)
				);
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data[$key] = 0;
			$task = 'apply';
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		PluginHelper::importPlugin('bwpostman');
		$app->triggerEvent('onBwPostmanBeforeNewsletterControllerValidate', array(&$form));

		// convert attachment JSON to array, to be able to check subform
		if (isset($data['attachment']) && is_string($data['attachment']))
		{
			$data['attachment'] = json_decode($data['attachment']);

			if (!is_object($data['attachment']))
			{
				$data['attachment'] = new stdClass;
			}
		}

		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');

			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);

			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$app->enqueueMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');

			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar),
					false
				)
			);
			return false;
		}

		$this->setMessage(
			Text::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
				? $this->text_prefix
				: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isClient('site') ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
			case 'publish_apply':
				// Set the record data in the session.
					$recordId = (int)$model->getState($this->context . '.id');
					$this->holdEditId($context, $recordId);
					$app->setUserState($context . '.data', null);
					$model->checkout($recordId);

					// Redirect back to the edit screen.
					$this->setRedirect(
						Route::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar),
							false
						)
					);
				break;

			case 'save2new':
					// Clear the record id and data from the session.
					$this->releaseEditId($context, $recordId);
					$app->setUserState($context . '.data', null);

					// Redirect back to the edit screen.
					$this->setRedirect(
						Route::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend(null, $urlVar),
							false
						)
					);
				break;

			case 'publish_save':
					// Clear the record id and data from the session.
					$this->releaseEditId($context, $recordId);
					$app->setUserState($context . '.data', null);
					$app->setUserState('com_bwpostman.edit.newsletter.data', null);

					// Redirect  to the list screen.
					$this->setRedirect(
						Route::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(),
							false
						)
					);
				break;

			default:
					// Clear the record id and data from the session.
					$this->releaseEditId($context, $recordId);
					$app->setUserState($context . '.data', null);
					$app->setUserState('com_bwpostman.edit.newsletter.data', null);

					PluginHelper::importPlugin('bwpostman');
					$app->triggerEvent('onBwPostmanAfterNewsletterSave', array());

					// Redirect to the list screen.
					$this->setRedirect(
						Route::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(),
							false
						)
					);
				break;
		}

		// Invoke the postSave method to allow for the child class to access the model.
		// @todo Necessary? Usable for Plugins?
		$this->postSaveHook($model, $validData);

		return true;
	}

	/**
	 * Method to set the newsletter contents while changing tabs
	 *
	 * @return void
	 *
	 * @throws 	Exception
	 *
	 * @since	1.0.1
	 */
	public function changeTab()
	{
		$app      = Factory::getApplication();
		$recordId = $this->input->getInt('id', 0);
		$tab      = (string)$this->input->get('tab', 'edit_basic');

		$app->setUserState($this->context . '.tab' . $recordId, $tab);

		$this->getModel('newsletter')->changeTab();

		if ($this->getTask() === 'changeTab')
		{
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. '&layout=' . $tab . '&id=' . $recordId,
					false
				)
			);
		}
		elseif($this->getTask() === 'publish_save')
		{
			$app->setUserState('bwpostman.newsletters.tab', 'sent');
			$this->input->set('tab', 'sent');
		}
	}

	/**
	 * Method to send out newsletter from newsletters list
	 *
	 * @return void
	 *
	 * @throws 	Exception
	 *
	 * @since
	 */
	public function sendOut()
	{
		// get newsletter ID to send
		$app      = Factory::getApplication();
		$cids     = $this->input->get('cid', array(), 'array');
		$recordId = (int)$cids[0];
		$tab      = 'send';

		$cids = ArrayHelper::toInteger($cids);

		if (count($cids) > 1)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_WARNING_SENDING_ONLY_ONE_NL'), 'warning');
		}

		// check for is_template
		$model	= $this->getModel();

		foreach ($cids as $cid)
		{
			if ($model->isTemplate($cid))
			{
				$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_IS_TEMPLATE_ERROR'), 'error');
				$this->setRedirect(
					Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false)
				);
				return;
			}
		}

		// set edit tab to send
		$app->setUserState($this->context . '.tab' . $recordId, $tab);

		// redirect to edit, because we may want to sent regular or for testing
		$this->setRedirect(
			Route::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. '&layout=edit_' . $tab . '&id=' . $recordId,
				false
			)
		);
	}

	/**
	 * Method to send a newsletter to the subscribers or the test-recipients
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function sendmail(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app   = Factory::getApplication();
		$model = $this->getModel();
		$error = array();
		$link  = '';
		$this->bwLogger->addEntry(new LogEntry('NL controller sendmail reached', BwLogger::BW_DEBUG, 'send'));

		// Get record ID from list view
		$ids = $this->input->get('cid', 0, '');

		$recordId = $ids;

		// If we come from single view, record ID is 0 at new newsletter
		if ($recordId === 0 || $recordId === null)
		{
			$recordId	= (int)$this->input->get('id', 0);
		}

		$data = $model->preSendChecks($error, $recordId);
		$this->bwLogger->addEntry(new LogEntry('NL controller preSendChecks finished', BwLogger::BW_DEBUG, 'send'));

		// If preSendChecks fails redirect to edit
		if ($error)
		{
			for ($i = 0; $i <= count($error); $i++)
			{
				$app->enqueueMessage($error[$i], 'error');
			}

			$link = Route::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId),
				false
			);

			$this->setRedirect($link);

			return false;
		}

		// Sending is allowed, form data are valid, newsletter is no content template and saving was successful
		$startsending = 0;
		$task         = $this->input->getCmd('task', 0);
		$unconfirmed  = $this->input->get('send_to_unconfirmed', 0);
		$app->setUserState('bwpostman.send.alsoUnconfirmed', $unconfirmed);

		// Store the newsletter into the newsletters-table
		if ($model->save($data))
		{ // save newsletter is ok
			// make sure, recordID matches data id (because we may come from list view or from a new newsletter)
			$recordId = (int)$model->getState('newsletter.id');
			$ret_msg  = '';
			$this->bwLogger->addEntry(new LogEntry('NL controller model save finished', BwLogger::BW_DEBUG, 'send'));

			switch ($task)
			{
				case "sendmail":
				case "sendmailandpublish":
					// Check if there are assigned mailinglists or joomla user groups and if they contain subscribers/users
					if (!$model->checkForRecipients($ret_msg, $recordId, $unconfirmed, $data['campaign_id']))
					{
						$app->enqueueMessage($ret_msg, 'error');
					}
					else
					{
						if (!$model->sendNewsletter($ret_msg, 'recipients', $recordId, $unconfirmed,
							$data['campaign_id']))
						{
							$app->enqueueMessage($ret_msg, 'error');
						}
						else
						{
							$startsending = 1;
							$model->checkin($recordId);
							// set start tab 'basic'
						}

					}

				$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
				break;
				case "sendtestmail":
					// Check if there are test-recipients
					if (!$model->checkForTestrecipients())
					{
						$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_TESTRECIPIENTS'), 'error');
					}
					else
					{
						if (!$model->sendNewsletter($ret_msg, 'testrecipients', $recordId, $unconfirmed,
							$data['campaign_id']))
						{
							$app->enqueueMessage($ret_msg, 'error');
						}
						else
						{
							$startsending = 1;
							$model->checkin($recordId);
							// set start tab 'basic'
						}

					}

					$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
					break;
			}

			if ($startsending)
			{
				$this->bwLogger->addEntry(new LogEntry('NL controller start sending reached', BwLogger::BW_DEBUG, 'send'));
				if ($task == "sendmailandpublish")
				{
					$app->setUserState('com_bwpostman.newsletters.sendmailandpublish', 1);
				}

				$app->setUserState('com_bwpostman.edit.newsletter.data', null);
				$app->setUserState('newsletter.id', null);
				$app->setUserState('com_bwpostman.newsletters.publish_id', $recordId);
				$app->setUserState('com_bwpostman.newsletters.mails_per_pageload', 	$this->input->get('mails_per_pageload'));
				$link = Route::_('index.php?option=com_bwpostman&view=newsletter&task=startsending&layout=nl_send', false);
			}
			else
			{
				$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
				$link = Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId),
					false
				);
			}
		}

		if ($link !== '')
		{
			$this->setRedirect($link);
		}

		return true;
	}

	/**
	 * Method to copy a newsletter
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function copy()
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app = Factory::getApplication();

		// Access check.
		if (!$this->allowAdd())
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_COPY_CREATE_RIGHTS_MISSING'), 'error');
		}

		// Get the newsletter IDs to copy
		$cid   = ArrayHelper::toInteger($this->input->get('cid', array(), 'array'));
		$model = $this->getModel();

		foreach ($cid as $id)
		{
			if (!$this->allowEdit(array('id' => (int)$id)))
			{
				$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_COPY_CREATE_RIGHTS_MISSING'), 'error');
			}
			else
			{
				$res = $model->copy($id);

				if ($res === true)
				{
					PluginHelper::importPlugin('bwpostman');
					$app->triggerEvent('onBwPostmanAfterNewsletterCopy', array());
				}
			}
		}

		parent::display();
	}

	/**
	 * Method to archive one or more newsletters
	 * --> subscribers-table: archive_flag = 1, set archive_date
	 *
	 * @access	public
	 *
	 * @return 	bool    true on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function archive(): bool
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Get the selected newsletter(s)
		$res = true;
		$msg = '';

		// Which tab are we in?
		$layout	= $this->input->getWord('tab', 'unsent');

		// Get the selected newsletter(s)
		$cid = $this->input->get('cid', array(0), 'post');

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

		PluginHelper::importPlugin('bwpostman');
		Factory::getApplication()->triggerEvent('onBwPostmanBeforeNewsletterArchive', array(&$cid, &$msg, &$res));

		if ($res === false)
		{
			$link = Route::_('index.php?option=com_bwpostman&view=newsletters&layout=' . $layout, false);
			$this->setRedirect($link, $msg, 'error');
		}

		$n     = count($cid);
		$model = $this->getModel('newsletter');

		if(!$model->archive($cid, 1))
		{ // Couldn't archive
			if ($n > 1)
			{
				echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_NLS_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				echo "<script> alert ('" . Text::_('COM_BWPOSTMAN_NL_ERROR_ARCHIVING', true) . "'); window.history.go(-1); </script>\n";
			}
		}
		else
		{ // Archived successfully

			if ($n > 1)
			{
				$msg = Text::_('COM_BWPOSTMAN_NLS_ARCHIVED');
			}
			else
			{
				$msg = Text::_('COM_BWPOSTMAN_NL_ARCHIVED');
			}

			$link = Route::_('index.php?option=com_bwpostman&view=newsletters', false);
			$this->setRedirect($link, $msg);
		}

		return true;
	}

	/**
	 * Changes the state of isTemplate switch
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since	2.2.0
	 */
	public function changeIsTemplate()
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$cid = $this->input->get('cid', array(), 'array');

		// Make sure the item ids are integers
		$cid = ArrayHelper::toInteger($cid);

		if (empty($cid))
		{
			$this->bwLogger->addEntry(new LogEntry(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), BwLogger::BW_WARNING, 'newsletter'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			try
			{
				$result = $model->changeIsTemplate((int)$cid[0]);

				if ($result === 0)
				{
					$nText = 'COM_BWPOSTMAN_NLS_N_ITEMS_IS_TEMPLATE_0';
				}
				else
				{
					$nText = 'COM_BWPOSTMAN_NLS_N_ITEMS_IS_TEMPLATE_1';
				}

				if ($nText !== null)
				{
					$this->setMessage(Text::_($nText));
				}
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$extension = $this->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	integer		$recordId	The primary key id for the item.
	 * @param	string		$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string		The arguments to append to the redirect URL.
	 *
	 * @since	1.2.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id'): string
	{
		$layout	= $this->input->getWord('layout', 'edit_basic');

		$append	= '';

		// Setup redirect info.
		if ($layout)
		{
			if ($layout === 'default')
			{
				$layout	= 'edit_basic';
			}

			$append .= '&layout=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}
}
