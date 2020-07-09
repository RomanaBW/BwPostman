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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\UserHelper;

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
				$itemid        = BwPostmanSubscriberHelper::getMenuItemid('edit'); // Itemid from edit-view
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
				$itemid        = BwPostmanSubscriberHelper::getMenuItemid('register'); // Itemid from register-view
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
		$itemid  = BwPostmanSubscriberHelper::getMenuItemid('edit'); // Itemid from edit-view
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
	 * @return    boolean|\JException True on success | error object
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function sendMail(&$subscriber, $type, $itemid = null)
	{
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_bwpostman');

		$name      = $subscriber->name;
		$firstname = $subscriber->firstname;
		if ($firstname != '')
		{
			$name = $firstname . ' ' . $name;
		} //Cat fo full name

		$sitename = Factory::getConfig()->get('sitename');
		$siteURL  = Uri::root();

		$active_title      = Text::_($params->get('activation_salutation_text'));
		$active_intro      = Text::_($params->get('activation_text'));
		$permission_text   = Text::_($params->get('permission_text'));

		$active_msg        = $active_title;

		if ($name !== '')
		{
			$active_msg        .= ' ' . $name;
		}

		$activationSalutation = $active_msg;
		$active_msg        .= "\n\n" . $active_intro . "\n";

		$body    = '';
		$subject = '';


		switch ($type)
		{
			case 0: // Send registration email, registration by frontend
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

				$body = $active_msg . Text::_('COM_BWPOSTMAN_ACTIVATION_CODE_MSG') . " " . $link . "\n\n" . $permission_text;
				$body .= "\n\n" . Text::_($params->get('legal_information_text'));
				break;
			case 1: // Send Editlink
				$editlink = $subscriber->editlink;
				$subject  = Text::sprintf('COM_BWPOSTMAN_SEND_EDITLINK_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_EDITLINK_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&view=edit&editlink={$editlink}"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_EDITLINK_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=edit&editlink={$editlink}"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text'));
				break;
			case 2: // Send Activation reminder
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$activationSalutation,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$activationSalutation,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text'));
				break;
			case 3: // Send confirmation mail because the email address has been changed
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$activationSalutation,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$activationSalutation,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text'));
				Factory::getApplication()->enqueueMessage(Text::_("COM_BWPOSTMAN_SEND_CONFIRM_SCREEN_MSG"));
				break;
			case 4: // Send registration mail because of import or new account
				$subject 	= Text::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body 	= Text::sprintf(
						'COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG',
						$name,
						$siteURL,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
				else
				{
					$body 	= $activationSalutation;
					$body 	.= Text::sprintf(
						'COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG',
						$siteURL,
						$siteURL . "index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}"
					);
				}
		}

		$subscriber_id = $app->getUserState("com_bwpostman.subscriber.id");

		if(isset($subscriber_id))
		{
			PluginHelper::importPlugin('bwpostman');

			if (PluginHelper::isEnabled('bwpostman', 'personalize'))
			{
				$app->triggerEvent('onBwPostmanPersonalize', array('com_bwpostman.send', &$body, $subscriber_id));
			}
		}

		$subject = html_entity_decode($subject, ENT_QUOTES);
		$body = html_entity_decode($body, ENT_QUOTES);

		// Get a JMail instance and fill in mailer data
		$mailer = Factory::getMailer();
		$sender = array();
		$reply  = array();

		$sender[0] = $params->get('default_from_email');
		$sender[1] = Text::_($params->get('default_from_name'));

		$reply[0] = $params->get('default_from_email');
		$reply[1] = Text::_($params->get('default_from_name'));

		$mailer->setSender($sender);
		$mailer->addReplyTo($reply[0], $reply[1]);
		$mailer->addRecipient($subscriber->email);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->isHtml(false);

		$res = $mailer->Send();

		return $res;
	}

	/**
	 * Method to build the gender select list
	 *
	 * @param string   $gender_selected
	 * @param string   $name
	 * @param string   $class
	 *
	 * @return string
	 *
	 * @since       2.0.0 (here)
	 */
	public static function buildGenderList($gender_selected = '2', $name = 'gender', $class = '')
	{

		if ($class != '')
		{
			$class = ' class="' . $class . '"';
		}
		$gender = '<select id="gender"' . $class . ' name="'  . $name . '">';

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

	/**
	 * Method to check if a subscriber has a subscription to a specific mailinglist
	 *
	 * @param JForm $form   subscriber form
	 *
	 * @since 2.4.0 here
	 */
	static public function customizeSubscriberDataFields(&$form)
	{
		// Check to show confirmation data or checkbox
		$c_date	= strtotime($form->getValue('confirmation_date'));
		if (empty($c_date))
		{
			$form->setFieldAttribute('confirmation_date', 'type', 'hidden');
			$form->setFieldAttribute('confirmed_by', 'type', 'hidden');
			$form->setFieldAttribute('confirmation_ip', 'type', 'hidden');
		}
		else
		{
			$form->setFieldAttribute('status', 'type', 'hidden');
		}

		// Check to show registration data
		$r_date	= $form->getValue('registration_date');
		if (empty($r_date))
		{
			$form->setFieldAttribute('registration_date', 'type', 'hidden');
			$form->setFieldAttribute('registered_by', 'type', 'hidden');
			$form->setFieldAttribute('registration_ip', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == Factory::getDbo()->getNullDate())
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}
	}

	/**
	 * Method to get the Joomla UID by email
	 *
	 * @param   string      $email
	 *
	 * @return  integer     $user_id
	 *
	 * @since   2.4.0 (here)
	 */
	public static function getJoomlaUserIdByEmail($email)
	{
		$_db   = Factory::getDbo();
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__users'));
		$query->where($_db->quoteName('email') . ' = ' . $_db->Quote($email));

		$_db->setQuery($query);

		$user_id = (int)$_db->loadResult();

		return $user_id;
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @param   string  $view
	 *
	 * @return 	int     $itemid     menu item ID
	 *
	 * @since       0.9.1
	 */
	public static function getMenuItemid($view)
	{
		$_db = Factory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=' . $view));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		$_db->setQuery($query);
		$itemid = $_db->loadResult();

		if (empty($itemid))
		{
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$_db->setQuery($query);
			$itemid = $_db->loadResult();
		}

		return $itemid;
	}
}
