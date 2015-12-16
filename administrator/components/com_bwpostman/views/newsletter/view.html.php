<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single html newsletters view for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
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

/**
 * BwPostman Newsletter View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 */
class BwPostmanViewNewsletter extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	/**
	 * Display
	 *
	 * @access	public
	 * @param	string Template
	 */
	public function display($tpl=null)
	{
		// Initialize variables
		$dispatcher = JEventDispatcher::getInstance();
		$res		= JPluginHelper::importPlugin('bwpostman');
		$app		= JFactory::getApplication();
		$_db		= JFactory::getDBO();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		// Get input data
		$jinput		= $app->input;
		$cid		= $jinput->getInt('id', 0);
		$referrer	= $jinput->get->get('referrer', '', 'string');

		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->state		= $this->get('State');
		$this->canDo		= BwPostmanHelper::getActions($this->item->id, 'newsletter');
		$this->template		= $app->getTemplate();
		$this->params		= JComponentHelper::getParams('com_bwpostman');

		$dispatcher->trigger('onBwPostmanBeforeNewsletterEdit', array(&$this->item, $referrer));

		// set some needed flags
		// flag, if rendered content exists or not
		if ($this->item->html_version || $this->item->text_version) {
			$this->content_exists = true;
		} else {
			$this->content_exists = false;
		}

		// flag for selected content before editing
		if (is_array($this->item->selected_content)) {
			$this->selected_content_old	= implode(',', $this->item->selected_content);
		}
		elseif (isset($this->item->selected_content)) {
			$this->selected_content_old	= $this->item->selected_content;
		}
		else {
			$this->selected_content_old	= '';
		}

		// flags for template ids before editing
		$this->template_id_old		= $this->item->template_id_old;
		$this->text_template_id_old	= $this->item->text_template_id_old;

		$this->addToolbar();

		// reset temporary state
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', false);

		// Call parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$uri		= JFactory::getURI();
		$userId		= JFactory::getUser()->get('id');
		$layout		= JFactory::getApplication()->input->get('layout', '');

		// Get document object, set document title and add css
		$document	= JFactory::getDocument();
		$document->setTitle('COM_BWPOSTMAN_NL_DETAILS');
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');
		$document->addScript(JURI::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpostman_nl.js');

		// Set toolbar title and items
		$canDo			= BwPostmanHelper::getActions($this->item->id, 'newsletter');
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$this->canDo	= $canDo;

		$isNew = ($this->item->id == 0);

		// If we come from sent newsletters, we have to do other stuff than normal
		if ($layout == 'edit_publish') {
			JToolBarHelper::save('newsletter.publish_save');
			JToolBarHelper::cancel('newsletter.cancel');
			JToolBarHelper::title(JText::_('COM_BWPOSTMAN_NL_PUBLISHING_DETAILS').': <small>[ ' . JText::_('NEW').' ]</small>', 'plus');
		}
		else {

			// For new records, check the create permission.
			if ($isNew && $canDo->get('core.create')) {
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_NL_DETAILS').': <small>[ ' . JText::_('EDIT').' ]</small>', 'edit');
				JToolBarHelper::save('newsletter.save');
				JToolBarHelper::apply('newsletter.apply');

				$task		= JFactory::getApplication()->input->get('task', '', 'string');
				// If we came from the main page we will show a back button
				if ($task == 'add') {
					JToolBarHelper::back();
				}
				else {
					JToolBarHelper::cancel('newsletter.cancel');
				}
			}
			else {
				// Can't save the record if it's checked out.
				if (!$checkedOut) {
					// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
					if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
						JToolBarHelper::save('newsletter.save');
						JToolBarHelper::apply('newsletter.apply');
					}
				}
				// Rename the cancel button for existing items
				JToolBarHelper::cancel('newsletter.cancel', 'COM_BWPOSTMAN_CLOSE');
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_NL_DETAILS').': <small>[ ' . JText::_('EDIT').' ]</small>', 'edit');
			}
	/*		JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			if ($canDo->get('core.create') || $canDo->get('core.edit') || $canDo->get('core.send')) JToolBarHelper::custom('newsletter.checkForm', 'thumbs-up', 'checkform_f2', 'COM_BWPOSTMAN_NL_CHECK_FORM', false);
	*/
		}
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
