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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Maintenance;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Plugin\Bwpostman\Bwtimecontrol\Helper\BwPostmanPhpCron;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\LinkButton;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\File;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman maintenance View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	CoverPage
 *
 * @since       1.0.1
 */
class HtmlView extends BaseHtmlView
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
	 * @return  HtmlView  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function display($tpl = null): HtmlView
	{
		$app	= Factory::getApplication();
		HTMLHelper::_('bootstrap.framework');

		PluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['maintenance'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$jinput		= $app->input;
		$model		= $this->getModel();
		$layout		= $jinput->getCmd('layout', '');

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		if (PluginHelper::isEnabled('bwpostman', 'bwtimecontrol'))
		{
            \JLoader::registerNamespace('BoldtWebservice\\Plugin\\Bwpostman\\Bwtimecontrol\\Helper', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers');

            $cron = new BwPostmanPhpCron;
			$refreshInterval = 60;

			// Check for start file
			if (property_exists($cron, 'startFile') && File::exists(JPATH_PLUGINS . $cron->startFile))
			{
				$url = 'index.php?option=' . $jinput->getCmd('option', 'com_bwpostman') . '&view=maintenance';
				echo '<meta http-equiv="refresh" content="' . $refreshInterval . '; URL=' . $url . '">';

				$app->enqueueMessage(Text::_('PLG_BWTIMECONTROL_MAINTENANCE_STARTING_CRON'), 'Info');
			}

			// Check for started file
			if (property_exists($cron, 'startedFile') && File::exists(JPATH_PLUGINS . $cron->startedFile))
			{
				$app->enqueueMessage(Text::_('PLG_BWTIMECONTROL_MAINTENANCE_CRON_STARTED'), 'Info');
			}

			// Check for stop file
			if (property_exists($cron, 'stopFile') && File::exists(JPATH_PLUGINS . $cron->stopFile))
			{
				$url = 'index.php?option=' . $jinput->getCmd('option', 'com_bwpostman') . '&view=maintenance';
				echo '<meta http-equiv="refresh" content="' . $refreshInterval . '; URL=' . $url . '">';

				$app->enqueueMessage(Text::_('PLG_BWTIMECONTROL_MAINTENANCE_STOPPING_CRON'), '');
			}

			// Check for stopped file
			if (property_exists($cron, 'stoppedFile') && File::exists(JPATH_PLUGINS . $cron->stoppedFile))
			{
				$app->enqueueMessage(Text::_('PLG_BWTIMECONTROL_MAINTENANCE_CRON_STOPPED'), 'Info');
			}
		}

		$this->template	= $app->getTemplate();

		$this->addToolbar();

		switch ($layout)
		{
			case 'saveTables':
				$this->check_res	= $model->saveTables(null, false);
				break;
			case 'updateCheckSave':
			case 'checkTables':
			case 'restoreTables':
			case 'doRestore':
				break;
			default:
		}

		parent::display($tpl);

		return $this;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       2.4.0
	 */
	protected function addToolbar()
	{
		$app    = Factory::getApplication();
		$layout = $app->input->getCmd('layout', '');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();
		$document = $app->getDocument();

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MAINTENANCE'), 'wrench');

		$options['text'] = "COM_BWPOSTMAN_BACK";
		$options['name'] = 'back';
		$options['url'] = "index.php?option=com_bwpostman&view=maintenance";
		$options['icon'] = "icon-arrow-left";

		$button = new LinkButton('back');

		// Set toolbar items for the page
		if ($layout == 'restoreTables')
		{
			$app->input->set('hidemainmenu', true);
			$document->setTitle(Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'));
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE'), 'download');

			$button->setOptions($options);

			$toolbar->appendButton($button);
		}

		if ($layout == 'doRestore')
		{
			$app->input->set('hidemainmenu', true);
			$document->setTitle(Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'));
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DO_RESTORE'), 'download');

			$button->setOptions($options);

			$toolbar->appendButton($button);
		}

		if ($layout == 'checkTables')
		{
			$app->input->set('hidemainmenu', true);
			$document->setTitle(Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'));
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECKTABLES'), 'download');

			$button->setOptions($options);

			$toolbar->appendButton($button);
		}

		if ($layout == 'updateCheckSave')
		{
			$document->setTitle(Text::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'));
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MAINTENANCE_UPDATECHECKSAVE'), 'download');

			$options['text'] = "COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN";
			$options['name'] = 'back';
			$options['url'] = "javascript:window.close()";
			$options['icon'] = "icon-arrow-left";

			$button->setOptions($options);

			$toolbar->appendButton($button);

			$style = '.layout-updateCheckSave .navbar {display:none;}'
				. '.layout-updateCheckSave .subhead-fixed {position: relative;top: 0;}'
				. 'body {padding-top:0;}';
			$document->getWebassetManager()->addInlineStyle($style);
			$document->getWebassetManager()->useStyle('com_bwpostman.install');
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('maintenance');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
