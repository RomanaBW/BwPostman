<?php
namespace Helper;
use Codeception\Lib\Driver\Db;
use Codeception\Module;
use Page\Generals;

/** here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
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
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public static function grabFromDatabaseWithLimit($table_name, $columns, $archive, $status, $order_col, $order_dir, $limit, $criteria = [], array $credentials)
	{
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);
		$special    = 'WHERE `a`.`archive_flag` = ' . $archive;

		if (strpos($table_name, 'newsletters') !== false)
		{
			$special    .= " AND `mailing_date` = '0000-00-00 00:00:00'";
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
//codecept_debug('Whole list query:');
//codecept_debug($query);
		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchAll(\PDO::FETCH_ASSOC);
//codecept_debug($result);
		return $result;
	}

	/**
	 * DbHelper method to get activation code of subscription
	 *
	 * @param   string      $subscriber_mail    mail address of subscriber
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public static function fetchActivationCode($subscriber_mail, $criteria = array(), array $credentials)
	{
		$table_name = Generals::$db_prefix . 'bwpostman_subscribers';

		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

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

		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query  = "SELECT `activation` FROM `$table_name` WHERE `email` = '$user_mail';";
//codecept_debug('Query');
//codecept_debug($query);

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
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public static function fetchEditLink($subscriber_mail, $criteria = array(), array $credentials)
	{
		$table_name = Generals::$db_prefix . 'bwpostman_subscribers';

		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query  = "SELECT `editlink` FROM `$table_name` WHERE `email` = '$subscriber_mail';";
//		codecept_debug('Query:');
//		codecept_debug($query);
		$sth    = $driver->executeQuery($query, $criteria);

		$result = $sth->fetchColumn();
//		codecept_debug($result);
		return $result;
	}

	/**
	 * DbHelper method to get options of BwPostman extension from manifest
	 *
	 * @param   string      $extension          component, module name
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  object      $options            desired options
	 *
	 * @since   2.0.0
	 */
	public static function grabManifestOptionsFromDatabase($extension, $criteria = array(), array $credentials)
	{
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		if (strpos($extension, 'mod_') !== false)
		{
			$table_name = Generals::$db_prefix . 'modules';
//			$criteria[] = "WHERE `module` = '$extension'";
			$where      = " WHERE `module` = '$extension'";
			$n          = 26;
		}
		else
		{
			$table_name = Generals::$db_prefix . 'extensions';
//			$criteria[] = "WHERE `element` = '$extension'";
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
	 * @param   array       $criteria           special criteria, i.e. WHERE
	 * @param   array       $credentials        credentials of database
	 *
	 * @since   2.0.0
	 */
	public static function setManifestOptionsInDatabase($extension, $options, $criteria = array(), array $credentials)
	{
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		if (strpos($extension, 'mod_') !== false)
		{
			$table_name = Generals::$db_prefix . 'modules';
//			$criteria[] = "WHERE `module` = '$extension'";
			$where      = " WHERE `module` = '$extension'";
		}
	else
		{
			$table_name = Generals::$db_prefix . 'extensions';
//			$criteria[] = "WHERE `element` = '$extension'";
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
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);
		$table_name = Generals::$db_prefix . $table;

//codecept_debug('Arrived in DbHelper');
		$query      = "UPDATE $table_name SET " . implode(', ', $values);
		$driver->executeQuery($query, $criteria);
	}

	/**
	 * DbHelper Method to get ID of an extension
	 *
	 * @param   string      $extension          component, module name
	 * @param   array       $credentials        credentials of database
	 *
	 * @return  integer     $id                 ID of the extension
	 *
	 * @since   2.0.0
	 */
	public static function getExtensionIdFromDatabase($extension, array $credentials)
	{
		$criteria   = array();
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$table_name = Generals::$db_prefix . 'extensions';

		$query      = "SELECT `extension_id` FROM $table_name WHERE `element` = 'com_bwpostman'";
		$sth        = $driver->executeQuery($query, $criteria);

		$result         = $sth->fetch(\PDO::FETCH_ASSOC);

		return $result['extension_id'];
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
/*
		$tables_t     = array();
		$tables_t[]   = "jos_bwpostman_campaigns";
		$tables_t[]   = "jos_bwpostman_campaigns_mailinglists";
		$tables_t[]   = "jos_bwpostman_mailinglists";
		$tables_t[]   = "jos_bwpostman_newsletters";
		$tables_t[]   = "jos_bwpostman_newsletters_mailinglists";
		$tables_t[]   = "jos_bwpostman_sendmailcontent";
		$tables_t[]   = "jos_bwpostman_sendmailqueue";
		$tables_t[]   = "jos_bwpostman_subscribers";
		$tables_t[]   = "jos_bwpostman_subscribers_mailinglists";
		$tables_t[]   = "jos_bwpostman_tc_campaign";
		$tables_t[]   = "jos_bwpostman_tc_sendmailcontent";
		$tables_t[]   = "jos_bwpostman_tc_sendmailqueue";
		$tables_t[]   = "jos_bwpostman_templates";
		$tables_t[]   = "jos_bwpostman_templates_tpl";
*/
		$query      = "SHOW TABLES LIKE '%bwpostman%'";
		$criteria   = array();
		$driver     = new Db($credentials['dsn'], $credentials['user'], $credentials['password']);

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
}
