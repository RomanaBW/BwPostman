<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber helper class for frontend.
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Class BwPostmanSubscriberHelper
 *
 * @since       2.0.0
 */
class BwPostmanSubscriberHelper
{
	/**
	 * Method to store the subscriber ID into a session object
	 * --> only if a guest comes from an editlink-uri
	 *
	 * @access      public
	 *
	 * @param       int $subscriberid subscriber ID
	 * @param       int $itemid       menu item ID
	 *
	 * @return      string  $link
	 *
	 * @since       2.0.0 (here)
	 */
	public static function loginGuest($subscriberid = 0, $itemid = null)
	{
		$session = Factory::getSession();

		$session_subscriberid = array('id' => $subscriberid);
		$session->set('session_subscriberid', $session_subscriberid);

		$link = 'index.php?option=com_bwpostman&view=edit';

		if (!is_null($itemid))
		{
			$link .= '&Itemid=' . $itemid;
		}

		return $link;
	}

	/**
	 * Method to check by user ID if a user has a newsletter account (user = no guest)
	 *
	 * Returns 0 if user has no newsletter subscription
	 *
	 * @param    int $uid user ID
	 *
	 * @return    int $id     subscriber ID
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function getSubscriberID($uid)
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' = ' . (int) $uid);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		try
		{
			$_db->setQuery($query);
			$id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (empty($id))
		{
			$id = 0;
		}

		return $id;
	}

	/**
	 * Method to get the data of a subscriber who has a newsletter account from the subscribers-table
	 * because we need to know if his account is okay or archived or not activated (user = no guest)
	 *
	 * @access    public
	 *
	 * @param    int $id subscriber ID
	 *
	 * @return    object  $subscriber subscriber object
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function getSubscriberData($id)
	{
		$subscriber = null;
		$_db        = Factory::getDbo();
		$query      = $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		try
		{
			$_db->setQuery($query);
			$subscriber = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $subscriber;
	}

	/**
	 * Method to process invalid subscriber data
	 *
	 * @access    public
	 *
	 * @param    object $err          associative array of error data
	 * @param    int    $subscriberid subscriber ID
	 * @param    string $email        subscriber email
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorSubscriberData($err, &$subscriberid = null, $email = null)
	{
		$jinput        = Factory::getApplication()->input;
		$session       = Factory::getSession();
		$session_error = array();

		// The error code numbers 4-6 are the same like in the subscribers-table check function
		switch ($err->err_code)
		{
			case 405: // Subscriber account is blocked by the system
				$session_error = array(
					'err_msg'     => $err->err_msg,
					'err_email'   => $email,
					'err_code'    => $err->err_code,
					'err_id'      => $err->err_id,
					'err_subs_id' => $subscriberid
				);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountblocked');
				break;
			case 406: // Subscriber account is not activated
				$session_error = array(
					'err_msg'     => $err->err_msg,
					'err_id'      => $err->err_id,
					'err_email'   => $email,
					'err_code'    => $err->err_code,
					'err_subs_id' => $subscriberid
				);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountnotactivated');
				break;
			case 407: // Subscriber account already exists
				require_once(JPATH_SITE . '/components/com_bwpostman/models/edit.php');
				$model = new BwPostmanModelEdit();
				$itemid        = $model->getItemid(); // Itemid from edit-view
				$session_error = array(
					'err_msg'     => $err->err_msg,
					'err_id'      => $err->err_id,
					'err_email'   => $email,
					'err_itemid'  => $itemid,
					'err_code'    => $err->err_code,
					'err_subs_id' => $subscriberid
				);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_accountgeneral');
				break;
			case 408: // Email doesn't exist
				require_once(JPATH_SITE . '/components/com_bwpostman/models/register.php');
				$model = new BwPostmanModelRegister();
				$itemid        = $model->getItemid(); // Itemid from register-view
				$session_error = array(
					'err_msg'     => $err->err_msg,
					'err_id'      => 0,
					'err_email'   => $email,
					'err_itemid'  => $itemid,
					'err_code'    => $err->err_code,
					'err_subs_id' => $subscriberid
				);
				$jinput->set('view', 'register');
				$jinput->set('layout', 'error_geteditlink');
				break;
		}

		$session->set('session_error', $session_error);
	}

	/**
	 * Method to process wrong or empty edit links
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorEditlink()
	{
		$jinput  = Factory::getApplication()->input;
		$session = Factory::getSession();

		$session_error = array('err_msg' => 'COM_BWPOSTMAN_ERROR_WRONGEDITLINK');
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_geteditlink');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to process wrong or empty activation code
	 *
	 * @param    string error message
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorActivationCode($err_msg)
	{
		$jinput  = Factory::getApplication()->input;
		$session = Factory::getSession();

		$session_error = array(
			'err_msg' => $err_msg,
			'err_id'  => 0
		);
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_accountnotactivated');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to process a wrong unsubscribe-link
	 *
	 * @param    string error message
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorUnsubscribe($err_msg)
	{
		$jinput  = Factory::getApplication()->input;
		require_once(JPATH_SITE . '/components/com_bwpostman/models/edit.php');
		$model = new BwPostmanModelEdit();
		$itemid  = $model->getItemid(); // Itemid from edit-view
		$session = Factory::getSession();

		$session_error = array(
			'err_msg'    => $err_msg,
			'err_itemid' => $itemid
		);
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_accountgeneral');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to process errors which occur if an email could not been send
	 *
	 * @param    string $err_msg error message
	 * @param    string $email   email error
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorSendingEmail($err_msg, $email = null)
	{
		$jinput        = Factory::getApplication()->input;
		$session       = Factory::getSession();
		$session_error = array(
			'err_msg'   => $err_msg,
			'err_email' => $email
		);

		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_email');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to process successfully performed actions
	 *
	 * @param    string $success_msg success message
	 * @param    string $editlink    editlink
	 * @param    int    $itemid      menu item ID
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function success($success_msg, $editlink = null, $itemid = null)
	{
		$jinput          = Factory::getApplication()->input;
		$session         = Factory::getSession();
		$session_success = array(
			'success_msg' => $success_msg,
			'editlink'    => $editlink,
			'itemid'      => $itemid
		);

		$session->set('session_success', $session_success);

		$jinput->set('layout', 'success_msg');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to send an email
	 *
	 * @param    object $subscriber
	 * @param    int    $type   emailtype    --> 0 = send registration email, 1 = send editlink, 2 = send activation reminder
	 * @param    int    $itemid menu item ID
	 *
	 * @return    boolean True on success | error object
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function sendMail(&$subscriber, $type, $itemid = null)
	{
		$params    = ComponentHelper::getParams('com_bwpostman');
		$email     = $subscriber->email;
		$name      = $subscriber->name;
		$firstname = $subscriber->firstname;
		if ($firstname != '')
		{
			$name = $firstname . ' ' . $name;
		}

		$sitename          = Factory::getConfig()->get('sitename');
		$mailfrom          = $params->get('default_from_email');
		$fromname          = Text::_($params->get('default_from_name'));
		$active_title      = Text::_($params->get('activation_salutation_text'));
		$active_intro      = Text::_($params->get('activation_text'));
		$permission_text   = Text::_($params->get('permission_text'));
		$legal_information = Text::_($params->get('legal_information_text'));

		$app = Factory::getApplication();
		$subscriber_id = $app->getUserState("com_bwpostman.subscriber.id");

		if(isset($subscriber_id))
		{
			PluginHelper::importPlugin('bwpostman');

			if (PluginHelper::isEnabled('bwpostman', 'personalize'))
			{
				$app->triggerEvent('onBwPostmanPersonalize', array('com_bwpostman.send', &$active_title, $subscriber_id));
			}
		}

		$active_msg        = $active_title;

		if ($name !== '')
		{
			$active_msg        .= ' ' . $name;
		}

		$active_msg        .= "\n\n" . $active_intro . "\n";

		$message           = '';
		$subject           = '';

		$siteURL = Uri::root();

		switch ($type)
		{
			case 0: // Send Registration email
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_REGISTRATION_SUBJECT', $sitename);

				if (is_null($itemid))
				{
					$link = $siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}";
				}
				else
				{
					$link = $siteURL
						. "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}";
				}

				$message = $active_msg . Text::_('COM_BWPOSTMAN_ACTIVATION_CODE_MSG') . " " . $link . "\n\n" . $permission_text;
				break;
			case 1: // Send Editlink
				$editlink = $subscriber->editlink;
				$subject  = Text::sprintf('COM_BWPOSTMAN_SEND_EDITLINK_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_EDITLINK_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&view=edit&editlink={$editlink}"
					);
				}
				else
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_EDITLINK_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=edit&editlink={$editlink}"
					);
				}
				break;
			case 2: // Send Activation reminder
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				else
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				break;
			case 3: // Send confirmation mail because the email address has been changed
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$name,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				else
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$name,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				Factory::getApplication()->enqueueMessage(Text::_("COM_BWPOSTMAN_SEND_CONFIRM_SCREEN_MSG"));
				break;
		}

		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message .= "\n\n" . $legal_information;
		$message = html_entity_decode($message, ENT_QUOTES);

		// Get a JMail instance
		$mailer = Factory::getMailer();
		$sender = array();
		$reply  = array();

		$sender[0] = $mailfrom;
		$sender[1] = $fromname;

		$reply[0] = $mailfrom;
		$reply[1] = $fromname;

		$mailer->setSender($sender);
		$mailer->addReplyTo($reply[0], $reply[1]);
		$mailer->addRecipient($email);
		$mailer->setSubject($subject);
		$mailer->setBody($message);

		$res = $mailer->Send();

		return $res;
	}

	/**
	 * Method to build the gender select list
	 *
	 * @param string   $gender_selected
	 * @param string   $name
	 *
	 * @return string
	 *
	 * @since       2.0.0 (here)
	 */
	public static function buildGenderList($gender_selected = '2', $name = 'gender')
	{

		$gender = '<select id="gender" name="'  . $name . '" >';

		$gender .= '<option value="2"';
		if ($gender_selected == '2')
		{
			$gender .= ' selected="selected"';
		}

		$gender .= '>' . Text::_('COM_BWPOSTMAN_NO_GENDER') . '</option>';

		$gender .= '<option value="0"';
		if ($gender_selected == '0')
		{
			$gender .= ' selected="selected"';
		}

		$gender .= '>' . Text::_('COM_BWPOSTMAN_MALE') . '</option>';

		$gender .= '<option value="1"';
		if ($gender_selected == '1')
		{
			$gender .= ' selected="selected"';
		}

		$gender .= '>' . Text::_('COM_BWPOSTMAN_FEMALE') . '</option>';

		$gender .= '</select>';

		return $gender;
	}

