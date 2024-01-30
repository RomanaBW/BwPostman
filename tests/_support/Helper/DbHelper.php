<?php
namespace Helper;
use Codeception\Lib\Driver\Db;
use Codeception\Module;
use Page\Generals;

/** here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
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
 *
 * @since   2.0.0
 */

class DbHelper extends Module
 {
	/**
	 * DbHelper method get list data from database with limit
	 *
	 * @param   string      $table_name     name of the table to get values from
	 * @param   string      $columns        select columns
	 * @param   integer     $archive        archive state
	 * @param   string      $status         where clause for subscribers
	 * @param   string      $order_col      order column
	 * @param   string      $order_dir      order direction
	 * @param   integer     $limit          number of values to get from database
	 * @param   array       $criteria       special criteria, i.e. WHERE
	 * @param   array       $credentials    credentials of database
	 * @param   int         $tab            tab of view, not always needed
	 *
	 * @return  array
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function grabFromDatabaseWithLimit($table_name, $columns, $archive, $status, $order_col, $order_dir, $limit, $criteria = array(), array $credentials, $tab = 1)
	{
		$driver = self::getDbDriver($credentials);
		$special    = 'WHERE `a`.`archive_flag` = ' . $archive;

		if (strpos($table_name, 'newsletters') !== false)
		{
			if ($tab == 1)
			{
				$special    .= " AND `mailing_date` = '" . Generals::$null_date . "'";
			}
			elseif ($tab == 2)
			{
				$special    .= " AND `mailing_date` != '" . Generals::$null_date . "'";
			}
		}

		if (strpos($table_name, 'templates') !== false)
		{
			$special    .= " AND `a`.`id` > '0'";
		}
		if ($status != '')
			$special    .= ' AND `status` = ' . $status;

		if ($order_col != '') {
			$special    .= ' ORDER BY ' . $order_col;
			if ($order_dir != '') {
				$special    .= ' ' . $order_dir;
			}
		}

		if ($limit != 0) {
			$special .= ' LIMIT ' . $limit;
		}

		$query  = "SELECT $columns FROM $table_name $special";

		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}

	/**
	 * DbHelper method to get activation code of subscription
	 *
	 * @param string  $subscriber_mail mail address of subscriber
	 * @param array   $criteria        special criteria, i.e. WHERE
	 * @param   array $credentials     credentials of database
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public static function fetchActivationCode(string $subscriber_mail, array $criteria = array(), array $credentials = array())
	{
		$table_name = Generals::$db_prefix . 'bwpostman_subscribers';

		$driver = self::getDbDriver($credentials);

		$query  = "SELECT `activation` FROM `$table_name` WHERE `email` = '$subscriber_mail';";

		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchColumn();

		return $result;
	}

	/**
	 * DbHelper method to get activation code of user registration
	 *
	 * @param   string      $user_mail          mail address of subscriber
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public static function fetchJoomlaActivationCode($user_mail, $criteria = array(), array $credentials)
	{
		$table_name = Generals::$db_prefix . 'users';

		$driver = self::getDbDriver($credentials);

		$query  = "SELECT `activation` FROM `$table_name` WHERE `email` = '$user_mail';";

		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchColumn();

		return $result;
	}

	/**
	 * DbHelper method to get editlink code of subscription
	 *
	 * @param   string      $subscriber_mail    mail address of subscriber
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public static function fetchEditLink($subscriber_mail, $criteria = array(), array $credentials)
	{
		$table_name = Generals::$db_prefix . 'bwpostman_subscribers';

		$driver = self::getDbDriver($credentials);

		$query  = "SELECT `editlink` FROM `$table_name` WHERE `email` = '$subscriber_mail';";

		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchColumn();
		return $result;
	}

	/**
	 * DbHelper method to get options of BwPostman extension from manifest
	 *
	 * @param   string      $extension          component, module name
	 * @param   array       $credentials        credentials of database
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 *
	 * @return  object      $options            desired options
	 *
	 * @since   2.0.0
	 *
	 * @throws \Exception
	 */
	public static function grabManifestOptionsFromDatabase($extension, array $credentials, $criteria = array())
	{
		$driver = self::getDbDriver($credentials);

		if (strpos($extension, 'mod_') !== false)
		{
			$table_name = Generals::$db_prefix . 'modules';
			$where      = " WHERE `module` = '$extension'";
			$n          = 0;
		}
		else
		{
			$table_name = Generals::$db_prefix . 'extensions';
			$where      = " WHERE `element` = '$extension'";
			$n          = 0;
		}

		$query      = "SELECT `params` FROM $table_name $where";
		$sth        = $driver->executeQuery($query, $criteria);

		$params     = $sth->fetchAll(\PDO::FETCH_ASSOC);
		$options    = json_decode($params[$n]['params']);

		return $options;
	}

	/**
	 * DbHelper method to set options of BwPostman extension from manifest
	 *
	 * @param   string      $extension          component, module, plugin name (element name)
	 * @param   string      $options            option value to update
	 * @param   array       $credentials        credentials of database
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function setManifestOptionsInDatabase($extension, $options, array $credentials, $criteria = array())
	{
		$driver = self::getDbDriver($credentials);

		if (strpos($extension, 'mod_') !== false)
		{
			$table_name = Generals::$db_prefix . 'modules';
			$where      = " WHERE `module` = '$extension'";
		}
	    else
		{
			$table_name = Generals::$db_prefix . 'extensions';
			$where      = " WHERE `element` = '$extension'";
		}

		$query      = "UPDATE $table_name SET `params` = '$options' $where";

		$driver->executeQuery($query, $criteria);
	}

	/**
	 * DbHelper method to update values in given table
	 *
	 * @param   string      $table              the name of the table to update without any prefix
	 * @param   array       $values             array of key = 'value' data to update
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @since   2.0.0
	 */
	public static function updateTable($table, $values, $criteria, array $credentials)
	{
		$driver = self::getDbDriver($credentials);
		$table_name = Generals::$db_prefix . $table;

		$query      = "UPDATE $table_name SET " . implode(', ', $values);
		$driver->executeQuery($query, $criteria);
	}

	/**
	 * DbHelper method to update values in given table
	 *
	 * @param   string      $table              the name of the table to update without any prefix
	 * @param   int         $value              value to set to
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @since   2.0.0
	 */
	public static function resetAutoIncrement($table, $value, array $criteria, array $credentials)
	{
		$driver = self::getDbDriver($credentials);
		$table_name = Generals::$db_prefix . $table;

		$query      = "ALTER TABLE `$table_name` AUTO_INCREMENT = " . $value;
		$driver->executeQuery($query, $criteria);
	}

    /**
     * DbHelper method to truncate session table
     *
     * @param   array       $credentials        credentials of database
     *
     * @since   2.0.0
     */
    public static function truncateSession(array $credentials)
    {
        $criteria   = array();
        $driver     = self::getDbDriver($credentials);
        $table_name = Generals::$db_prefix . 'session';

        $query      = "TRUNCATE TABLE " . $table_name;
        $driver->executeQuery($query, $criteria);
    }

    /**
	 * DbHelper Method to get ID of an extension
	 *
	 * @param string  $extension   component, module name
	 * @param   array $credentials credentials of database
	 *
	 * @return  integer     $id                 ID of the extension
	 *
	 * @since   2.0.0
	 */
	public static function getExtensionIdFromDatabase(string $extension, array $credentials): int
	{
		$criteria   = array();
		$driver = self::getDbDriver($credentials);

		$table_name = Generals::$db_prefix . 'extensions';

		$query      = "SELECT `extension_id` FROM $table_name WHERE `element` = '$extension'";
		$sth        = $driver->executeQuery($query, $criteria);

		$result         = $sth->fetch(\PDO::FETCH_ASSOC);

		return (int)$result['extension_id'];
	}

	/**
	 * DbHelper Method to enabled state of an extension
	 *
	 * @param string  $extension   component, module name
	 * @param   array $credentials credentials of database
	 *
	 * @return  bool     $enabled            enabled of extension
	 *
	 * @since   2.0.0
	 */
	public static function getExtensionEnabledStateFromDatabase(string $extension, array $credentials): bool
	{
		$criteria   = array();
		$driver = self::getDbDriver($credentials);

		$table_name = Generals::$db_prefix . 'extensions';

		$query      = "SELECT `enabled` FROM $table_name WHERE `element` = '$extension'";
		$sth        = $driver->executeQuery($query, $criteria);

		$result         = $sth->fetch(\PDO::FETCH_ASSOC);

		return (boolean)$result['enabled'];
	}

	/**
	 * DbHelper Method to get all table names
	 *
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  string      $names_string       all table names divided by space
	 *
	 * @since   2.0.0
	 */
	public static function getTableNames(array $credentials)
	{
		$tables   = array();

		$query      = "SHOW TABLES LIKE '%bwpostman%'";
		$criteria   = array();
		$driver = self::getDbDriver($credentials);

		$sth        = $driver->executeQuery($query, $criteria);
		$result     = $sth->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($result as $item)
		{
			foreach ($item as $key => $value)
			{
				$tables[] = $value;
			}
		}
		$asset_table  = Generals::$db_prefix . 'assets';
		$tables[]     = $asset_table;

		$names_string = implode(" ", $tables);

		return $names_string;
	}

	/**
	 * DbHelper Method to get group ID by name
	 *
	 * @param   string      $usergroup          name of usergroup
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  int         $group_id
	 *
	 * @since   2.0.0
	 */
	public static function getGroupIdByName($usergroup, array $criteria, array $credentials)
	{
		$driver     = self::getDbDriver($credentials);

		$table_name = Generals::$db_prefix . 'usergroups';
		$query      = "SELECT `id` FROM $table_name WHERE `title` = '$usergroup'";
		$sth        = $driver->executeQuery($query, $criteria);

		$result         = $sth->fetch(\PDO::FETCH_ASSOC);

		return $result['id'];

	}

	/**
	 * DbHelper Method to get rule names by component asset
	 *
	 * @param   string      $extension          name of extension to get rule names for
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  int         $group_id
	 *
	 * @since   2.0.0
	 */
	public static function getRuleNamesByComponentAsset($extension, array $criteria, array $credentials)
	{
		$driver     = self::getDbDriver($credentials);

		$table_name = Generals::$db_prefix . 'assets';
		$query      = "SELECT `rules` FROM $table_name WHERE `name` = '$extension'";
		$sth        = $driver->executeQuery($query, $criteria);

		$result     = $sth->fetch(\PDO::FETCH_ASSOC);

		$return_value   = str_replace("\\", "", $result['rules']);

		return $return_value;

	}

	/**
	 * @param array $credentials
	 *
	 * @return Db
	 *
	 * @since version
	 */
	private static function getDbDriver(array $credentials)
	{
		$driver = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		return $driver;
	}
}
