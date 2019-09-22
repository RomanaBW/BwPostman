<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance view for backend.
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

//JHtml::_('jquery.framework', true, null, true);

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper classes
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');

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
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

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
	 * @param	string $tpl Template
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		JHtml::_('bootstrap.framework');
		JHtml::_('jquery.framework');

		JPluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['maintenance'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$jinput		= JFactory::getApplication()->input;
		$model		= $this->getModel();
		$layout		= $jinput->getCmd('layout', '');

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		if (JPluginHelper::isEnabled('bwpostman', 'bwtimecontrol'))
		{
			require_once JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers/phpcron.php';
			$cron = new BwPostmanPhpCron;

			// Check for start file
			if (JFile::exists(JPATH_PLUGINS . $cron->startFile))
			{
				$url = 'index.php?option=' . $jinput->getCmd('option', 'com_bwpostman') . '&view=maintenance';
				echo '<meta http-equiv="refresh" content="10; URL=' . $url . '">';

				$app->enqueueMessage(JText::_('PLG_BWTIMECONTROL_MAINTENANCE_STARTING_CRON'), 'Info');
			}

			// Check for started file
			if (JFile::exists(JPATH_PLUGINS . $cron->startedFile))
			{
				$app->enqueueMessage(JText::_('PLG_BWTIMECONTROL_MAINTENANCE_CRON_STARTED'), 'Info');
			}

			// Check for stop file
			if (JFile::exists(JPATH_PLUGINS . $cron->stopFile))
			{
				$url = 'index.php?option=' . $jinput->getCmd('option', 'com_bwpostman') . '&view=maintenance';
				echo '<meta http-equiv="refresh" content="10; URL=' . $url . '">';

				$app->enqueueMessage(JText::_('PLG_BWTIMECONTROL_MAINTENANCE_STOPPING_CRON'), '');
			}

			// Check for stopped file
			if (JFile::exists(JPATH_PLUGINS . $cron->stoppedFile))
			{
				$app->enqueueMessage(JText::_('PLG_BWTIMECONTROL_MAINTENANCE_CRON_STOPPED'), 'Info');
			}
		}

			$this->template	= $app->getTemplate();

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title(JText::_('COM_BWPOSTMAN_MAINTENANCE'), 'wrench');
		$bar	= JToolbar::getInstance('toolbar');

		// Set toolbar items for the page
		if ($layout == 'restoreTables')
		{
			$alt 	= "COM_BWPOSTMAN_BACK";
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
			$document->addStyleDeclaration($style);
			$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/install.css');
		}

		if ($this->permissions['com']['admin'])
		{
			JToolbarHelper::preferences('com_bwpostman', '500', '900');
		}

		JToolbarHelper::spacer();
		JToolbarHelper::divider();
		JToolbarHelper::spacer();

		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = BwPostmanHTMLHelper::getManualLink('maintenance');
		$forumLink  = BwPostmanHTMLHelper::getForumLink();

		if(version_compare(JVERSION, '3.99', 'le'))
		{
			$bar->appendButton('Extlink', 'users', JText::_('COM_BWPOSTMAN_FORUM'), $forumLink);
			$bar->appendButton('Extlink', 'book', JText::_('COM_BWPOSTMAN_MANUAL'), $manualLink);
		}
		else
		{
			$manualOptions = array('url' => $manualLink, 'icon-class' => 'book', 'idName' => 'manual');
			$forumOptions  = array('url' => $forumLink, 'icon-class' => 'users', 'idName' => 'forum');

			$manualButton = new JButtonExtlink('Extlink', JText::_('COM_BWPOSTMAN_MANUAL'), $manualOptions);
			$forumButton  = new JButtonExtlink('Extlink', JText::_('COM_BWPOSTMAN_FORUM'), $forumOptions);

			$bar->appendButton($manualButton);
			$bar->appendButton($forumButton);
		}

		if(version_compare(JVERSION, '3.99', 'le'))
		{
			BwPostmanHelper::addSubmenu('bwpostman');
		}

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

		if (empty($layout))
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);

		return $this;
	}
}
