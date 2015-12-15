<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance view for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

// Require helper classes
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman maintenance View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	CoverPage
 */
class BwPostmanViewMaintenance extends JViewLegacy
{
	/**
	 * Display
	 *
	 * @access	public
	 * @param	string Template
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();

		if (!BwPostmanHelper::canView('maintenance')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}
		else {
			$document 	= JFactory::getDocument();
			$jinput		= JFactory::getApplication()->input;
			$model		= $this->getModel();
			$layout		= $jinput->getCmd('layout', '');
//dump ($layout, 'View Layout');
//dump ($tpl, 'View TPL');

			//check for queue entries
			$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

			$this->template	= $app->getTemplate();
			$dest			= $app->getUserState('com_bwpostman.maintenance.dest', '');

			// Get document object, set document title and add css
			$document = JFactory::getDocument();
			$document->setTitle(JText::_('COM_BWPOSTMAN'));
			$document->addStyleSheet(JURI::root(true) . 'components/com_bwpostman/assets/css/bwpostman_backend.css');

			// Set toolbar title
			JToolBarHelper::title (JText::_('COM_BWPOSTMAN_MAINTENANCE'), 'wrench');

			$canDo = BwPostmanHelper::getActions();

			// Set toolbar items for the page
			if ($layout == 'restoreTables') {
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolBar::getInstance('toolbar');
				$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'));
				$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'), 'download');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
			}

				if ($layout == 'doRestore') {
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolBar::getInstance('toolbar');
				$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'));
				$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'), 'download');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
			}

			if ($layout == 'checkTables') {
				$alt 	= "COM_BWPOSTMAN_BACK";
				$bar	= JToolBar::getInstance('toolbar');
				$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'));
				$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'), 'download');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
			}

			if ($layout == 'updateCheckSave') {
				$alt 	= "COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN";
				$bar	= JToolBar::getInstance('toolbar');
				$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'));
				$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'), 'download');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::spacer();
				JToolBarHelper::divider();
				JToolBarHelper::spacer();
			}

			if ($canDo->get('core.manage')) JToolBarHelper::preferences('com_bwpostman', '500', '900');
			JToolBarHelper::spacer();
			JToolBarHelper::divider();
			JToolBarHelper::spacer();
			JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
			JToolBarHelper::spacer();

			BwPostmanHelper::addSubmenu('maintenance');

			switch ($layout) {
				case 'updateCheckSave':
					break;
				case 'checkTables':
					break;
				case 'saveTables':
					$this->check_res	= $model->saveTables(false);
					break;
				case 'restoreTables':
					break;
				case 'doRestore':
					echo '<div class="well">';
					$this->check_res	= $model->restoreTables($dest);
					echo '</div>';
					break;
					default:
			}

			if (empty($layout)) $this->sidebar = JHtmlSidebar::render();

			parent::display($tpl);
		}
	}
}
