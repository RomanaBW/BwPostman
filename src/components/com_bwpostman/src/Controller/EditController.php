<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit controller for frontend.
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

namespace BoldtWebservice\Component\BwPostman\Site\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use stdClass;

/**
 * Class BwPostmanControllerEdit
 *
 * @since   2.0.0
 */
class EditController extends FormController
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
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$this->factory = $app->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('sendEditLink', 'sendEditLink');
		$session = $app->getSession();

		//clear session error and success
		$session_error = $session->get('session_error');
		if(isset($session_error) && is_array($session_error))
		{
			$session->clear('session_error');
		}

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

		$user 	        = $app->getIdentity();
		$user_is_guest  = $user->get('guest');
		$userid 		= (int) $user->get('id');
		$subsTable      = $this->getModel('subscriber', 'Administrator')->getTable('Subscriber');

		// if user is logged in fetch subscriber id (subscriber id = 0 means the user has no newsletter subscription)
		if ($userid)
		{
			$subscriberid = $subsTable->getSubscriberIdByUserId($userid);
		}

		// Check if the variable editlink exists in the uri
		$uri		= Uri::getInstance();
		$editlink	= $uri->getVar("editlink");

		// Get subscriber id from session, clear session if necessary
		$session_subscriberid = $session->get('session_subscriberid');

		if(isset($session_subscriberid) && is_array($session_subscriberid))
		{
			if ($user_is_guest)
			{
				if (!empty($editlink))
				{
					if ($model->checkEditlink($editlink) === (int)$session_subscriberid['id'])
					{
						$subscriberid = (int)$session_subscriberid['id'];
					}
					else
					{
						$session->clear('session_subscriberid');
					}
				}
				elseif (is_null($editlink))
				{
					$subscriberid = (int)$session_subscriberid['id'];
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
			$subscriberdata	= $subsTable->getSubscriberState((int) $subscriberid);

			if (is_object($subscriberdata))
			{
				if ($user_is_guest)
				{
					$userid = (int) $subscriberdata->user_id;
				}

				if (!$this->checkActiveSubscription($subscriberdata, $err))
				{
					BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
				}
			}
		}
		else
		{ // Guest with unknown subscriber id (not stored in the session)
			$link = Uri::base() . 'index.php?option=com_bwpostman&view=register&layout=error_geteditlink';

			if (is_null($editlink))
			{
			}
			elseif (empty($editlink))
			{
				BwPostmanSubscriberHelper::errorEditlink();
				$this->setRedirect($link);
			}
			else
			{
				$subscriberid	= (int) $model->checkEditlink($editlink);

				if (!$subscriberid)
				{
					BwPostmanSubscriberHelper::errorEditlink();
					$this->setRedirect($link);
				}
				else
				{
					$subscriberdata	= $subsTable->getSubscriberState($subscriberid);

					if (!$this->checkActiveSubscription($subscriberdata, $err))
					{
						BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
						parent::display();
					}
					else
					{
						$itemid	= (int) BwPostmanSubscriberHelper::getMenuItemid('edit'); // Itemid from edit-view

						$link   = BwPostmanSubscriberHelper::loginGuest($subscriberid, $itemid);
						$this->setRedirect($link, false);
					}
				}
			}
		}

		$this->setData((int) $subscriberid, $userid);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param string $name   The name of the model.
	 * @param string $prefix The prefix for the PHP class name.
	 * @param array  $config An optional associative array of configuration settings.
	 *
	 * @return BaseDatabaseModel
	 *
	 * @throws Exception
	 *
	 * @since    4.0.0
	 */
	public function getModel($name = 'Edit', $prefix = 'Site', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method to reset the subscriber ID and userid
	 *
	 * @param int $subscriberid subscriber ID
	 * @param int $userid       user ID
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setData(int $subscriberid = 0, int $userid = 0)
	{
		$app	= Factory::getApplication();
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
		$app     = Factory::getApplication();
		$jinput	 = $app->input;
		$session = $app->getSession();

		$session_error = $session->get('session_error');

		if(!(isset($session_error) && is_array($session_error)))
		{
			if (($this->userid) && ($this->subscriberid))
			{
				$jinput->set('view', 'edit');
			}
			elseif (($this->userid) && (!$this->subscriberid))
			{
				$jinput->set('view', 'register');
			}
			elseif ((!$this->userid) && ($this->subscriberid))
			{
				$jinput->set('view', 'edit');
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
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function save($key = null, $urlVar = null)
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$post	= $jinput->getArray(
			array(
				'edit' => 'string',
				'email' => 'string',
				'emailformat' => 'uint',
				'firstname' => 'string',
				'firstname_field_obligation' => 'uint',
				'gender' => 'uint',
				'special' => 'string',
				'id' => 'uint',
				'language' => 'string',
				'mailinglists' => 'array',
				'name' => 'string',
				'name_field_obligation' => 'uint',
				'task' => 'string',
				'option' => 'string',
				'unsubscribe' => 'uint'
			)
		);

		// @ToDo: Consider about correct action. Shouldn't we cancel? As a placeholder until then we only show a warning.
		// Correct action is necessary because we send the confirmation mail to this mail address, if it has changed.
		// Probably a full check (see import subscribers) is the better way…
		if (!MailHelper::isEmailAddress($post['email']))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_WARNING_FAULTY_MAIL_ADDRESS', $post['email']), 'warning');
		}

		$newEmail  = false;
		$subsTable = $this->getModel('subscriber', 'Administrator')->getTable('Subscriber');

		if (isset($post['unsubscribe']))
		{
			$this->unsubscribe($post['id']);
			$link = Uri::base() . 'index.php?option=com_bwpostman&view=register';
		}
		else
		{
			$model  = $this->getModel('edit');
			$itemid = BwPostmanSubscriberHelper::getMenuItemid('edit');
			$link   = Uri::base() . 'index.php?option=com_bwpostman&view=edit&Itemid=' . $itemid;

			// Email address has changed
			if (($post['email'] !== "") && ($post['email'] !== $model->getEmailaddress($post['id'])))
			{
				$newEmail					= true;
				$post['status'] 			= 0;
				$post['confirmation_date'] 	= $model->getDbo()->getNullDate();
				$post['confirmed_by'] 		= -1;
				$post['activation']			= $subsTable->createActivation();
			}

			// Store the data if possible
			if (!$model->save($post))
			{
				// Store the input data into the session object
				$session			= $app->getSession();
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

					$session				= $app->getSession();
					$session_subscriberid	= $session->get('session_subscriberid');

					if(isset($session_subscriberid) && is_array($session_subscriberid))
					{
						$session->clear('session_subscriberid');
					}

					$jinput->set('view', 'register');
					$link   = Uri::base() . 'index.php?option=com_bwpostman&view=register';
				}
				else
				{
					// No new email address has been stored --> the account doesn't need to be confirmed again
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_CHANGES_SAVED_SUCCESSFULLY', 'message'));

					// If the user has chosen the button "save modifications & leave edit mode" we clear the session object
					// now no subscriber_id is stored into the session
					if ($post['edit'] == "submitleave")
					{
						$session				= $app->getSession();
						$session_subscriberid	= $session->get('session_subscriberid');

						if(isset($session_subscriberid) && is_array($session_subscriberid))
						{
							$session->clear('session_subscriberid');
						}

						$jinput->set('view', 'register');
						$app->setUserState('subscriber.id', 0);
						$link   = Uri::base() . 'index.php?option=com_bwpostman&view=register';
					}
					else
					{
						$uid	= (int)$subsTable->getUserIdOfSubscriber($post['id']);
						$this->setData($post['id'], $uid);

						$app->setUserState('subscriber.id', $post['id']);
						$jinput->set('view', 'edit');
					}
				}
			}
		}
		$this->setRedirect($link);

		parent::display();
	}

	/**
	 * Method to unsubscribe
	 * --> through an unsubscribe-link
	 * --> through the edit view
	 *
	 * @param int $id Subscriber ID
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function unsubscribe(int $id = 0)
	{
		// Initialize some variables
		$app    = Factory::getApplication();
		$jinput	= $app->input;
		$model	= $this->getModel('register');
		$itemid	= BwPostmanSubscriberHelper::getMenuItemid('register');

		// Check if the variable editlink exists in the uri
		$uri		= Uri::getInstance();
		$email		= $uri->getVar("email");
		$editlink	= $uri->getVar("code");

		// We come from the edit view
		if ($id)
		{
			$subsTable       = $this->getModel('subscriber', 'Administrator')->getTable('Subscriber');
			$unsubscribedata = $subsTable->getSubscriberState($id);
			$email           = $unsubscribedata->email;
			$editlink        = $unsubscribedata->editlink;

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
		$session				= $app->getSession();
		$session_subscriberid	= $session->get('session_subscriberid');

		if(isset($session_subscriberid) && is_array($session_subscriberid))
		{
			$session->clear('session_subscriberid');
		}

		$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUCCESS_UNSUBSCRIBE'), $msg_type);
		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to send the editlink
	 * --> is needed to get access to the edit form
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function sendEditlink()
	{
		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app    = Factory::getApplication();
		$jinput	= $app->input;
		$model	= $this->getModel('register');
		$post	= $jinput->getArray(
			array(
				'email' => 'string',
				'language' => 'string',
				'task' => 'string',
				'option' => 'string'
			)
		);

		// @ToDo: Consider about correct action. Shouldn't we cancel? As a placeholder until then we only show a warning.
		// Correct action is necessary because we send the confirmation mail to this mail address, if it has changed.
		// Probably a full check (see import subscribers) is the better way…
		if (!MailHelper::isEmailAddress($post['email']))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_WARNING_FAULTY_MAIL_ADDRESS', $post['email']), 'warning');
		}

		$id	            = $model->isRegSubscriber($post['email']);
		$err            = new stdClass();
		$err->err_code  = 0;
		$subs_id        = null;
		$subscriber     = new stdClass();
		$subsModel      = $this->getModel('subscriber', 'Administrator');
		$subsTable      = $subsModel->getTable('Subscriber');
		$subscriberdata = $subsTable->getSubscriberState($id);

		if (!is_object($subscriberdata))
		{
			$subs_id       = null;
			$err->err_id   = $id;
			$err->err_code = 408; // Email address doesn't exist
			$err->err_msg  = Text::sprintf(
				'COM_BWPOSTMAN_ERROR_EMAILDOESNTEXIST',
				$post['email'],
				Route::_('index.php?option=com_bwpostman&view=register')
			);
		}
		elseif ((int)$subscriberdata->archive_flag === 1)
		{
			$subs_id		= $subscriberdata->id;
			$err->err_id    = $id;
			$err->err_code	= 405; // Email address exists but is blocked
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
		}
		elseif ((int)$subscriberdata->status === 0)
		{
			$subs_id		= $subscriberdata->id;
			$err->err_id    = $id;
			$err->err_code	= 406; // Email address exists but account is not activated
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
		}

		if ($err->err_code !== 0)
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
			$itemid	= BwPostmanSubscriberHelper::getMenuItemid('edit');
			$res	= BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

			if ($res === true)
			{ // Email has been sent
				$success_msg 	= 'COM_BWPOSTMAN_SUCCESS_EMAILEDITLINK';
				BwPostmanSubscriberHelper::success($success_msg, '', $itemid);	// We need no editlink or itemid for the output in this layout
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
	 * @param object $subscriberdata
	 * @param object $err
	 *
	 * @return boolean  $result
	 *
	 * @since   2.0.0
	 */
	protected function checkActiveSubscription(object $subscriberdata, object &$err): bool
	{
		$result = true;

		// The error code numbers are the same like in the subscribers-table check function
		if ((int)$subscriberdata->archive_flag === 1)
		{
			$err->id       = $subscriberdata->id;
			$err->err_code = 405;
			$err->err_msg  = 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
			$result = false;
		}
		elseif ((int)$subscriberdata->status === 0)
		{
			$err->id       = $subscriberdata->id;
			$err->err_code = 406;
			$err->err_msg  = 'COM_BWPOSTMAN_ERROR_ACCOUNTNOTACTIVATED';
			$result = false;
		}

		return $result;
	}
}
