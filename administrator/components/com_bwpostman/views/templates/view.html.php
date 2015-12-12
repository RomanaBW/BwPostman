<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all templates view for backend.
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
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman templates View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	templates
 */
class BwPostmanViewTemplates extends JViewLegacy
{
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
		$user		= JFactory::getUser();

		if (!BwPostmanHelper::canView('templates')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
		// Build the key for the userState
			$key			= $this->getName();
			$filter_search	= $app->getUserStateFromRequest($key.'search_filter', 'filter.search_filter', 'title', 'string');

			// Get data from the model
			$this->state			= $this->get('State');
			$this->items			= $this->get('Items');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
			$this->pagination		= $this->get('Pagination');
			$this->total			= $this->get('total');

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('templates');

			$this->sidebar = JHtmlSidebar::render();

			// Call parent display
			parent::display($tpl);
		}
	}


	/**
	 * Add the page title, submenu and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$canDo	= BwPostmanHelper::getActions(0, 'template');
		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_TPL'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolBarHelper::title (JText::_('COM_BWPOSTMAN_TPL'), 'picture');

		// Set toolbar items for the page
		if ($canDo->get('core.create'))		JToolBarHelper::custom('template.addhtml', 'calendar', 'HTML', 'COM_BWPOSTMAN_TPL_ADDHTML', false);
		if ($canDo->get('core.edit'))		JToolBarHelper::custom('template.addtext', 'new', 'TEXT', 'COM_BWPOSTMAN_TPL_ADDTEXT', false);
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))	JToolBarHelper::editList('template.edit');

		if ($canDo->get('core.edit.state')) {
			JToolbarHelper::makeDefault('template.setDefault', 'COM_BWPOSTMAN_TPL_SET_DEFAULT');
			JToolBarHelper::publishList('templates.publish');
			JToolBarHelper::unpublishList('templates.unpublish');
		}

		JToolBarHelper::divider();
		JToolBarHelper::spacer();

		if ($canDo->get('core.archive')) {
			JToolBarHelper::archiveList('template.archive');
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
		}
		if ($canDo->get('core.manage')) {
			JToolBarHelper::checkin('templates.checkin');
			JToolBarHelper::divider();
		}
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
