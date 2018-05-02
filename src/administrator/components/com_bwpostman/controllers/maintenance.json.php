<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance controller for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt, Karl Klostermann
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
defined('_JEXEC') or die('Restricted access');

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/maintenance.php');

/**
 * BwPostman Campaigns Controller
 *
 * @package		BwPostman-Admin
 * @subpackage	Campaigns
 *
 * @since       1.0.1
 */
class BwPostmanControllerMaintenance extends JControllerLegacy
{

	/**
	 * Method to call checkTables tables process via ajax
	 *
	 * @access	public
	 *
	 * @since   1.3.0
	 */
	public function tCheck()
	{
		try
		{
			// Check for request forgeries
			if (!JSession::checkToken('get')) {
				throw new BwException((JText::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

			$app = JFactory::getApplication();
			$session = JFactory::getSession();
			$jinput	= $app->input;

			$step = $jinput->get('step', 0);
			$alertClass = 'success';
			$ready = "0";

			// start output buffer
			ob_start();

			switch($step)
			{
				default:
				case 'step0':
					// save tables
					$model	= $this->getModel('maintenance');
					echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES') . '</h4>';
					$savedTables = $model->saveTables(true);
					echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES') . '</h4>';
					if ($savedTables != true)
					{
						$alertClass = 'warning';
						$ready = "0";
					}

					$step = "1";
					break;

				case 'step1':
					// initialize session to prevent memory overflow
					$session->set('tcheck_content', '');
					$session->set('tcheck_needTa', '');
					$session->set('tcheck_inTaNa', '');

					// get needed tables from installation file
					$this->getNeededTables($session);
					$step = "2";
					break;

				case 'step2':
					// get installed table names
					$this->getInstalledTableNames($session);
					$step = "3";
					break;

				case 'step3':
					// convert to generic table names
					$this->convertTableNames($session);
					$step = "4";
					break;

				case 'step4':
					// check table columns
					$this->checkTableColumns($session);
					$step = "5";
					break;

				case 'step5':
					// check asset IDs (necessary because asset_id = 0 prevents deleting) and user IDs in subscriber table
					$this->checkAssetAndUserIds($session);
					// clear session variables
					$session->clear('tcheck_needTa');
					$session->clear('tcheck_inTaNa');
					$ready = "1";
					$step = "6";
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $ready != "1"
			$storedContent = $session->get('tcheck_content', '');
			$content = $storedContent . $content;
			if ($ready != "1")
			{
				$result = '';
				$session->set('tcheck_content', $content);
			}
			else
			{
				$result = $content;
				$session->clear('tcheck_content');
			}

			// clean the output buffer and turn off output buffering
			ob_end_clean();

			// set json response
			$res = array(
				"aClass"	=> $alertClass,
				"ready"		=> $ready,
				"result"	=> $result,
				"step"		=> $step
			);

			// ajax response
			// $appWeb    = new JApplicationWeb();
			//$appWeb->setHeader('Content-Type', 'application/json', true);
			JResponse::setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}
		catch (BwException $e)
		{
			echo $e->getMessage();
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			$msg['type']	= 'error';
		}

		catch (Exception $e)
		{
			echo $e->getMessage();
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			$msg['type']	= 'error';
		}
	}

	/**
	 * Method to call restoreTables tables process via ajax
	 *
	 * @access	public
	 *
	 * @since   1.3.0
	 */
	public function tRestore()
	{
		try
		{
			// Check for request forgeries
			if (!JSession::checkToken('get'))
			{
				throw new BwException((JText::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

			if (function_exists('set_time_limit'))
			{
				set_time_limit(0);
			}

			$app     = JFactory::getApplication();

			// Initialize variables
			$jinput  = $app->input;
			$error   = '';
			if(BWPOSTMAN_LOG_MEM) {
				$log_options = array('test' => 'testtext');
				$logger      = new BwLogger($log_options);
			}

			$session = JFactory::getSession();
			$file    = $app->getUserState('com_bwpostman.maintenance.dest', '');
			$model   = $this->getModel('maintenance');

			$step       = $jinput->get('step', 'step1');
			$alertClass = 'success';
			$ready      = "0";
			if($step == 'step1') {
				$content    = '';

				// initialize session to prevent memory overflow
				$session->set('trestore_content', '');
				$session->set('tcheck_content', '');
				$session->set('trestore_i', 0);
				$session->set('trestore_tablenames', '');
				$session->set('tcheck_needTa', '');
				$session->set('tcheck_inTaNa', '');
				JFactory::getApplication()->setUserState('com_bwpostman.maintenance.tables', '');
			}

			// start output buffer
			ob_start();

			switch ($step)
			{
				default:
				case 'step1':
					try
					{
						if(BWPOSTMAN_LOG_MEM) {
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						// parse table data
						$table_names = $model->parseTablesData($file);

						if (!is_array($table_names))
						{
							echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_NO_TABLES_ERROR') . '</p>';
							$alertClass = 'error';
							$ready      = "1";
						}

						$session->set('trestore_tablenames', $table_names);
						$step = "2";

						if(BWPOSTMAN_LOG_MEM) {
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 3: %01.3f MB',
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step2':
					try
					{
						if(BWPOSTMAN_LOG_MEM)
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						// output generals, get component asset and user groups
						$model->outputGeneralInformation();

						$step = "3";

						if(BWPOSTMAN_LOG_MEM)
						{
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 2: %01.3f MB',
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step3':
					try
					{
						if(BWPOSTMAN_LOG_MEM)
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_PROCESS') . '</h4>';
						$model->processAssetUserGroups($session->get('trestore_tablenames', ''));
						$step = "4";

						if(BWPOSTMAN_LOG_MEM)
						{
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 4: %01.3f MB',
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step4':
					try
					{
						if(BWPOSTMAN_LOG_MEM)
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_RESTORE_POINT') . '</h4>';
						$model->createRestorePoint();
						$step = "5";

						if(BWPOSTMAN_LOG_MEM)
						{
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 5: %01.3f MB',
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step5':
					try
					{
						if(BWPOSTMAN_LOG_MEM)
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						// delete all existing asset sub entries of BwPostman
						echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_HEAL_ASSETS') . '</h4>';
						$model->deleteSubAssets();

						// uncomment next line to test rollback (only makes sense, if deleted tables contained data)
//						throw new BwException(JText::_('Test-Exception DeleteAssets Controller'));

						// repair holes in lft and rgt values, update component asset
						$model->healAssetsTable();
						$step = "6";

						if(BWPOSTMAN_LOG_MEM)
						{
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 6: %01.3f MB',
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$model->restoreRestorePoint();
						$error  = '<p class="bw_tablecheck_error err">' . $e->getMessage() . '</p>';
						$error  .= JFactory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step6':
					try
					{
						// get stored $base_asset and $curr_asset_id from session
						$table_names = $session->get('trestore_tablenames', '');
						$i           = $session->get('trestore_i', 0);

						if ($i == 0)
						{
							echo '<h4>' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_ANEW_TABLE', $table_names[$i]) . '</h4>';
							$model->anewBwPostmanTables($table_names);
						}

						if(BWPOSTMAN_LOG_MEM)
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);
						}

						// loop over all tables
						echo '<h5>' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_TABLE', $table_names[$i]) . '</h5>';
						$model->reWriteTables($table_names[$i]);
						$i++;
						$session->set('trestore_i', $i);
						$step = "6";

						if ($i == count($table_names))
						{
							// clear session variables
							$session->clear('trestore_tablenames');
							$session->clear('trestore_i');
							$step  = "7";
						}

						if(BWPOSTMAN_LOG_MEM) {
							$logger->addEntry(
								new JLogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 7, Tabelle %s: %01.3f MB',
										$table_names[$i - 1],
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									)
								)
							);
						}
					}
					catch (BwException $e)
					{
						$model->restoreRestorePoint();
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$error  .= JFactory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						$alertClass = 'error';
						$ready      = "1";
					}
					break;

				case 'step7':
					JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', '');
					// get needed tables from installation file
					$this->getNeededTables($session);
					$step = "8";
					break;

				case 'step8':
					// get installed table names
					$this->getInstalledTableNames($session);
					$step = "9";
					break;

				case 'step9':
					// convert to generic table names
					$this->convertTableNames($session);
					$step = "10";
					break;

				case 'step10':
					// check table columns
					$this->checkTableColumns($session);
					$step = "11";
					break;

				case 'step11':
					try
					{
						// check asset IDs (necessary because asset_id = 0 prevents deleting) and user IDs in subscriber table
						$this->checkAssetAndUserIds($session);
						// clear session variables
						$session->clear('tcheck_needTa');
						$session->clear('tcheck_inTaNa');
						$ready = "1";
						$step = "12";
					}
					catch (BwException $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$error  .= JFactory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						$alertClass = 'error';
						$ready      = "1";
					}
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $ready != "1"
			$storedContent = $session->get('trestore_content', '');
			$content       = $content . $storedContent;
			if ($ready != "1")
			{
				$result = $content;
				$session->set('trestore_content', $content);
			}
			else
			{
				$result = $content;
				$session->clear('trestore_content');
				if($error != '')
				{
					// clear session variables
					$session->clear('trestore_tablenames');
					$session->clear('trestore_i');
				}
			}

			// clean the output buffer and turn off output buffering
			ob_end_clean();

			// set json response
			$res = array(
				"aClass"  => $alertClass,
				"ready"   => $ready,
				"result"  => $result,
				"error"   => $error,
				"step"    => $step
			);

			// ajax response
			//$appWeb    = new JApplicationWeb();
			//$appWeb->setHeader('Content-Type', 'application/json', true);
			JResponse::setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}
		catch (BwException $e)
		{
			$error  = '<p class="bw_tablecheck_error err">' . $e->getMessage() . '</p>';
			$alertClass = 'error';
			$ready      = "1";
			$step       = "12";
			$result     = "";

			// set json response
			$res = array(
				"aClass"  => $alertClass,
				"ready"   => $ready,
				"result"  => $result,
				"error"   => $error,
				"step"    => $step
			);

			// ajax response
			//$appWeb    = new JApplicationWeb();
			//$appWeb->setHeader('Content-Type', 'application/json', true);
			JResponse::setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}

		catch (RuntimeException $e)
		{
			$error  = '<p class="bw_tablecheck_error err">' . $e->getMessage() . '</p>';
			$alertClass = 'error';
			$ready      = "1";
			$step       = "12";

			// set json response
			$res = array(
				"aClass"  => $alertClass,
				"ready"   => $ready,
				"result"  => '',
				"error"   => $error,
				"step"    => $step
			);

			// ajax response
			//$appWeb    = new JApplicationWeb();
			//$appWeb->setHeader('Content-Type', 'application/json', true);
			JResponse::setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}

		catch (Exception $e)
		{
			echo $e->getMessage();
			$msg['message']	= JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			$msg['type']	= 'error';
		}
	}

	/**
	 * Method to get needed tables from installation file
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @since   1.3.0
	 */

	protected function getNeededTables($session)
	{
		$model        = $this->getModel('maintenance');

		$neededTables = $model->getNeededTables();
		if (!is_array($neededTables))
		{
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_NEEDED_ERROR') . '</p>';
			$alertClass = 'error';
			$ready      = "1";
		}

		// store $neededTables in session
		$session->set('tcheck_needTa', $neededTables);
	}

	/**
	 * Method to get installed table names
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @since   1.3.0
	 */

	protected function getInstalledTableNames($session)
	{
		$model				= $this->getModel('maintenance');
		$tableNamesArray	= $model->getTableNamesFromDB();
		if (!is_array($tableNamesArray))
		{
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_INSTALLED_ERROR') . '</p>';
			$alertClass = 'error';
			$ready = "1";
		}

		foreach ($tableNamesArray as $tableName)
		{
			$installedTableNames[] = $tableName['tableNameGeneric'];
		}

		// store $installedTableNames in session
		$session->set('tcheck_inTaNa', $installedTableNames);
	}

	/**
	 * Method to convert to generic table names
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @since   1.3.0
	 */

	protected function convertTableNames($session)
	{
		$model	             = $this->getModel('maintenance');
		$genericTableNames   = $session->get('tcheck_inTaNa');
		$neededTables        = $session->get('tcheck_needTa');

		echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_GENERALS') . '</h4>';

		// check table names
		if (!$model->checkTableNames($neededTables, $genericTableNames, 'check'))
		{
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_NAMES_ERROR') . '</p>';
			$alertClass = 'error';
			$ready = "1";
		}
	}

	/**
	 * Method to convert to generic table names
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @since   1.3.0
	 */

	protected function checkTableColumns($session)
	{
		// get stored session variables
		$model	      = $this->getModel('maintenance');
		$neededTables = $session->get('tcheck_needTa');

		echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_TABLE_COLUMNS') . '</h4>';
		// check table columns
		for ($i = 0; $i < count($neededTables); $i++)
		{
			echo '<h5>' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_COLUMNS_TABLE', $neededTables[$i]->name) . '</h5>';
			$res = $model->checkTableColumns($neededTables[$i]);
			if ($res == 2)
			{
				$i--;
			}

			if ($res == 0)
			{
				echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_COLS_ERROR') . '</p>';
				$alertClass = 'error';
				$ready      = "1";
			}
		}
	}

	/**
	 * Method to check Assets and User IDs
	 *
	 * @param   $session
	 *
	 * @since   1.3.0
	 */
	protected function checkAssetAndUserIds($session)
	{
		$model	= $this->getModel('maintenance');

		echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_ASSET_IDS') . '</h4>';
		// check asset IDs (necessary because asset_id = 0 prevents deleting)
		if (!$model->checkAssetId())
		{
			echo '<p class="bw_tablecheck_warn">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN') . '</p>';
			$alertClass = 'warning';
		}

		// check asset IDs (necessary because asset_id = 0 prevents deleting)
		if (!$model->checkAssetParentId())
		{
			echo '<p class="bw_tablecheck_warn">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN') . '</p>';
			$alertClass = 'warning';
		}

		echo '<br />';
		// check user IDs in subscriber Table
		echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_USER_IDS') . '</h4>';
		if (!$model->checkUserIds())
		{
			echo '<p class="bw_tablecheck_warn">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_WARN') . '</p>';
			$alertClass = 'warning';
		}
		else
		{
			echo str_pad('<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_OK') . '</p>', 4096);
		}
	}
}
