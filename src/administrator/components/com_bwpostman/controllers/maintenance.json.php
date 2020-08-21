<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance controller for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt, Karl Klostermann
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/maintenance.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/webapp/BwWebApp.php');

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
	 * Integer to hold ready state
	 *
	 * @var integer
	 *
	 * @since 2.4.0
	 */
	protected $ready = 0;

	/**
	 * String to hold current message css class
	 *
	 * @var string
	 *
	 * @since 2.4.0
	 */
	protected $alertClass = 'success';

	/**
	 * String to hold current error message
	 *
	 * @var string
	 *
	 * @since 2.4.0
	 */
	protected $errorMessage = '';

	/**
	 * Method to call checkTables tables process via ajax
	 *
	 * @return 	void
	 *
	 * @since   1.3.0
	 */
	public function tCheck()
	{
		$session = Factory::getSession();
		$model   = $this->getModel('maintenance');

		try
		{
			// Check for request forgeries
			if (!Session::checkToken('get')) {
				throw new Exception((Text::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

			$app     = Factory::getApplication();
			$jinput  = $app->input;

			$step = $jinput->get('step', 0);
			$this->alertClass = 'success';
			$this->ready = "0";

			// start output buffer
			ob_start();

			switch($step)
			{
				default:
				case 'step0':
					// save tables
					echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES') . '</h4>';
					$savedTables = $model->saveTables(null, true);
					echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES') . '</h4>';

					if ($savedTables !== true)
					{
						$this->alertClass = 'warning';
						$this->ready = "0";
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
					$this->checkAssetAndUserIds();
					// clear session variables
					$session->clear('tcheck_needTa');
					$session->clear('tcheck_inTaNa');
					$this->ready = "1";
					$step = "6";
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $this->ready != "1"
			$storedContent = $session->get('tcheck_content', '');
			$content = $storedContent . $content;
			if ($this->ready != "1")
			{
				$result = '';
				$session->set('tcheck_content', $content);
			}
			else
			{
				$result = $content;

				$successMessage  = '<p class="alert alert-success bw_tablecheck_finished mb-2">';
				$successMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OK');
				$successMessage .= '</p>';

				$result = $successMessage . $result;

				$session->clear('tcheck_content');
			}

			// clean the output buffer and turn off output buffering
			ob_end_clean();

			// set json response
			$res = array(
				"aClass"	=> $this->alertClass,
				"ready"		=> $this->ready,
				"result"	=> $result,
				"step"		=> $step
			);

			// ajax response
			$appWeb = new BwWebApp();
			$appWeb->setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}
		catch (BwException $e)
		{
			$this->alertClass = 'error';
			$this->ready      = "1";
			$error            = $e->getMessage();
			$errorCode        = (int)$e->getCode();
			$result           = $error . $session->get('tcheck_content', '');

			$this->handleBwException($errorCode, $result, $error, $step);
		}

		catch (RuntimeException $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}

		catch (Exception $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}
	}

	/**
	 * Method to call restoreTables tables process via ajax
	 *
	 * @return 	void
	 *
	 * @since   1.3.0
	 */
	public function tRestore()
	{
		$session = Factory::getSession();
		$model   = $this->getModel('maintenance');

		try
		{
			// Check for request forgeries
			if (!Session::checkToken('get'))
			{
				throw new Exception((Text::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

			if (function_exists('set_time_limit'))
			{
				set_time_limit(0);
			}

			$app = Factory::getApplication();

			// Initialize variables
			$jinput = $app->input;
			$error  = '';

			$logOptions = array();
			$logger     = BwLogger::getInstance($logOptions);

			$file = $app->getUserState('com_bwpostman.maintenance.dest', '');

			$step = $jinput->get('step', 'step1');
			$this->alertClass = 'success';
			$this->ready      = "0";

			if($step == 'step1') {
				// initialize session to prevent memory overflow
				$session->set('trestore_content', '');
				$session->set('tcheck_content', '');
				$session->set('trestore_i', 0);
				$session->set('trestore_tablenames', '');
				$session->set('tcheck_needTa', '');
				$session->set('tcheck_inTaNa', '');
				Factory::getApplication()->setUserState('com_bwpostman.maintenance.modifiedAssets', array());
			}

			// start output buffer
			ob_start();

			switch ($step)
			{
				default:
				case 'step1':
					try
					{
						$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

						// parse table data
						$table_names = $model->parseTablesData($file);

						if (!is_array($table_names))
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_NO_TABLES_ERROR');
							throw new BwException($error, 1010);
						}

						$session->set('trestore_tablenames', $table_names);
						$step = "2";

						$logger->addEntry(
							new LogEntry(
								sprintf(
									'Speicherverbrauch in Schritt 1: %01.3f MB',
									(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
								),
								BwLogger::BW_DEBUG, 'maintenance')
						);
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1011);
					}
					break;

				case 'step2':
					try
					{
						$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

						// output generals, get component asset and user groups
						$model->outputGeneralInformation();

						$step = "3";

						$logger->addEntry(
							new LogEntry(
								sprintf(
									'Speicherverbrauch in Schritt 2: %01.3f MB',
									(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
								),
								BwLogger::BW_DEBUG, 'maintenance')
						);
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1020);
					}
					break;

				case 'step3':
					Factory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', '');
					// get needed tables from installation file
					$neededTableNames = $this->getNeededTables($session);

					// Reduce parsed table names to such which are needed by BwPostman (got from sql installation files)
					// to prevent adding tables to database which are not part of BwPostman or its installed extensions
					$parsedTableNames  = $session->get('trestore_tablenames', array());
					$reducedTableNames = array();

					foreach ($parsedTableNames as $parsedTableName)
					{
						if (in_array($parsedTableName, $neededTableNames))
						{
							$reducedTableNames[] = $parsedTableName;
						}
					}

					$session->set('trestore_tablenames', $reducedTableNames);

					$step = "4";
					break;

				case 'step4':
					try
					{
						$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

						echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_PROCESS') . '</h4>';
						$assetGroupsProcessed = $model->healAssetUserGroups($session->get('trestore_tablenames', ''));

						if ($assetGroupsProcessed === false)
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_GENERAL_ERROR');
							throw new BwException($error, 1030);
						}

						$step = "5";

						$logger->addEntry(
							new LogEntry(
								sprintf(
									'Speicherverbrauch in Schritt 3: %01.3f MB',
									(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
								),
								BwLogger::BW_DEBUG, 'maintenance')
						);
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1031);
					}
					break;

				case 'step5':
					try
					{
						$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

						echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_RESTORE_POINT') . '</h4>';
						$restorePointCreated = $model->createRestorePoint();

						if ($restorePointCreated === false)
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR');
							throw new BwException($error, 1040);
						}

						$step = "6";

						$logger->addEntry(
							new LogEntry(
								sprintf(
									'Speicherverbrauch in Schritt 5: %01.3f MB',
									(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
								),
								BwLogger::BW_DEBUG, 'maintenance')
						);
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1041);
					}
					break;

				case 'step6':
					try
					{
						$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

						// delete all existing asset sub entries of BwPostman at #__assets
						echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_HEAL_ASSETS') . '</h4>';
						$subAssetsDeleted = $model->deleteSubAssets();

						if ($subAssetsDeleted === false)
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_ERROR');
							throw new BwException($error, 1050);
						}

						// uncomment next line to test rollback (only makes sense, if deleted tables contained data)
//						throw new BwException(Text::_('Test-Exception DeleteAssets Controller'), 1051);

						// repair holes in lft and rgt values, update component asset at #__assets
						$assetTableHealed = $model->healAssetsTable();

						if ($assetTableHealed === false)
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR');
							throw new BwException($error, 1052);
						}

						$step = "7";

						$logger->addEntry(
							new LogEntry(
								sprintf(
									'Speicherverbrauch in Schritt 6: %01.3f MB',
									(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
								),
								BwLogger::BW_DEBUG, 'maintenance')
						);
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error err">' . $e->getMessage() . '</p>';
						$error  .= Factory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						throw new BwException($error, 1053);
					}
					break;

				case 'step7':
					try
					{
						// get stored $base_asset and $curr_asset_id from session
						$table_names = $session->get('trestore_tablenames', '');
						$i           = $session->get('trestore_i', 0);
						$error       = '';

						if ($i == 0)
						{
							echo '<h4>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_ANEW_TABLE', $table_names[$i]) . '</h4>';
							$tablesRenewed = $model->anewBwPostmanTables($table_names);

							if ($tablesRenewed !== true)
							{
								$error = Text::_($tablesRenewed);
								throw new BwException($error, 1060);
							}
						}

						if ($error === '')
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

							// loop over all tables
							echo '<h5>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_TABLE',
									$table_names[$i]) . '</h5>';

							$lastTable = false;

							if ($i + 1 === count($table_names))
							{
								$lastTable = true;
							}

							$tablesRewritten = $model->reWriteTables($table_names[$i], $lastTable);

							if ($tablesRewritten === false)
							{
								$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_REWRITE_TABLE_ERROR');
								throw new BwException($error, 1061);
							}

							$i++;
							$session->set('trestore_i', $i);
							$step = "7";

							if ($lastTable)
							{
								// clear session variables
								$session->clear('trestore_tablenames');
								$session->clear('trestore_i');
								$step = "8";
							}

							$logger->addEntry(
								new LogEntry(
									sprintf(
										'Speicherverbrauch in Schritt 7, Tabelle %s: %01.3f MB',
										$table_names[$i - 1],
										(memory_get_usage(true) / (1024.0 * 1024.0) - $mem0)
									),
									BwLogger::BW_DEBUG, 'maintenance')
							);
						}
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$error  .= Factory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						throw new BwException($error, 1062);
					}
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
						$this->checkAssetAndUserIds();

						// clear session variables
						$session->clear('tcheck_needTa');
						$session->clear('tcheck_inTaNa');
						$this->ready = "1";
						$step = "12";
					}
					catch (Exception $e)
					{
						$error  = '<p class="bw_tablecheck_error">' . $e->getMessage() . '</p>';
						$error  .= Factory::getApplication()->getUserState('com_bwpostman.maintenance.restorePoint_text', '');
						throw new BwException($error, 1110);
					}
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $this->ready != "1"
			$storedContent = $session->get('trestore_content', '');
			$content       = $content . $storedContent;

			if ($this->ready != "1")
			{
				$result = $content;
				$session->set('trestore_content', $content);
			}
			else
			{
				$result = $content;

				$successMessage = '<p class="alert alert-success bw_tablecheck_finished mb-2">';
				$successMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OK');
				$successMessage .= '</p>';

				$result = $successMessage . $result;

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
				"aClass"  => $this->alertClass,
				"ready"   => $this->ready,
				"result"  => $result,
				"error"   => $error,
				"step"    => $step
			);

			// ajax response
			$appWeb = new BwWebApp();
			$appWeb->setHeader('Content-Type', 'application/json', true);
			echo json_encode($res);
			$app->close();
		}
		catch (BwException $e)
		{
			$this->alertClass = 'error';
			$this->ready      = "1";
			$error            = $e->getMessage();
			$errorCode        = (integer)$e->getCode();
			$result           = $error . $session->get('trestore_content', '');

			$this->handleBwException($errorCode, $result, $error, $step);
		}

		catch (RuntimeException $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}

		catch (Exception $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}
	}

	/**
	 * Method to get needed tables from installation file
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @return array
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function getNeededTables($session)
	{
		$model        = $this->getModel('maintenance');
		$neededTables = $model->getNeededTables();

		if ($neededTables === false || !is_array($neededTables))
		{
			$errorMessage = '<p class="alert alert-error bw_tablecheck_error">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_NEEDED_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, 1070);
		}

		// store $neededTables in session
		$session->set('tcheck_needTa', $neededTables);

		$neededTableNames = array();

		foreach ($neededTables as $neededTable)
		{
			$neededTableNames[] = $neededTable->name;
		}

		return $neededTableNames;
	}

	/**
	 * Method to get installed table names
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @return void
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function getInstalledTableNames($session)
	{
		$model           = $this->getModel('maintenance');
		$tableNamesArray = $model->getTableNamesFromDB();

		if ($tableNamesArray === false || !is_array($tableNamesArray))
		{
			$errorMessage = '<p class="alert alert-error bw_tablecheck_error">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_INSTALLED_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, 1080);
		}

		$installedTableNames = array();

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
	 * @return void
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function convertTableNames($session)
	{
		$model             = $this->getModel('maintenance');
		$genericTableNames = $session->get('tcheck_inTaNa');
		$neededTables      = $session->get('tcheck_needTa');

		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_GENERALS') . '</h4>';

		// check table names
		if ($model->checkTableNames($neededTables, $genericTableNames, 'check') === false)
		{
			$errorMessage = '<p class="bw_tablecheck_error">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_NAMES_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, 1090);
		}
	}

	/**
	 * Method to convert to generic table names
	 *
	 * @param   $session    $session    The session of this task
	 *
	 * @return void
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function checkTableColumns($session)
	{
		// get stored session variables
		$model        = $this->getModel('maintenance');
		$neededTables = $session->get('tcheck_needTa');

		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_TABLE_COLUMNS') . '</h4>';
		// check table columns
		for ($i = 0; $i < count($neededTables); $i++)
		{
			echo '<h5>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_COLUMNS_TABLE', $neededTables[$i]->name) . '</h5>';
			$res = $model->checkTableColumns($neededTables[$i]);

			if ($res === 2)
			{
				$i--;
			}

			if ($res === false)
			{
				$errorMessage = '<p class="alert alert-error bw_tablecheck_error">';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_COLS_ERROR');
				$errorMessage .= '<br /><br />';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
				$errorMessage .= '</p>';

				throw new BwException($errorMessage, 1100);
			}
		}
	}

	/**
	 * Method to check Assets and User IDs
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   1.3.0
	 */
	protected function checkAssetAndUserIds()
	{
		$model	= $this->getModel('maintenance');

		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_ASSET_IDS') . '</h4>';

		// check asset IDs (necessary because asset_id = 0 prevents deleting)
		if (!$model->checkAssetId())
		{
			$errorMessage = '<p class="alert alert-warning bw_tablecheck_warn">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN');
			$errorMessage .= '</p>';

			$this->errorMessage = $errorMessage;
			$this->alertClass = 'warning';
		}

		// check asset IDs (necessary because asset_id = 0 prevents deleting)
		if (!$model->checkAssetParentId())
		{
			$errorMessage = '<p class="alert alert-warning bw_tablecheck_warn">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN');
			$errorMessage .= '</p>';

			$this->errorMessage = $errorMessage;
			$this->alertClass = 'warning';
		}

		echo '<br />';
		// check user IDs in subscriber Table
		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_USER_IDS') . '</h4>';

		if (!$model->checkUserIds())
		{
			$errorMessage = '<p class="alert alert-warning bw_tablecheck_warn">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_WARN');
			$errorMessage .= '</p>';

			$this->errorMessage = $errorMessage;
			$this->alertClass = 'warning';
		}
		else
		{
			echo str_pad('<p class="bw_tablecheck_ok">' . Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_OK') . '</p>', 4096);
		}
	}

	/**
	 * Method to handle BwException for table check and restore the same way
	 *
	 * @param integer   $errorCode
	 * @param string    $result
	 * @param string    $error
	 * @param string    $step
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	private function handleBwException($errorCode, $result, $error, $step)
	{
		$app   = Factory::getApplication();
		$model = $this->getModel('maintenance');

		if ((1050 <= $errorCode) && ($errorCode <= 1100))
		{
			$model->restoreRestorePoint();
		}

		// clean the output buffer and turn off output buffering
		ob_end_clean();

		// set json response
		$res = array(
			"aClass" => $this->alertClass,
			"ready"  => $this->ready,
			"result" => $result,
			"error"  => $error,
			"step"   => $step
		);

		// ajax response
		$appWeb = new BwWebApp();
		$appWeb->setHeader('Content-Type', 'application/json', true);
		echo json_encode($res);
		$app->close();
	}
}
