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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Subscribers;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\Component\ComponentHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Subscribers View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
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
	protected array $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       0.9.1
	 */
	protected object $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       0.9.1
	 */
	protected array|object $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       0.9.1
	 */
	public object $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       0.9.1
	 */
	public object $activeFilters;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 *
	 * @since       0.9.1
	 */
	public string $total;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public object $sidebar;

	/**
	 * property to hold mailinglists
	 *
	 * @var array  $mailinglists
	 *
	 * @since       0.9.1
	 */
	public array $mailinglists;

	/**
	 * property to hold params
	 *
	 * @var object  $params
	 *
	 * @since       0.9.1
	 */
	public object $params;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public array $permissions;

	/**
	 * property to hold context
	 *
	 * @var string  $context
	 *
	 * @since       0.9.1
	 */
	public string $context;

	/**
	 * property to hold filtering mailinglist
	 *
	 * @var string  $filterMl
	 *
	 * @since       2.2.0
	 */
	public string $filterMl;

	/**
	 * Array for confirmed subscribers
	 *
	 * @var    array
	 *
	 * @since  3.0.0
	 */
	public array $confirmed;

	/**
	 * Array for unconfirmed subscribers
	 *
	 * @var    array
	 *
	 * @since  3.0.0
	 */
	public array $unconfirmed;

	/**
	 * Array for test recipients
	 *
	 * @var    array
	 *
	 * @since  3.0.0
	 */
	public array $testers;

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
		$app = Factory::getApplication();

		$this->permissions = $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['subscriber'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_SUB')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}


		// Get data from the model
        $model = $this->getModel();
		$this->state			= $model->getState();
		$this->items 			= $model->getItems();
		$this->mailinglists 	= $model->getMailinglists();
		$this->filterForm		= $model->getFilterForm();
		$this->activeFilters	= $model->getActiveFilters();
		$this->pagination		= $model->getPagination();
		$this->total 			= $model->getTotal();
		$this->params           = ComponentHelper::getParams('com_bwpostman');
		$this->context			= 'com_bwpostman.subscribers';
		$this->filterMl         = $this->state->get('filter.mailinglist');

		$this->addToolbar();

		// Show the layout depending on the tab
		$tpl = $app->input->get('tab', '');

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
	 * Add the page title, submenu and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar(): void
    {
		$app = Factory::getApplication();
		$tab = $app->input->get('tab', 'confirmed');

		// Get the toolbar object instance
		        $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar();

		$this->getDocument()->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_subscribers');

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

					// Add a batch button
					if (BwPostmanHelper::canEdit('subscriber'))
					{
						$childBar->popupButton('batch')
							->text('JTOOLBAR_BATCH')
							->selector('collapseModal')
							->listCheck(true);

							if ($tab === 'unconfirmed')
							{
								// Send confirm mail again button
								$childBar->basicButton('sendconfirmmail')
									->task('subscriber.sendconfirmmail')
									->icon('icon-envelope')
									->text('COM_BWPOSTMAN_SUB_SEND_CONFIRMMAIL');
							}
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

		$manualButton = BwPostmanHTMLHelper::getManualButton('subscribers');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
