<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main controller for frontend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import CONTROLLER and Helper object class
jimport('joomla.application.component.controller');

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;

// Require component helper classes and exception class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/subscriberhelper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/exceptions/BwException.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/subscriberhelper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/subscriber.php');


/**
 * Class BwPostmanControllerRegister
 *
 * @since       2.0.0
 */
class BwPostmanControllerRegister extends JControllerLegacy
{

	/**
	 * Subscriber ID
	 *
	 * @var integer
	 *
	 * @since       2.0.0
	 */
	protected $subscriberid;

	/**
	 * User ID in subscriber-table
	 *
	 * @var integer
	 *
	 * @since       2.0.0
	 */
	protected $userid;

	/**
	 * Constructor
	 * Checks the session variables and deletes them if necessary
	 * Sets the userid and subscriberid
	 * Checks if something is wrong with the subscriber-data (not activated/blocked)
	 *
	 * @since       2.0.0
	 */
	public function __construct()
	{
		parent::__construct();

		$app    = Factory::getApplication();
		$user     = Factory::getUser();
		$userId = (int) $user->get('id');

		$subscriberId = 0;

		// Check if user is logged (subscriber id = 0 means the user has no newsletter subscription)
		if ($userId)
		{
			$subsTable    = $this->getModel('subscriber', 'Administrator')->getTable('Subscriber');
			$subscriberId = $subsTable->getSubscriberIdByUserId($userId);
		}
		// Check if user is in edit mode
		else
		{
			$session  = $app->getSession();
			$subsData = $session->get('session_subscriberid', null);

			if (is_array($subsData))
			{
				$subscriberId = $subsData['id'];
			}
		}

		// if user is subscriber, redirect to edit subscription
		if ($subscriberId)
		{
			$itemid = BwPostmanSubscriberHelper::getMenuItemid('edit');
			$route  = Route::_('index.php?option=com_bwpostman&view=edit&itemid=' . $itemid, false);

			$this->setRedirect($route);
		}
}

