<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman installation helper
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
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

defined('JPATH_PLATFORM') or die;


/**
 * Component helper class
 *
 * @since  1.2.0
 */
class BwPostmanInstallHelper
{
	/**
	 * Does the database server claim to have support for UTF-8 Multibyte (utf8mb4) collation?
	 *
	 * This is a copy of /administrator/components/com_admin/script.php
	 *
	 * @param   string  $format  The type of database connection.
	 *
	 * @return  boolean
	 *
	 * @since   2.0.0
	 */
	public static function serverClaimsUtf8mb4Support($format)
	{
		$_db = JFactory::getDbo();

		switch ($format)
		{
			case 'mysql':
				$client_version = mysql_get_client_info();
				$server_version = $_db->getVersion();
				break;
			case 'mysqli':
				$client_version = mysqli_get_client_info();
				$server_version = $_db->getVersion();
				break;
			case 'pdomysql':
				$client_version = $_db->getOption(PDO::ATTR_CLIENT_VERSION);
				$server_version = $_db->getOption(PDO::ATTR_SERVER_VERSION);
				break;
			default:
				$client_version = false;
				$server_version = false;
		}
		if ($client_version && version_compare($server_version, '5.5.3', '>='))
		{
			if (strpos($client_version, 'mysqlnd') !== false)
			{
				$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);

				return version_compare($client_version, '5.0.9', '>=');
			}
			else
			{
				return version_compare($client_version, '5.5.3', '>=');
			}
		}

		return false;
	}

	/**
	 * Method to convert tables to utf8mb4 collation and character set
	 *
	 * This is a modified copy of /administrator/components/com_admin/script.php
	 *
	 * @param   string $reference_table     the table to check conversion for
	 * @param   string $conversion_file     name of conversion file
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public static function convertToUtf8Mb4($reference_table = '', $conversion_file = '')
	{
		$_db        = JFactory::getDbo();
		$converted  = false;

		// This is only required for MySQL databases
		$name = $_db->getName();

		if (stristr($name, 'mysql') === false)
		{
			return;
		}

		// check if already converted
		// @Todo: How to solve, if a new table joins up?
		if (self::tablesAreConverted($reference_table))
		{
			return;
		}

		// Check if utf8mb4 is supported
		if (self::serverClaimsUtf8mb4Support($name))
		{
			$converted  = true;
			// Perform the conversion

			if (is_file($conversion_file))
			{
				$fileContents = @file_get_contents($conversion_file);
				$queries      = $_db->splitSql($fileContents);

				if (!empty($queries))
				{
					foreach ($queries as $query)
					{
						try
						{
							$_db->setQuery($query)->execute();
						}
						catch (RuntimeException $e)
						{
							$converted = false;

							// Still render the error message from the Exception object
							JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
						}
					}
				}
			}
		}

		// Show if there was some error
		if ($converted == false)
		{
			// Show an error message telling to check database problems
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_DATABASE_ERROR_DATABASE_UPGRADE_FAILED'), 'error');
		}
	}

	/**
	 * Are the tables already converted?
	 *
	 * @param   string  $test_table     the table to check conversion for
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public static function tablesAreConverted($test_table = '')
	{
		//get database name
		$config     = JFactory::getConfig();
		$dbprefix   = $config->get('dbprefix');
		$table_name = $dbprefix . $test_table;
		$ret        = false;

		$_db = JFactory::getDbo();

		$query  = 'SHOW TABLE STATUS WHERE Name = ' . $_db->quote($table_name);
		$_db->setQuery($query);
		try
		{
			$table_status = $_db->loadAssoc();

			if ($table_status['Collation'] == 'utf8mb4_unicode_ci')
			{
				$ret = true;
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $ret;
	}
}
