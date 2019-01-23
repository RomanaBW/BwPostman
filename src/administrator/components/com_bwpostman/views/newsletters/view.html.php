<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters view for backend.
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
 * BwPostman Newsletters View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanViewNewsletters extends JViewLegacy
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
	 * property to hold pagination object for queue
	 *
	 * @var object  $pagination
	 *
	 * @since       0.9.1
	 */
	protected $queuePagination;

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
	 * property to hold queue entries property
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       0.9.1
	 */
	public $queueEntries;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 *
	 * @since       0.9.1
	 */
	public $total;

	/**
	 * property to hold count queue
	 *
	 * @var string $count_queue
	 *
	 * @since       0.9.1
	 */
	public $count_queue;

	/**
	 * property to hold context
	 *
	 * @var string $context
	 *
	 * @since       0.9.1
	 */
	public $context;

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

		if (!$this->permissions['view']['newsletter'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$jinput		= JFactory::getApplication()->input;
		$uri		= JUri::getInstance();

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
		$this->pagination		= $this->get('Pagination');
		$this->queuePagination	= $this->get('QueuePagination');
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
		return $this;
	}

	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		$tab	= $this->state->get('tab', 'unsent');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_NLS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Add Javascript to make squeezebox close-button invisible
		$document->addScriptDeclaration('
			window.dispButton = function() {
				document.getElementById("sbox-btn-close").style.display = "none";
			}
		');

		// Set toolbar title
		JToolbarHelper::title(JText::_('COM_BWPOSTMAN_NLS'), 'envelope');

		// Set toolbar items for the page

		switch ($tab)
		{ // The layout-variable tells us which tab we are in
			case "sent":
				if (BwPostmanHelper::canEdit('newsletter'))
				{
					JToolbarHelper::editList('newsletter.edit');
				}

				if (BwPostmanHelper::canEditState('newsletter'))
				{
					JToolbarHelper::publishList('newsletters.publish');
					JToolbarHelper::unpublishList('newsletters.unpublish');
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if ($this->permissions['newsletter']['create'])
				{
					JToolbarHelper::custom('newsletter.copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canEdit('newsletter', 0) || BwPostmanHelper::canEditState('newsletter', 0)) {
					JToolbarHelper::checkin('newsletters.checkin');
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canArchive('newsletter'))
				{
					JToolbarHelper::archiveList('newsletter.archive');
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}
				break;
			case "queue":
				$bar = JToolbar::getInstance('toolbar');
				$alt = "COM_BWPOSTMAN_NL_CONTINUE_SENDING";
				if ($this->permissions['newsletter']['send'])
				{
					JToolbarHelper::custom(
						'newsletters.resetSendAttempts',
						'unpublish.png',
						'unpublish_f2.png',
						'COM_BWPOSTMAN_NL_RESET_TRIAL',
						false
					);
					$bar->appendButton(
						'Popup',
						'envelope',
						$alt,
						'index.php?option=com_bwpostman&view=newsletter&layout=queue_modal&format=raw&task=continue_sending',
						600,
						600
					);
					JToolbarHelper::custom('newsletters.clear_queue', 'delete.png', 'delete_f2.png', 'COM_BWPOSTMAN_NL_CLEAR_QUEUE', false);
				}
				break;
			case "unsent":
			default:
				if ($this->permissions['newsletter']['create'])
				{
					JToolbarHelper::addNew('newsletter.add');
				}

				if (BwPostmanHelper::canEdit('newsletter'))
				{
					JToolbarHelper::editList('newsletter.edit');
				}

				if ($this->permissions['newsletter']['create'])
				{
					JToolbarHelper::custom('newsletter.copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}

				JToolbarHelper::divider();
				JToolbarHelper::spacer();

				if ($this->permissions['newsletter']['send'])
				{
					JToolbarHelper::custom('newsletter.sendOut', 'envelope', 'send_f2.png', 'COM_BWPOSTMAN_NL_SEND', true);
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canArchive('newsletter'))
				{
					JToolbarHelper::archiveList('newsletter.archive');
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canEdit('newsletter', 0) || BwPostmanHelper::canEditState('newsletter', 0))
				{
					JToolbarHelper::checkin('newsletters.checkin');
					JToolbarHelper::divider();
				}
				break;
		}

		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('newsletters');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		$bar->appendButton('extlink', 'users', JText::_('COM_BWPOSTMAN_FORUM'), $forumLink);
		$bar->appendButton('extlink', 'book', JText::_('COM_BWPOSTMAN_MANUAL'), $manualLink);

		JToolbarHelper::spacer();
	}
}
