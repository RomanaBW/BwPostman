<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit view for frontend.
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

/**
 * Class BwPostmanViewEdit
 */
class BwPostmanViewEdit extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$this->user	= JFactory::getUser();
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
			$this->selected_mailinglists = $subscriber->mailinglists;
		}
		else {
			$subscriber	= $this->get('Item');
			if(!is_object($subscriber)){
				$subscriber = $model->fillVoidSubscriber();
			}
			else {
				$this->selected_mailinglists = $subscriber->mailinglists;
			}
		}
		if (is_array($this->selected_mailinglists)) $app->setUserState('com_bwpostman.subscriber.selected_lists', $this->selected_mailinglists);

		// Get the mailinglists which the subscriber is authorized to see
		$this->available_mailinglists = $this->get('mailinglists');

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
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->setTitle($params->get('page_title'));
		$document->addStyleSheet(JURI::root(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet(JURI::root(true) . $css_filename);

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		// Build the email format select list
		if (!isset($subscriber->emailformat)) {
			$mailformat_selected = $params->get('default_emailformat');
		}
		else {
			$mailformat_selected = $subscriber->emailformat;
		}

		$emailformat 	= '<fieldset id="edit_mailformat" class="radio btn-group">';
		$emailformat		.= '<input type="radio" name="emailformat" id="formatText" value="0"';
		if(!$mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}
		$emailformat     .= ' />';
		$emailformat		.= '<label for="formatText"><span>'. JText::_('COM_BWPOSTMAN_TEXT') . '</span></label>';
		$emailformat     .= '<input type="radio" name="emailformat" id="formatHtml" value="1"';
		if($mailformat_selected)
		{
			$emailformat .= ' checked="checked"';
		}
		$emailformat     .= ' />';
		$emailformat     .= '<label for="formatHtml"><span>' . JText::_('COM_BWPOSTMAN_HTML') . '</span></label>';
		$emailformat     .= '</fieldset>';
		$lists['emailformat'] = $emailformat;

		// Build the gender select list
		if (!isset($subscriber->gender)) {
			$gender_selected = '';
		}
		else {
			$gender_selected = $subscriber->gender;
		}

		$gender 	= '<fieldset id="edit_gender" class="radio btn-group">';
		$gender		.= '<input type="radio" name="gender" id="genMale" value="0"';
		if($gender_selected === 0)
		{
			$gender .= ' checked="checked"';
		}
		$gender     .= ' />';
		$gender		.= '<label for="genMale"><span>'. JText::_('COM_BWPOSTMAN_MALE') . '</span></label>';
		$gender     .= '<input type="radio" name="gender" id="genFemale" value="1"';
		if($gender_selected)
		{
			$gender .= ' checked="checked"';
		}
		$gender     .= ' />';
		$gender     .= '<label for="genFemale"><span>' . JText::_('COM_BWPOSTMAN_FEMALE') . '</span></label>';
		$gender     .= '</fieldset>';
		$lists['gender'] = $gender;

		// Save a reference into the view
		$this->lists        = $lists;
		$this->subscriber   = $subscriber;
		$this->params       = $params;

		// Set parent display
		parent::display($tpl);
	}
}
