<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman maintenance model for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

// Import MODEL and Helper object class
jimport('joomla.application.component.model');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

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
	protected $assetTargetTables  = array('campaigns', 'mailinglists', 'newsletters', 'subscribers', 'templates');

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
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function saveTables($update = false)
	{
		// @ToDo: Use simpleXml correctly
		// Access check.
		$permissions = JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$permissions['maintenance']['save'])
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
		$handle    = fopen($file_name, 'wb');

		try
		{
			if (!JFolder::exists($path))
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_FOUND', $path)
						. '</p>';

					return false;
				}

				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_FOUND', $path));
			}

			if ($handle === false)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_WRITABLE', $path)
						. '</p>';

					return false;
				}

				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_FOLDER_NOT_WRITABLE', $path));
			}

			// get all names of installed BwPostman tables
			$this->getTableNamesFromDB();

			if ($this->tableNames === null)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_GET_TABLE_NAMES', $path)
						. '</p>';

					return false;
				}

				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_GET_TABLE_NAMES'));
			}

			// write file header
			$file_data   = array();
			$file_data[] = $this->buildXmlHeader();

			if (fwrite($handle, implode("\n", $file_data)) === false)
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITING_HEADER', $path)
						. '</p>';

					return false;
				}

				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITING_HEADER'));
			}

			foreach ($this->tableNames as $table)
			{
				// do not save the table "bwpostman_templates_tpl"
				if (strpos($table['tableNameRaw'], 'templates_tpl') === false)
				{
					$file_data = array();
					$tableName = $table['tableNameGeneric'];

					$file_data[] = "\t\t<tables>";                                // set XML tables section
					$file_data[] = $this->buildXmlStructure($tableName);            // get table description
					if (fwrite($handle, implode("\n", $file_data)) === false)
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_error">'
								. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name)
								. '</p>';

							return false;
						}

						throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name));
					}
					else
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_ok">'
								. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_TABLE_SUCCESS', $tableName)
								. '</p>';
						}
					}

					$file_data = array();

					$res = $this->buildXmlData($tableName, $handle);        // write table data
					if (!$res)
					{
						if ($update)
						{
							echo '<p class="bw_tablecheck_error">'
								. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name)
								. '</p>';

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
							echo '<p class="bw_tablecheck_error">'
								. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_ASSETS_WRITE_FILE_ERROR', $file_name)
								. '</p>';

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
					echo '<p class="bw_tablecheck_ok">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_SUCCESS', $file_name)
						. '</p>';
				}
			}
			else
			{
				if ($update)
				{
					echo '<p class="bw_tablecheck_error">'
						. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_ERROR_WRITE_FILE_NAME', $file_name)
						. '</p>';

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
		catch (Exception $e)
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
	 * Method to get a list of names of all installed tables of BwPostman form database in the form
	 * <prefix>tablename. Also sets a list as property of all BwPostman tables with different variations of name
	 *
	 * @return    array
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function getTableNamesFromDB()
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

		$tableArray = array();

		foreach ($tableNames as $tableName)
		{
			if (strpos($tableName, '_tmp') === false)
			{
				$table['tableNameDb'] 		= $tableName;
				$table['tableNameGeneric']	= self::getGenericTableName($tableName);
				$table['tableNameRaw']		= $this->getRawTableName($table['tableNameGeneric']);
				$table['tableNameUC']		= ucfirst(substr($table['tableNameRaw'], 0, -1));

				$tableArray[] = $table;
			}
		}

		$this->tableNames = $tableArray;

		return $tableArray;
	}

	/**
	 * Builds the XML data header for the tables to export. Based on Joomla JDatabaseExporter
	 *
	 * @return    string    An XML string
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	protected function buildXmlHeader()
	{
		// @ToDo: Use simpleXml correctly
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

		// Get assets of sections
		foreach ($this->tableNames as $table)
		{
			$hasAsset 	= $this->checkForAsset($table['tableNameGeneric']);

			if ($hasAsset)
			{
				$assetData = $this->getTableAssetData($table['tableNameRaw'], '');

				$data[] = $assetData[0];
			}
		}

		// write component asset
		$buffer[] = "\t\t\t" . '<component_assets>';

		foreach ($data as $datum)
		{
			$buffer[] = "\t\t\t\t" . '<dataset>';
			if (is_array($datum))
			{
				foreach ($datum as $key => $value)
				{
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					$buffer[]      = "\t\t\t\t\t<" . $key . ">" . $insert_string . "</" . $key . ">";
				}
			}

			$buffer[] = "\t\t\t\t" . '</dataset>';
		}

		$buffer[] = "\t\t\t</component_assets>";

		// process user groups
		$groups = $this->getUsergroupsUsedInAssets();

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
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	private function getUsergroupsUsedInAssets()
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
				echo '<p class="bw_tablecheck_error">'
					. JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OPEN_INSTALL_FILE_ERROR', $filename)
					. '</p>';
				return false;
			}
			else
			{
				// empty arrays
				$file_content = array();
				$txt_array    = array();

				// get file content
				while(!feof($fh))
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

				$queries    = array();
				$string     = '';
				$i          = 0;
				foreach ($txt_array as $key => $value)
				{
					$pos = strpos($value, 'CREATE');
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
	 * @param    string    $table   The name of the table.
	 *
	 * @return   string             The name of the table with the database prefix replaced with #__.
	 *
	 * @since    1.0.1
	 */

	public static function getGenericTableName($table)
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
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function checkTableNames($neededTables, $genericTableNames, $mode = 'check')
	{
		// @ToDo: Check if exceptions are handled correctly
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
				echo '<p class="bw_tablecheck_warn">' .
					JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED', implode(',', $diff_1)) .
					'</p>';

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
						echo '<p class="bw_tablecheck_error">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_ERROR', $missingTable) .
							'</p>';
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_SUCCESS', $missingTable) .
							'</p>';
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
					echo '<p class="bw_tablecheck_warn">' .
						JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE', implode(',', $diff_2)) .
						'</p>';

					// delete obsolete tables
					foreach ($diff_2 as $obsoleteTable)
					{
						$query = "DROP TABLE IF EXISTS " . $obsoleteTable;

						$_db->setQuery($query);
						$deleteDB = $_db->execute();
						if (!$deleteDB)
						{
							echo '<p class="bw_tablecheck_error">' .
								JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_ERROR', $obsoleteTable) .
								'</p>';
						}
						else
						{
							echo '<p class="bw_tablecheck_ok">' .
								JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_SUCCESS', $obsoleteTable) .
								'</p>';
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
				if($start !== false)
				{
					$collation = substr($create_statement[$table->name], $start + 8);
				}

				if((strcasecmp($engine, $table->engine) != 0)
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

					$query = 'ALTER TABLE ' . $_db->quoteName($table->name) . $engine_text . $c_set_text . $collation_text;
					$_db->setQuery($query);
					$modifyTable = $_db->execute();
					if (!$modifyTable)
					{
						echo '<p class="bw_tablecheck_error">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_ERROR', $table->name) .
							'</p>';

						return false;
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_SUCCESS', $table->name) .
							'</p>';
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
					echo '<p class="bw_tablecheck_warn">' .
						JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_WRONG', $table->name) .
						'</p>';

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
						echo '<p class="bw_tablecheck_error">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_ERROR', $table->name) .
							'</p>';

						return false;
					}
					else
					{
						echo '<p class="bw_tablecheck_ok">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_SUCCESS', $table->name) .
							'</p>';
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
						echo '<p class="bw_tablecheck_warn">' .
							JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_WRONG', $table->name) .
							'</p>';

						$query = 'ALTER TABLE ' . $_db->quoteName($table->name);
						$query .= ' MODIFY ' . $_db->quoteName($table->primary_key);
						$query .= ' INT AUTO_INCREMENT';
						$_db->setQuery($query);
						$incrementKey = $_db->execute();
						if (!$incrementKey)
						{
							echo '<p class="bw_tablecheck_error">' .
								JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_ERROR', $table->name) .
								'</p>';

							return false;
						}
						else
						{
							echo '<p class="bw_tablecheck_ok">' .
								JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_INCREMENT_INSTALL_SUCCESS', $table->name) .
								'</p>';
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
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function checkTableColumns($checkTable)
	{
		// @ToDo: Check if exceptions are handled correctly
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

					echo '<p class="bw_tablecheck_warn">' .
						JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COLS', $neededColumns[$i]['Column'], $checkTable->name) .
						'</p>';
					$query = "ALTER TABLE " . $_db->quoteName($checkTable->name);
					$query .= " ADD " . $_db->quoteName($neededColumns[$i]['Column']);
					$query .= ' ' . $neededColumns[$i]['Type'] . $null . $default;
					$query .= " AFTER " . $_db->quoteName($neededColumns[$i - 1]['Column']);

					$_db->setQuery($query);
					$insertCol = $_db->execute();

					if (!$insertCol)
					{
						echo '<p class="bw_tablecheck_error">' .
							JText::sprintf(
								'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_ERROR',
								$neededColumns[$i]['Column'],
								$checkTable->name
							) .
							'</p>';

						return 0;
					}
					else
					{
						echo str_pad(
							'<p class="bw_tablecheck_ok">' .
							JText::sprintf(
								'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_SUCCESS',
								$neededColumns[$i]['Column'],
								$checkTable->name
							) .
							'</p>',
							4096
						);

						return 2; // reset iteration
					}
				}

				// check for obsolete col names
				if(array_search($installedColumns[$i]['Field'], $search_cols_2) === false)
				{
					echo '<p class="bw_tablecheck_warn">' .
						JText::sprintf(
							'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COLS',
							$installedColumns[$i]['Field'],
							$checkTable->name
						) .
						'</p>';
					$query = "ALTER TABLE " . $_db->quoteName($checkTable->name) . " DROP " . $_db->quoteName($installedColumns[$i]['Field']);

					$_db->setQuery($query);
					$deleteCol = $_db->execute();

					if (!$deleteCol)
					{
						echo '<p class="bw_tablecheck_error">' .
							JText::sprintf(
								'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_ERROR',
								$installedColumns[$i]['Field'],
								$checkTable->name
							) .
							'</p>';

						return 0;
					}
					else
					{
						echo str_pad(
							'<p class="bw_tablecheck_ok">' .
							JText::sprintf(
								'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_SUCCESS',
								$installedColumns[$i]['Field'],
								$checkTable->name
							) .
							'</p>',
							4096
						);

						return 2; // reset iteration
					}
				}
			}

			echo str_pad(
				'<p class="bw_tablecheck_ok">' .
				JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_OK', $checkTable->name) .
				'</p>',
				4096
			);

			for ($i = 0; $i < count($neededColumns); $i++)
			{
				$diff = array_udiff($neededColumns[$i], $installedColumns[$i], 'strcasecmp');
				if (!empty($diff))
				{
					echo '<p class="bw_tablecheck_warn">' .
						JText::sprintf(
							'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES',
							implode(',', array_keys($diff)),
							$neededColumns[$i]['Column'],
							$checkTable->name
						) .
						'</p>';
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
							echo '<p class="bw_tablecheck_error">' .
								JText::sprintf(
									'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_ERROR',
									$missingCol,
									$neededColumns[$i]['Column'],
									$checkTable->name
								) .
								'</p>';
						}
						else
						{
							echo str_pad(
								'<p class="bw_tablecheck_ok">' .
								JText::sprintf(
									'COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_SUCCESS',
									$missingCol,
									$neededColumns[$i]['Column'],
									$checkTable->name
								) .
								'</p>',
								4096
							);
						}
					}
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		echo str_pad(
			'<p class="bw_tablecheck_ok">' .
			JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_ATTRIBUTES_OK', $checkTable->name) .
			'</p>',
			4096
		);

		return 1;
	}

	/**
	 * Method to check, if column asset_id has a real value. If not, there is no possibility to delete data sets in BwPostman.
	 * Therefore each dataset without real value for asset_id has to be stored one time, to get this value
	 *
	 * @return    bool
	 *
	 * @since    1.0.1
	 *
	 * @throws Exception
	 * @throws BwException
	 */
	public function checkAssetId()
	{
		// Set tables that has column asset_id
		// @ToDo: Check if exceptions are handled correctly
		$this->getTableNamesFromDB();

		foreach ($this->tableNames as $table)
		{
			// Shortcut
			$tableNameGeneric	= $table['tableNameGeneric'];
			$hasAsset 			= $this->checkForAsset($tableNameGeneric);

			if ($hasAsset)
			{
				// Get items without real asset id (=0)
				$itemsWithoutAsset = $this->getItemsWithoutAssetId($tableNameGeneric);

				if (is_array($itemsWithoutAsset))
				{
					$mapOldAssetIdsToNew = $this->insertAssets($itemsWithoutAsset, $table);

					$this->insertItems($itemsWithoutAsset, $table['tableNameGeneric'], $mapOldAssetIdsToNew);
				}

				echo '<p class="bw_tablecheck_ok">' .
					JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSET_OK', $tableNameGeneric) .
					'</p>';
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
	 * @since    1.0.1
	 *
	 * @throws Exception
	 * @throws BwException
	 */
	public function checkAssetParentId()
	{
		// Set tables that has column asset_id
		// @ToDo: Check if exceptions are handled correctly
		$this->getTableNamesFromDB();

		// Get component asset id
		$componentAsset = $this->getBaseAsset('component', true);

		foreach ($this->tableNames as $table)
		{
			// Shortcut
			$tableNameGeneric = $table['tableNameGeneric'];
			$hasAsset         = $this->checkForAsset($tableNameGeneric);

			if ($hasAsset)
			{
				// Get section asset
				$sectionAsset = $this->getBaseAsset($table['tableNameRaw'], true);

				try
				{
					// Replace parent asset id of items with component as parent
					$db	= JFactory::getDbo();
					$query	= $db->getQuery(true);

					$query->update($db->quoteName('#__assets'));
					$query->set($db->quoteName('parent_id') . " = " . $db->Quote($sectionAsset['id']));
					$query->where($db->quoteName('name') . ' LIKE ' . $db->Quote($sectionAsset['name'] . '.%'));
					$query->where($db->quoteName('parent_id') . ' <> ' . $db->Quote($sectionAsset['id']));

					$db->setQuery($query);

					$db->execute();
				}
				catch (RuntimeException $e)
				{
					throw new BwException(
						JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $sectionAsset['name'])
					);
				}
			}
		}

		return true;
	}

	/**
	 * Method to check, if user_id of subscriber matches ID in joomla user table, updating if mail address exists.
	 * Only datasets with entered user_id in table subscribers will be checked
	 *
	 * @return    bool
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	public function checkUserIds()
	{
		// @ToDo: Check if exceptions are handled correctly
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
	 * @return    string    An couple of XML lines (strings).
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	private function buildXmlStructure($tableName)
	{
		// @ToDo: Check if exceptions are handled correctly
		// @ToDo: Use simpleXml correctly
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
	 * @throws BwException if writing file is not possible
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	private function buildXmlData($tableName, $handle)
	{
		// @ToDo: Check if exceptions are handled correctly
		// @ToDo: Use simpleXml correctly
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
						|| (($tableName == '#__bwpostman_templates')
						&& (($key == 'tpl_html')
						|| ($key == 'tpl_css')
						|| ($key == 'tpl_article')
						|| ($key == 'tpl_divider')))
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
	 * @return    string    XML lines
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	private function buildXmlAssets($tableName)
	{
		// @ToDo: Check if exceptions are handled correctly
		// @ToDo: Use simpleXml correctly
		$table_name_raw = $this->getRawTableName($tableName);

		// @ToDo: use checkForAsset($table)
		if (in_array($table_name_raw, $this->assetTargetTables))
		{
			$buffer     = array();

			$data = $this->getTableAssetData($table_name_raw);

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
		// @ToDo: Use simpleXml correctly
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
		// @ToDo: Check if exceptions are handled correctly
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
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function processAssetUserGroups($table_names)
	{
		// @ToDo: Check if exceptions are handled correctly
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
				$new_groups = $this->getCurrentUserGroups($usergroups);

				if (is_array($new_groups))
				{
					// rewrite component asset user groups
					$this->rewriteAssetUserGroups('component', $com_assets, $new_groups);
					$com_assets = JFactory::getApplication()->setUserState('com_bwpostman.maintenance.com_assets', $com_assets);

					// rewrite table asset user groups
					foreach ($table_names as $table)
					{
						// table with assets?
						if (key_exists('table_assets', $tables[$table]))
						{
							// get table assets
							$assets = $tables[$table]['table_assets'];
							$this->rewriteAssetUserGroups($table, $assets, $new_groups);
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
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_PROCESS_USERGROUPS_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to the delete existing tables,create them anew, update component asset (rules) and initialize table assets
	 *
	 * @param   array $tables array of generic table names read from backup file
	 *
	 * @return    void
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    2.0.0
	 */
	public function anewBwPostmanTables($tables)
	{
		// @ToDo: Check if exceptions are handled correctly
		// @ToDo: Check for process of plugin tables
		$tmp_file	= JFactory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
		$fp			= fopen($tmp_file, 'r');
		$tablesQueries	= unserialize(fread($fp, filesize($tmp_file)));

		// delete tables and create it anew
		foreach ($tables as $table)
		{
			$this->deleteBwPostmanTable($table);
			$this->createBwPostmanTableAnew($table, $tablesQueries);
		}

		// Update component asset and initialize section assets
		$this->createBaseAssets(true);
	}

	/**
	 * Method to the rewrite tables
	 *
	 * @param   string $table     generic name of table to rewrite
	 *
	 * @return    void
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function reWriteTables($table)
	{
		// @ToDo: Check if exceptions are handled correctly
		$tmp_file	= JFactory::getApplication()->getUserState('com_bwpostman.maintenance.tmp_file', null);
		$fp			= fopen($tmp_file, 'r');
		try
		{
			$tables             = unserialize(fread($fp, filesize($tmp_file)));
			$asset_loop         = 0;
			$curr_asset_id      = 0;
			$asset_transform    = array();
			$base_asset         = $this->getBaseAsset($this->getRawTableName($table));

			$this->assetColnames = array_keys(JFactory::getDbo()->getTableColumns('#__assets'));

			$asset_name     = $base_asset['name'];

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
						$this->updateBaseAsset($update_asset);
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
							$dataset[] = $this->prepareAssetValues($asset, $asset_transform, $s, $base_asset, $curr_asset_id);

							$s++;

							// if asset loop max is reached or last data set, insert into table
							if (($asset_loop == $asset_loop_max) || ($s == $asset_max))
							{
								// write collected assets to table
								$this->writeLoopAssets($dataset, $s, $base_asset, $asset_transform);

								//reset loop values
								$asset_loop = 0;
								$dataset    = array();
							}
						} // end foreach table assets
					} // end switch base asset
				} // end table assets exists
			} // end asset inserting

			/*
			Import data (can't use table bind/store, because we have IDs and Joomla sets mode to update, if ID is set,
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
						$this->writeLoopDatasets($dataset, $table);

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

			/*
			 * // For transaction test purposes only
			if($table_name_raw == 'newsletters') {
				throw new BwException(JText::_('Test-Exception Newsletter written'));
			}
			*/

			if ($table == '#__bwpostman_templates')
			{
				fclose($fp);
				unlink($tmp_file);
				$this->deleteRestorePoint();
			}
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
	 * @throws Exception
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
	 *
	 * @throws Exception
	 */
	public static function adjustMLAccess()
	{
		// @ToDo: Check if exceptions are handled correctly
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
	 * @return  array   $table_names      array of generic table names
	 *
	 * @throws  BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function parseTablesData($file)
	{
		// @ToDo: Check if exceptions are handled correctly
		$log_options = array('test' => 'testtext');
		$logger      = new BwLogger($log_options);

		if (BWPOSTMAN_LOG_MEM)
		{
			$logger->addEntry(new JLogEntry(sprintf('Memory consumption before parsing: %01.3f MB', (memory_get_usage(true) / (1024.0 * 1024.0)))));
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
			$logger->addEntry(
				new JLogEntry(
					sprintf(
						'Memory consumption while parsing with XML file: %01.3f MB',
						(memory_get_usage(true) / (1024.0 * 1024.0))
					)
				)
			);
		}

		// Get general data
		$generals   = array();
		if (property_exists($xml->database->Generals, 'BwPostmanVersion'))
		{
			$generals['BwPostmanVersion'] = (string) $xml->database->Generals->BwPostmanVersion;
		}

		if (property_exists($xml->database->Generals, 'SaveDate'))
		{
			$generals['SaveDate'] = (string) $xml->database->Generals->SaveDate;
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
			$logger->addEntry(
				new JLogEntry(
					sprintf(
						'Memory consumption while parsing before loop: %01.3f MB',
						(memory_get_usage(true) / (1024.0 * 1024.0))
					)
				)
			);
		}

		// paraphrase tables array per table for better handling and convert simple xml objects to strings
		$i = 0;
		while (null !== $tmp_table = array_shift($x_tables))
		{
			if (BWPOSTMAN_LOG_MEM)
			{
				$logger->addEntry(
					new JLogEntry(
						sprintf(
							'Memory consumption while parsing at very beginning loop: %01.3f MB',
							(memory_get_usage(true) / (1024.0 * 1024.0))
						)
					)
				);
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
				$logger->addEntry(
					new JLogEntry(
						sprintf(
							'Memory consumption while parsing at loop with query: %01.3f MB',
							(memory_get_usage(true) / (1024.0 * 1024.0))
						)
					)
				);
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
				$logger->addEntry(
					new JLogEntry(
						sprintf(
							'Memory consumption while parsing at loop with assets: %01.3f MB',
							(memory_get_usage(true) / (1024.0 * 1024.0))
						)
					)
				);
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
				$logger->addEntry(
					new JLogEntry(
						sprintf(
							'Memory consumption while parsing at loop with data sets: %01.3f MB',
							(memory_get_usage(true) / (1024.0 * 1024.0))
						)
					)
				);
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
				$logger->addEntry(
					new JLogEntry(
						sprintf(
							'Memory consumption while parsing of table %s: %01.3f MB',
							$table_names[$i - 1],
							(memory_get_peak_usage(true) / (1024.0 * 1024.0))
						)
					)
				);
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
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			$_db = JFactory::getDbo();

			$query = $_db->getQuery(true);
			$query->delete($_db->quoteName('#__assets'));
			$query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote('%com_bwpostman.%'));

			$_db->setQuery($query);
			$asset_delete = $_db->execute();

			// Uncomment next line to test rollback (only makes sense, if deleted tables contained data)
			// throw new BwException(JText::_('Test-Exception DeleteAssets Model'));

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
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_DELETE_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to heal assets table
	 *
	 * repairs lft and rgt values in asset table, updates component asset
	 * closes gap caused by deleting sub assets of BwPostman
	 *
	 * @throws  BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function healAssetsTable()
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			// com_assets are from state = from input file!
			$com_assets = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());
			$_db        = JFactory::getDbo();
			$query      = $_db->getQuery(true);

			// first get lft from main asset com_bwpostman, This is the one already existing in table
			$base_asset = $this->getBaseAsset('component', true);

			// Calculate complete gap caused by BwPostman. Subtract 1 to provide space for right value of BwPostman
			$gap        = $base_asset['rgt'] - $base_asset['lft'] - 1;

			// second shift down rgt values by gap for all assets above lft of BwPostman
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " - " . $gap . ") ");
			$query->where($_db->quoteName('lft') . ' >= ' . $base_asset['lft']);

			$_db->setQuery($query);
			$set_asset_right = $_db->execute();

			// now shift down lft values by gap for all assets above lft of BwPostman
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
				$query->set($_db->quoteName('rules') . " = " . $_db->quote($com_assets[0]['rules']));
			}

			$query->where($_db->quoteName('lft') . ' = ' . $base_asset['lft']);

			$_db->setQuery($query);
			$set_asset_base = $_db->execute();

			// Uncomment next line to test rollback (only makes sense, if deleted tables contained data)
			// throw new BwException(JText::_('Test-Exception HealAssets Model'));

			if (!$set_asset_left || !$set_asset_right || !$set_asset_base)
			{
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_ERROR'));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_SUCCESS') . '</p><br />';
				$base_asset['rgt'] = $base_asset['lft'] + 1;
			}
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to get the base asset of BwPostman. If state exists, catch values from state, else use asset table
	 *
	 * @param   string  $table
	 * @param   boolean $onlyHeal
	 *
	 * @return array    $base_asset     base asset of BwPostman
	 *
	 * @throws  BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	protected function getBaseAsset($table = 'component', $onlyHeal = false)
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			$stateAssetsRaw = '';

			if (!$onlyHeal && $table != 'component')
			{
				$stateAssetsRaw = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', '');
			}

			if (is_array($stateAssetsRaw) && count($stateAssetsRaw) > 0)
			{
				$base_asset = $this->extractBaseAssetFromState(array('tableNameUC' => $table), $stateAssetsRaw);
			}
			else
			{
				$base_asset = $this->getBaseAssetFromTable($table);
			}

			return $base_asset;
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_ASSET_REPAIR_DATABASE_ERROR'));
		}
	}

	/**
	 * Method to write a new asset at the table asset. Shifts left and right value at existing assets and inserts the new asset.
	 *
	 * @param   array    $table
	 * @param   boolean  $showMessage
	 *
	 * @return mixed    $base_asset     base asset of BwPostman
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function insertBaseAsset($table, $showMessage = true)
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			// Get asset rules
			$asset      = $this->getBaseAssetRules($table);
			$com_asset	= $this->getBaseAsset('component');

			// Provide space for new asset and insert it
			$move_asset_right = $this->shiftRightAssets($com_asset);
			$move_asset_left  = $this->shiftLeftAssets($com_asset);

			if ($table == 'component')
			{
				$rules = $com_asset['rules'];
				$writeAsset = $this->updateComponentRules($rules);
			}
			else
			{
				$writeAsset = $this->insertAssetToTable($com_asset, $asset);
			}

			// Get Base Asset
			$base_asset = $this->getAssetFromTableByName($asset['name']);

			if (!$move_asset_left || !$move_asset_right || !$writeAsset)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_ERROR'));
			}
			else
			{
				if ($showMessage)
				{
					$writeTableName = $table;
					if (is_array($table))
					{
						$writeTableName = $table['tableNameUC'];
					}

					echo '<p class="bw_tablecheck_ok">' .
						JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_SUCCESS', $writeTableName) .
						'</p><br />';
				}

				return $base_asset;
			}
		}
		catch (RuntimeException $e)
		{
			$tableName = $table;
			if (is_array($table))
			{
				$tableName = $table['tableNameUC'];
			}

			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_INSERT_TABLE_ASSET_DATABASE_ERROR', $tableName));
		}
	}

	/**
	 * Method to write a new asset at the table asset. Shifts left and right value at existing assets and inserts the new asset.
	 *
	 * @param   string $sectionName
	 * @param   array  $sectionRules
	 *
	 * @return mixed    $base_asset     base asset of BwPostman
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function updateSectionAsset($sectionName, $sectionRules)
	{
		// @ToDo: Check if exceptions are handled correctly

		$assetName = 'com_bwpostman.' . $sectionName;

		try
		{
			$rules = new JAccessRules($sectionRules);

			$db	= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->update($db->quoteName('#__assets'));
			$query->set($db->quoteName('rules') . " = " . $db->Quote($rules));
			$query->where($db->quoteName('name') . ' = ' . $db->Quote($assetName));

			$db->setQuery($query);
			$result = $db->execute();

			return $result;
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $sectionName));
		}
	}

	/**
	 * Method to update an existing asset at the table asset
	 *
	 * @param   array  $asset
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function updateBaseAsset($asset = array())
	{
		// @ToDo: Check if exceptions are handled correctly
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
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_UPDATE_TABLE_ASSET_DATABASE_ERROR', $asset['name']));
		}
	}

	/**
	 * Method to get the current default asset of table or BwPostman, based on section asset (parent)
	 *
	 * @param   array  $sectionAsset
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
	 * @param   array   $dataset            array of data sets to write
	 * @param   int     $assetLoopCounter                  actual value of general control variable
	 * @param   array   $base_asset         base asset values
	 * @param   array   $mapOldAssetIdsToNew    transformation array of asset ids old vs. new
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function writeLoopAssets($dataset, $assetLoopCounter, $base_asset, &$mapOldAssetIdsToNew)
	{
		// @ToDo: Check if exceptions are handled correctly
		$log_options = array('test' => 'testtext');
		$logger      = new BwLogger($log_options);

		try
		{
			$_db            = JFactory::getDbo();

			// Prepare insert data (convert to string, remove last bracket)
			$insert_data = implode(',', $dataset);
			$insert_data = substr($insert_data, 1, (strlen($insert_data) - 2));

			$query = $_db->getQuery(true);
			$query->insert($_db->quoteName('#__assets'));
			$query->columns($this->assetColnames);
			$query->values($insert_data);
			$_db->setQuery($query);
			$logger->addEntry(new JLogEntry('Write Loop Assets Query 1: ' . (string) $query));
			if (!$_db->execute())
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR'));
			}

			// calculate number of inserted ids
			$last_id  = $_db->insertid();
			$num_rows = count($dataset);
			for ($i = 0; $i < $num_rows; $i++)
			{
				$mapOldAssetIdsToNew[$assetLoopCounter - ($num_rows - $i)]['newAssetId'] = $last_id + $i;
			}

			// shift rgt values from all assets since rgt of table asset
			$query = $_db->getQuery(true);
			$query->update($_db->quoteName('#__assets'));
			$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " + " . ($num_rows * 2) . ") ");
			$query->where($_db->quoteName('rgt') . ' >= ' . $base_asset['rgt']);
			$query->where($_db->quoteName('name') . ' NOT LIKE ' . $_db->quote('%' . $base_asset['name'] . '.%'));

			$_db->setQuery($query);
			$set_asset_right = $_db->execute();

			// now shift lft values from all assets above lft of BwPostman
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
		}
	}

	/**
	 * Method to write the collected datasets by loop
	 *
	 * @param   array   $dataset            array of data sets to write
	 * @param   string  $table              table name to write in
	 *
	 * @throws  BwException
	 *
	 * @since    1.3.0
	 */
	protected function writeLoopDatasets($dataset, $table)
	{
		// @ToDo: Check if exceptions are handled correctly
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
		}
	}

	/**
	 * Method to get the maximum value for item loop, depending on processed table
	 *
	 * @param   string  $table      table name to get value
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

		return  $data_loop_max;
	}

	/**
	 * Method to see if user groups have changed, get new IDs or create new user groups if needed
	 *
	 * @param   array $usergroups user groups from backup file
	 *
	 * @return  mixed   array $group    array of old_id and new_id or false if no group id has changed
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	private function getCurrentUserGroups($usergroups)
	{
		// @ToDo: Check if exceptions are handled correctly
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
	 * Method to rewrite user groups in the assets. Needed, if backup file processed contains other usergroups than currently installed ones.
	 *
	 * @param   string $table  component or table name of the assets are to rewrite
	 * @param   array  $assets array of the table assets
	 * @param   array  $groups array with old and new ID of changed user groups
	 *
	 * @return  void
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	private function rewriteAssetUserGroups($table, &$assets, $groups)
	{
		// @ToDo: Check if exceptions are handled correctly
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
								$assets[$i]['rules'] = json_encode($rules);
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
	 * Method to create tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function createRestorePoint()
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->getAffectedTables();

			foreach ($tables as $table)
			{
				$tableNameGeneric = $table['tableNameGeneric'];

				// delete eventually remaining temporary tables
				$query = 'DROP TABLE IF EXISTS ' . $_db->quoteName($tableNameGeneric . '_tmp');

				$_db->setQuery($query);
				$_db->execute();

				// copy affected tables to temporary tables, structure part
				$query = 'CREATE TABLE ' . $_db->quoteName($tableNameGeneric . '_tmp') . ' LIKE ' . $_db->quoteName($tableNameGeneric);

				$_db->setQuery($query);
				$_db->execute();

				// copy affected tables to temporary tables, data set part
				$query = 'INSERT INTO ' . $_db->quoteName($tableNameGeneric . '_tmp') . ' SELECT * FROM ' . $_db->quoteName($tableNameGeneric);

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
	 * Method to restore tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	public function restoreRestorePoint()
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->getAffectedTables();

			foreach ($tables as $table)
			{
				// delete newly created tables
				$query = ('DROP TABLE IF EXISTS ' . $_db->quoteName($table['tableNameGeneric']));

				$_db->setQuery($query);
				$_db->execute();

				// delete newly created tables
				$query = (
					'RENAME TABLE ' . $_db->quoteName($table["tableNameGeneric"] . '_tmp') . ' TO ' . $_db->quoteName($table["tableNameGeneric"])
				);

				$_db->setQuery($query);
				$_db->execute();
			}

			JFactory::getApplication()->setUserState(
				'com_bwpostman.maintenance.restorePoint_text',
				'<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_POINT_RESTORED_WARNING') . '</p>'
			);
		}
		catch (RuntimeException $e)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_RESTORE_RESTORE_POINT_ERROR'));
		}
	}

	/**
	 * Method to delete tmp copies of affected tables. This is a very fast method to use as restore point. If error occurred,
	 * only delete current tables and rename tmp names to the original ones. If all went well, delete tmp tables.
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	protected function deleteRestorePoint()
	{
		// @ToDo: Check if exceptions are handled correctly
		try
		{
			$_db    = JFactory::getDbo();
			$tables = $this->getAffectedTables();

			foreach ($tables as $table)
			{
				$query = ('DROP TABLE IF EXISTS ' . $_db->quoteName($table['tableNameGeneric'] . '_tmp'));

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
	 * Method to get the affected tables for restore point, but without temporary tables. Affected tables are not only all
	 * tables with bwpostman in their name, but also assets and usergroups
	 *
	 * @return  array   $tableNames     array of affected tables
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since    1.3.0
	 */
	protected function getAffectedTables()
	{
		// @ToDo: Check if exceptions are handled correctly
		// get db prefix
		$prefix = JFactory::getDbo()->getPrefix();

		// get all names of installed BwPostman tables
		$this->getTableNamesFromDB();

		if(!is_array($this->tableNames)) {
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_AFFECTED_TABLES_ERROR'));
		}

		$tables = array();
		foreach ($this->tableNames as $table)
		{
			if(!strpos($table['tableNameGeneric'], '_tmp')) {
				$tables[]   = $table;
			}
		}

		$tables[]['tableNameGeneric']   = $prefix . 'usergroups';
		$tables[]['tableNameGeneric']   = $prefix . 'assets';

		return $tables;
	}

	/**
	 * Method to get the asset of a specific table, called base asset
	 *
	 * @param string   $table_name_raw  for which table we want to get the base asset
	 * @param string   $dot             if dot is present, we search for a specific table, else component is meant
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function getTableAssetData($table_name_raw, $dot = '.')
	{
		// @ToDo: Check if exceptions are handled correctly
		$endString	= $dot;
		$data		= array();

		if ($dot == '.')
		{
			$endString .= '%';
		}

		// raw table name are plural, assets are singular
		$asset_name = '%com_bwpostman.' . substr($table_name_raw, 0, strlen($table_name_raw) - 1) . $endString;

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

		return $data;
	}

	/**
	 * Method to get the table name with bwpostman prefix
	 *
	 * @param   string  $table      table name to get value
	 *
	 * @return  string  $bwpmTableName
	 *
	 * @throws BwException
	 *
	 * @since    1.3.0
	 */
	protected function getBwpmTableName($table)
	{
		$start = strpos($table, '_', 3);

		if ($start === false)
		{
			throw new BwException(JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_GET_TABLE_NAME_ERROR'));
		}

		$bwpmTableName = substr($table, $start + 1);
		return $bwpmTableName;
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
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getBwPostmanUsergroups($table)
	{
		// @ToDo: Check if exceptions are handled correctly
		$_db = JFactory::getDbo();

		$searchValues = array("'BwPostmanAdmin'", "'BwPostmanManager'", "'BwPostmanPublisher'", "'BwPostmanEditor'");

		if($table != 'component')
		{
			$suffixes     = array("Admin", "Publisher", "Editor");

			foreach ($suffixes as $suffix)
			{
				$value  = 'BwPostman';
				$value .= $table;
				$value .= $suffix;
				$value  = $_db->quote($value);

				$searchValues[] = $value;
			}
		}

		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('title'));
		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__usergroups'));
		$query->where($_db->quoteName('title') . ' IN (' . implode(',', $searchValues) . ')');

		$_db->setQuery($query);

		$bwpmUserGroups = $_db->loadAssocList('title');

		return $bwpmUserGroups;
	}

	/**
	 * Method
	 *
	 * @param array $table
	 *
	 * @return array
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function getBaseAssetRules($table)
	{
		// @ToDo: Check if exceptions are handled correctly
		// @ToDo: Method name is misleading! Not only rules are returned, the whole asset is returned.
		$stateAssetsRaw = JFactory::getApplication()->getUserState('com_bwpostman.maintenance.com_assets', array());

		if (is_array($stateAssetsRaw) && count($stateAssetsRaw) > 0)
		{
			$asset = $this->extractBaseAssetFromState($table, $stateAssetsRaw);
			if ($asset !== false)
			{
				return $asset;
			}
		}

		$com_asset = $this->getBaseAsset('component');

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
	 * @param string $table as generic table name
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	public static function checkForAsset($table)
	{
		$hasAsset   = false;

		$_db = JFactory::getDbo();

		$columns = $_db->getTableColumns($table);

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
	 * @return array $mergedRules
	 *
	 * @since 2.0.0
	 */
	private function presetSectionRules($table)
	{
		$tableName		= substr($table['tableNameRaw'], 0, -1) . '.';
		$tableNameUC	= $table['tableNameUC'];
		$bwpmUserGroups	= $this->getBwPostmanUsergroups($tableNameUC);

		$sectionPublisher = 'BwPostman' . $tableNameUC . 'Publisher';
		$sectionEditor    = 'BwPostman' . $tableNameUC . 'Editor';

		// If there is no real table, component entries will be overridden. This makes it possible to assign rules in one run
		if (key_exists('BwPostmanAdmin', $bwpmUserGroups))
		{
			$rules['bwpm.' . $tableName . 'create'] = array(
				$bwpmUserGroups['BwPostmanAdmin']['id']     => true,
			);
		}

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
				$bwpmUserGroups['BwPostmanAdmin']['id']     => true,
			);
		}

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
		$keys = array_keys($this->sectionRules);

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
	 * Method to initialize assets for component and sections with predefined basic rules at installation
	 *
	 * @param boolean $updateComponent
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function createBaseAssets($updateComponent = false)
	{
		// @ToDo: Check if exceptions are handled correctly
		$this->getTableNamesFromDB();

		// Get rules
		if (!$updateComponent)
		{
			$this->initializeComponentAssets();
			$rulesJson = $this->componentRules;
		}
		else
		{
			// @ToDo: get component rules from state
			$componentAsset	= $this->getBaseAssetRules(array('tableNameUC' => 'component'));
			$rulesJson		= $componentAsset['rules'];
		}

		$rules = new JAccessRules($rulesJson);

		$this->updateComponentRules($rules);
		$this->initializeSectionAssets();

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);
			if ($hasAsset)
			{
				$sectionRules = $this->presetSectionRules($table);

				$sectionName = substr($table['tableNameRaw'], 0, -1);

				// @ToDo: Check if asset exists. If so, update, else insert
				$sectionAssetExists = $this->getAssetFromTableByName('com_bwpostman.' . $sectionName);

				if (!is_null($sectionAssetExists))
				{
					$this->updateSectionAsset($sectionName, $sectionRules);
				}
				else
				{
					$this->insertBaseAsset($table, false);
				}
			}
		}
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
		// @ToDo: Check if exceptions are handled correctly
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__assets'));
		$query->set($db->quoteName('rules') . " = " . $db->quote($rules));
		$query->where($db->quoteName('name') . ' = ' . $db->Quote('com_bwpostman'));

		$db->setQuery($query);
		$writeAsset = $db->execute();

		return $writeAsset;
	}

	/**
	 * Method to initialize asset for component
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function initializeComponentAssets()
	{
		$rules	= array();

		// Get all BwPostman usergroups
		$bwpmUserGroups = $this->getAllBwpmUserGroups();
		$joomlaGroups	= $this->getJoomlaGroups();
		$usedGroups		= array_merge($bwpmUserGroups, $joomlaGroups);

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
			$usedGroups['Administrator']['id']             => true,
			$usedGroups['Manager']['id']                   => true,
			$usedGroups['BwPostmanAdmin']['id']            => true,
			$usedGroups['BwPostmanCampaignAdmin']['id']    => false,
			$usedGroups['BwPostmanNewsletterAdmin']['id']  => false,
			$usedGroups['BwPostmanSubscriberAdmin']['id']  => false,
			$usedGroups['BwPostmanTemplateAdmin']['id']    => false,
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
	}

	/**
	 * Method to initialize assets for all sections/tables over all possible usergroups
	 * To prevent warnings at restore, usergroups are reduced to them that are installed
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function initializeSectionAssets()
	{
		// @ToDo: Check if exceptions are handled correctly
		// Set all actions possible in and with sections
		$actions = array('create', 'edit', 'edit.own', 'edit.state', 'archive', 'restore', 'delete', 'send');

		$rules = $this->componentRules;

		// Get all actions of usergroups which might have to do with BwPostman and which exists at this installation
		$reducedGroupsActions = $this->getReducedSampleRightsArray();

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);
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
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = true;
							break;

						case 'edit':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = false;
							break;

						case 'edit.own':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = true;
							break;

						case 'edit.state':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = false;
							break;

						case 'archive':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = false;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = false;
							break;

						case 'restore':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = false;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = false;
							break;

						case 'delete':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = false;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = false;
							break;

						case 'send':
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionPublisher] = true;
							$rules['bwpm.' . $singularTableName . '.' . $action][$BwPmSectionEditor] = true;
							break;
					}
				}
			}
		}

		$reducedRules = $this->reduceRightsForInstalledGroups($rules);

		$this->sectionRules = $reducedRules;
	}

	/**
	 * Method to get all predefined (sample) rules, reduced to installed usergroups
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getReducedSampleRightsArray()
	{
		$bwpmUserGroups = $this->getAllBwpmUserGroups();
		$joomlaGroups	= $this->getJoomlaGroups();
		$usedGroups		= array_merge($bwpmUserGroups, $joomlaGroups);

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
		// @ToDo: Check if exceptions are handled correctly
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->update($_db->quoteName('#__assets'));
		$query->set($_db->quoteName('rgt') . " = (" . $_db->quoteName('rgt') . " + 2 ) ");
		$query->where($_db->quoteName('rgt') . ' >= ' . $com_asset['rgt']);

		$_db->setQuery($query);
		$move_asset_right = $_db->execute();

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
		// @ToDo: Check if exceptions are handled correctly
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->update($_db->quoteName('#__assets'));
		$query->set($_db->quoteName('lft') . " = (" . $_db->quoteName('lft') . " + 2 ) ");
		$query->where($_db->quoteName('lft') . ' > ' . $com_asset['rgt']);

		$_db->setQuery($query);
		$move_asset_left = $_db->execute();

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
		// @ToDo: Check if exceptions are handled correctly
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->insert($_db->quoteName('#__assets'));

		$query->columns(
			array(
				$_db->quoteName('id'),
				$_db->quoteName('parent_id'),
				$_db->quoteName('lft'),
				$_db->quoteName('rgt'),
				$_db->quoteName('level'),
				$_db->quoteName('name'),
				$_db->quoteName('title'),
				$_db->quoteName('rules')
			)
		);
		$query->values(
			$_db->quote(0) . ',' .
			$_db->quote($com_asset['id']) . ',' .
			$_db->quote((int) $com_asset['rgt']) . ',' .
			$_db->quote((int) $com_asset['rgt'] + 1) . ',' .
			$_db->quote((int) $com_asset['level'] + 1) . ',' .
			$_db->quote($asset['name']) . ',' .
			$_db->quote($asset['title']) . ',' .
			$_db->quote($asset['rules'])
		);
		$_db->setQuery($query);
		$insert_asset = $_db->execute();

		return $insert_asset;
	}

	/**
	 * Get complete asset from asset table by asset name
	 *
	 * @param $assetName
	 *
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	private function getAssetFromTableByName($assetName)
	{
		// @ToDo: Check if exceptions are handled correctly
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__assets'));
		$query->where($_db->quoteName('name') . ' = ' . $_db->quote($assetName));

		$_db->setQuery($query);

		$base_asset = $_db->loadAssoc();

		return $base_asset;
	}

	/**
	 * Extracts base asset from provided array. Used for getting asset from state array.
	 *
	 * @param array $table
	 * @param $stateAssetsRaw
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

		return false;
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
		// @ToDo: Check if exceptions are handled correctly
		$searchValue = 'com_bwpostman';

		if ($table != 'component')
		{
			$searchValue .= '.' . substr($table, 0, -1);
		}

		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__assets'));
		$query->where($_db->quoteName('name') . ' = ' . $_db->quote($searchValue));
		$_db->setQuery($query);

		$base_asset = $_db->loadAssoc();

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
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getAllBwpmUserGroups()
	{
		// @ToDo: Check if exceptions are handled correctly
		$bwpmUserGroups = array();

		foreach ($this->tableNames as $table)
		{
			$hasAsset = $this->checkForAsset($table['tableNameGeneric']);
			if ($hasAsset)
			{
				$bwpmUserGroups = array_merge($this->getBwPostmanUsergroups($table['tableNameUC']), $bwpmUserGroups);
			}
		}

		return $bwpmUserGroups;
	}

	/**
	 * Method to get all Joomla! usergroups that might be used at BwPostman
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getJoomlaGroups()
	{
		// @ToDo: Check if exceptions are handled correctly
		$searchValues = array("'Administrator'", "'Manager'", "'Publisher'", "'Editor'");

		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('title'));
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__usergroups'));
		$query->where($db->quoteName('title') . ' IN (' . implode(',', $searchValues) . ')');

		$db->setQuery($query);

		$joomlaGroups = $db->loadAssocList('title');

		return $joomlaGroups;
	}

	/**
	 * Method to get all Items of a table of BwPostman, which have asset_id = 0. This is the indicator that an asset is needed
	 * but not present at asset table.
	 *
	 * @param $tableNameGeneric
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception
	 */
	private function getItemsWithoutAssetId($tableNameGeneric)
	{
		// @ToDo: Check if exceptions are handled correctly
		$_db   = JFactory::getDbo();
		$items = array();

		$query = $_db->getQuery(true);
		$query->select('*');
		$query->from($_db->quoteName($tableNameGeneric));
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

		return $items;
	}

	/**
	 * Method to prepare collected values (array) to a string used by insert query for multiple inserts
	 *
	 * @param array $default_asset   asset that holds current preset for new asset
	 * @param $item
	 * @param $title
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function writeInsertStringFromCurrentItem(&$default_asset, $item, $title)
	{
		$db	= JFactory::getDbo();

		$curr_asset          = $default_asset;
		$curr_asset['lft']   = $default_asset['lft'];
		$curr_asset['rgt']   = $default_asset['rgt'];
		$curr_asset['name']  = $default_asset['name'] . '.' . $item['id'];
		$curr_asset['title'] = $db->escape($item[$title]);

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
		$db = JFactory::getDbo();

		$values = array();

		foreach ($arrayData as $k => $v)
		{
			$values[$k] = $db->quote($v);
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
	 * @return array
	 *
	 * @throws BwException
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function insertAssets($itemsWithoutAsset, $table)
	{
		// @ToDo: Check if exceptions are handled correctly
		$sectionAsset = $this->getBaseAsset($table['tableNameRaw'], true);
		if (!is_array($sectionAsset) || !key_exists('rules', $sectionAsset))

		{
			$sectionAsset = $this->insertBaseAsset($table);
		}

		$default_asset	= $this->getDefaultAsset($sectionAsset);
		$title			= $this->getAssetTitle($table['tableNameGeneric']);

		$assetLoopCounter	= 0;
		$asset_loop			= 0;
		$asset_loop_max		= 1000;
		$asset_max			= count($itemsWithoutAsset);

		$mapOldAssetIdsToNew = array();

		$this->assetColnames = array_keys(JFactory::getDbo()->getTableColumns('#__assets'));

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
				$this->writeLoopAssets($dataset, $assetLoopCounter, $sectionAsset, $mapOldAssetIdsToNew);

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
	 * @since 2.0.0
	 *
	 * @throws BwException
	 */
	private function insertItems($itemsWithoutAsset, $tableNameGeneric, $mapOldAssetIdsToNew)
	{
		// @ToDo: Check if exceptions are handled correctly
		/*
		 * Import item data (can't use table bind/store, because we have IDs and Joomla sets mode to update,
		 * if ID is set, but in empty tables there is nothing to update)
		 */
		$max_count			= ini_get('max_execution_time');
		$assetLoopCounter	= 0;
		$count				= 0;
		$data_loop_max		= $this->getDataLoopMax($tableNameGeneric);
		$data_max			= count($itemsWithoutAsset);

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
					$this->writeLoopDatasets($dataset, $tableNameGeneric);

					// reset loop values
					$data_loop = 0;
					$dataset   = array();
				}
			} // end foreach table items
		} // endif data sets exists
	}

	/**
	 * Method to delete a specific BwPostman table
	 * @param $table
	 *
	 * @since 2.0.0
	 *
	 * @throws BwException
	 */
	protected function deleteBwPostmanTable($table)
	{
		// @ToDo: Check if exceptions are handled correctly
		$db	= JFactory::getDbo();

		$drop_table = $db->dropTable($table);
		if (!$drop_table)
		{
			throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_ERROR', $table));
		}
		else
		{
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DROP_TABLE_SUCCESS', $table) . '</p>';
		}
	}

	/**
	 * Method to create a specific BwPostman table anew.
	 * Used while restoring tables from backup. The create query comes from backup file, because we have to meet
	 * BwPostman/table version appropriate to saved tables. After restoring a check of tables is automatically done by
	 * BwPostman to ensure tables now meet installed BwPostman version.
	 *
	 * @param string   $table           table to create
	 * @param array    $tablesQueries   query used for creation
	 *
	 * @since 2.0.0
	 *
	 * @throws BwException
	 * @throws Exception
	 */
	protected function createBwPostmanTableAnew($table, $tablesQueries)
	{
		// @ToDo: Check if exceptions are handled correctly
		if ($table != 'component')
		{
			$db	= JFactory::getDbo();

			$query = str_replace("\n", '', $tablesQueries[$table]['queries']);
			$db->setQuery($query);
			$create_table = $db->execute();
			if (!$create_table)
			{
				throw new BwException(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_ERROR', $table));
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_CREATE_TABLE_SUCCESS', $table) . '</p>';
			}
		}
	}

	/**
	 * Method to prepare asset dataset for write to asset table
	 *
	 * @param array     $asset             asset to prepare
	 * @param array     $asset_transform   array to hold map for item id, old asset id and newly created asset id
	 *                                     item id is already written, old asset id is entered here
	 * @param integer   $s                 control counter
	 * @param array     $base_asset        base asset to get parent id
	 * @param integer   $curr_asset_id     variable to memorize current values for rgt and lft
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function prepareAssetValues($asset, &$asset_transform, $s, $base_asset, &$curr_asset_id)
	{
		// @ToDo: $current_asset_id is misleading. This variable holds current value for lft and rgt!
		$db		= JFactory::getDbo();
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
					$values['lft'] = $curr_asset_id++;
					break;
				case 'rgt':
					$values['rgt'] = $curr_asset_id++;
					break;
				default:
					$values[$k] = $db->quote($v);
					break;
			}
		}

		$dataset = '(' . implode(',', $values) . ')';

		return $dataset;
	}
}
