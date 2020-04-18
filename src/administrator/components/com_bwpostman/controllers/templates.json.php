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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import CONTROLLER object class
jimport('joomla.application.component.controlleradmin');

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/templates.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/webapp/BwWebApp.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

/**
 * BwPostman Templates Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Templates
 *
 * @since       1.1.0
 */
class BwPostmanControllerTemplates extends JControllerAdmin
{
	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @access	public
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since       1.1.0
	 */
	public function installtpl()
	{
		// Check for request forgeries
		if (!JSession::checkToken('get'))
		{
			throw new BwException((JText::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
		}

		$app	= JFactory::getApplication();
		$appWeb = new BwWebApp();
		$jinput	= $app->input;

		$step       = $jinput->get('step', 1);
		$alertClass = 'success';
		$ready      = "0";
		$msg        = null;

		// Get file details from uploaded file
		$file = $app->getUserState('com_bwpostman.templates.uploadfile', '');

		$model	= $this->getModel('templates');

		$log_options  = array();
		$logger      = BwLogger::getInstance($log_options);

		try
		{
			// start output buffer
			ob_start();

			switch($step)
			{
				default:
				case 'step1':
					// extract archive
					if (!$model->extractTplFiles($file))
					{
						$model->deleteTempFolder($file);
						echo '<h3 class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$msg    = JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR');
						$alertClass = 'error';
						$ready = "1";
					}

					$step = "2";
					break;

				case 'step2':
					// install data to table #__bwpostman_templates_tpl
					$templatestplsql = 'bwp_templatestpl.sql';
					if (!$model->installTplFiles($templatestplsql, $step))
					{
						$model->deleteTempFolder($file);
						$msg    = JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR');
						echo '<h3 class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$alertClass = 'error';
						$ready = "1";
					}

					$step = "3";
					break;

				case 'step3':
					// install data to table #__bwpostman_templates
					$templatessql = 'bwp_templates.sql';
					if (!$model->installTplFiles($templatessql, $step))
					{
						$model->deleteTempFolder($file);
						echo '<h3 class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR') . '</h3>';
						$msg    = JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR');
						$alertClass = 'error';
						$ready = "1";
					}

					$step = "4";
					break;

				case 'step4':
					// copy thumbnail
					if (!$model->copyThumbsFiles($file))
					{
						$alertClass = 'warning';
					}

					$step = "5";
					break;

				case 'step5':
					// delete temp folder
					if (!$model->deleteTempFolder($file))
					{
						$alertClass = 'warning';
					}

					$app->setUserState('com_bwpostman.templates.uploadfile', '');
					$ready = "1";
					$step = "6";
					echo '<h3 class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_OK') . '</h3>';
					break;
			}

			// return the contents of the output buffer
			$content    = ob_get_contents();
			$result     = $content;

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
		catch (BwException $e)
		{
			$result  = '<p class="bw_tablecheck_error err">' . $e->getMessage() . '</p>';
			$msg    = JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR');
			$alertClass = 'error';
			$ready      = "1";
			$step       = "6";

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

		if ($msg)
		{ // install failed
			$link	= JRoute::_('index.php?option=com_bwpostman&view=templates', false);
			$this->setRedirect($link, $msg, 'error');
		}
		else
		{ // template installed
			$msg	= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_OK');
			$link	= JRoute::_('index.php?option=com_bwpostman&view=templates', false);
			$this->setRedirect($link, $msg);
		}
	}
}
