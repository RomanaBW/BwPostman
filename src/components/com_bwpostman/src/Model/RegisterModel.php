<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register model for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

namespace BoldtWebservice\Component\BwPostman\Site\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Log\LogEntry;
use Joomla\CMS\Mail\Exception\MailDisabledException;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Mail\MailHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class BwPostmanModelRegister
 *
 * @since       0.9.1
 */
class RegisterModel extends AdminModel
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	Table	A database object
	 *
	 * @throws Exception
	 *
	 * @since  1.0.1
	 */
	public function getTable($name = 'Subscriber', $prefix = 'Administrator', $options = array()): Table
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function populateState()
	{
		$jinput	= Factory::getApplication()->input;

		// Load state from the request.
		$pk = $jinput->getInt('id');
		$this->setState('subscriber.id', $pk);

		$offset = $jinput->getUint('limitstart');
		$this->setState('list.offset', $offset);

		// TODO: Tune these values based on other permissions.
		$user		= Factory::getApplication()->getIdentity();
		if ((!$user->authorise('core.edit.state', 'com_bwpostman')) &&  (!$user->authorise('core.edit', 'com_bwpostman')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		$this->setState('filter.language', Multilanguage::isEnabled());
	}

	/**
	 * Method to get the record form.
	 *
	 * @param array   $data     Data for the form.
	 * @param boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    false|Form    A JForm object on success, false on failure
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_bwpostman.subscriber', 'subscriber', array('control' => 'jform', 'load_data' => $loadData));

		// @ToDo: $this->loadForm throws RuntimeException, if form or file not found => there is never an empty form
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to check by an input email address if a user has a newsletter account (user = no guest)
	 *
	 * @param string $email user email
	 *
	 * @return    int     $uid    user ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function isRegUser(string $email): int
	{
		$uid = BwPostmanSubscriberHelper::getJoomlaUserIdByEmail($email);

		if ($uid == null)
		{
			$uid = 0;
		}

		return $uid;
	}

	/**
	 * Method to check if an email address exists in the subscribers-table
	 *
	 * @param string $email subscriber email
	 *
	 * @return 	int     $id     subscriber ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function isRegSubscriber(string $email): int
	{
		return $this->getTable()->getSubscriberIdByEmail($email);
	}

	/**
	 * Method to save the subscriber data into the subscribers-table
	 * Sets editlink and activation code and checks if the data are valid
	 *
	 * @param 	array   $data       associative array of data to store
	 *
	 * @return 	Boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function save($data): bool
	{
		$app	= Factory::getApplication();

		// Check input values
		if (!BwPostmanSubscriberHelper::checkSubscriberInputFields($data))
		{
			return false;
		}

		// Create the editlink and check if the string doesn't exist twice or more
		$subsTable = $this->getTable();

		$data['editlink'] = $subsTable->getEditlink();

		// Create the activation and check if the string doesn't exist twice or more
		$data['activation'] = $subsTable->createActivation();
		$app->setUserState('com_bwpostman.subscriber.activation', $data['activation']);

		if (parent::save($data))
		{
			// Get the subscriber id
			$subscriber_id	= $app->getUserState('com_bwpostman.subscriber.id', 0);

			if (isset($data['mailinglists']))
			{
				if ($data['mailinglists'] != '')
				{
					$subsMlTable = $this->getTable('SubscribersMailinglists');
					$subsMlTable->storeMailinglistsOfSubscriber($subscriber_id, $data['mailinglists']);
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete a subscriber and the subscribed mailinglists
	 * --> is also called from the store method if a email is registered but archived by the user himself
	 *
	 * @param 	int     $pks        subscriber ID
	 *
	 * @return 	Boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks = null): bool
	{
		$params 	= ComponentHelper::getParams('com_bwpostman');
		$send_mail	= $params->get('deactivation_to_webmaster', '0s');
		$subscriber = null;
		$subsTable = $this->getTable();

		if ($pks)
		{
			if ($send_mail)
			{
				$subscriber = $subsTable->getSingleSubscriberData((int)$pks);
			}

			// delete subscriber from subscribers table
			try
			{
				$subsTable->delete((int)$pks);

				// delete subscriber entries from subscribers-lists table
				$subsMlTable = $this->getTable('SubscribersMailinglists');
				$subsMlTable->deleteMailinglistsOfSubscriber((int)$pks);
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_DELETE_MAILINGLISTS'), 'warning');
				return false;
			}
		}

		if (is_object($subscriber))
		{
			$this->sendDeactivationNotification($subscriber);
		}

		return true;
	}

	/**
	 * Method to activate the newsletter account of a subscriber
	 *
	 * @param string $activation    activation code for the newsletter account
	 * @param string $ret_err_msg   error message
	 * @param string $ret_editlink  editlink for editing the subscriber data
	 * @param string $activation_ip IP used for activation
	 *
	 * @return 	integer|Boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function activateSubscriber(string $activation, string &$ret_err_msg, string &$ret_editlink, string $activation_ip)
	{
		$subsTable  = $this->getTable();
		$subscriber = $subsTable->getSubscriberActivationData($activation);

		if (isset($subscriber->editlink))
		{
			$ret_editlink = $subscriber->editlink;
		}

		if (isset($subscriber->id))
		{
			$id = (int)$subscriber->id;
		}

		// Is it a valid user to activate?
		if (!empty($id))
		{
			$subsTable->storeSubscriberActivation($id, $activation_ip);
		}
		else
		{
			// The activation code does not exist in the db
			$ret_err_msg = 'COM_BWPOSTMAN_ERROR_WRONGACTIVATIONCODE_2';
			return false;
		}

		return $id;
	}

	/**
	 * Method to unsubscribe
	 * --> the subscriber data will be deleted
	 *
	 * @param string $editlink
	 * @param string $email
	 * @param string $ret_err_msg error message
	 *
	 * @return 	Boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function unsubscribe(string $editlink, string $email, string &$ret_err_msg): bool
	{
		$id = $this->getTable()->validateSubscriberEditlink($email, $editlink);

		if ($id)
		{
			if ($this->delete($id))
			{
				return true;
			}
			else
			{
				$ret_err_msg = 'COM_BWPOSTMAN_ERROR_UNSUBSCRIBE';
				return false;
			}
		}
		else
		{
			$ret_err_msg = 'COM_BWPOSTMAN_ERROR_WRONGUNSUBCRIBECODE';
			return false;
		}
	}

	/**
	 * Method to send an information to webmaster, when a subscriber delete the account
	 *
	 * @param object $subscriber subscriber
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since       2.0.3
	 */
	public function sendDeactivationNotification(object $subscriber)
	{
		// set subject
		$subject = Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION');

		// Set body
		$body	= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT');
		$body	.= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT_NAME') . $subscriber->name . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT_FIRSTNAME') . $subscriber->firstname . "\n\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT_EMAIL') . $subscriber->email . "\n\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT_REGISTRATION_DATE') . $subscriber->registration_date . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_DEACTIVATION_TEXT_CONFIRMATION_DATE') . $subscriber->confirmation_date . "\n";

		try
		{
			$mailer = $this->setNotificationAddresses('deactivation');

			$mailer->setSubject($subject);
			$mailer->setBody($body);

			// Send the email
			$mailer->Send();
		}
		catch (UnexpectedValueException | MailDisabledException | \PHPMailer\PHPMailer\Exception $exception)
		{
			$logOptions = array();
			$logger     = BwLogger::getInstance($logOptions);
			$message    = $exception->getMessage();

			$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'deactivation'));
		}
	}

	/**
	 * Method to send an information to webmaster, when a new subscriber activated the account
	 *
	 * @param int $subscriber_id subscriber id
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function sendActivationNotification(int $subscriber_id)
	{
		// set subject
		$subject = Text::_('COM_BWPOSTMAN_NEW_ACTIVATION');

		// get body-data for mail and set body
		$subscriber = $this->getTable()->getSingleSubscriberData($subscriber_id);

		// Set registered by name
		BwPostmanSubscriberHelper::createSubscriberRegisteredBy($subscriber);

		// Set confirmed by name
		BwPostmanSubscriberHelper::createSubscriberConfirmedBy($subscriber);

		// Set body
		$body	= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT');
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_NAME') . $subscriber->name . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_FIRSTNAME') . $subscriber->firstname . "\n\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_EMAIL') . $subscriber->email . "\n\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_DATE') . $subscriber->registration_date . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_IP') . $subscriber->registration_ip . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_REGISTRATION_BY') . $subscriber->registered_by . "\n\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_DATE') . $subscriber->confirmation_date . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_IP') . $subscriber->confirmation_ip . "\n";
		$body	.= Text::_('COM_BWPOSTMAN_NEW_ACTIVATION_TEXT_CONFIRMATION_BY') . $subscriber->confirmed_by . "\n";

		try
		{
			$mailer = $this->setNotificationAddresses();
			$mailer->setSubject($subject);
			$mailer->setBody($body);

			// Send the email
			$mailer->Send();
		}
		catch (UnexpectedValueException | MailDisabledException | \PHPMailer\PHPMailer\Exception $exception)
		{
			$logOptions = array();
			$logger     = BwLogger::getInstance($logOptions);
			$message    = $exception->getMessage();

			$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'activation'));
		}
	}

	/**
	 * Method to set the sender, the reply to and the recipient for activation notification mail
	 *
	 * @param string $mode activation or deactivation of subscription
	 *
	 * @return object   $mailer  The mailer object
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	private function setNotificationAddresses(string $mode = 'activation'): object
	{
		$mailer	    = Factory::getMailer();
		$params     = ComponentHelper::getParams('com_bwpostman');

		// set sender and reply-to
		$sender = BwPostmanSubscriberHelper::getSender();
		$reply  = BwPostmanSubscriberHelper::getReplyTo();

		$mailer->setSender($sender);
		$mailer->addReplyTo($reply);

		// set recipient
		$recipient_mail = MailHelper::cleanAddress($params->get('activation_to_webmaster_email', ''));
		$recipient_name	= Text::_($params->get('activation_from_name', ''));

		if ($mode === 'deactivation')
		{
			$recipient_mail = MailHelper::cleanAddress($params->get('deactivation_to_webmaster_email', ''));
			$recipient_name	= Text::_($params->get('deactivation_from_name', ''));
		}

		if (!is_string($recipient_mail))
		{
			$recipient_mail = $sender[0];
		}

		if (!is_string($recipient_name))
		{
			$recipient_name = $sender[1];
		}

		$mailer->addRecipient($recipient_mail, $recipient_name);

		return $mailer;
	}
}
