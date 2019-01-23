<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive view for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
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
	 * property to hold user permissions
	 *
	 * @var array  $permissions
	 *
	 * @since       2.0.0
	 */
	protected $permissions;

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
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['archive'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Get data from the model
		$this->items 			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->state			= $this->get('State');

		$request_result = $this->checkForAllowedTabs();

		if ($request_result === false)
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		if ($request_result === 'redirect')
		{
			$app->redirect($this->request_url);
		}

		$this->addToolbar();

		BwPostmanHelper::addSubmenu('archive');

		$this->sidebar = JHtmlSidebar::render();

		// Call parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @throws Exception
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
		JToolbarHelper::title(JText::_('COM_BWPOSTMAN_ARC'), 'list');

		// Set toolbar items for the page (depending on the tab which we are in)
		$layout = $jinput->get('layout', 'newsletters');
		switch ($layout)
		{ // Which tab are we in?
			case "newsletters":
				if (BwPostmanHelper::canRestore('newsletter', 0))
				{
					JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
				}

				if (BwPostmanHelper::canDelete('newsletter', 0))
				{
					JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_NL'), 'archive.delete');
				}
				break;
			case "subscribers":
				if (BwPostmanHelper::canRestore('subscriber', 0))
				{
					JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
				}

				if (BwPostmanHelper::canDelete('subscriber', 0))
				{
					JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_SUB'), 'archive.delete');
				}
				break;
			case "campaigns":
				// Special unarchive and delete button because we need a confirm dialog with 3 options
				$bar = JToolbar::getInstance('toolbar');
				$alt_archive = JText::_('COM_BWPOSTMAN_UNARCHIVE');
				if (BwPostmanHelper::canRestore('campaign', 0))
				{
					$link = 'index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmunarchive';
					$bar->appendButton('Popup', 'unarchive', $alt_archive, $link, 500, 130);
				}

				$alt_delete = "delete";
				if (BwPostmanHelper::canDelete('campaign', 0))
				{
					$link = 'index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmdelete';
					$bar->appendButton('Popup', 'delete', $alt_delete, $link, 500, 150);
				}
				break;
			case "mailinglists":
				if (BwPostmanHelper::canRestore('mailinglist', 0))
				{
					JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
				}

				if (BwPostmanHelper::canDelete('mailinglist', 0))
				{
					JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_ML'), 'archive.delete');
				}
				break;
			case "templates":
				if (BwPostmanHelper::canRestore('template', 0))
				{
					JToolbarHelper::unarchiveList('archive.unarchive', JText::_('COM_BWPOSTMAN_UNARCHIVE'));
				}

				if (BwPostmanHelper::canDelete('template', 0))
				{
					JToolbarHelper::deleteList(JText::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_TPL'), 'archive.delete');
				}
				break;
		}

		JToolbarHelper::spacer();
		JToolbarHelper::divider();
		JToolbarHelper::spacer();

		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('archive');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		$bar->appendButton('extlink', 'users', JText::_('COM_BWPOSTMAN_FORUM'), $forumLink);
		$bar->appendButton('extlink', 'book', JText::_('COM_BWPOSTMAN_MANUAL'), $manualLink);

		JToolbarHelper::spacer();
	}

	/**
	 * Check permission for archive tab and set layout to allowed one, if needed
	 *
	 * @since       2.0.0
	 */
	private function checkForAllowedTabs()
	{
		$uri        = JUri::getInstance('SERVER');
		$uriString = $uri->toString();
		$uriShort  = substr($uriString, strrpos($uriString, '/') + 1, strlen($uriString));

		$layout = $this->extractLayout($uriShort);
		if ($layout == false)
		{
			return false;
		}

		$allowedLayouts = $this->getAllowedLayouts();

		if (!count($allowedLayouts))
		{
			return false;
		}

		if (!in_array($layout, $allowedLayouts))
		{
			$this->request_url = str_replace($layout, $allowedLayouts[0], $uriShort);
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
	 * @since 1.3.2
	 */
	private function extractLayout($uri_string)
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
	 * @return array $allowedLayouts  allowed layouts
	 *
	 * @since 1.3.2
	 */
	private function getAllowedLayouts()
	{
		$allowedLayouts = array();

		// check for allowed layouts
		$allLayouts    = array('newsletter', 'subscriber', 'campaign', 'mailinglist', 'template');
		foreach ($allLayouts as $item)
		{
			$allowedView	= $this->permissions['view'][$item];
			if ($allowedView)
			{
				$allowedLayouts[] = $item . 's';
			}
		}

		return $allowedLayouts;
	}
}
