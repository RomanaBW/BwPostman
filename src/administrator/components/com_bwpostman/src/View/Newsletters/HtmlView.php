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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Newsletters;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Newsletters View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
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
	 * @return  HtmlView  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null): HtmlView
	{
		$app	= Factory::getApplication();

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['newsletter'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$jinput		= $app->input;
		$uri		= Uri::getInstance();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$app->setUserState('com_bwpostman.edit.newsletter.referrer', 'Newsletters');
		// The query always contains the tab which we are in, but this might be confusing
		// That's why we will set the query only to controller = newsletters
		$uri_query	= 'option=com_bwpostman&view=newsletters';
		$uri->setQuery($uri_query);

		// Get data from the model
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->filterForm		= $this->getModel()->getFilterForm();
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->queuePagination	= $this->get('QueuePagination');
		$this->total 			= $this->get('total');
		$this->count_queue		= $this->get('CountQueue');
		$this->context			= 'com_bwpostman.newsletters';

		$this->addToolbar();

		// Show the layout depending on the tab
		$tpl = $jinput->get('tab', 'unsent');

		if ($tpl === 'queue' && (int)$this->count_queue === 0)
		{
			$tpl = 'unsent';
		}

		$app->setUserState('com_bwpostman.newsletters.layout', $tpl);

		// Call parent display
		parent::display($tpl);
		return $this;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		$tab	= $this->state->get('tab', 'unsent');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		$wa = $this->document->getWebAssetManager();
		$wa->useScript('com_bwpostman.admin-bwpm_nls');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_NLS'), 'envelope');

		// Set toolbar items for the page

		switch ($tab)
		{ // The layout-variable tells us which tab we are in
			case "sent":
				if (BwPostmanHelper::canArchive('newsletter') || BwPostmanHelper::canEdit('newsletter') || BwPostmanHelper::canEditState('newsletter'))
				{
					$dropdown = $toolbar->dropdownButton('status-group')
						->text('JTOOLBAR_CHANGE_STATUS')
						->toggleSplit(false)
						->icon('fa fa-ellipsis-h')
						->buttonClass('btn btn-action')
						->listCheck(true);

					$childBar = $dropdown->getChildToolbar();

					if (BwPostmanHelper::canEdit('newsletter'))
					{
						$childBar->edit('newsletter.edit')->listCheck(true);
					}

					if (BwPostmanHelper::canEditState('newsletter'))
					{
						$childBar->publish('newsletters.publish')->listCheck(true);
						$childBar->unpublish('newsletters.unpublish')->listCheck(true);
					}

					if (BwPostmanHelper::canEdit('newsletter', 0) || BwPostmanHelper::canEditState('newsletter'))
					{
						$childBar->checkin('newsletters.checkin')->listCheck(true);
					}

					if (BwPostmanHelper::canArchive('newsletter'))
					{
						$childBar->archive('newsletter.archive')->listCheck(true);
					}
				}

				if ($this->permissions['newsletter']['create'])
				{
					ToolbarHelper::custom('newsletter.copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}

				break;
			case "queue":
				if ($this->permissions['newsletter']['send'])
				{
					ToolbarHelper::custom(
						'newsletters.resetSendAttempts',
						'checkin.png',
						'unpublish_f2.png',
						'COM_BWPOSTMAN_NL_RESET_TRIAL',
						false
					);
					$url = "index.php?option=com_bwpostman&view=newsletter&task=startsending&layout=nl_send";
					$icon = "envelope";
					$text = "COM_BWPOSTMAN_NL_CONTINUE_SENDING";
					$toolbar->AppendButton('Link', $icon, $text, $url);

					ToolbarHelper::custom('newsletters.clear_queue', 'trash.png', 'delete_f2.png', 'COM_BWPOSTMAN_NL_CLEAR_QUEUE', false);
				}
				break;
			case "unsent":
			default:
				if ($this->permissions['newsletter']['create'])
				{
					ToolbarHelper::addNew('newsletter.add');
				}

				if (BwPostmanHelper::canArchive('newsletter') || BwPostmanHelper::canEdit('newsletter') || BwPostmanHelper::canEditState('newsletter'))
				{
					$dropdown = $toolbar->dropdownButton('status-group')
						->text('JTOOLBAR_CHANGE_STATUS')
						->toggleSplit(false)
						->icon('fa fa-ellipsis-h')
						->buttonClass('btn btn-action')
						->listCheck(true);

					$childBar = $dropdown->getChildToolbar();

					if (BwPostmanHelper::canEdit('newsletter'))
					{
						$childBar->edit('newsletter.edit')->listCheck(true);
					}

					if (BwPostmanHelper::canEdit('newsletter', 0) || BwPostmanHelper::canEditState('newsletter'))
					{
						$childBar->checkin('newsletters.checkin')->listCheck(true);
					}

					if (BwPostmanHelper::canArchive('newsletter'))
					{
						$childBar->archive('newsletter.archive')->listCheck(true);
					}

					if ($this->permissions['newsletter']['create'])
					{
						$html = '<joomla-toolbar-button id="status-group-children-duplicate" task="newsletter.copy" list-selection="">';
						$html .= '<button class="button-duplicate dropdown-item" type="button">';
						$html .= '<span class="icon-copy" aria-hidden="true"></span>';
						$html .= Text::_('JTOOLBAR_DUPLICATE');
						$html .= '</button>';
						$html .= '</joomla-toolbar-button>';

						$childBar->appendButton('Custom', $html);
					}

					if ($this->permissions['newsletter']['send'])
					{
						$html = '<joomla-toolbar-button id="status-group-children-send" task="newsletter.sendOut" list-selection="">';
						$html .= '<button class="button-send dropdown-item" type="button">';
						$html .= '<span class="icon-envelope" aria-hidden="true"></span>';
						$html .= Text::_('COM_BWPOSTMAN_NL_SEND');
						$html .= '</button>';
						$html .= '</joomla-toolbar-button>';

						$childBar->appendButton('Custom', $html);
					}
				}
				break;
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('newsletters');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
