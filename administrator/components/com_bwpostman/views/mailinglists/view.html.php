<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists view for backend.
 *
 * @version 1.3.2 bwpm
 * @package BwPostman-Admin
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

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman Lists View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Mailinglists
 */
class BwPostmanViewMailinglists extends JViewLegacy
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
		$app	= JFactory::getApplication();

		if (!BwPostmanHelper::canView('mailinglists')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
			// Get data from the model
			$this->state			= $this->get('State');
			$this->items			= $this->get('Items');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
			$this->pagination		= $this->get('Pagination');
			$this->total			= $this->get('total');

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('mailinglists');

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
		$canDo	= BwPostmanHelper::getActions(0, 'mailinglists');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_MLS'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolBarHelper::title (JText::_('COM_BWPOSTMAN_MLS'), 'list');

		// Set toolbar items for the page
		if ($canDo->get('core.create'))	JToolBarHelper::addNew('mailinglist.add');
		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))	JToolBarHelper::editList('mailinglist.edit');
		JToolBarHelper::divider();
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('mailinglists.publish');
			JToolBarHelper::unpublishList('mailinglists.unpublish');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.archive')) {
			JToolBarHelper::archiveList('mailinglist.archive');
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
		}
		if ($canDo->get('core.manage')) {
			JToolBarHelper::checkin('mailinglists.checkin');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
