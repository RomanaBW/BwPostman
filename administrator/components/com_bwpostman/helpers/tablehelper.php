<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman table helper class for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

defined ('_JEXEC') or die ();

abstract class BwPostmanTableHelper {

	/**
	 * Method to check, if user_id of subscriber matches ID in joomla user table, updating if mail address exists.
	 * Only datasets with entered user_id in table subscribers will be checked
	 *
	 * @return	bool
	 *
	 * @since	1.0.1
	 */
	public static function checkUserIds()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('user_id') . ' > ' . (int) 0);

		$_db->setQuery($query);
		$users	= $_db->loadObjectList();

		// update user_id in subscribers table
		foreach ($users as $user) {
			// get ids from users table if mail address exists in user table
			$query->clear();
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__users'));
			$query->where($_db->quoteName('email') . ' = ' . $_db->quote($user->email));

			$_db->setQuery($query);
			$user->user_id	= $_db->loadResult();

			// update subscribers table
			$query->clear();
			$query->update($_db->quoteName('#__bwpostman_subscribers'));
			$query->set($_db->quoteName('user_id') . " = " . (int) $user->user_id);
			$query->where($_db->quoteName('id') . ' = ' . (int) $user->id);

			$_db->setQuery($query);
			$_db->execute();
		}
		return true;
	}

	/**
	 * Method to check, if column asset_id has a real value. If not, there is no possibility to delete datasets in BwPostman.
	 * Therefore each dataset without real value for asset_id has to be stored one time, to get this value
	 *
	 * @return	bool
	 *
	 * @since	1.0.1
	 */
	public static function checkAssetId()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		// set tables that has column asset_id
		$tablesToCheck	= array('#__bwpostman_campaigns', '#__bwpostman_mailinglists', '#__bwpostman_newsletters', '#__bwpostman_subscribers', '#__bwpostman_templates');

		// get items without real asset id (=0)
		foreach ($tablesToCheck as $table) {
			$query->clear();
			$query->select('*');
			$query->from($_db->quoteName($table));
			$query->where($_db->quoteName('asset_id') . ' = ' . (int) 0);

			$_db->setQuery($query);
			$items	= $_db->loadObjectList();

			// if there are items without asset id, get table object…
			if (is_array($items)) {
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/tables/');
				switch ($table) {
					case '#__bwpostman_campaigns':
							$tableObject	= JTable::getInstance('Campaigns', 'BwPostmanTable');
							$item_name		= 'campaign';
						break;
					case '#__bwpostman_mailinglists':
							$tableObject	= JTable::getInstance('Mailinglists', 'BwPostmanTable');
							$item_name		= 'mailinglist';
						break;
					case '#__bwpostman_newsletters':
							$tableObject	= JTable::getInstance('Newsletters', 'BwPostmanTable');
							$item_name		= 'newsletter';
						break;
					case '#__bwpostman_subscribers':
							$tableObject	= JTable::getInstance('Subscribers', 'BwPostmanTable');
							$item_name		= 'subscriber';
						break;
					case '#__bwpostman_templates':
							$tableObject	= JTable::getInstance('Templates', 'BwPostmanTable');
							$item_name		= 'template';
						break;
				}

				// …and process storing. That creates a correct asset id
				foreach ($items as $item) {
					// Bind the data…
					if (!$tableObject->bind($item)) {
//						$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_SAVE_ASSET_BIND_ERROR', $item_name, $item->id), 'error');
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_SAVE_ASSET_BIND_ERROR', $item_name, $item->id) . '</p>';
						return false;
					}
					// …and store them
					if (!$tableObject->store()) {
//						$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_SAVE_ASSET_STORE_ERROR', $item_name, $item->id), 'error');
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_SAVE_ASSET_STORE_ERROR', $item_name, $item->id) . '</p>';
						return false;
					}
				}
			}
//			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSET_OK', $table), 'message');
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSET_OK', $table) . '</p>';
//			ob_flush();
//			flush();
		}
		return true;
	}

	/**
	 * Method to check BwPostman tables
	 *
	 * @return	boolean		true if all is ok
	 *
	 * @since	1.0.1
	 */
	public static function checkTables()
	{
		// get needed tables from installation file
		$neededTables	= self::getNeededTables();
		if (!is_array($neededTables)) {
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_NEEDED_ERROR') . '</p>';
			return false;
		}

		// get installed table names
		$installedTableNames	= self::getTableNamesFromDB();
		if (!is_array($installedTableNames)) {
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_INSTALLED_ERROR') . '</p>';
			return false;
		}

		// convert to generic table names
		foreach ($installedTableNames as $table) {
			$genericTableNames[]	= self::getGenericTableName($table);
		}

		// check table names
		if (!self::checkTableNames($neededTables, $genericTableNames, 'check')) {
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECKNAMES_ERROR') . '</p>';
			return false;
		}

		// check table columns
		for ($i=0; $i < count($neededTables); $i++) {
			$res	= self::checkTableColumns($neededTables[$i]);

			if ($res == 2) $i--;
			if ($res == 0) {
				echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_CHECKCOLS_ERROR') . '</p>';
				return false;
			}
		}

		// check asset IDs (necessary because asset_id = 0 prevents deleting)
		if (!self::checkAssetId()) {
			echo '<p class="bw_tablecheck_warn">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_ASSETS_WARN') . '</p>';
//			ob_flush();
//			flush();
			// @todo shall we break here or not?
			// return false;
		}

		// check user IDs in subscriber Table
		if (!self::checkUserIds()) {
			echo '<p class="bw_tablecheck_warn">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USERID_WARN') . '</p>';
//			ob_flush();
//			flush();
			// @todo shall we break here or not?
			// return false;
		}
		else {
			echo str_pad('<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_USERID_OK') . '</p>', 4096);
