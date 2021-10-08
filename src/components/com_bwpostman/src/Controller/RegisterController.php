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

namespace BoldtWebservice\Component\BwPostman\Site\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use stdClass;

/**
 * Class BwPostmanControllerRegister
 *
 * @since       2.0.0
 */
class RegisterController extends FormController
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
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function __construct($config = array())
	{
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);
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
	public function getModel($name = 'register', $prefix = 'Site', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method to save the registration
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
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

		$model   = $this->getModel();
		$session = $app->getSession();

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
				'bwp-' . BwPostmanHelper::getCaptcha() => 'string',
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
			$post['user_id'] = $this->userid;
		}

		// process input data, which will *not* be stored in state
		$date = Factory::getDate();
		$time = $date->toSql();

		$post['status'] 			= 0;
		$post['registration_date'] 	= $time;
		$post['registered_by'] 		= 0;
		$post['confirmed_by'] 		= '-1';
		$post['archived_by'] 		= '-1';

		$itemid       = BwPostmanSubscriberHelper::getMenuItemid('register');
		$menuItemPath = BwPostmanSubscriberHelper::getMenuItemPath($itemid);

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

			$err = $app->getUserState('com_bwpostman.subscriber.register.error');

			if (is_array($err))
			{
				$err	= ArrayHelper::toObject($err);
				BwPostmanSubscriberHelper::errorSubscriberData($err, $post['user_id'], $post['email']);
			}

			$itemPath = '';

			if ($itemid > 0)
			{
				$itemPath = '&Itemid=' . $itemid;
			}

			$route = Route::_('index.php?option=com_bwpostman&view=register' . $itemPath, false);

			if ($menuItemPath!== '' && ($app->get('sef') === '1' || $app->get('sef') === true))
			{
				$route = '/index.php/' . $menuItemPath . '?view=register';
			}

			$this->setRedirect($route);
			$this->redirect();

			return false;
		}
		else
		{
			$subscriber				= new stdClass();
			$subscriber->name 		= $post['name'];
			$subscriber->firstname	= $post['firstname'];
			$subscriber->email 		= $post['email'];
			$subscriber->activation = $app->getUserState('com_bwpostman.subscriber.activation', '');

			$type	= 0; // Send Registration email

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

		return true;
	}

	/**
	 * Method to activate an account via the activation link
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function activate()
	{
		// Initialize variables
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Do we have an activation string?
		$activation		= $jinput->getAlnum('subscriber', '');
		$activation		= $this->getModel()->getDbo()->escape($activation);
		$activation_ip	= $jinput->server->get('REMOTE_ADDR', '', '');
		$params 		= ComponentHelper::getParams('com_bwpostman');
		$send_mail		= $params->get('activation_to_webmaster');

		// No activation string
		if (empty($activation))
		{
			$err_msg = 'COM_BWPOSTMAN_ERROR_WRONGACTIVATIONCODE';
			BwPostmanSubscriberHelper::errorActivationCode($err_msg);
		}
		else
		{
			$model = $this->getModel();

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
	 * @return void
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
		$model			= $this->getModel();
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

		$subsTable      = $this->getModel('subscriber', 'Administrator')->getTable('Subscriber');
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
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function showCaptcha()
	{
		BwPostmanHelper::showCaptcha();
	}
}
