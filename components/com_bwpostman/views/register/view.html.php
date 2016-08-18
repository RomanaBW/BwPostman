<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register view for frontend.
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

// Import VIEW object class
jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

// Require helper classes
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT . '/helpers/subscriberhelper.php');

/**
 * Class BwPostmanViewRegister
 *
 * @since       0.9.1
 */
class BwPostmanViewRegister extends JViewLegacy
{
	/**
	 * The subscriber data
	 *
	 * @var    object
	 *
	 * @since       0.9.1
	 */
	public $subscriber = null;

	/**
	 * several needed lists
	 *
	 * @var    array
	 *
	 * @since       0.9.1
	 */
	public $lists = null;

	/**
	 * The component parameters
	 *
	 * @var    object   Registry object
	 *
	 * @since       0.9.1
	 */
	public $params = null;

	/**
	 * The component captcha
	 *
	 * @var    string
	 *
	 * @since       0.9.1
	 */
	public $captcha = null;

	/**
	 * The current error object
	 *
	 * @var    object
	 *
	 * @since       0.9.1
	 */
	public $error = null;

	/**
	 * The current success object
	 *
	 * @var    object
	 *
	 * @since       0.9.1
	 */
	public $success = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       0.9.1
	 */
	public function display($tpl=null)
	{
		$app		    = JFactory::getApplication();
		$document       = JFactory::getDocument();
		$this->params	= JComponentHelper::getParams('com_bwpostman', true);
		$layout		    = $this->getLayout();

		$this->captcha	= BwPostmanHelper::getCaptcha(1);

		// Add document css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document->addStyleSheet(JUri::root(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet(JUri::root(true) . $css_filename);

		switch ($layout)
		{
			case "error_accountblocked":
			case "error_accountgeneral":
			case "error_accountnotactivated":
			case "error_email":
			case "error_geteditlink":
				$this->_displayError();
				break;
			case "success_msg":
				$this->_displaySuccess();
				break;
			default:
				$this->_displayDefault();
				break;
		}
		parent::display($tpl);
	}

	/**
	 * View Error Display
	 *
	 * @access	private
	 *
	 * @since       0.9.1
	 */
	private function _displayError()
	{
		$session	    = JFactory::getSession();
		$this->error    = new stdClass();
		$err	    = $session->get('session_error', null);

		if(isset($err) && is_array($err))
		{
			foreach ($err AS $key => $value)
			{
				$this->error->$key = $value;
			}
			$session->clear('session_error');
		}

		//reset error state
		JFactory::getApplication()->setUserState('com_bwpostman.subscriber.register.error', null);
	}

	/**
	 * View Success Display
	 *
	 * @access	private
	 *
	 * @since       0.9.1
	 */
	private function _displaySuccess()
	{
		$session	    = JFactory::getSession();
		$this->success  = new stdClass();

		$session_success = $session->get('session_success');
		if(isset($session_success) && is_array($session_success))
		{
			foreach ($session_success AS $key => $value)
			{
				$this->success->$key = $value;
				$session->clear('session_success');
			}
		}
	}

	/**
	 * View Default Display
	 *
	 * @access	private
	 *
	 * @since       0.9.1
	 */
	private function _displayDefault()
	{
		$user		= JFactory::getUser();
		$session	= JFactory::getSession();
		$subscriber	= new stdClass();
		$lists      = array();

		// If there occurred an error while storing the data load the data from the session
		$subscriber_data = $session->get('subscriber_data');

		if(isset($subscriber_data) && is_array($subscriber_data))
		{
			foreach ($subscriber_data AS $key => $value)
			{
				$subscriber->$key = $value;
			}
			$subscriber->id	= 0;
			$session->clear('subscriber_data');
		}
		else
		{
			$subscriber = BwPostmanSubscriberHelper::fillVoidSubscriber();
			// If the user is logged into the website get the data from users-table
			if (!$user->get('guest'))
			{
				$subscriber->name = $user->get('name');
				$subscriber->email = $user->get('email');
			}
		}

		// Get the mailinglists which the subscriber is authorized to see
		$lists['available_mailinglists'] = BwPostmanSubscriberHelper::getAuthorizedMailinglists($subscriber->id);

		// Build the email format select list
		if (!isset($subscriber->emailformat))
		{
			$mailformat_selected = $this->params->get('default_emailformat');
		}
		else
		{
			$mailformat_selected = $subscriber->emailformat;
		}
		$lists['emailformat'] = BwPostmanSubscriberHelper::buildMailformatSelectList($mailformat_selected);

		// Build the gender select list
		if (!isset($subscriber->gender))
		{
			$gender_selected = 0;
		}
		else
		{
			$gender_selected = $subscriber->gender;
		}
		$lists['gender'] = BwPostmanSubscriberHelper::buildGenderList($gender_selected);

		// Save references into view
		$this->lists        = $lists;
		$this->subscriber   = $subscriber;
	}
}
