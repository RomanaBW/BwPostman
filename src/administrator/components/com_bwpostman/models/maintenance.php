<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance model for backend.
 *
 * @version 2.1.0 build 416
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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

// Import MODEL and Helper object class
jimport('joomla.application.component.model');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\LogEntry;

// Require some classes
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/exceptions/BwException.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

/**
 * BwPostman maintenance page model
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	MaintenancePage
 *
 * @since       1.0.1
 */
class BwPostmanModelMaintenance extends JModelLegacy
{

	/**
	 * Array to hold table names of tables used by BwPostman.
	 * Needed, because multiple shapes of names are necessary.
	 * Also needed, because we might hold names of tables used with plugins
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $tableNames = array();

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $componentRules = array();

	/**
	 * Array to hold rules of component and sections
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $sectionRules = array();

	/**
	 * Array to hold names of columns of asset table
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $assetColnames = array();

	/**
	 * Array to hold used groups with title and id
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $usedGroups = array();

	/**
	 * Deprecated
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	protected $assetTargetTables = array('component', 'campaigns', 'mailinglists', 'newsletters', 'subscribers', 'templates');

	/**
	 * Instance of BwLogger
	 *
	 * @var BwLogger
	 *
	 * @since 2.4.0
	 */
	protected $logger;

	/**
	 * Database object
	 *
	 * @var object
	 *
	 * @since 2.4.0
	 */
	protected $db;

	/**
	 * SimpleXML object
	 *
	 * @var object
	 *
	 * @since 2.4.0
	 */
	protected $xml;

	/**
	 * Are we at Joomla 4?
	 *
	 * @var boolean
	 *
	 * @since 2.4.0
	 */
	protected $isJ4;

	/**
	 * Constructor.
	 *
	 * @throws Exception
	 * @since   2.4.0
	 *
	 */
	public function __construct()
	{
		$logOptions   = array();
		$this->logger = BwLogger::getInstance($logOptions);

		$this->db = Factory::getDbo();

		if(version_compare(JVERSION, '3.999.999', 'ge'))
		{
			$this->isJ4 = true;
		}

		parent::__construct();
	}

	/**
	 * Method to backup tables
	 *
	 * Cannot use File::write() because we want to append data
	 *
	 * @access      public
	 *
	 * @param string  $fileName
	 * @param boolean $update
	 *
	 * @return  string|boolean
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function saveTables($fileName, $update = false)
	{
		// Access check.
		$permissions = Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$permissions['maintenance']['save'])
		{
			return false;
		}

		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/maintenancehelper.php');

		if (is_null($fileName))
		{
			$dottedVersion = BwPostmanHelper::getInstalledBwPostmanVersion();

			if ($dottedVersion === false)
			{
				return false;
			}

			$version       = str_replace('.', '_', $dottedVersion);
			$fileName      = "BwPostman_" . $version . "_Tables_" . Factory::getDate()->format("Y-m-d_H_i") . '.xml';
		}

		// create (empty) backup file
		$path = JPATH_ROOT . "/images/bw_postman/backup_tables";

		if (!Folder::exists($path))
		{
			if (!Folder::create($path))
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_FOUND', $path);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . $message . '</p>';

					return false;
				}
			}
		}

		$fileName = $path . '/' . $fileName;
		$handle   = fopen($fileName, 'wb');

		try
		{
			if ($handle === false)
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_WRITABLE', $path);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . $message . '</p>';

					return false;
				}
			}

			// get all names of installed BwPostman tables
			if ($this->getTableNamesFromDB() === false)
			{
				return false;
			}

			if ($this->tableNames === null)
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_GET_TABLE_NAMES', $path);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . $message . '</p>';

					return false;
				}
			}

			// Build file header XML
			$xmlHeader = $this->buildXmlHeader();

			if ($xmlHeader === false)
			{
				return false;
			}

			foreach ($this->tableNames as $table)
			{
				// do not save the table "bwpostman_templates_tpl"
				if (strpos($table['tableNameRaw'], 'templates_tpl') === false)
				{
					$databaseXml = $this->xml->children();
					$tablesXml = $databaseXml->addChild('tables');

					$tableName = $table['tableNameGeneric'];

					// Build table description XML
					$tableStructure = $this->buildXmlStructure($tableName, $tablesXml);

					if ($tableStructure === false)
					{
						$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $fileName);
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . $message . '</p>';

							return false;
						}
					}
					else
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_TABLE_SUCCESS', $tableName);
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

						if ($update)
						{
							echo '<p class="bw_tablecheck_ok">'	. $message . '</p>';
						}
					}

					// Build table data XML
					if (!$this->buildXmlData($tableName, $tablesXml))
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $fileName);
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . $ $message . '</p>';
						}

						return false;
					}

					$xmlAssets = $this->buildXmlAssets($tableName, $tablesXml);                // write data assets

					if ($xmlAssets === false)
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_ASSETS_WRITE_FILE_ERROR', $fileName);
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . $message . '</p>';
						}

						return false;
					}
				}
			}

			// Reformat XML string with new lines and indents for each entry
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($this->xml->asXML());

			$file_data = $dom->saveXML();


			if (fwrite($handle, $file_data) !== false)
			{
				$compressed = ComponentHelper::getParams('com_bwpostman')->get('compress_backup', true);
				$backupFile = $fileName;

				if ($compressed)
				{
					$backupFile = BwPostmanMaintenanceHelper::compressBackupFile($fileName);
				}

				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_SUCCESS', $fileName);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				if ($update)
				{
					echo '<p class="bw_tablecheck_ok">'	. $message . '</p>';
				}
			}
			else
			{
				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $fileName);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . $message . '</p>';
				}

				return false;
			}

			fclose($handle);
		}
		catch (Exception $e)
		{
			File::delete($fileName);
			fclose($handle);

			return false;
		}

		if ($update)
		{
			return true;
		}
		else
		{
			return $backupFile;
		}
	}

	/**
	 * Method to get a list of names of all installed tables of BwPostman form database in the form
	 * <prefix>tablename. Also sets a list as property of all BwPostman tables with different variations of name
	 *
	 * @return   array|boolean
	 *
	 * @since    1.0.1
	 */
	public function getTableNamesFromDB()
	{
		// Get database name
		$dbname = self::getDBName();

		//build query to get all names of installed BwPostman tables
		$query = "SHOW TABLES WHERE `Tables_in_{$dbname}` LIKE '%bwpostman%'";

		$this->db->setQuery($query);

		try
		{
			$tableNames = $this->db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		$tableArray = array();

		foreach ($tableNames as $tableName)
		{
			if (strpos($tableName, '_tmp') === false)
			{
				$table['tableNameDb']      = $tableName;
				$table['tableNameGeneric'] = self::getGenericTableName($tableName);
				$table['tableNameRaw']     = $this->getRawTableName($table['tableNameGeneric']);
				$table['tableNameUC']      = ucfirst(substr($table['tableNameRaw'], 0, -1));

				$tableArray[] = $table;
			}
		}

		$this->tableNames = $tableArray;

		return $tableArray;
	}

	/**
	 * Builds the XML data header for the tables to export. Based on Joomla JDatabaseExporter
	 *
	 * @return    boolean    true on success, false on failure
	 *
	 * @since    1.0.1
	 */
	private function buildXmlHeader()
	{
		// Get version of BwPostman
		$version = $this->getBwPostmanVersion();

		if ($version === false)
		{
			return false;
		}

		// Get database name
		$dbname = self::getDBName();

		// build generals
		$this->xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"  standalone="yes"?><mysqldump xmlns:xsi="http://www.w3.org/TR/xmlschema-1"></mysqldump>');

		$databaseXml = $this->xml->addChild('database');
		$databaseXml->addAttribute('name', $dbname);

		$generalsXml = $databaseXml->addChild('Generals');
		$generalsXml->addChild('BwPostmanVersion', $version);
		$generalsXml->addChild('SaveDate', Factory::getDate()->format("Y-m-d_H:i"));

		$assetsToSave = $this->getAllBwPostmanAssetsToSave();

		if ($assetsToSave === false)
		{
			return false;
		}

		// Get assets of sections
		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				$assetData = $this->getTableAssetData($table['tableNameRaw'], '');

				if ($assetData === false)
				{
					return false;
				}

				$assetsToSave[] = $assetData[0];
			}
		}

		// write component asset
		$assetXml = $generalsXml->addChild('component_assets');

		foreach ($assetsToSave as $assetToSave)
		{
			$datasetXml = $assetXml->addChild('dataset');

			if (is_array($assetToSave))
			{
				foreach ($assetToSave as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					$datasetXml->addChild($key, $insert_string);
				}
			}
		}

		// process user groups
		$groups = $this->getUsergroupsUsedInAssets();

		if ($groups === false)
		{
			return false;
		}

		$userGroupsXml = $generalsXml->addChild('component_usergroups');

		if (is_array($groups))
		{
			foreach ($groups as $item)
			{
				$userGroupXml = $userGroupsXml->addChild('usergroup');

				foreach ($item as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					$userGroupXml->addChild($key, $insert_string);
				}
			}
		}

