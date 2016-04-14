<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main controller for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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
jimport('joomla.application.component.controller');

// Require component admin helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * Class BwPostmanController
 */
class BwPostmanController extends JControllerLegacy
{

	/**
	 * Subscriber ID
	 *
	 * @var int
	 */
	var $_subscriberid;

	/**
	 * User ID in subscriber-table
	 *
	 * @var int
	 */
	var $_userid;

	/**
	 * Constructor
	 * Checks the session variables and deletes them if nescessary
	 * Sets the userid and subscriberid
	 * Checks if something is wrong with the subscriber-data (not activated/blocked)
	 */
	public function __construct()
	{
		parent::__construct();
		$jinput	= JFactory::getApplication()->input;

		$view = $jinput->get('view');
		$userid = 0;

		if (($view != 'newsletters') && ($view != 'newsletter')) {

			$user 	= JFactory::getUser();

			// Check if the variable editlink exists in the uri
			$uri			= JFactory::getURI();
			$editlink		= $uri->getVar("editlink", null);
			$model			= $this->getModel('edit');
			$session		= JFactory::getSession();
			$err			= new stdClass();
			$err->err_code	= 0;

			$session_subscriberid = $session->get('session_subscriberid');
			if(isset($session_subscriberid) && is_array($session_subscriberid)) {
				if ($user->get('guest')) {
					if (!empty($editlink)) {
						if ($model->checkEditlink($editlink) == $session_subscriberid['id']) {
							$subscriberid = $session_subscriberid['id'];
						}
						else {
							$session->clear('session_subscriberid');
						}
					}
					elseif (is_null($editlink)) {
						$subscriberid = $session_subscriberid['id'];
					}
				}
				else {
					$session->clear('session_subscriberid');
				}
			}
			else {
				$subscriberid = 0;
			}

//			$app_error      = JFactory::getApplication()->getUserState('com_bwpostman.subscriber.register.error', null);
			$session_error = $session->get('session_error');
			if(isset($session_error) && is_array($session_error)){
				$session->clear('session_error');
			}

			$session_success = $session->get('session_success');
			if(isset($session_success) && is_array($session_success)){
				$session->clear('session_success');
			}

			if ($subscriberid) { // Guest with subscriber id which is stored in the session
				$model			= $this->getModel('register');
				$subscriberdata	= $model->getSubscriberData ((int) $subscriberid);
				if (is_object($subscriberdata))
				{
					$userid = (int) $subscriberdata->user_id;

					// The error code numbers are the same like in the subscribers-table check function
					if ($subscriberdata->archive_flag == 1) {
						$err->err_code	= 405;
						$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
					}
					elseif ($subscriberdata->status == 0) {
						$err->err_code	= 406;
						$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
					}

					if ($err->err_code != 0) {
						$this->errorSubscriberData($err, $subscriberid, $subscriberdata->email);
					}
				}
			}
			elseif (!$user->get('guest')) { // User
				$model			= $this->getModel('register');
				$userid 		= (int) $user->get('id');
				$subscriberid	= (int) $model->getSubscriberID($userid); // = 0 if the user no newsletter account

				if ($subscriberid) {
					$subscriberdata = $model->getSubscriberData ((int) $subscriberid);

					// The error code numbers are the same like in the subscribers-table check function
					if ($subscriberdata->archive_flag == 1) {
						$err->err_code	= 405;
						$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
					}
					elseif ($subscriberdata->status == 0) {
						$err->err_code	= 406;
						$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
					}

					if ($err->err_code != 0) {
						$this->errorSubscriberData($err, $subscriberid, $subscriberdata->email);
					}
				}
			}
			else { // Guest
				if (is_null($editlink)) {
					$userid = 0;
				}
				elseif (empty($editlink)) {
					$this->errorEditlink();
				}
				else {
					$model			= $this->getModel('edit');
					$subscriberid	= (int) $model->checkEditlink($editlink);

					if (!$subscriberid) {
						$this->errorEditlink();
					}
					else {
						$model			= $this->getModel('register');
						$subscriberdata	= $model->getSubscriberData ((int) $subscriberid);

						// The error code numbers are the same like in the subscribers-table check function
						if ($subscriberdata->archive_flag == 1) {
							$err->err_code	= 405;
							$err->err_msg	= 'COM_BWPOSTMAN_ERROR_SUB_EDITLINK_ACCOUNTBLOCKED';
						}
						elseif ($subscriberdata->status == 0) {
							$err->err_code	= 406;
							$err->err_msg	= 'COM_BWPOSTMAN_ERROR_SUB_EDITLINK_ACCOUNTNOTACTIVATED';
						}

						if ($err->err_code != 0) {
							$this->errorSubscriberData($err, $subscriberid, $subscriberdata->email);
						}
						else {
							$model	= $this->getModel('edit');
							$itemid	= (int) $model->getItemid(); // Itemid from edit-view

							$this->loginGuest((int) $subscriberid, (int) $itemid);
						}
					}
				}
			}
			$this->setData((int) $subscriberid, (int) $userid);
		}
	}

