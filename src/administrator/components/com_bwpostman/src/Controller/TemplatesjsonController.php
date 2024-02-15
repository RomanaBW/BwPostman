<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates json controller for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwWebApp;
use RuntimeException;

/**
 * BwPostman Templates Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Templates
 *
 * @since       1.1.0
 */
class TemplatesjsonController extends AdminController
{
	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	public function installtpl()
	{
		try
		{
			// Check for request forgeries
			if (!Session::checkToken('get'))
			{
				throw new Exception((Text::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

			$app    = Factory::getApplication();
			$appWeb = new BwWebApp();
			$jinput	= $app->input;

			$step       = $jinput->get('step', 1);
			$alertClass = 'success';
			$ready      = "0";

			// Get file details from uploaded file
			$file = $app->getUserState('com_bwpostman.templates.uploadfile', '');

			$model = $this->getModel('templates');

			// start output buffer
			ob_start();

			switch($step)
			{
				default:
				case 'step1':
					$step = "2";
					// extract archive
					if ($file === '')
					{
						throw new Exception(Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD').Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_NO_FILE'));
					}

					if (!$model->extractTplFiles($file))
					{
						$model->deleteTempFolder($file);
						echo '<h3 class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$alertClass = 'error';
						$ready = "1";
					}

					break;

				case 'step2':
					$step = "3";
					// install data to table #__bwpostman_templates_tpl
					$templatestplsql = 'bwp_templatestpl.sql';
					if (!$model->installTplFiles($templatestplsql, "STEP2"))
					{
						$model->deleteTempFolder($file);
						echo '<h3 class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$alertClass = 'error';
						$ready = "1";
					}

					break;

				case 'step3':
					$step = "4";
					// install data to table #__bwpostman_templates
					$templatessql = 'bwp_templates.sql';
					if (!$model->installTplFiles($templatessql, "STEP3"))
					{
						$model->deleteTempFolder($file);
						echo '<h3 class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$alertClass = 'error';
						$ready = "1";
					}

					break;

				case 'step4':
					$step = "5";
					// copy thumbnail
					if (!$model->copyThumbsFiles())
					{
						$alertClass = 'warning';
					}

					break;

				case 'step5':
					$step = "6";
					// delete temp folder
					if (!$model->deleteTempFolder($file))
					{
						$alertClass = 'warning';
					}

					$app->setUserState('com_bwpostman.templates.uploadfile', '');
					$ready = "1";
					echo '<h3 class="text-success">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_OK') . '</h3>';
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();
			$result  = $content;

			// clean the output buffer and turn off output buffering
			ob_end_clean();

			// set json response
			$res = array(
				"aClass"  => $alertClass,
				"ready"   => $ready,
				"result"  => $result,
				"step"    => $step
			);


			// ajax response
			$appWeb->setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}
		catch (RuntimeException | Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'TemplatesJsonController BE');

            echo Text::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '<br />';
			echo $exception->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}
	}
}