		return true;
	}

	/**
	 * Get all usergroups used by assets of BwPostman
	 *
	 * @return    array|boolean            id and name of user groups
	 *
	 * @since    1.3.0
	 */
	private function getUsergroupsUsedInAssets()
	{
		$query     = $this->db->getQuery(true);
		$allgroups = array();
		$rules     = array();

		// Get all asset rules of BwPostman
		$query->select('rules');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' LIKE ' . $this->db->quote('%com_bwpostman%'));

		$this->db->setQuery($query);

		try
		{
			$rules = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		foreach ($rules as $data)
		{
			$item = json_decode($data['rules']);
			// @ToDo: Do this saveguard against misspelled rules also at restore!!!!!
			// @ToDo: Ensure, that php errors and warnings don't appear in XML file
			if (!empty($item))
			{
				foreach ($item as $rule)
				{
					foreach ($rule as $key => $value)
					{
						$allgroups[] = $key;
					}
				}
			}
		}

		$groups = array_unique($allgroups);
		sort($groups);

		// Get the tree paths from the group(node) to the root
		$res_tree = array();
		foreach ($groups as $group)
		{
			$query = $this->db->getQuery(true);
			$query->select('p.id');
			$query->from($this->db->quoteName('#__usergroups') . ' AS n, ' . $this->db->quoteName('#__usergroups') . ' AS p');
			$query->where('n.lft BETWEEN p.lft AND p.rgt');
			$query->where('n.id = ' . (int) $group);
			$query->order('p.lft');

			$this->db->setQuery($query);

			try
			{
				$tree = $this->db->loadAssocList();
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}

			if (is_array($tree))
			{
				foreach ($tree as $key => $value)
				{
					$res_tree[] = $value['id'];
				}
			}
		}

		$allgroups     = array_merge($groups, $res_tree);
		$groups_unique = array();
		foreach ($allgroups as $row)
		{
			$groups_unique[$row] = $row;
		}

		asort($groups_unique);

		// Get all used user groups and their parents sorted by level
		$res_groups = array();
		foreach ($groups_unique as $group)
		{
			$query     = $this->db->getQuery(true);
			$sub_query = $this->db->getQuery(true);

			$sub_query->select('COUNT(*)-1');
			$sub_query->from($this->db->quoteName('#__usergroups') . ' AS n');
			$sub_query->from($this->db->quoteName('#__usergroups') . ' AS p');
			$sub_query->where('n.lft BETWEEN p.lft AND p.rgt');
			$sub_query->where('n.id = ' . (int) $group);
			$sub_query->group('n.lft');
			$sub_query->order('n.lft');

			$query->select('(' . $sub_query . ') AS level, p.id, p.title, p.parent_id');
			$query->from($this->db->quoteName('#__usergroups') . 'AS p');
			$query->where($this->db->quoteName('id') . ' = ' . (int) $group);

			$this->db->setQuery($query);

			try
			{
				$res_groups[] = $this->db->loadAssoc();
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}

		asort($res_groups);

		return $res_groups;
	}

	/**
	 * Method to get the needed tables and its properties from sql install file
	 *
	 * @return    mixed array/bool        true if all is ok
	 *
	 * @since    1.0.1
	 */
	public function getNeededTables()
	{
		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// get paths to sql install files
		$paths   = array();
		$paths[] = JPATH_ADMINISTRATOR . '/components/com_bwpostman/sql/';

		if (Folder::exists(JPATH_PLUGINS . '/bwpostman/'))
		{
			$path2     = JPATH_PLUGINS . '/bwpostman/';
			$p_folders = Folder::folders($path2);

			foreach ($p_folders as $folder)
			{
				if (Folder::exists($path2 . $folder . '/sql/'))
				{
					$paths[] = $path2 . $folder . '/sql/';
				}
			}
		}

		$tables = array();

		foreach ($paths as $path)
		{
			// get sql install file
			$filename = $path . 'install.sql';

			if (false === $fh = fopen($filename, 'r'))
			{ // File cannot be opened
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OPEN_INSTALL_FILE_ERROR', $filename);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				echo '<p class="bw_tablecheck_error">' . $message . '</p>';

				return false;
			}
			else
			{
				// empty arrays
				$file_content = array();
				$txt_array    = array();

				// get file content
				while (!feof($fh))
				{
					$file_content[] = fgets($fh);
				}

				fclose($fh);

				// eliminate unneeded rows (comments, empty lines, DROP TABLE)
				foreach ($file_content as $row)
				{
					if ((strpos($row, '--') === false) && (stripos($row, 'DROP') === false) && (trim($row) != ''))
					{
						$txt_array[] = $row;
					}
				}

				$queries = array();
				$string  = '';
				$i       = 0;
				foreach ($txt_array as $key => $value)
				{
					$pos = strpos($value, 'CREATE');
					if ($pos !== false)
					{
						if ($i != 0)
						{ // fill array only with complete query
							$queries[] = $string;
						}

						$string = $value . ' ';
					}
					else
					{
						$string .= $value . ' ';
					}

					$i++;
				}

				$queries[] = $string;

				if (count($queries))
				{
					foreach ($queries as $query)
					{
						$table = new stdClass();
						$query = implode(array_map('trim', preg_split('/(\n|\r\r)/i', $query)));
						$query = preg_replace('/\s+/', ' ', trim($query));

						$table->install_query = $query;

						// get table name
						$start = strpos($query, '#');
						if ($start !== false)
						{
							$stop        = strpos($query, '`', $start);
							$length      = $stop - $start;
							$table->name = substr($query, $start, $length);
						}

						// get engine
						$start = stripos($query, 'ENGINE');
						if ($start !== false)
						{
							$stop          = strpos($query, ' ', $start);
							$length        = $stop - $start;
							$table->engine = substr($query, $start + 7, $length - 7);
						}

						// get default character set
						$start = stripos($query, 'DEFAULT CHARSET');
						if ($start !== false)
						{
							$stop           = stripos($query, ' COLLATE');
							$length         = $stop - $start - 16;
							$table->charset = substr($query, $start + 16, $length);
						}

						// get default collation
						$start = stripos($query, 'COLLATE');
						if ($start !== false)
						{
							$stop             = stripos($query, ';', $start);
							$length           = $stop - $start - 8;
							$table->collation = substr($query, $start + 8, $length);
						}

						// get primary key
						$start = strripos($query, '(`') + 2;
						if ($start !== false)
						{
							$stop               = strripos($query, '`)');
							$length             = $stop - $start;
							$table->primary_key = str_replace("`", '', substr($query, $start, $length));
						}

						// eliminate primary key
						$start = stripos($query, ',PRIMARY');
						if ($start !== false)
						{
							$stop    = strpos($query, '`)') + 2;
							$length  = $stop - $start;
							$search  = substr($query, $start, $length);
							$sub_txt = str_replace($search, '', $query);
							$query   = trim($sub_txt);
						}

						// get columns definitions
						$start = strpos($query, '(');
						if ($start !== false)
						{
							$stop          = strripos($query, ')');
							$length        = $stop - $start;
							$column_string = substr($query, $start + 1, $length - 1);
							$columns       = explode(',', $column_string);

							foreach ($columns as $column)
							{
								$col_arr = new stdClass();

								// get column name
								$column = trim($column);
								$length = strpos($column, ' ');
								if ($length > 0)
								{
									$col_arr->Column = substr($column, 1, $length - 2);
									$sub_txt         = substr($column, $length + 1);
									$column          = $sub_txt;
								}

								// get column type
								$length = strpos($column, ' ');
								if ($length > 0)
								{
									$col_arr->Type = substr($column, 0, $length);
									$sub_txt       = substr($column, $length + 1);
									$column        = $sub_txt;
								}

								// get NOT NULL
								$start = stripos($column, 'NOT NULL');
								if ($start !== false)
								{
									$col_arr->Null = 'NO';
									$sub_txt       = str_replace('NOT NULL', '', $column);
									$column        = trim($sub_txt);
								}

								// get NULL
								$start = stripos($column, 'NULL');
								if ($start !== false)
								{
									$col_arr->Null = 'YES';
									$sub_txt       = str_replace('NULL', '', $column);
									$column        = trim($sub_txt);
								}

								// get autoincrement
								$start = stripos($column, 'auto_increment');
								if ($start !== false)
								{
									$col_arr->Extra = substr($column, $start, 15);
									$sub_txt        = str_replace('auto_increment', '', $column);
									$column         = trim($sub_txt);
									$table->auto    = $col_arr->Column;
								}

								// get default
								$start = stripos($column, 'default');
								if ($start !== false)
								{
									$start            = $start + 9;
									$stop             = strpos($column, "'", $start);
									$length           = $stop - $start;
									$col_arr->Default = substr($column, $start, $length);
									$sub_txt          = str_replace($col_arr->Default, '', $column);
									$column           = trim($sub_txt);
								}

								// get unsigned
								$start = stripos($column, 'unsigned');
								if ($start !== false)
								{
									$col_arr->Type .= ' unsigned';
								}

								$table->columns[] = $col_arr;
							} // end foreach columns
							$tables[] = $table;
						} // end get columns definitions
					} // end foreach queries
				} // end if queries exists
			} // end get file content
		} // end foreach file names

		return $tables;
	}

	/**
	 * Get the generic name of the table, converting the database prefix to the wildcard string. Based on Joomla JDatabaseExporter
	 *
	 * @param string $table The name of the table.
	 *
	 * @return   string             The name of the table with the database prefix replaced with #__.
	 *
	 * @since    1.0.1
	 */

	public static function getGenericTableName($table)
	{
		// get db prefix
		$prefix = Factory::getDbo()->getPrefix();

		// Replace the magic prefix if found.
		$table = preg_replace("|^$prefix|", '#__', $table);

		return $table;
	}

	/**
	 * Method to compare needed tables names with installed ones, check engine, default charset and primary key
	 *
	 * @param array  $neededTables      object list of tables, that must be installed
	 * @param array  $genericTableNames names of tables, that are installed
	 * @param string $mode              mode to check, "check and repair" or "restore"
	 *
	 * @return    boolean        true if all is ok
	 *
	 * @since    1.0.1
	 */
	public function checkTableNames($neededTables, $genericTableNames, $mode = 'check')
	{
		if (!is_array($neededTables) && !is_array($genericTableNames))
		{
			return false;
		}

		$neededTableNames = array();

		// extract table names from table object list,
		foreach ($neededTables as $table)
		{
			$neededTableNames[] = $table->name;
		}

		if (!$this->handleNeededTables($neededTableNames, $genericTableNames, $neededTables))
		{
			return false;
		}

		// Process obsolete tables only if in check mode
		if ($mode == 'check')
		{
			if (!$this->handleObsoleteTables($genericTableNames, $neededTableNames))
			{
				return false;
			}
		}

		if (!$this->handleTableProperties($neededTables))
		{
			return false;
		}

		$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_OK');
		echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

		return true;
	}

	/**
	 * Check if all needed tables are installed and install them, if not present
	 *
	 * @param array $neededTableNames
	 * @param array $genericTableNames
	 * @param array $neededTables
	 *
	 * @return boolean
	 *
	 * @since 2.4.0
	 */
	private function handleNeededTables(array $neededTableNames, array $genericTableNames, array $neededTables)
	{
		$diff_1 = array_diff($neededTableNames, $genericTableNames);

		if (!empty($diff_1))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED', implode(',', $diff_1));
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

			// set all install queries
			$queries = array();

			foreach ($neededTables as $table)
			{
				$queries[$table->name] = $table->install_query;
			}

			// install missing tables (complete queries exists in table object list from install file)
			foreach ($diff_1 as $missingTable)
			{
				$query = $queries[$missingTable];
				$this->db->setQuery($query);

				try
				{
					$createDB = $this->db->execute();

					if (!$createDB)
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_ERROR',	$missingTable);
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

						echo '<p class="bw_tablecheck_error">' . $message . '</p>';
					}
					else
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_SUCCESS', $missingTable);
						echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
					}
				}
				catch (RuntimeException $exception)
				{
					$message = $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}
			}
		}
		else
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ALL_TABLES_INSTALLED');
			echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
		}

		return true;
	}

	/**
	 * Check for obsolete tables and delete them, if necessary
	 *
	 * @param array $genericTableNames
	 * @param array $neededTableNames
	 *
	 * @return boolean
	 *
	 * @since 2.4.0
	 */
	private function handleObsoleteTables(array $genericTableNames, array $neededTableNames)
	{
		$diff_2 = array_diff($genericTableNames, $neededTableNames);

		if (!empty($diff_2))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE', implode(',', $diff_2));
			echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

			// delete obsolete tables
			foreach ($diff_2 as $obsoleteTable)
			{
				$query = "DROP TABLE IF EXISTS " . $obsoleteTable;

				$this->db->setQuery($query);
				try
				{
					$deleteDB = $this->db->execute();

					if (!$deleteDB)
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_ERROR', $obsoleteTable);
						echo '<p class="bw_tablecheck_error">' . $message . '</p>';
					}
					else
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_SUCCESS', $obsoleteTable);
						echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
					}
				}
				catch (RuntimeException $exception)
				{
					$message = $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}
			}
		}
		else
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NO_OBSOLETE_TABLES');
			echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
		}

		return true;
	}

	/**
	 * Check for correct tables properties and adjust them, if necessary
	 *
	 * @param array $neededTables
	 *
	 * @return boolean
	 *
	 * @since 2.4.0
	 */
	private function handleTableProperties(array $neededTables)
	{
		if(!$this->checkEngineAndCharset($neededTables))
		{
			return false;
		}

		$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ENGINE_OK');
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

		if (!$this->checkPrimaryAndIncrement($neededTables))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check for correct database engine, character set and collation
	 *
	 * @param array $neededTables
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function checkEngineAndCharset(array $neededTables)
	{
		foreach ($neededTables as $table)
		{
			try
			{
				$createTableQuery = $this->db->getTableCreate($table->name)[$table->name];
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ENGINE_OK');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
			$engine          = '';
			$c_set           = '';
			$collation       = '';

			// get engine of installed table
			$start = strpos($createTableQuery, 'ENGINE=');
			if ($start !== false)
			{
				$stop   = strpos($createTableQuery, ' ', $start);
				$length = $stop - $start - 7;
				$engine = substr($createTableQuery, $start + 7, $length);
			}

			// get default charset of installed table
			$start = strpos($createTableQuery, 'DEFAULT CHARSET=');
			$stop  = 0;
			if ($start !== false)
			{
				$stop   = strpos($createTableQuery, ' ', $start);
				$length = $stop - $start;
				$c_set  = substr($createTableQuery, $start + 16, $length);
			}

			// get collation of installed table
			$start = strpos($createTableQuery, 'COLLATE=', $stop);
			if ($start !== false)
			{
				$collation = substr($createTableQuery, $start + 8);
			}

			if ((strcasecmp($engine, $table->engine) != 0)
				|| (strcasecmp($c_set, $table->charset) != 0)
				|| (strcasecmp($collation, $table->collation) != 0))
			{
				$engine_text    = '';
				$c_set_text     = '';
				$collation_text = '';
				if ($engine != '')
				{
					$engine_text = ' ENGINE=' . $engine;
				}

				if ($c_set != '')
				{
					$c_set_text = ' DEFAULT CHARSET=' . $c_set;
				}

				if ($collation != '')
				{
					$collation_text = ' COLLATION ' . $collation;
				}

				$query = 'ALTER TABLE ' . $this->db->quoteName($table->name) . $engine_text . $c_set_text . $collation_text;
				$this->db->setQuery($query);

				try
				{
					$modifyTable = $this->db->execute();

					if (!$modifyTable)
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_ERROR', $table->name);
						echo '<p class="bw_tablecheck_error">' . $message . '</p>';

						return false;
					}
					else
					{
						$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_SUCCESS', $table->name);
						echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
					}
				}
				catch (RuntimeException $exception)
				{
					$message = $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Check for correct primary key and auto increment
	 *
	 * @param array $neededTables
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function checkPrimaryAndIncrement(array $neededTables)
	{
		foreach ($neededTables as $table)
		{
			$installed_key = $this->getInstalledPrimaryKey($table);

			if ($installed_key === false)
			{
				return false;
			}

			// compare primary key of installed table with needed one
			if (strcasecmp($table->primary_key, $installed_key) != 0)
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_WRONG', $table->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_WARNING, 'maintenance'));

				echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

				if ($installed_key != '')
				{
					if(!$this->dropWrongPrimaryKey($table, $installed_key))
					{
						return false;
					}
				}

				if(!$this->writeCorrectPrimaryKey($table))
				{
					return false;
				}
			}

			// get col name of autoincrement of installed table
			if (property_exists($table, 'auto'))
			{
				$increment_key = $this->getAutoIncrement($table);

				if($increment_key === false)
				{
					return false;
				}

				if (strcasecmp($table->auto, $increment_key) != 0)
				{
					if(!$this->setCorrectAutoIncrement($table))
					{
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get primary key of installed table
	 *
	 * @param $table
	 *
	 * @return string|boolean
	 *
	 * @since 2.4.0
	 */
	private function getInstalledPrimaryKey($table)
	{
		try
		{
			$installed_key_tmp = $this->db->getTableKeys($table->name);
		}
		catch (RuntimeException $exception)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ENGINE_OK');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return true;
		}

		$installed_key     = '';

		if (count($installed_key_tmp) > 1)
		{
			for ($i = 0; $i < count($installed_key_tmp); $i++)
			{
				$installed_key .= $installed_key_tmp[$i]->Column_name . ',';
			}

			$length        = strlen($installed_key) - 1;
			$tmp_string    = substr($installed_key, 0, $length);
			$installed_key = $tmp_string;
		}
		elseif (count($installed_key_tmp) == 1)
		{
			$installed_key .= $installed_key_tmp[0]->Column_name;
		}

		return $installed_key;
	}

	/**
	 * Drop wrong primary key of installed table
	 *
	 * @param $table
	 * @param $installed_key
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function dropWrongPrimaryKey($table, $installed_key)
	{
		$type = '';
		foreach ($table->columns as $column)
		{
			if ($column->Column == $installed_key)
			{
				$type = $column->Type;
			}
		}

		$query = 'ALTER TABLE ' . $this->db->quoteName($table->name);
		$query .= ' MODIFY ' . $this->db->quoteName($installed_key) . ' ';
		$query .= $type . ', DROP PRIMARY KEY';
		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return true;
	}

	/**
	 * Write correct primary key to installed table
	 *
	 * @param $table
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function writeCorrectPrimaryKey($table)
	{
		$query = 'ALTER TABLE ' . $this->db->quoteName($table->name) . ' ADD PRIMARY KEY (' . $this->db->quoteName($table->primary_key) . ')';
		$this->db->setQuery($query);

		try
		{
			$modifyKey = $this->db->execute();

			if (!$modifyKey)
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_ERROR', $table->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_error">' . $message . '</p>';

				return false;
			}
			else
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_SUCCESS', $table->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
			}
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return true;
	}

	/**
	 * Get auto increment from installed table
	 *
	 * @param $table
	 *
	 * @return string|bool
	 *
	 * @since 2.4.0
	 */
	private function getAutoIncrement($table)
	{
		$query = 'SHOW columns FROM ' . $this->db->quoteName($table->name) . ' WHERE extra = "auto_increment"';
		$this->db->setQuery($query);

		try
		{
			$increment_key = $this->db->loadResult();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $increment_key;
	}

	/**
	 * Set correct auto increment to installed table
	 *
	 * @param $table
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function setCorrectAutoIncrement($table)
	{
		$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_WRONG', $table->name);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

		$query = 'ALTER TABLE ' . $this->db->quoteName($table->name);
		$query .= ' MODIFY ' . $this->db->quoteName($table->primary_key);
		$query .= ' INT AUTO_INCREMENT';
		$this->db->setQuery($query);

		try
		{
			$incrementKey = $this->db->execute();

			if (!$incrementKey)
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_ERROR', $table->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				echo '<p class="bw_tablecheck_error">' . $message . '</p>';

				return false;
			}
			else
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_SUCCESS', $table->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
			}
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return true;
	}

	/**
	 * Method to check needed table columns
	 *
	 * @param object $checkTable object of table, that must be installed
	 *
	 * @return    boolean|integer
	 *
	 * @since    1.0.1
	 */
	public function checkTableColumns($checkTable)
	{
		if (!is_object($checkTable))
		{
			return 0;
		}

		$neededColumns    = array();

		foreach ($checkTable->columns as $col)
		{
			if (is_object($col))
			{
				$neededColumns[] = ArrayHelper::fromObject($col, true);
			}
			else
			{
				$neededColumns[] = $col;
			}
		}

		$search_cols_2 = array();

		foreach ($neededColumns as $col)
		{
			$search_cols_2[] = $col['Column'];
		}

		$installedColumns = array();

		try
		{
			$columnsObject = $this->db->getTableColumns($checkTable->name, false);
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			return false;
		}

		foreach ($columnsObject as $col)
		{
			$installedColumns[] = ArrayHelper::fromObject($col, true);
		}

		// prepare check for col names
		$search_cols_1 = array();

		foreach ($installedColumns as $col)
		{
			$search_cols_1[] = $col['Field'];
		}

		// check for col names
		for ($i = 0; $i < count($neededColumns); $i++)
		{
			// check for needed col names
			if($this->handleNeededColumns($neededColumns, $i, $search_cols_1, $checkTable) === false)
			{
				return false;
			}

			// check for obsolete col names
			if($this->handleObsoleteColumns($installedColumns[$i], $search_cols_2, $checkTable) === false)
			{
				return  false;
			}
		}

		$message = str_pad(Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_OK', $checkTable->name), 4096);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_ok">' . $message	 . '</p>';

		if(!$this->handleColumnAttributes($neededColumns, $installedColumns, $checkTable))
		{
			return false;
		}

		$message = str_pad(strip_tags(Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_ATTRIBUTES_OK', $checkTable->name)), 4096);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

		return 1;
	}


	/**
	 * Check for missing columns and install them, if needed
	 *
	 * @param array $neededColumns
	 * @param       $i
	 * @param array $search_cols_1
	 * @param       $checkTable
	 *
	 * @return boolean|integer
	 *
	 * @since 2.4.0
	 */
	private function handleNeededColumns(array $neededColumns, $i, array $search_cols_1, $checkTable)
	{
		if (array_search($neededColumns[$i]['Column'], $search_cols_1) === false)
		{
			($neededColumns[$i]['Null'] == 'NO') ? $null = ' NOT NULL' : $null = ' NULL ';
			(isset($neededColumns[$i]['Default'])) ? $default = ' DEFAULT ' . $this->db->quote($neededColumns[$i]['Default']) : $default = '';

			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COLS', $neededColumns[$i]['Column'], $checkTable->name);
			echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

			$query = "ALTER TABLE " . $this->db->quoteName($checkTable->name);
			$query .= " ADD " . $this->db->quoteName($neededColumns[$i]['Column']);
			$query .= ' ' . $neededColumns[$i]['Type'] . $null . $default;
			$query .= " AFTER " . $this->db->quoteName($neededColumns[$i - 1]['Column']);

			$this->db->setQuery($query);

			try
			{
				$insertCol = $this->db->execute();

				if (!$insertCol)
				{
					$message = Text::sprintf(
						'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_ERROR',
						$neededColumns[$i]['Column'],
						$checkTable->name
					);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					echo '<p class="bw_tablecheck_error">' . $message . '</p>';

					return 0;
				}
				else
				{
					$message = str_pad(
						Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_SUCCESS',
							$neededColumns[$i]['Column'],
							$checkTable->name),
						4096
					);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

					echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

					return 2; // reset iteration
				}
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Check for obsolete columns and remove them, if needed
	 * @param       $installedColumns
	 * @param array $search_cols_2
	 * @param       $checkTable
	 *
	 * @return boolean|integer
	 *
	 * @since 2.4.0
	 */
	private function handleObsoleteColumns($installedColumns, array $search_cols_2, $checkTable)
	{
		if (array_search($installedColumns['Field'], $search_cols_2) === false)
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COLS',
				$installedColumns['Field'],
				$checkTable->name
			);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

			$query = "ALTER TABLE " . $this->db->quoteName($checkTable->name) . " DROP " . $this->db->quoteName($installedColumns['Field']);

			$this->db->setQuery($query);

			try
			{
				$deleteCol = $this->db->execute();

				if (!$deleteCol)
				{
					$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_ERROR',
						$installedColumns['Field'],
						$checkTable->name);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					echo '<p class="bw_tablecheck_error">' . $message . '</p>';

					return 0;
				}
				else
				{
					$message = str_pad(
						Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_SUCCESS',
							$installedColumns['Field'],
							$checkTable->name),
						4096
					);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

					echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

					return 2; // reset iteration
				}
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Check for column attributes and correct them, if needed
	 *
	 * @param array $neededColumns
	 * @param array $installedColumns
	 * @param       $checkTable
	 *
	 * @return bool
	 *
	 * @since 2.4.0
	 */
	private function handleColumnAttributes(array $neededColumns, array $installedColumns, $checkTable)
	{
		for ($i = 0; $i < count($neededColumns); $i++)
		{
			$diff = array_udiff($neededColumns[$i], $installedColumns[$i], 'strcasecmp');

			if (!empty($diff))
			{
				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES',
					implode(',', array_keys($diff)),
					$neededColumns[$i]['Column'],
					$checkTable->name);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_WARNING, 'maintenance'));

				echo '<p class="bw_tablecheck_warn">' . $message . '</p>';

				// install missing columns
				foreach (array_keys($diff) as $missingCol)
				{
					if ($neededColumns[$i]['Null'] == 'NO')
					{
						$null = ' NOT NULL';
					}
					else
					{
						$null = 'YES';
					}

					if (isset($neededColumns[$i]['Default']))
					{
						$default = ' DEFAULT ' . $this->db->quote($neededColumns[$i]['Default']);
					}
					else
					{
						$default = '';
					}

					$query = "ALTER TABLE " . $this->db->quoteName($checkTable->name);
					$query .= " MODIFY " . $this->db->quoteName($neededColumns[$i]['Column']) . ' ' . $neededColumns[$i]['Type'] . $null . $default;

					if (array_key_exists('Extra', $neededColumns[$i]))
					{
						$query .= " " . $neededColumns[$i]['Extra'];
					}

					$this->db->setQuery($query);

					try
					{
						$alterCol = $this->db->execute();

						if (!$alterCol)
						{
							$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_ERROR',
								$missingCol,
								$neededColumns[$i]['Column'],
								$checkTable->name);
							$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

							echo '<p class="bw_tablecheck_error">' . $message . '</p>';
						}
						else
						{
							$message = str_pad(
								Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_SUCCESS',
									$missingCol,
									$neededColumns[$i]['Column'],
									$checkTable->name),
								4096
							);
							$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

							echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
						}
					}
					catch (RuntimeException $exception)
					{
						$message = $exception->getMessage();
						$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to check, if column asset_id has a real value. If not, there is no possibility to delete data sets in BwPostman.
	 * Therefore each dataset without real value for asset_id has to be stored one time, to get this value
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 *
	 */
	public function checkAssetId()
	{
		if($this->getTableNamesFromDB() === false)
		{
			return false;
		}

		// Set tables that have column asset_id
		foreach ($this->tableNames as $table)
		{
			// Shortcut
			$tableNameGeneric = $table['tableNameGeneric'];
			$section          = strtolower($table['tableNameUC']);
			$hasAsset         = $this->checkForAsset($tableNameGeneric);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				// Get all item ids and entered asset_id
				$itemAssetList = $this->getItemAssetList($tableNameGeneric);

				if($itemAssetList === false)
				{
					return false;
				}

				// The following array $itemIdsWithoutAssets holds ids of items, where the asset_id does not exists at
				// section table or assets table or where an existing asset_id does not match the asset name, build by
				// component.section.item_id.
				// But be careful! This array also contains items, for which an appropriate asset name exists, but with
				// wrong asset_id at items table. The assets for these items cannot be inserted at assets table, because
				// they exists. So there is a counter-check necessary at the end of this check.
				$itemIdsWithoutAssets = array();

				foreach ($itemAssetList as $item)
				{
					$assetName = 'com_bwpostman.' . $section . '.' . $item->id;
					// Check if asset_id is 0 or null
					if ($item->asset_id === 0 || $item->asset_id === null)
					{
						// If so, we need a new asset (add to array $itemIdsWithoutAssets)
						$itemIdsWithoutAssets[] = $item->id;
					}
					else
					{
						// Else check if asset_id exists at assets table
						$assetExists = $this->checkAssetIdExists($item->asset_id);

						if ($assetExists === -1)
						{
							return false;
						}

						if ($assetExists)
						{
							// If so, check, if asset name fits component, section and item id
							$assetNameFits = $this->checkAssetNameFits($item->asset_id, $assetName);

							if (!$assetNameFits === -1)
							{
								return false;
							}

							// If asset name not fits, we need a new asset (add to array $itemIdsWithoutAssets)
							if (!$assetNameFits)
							{
								$itemIdsWithoutAssets[] = $item->id;
							}
						}
						else
						{
							// Else add to array
							$itemIdsWithoutAssets[] = $item->id;
						}
					}
				}

				// Counter check: See, if assets with an asset name matching item exists. If so, collect them to update
				// items table and remove it from list to insert
				$assetIdsByName          = array();
				$nbrItemIdsWithoutAssets = count($itemIdsWithoutAssets);

				if ($nbrItemIdsWithoutAssets > 0)
				{
					for ($i = 0; $i < $nbrItemIdsWithoutAssets; $i++)
					{
						$item      = $itemIdsWithoutAssets[$i];
						$assetName = 'com_bwpostman.' . $section . '.' . $item;

						$assetId = $this->getAssetIdByAssetName($assetName);

						if ($assetId ===  -1)
						{
							return false;
						}

						if (is_integer($assetId))
						{
							unset($itemIdsWithoutAssets[$i]);
							$assetIdsByName[$item] = $assetId;
						}
					}
				}

				// Get complete table items, where assets are to heal (collected above) and heal them
				if (is_array($itemIdsWithoutAssets) && count($itemIdsWithoutAssets) > 0)
				{
					$itemsWithoutAsset = $this->getCompleteItemsWithoutAssetId($tableNameGeneric, $itemIdsWithoutAssets);

					if ($itemIdsWithoutAssets === false)
					{
						return false;
					}

					if (is_array($itemsWithoutAsset) && count($itemsWithoutAsset) > 0)
					{
						for ($i = 0; $i < count($itemsWithoutAsset); $i++)
						{
							$itemsWithoutAsset[$i]['asset_id'] = 0;
						}

						$mapOldAssetIdsToNew = $this->insertAssets($itemsWithoutAsset, $table);

						if ($mapOldAssetIdsToNew === false)
						{
							return false;
						}

						if (!$this->insertItems($itemsWithoutAsset, $table['tableNameGeneric'], $mapOldAssetIdsToNew))
						{
							return  false;
						}
					}
				}

				// Correct asset_id at items table, where asset_id does not match an appropriate asset, but an appropriate
				// asset name is found at assets table
				if (is_array($assetIdsByName) && count($assetIdsByName) > 0)
				{
					if(!$this->healAssetsAtItemsTable($tableNameGeneric, $assetIdsByName))
					{
						return false;
					}
				}

				$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSET_OK', $tableNameGeneric);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
			}
		}

		return true;
	}

	/**
	 * Method to check, if column asset_id has a real value. If not, there is no possibility to delete data sets in BwPostman.
	 * Therefore each dataset without real value for asset_id has to be stored one time, to get this value
	 *
	 * @return    bool
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 *
	 */
	public function checkAssetParentId()
	{
		// Set tables that has column asset_id
		if($this->getTableNamesFromDB() === false)
		{
			return false;
		}

		foreach ($this->tableNames as $table)
		{
			// Shortcut
			$tableNameGeneric = $table['tableNameGeneric'];
			$hasAsset         = $this->checkForAsset($tableNameGeneric);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				// Get section asset
				$sectionAsset = $this->getBaseAsset($table['tableNameRaw'], true);

				if ($sectionAsset === false)
				{
					return  false;
				}

				// Replace parent asset id of items with component as parent
				$query = $this->db->getQuery(true);

				$query->update($this->db->quoteName('#__assets'));
				$query->set($this->db->quoteName('parent_id') . " = " . $this->db->Quote($sectionAsset['id']));
				$query->where($this->db->quoteName('name') . ' LIKE ' . $this->db->Quote($sectionAsset['name'] . '.%'));
				$query->where($this->db->quoteName('parent_id') . ' <> ' . $this->db->Quote($sectionAsset['id']));

				$this->db->setQuery($query);

				try
				{
					$this->db->execute();
				}
				catch (RuntimeException $exception)
				{
					$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $sectionAsset['name']);
					$message .= ': ';
					$message .= $exception->getMessage();

					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to check, if user_id of subscriber matches ID in joomla user table, updating if mail address exists.
	 * Only datasets with entered user_id in table subscribers will be checked
	 *
	 * @return    boolean
	 *
	 * @since    1.0.1
	 */
	public function checkUserIds()
	{
		$query = $this->db->getQuery(true);

		try
		{
			$query->select('*');
			$query->from($this->db->quoteName('#__bwpostman_subscribers'));
			$query->where($this->db->quoteName('user_id') . ' > ' . (int) 0);

			$this->db->setQuery($query);
			$subscribers = $this->db->loadObjectList();

			// update user_id in subscribers table
			foreach ($subscribers as $subscriber)
			{
				// get ids from users table if mail address exists in user table
				$query->clear();
				$query->select($this->db->quoteName('id'));
				$query->from($this->db->quoteName('#__users'));
				$query->where($this->db->quoteName('email') . ' = ' . $this->db->quote($subscriber->email));

				$this->db->setQuery($query);
				$subscriber->user_id = $this->db->loadResult();

				// update subscribers table
				$query->clear();
				$query->update($this->db->quoteName('#__bwpostman_subscribers'));
				$query->set($this->db->quoteName('user_id') . " = " . (int) $subscriber->user_id);
				$query->where($this->db->quoteName('id') . ' = ' . (int) $subscriber->id);

				$this->db->setQuery($query);
				$this->db->execute();
			}
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return true;
	}

	/**
	 * Builds the XML structure to export. Based on Joomla JDatabaseExporter
	 *
	 * @param string           $tableName name of table to build structure for
	 * @param SimpleXMLElement $tablesXml
	 *
	 * @return    boolean    true on success, false on database exception.
	 *
	 * @since    1.0.1
	 */
	private function buildXmlStructure($tableName, $tablesXml)
	{
		// Get the details columns information and install query.
		try
		{
			$keys   = $this->db->getTableKeys($tableName);
			$fields = $this->db->getTableColumns($tableName, false);
			$query  = implode('', $this->db->getTableCreate($tableName));

		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		$tableStructureXML = $tablesXml->addChild('table_structure');
		$tableStructureXML->addAttribute('table', $tableName);
		$tableNameXml = $tableStructureXML->addChild('table_name');
		$tableNameXml->addChild('name', $tableName);
		$installQueryXml = $tableStructureXML->addChild('install_query');
		$installQueryXml->addChild('query', $query);


		if (is_array($fields))
		{
			$fieldsXml = $tableStructureXML->addChild('fields');

			foreach ($fields as $field)
			{
				$fieldXml = $fieldsXml->addChild('field');
				$fieldXml->addChild('Column', $field->Field);
				$fieldXml->addChild('Type', $field->Type);
				$fieldXml->addChild('Null', $field->Null);
				$fieldXml->addChild('Key', $field->Key);

				if (isset($field->Default))
				{
					$fieldXml->addChild('Default', $field->Default);
				}

				$fieldXml->addChild('Extra', $field->Extra);
			}
		}

		if (is_array($keys))
		{
			$keysXml = $tableStructureXML->addChild('keys');

			foreach ($keys as $key)
			{
				$keyXml = $keysXml->addChild('key');
				$keyXml->addChild('Non_unique', $key->Non_unique);
				$keyXml->addChild('Key_name', $key->Key_name);
				$keyXml->addChild('Seq_in_index', $key->Seq_in_index);
				$keyXml->addChild('Column_name', $key->Column_name);
				$keyXml->addChild('Collation', $key->Collation);
				$keyXml->addChild('Null', $key->Null);
				$keyXml->addChild('Index_type', $key->Index_type);
				$keyXml->addChild('Comment', $key->Comment);
			}
		}

		return true;
	}

	/**
	 * Builds the XML data to export
	 *
	 * @param string           $tableName name of table
	 * @param SimpleXMLElement $tablesXml    XML element tables
	 *
	 * @return   bool        True on success
	 *
	 * @since    1.0.1
	 */
	private function buildXmlData($tableName, $tablesXml)
	{
		$data = $this->getTableDataToSave($tableName);

		$tableXml = $tablesXml->addChild('table_data');
		$tableXml->addAttribute('table', $tableName);

		if (is_array($data))
		{
			foreach ($data as $item)
			{
				$datasetXml = $tableXml->addChild('dataset');

				foreach ($item as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));

					if (((($tableName == '#__bwpostman_sendmailcontent') || ($tableName == '#__bwpostman_tc_sendmailcontent')) && ($key == 'body'))
						|| (($tableName == '#__bwpostman_newsletters') && ($key == 'html_version'))
						|| (($tableName == '#__bwpostman_templates')
							&& (($key == 'tpl_html')
								|| ($key == 'tpl_css')
								|| ($key == 'tpl_article')
								|| ($key == 'tpl_divider')))
					)
					{
						$insert_string = '<![CDATA[' . $insert_string . ']]>';
					}

					$datasetXml->addChild($key, $insert_string);
				}
			}
		}

		return true;
	}

	/**
	 * Builds the XML assets to export
	 *
	 * @param string           $tableName name of table
	 * @param SimpleXMLElement $tablesXml
	 *
	 * @return boolean true on success, false on failure
	 *
	 * @since    1.0.1
	 */
	private function buildXmlAssets($tableName, $tablesXml)
	{
		$table_name_raw = $this->getRawTableName($tableName);

		// @ToDo: use checkForAsset($table)
		if (in_array($table_name_raw, $this->assetTargetTables))
		{
			$data = $this->getTableAssetData($table_name_raw);

			if ($data === false)
			{
				return  false;
			}

			$tableAssetsXml = $tablesXml->addChild('table_assets');
			$tableAssetsXml->addAttribute('table', $tableName);

			if (is_array($data))
			{
				foreach ($data as $item)
				{
					$datasetXml = $tableAssetsXml->addChild('dataset');

					foreach ($item as $key => $value)
					{
						$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
						$datasetXml->addChild($key, $insert_string);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Builds the XML data footer for the tables to export
	 *
	 * @return    string    An XML string
	 *
	 * @since    1.0.1
	 *
	 * @deprecated since 2.4.0
	 */
	private function buildXmlFooter()
	{
		$buffer = array();

		$buffer[] = "\t</database>";
		$buffer[] = '</mysqldump>';

		return implode("\n", $buffer);
	}

	/**
	 * Method to output general information
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function outputGeneralInformation()
	{
		// Output general information
		$generals = Factory::getApplication()->getUserState('com_bwpostman.maintenance.generals', null);

		if (key_exists('BwPostmanVersion', $generals) || key_exists('SaveDate', $generals))
		{
			echo '<h4>' . Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_OUTPUT_GENERALS') . '</h4>';
			if (key_exists('BwPostmanVersion', $generals))
			{
				$message =  Text::_('COM_BWPOSTMAN_VERSION') . $generals['BwPostmanVersion'];
				echo '<p class="bw_tablecheck_info">' . $message . '</p>';
			}

			if (key_exists('SaveDate', $generals))
			{
				$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_DATE') . $generals['SaveDate'];
				echo '<p class="bw_tablecheck_info">' . $message . '</p>';
			}
		}
	}

	/**
	 * Method to the rewrite user groups in assets if needed
	 *
	 * @param   $table_names    array   names of tables
	 *
	 * stores the result array in state
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function processAssetUserGroups($table_names)
	{
		// process user groups, if they exists in backup
		$com_assets = Factory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());
		$usergroups = Factory::getApplication()->getUserState('com_bwpostman.maintenance.usergroups', array());
		$tmp_file   = Factory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
		$fp         = fopen($tmp_file, 'r');
		$tables     = unserialize(fread($fp, filesize($tmp_file)));
		Factory::getApplication()->setUserState('com_bwpostman.maintenance.tables', $tables);
		fclose($fp);

		if (count($usergroups))
		{
			$groupsToReplace = $this->getCurrentUserGroups($usergroups);

			if ($groupsToReplace === -1)
			{
				return false;
			}

			if (is_array($groupsToReplace))
			{
				// rewrite component asset user groups
				if (!$this->rewriteAssetUserGroups('component', $com_assets, $groupsToReplace))
				{
					return  false;
				}

				$com_assets = Factory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);

				// rewrite table asset user groups
				foreach ($table_names as $table)
				{
					// table with assets?
					if (key_exists('table_assets', $tables[$table]))
					{
						// get table assets
						$assets = $tables[$table]['table_assets'];
						if (!$this->rewriteAssetUserGroups($table, $assets, $groupsToReplace))
						{
							return  false;
						}
					}
				}
			}

			Factory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);
			Factory::getApplication()->setUserState('com_bwpostman.maintenance.usergroups', '');

			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_PROCESSED');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
		}
		else
		{
			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_MESSAGE');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
		}

		return  true;
	}

	/**
	 * Method to the delete existing tables,create them anew, update component asset (rules) and initialize table assets
	 *
	 * @param array $tables array of generic table names read from backup file
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 *
	 * @since    2.0.0
	 */
	public function anewBwPostmanTables($tables)
	{
		// @ToDo: Check for process of plugin tables
		$tmp_file      = Factory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
		$fp            = fopen($tmp_file, 'r');
		$tablesQueries = unserialize(fread($fp, filesize($tmp_file)));

		// delete tables and create it anew
		foreach ($tables as $table)
		{
			if (!$this->deleteBwPostmanTable($table))
			{
				return false;
			}

			if (!$this->createBwPostmanTableAnew($table, $tablesQueries))
			{
				return  false;
			}
		}

		// Update component asset and initialize section assets
		if (!$this->createBaseAssets(true))
		{
			return false;
		}

		return  true;
	}

	/**
	 * Method to the rewrite tables content one by one from backup file
	 *
	 * @param string  $table     generic name of table to rewrite
	 * @param boolean $lastTable is this the last table?
	 *
	 * @return    boolean
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function reWriteTables($table, $lastTable = false)
	{
		$tmp_file        = Factory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
		$tmpFileExists   = file_exists($tmp_file);
		$dest            = Factory::getApplication()->getUserState('com_bwpostman.maintenance.dest', '');
		$tablesFromState = Factory::getApplication()->getUserState('com_bwpostman.maintenance.tables', array());

		if ($tmpFileExists)
		{
			$fp = fopen($tmp_file, 'r');

			try
			{
				$tables          = unserialize(fread($fp, filesize($tmp_file)));
				$asset_loop      = 0;
				$asset_siblings   = 0;
				$asset_transform = array();
				$rawTableName    = $this->getRawTableName($table);

				$base_asset      = $this->getBaseAsset($rawTableName);

				if ($base_asset === false)
				{
					return false;
				}

				try
				{
					$this->assetColnames = array_keys($this->db->getTableColumns('#__assets'));
				}
				catch (RuntimeException $exception)
				{
					$message =  $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}

				$asset_name = $base_asset['name'];

				// set some loop values (block size, …)
				$data_loop_max = $this->getDataLoopMax($table);
				$max_count     = ini_get('max_execution_time');
				$data_max      = 0;
				if (key_exists('table_data', $tables[$table]))
				{
					$data_max = count($tables[$table]['table_data']);
				}

				$asset_loop_max = 1000;
				$asset_max      = 0;
				if (isset($tables[$table]['table_assets']))
				{
					$tables[$table]['table_assets'] = $tablesFromState[$table]['table_assets'];
					$asset_max = count($tables[$table]['table_assets']);
				}

				//Asset Inserting
				if ($asset_name != '')
				{
					$s     = 0;
					$count = 0;

					// if there are data sets
					if ($asset_max)
					{
						$asset_loop = 0;
					}

					// … insert data sets…
					if (isset($tables[$table]['table_assets']))
					{
						if ($tables[$table]['table_assets'][0]['name'] === $asset_name)
						{ // update base asset
							$update_asset = array_shift($tables[$table]['table_assets']);

							if (!$this->updateBaseAsset($update_asset))
							{
								return false;
							}
						}
						else
						{ // process dataset assets
							foreach ($tables[$table]['table_assets'] as $asset)
							{
								$asset_loop++;

								if ($count++ == $max_count)
								{
									$count = 0;
									ini_set('max_execution_time', ini_get('max_execution_time'));
								}

								// collect data sets until loop max
								$dataset[] = $this->prepareAssetValues($asset, $asset_transform, $s, $base_asset, $asset_siblings);
								$s++;

								// if asset loop max is reached or last data set, insert into table
								if (($asset_loop == $asset_loop_max) || ($s == $asset_max))
								{
									// write collected assets to table
									if (!$this->writeLoopAssets($dataset, $s, $base_asset, $asset_transform))
									{
										return false;
									}

									//reset loop values
									$asset_loop = 0;
									$dataset    = array();
								}
							} // end foreach table assets
						} // end switch base asset
					} // end table assets exists
				} // end asset inserting

				/*
				 * Import data (can't use table bind/store, because we have IDs and Joomla sets mode to update, if ID is set,
				 * but in empty tables there is nothing to update)
				 */
				$s     = 0;
				$count = 0;

				// if there are data sets
				if ($data_max)
				{
					$dataset   = array();
					$data_loop = 0;

					// … insert data sets…
					foreach ($tables[$table]['table_data'] as $item)
					{
						$data_loop++;

						// update asset_id
						if ($asset_name != '')
						{
							$asset_found = false;
							for ($i = 0; $i < count($asset_transform); $i++)
							{
								$new_id = array_search($item['asset_id'], $asset_transform[$i]);
								if ($new_id !== false)
								{
									$item['asset_id'] = $asset_transform[$i]['newAssetId'];
									$asset_found      = true;
									break;
								}
							}

							if (!$asset_found)
							{
								$item['asset_id'] = 0;
							}
						}

						if ($count++ == $max_count)
						{
							$count = 0;
							ini_set('max_execution_time', ini_get('max_execution_time'));
						}

						// collect data sets until loop max
						$values = $this->dbQuoteArray($item);

						$dataset[] = '(' . implode(',', $values) . ')';
						$s++;

						// if data loop max is reached or last data set, insert into table
						if (($data_loop == $data_loop_max) || ($s == $data_max))
						{
							// write collected data sets to table
							if (!$this->writeLoopDatasets($dataset, $table))
							{
								return false;
							}

							// reset loop values
							$data_loop = 0;
							$dataset   = array();
						}
					} // end foreach table items
				} // endif data sets exists

				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STORE_SUCCESS', $table);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p><br />';

				if ($table == '#__bwpostman_subscribers')
				{
					if (!self::checkUserIds())
					{
						return false;
					}
				}

				/*
				 * // For transaction test purposes only
				if($table_name_raw == 'newsletters') {
					throw new BwException(Text::_('Test-Exception Newsletter written'));
				}
				*/

				if ($lastTable)
				{
					fclose($fp);

					unlink($tmp_file);
					unlink($dest);
					$this->deleteRestorePoint();
				}
			}
//			@ToDo: All runtime exceptions are handled in sub routines, catch will never met, so close and unlink have to be done other way
			catch (RuntimeException $exception)
			{
				fclose($fp);
				unlink($tmp_file);
				unlink($dest);

				$message =  $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));
			}
		}
		return true;
	}

	/**
	 * Method to get the version of BwPostman
	 *
	 * @return    string    version of BwPostman
	 *
	 * @since    1.0.8
	 */
	private function getBwPostmanVersion()
	{
		$query  = $this->db->getQuery(true);
		$result = '';

		$query->select($this->db->quoteName('manifest_cache'));
		$query->from($this->db->quoteName('#__extensions'));
		$query->where($this->db->quoteName('element') . " = " . $this->db->quote('com_bwpostman'));
		$this->db->setQuery($query);

		try
		{
			$result = $this->db->loadResult();
		}
		catch (RuntimeException $exception)
		{
			$message =  $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

		}

		$manifest = json_decode($result, true);

		return $manifest['version'];
	}

	/**
	 * Method to get the database name
	 *
	 * @return    string    database name
	 *
	 * @since    1.0.1
	 */
	protected static function getDBName()
	{
		$config = Factory::getConfig();

		// Get database name
		return $config->get('db', '');
	}

	/**
	 * Method parse XML data
	 *
	 * stores the result array in state
	 *
	 * @param string $file
	 *
	 * @return  array|boolean   $table_names      array of generic table names
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function parseTablesData($file)
	{
		$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
		$message =  sprintf('Memory   consumption before parsing:  %01.3f MB', $memoryConsumption);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

		if ($file == '')
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_ERROR_NO_FILE');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		// get import file
		if (false === $fh = fopen($file, 'rb'))
		{ // File cannot be opened
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OPEN_FILE_ERROR', $file);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		// get XML data
		$xml = new SimpleXMLElement($file, null, true);
		fclose($fh);

		// check if xml file is ok (most error case: non-xml-conform characters in xml file)
		if (!is_object($xml))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_READ_XML_ERROR', $file);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		if (!property_exists($xml, 'database'))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_WRONG_FILE_ERROR', $file);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
		$message =  sprintf('Memory consumption while parsing with XML file: %01.3f MB', $memoryConsumption);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

		// Get general data
		$generals = array();
		if (property_exists($xml->database->Generals, 'BwPostmanVersion'))
		{
			$generals['BwPostmanVersion'] = (string) $xml->database->Generals->BwPostmanVersion;
		}

		if (property_exists($xml->database->Generals, 'SaveDate'))
		{
			$generals['SaveDate'] = (string) $xml->database->Generals->SaveDate;
		}

		Factory::getApplication()->setUserState('com_bwpostman.maintenance.generals', $generals);

		// Get component asset
		$com_assets = array();
		if (property_exists($xml->database->Generals, 'component_asset'))
		{
			$com_assets[] = get_object_vars($xml->database->Generals->component_asset);
		}
		else
		{
			if (property_exists($xml->database->Generals, 'component_assets'))
			{
				$comAssetsXml = get_object_vars($xml->database->Generals->component_assets);
				if (count($comAssetsXml))
				{
					foreach ($comAssetsXml['dataset'] as $item)
					{
						$com_assets[] = get_object_vars($item);
					}
				}
			}
		}

		Factory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);

		// Get backed up user groups
		$usergroups = array();
		if (property_exists($xml->database->Generals, 'component_usergroups'))
		{
			$u_groups = get_object_vars($xml->database->Generals->component_usergroups);
			if (count($u_groups))
			{
				foreach ($u_groups['usergroup'] as $item)
				{
					$usergroups[] = get_object_vars($item);
				}
			}
		}

		Factory::getApplication()->setUserState('com_bwpostman.maintenance.usergroups', $usergroups);

		// Get all tables from the xml file converted to arrays recursively, results in an array/list of table-arrays
		$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_PARSE_DATA');
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<h4>' . $message . '</h4>';

		$x_tables = array();

		foreach ($xml->database->tables as $table)
		{
			$x_tables[] = $table;
		}

		unset($xml);
		unset($table);

		if (count($x_tables) === 0)
		{
			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_NO_TABLES_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		$adjust_prefix = false;

		// get db prefix
		$new_prefix = $this->db->getPrefix();

		$sample_table = $x_tables[0];
		$sample_query = (string) $sample_table->table_structure->install_query->query;
		$is_prefix    = substr($sample_query, 14, (strpos($sample_query, '_') - 13));
		if ($is_prefix != $new_prefix)
		{
			$adjust_prefix = true;
		}

		unset($sample_table);
		unset($sample_query);

		// extract table names
		$table_names = array();
		foreach ($x_tables as $table)
		{
			$table_names[] = (string) $table->table_structure->table_name->name;
		}

		unset($table);

		// get buffer file
		$tmp_file = Factory::getConfig()->get('tmp_path') . '/bwpostman_restore.tmp';
		if (false === $fp = fopen($tmp_file, 'w+'))
		{ // File cannot be opened
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OPEN_TMPFILE_ERROR', $tmp_file);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		// empty buffer file
		if (false === ftruncate($fp, 0))
		{ // File cannot be truncated
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_TRUNCATE_TMPFILE_ERROR', $tmp_file);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
		$message =  sprintf('Memory consumption while parsing before loop: %01.3f MB', $memoryConsumption);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

		// paraphrase tables array per table for better handling and convert simple xml objects to strings
		$i = 0;
		while (null !== $tmp_table = array_shift($x_tables))
		{
			$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
			$message =  sprintf('Memory consumption while parsing at very beginning loop: %01.3f MB', $memoryConsumption);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

			$w_table = array();

			// extract install queries
			$w_table['queries'] = (string) $tmp_table->table_structure->install_query->query;
			if ($adjust_prefix)
			{
				$w_table['queries'] = str_replace($is_prefix, $new_prefix, $w_table['queries']);
			}

			$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
			$message =  sprintf('Memory consumption while parsing at loop with query: %01.3f MB', $memoryConsumption);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

			// extract table assets
			if (property_exists($tmp_table, 'table_assets'))
			{
				$assets       = array();
				$table_assets = (array) $tmp_table->table_assets;
				if (key_exists('dataset', $table_assets))
				{
					if (is_array($table_assets['dataset']))
					{
						foreach ($table_assets['dataset'] as $item)
						{
							$ds    = array();
							$props = get_object_vars($item);
							foreach ($props as $k => $v)
							{
								$xy     = (string) $v;
								$ds[$k] = $xy;
							}

							$assets[] = $ds;
						}
					}
					else
					{
						$assets[] = get_object_vars($table_assets['dataset']);
					}

					if (count($assets) > 0)
					{
						$w_table['table_assets'] = $assets;
					}

					unset($assets);
				}
			}

			$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
			$message =  sprintf('Memory consumption while parsing at loop with assets: %01.3f MB', $memoryConsumption);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

			// get table data; cannot use get_object_vars() because this returns empty objects on empty values, not empty array fields
			$items = array();
			if (property_exists($tmp_table, 'table_data'))
			{
				$table_data = (array) $tmp_table->table_data;
				unset($tmp_table);
				if (key_exists('dataset', $table_data))
				{
					if (is_array($table_data['dataset']))
					{
						foreach ($table_data['dataset'] as $item)
						{
							$ds    = array();
							$props = get_object_vars($item);
							foreach ($props as $k => $v)
							{
								$xy     = (string) $v;
								$ds[$k] = $xy;
							}

							$items[] = $ds;
						}
					}
					else
					{
						$items[] = get_object_vars($table_data['dataset']);
					}
				}
			}

			$w_table['table_data'] = $items;

			$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
			$message =  sprintf('Memory consumption while parsing at loop with data sets: %01.3f MB', $memoryConsumption);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));

			unset($items);

			// write table data to buffer file
			$write_data = '';
			if ($i == 0)
			{
				$write_data .= 'a:' . count($table_names) . ':{';
			}

			$write_data .= 's:' . strlen($table_names[$i]) . ':"' . $table_names[$i] . '";';
			$write_data .= serialize($w_table);
			unset($w_table);
			if ($i == (count($table_names) - 1))
			{
				$write_data .= '}';
			}

			if (false === fwrite($fp, $write_data))
			{
				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_WRITE_TMPFILE_ERROR', $tmp_file);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return  false;
			}

			$i++;

			$memoryConsumption = memory_get_usage(true) / (1024.0 * 1024.0);
			$message =  sprintf('Memory consumption while parsing of table %s: %01.3f MB', $table_names[$i - 1],$memoryConsumption);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'maintenance'));
		}

		$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_PARSE_SUCCESS');
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_ok">' . $message . '</p><br />';

		Factory::getApplication()->setUserState('com_bwpostman.maintenance.tmp_file', $tmp_file);
		fclose($fp);

		return $table_names;
	}

	/**
	 * Method delete all sub assets of component
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	public function deleteSubAssets()
	{
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' LIKE ' . $this->db->quote('%com_bwpostman.%'));

		$this->db->setQuery($query);

		try
		{
			$asset_delete = $this->db->execute();

			// Uncomment next line to test rollback (only makes sense, if deleted tables contained data)
			// throw new BwException(Text::_('Test-Exception DeleteAssets Model'));

			if (!$asset_delete)
			{
				$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_ERROR');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
			else
			{
				$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_SUCCESS');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
			}
		}
		catch (RuntimeException $exception)
		{
			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_DATABASE_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));
		}

		return  true;
	}

	/**
	 * Method to heal assets table
	 *
	 * repairs lft and rgt values in asset table, updates component asset
	 * closes gap caused by deleting sub assets of BwPostman
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function healAssetsTable()
	{
		try
		{
			// com_assets are from state = from input file!
			$com_assets = Factory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());
			$query      = $this->db->getQuery(true);

			// first get lft from main asset com_bwpostman, This is the one already existing in table
			$base_asset = $this->getBaseAsset('component', true);

			if (!$base_asset)
			{
				return  false;
			}

			// Calculate complete gap caused by BwPostman. Subtract 1 to provide space for right value of BwPostman
			$gap = $base_asset['rgt'] - $base_asset['lft'] - 1;

			// second shift down rgt values by gap for all assets above lft of BwPostman
			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('rgt') . " = (" . $this->db->quoteName('rgt') . " - " . $gap . ") ");
			$query->where($this->db->quoteName('lft') . ' >= ' . $base_asset['lft']);

			$this->db->setQuery($query);
			$set_asset_right = $this->db->execute();

			// now shift down lft values by gap for all assets above lft of BwPostman
			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('lft') . " = (" . $this->db->quoteName('lft') . " - " . $gap . ") ");
			$query->where($this->db->quoteName('lft') . ' > ' . $base_asset['lft']);

			$this->db->setQuery($query);
			$set_asset_left = $this->db->execute();

			// next set rgt value of BwPostman and update component rules
			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('rgt') . " = (" . $this->db->quoteName('lft') . " + 1)");
			$query->set($this->db->quoteName('title') . " = " . $this->db->quote('BwPostman Component'));

			if (isset($com_assets[0]['rules']))
			{
				$query->set($this->db->quoteName('rules') . " = " . $this->db->quote($com_assets[0]['rules']));
			}

			$query->where($this->db->quoteName('lft') . ' = ' . $base_asset['lft']);

			$this->db->setQuery($query);
			$set_asset_base = $this->db->execute();

			// Uncomment next line to test rollback (only makes sense, if deleted tables contained data)
			// throw new BwException(Text::_('Test-Exception HealAssets Model'));

			if (!$set_asset_left || !$set_asset_right || !$set_asset_base)
			{
				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
			else
			{
				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_SUCCESS');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

				echo '<p class="bw_tablecheck_ok">' . $message . '</p><br />';
				$base_asset['rgt'] = $base_asset['lft'] + 1;
			}
		}
		catch (RuntimeException $exception)
		{
			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return  true;
	}

	/**
	 * Method to get the base asset of BwPostman. If state exists, catch values from state, else use asset table
	 *
	 * @param string  $table
	 * @param boolean $onlyHeal
	 *
	 * @return array|boolean    $base_asset     base asset of BwPostman
	 *
	 * @throws exception
	 *
	 * @since    1.3.0
	 */
	protected function getBaseAsset($table = 'component', $onlyHeal = false)
	{
		$stateAssetsRaw = '';

		if (!in_array($table, $this->assetTargetTables))
		{
			return -1;
		}

		if (!$onlyHeal && $table != 'component')
		{
			$stateAssetsRaw = Factory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', '');
		}

		if (is_array($stateAssetsRaw) && count($stateAssetsRaw) > 0)
		{
			if (substr($table, -1) === 's')
			{
				$table = substr($table,0, -1);
			}

			$base_asset = $this->extractBaseAssetFromState(array('tableNameUC' => $table), $stateAssetsRaw);
		}
		else
		{
			$base_asset = $this->getBaseAssetFromTable($table);
		}

		return $base_asset;
	}

	/**
	 * Method to write a new asset at the table asset. Shifts left and right value at existing assets and inserts the new asset.
	 *
	 * @param array   $table
	 * @param boolean $showMessage
	 *
	 * @return string|boolean    $base_asset     base asset of BwPostman
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function insertBaseAsset($table, $showMessage = true)
	{
		// Get asset rules
		$asset     = $this->getBaseAssetItem($table);
		$com_asset = $this->getBaseAsset('component');

		if ($asset === false || $com_asset === false)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		// Provide space for new asset and insert it
		$move_asset_right = $this->shiftRightAssets($com_asset);
		$move_asset_left  = $this->shiftLeftAssets($com_asset);

		if (!$move_asset_left || !$move_asset_right)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		if ($table == 'component')
		{
			$rules      = $com_asset['rules'];
			$writeAsset = $this->updateComponentRules($rules);
		}
		else
		{
			$writeAsset = $this->insertAssetToTable($com_asset, $asset);
		}

		if (!$writeAsset)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		// Get Base Asset
		$base_asset = $this->getAssetFromAssetsTableByName($asset['name']);

		if ($base_asset === false)
		{
			return false;
		}

		if ($showMessage)
		{
			$writeTableName = $table;
			if (is_array($table))
			{
				$writeTableName = $table['tableNameUC'];
			}

			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_SUCCESS', $writeTableName);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			echo '<p class="bw_tablecheck_ok">' . $message . '</p><br />';
		}

		$tableName = $table;
		if (is_array($table))
		{
			$tableName = $table['tableNameUC'];
		}

		$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_DATABASE_ERROR', $tableName);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

		return $base_asset;
	}

	/**
	 * Method to write a new asset at the table asset. Shifts left and right value at existing assets and inserts the new asset.
	 *
	 * @param string $sectionName
	 * @param array  $sectionRules
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	public function updateSectionAsset($sectionName, $sectionRules)
	{
		$assetName = 'com_bwpostman.' . $sectionName;

		try
		{
			$rules = new JAccessRules($sectionRules);

			$query = $this->db->getQuery(true);

			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('rules') . " = " . $this->db->Quote($rules));
			$query->where($this->db->quoteName('name') . ' = ' . $this->db->Quote($assetName));

			$this->db->setQuery($query);
			$result = $this->db->execute();

			return $result;
		}
		catch (RuntimeException $exception)
		{
			$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $sectionName);
			$message .= ': ';
			$message .= $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}
	}

	/**
	 * Method to update an existing asset at the table asset
	 *
	 * @param array $asset
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	protected function updateBaseAsset($asset = array())
	{
		if (empty($asset))
		{
			$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_ERROR_EMPTY');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		$query = $this->db->getQuery(true);

		$query->update($this->db->quoteName('#__assets'));
		$query->set($this->db->quoteName('rules') . " = " . $this->db->quote($asset['rules']));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->quote($asset['name']));

		$this->db->setQuery($query);

		try
		{
			$update_asset = $this->db->execute();

			if (!$update_asset)
			{
				$message =  Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_ERROR');
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}
		catch (RuntimeException $exception)
		{
			$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $asset['name']);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return  true;
	}

	/**
	 * Method to get the current default asset of table or BwPostman, based on section asset (parent)
	 *
	 * @param array $sectionAsset
	 *
	 * @return  array   $default_asset  default asset of table or BwPostman
	 *
	 * @since    1.3.0
	 */
	protected function getDefaultAsset($sectionAsset)
	{
		$default_asset = $sectionAsset;

		$default_asset['parent_id'] = $sectionAsset['id'];
		$default_asset['id']        = 0;
		$default_asset['lft']       = $sectionAsset['rgt'];
		$default_asset['rgt']       = $default_asset['lft'] + 1;
		$default_asset['level']     = (int) $sectionAsset['level'] + 1;

		return $default_asset;
	}

	/**
	 * Method to write the collected assets by loop. Also shifts left and right values by number of inserted assets.
	 *
	 * @param array $dataset             array of data sets to write
	 * @param int   $assetLoopCounter    actual value of general control variable
	 * @param array $base_asset          base asset values
	 * @param array $mapOldAssetIdsToNew transformation array of asset ids old vs. new
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	protected function writeLoopAssets($dataset, $assetLoopCounter, $base_asset, &$mapOldAssetIdsToNew)
	{
		// Prepare insert data (convert to string, remove last bracket)
		$insert_data = implode(',', $dataset);
		$insert_data = substr($insert_data, 1, (strlen($insert_data) - 2));

		$query = $this->db->getQuery(true);
		$query->insert($this->db->quoteName('#__assets'));
		$query->columns($this->assetColnames);
		$query->values($insert_data);
		$this->db->setQuery($query);

		$this->logger->addEntry(new LogEntry('Write Loop Assets Query 1: ' . (string) $query, BwLogger::BW_DEBUG,
			'maintenance'));

		try
		{
			$this->db->execute();
		}
		catch (RuntimeException$exception)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR') . ': ' . $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		// calculate number of inserted ids
		$last_id  = $this->db->insertid();
		$num_rows = count($dataset);
		for ($i = 0; $i < $num_rows; $i++)
		{
			$mapOldAssetIdsToNew[$assetLoopCounter - ($num_rows - $i)]['newAssetId'] = $last_id + $i;
		}

		try
		{
			// shift rgt values from all assets since rgt of table asset
			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('rgt') . " = (" . $this->db->quoteName('rgt') . " + " . ($num_rows * 2) . ") ");
			$query->where($this->db->quoteName('rgt') . ' >= ' . $base_asset['rgt']);
			$query->where($this->db->quoteName('name') . ' NOT LIKE ' . $this->db->quote('%' . $base_asset['name'] . '.%'));

			$this->db->setQuery($query);

			$this->db->execute();

			// now shift lft values from all assets above lft of BwPostman
			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__assets'));
			$query->set($this->db->quoteName('lft') . " = (" . $this->db->quoteName('lft') . " + " . ($num_rows * 2) . ")");
			$query->where($this->db->quoteName('lft') . ' > ' . $base_asset['rgt']);
			$query->where($this->db->quoteName('name') . ' NOT LIKE ' . $this->db->quote('%' . $base_asset['name'] . '.%'));

			$this->db->setQuery($query);
			$this->db->execute();

		}
		catch (RuntimeException $exception)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR') . ': ' . $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return  true;
	}

	/**
	 * Method to write the collected datasets by loop
	 *
	 * @param array  $dataset array of data sets to write
	 * @param string $table   table name to write in
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	protected function writeLoopDatasets($dataset, $table)
	{
		try
		{
			// get table column names, may throw runtime exception
			$table_colnames = array_keys($this->db->getTableColumns($table));

			$insert_data = implode(',', $dataset);
			$insert_data = substr($insert_data, 1, (strlen($insert_data) - 2));

			$query = 'REPLACE INTO ' . $this->db->quoteName($table) . '(' . implode(',',
					$table_colnames) . ') VALUES (' . $insert_data . ')';

			$this->db->setQuery($query);

			$this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR') . ': ' . $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return true;
	}

	/**
	 * Method to get the maximum value for item loop, depending on processed table
	 *
	 * @param string $table table name to get value
	 *
	 * @return  int     $data_loop_max
	 *
	 * @since    1.3.0
	 */
	protected function getDataLoopMax($table)
	{
		switch ($table)
		{
			case '#__bwpostman_newsletters':
			case '#__bwpostman_sendmailcontent':
			case '#__bwpostman_sendmailqueue':
			case '#__bwpostman_tc_sendmailcontent':
			case '#__bwpostman_tc_sendmailqueue':
				$data_loop_max = 20;
				break;
			case '#__bwpostman_subscribers_mailinglists':
			case '#__bwpostman_newsletters_mailinglists':
			case '#__bwpostman_campaigns_mailinglists':
				$data_loop_max = 10000;
				break;
			default:
				$data_loop_max = 1000;
				break;
		}

		return $data_loop_max;
	}

	/**
	 * Method to see if user groups have changed, get new IDs or create new user groups if needed
	 *
	 * @param array $usergroups user groups from backup file
	 *
	 * @return  array|boolean $group    array of old_id and new_id or false if no group id has changed. According groups were skipped.
	 *
	 * @since    1.3.0
	 */
	private function getCurrentUserGroups($usergroups)
	{
		$groups = array();

		// first compare current user groups with backed up ones
		foreach ($usergroups as $item)
		{
			$query  = $this->db->getQuery(true);

			$query->select($this->db->quoteName('id'));
			$query->from($this->db->quoteName('#__usergroups'));
			$query->where($this->db->quoteName('title') . ' = ' . $this->db->quote($item['title']));

			$this->db->setQuery($query);
			try
			{
				$result = $this->db->loadAssoc();
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return -1;
			}

			// user group not found
			if (!$result)
			{
				// insert new user group
				if ($this->isJ4)
				{
					$userModel = new Joomla\Component\Users\Administrator\Model\GroupModel();
				}
				else
				{
					JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
					$userModel = JModelLegacy::getInstance('Group', 'UsersModel');
				}

				$data['id']        = 0;
				$data['title']     = $item['title'];
				$data['parent_id'] = $item['parent_id'];
				$success           = $userModel->save($data);

				if (!$success)
				{
					$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ADD_USERGROUP_ERROR', 	$item['title']);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return -1;
				}

				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('id'));
				$query->from($this->db->quoteName('#__usergroups'));
				$query->where($this->db->quoteName('title') . ' = ' . $this->db->quote($item['title']));

				$this->db->setQuery($query);
				try
				{
					$result = $this->db->loadResult();
				}
				catch (RuntimeException $exception)
				{
					$message = $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return -1;
				}

				$groups[] = array('old_id' => $item['id'], 'new_id' => $result, 'title' => $item['title']);
			}
			else
			{
				// user group has new ID
				if ($result['id'] !== $item['id'])
				{
					// memorize new id
					$groups[] = array('old_id' => $item['id'], 'new_id' => $result['id'], 'title' => $item['title']);
				}
			}
		}

		if (count($groups))
		{
			return $groups;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to rewrite user groups in the assets. Needed, if backup file processed contains other usergroups than currently installed ones.
	 *
	 * @param string $table           component or table name of the assets are to rewrite
	 * @param array  $assets          array of the table assets
	 * @param array  $groupsToReplace array with old and new ID of changed user groups
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	private function rewriteAssetUserGroups($table, &$assets, $groupsToReplace)
	{
		$tables  = Factory::getApplication()->getUserState('com_bwpostman.maintenance.tables', array());
		$old_ids = array();
		foreach ($groupsToReplace as $groupToReplace)
		{
			$old_ids[] = $groupToReplace['old_id'];
		}

		// check assets
		$i = 0;
		foreach ($assets as $asset)
		{
			if (key_exists('rules', $asset))
			{
				$rules = json_decode($asset['rules'], true);
				if ($rules !== null)
				{
					// rewrite user groups in rule
					foreach ($rules as $action => $rule)
					{
						$rewrite = false;
						$ruleNew = array();

						foreach ($rule as $key => $value)
						{
							$found = array_search($key, $old_ids);
							if ($found !== false)
							{
								$rewrite          = true;
								$newKey           = $groupsToReplace[$found]['new_id'];
								$ruleNew[$newKey] = $value;
							}
							else
							{
								$ruleNew[$key] = $value;
							}
						}
						$rules[$action] = $ruleNew;

						if ($rewrite)
						{
							if ($table == 'component')
							{
								$assets[$i]['rules'] = json_encode($rules);
							}
							else
							{
								// update table assets
								$tables[$table]['table_assets'][$i]['rules'] = json_encode($rules);
								Factory::getApplication()->setUserState('com_bwpostman.maintenance.tables', $tables);
							}
						}
					}
				}
				else
				{
					$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_REWRITE_USERGROUP_RULE_ERROR',	$asset['rules'], $table);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}

				$i++;
			}
		}

		return  true;
	}

	/**
	 * Method to create tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	public function createRestorePoint()
	{
		$tables = $this->getAffectedTables();

		foreach ($tables as $table)
		{
			$tableNameGeneric = $table['tableNameGeneric'];

			// delete eventually remaining temporary tables
			$query = 'DROP TABLE IF EXISTS ' . $this->db->quoteName($tableNameGeneric . '_tmp');

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR');
				$message .= ": ";
				$message .= $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}

			// copy affected tables to temporary tables, structure part
			$query = 'CREATE TABLE ' . $this->db->quoteName($tableNameGeneric . '_tmp') . ' LIKE ' . $this->db->quoteName($tableNameGeneric);

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR');
				$message .= ": ";
				$message .= $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}

			// copy affected tables to temporary tables, data set part
			$query = 'INSERT INTO ' . $this->db->quoteName($tableNameGeneric . '_tmp') . ' SELECT * FROM ' . $this->db->quoteName($tableNameGeneric);

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR');
				$message .= ": ";
				$message .= $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to restore tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 *
	 * @since    1.3.0
	 */
	public function restoreRestorePoint()
	{
		$tables = $this->getAffectedTables();

		foreach ($tables as $table)
		{
			// delete newly created tables
			$query = ('DROP TABLE IF EXISTS ' . $this->db->quoteName($table['tableNameGeneric']));

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESTORE_RESTORE_POINT_ERROR');
				$message .= ": ";
				$message .= $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}

			// delete newly created tables
			$query = (
				'RENAME TABLE ' . $this->db->quoteName($table["tableNameGeneric"] . '_tmp') . ' TO ' . $this->db->quoteName($table["tableNameGeneric"])
			);

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESTORE_RESTORE_POINT_ERROR');
				$message .= ": ";
				$message .= $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}

		$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_POINT_RESTORED_WARNING');
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		Factory::getApplication()->setUserState('com_bwpostman.maintenance.restorePoint_text', '<p class="bw_tablecheck_error">' . $message . '</p>');

		return true;
	}

	/**
	 * Method to delete tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	protected function deleteRestorePoint()
	{
		$tables = $this->getAffectedTables();

		if (!$tables)
		{
			return false;
		}

			foreach ($tables as $table)
			{
				$query = ('DROP TABLE IF EXISTS ' . $this->db->quoteName($table['tableNameGeneric'] . '_tmp'));

				$this->db->setQuery($query);

				try
				{
					$this->db->execute();
				}
				catch (RuntimeException $exception)
				{
					$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DELETE_RESTORE_POINT_ERROR') . $exception->getMessage();
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

					return false;
				}
			}

		return  true;
	}

	/**
	 * Method to get the affected tables for restore point, but without temporary tables. Affected tables are not only all
	 * tables with bwpostman in their name, but also assets and usergroups
	 *
	 * @return  array|boolean   $tableNames     array of affected tables
	 *
	 * @since    1.3.0
	 */
	protected function getAffectedTables()
	{
		// get db prefix
		$prefix = $this->db->getPrefix();

		// get all names of installed BwPostman tables
		if ($this->getTableNamesFromDB() === false)
		{
			return  false;
		}

		if (!is_array($this->tableNames))
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_AFFECTED_TABLES_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return  false;
		}

		$tables = array();
		foreach ($this->tableNames as $table)
		{
			if (!strpos($table['tableNameGeneric'], '_tmp'))
			{
				$tables[] = $table;
			}
		}

		$tables[]['tableNameGeneric'] = $prefix . 'usergroups';
		$tables[]['tableNameGeneric'] = $prefix . 'assets';

		return $tables;
	}

	/**
	 * Method to get the asset of a specific table, called base asset
	 *
	 * @param string $table_name_raw for which table we want to get the base asset
	 * @param string $dot            if dot is present, we search for a specific table, else component is meant
	 *
	 * @return array|boolean
	 *
	 * @since 2.0.0
	 */
	private function getTableAssetData($table_name_raw, $dot = '.')
	{
		$endString = $dot;

		if ($dot == '.')
		{
			$endString .= '%';
		}

		// raw table name are plural, assets are singular
		$asset_name = '%com_bwpostman.' . substr($table_name_raw, 0, strlen($table_name_raw) - 1) . $endString;

		$query = $this->db->getQuery(true);

		// Get the assets for this table from database
		$query->select('*');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' LIKE ' . $this->db->quote($asset_name));

		$this->db->setQuery($query);

		try
		{
			$data = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $data;
	}

	/**
	 * Method to get the table name with bwpostman prefix
	 *
	 * @param string $table table name to get value
	 *
	 * @return  string  $bwpmTableName
	 *
	 * @since    1.3.0
	 */
	protected function getBwpmTableName($table)
	{
		$start = strpos($table, '_', 3);

		if ($start === false)
		{
			$message = Text::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_TABLE_NAME_ERROR');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));
		}

		return substr($table, $start + 1);
	}

	/**
	 * @param string $tableName table name in format #__bwpostman_… (generic table name)
	 *
	 * @return string $tableNameRaw without leading and concluding part
	 *
	 * @since 2.0.0
	 */
	private function getRawTableName($tableName)
	{
		return str_replace('#__bwpostman_', '', $tableName);
	}

	/**
	 * Method to get all installed BwPostman usergroups by a specific table/section
	 *
	 * @param string $table in format UC first
	 *
	 * @return array|boolean
	 *
	 * @since 2.0.0
	 */
	private function getBwPostmanUsergroups($table)
	{
		$searchValues = array("'BwPostmanAdmin'", "'BwPostmanManager'", "'BwPostmanPublisher'", "'BwPostmanEditor'");

		if ($table != 'component')
		{
			$suffixes = array("Admin", "Publisher", "Editor");

			foreach ($suffixes as $suffix)
			{
				$value = 'BwPostman';
				$value .= $table;
				$value .= $suffix;
				$value = $this->db->quote($value);

				$searchValues[] = $value;
			}
		}

		$query = $this->db->getQuery(true);

		$query->select($this->db->quoteName('title'));
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__usergroups'));
		$query->where($this->db->quoteName('title') . ' IN (' . implode(',', $searchValues) . ')');

		$this->db->setQuery($query);

		try
		{
			$bwpmUserGroups = $this->db->loadAssocList('title');
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $bwpmUserGroups;
	}

	/**
	 * Method to get an array of name, title and rules as asset for a specific section (table or component)
	 *
	 * @param array $table
	 *
	 * @return array|boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function getBaseAssetItem($table)
	{
		// If state is set, use this
		$stateAssetsRaw = Factory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());

		if (is_array($stateAssetsRaw) && count($stateAssetsRaw) > 0)
		{
			$asset = $this->extractBaseAssetFromState($table, $stateAssetsRaw);

			if ($asset !== false)
			{
				return $asset;
			}
		}

		$com_asset = $this->getBaseAsset('component');

		if ($com_asset === false)
		{
			return  false;
		}

		$rules = $this->sectionRules;

		switch ($table['tableNameUC'])
		{
			case 'Campaign':
				$asset['name']  = 'com_bwpostman.campaign';
				$asset['title'] = 'BwPostman Campaigns';
				$asset['rules'] = new JAccessRules($rules);
				break;

			case 'Mailinglist':
				$asset['name']  = 'com_bwpostman.mailinglist';
				$asset['title'] = 'BwPostman Mailinglists';
				$asset['rules'] = new JAccessRules($rules);
				break;

			case 'Newsletter':
				$asset['name']  = 'com_bwpostman.newsletter';
				$asset['title'] = 'BwPostman Newsletters';
				$asset['rules'] = new JAccessRules($rules);
				break;

			case 'Subscriber':
				$asset['name']  = 'com_bwpostman.subscriber';
				$asset['title'] = 'BwPostman Subscribers';
				$asset['rules'] = new JAccessRules($rules);
				break;

			case 'Template':
				$asset['name']  = 'com_bwpostman.template';
				$asset['title'] = 'BwPostman Templates';
				$asset['rules'] = new JAccessRules($rules);
				break;

			case 'component':
			default:
				$asset['name']  = 'com_bwpostman';
				$asset['title'] = 'BwPostman Component';
				$asset['rules'] = $com_asset['rules'];
				break;
		}

		return $asset;
	}

	/**
	 * Method to check if column asset_id exists for a specific table
	 *
	 * @param string $table as generic table name
	 *
	 * @return boolean|integer
	 *
	 * @since 2.0.0
	 */
	public function checkForAsset($table)
	{
		$hasAsset = false;

		try
		{
			$columns = $this->db->getTableColumns($table);
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return -1;
		}

		if (array_key_exists('asset_id', $columns))
		{
			$hasAsset = true;
		}

		return $hasAsset;
	}

	/**
	 * Method to preset rule values for all predefined bwpostman usergroups for a specific table/section
	 *
	 * @param array $table
	 *
	 * @return array|boolean $mergedRules
	 *
	 * @since 2.0.0
	 */
	private function presetSectionRules($table)
	{
		$tableName      = substr($table['tableNameRaw'], 0, -1) . '.';
		$tableNameUC    = $table['tableNameUC'];
		$bwpmUserGroups = $this->getBwPostmanUsergroups($tableNameUC);

		if (!$bwpmUserGroups)
		{
			return  false;
		}

		$sectionPublisher = 'BwPostman' . $tableNameUC . 'Publisher';
		$sectionEditor    = 'BwPostman' . $tableNameUC . 'Editor';

		// If there is no real table, component entries will be overridden. This makes it possible to assign rules in one run
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$rules['bwpm.' . $tableName . 'create'] = array(
				$bwpmUserGroups['BwPostmanAdmin']['id'] => true,
			);
		}

		// Set rules for edit/edit_own
		$tmpRule = array();
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
		}

		if (key_exists('BwPostmanEditor', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanEditor']['id']] = false;
		}

		if (key_exists($sectionEditor, $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups[$sectionEditor]['id']] = false;
		}

		$rules['bwpm.' . $tableName . 'edit'] = $tmpRule;

		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$rules['bwpm.' . $tableName . 'edit.own'] = array(
				$bwpmUserGroups['BwPostmanAdmin']['id'] => true,
			);
		}

		// Set rules for edit_state
		$tmpRule = array();
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
		}

		if (key_exists('BwPostmanEditor', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanEditor']['id']] = false;
		}

		if (key_exists($sectionEditor, $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups[$sectionEditor]['id']] = false;
		}

		$rules['bwpm.' . $tableName . 'edit.state'] = $tmpRule;

		// Set rules for archive
		$tmpRule = array();
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
		}

		if (key_exists('BwPostmanPublisher', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanPublisher']['id']] = false;
		}

		if (key_exists($sectionPublisher, $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups[$sectionPublisher]['id']] = false;
		}

		$rules['bwpm.' . $tableName . 'archive'] = $tmpRule;

		// Set rules for restore
		$tmpRule = array();
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
		}

		if (key_exists('BwPostmanPublisher', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanPublisher']['id']] = false;
		}

		if (key_exists($sectionPublisher, $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups[$sectionPublisher]['id']] = false;
		}

		$rules['bwpm.' . $tableName . 'restore'] = $tmpRule;

		// Set rules for delete
		$tmpRule = array();
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
		}

		if (key_exists('BwPostmanPublisher', $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups['BwPostmanPublisher']['id']] = false;
		}

		if (key_exists($sectionPublisher, $bwpmUserGroups))
		{
			$tmpRule[$bwpmUserGroups[$sectionPublisher]['id']] = false;
		}

		$rules['bwpm.' . $tableName . 'delete'] = $tmpRule;

		// Set rules for send
		if ($tableNameUC == 'Newsletter')
		{
			$tmpRule = array();
			if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
			{
				$tmpRule[$bwpmUserGroups['BwPostmanAdmin']['id']] = true;
			}
		}

		$rules['bwpm.' . $tableName . 'send'] = $tmpRule;

		// Merge specific rules with predefined (basic) rules
		$mergedRules = array();
		$keys        = array_keys($this->sectionRules);

		foreach ($keys as $key)
		{
			// If predefined rule exists at specific rules, then merge
			if (key_exists($key, $rules))
			{
				$mergedRules[$key] = array_replace($this->sectionRules[$key], $rules[$key]);
			}
			// Else take predefined rule
			else
			{
				$mergedRules[$key] = $this->sectionRules[$key];
			}
		}

		return $mergedRules;
	}

	/**
	 * Method to initialize assets for component and sections with predefined basic rules at installation and update of component
	 *
	 * @param boolean $updateComponent
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function createBaseAssets($updateComponent = false)
	{
		if ($this->getTableNamesFromDB() === false)
		{
			return false;
		}

		// Get rules
		if (!$updateComponent)
		{
			if (!$this->initializeComponentAssets())
			{
				return false;
			}
			$rulesJson = $this->componentRules;
		}
		else
		{
			// @ToDo: get component rules from state
			$componentAsset = $this->getBaseAssetItem(array('tableNameUC' => 'component'));

			if ($componentAsset === false)
			{
				return false;
			}

			$rulesJson = $componentAsset['rules'];
		}

		$rules = new JAccessRules($rulesJson);

		if (!$this->updateComponentRules($rules))
		{
			return false;
		}

		if (!$this->initializeSectionAssets())
		{
			return false;
		}

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				$sectionRules = $this->presetSectionRules($table);

				if ($sectionRules === false)
				{
					return false;
				}

				$sectionName = substr($table['tableNameRaw'], 0, -1);

				$sectionAssetExists = $this->getAssetFromAssetsTableByName('com_bwpostman.' . $sectionName);

				if ($sectionAssetExists === false)
				{
					return false;
				}

				if (!is_null($sectionAssetExists))
				{
					if (!$this->updateSectionAsset($sectionName, $sectionRules))
					{
						return false;
					}
				}
				else
				{
					if ($this->insertBaseAsset($table, false) === false)
					{
						return false;
					}
				}
			}
		}

		return  true;
	}

	/**
	 * Method to update component rules
	 *
	 * @param JAccessRules $rules
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function updateComponentRules($rules)
	{
		$query = $this->db->getQuery(true);

		$query->update($this->db->quoteName('#__assets'));
		$query->set($this->db->quoteName('rules') . " = " . $this->db->quote($rules));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->Quote('com_bwpostman'));

		$this->db->setQuery($query);

		try
		{
			$writeAsset = $this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $writeAsset;
	}

	/**
	 * Method to initialize asset for component
	 * All possible actions for all usergroups BwPostman delivers are set.
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function initializeComponentAssets()
	{
		$rules = array();

		// Get all BwPostman usergroups
		$bwpmUserGroups = $this->getAllBwpmUserGroups();

		if ($bwpmUserGroups === false)
		{
			return false;
		}

		$joomlaGroups   = $this->getJoomlaGroups();

		if ($joomlaGroups === false)
		{
			return false;
		}

		$usedGroups     = array_merge($bwpmUserGroups, $joomlaGroups);

		$rules['core.admin'] = array(
			$usedGroups['Administrator']['id']    => true,
			$usedGroups['BwPostmanAdmin']['id']   => true,
			$usedGroups['BwPostmanManager']['id'] => false,
		);

		$rules['core.login.admin'] = array(
			$usedGroups['Administrator']['id']  => true,
			$usedGroups['BwPostmanAdmin']['id'] => true,
		);

		$rules['core.manage'] = array(
			$usedGroups['Administrator']['id']  => true,
			$usedGroups['Manager']['id']        => true,
			$usedGroups['BwPostmanAdmin']['id'] => true,
		);

		$rules['bwpm.create'] = array(
			$usedGroups['Administrator']['id']  => true,
			$usedGroups['Manager']['id']        => true,
			$usedGroups['BwPostmanAdmin']['id'] => true,
		);

		$rules['bwpm.delete'] = array(
			$usedGroups['Administrator']['id']                 => true,
			$usedGroups['Manager']['id']                       => true,
			$usedGroups['BwPostmanAdmin']['id']                => true,
			$usedGroups['BwPostmanPublisher']['id']            => false,
			$usedGroups['BwPostmanCampaignPublisher']['id']    => false,
			$usedGroups['BwPostmanMailinglistPublisher']['id'] => false,
			$usedGroups['BwPostmanNewsletterPublisher']['id']  => false,
			$usedGroups['BwPostmanSubscriberPublisher']['id']  => false,
			$usedGroups['BwPostmanTemplatePublisher']['id']    => false,
		);

		$rules['bwpm.edit'] = array(
			$usedGroups['Administrator']['id']              => true,
			$usedGroups['Manager']['id']                    => true,
			$usedGroups['BwPostmanAdmin']['id']             => true,
			$usedGroups['BwPostmanEditor']['id']            => false,
			$usedGroups['BwPostmanCampaignEditor']['id']    => false,
			$usedGroups['BwPostmanMailinglistEditor']['id'] => false,
			$usedGroups['BwPostmanNewsletterEditor']['id']  => false,
			$usedGroups['BwPostmanSubscriberEditor']['id']  => false,
			$usedGroups['BwPostmanTemplateEditor']['id']    => false,
		);

		$rules['bwpm.edit.own'] = array(
			$usedGroups['Administrator']['id']  => true,
			$usedGroups['Manager']['id']        => true,
			$usedGroups['BwPostmanAdmin']['id'] => true,
		);

		$rules['bwpm.edit.state'] = array(
			$usedGroups['Administrator']['id']              => true,
			$usedGroups['Manager']['id']                    => true,
			$usedGroups['BwPostmanAdmin']['id']             => true,
			$usedGroups['BwPostmanEditor']['id']            => false,
			$usedGroups['BwPostmanCampaignEditor']['id']    => false,
			$usedGroups['BwPostmanMailinglistEditor']['id'] => false,
			$usedGroups['BwPostmanNewsletterEditor']['id']  => false,
			$usedGroups['BwPostmanSubscriberEditor']['id']  => false,
			$usedGroups['BwPostmanTemplateEditor']['id']    => false,
		);

		$rules['bwpm.archive'] = array(
			$usedGroups['Administrator']['id']                 => true,
			$usedGroups['Manager']['id']                       => true,
			$usedGroups['BwPostmanAdmin']['id']                => true,
			$usedGroups['BwPostmanPublisher']['id']            => false,
			$usedGroups['BwPostmanCampaignPublisher']['id']    => false,
			$usedGroups['BwPostmanMailinglistPublisher']['id'] => false,
			$usedGroups['BwPostmanNewsletterPublisher']['id']  => false,
			$usedGroups['BwPostmanSubscriberPublisher']['id']  => false,
			$usedGroups['BwPostmanTemplatePublisher']['id']    => false,
		);

		$rules['bwpm.restore'] = array(
			$usedGroups['Administrator']['id']                 => true,
			$usedGroups['Manager']['id']                       => true,
			$usedGroups['BwPostmanAdmin']['id']                => true,
			$usedGroups['BwPostmanPublisher']['id']            => false,
			$usedGroups['BwPostmanCampaignPublisher']['id']    => false,
			$usedGroups['BwPostmanMailinglistPublisher']['id'] => false,
			$usedGroups['BwPostmanNewsletterPublisher']['id']  => false,
			$usedGroups['BwPostmanSubscriberPublisher']['id']  => false,
			$usedGroups['BwPostmanTemplatePublisher']['id']    => false,
		);

		$rules['bwpm.send'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
		);

		$rules['bwpm.view.archive'] = array(
			$usedGroups['Administrator']['id']                 => true,
			$usedGroups['Manager']['id']                       => true,
			$usedGroups['BwPostmanAdmin']['id']                => true,
			$usedGroups['BwPostmanPublisher']['id']            => false,
			$usedGroups['BwPostmanCampaignPublisher']['id']    => false,
			$usedGroups['BwPostmanMailinglistPublisher']['id'] => false,
			$usedGroups['BwPostmanNewsletterPublisher']['id']  => false,
			$usedGroups['BwPostmanSubscriberPublisher']['id']  => false,
			$usedGroups['BwPostmanTemplatePublisher']['id']    => false,
		);

		$rules['bwpm.view.maintenance'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanManager']['id']          => false,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
		);
		/*
				$rules['bwpm.view.manage'] = array(
					$usedGroups['Administrator']['id']             => true,
					$usedGroups['Manager']['id']                   => true,
					$usedGroups['BwPostmanPublisher']['id']        => false,
					$usedGroups['BwPostmanAdmin']['id']            => true,
					$usedGroups['BwPostmanPublisher']['id']        => false,
					$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
					$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
					$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
					$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
					$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
				);
		*/
		$rules['bwpm.view.campaign'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
		);

		$rules['bwpm.view.mailinglist'] = array(
			$usedGroups['Administrator']['id']            => true,
			$usedGroups['Manager']['id']                  => true,
			$usedGroups['BwPostmanAdmin']['id']           => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']   => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id'] => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id'] => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']   => false,
		);

		$rules['bwpm.view.newsletter'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
		);

		$rules['bwpm.view.subscriber'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
		);

		$rules['bwpm.view.template'] = array(
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id'] => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
		);

		$rules['bwpm.admin.campaign'] = array(
			$usedGroups['Administrator']['id']              => true,
			$usedGroups['Manager']['id']                    => true,
			$usedGroups['BwPostmanAdmin']['id']             => true,
			$usedGroups['BwPostmanPublisher']['id']         => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id']  => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']   => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']   => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']     => false,
			$usedGroups['BwPostmanCampaignPublisher']['id'] => false,
		);

		$rules['bwpm.admin.mailinglist'] = array(
			$usedGroups['Administrator']['id']                 => true,
			$usedGroups['Manager']['id']                       => true,
			$usedGroups['BwPostmanAdmin']['id']                => true,
			$usedGroups['BwPostmanPublisher']['id']            => false,
			$usedGroups['BwPostmanCampaignAdmin']['id']        => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']      => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']      => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']        => false,
			$usedGroups['BwPostmanMailinglistPublisher']['id'] => false,
		);

		$rules['bwpm.admin.newsletter'] = array(
			$usedGroups['Administrator']['id']                => true,
			$usedGroups['Manager']['id']                      => true,
			$usedGroups['BwPostmanAdmin']['id']               => true,
			$usedGroups['BwPostmanPublisher']['id']           => false,
			$usedGroups['BwPostmanCampaignAdmin']['id']       => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id']    => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']     => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']       => false,
			$usedGroups['BwPostmanNewsletterPublisher']['id'] => false,
		);

		$rules['bwpm.admin.subscriber'] = array(
			$usedGroups['Administrator']['id']                => true,
			$usedGroups['Manager']['id']                      => true,
			$usedGroups['BwPostmanPublisher']['id']           => false,
			$usedGroups['BwPostmanAdmin']['id']               => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']       => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id']    => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']     => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']       => false,
			$usedGroups['BwPostmanSubscriberPublisher']['id'] => false,
		);

		$rules['bwpm.admin.template'] = array(
			$usedGroups['Administrator']['id']              => true,
			$usedGroups['Manager']['id']                    => true,
			$usedGroups['BwPostmanAdmin']['id']             => true,
			$usedGroups['BwPostmanPublisher']['id']         => false,
			$usedGroups['BwPostmanCampaignAdmin']['id']     => false,
			$usedGroups['BwPostmanMailinglistAdmin']['id']  => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']   => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']   => false,
			$usedGroups['BwPostmanTemplatePublisher']['id'] => false,
		);

		$this->componentRules = $rules;

		return true;
	}

	/**
	 * Method to initialize assets for all sections/tables over all possible usergroups
	 * To prevent warnings at restore, usergroups are reduced to them that are installed
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function initializeSectionAssets()
	{
		// Set all actions possible in and with sections
		$actions = array('create', 'edit', 'edit.own', 'edit.state', 'archive', 'restore', 'delete', 'send');

		$rules = $this->componentRules;

		// Get all actions of usergroups which might have to do with BwPostman and which exists at this installation
		$reducedGroupsActions = $this->getReducedSampleRightsArray();

		if ($reducedGroupsActions === false)
		{
			return false;
		}

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				$singularTableName = substr($table['tableNameRaw'], 0, -1);

				foreach ($actions as $action)
				{
					if ($action == 'send' && $singularTableName != 'newsletter')
					{
						continue;
					}

					if ($action != 'send')
					{
						$rules['bwpm.' . $singularTableName . '.' . $action] = $reducedGroupsActions[$action];
					}
					else
					{
						$rules['bwpm.' . $singularTableName . '.' . $action] = $reducedGroupsActions['newsletter.' . $action];
					}

					$BwPmSectionAdmin     = 'BwPostman' . ucfirst($singularTableName) . 'Admin';
					$BwPmSectionPublisher = 'BwPostman' . ucfirst($singularTableName) . 'Publisher';
					$BwPmSectionEditor    = 'BwPostman' . ucfirst($singularTableName) . 'Editor';

					//@ToDo: Set permissions for section groups
					$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionAdmin] = true;

					switch ($action)
					{
						case 'create':
						case 'edit.own':
						case 'send':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor]    = true;
							break;

						case 'edit':
						case 'edit.state':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor]    = false;
							break;

						case 'archive':
						case 'restore':
						case 'delete':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = false;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor]    = false;
							break;
					}
				}
			}
		}

		$reducedRules = $this->reduceRightsForInstalledGroups($rules);

		$this->sectionRules = $reducedRules;

		return true;
	}

	/**
	 * Method to get all predefined (sample) rules, reduced to installed usergroups
	 *
	 * @return array|boolean
	 *
	 * @since 2.0.0
	 */
	private function getReducedSampleRightsArray()
	{
		$bwpmUserGroups = $this->getAllBwpmUserGroups();

		if ($bwpmUserGroups === false)
		{
			return false;
		}

		$joomlaGroups   = $this->getJoomlaGroups();

		if ($joomlaGroups === false)
		{
			return false;
		}

		$usedGroups     = array_merge($bwpmUserGroups, $joomlaGroups);

		$allRightsForInstalledGroups = array();

		// First: Set general rules for all sample BwPostman and basic Joomla! usergroups
		$actions['create'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => true,
			'BwPostmanEditor'           => true,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['edit'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => true,
			'BwPostmanEditor'           => false,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['edit.own'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => true,
			'BwPostmanEditor'           => true,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['edit.state'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => true,
			'BwPostmanEditor'           => false,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['archive'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => false,
			'BwPostmanEditor'           => false,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['restore'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => false,
			'BwPostmanEditor'           => false,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['delete'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => false,
			'BwPostmanEditor'           => false,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => false,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		$actions['newsletter.send'] = array(
			'Administrator'             => true,
			'Manager'                   => true,
			'Publisher'                 => true,
			'Editor'                    => true,
			'BwPostmanAdmin'            => true,
			'BwPostmanManager'          => true,
			'BwPostmanPublisher'        => true,
			'BwPostmanEditor'           => true,
			'BwPostmanCampaignAdmin'    => false,
			'BwPostmanMailinglistAdmin' => false,
			'BwPostmanNewsletterAdmin'  => true,
			'BwPostmanSubscriberAdmin'  => false,
			'BwPostmanTemplateAdmin'    => false,
		);

		// Second:; Check if usergroups are installed. If so, take the rule in return array
		// @ToDo: Also remove child groups, if noting changed related to parent
		foreach ($actions as $action => $groupRules)
		{
			if (key_exists('Administrator', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['Administrator']['title']] = $groupRules['Administrator'];
			}

			if (key_exists('Manager', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['Manager']['title']] = $groupRules['Manager'];
			}

			if (key_exists('Publisher', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['Publisher']['title']] = $groupRules['Publisher'];
			}

			if (key_exists('Editor', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['Editor']['title']] = $groupRules['Editor'];
			}

			if (key_exists('BwPostmanAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanAdmin']['title']] = $groupRules['BwPostmanAdmin'];
			}

			if (key_exists('BwPostmanManager', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanManager']['title']] = $groupRules['BwPostmanManager'];
			}

			if (key_exists('BwPostmanPublisher', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanPublisher']['title']] = $groupRules['BwPostmanPublisher'];
			}

			if (key_exists('BwPostmanEditor', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanEditor']['title']] = $groupRules['BwPostmanEditor'];
			}

			if (key_exists('BwPostmanCampaignAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanCampaignAdmin']['title']] = $groupRules['BwPostmanCampaignAdmin'];
			}

			if (key_exists('BwPostmanMailinglistAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanMailinglistAdmin']['title']] = $groupRules['BwPostmanMailinglistAdmin'];
			}

			if (key_exists('BwPostmanNewsletterAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanNewsletterAdmin']['title']] = $groupRules['BwPostmanNewsletterAdmin'];
			}

			if (key_exists('BwPostmanSubscriberAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanSubscriberAdmin']['title']] = $groupRules['BwPostmanSubscriberAdmin'];
			}

			if (key_exists('BwPostmanTemplateAdmin', $usedGroups))
			{
				$allRightsForInstalledGroups[$action][$usedGroups['BwPostmanTemplateAdmin']['title']] = $groupRules['BwPostmanTemplateAdmin'];
			}
		}

		$this->usedGroups = $usedGroups;

		return $allRightsForInstalledGroups;
	}

	/**
	 * Method to compare rules with that of parent to reduce redundant entries
	 *
	 * @param array $actionRules
	 *
	 * @return array $reducedRules
	 *
	 * @since 2.0.0
	 */
	private function reduceRightsForInstalledGroups($actionRules)
	{
		$groups = $this->usedGroups;

		$reducedRules = array();

		foreach ($actionRules as $action => $groupRules)
		{
			$reducedRule = array();
			for ($i = 0; $i < count($groupRules); $i++)
			{
				if (key_exists('Administrator', $groupRules))
				{
					$reducedRule[$groups['Administrator']['id']] = $groupRules['Administrator'];
				}

				if (key_exists('Manager', $groupRules)
					&& key_exists('Administrator', $groupRules)
					&& $groupRules['Manager'] != $groupRules['Administrator'])
				{
					$reducedRule[$groups['Manager']['id']] = $groupRules['Manager'];
				}

				if (key_exists('Publisher', $groupRules)
					&& key_exists('Manager', $groupRules)
					&& $groupRules['Publisher'] != $groupRules['Manager'])
				{
					$reducedRule[$groups['Publisher']['id']] = $groupRules['Publisher'];
				}
				elseif (key_exists('Publisher', $groupRules)
					&& key_exists('Administrator', $groupRules)
					&& $groupRules['Publisher'] != $groupRules['Administrator'])
				{
					$reducedRule[$groups['Publisher']['id']] = $groupRules['Publisher'];
				}

				if (key_exists('Editor', $groupRules)
					&& key_exists('Publisher', $groupRules)
					&& $groupRules['Editor'] != $groupRules['Manager'])
				{
					$reducedRule[$groups['Editor']['id']] = $groupRules['Editor'];
				}

				if (key_exists('BwPostmanAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanAdmin']['id']] = $groupRules['BwPostmanAdmin'];
				}

				if (key_exists('BwPostmanPublisher', $groupRules)
					&& key_exists('BwPostmanAdmin', $groupRules)
					&& $groupRules['BwPostmanPublisher'] != $groupRules['BwPostmanAdmin'])
				{
					$reducedRule[$groups['BwPostmanPublisher']['id']] = $groupRules['BwPostmanPublisher'];
				}

				if (key_exists('BwPostmanEditor', $groupRules)
					&& key_exists('BwPostmanPublisher', $groupRules)
					&& $groupRules['BwPostmanEditor'] != $groupRules['BwPostmanPublisher'])
				{
					$reducedRule[$groups['BwPostmanEditor']['id']] = $groupRules['BwPostmanEditor'];
				}

				if (key_exists('BwPostmanCampaignAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanCampaignAdmin']['id']] = $groupRules['BwPostmanCampaignAdmin'];
				}

				if (key_exists('BwPostmanCampaignPublisher', $groupRules)
					&& key_exists('BwPostmanCampaignAdmin', $groupRules)
					&& $groupRules['BwPostmanCampaignPublisher'] != $groupRules['BwPostmanCampaignAdmin'])
				{
					$reducedRule[$groups['BwPostmanCampaignPublisher']['id']] = $groupRules['BwPostmanCampaignPublisher'];
				}

				if (key_exists('BwPostmanCampaignEditor', $groupRules)
					&& key_exists('BwPostmanCampaignPublisher', $groupRules)
					&& $groupRules['BwPostmanCampaignEditor'] != $groupRules['BwPostmanCampaignPublisher'])
				{
					$reducedRule[$groups['BwPostmanCampaignEditor']['id']] = $groupRules['BwPostmanCampaignEditor'];
				}

				if (key_exists('BwPostmanMailinglistAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanMailinglistAdmin']['id']] = $groupRules['BwPostmanMailinglistAdmin'];
				}

				if (key_exists('BwPostmanMailinglistPublisher', $groupRules)
					&& key_exists('BwPostmanMailinglistAdmin', $groupRules)
					&& $groupRules['BwPostmanMailinglistPublisher'] != $groupRules['BwPostmanMailinglistAdmin'])
				{
					$reducedRule[$groups['BwPostmanMailinglistPublisher']['id']] = $groupRules['BwPostmanMailinglistPublisher'];
				}

				if (key_exists('BwPostmanMailinglistEditor', $groupRules)
					&& key_exists('BwPostmanMailinglistPublisher', $groupRules)
					&& $groupRules['BwPostmanMailinglistEditor'] != $groupRules['BwPostmanMailinglistPublisher'])
				{
					$reducedRule[$groups['BwPostmanMailinglistEditor']['id']] = $groupRules['BwPostmanMailinglistEditor'];
				}

				if (key_exists('BwPostmanNewsletterAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanNewsletterAdmin']['id']] = $groupRules['BwPostmanNewsletterAdmin'];
				}

				if (key_exists('BwPostmanNewsletterPublisher', $groupRules)
					&& key_exists('BwPostmanNewsletterAdmin', $groupRules)
					&& $groupRules['BwPostmanNewsletterPublisher'] != $groupRules['BwPostmanNewsletterAdmin'])
				{
					$reducedRule[$groups['BwPostmanNewsletterPublisher']['id']] = $groupRules['BwPostmanNewsletterPublisher'];
				}

				if (key_exists('BwPostmanNewsletterEditor', $groupRules)
					&& key_exists('BwPostmanNewsletterPublisher', $groupRules)
					&& $groupRules['BwPostmanNewsletterEditor'] != $groupRules['BwPostmanNewsletterPublisher'])
				{
					$reducedRule[$groups['BwPostmanNewsletterEditor']['id']] = $groupRules['BwPostmanNewsletterEditor'];
				}

				if (key_exists('BwPostmanSubscriberAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanSubscriberAdmin']['id']] = $groupRules['BwPostmanSubscriberAdmin'];
				}

				if (key_exists('BwPostmanSubscriberPublisher', $groupRules)
					&& key_exists('BwPostmanSubscriberAdmin', $groupRules)
					&& $groupRules['BwPostmanSubscriberPublisher'] != $groupRules['BwPostmanSubscriberAdmin'])
				{
					$reducedRule[$groups['BwPostmanSubscriberPublisher']['id']] = $groupRules['BwPostmanSubscriberPublisher'];
				}

				if (key_exists('BwPostmanSubscriberEditor', $groupRules)
					&& key_exists('BwPostmanSubscriberPublisher', $groupRules)
					&& $groupRules['BwPostmanSubscriberEditor'] != $groupRules['BwPostmanSubscriberPublisher'])
				{
					$reducedRule[$groups['BwPostmanSubscriberEditor']['id']] = $groupRules['BwPostmanSubscriberEditor'];
				}

				if (key_exists('BwPostmanTemplateAdmin', $groupRules))
				{
					$reducedRule[$groups['BwPostmanTemplateAdmin']['id']] = $groupRules['BwPostmanTemplateAdmin'];
				}

				if (key_exists('BwPostmanTemplatePublisher', $groupRules)
					&& key_exists('BwPostmanTemplateAdmin', $groupRules)
					&& $groupRules['BwPostmanTemplatePublisher'] != $groupRules['BwPostmanTemplateAdmin'])
				{
					$reducedRule[$groups['BwPostmanTemplatePublisher']['id']] = $groupRules['BwPostmanTemplatePublisher'];
				}

				if (key_exists('BwPostmanTemplateEditor', $groupRules)
					&& key_exists('BwPostmanTemplatePublisher', $groupRules)
					&& $groupRules['BwPostmanTemplateEditor'] != $groupRules['BwPostmanTemplatePublisher'])
				{
					$reducedRule[$groups['BwPostmanTemplateEditor']['id']] = $groupRules['BwPostmanTemplateEditor'];
				}
			}

			$reducedRules[$action] = $reducedRule;
		}

		return $reducedRules;
	}

	/**
	 * Method to provide space for new asset by shifting right value by 2 since parent asset
	 * Shift is 2 to provide space for new left and right value
	 *
	 * @param $com_asset
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function shiftRightAssets($com_asset)
	{
		$query = $this->db->getQuery(true);

		$query->update($this->db->quoteName('#__assets'));
		$query->set($this->db->quoteName('rgt') . " = (" . $this->db->quoteName('rgt') . " + 2 ) ");
		$query->where($this->db->quoteName('rgt') . ' >= ' . $com_asset['rgt']);

		$this->db->setQuery($query);

		try
		{
			$move_asset_right = $this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $move_asset_right;
	}

	/**
	 * Method to provide space for new asset by shifting left value by 2 above parent asset
	 * Shift is 2 to provide space for new left and right value
	 *
	 * @param $com_asset
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function shiftLeftAssets($com_asset)
	{
		$query = $this->db->getQuery(true);

		$query->update($this->db->quoteName('#__assets'));
		$query->set($this->db->quoteName('lft') . " = (" . $this->db->quoteName('lft') . " + 2 ) ");
		$query->where($this->db->quoteName('lft') . ' > ' . $com_asset['rgt']);

		$this->db->setQuery($query);

		try
		{
			$move_asset_left = $this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $move_asset_left;
	}

	/**
	 * Method to insert new asset at space provided by shiftRightAsset() and shiftLeftAsset()
	 *
	 * @param $com_asset
	 * @param $asset
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	private function insertAssetToTable($com_asset, $asset)
	{
		$query = $this->db->getQuery(true);

		$query->insert($this->db->quoteName('#__assets'));

		$query->columns(
			array(
				$this->db->quoteName('id'),
				$this->db->quoteName('parent_id'),
				$this->db->quoteName('lft'),
				$this->db->quoteName('rgt'),
				$this->db->quoteName('level'),
				$this->db->quoteName('name'),
				$this->db->quoteName('title'),
				$this->db->quoteName('rules')
			)
		);
		$query->values(
			$this->db->quote(0) . ',' .
			$this->db->quote($com_asset['id']) . ',' .
			$this->db->quote((int) $com_asset['rgt']) . ',' .
			$this->db->quote((int) $com_asset['rgt'] + 1) . ',' .
			$this->db->quote((int) $com_asset['level'] + 1) . ',' .
			$this->db->quote($asset['name']) . ',' .
			$this->db->quote($asset['title']) . ',' .
			$this->db->quote($asset['rules'])
		);
		$this->db->setQuery($query);

		try
		{
			$insert_asset = $this->db->execute();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $insert_asset;
	}

	/**
	 * Get complete asset from assets table by asset name
	 *
	 * @param $assetName
	 *
	 * @return string|boolean
	 *
	 * @since 2.0.0
	 */
	private function getAssetFromAssetsTableByName($assetName)
	{
		$query = $this->db->getQuery(true);

		$query->select('*');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->quote($assetName));

		$this->db->setQuery($query);

		try
		{
			$base_asset = $this->db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $base_asset;
	}

	/**
	 * Extracts base asset from provided array. Used for getting asset from state array.
	 *
	 * @param array $table
	 * @param       $stateAssetsRaw
	 *
	 * @return mixed array|boolean
	 *
	 * @since 2.0.0
	 */
	protected function extractBaseAssetFromState($table, $stateAssetsRaw)
	{
		$assetName = 'com_bwpostman';

		if ($table['tableNameUC'] != 'component')
		{
			$assetName .= '.' . strtolower($table['tableNameUC']);
		}

		foreach ($stateAssetsRaw as $asset)
		{
			if (key_exists('name', $asset) && $asset['name'] == $assetName)
			{
				return $asset;
			}
		}

		return true;
	}

	/**
	 * Method to get section asset from table by provided raw table name. Also usable for component asset
	 *
	 * @param $table
	 *
	 * @return mixed array|boolean
	 *
	 * @since 2.0.0
	 */
	protected function getBaseAssetFromTable($table)
	{
		$searchValue = 'com_bwpostman';

		if ($table != 'component')
		{
			$searchValue .= '.' . substr($table, 0, -1);
		}

		$query = $this->db->getQuery(true);

		$query->select('*');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->quote($searchValue));
		$this->db->setQuery($query);

		try
		{
			$base_asset = $this->db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $base_asset;
	}

	/**
	 * Method to get asset title for a specific table (hard coded)
	 *
	 * @param $table
	 *
	 * @return string
	 *
	 * @since 2,0.0
	 */
	protected function getAssetTitle($table)
	{
		$switchValue = $table;

		if (is_array($table))
		{
			$switchValue = $table['tableNameGeneric'];
		}

		switch ($switchValue)
		{
			case '#__bwpostman_campaigns':
			case '#__bwpostman_mailinglists':
			case '#__bwpostman_templates':
			default:
				$title = 'title';
				break;
			case '#__bwpostman_newsletters':
				$title = 'subject';
				break;
			case '#__bwpostman_subscribers':
				$title = 'name';
				break;
		}

		return $title;
	}

	/**
	 * Method to get all BwPostman usergroups, which are used at sections with assets
	 *
	 * @return array|boolean
	 *
	 * @since 2.0.0
	 */
	private function getAllBwpmUserGroups()
	{
		$bwpmUserGroups = array();

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);

			if ($hasAsset === -1)
			{
				return false;
			}

			if ($hasAsset)
			{
				$bwpmTableGroups = $this->getBwPostmanUsergroups($table['tableNameUC']);

				if ($bwpmTableGroups === false)
				{
					return false;
				}

				$bwpmUserGroups = array_merge($bwpmTableGroups, $bwpmUserGroups);
			}
		}

		return $bwpmUserGroups;
	}

	/**
	 * Method to get all Joomla! usergroups that might be used at BwPostman
	 *
	 * @return array|boolean
	 *
	 * @since 2.0.0
	 */
	private function getJoomlaGroups()
	{
		$searchValues = array("'Administrator'", "'Manager'", "'Publisher'", "'Editor'");

		$query = $this->db->getQuery(true);

		$query->select($this->db->quoteName('title'));
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__usergroups'));
		$query->where($this->db->quoteName('title') . ' IN (' . implode(',', $searchValues) . ')');

		$this->db->setQuery($query);

		try
		{
			$joomlaGroups = $this->db->loadAssocList('title');
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $joomlaGroups;
	}

	/**
	 * Method to get all assets of BwPostman to save
	 *
	 * @return array|mixed
	 *
	 * @since 2.4.0
	 */
	protected function getAllBwPostmanAssetsToSave()
	{
		$query = $this->db->getQuery(true);

		// Get the assets for component from database
		$query->select('*');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->quote('com_bwpostman'));

		$this->db->setQuery($query);

		try
		{
			$assets = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $assets;
	}

	/**
	 * Method to get all ids and asset_ids of a table with assets of BwPostman
	 *
	 * @param $tableNameGeneric
	 *
	 * @return array|boolean
	 *
	 * @since 2.4.0
	 *
	 */
	private function getItemAssetList($tableNameGeneric)
	{
		$query = $this->db->getQuery(true);
		$query->select('id');
		$query->select('asset_id');
		$query->from($this->db->quoteName($tableNameGeneric));

		$this->db->setQuery($query);
		try
		{
			$items = $this->db->loadObjectList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $items;
	}

	/**
	 * Method to check if an asset_id exists at assets table
	 *
	 * @param $assetId
	 *
	 * @return boolean|integer
	 *
	 * @since 2.4.0
	 *
	 */
	private function checkAssetIdExists($assetId)
	{
		$res = null;

		$query = $this->db->getQuery(true);
		$query->select('id');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('id') . ' = ' . (int) $assetId);

		$this->db->setQuery($query);
		try
		{
			$res = $this->db->loadResult();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return -1;
		}

		if ($res === $assetId)
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get all Items of a table of BwPostman, which have asset_id = 0. This is the indicator that an asset is needed
	 * but not present at asset table.
	 *
	 * @param $assetId
	 * @param $assetName
	 *
	 * @return boolean
	 *
	 * @since 2.4.0
	 *
	 */
	private function checkAssetNameFits($assetId, $assetName)
	{
		$res = null;

		$query = $this->db->getQuery(true);
		$query->select('name');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('id') . ' = ' . (int) $assetId);

		$this->db->setQuery($query);
		try
		{
			$res = $this->db->loadResult();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return -1;
		}

		if ($res === $assetName)
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get an asset id by an asset name. If the name exists returns the asset id, else false
	 *
	 * @param $assetName
	 *
	 * @return integer|boolean
	 *
	 * @since 2.4.0
	 *
	 */
	private function getAssetIdByAssetName($assetName)
	{
		$assetId = null;

		$query = $this->db->getQuery(true);
		$query->select('id');
		$query->from($this->db->quoteName('#__assets'));
		$query->where($this->db->quoteName('name') . ' = ' . $this->db->quote($assetName));

		$this->db->setQuery($query);
		try
		{
			$assetId = (integer) $this->db->loadResult();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return -1;
		}

		if ($assetId !== null)
		{
			return $assetId;
		}

		return false;
	}

	/**
	 * Method to update the asset ids at a specific table of BwPostman
	 *
	 * @param string $tableNameGeneric
	 * @param array  $assetIdsByName itemId|assetId
	 *
	 * @return boolean
	 *
	 * @since 2.4.0
	 *
	 */
	private function healAssetsAtItemsTable($tableNameGeneric, $assetIdsByName)
	{
		// @ToDo: Here a simple foreach loop is used because it is expected, that there are less entries at the array. Perhaps
		// a more speed-friendly version with a bunch of updates at the same time is needed
		foreach ($assetIdsByName as $id => $assetId)
		{
			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName($tableNameGeneric));
			$query->set($this->db->quoteName('asset_id') . " = " . $this->db->Quote($assetId));
			$query->where($this->db->quoteName('id') . ' = ' . $this->db->Quote($id));

			$this->db->setQuery($query);

			try
			{
				$this->db->execute();
			}
			catch (RuntimeException $exception)
			{
				$message = $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}
		return true;
	}

	/**
	 * Method to get all items of a table of BwPostman, which have asset_id = 0. This is the indicator that an asset is needed
	 * but not present at asset table. The ids of items with asset_id = 0 are known
	 *
	 * @param string $tableNameGeneric
	 * @param array  $itemIds
	 *
	 * @return array|boolean
	 *
	 * @since 2.4.0
	 *
	 */
	private function getCompleteItemsWithoutAssetId($tableNameGeneric, $itemIds)
	{
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName($tableNameGeneric));
		$query->where($this->db->quoteName('id') . ' IN (' . implode(',', $itemIds) . ')');

		$this->db->setQuery($query);
		try
		{
			$items = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $items;
	}

	/**
	 * Method to detect all items of a table of BwPostman, which have asset_id = 0. This is the indicator that an asset is needed
	 * but not present at asset table.
	 *
	 * @param $tableNameGeneric
	 *
	 * @return array|boolean
	 *
	 * @throws Exception
	 * @since 2.0.0
	 *
	 */
	private function getItemsWithoutAssetId($tableNameGeneric)
	{
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName($tableNameGeneric));
		$query->where($this->db->quoteName('asset_id') . ' = ' . (int) 0);

		$this->db->setQuery($query);

		try
		{
			$items = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $items;
	}

	/**
	 * Method to prepare collected values (array) to a string used by insert query for multiple inserts
	 *
	 * @param array $default_asset asset that holds current preset for new asset
	 * @param       $item
	 * @param       $title
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function writeInsertStringFromCurrentItem(&$default_asset, $item, $title)
	{
		$curr_asset          = $default_asset;
		$curr_asset['lft']   = $default_asset['lft'];
		$curr_asset['rgt']   = $default_asset['rgt'];
		$curr_asset['name']  = $default_asset['name'] . '.' . $item['id'];
		$curr_asset['title'] = $this->db->escape($item[$title]);

		$default_asset['lft'] += 2;
		$default_asset['rgt'] += 2;

		$dataset = '(';

		foreach ($this->assetColnames as $colName)
		{
			$dataset .= "'" . $curr_asset[$colName] . "',";
		}

		$dataset = substr($dataset, 0, -1) . ')';

		return $dataset;
	}

	/**
	 * Method to quote the values of an array for use as strings with database
	 *
	 * @param $arrayData
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function dbQuoteArray($arrayData)
	{
		$values = array();

		foreach ($arrayData as $k => $v)
		{
			$values[$k] = $this->db->quote($v);
		}

		return $values;
	}

	/**
	 * Method to loop over given items without assets, prepare the insert query sting and write the query.
	 * To prevent memory overflow or runtime exceptions, there is a (conservatively hard coded) max value for one insert task.
	 * Also there is a mapping created to hold the newly created asset ids to update items. Remember: asset_id was 0 at item,
	 * now there is an asset_id which the item should know about
	 *
	 * @param $itemsWithoutAsset
	 * @param $table
	 *
	 * @return array|boolean
	 *
	 * @throws exception
	 *
	 * @since 2.0.0
	 */
	private function insertAssets($itemsWithoutAsset, $table)
	{
		$sectionAsset = $this->getBaseAsset($table['tableNameRaw'], true);

		if ($sectionAsset === false)
		{
			return  false;
		}

		if (!is_array($sectionAsset) || !key_exists('rules', $sectionAsset))
		{
			$sectionAsset = $this->insertBaseAsset($table);
		}

		if ($sectionAsset === false)
		{
			return false;
		}

		$default_asset = $this->getDefaultAsset($sectionAsset);
		$title         = $this->getAssetTitle($table['tableNameGeneric']);

		$assetLoopCounter = 0;
		$asset_loop       = 0;
		$asset_loop_max   = 1000;
		$asset_max        = count($itemsWithoutAsset);

		$mapOldAssetIdsToNew = array();

		$this->assetColnames = array_keys($this->db->getTableColumns('#__assets'));

		// Collect assets data sets
		foreach ($itemsWithoutAsset as $item)
		{
			$asset_loop++;

			$mapOldAssetIdsToNew[$assetLoopCounter]['ItemId'] = $item['id'];

			$dataset[] = $this->writeInsertStringFromCurrentItem($default_asset, $item, $title);

			$assetLoopCounter++;

			// if asset loop max is reached or last data set, insert into table
			if (($asset_loop == $asset_loop_max) || ($assetLoopCounter == $asset_max))
			{
				// write collected assets to table
				if (!$this->writeLoopAssets($dataset, $assetLoopCounter, $sectionAsset, $mapOldAssetIdsToNew))
				{
					return false;
				}

				//reset loop values
				$asset_loop = 0;
				$dataset    = array();
			}
		}

		return $mapOldAssetIdsToNew;
	}

	/**
	 * Method to write items with newly created asset_ids
	 * This method could be used for newly created items (e.g. at installation) as well as while restoring tables
	 *
	 * @param $itemsWithoutAsset
	 * @param $tableNameGeneric
	 * @param $mapOldAssetIdsToNew
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 *
	 */
	private function insertItems($itemsWithoutAsset, $tableNameGeneric, $mapOldAssetIdsToNew)
	{
		/*
		 * Import item data (can't use table bind/store, because we have IDs and Joomla sets mode to update,
		 * if ID is set, but in empty tables there is nothing to update)
		 */
		$max_count        = ini_get('max_execution_time');
		$assetLoopCounter = 0;
		$count            = 0;
		$data_loop_max    = $this->getDataLoopMax($tableNameGeneric);
		$data_max         = count($itemsWithoutAsset);

		if ($count++ == $data_max)
		{
			$count = 0;
			ini_set('max_execution_time', ini_get('max_execution_time'));
		}

		// If there are data sets
		if ($data_max)
		{
			$dataset   = array();
			$data_loop = 0;

			// … insert data sets…
			foreach ($itemsWithoutAsset as $item)
			{
				$data_loop++;

				// update asset_id
				for ($i = 0; $i < count($mapOldAssetIdsToNew); $i++)
				{
					$itemIdentified = array_search($item['id'], $mapOldAssetIdsToNew[$i]);
					if ($itemIdentified !== false)
					{
						$item['asset_id'] = (int) $mapOldAssetIdsToNew[$i]['newAssetId'];
						break;
					}
				}

				if ($count++ == $max_count)
				{
					$count = 0;
					ini_set('max_execution_time', ini_get('max_execution_time'));
				}

				// collect data sets until loop max
				$values = $this->dbQuoteArray($item);

				$dataset[] = '(' . implode(',', $values) . ')';
				$assetLoopCounter++;

				// if data loop max is reached or last data set, insert into table
				if (($data_loop == $data_loop_max) || ($assetLoopCounter == $data_max))
				{
					// write collected data sets to table
					if (!$this->writeLoopDatasets($dataset, $tableNameGeneric))
					{
						return false;
					}

					// reset loop values
					$data_loop = 0;
					$dataset   = array();
				}
			} // end foreach table items
		} // endif data sets exists

		return true;
	}

	/**
	 * Method to delete a specific BwPostman table
	 *
	 * @param $table
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 *
	 */
	protected function deleteBwPostmanTable($table)
	{
		try
		{
			$drop_table = $this->db->dropTable($table);

			if (!$drop_table)
			{
				$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_ERROR', $table);
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}
		}
		catch (RuntimeException $exception)
		{
			$message =  $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}
		$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_SUCCESS', $table);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

		echo '<p class="bw_tablecheck_ok">' . $message . '</p>';

		return  true;
	}

	/**
	 * Method to create a specific BwPostman table anew.
	 * Used while restoring tables from backup. The create query comes from backup file, because we have to meet
	 * BwPostman/table version appropriate to saved tables. After restoring a check of tables is automatically done by
	 * BwPostman to ensure tables now meet installed BwPostman version.
	 *
	 * @param string $table         table to create
	 * @param array  $tablesQueries query used for creation
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 *
	 */
	protected function createBwPostmanTableAnew($table, $tablesQueries)
	{
		if ($table != 'component')
		{
			$query = str_replace("\n", '', $tablesQueries[$table]['queries']);
			$this->db->setQuery($query);

			try
			{
				$create_table = $this->db->execute();
				if (!$create_table)
				{
					$message = Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_ERROR', $table);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));
				}
			}
			catch (RuntimeException $exception)
			{
				$message =  $exception->getMessage();
				$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

				return false;
			}

			$message =  Text::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_SUCCESS', $table);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'maintenance'));

			echo '<p class="bw_tablecheck_ok">' . $message . '</p>';
		}

		return true;
	}

	/**
	 * Method to prepare asset dataset for write to asset table
	 *
	 * @param array   $asset               asset to prepare
	 * @param array   $asset_transform     array to hold map for item id, old asset id and newly created asset id
	 *                                     item id is already written, old asset id is entered here
	 * @param integer $s                   control counter
	 * @param array   $base_asset          base asset to get parent id
	 * @param integer $asset_siblings      variable to memorize current values for rgt and lft
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function prepareAssetValues($asset, &$asset_transform, $s, $base_asset, &$asset_siblings)
	{
		$values = array();

		foreach ($asset as $k => $v)
		{
			// rewrite parent_id, lft and rgt
			switch ($k)
			{
				case 'id':
					$asset_transform[$s]['old'] = $v;
					$values['id']               = 0;
					break;
				case 'parent_id':
					$values['parent_id'] = $base_asset['id'];
					break;
				case 'lft':
					$values['lft'] = $asset_siblings++;
					break;
				case 'rgt':
					$values['rgt'] = $asset_siblings++;
					break;
				default:
					$values[$k] = $this->db->quote($v);
					break;
			}
		}

		return '(' . implode(',', $values) . ')';
	}

	/**
	 * Method to get the table data of a BwPostman table for saving
	 *
	 * @param string  $tableName     the name of the table to save
	 *
	 * @return array|boolean false on failure
	 *
	 * @since 2.4.0
	 */
	private function getTableDataToSave($tableName)
	{
		$query = $this->db->getQuery(true);

		// Get the data from table
		$query->select('*');
		$query->from($this->db->quoteName($tableName));

		$this->db->setQuery($query);

		try
		{
			$data = $this->db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
			$message = $exception->getMessage();
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'maintenance'));

			return false;
		}

		return $data;
	}
}
