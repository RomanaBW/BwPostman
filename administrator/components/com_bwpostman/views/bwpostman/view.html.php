<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main view for backend.
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

// Require helper classes
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman General View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	CoverPage
 */
class BwPostmanViewBwPostman extends JViewLegacy
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
		$uri		= JFactory::getURI();
		$uri_string	= $uri->toString();
		$canDo		= BwPostmanHelper::getActions();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		// Get data from the model
		$this->archive		= $this->get('Archivedata');
		$this->general		= $this->get('Generaldata');
		$this->canDo		= $canDo;
		$this->request_url	= $uri_string;

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolBarHelper::title (JText::_('COM_BWPOSTMAN'), 'envelope');

		// Set toolbar items for the page
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_bwpostman', '500', '900');
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
		}
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();

		BwPostmanHelper::addSubmenu('bwpostman');

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}
}