	/**
	 * Method to reset the subscriber ID and userid
	 *
	 * @access	public
	 *
	 * @param	int $subscriberid   subcriber ID
	 * @param 	int $userid         user ID
	 */
	public function setData($subscriberid = 0, $userid = 0)
	{
		$app	= JFactory::getApplication();
		$app->setUserState('subscriber.id', $subscriberid);

		$this->_subscriberid	= $subscriberid;
		$this->_userid			= $userid;
	}

	/**
	 * Display
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	boolean		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$jinput	= JFactory::getApplication()->input;
		$view	= $jinput->get('view');

		switch ($view) {
			case "newsletters":
				$jinput->set('view', 'newsletters');
				break;

				// View of a single newsletter
			case "newsletter":
				$jinput->set('view', 'newsletter');
				break;

			// Register form
			case "register":
				$session		= JFactory::getSession();
				$session_error	= $session->get('session_error');

				$jinput->set('view', 'edit');

				if(!(isset($session_error) && is_array($session_error))) {
					if (($this->_userid) && ($this->_subscriberid)) {
					}
					elseif (($this->_userid) && (!$this->_subscriberid)) {
						$jinput->set('view', 'register');
					}
					elseif ((!$this->_userid) && ($this->_subscriberid)) {
					}
					else {
						$jinput->set('view', 'register');
					}
				}
				break;
			case "edit":
				$session		= JFactory::getSession();
				$session_error	= $session->get('session_error');

				$jinput->set('view', 'edit');

				if(!(isset($session_error) && is_array($session_error))) {
					if (($this->_userid) && ($this->_subscriberid)) {
					}
					elseif (($this->_userid) && (!$this->_subscriberid)) {
						$jinput->set('view', 'register');
					}
					elseif ((!$this->_userid) && ($this->_subscriberid)) {
					}
					else {
						$jinput->set('layout', 'editlink_form');
					}
				}
				break;
		}
		parent::display();
	}

	/**
	 * Method to store the subscriber ID into a session object
	 * --> only if a guest comes from an editlink-uri
	 *
	 * @access	public
	 *
	 * @param 	int $subscriberid   subscriber ID
	 * @param 	int $itemid         menu item ID
	 *
	 * @return	void
	 */
	public function loginGuest($subscriberid = 0, $itemid = null)
	{
		$uri		= JURI::root();
		$session	= JFactory::getSession();

		$session_subscriberid= array('id' => $subscriberid);
		$session->set('session_subscriberid', $session_subscriberid);

		if (is_null($itemid)) {
			$this->setRedirect($uri.'index.php?option=com_bwpostman&view=edit');
		}
		else {
			$this->setRedirect($uri.'index.php?option=com_bwpostman&view=edit&Itemid='.$itemid);
		}
	}

