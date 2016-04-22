<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all templates view for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman templates View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	templates
 */
class BwPostmanViewTemplates extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.1.0
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();

		if (!BwPostmanHelper::canView('templates')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
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
		}
	}


	/**
	 * Add the page title, submenu and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$jinput	= JFactory::getApplication()->input;
		$layout	= $jinput->getCmd('layout', '');
		$canDo	= BwPostmanHelper::getActions(0, 'template');
		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_TPL'));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		switch ($layout) {
			case 'uploadtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolBar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::title (JText::_('COM_BWPOSTMAN_TPL_UPLOADTPL'), 'upload');
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
				break;
			case 'installtpl':
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolBar::getInstance('toolbar');
				$backlink 	= 'index.php?option=com_bwpostman&view=templates';
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::title (JText::_('COM_BWPOSTMAN_TPL_INSTALLTPL'), 'plus');
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
				break;
			default:
				// Set toolbar title
				JToolBarHelper::title (JText::_('COM_BWPOSTMAN_TPL'), 'picture');

				// Set toolbar items for the page
				if ($canDo->get('bwpm.create'))		JToolBarHelper::custom('template.addhtml', 'calendar', 'HTML', 'COM_BWPOSTMAN_TPL_ADDHTML', false);
				if ($canDo->get('bwpm.create'))		JToolBarHelper::custom('template.addtext', 'new', 'TEXT', 'COM_BWPOSTMAN_TPL_ADDTEXT', false);
				if (($canDo->get('bwpm.edit')) || ($canDo->get('bwpm.edit.own')))	JToolBarHelper::editList('template.edit');

				if ($canDo->get('bwpm.edit.state')) {
					JToolbarHelper::makeDefault('template.setDefault', 'COM_BWPOSTMAN_TPL_SET_DEFAULT');
					JToolBarHelper::publishList('templates.publish');
					JToolBarHelper::unpublishList('templates.unpublish');
				}

				JToolBarHelper::divider();
				JToolBarHelper::spacer();

				if ($canDo->get('bwpm.archive')) {
					JToolBarHelper::archiveList('template.archive');
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
				}
				if ($canDo->get('core.manage')) {
					JToolBarHelper::checkin('templates.checkin');
					JToolBarHelper::divider();
				}
				// templateupload
				if ($canDo->get('bwpm.create')) {
					$bar = JToolBar::getInstance('toolbar');
					JHTML::_( 'behavior.modal' );
					$html = '<a class="btn btn-small" href="'. JURI::root(true) . '/administrator/index.php?option=com_bwpostman&view=templates&layout=uploadtpl" rel="{handler: \'iframe\', size: {x: 850, y: 500}}" ><span class="icon-upload"></span>' .JText::_('COM_BWPOSTMAN_TPL_INSTALLTPL'). '</a>';
					$bar->appendButton( 'Custom', $html );
				}
		}
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolBarHelper::spacer();
	}
}
