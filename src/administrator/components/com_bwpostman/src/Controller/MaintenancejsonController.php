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

namespace BoldtWebservice\Component\BwPostman\Administrator\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwWebApp;
use BoldtWebservice\Component\BwPostman\Administrator\Model\MaintenanceModel;
use RuntimeException;

/**
 * BwPostman Campaigns Controller
 *
 * @package		BwPostman-Admin
 * @subpackage	Campaigns
 *
 * @since       1.0.1
 */
class MaintenancejsonController extends AdminController
{
	/**
	 * Integer to hold ready state
	 *
	 * @var integer
	 *
	 * @since 3.0.0
	 */
	protected int $ready = 0;

	/**
	 * String to hold current message css class
	 *
	 * @var string
	 *
	 * @since 3.0.0
	 */
	protected string $alertClass = 'success';

	/**
	 * String to hold current error message
	 *
	 * @var string
	 *
	 * @since 3.0.0
	 */
	protected string $errorMessage = '';

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since	4.0.0
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);
	}

	/**
	 * Method to call checkTables tables process via ajax
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   1.3.0
	 */
	public function tCheck(): void
	{
		$app     = Factory::getApplication();
		$session = $app->getSession();
		$model   = new MaintenanceModel();
		$jinput  = $app->input;

		$step = $jinput->get('step', 0);

		try
		{
			// Check for request forgeries
			if (!Session::checkToken('get')) {
				throw new Exception((Text::_('COM_BWPOSTMAN_JINVALID_TOKEN')));
			}

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
					$savedTables = $model->saveTables('', true);
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

					$sessionContent = $session->get('tcheck_content', '');

					$this->createRestorePoint($sessionContent, 1300);

					$session->set('tcheck_content', $sessionContent);
					$step = "2";
					break;

				case 'step2':
					// get needed tables from installation file
					$this->getNeededTables($session, 1320);
					$step = "3";
					break;

				case 'step3':
					// get installed table names
					$this->getInstalledTableNames($session, 1330);
					$step = "4";
					break;

				case 'step4':
					// convert to generic table names
					$this->convertTableNames($session, 1340);
					$step = "5";
					break;

				case 'step5':
					// check table columns
					$this->checkTableColumns($session, 1350);
					$step = "6";
					break;

				case 'step6':
					// check asset IDs (necessary because asset_id = 0 prevents deleting) and user IDs in subscriber table
					try
					{
						$sessionContent = $session->get('tcheck_content', '');

						// check asset IDs (necessary because asset_id = 0 prevents deleting) and user IDs in subscriber table
						$this->checkAssetAndUserIds($sessionContent);
						$this->checkAssetAndUserIds($sessionContent, 'users');

						$session->set('tcheck_content', $sessionContent);


						$step = "7";
					}
					catch (Exception $e)
					{
						$error  = $e->getMessage();
						throw new BwException($error, 1360);
					}
					break;

				case 'step7':
					try
					{
						$sessionContent = $session->get('tcheck_content', '');

						$this->deleteRestorePoint($sessionContent);

						$session->set('tcheck_content', $sessionContent);

						// clear session variables
						$session->clear('tcheck_needTa');
						$session->clear('tcheck_inTaNa');
						$app->setUserState('com_bwpostman.maintenance.generals', null);
						$this->ready = "1";
						$step = "8";
					}
					catch (Exception $e)
					{
						$error  = '<p class="text-danger">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1370);
					}
					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $this->ready != "1"
			$storedContent = $session->get('tcheck_content', '');
			$content = $content . $storedContent;
			$result  = $content;

			if ($this->ready != "1")
			{
				$session->set('tcheck_content', $content);
			}
			else
			{

				$successMessage  = '<p class="alert alert-success fw-bold mb-2">';
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

		catch (RuntimeException | Exception $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}
	}

	/**
	 * Method to call restoreTables tables process via ajax
	 *
	 * @return    void
	 *
	 * @throws Exception
	 *
	 * @since   1.3.0
	 */
	public function tRestore(): void
	{
		$app     = Factory::getApplication();
		$session = $app->getSession();
		$model   = new MaintenanceModel();
		$jinput  = $app->input;

		$step = $jinput->get('step', 'step1');

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

			// Initialize variables
			$error  = '';

			$logOptions = array();
			$logger     = BwLogger::getInstance($logOptions);

			$file = $app->getUserState('com_bwpostman.maintenance.dest', '');

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
				$app->setUserState('com_bwpostman.maintenance.modifiedAssets', array());
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
						$error  = '<p class="text-danger">' . $e->getMessage() . '</p>';
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
						$error  = '<p class="text-danger">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1020);
					}
					break;

				case 'step3':
					$app->setUserState('com_bwpostman.maintenance.com_assets', '');
					// get needed tables from installation file
					$neededTableNames = $this->getNeededTables($session, 1030);

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
							throw new BwException($error, 1040);
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
						$error  = '<p class="text-danger">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1041);
					}
					break;

				case 'step5':
					$sessionContent = $session->get('trestore_content', '');

					$this->createRestorePoint($sessionContent, 1050);

					$session->set('trestore_content', $sessionContent);
					$step = "6";
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
							throw new BwException($error, 1060);
						}

						// uncomment next line to test rollback (only makes sense, if deleted tables contained data)
