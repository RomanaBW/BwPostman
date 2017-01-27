<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance view for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
 *
 * @subpackage 	CoverPage
 *
 * @since       1.0.1
 */
class BwPostmanViewMaintenance extends JViewLegacy
{
	/**
	 * property to hold queue entries
	 *
	 * @var boolean   $queueEntries
	 *
	 * @since       1.0.1
	 */
	protected $queueEntries;

	/**
	 * property to hold template object
	 *
	 * @var object  $template
	 *
	 * @since       1.0.1
	 */
	protected $template;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       1.0.1
	 */
	protected $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       1.0.1
	 */
	public $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       1.0.1
	 */
	public $activeFilters;

	/**
	 * property to hold check res
	 *
	 * @var string $check_res
	 *
	 * @since       1.0.1
	 */
	public $check_res;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       1.0.1
	 */
	public $sidebar;

	/**
	 * property to hold total value
	 *
	 * @var object  $total
	 *
	 * @since       1.0.1
	 */
	public $total;

	/**
	 * Execute and display a template script.
	 *
	 * @access	public
	 *
	 * @param	string $tpl Template
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       1.0.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		JHtml::_('bootstrap.framework');
		JHtml::_('jquery.framework');

		if (!BwPostmanHelper::canView('maintenance'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$jinput		= JFactory::getApplication()->input;
		$model		= $this->getModel();
		$layout		= $jinput->getCmd('layout', '');

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->template	= $app->getTemplate();

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title (JText::_('COM_BWPOSTMAN_MAINTENANCE'), 'wrench');

		// Set toolbar items for the page
		if ($layout == 'restoreTables')
		{
			$alt 	= "COM_BWPOSTMAN_BACK";
			$bar	= JToolbar::getInstance('toolbar');
			$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'));
			$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'), 'download');
			$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}

			if ($layout == 'doRestore')
			{
			$alt 	= "COM_BWPOSTMAN_BACK";
			$bar	= JToolbar::getInstance('toolbar');
			$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'));
			$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'), 'download');
			$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}

		if ($layout == 'checkTables')
		{
			JFactory::getApplication()->input->set('hidemainmenu', true);
			$alt 	= "COM_BWPOSTMAN_BACK";
			$bar	= JToolbar::getInstance('toolbar');
			$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'));
			$backlink 	= 'index.php?option=com_bwpostman&view=maintenance';
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'), 'download');
			$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}

		if ($layout == 'updateCheckSave')
		{
			$alt 	= "COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN";
			$bar	= JToolbar::getInstance('toolbar');
			$document->setTitle(JText::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'));
			$backlink 	= 'javascript:window.close()';
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'), 'download');
			$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
			$style	= '.layout-updateCheckSave .navbar {display:none;}'
					. '.layout-updateCheckSave .subhead-fixed {position: relative;top: 0;}'
					. 'body {padding-top:0;}';
			$document->addStyleDeclaration( $style );
			$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/install.css');
		}

		if (BwPostmanHelper::canAdmin())
			JToolbarHelper::preferences('com_bwpostman', '500', '900');
		JToolbarHelper::spacer();
		JToolbarHelper::divider();
		JToolbarHelper::spacer();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'https://www.boldt-webservice.de/en/forum-en/bwpostman.html');
		JToolbarHelper::spacer();

		BwPostmanHelper::addSubmenu('maintenance');

		switch ($layout)
		{
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
				break;
			default:
		}

		if (empty($layout)) $this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}
}