//			ob_flush();
//			flush();
		}

		return true;
	}

	/**
	 * Method to save BwPostman tables
	 *
	 * @param	boolean		$update save while component update?
	 *
	 * @return	boolean		true if save file is written
	 *
	 * @since	1.0.1
	 */
	public static function saveTables($update = FALSE)
	{
		// Import JFolder and JFileobject class
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$date		= JFactory::getDate();
		$path		= JFactory::getConfig()->get('tmp_path');
		$file_name	= $path . '/BwPostman_Tables_Server_' . $date->format("Y-m-d_H_i") . '.xml';
		$fp			= fopen($file_name, 'wb');

		if (!JFolder::exists($path)) {
			if ($update) echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_FOLDER_NOT_FOUND', $path) . '</p>';
			return false;
		}

		// get all names of installed BwPostman tables
		$tableNames	= self::getTableNamesFromDB();

		$file_data		= array();
		$file_data[]	= self::buildXmlHeader();							// get XML header

		if ($res = fwrite($fp, implode("\n", $file_data))) {
			$file_data	= array();

			foreach ($tableNames as $table) {
			// do not save the table "bwpostman_templates_tpl"
				if (strpos($table, 'templates_tpl') !== false) {
				}
				else {
					$tableName	= self::getGenericTableName($table);

					$file_data[]	= "\t\t<tables>";								// set XML tables section
					$file_data[]	= self::buildXmlStructure($tableName);			// get table description
					if (!$res = fwrite($fp, implode("\n", $file_data))) {
						if ($update) echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_ERROR', $file_name) . '</p>';
						break;
					}
					else {
						if ($update) echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_TABLE_SUCCESS', $tableName) . '</p>';
					}
					$file_data	= array();

					self::buildXmlData($tableName, $fp);		// write table data
					$file_data[]	= "\t\t</tables>\n";								// set XML tables section
					if (!$res = fwrite($fp, implode("\n", $file_data))) {
						if ($update) echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_ERROR', $file_name) . '</p>';
						break;
					}
					fflush($fp);
					$file_data	= array();

				}
			}

			$file_data[]	= self::buildXmlFooter();							// get XML footer
			$file_data		= implode("\n", $file_data);
		}

		if ($res = fwrite($fp, $file_data)) {
			if ($update) echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_SUCCESS', $file_name) . '</p>';
		}
		else {
			if ($update) echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_ERROR', $file_name) . '</p>';
		}

		if ($update) {
			return true;
		}
		else {
			return $file_name;
		}
	}

	/**
	 * Method to to restore BwPostman tables
	 *
	 * @param	string		$filename   name of file to restore
	 *
	 * @return	boolean		true if all is ok
	 *
	 * @since	1.0.1
	 */
	public static function restoreTables($filename	= '')
	{
		$_db	= JFactory::getDbo();

		// get import file
		if (false === $fh = fopen($filename, 'r')) { // File cannot be opened
			echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_OPEN_FILE_ERROR', $filename) . '</p>';
			return false;
		}
		else {
			// Parse the XML
			$xml	= simplexml_load_file($filename);

			// check if xml file is ok (most error case: non-xml-conform characters in xml file)
			if (!is_object($xml)) {
				echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_READ_XML_ERROR', $filename) . '</p>';
				return false;
			}

			// Initialize some other variables
			$generals		= array();
			$table_names	= array();
			$neededTables	= array();
			$tables			= array();
			$x_tables		= array();

			//Get general intormation
			if (property_exists($xml->database, 'Generals')) {
				$generals['version']	= $xml->database->Generals->BwPostmanVersion->__toString();
				$generals['savedate']	= $xml->database->Generals->SaveDate->__toString();
				echo '<p class="bw_tablecheck_warn">' . 'Version: ' . $generals['version'] . '</p>';
				echo '<p class="bw_tablecheck_warn">' . 'Datum: ' . $generals['savedate'] . '</p>';
//				ob_flush();
//				flush();

			}

			// Get all tables from the xml file converted to arrays recursively, results in an array/list of table-arrays
			foreach ($xml->database->tables as $table) $x_tables[]	= $table;

			if (count($x_tables) == 0) {
				echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_NO_TABLES_ERROR') . '</p>';
				return false;
			}

			// extract table names and install queries
			foreach ($x_tables as $table) {
				$table_names[]	= $table->table_structure->table_name->name->__toString();
				$queries[]		= $table->table_structure->install_query->query->__toString();
			}

			// paraphrase tables array for better handling and convert objects to strings
			// process datasets
			for ($i=0; $i < count($x_tables); $i++) {
				$items	= array();
				foreach ($x_tables[$i]->table_data->dataset as $item) {
					$props	= get_object_vars($item);
					$p_keys	= array_keys($props);
					for ($j = 0; $j < count($props); $j++) {
						if (is_object($props[$p_keys[$j]])) {
							$props[$p_keys[$j]]	= $props[$p_keys[$j]]->__toString();
						}
					}
					$items[]	= $props;
				}
				$tables[$table_names[$i]]['table_data']	= $items;

				// process table keys
				$items	= array();
				foreach ($x_tables[$i]->table_structure->keys->key as $key)	{
					$keys	= get_object_vars($key);
					$p_keys	= array_keys($keys);
					for ($j = 0; $j < count($keys); $j++) {
						if (is_object($keys[$p_keys[$j]])) {
							$keys[$p_keys[$j]]	= $keys[$p_keys[$j]]->__toString();
						}
					}
					$items[]	= $keys;
				}
				foreach ($items as $item) $tables[$table_names[$i]]['table_structure']['table_keys'][$item['Column_name']]= $item;

				// process table columns
				$items	= array();
				foreach ($x_tables[$i]->table_structure->fields->field as $column) {
					$cols	= get_object_vars($column);
					$p_keys	= array_keys($cols);
					for ($j = 0; $j < count($cols); $j++) {
						if (is_object($cols[$p_keys[$j]])) {
							$cols[$p_keys[$j]]	= $cols[$p_keys[$j]]->__toString();
						}
					}
					$items[]	= $cols;

				}
				foreach ($items as $item) $tables[$table_names[$i]]['table_structure']['fields'][$item['Column']]= $item;

				// process table name
				$tables[$table_names[$i]]['table_structure']['table_name']	= $x_tables[$i]->table_structure->table_name->name->__toString();
			}
			unset($x_tables);

			// check if all needed tables are installed and install missing tables
			// collect data, for check needed
			for ($i = 0; $i < count($table_names); $i++) {
				// get table name, columns and install query
				$neededTables[$i]					= new stdClass();
				$neededTables[$i]->name				= $table_names[$i];
				$neededTables[$i]->columns			= $tables[$table_names[$i]]['table_structure']['fields'];
				$neededTables[$i]->install_query	= $queries[$i];

				// get primary key
				$p_key_needed					= array_keys($tables[$table_names[$i]]['table_structure']['table_keys']);
				$neededTables[$i]->primary_key	= implode(',', $p_key_needed);


				// get engine of installed table
				$start	= strpos($queries[$i], 'ENGINE=');
				if ($start !== false) {
					$stop	= strpos($queries[$i], ' ', $start);
					$length	= $stop - $start - 7;
					$neededTables[$i]->engine	= substr($queries[$i], $start + 7, $length);
				}

				// get default charset of installed table
				$start	= strpos($queries[$i], 'DEFAULT CHARSET=');
				if ($start !== false) {
					$neededTables[$i]->charset	= substr($queries[$i], $start + 16);
				}
			}

			// get installed table names and convert them to generic names
			$genericTableNames		= array();
			$installedTableNames	= self::getTableNamesFromDB();

			foreach ($installedTableNames as $table) {
				$genericTableNames[]	= self::getGenericTableName($table);
			}

			// check table names
			if (!self::checkTableNames($neededTables, $genericTableNames, 'restore')) {
				echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CHECKNAMES_ERROR') . '</p>';
				return false;
			}

			// check table columns
			for ($i=0; $i < count($neededTables); $i++) {
				$res	= self::checkTableColumns($neededTables[$i]);

				if ($res == 2) $i--;
				if ($res == 0) {
					echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TABLES_CHECKCOLS_ERROR') . '</p>';
					return false;
				}
			}

			// Import filesystem libraries. Perhaps not necessary, but does not hurt
			jimport('joomla.filesystem.folder');

			// get paths to table files to include to search path
			$include_path	= array();
			$include_path[]	= JPATH_ADMINISTRATOR . '/components/com_bwpostman/tables/';

			if (JFolder::exists(JPATH_PLUGINS . '/bwpostman/')) {
				$plugin_path	= JPATH_PLUGINS . '/bwpostman/';
				$p_folders		= JFolder::folders($plugin_path);

				foreach ($p_folders as $folder) {
					if (JFolder::exists($plugin_path . $folder . '/tables/')) $include_path[]	= $plugin_path . $folder . '/tables/';
				}
			}
			JTable::addIncludePath($include_path);

			// get table object
			foreach ($tables as $table) {
				// get raw table name
				$start	= strpos($table['table_structure']['table_name'], '_', 3);

				if ($start !== false) {
					$table_name_raw	= substr($table['table_structure']['table_name'], $start + 1);
				}

				$tableObject	= JTable::getInstance($table_name_raw, 'BwPostmanTable');

				// set asset name
				if (property_exists($tableObject, 'asset_id')) {
					$asset_name	= '%com_bwpostman.' . substr($table_name_raw, 0, strlen($table_name_raw) - 1) . '%';
				}
				else {
					$asset_name = '';
				}

				// clear table
				$query	= 'TRUNCATE TABLE ' . $_db->quoteName($table['table_structure']['table_name']);

				$_db->setQuery($query);
				$deleteTable	= $_db->Execute($query);
				if (!$deleteTable) {
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TRUNCATE_ERROR', $table['table_structure']['table_name']) . '</p>';
					return false;
				}
				else {
					echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_TRUNCATE_SUCCESS', $table['table_structure']['table_name']) . '</p>';
//					ob_flush();
//					flush();
				}

				// delete existing assets
				$query		= $_db->getQuery(true);
				if ($asset_name != '') {
					$query->delete($_db->quoteName('#__assets'));
					$query->where($_db->quoteName('name') . ' LIKE ' . $_db->Quote($asset_name));

					$_db->setQuery($query);
					$deleteAssets	= $_db->Execute($query);
					if (!$deleteAssets) {
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DELETE_ASSETS_ERROR', $table['table_structure']['table_name']) . '</p>';
						return false;
					}
					else {
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_DELETE_ASSETS_SUCCESS', $table['table_structure']['table_name']) . '</p>';
//						ob_flush();
//						flush();
					}
				} // end delete assets

				// import data (cant use table bind/store because we have IDs and Joomla sets mode to update, if ID is set, but in empty tables there is nothing to update
				foreach ($table['table_data'] as $item) {
					$keys	= array();
					$values	= array();

					foreach ($item as $k =>$v) {
						$keys[]		= $k;
						$values[]	= $_db->Quote($v);
					}
					$query->clear();

					$query->insert($_db->quoteName($table['table_structure']['table_name']));
					$query->columns($keys);
					$query->values(implode(',', $values));
					$_db->setQuery($query);

					if (!$_db->execute()) {
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_SAVE_DATA_ERROR', $table['table_structure']['table_name'], $item->id) . '</p>';
						return false;
					}

					// Asset Tracking
					if ($asset_name != '') {
						$tableObject->bind($item);
						$parentId	= $tableObject->getAssetParentId();
						$name		= $tableObject->getAssetName();
						$title		= $tableObject->getAssetTitle();

						$asset	= JTable::getInstance('Asset', 'JTable', array('dbo' => $tableObject->getDbo()));
						$asset->loadByName($name);

						// Re-inject the asset id.
						$item['asset_id'] = $asset->id;

						// Check for an error.
						if ($error = $asset->getError())
						{
							$tableObject->setError($error);
							return false;
						}

						// Specify how a new or moved node asset is inserted into the tree.
						if (empty($item['asset_id']) || $asset->parent_id != $parentId)
						{
							$asset->setLocation($parentId, 'last-child');
						}

						// Prepare the asset to be stored. No rules at this point
						$asset->parent_id   = $parentId;
						$asset->name        = $name;
						$asset->title       = $title;

						if (!$asset->check() || !$asset->store())
						{
							$tableObject->setError($asset->getError());
							return false;
						}

						// Create an asset_id or heal one that is corrupted.
						if (empty($item['asset_id']))
						{
							// Update the asset_id field in this table.
							$item['asset_id']	= (int) $asset->id;
							$key_name			= array_keys( $table['table_structure']['table_keys']);

							$query->clear();
							$query->update($_db->quoteName($table['table_structure']['table_name']));
							$query->set('asset_id = ' . (int) $item['asset_id']);
							$query->where($_db->quoteName($key_name[0]) . ' = ' . (int) $item[$key_name[0]]);
							$_db->setQuery($query);

							if (!$_db->execute())
							{
								$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $_db->getErrorMsg()));
								$tableObject->setError($e);
								return false;
							}
						}
					}
				}
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_RESTORE_STORE_SUCCESS', $table['table_structure']['table_name']) . '</p>';
//				ob_flush();
//				flush();

				if ($table['table_structure']['table_name'] == '#__bwpostman_subscribers') {
					self::checkUserIds();
				}
			} // end foreach x_tables
		} // end file import

		// delete temporary file
		jimport('joomla.filesystem.file');
		if (JFile::exists($filename)) {
			JFile::delete($filename);
		}

		return true;
	}

	/**
	 * Method to compare needed tables names with installed ones, check engine, default charset and primary key
	 *
	 * @param	array		$neededTables       object list of tables, that must be installed
	 * @param	array		$genericTableNames  names of tables, that are installed
	 * @param	string		$mode               mode to check, "check and repair" or "restore"
	 *
	 * @return	boolean		true if all is ok
	 *
	 * @since	1.0.1
	 */
	protected static function checkTableNames($neededTables, $genericTableNames, $mode = 'check')
	{
		if (!is_array($neededTables) && !is_array($genericTableNames)) {
			return false;
		}

		$_db				= JFactory::getDbo();
		$neededTableNames	= array();

		// extract table names from table object list,
		foreach ($neededTables as $table) {
			$neededTableNames[]	= $table->name;
		}

		// compare table names first direction (all needed tables installed?)
		$diff_1	= array_diff($neededTableNames, $genericTableNames);
		if (!empty($diff_1)) {
			echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED', implode(',', $diff_1)) . '</p>';
//			ob_flush();
//			flush();

			// set all install queries
			$queries	= array();
			foreach ($neededTables as $table) {
				$queries[$table->name]	=	$table->install_query;
			}

			// install missing tables (complete queries exists in table object list from install file)
			foreach ($diff_1 as $missingTable) {
				$query		= $queries[$missingTable];

				$_db->setQuery($query);
				$createDB	= $_db->Execute($query);
				if (!$createDB) {
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_ERROR', $missingTable) . '</p>';
//					ob_flush();
//					flush();
				}
				else {
					echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NEEDED_CREATE_SUCCESS', $missingTable) . '</p>';
//					ob_flush();
//					flush();
				}
			}
		}
		else {
			echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ALL_TABLES_INSTALLED') . '</p>';
//			ob_flush();
//			flush();
		}

		// compare table names second direction (obsolete tables installed?). Only if in check mode
		if ($mode == 'check') {
			$diff_2	= array_diff($genericTableNames, $neededTableNames);
			if (!empty($diff_2)) {
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE', implode(',', $diff_2)) . '</p>';
//				ob_flush();
//				flush();

				// delete obsolete tables
				foreach ($diff_2 as $obsoleteTable) {
					$query		= "DROP TABLE IF EXISTS " . $obsoleteTable;

					$_db->setQuery($query);
					$deleteDB	= $_db->Execute($query);
					if (!$deleteDB) {
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_ERROR', $obsoleteTable) . '</p>';
//						ob_flush();
//						flush();
					}
					else {
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_OBSOLETE_DELETE_SUCCESS', $obsoleteTable) . '</p>';
//						ob_flush();
//						flush();
					}
				}
			}
			else {
				echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_NO_OBSOLETE_TABLES') . '</p>';
//				ob_flush();
//				flush();
			}
		}

		// check table engine and default charset
		foreach ($neededTables as $table) {
			$create_statement	= $_db->getTableCreate($table->name);

			// get engine of installed table
			$start	= strpos($create_statement[$table->name], 'ENGINE=');
			if ($start !== false) {
				$stop	= strpos($create_statement[$table->name], ' ', $start);
				$length	= $stop - $start - 7;
				$engine	= substr($create_statement[$table->name], $start + 7, $length);
			}

			// get default charset of installed table
			$start	= strpos($create_statement[$table->name], 'DEFAULT CHARSET=');
			if ($start !== false) {
				$c_set	= substr($create_statement[$table->name], $start + 16);
			}

			if ((strcasecmp($engine, $table->engine) != 0) || (strcasecmp($c_set, $table->charset) != 0)) {
				$query	= 'ALTER TABLE ' . $_db->quoteName($table->name) . ' ENGINE=INNODB DEFAULT CHARSET=utf8';
				$_db->setQuery($query);
				$modifyTable	= $_db->Execute($query);
				if (!$modifyTable) {
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_ERROR', $table->name) . '</p>';
					return false;
				}
				else {
					echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_MODIFY_TABLE_SUCCESS', $table->name) . '</p>';
//					ob_flush();
//					flush();
				}
			}
		}
		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_ENGINE_OK') . '</p>';
//		ob_flush();
//		flush();

		// check primary key (There can be only one!)
		foreach ($neededTables as $table) {
			$installed_key_tmp	= $_db->getTableKeys($table->name);
			$installed_key		= '';

			if (count($installed_key_tmp) > 1) {
				for ($i = 0; $i < count($installed_key_tmp); $i++) {
					$installed_key	.= $installed_key_tmp[$i]->Column_name . ',';
				}
				$length			= strlen($installed_key) - 1;
				$tmp_string		= substr($installed_key, 0, $length);
				$installed_key	= $tmp_string;
			}
			else {
				$installed_key	.= $installed_key_tmp[0]->Column_name;
			}

			// compare table key
			if (strcasecmp($table->primary_key, $installed_key) != 0) {
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_WRONG', $table->name) . '</p>';
//				ob_flush();
//				flush();

					$query	= 'ALTER TABLE ' . $_db->quoteName($table->name) . ' ADD PRIMARY KEY, ADD PRIMARY KEY ' . $_db->quoteName($table->primary_key);
					$_db->setQuery($query);
					$modifyKey	= $_db->Execute($query);
					if (!$modifyKey) {
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_ERROR', $table->name) . '</p>';
						return false;
					}
					else {
						echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_INSTALL_SUCCESS', $table->name) . '</p>';
//						ob_flush();
//						flush();
					}
			}
		}
		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_KEYS_OK') . '</p>';
//		ob_flush();
//		flush();

		return true;
	}

	/**
	 * Method to check needed tables columns
	 *
	 * @param	object		$checkTable     object of table, that must be installed
	 *
	 * @return	boolean		true if all is ok
	 *
	 * @since	1.0.1
	 */
	protected static function checkTableColumns($checkTable)
	{
		if (!is_object($checkTable)) {
			return 0;
		}

		$_db	= JFactory::getDbo();

		$neededColumns		= array();
		$installedColumns	= array();

		foreach ($checkTable->columns as $col) {
			if (is_Object($col)) {
				$neededColumns[]	= JArrayHelper::fromObject($col, true);
			}
			else {
				$neededColumns[]	= $col;
			}
		}
		foreach ($_db->getTableColumns($checkTable->name, false) as $col) {
			$installedColumns[]	= JArrayHelper::fromObject($col, true);
		}

		// prepare check for col names
		$search_cols_1	= array();
		$search_cols_2	= array();
		foreach ($installedColumns as $col) {
			$search_cols_1[]	= $col['Field'];
		}
		foreach ($neededColumns as $col) {
			$search_cols_2[]	= $col['Column'];
		}

		// check for col names
		for ($i=0; $i < count($neededColumns); $i++) {

			// check for needed col names
			if (array_search($neededColumns[$i]['Column'], $search_cols_1) === false) {
				($neededColumns[$i]['Null'] == 'NO') ? $null = ' NOT NULL' : $null = '';
				(isset($neededColumns[$i]['Default'])) ? $default	= ' DEFAULT ' . $_db->Quote($neededColumns[$i]['Default']) : $default	= '';

				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COLS', $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
//				ob_flush();
//				flush();
				$query	= "ALTER TABLE " . $_db->quoteName($checkTable->name) . " ADD " . $_db->quoteName($neededColumns[$i]['Column']) . ' ' . $neededColumns[$i]['Type'] . $null . $default . " AFTER " . $_db->quoteName($neededColumns[$i-1]['Column']);

				$_db->setQuery($query);
				$insertCol	= $_db->Execute($query);

				if (!$insertCol) {
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_ERROR', $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
					return 0;
				}
				else {
					echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_CREATE_SUCCESS', $neededColumns[$i]['Column'], $checkTable->name) . '</p>', 4096);
//					ob_flush();
//					flush();
					return 2; // Durchlauf zurücksetzen
				}
			}

			// check for obsolete col names
			if (array_search($installedColumns[$i]['Field'], $search_cols_2) === false) {

				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COLS', $installedColumns[$i]['Field'], $checkTable->name) . '</p>';
//				ob_flush();
//				flush();
				$query	= "ALTER TABLE " . $_db->quoteName($checkTable->name) . " DROP " . $_db->quoteName($installedColumns[$i]['Field']);

				$_db->setQuery($query);
				$deleteCol	= $_db->Execute($query);

				if (!$deleteCol) {
					echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_ERROR', $installedColumns[$i]['Field'], $checkTable->name) . '</p>';
					return 0;
				}
				else {
					echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF2_COL_CREATE_SUCCESS', $installedColumns[$i]['Field'], $checkTable->name) . '</p>', 4096);
//					ob_flush();
//					flush();
					return 2; // Durchlauf zurücksetzen

				}
			}
		}
		echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_OK', $checkTable->name) . '</p>', 4096);
//		ob_flush();
//		flush();

		for ($i=0; $i < count($neededColumns); $i++) {
			$diff	= array_udiff($neededColumns[$i], $installedColumns[$i], 'strcasecmp');

			if (!empty($diff)) {
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES', implode(',', array_keys($diff)), $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
//				ob_flush();
//				flush();

				// install missing columns
				foreach (array_keys($diff) as $missingCol) {
					($neededColumns[$i]['Null'] == 'NO') ? $null = ' NOT NULL' : $null = '';
					(isset($neededColumns[$i]['Default'])) ? $default	= ' DEFAULT ' . $_db->Quote($neededColumns[$i]['Default']) : $default	= '';
					$query	 = "ALTER TABLE " . $_db->quoteName($checkTable->name);
					$query	.= " MODIFY " . $_db->quoteName($neededColumns[$i]['Column']) . ' ' . $neededColumns[$i]['Type'] . $null . $default;
					if (array_key_exists('Extra', $neededColumns[$i])) $query	.= " " . $neededColumns[$i]['Extra'];

					$_db->setQuery($query);
					$alterCol	= $_db->Execute($query);

					if (!$alterCol) {
						echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_ERROR', $missingCol, $neededColumns[$i]['Column'], $checkTable->name) . '</p>';
//						ob_flush();
//						flush();
					}
					else {
						echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_DIFF_COL_ATTRIBUTES_SUCCESS', $missingCol, $neededColumns[$i]['Column'], $checkTable->name) . '</p>', 4096);
//						ob_flush();
//						flush();
					}
				}
			}
		}
		echo str_pad('<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_COMPARE_COLS_ATTRIBUTES_OK', $checkTable->name) . '</p>', 4096);
