<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists view for backend.
 *
 * @version %%version_number%% build %%build_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
defined('_JEXEC') or die('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');

/**
 * BwPostman Lists View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Mailinglists
 *
 * @since       0.9.1
 */
class BwPostmanViewMailinglists extends JViewLegacy
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 *
	 * @since       0.9.1
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       0.9.1
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       0.9.1
	 */
	protected $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       0.9.1
	 */
	public $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       0.9.1
	 */
	public $activeFilters;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 *
	 * @since       0.9.1
	 */
	public $total;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['mailinglist'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

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

		return $this;
	}


	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_MLS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MLS'), 'list');

		// Set toolbar items for the page
		if ($this->permissions['mailinglist']['create'])
		{
			JToolbarHelper::addNew('mailinglist.add');
		}

		if (BwPostmanHelper::canEdit('mailinglist'))
		{
			JToolbarHelper::editList('mailinglist.edit');
		}

		JToolbarHelper::divider();
		if (BwPostmanHelper::canEditState('mailinglist'))
		{
			JToolbarHelper::publishList('mailinglists.publish');
			JToolbarHelper::unpublishList('mailinglists.unpublish');
			JToolbarHelper::divider();
		}

		if (BwPostmanHelper::canArchive('mailinglist'))
		{
			JToolbarHelper::archiveList('mailinglist.archive');
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}

		if (BwPostmanHelper::canEdit('mailinglist', 0) || BwPostmanHelper::canEditState('mailinglist', 0))
		{
			JToolbarHelper::checkin('mailinglists.checkin');
			JToolbarHelper::divider();
		}

		$link   = BwPostmanHTMLHelper::getForumLink();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, $link);

		JToolbarHelper::spacer();
	}
}
