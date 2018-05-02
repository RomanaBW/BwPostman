<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit controller for frontend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Site
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

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

// Require component admin helper class and exception class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/exceptions/BwException.php');
require_once(JPATH_COMPONENT . '/helpers/subscriberhelper.php');


/**
 * Class BwPostmanControllerEdit
 *
 * @since   2.0.0
 */
class BwPostmanControllerEdit extends JControllerLegacy
{

	/**
	 * Subscriber ID
	 *
	 * @var integer
	 *
	 * @since   2.0.0
	 */
	private $subscriberid;

	/**
	 * User ID in subscriber-table
	 *
	 * @var integer
	 *
	 * @since   2.0.0
	 */
	private $userid;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('sendEditLink', 'sendEditLink');

		$session		= JFactory::getSession();

		//clear session error and success
		// @Todo: is it necessary to check for array?
		$session_error = $session->get('session_error');
		if(isset($session_error) && is_array($session_error))
		{
			$session->clear('session_error');
		}

		// @Todo: is it necessary to check for array?
		$session_success = $session->get('session_success');
		if(isset($session_success) && is_array($session_success))
		{
			$session->clear('session_success');
		}

		// initialize variables
		$subscriberid   = 0;
		$model			= $this->getModel('edit');
		$err			= new stdClass();
		$err->err_code	= 0;

		$user 	        = JFactory::getUser();
		$user_is_guest  = $user->get('guest');
		$userid 		= (int) $user->get('id');

		// if user is logged in fetch subscriber id
		if ($userid)
		{
			$subscriberid	= (int) BwPostmanSubscriberHelper::getSubscriberID($userid); // = 0 if the user has no newsletter subscription
		}

		// Check if the variable editlink exists in the uri
		$uri		= JUri::getInstance();
		$editlink	= $uri->getVar("editlink", null);

		// Get subscriber id from session, clear session if necessary
		$session_subscriberid = $session->get('session_subscriberid');
		if(isset($session_subscriberid) && is_array($session_subscriberid))
		{
			if ($user_is_guest)
			{
				if (!empty($editlink))
				{
					if ($model->checkEditlink($editlink) == $session_subscriberid['id'])
					{
						$subscriberid = $session_subscriberid['id'];
					}
					else
					{
						$session->clear('session_subscriberid');
					}
				}
				elseif (is_null($editlink))
				{
					$subscriberid = $session_subscriberid['id'];
				}
			}
			else
			{
				$session->clear('session_subscriberid');
			}
		}

		// get subscriber data
		if ($subscriberid)
		{
			// Guest with known subscriber id (stored in the session) or logged in user
			$subscriberdata	= BwPostmanSubscriberHelper::getSubscriberData((int) $subscriberid);
			if (is_object($subscriberdata))
			{
				if ($user_is_guest)
				{
					$userid = (int) $subscriberdata->user_id;
				}

				$active_subscription    = $this->checkActiveSubscription($subscriberdata, $err);

				if (!$active_subscription)
				{
					BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
				}
			}
		}
		else
		{ // Guest with unknown subscriber id (not stored in the session)
			if (is_null($editlink))
			{
			}
			elseif (empty($editlink))
			{
				BwPostmanSubscriberHelper::errorEditlink();
				$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=register&layout=error_geteditlink', false));
			}
			else
			{
				$subscriberid	= (int) $model->checkEditlink($editlink);

				if (!$subscriberid)
				{
					BwPostmanSubscriberHelper::errorEditlink();
					$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=register&layout=error_geteditlink', false));
				}
				else
				{
					$subscriberdata	= BwPostmanSubscriberHelper::getSubscriberData((int) $subscriberid);

					$active_subscription    = $this->checkActiveSubscription($subscriberdata, $err);

					if (!$active_subscription)
					{
						BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
						parent::display();
					}
					else
					{
						$itemid	= (int) $model->getItemid(); // Itemid from edit-view

						$link   = BwPostmanSubscriberHelper::loginGuest((int) $subscriberid, (int) $itemid);
						$this->setRedirect($link, false);
					}
				}
			}
		}

