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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Button\LinkButton;
use Joomla\CMS\Toolbar\Button\CustomButton;

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
		$app		= Factory::getApplication();

		$this->permissions		= Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['template'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Template export
		$jinput	= $app->input;
		$task = $jinput->get('task', NULL);
		if ($task == 'export')
		{
			$basename		= $this->get('BaseName');
			$zip_created	= $this->get('ExportTpl');
		}
		if (isset($zip_created))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_TPL_EXPORTTPL_OK', Route::_(Uri::root() . 'images/bw_postman/templates/' . $basename) , Text::_('COM_BWPOSTMAN_TPL_DOWNLOAD'), Text::_('JCANCEL')), 'message');
			$app->redirect('index.php?option=com_bwpostman&view=templates', false);
		}

		// Get data from the model
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->total			= $this->get('total');

		if(version_compare(JVERSION, '3.999.999', 'le'))
		{
			BwPostmanHelper::addSubmenu('bwpostman');
			$this->addToolbarLegacy();
		}
		else
		{
			$this->addToolbar();
		}

		$this->sidebar = JHtmlSidebar::render();

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
		$jinput	= Factory::getApplication()->input;
		$layout	= $jinput->getCmd('layout', '');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_TPLS'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');
		$document->addScript(JUri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_templates.js');

		$options['name'] = 'back';
		$options['url'] = 'index.php?option=com_bwpostman&view=templates';
		$options['text'] = "COM_BWPOSTMAN_BACK";
		$options['icon'] = "icon-arrow-left";

		switch ($layout)
		{
			case 'uploadtpl':
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPL_UPLOADTPL'), 'upload');

				$button = new LinkButton('back');
				$button->setOptions($options);

				$toolbar->AppendButton($button);
				break;
			case 'installtpl':
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPL_INSTALLTPL'), 'plus');

				$button = new LinkButton('back');
				$button->setOptions($options);

				$toolbar->AppendButton($button);
				break;
			default:
				// Set toolbar title
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPLS'), 'picture');

				// Set toolbar items for the page
				if ($this->permissions['template']['create'])
				{
					ToolbarHelper::custom('template.addhtml', 'calendar', 'HTML', 'COM_BWPOSTMAN_TPL_ADDHTML', false);
				}

				if ($this->permissions['template']['create'])
				{
					ToolbarHelper::custom('template.addtext', 'new', 'TEXT', 'COM_BWPOSTMAN_TPL_ADDTEXT', false);
				}

				if (BwPostmanHelper::canEdit('template', 0) || BwPostmanHelper::canEditState('template', 0) || BwPostmanHelper::canArchive('template'))
				{
					$dropdown = $toolbar->dropdownButton('status-group')
						->text('JTOOLBAR_CHANGE_STATUS')
						->toggleSplit(false)
						->icon('fa fa-ellipsis-h')
						->buttonClass('btn btn-action')
						->listCheck(true);

					$childBar = $dropdown->getChildToolbar();

					if (BwPostmanHelper::canEdit('template'))
					{
						$childBar->edit('template.edit')->listCheck(true);
					}

					if (BwPostmanHelper::canEditState('template'))
					{
						$childBar->publish('templates.publish')->listCheck(true);
						$childBar->unpublish('templates.unpublish')->listCheck(true);
						$childBar->makeDefault('template.setDefault', 'COM_BWPOSTMAN_TPL_SET_DEFAULT')->listCheck(true);
					}

					if (BwPostmanHelper::canArchive('template'))
					{
						$childBar->archive('template.archive')->listCheck(true);
					}

					if (BwPostmanHelper::canEdit('template', 0) || BwPostmanHelper::canEditState('template', 0))
					{
						$childBar->checkin('templates.checkin')->listCheck(true);
					}

					// template upload and export
					if (BwPostmanHelper::canAdd('template'))
					{
						$html = '<joomla-toolbar-button id="status-group-children-export" task="templates.exportTpl" list-selection="">';
						$html .= '<button class="button-download dropdown-item" type="button">';
						$html .= '<span class="icon-download" aria-hidden="true"></span>';
						$html .= Text::_('COM_BWPOSTMAN_TPL_EXPORTTPL');
						$html .= '</button>';
						$html .= '</joomla-toolbar-button>';

						$options['text'] = "COM_BWPOSTMAN_TPL_INSTALLTPL";
						$options['html'] = $html;

						$button = new CustomButton('upload');
						$button->setOptions($options);

						$childBar->AppendButton($button);
					}
				}

				// template upload
				if (BwPostmanHelper::canAdd('template'))
				{
					$installLink = Route::_('index.php?option=com_bwpostman&view=templates&layout=uploadtpl');
					$html = '<joomla-toolbar-button id="toolbar-upload">';
					$html .= '<a id="toolbar-install-template" class="button-upload btn btn-sm btn-primary" href="' . $installLink . '" rel="{handler: \'iframe\', size: {x: 850, y: 500}, iframeOptions: {id: \'uploadFrame\'}}">';
					$html .= '<span class="icon-upload"></span>';
					$html .= Text::_('COM_BWPOSTMAN_TPL_INSTALLTPL');
					$html .= '</a>';
					$html .= '</joomla-toolbar-button>';

					$options['text'] = "COM_BWPOSTMAN_TPL_INSTALLTPL";
					$options['html'] = $html;

					$button = new CustomButton('upload');
					$button->setOptions($options);

					$toolbar->AppendButton($button);
				}
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('templates');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}

	/**
	 * Add the page title, submenu and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	protected function addToolbarLegacy()
	{
		$jinput	= Factory::getApplication()->input;
		$layout	= $jinput->getCmd('layout', '');
		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_TPLS'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');
		$document->addScript(JUri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_templates.js');

		switch ($layout)
		{
			case 'uploadtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= Toolbar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPL_UPLOADTPL'), 'upload');
				ToolbarHelper::spacer();
				ToolbarHelper::divider();
				ToolbarHelper::spacer();
				break;
			case 'installtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= Toolbar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPL_INSTALLTPL'), 'plus');
				ToolbarHelper::spacer();
				ToolbarHelper::divider();
				ToolbarHelper::spacer();
				break;
			default:
				// Set toolbar title
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPLS'), 'picture');

				// Set toolbar items for the page
				if ($this->permissions['template']['create'])
				{
					ToolbarHelper::custom('template.addhtml', 'calendar', 'HTML', 'COM_BWPOSTMAN_TPL_ADDHTML', false);
				}

				if ($this->permissions['template']['create'])
				{
					ToolbarHelper::custom('template.addtext', 'new', 'TEXT', 'COM_BWPOSTMAN_TPL_ADDTEXT', false);
				}

				if (BwPostmanHelper::canEdit('template'))
				{
					ToolbarHelper::editList('template.edit');
				}

				if (BwPostmanHelper::canEditState('template'))
				{
					ToolbarHelper::makeDefault('template.setDefault', 'COM_BWPOSTMAN_TPL_SET_DEFAULT');
					ToolbarHelper::publishList('templates.publish');
					ToolbarHelper::unpublishList('templates.unpublish');
				}

				ToolbarHelper::divider();
				ToolbarHelper::spacer();

				if (BwPostmanHelper::canArchive('template'))
				{
					ToolbarHelper::archiveList('template.archive');
					ToolbarHelper::divider();
					ToolbarHelper::spacer();
				}

				if (BwPostmanHelper::canEdit('template', 0) || BwPostmanHelper::canEditState('template', 0))
				{
					ToolbarHelper::checkin('templates.checkin');
					ToolbarHelper::divider();
				}

				// template upload
				if (BwPostmanHelper::canAdd('template'))
				{
					$bar = Toolbar::getInstance('toolbar');
					$html = '<a id="toolbar-install-template" class="btn btn-small" href="' . Uri::root(true) .
						'/administrator/index.php?option=com_bwpostman&view=templates&layout=uploadtpl"
								rel="{handler: \'iframe\', size: {x: 850, y: 500}, iframeOptions: {id: \'uploadFrame\'}}" >
							<span class="icon-download"></span>' . Text::_('COM_BWPOSTMAN_TPL_INSTALLTPL') .
						'</a>';

					$bar->appendButton('Custom', $html);

					ToolbarHelper::custom('templates.exportTpl', 'download', '', 'COM_BWPOSTMAN_TPL_EXPORTTPL', true);
					ToolbarHelper::divider();
					ToolbarHelper::spacer();
				}
		}

		$bar = Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('templates');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		$bar->appendButton('Extlink', 'users', Text::_('COM_BWPOSTMAN_FORUM'), $forumLink);
		$bar->appendButton('Extlink', 'book', Text::_('COM_BWPOSTMAN_MANUAL'), $manualLink);
	}
}
