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

// Require component admin helper class and exception class
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/libraries/exceptions/BwException.php');
require_once (JPATH_COMPONENT . '/helpers/subscriberhelper.php');


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
	 * Checks the session variables and deletes them if necessary
	 * Sets the userid and subscriberid
	 * Checks if something is wrong with the subscriber-data (not activated/blocked)
	 */
	public function __construct()
	{
		parent::__construct();
/*		$jinput	= JFactory::getApplication()->input;

		$view   = $jinput->get('view');

		if (($view != 'newsletters') && ($view != 'newsletter')) {
			$session		= JFactory::getSession();

			//clear session error and success
			// @Todo: is it necessary to check for array?
			$session_error = $session->get('session_error');
			if(isset($session_error) && is_array($session_error)){
				$session->clear('session_error');
			}

			// @Todo: is it necessary to check for array?
			$session_success = $session->get('session_success');
			if(isset($session_success) && is_array($session_success)){
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
			if ($userid)
			{
				$subscriberid	= (int) BwPostmanSubscriberHelper::getSubscriberID($userid); // = 0 if the user no newsletter account
			}


			// Check if the variable editlink exists in the uri
			$uri			= JUri::getInstance();
			$editlink		= $uri->getVar("editlink", null);

			// Get subscriber id from session
			$session_subscriberid = $session->get('session_subscriberid');
			if(isset($session_subscriberid) && is_array($session_subscriberid)) {
				if ($user_is_guest) {
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

			// get subscriber data
			if ($subscriberid) { // Guest with known subscriber id (stored in the session) or logged in user
				$subscriberdata	= BwPostmanSubscriberHelper::getSubscriberData ((int) $subscriberid);
				if (is_object($subscriberdata))
				{
					if ($user_is_guest)
					{
						$userid = (int) $subscriberdata->user_id;
					}
					$active_subscription    = $this->_checkActiveSubscription($subscriberdata, $err);

					if ($active_subscription) {
						BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
					}
				}
			}
			else { // Guest with unknown subscriber id (not stored in the session)
				if (is_null($editlink)) {
				}
				elseif (empty($editlink)) {
					BwPostmanSubscriberHelper::errorEditlink();
				}
				else {
					$subscriberid	= (int) $model->checkEditlink($editlink);

					if (!$subscriberid) {
						BwPostmanSubscriberHelper::errorEditlink();
					}
					else {
						$subscriberdata	= BwPostmanSubscriberHelper::getSubscriberData ((int) $subscriberid);

						$active_subscription    = $this->_checkActiveSubscription($subscriberdata, $err);

						if ($active_subscription) {
							BwPostmanSubscriberHelper::errorSubscriberData($err, $subscriberid, $subscriberdata->email);
						}
						else {
							$itemid	= (int) $model->getItemid(); // Itemid from edit-view

							BwPostmanSubscriberHelper::loginGuest((int) $subscriberid, (int) $itemid);
						}
					}
				}
			}
			$this->setData((int) $subscriberid, (int) $userid);
		}
*/	}

	/**
	 * Method to reset the subscriber ID and userid
	 *
	 * @access	public
	 *
	 * @param	int $subscriberid   subcriber ID
	 * @param 	int $userid         user ID
	 */
/*	public function setData($subscriberid = 0, $userid = 0)
	{
		$app	= JFactory::getApplication();
		$app->setUserState('subscriber.id', $subscriberid);

		$this->_subscriberid	= $subscriberid;
		$this->_userid			= $userid;
	}
*/
	/**
	 * Display
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	boolean		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return void
	 */
/*	public function display($cachable = false, $urlparams = false)
	{
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->getCmd('view', 'edit');
		$vFormat = $document->getType();
		$lName   = $this->input->getCmd('layout', 'default');

		if ($view = $this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				case "newsletters":
					$this->input->set('view', 'newsletters');
					break;

				// View of a single newsletter
				case "newsletter":
					$this->input->set('view', 'newsletter');
					break;

				// Register form
				case "register":
					$session       = JFactory::getSession();
					$session_error = $session->get('session_error');

					//				$this->input->set('view', 'edit');

					if (!(isset($session_error) && is_array($session_error)))
					{
						if (($this->_userid) && ($this->_subscriberid))
						{
						}
						elseif (($this->_userid) && (!$this->_subscriberid))
						{
							//						$this->input->set('view', 'register');
							// Redirect to register page.
							$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=register', false));
						}
						elseif ((!$this->_userid) && ($this->_subscriberid))
						{
						}
						else
						{
							// Redirect to register page.
							$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=register', false));
						}
					}
					break;
				case "edit":
					$session       = JFactory::getSession();
					$session_error = $session->get('session_error');

					$this->input->set('view', 'edit');

					if (!(isset($session_error) && is_array($session_error)))
					{
						if (($this->_userid) && ($this->_subscriberid))
						{
						}
						elseif (($this->_userid) && (!$this->_subscriberid))
						{
							$this->input->set('view', 'register');
						}
						elseif ((!$this->_userid) && ($this->_subscriberid))
						{
						}
						else
						{
							// Redirect to editlink form page.
//							$this->setRedirect(JRoute::_('index.php?option=com_bwpostman&view=edit&layout=editlink_form', false));
							$this->input->set('layout', 'editlink_form');
						}
					}
					break;
			}
		}
		parent::display();
	}
*/
	/**
	 * Method to show a captcha
	 *
	 * @since	1.0.1
	 */
	public function showCaptcha() {
		BwPostmanHelper::showCaptcha();
	}

}
