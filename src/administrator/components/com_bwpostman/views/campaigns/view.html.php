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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\Plugin\PluginHelper;

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');

/**
 * BwPostman Campaigns View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Campaigns
 *
 * @since       0.9.1
 */
class BwPostmanViewCampaigns extends JViewLegacy
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
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

	/**
	 * property to hold total value
	 *
	 * @var object  $total
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

		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['campaign'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_CAMS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		PluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

		// Get data from the model
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->total			= $this->get('total');

		// trigger Plugin BwTimeControl event and get results
//		$this->auto_nbr	= Factory::getApplication()->triggerEvent('onBwPostmanCampaignsPrepare', array (&$this->items));

		BwPostmanHelper::addSubmenu('bwpostman');
		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

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
	protected function addToolbar()
	{

		PluginHelper::importPlugin('bwpostman');

		// Get document object, set document title and add css
		$document	= Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_CAMS'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');
		$document->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_confirm_archive_cam_nls.js');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_CAMS'), 'list');

		// Set toolbar items for the page
		if ($this->permissions['campaign']['create'])
		{
			ToolbarHelper::addNew('campaign.add');
		}

		if (BwPostmanHelper::canEdit('campaign'))
		{
			ToolbarHelper::editList('campaign.edit');
		}

		ToolbarHelper::divider();
		ToolbarHelper::spacer();

		// Special archive button because we need a confirm dialog with 3 options
		if (BwPostmanHelper::canArchive('campaign'))
		{
			$bar = Toolbar::getInstance('toolbar');
			$alt = "COM_BWPOSTMAN_ARC";
			$bar->appendButton(
				'Popup',
				'archive',
				$alt,
				'index.php?option=com_bwpostman&amp;controller=campaigns&amp;tmpl=component&amp;view=campaigns&amp;layout=default_confirmarchive',
				500,
				110
			);
			ToolbarHelper::spacer();
			ToolbarHelper::divider();
			ToolbarHelper::spacer();
		}

		if (BwPostmanHelper::canEdit('campaign', 0) || BwPostmanHelper::canEditState('campaign', 0))
		{
			ToolbarHelper::checkin('campaigns.checkin');
			ToolbarHelper::divider();
		}

		// trigger BwTimeControl event
		Factory::getApplication()->triggerEvent('onBwPostmanCampaignsPrepareToolbar', array());

		$bar = Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('campaigns');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		$bar->appendButton('Extlink', 'users', Text::_('COM_BWPOSTMAN_FORUM'), $forumLink);
		$bar->appendButton('Extlink', 'book', Text::_('COM_BWPOSTMAN_MANUAL'), $manualLink);
	}
}
