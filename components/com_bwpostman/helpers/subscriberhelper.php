<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber helper class for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
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

defined('_JEXEC') or die;

/**
 * Class BwPostmanSubscriberHelper
 */
class BwPostmanSubscriberHelper
{
	/**
	 * Method to store the subscriber ID into a session object
	 * --> only if a guest comes from an editlink-uri
	 *
	 * @access	public
	 *
	 * @param 	int $subscriberid   subscriber ID
	 * @param 	int $itemid         menu item ID
	 *
	 * @return	string  $link
	 */
	public static function loginGuest($subscriberid = 0, $itemid = null)
	{
		$session	= JFactory::getSession();

		$session_subscriberid= array('id' => $subscriberid);
		$session->set('session_subscriberid', $session_subscriberid);

		$link   = 'index.php?option=com_bwpostman&view=edit';

		if (!is_null($itemid)) {
			$link .= '&Itemid='.$itemid;
		}
		return $link;
	}

	/**
	 * Method to check by user ID if a user has a newsletter account (user = no guest)
	 *
	 * @access 	public
	 *
	 * @param 	int $uid    user ID
	 *
	 * @return 	int $id     subscriber ID
	 */
	public static function getSubscriberID ($uid)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $uid);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery((string) $query);
		$id = $_db->loadResult();

		if (empty($id)) {
			$id = 0;
		}
		return $id;
	}

	/**
	 * Method to get the data of a subscriber who has a newsletter account from the subscribers-table
	 * because we need to know if his account is okay or archived or not activated (user = no guest)
	 *
	 * @access 	public
	 *
	 * @param 	int     $id         subscriber ID
	 *
	 * @return 	object  $subscriber subscriber object
	 */
	public static function getSubscriberData ($id)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		$_db->setQuery($query);

		$subscriber = $_db->loadObject();

		return $subscriber;
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
	public static function errorSubscriberData($err, &$subscriberid = null, $email = null)
	{
		$jinput		    = JFactory::getApplication()->input;
		$session	    = JFactory::getSession();
		$session_error  = array();

		// The error code numbers 4-6 are the same like in the subscribers-table check function
		switch ($err->err_code) {
			case 405: // Subscriber account is blocked by the system
				$session_error = array(
									'err_msg' => $err->err_msg,
									'err_email' => $email,
									'err_code' => $err->err_code,
									'err_id' => $err->err_id,
									'err_subs_id' => $subscriberid
								);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountblocked');
				break;
			case 406: // Subscriber account is not activated
				$session_error = array(
									'err_msg' => $err->err_msg,
									'err_id' => $err->err_id,
									'err_email' => $email,
									'err_code' => $err->err_code,
									'err_subs_id' => $subscriberid
								);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountnotactivated');
				break;
			case 407: // Subscriber account already exists
				$model = JModelLegacy::getInstance('edit', 'BwpostmanModel');
				$itemid = $model->getItemid(); // Itemid from edit-view
				$session_error = array(
									'err_msg' => $err->err_msg,
									'err_id' => $err->err_id,
									'err_email' => $email,
									'err_itemid' => $itemid,
									'err_code' => $err->err_code,
									'err_subs_id' => $subscriberid
								);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountgeneral');
				break;
			case 408: // Email doesn't exist
				$model = JModelLegacy::getInstance('register', 'BwpostmanModel');
				$itemid = $model->getItemid(); // Itemid from register-view
				$session_error = array(
									'err_msg' => $err->err_msg,
									'err_id' => 0,
									'err_email' => $email,
									'err_itemid' => $itemid,
									'err_code' => $err->err_code,
									'err_subs_id' => $subscriberid
								);
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
	public static function errorEditlink()
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
	public static function errorActivationCode($err_msg)
	{
		$jinput		= JFactory::getApplication()->input;
		$session	= JFactory::getSession();

		$session_error	= array(
							'err_msg' => $err_msg,
							'err_id' => 0
						);
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
	public static function errorUnsubscribe($err_msg)
	{
		$jinput		= JFactory::getApplication()->input;
		$model = JModelLegacy::getInstance('edit', 'BwpostmanModel');
		$itemid		= $model->getItemid(); // Itemid from edit-view
		$session	= JFactory::getSession();

		$session_error	= array(
							'err_msg' => $err_msg,
							'err_itemid' => $itemid
						);
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
	public static function errorSendingEmail($err_msg, $email = null)
	{
		$jinput			= JFactory::getApplication()->input;
		$session		= JFactory::getSession();
		$session_error	= array(
							'err_msg' => $err_msg,
							'err_email' => $email
						);

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
	public static function success($success_msg, $editlink = null, $itemid = null)
	{
		$jinput				= JFactory::getApplication()->input;
		$session			= JFactory::getSession();
		$session_success	= array(
								'success_msg' => $success_msg,
								'editlink' => $editlink,
								'itemid' => $itemid
							);

		$session->set('session_success', $session_success);

		$jinput->set('layout', 'success_msg');
		$jinput->set('view', 'register');

		return;
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
	public static function sendMail(&$subscriber, $type, $itemid = null)
	{
		$params 	= JComponentHelper::getParams('com_bwpostman');
		$email 		= $subscriber->email;
		$name 		= $subscriber->name;
		$firstname 	= $subscriber->firstname;
		if ($firstname != '') $name = $firstname . ' ' . $name;

		$sitename			= JFactory::getConfig()->get('sitename');
		$mailfrom			= $params->get('default_from_email');
		$fromname			= $params->get('default_from_name');
		$active_title		= $params->get('activation_salutation_text');
		$active_intro		= $params->get('activation_text');
		$permission_text	= $params->get('permission_text');
		$legal_information	= $params->get('legal_information_text');
		$active_msg			= $active_title . ' ' . $name . ",\n\n" . $active_intro . "\n";
		$message            = '';
		$subject            = '';

		$siteURL = JUri::root();

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

}