	/**
	 * Method to process invalid subscriber data
	 *
	 * @access	public
	 *
	 * @param 	object   $err            associative array of error data
	 * @param 	int     $subscriberid   subscriber ID
	 * @param 	string  $email          subscriber email
	 */
	public function errorSubscriberData($err, $subscriberid = null, $email = null)
	{
		$jinput		= JFactory::getApplication()->input;
		$session	= JFactory::getSession();

		// The error code numbers 4-6 are the same like in the subscribers-table check function
		switch ($err->err_code) {
			case 405: // Subscriber account is blocked by the system
				$session_error = array('err_msg' => $err->err_msg, 'err_email' => $email, 'err_code' => $err->err_code, 'err_id' => $err->err_id);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountblocked');
				break;
			case 406: // Subscriber account is not activated
				$session_error = array('err_msg' => $err->err_msg, 'err_id' => $subscriberid, 'err_email' => $email, 'err_code' => $err->err_code, 'err_id' => $err->err_id);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountnotactivated');
				break;
			case 407: // Subscriber account already exists
				$model = $this->getModel('edit');
				$itemid = $model->getItemid(); // Itemid from edit-view
				$session_error = array('err_msg' => $err->err_msg, 'err_id' => $subscriberid, 'err_email' => $email, 'err_itemid' => $itemid, 'err_code' => $err->err_code, 'err_id' => $err->err_id);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountgeneral');
				break;
			case 408: // Email doesn't exist
				$model = $this->getModel('register');
				$itemid = $model->getItemid(); // Itemid from register-view
				$session_error = array('err_msg' => $err->err_msg, 'err_id' => 0, 'err_email' => $email, 'err_itemid' => $itemid, 'err_code' => $err->err_code);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_geteditlink');
				break;
		}
		$session->set('session_error', $session_error);
		return;
	}

	/**
	 * Method to process wrong or empty editlinks
	 *
	 * @access 	public
	 */
	public function errorEditlink()
	{
		$jinput		= JFactory::getApplication()->input;
		$session	= JFactory::getSession();

		$session_error	= array('err_msg' => 'COM_BWPOSTMAN_ERROR_WRONGEDITLINK');
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_geteditlink');
		$jinput->set('view', 'register');

		return;
	}

	/**
	 * Method to process wrong or empty activation code
	 *
	 * @access	public
	 * @param	string error message
	 */
	public function errorActivationCode($err_msg)
	{
		$jinput		= JFactory::getApplication()->input;
		$session	= JFactory::getSession();

		$session_error	= array('err_msg' => $err_msg, 'err_id' => 0);
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_accountnotactivated');
		$jinput->set('view', 'register');

		return;
	}

	/**
	 * Method to process a wrong unsubscribe-link
	 *
	 * @access	public
	 * @param 	string error message
	 */
	public function errorUnsubscribe($err_msg)
	{
		$jinput		= JFactory::getApplication()->input;
		$model		= $this->getModel('edit');
		$itemid		= $model->getItemid(); // Itemid from edit-view
		$session	= JFactory::getSession();

		$session_error	= array('err_msg' => $err_msg, 'err_itemid' => $itemid);
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_accountgeneral');
		$jinput->set('view', 'register');

		return;
	}

	/**
	 * Method to process errors which occur if an email couldn't been send
	 *
	 * @access	public
	 *
	 * @param	string $err_msg     error message
	 * @param 	string $email       email error
	 */
	public function errorSendingEmail($err_msg, $email = null)
	{
		$jinput			= JFactory::getApplication()->input;
		$session		= JFactory::getSession();
		$session_error	= array('err_msg' => $err_msg, 'err_email' => $email);

		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_email');
		$jinput->set('view', 'register');

		return;
	}

	/**
	 * Method to process successfully performed actions
	 *
	 * @access	public
	 *
	 * @param 	string  $success_msg     success message
	 * @param 	string  $editlink        editlink
	 * @param 	int     $itemid         menu item ID
	 */
	public function success($success_msg, $editlink = null, $itemid = null)
	{
		$jinput				= JFactory::getApplication()->input;
		$session			= JFactory::getSession();
		$session_success	= array('success_msg' => $success_msg, 'editlink' => $editlink, 'itemid' => $itemid);

		$session->set('session_success', $session_success);

		$jinput->set('layout', 'success_msg');
		$jinput->set('view', 'register');

		return;
	}