//						throw new BwException(Text::_('Test-Exception DeleteAssets Controller'), 1051);

						// repair holes in lft and rgt values, update component asset at #__assets
						$assetTableHealed = $model->healAssetsTable();

						if ($assetTableHealed === false)
						{
							$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR');
							throw new BwException($error, 1061);
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
						$error  = '<p class="text-danger err">' . $e->getMessage() . '</p>';
						throw new BwException($error, 1062);
					}
					break;

				case 'step7':
					try
					{
						// get stored $base_asset and $curr_asset_id from session
						$table_names    = $session->get('trestore_tablenames', '');
						$i              = $session->get('trestore_i', 0);
						$currentContent = '';

						if ($i == 0)
						{
							echo '<h4>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_ANEW_TABLE', $table_names[$i]) . '</h4>';
							$tablesRenewed = $model->anewBwPostmanTables($table_names);

							if ($tablesRenewed !== true)
							{
								$error = Text::_($tablesRenewed);
								throw new BwException($error, 1070);
							}
						}

						$content = ob_get_contents();

						// use session to store result while $this->ready != "1"
						$storedContent = $session->get('trestore_content', '');

						if ($i == 0)
						{
							$content       = $content . '<br />';
						}
						$content       = $content . $storedContent;
						$session->set('trestore_content', $content);

						ob_end_clean();

						ob_start();

						if ($error === '')
						{
							$mem0 = memory_get_usage(true) / (1024.0 * 1024.0);

							// loop over all tables
							$currentContent .= '<h5>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_TABLE',
									$table_names[$i]) . '</h5>';

							$lastTable = false;

							if ($i + 1 === count($table_names))
							{
								$lastTable = true;
							}

							$tablesRewritten = $model->reWriteTables($table_names[$i], $currentContent, $lastTable);

							if ($tablesRewritten === false)
							{
								$error = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_REWRITE_TABLE_ERROR') . '<br />';
								throw new BwException($error, 1071);
							}

							$i++;
							echo $currentContent;
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
						$error  = '<p class="text-danger">' . $e->getMessage() . '</p>';
