<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all mailinglists view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Mailinglists;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Lists View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Mailinglists
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
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public array $permissions;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public object $sidebar;

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

        if (!$this->permissions['view']['mailinglist'])
        {
            $app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_MLS')), 'error');
            $app->redirect('index.php?option=com_bwpostman');
        }

		// Get data from the model
        $model = $this->getModel();
		$this->state			= $model->getState();;
		$this->items			= $model->getItems();;
		$this->filterForm		= $model->getFilterForm();
		$this->activeFilters	= $model->getActiveFilters();;
		$this->pagination		= $model->getPagination();;
		$this->total			= $model->getTotal();

        $this->addToolbar();

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
		// Get the toolbar object instance
		        $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar()->getInstance();

		$this->getDocument()->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_confirm_archive');

        // Set toolbar title
        ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MLS'), 'list');

        // Set toolbar items for the page
        if ($this->permissions['mailinglist']['create'])
        {
            $toolbar->addNew('mailinglist.add');
        }

        if (BwPostmanHelper::canEdit('mailinglist') || BwPostmanHelper::canEditState('mailinglist') || BwPostmanHelper::canArchive('mailinglist'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fa fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if (BwPostmanHelper::canEdit('mailinglist'))
            {
                $childBar->edit('mailinglist.edit')->listCheck(true);
            }


            $childBar->publish('mailinglists.publish')->listCheck(true);
            $childBar->unpublish('mailinglists.unpublish')->listCheck(true);

            if (BwPostmanHelper::canArchive('mailinglist'))
            {
                $childBar->archive('mailinglist.archive')->listCheck(true);
            }

			if (BwPostmanHelper::canEdit('mailinglist', []) || BwPostmanHelper::canEditState('mailinglist'))
			{
				$childBar->checkin('mailinglists.checkin')->listCheck(true);
			}
		}

		$manualButton = BwPostmanHTMLHelper::getManualButton('mailinglists');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

        $toolbar->appendButton($manualButton);
        $toolbar->appendButton($forumButton);
    }
}