	/**
	 * Method to save changes from the edit-view
	 *
	 * @access public
	 * @since	1.0.1
	 */
	public function save()
	{

		$jinput	= JFactory::getApplication()->input;
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

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
					));

		$newEmail	= false;

		if (isset($post['unsubscribe'])) {
			$this->unsubscribe($post['id']);
			$link = JRoute::_('index.php?option=com_bwpostman&view=register', false);
		}
		else {
			$model = $this->getModel('edit');

			// Email address has changed
			if (($post['email'] != "") && ($post['email'] != $model->getEmailaddress($post['id']))){
				$newEmail					= true;
				$post['status'] 			= 0;
				$post['confirmation_date'] 	= 0;
				$post['confirmed_by'] 		= '-1';
				$post['activation']			= $model->getActivation();
			}

			// Store the data if possible
			if (!$model->save($post)) {
				// Store the input data into the session object
				$session			= JFactory::getSession();
				$error              = $model->getError();
				$subscriber_data	= array('id' => $post['id'], 'name' => $post['name'], 'firstname' => $post['firstname'], 'email' => $post['email'], 'emailformat' => $post['emailformat'], 'list' => $post['list'], 'err_code' => $error['err_id']);
				$session->set('subscriber_data', $subscriber_data);

				$jinput->set('view', 'edit');
			}
			else { // Storing the data has been successful
				if ($newEmail) { // A new email address has been stored --> the account needs to be confirmed again
					$subscriber = new stdClass();
					$subscriber->name 		= $post['name'];
					$subscriber->firstname	= $post['firstname'];
					$subscriber->email 		= $post['email'];
					$subscriber->activation = $post['activation'];

					$type	= 3; // Send confirmation email
					$itemid = $model->getItemid();

					// Send confirmation mail
					$res = $this->_sendMail($subscriber, $type, $itemid);

					if ($res === true) { // Email has been sent
						$success_msg = 'COM_BWPOSTMAN_SUCCESS_CONFIRMEMAIL';
						$this->success($success_msg);
					} else { // Email has not been sent
						$err_msg 	= 'COM_BWPOSTMAN_ERROR_CONFIRMEMAIL';
						$this->errorSendingEmail($err_msg, $post['email']);
					}

					$session				= JFactory::getSession();
					$session_subscriberid	= $session->get('session_subscriberid');

					if(isset($session_subscriberid) && is_array($session_subscriberid)){
						$session->clear('session_subscriberid');
					}
					$jinput->set('view', 'register');
				}
				else { // No new email address has been stored --> the account doesn't need to be confirmed again
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CHANGES_SAVED_SUCCESSFULLY', 'message'));

					// If the user has choosen the button "save modifications & leave edit mode" we clear the session object
					// now no subscriber_id is stored into the session
					if ($post['edit'] == "submitleave") {
						$session				= JFactory::getSession();
						$session_subscriberid	= $session->get('session_subscriberid');

						if(isset($session_subscriberid) && is_array($session_subscriberid)){
							$session->clear('session_subscriberid');
						}
						$jinput->set('view', 'register');
					}
					else {
						$uid	= $model->getUserId($post['id']);
						$this->setData($post['id'], $uid);

						$app->setUserState('subscriber.id', $post['id']);
						$jinput->set('view', 'edit');
					}
				}
			}
		$link = JRoute::_('index.php?option=com_bwpostman&view=edit&Itemid=' . $model->getItemid(), false);
		}
	$this->setRedirect($link);
	parent::display();
	}

	/**
	 * Method to save the registration
	 *
	 * @access public
	 * @author Romana Boldt
	 *
	 * @since	1.0.1
	 */
	public function register_save()
	{
		$jinput	= JFactory::getApplication()->input;
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$model		= $this->getModel('register');
		$session	= JFactory::getSession();
		$error		= $session->get('session_error');

		$post	= $jinput->getArray(
					array(
						'agreecheck_mod' => 'string',
						'a_emailformat' => 'string',
						'a_firstname' => 'string',
						'a_name' => 'string',
						'a_gender' => 'string',
						'a_special' => 'string',
						'agreecheck' => 'string',
						'emailformat' => 'string',
						'firstname' => 'string',
						'name' => 'string',
						'gender' => 'string',
						'special' => 'string',
						'email' => 'string',
						'falle' => 'string',
						'language' => 'string',
						'mailinglists' => 'array',
						'firstname_field_obligation' => 'string',
						'name_field_obligation' => 'string',
						'special_field_obligation' => 'string',
						'firstname_field_obligation_mod' => 'string',
						'name_field_obligation_mod' => 'string',
						'special_field_obligation_mod' => 'string',
						'show_special_mod' => 'string',
						'show_special' => 'string',
						'show_name_field' => 'string',
						'show_name_field_mod' => 'string',
						'show_firstname_field' => 'string',
						'show_firstname_field_mod' => 'string',
						'registration_ip' => 'string',
						'stringQuestion' => 'string',
						'stringCaptcha' => 'string',
						'codeCaptcha' => 'string',
						'bwp-' . BwPostmanHelper::getCaptcha(1) => 'string',
						'bwp-' . BwPostmanHelper::getCaptcha(2) => 'string',
						'task' => 'string'
					));

		if (isset($post['a_firstname'])) {
			if ($post['a_firstname'] == JText::_('COM_BWPOSTMAN_FIRSTNAME')) {
				$post['firstname']	= '';
			} else {
				$post['firstname']	= $post['a_firstname'];
			}
			unset($post['a_firstname']);
		}

		if (isset($post['a_name'])) {
			if ($post['a_name'] == JText::_('COM_BWPOSTMAN_NAME')) {
				$post['name']	= '';
			} else {
				$post['name']	= $post['a_name'];
			}
			unset($post['a_name']);
		}

		if (isset($post['a_gender'])) {
			$post['gender']	= $post['a_gender'];
			unset($post['a_gender']);
		}

		if (isset($post['a_special'])) {
			$post['special']	= $post['a_special'];
			unset($post['a_special']);
		}

		if (isset($post['name_field_obligation_mod'])) {
			$post['name_field_obligation']	= $post['name_field_obligation_mod'];
			unset($post['name_field_obligation_mod']);
		}

		if (isset($post['firstname_field_obligation_mod'])) {
			$post['firstname_field_obligation']	= $post['firstname_field_obligation_mod'];
			unset($post['firstname_field_obligation_mod']);
		}

		if (isset($post['special_field_obligation_mod'])) {
			$post['special_field_obligation']	= $post['special_field_obligation_mod'];
			unset($post['special_field_obligation_mod']);
		}

		if (isset($post['show_name_field_mod'])) {
			$post['show_name_field']	= $post['show_name_field_mod'];
			unset($post['show_name_field_mod']);
		}

		if (isset($post['show_firstname_field_mod'])) {
			$post['show_firstname_field']	= $post['show_firstname_field_mod'];
			unset($post['show_name_firstfield_mod']);
		}

		if (isset($post['show_special_mod'])) {
			$post['show_special']	= $post['show_special_mod'];
			unset($post['show_special_mod']);
		}

		if (isset($post['a_emailformat'])) {
			$post['emailformat']	= $post['a_emailformat'];
			unset($post['a_emailformat']);
		}

		if (isset($post['agreecheck_mod'])) {
			$post['agreecheck']	= $post['agreecheck_mod'];
			unset($post['agreecheck_mod']);
		}

		$app->setUserState('com_bwpostman.subscriber.register.data', $post);

		// Subscriber is guest
		if (!$this->_userid) {
			// Check if the email-adress from the registration form is stored in user-table and gives back the id
			$post['user_id'] = $model->isRegUser($post['email']);
			// Subscriber is user
		} else {
			$post['user_id'] = $this->_userid;
		}

		$date = JFactory::getDate();
		$time = $date->toSql();

		$post['status'] 			= 0;
		$post['registration_date'] 	= $time;
		$post['registered_by'] 		= 0;
		$post['confirmed_by'] 		= '-1';
		$post['archived_by'] 		= '-1';

		if (!$model->save($post)) {
			$subscriber_data = array('name' => $post['name'], 'firstname' => $post['firstname'], 'email' => $post['email'], 'emailformat' => $post['emailformat'], 'mailinglists' => $post['mailinglists']);
			$session->set('subscriber_data', $subscriber_data);

			$err = $app->getUserState('com_bwpostman.subscriber.register.error', null);

			if (is_array($err)) {
				$err	= JArrayHelper::toObject($err);
				$this->errorSubscriberData($err, $post['user_id'], $post['email']);
			}
			else {
				$link = JRoute::_('index.php?option=com_bwpostman&view=register', false);
				$this->setRedirect($link);
			}
		}
		else {
			$subscriber				= new stdClass();
			$subscriber->name 		= $post['name'];
			$subscriber->firstname	= $post['firstname'];
			$subscriber->email 		= $post['email'];
			$subscriber->activation = $app->getUserState('com_bwpostman.subscriber.activation', '');

			$type	= 0; // Send Registration email
			$itemid = $model->getItemid();

			// Send registration confirmation mail
			$res = $this->_sendMail($subscriber, $type, $itemid);

			if ($res === true) { // Email has been sent
				$msg = 'COM_BWPOSTMAN_SUCCESS_ACCOUNTREGISTRATION';
				$this->success($msg);
			} else { // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_REGISTRATIONEMAIL';
				$this->errorSendingEmail($err_msg, $post['email']);
			}
		}
		parent::display();
	}

	/**
	 * Method to unsubscribe
	 * --> through an unsubscribe-link
	 * --> through the edit view
	 *
	 * @access	public
	 *
	 * @param 	int $id     Subscriber ID
	 */
	public function unsubscribe($id = null)
	{
		// Initialize some variables
		$jinput	= JFactory::getApplication()->input;
		$db		= JFactory::getDBO();
		$model	= $this->getModel('register');
		$itemid	= $model->getItemid();

		// We come from the edit view
		if ($id) {
			$unsubscribedata	= $model->getSubscriberData($id);
			$email				= $unsubscribedata->email;
			$editlink			= $unsubscribedata->editlink;

		// We come from an unsubscribe-link
		}
		else {
			// Do we have an code?
			$editlink = $jinput->get('code', '', '', 'alnum');
			$editlink = $db->escape($editlink);

			// Do we have an email address?
			$email = $jinput->get('email', '', '', 'string');
			$email = $db->escape($email);

		}

		// Editlink-variable or email-variable is empty
		if ((empty($editlink)) || (empty($email))) {
			$msg		= 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
			$msg_type	= 'error';
			$this->errorUnsubscribe($msg);
		}
		else {
			// The editlink or email don't exist in the subscribers-table
			$msg    = '';
			if (!$model->unsubscribe($editlink, $email, $msg)) {
				$msg		= 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
				$msg_type	= 'error';
				$this->errorUnsubscribe($msg);
			}
			else { // Everything is fine, account has been deleted
				$msg = 'COM_BWPOSTMAN_SUCCESS_UNSUBSCRIBE';
				$msg_type	= 'message';
				$this->success($msg, $editlink, $itemid);
			}
		}

		// If we come from the edit view we have to clear the session object
		// otherwise the subscriber can get to the edit view again
		$session				= JFactory::getSession();
		$session_subscriberid	= $session->get('session_subscriberid');

		if(isset($session_subscriberid) && is_array($session_subscriberid)){
			$session->clear('session_subscriberid');
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_SUCCESS_UNSUBSCRIBE'), $msg_type);
		$jinput->set('view', 'register');
		parent::display();

	}

	/**
	 * Method to activate an account via the activation link
	 *
	 * @access public
	 */
	public function activate()
	{
		// Initialize some variables
		$db		= JFactory::getDBO();
		$jinput	= JFactory::getApplication()->input;

		// Do we have an activation string?
		$activation		= $jinput->getAlnum('subscriber', '');
		$activation		= $db->escape($activation);
		$activation_ip	= $_SERVER['REMOTE_ADDR'];
		$params 		= JComponentHelper::getParams('com_bwpostman');
		$send_mail		= $params->get('activation_to_webmaster');

		// No activation string
		if (empty($activation)) {
			$err_msg = 'COM_BWPOSTMAN_ERROR_WRONGACTIVATIONCODE';
			$this->errorActivationCode($err_msg);
		}
		else {
			$model = $this->getModel('register');

			// An error occured while activation the subscriber account
			$err_msg    = '';
			$editlink   = '';
			$subscriber_id = $model->activateSubscriber($activation, $err_msg, $editlink, $activation_ip);
			if ($subscriber_id == false) {
				$this->errorActivationCode($err_msg);
				// Everything is okay, account has been activated
			} else {
				// Show a forwarding link to edit the subscriber account
				// --> a guest needs the editlink, a user not
				// --> we also need the menu item ID if we want to get right menu item when calling the forward link
				$itemid = $model->getItemid();
				$success_msg = 'COM_BWPOSTMAN_SUCCESS_ACCOUNTACTIVATION';
				$this->success($success_msg, $editlink, $itemid);
				if ($send_mail) $model->sendActivationNotification($subscriber_id);
			}
		}

		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to send the editlink
	 * --> is needed to get access to the editform
	 *
	 * @access public
	 */
	public function sendEditlink ()
	{
		$jinput	= JFactory::getApplication()->input;
		$model	= $this->getModel('register');
		$post	= $jinput->getArray(
				array(
						'email' => 'string',
						'language' => 'string',
						'task' => 'string',
						'option' => 'string'
				));

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$id				= $model->isRegSubscriber($post['email']);
		$err			= new stdClass();
		$err->err_code	= 0;
		$editlink		= '';
		$subscriber		= new stdClass();
		$subscriberdata = $model->getSubscriberData($id);

		if (!is_object($subscriberdata)) {
			$subs_id		= null;
			$err->err_code	= 408; // Email address doesn't exist
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_EMAILDOESNTEXIST';
		}
		elseif ($subscriberdata->archive_flag == 1) {
			$subs_id		= $subscriberdata->id;
			$err->err_code	= 405; // Email address exists but is blocked
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
		}
		elseif ($subscriberdata->status == 0) {
			$subs_id		= $subscriberdata->id;
			$err->err_code	= 406; // Email address exists but account is not activated
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
		}

		if ($err->err_code != 0) {
			// we use not $subscriberdata->id - if $ID==NULL Notice: Trying to get property of non-object
			$this->errorSubscriberData($err, $subs_id, $post['email']);
		}
		else { // Everything is okay
			$subscriber->editlink 	= $subscriberdata->editlink;
			$subscriber->name 		= $subscriberdata->name;
			$subscriber->firstname	= $subscriberdata->firstname;
			$subscriber->email 		= $subscriberdata->email;

			$type	= 1; // Send Editlink
			$model	= $this->getModel('edit');
			$itemid	= $model->getItemid();
			$res	= $this->_sendMail($subscriber, $type, $itemid);

			if ($res === true) { // Email has been sent
				$success_msg 	= 'COM_BWPOSTMAN_SUCCESS_EMAILEDITLINK';
				$this->success($success_msg, $editlink, $itemid);	// We need no editlink or itemid for the output in this layout
			}
			else { // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_EDITLINKEMAIL';
				$this->errorSendingEmail($err_msg, $subscriber->email);
			}
			$jinput->set('view', 'register');
		}
		parent::display();
	}

	/**
	 * Method to send the activation link
	 * --> is needed if someone forgot the activation link
	 *
	 * @access public
	 */
	public function sendActivation()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Get required system objects
		$model			= $this->getModel('register');
		$err			= new stdClass();
		$err->err_code	= 0;
		$post			= $jinput->getArray(
							array(
								'email' => 'string',
								'id' => 'string',
								'task' => 'string',
								'language' => 'string',
								'option' => 'string'
							));

		$id	= $post['id'];

		if (array_key_exists('email', $post)) {
			if ($post['email'] !== NULL)
				$id = $model->isRegSubscriber($post['email']);
		}

		$subscriberdata = $model->getSubscriberData($id);

		if (!is_object($subscriberdata)) {
			$subs_id		= null;
			$err->err_code	= 408; // Email address doesn't exist
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_EMAILDOESNTEXIST';
		}
		elseif ($subscriberdata->archive_flag == 1) {
			$subs_id		= $subscriberdata->id;
			$err->err_code	= 405; // Email address exists but is blocked
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
		}

		if ($err->err_code != 0) {
			$this->errorSubscriberData($err, $subs_id, $post['email']);
		}
		else	{ // Everything is okay
			$subscriber				= new stdClass();
			$subscriber->name 		= $subscriberdata->name;
			$subscriber->firstname	= $subscriberdata->firstname;
			$subscriber->email 		= $subscriberdata->email;
			$subscriber->activation = $subscriberdata->activation;

			$type	= 2; // Send Activation reminder
			$itemid	= $model->getItemid();
			$res	= $this->_sendMail($subscriber, $type, $itemid);

			if ($res === true) {// Email has been sent
				$success_msg 	= 'COM_BWPOSTMAN_SUCCESS_ACTIVATIONEMAIL';
				$this->success($success_msg, $subscriberdata->editlink, $itemid);
			}
			else { // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_ACTIVATIONEMAIL';
				$this->errorSendingEmail($err_msg, $subscriber->email);
			}
		}
		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to send an email
	 *
	 * @param 	object  $subscriber
	 * @param 	int     $type           emailtype	--> 0 = send registration email, 1 = send editlink, 2 = send activation reminder
	 * @param	int     $itemid         menu item ID
	 *
	 * @return 	boolean True on success | error object
	 */
	protected function _sendMail(&$subscriber, $type, $itemid = null)
	{
		$app		= JFactory::getApplication();
		$params 	= JComponentHelper::getParams('com_bwpostman');
		$email 		= $subscriber->email;
		$name 		= $subscriber->name;
		$firstname 	= $subscriber->firstname;
		if ($firstname != '') $name = $firstname . ' ' . $name;

		$sitename			= $app->getCfg('sitename');
		$mailfrom			= $params->get('default_from_email');
		$fromname			= $params->get('default_from_name');
		$active_title		= $params->get('activation_salutation_text');
		$active_intro		= $params->get('activation_text');
		$permission_text	= $params->get('permission_text');
		$legal_information	= $params->get('legal_information_text');
		$active_msg			= $active_title . ' ' . $name . ",\n\n" . $active_intro . "\n";

		$siteURL = JURI::root();

		switch ($type) {
			case 0: // Send Registration email
				$subject 	= JText::sprintf('COM_BWPOSTMAN_SEND_REGISTRATION_SUBJECT', $sitename);

				if (is_null($itemid)) {
					$link 	= $siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}";
				}
				else {
					$link 	= $siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}";
				}
				$message = $active_msg . JText::_('COM_BWPOSTMAN_ACTIVATION_CODE_MSG') . " " . $link . "\n\n" . $permission_text;
				break;
			case 1: // Send Editlink
				$editlink 	= $subscriber->editlink;
				$subject 	= JText::sprintf('COM_BWPOSTMAN_SEND_EDITLINK_SUBJECT', $sitename);
				if (is_null($itemid)) {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_EDITLINK_MSG', $name, $sitename, $siteURL."index.php?option=com_bwpostman&view=edit&editlink={$editlink}");
				}
				else {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_EDITLINK_MSG', $name, $sitename, $siteURL."index.php?option=com_bwpostman&Itemid={$itemid}&view=edit&editlink={$editlink}");
				}
				break;
			case 2: // Send Activation reminder
				$subject 	= JText::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_SUBJECT', $sitename);
				if (is_null($itemid)) {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG', $name, $sitename, $siteURL."index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}");
				}
				else {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG', $name, $sitename, $siteURL."index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}");
				}
				break;
			case 3: // Send confirmation mail because the email address has been changed
				$subject 	= JText::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_SUBJECT', $sitename);
				if (is_null($itemid)) {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG', $name, $siteURL."index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}");
				}
				else {
					$message 	= JText::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG', $name, $siteURL."index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}");
				}
				break;
		}

		$subject	= html_entity_decode($subject, ENT_QUOTES);
		$message	.= "\n\n" . $legal_information;
		$message	= html_entity_decode($message, ENT_QUOTES);

		// Get a JMail instance
		$mailer		= JFactory::getMailer();
		$sender		= array();
		$reply		= array();

		$sender[0]	= $mailfrom;
		$sender[1]	= $fromname;

		$reply[0]	= $mailfrom;
		$reply[1]	= $fromname;

		$mailer->setSender($sender);
		$mailer->addReplyTo($reply[0],$reply[1]);
		$mailer->addRecipient($email);
		$mailer->setSubject($subject);
		$mailer->setBody($message);

		$res = $mailer->Send();

		return $res;
	}

	/**
	 * Method to show a captcha
	 *
	 * @since	1.0.1
	 */
	public function showCaptcha() {
		BwPostmanHelper::showCaptcha();
	}
}
