<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists view for backend.
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
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 */
	public $filterForm;

	/**
	 * property to hold sactive filters
	 *
	 * @var object  $activeFilters
	 */
	public $activeFilters;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 */
	public $total;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 */
	public $sidebar;

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

		if (!BwPostmanHelper::canView('mailinglists'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else
		{
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
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title (JText::_('COM_BWPOSTMAN_MLS'), 'list');

		// Set toolbar items for the page
		if ($canDo->get('bwpm.create'))	JToolbarHelper::addNew('mailinglist.add');
		if (($canDo->get('bwpm.edit')) || ($canDo->get('bwpm.edit.own')))	JToolbarHelper::editList('mailinglist.edit');
		JToolbarHelper::divider();
		if ($canDo->get('bwpm.edit.state'))
		{
			JToolbarHelper::publishList('mailinglists.publish');
			JToolbarHelper::unpublishList('mailinglists.unpublish');
			JToolbarHelper::divider();
		}
		if ($canDo->get('bwpm.archive'))
		{
			JToolbarHelper::archiveList('mailinglist.archive');
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}
		if ($canDo->get('bwpm.manage'))
		{
			JToolbarHelper::checkin('mailinglists.checkin');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolbarHelper::spacer();
	}
}
