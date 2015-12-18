<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers view for backend.
 *
 * @version 1.3.0 bwpm
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
 * BwPostman Subscribers View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class BwPostmanViewSubscribers extends JViewLegacy
{
	/**
	 * Display
	 *
	 * @access	public
	 * @param	string Template
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		if (!BwPostmanHelper::canView('subscribers')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_SUB')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
			// Get data from the model
			$this->state			= $this->get('State');
			$this->items 			= $this->get('Items');
			$this->mailinglists 	= $this->get('Mailinglists');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
			$this->pagination		= $this->get('Pagination');
			$this->total 			= $this->get('total');
			$this->context			= 'com_bwpostman.subscribers';

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('subscribers');

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
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$tab	= $app->getUserState($this->context . '.tab', 'confirmed');
		$canDo	= BwPostmanHelper::getActions(0, 'subscribers');
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_CAMS'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolBarHelper::title (JText::_('COM_BWPOSTMAN_SUB'), 'users');

		// Set toolbar items for the page
		switch ($tab) { // The layout-variable tells us which tab we are in
			default;
			case "confirmed":
			case "unconfirmed":
					if ($canDo->get('core.create'))	JToolBarHelper::addNew('subscriber.add');
					if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))	JToolBarHelper::editList('subscriber.edit');
					JToolBarHelper::spacer();
					JToolBarHelper::divider();
					JToolBarHelper::spacer();

					if ($canDo->get('core.create'))		JToolBarHelper::custom('subscribers.importSubscribers', 'download', 'import_f2', 'COM_BWPOSTMAN_SUB_IMPORT', false);
					if ($canDo->get('core.edit'))		JToolBarHelper::custom('subscribers.exportSubscribers', 'upload', 'export_f2', 'COM_BWPOSTMAN_SUB_EXPORT', false);
					if ($canDo->get('core.archive')) {
						JToolBarHelper::divider();
						JToolBarHelper::spacer();
						JToolBarHelper::archiveList('subscriber.archive');
					}
					// Add a batch button
					if ($user->authorise('core.create', 'com_bwpostman') && $user->authorise('core.edit', 'com_bwpostman') && $user->authorise('core.edit.state', 'com_bwpostman'))
					{
						JHtml::_('bootstrap.modal', 'collapseModal');
						$title = JText::_('JTOOLBAR_BATCH');

						// Instantiate a new JLayoutFile instance and render the batch button
						$layout = new JLayoutFile('joomla.toolbar.batch');

						$dhtml = $layout->render(array('title' => $title));
						$bar->appendButton('Custom', $dhtml, 'batch');
					}
				break;
			case "testrecipients":
					if ($canDo->get('core.create'))	JToolBarHelper::addNew('subscriber.add_test');
					if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))	JToolBarHelper::editList('subscriber.edit');
					JToolBarHelper::spacer();
					JToolBarHelper::divider();
					if ($canDo->get('core.archive'))	JToolBarHelper::archiveList('subscriber.archive');
				break;
		}
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		if ($canDo->get('core.manage')) {
			JToolBarHelper::checkin('subscribers.checkin');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
	}
}
