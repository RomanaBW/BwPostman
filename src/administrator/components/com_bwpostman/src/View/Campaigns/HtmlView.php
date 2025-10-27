<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all campaigns for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Campaigns;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\Plugin\PluginHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Event\Event;
use Joomla\Utilities\ArrayHelper;

/**
 * BwPostman Campaigns View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Campaigns
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
     * property to hold sidebar
     *
     * @var object  $sidebar
     *
     * @since       0.9.1
     */
    public object $sidebar;

    /**
     * property to hold total value
     *
     * @var object  $total
     *
     * @since       0.9.1
     */
    public object $total;

    /**
     * property to hold permissions as array
     *
     * @var array $permissions
     *
     * @since       2.0.0
     */
    public array $permissions;

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

        $this->permissions = $app->getUserState('com_bwpm.permissions', []);

        if (!$this->permissions['view']['campaign'])
        {
            $app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_CAMS')), 'error');
            $app->redirect('index.php?option=com_bwpostman');
        }

        PluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

        // Get data from the model
        $model = $this->getModel();
        $this->state			= $model->getState();
        $this->items			= $model->getItems();
        $this->filterForm		= $model->getFilterForm();
        $this->activeFilters	= $model->getActiveFilters();
        $this->pagination		= $model->getPagination();
        $this->total			= $model->getTotal();

        // trigger Plugin BwTimeControl event and get results
//		$this->auto_nbr	= $app->triggerEvent('onBwPostmanCampaignsPrepare', array (&$this->items));

        $this->addToolbar();

        // Call parent display
        parent::display($tpl);
        return $this;
    }


    /**
     * Add the page title, styles and toolbar.
     *
     * @throws Exception
     *
     * @since       0.9.1
     */
    protected function addToolbar(): void
    {
        PluginHelper::importPlugin('bwpostman');

        // Get the toolbar object instance
                $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar();

        $this->getDocument()->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_confirm_archive_cam_nls');

        // Set toolbar title
        ToolbarHelper::title(Text::_('COM_BWPOSTMAN_CAMS'), 'list');

        // Set toolbar items for the page
        if ($this->permissions['campaign']['create'])
        {
            ToolbarHelper::addNew('campaign.add');
        }

        if (BwPostmanHelper::canEdit('campaign', []) || BwPostmanHelper::canEditState('campaign') || BwPostmanHelper::canArchive('campaign'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fa fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if (BwPostmanHelper::canEdit('campaign'))
            {
                $childBar->edit('campaign.edit')->listCheck(true);
            }

            if (BwPostmanHelper::canEdit('campaign', []) || BwPostmanHelper::canEditState('campaign'))
            {
                $childBar->checkin('campaigns.checkin')->listCheck(true);
            }

            // Special archive button because we need a confirm dialog
            if (BwPostmanHelper::canArchive('campaign'))
            {
                $options['url'] = "index.php?option=com_bwpostman&amp;controller=campaigns&amp;tmpl=component&amp;view=campaigns&amp;layout=default_confirmarchive";
                $options['icon'] = "icon-archive";
                $options['text'] = "COM_BWPOSTMAN_ARC";
                $options['bodyHeight'] = 50;
                $options['name'] = 'archive';

                $button = new PopupButton('archive');
                $button->setOptions($options);

                $childBar->AppendButton($button);
            }
        }

        // trigger BwTimeControl event
        $event = new Event('onBwPostmanCampaignsPrepareToolbar', [
            'subject'  => ArrayHelper::fromObject($this),
        ]);
        Factory::getApplication()->getDispatcher()->dispatch($event->getName(), $event);
        $eventResults = $event->getArgument('result', []);

        $manualButton = BwPostmanHTMLHelper::getManualButton('campaigns');
        $forumButton  = BwPostmanHTMLHelper::getForumButton();

        $toolbar->appendButton($manualButton);
        $toolbar->appendButton($forumButton);
    }
}
