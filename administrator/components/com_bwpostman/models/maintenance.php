<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL and Helper object class
jimport('joomla.application.component.model');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require some classes
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/libraries/exceptions/BwException.php');
//require_once (JPATH_COMPONENT_ADMINISTRATOR . '/libraries/logging/BwLogger.php');

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
	 * Constructor
	 *
	 * @since       1.0.1
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Method to save tables
	 *
	 * Cannot use JFile::write() because we want to append data
	 *
	 * @access    public
	 *
	 * @param   boolean $update
	 *
	 * @return  mixed
	 *
	 * @throws  BwException on errors
	 *
	 * @since       1.0.1
	 */
	public function saveTables($update = false)
	{
		// Access check.
		if (!BwPostmanHelper::canAdmin())
		{
			return false;
		}

		// Import JFolder and JFileObject class
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// create (empty) backup file
		$date      = JFactory::getDate()->format("Y-m-d_H_i");
		$path      = IS_WIN ? JFactory::getConfig()->get('tmp_path') : JFolder::makeSafe(JFactory::getConfig()->get('tmp_path'));
		$file_name = $path . '/' . JFile::makeSafe('BwPostman_Tables_Server_' . $date . '.xml');

		try
		{
			if (!JFolder::exists($path))
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_FOUND', $path) . '</p>';

					return false;
				}
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_FOUND', $path));
			}
			$handle = fopen($file_name, 'wb');
			if ($handle === false)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_WRITABLE', $path) . '</p>';

					return false;
				}
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_WRITABLE', $path));
			}

			// get all names of installed BwPostman tables
			$tableNames = $this->getTableNamesFromDB();
			if ($tableNames === null)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_GET_TABLE_NAMES', $path) . '</p>';

					return false;
				}
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_GET_TABLE_NAMES'));
			}

			// write file header
			$file_data   = array('');
			$file_data[] = $this->buildXmlHeader();

			if (fwrite($handle, implode("\n", $file_data)) === false)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITING_HEADER', $path) . '</p>';

					return false;
				}
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITING_HEADER'));
			}

			foreach ($tableNames as $table)
			{
				// do not save the table "bwpostman_templates_tpl"
				if (strpos($table, 'templates_tpl') === false)
				{
					$file_data = array();
					$tableName = $this->getGenericTableName($table);

					$file_data[] = "\t\t<tables>";                                // set XML tables section
					$file_data[] = $this->buildXmlStructure($tableName);            // get table description
					if (fwrite($handle, implode("\n", $file_data)) === false)
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name) . '</p>';

							return false;
						}
						throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name));
					}
					else
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_TABLE_SUCCESS', $tableName) . '</p>';
						}
					}
					$file_data = array();

					$res = $this->buildXmlData($tableName, $handle);        // write table data
					if (!$res)
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name) . '</p>';

							return false;
						}
						break;
					}
					$file_data   = array();
					$file_data[] = $this->buildXmlAssets($tableName);                // write data assets
					$file_data[] = "\t\t</tables>\n";                                // set XML tables section
					if (fwrite($handle, implode("\n", $file_data)) === false)
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_ASSETS_WRITE_FILE_ERROR', $file_name) . '</p>';

							return false;
						}
						throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_ASSETS_WRITE_FILE_ERROR', $file_name));
					}
					$file_data = array();
				}
			}

			$file_data[] = $this->buildXmlFooter();                            // get XML footer
			$file_data   = implode("\n", $file_data);

			if (fwrite($handle, $file_data) !== false)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_SUCCESS', $file_name) . '</p>';
				}
			}
			else
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name) . '</p>';

					return false;
				}
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name));
			}
			fclose($handle);
		}
		catch (BwException $e)
		{
			echo $e->getMessage();
			JFile::delete($file_name);
			fclose($handle);

			return false;
		}

		if ($update)
		{
			return true;
		}
		else
		{
			return $file_name;
		}
	}

	/**
	 * Method to get a list of names of all installed tables of BwPostman form database
	 *
	 * @return    array $tableNames    list of names of all installed table names of BwPostman
	 *
	 * @since    1.0.1
	 */
	public static function getTableNamesFromDB()
	{
		$_db        = JFactory::getDbo();
		$tableNames = array();

		// Get database name
		$dbname = self::getDBName();

		//build query to get all names of installed BwPostman tables
		$query = "SHOW TABLES WHERE `Tables_in_{$dbname}` LIKE '%bwpostman%'";

		$_db->setQuery($query);

		try
		{
			$tableNames = $_db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $tableNames;
	}

	/**
	 * Builds the XML data header for the tables to export. Based on Joomla JDatabaseExporter
	 *
	 * @return    string    An XML string
	 *
	 * @since    1.0.1
	 */
	protected function buildXmlHeader()
	{
		// Get version of BwPostman
		$version = $this->getBwPostmanVersion();

		// Get database name
		$dbname = self::getDBName();

		$buffer = array();

		// build generals
		$buffer[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$buffer[] = '<mysqldump xmlns:xsi="http://www.w3.org/TR/xmlschema-1">';
		$buffer[] = "\t<database name=\"$dbname\">";
		$buffer[] = "\t\t<Generals>";
		$buffer[] = "\t\t\t<BwPostmanVersion>" . $version . "</BwPostmanVersion>";
		$buffer[] = "\t\t\t<SaveDate>" . JFactory::getDate()->format("Y-m-d_H:i") . "</SaveDate>";

		$_db    = JFactory::getDbo();
		$data   = array();
		$query  = $_db->getQuery(true);

		// Get the assets for component from database
		$query->select('*');
		$query->from($_db->quoteName('#__assets'));
		$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman'));

		$_db->setQuery($query);

		try
		{
			$data = $_db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// write component asset
		$buffer[] = "\t\t\t" . '<component_assets>';
		$buffer[] = "\t\t\t\t" . '<dataset>';
		if (is_array($data))
		{
			foreach ($data[0] as $key => $value)
			{
				$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
				$buffer[]      = "\t\t\t\t\t<" . $key . ">" . $insert_string . "</" . $key . ">";
			}
		}
		$buffer[] = "\t\t\t\t" . '</dataset>';
		$buffer[] = "\t\t\t</component_assets>";

		// process user groups
		$groups = $this->getByAssetUsedUsergroups();

		$buffer[] = "\t\t\t" . '<component_usergroups>';
		if (is_array($groups))
		{
			foreach ($groups as $item)
			{
				$buffer[] = "\t\t\t\t" . '<usergroup>';
				foreach ($item as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					$buffer[]      = "\t\t\t\t\t<" . $key . ">" . $insert_string . "</" . $key . ">";
				}
				$buffer[] = "\t\t\t\t" . '</usergroup>';
			}
		}
		$buffer[] = "\t\t\t</component_usergroups>";

		$buffer[] = "\t\t</Generals>";
		$buffer[] = "";

		return implode("\n", $buffer);
	}

	/**
	 * Get all usergroups used by assets of BwPostman
	 *
	 * @return    array            id and name of user groups
	 *
	 * @since    1.3.0
	 */
	private function getByAssetUsedUsergroups()
	{
		$_db        = JFactory::getDbo();
		$query      = $_db->getQuery(true);
		$allgroups  = array();
		$rules      = array();

		// Get all asset rules of BwPostman
		$query->select('rules');
		$query->from($_db->quoteName('#__assets'));
		$query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote('%com_bwpostman%'));

		$_db->setQuery($query);

		try
		{
			$rules = $_db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
						if ($value == '1')
						{
							$allgroups[] = $key;
						}
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
			$tree   = array();
			$query  = $_db->getQuery(true);
			$query->select('p.id');
			$query->from($_db->quoteName('#__usergroups') . ' AS n, ' . $_db->quoteName('#__usergroups') . ' AS p');
			$query->where('n.lft BETWEEN p.lft AND p.rgt');
			$query->where('n.id = ' . (int) $group);
			$query->order('p.lft');

			$_db->setQuery($query);

			try
			{
				$tree = $_db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
			$query     = $_db->getQuery(true);
			$sub_query = $_db->getQuery(true);

			$sub_query->select('COUNT(*)-1');
			$sub_query->from($_db->quoteName('#__usergroups') . ' AS n');
			$sub_query->from($_db->quoteName('#__usergroups') . ' AS p');
			$sub_query->where('n.lft BETWEEN p.lft AND p.rgt');
			$sub_query->where('n.id = ' . (int) $group);
			$sub_query->group('n.lft');
			$sub_query->order('n.lft');

			$query->select('(' . $sub_query . ') AS level, p.id, p.title, p.parent_id');
			$query->from($_db->quoteName('#__usergroups') . 'AS p');
			$query->where($_db->quoteName('id') . ' = ' . (int) $group);

			$_db->setQuery($query);

			try
			{
				$res_groups[] = $_db->loadAssoc();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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

		if (JFolder::exists(JPATH_PLUGINS . '/bwpostman/'))
		{
			$path2     = JPATH_PLUGINS . '/bwpostman/';
			$p_folders = JFolder::folders($path2);

			foreach ($p_folders as $folder)
			{
				if (JFolder::exists($path2 . $folder . '/sql/'))
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
				echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OPEN_INSTALL_FILE_ERROR', $filename) . '</p>';
				return false;
			}
			else
			{
				// empty arrays
				$file_content = array();
				$txt_array    = array();

				// get file content
				while (!feof($fh)) $file_content[] = fgets($fh);
				fclose($fh);

				// eliminate unneeded rows (comments, empty lines, DROP TABLE)
				foreach ($file_content as $row)
				{
					if ((strpos($row, '--') === false) && (stripos($row, 'DROP') === false) && (trim($row) != ''))
					{
						$txt_array[] = $row;
					}
				}

				$queries    = array();
				$string     = '';
				$i          = 0;
				foreach ($txt_array as $key => $value) {
					$pos = strpos ($value, 'CREATE');
					if ($pos !== false) {
						if ($i != 0)
						{ // fill array only with complete query
							$queries[]  = $string;
						}
						$string      = $value . ' ';
					}
					else
					{
						$string  .= $value . ' ';
					}
					$i++;
				}
				$queries[]  = $string;

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
	 * @param    string $table The name of the table.
	 *
	 * @return    string            The name of the table with the database prefix replaced with #__.
	 *
	 * @since    1.0.1
	 */

	public function getGenericTableName($table)
	{
		$_db = JFactory::getDbo();

		// get db prefix
		$prefix = $_db->getPrefix();

		// Replace the magic prefix if found.
		$table = preg_replace("|^$prefix|", '#__', $table);

		return $table;
	}

	/**
	 * Method to compare needed tables names with installed ones, check engine, default charset and primary key
	 *
	 * @param    array  $neededTables      object list of tables, that must be installed
	 * @param    array  $genericTableNames names of tables, that are installed
	 * @param    string $mode              mode to check, "check and repair" or "restore"
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

		$_db              = JFactory::getDbo();
		$neededTableNames = array();

		// extract table names from table object list,
		foreach ($neededTables as $table)
		{
			$neededTableNames[] = $table->name;
		}

		try
		{
			// compare table names first direction (all needed tables installed?)
			$diff_1 = array_diff($neededTableNames, $genericTableNames);
			if (!empty($diff_1))
			{
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED', implode(',', $diff_1)) . '</p>';

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

					$_db->setQuery($query);
					$createDB = $_db->execute();
					if (!$createDB)
					{
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_ERROR', $missingTable) . '</p>';
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_SUCCESS', $missingTable) . '</p>';
					}
				}
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ALL_TABLES_INSTALLED') . '</p>';
			}

			// compare table names second direction (obsolete tables installed?). Only if in check mode
			if ($mode == 'check')
			{
				$diff_2 = array_diff($genericTableNames, $neededTableNames);
				if (!empty($diff_2))
				{
					echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE', implode(',', $diff_2)) . '</p>';

					// delete obsolete tables
					foreach ($diff_2 as $obsoleteTable)
					{
						$query = "DROP TABLE IF EXISTS " . $obsoleteTable;

						$_db->setQuery($query);
						$deleteDB = $_db->execute();
						if (!$deleteDB)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_ERROR', $obsoleteTable) . '</p>';
						}
						else
						{
							echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_SUCCESS', $obsoleteTable) . '</p>';
						}
					}
				}
				else
				{
					echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NO_OBSOLETE_TABLES') . '</p>';
				}
			}

			// check table engine and default charset
			foreach ($neededTables as $table)
			{
				$create_statement = $_db->getTableCreate($table->name);
				$engine           = '';
				$c_set            = '';
				$collation        = '';

				// get engine of installed table
				$start = strpos($create_statement[$table->name], 'ENGINE=');
				if ($start !== false)
				{
					$stop   = strpos($create_statement[$table->name], ' ', $start);
					$length = $stop - $start - 7;
					$engine = substr($create_statement[$table->name], $start + 7, $length);
				}

				// get default charset of installed table
				$start = strpos($create_statement[$table->name], 'DEFAULT CHARSET=');
				$stop  = 0;
				if ($start !== false)
				{
					$stop   = strpos($create_statement[$table->name], ' ', $start);
					$length = $stop - $start;
					$c_set  = substr($create_statement[$table->name], $start + 16, $length);
				}

				// get collation of installed table
				$start = strpos($create_statement[$table->name], 'COLLATE=', $stop);
				if ($start !== false)
				{
					$collation = substr($create_statement[$table->name], $start + 8);
				}

				if ((strcasecmp($engine, $table->engine) != 0) || (strcasecmp($c_set, $table->charset) != 0) || (strcasecmp($collation, $table->collation) != 0))
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
					$query = 'ALTER TABLE ' . $_db->quoteName($table->name) . $engine_text . $c_set_text . $collation_text;
					$_db->setQuery($query);
					$modifyTable = $_db->execute();
					if (!$modifyTable)
					{
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_ERROR', $table->name) . '</p>';

						return false;
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_SUCCESS', $table->name) . '</p>';
					}
				}
			}
			echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ENGINE_OK') . '</p>';

			// check primary key (There can be only one!) and auto increment
			foreach ($neededTables as $table)
			{
				// get key of installed table
				$installed_key_tmp = $_db->getTableKeys($table->name);
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

				// compare table key
				if (strcasecmp($table->primary_key, $installed_key) != 0)
				{
					echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_WRONG', $table->name) . '</p>';

					if ($installed_key != '')
					{
						// wrong primary key, get type of key and drop wrong key
						$type = '';
						foreach ($table->columns as $column)
						{
							if ($column->Column == $installed_key)
							{
								$type = $column->Type;
							}
						}
						$query = 'ALTER TABLE ' . $_db->quoteName($table->name);
						$query .= ' MODIFY ' . $_db->quoteName($installed_key) . ' ';
						$query .= $type . ', DROP PRIMARY KEY';
						$_db->setQuery($query);
						$_db->execute();
					}

					$query     = 'ALTER TABLE ' . $_db->quoteName($table->name) . ' ADD PRIMARY KEY (' . $_db->quoteName($table->primary_key) . ')';
					$_db->setQuery($query);
					$modifyKey = $_db->execute();

					if (!$modifyKey)
					{
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_ERROR', $table->name) . '</p>';

						return false;
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_SUCCESS', $table->name) . '</p>';
					}
				}

				// get col name of autoincrement of installed table
				if (property_exists($table, 'auto'))
				{
					$query = 'SHOW columns FROM ' . $_db->quoteName($table->name) . ' WHERE extra = "auto_increment"';
					$_db->setQuery($query);
					$increment_key = $_db->loadResult();

					if (strcasecmp($table->auto, $increment_key) != 0)
					{
						echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_WRONG', $table->name) . '</p>';

						$query = 'ALTER TABLE ' . $_db->quoteName($table->name);
						$query .= ' MODIFY ' . $_db->quoteName($table->primary_key);
						$query .= ' INT AUTO_INCREMENT';
						$_db->setQuery($query);
						$incrementKey = $_db->execute();
						if (!$incrementKey)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_ERROR', $table->name) . '</p>';

							return false;
						}
						else
						{
							echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_SUCCESS', $table->name) . '</p>';
						}
					}
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_OK') . '</p>';

		return true;
	}

	/**
	 * Method to check needed tables columns
	 *
	 * @param    object $checkTable object of table, that must be installed
	 *
	 * @return    boolean        true if all is ok
	 *
	 * @since    1.0.1
	 */
	public function checkTableColumns($checkTable)
	{
		if (!is_object($checkTable))
		{
			return 0;
		}

		$_db = JFactory::getDbo();

		$neededColumns    = array();
		$installedColumns = array();

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
		foreach ($_db->getTableColumns($checkTable->name, false) as $col)
		{
			$installedColumns[] = ArrayHelper::fromObject($col, true);
		}

		// prepare check for col names
		$search_cols_1 = array();
		$search_cols_2 = array();
		foreach ($installedColumns as $col)
		{
			$search_cols_1[] = $col['Field'];
		}
		foreach ($neededColumns as $col)
		{
			$search_cols_2[] = $col['Column'];
		}

		try
		{
			// check for col names
			for ($i = 0; $i < count($neededColumns); $i++)
			{
				// check for needed col names
				if (array_search($neededColumns[$i]['Column'], $search_cols_1) === false)
				{
					($neededColumns[$i]['Null'] == 'NO') ? $null = ' NOT NULL' : $null = ' NULL ';
					(isset($neededColumns[$i]['Default'])) ? $default = ' DEFAULT ' . $_db->quote($neededColumns[$i]['Default']) : $default = '';

					echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COLS', $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
					$query = "ALTER TABLE " . $_db->quoteName($checkTable->name);
					$query .= " ADD " . $_db->quoteName($neededColumns[$i]['Column']);
					$query .= ' ' . $neededColumns[$i]['Type'] . $null . $default;
					$query .= " AFTER " . $_db->quoteName($neededColumns[$i - 1]['Column']);

					$_db->setQuery($query);
					$insertCol = $_db->execute();

					if (!$insertCol)
					{
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_ERROR', $neededColumns[$i]['Column'], $checkTable->name) . '</p>';

						return 0;
					}
					else
					{
						echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_SUCCESS', $neededColumns[$i]['Column'], $checkTable->name) . '</p>', 4096);

						return 2; // reset iteration
					}
				}

				// check for obsolete col names
				if (array_search($installedColumns[$i]['Field'], $search_cols_2) === false)
				{

					echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COLS', $installedColumns[$i]['Field'], $checkTable->name) . '</p>';
					$query = "ALTER TABLE " . $_db->quoteName($checkTable->name) . " DROP " . $_db->quoteName($installedColumns[$i]['Field']);

					$_db->setQuery($query);
					$deleteCol = $_db->execute();

					if (!$deleteCol)
					{
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_ERROR', $installedColumns[$i]['Field'], $checkTable->name) . '</p>';

						return 0;
					}
					else
					{
						echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_SUCCESS', $installedColumns[$i]['Field'], $checkTable->name) . '</p>', 4096);

						return 2; // reset iteration

					}
				}
			}
			echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_OK', $checkTable->name) . '</p>', 4096);

			for ($i = 0; $i < count($neededColumns); $i++)
			{
				$diff = array_udiff($neededColumns[$i], $installedColumns[$i], 'strcasecmp');
				if (!empty($diff))
				{
					echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES', implode(',', array_keys($diff)), $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
					// install missing columns
					foreach (array_keys($diff) as $missingCol)
					{
						($neededColumns[$i]['Null'] == 'NO') ? $null = ' NOT NULL' : $null = 'YES';
						(isset($neededColumns[$i]['Default'])) ? $default = ' DEFAULT ' . $_db->quote($neededColumns[$i]['Default']) : $default = '';
						$query = "ALTER TABLE " . $_db->quoteName($checkTable->name);
						$query .= " MODIFY " . $_db->quoteName($neededColumns[$i]['Column']) . ' ' . $neededColumns[$i]['Type'] . $null . $default;
						if (array_key_exists('Extra', $neededColumns[$i]))
						{
							$query .= " " . $neededColumns[$i]['Extra'];
						}

						$_db->setQuery($query);
						$alterCol = $_db->execute();
						if (!$alterCol)
						{
							echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_ERROR', $missingCol, $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
						}
						else
						{
							echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_SUCCESS', $missingCol, $neededColumns[$i]['Column'], $checkTable->name) . '</p>', 4096);
						}
					}
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_ATTRIBUTES_OK', $checkTable->name) . '</p>', 4096);

		return 1;
	}

	/**
	 * Method to check, if column asset_id has a real value. If not, there is no possibility to delete data sets in BwPostman.
	 * Therefore each dataset without real value for asset_id has to be stored one time, to get this value
	 *
	 * @return    bool
	 *
	 * @since    1.0.1
	 */
	public function checkAssetId()
	{
		$_db            = JFactory::getDbo();
		// set tables that has column asset_id
		$tablesToCheck = array('#__bwpostman_campaigns', '#__bwpostman_mailinglists', '#__bwpostman_newsletters', '#__bwpostman_subscribers', '#__bwpostman_templates');
		$asset_loop     = 0;


		// get items without real asset id (=0)
		foreach ($tablesToCheck as $table)
		{
			$base_asset     = $this->_getBaseAsset($table);
			if (!is_array($base_asset) || !key_exists('rules', $base_asset))
			{
				$base_asset = $this->_insertBaseAsset($table);
			}
			$curr_asset_id  = $base_asset['rgt'];
			$items          = array();

			$query  = $_db->getQuery(true);
			$query->select('*');
			$query->from($_db->quoteName($table));
			$query->where($_db->quoteName('asset_id') . ' = ' . (int) 0);

			$_db->setQuery($query);
			try
			{
				$items = $_db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// if there are items without asset id, get table object…
			if (is_array($items))
			{
				// get raw table name, table object and asset name
				$table_name_raw = $this->_getRawTableName($table);
				$tableObject    = JTable::getInstance($table_name_raw, 'BwPostmanTable');

				if (property_exists($tableObject, 'asset_id'))
				{
					$asset_name = $base_asset['name'];
				}
				else
				{
					$asset_name = '';
				}

				// get title for asset
				switch ($table)
				{
					case '#__bwpostman_campaigns':
					case '#__bwpostman_mailinglists':
					case '#__bwpostman_templates':
					default:
							$title   = 'title';
						break;
					case '#__bwpostman_newsletters':
							$title   = 'subject';
						break;
					case '#__bwpostman_subscribers':
							$title   = 'name';
						break;
				}

				// set some loop values (block size, …)
				$default_asset   = $this->_getDefaultAsset($table);
				$data_loop_max   = $this->_getDataLoopMax($table);
				$max_count       = ini_get('max_execution_time');
				$data_max        = count($items);
				$asset_max       = $data_max;
				$asset_loop_max  = 1000;
				$asset_transform = array();

				//Asset Inserting
				$s      = 0;
				$count  = 0;

				// if there are data sets
				if ($asset_max)
				{
					$asset_loop = 0;
				}

				// … insert data sets…
				foreach ($items as $item)
				{
					$asset_loop++;

					if ($count++ == $max_count)
					{
						$count = 0;
						ini_set('max_execution_time', ini_get('max_execution_time'));
					}
					$values = array();

					// collect data sets until loop max
					$curr_asset                 = $default_asset;
					$curr_asset['lft']          = $curr_asset_id++;
					$curr_asset['rgt']          = $curr_asset_id++;
					$curr_asset['name']         = $asset_name . '.' . $_db->escape($item['id']);
					$curr_asset['title']        = $_db->escape($item[$title]);

					foreach($curr_asset as $k => $v)
					{
						$values[$k] = $_db->quote($v);
					}

					$asset_transform[$s]['id'] = $item['id'];

					$dataset[] = '(' . implode(',', $values) . ')';
					$s++;

					// if asset loop max is reached or last data set, insert into table
					if (($asset_loop == $asset_loop_max) || ($s == $asset_max))
					{
						// write collected assets to table
						$this->_writeLoopAssets($dataset, $s, $base_asset, $asset_transform);

						//reset loop values
						$asset_loop = 0;
						$dataset    = array();
					}
				} // end foreach table assets

				// import data (can't use table bind/store, because we have IDs and Joomla sets mode to update, if ID is set, but in empty tables there is nothing to update)
				$s     = 0;
				$count = 0;

				// if there are data sets
				if ($data_max)
				{
					$dataset   = array();
					$data_loop = 0;

					// … insert data sets…
					foreach ($items as $item)
					{
						$data_loop++;

						// update asset_id
						if ($asset_name != '')
						{
							for ($i = 0; $i < count($asset_transform); $i++)
							{
								$new_id = array_search($item['id'], $asset_transform[$i]);
								if ($new_id !== false)
								{
									$item['asset_id'] = (int) $asset_transform[$i]['new'];
									break;
								}
							}
						}

						if ($count++ == $max_count)
						{
							$count = 0;
							ini_set('max_execution_time', ini_get('max_execution_time'));
						}
						$values = array();

						// collect data sets until loop max
						foreach ($item as $k => $v)
						{
							$values[] = $_db->quote($v);
						}
						$dataset[] = '(' . implode(',', $values) . ')';
						$s++;

						// if data loop max is reached or last data set, insert into table
						if (($data_loop == $data_loop_max) || ($s == $data_max))
						{
							// write collected data sets to table
							$this->_writeLoopDatasets($dataset, $table);

							// reset loop values
							$data_loop = 0;
							$dataset   = array();
						}
					} // end foreach table items
				} // endif data sets exists

			}
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSET_OK', $table) . '</p>';
			}
		return true;
	}

	/**
	 * Method to check, if user_id of subscriber matches ID in joomla user table, updating if mail address exists.
	 * Only datasets with entered user_id in table subscribers will be checked
	 *
	 * @return    bool
	 *
	 * @since    1.0.1
	 */
	public function checkUserIds()
	{
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		try
		{
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('user_id') . ' > ' . (int) 0);

			$_db->setQuery($query);
			$users = $_db->loadObjectList();

			// update user_id in subscribers table
			foreach ($users as $user)
			{
				// get ids from users table if mail address exists in user table
				$query->clear();
				$query->select($_db->quoteName('id'));
				$query->from($_db->quoteName('#__users'));
				$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user->email));

				$_db->setQuery($query);
				$user->user_id = $_db->loadResult();

				// update subscribers table
				$query->clear();
				$query->update($_db->quoteName('#__bwpostman_subscribers'));
				$query->set($_db->quoteName('user_id') . " = " . (int) $user->user_id);
				$query->where($_db->quoteName('id') . ' = ' . (int) $user->id);

				$_db->setQuery($query);
				$_db->execute();
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return true;
	}

	/**
	 * Builds the XML structure to export. Based on Joomla JDatabaseExporter
	 *
	 * @param   string $tableName name of table to build structure for
	 *
	 * @return    array    An array of XML lines (strings).
	 *
	 * @since    1.0.1
	 */
	private function buildXmlStructure($tableName)
	{
		$_db    = JFactory::getDbo();
		$buffer = array();
		$fields = array();
		$keys   = array();
		$query  = '';

		// Get the details columns information and install query.
		try
		{
			$keys   = $_db->getTableKeys($tableName);
			$fields = $_db->getTableColumns($tableName, false);
			$query  = implode('', $_db->getTableCreate($tableName));
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$buffer[] = "\t\t\t<table_structure table=\"$tableName\">";
		$buffer[] = "\t\t\t\t<table_name>";
		$buffer[] = "\t\t\t\t\t<name>$tableName</name>";
		$buffer[] = "\t\t\t\t</table_name>";
		$buffer[] = "\t\t\t\t<install_query>";
		$buffer[] = "\t\t\t\t\t<query>$query</query>";
		$buffer[] = "\t\t\t\t</install_query>";

		if (is_array($fields))
		{
			$buffer[] = "\t\t\t\t<fields>";
			foreach ($fields as $field)
			{
				$buffer[] = "\t\t\t\t\t<field>";
				$buffer[] = "\t\t\t\t\t\t<Column>$field->Field</Column>";
				$buffer[] = "\t\t\t\t\t\t<Type>$field->Type</Type>";
				$buffer[] = "\t\t\t\t\t\t<Null>$field->Null</Null>";
				$buffer[] = "\t\t\t\t\t\t<Key>$field->Key</Key>";
				if (isset($field->Default))
				{
					$buffer[] = "\t\t\t\t\t\t<Default>$field->Default</Default>";
				}
				$buffer[] = "\t\t\t\t\t\t<Extra>$field->Extra</Extra>";
				$buffer[] = "\t\t\t\t\t</field>";
			}
			$buffer[] = "\t\t\t\t</fields>";
		}

		if (is_array($keys))
		{
			$buffer[] = "\t\t\t\t<keys>";
			foreach ($keys as $key)
			{
				$buffer[] = "\t\t\t\t\t<key>";
				$buffer[] = "\t\t\t\t\t\t<Non_unique>$key->Non_unique</Non_unique>";
				$buffer[] = "\t\t\t\t\t\t<Key_name>$key->Key_name</Key_name>";
				$buffer[] = "\t\t\t\t\t\t<Seq_in_index>$key->Seq_in_index</Seq_in_index>";
				$buffer[] = "\t\t\t\t\t\t<Column_name>$key->Column_name</Column_name>";
				$buffer[] = "\t\t\t\t\t\t<Collation>$key->Collation</Collation>";
				$buffer[] = "\t\t\t\t\t\t<Null>$key->Null</Null>";
				$buffer[] = "\t\t\t\t\t\t<Index_type>$key->Index_type</Index_type>";
				$buffer[] = "\t\t\t\t\t\t<Comment>htmlspecialchars($key->Comment)</Comment>";
				$buffer[] = "\t\t\t\t\t</key>";
			}
			$buffer[] = "\t\t\t\t</keys>";
		}

		$buffer[] = "\t\t\t</table_structure>\n";

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML data to export
	 *
	 * @param    string   $tableName name of table
	 * @param    resource $handle    handle of backup file
	 *
	 * @return   bool        True on success
	 *
	 * @throws  BwException if writing file is not possible
	 *
	 * @since    1.0.1
	 */
	private function buildXmlData($tableName, $handle)
	{
		// Import JFolder and JFileObject class
		jimport('joomla.filesystem.file');

		$_db    = JFactory::getDbo();
		$query  = $_db->getQuery(true);
		$data   = array();

		// Get the data from table
		$query->select('*');
		$query->from($_db->quoteName($tableName));

		$_db->setQuery($query);

		try
		{
			$data = $_db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (fwrite($handle, "\t\t\t<table_data table=\"$tableName\">\n") === false)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_GENERAL'));
		}

		if (is_array($data))
		{
			foreach ($data as $item)
			{
				if (fwrite($handle, "\t\t\t\t<dataset>\n") === false)
				{
					throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_GENERAL'));
				}

				foreach ($item as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					if (((($tableName == '#__bwpostman_sendmailcontent') || ($tableName == '#__bwpostman_tc_sendmailcontent')) && ($key == 'body'))
						|| (($tableName == '#__bwpostman_newsletters') && ($key == 'html_version'))
						|| (($tableName == '#__bwpostman_templates') && (($key == 'tpl_html') || ($key == 'tpl_css') || ($key == 'tpl_article') || ($key == 'tpl_divider')))
					)
					{
						$insert_string = '<![CDATA[' . $insert_string . ']]>';
					}
					if (fwrite($handle, "\t\t\t\t\t<$key>" . $insert_string . "</$key>\n") === false)
					{
						throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_GENERAL'));
					}
				}
				if (fwrite($handle, "\t\t\t\t</dataset>\n") === false)
				{
					throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_GENERAL'));
				}
			}
		}

		if (fwrite($handle, "\t\t\t</table_data>\n") === false)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_GENERAL'));
		}

		return true;
	}

	/**
	 * Builds the XML assets to export
	 *
	 * @param    string $tableName name of table
	 *
	 * @return    array    An array of XML lines (strings).
	 *
	 * @since    1.0.1
	 */
	private function buildXmlAssets($tableName)
	{
		$target_tables  = array('campaigns', 'mailinglists', 'newsletters', 'subscribers', 'templates');
		$start          = strpos($tableName, '_', 3);
		$table_name_raw = '';
		if ($start !== false)
		{
			$table_name_raw = substr($tableName, $start + 1);
		}
		if (in_array($table_name_raw, $target_tables))
		{
			$asset_name = '%com_bwpostman.' . substr($table_name_raw, 0, strlen($table_name_raw) - 1) . '%';
			$buffer     = array();
			$data       = array();

			$_db   = JFactory::getDbo();
			$query = $_db->getQuery(true);

			// Get the assets for this table from database
			$query->select('*');
			$query->from($_db->quoteName('#__assets'));
			$query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote($asset_name));

			$_db->setQuery($query);

			try
			{
				$data = $_db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			$buffer[] = "\t\t\t" . '<table_assets table="' . $tableName . '">';
			if (is_array($data))
			{
				foreach ($data as $item)
				{
					$buffer[] = "\t\t\t\t<dataset>";
					foreach ($item as $key => $value)
					{
						$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
						$buffer[]      = "\t\t\t\t\t<" . $key . ">" . $insert_string . "</" . $key . ">";
					}
					$buffer[] = "\t\t\t\t</dataset>";
				}
			}
			$buffer[] = "\t\t\t</table_assets>";
		}
		else
		{
			$buffer[] = "\t\t\t" . '<table_assets table="' . $tableName . '">';
			$buffer[] = "\t\t\t</table_assets>";
		}

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML data footer for the tables to export
	 *
	 * @return    string    An XML string
	 *
	 * @since    1.0.1
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
	 * @since    1.3.0
	 */
	public function outputGeneralInformation()
	{
		// Output general information
		$generals   = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.generals', null);

		if (key_exists('BwPostmanVersion', $generals) || key_exists('SaveDate', $generals))
		{
			echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_OUTPUT_GENERALS') . '</h4>';
			if (key_exists('BwPostmanVersion', $generals))
			{
				echo '<p class="bw_tablecheck_info">' . JText::_('COM_BWPOSTMAN_VERSION') . $generals['BwPostmanVersion'] . '</p>';
			}
			if (key_exists('SaveDate', $generals))
			{
				echo '<p class="bw_tablecheck_info">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_DATE') . $generals['SaveDate'] . '</p>';
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
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function processAssetUserGroups($table_names)
	{
		try
		{
			// process user groups, if they exists in backup
			$com_assets = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());
			$usergroups = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.usergroups', array());
			$tmp_file   = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
			$fp         = fopen($tmp_file, 'r');
			$tables     = unserialize(fread($fp, filesize($tmp_file)));
			fclose($fp);

			if (count($usergroups))
			{
				$new_groups = $this->_getActualUserGroups($usergroups);

				if (is_array($new_groups))
				{
					// rewrite component asset user groups
					$this->_rewriteAssetUserGroups('component', $com_assets, $new_groups);
					$com_assets = JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);

					// rewrite table asset user groups
					foreach ($table_names as $table)
					{
						// table with assets?
						if (key_exists('table_assets', $tables[$table]))
						{
							// get table assets
							$assets = $tables[$table]['table_assets'];
							$this->_rewriteAssetUserGroups($table, $assets, $new_groups);
						}
					}
				}
				JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);
				JFactory::getApplication()->setUserState('com_bwpostman.maintenance.usergroups', '');
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_PROCESSED') . '</p>';
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_MESSAGE') . '</p>';
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to the rewrite tables
	 *
	 * @param   string $table name of table to rewrite
	 *
	 * @return    JResponseJson
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function reWriteTables($table)
	{
		try
		{
			$tmp_file           = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
			$fp                 = fopen($tmp_file, 'r');
			$tables             = unserialize(fread($fp, filesize($tmp_file)));
			$_db                = JFactory::getDbo();
			$asset_loop         = 0;
			$curr_asset_id      = 0;
			$asset_transform    = array();
			$base_asset         = array();


			// delete table
			$drop_table = $_db->dropTable($table);
			if (!$drop_table)
			{
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_ERROR', $table));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_SUCCESS', $table) . '</p>';
			}

			// create this table anew
			$query = str_replace("\n", '', $tables[$table]['queries']);
			$_db->setQuery($query);
			$create_table = $_db->execute();
			if (!$create_table)
			{
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_ERROR', $table));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_SUCCESS', $table) . '</p>';
			}

			// get raw table name, table object and asset name
			// @ToDo: process plugin tables
			$table_name_raw = $this->_getRawTableName($table);
			$tableObject    = JTable::getInstance($table_name_raw, 'BwPostmanTable');
			$asset_name     = '';

			// set asset name
			// next if (surrounding tables without assets) is a workaround for plugin table
			if (is_object($tableObject))
			{
				if (property_exists($tableObject, 'asset_id'))
				{
					// write table asset
					$base_asset = $this->_insertBaseAsset($table);
					if (!is_array($base_asset))
					{
						throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR', $table));
					}
					else
					{
//		    			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_SUCCESS', $table) . '</p>';
					}
					$curr_asset_id = $base_asset['lft'] + 1;

//				    $asset_name = 'com_bwpostman.' . substr($table_name_raw, 0, strlen($table_name_raw) - 1);
					$asset_name = $base_asset['name'];
				}
			}

			// set some loop values (block size, …)
			$data_loop_max  = $this->_getDataLoopMax($table);
			$max_count      = ini_get('max_execution_time');
			$data_max       = 0;
			if (key_exists('table_data', $tables[$table]))
			{
				$data_max = count($tables[$table]['table_data']);
			}

			$asset_loop_max = 1000;
			$asset_max      = 0;
			if (isset($tables[$table]['table_assets']))
			{
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
						$update_asset   = array_shift($tables[$table]['table_assets']);
						$this->_updateBaseAsset($update_asset);
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
							$values = array();

							// collect data sets until loop max
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
										$values['lft'] = $curr_asset_id++;
										break;
									case 'rgt':
										$values['rgt'] = $curr_asset_id++;
										break;
									default:
										$values[$k] = $_db->quote($v);
										break;
								}
							}
							$dataset[] = '(' . implode(',', $values) . ')';
							$s++;

							// if asset loop max is reached or last data set, insert into table
							if (($asset_loop == $asset_loop_max) || ($s == $asset_max))
							{
								// write collected assets to table
								$this->_writeLoopAssets($dataset, $s, $base_asset, $asset_transform);

								//reset loop values
								$asset_loop = 0;
								$dataset    = array();
							}
						} // end foreach table assets
					} // end switch base asset
				} // end table assets exists
			} // end asset inserting

			// import data (can't use table bind/store, because we have IDs and Joomla sets mode to update, if ID is set, but in empty tables there is nothing to update)
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
						$asset_found    = false;
						for ($i = 0; $i < count($asset_transform); $i++)
						{
							$new_id = array_search($item['asset_id'], $asset_transform[$i]);
							if ($new_id !== false)
							{
								$item['asset_id'] = $asset_transform[$i]['new'];
								$asset_found    = true;
								break;
							}
						}
						if(!$asset_found) {
							$item['asset_id'] = 0;
						}
					}

					if ($count++ == $max_count)
					{
						$count = 0;
						ini_set('max_execution_time', ini_get('max_execution_time'));
					}
					$values = array();

					// collect data sets until loop max
					foreach ($item as $k => $v)
					{
						$values[] = $_db->quote($v);
					}
					$dataset[] = '(' . implode(',', $values) . ')';
					$s++;

					// if data loop max is reached or last data set, insert into table
					if (($data_loop == $data_loop_max) || ($s == $data_max))
					{
						// write collected data sets to table
						$this->_writeLoopDatasets($dataset, $table);

						// reset loop values
						$data_loop = 0;
						$dataset   = array();
					}
				} // end foreach table items
			} // endif data sets exists
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STORE_SUCCESS', $table) . '</p><br />';

			if ($table == '#__bwpostman_subscribers')
			{
				self::checkUserIds();
			}

			if($table_name_raw == 'newsletters') { // for transaction test purposes only
//				throw new BwException(JText::_('Test-Exception Newsletter written'));
			}

			if ($table_name_raw == 'templates')
			{
				fclose($fp);
				unlink($tmp_file);
				$this->_deleteRestorePoint();
			}
			//JFactory::getApplication()->setUserState('com_bwpostman.maintenance.curr_asset_id', $curr_asset_id);
		}
		catch (BwException $e)
		{
			fclose($fp);
			throw new BwException($e->getMessage());
		}
		catch (RuntimeException $e)
		{
			fclose($fp);
			throw new BwException($e->getMessage());
		}
	}

	/**
	 * Method to get the version of BwPostman
	 *
	 * @return    string    version
	 *
	 * @since    1.0.8
	 */
	private function getBwPostmanVersion()
	{
		$db     = JFactory::getDbo();
		$query  = $db->getQuery(true);
		$result = '';

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->setQuery($query);

		try
		{
			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
		$config = JFactory::getConfig();

		// Get database name
		$dbname = $config->get('db', '');

		return $dbname;
	}

	/**
	 * Method to adjust field access in table mailinglists
	 *
	 * in prior versions of BwPostman access holds the values like viewlevels, but beginning with 0.
	 * But 0 is in Joomla the value for new dataset, so in version 1.0.1 of BwPostman this will be adjusted (incremented)
	 *
	 * @return    void
	 *
	 * @since    1.3.0 here, before in install script since 1.0.1
	 */
	public static function adjustMLAccess()
	{
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_mailinglists'));
		$query->set($_db->quoteName('access') . " = " . $_db->quoteName('access') . '+1');
		$_db->setQuery($query);
		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return;
	}

	/**
	 * Method parse XML data
	 *
	 * stores the result array in state
	 *
	 * @param   string  $file
	 *
	 * @return  array   $table_names      array of table names
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function parseTablesData($file)
	{
		if (BWPOSTMAN_LOG_MEM)
		{
//			$log_options = array('test' => 'testtext');
//			$logger      = new BwLogger($log_options);
		}

		if (BWPOSTMAN_LOG_MEM)
		{
//			$logger->addEntry(new JLogEntry(sprintf('Memory consumption before parsing: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
		}

		if ($file == '')
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_ERROR_NO_FILE'));
		}

		// get import file
		if (false === $fh = fopen($file, 'rb'))
		{ // File cannot be opened
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OPEN_FILE_ERROR', $file));
		}

		// get XML data
		$xml = simplexml_load_file($file);
		fclose($fh);

		// check if xml file is ok (most error case: non-xml-conform characters in xml file)
		if (!is_object($xml))
		{
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_READ_XML_ERROR', $file));
		}
		if (!property_exists($xml, 'database'))
		{
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_WRONG_FILE_ERROR', $file));
		}

		if (BWPOSTMAN_LOG_MEM)
		{
//			$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing with XML file: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
		}

		// Get general data
		$generals   = array();
		if (property_exists($xml->database->Generals, 'BwPostmanVersion'))
		{
			$generals['BwPostmanVersion'] = (string)$xml->database->Generals->BwPostmanVersion;
		}
		if (property_exists($xml->database->Generals, 'SaveDate'))
		{
			$generals['SaveDate'] = (string)$xml->database->Generals->SaveDate;
		}
		JFactory::getApplication()->setUserState('com_bwpostman.maintenance.generals', $generals);

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
				if (property_exists($xml->database->Generals->component_assets, 'dataset'))
				{
					$com_assets[] = get_object_vars($xml->database->Generals->component_assets->dataset);
				}
			}
		}
		JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);

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
		JFactory::getApplication()->setUserState('com_bwpostman.maintenance.usergroups', $usergroups);


		// Get all tables from the xml file converted to arrays recursively, results in an array/list of table-arrays
		echo '<h4>' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_PARSE_DATA') . '</h4>';
		$x_tables = array();
		foreach ($xml->database->tables as $table)
		{
			$x_tables[] = $table;
		}
		unset($xml);
		unset($table);

		if (count($x_tables) == 0)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_NO_TABLES_ERROR'));
		}

		$adjust_prefix = false;

		// get db prefix
		$new_prefix = JFactory::getDbo()->getPrefix();

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
		$table_names    = array();
		foreach ($x_tables as $table)
		{
			$table_names[] = (string) $table->table_structure->table_name->name;
		}
		unset($table);

		// get buffer file
		$tmp_file = JFactory::getConfig()->get('tmp_path') . '/bwpostman_restore.tmp';
		if (false === $fp = fopen($tmp_file, 'w+'))
		{ // File cannot be opened
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OPEN_TMPFILE_ERROR', $tmp_file));
		}

		// empty buffer file
		if (false === ftruncate($fp, 0))
		{ // File cannot be truncated
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_TRUNCATE_TMPFILE_ERROR', $tmp_file));
		}

		if (BWPOSTMAN_LOG_MEM)
		{
//			$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing before loop: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
		}

		// paraphrase tables array per table for better handling and convert simple xml objects to strings
		$i = 0;
		while (null !== $tmp_table = array_shift($x_tables))
		{
			if (BWPOSTMAN_LOG_MEM)
			{
//				$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing at very beginning loop: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
			}

			$w_table = array();

			// extract install queries
			$w_table['queries'] = (string) $tmp_table->table_structure->install_query->query;
			if ($adjust_prefix)
			{
				$w_table['queries'] = str_replace($is_prefix, $new_prefix, $w_table['queries']);
			}

			if (BWPOSTMAN_LOG_MEM)
			{
//				$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing at loop with query: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
			}

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
							$ds = array();
							$props  = get_object_vars($item);
							foreach ($props as $k => $v)
							{
								$xy     = (string) $v;
								$ds[$k] = $xy;
							}
							$assets[]    = $ds;
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
			if (BWPOSTMAN_LOG_MEM)
			{
//				$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing at loop with assets: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
			}

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
							$ds = array();
							$props  = get_object_vars($item);
							foreach ($props as $k => $v)
							{
								$xy     = (string) $v;
								$ds[$k] = $xy;
							}
							$items[]    = $ds;
						}
					}
					else
					{
						$items[] = get_object_vars($table_data['dataset']);
					}
				}
			}
			$w_table['table_data'] = $items;
			if (BWPOSTMAN_LOG_MEM)
			{
//				$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing at loop with data sets: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
			}

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
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_WRITE_TMPFILE_ERROR', $tmp_file));
			}
			$i++;
			if (BWPOSTMAN_LOG_MEM)
			{
//				$logger->addEntry(new JLogEntry(sprintf('Memory consumption while parsing of table %s: %01.3f MB', $table_names[$i - 1], (memory_get_peak_usage(true) / (1024.0 * 1024.0)))));
			}
		}
		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_PARSE_SUCCESS') . '</p><br />';
		JFactory::getApplication()->setUserState('com_bwpostman.maintenance.tmp_file', $tmp_file);
		fclose($fp);

		return $table_names;
	}

	/**
	 * Method delete all sub assets of component
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function deleteSubAssets()
	{
		try
		{
			$_db = JFactory::getDbo();

			$query = $_db->getQuery(true);
			$query->delete($_db->quoteName('#__assets'));
			$query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote('%com_bwpostman.%'));

			$_db->setQuery($query);
			$asset_delete = $_db->execute();

			// uncomment next line to test rollback (only makes sense, if deleted tables contained data)
//						throw new BwException(JText::_('Test-Exception DeleteAssets Model'));

			if (!$asset_delete)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_ERROR'));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_SUCCESS') . '</p>';
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to heal assets table
	 *
	 * repairs lft and rgt values in asset table, updates component asset
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function healAssetsTable()
	{
		try
		{
			$com_assets = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());
			$_db        = JFactory::getDbo();
			$query      = $_db->getQuery(true);

			// first get lft from main asset com_bwpostman
			$base_asset = $this->_getBaseAsset();
			$gap        = $base_asset['rgt'] - $base_asset['lft'] - 1;

			// second set rgt values from all assets above lft of BwPostman
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " - " . $gap . ") ");
			$query->where($_db->quoteName('lft') . ' >= ' . $base_asset['lft']);

			$_db->setQuery($query);
			$set_asset_right = $_db->execute();

			// now set lft values from all assets above lft of BwPostman
			$query      = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('lft') . " = (" . $_db->quoteName('lft') . " - " . $gap . ") ");
			$query->where($_db->quoteName('lft') . ' > ' . $base_asset['lft']);

			$_db->setQuery($query);
			$set_asset_left = $_db->execute();

			// next set rgt value of BwPostman and update component rules
			$query      = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('lft') . " + 1)");
			$query->set($_db->quoteName('title') . " = " . $_db->quote('BwPostman Component'));
			if (isset($com_assets[0]['rules']))
			{
				$query->set($_db->quoteName('rules') . " = " . $_db->quote($_db->escape($com_assets[0]['rules'])));
			}
			$query->where($_db->quoteName('lft') . ' = ' . $base_asset['lft']);

			$_db->setQuery($query);
			$set_asset_base = $_db->execute();

			// uncomment next line to test rollback (only makes sense, if deleted tables contained data)
//			throw new BwException(JText::_('Test-Exception HealAssets Model'));

			if (!$set_asset_left || !$set_asset_right || !$set_asset_base)
			{
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR'));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_SUCCESS') . '</p><br />';
				$base_asset['rgt'] = $base_asset['lft'] + 1;
			}
			JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', '');
		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to get the base asset of BwPostman
	 *
	 * @param   string  $table
	 *
	 * @return array    $base_asset     base asset of BwPostman
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _getBaseAsset($table = 'component')
	{
		try
		{
			$_db   = JFactory::getDbo();
			$query = $_db->getQuery(true);

			$query->select('*');
			$query->from($_db->quoteName('#__assets'));

			switch ($table)
			{
				case '#__bwpostman_campaigns':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.campaign'));
					break;
				case '#__bwpostman_mailinglists':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.mailinglist'));
					break;
				case '#__bwpostman_newsletters':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.newsletter'));
					break;
				case '#__bwpostman_subscribers':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.subscriber'));
					break;
				case '#__bwpostman_templates':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.template'));
					break;
				case 'component':
				default:
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman'));
					break;
			}

			$_db->setQuery($query);
			$base_asset = $_db->loadAssoc();

			return $base_asset;

		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to write the table asset
	 *
	 * @param   string  $table
	 *
	 * @return array    $base_asset     base asset of BwPostman
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _insertBaseAsset($table = 'component')
	{
		try
		{
			$com_asset   = $this->_getBaseAsset('component');
			$_db   = JFactory::getDbo();
			$query = $_db->getQuery(true);

			// first shift rgt values by 2 from all assets since rgt of BwPostman
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " + 2 ) ");
			$query->where($_db->quoteName('rgt') . ' >= ' . $com_asset['rgt']);

			$_db->setQuery($query);
			$move_asset_right = $_db->execute();

			// now shift lft values by 2 from all assets above lft of BwPostman
			$query = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('lft') . " = (" . $_db->quoteName('lft') . " + 2 ) ");
			$query->where($_db->quoteName('lft') . ' > ' . $com_asset['rgt']);

			$_db->setQuery($query);
			$move_asset_left = $_db->execute();

			// finally insert new table asset
			$query = $_db->getQuery(true);
			$query->insert($_db->quoteName('#__assets'));

			switch ($table)
			{
				case '#__bwpostman_campaigns':
						$asset_name  = 'com_bwpostman.campaign';
						$asset_title = 'BwPostman Campaigns';
						$asset_rules = '{"bwpm.campaign.edit":{"6":1,"4":1},"bwpm.campaign.edit.state":{"6":1,"5":1},"bwpm.campaign.edit.own":{"6":1,"3":1},"bwpm.campaign.archive":[],"bwpm.campaign.restore":[],"bwpm.campaign.delete":{"6":1}}';
					break;
				case '#__bwpostman_mailinglists':
						$asset_name  = 'com_bwpostman.mailinglist';
						$asset_title = 'BwPostman Mailinglists';
						$asset_rules = '{"bwpm.mailinglist.edit":{"6":1,"4":1},"bwpm.mailinglist.edit.state":{"6":1,"5":1},"bwpm.mailinglist.edit.own":{"6":1,"3":1},"bwpm.mailinglist.archive":[],"bwpm.mailinglist.restore":[],"bwpm.mailinglist.delete":{"6":1}}';
					break;
				case '#__bwpostman_newsletters':
						$asset_name  = 'com_bwpostman.newsletter';
						$asset_title = 'BwPostman Newsletters';
						$asset_rules = '{"bwpm.newsletter.edit":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.edit.state":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.edit.own":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.send":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.archive":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.restore":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.newsletter.delete":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0}}';
					break;
				case '#__bwpostman_subscribers':
						$asset_name  = 'com_bwpostman.subscriber';
						$asset_title = 'BwPostman Subscribers';
						$asset_rules = '{"bwpm.subscriber.edit":{"6":1,"4":1},"bwpm.subscriber.edit.state":{"6":1,"5":1},"bwpm.subscriber.edit.own":{"6":1,"3":1},"bwpm.subscriber.archive":[],"bwpm.subscriber.restore":[],"bwpm.subscriber.delete":{"6":1}}';
					break;
				case '#__bwpostman_templates':
						$asset_name  = 'com_bwpostman.template';
						$asset_title = 'BwPostman Templates';
						$asset_rules = '{"bwpm.template.edit":{"1":0,"9":0,"6":1,"7":0,"2":0,"3":0,"4":1,"5":0,"8":0},"bwpm.template.edit.state":{"1":0,"9":0,"6":1,"7":0,"2":0,"3":0,"4":0,"5":1,"8":0},"bwpm.template.edit.own":{"1":0,"9":0,"6":1,"7":0,"2":0,"3":1,"4":0,"5":0,"8":0},"bwpm.template.send":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.template.archive":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.template.restore":{"1":0,"9":0,"6":0,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0},"bwpm.template.delete":{"1":0,"9":0,"6":1,"7":0,"2":0,"3":0,"4":0,"5":0,"8":0}}';
					break;
				case 'component':
				default:
						$asset_name  = 'com_bwpostman';
						$asset_title = 'BwPostman Component';
						$asset_rules = $com_asset['rules'];
					break;
			}

			$query->columns(array(
				$_db->quoteName('id'),
				$_db->quoteName('parent_id'),
				$_db->quoteName('lft'),
				$_db->quoteName('rgt'),
				$_db->quoteName('level'),
				$_db->quoteName('name'),
				$_db->quoteName('title'),
				$_db->quoteName('rules')
			));
			$query->values(
				$_db->quote(0) . ',' .
				$_db->quote($com_asset['id']) . ',' .
				$_db->quote((int)$com_asset['rgt']) . ',' .
				$_db->quote((int)$com_asset['rgt'] + 1) . ',' .
				$_db->quote((int)$com_asset['level'] + 1) . ',' .
				$_db->quote($asset_name) . ',' .
				$_db->quote($asset_title) . ',' .
				$_db->quote($asset_rules)
			);
			$_db->setQuery($query);
			$insert_asset   = $_db->execute();

			$query->select('*');
			$query->from($_db->quoteName('#__assets'));
			$query->where($_db->quoteName('name') . ' = ' . $_db->quote($asset_name));

			$_db->setQuery($query);

			$base_asset = $_db->loadAssoc();

			if (!$move_asset_left || !$move_asset_right || !$insert_asset)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR'));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_SUCCESS', $table) . '</p><br />';
				return $base_asset;
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_DATABASE_ERROR', $table));
		}
	}

	/**
	 * Method to write the table asset
	 *
	 * @param   array  $asset
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _updateBaseAsset($asset = array())
	{
		try
		{
			if (empty($asset))
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_ERROR_EMPTY'));
			}
			$_db   = JFactory::getDbo();
			$query = $_db->getQuery(true);

			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rules') . " = " . $_db->quote($asset['rules']));
			$query->where($_db->quoteName('name') . ' = ' . $_db->quote($asset['name']));

			$_db->setQuery($query);
			$update_asset   = $_db->execute();

			if (!$update_asset)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_ERROR'));
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $asset['name']));
		}
	}

	/**
	 * Method to get the default asset of table or BwPostman
	 *
	 * @param   string  $table
	 *
	 * @return  array   $default_asset  default asset of table or BwPostman
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _getDefaultAsset($table = 'component')
	{
		try
		{
			$_db = JFactory::getDbo();
			$query = $_db->getQuery(true);

			$query->select('*');
			$query->from($_db->quoteName('#__assets'));

			switch ($table)
			{
				case '#__bwpostman_campaigns':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.campaign'));
					break;
				case '#__bwpostman_mailinglists':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.mailinglist'));
					break;
				case '#__bwpostman_newsletters':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.newsletter'));
					break;
				case '#__bwpostman_subscribers':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.subscriber'));
					break;
				case '#__bwpostman_templates':
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman.template'));
					break;
				case 'component':
				default:
						$query->where($_db->quoteName('name') . ' = ' . $_db->quote('com_bwpostman'));
					break;
			}

			$_db->setQuery($query);
			$default_asset = $_db->loadAssoc();
//echo dump ($default_asset, 'Default Asset Table ' . $table);
			$default_asset['parent_id'] = $default_asset['id'];
			$default_asset['id']        = 0;
			$default_asset['level']     = (int) $default_asset['level'] + 1;
//			$default_asset['rules']     = $default_asset['rules'];

			return $default_asset;

		}
		catch (RuntimeException $e)
		{
			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

/*
	"core.admin":               {"7":1},
	"core.archive":             {"7":1,"6":1},
	"core.create":              {"7":1,"6":1},
	"core.delete":              {"7":1,"6":1},
	"core.edit":                {"7":1,"6":1},
	"core.edit.own":            {"7":1,"6":1},
	"core.edit.state":          {"7":1,"6":1},
	"core.manage":              {"7":1,"6":1},
	"core.restore":             {"7":1,"6":1},
	"core.send":                {"7":1,"6":1},
	"bwpm.view.archive":        {"7":1,"6":1},
	"bwpm.view.campaigns":      {"7":1,"6":1},
	"bwpm.view.maintenance":    {"7":1,"6":1},
	"bwpm.view.manage":         {"7":1,"6":1},
	"bwpm.view.mailinglists":   {"7":1,"6":1},
	"bwpm.view.newsletters":    {"7":1,"6":1},
	"bwpm.view.subscribers":    {"7":1,"6":1},
	"bwpm.view.templates":      {"7":1,"6":1}
*/
	/**
	 * Method to write the assets collected by loop
	 *
	 * @param   array   $dataset            array of data sets to write
	 * @param   int     $s                  actual value of general control variable
	 * @param   array   $base_asset         base asset values
	 * @param   array   $asset_transform    transformation array of asset ids old vs. new
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _writeLoopAssets($dataset, $s, $base_asset, &$asset_transform)
	{
		try
		{
			$_db            = JFactory::getDbo();
			$asset_colnames = array_keys($_db->getTableColumns('#__assets'));


			$insert_data = implode(',', $dataset);
			$insert_data = substr($insert_data, 1, (strlen($insert_data) - 2));
			$query       = $_db->getQuery(true);

			$query->insert($_db->quoteName('#__assets'));
			$query->columns($asset_colnames);
			$query->values($insert_data);
			$_db->setQuery($query);
			if (!$_db->execute())
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR'));
			}
			// calculate inserted ids
			$last_id  = $_db->insertid();
			$num_rows = count($dataset);
			for ($i = 0; $i < $num_rows; $i++)
			{
				$asset_transform[$s - ($num_rows - $i)]['new'] = $last_id + $i;
			}

			// set rgt values from all assets since rgt of table asset
			$query = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " + " . ($num_rows * 2) . ") ");
			$query->where($_db->quoteName('rgt') . ' >= ' . $base_asset['rgt']);
			$query->where($_db->quoteName('name') . ' NOT LIKE ' . $_db->quote('%' . $base_asset['name'] . '.%'));

			$_db->setQuery($query);
			$set_asset_right = $_db->execute();

			// now set lft values from all assets above lft of BwPostman
			$query = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('lft') . " = (" . $_db->quoteName('lft') . " + " . ($num_rows * 2) . ")");
			$query->where($_db->quoteName('lft') . ' > ' . $base_asset['rgt']);
			$query->where($_db->quoteName('name') . ' NOT LIKE ' . $_db->quote('%' . $base_asset['name'] . '.%'));

			$_db->setQuery($query);
			$set_asset_left = $_db->execute();

			if (!$set_asset_left || !$set_asset_right)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR'));
			}

		}
		catch (RuntimeException $e)
		{
			throw new BwException($e->getMessage());
//			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to write the assets collected by loop
	 *
	 * @param   array   $dataset            array of data sets to write
	 * @param   string  $table              table name to write in
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _writeLoopDatasets($dataset, $table)
	{
		try
		{
			$_db    = JFactory::getDbo();

			// …get table column names
			$table_colnames     = array_keys($_db->getTableColumns($table));

			$insert_data = implode(',', $dataset);
			$insert_data = substr($insert_data, 1, (strlen($insert_data) - 2));

			$query  = 'REPLACE INTO ' . $_db->quoteName($table) . '(' . implode(',', $table_colnames) . ') VALUES (' . $insert_data . ')';

			$_db->setQuery($query);
			if (!$_db->execute())
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR'));
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException($e->getMessage());
//			throw new BwException (JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to get the maximum value for loop
	 *
	 * @param   string  $table      table name to get value
	 *
	 * @return  int     $data_loop_max
	 *
	 * @since    1.3.0
	 */
	protected function _getDataLoopMax($table)
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
		return  $data_loop_max;
	}

	/**
	 * Method to get the the raw table name
	 *
	 * @param   string  $table      table name to get value
	 *
	 * @return  string  $table_name_raw
	 *
	 * @throws BwException
	 *
	 * @since    1.3.0
	 */
	protected function _getRawTableName($table)
	{
		$start = strpos($table, '_', 3);

		if ($start === false)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_TABLE_NAME_ERROR'));
		}
		$table_name_raw = substr($table, $start + 1);
		return $table_name_raw;
	}

/**
	 * Method to see if user groups have changed, get new IDs or create new user groups if needed
	 *
	 * @param   array $usergroups user groups from backup file
	 *
	 * @return  mixed   array $group    array of old_id and new_id or false if no group id has changed
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	private function _getActualUserGroups($usergroups)
	{
		$_db    = JFactory::getDbo();
		$groups = array();

		// first compare current user groups with backed up ones
		foreach ($usergroups as $item)
		{
			$query  = $_db->getQuery(true);
			$result = array();

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__usergroups'));
			$query->where($_db->quoteName('title') . ' = ' . $_db->quote($item['title']));

			$_db->setQuery($query);
			try
			{
				$result = $_db->loadAssoc();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// user group not found
			if (!$result)
			{
				// insert new user group
				jimport('joomla.application.component.model');
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
				$userModel = JModelLegacy::getInstance('Group', 'UsersModel');

				$data['id']        = 0;
				$data['title']     = $item['title'];
				$data['parent_id'] = $item['parent_id'];
				$success           = $userModel->save($data);

				if (!$success)
				{
					throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ADD_USERGROUP_ERROR', $item['title']));
				}

				$query = $_db->getQuery(true);
				$query->select($_db->quoteName('id'));
				$query->from($_db->quoteName('#__usergroups'));
				$query->where($_db->quoteName('title') . ' = ' . $_db->quote($item['title']));

				$_db->setQuery($query);
				try
				{
					$result = $_db->loadResult();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				$groups[] = array('old_id' => $item['id'], 'new_id' => $result);
			}
			else
			{
				// user group has new ID
				if ($result['id'] !== $item['id'])
				{
					// memorize new id
					$groups[] = array('old_id' => $item['id'], 'new_id' => $result['id']);
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
	 * Method to rewrite user groups in the assets
	 *
	 * @param   string $table  component or table name of the assets are to rewrite
	 * @param   array  $assets array of the table assets
	 * @param   array  $groups array with old and new ID of changed user groups
	 *
	 * @return  void
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	private function _rewriteAssetUserGroups($table, &$assets, $groups)
	{
		$tables  = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.tables');
		$old_ids = array();
		foreach ($groups as $group)
		{
			$old_ids[] = $group['old_id'];
		}

		// check assets
		$i = 0;
		foreach ($assets as $asset)
		{
			if (key_exists('rules', $asset))
			{
				$rules = json_decode($asset['rules']);
				if ($rules !== null)
				{
					// rewrite user groups in rule
					foreach ($rules as $rule)
					{
						$rewrite = false;
						foreach ($rule as $key => $value)
						{
							$found = array_search($key, $old_ids);
							if ($found !== false)
							{
								$rewrite        = true;
								$key_old        = $groups[$found]['old_id'];
								$key_new        = $groups[$found]['new_id'];
								$rule->$key_new = $value;
								unset($rule->$key_old);
							}
						}
						if ($rewrite)
						{
							if ($table == 'component')
							{
								$assets[0] = json_encode($rules);
							}
							else
							{
								// update table assets
								$tables[$table]['table_assets'][$i]['rules'] = json_encode($rules);
							}
						}
					}
				}
				else
				{
					throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_REWRITE_USERGROUP_RULE_ERROR', $asset['rules'], $table));
				}
				$i++;
			}
		}
	}

	/**
	 * Method to create copies of affected tables
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function createRestorePoint()
	{
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->_getAffectedTables();

			foreach ($tables as $table)
			{
				// delete eventually remaining temporary tables
				$query = 'DROP TABLE IF EXISTS ' . $_db->quoteName($table . '_tmp');

				$_db->setQuery($query);
				$_db->execute();

				// copy affected tables to temporary tables, structure part
				$query = 'CREATE TABLE ' . $_db->quoteName($table . '_tmp') . ' LIKE ' . $_db->quoteName($table);

				$_db->setQuery($query);
				$_db->execute();

				// copy affected tables to temporary tables, data set part
				$query = 'INSERT INTO ' . $_db->quoteName($table . '_tmp') . ' SELECT * FROM ' . $_db->quoteName($table);

				$_db->setQuery($query);
				$_db->execute();
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_RESTORE_POINT_ERROR'));
		}
	}

	/**
	 * Method to restore copies of affected tables
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	public function restoreRestorePoint()
	{
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->_getAffectedTables();

			foreach ($tables as $table)
			{
				// delete newly created tables
				$query = ('DROP TABLE IF EXISTS ' . $_db->quoteName($table));

				$_db->setQuery($query);
				$_db->execute();

				// delete newly created tables
				$query = ('RENAME TABLE ' . $_db->quoteName($table . '_tmp') . ' TO ' . $_db->quoteName($table));

				$_db->setQuery($query);
				$_db->execute();
			}
			JFactory::getApplication()->setUserState('com_bwpostman.maintenance.restorePoint_text', '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_POINT_RESTORED_WARNING') . '</p>');
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESTORE_RESTORE_POINT_ERROR'));
		}
	}

	/**
	 * Method to delete copies of affected tables
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _deleteRestorePoint()
	{
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->_getAffectedTables();

			foreach ($tables as $table)
			{
				$query = ('DROP TABLE IF EXISTS ' . $_db->quoteName($table . '_tmp'));

				$_db->setQuery($query);
				$_db->execute();
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DELETE_RESTORE_POINT_ERROR'));
		}
	}

	/**
	 * Method to get the affected tables for restore point, but without temporary tables
	 *
	 * @return  array   $tableNames     array of affected tables
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function _getAffectedTables()
	{
		// get db prefix
		$prefix = JFactory::getDbo()->getPrefix();

		// get all names of installed BwPostman tables
		$tableNames = $this->getTableNamesFromDB();

		if(!is_array($tableNames)) {
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_AFFECTED_TABLES_ERROR'));
		}

		$tables = array();
		foreach ($tableNames as $table)
		{
			if(!strpos($table, '_tmp')) {
				$tables[]   = $table;
			}
		}
		$tables[]   = $prefix . 'usergroups';
		$tables[]   = $prefix . 'assets';

		return $tables;
	}
}
