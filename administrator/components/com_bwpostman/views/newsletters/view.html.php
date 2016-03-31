<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters view for backend.
 *
 * @version 2.0.0 bwpm
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


/**
 * BwPostman Newsletters View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 */
class BwPostmanViewNewsletters extends JViewLegacy
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 */
	protected $state;


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

		if (!BwPostmanHelper::canView('newsletters')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
			$jinput		= JFactory::getApplication()->input;
			$uri		= JFactory::getURI();

			//check for queue entries
			$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

			$app->setUserState('com_bwpostman.edit.newsletter.referrer', 'newsletters');
			// The query always contains the tab which we are in, but this might be confusing
			// That's why we will set the query only to controller = newsletters
			$uri_query	= 'option=com_bwpostman&view=newsletters';
			$uri->setQuery($uri_query);

			// Get data from the model
			$this->state			= $this->get('State');
			$this->items			= $this->get('Items');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
//			$this->queue			= $this->get('Queue');
			$this->pagination		= $this->get('Pagination');
			$this->total 			= $this->get('total');
			$this->count_queue		= $this->get('CountQueue');
			$this->context			= 'com_bwpostman.newsletters';

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('newsletters');

			$this->sidebar = JHtmlSidebar::render();

			// Show the layout depending on the tab
			$tpl = $jinput->get('tab', 'unsent');

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
		$tab	= $this->state->get('tab', 'unsent');
		$canDo	= BwPostmanHelper::getActions(0, 'newsletters');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_NLS'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolBarHelper::title (JText::_('COM_BWPOSTMAN_NLS'), 'envelope');

		// Set toolbar items for the page

		switch ($tab) { // The layout-variable tells us which tab we are in
			case "sent":
				if ($canDo->get('core.edit.state'))	{
					JToolBarHelper::publishList('newsletters.publish');
					JToolBarHelper::unpublishList('newsletters.unpublish');
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.create'))	{
					JToolBarHelper::custom('newsletter.copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.admin')) {
					JToolBarHelper::checkin('newsletters.checkin');
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.archive')) {
					JToolBarHelper::archiveList('newsletter.archive');
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				break;
			case "queue":
				$bar= JToolBar::getInstance('toolbar');
				$alt = "COM_BWPOSTMAN_NL_CONTINUE_SENDING";
				if ($canDo->get('core.send')) {
					JToolBarHelper::custom('newsletters.resetSendAttempts', 'unpublish.png', 'unpublish_f2.png', 'COM_BWPOSTMAN_NL_RESET_TRIAL', false);
					$bar->appendButton('Popup', 'envelope', $alt, 'index.php?option=com_bwpostman&view=newsletter&layout=queue_modal&format=raw&task=continue_sending', 600, 600);
					JToolBarHelper::custom('newsletters.clear_queue', 'delete.png', 'delete_f2.png', 'COM_BWPOSTMAN_NL_CLEAR_QUEUE', false);
				}
				break;
			case "unsent":
			default:
				if ($canDo->get('core.create'))	JToolBarHelper::addNew('newsletter.add');
				if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))	JToolBarHelper::editList('newsletter.edit');
				if ($canDo->get('core.create'))	JToolBarHelper::custom('newsletter.copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				JToolBarHelper::divider();
				JToolBarHelper::spacer();

				if ($canDo->get('core.send')) {
					JToolBarHelper::custom('newsletter.sendOut', 'envelope', 'send_f2.png', 'COM_BWPOSTMAN_NL_SEND', true);
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.archive')) {
					JToolBarHelper::archiveList('newsletter.archive');
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.manage')) {
					JToolBarHelper::checkin('newsletters.checkin');
					JToolBarHelper::divider();
				}
				break;
		}
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
