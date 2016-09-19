<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive view for backend.
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
 * BwPostman Archive View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Archive
 *
 * @since       0.9.1
 */
class BwPostmanViewArchive extends JViewLegacy
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
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       0.9.1
	 */
	public $request_url;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

	/**
	 * Display
	 *
	 * @access	public
	 *
	 * @param	string $tpl Template
	 *
	 * @return void
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		if (!BwPostmanHelper::canView('archive'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else
		{
			// Get data from the model
			$this->items 			= $this->get('Items');
			$this->pagination		= $this->get('Pagination');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
			$this->state			= $this->get('State');

			$request_result = $this->_checkForAllowedTab();

			if ($request_result === false)
			{
				$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_ARC')), 'error');
				$app->redirect('index.php?option=com_bwpostman');
			}

			if ($request_result === 'redirect')
			{
				$app->redirect($this->request_url);
				// http://www.dev.nil/administrator/index.php?option=com_bwpostman&view=archive&layout=newsletters
			}

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('archive');

			$this->sidebar = JHtmlSidebar::render();

			// Call parent display
			parent::display($tpl);
		}
	}

	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		$jinput	= JFactory::getApplication()->input;

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_ARC'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title (JText::_('COM_BWPOSTMAN_ARC'), 'list');

		// Set toolbar items for the page (depending on the tab which we are in)
		$layout = $jinput->get('layout', 'newsletters');
		switch ($layout)
		{ // Which tab are we in?
			case "newsletters":
					if (BwPostmanHelper::canRestore('archive'))	JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
					if (BwPostmanHelper::canDelete('archive'))	JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_NL'), 'archive.delete');
				break;
			case "subscribers":
					if (BwPostmanHelper::canRestore('archive'))	JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
					if (BwPostmanHelper::canDelete('archive'))	JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_SUB'), 'archive.delete');
				break;
			case "campaigns":
					// Special unarchive and delete button because we need a confirm dialog with 3 options
					$bar= JToolbar::getInstance('toolbar');
					$alt_archive = "unarchive";
					if (BwPostmanHelper::canRestore('archive'))	$bar->appendButton('Popup', 'unarchive', $alt_archive, 'index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmunarchive', 500, 130);
					$alt_delete = "delete";
					if (BwPostmanHelper::canDelete('archive'))	$bar->appendButton('Popup', 'delete', $alt_delete, 'index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmdelete', 500, 150);
				break;
			case "mailinglists":
					if (BwPostmanHelper::canRestore('archive'))	JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
					if (BwPostmanHelper::canDelete('archive'))	JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_ML'), 'archive.delete');
				break;
			case "templates":
					if (BwPostmanHelper::canRestore('archive'))	JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
					if (BwPostmanHelper::canDelete('archive'))	JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_TPL'), 'archive.delete');
				break;
		}
		JToolbarHelper::spacer();
		JToolbarHelper::divider();
		JToolbarHelper::spacer();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolbarHelper::spacer();
	}

	/**
	 * Check permission for archive tab and set layout to allowed one, if needed
	 *
	 * @since       2.0.0
	 */
	private function _checkForAllowedTab()
	{
		$uri        = JUri::getInstance('SERVER');
		$uri_string = $uri->toString();
		$uri_short  = substr($uri_string, strrpos($uri_string, '/') + 1, strlen($uri_string));

		$layout = $this->_extractLayout($uri_short);
		if ($layout == false)
		{
			return false;
		}

		$allowed_layout = $this->_getAllowedLayout($layout);

		if ($allowed_layout == false)
		{
			return false;
		}

		if ($allowed_layout != $layout)
		{
			$this->request_url = str_replace($layout, $allowed_layout, $uri_short);
			return 'redirect';
		}

		return true;
	}

	/**
	 *
	 * @param   string      $uri_string
	 *
	 * @return string|bool $layout  requested layout or false on error
	 *
	 * @since version
	 */
	private function _extractLayout($uri_string)
	{
		$uri_array = explode('&', $uri_string);

		if (count($uri_array) != 3)
		{
			return false;
		}

		$layout_arr = explode('=', $uri_array[2]);

		if (count($layout_arr) != 2)
		{
			return false;
		}

		$layout = $layout_arr[1];

		return $layout;
	}

	/**
	 *
	 * @param   string      $layout
	 *
	 * @return string|bool $allowed_layout  allowed layout or false on error
	 *
	 * @since version
	 */
	private function _getAllowedLayout($layout)
	{
		if (BwPostmanHelper::canView(substr($layout, 0, -1)))
		{
			return $layout;
		}

		// check for some allowed layout
		$all_layouts    = array('newsletter', 'subscriber', 'campaign', 'mailinglist', 'campaign');
		foreach ($all_layouts as $item)
		{
			$allowed    = BwPostmanHelper::canView($item);
			if ($allowed)
			{
				return  $item . 's';
			}
		}
	}
}
