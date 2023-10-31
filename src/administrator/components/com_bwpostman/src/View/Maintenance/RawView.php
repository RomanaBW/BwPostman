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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Maintenance;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Controller\MaintenanceController;
use BoldtWebservice\Component\BwPostman\Administrator\Model\MaintenanceModel;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwWebApp;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Maintenance RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       1.0.1
 */
class RawView extends BaseHtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  RawView  A string if successful, otherwise a JError object.
	 *
	 * @since       1.0.1
	 *
	 * @throws Exception
	 *
	 */
	public function display($tpl = null): RawView
	{
		$app 	= Factory::getApplication();
		$jinput	= $app->input;
		$date	= Factory::getDate();

		if (!BwPostmanHelper::canView('maintenance'))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$layout	= $jinput->get('layout');

		if ($layout == 'saveTables')
		{
			$compressed     = ComponentHelper::getParams('com_bwpostman')->get('compress_backup', true);
			$dottedVersion  = BwPostmanHelper::getInstalledBwPostmanVersion();
			$version	    = str_replace('.', '_', $dottedVersion);
			$filename	    = "BwPostman_" . $version . "_Tables_" . $date->format("Y-m-d_H_i") . '.xml';
			$xmlFileName    = File::makeSafe($filename);
			$mimeType	    = "application/xml";

			if ($compressed)
			{
				$mimeType	= "application/zip";
				$filename   .= '.zip';
				$xmlFileName = File::makeSafe(File::stripExt($filename));
			}

			// Maybe we need other headers depending on browser type...
			$browser		= Browser::getInstance();
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
			$document = $app->getDocument();
			$document->setMimeEncoding($mimeType);
			$document->getWebAssetManager()->registerAndUseScript('com_bwpostman/admin-bwpm_checktables.js', 'com_bwpostman.admin-bwpm_checktables');

			@ob_end_clean();
			ob_start();

			$appWeb->sendHeaders();

			// Get the export data
			$model	= new MaintenanceModel();

			readfile($model->saveTables($xmlFileName, false));
		}

		if ($layout == 'doRestore')
		{
			$controller	= new MaintenanceController();

			$controller->restoreTables();
		}

		if ($layout == 'checkTables')
		{
			$controller	= new MaintenanceController();

			echo '<div class="modal" rel="{size: {x: 700, y: 500}}">';
			$controller->checkTables();
			echo '</div>';

		}
		return $this;
	}
}
