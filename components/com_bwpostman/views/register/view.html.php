<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register view for frontend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

class BwPostmanViewRegister extends JViewLegacy
{
	/**
	 * Display
	 * --> load View depending on the layout
	 *
	 * @access	public
	 * @param 	string Template
	 */
	public function display($tpl=null)
	{
		$app		= JFactory::getApplication();
		$layout		= $this->getLayout();
		$document	= JFactory::getDocument();

		$params			= $app->getPageParameters();
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';
		$this->captcha	= BwPostmanHelper::getCaptcha(1);

		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet($css_filename);

		switch ($layout) {
			case "error_accountblocked":
			case "error_accountgeneral":
			case "error_accountnotactivated":
			case "error_email":
			case "error_geteditlink":
				$this->_displayError($tpl);
				return;
				break;
			case "success_msg":
				$this->_displaySuccess($tpl);
				return;
				break;
			default:
				$this->_displayDefault($tpl);
				return;
				break;
		}
		parent::display($tpl);
	}

	/**
	 * View Error Display
	 *
	 * @access	private
	 * @param 	string Template
	 */
	private function _displayError($tpl)
	{
		$app		= JFactory::getApplication();
		$uri_root	= JFactory::getURI()->root();
		$config		= JFactory::getConfig();
		$params		= $app->getPageParameters();
		$menu		= $app->getMenu()->getActive();
		$err		= JFactory::getSession()->get('session_error', null);
		$error		= new stdClass();

		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		if(isset($err) && is_array($err)){
			foreach ($err AS $key => $value) {
				$error->$key = $value;
			}
		}

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) {
			$menu_params = new JRegistry();
			$menu_params->loadString($menu->params, 'JSON');
			if (!$menu_params->get('page_title')) {
				$title = JText::_('COM_BWPOSTMAN_NL_REGISTRATION');
			}
			else {
				$title = $menu_params->get('page_title');
			}
		}
		else {
			$params->set('page_title',	JText::_('COM_BWPOSTMAN_NL_REGISTRATION'));
		}

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet($css_filename);

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		// Save references into view
		$this->assignRef('config', $config);
		$this->assignRef('error', $error);
		$this->assignRef('params', $params);
		$this->assignRef('uri', $uri_root);

		//reset error state
		$app->setUserState('com_bwpostman.subscriber.register.error', null);

		// Set parent display
		parent::display();
	}

	/**
	 * View Success Display
	 *
	 * @access	private
	 * @param	string Template
	 */
	private function _displaySuccess($tpl)
	{
		$app		= JFactory::getApplication();
		$uri		= JFactory::getURI();
		$root		= $uri->root();
		$user 		= JFactory::getUser();
		$session	= JFactory::getSession();
		$success	= new stdClass();
		$params		= $app->getPageParameters();
		$menu		= $app->getMenu()->getActive();

		$session_success = $session->get('session_success');
		if(isset($session_success) && is_array($session_success)){
			foreach ($session_success AS $key => $value) {
				$success->$key = $value;
				$session->clear('session_success');
			}
		}

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) {
			$menu_params = new JRegistry();
			$menu_params->loadString($menu->params, 'JSON');
			if (!$menu_params->get('page_title')) {
				$title = JText::_('COM_BWPOSTMAN_NL_REGISTRATION');
			}
			else {
				$title = $menu_params->get('page_title');
			}
		}
		else {
			$params->set('page_title',	JText::_('COM_BWPOSTMAN_NL_REGISTRATION'));
		}

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet($css_filename);

		// Save references into view
		$this->assignRef('params', $params);
		$this->assignRef('success', $success);
		$this->assignRef('uri', $root);
		$this->assignRef('user', $user);

		// Set parent display
		parent::display();
	}

	/**
	 * View Default Display
	 *
	 * @access	private
	 * @param 	string Template
	 */
	private function _displayDefault($tpl)
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$session	= JFactory::getSession();
		$reg_model	= $this->getModel('register');
		$params		= $app->getPageParameters('com_bwpostman');
		$menu		= $app->getMenu()->getActive();
		$subscriber	= new stdClass();


		// If there occured an error while storing the data load the data from the session
		$subscriber_data = $session->get('subscriber_data');

		if(isset($subscriber_data) && is_array($subscriber_data)){
			foreach ($subscriber_data AS $key => $value) {
				$subscriber->$key = $value;
			}
			$subscriber->id	= 0;
			$session->clear('subscriber_data');
			$selected_mailinglists = $subscriber->mailinglists;
		}
		else {
			// If the user is logged into the website get the data from users-table
			if (!$user->get('guest')) {
				$subscriber->name = $user->get('name');
				$subscriber->email = $user->get('email');
			}
			else {
				$subscriber = $reg_model->fillVoidSubscriber();
			}
		}

		// Get the mailinglists which the subscriber is authorized to see
		$available_mailinglists = $this->get('mailinglists');

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu)) {
			$menu_params = new JRegistry();
			$menu_params->loadString($menu->params, 'JSON');
			if (!$menu_params->get('page_title')) {
				$title = JText::_('COM_BWPOSTMAN_NL_REGISTRATION');
			}
			else {
				$title = $menu_params->get('page_title');
			}
		}
		else {
			$params->set('page_title',	JText::_('COM_BWPOSTMAN_NL_REGISTRATION'));
		}

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet($css_filename);

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		// Build the emailormat select list
		if (!isset($subscriber->emailformat)) {
			$selected = $params->get('default_emailformat');
		} else {
			$selected = $subscriber->emailformat;
		}
		$emailformat 	= array();
		$emailformat[] 	= JHTML::_('select.option',  '0', JText::_('COM_BWPOSTMAN_TEXT'));
		$emailformat[] 	= JHTML::_('select.option',  '1', JText::_('COM_BWPOSTMAN_HTML'));
		$lists['emailformat']= JHTML::_('select.radiolist',  $emailformat, 'emailformat', 'class="inputbox" ', 'value', 'text', $selected);

		// Save references into view
		$this->assignRef('available_mailinglists', $available_mailinglists);
		$this->assignRef('lists', $lists);
		$this->assignRef('params', $params);
		$this->assignRef('selected_mailinglists', $selected_mailinglists);
		$this->assignRef('subscriber', $subscriber);
		$this->assignRef('user', $user);

		parent::display($tpl);
	}
}