//						$step = "13";
						throw new BwException($error, 1072);
					}
					break;

				case 'step8':
					// get installed table names
					$this->getInstalledTableNames($session, 1080);
					$step = "9";
					break;

				case 'step9':
					// convert to generic table names
					$this->convertTableNames($session, 1090);
					$step = "10";
					break;

				case 'step10':
					// check table columns
					$versionOfBackup = $app->getUserState('com_bwpostman.maintenance.generals')['BwPostmanVersion'];

					try
					{
					$this->checkTableColumns($session, 1100, $versionOfBackup);
					$step = "11";
					}
					catch (Exception $e)
					{
						$error  = $e->getMessage();
						throw new BwException($error, 1101);
					}

					break;

				case 'step11':
					try
					{
						$sessionContent = $session->get('trestore_content', '');

						// check asset IDs (necessary because asset_id = 0 prevents deleting) and user IDs in subscriber table
						$this->checkAssetAndUserIds($sessionContent);
						$this->checkAssetAndUserIds($sessionContent, 'users');

						$session->set('trestore_content', $sessionContent);

						$step = "12";
					}
					catch (Exception $e)
					{
						$error  = $e->getMessage();
						throw new BwException($error, 1110);
					}
					break;

				case 'step12':
					$sessionContent = $session->get('trestore_content', '');

					$this->deleteRestorePoint($sessionContent);

					$session->set('trestore_content', $sessionContent);

					// clear session variables
					$session->clear('tcheck_needTa');
					$session->clear('tcheck_inTaNa');
					$app->setUserState('com_bwpostman.maintenance.generals', null);
					$this->ready = "1";
					$step = "13";

					break;
			}

			// return the contents of the output buffer
			$content = ob_get_contents();

			// use session to store result while $this->ready != "1"
			$storedContent = $session->get('trestore_content', '');
			$content       = $content . $storedContent;
			$result        = $content;

			if ($this->ready != "1")
			{
				$session->set('trestore_content', $content);
			}
			else
			{
				$successMessage = '<p class="alert alert-success fw-bold mb-2">';
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
			$app->setUserState('com_bwpostman.maintenance.generals', null);
		}

		catch (RuntimeException | Exception $e)
		{
			echo Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR') . $e->getMessage();
			header('HTTP/1.1 400 ' . Text::_('COM_BWPOSTMAN_ERROR_MSG'));
			exit;
		}
	}

	/**
	 * Method to get needed tables from installation file
	 *
	 * @param   $session         $session    The session of this task
	 * @param integer $errorCode Needed, because we come from check and also from restore
	 *
	 * @return array
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function getNeededTables($session, int $errorCode): array
	{
		$model        = new MaintenanceModel();
		$neededTables = $model->getNeededTables();

		if ($neededTables === false || !is_array($neededTables))
		{
			$errorMessage = '<p class="alert alert-error">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_NEEDED_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, $errorCode);
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
	 * @param         $session   $session    The session of this task
	 * @param integer $errorCode Needed, because we come from check and also from restore
	 *
	 * @return void
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since   1.3.0
	 */

	protected function getInstalledTableNames($session, int $errorCode): void
	{
		$model           = new MaintenanceModel();
		$tableNamesArray = $model->getTableNamesFromDB();

		if ($tableNamesArray === false || !is_array($tableNamesArray))
		{
			$errorMessage = '<p class="alert alert-danger">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_INSTALLED_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, $errorCode);
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
	 * @param session $session   The session of this task
	 * @param integer $errorCode Needed, because we come from check and also from restore
	 *
	 * @return void
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function convertTableNames(Session $session, int $errorCode): void
	{
		$model             = new MaintenanceModel();
		$genericTableNames = $session->get('tcheck_inTaNa');
		$neededTables      = $session->get('tcheck_needTa');

		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_GENERALS') . '</h4>';

		// check table names
		if ($model->checkTableNames($neededTables, $genericTableNames) === false)
		{
			$errorMessage = '<p class="text-danger">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_NAMES_ERROR');
			$errorMessage .= '<br /><br />';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
			$errorMessage .= '</p>';

			throw new BwException($errorMessage, $errorCode);
		}
	}

	/**
	 * Method to convert to generic table names
	 *
	 * @param   $session                   $session          The session of this task
	 * @param integer $errorCode           Needed, because we come from check and also from restore
	 * @param string|null $versionOfBackup The version of the backup
	 *
	 * @return void
	 *
	 * @throws BwException
	 *
	 * @since   1.3.0
	 */

	protected function checkTableColumns($session, int $errorCode, string $versionOfBackup = null): void
	{
		echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_TABLE_COLUMNS') . '</h4>';

		// get stored session variables
		$model        = new MaintenanceModel();
		$neededTables = $session->get('tcheck_needTa');

		// If we are in restore mode, version of backup is not null, so compare versions
		if (!is_null($versionOfBackup))
		{
			// Compare versions. Do nothing, if backup version is newer than installed version to prevent data lost
			if (version_compare($versionOfBackup, $model->getBwPostmanVersion(), '>'))
			{
//				$message =  '<h5>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_COLUMNS_TABLE', $neededTables[$i]->name) . '</h5>';
				$message = '<p class="alert alert-warning">';
				$message .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_NO_COLUMN_CHECK');
				$message .= '</p>';

				echo $message;

				return;
			}
		}

		// check table columns
		for ($i = 0; $i < count($neededTables); $i++)
		{
			echo '<h5>' . Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_TABLE_COLUMNS_TABLE', $neededTables[$i]->name) . '</h5>';
			$res = $model->checkTableColumns($neededTables[$i]);

			if ($res === 2)
			{
				$i--;
			}

			if ($res === false || $res !== 'Column check finished')
			{
				$errorMessage = '<p class="alert alert-danger">';
				$errorMessage .= Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECK_COLS_ERROR', $neededTables[$i]->name);
				$errorMessage .= '<br /><br />';
				$errorMessage .= 'Error: ' . $res . '<br /><br />';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ERROR_FINISH');
				$errorMessage .= '</p>';

				throw new BwException($errorMessage, $errorCode);
			}
		}
	}

	/**
	 * Method to check Assets and User IDs
	 *
	 * @param string $sessionContent Content for output
	 * @param string $mode           Shall we check for assets or user ids?
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   1.3.0
	 */
	protected function checkAssetAndUserIds(string &$sessionContent, string $mode = 'assets'): void
	{
		$model        = new MaintenanceModel();
		$errorMessage = '';

		if($mode === 'assets')
		{
			$message = '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_ASSET_IDS') . '</h4>';

			// check asset IDs (necessary because asset_id = 0 prevents deleting)
			$checkAsset = $model->checkAssetId();

			if ($checkAsset === false)
			{
				$errorMessage = '<p class="alert alert-warning">';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN');
				$errorMessage .= '</p>';

				$this->alertClass = 'warning';
			}
			else
			{
				$message .= $checkAsset;
			}

			// check asset IDs (necessary because asset_id = 0 prevents deleting)
			if (!$model->checkAssetParentId())
			{
				$errorMessage = '<p class="alert alert-warning">';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_PARENT_ASSETS_WARN');
				$errorMessage .= '</p>';

				$this->alertClass = 'warning';
			}

			$sessionContent = $message  . $errorMessage . $sessionContent;
		}

		if($mode === 'users')
		{
			// check user IDs in subscriber Table
			$message   = '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CHECK_CHECK_USER_IDS') . '</h4>';
			$checkUser = $model->checkUserIds();

			if (!$checkUser)
			{
				$errorMessage = '<p class="alert alert-warning">';
				$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_WARN');
				$errorMessage .= '</p>';

				$this->alertClass = 'warning';
			}
			else
			{
				$message .= str_pad('<p class="text-success">' . Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USER_ID_OK') . '</p>', 4096);
			}
			$sessionContent = $message  . $errorMessage . $sessionContent;
		}
	}

	/**
	 * Method to create the restore point
	 *
	 * @param string  $sessionContent Content for output
	 * @param integer $errorCode      Needed, because we come from check and also from restore
	 *
	 * @return void
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since   3.1.3
	 */

	protected function createRestorePoint(string &$sessionContent, int $errorCode): void
	{
		$model        = new MaintenanceModel();
		$errorMessage = '';

		$message =  '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CREATE_RESTORE_POINT') . '</h4>';

		$createRestore = $model->createRestorePoint();

		if ($createRestore !== true)
		{
			$errorMessage = '<p class="alert alert-danger">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR');
			$errorMessage .= '<br />' . false;
			$errorMessage .= '</p>';

			$this->alertClass = 'error';

			$sessionContent = $message  . $errorMessage . $sessionContent;

			throw new BwException($errorMessage, $errorCode);
		}

		$sessionContent = $message  . $errorMessage . $sessionContent;
	}

	/**
	 * Method to delete the restore point
	 *
	 * @param string $sessionContent Content for output
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   3.1.3
	 */

	protected function deleteRestorePoint(string &$sessionContent): void
	{
		$model        = new MaintenanceModel();
		$errorMessage = '';

		$message = '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_DELETE_RESTORE_POINT') . '</h4>';

		$deleteRestore = $model->deleteRestorePoint();

		if ($deleteRestore !== true)
		{
			$errorMessage = '<p class="alert alert-warning">';
			$errorMessage .= Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_DELETE_RESTORE_POINT_WARN');
			$errorMessage .= '<br />' . $deleteRestore;
			$errorMessage .= '</p>';

			$this->alertClass = 'warning';
		}

		$sessionContent = $message  . $errorMessage . $sessionContent;

	}

	/**
	 * Method to handle BwException for table check and restore the same way
	 *
	 * @param integer $errorCode
	 * @param string  $result
	 * @param string  $error
	 * @param string  $step
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	private function handleBwException(int $errorCode, string $result, string $error, string $step): void
	{
		$app   = Factory::getApplication();
		$model = new MaintenanceModel();

		// Restore the restore point only if needed (and available)
		if ((1060 <= $errorCode) && ($errorCode <= 1200)
		|| (1340 <= $errorCode) && ($errorCode <= 1399))
		{
			$restoreResult = $model->restoreRestorePoint();

			if ($restoreResult !== true)
			{
				$message = '<p class="alert alert-danger">' . $restoreResult . '</p>';
				$message .= '<p class="alert alert-danger">' .  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESTORE_RESTORE_POINT_ERROR_NOT_DONE') . '</p>';
//				$result .=  $message;
				$error  .= $message;
			}
			else
			{
				$error .= '<p class="alert alert-danger">' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_POINT_RESTORED_WARNING') . "</p>";
			}
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
		$appWeb->close();
	}
}