	/**
	 * Method to build the mail format select list
	 *
	 * @param boolean $mailformat_selected
	 *
	 * @return string
	 *
	 * @since   2.0.0
	 */
	public static function buildMailformatSelectList($mailformat_selected)
	{
		$emailformat = '<fieldset id="edit_mailformat" class="radio btn-group">';
		$emailformat .= '<input type="radio" name="emailformat" id="formatText" value="0"';
		if (!$mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatText"><span>' . Text::_('COM_BWPOSTMAN_TEXT') . '</span></label>';
		$emailformat .= '<input type="radio" name="emailformat" id="formatHtml" value="1"';
		if ($mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatHtml"><span>' . Text::_('COM_BWPOSTMAN_HTML') . '</span></label>';
		$emailformat .= '</fieldset>';

		return $emailformat;
	}

	/**
	 * Method to delete all mailinglist entries for the subscriber_id from newsletters_mailinglists-table
	 *
	 * @param $subscriber_id
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public static function deleteMailinglistsOfSubscriber($subscriber_id)
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);
		$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' =  ' . (int) $subscriber_id);

		try
		{
			$_db->setQuery($query);
			$_db->execute();

			return true;
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Method to store subscribed mailinglists in newsletters_mailinglists-table
	 *
	 * @param $subscriber_id
	 * @param $mailinglist_ids
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function storeMailinglistsOfSubscriber($subscriber_id, $mailinglist_ids)
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);

		$query->columns(
			array(
				$_db->quoteName('subscriber_id'),
				$_db->quoteName('mailinglist_id')
			)
		);

		foreach ($mailinglist_ids AS $list_id)
		{
//			$query = $_db->getQuery(true);

			$query->insert($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->values(
				(int) $subscriber_id . ',' .
				(int) $list_id
			);
		}

		try
		{
			$_db->setQuery($query);
			$_db->execute();
			return  true;
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}

	/**
	 * Method to fill void data
	 * --> the subscriber data filled with default values
	 *
	 * @return 	object  $subscriber     subscriber object
	 *
	 * @since       2.0.0 (here)
	 */
	public static function fillVoidSubscriber()
	{
		// Load an empty subscriber
		$subscriber = Table::getInstance('subscribers', 'BwPostmanTable');
		$subscriber->load();
		$subscriber->mailinglists  = array();

		return $subscriber;
	}

	/**
	 * Method to get all mailinglists which the user is authorized to see
	 *
	 * @param   integer     $id
	 *
	 * @return 	object Mailinglists
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function getAuthorizedMailinglists($id)
	{
		$app		    = Factory::getApplication();
		$user_id	    = self::getUserId($id);
		$mailinglists   = null;
		$_db		    = Factory::getDbo();
		$query		    = $_db->getQuery(true);

		// get authorized viewlevels
		$accesslevels	= Access::getAuthorisedViewLevels($user_id);

		if (!in_array('3', $accesslevels))
		{
			// A user shall only see mailinglists which are public or - if registered - accessible for his view level and published
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->order($_db->quoteName('title') . 'ASC');
		}
		else
		{
			// A user with a super user status shall see all mailinglists
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->order($_db->quoteName('title') . 'ASC');
		}

		try
		{
			$_db->setQuery($query);
			$mailinglists = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Does the subscriber has internal mailinglists?
		$selected	= $app->getUserState('com_bwpostman.subscriber.selected_lists', '');

		if (is_array($selected))
		{
			$ml_ids		= array();
			$add_mls	= array();

			// compare available mailinglists with selected mailinglists, get difference
			foreach ($mailinglists as $value)
			{
				$ml_ids[]	= $value->id;
			}

			$get_mls	= array_diff($selected, $ml_ids);

			// if there are internal mailinglists selected, get them ...
			if (is_array($get_mls) && !empty($get_mls))
			{
				$query->clear();
				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('id') . ' IN (' . implode(',', $get_mls) . ')');
				$query->order($_db->quoteName('title') . 'ASC');

				try
				{
					$_db->setQuery($query);
					$add_mls = $_db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		// ...and add them to the mailinglists array
		if (!empty($add_mls))
		{
			$mailinglists	= array_merge($mailinglists, $add_mls);
		}

		return $mailinglists;
	}

	/**
	 * Method to get the user ID of a subscriber from the subscribers-table depending on the subscriber ID
	 * --> is needed for the constructor
	 *
	 * @param 	int     $id     subscriber ID
	 *
	 * @return 	int user ID
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function getUserId($id)
	{
		$user_id    = null;
		$_db	    = Factory::getDbo();
		$query	    = $_db->getQuery(true);

		$query->select($_db->quoteName('user_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('status') . ' != ' . (int) 9);

		try
		{
			$_db->setQuery($query);
			$user_id = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (is_null($user_id))
		{
			$user_id = 0;
		}

		return $user_id;
	}

	/**
	 * Method to get params from mod_bwpostman
	 *
	 * @param	int	    $id     module ID
	 *
	 * @return 	object	$params params object
	 *
	 * @throws Exception
	 *
	 * @since	2.2.0
	 */
	public static function getModParams($id = 0)
	{
		$params = null;
		$_db	= Factory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select('m.params');
		$query->from('#__modules AS m');
		$query->where('m.id = ' . $id);

		try
		{
			$_db->setQuery($query);
			$params	= $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $params;
	}
}
