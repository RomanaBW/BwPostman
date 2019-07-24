<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
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

// Import VIEW object class
jimport('joomla.application.component.view');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/webapp/BwWebApp.php');

//use Joomla\Filesystem\File as JFile;

/**
 * BwPostman Maintenance RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       1.0.1
 */
class BwPostmanViewMaintenance extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       1.0.1
	 * @throws Exception
	 *
	 */
	public function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$date	= JFactory::getDate();

		if (!BwPostmanHelper::canView('maintenance'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$layout	= $jinput->get('layout');

		if ($layout == 'saveTables')
		{
			jimport('joomla.filesystem.file');

			$compressed     = JComponentHelper::getParams('com_bwpostman')->get('compress_backup', true);
			$dottedVersion  = BwPostmanHelper::getInstalledBwPostmanVersion();
			$version	    = str_replace('.', '_', $dottedVersion);
			$filename	    = "BwPostman_" . $version . "_Tables_" . $date->format("Y-m-d_H_i") . '.xml';
			$xmlFileName    = JFile::makeSafe($filename);
			$mimeType	    = "application/xml";

			if ($compressed)
			{
				$mimeType	= "application/zip";
				$filename   .= '.zip';
				$xmlFileName = JFile::makeSafe(JFile::stripExt($filename));
			}

			// Maybe we need other headers depending on browser type...
			jimport('joomla.environment.browser');
			$browser		= JBrowser::getInstance();
			$user_browser	= $browser->getBrowser();
			$appWeb         = new BwWebApp();

			$appWeb->clearHeaders();

			$appWeb->setHeader('Content-Type', $mimeType, true); // Joomla will overwrite this...
			$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename\"", true);
			$appWeb->setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$appWeb->setHeader('Pragma', 'no-cache', true);

			if ($user_browser == "msie")
			{
				$appWeb->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
				$appWeb->setHeader('Pragma', 'public', true);
			}

			// Joomla overwrites content-type, we can't use $appWeb->setHeader()
			$document = JFactory::getDocument();
			$document->setMimeEncoding($mimeType);

			@ob_end_clean();
			ob_start();

			$appWeb->sendHeaders();

			// Get the export data
			$model	= $this->getModel('maintenance');

			readfile($model->saveTables($xmlFileName, false));
		}

		if ($layout == 'doRestore')
		{
			$model	= $this->getModel();
			$dest	= $app->getUserState('com_bwpostman.maintenance.dest', '');

			$model->restoreTables($dest);
		}

		if ($layout == 'checkTables')
		{
			$model	= $this->getModel();

			echo '<div class="modal" rel="{size: {x: 700, y: 500}}">';
			$model->checkTables();
			echo '</div>';

		}
		return $this;
	}
}