		$this->setData((int) $subscriberid, (int) $userid);
	}

	/**
	 * Method to reset the subscriber ID and userid
	 *
	 * @param	int $subscriberid   subscriber ID
	 * @param 	int $userid         user ID
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setData($subscriberid = 0, $userid = 0)
	{
		$app	= JFactory::getApplication();
		$app->setUserState('subscriber.id', $subscriberid);

		$this->subscriberid = $subscriberid;
		$this->userid       = $userid;
	}

	/**
	 * Display
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	boolean		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$jinput	= JFactory::getApplication()->input;

		$session		= JFactory::getSession();
		$session_error	= $session->get('session_error');

		$jinput->set('view', 'edit');

		if(!(isset($session_error) && is_array($session_error)))
		{
			if (($this->userid) && ($this->subscriberid))
			{
			}
			elseif (($this->userid) && (!$this->subscriberid))
			{
				$jinput->set('view', 'register');
			}
			elseif ((!$this->userid) && ($this->subscriberid))
			{
			}
			else
			{
				$jinput->set('layout', 'editlink_form');
			}
		}

		parent::display();
	}

	/**
	 * Method to save changes from the edit-view
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function save()
	{
		$jinput	= JFactory::getApplication()->input;
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			jexit(JText::_('JINVALID_TOKEN'));
		}

		$post	= $jinput->getArray(
			array(
				'edit' => 'string',
				'email' => 'string',
				'emailformat' => 'string',
				'firstname' => 'string',
				'firstname_field_obligation' => 'string',
				'gender' => 'string',
				'special' => 'string',
				'id' => 'string',
				'language' => 'string',
				'mailinglists' => 'array',
				'name' => 'string',
				'name_field_obligation' => 'string',
				'task' => 'string',
				'option' => 'string',
				'unsubscribe' => 'int'
			)
		);

		$newEmail	= false;

		if (isset($post['unsubscribe']))
		{
			$this->unsubscribe($post['id']);
		}
		else
		{
			$model  = $this->getModel('edit');
			$itemid = $model->getItemid();

			// Email address has changed
			if (($post['email'] != "") && ($post['email'] != $model->getEmailaddress($post['id'])))
			{
				$newEmail					= true;
				$post['status'] 			= 0;
				$post['confirmation_date'] 	= 0;
				$post['confirmed_by'] 		= '-1';
				$post['activation']			= $model->getActivation();
			}

			// Store the data if possible
			if (!$model->save($post))
			{
				// Store the input data into the session object
				$session			= JFactory::getSession();
				$error              = $model->getError();
				$subscriber_data	= array(
					'id' => $post['id'],
					'name' => $post['name'],
					'firstname' => $post['firstname'],
					'email' => $post['email'],
					'emailformat' => $post['emailformat'],
					'list' => $post['list'],
					'err_code' => $error['err_id']
				);
				$session->set('subscriber_data', $subscriber_data);

				$jinput->set('view', 'edit');
			}
			else
			{ // Storing the data has been successful
				if ($newEmail)
				{
					// A new email address has been stored --> the account needs to be confirmed again
					$subscriber = new stdClass();
					$subscriber->name 		= $post['name'];
					$subscriber->firstname	= $post['firstname'];
					$subscriber->email 		= $post['email'];
					$subscriber->activation = $post['activation'];

					$type	= 3; // Send confirmation email

					// Send confirmation mail
					$res = BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

					if ($res === true)
					{
						// Email has been sent
						$success_msg    = 'COM_BWPOSTMAN_SUCCESS_CONFIRMEMAIL';
						BwPostmanSubscriberHelper::success($success_msg);
					}
					else
					{
						// Email has not been sent
						$err_msg 	= 'COM_BWPOSTMAN_ERROR_CONFIRMEMAIL';
						BwPostmanSubscriberHelper::errorSendingEmail($err_msg, $post['email']);
					}

					$session				= JFactory::getSession();
					$session_subscriberid	= $session->get('session_subscriberid');

					if(isset($session_subscriberid) && is_array($session_subscriberid))
					{
						$session->clear('session_subscriberid');
					}

					$jinput->set('view', 'register');
				}
				else
				{
					// No new email address has been stored --> the account doesn't need to be confirmed again
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CHANGES_SAVED_SUCCESSFULLY', 'message'));

					// If the user has chosen the button "save modifications & leave edit mode" we clear the session object
					// now no subscriber_id is stored into the session
					if ($post['edit'] == "submitleave")
					{
						$session				= JFactory::getSession();
						$session_subscriberid	= $session->get('session_subscriberid');

						if(isset($session_subscriberid) && is_array($session_subscriberid))
						{
							$session->clear('session_subscriberid');
						}

						$jinput->set('view', 'register');
						$app->setUserState('subscriber.id', 0);
					}
					else
					{
						$uid	= BwPostmanSubscriberHelper::getUserId($post['id']);
						$this->setData($post['id'], $uid);

						$app->setUserState('subscriber.id', $post['id']);
						$jinput->set('view', 'edit');
					}
				}
			}
		}

		parent::display();
	}

	/**
	 * Method to unsubscribe
	 * --> through an unsubscribe-link
	 * --> through the edit view
	 *
	 * @param 	int $id     Subscriber ID
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function unsubscribe($id = null)
	{
		// Initialize some variables
		$jinput	= JFactory::getApplication()->input;
		$model	= $this->getModel('register');
		$itemid	= $model->getItemid();
		$email  = '';

		// We come from the edit view
		if ($id)
		{
			$unsubscribedata	= BwPostmanSubscriberHelper::getSubscriberData($id);
			$email				= $unsubscribedata->email;
			$editlink			= $unsubscribedata->editlink;

			// We come from an unsubscribe-link
		}

		// Editlink-variable or email-variable is empty
		if ((empty($editlink)) || (empty($email)))
		{
			$msg		= 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
			$msg_type	= 'error';
			BwPostmanSubscriberHelper::errorUnsubscribe($msg);
		}
		else
		{
			// The editlink or email don't exist in the subscribers-table
			$msg    = '';
			if (!$model->unsubscribe($editlink, $email, $msg))
			{
				$msg		= 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
				$msg_type	= 'error';
				BwPostmanSubscriberHelper::errorUnsubscribe($msg);
			}
			else
			{
				// Everything is fine, account has been deleted
				$msg = 'COM_BWPOSTMAN_SUCCESS_UNSUBSCRIBE';
				$msg_type	= 'message';
				BwPostmanSubscriberHelper::success($msg, $editlink, $itemid);
			}
		}

		// If we come from the edit view we have to clear the session object
		// otherwise the subscriber can get to the edit view again
		$session				= JFactory::getSession();
		$session_subscriberid	= $session->get('session_subscriberid');

		if(isset($session_subscriberid) && is_array($session_subscriberid))
		{
			$session->clear('session_subscriberid');
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_SUCCESS_UNSUBSCRIBE'), $msg_type);
		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to send the editlink
	 * --> is needed to get access to the edit form
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function sendEditlink()
	{
		$jinput	= JFactory::getApplication()->input;
		$model	= $this->getModel('register');
		$post	= $jinput->getArray(
			array(
				'email' => 'string',
				'language' => 'string',
				'task' => 'string',
				'option' => 'string'
			)
		);

		// Check for request forgeries
		if (!JSession::checkToken())
		{
			jexit(JText::_('JINVALID_TOKEN'));
		}

		$id				= $model->isRegSubscriber($post['email']);
		$err			= new stdClass();
		$err->err_code	= 0;
		$editlink		= '';
		$subs_id        = null;
		$subscriber		= new stdClass();
		$subscriberdata = BwPostmanSubscriberHelper::getSubscriberData($id);

		if (!is_object($subscriberdata))
		{
			$subs_id		= null;
			$err->err_id    = $id;
			$err->err_code	= 408; // Email address doesn't exist
			$err->err_msg	= JText::sprintf(
				'COM_BWPOSTMAN_ERROR_EMAILDOESNTEXIST',
				$post['email'],
				JRoute::_('index.php?option=com_bwpostman&view=register')
			);
		}
		elseif ($subscriberdata->archive_flag == 1)
		{
			$subs_id		= $subscriberdata->id;
			$err->err_id    = $id;
			$err->err_code	= 405; // Email address exists but is blocked
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
		}
		elseif ($subscriberdata->status == 0)
		{
			$subs_id		= $subscriberdata->id;
			$err->err_id    = $id;
			$err->err_code	= 406; // Email address exists but account is not activated
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
		}

		if ($err->err_code != 0)
		{
			// we use not $subscriberdata->id - if $ID==NULL Notice: Trying to get property of non-object
			BwPostmanSubscriberHelper::errorSubscriberData($err, $subs_id, $post['email']);
		}
		else
		{
			// Everything is okay
			$subscriber->editlink 	= $subscriberdata->editlink;
			$subscriber->name 		= $subscriberdata->name;
			$subscriber->firstname	= $subscriberdata->firstname;
			$subscriber->email 		= $subscriberdata->email;

			$type	= 1; // Send Editlink
			$model	= $this->getModel('edit');
			$itemid	= $model->getItemid();
			$res	= BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

			if ($res === true)
			{ // Email has been sent
				$success_msg 	= 'COM_BWPOSTMAN_SUCCESS_EMAILEDITLINK';
				BwPostmanSubscriberHelper::success($success_msg, $editlink, $itemid);	// We need no editlink or itemid for the output in this layout
			}
			else
			{ // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_EDITLINKEMAIL';
				BwPostmanSubscriberHelper::errorSendingEmail($err_msg, $subscriber->email);
			}

			$jinput->set('view', 'register');
		}

		parent::display();
	}

	/**
	 * Method to check if account is archived or not activated
	 *
	 * @param object    $subscriberdata
	 * @param object    $err
	 *
	 * @return boolean  $result
	 *
	 * @since   2.0.0
	 */
	protected function checkActiveSubscription($subscriberdata, &$err)
	{
		$result = true;

		// The error code numbers are the same like in the subscribers-table check function
		if ($subscriberdata->archive_flag == 1)
		{
			$err->id       = $subscriberdata->id;
			$err->err_code = 405;
			$err->err_msg  = 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
			$result = false;
		}
		elseif ($subscriberdata->status == 0)
		{
			$err->id       = $subscriberdata->id;
			$err->err_code = 406;
			$err->err_msg  = 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
			$result = false;
		}

		return $result;
	}
}
