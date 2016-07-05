<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all campaigns for backend.
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
 * BwPostman Campaigns View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */
class BwPostmanViewCampaigns extends JViewLegacy
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 */
	protected $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 */
	public $filterForm;

	/**
	 * property to hold sactive filters
	 *
	 * @var object  $activeFilters
	 */
	public $activeFilters;

	/**
	 * property to hold auto nbr
	 *
	 * @var string $auto_nbr
	 */
	public $auto_nbr;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 */
	public $sidebar;

	/**
	 * property to hold total value
	 *
	 * @var object  $total
	 */
	public $total;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		if (!BwPostmanHelper::canView('campaigns'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_CAMS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else
		{
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

			// Get data from the model
			$this->state			= $this->get('State');
			$this->items			= $this->get('Items');
			$this->filterForm		= $this->get('FilterForm');
			$this->activeFilters	= $this->get('ActiveFilters');
			$this->pagination		= $this->get('Pagination');
			$this->total			= $this->get('total');
			$this->auto_nbr			= 0;

			// trigger Plugin BwTimeControl event and get results
			$this->auto_nbr	= $dispatcher->trigger('onBwPostmanCampaignsPrepare', array (&$this->items));

			$this->addToolbar();

			BwPostmanHelper::addSubmenu('campaigns');

			$this->sidebar = JHtmlSidebar::render();

			// Call parent display
			parent::display($tpl);
		}
	}


	/**
	 * Add the page title, submenu and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$canDo	= BwPostmanHelper::getActions(0, 'campaign');

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');

		// Get document object, set document title and add css
		$document	= JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_CAMS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title (JText::_('COM_BWPOSTMAN_CAMS'), 'list');

		// Set toolbar items for the page
		if ($canDo->get('bwpm.create'))
			JToolbarHelper::addNew('campaign.add');
		if (($canDo->get('bwpm.edit')) || ($canDo->get('bwpm.edit.own')))
			JToolbarHelper::editList('campaign.edit');
		JToolbarHelper::divider();
		JToolbarHelper::spacer();

		// Special archive button because we need a confirm dialog with 3 options
		if ($canDo->get('bwpm.archive'))
		{
			$bar= JToolbar::getInstance('toolbar');
			$alt = "COM_BWPOSTMAN_ARC";
			$bar->appendButton('Popup', 'archive', $alt, 'index.php?option=com_bwpostman&amp;controller=campaigns&amp;tmpl=component&amp;view=campaigns&amp;layout=default_confirmarchive', 500, 110);
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}
		if ($canDo->get('core.manage'))
		{
			JToolbarHelper::checkin('campaigns.checkin');
			JToolbarHelper::divider();
		}

		// trigger BwTimeControl event
		$dispatcher->trigger('onBwPostmanCampaignsPrepareToolbar', array($canDo));

		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolbarHelper::spacer();
	}
}
