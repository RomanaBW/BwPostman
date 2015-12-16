<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template view for backend.
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

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman template View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	template
 */
class BwPostmanViewTemplate extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display
	 *
	 * @access	public
	 *
	 * @param	string Template
	 *
	 * @since	1.1.0
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$_db		= JFactory::getDBO();
		$template	= $app->getTemplate();
		$uri		= JFactory::getURI();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$app->setUserState('com_bwpostman.edit.template.id', JFactory::getApplication()->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->canDo	= BwPostmanHelper::getActions($this->item->id, 'template');

		// Save a reference into view
		$this->request_url	= $uri_string;
		$this->template		= $template;

		$this->addToolbar();

		// call usermade html template
		if ($this->item->tpl_id == '0')  $tpl = 'html';
		// call usermade text template
		if ($this->item->tpl_id == '998') $tpl = 'text';
		// call standard text template
		if ($this->item->tpl_id > '999')  $tpl = 'text_std';

		// Call parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 * @since	1.1.0
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$uri		= JFactory::getURI();
		$userId		= JFactory::getUser()->get('id');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('BWP_TPL_DETAILS'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get the user browser --> if the user has msie load the ie-css to show the tabs in the correct way
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();
		$user_browser = $browser->getBrowser();

		if ($user_browser == 'msie') {
			$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend_ie.css');
		}

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew = ($this->item->id < 1);

		// Set toolbar title and items
		$canDo = BwPostmanHelper::getActions($this->item->id, 'template');
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$this->canDo	= $canDo;

		// For new records, check the create permission.
		if ($isNew && $canDo->get('core.create')) {
			JToolBarHelper::save('template.save');
			JToolBarHelper::apply('template.apply');
			JToolBarHelper::cancel('template.cancel');

			JToolBarHelper::title(JText::_('COM_BWPOSTMAN_TPL_DETAILS').': <small>[ ' . JText::_('NEW').' ]</small>', 'plus');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::save('template.save');
					JToolBarHelper::apply('template.apply');
				}
			}
			// If checked out, we can still copy
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('template.save2copy');
			}
			// Rename the cancel button for existing items
			JToolBarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
			JToolBarHelper::title(JText::_('COM_BWPOSTMAN_TPL_DETAILS').':  <strong>' . $this->item->title  . '  </strong><small>[ ' . JText::_('EDIT').' ]</small> ', 'edit');
		}

		$backlink 	= $_SERVER['HTTP_REFERER'];
		$siteURL 	= $uri->base();

		// If we came from the cover page we will show a back-button
		if ($backlink == $siteURL.'index.php?option=com_bwpostman') {
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::back();
		}
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
