<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter controller for backend.
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
jimport('joomla.application.component.controllerform');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Newsletter Controller
 *
 * @since		1.0.1
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 */
class BwPostmanControllerNewsletter extends JControllerForm
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_NL';

	/**
	 * Constructor.
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @since	1.0.1
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		//register extra tasks
		$this->registerTask('setContent', 'setContent');
		$this->registerTask('sendmail', 'sendmail');
		$this->registerTask('sendmailandpublish', 'sendmail');
		$this->registerTask('sendtestmail', 'sendmail');
		$this->registerTask('publish_save', 'save');
		$this->registerTask('changeTab', 'changeTab');
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data		An array of input data.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowAdd($data = array())
	{
		$user	= JFactory::getUser();

		return ($user->authorise('core.create', 'com_bwpostman'));
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
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_bwpostman'))
		{
			return true;
		}

		// Check specific edit permission.
		if ($user->authorise('core.edit', 'com_bwpostman.newsletter.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_bwpostman.newsletter.' . $recordId) || $user->authorise('core.edit.own', 'com_bwpostman'))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		return false;
	}


	/**
	 * Method to check if you can send a newsletter.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0.1
	 */
	public static function allowSend($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();

		// Check general component send permission first.
		if ($user->authorise('core.send', 'com_bwpostman')) {
			return true;
		}

		// Check specific send permission.
		if ($user->authorise('core.send', 'com_bwpostman.newsletter.' . $recordId))
		{
			return true;
		}
		return false;
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
	 * @since	1.0.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= $this->input->post->get('cid', array(), 'array');
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
		$recordId	= (int) (count($cid) ? $cid[0] : $this->input->getInt($urlVar));
		$checkin	= property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice…
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			// …and do not allow the user to see the record.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
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
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
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
	 * @return 	Redirect
	 * @since	1.1.0
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app		= JFactory::getApplication();
		$model		= $this->getModel();
		$table		= $model->getTable();
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";


		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = $app->input->getInt($key);

		// Attempt to check-in the current record.
		if ($recordId)
		{
			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');

					$this->setRedirect(
							JRoute::_(
									'index.php?option=' . $this->option . '&view=' . $this->view_item
									. $this->getRedirectToItemAppend($recordId, $key), false
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

		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');
//		$dispatcher->trigger('onBwPostmanAfterNewsletterCancel', array());

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		return true;
	}

	/**
	 * Method to save a record abd set start tab 'basic' on save newsletter.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @access	public
	 * @return	Redirect
	 * @since	1.1.0
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$model		= $this->getModel();
		$table		= $model->getTable();
//		$data		= $this->input->post->get('jform', array(), 'array');
		$checkin	= property_exists($table, 'checked_out');
		$context	= "$this->option.edit.$this->context";
		$task		= $this->getTask();

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

		if (($task == 'save') || ($task == 'apply') || ($task == 'save2copy') || ($task == 'publish_save')) {
			$this->changeTab();
		}

		$data	= JArrayHelper::fromObject($app->getUserState('com_bwpostman.edit.newsletter.data'));
		$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');

		// Populate the row id from the session.
		$data[$key] = $recordId;

	// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if ($checkin && $model->checkin($data[$key]) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data[$key] = 0;
			$task = 'apply';
		}

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
			return false;
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
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
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
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
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
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
			return false;
		}

		$this->setMessage(
		JText::_(
			($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
				? $this->text_prefix
				: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
					$recordId = $model->getState($this->context . '.id');
					$this->holdEditId($context, $recordId);
					$app->setUserState($context . '.data', null);
					$model->checkout($recordId);

					// Redirect back to the edit screen.
					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
						)
					);
				break;

			case 'save2new':
					// Clear the record id and data from the session.
					$this->releaseEditId($context, $recordId);
					$app->setUserState($context . '.data', null);

					// Redirect back to the edit screen.
					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend(null, $urlVar), false
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
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
						)
					);
				break;

			default:
					// Clear the record id and data from the session.
					$this->releaseEditId($context, $recordId);
					$app->setUserState($context . '.data', null);
					$app->setUserState('com_bwpostman.edit.newsletter.data', null);

					$dispatcher = JEventDispatcher::getInstance();

					JPluginHelper::importPlugin('bwpostman');
					$dispatcher->trigger('onBwPostmanAfterNewsletterSave', array());

					// Redirect to the list screen.
					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
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
	 * @access	public
	 * @since	1.0.1
	 */
	public function changeTab()
	{
		$app		= JFactory::getApplication();
		$recordId	= $this->input->getInt('id', 0);
		$tab		= $this->input->get('tab', 'edit_basic');

		$app->setUserState($this->context . '.tab' . $recordId, $tab);

		$this->getModel('newsletter')->changeTab();

		if ($this->getTask() == 'changeTab') {
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. '&layout=' . $tab . '&id=' . $recordId, false
				)
			);
		}
	}

	/**
	 * Method to send out newsletter from newsletters list
	 *
	 * @access	public
	 */
	public function sendOut()
	{
		// get newsletter ID to send
		$app		= JFactory::getApplication();
		$cids		= $this->input->get('cid', array(), 'array');
		$recordId	= (int)$cids[0];
		$tab		= 'send';

		if (count($cids) > 1) $app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_WARNING_SENDING_ONLY_ONE_NL'), 'warning');

		// set edit tab to send
		$app->setUserState($this->context . '.tab' . $recordId, $tab);

		// redirect to edit, because we may want to sent regular or for testing
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. '&layout=edit_' . $tab . '&id=' . $recordId, false
			)
		);
	}

	/**
	 * Method to send a newsletter to the subscribers or the test-recipients
	 *
	 * @access	public
	 */
	public function sendmail()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$model	= $this->getModel('newsletter');
		$error	= false;

		// Get record ID from list view
		$ids    	= (int) $this->input->get('cid', 0, '', 'array');
		$recordId   = $ids[0];

		// If we come from single view, record ID is 0
		if ($recordId == 0) {
			$recordId	= $this->input->getInt('id', 0);
		}

		// Check the newsletter form
		$data	= $model->checkForm($recordId, $error);

		// if checkForm fails redrect to edit
		if ($error) {
			for ($i = 0; $i <= count($error); $i++)
			{
				$app->enqueueMessage($error[$i]['err_msg'], 'error');
			}
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, 'id'), false
							)
				);
		}
		// form data are valid
		else {
			$task			= $this->input->get('task', 0);
			$unconfirmed	= $this->input->get('send_to_unconfirmed', 0);
			$startsending	= 0;

			// Access check.
			if (!self::allowSend($data)) {
				$this->setError(JText::_('COM_BWPOSTMAN_NL_ERROR_SEND_NOT_PERMITTED'));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
						)
				);
				return false;
			}

			// Store the newsletter into the newsletters-table
			if ($model->save($data)) { // save newsletter is ok
				// make sure, recordID matches data id (because we may come from list view or form a new newsletter)
				$recordId	= $model->getState('newsletter.id');
				$ret_msg    = '';

				switch ($task) {
					case "sendmail":
					case "sendmailandpublish":
							// Check if there are assigned mailinglists or joomla user groups and if they contain subscribers/users
							if (!$model->checkRecipients($ret_msg, $recordId, $unconfirmed, $data['campaign_id'])) {
								$app->enqueueMessage($ret_msg, 'error');
								$error	= true;
								$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
								$link = JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend($recordId, 'id'), false
										);
								$this->setRedirect($link);
							}
							else {
								if (!$model->sendNewsletter($ret_msg, 'recipients', $recordId, $unconfirmed, $data['campaign_id'])) {
									$app->enqueueMessage($ret_msg, 'error');
									$error	= true;
									$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
									$link = JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend($recordId, 'id'), false
									);
									$this->setRedirect($link);
								}
								else {
									$startsending = 1;
									$model->checkin($recordId);
									// set start tab 'basic'
									$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
								}
							}
						break;
					case "sendtestmail":
							// Check if there are test-recipients
							if (!$model->checkTestrecipients()) {
								$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_TESTRECIPIENTS'), 'error');
								$error	= true;
								$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
								$link = JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend($recordId, 'id'), false
										);
								$this->setRedirect($link);
							}
							else {
								if (!$model->sendNewsletter($ret_msg, 'testrecipients', $recordId, $unconfirmed, $data['campaign_id'])) {
									$app->enqueueMessage($ret_msg, 'error');
									$error	= true;
									$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
									$link = JRoute::_(
											'index.php?option=' . $this->option . '&view=' . $this->view_item
											. $this->getRedirectToItemAppend($recordId, 'id'), false
									);
									$this->setRedirect($link);
								}
								else {
									$startsending = 1;
									$model->checkin($recordId);
									// set start tab 'basic'
									$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
								}
							}
						break;
				}

				if ($startsending){
					if ($task == "sendmailandpublish") $app->setUserState('com_bwpostman.newsletters.sendmailandpublish', 1);
					$app->setUserState('com_bwpostman.edit.newsletter.data', null);
					$app->setUserState('newsletter.id', null);
					$app->setUserState('com_bwpostman.newsletters.publish_id', $recordId);
					$app->setUserState('com_bwpostman.newsletters.mails_per_pageload', $this->input->get('mails_per_pageload'));
					$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&task=startsending&layout=queue', false);
					$this->setRedirect($link);
				}
			}
			else {
				$app->setUserState($this->context . '.tab' . $recordId, 'edit_basic');
				$link = JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, 'id'), false
						);
				$this->setRedirect($link);
			}
		}
	}

	/**
	 * Method to copy a newsletter
	 *
	 * @access	public
	 */
	public function copy()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();

		// Access check.
		if (!self::allowAdd()) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPY_CREATE_RIGHTS_MISSING'), 'error');
			return false;
		}

		$model	= $this->getModel('newsletter');

		// Get the newsletter IDs to copy
		$cid = $this->input->get('cid', array(), 'array');

		$res	= $model->copy($cid);

		if ($res === true) {
			$dispatcher = JEventDispatcher::getInstance();

			JPluginHelper::importPlugin('bwpostman');
			$dispatcher->trigger('onBwPostmanAfterNewsletterCopy', array());
		}

		parent::display();
	}

	/**
	 * Method to archive one or more newsletters
	 * --> subscribers-table: archive_flag = 1, set archive_date
	 *
	 * @access	public
	 * @return 	Redirect
	 */
	public function archive()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Get the selected newsletter(s)
		$cid		= $this->input->get('cid', array(0), 'post', 'array');
		$res		= true;
		$msg		= '';

		$dispatcher = JEventDispatcher::getInstance();

		// Which tab are we in?
		$layout	= $this->input->get('tab', 'unsent');

		// Get the selected newsletter(s)
		$cid	= $this->input->get('cid', array(0), 'post', 'array');

		JArrayHelper::toInteger($cid);
		JPluginHelper::importPlugin('bwpostman');
		$dispatcher->trigger('onBwPostmanBeforeNewsletterArchive', array(&$cid, &$msg, &$res));

		if ($res === false) {
			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&layout='.$layout, false);
			$this->setRedirect($link, $msg, 'error');
			return false;
		}

		$n		= count ($cid);
		$model	= $this->getModel('newsletter');

		if(!$model->archive($cid, 1)) { // Couldn't archive

			if ($n > 1) {
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NLS_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
			}
			else {
				echo "<script> alert ('".JText::_('COM_BWPOSTMAN_NL_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
			}
		}
		else { // Archived successfully

			if ($n > 1) {
				$msg = JText::_('COM_BWPOSTMAN_NLS_ARCHIVED');
			}
			else {
				$msg = JText::_('COM_BWPOSTMAN_NL_ARCHIVED');
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=newsletters&layout='.$layout, false);
			$this->setRedirect($link, $msg);
		}
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
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl	= $this->input->get('tmpl');
		$layout	= $this->input->get('layout', 'edit_basic', 'string');

		$append	= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout) {
			if ($layout == 'default') $layout	= 'edit_basic';
			$append .= '&layout=' . $layout;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}
		return $append;
	}

}