	/**
	 * Display
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	boolean		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return void
	 *
	 * @since       2.0.0
	 *
	 * @throws Exception
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	/**
	 * Method to save the registration
	 *
	 * @author Romana Boldt
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function save()
	{
		$jinput	= Factory::getApplication()->input;
		$app	= Factory::getApplication();

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$model		= $this->getModel('register');
		$session	= Factory::getSession();

		// process input data, which will be stored in state
		$post	= $jinput->getArray(
			array(
				'agreecheck_mod' => 'string',
				'agreecheck' => 'string',
				'a_emailformat' => 'uint',
				'emailformat' => 'uint',
				'a_firstname' => 'string',
				'firstname' => 'string',
				'a_name' => 'string',
				'name' => 'string',
				'a_gender' => 'string',
				'gender' => 'string',
				'a_special' => 'string',
				'special' => 'string',
				'email' => 'string',
				'falle' => 'string',
				'language' => 'string',
				'mailinglists' => 'array',
				'registration_ip' => 'string',
				'stringQuestion' => 'string',
				'stringCaptcha' => 'string',
				'codeCaptcha' => 'string',
				'bwp-' . BwPostmanHelper::getCaptcha(1) => 'string',
				'bwp-' . BwPostmanHelper::getCaptcha(2) => 'string',
				'task' => 'string',
				'mod_id' => 'string'
			)
		);

		if (isset($post['a_firstname']))
		{
			if ($post['a_firstname'] === Text::_('COM_BWPOSTMAN_FIRSTNAME'))
			{
				$post['firstname']	= '';
			}
			else
			{
				$post['firstname']	= $post['a_firstname'];
			}

			unset($post['a_firstname']);
		}

		if (isset($post['a_name']))
		{
			if ($post['a_name'] === Text::_('COM_BWPOSTMAN_NAME'))
			{
				$post['name']	= '';
			}
			else
			{
				$post['name']	= $post['a_name'];
			}

			unset($post['a_name']);
		}

		if (isset($post['a_gender']))
		{
			$post['gender']	= $post['a_gender'];
			unset($post['a_gender']);
		}

		if (isset($post['a_special']))
		{
			$post['special']	= $post['a_special'];
			unset($post['a_special']);
		}

		if (isset($post['a_emailformat']))
		{
			$post['emailformat']	= $post['a_emailformat'];
			unset($post['a_emailformat']);
		}

		if (isset($post['agreecheck_mod']))
		{
			$post['agreecheck']	= $post['agreecheck_mod'];
			unset($post['agreecheck_mod']);
		}

		// save input data in state
		$app->setUserState('com_bwpostman.subscriber.register.data', $post);

		// Subscriber is guest
		if (!$this->userid)
		{
			// Check if the email address from the registration form is stored in user table and gives back the id
			$post['user_id'] = $model->isRegUser($post['email']);
			// Subscriber is user
		}
		else
		{
			$post['user_id'] = (int)$this->userid;
		}

		// process input data, which will *not* be stored in state
		$date = Factory::getDate();
		$time = $date->toSql();

		$post['status'] 			= 0;
		$post['registration_date'] 	= $time;
		$post['registered_by'] 		= 0;
		$post['confirmed_by'] 		= '-1';
		$post['archived_by'] 		= '-1';

		if (!$model->save($post))
		{
			// process failed save
			$subscriber_data = array(
				'gender' => $post['gender'],
				'name' => $post['name'],
				'firstname' => $post['firstname'],
				'special' => $post['special'],
				'email' => $post['email'],
				'emailformat' => $post['emailformat'],
				'mailinglists' => $post['mailinglists']
			);
			$session->set('subscriber_data', $subscriber_data);

			$err = $app->getUserState('com_bwpostman.subscriber.register.error', null);

			if (is_array($err))
			{
				$err	= ArrayHelper::toObject($err);
				BwPostmanSubscriberHelper::errorSubscriberData($err, $post['user_id'], $post['email']);
			}
		}
		else
		{
			$subscriber				= new stdClass();
			$subscriber->name 		= $post['name'];
			$subscriber->firstname	= $post['firstname'];
			$subscriber->email 		= $post['email'];
			$subscriber->activation = $app->getUserState('com_bwpostman.subscriber.activation', '');

			$type	= 0; // Send Registration email
			$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

			// Send registration confirmation mail
			$res = BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

			if ($res === true)
			{ // Email has been sent
				$msg = 'COM_BWPOSTMAN_SUCCESS_ACCOUNTREGISTRATION';
				BwPostmanSubscriberHelper::success($msg);
			}
			else
			{ // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_REGISTRATIONEMAIL';
				BwPostmanSubscriberHelper::errorSendingEmail($err_msg, $post['email']);
			}
		}

		parent::display();
	}

	/**
	 * Method to activate an account via the activation link
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function activate()
	{
		// Initialize variables
		$jinput	= Factory::getApplication()->input;

		// Do we have an activation string?
		$activation		= $jinput->getAlnum('subscriber', '');
		$activation		= Factory::getDbo()->escape($activation);
		$activation_ip	= Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
		$params 		= ComponentHelper::getParams('com_bwpostman');
		$send_mail		= $params->get('activation_to_webmaster', '0');

		// No activation string
		if (empty($activation))
		{
			$err_msg = 'COM_BWPOSTMAN_ERROR_WRONGACTIVATIONCODE';
			BwPostmanSubscriberHelper::errorActivationCode($err_msg);
		}
		else
		{
			$model = $this->getModel('register');

			// An error occurred while activate the subscriber account
			$err_msg    = '';
			$editlink   = '';
			$subscriber_id = $model->activateSubscriber($activation, $err_msg, $editlink, $activation_ip);
			if ($subscriber_id === false)
			{
				BwPostmanSubscriberHelper::errorActivationCode($err_msg);
				// Everything is okay, account has been activated
			}
			else
			{
				// Show a forwarding link to edit the subscriber account
				// --> a guest needs the editlink, a user not
				// --> we also need the menu item ID if we want to get right menu item when calling the forward link
				$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');
				$success_msg = 'COM_BWPOSTMAN_SUCCESS_ACCOUNTACTIVATION';
				BwPostmanSubscriberHelper::success($success_msg, $editlink, $itemid);
				if ($send_mail)
				{
					$model->sendActivationNotification($subscriber_id);
				}
			}
		}

		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to send the activation link
	 * --> is needed if someone forgot the activation link
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function sendActivation()
	{
		$jinput	    = Factory::getApplication()->input;
		$subs_id    = null;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Get required system objects
		$model			= $this->getModel('register');
		$err			= new stdClass();
		$err->err_code	= 0;
		$post			= $jinput->getArray(
			array(
				'email' => 'string',
				'id' => 'uint',
				'task' => 'string',
				'language' => 'string',
				'option' => 'string'
			)
		);

		$id	= $post['id'];

		if (array_key_exists('email', $post))
		{
			if ($post['email'] !== null)
			{
				$id = $model->isRegSubscriber($post['email']);
			}
		}

		require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/subscriber.php');
		$subsTable    = $this->getModel('subscriber')->getTable('Subscribers');
		$subscriberdata = $subsTable->getSubscriberState($id);

		if (!is_object($subscriberdata))
		{
			$subs_id		= null;
			$err->err_id    = null;
			$err->err_code	= 408; // Email address does not exist
			$err->err_msg	= Text::sprintf(
				'COM_BWPOSTMAN_ERROR_EMAILDOESNTEXIST',
				$post['email'],
				Route::_('index.php?option=com_bwpostman&view=register')
			);
		}
		elseif ((int)$subscriberdata->archive_flag === 1)
		{
			$subs_id		= (int)$subscriberdata->id;
			$err->err_id    = (int)$subscriberdata->id;
			$err->err_code	= 405; // Email address exists but is blocked
			$err->err_msg	= 'COM_BWPOSTMAN_ERROR_ACCOUNTBLOCKED';
		}

		if ($err->err_code != 0)
		{
			BwPostmanSubscriberHelper::errorSubscriberData($err, $subs_id, $post['email']);
		}
		else
		{ // Everything is okay
			$subscriber				= new stdClass();
			$subscriber->name 		= $subscriberdata->name;
			$subscriber->firstname	= $subscriberdata->firstname;
			$subscriber->email 		= $subscriberdata->email;
			$subscriber->activation = $subscriberdata->activation;

			$type	= 2; // Send Activation reminder
			$itemid	= BwPostmanSubscriberHelper::getMenuItemid('register');
			$res	= BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

			if ($res === true)
			{// Email has been sent
				$success_msg 	= 'COM_BWPOSTMAN_SUCCESS_ACTIVATIONEMAIL';
				BwPostmanSubscriberHelper::success($success_msg, $subscriberdata->editlink, $itemid);
			}
			else
			{ // Email has not been sent
				$err_msg 	= 'COM_BWPOSTMAN_ERROR_ACTIVATIONEMAIL';
				BwPostmanSubscriberHelper::errorSendingEmail($err_msg, $subscriber->email);
			}
		}

		$jinput->set('view', 'register');
		parent::display();
	}

	/**
	 * Method to show a captcha
	 *
	 * @since	1.0.1
	 */
	public function showCaptcha()
	{
		BwPostmanHelper::showCaptcha();
	}
}
