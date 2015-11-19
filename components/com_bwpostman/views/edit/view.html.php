<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit view for frontend.
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

class BwPostmanViewEdit extends JViewLegacy
{
	/**
	 * Display
	 *
	 * @access	public
	 * @param	string Template
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$user 		= JFactory::getUser();
		$params		= $app->getPageParameters();
		$menu		= $app->getMenu()->getActive();
		$model		= $this->getModel('edit');
		
		// If there occured an error while storing the data load the data from the session
		$subscriber_data = $session->get('subscriber_data');
		
		if(isset($subscriber_data) && is_array($subscriber_data)){
			$subscriber	= new stdClass();
			foreach ($subscriber_data AS $key => $value) {
				$subscriber->$key = $value;
			}
			$subscriber->id	= 0;
			$session->clear('subscriber_data');
			$selected_mailinglists = $subscriber->mailinglists;
		} 
		else {
			$subscriber	= $this->get('Item');
			if(!is_object($subscriber)){
				$subscriber = $model->fillVoidSubscriber();
			} 
			else {
				$selected_mailinglists = $subscriber->mailinglists;
			}
		}
		if (is_array($selected_mailinglists)) $app->setUserState('com_bwpostman.subscriber.selected_lists', $selected_mailinglists); 
		 
		// Get the mailinglists which the subscriber is authorized to see
		$available_mailinglists = $this->get('mailinglists');

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		$title	= JText::_('COM_BWPOSTMAN_NL_REGISTRATION');
		
		if (is_object($menu)) {
			$menu_params	= new JRegistry();
			$menu_params->loadString($menu->params, 'JSON');
			if ($menu_params->get('page_title')) $title = $menu_params->get('page_title');
		}
		$params->set('page_title',	$title);

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
//		$css_filename	= JURI::root(true) . '/templates/' . $templateName . '/css/com_bwpostman.css';
		$css_filename	= 'templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet('components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists($css_filename)) $document->addStyleSheet($css_filename);
				
		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		// Build the emailormat select list
		if (!isset($subscriber->emailformat)) {
			$selected = $params->get('default_emailformat');
		} 
		else {
			$selected = $subscriber->emailformat;
		}

		// Build the emailormat select list
		$emailformat 			= array();
		$emailformat[] 			= JHTML::_('select.option',  '0', '<span>' . JText::_('COM_BWPOSTMAN_TEXT') . '</span>');
		$emailformat[]			= JHTML::_('select.option',  '1', '<span>' . JText::_('COM_BWPOSTMAN_HTML') . '</span>');
		$lists['emailformat']	= JHTML::_('select.radiolist',  $emailformat, 'a_emailformat', 'class="checkbox" ', 'value', 'text', $selected);
		
		// Save a reference into the view
		$this->assignRef('available_mailinglists', $available_mailinglists);
		$this->assignRef('selected_mailinglists', $selected_mailinglists);
		$this->assignRef('lists', $lists);
		$this->assignRef('subscriber', $subscriber);
		$this->assignRef('params', $params);
		$this->assignRef('user', $user);
		
		// Set parent display
		parent::display($tpl);
	}
}
