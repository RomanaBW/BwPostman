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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Table\SubscriberTable;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\LogEntry;
use Joomla\CMS\Mail\Exception\MailDisabledException;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use RuntimeException;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwEmailValidation;
use stdClass;
use UnexpectedValueException;

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
	 * @param int      $subscriberid subscriber ID
	 * @param int|null $itemid       menu item ID
	 *
	 * @return      string  $link
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function loginGuest(int $subscriberid = 0, int $itemid = null): string
	{
		$session = Factory::getApplication()->getSession();

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
	 * @param object      $err          associative array of error data
	 * @param int|null    $subscriberid subscriber ID
	 * @param string|null $email        subscriber email
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorSubscriberData(object $err, int $subscriberid = null, string $email = null): void
	{
		$jinput        = Factory::getApplication()->input;
		$session       = Factory::getApplication()->getSession();
		$session_error = array();

		// The error code numbers 4-6 are the same as in the subscribers-table check function
		switch ($err->err_code)
		{
			case 405: // Subscriber account is blocked by the system, i.e. archived
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
			case 406: // Subscriber account is unconfirmed
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
				$itemid        = self::getMenuItemid('edit'); // Itemid from edit-view
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
				$itemid        = self::getMenuItemid('register'); // Itemid from register-view
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
	public static function errorEditlink(): void
	{
		$session = Factory::getApplication()->getSession();

		$session_error = array('err_msg' => 'COM_BWPOSTMAN_ERROR_WRONGEDITLINK');
		$session->set('session_error', $session_error);
	}

	/**
	 * Method to process wrong or empty activation code
	 *
	 * @param string $err_msg error message
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorActivationCode(string $err_msg): void
	{
		$jinput  = Factory::getApplication()->input;
		$session = Factory::getApplication()->getSession();

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
	 * @param string $err_msg error message
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorUnsubscribe(string $err_msg): void
	{
		$jinput  = Factory::getApplication()->input;
		$itemid  = self::getMenuItemid('edit'); // Itemid from edit-view
		$session = Factory::getApplication()->getSession();

		$session_error = array(
			'err_msg'    => $err_msg,
			'err_itemid' => $itemid
		);
		$session->set('session_error', $session_error);

		$jinput->set('layout', 'error_accountgeneral');
		$jinput->set('view', 'register');
	}

	/**
	 * Method to process errors which occur if an email could not be sent
	 *
	 * @param string $err_msg error message
	 * @param string $email   email error
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function errorSendingEmail(string $err_msg, string $email = ''): void
	{
		$jinput        = Factory::getApplication()->input;
		$session       = Factory::getApplication()->getSession();
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
	 * @param string      $success_msg success message
	 * @param string|null $editlink    editlink
	 * @param int|null    $itemid      menu item ID
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function success(string $success_msg, string $editlink = null, int $itemid = null): void
	{
		$jinput          = Factory::getApplication()->input;
		$session         = Factory::getApplication()->getSession();
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
	 * @param object   $subscriber
	 * @param int      $type   emailtype    --> 0 = send registration email, 1 = send editlink, 2 = send activation reminder
	 * @param int|null $itemid menu item ID
	 *
	 * @return    boolean True on success | error object
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function sendMail(object $subscriber, int $type, int $itemid = null): bool
	{
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_bwpostman');

		$name      = $subscriber->name;
		$firstname = $subscriber->firstname;

		if ($firstname != '')
		{
			$name = $firstname . ' ' . $name;
		} //Cat fo full name

		$sitename = $app->getConfig()->get('sitename');
		$siteURL  = Uri::root();

		$active_title      = Text::_($params->get('activation_salutation_text', ''));
		$active_intro      = Text::_($params->get('activation_text', ''));
		$permission_text   = Text::_($params->get('permission_text', ''));

		$active_msg = $active_title;

		if ($name !== '')
		{
			$active_msg .= ' ' . $name;
		}

		$activationSalutation = $active_msg;
		$active_msg          .= "\n\n" . $active_intro . "\n";

		$body    = '';
		$subject = '';


		switch ($type)
		{
			case 0: // Send registration email, registration by frontend
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_REGISTRATION_SUBJECT', $sitename);

				if (is_null($itemid))
				{
					$link = $siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber=$subscriber->activation";
				}
				else
				{
					$link = $siteURL
						. "index.php?option=com_bwpostman&Itemid=$itemid&view=register&task=activate&subscriber=$subscriber->activation";
				}

				$body = $active_msg . Text::_('COM_BWPOSTMAN_ACTIVATION_CODE_MSG') . " " . $link . "\n\n" . $permission_text;
				$body .= "\n\n" . Text::_($params->get('legal_information_text', ''));
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
						$siteURL . "index.php?option=com_bwpostman&view=edit&editlink=$editlink"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_EDITLINK_MSG',
						$name,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid=$itemid&view=edit&editlink=$editlink"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text', ''));
				break;
			case 2: // Send Activation reminder
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_ACTVIATIONCODE_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$activationSalutation,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_ACTVIATIONCODE_MSG',
						$activationSalutation,
						$sitename,
						$siteURL . "index.php?option=com_bwpostman&Itemid=$itemid&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text', ''));
				break;
			case 3: // Send confirmation mail because the email address has been changed
				$subject = Text::sprintf('COM_BWPOSTMAN_SEND_CONFIRMEMAIL_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$activationSalutation,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
				else
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SEND_CONFIRMEMAIL_MSG',
						$activationSalutation,
						$siteURL . "index.php?option=com_bwpostman&Itemid=$itemid&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
				$body .= "\n\n" . Text::_($params->get('legal_information_text', ''));
				$app->enqueueMessage(Text::_("COM_BWPOSTMAN_SEND_CONFIRM_SCREEN_MSG"));
				break;
			case 4: // Send registration mail because of import or new account
				$subject 	= Text::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_SUBJECT', $sitename);
				if (is_null($itemid))
				{
					$body = Text::sprintf(
						'COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG',
						$name,
						$siteURL,
						$siteURL . "index.php?option=com_bwpostman&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
				else
				{
					$body = $activationSalutation;
					$body .= Text::sprintf(
						'COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG',
						$siteURL,
						$siteURL . "index.php?option=com_bwpostman&Itemid=$itemid&view=register&task=activate&subscriber=$subscriber->activation"
					);
				}
		}

		$subscriber_id = $app->getUserState("com_bwpostman.subscriber.id");

		if(isset($subscriber_id))
		{
			PluginHelper::importPlugin('bwpostman');

			if (PluginHelper::isEnabled('bwpostman', 'personalize'))
			{
//				$arguments = array('com_bwpostman.send', &$body, $subscriber_id);
//				$event = AbstractEvent::create('onBwPostmanPersonalize', $arguments);
//
//				$app->getDispatcher()->dispatch('onBwPostmanPersonalize', $event);
				$app->triggerEvent('onBwPostmanPersonalize', array('com_bwpostman.send', &$body, $subscriber_id));
			}
		}

		$subject = html_entity_decode($subject, ENT_QUOTES);
		$body    = html_entity_decode($body, ENT_QUOTES);

		// Get a JMail instance and fill in mailer data
		$mailer = Factory::getMailer();

		$sender = self::getSender();
		$reply  = self::getReplyTo();

		try
		{
			$mailer->setSender($sender);
			$mailer->addReplyTo($reply[0], $reply[1]);
			$mailer->addRecipient($subscriber->email);
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->isHtml(false);

			$res = $mailer->Send();
		}
		catch (UnexpectedValueException | MailDisabledException | \PHPMailer\PHPMailer\Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'Activation');

			$res        = false;
		}

		return $res;
	}

	/**
	 * Method to build the gender select list
	 *
	 * @param string $gender_selected
	 * @param string $name
	 * @param string $class
	 * @param string $idPrefix
	 *
	 * @return string
	 *
	 * @since       2.0.0 (here)
	 */
	public static function buildGenderList(string $gender_selected = '2', string $name = 'gender', string $class = '', string $idPrefix = ''): string
	{

		if ($class != '')
		{
			$class = ' class="' . $class . '"';
		}
		$genderId = $idPrefix . 'gender';
		$gender = '<select id="' . $genderId . '"' . $class . ' name="'  . $name . '">';

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
	public static function buildMailformatSelectList(bool $mailformat_selected): string
	{
		$emailformat = '<fieldset id="edit_mailformat" class="radio btn-group">';
		$emailformat .= '<input type="radio" name="emailformat" id="formatText" class="rounded-left" value="0"';
		if (!$mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatText" class="rounded-left"><span>' . Text::_('COM_BWPOSTMAN_TEXT') . '</span></label>';
		$emailformat .= '<input type="radio" name="emailformat" id="formatHtml" value="1"';
		if ($mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}

		$emailformat .= ' />';
		$emailformat .= '<label for="formatHtml" class="rounded-right"><span>' . Text::_('COM_BWPOSTMAN_HTML') . '</span></label>';
		$emailformat .= '</fieldset>';

		return $emailformat;
	}

	/**
	 * Method to fill void data
	 * --> the subscriber data filled with default values
	 *
	 * @return    object  $subscriber     subscriber object
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0 (here)
	 */
	public static function fillVoidSubscriber(): object
	{
		// Load an empty subscriber
		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		$subscriber = $MvcFactory->createTable('Subscriber', 'Administrator');
		$subscriber->load();
		$subscriber->mailinglists  = array();

		return $subscriber;
	}

	/**
	 * Method to get params from mod_bwpostman
	 *
	 * @param int $id module ID
	 *
	 * @return 	object	$params params object
	 *
	 * @throws Exception
	 *
	 * @since	2.2.0
	 */
	public static function getModParams(int $id = 0): object
	{
		$params = new stdClass();
		$db     = Factory::getContainer()->get(DatabaseInterface::class);
		$query  = $db->getQuery(true);

		$query->select('params');
		$query->from('#__modules');
		$query->where('id = ' . $db->quote($id));

		try
		{
			$db->setQuery($query);

			$params	= $db->loadObject();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SubscriberHelper BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $params;
	}

	/**
	 * Method to check if a subscriber has a subscription to a specific mailinglist
	 *
	 * @param Form $form subscriber form
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	static public function customizeSubscriberDataFields(Form $form): void
	{
		$nullDate = Factory::getContainer()->get(DatabaseInterface::class)->getNullDate();

		// Check to show confirmation data or checkbox
		$c_date = $form->getValue('confirmation_date', null, $nullDate);

		// check if conformation date is '0000-00-00 00:00:00'
		if ($c_date === $nullDate || $c_date === null)
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
		if ($m_date === $nullDate || $m_date === null)
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}
	}

	/**
	 * Method to get the Joomla UID by email
	 *
	 * @param string $email
	 *
	 * @return  integer     $user_id
	 *
	 * @throws Exception
	 *
	 * @since   2.4.0 (here)
	 */
	public static function getJoomlaUserIdByEmail(string $email): int
	{
		$user_id = 0;

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__users'));
		$query->where($db->quoteName('email') . ' = ' . $db->Quote($email));

		try
		{
			$db->setQuery($query);

			$user_id = (int)$db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $user_id;
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @param string $view
	 *
	 * @return    int     $itemid     menu item ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function getMenuItemid(string $view): int
	{
		$itemid = 0;

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_bwpostman&view=' . $view));
		$query->where($db->quoteName('published') . ' = ' . 1);

		try
		{
			$db->setQuery($query);

			$itemid = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (is_null($itemid))
		{
			$itemid = 0;
		}

		return $itemid;
	}

	/**
	 * Method to create the registered_by value
	 *
	 * @param object $subscriber
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since    2.4.0
	 */
	public static function createSubscriberRegisteredBy(object $subscriber): void
	{
		if ($subscriber->registered_by == 0)
		{
			if ($subscriber->name != '')
			{
				$subscriber->registered_by	= $subscriber->name;
				if ($subscriber->firstname != '')
				{
					$subscriber->registered_by	.= ", " . $subscriber->firstname;
				}
			}
			else
			{
				$subscriber->registered_by = "User";
			}
		}
		else
		{
			$db = Factory::getContainer()->get(DatabaseInterface::class);

			$query_reg	= $db->getQuery(true);
			$query_reg->select('name');
			$query_reg->from($db->quoteName('#__users'));
			$query_reg->where($db->quoteName('id') . ' = ' . (int) $subscriber->registered_by);

			try
			{
				$db->setQuery($query_reg);

				$subscriber->registered_by = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Method to create the confirmed_by value
	 *
	 * @param object $subscriber
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since    2.4.0
	 */
	public static function createSubscriberConfirmedBy(object $subscriber): void
	{
		if ($subscriber->confirmed_by == 0)
		{
			if ($subscriber->name != '')
			{
				$subscriber->confirmed_by	= $subscriber->name;
				if ($subscriber->firstname != '')
				{
					$subscriber->confirmed_by	.= ", " . $subscriber->firstname;
				}
			}
			else
			{
				$subscriber->confirmed_by = "User";
			}
		}
		else
		{
			$db = Factory::getContainer()->get(DatabaseInterface::class);

			$query_conf	= $db->getQuery(true);
			$query_conf->select('name');
			$query_conf->from($db->quoteName('#__users'));
			$query_conf->where($db->quoteName('id') . ' = ' . (int) $subscriber->confirmed_by);

			try
			{
				$db->setQuery($query_conf);

				$subscriber->confirmed_by = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Method to get the fields list for subscriber export
	 *
	 * @return array	export fields list
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	static public function getExportFieldsList(): array
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);

		$query = "SHOW COLUMNS FROM {$db->quoteName('#__bwpostman_subscribers')}
			WHERE {$db->quoteName('Field')} NOT IN (
				{$db->quote('activation')},
				{$db->quote('editlink')},
				{$db->quote('checked_out')},
				{$db->quote('checked_out_time')})";

		try
		{
			$db->setQuery($query);

			$columns = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return array();
		}

		return $columns;
	}

	/**
	 * Method check, if entered values contains links and mail address is valid
	 * Checking mail address is done with two steps:
	 * - First only syntax of entered value is checked. This is done always
	 * - Second mail address is checked, if it is reachable. This is only done, if parameter at options of BwPostman is set
	 *
	 * @param array $data
	 *
	 * @return boolean	false, if link is present or mail address could not be verified
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	static public function checkSubscriberInputFields(array $data): bool
	{
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_bwpostman', true);

		// Check first name
		if (isset($data['firstname']))
		{
			if (BwPostmanFilterHelper::containsLink($data['firstname']))
			{
				$fieldName = Text::_('COM_BWPOSTMAN_FIRSTNAME');

				if ($app->isClient('administrator'))
				{
					$fieldName = Text::_('COM_BWPOSTMAN_SUB_FIRSTNAME');
				}

				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_INVALID_FIELD_CONTENT', $fieldName), 'error');

				return false;
			}
		}

		// Check last name
		if (isset($data['name']))
		{
			if (BwPostmanFilterHelper::containsLink($data['name']))
			{
				$fieldName = Text::_('COM_BWPOSTMAN_NAME');

				if ($app->isClient('administrator'))
				{
					$fieldName = Text::_('COM_BWPOSTMAN_SUB_NAME');
				}

				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_INVALID_FIELD_CONTENT', $fieldName), 'error');

				return false;
			}
		}

		// Check special field
		if (isset($data['special']))
		{
			if (BwPostmanFilterHelper::containsLink($data['special']))
			{
				if ($params->get('special_label', '') != '')
				{
					$fieldName = Text::_($params->get('special_label', ''));
				}
				else
				{
					$fieldName = Text::_('COM_BWPOSTMAN_SPECIAL');
				}

				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_INVALID_FIELD_CONTENT', $fieldName), 'error');

				return false;
			}
		}

		if (isset($data['email']) && $data['email'] != '')
		{
			// Simple check for valid mail address
			if (!MailHelper::isEmailAddress($data['email']))
			{
				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_INVALID_FIELD_CONTENT', Text::_('COM_BWPOSTMAN_EMAIL')), 'error');

				return false;
			}

			// Enhanced check, if mail address is reachable
			if ((int)$params->get('verify_mailaddress', '0') === 1)
			{
				if(!self::validateEmail($data['email']))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ERROR_INVALID_FIELD_CONTENT', Text::_('COM_BWPOSTMAN_EMAIL')), 'error');

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to validate one email address
	 *
	 * @param string $email Subscriber/Test-recipient email address
	 *
	 * @return	boolean  true if email address is valid and reachable
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public static function validateEmail(string $email): bool
	{
		$config     = Factory::getApplication()->getConfig();
		$logOptions = array();

		$validator = new BwEmailValidation($logOptions);

		$validator->setEmailFrom($config->get('mailfrom'));
		$validator->setConnectionTimeout(30);
		$validator->setStreamTimeout(5);
		$validator->setStreamTimeoutWait(0);

		$isValidEmail = $validator->check($email);

//		PluginHelper::importPlugin('system');
//		$dispatcher = EventDispatcher::getInstance();

//		$isValidEmail = $dispatcher->trigger('onSubscriberBeforeSave', $email);


		return $isValidEmail;
	}

	/**
	 * Method to get sender name and mail address from config
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public static function getSender(): array
	{
		$config = Factory::getApplication()->getConfig();
		$params = ComponentHelper::getParams('com_bwpostman');
		$sender = array();

		$sender[0] = MailHelper::cleanAddress($params->get('default_from_email', $config->get('mailfrom')));
		$sender[1] = Text::_($params->get('default_from_name', $config->get('fromname')));

		return $sender;
	}

	/**
	 * Method to get reply to name and mail address from config
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public static function getReplyTo(): array
	{
		$config = Factory::getApplication()->getConfig();
		$params = ComponentHelper::getParams('com_bwpostman');
		$reply = array();

		$reply[0] = MailHelper::cleanAddress($params->get('default_from_email', $config->get('mailfrom')));
		$reply[1] = Text::_($params->get('default_reply_email', $config->get('fromname')));

		return $reply;
	}

	/**
	 * Method to check if an editlink has length of 32 characters and has only hex characters
	 *
	 * @param string $editlink
	 *
	 * @return bool
	 *
	 * @since 4.2.0
	 */
	public static function isValidEditlink(string $editlink = ''): bool
	{
		return strlen($editlink) === 32 && ctype_xdigit($editlink);
	}

	/**
	 * Method to repair a faulty editlink: Fetch new editlink and store it at subscriber
	 *
	 * @param int $subscriberId
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 4.2.0
	 */
	public static function repairEditlink(int $subscriberId = 0): array
	{
		$db              = Factory::getContainer()->get(DatabaseInterface::class);
		$subscriberTable = new SubscriberTable($db);

		$editlink        = $subscriberTable->getEditlink();
		$editlinkUpdated = $subscriberTable->updateEditlink($subscriberId, $editlink);

		return array ($editlink, $editlinkUpdated);
	}
}