//		ob_flush();
//		flush();

	return 1;
	}

	/**
	 * Method to get the needed tables and its properties from sql install file
	 *
	 * @return	boolean		true if all is ok
	 *
	 * @since	1.0.1
	 */
	protected static function getNeededTables()
	{
		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// get paths to sql install files
		$paths		= array();
		$paths[]	= JPATH_ADMINISTRATOR . '/components/com_bwpostman/sql/';

		if (JFolder::exists(JPATH_PLUGINS . '/bwpostman/')) {
			$path2		= JPATH_PLUGINS . '/bwpostman/';
			$p_folders	= JFolder::folders($path2);

			foreach ($p_folders as $folder) {
				if (JFolder::exists($path2 . $folder . '/sql/')) $paths[]	= $path2 . $folder . '/sql/';
			}
		}

		$file_content	= array();
		$txt_array		= array();
		$tables			= array();

		foreach ($paths as $path) {
			// get sql install file
			$filename	= $path . 'install.sql';

			if (false === $fh = fopen($filename, 'r')) { // File cannot be opened
				echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_CHECK_TABLES_OPEN_INSTALLFILE_ERROR', $filename) . '</p>';
				return false;
			}
			else { // get file content
				while(!feof($fh)) $file_content[] = fgets($fh);
				fclose($fh);

				// eliminate unneeded rows (comments, empty lines, DROP TABLE)
				foreach ($file_content as $row) {
					if ((strpos($row, '--') === false) && (stripos($row, 'DROP') === false) && (trim($row) != '')) {
						$txt_array[]	= $row;
					}
				}
				//build complete text string
				$txt		= implode(' ', $txt_array);
				$queries	= array();

				// extract full queries
				while ($length	= strpos($txt, 'CREATE', 1)) {
					$queries[]	= substr($txt, 0, $length);
					$sub_txt	= substr($txt, $length);
					$txt		= $sub_txt;
				}
				$queries[]	= $txt;

				foreach ($queries as $query) {
					$table	= new stdClass();
					$query	= implode( array_map('trim',preg_split('/(\n|\r\r)/i', $query)) );

					$table->install_query	= $query;

					// get table name
					$start	= strpos($query, '#');
					if ($start !== false) {
						$stop			= strpos($query, '`', $start);
						$length			= $stop - $start;
						$table->name	= substr($query, $start, $length);
					}
					// get engine
					$start	= stripos($query, 'ENGINE');
					if ($start !== false) {
						$stop			= strpos($query, ' ', $start);
						$length			= $stop - $start;
						$table->engine	= substr($query, $start + 7, $length - 7);
					}

					// get default characterset
					$start	= stripos($query, 'DEFAULT CHARSET');
					if ($start !== false) {
						$stop			= strpos($query, ';', $start);
						$length			= $stop - $start - 16;
						$table->charset	= substr($query, $start + 16, $length);
					}

					// get primary key
					$start	= strripos($query, '(`') + 2;
					if ($start !== false) {
						$stop				= strripos($query, '`)');
						$length				= $stop - $start;
						$table->primary_key	= str_replace("`", '', substr($query, $start, $length));
					}

					// eliminate primary key
					$start	= stripos($query, ',PRIMARY');
					if ($start !== false) {
						$stop		= strpos($query, '`)') + 2;
						$length		= $stop - $start;
						$search		= substr($query, $start, $length);
						$sub_txt	= str_replace($search, '', $query);
						$query		= trim($sub_txt);
					}

					// get columns definitions
					$start	= strpos($query, '(');
					if ($start !== false) {
						$stop			= strripos($query, ')');
						$length			= $stop - $start;
						$column_string	= substr($query, $start + 1, $length-1);
						$columns		= explode(',', $column_string);

						foreach ($columns as $column) {
							$col_arr	= new stdClass();

							// get column name
							$column	= trim($column);
							$length	= strpos($column, ' ');
							if ($length > 0) {
								$col_arr->Column	= substr($column, 1, $length-2);
								$sub_txt		= substr($column, $length+1);
								$column			= $sub_txt;
							}

							// get column type
							$length	= strpos($column, ' ');
							if ($length > 0) {
								$col_arr->Type	= substr($column, 0, $length);
								$sub_txt		= substr($column, $length+1);
								$column			= $sub_txt;
							}

							// get NOT NULL
							$start	= stripos($column, 'NOT NULL');
							if ($start !== false) {
								$col_arr->Null	= 'NO';
								$sub_txt		= str_replace('NOT NULL', '', $column);
								$column			= trim($sub_txt);
							}

							// get NULL
							$start	= stripos($column, 'NULL');
							if ($start !== false) {
								$col_arr->Null	= substr($column, $start, 4);
								$sub_txt			= str_replace('NULL', '', $column);
								$column				= trim($sub_txt);
							}

							// get autoincrement
							$start	= stripos($column, 'auto_increment');
							if ($start !== false) {
								$col_arr->Extra	= substr($column, $start, 15);
								$sub_txt		= str_replace('auto_increment', '', $column);
								$column			= trim($sub_txt);
							}

							// get default
							$start	= stripos($column, 'default');
							if ($start !== false) {
								$start				= $start + 9;
								$stop				= strpos($column, "'", $start);
								$length				= $stop - $start;
								$col_arr->Default	= substr($column, $start, $length);
								$sub_txt			= str_replace($col_arr->Default, '', $column);
								$column				= trim($sub_txt);
							}
							// get unsigned
							$start	= stripos($column, 'unsigned');
							if ($start !== false) {
								$col_arr->Type		.= ' unsigned';
							}
							$table->columns[]	= $col_arr;
						} // end foreach columns
						$tables[]	= $table;
					} // end get columns definitions
				} // end foreach queries
			} // end get file content
		} // end foreach filenames
		return $tables;
	}

	/**
	 * Method to get the version of BwPostman
	 *
	 * @return	string	version
	 *
	 * @since	1.0.8
	 */
	protected static function getBwPostmanVersion()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->SetQuery($query);

		$manifest = json_decode($db->loadResult(), true);

		return $manifest['version'];
	}

	/**
	 * Method to get the database name
	 *
	 * @return	string	database name
	 *
	 * @since	1.0.1
	 */
	protected static function getDBName()
	{
		$config 	= JFactory::getConfig();

		// Get database name
		$dbname = $config->get('db','');

		return $dbname;
	}

	/**
	 * Method to get the table names of BwPostman form database
	 *
	 * @return	string	database name
	 *
	 * @since	1.0.1
	 */
	protected static function getTableNamesFromDB()
	{
		$_db	= JFactory::getDbo();

		// Get database name
		$dbname = self::getDBName() ;

		//build query to get all names of installed BwPostman tables
		$query = "SHOW TABLES WHERE `Tables_in_{$dbname}` LIKE '%bwpostman%'";

		$_db->setQuery($query);

		$tableNames = $_db->loadColumn();

		return $tableNames;
	}

	/**
	 * Builds the XML data header for the tables to export. Based on Joomla JDatabaseExporter
	 *
	 * @return	string	An XML string
	 *
	 * @since	1.0.1
	 */
	protected static function buildXmlHeader()
	{
		// Get version of BwPostman
		$version	= self::getBwPostmanVersion();

		// Get database name
		$dbname	= self::getDBName();

		$buffer	= array();

		$buffer[]	= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		$buffer[]	= '<mysqldump xmlns:xsi="http://www.w3.org/TR/xmlschema-1">';
		$buffer[]	= "\t<database name=\"$dbname\">";
		$buffer[]	= "\t\t<Generals>";
		$buffer[]	= "\t\t\t<BwPostmanVersion>" . $version . "</BwPostmanVersion>";
		$buffer[]	= "\t\t\t<SaveDate>" . JFactory::getDate()->format("Y-m-d_H:i") . "</SaveDate>";
		$buffer[]	= "\t\t</Generals>\n";

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML data footer for the tables to export
	 *
	 * @return	string	An XML string
	 *
	 * @since	1.0.1
	 */
	protected static function buildXmlFooter()
	{
		$buffer = array();

		$buffer[] = "\t</database>";
		$buffer[] = '</mysqldump>';

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML structure to export. Based on Joomla JDatabaseExporter
	 *
	 * @param   string  $tableName  name of table to build structure for
	 *
	 * @return	array	An array of XML lines (strings).
	 *
	 * @since	1.0.1
	 */
	protected static function buildXmlStructure($tableName)
	{
		$_db	= JFactory::getDbo();
		$buffer = array();

		// Get the details columns information and install query.
		$keys	= $_db->getTableKeys($tableName);
		$fields	= $_db->getTableColumns($tableName, false);
		$query	= implode('', $_db->getTableCreate($tableName));

		$buffer[] = "\t\t\t<table_structure table=\"$tableName\">";
		$buffer[] = "\t\t\t\t<table_name>";
		$buffer[] = "\t\t\t\t\t<name>$tableName</name>";
		$buffer[] = "\t\t\t\t</table_name>";
		$buffer[] = "\t\t\t\t<install_query>";
		$buffer[] = "\t\t\t\t\t<query>$query</query>";
		$buffer[] = "\t\t\t\t</install_query>";

		if (is_array($fields)) {
			$buffer[] = "\t\t\t\t<fields>";
			foreach ($fields as $field) {
				$buffer[]	= "\t\t\t\t\t<field>";
				$buffer[]	= "\t\t\t\t\t\t<Column>$field->Field</Column>";
				$buffer[]	= "\t\t\t\t\t\t<Type>$field->Type</Type>";
				$buffer[]	= "\t\t\t\t\t\t<Null>$field->Null</Null>";
				$buffer[]	= "\t\t\t\t\t\t<Key>$field->Key</Key>";
				isset($field->Default)	? $buffer[]	= "\t\t\t\t\t\t<Default>$field->Default</Default>" : '';
				$buffer[]	= "\t\t\t\t\t\t<Extra>$field->Extra</Extra>";
				$buffer[]	= "\t\t\t\t\t</field>";
			}
			$buffer[] = "\t\t\t\t</fields>";
		}

		if (is_array($keys)) {
			$buffer[] = "\t\t\t\t<keys>";
			foreach ($keys as $key) {
				$buffer[]	= "\t\t\t\t\t<key>";
				$buffer[]	= "\t\t\t\t\t\t<Non_unique>$key->Non_unique</Non_unique>";
				$buffer[]	= "\t\t\t\t\t\t<Key_name>$key->Key_name</Key_name>";
				$buffer[]	= "\t\t\t\t\t\t<Seq_in_index>$key->Seq_in_index</Seq_in_index>";
				$buffer[]	= "\t\t\t\t\t\t<Column_name>$key->Column_name</Column_name>";
				$buffer[]	= "\t\t\t\t\t\t<Collation>$key->Collation</Collation>";
				$buffer[]	= "\t\t\t\t\t\t<Null>$key->Null</Null>";
				$buffer[]	= "\t\t\t\t\t\t<Index_type>$key->Index_type</Index_type>";
				$buffer[]	= "\t\t\t\t\t\t<Comment>htmlspecialchars($key->Comment)</Comment>";
				$buffer[]	= "\t\t\t\t\t</key>";
			}
			$buffer[] = "\t\t\t\t</keys>";
		}

		$buffer[] = "\t\t\t</table_structure>\n";

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML data to export
	 *
	 * @param	string	$tableName  name of table
	 * @param	handle	$fp         handle of backup file
	 *
	 * @return	array	An array of XML lines (strings).
	 *
	 * @since	1.0.1
	 */
	protected static function buildXmlData($tableName, $fp)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		// Get the data from table
		$query->select('*');
		$query->from($_db->quoteName($tableName));

		$_db->setQuery($query);

		$data	= $_db->loadAssocList();

		$res = fwrite($fp, "\t\t\t<table_data table=\"$tableName\">\n");
		if (is_array($data)) {
			foreach ($data as $item)
			{
				$res = fwrite($fp, "\t\t\t\t<dataset>\n");
				foreach ($item as $key => $value) {
					$insert_string = str_replace('&', '&amp;', html_entity_decode($value, 0, 'UTF-8'));
					if (((($tableName == '#__bwpostman_sendmailcontent') || ($tableName == '#__bwpostman_tc_sendmailcontent')) && ($key == 'body'))
							|| (($tableName == '#__bwpostman_newsletters') && ($key == 'html_version'))
							|| (($tableName == '#__bwpostman_templates') && (($key == 'tpl_html') || ($key == 'tpl_css') || ($key == 'tpl_article') || ($key == 'tpl_divider')))) {
						$insert_string = '<![CDATA[' . $insert_string . ']]>';
					}
					$res = fwrite($fp, "\t\t\t\t\t<$key>" . $insert_string . "</$key>\n");
				}
				$res = fwrite($fp, "\t\t\t\t</dataset>\n");
				fflush($fp);
			}
		}
//		$buffer[] = "\t\t\t</table_data>\n";
		$res = fwrite($fp, "\t\t\t</table_data>\n");

		if ($res)
			return true;
		else
			return false;
	}

	/**
	 * Checks if all data and options are in order prior to exporting. Based on Joomla JDatabaseExporter
	 *
	 * @param	database    $db     database connector
	 *
	 * @return	boolean true on success.
	 *
	 * @since	1.0.1
	 *
	 * @throws	Exception if an error is encountered.
	 */
	protected static function check($db)
	{
		// Check if the db connector has been set.
		if (!($db instanceof JDatabaseMySQLi))
		{
			throw new Exception('JPLATFORM_ERROR_DATABASE_CONNECTOR_WRONG_TYPE');
			return false;
		}

		return true;
	}

	/**
	 * Get the generic name of the table, converting the database prefix to the wildcard string. Based on Joomla JDatabaseExporter
	 *
	 * @param	string	    $table	The name of the table.
	 *
	 * @return	string			The name of the table with the database prefix replaced with #__.
	 *
	 * @since	1.0.1
	 */
	protected static function getGenericTableName($table)
	{
		$_db	= JFactory::getDbo();

		// get db prefix
		$prefix = $_db->getPrefix();

		// Replace the magic prefix if found.
		$table = preg_replace("|^$prefix|", '#__', $table);

		return $table;
	}

	/**
	 * Writes the XML data file to disc, creates folder to save if not exists
	 *
	 * @param	string		$buffer		The data to write
	 *
	 * @return	boolean		true on success
	 *
	 * @since	1.0.1
	 */
	protected static function writeFile($buffer = '')
	{
		// Import JFolder and JFile object class
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$date		= JFactory::getDate();
		$path		= JFactory::getConfig()->get('tmp_path');
		$file_name	= $path . '/BwPostman_Tables_Server_' . $date->format("Y-m-d_H_i") . '.xml';

		if (!JFolder::exists($path)) {
			echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_FOLDER_NOT_FOUND', $path) . '</p>';
			return false;
		}
		if ($res = JFile::write($file_name, $buffer)) {
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_SUCCESS', $file_name) . '</p>';
		}
		else {
			echo '<p class="bw_tablecheck_error">' . JText::sprintf('COM_BWPOSTMAN_MAINTENANCE_SAVE_TABLES_WRITE_FILE_ERROR', $file_name) . '</p>';
		}
		return $res;
	}
}
