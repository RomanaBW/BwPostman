<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all templates view for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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
 * BwPostman templates View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	templates
 *
 * @since       1.1.0
 */
class BwPostmanViewTemplates extends JViewLegacy
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 *
	 * @since       1.1.0
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       1.1.0
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       1.1.0
	 */
	protected $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       1.1.0
	 */
	public $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       1.1.0
	 */
	public $activeFilters;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 *
	 * @since       1.1.0
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
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       1.1.0
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
	 * @since   1.1.0
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['template'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Get data from the model
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->total			= $this->get('total');

		$this->addToolbar();

		BwPostmanHelper::addSubmenu('templates');

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
	 * @since       1.1.0
	 */
	protected function addToolbar()
	{
		$jinput	= JFactory::getApplication()->input;
		$layout	= $jinput->getCmd('layout', '');
		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_TPLS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		switch ($layout)
		{
			case 'uploadtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolbar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolbarHelper::title(JText::_('COM_BWPOSTMAN_TPL_UPLOADTPL'), 'upload');
				JToolbarHelper::spacer();
				JToolbarHelper::divider();
				JToolbarHelper::spacer();
				break;
			case 'installtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolbar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolbarHelper::title(JText::_('COM_BWPOSTMAN_TPL_INSTALLTPL'), 'plus');
				JToolbarHelper::spacer();
				JToolbarHelper::divider();
				JToolbarHelper::spacer();
				break;
			default:
				// Set toolbar title
				JToolbarHelper::title(JText::_('COM_BWPOSTMAN_TPLS'), 'picture');

				// Set toolbar items for the page
				if ($this->permissions['template']['create'])
				{
					JToolbarHelper::custom('template.addhtml', 'calendar', 'HTML', 'COM_BWPOSTMAN_TPL_ADDHTML', false);
				}

				if ($this->permissions['template']['create'])
				{
					JToolbarHelper::custom('template.addtext', 'new', 'TEXT', 'COM_BWPOSTMAN_TPL_ADDTEXT', false);
				}

				if (BwPostmanHelper::canEdit('template'))
				{
					JToolbarHelper::editList('template.edit');
				}

				if (BwPostmanHelper::canEditState('template'))
				{
					JToolbarHelper::makeDefault('template.setDefault', 'COM_BWPOSTMAN_TPL_SET_DEFAULT');
					JToolbarHelper::publishList('templates.publish');
					JToolbarHelper::unpublishList('templates.unpublish');
				}

				JToolbarHelper::divider();
				JToolbarHelper::spacer();

				if (BwPostmanHelper::canArchive('template'))
				{
					JToolbarHelper::archiveList('template.archive');
					JToolbarHelper::divider();
					JToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canEdit('template', 0) || BwPostmanHelper::canEditState('template', 0))
				{
					JToolbarHelper::checkin('templates.checkin');
					JToolbarHelper::divider();
				}

				// template upload
				if (BwPostmanHelper::canAdd('template'))
				{
					$bar = JToolbar::getInstance('toolbar');
					JHtml::_('behavior.modal');
					$html = '<a class="btn btn-small" href="' . JUri::root(true) .
								'/administrator/index.php?option=com_bwpostman&view=templates&layout=uploadtpl" 
									rel="{handler: \'iframe\', size: {x: 850, y: 500}, iframeOptions: {id: \'uploadFrame\'}}" >
								<span class="icon-upload"></span>' . JText::_('COM_BWPOSTMAN_TPL_INSTALLTPL') .
							'</a>';
					$bar->appendButton('Custom', $html);

				JToolbarHelper::custom('templates.exportTpl', 'download', '', 'COM_BWPOSTMAN_TPL_EXPORTTPL', true);
				JToolbarHelper::divider();
				JToolbarHelper::spacer();
				}
		}

		$link   = BwPostmanHTMLHelper::getForumLink();

		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, $link);

		JToolbarHelper::spacer();
	}
}
