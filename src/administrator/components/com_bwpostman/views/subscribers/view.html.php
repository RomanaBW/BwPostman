<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers view for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');


/**
 * BwPostman Subscribers View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class BwPostmanViewSubscribers extends JViewLegacy
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
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

	/**
	 * property to hold mailinglists
	 *
	 * @var array  $mailinglists
	 *
	 * @since       0.9.1
	 */
	public $mailinglists;

	/**
	 * property to hold params
	 *
	 * @var object  $params
	 *
	 * @since       0.9.1
	 */
	public $params;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold context
	 *
	 * @var string  $context
	 *
	 * @since       0.9.1
	 */
	public $context;

	/**
	 * property to hold filtering mailinglist
	 *
	 * @var string  $filterMl
	 *
	 * @since       2.2.0
	 */
	public $filterMl;

	/**
	 * Array for confirmed subscribers
	 *
	 * @var    array
	 *
	 * @since  2.4.0
	 */
	public $confirmed;

	/**
	 * Array for unconfirmed subscribers
	 *
	 * @var    array
	 *
	 * @since  2.4.0
	 */
	public $unconfirmed;

	/**
	 * Array for test recipients
	 *
	 * @var    array
	 *
	 * @since  2.4.0
	 */
	public $testers;

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
		$app	= Factory::getApplication();

		$this->permissions		= Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['subscriber'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_SUB')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}


		// Get data from the model
		$this->state			= $this->get('State');
		$this->items 			= $this->get('Items');
		$this->mailinglists 	= $this->get('Mailinglists');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->total 			= $this->get('total');
		$this->params           = ComponentHelper::getParams('com_bwpostman');
		$this->context			= 'com_bwpostman.subscribers';
		$this->filterMl         = $this->state->get('filter.mailinglist');

		if(version_compare(JVERSION, '3.999.999', 'le'))
		{
			$this->addToolbarLegacy();
			BwPostmanHelper::addSubmenu('bwpostman');

			$this->sidebar = JHtmlSidebar::render();
		}
		else
		{
			$this->addToolbar();
		}

		// Show the layout depending on the tab
		$tpl = Factory::getApplication()->input->get('tab', '');

		if ($tpl === '')
		{
			$tpl = $app->getUserState('com_bwpostman.subscribers.layout', 'confirmed');
		}

		$app->setUserState('com_bwpostman.subscribers.layout', $tpl);

		// Call parent display
		parent::display($tpl);
		return $this;
	}

	/**
	 * Add the page title and toolbar for Joomla 4.
	 *
	 * @throws Exception
	 *
	 * @since       2.4.0
	 */
	protected function addToolbar()
	{
		$tab	= Factory::getApplication()->input->get('tab', 'confirmed');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_SUBS'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB'), 'users');

		// Set toolbar items for the page
		switch ($tab)
		{ // The layout-variable tells us which tab we are in
			default:
			case "confirmed":
			case "unconfirmed":
				if ($this->permissions['subscriber']['create'])
				{
					$toolbar->addNew('subscriber.add');
				}

				if (BwPostmanHelper::canArchive('subscriber') || $this->permissions['subscriber']['create'] || BwPostmanHelper::canEdit('subscriber') || BwPostmanHelper::canEditState('subscriber'))
				{
					$dropdown = $toolbar->dropdownButton('status-group')
						->text('JTOOLBAR_CHANGE_STATUS')
						->toggleSplit(false)
						->icon('fa fa-ellipsis-h')
						->buttonClass('btn btn-action')
						->listCheck(true);

					$childBar = $dropdown->getChildToolbar();

					if (BwPostmanHelper::canEdit('subscriber'))
					{
						$childBar->edit('subscriber.edit')->listCheck(true);
					}

					if (BwPostmanHelper::canArchive('subscriber'))
					{
						$childBar->archive('subscriber.archive')->listCheck(true);
					}

					if (BwPostmanHelper::canEdit('subscriber') || BwPostmanHelper::canEditState('subscriber'))
					{
						$childBar->checkin('subscribers.checkin')->listCheck(true);
					}
				}

				if ($this->permissions['subscriber']['create'])
				{
					ToolbarHelper::custom('subscribers.importSubscribers', 'download', 'import_f2', 'COM_BWPOSTMAN_SUB_IMPORT', false);
				}

				if ($this->permissions['subscriber']['edit'])
				{
					if ($this->filterMl !== '')
					{
						// Get popup with yes/no buttons
						$options['url'] = "index.php?option=com_bwpostman&view=subscribers&format=raw&layout=default_filteredexport";
						$options['icon'] = "icon-upload";
						$options['text'] = "COM_BWPOSTMAN_SUB_EXPORT";
						$options['bodyHeight'] = 50;
						$options['name'] = 'upload';

						$button = new PopupButton('upload');
						$button->setOptions($options);

						$toolbar->AppendButton($button);
					}
					else
					{
						ToolbarHelper::custom('subscribers.exportSubscribers', 'upload', 'export_f2', 'COM_BWPOSTMAN_SUB_EXPORT', false);
					}
				}
				break;

			case "testrecipients":
				if ($this->permissions['subscriber']['create'])
				{
					$toolbar->addNew('subscriber.add_test');
				}

				$dropdown = $toolbar->dropdownButton('status-group')
					->text('JTOOLBAR_CHANGE_STATUS')
					->toggleSplit(false)
					->icon('fa fa-ellipsis-h')
					->buttonClass('btn btn-action')
					->listCheck(true);

				$childBar = $dropdown->getChildToolbar();

				if (BwPostmanHelper::canEdit('subscriber'))
				{
					$childBar->edit('subscriber.edit')->listCheck(true);
				}

				if (BwPostmanHelper::canArchive('subscriber'))
				{
					$childBar->archive('subscriber.archive')->listCheck(true);
				}
				break;
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('subscribers');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}

	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function addToolbarLegacy()
	{
		$app	= Factory::getApplication();
		$tab	= $app->getUserState($this->context . '.tab', 'confirmed');

		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_SUB'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB'), 'users');

		// Set toolbar items for the page
		switch ($tab)
		{ // The layout-variable tells us which tab we are in
			default:
			case "confirmed":
			case "unconfirmed":
				if ($this->permissions['subscriber']['create'])
				{
					ToolbarHelper::addNew('subscriber.add');
				}

				if (BwPostmanHelper::canEdit('subscriber'))
				{
					ToolbarHelper::editList('subscriber.edit');
				}

				ToolbarHelper::spacer();
				ToolbarHelper::divider();
				ToolbarHelper::spacer();

				if ($this->permissions['subscriber']['create'])
				{
					ToolbarHelper::custom('subscribers.importSubscribers', 'download', 'import_f2', 'COM_BWPOSTMAN_SUB_IMPORT', false);
				}

				if ($this->permissions['subscriber']['edit'])
				{
					if ($this->filterMl !== '')
					{
						// Get popup with yes/no buttons
						$bar = Toolbar::getInstance('toolbar');
						$alt_export = Text::_('COM_BWPOSTMAN_SUB_EXPORT');
						$link = 'index.php?option=com_bwpostman&view=subscribers&format=raw&layout=default_filteredexport';
						$bar->appendButton('Popup', 'upload', $alt_export, $link, 500, 130);
					}
					else
					{
						ToolbarHelper::custom('subscribers.exportSubscribers', 'upload', 'export_f2', 'COM_BWPOSTMAN_SUB_EXPORT', false);
					}
				}

				if (BwPostmanHelper::canArchive('subscriber'))
				{
					ToolbarHelper::divider();
					ToolbarHelper::spacer();
					ToolbarHelper::archiveList('subscriber.archive');
				}

				// Add a batch button
				if ($this->permissions['subscriber']['create'] || BwPostmanHelper::canEdit('subscriber'))
				{
					HTMLHelper::_('bootstrap.modal', 'collapseModal');
					$title = Text::_('JTOOLBAR_BATCH');

					// Instantiate a new JLayoutFile instance and render the batch button
					$layout = new JLayoutFile('joomla.toolbar.batch');

					$dhtml = $layout->render(array('title' => $title));
					$bar->appendButton('Custom', $dhtml, 'batch');
				}
				break;
			case "testrecipients":
				if ($this->permissions['subscriber']['create'])
				{
					ToolbarHelper::addNew('subscriber.add_test');
				}

				if (BwPostmanHelper::canEdit('subscriber'))
				{
					ToolbarHelper::editList('subscriber.edit');
				}

				ToolbarHelper::spacer();
				ToolbarHelper::divider();
				if (BwPostmanHelper::canArchive('subscriber'))
				{
					ToolbarHelper::archiveList('subscriber.archive');
				}
				break;
		}

		ToolbarHelper::divider();
		ToolbarHelper::spacer();
		if (BwPostmanHelper::canEdit('subscriber') || BwPostmanHelper::canEditState('subscriber'))
		{
			ToolbarHelper::checkin('subscribers.checkin');
			ToolbarHelper::divider();
		}

		$bar = Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('subscribers');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		$bar->appendButton('Extlink', 'users', Text::_('COM_BWPOSTMAN_FORUM'), $forumLink);
		$bar->appendButton('Extlink', 'book', Text::_('COM_BWPOSTMAN_MANUAL'), $manualLink);
	}
}
