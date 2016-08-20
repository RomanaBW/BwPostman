<?php
namespace Helper;
use Codeception;
use Page\Generals;

/**
 * here you can define custom actions
 *all public methods declared in helper class will be available in $I
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
class Acceptance extends Codeception\Module
{
	/**
	 * Method to fill database with test data before tests are processed
	 *
	 * @since   2.0.0
	 */
	public function _getQueryBase()
	{
		return "mysql -u " . Generals::$db_user . " -p" . Generals::$db_pw . " " . Generals::$db_db . " < " . Generals::$db_data_path;
	}

	/**
	 * Method to fill database with test data before tests are processed
	 *
	 * @since   2.0.0
	 */
	public function _getBackupQuery()
	{
		$credentials    = $this->_getDbCredentials();

		$command    = "mysqldump -u " . Generals::$db_user . " -p" . Generals::$db_pw;
		$options    = "  --skip-add-drop-table --single-transaction ";
		$database   = Generals::$db_db;
		$special    = " | sed -r 's/CREATE TABLE (`[^`]+`)/TRUNCATE TABLE \\1;\\nCREATE TABLE IF NOT EXISTS \\1/g'";
		$target     = " > " . Generals::$db_data_path;

		$tables     = DbHelper::getTableNames($credentials);

		$query   = $command . $options . $database . " " . $tables . $special . $target;

		return $query;
	}
	/**
	 * Method to fill database with test data before tests are processed
	 *
	 * @since   2.0.0
	 */
	public function _beforeSuite($I)
	{
		$query_base     = self::_getQueryBase();
		$backup_query   = self::_getBackupQuery();

		// connect to server
		$connection = ssh2_connect(Generals::$ssh_server, Generals::$ssh_port, Generals::$ssh_options);
		ssh2_auth_pubkey_file($connection, Generals::$ssh_user, Generals::$ssh_key_pub, Generals::$ssh_key_rsa);

		// backup dev tables
		ssh2_exec($connection, $backup_query . Generals::$db_data_end);

		// inject test data
		ssh2_exec($connection, $query_base . Generals::$db_data_start);

		// get component options
		$options    = $this->getManifestOptions('com_bwpostman');
		Generals::setComponentOptions($options);
	}

	/**
	 * Method to truncate database after tests are done
	 *
	 * @since   2.0.0
	 */
	public function _afterSuite()
	{
		$query_base = self::_getQueryBase();

		// connect to server
		$connection = ssh2_connect(Generals::$ssh_server, Generals::$ssh_port, Generals::$ssh_options);
		ssh2_auth_pubkey_file($connection, Generals::$ssh_user, Generals::$ssh_key_pub, Generals::$ssh_key_rsa);

		// restore dev tables
		ssh2_exec($connection, $query_base . Generals::$db_data_end);
	}

	/**
	 * Method to change browser
	 *
	 * @param $browser
	 *
	 * @since   2.0.0
	 */
	public function changeBrowser($browser)
	{
		$this->getModule('WebDriver')->_reconfigure(array('browser' => $browser));
	}

	/**
	 * Method to check if an element exists at page
	 *
	 * @param   \AcceptanceTester   $I
	 * @param  string               $element
	 *
	 * @return bool                 true on success
	 *
	 * @since   2.0.0
	 */
	public function elementExists(\AcceptanceTester $I, $element = '')
	{
		try
		{
			$I->seeElement($element);
		}
		catch (\PHPUnit_Framework_AssertionFailedError $f)
		{
			return false;
		}
		return true;
	}

	/**
	 * Helper method get table rows in list view
	 *
	 * @param   \AcceptanceTester $I
	 *
	 * @return  array   $rows
	 *
	 * @since   2.0.0
	 */
	public function GetTableRows(\AcceptanceTester $I)
	{
		// get all table rows
		$rows  = $I->grabMultiple('tr');

		// remove table header if exists
		if (self::elementExists($I, 'thead'))
		{
			array_shift($rows);
		}

		// remove table footer if exists
		if (self::elementExists($I, 'tfoot'))
		{
			array_shift($rows);
		}

		// remove empty elements
		$rows   = array_filter($rows);

		$result = str_replace(array("\r\n", "\n", "\r"), ' ', $rows);

		return $result;
	}

	/**
	 * Helper method get list length in list view
	 *
	 * @param   \AcceptanceTester $I
	 *
	 * @return  int
	 *
	 * @since   2.0.0
	 */
	public function GetListLength(\AcceptanceTester $I)
	{
		$rcount   = count($I->GetTableRows($I));
		return $rcount;
	}

	/**
	 * Helper method get list data from database
	 *
	 * @param   string  $table_name name of the table to get values from
	 * @param   string  $columns    select columns
	 * @param   int     $archive    archive state
	 * @param   string  $status     where clause for subscribers
	 * @param   string  $order_col  order column
	 * @param   string  $order_dir  order direction
	 * @param   integer $limit      number of values to get from database
	 * @param   array   $criteria   special criteria, i.e. WHERE
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public function GetListData($table_name = 'mailinglists', $columns = '*', $archive = 0, $status = '', $order_col = '', $order_dir = 'ASC', $limit = 20, $criteria = array())
	{
		$credentials    = $this->_getDbCredentials();
		$join           = '';

		if (strpos($table_name, 'mailinglists') !== false)
		{ // Join over the asset groups.
			$columns .= ', ag.title AS access_level';
			$join .= ' LEFT JOIN ' . Generals::$db_prefix . 'viewlevels AS ag ON ag.id = a.access';
		}

		if (strpos($table_name, 'newsletters') !== false)
		{ // Join over the users for the authors.
			$columns .= ', ua.name AS authors';
			$join .= ' LEFT JOIN ' . Generals::$db_prefix . 'users AS ua ON ua.id = a.created_by';

			// Join over campaigns for campaign title.
			$columns .= ', c.title AS campaign_id';
			$join .= ' LEFT JOIN ' . Generals::$db_prefix . 'bwpostman_campaigns AS c ON c.id = a.campaign_id';
		}

		$table_name  = Generals::$db_prefix . 'bwpostman_' . $table_name;
		$table_name .= $join;

		$result = DbHelper::grabFromDatabaseWithLimit($table_name, $columns, $archive, $status, $order_col, $order_dir, $limit, $criteria, $credentials);
		return $result;
	}

	/**
	 * Helper method substitute access
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	private function _SubstituteAccess($data = array())
	{
		$result     = array();
		$usergroups = $this->_getUsergroups();
		foreach ($data as $item)
		{
			$dataset = array();
			foreach ($item as $key => $value)
			{
				if ($key == 'access')
				{
					$value = $usergroups[$value];
				}
				$dataset[$key] = $value;
			}
			$result[] = $dataset;
		}

		return $result;
	}

	/**
	 * Helper method substitute gender
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	private function _SubstituteGender($data = array())
	{
		$result     = array();
		$gender     = array('male', 'female');
		foreach ($data as $item)
		{
			$dataset = array();
			foreach ($item as $key => $value)
			{
				if ($key == 'gender')
				{
					if ($value != '')
					{
						$value = $gender[$value];
					}
					else
					{
						$value = '';
					}
				}
				$dataset[$key] = $value;
			}
			$result[] = $dataset;
		}

		return $result;
	}

	/**
	 * Helper method substitute mail format
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	private function _SubstituteMailformat($data = array())
	{
		$result     = array();
		$format     = array('Text', 'HTML');
		foreach ($data as $item)
		{
			$dataset = array();
			foreach ($item as $key => $value)
			{
				if ($key == 'emailformat')
				{
					$key   = 'Email format';
					$value = $format[$value];
				}
				$dataset[$key] = $value;
			}
			$result[] = $dataset;
		}

		return $result;
	}

	/**
	 * Helper method substitute template format
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	private function SubstituteTemplateFormat($data = array())
	{
		$result     = array();

		foreach ($data as $item)
		{
			$dataset = array();
			foreach ($item as $key => $value)
			{
				if ($key == 'tpl_id')
				{
					if ($value < 998)
					{
						$value = 'HTML';
					}
					else
					{
						$value = 'TEXT';
					}
				}
				$dataset[$key] = $value;
			}
			$result[] = $dataset;
		}

		return $result;
	}

	/**
	 * DbHelper method get query for number of subscribers per mailinglist
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	private function _getUsergroups()
	{
		$credentials = $this->_getDbCredentials();
		$criteria    = array();
		$driver      = new Codeception\Lib\Driver\Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query = 'SELECT `id`, `title` FROM `' . Generals::$db_prefix . 'viewlevels`';

		$sth = $driver->executeQuery($query, $criteria);
		$res = $sth->fetchAll(\PDO::FETCH_ASSOC);;

		$groups = array();
		foreach ($res as $item)
		{
			$groups[$item['id']] = $item['title'];
		}

		return $groups;
	}

	/**
	 * DbHelper method get query for number of subscribers per mailinglist
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public function getQueryNumberOfSubscribers()
	{
		// Build sub queries which counts the subscribers of each mailinglists
		$sub_query = 'SELECT `d`.`id` FROM `' . Generals::$db_prefix . 'bwpostman_subscribers` AS `d` WHERE `d`.`archive_flag` = 0';

		$query = '(SELECT COUNT(`b`.`subscriber_id`) AS `subscribers`';
		$query .= ' FROM `' . Generals::$db_prefix . 'bwpostman_subscribers_mailinglists` AS `b`';
		$query .= ' WHERE `b`.`mailinglist_id` = `a`.`id`';
		$query .= ' AND `b`.`subscriber_id` IN (' . $sub_query . ')) AS `subscribers`';

		return $query;
	}

	/**
	 * DbHelper method get query for number of newsletters per campaigns
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public function getQueryNumberOfNewsletters()
	{
		// Build sub query which counts the newsletters  of each campaign
		$query = '(SELECT COUNT(`b`.`id`)';
		$query .= ' FROM `' . Generals::$db_prefix . 'bwpostman_newsletters` AS `b`';
		$query .= ' WHERE `b`.`campaign_id` = `a`.`id`';
		$query .= ' AND `b`.`archive_flag` = 0) AS `newsletters`';

		return $query;
	}

	/**
	 * DbHelper method get query for number of mailinglists per subscriber
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public function getQueryNumberOfMailinglists()
	{
		// Build sub queries which counts the mailinglists of each subscriber
		$sub_query = 'SELECT `d`.`id` FROM `' . Generals::$db_prefix . 'bwpostman_subscribers` AS `d` WHERE `d`.`archive_flag` = 0';

		$query = '(SELECT COUNT(`b`.`mailinglist_id`) AS `mailinglists`';
		$query .= ' FROM `' . Generals::$db_prefix . 'bwpostman_subscribers_mailinglists` AS `b`';
		$query .= ' WHERE `b`.`subscriber_id` = `a`.`id`';
		$query .= ' AND `b`.`subscriber_id` IN (' . $sub_query . ')) AS `mailinglists`';

		return $query;
	}

	/**
	 * Method to get database credentials from configuration yml
	 *
	 * @return mixed
	 *
	 * @since   2.0.0
	 */
	private function _getDbCredentials()
	{
		$_db = $this->getModule('Db');

		$credentials['dsn']      = $_db->_getConfig('dsn');
		$credentials['user']     = $_db->_getConfig('user');
		$credentials['password'] = $_db->_getConfig('password');

		return $credentials;
	}

	/**
	 * Method to get activation code from database
	 *
	 * @param   string $subscriber_mail mail address of subscriber
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */

	public function getActivationCode($subscriber_mail)
	{
		$credentials = self::_getDbCredentials();

		$result = DbHelper::fetchActivationCode($subscriber_mail, $criteria = array(), $credentials);

		return $result;
	}

	/**
	 * Method to get editlink code from database
	 *
	 * @param   string $subscriber_mail mail address of subscriber
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */

	public function getEditlinkCode($subscriber_mail)
	{
		$credentials    = self::_getDbCredentials();
		$result         = DbHelper::fetchEditLink($subscriber_mail, $criteria = array(), $credentials);

		return $result;
	}

	/**
	 * Helper method to loop over filters
	 *
	 * @param \AcceptanceTester     $I              tester object
	 * @param object                $ManageData     manage data (per section)
	 * @param string                $manner         header or select list
	 * @param array                 $columns        columns for query
	 * @param string                $table          table of section
	 * @param integer               $archive        archived items or not?
	 * @param string                $status         published or not? Leave empty, if status not given in table
	 * @param integer               $loop_counts    how many sort criteria are given?
	 *
	 * @since   2.0.0
	 */
	public function loopFilterList(\AcceptanceTester $I, $ManageData, $manner, $columns, $table, $archive, $status, $loop_counts  = 0)
	{
		// Get list length
		$list_length = $I->GetListLength($I);

		// loop over sorting criterion
		$i = 2;
		if (strpos($table, 'newsletters') !== false)
		{
			$i = 1;
		}
		if (strpos($table, 'templates') !== false)
		{
			$i = 3;
		}
		foreach ($ManageData::$sort_criteria as $key => $criterion)
		{
			foreach (Generals::$sort_orders as $order)
			{
				if ($order == 'ascending')
				{
					$i++;
					if ((strpos($table, 'templates') !== false) && ($i == 5))
					{
						$i = 6;
					}

					if ($i == $loop_counts)
					{
						$i = 2;
					}
					$db_order = 'ASC';
					$arrow    = 'up';
				}
				else
				{
					$db_order = 'DESC';
					$arrow    = 'down';
				}

				$row_values_raw = $I->GetListData($table, $columns, $archive, $status, $ManageData::$select_criteria[$key], $db_order, $list_length);
				if ($key == 'access')
				{
					$row_values = self::_SubstituteAccess($row_values_raw);
				}
				elseif ($key == 'gender')
				{
					$row_values = self::_SubstituteGender($row_values_raw);
				}
				elseif ($key == 'Email format')
				{
					$row_values = self::_SubstituteMailformat($row_values_raw);
				}
				elseif ($key == 'tpl_id')
				{
					$row_values = self::SubstituteTemplateFormat($row_values_raw);
				}
				else
				{
					$row_values = $row_values_raw;
				}

				$row_values_nominal = array();
				foreach ($row_values as $row)
				{
					$row_values_nominal[] = str_replace(array("\r\n", "\n", "\r", "<br />"), ' ', $row);
				}

				if ($manner == 'header')
				{
					$I->click(sprintf(Generals::$table_headcol_link_location, $i));
				}
				else
				{
					$I->clickSelectList(Generals::$ordering_list, Generals::$ordering_value . $ManageData::$sort_criteria_select[$key] . " " . $order . "']");

				}
				$I->expectTo('see arrow ' . $arrow . ' at ' . $criterion);
				$I->seeElement(sprintf(Generals::$table_headcol_arrow_location, $i), array('class' => Generals::$sort_arrows[$arrow]));
				$I->expectTo('see text ' . $ManageData::$sort_criteria_select[$key] . ' ' . $order);
				$I->see($ManageData::$sort_criteria_select[$key] . ' ' . $order, Generals::$select_list_selected_location);

				// loop over column values
				$row_values_actual = self::GetTableRows($I);

				for ($k = 0; $k < $list_length; $k++)
				{
					$needle     = $row_values_nominal[$k][$key];
					$haystack   = $row_values_actual[$k];

					switch ($key)
					{
						case 'published':
							$col = 4;
								if (strpos($table, 'templates') !== false)
								{
									$col = 6;
								}
								if ($needle == '1')
								{
									$I->seeElement(sprintf(Generals::$publish_row, ($k + 1), $col));
								}
								else
								{
									$I->seeElement(sprintf(Generals::$unpublish_row, ($k + 1), $col));
								}
							break;
						case 'attachment';
								if ($needle != '')
								{
									$I->seeElement(sprintf(Generals::$attachment_row, ($k + 1)));
								}
							break;
						case 'modified_time':
							if ($needle == Generals::$null_date)
							{
								//do nothing;
							}
							break;
						case 'gender';
						case 'authors';
						case 'campaign_id':
							if (($needle == '') || ($needle == null))
							{
								//do nothing;
							}
							break;
						default:
							if (!($key == 'user_id') || ($needle != '0'))
							{
								$I->assertContains($needle, $haystack);
							}
					} // end key switch
				} //end row loop
			} // end foreach sort dir
		} // end foreach sort order
	}

	/**
	 * Helper method to filter by status
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public function filterByStatus(\AcceptanceTester $I)
	{
		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select published
		$I->clickSelectList(Generals::$status_list, Generals::$status_published);

		$I->dontSeeElement(Generals::$icon_unpublished);

		// select unpublished
		$I->clickSelectList(Generals::$status_list, Generals::$status_unpublished);

		$I->dontSeeElement(Generals::$icon_published);
	}

	/**
	 * Helper method to publish by icon
	 *
	 * @param \AcceptanceTester $I
	 * @param object            $ManageData
	 * @param string            $item
	 *
	 * @since   2.0.0
	 */
	public function publishByIcon(\AcceptanceTester $I, $ManageData, $item)
	{
		// switch status by icon
		$I->clickAndWait($ManageData::$publish_button, 2);
		$I->see("One " . $item . " published!");
		$I->seeElement($ManageData::$publish_result);

		$I->clickAndWait($ManageData::$unpublish_button,1);
		$I->see("One " . $item . " unpublished!");
		$I->seeElement($ManageData::$unpublish_result);
	}

	/**
	 * Helper method to publish by toolbar
	 *
	 * @param \AcceptanceTester $I
	 * @param object            $ManageData
	 * @param string            $item
	 *
	 * @since   2.0.0
	 */
	public function publishByToolbar(\AcceptanceTester $I, $ManageData, $item)
	{
		// switch status by toolbar
		$I->wait(2);
		$I->click($ManageData::$publish_button2);
		$I->clickAndWait(Generals::$toolbar['Publish'], 1);
		$I->see("One " . $item . " published!");
		$I->seeElement($ManageData::$publish_result2);

		$I->click($ManageData::$unpublish_button2);
		$I->clickAndWait(Generals::$toolbar['Unpublish'], 1);
		$I->see("One " . $item . " unpublished!");
		$I->seeElement($ManageData::$unpublish_result2);
	}

	/**
	 * Helper method to check pagination
	 *
	 * @param \AcceptanceTester $I
	 * @param object            $ManageData
	 * @param int               $listlenght
	 *
	 * @since   2.0.0
	 */
	public function checkPagination(\AcceptanceTester $I, $ManageData, $listlenght)
	{
		if (isset($ManageData::$p1_val1))
		{
			$I->assertEquals($listlenght, count(self::GetTableRows($I)));
			$this->_browsePages($I, $ManageData::$p1_val1, $ManageData::$p1_field1, $ManageData::$p1_val_last, $ManageData::$p1_field_last);
		}

		if (isset($ManageData::$p2_val1))
		{
			$I->clickAndWait(Generals::$next_page,1);
			$this->_browsePages($I, $ManageData::$p2_val1, $ManageData::$p2_field1, $ManageData::$p2_val_last, $ManageData::$p2_field_last);
		}

		if (isset($ManageData::$p_last_val1))
		{
			$I->clickAndWait(Generals::$last_page,1);
			$this->_browsePages($I, $ManageData::$p_last_val1, $ManageData::$p_last_field1, $ManageData::$p_last_val_last, $ManageData::$p_last_field_last);
		}

		if (isset($ManageData::$p_prev_val1))
		{
			$I->clickAndWait(Generals::$prev_page,1);
			$this->_browsePages($I, $ManageData::$p_prev_val1, $ManageData::$p_prev_field1, $ManageData::$p_prev_val_last, $ManageData::$p_prev_field_last);
		}

		if (isset($ManageData::$p1_val1))
		{
			$I->clickAndWait(Generals::$first_page, 1);
			$this->_browsePages($I, $ManageData::$p1_val1, $ManageData::$p1_field1, $ManageData::$p1_val_last, $ManageData::$p1_field_last);
		}

		if (isset($ManageData::$p3_val1))
		{
			$I->clickAndWait(Generals::$page_3, 1);
			$this->_browsePages($I, $ManageData::$p3_val1, $ManageData::$p3_field1, $ManageData::$p3_val3, $ManageData::$p3_field3);
		}
	}

	/**
	 * Helper method to check list limit
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public function checkListlimit(\AcceptanceTester $I)
	{
		$I->assertEquals(20, count($I->GetTableRows($I)));

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_5);
		$I->assertEquals(5, count($I->GetTableRows($I)));

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_15);
		$I->assertEquals(15, count($I->GetTableRows($I)));

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_20);
		$I->assertEquals(20, count($I->GetTableRows($I)));

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10);
		$I->assertEquals(10, count($I->GetTableRows($I)));
	}

	/**
	 * Helper method to check filter by access
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public function filterByAccess(\AcceptanceTester $I)
	{
		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select public
		$I->clickSelectList(Generals::$access_list, Generals::$access_public);

		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select guest
		$I->clickSelectList(Generals::$access_list, Generals::$access_guest);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select registered
		$I->clickSelectList(Generals::$access_list, Generals::$access_registered);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select special
		$I->clickSelectList(Generals::$access_list, Generals::$access_special);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select super users
		$I->clickSelectList(Generals::$access_list, Generals::$access_super);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
	}

	/**
	 * Helper method to check filter by access
	 *
	 * @param \AcceptanceTester $I
	 * @param object            $ManageData
	 * @param bool              $exact
	 *
	 * @since   2.0.0
	 */
	public function searchLoop(\AcceptanceTester $I, $ManageData, $exact = true)
	{
		// loop search value
		for ($j = 0; $j < count($ManageData::$search_val); $j++)
		{
			// loop search by
			$I->fillField(Generals::$search_field, $ManageData::$search_val[$j]);
			for ($i = 0; $i < count($ManageData::$search_by); $i++)
			{
				// Get filter bar
				$I->clickAndWait(Generals::$filterbar_button,1);

				// open 'search by' list, select 'search by' value
				$I->clickSelectList( Generals::$search_list, $ManageData::$search_by[$i]);
				// click search button
				$I->clickAndWait(Generals::$search_button,2);
				// check result
				if ((int) $ManageData::$search_res[$j][$i] == 0)
				{
					$I->see(Generals::$null_msg, Generals::$null_row);
				}
				elseif ($exact)
				{
					$I->assertTableSearchResult($ManageData::$search_val[$j], (int) $ManageData::$search_res[$j][$i]);
				}
			}
		}
	}

	/**
	 * Helper method archive and delete items, specified by EditData
	 *
	 * @param   \AcceptanceTester               $I
	 * @param   object                          $ManageData
	 * @param   object                          $EditData
	 *
	 * @before  _login
	 *
	 * @depends CreateOneMailinglistCompleteMainView
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function HelperArcDelItems(\AcceptanceTester $I, $ManageData, $EditData)
	{
		// ensure we are on the section list page
		$I->see($ManageData::$section, Generals::$pageTitle);

		// select items to archive
		$I->fillField(Generals::$search_field, $EditData::$field_title);
		$I->clickAndWait(Generals::$filterbar_button,1);
		$I->clickSelectList( Generals::$search_list, $EditData::$archive_identifier);
		$I->clickAndWait(Generals::$search_button,1);
		$I->see($EditData::$field_title, $EditData::$archive_title_col);

		//count items
		$count  = $I->GetListLength($I);

		// archive items
		$I->checkOption(Generals::$check_all_button);
		$I->clickAndWait($EditData::$archive_button, 1);

		if ($ManageData::$section == 'template')
		{
			// process confirmation popup
			$I->seeInPopup($EditData::$archive_confirm);
			$I->acceptPopup();
		}
		elseif ($ManageData::$section == 'campaigns')
		{
			// process newsletter popup
			$I->switchToIFrame($ManageData::$popup_archive_iframe);
			$I->see($ManageData::$popup_archive_newsletters);
			$I->clickAndWait($ManageData::$popup_button_no, 1);
			$I->switchToIFrame();
		}

		// see message archived
		$I->waitForElement(Generals::$alert_header);
		$I->see(Generals::$alert_msg_txt, Generals::$alert_header);
		if ($count == 1)
		{
			$I->see($EditData::$archive_success_msg, Generals::$alert_success);
		}
		else
		{
			$I->see($EditData::$archive_success2_msg, Generals::$alert_success);
		}

		// switch to archive
		$I->amOnPage(Generals::$archive_url);
		$I->see(Generals::$archive_txt, Generals::$pageTitle);
		$I->click($EditData::$archive_tab);

		// select items to delete
		$I->fillField(Generals::$search_field, $EditData::$field_title);
		$I->clickAndWait(Generals::$filterbar_button,1);
		$I->clickSelectList( Generals::$search_list, $EditData::$delete_identifier);
		$I->clickAndWait(Generals::$search_button,1);
		$I->see($EditData::$field_title);

		//count items
		$count  = $I->GetListLength($I);

		$I->checkOption(Generals::$check_all_button);
		$I->clickAndWait($EditData::$delete_button, 1);

		if ($ManageData::$section == 'campaigns')
		{
			$I->switchToIFrame($ManageData::$popup_delete_iframe);
			$I->see($ManageData::$popup_delete_newsletters);
			$I->clickAndWait($ManageData::$popup_button_no, 1);
			$I->switchToIFrame();
		}
		else
		{
			// process confirmation popup
			$I->seeInPopup($EditData::$remove_confirm);
			$I->acceptPopup();
		}

		// see message deleted
		$I->waitForElement(Generals::$alert_header);
		$I->see(Generals::$alert_msg_txt, Generals::$alert_header);
		if ($count == 1)
		{
			$I->see($EditData::$success_remove, Generals::$archive_alert_success);
		}
		else
		{
			$I->see($EditData::$success_remove2, Generals::$archive_alert_success);
		}
		$I->dontSee($EditData::$field_title);

		// return to campaigns
		$I->waitForElement(Generals::$alert_header);
		$I->amOnPage($ManageData::$url);
		$I->see($ManageData::$section, Generals::$pageTitle);
	}

	/**
	 * Method to get options of specified extension
	 *
	 * @param   string      $extension      the extension to get the options for
	 *
	 * @return  object      $options
	 *
	 * @since   2.0.0
	 */
	public function getManifestOptions($extension)
	{
		$credentials    = $this->_getDbCredentials();
		$criteria       = array();

		$options = DbHelper::grabManifestOptionsFromDatabase($extension, $criteria, $credentials);

		if ($extension == 'mod_bwpostman' && property_exists($options, 'com_params'))
		{
			if ($options->com_params)
			{
				$extension = 'com_bwpostman';
				$options = DbHelper::grabManifestOptionsFromDatabase($extension, $criteria, $credentials);
			}
		}

		return $options;
	}

	/**
	 * Method to set options of specified extension
	 *
	 * @param   string      $extension      the extension to set the option for
	 * @param   string      $option         the option to update
	 * @param   string      $value          the new value for this option
	 *
	 * @since   2.0.0
	 */
	public function setManifestOption($extension = 'com_bwpostman', $option, $value)
	{
		$credentials    = $this->_getDbCredentials();
		$criteria       = array();
		$options        = Generals::$com_options;

		$options->$option   = $value;

		$options_string = json_encode($options);

		DbHelper::setManifestOptionsFromDatabase($extension, $options_string, $criteria, $credentials);
	}

	/**
	 * Updates an SQL record into a database. This record will **not** be reset after the test.
	 *
	 * ``` php
	 * <?php
	 * $I->updateInDatabase('users', array('email' => 'miles@davis.com'), array('name' => 'miles'));
	 * ?>
	 * ```
	 *
	 * @param   string      $table              the name of table to update
	 * @param   array       $data               array of key-value pairs to update
	 * @param   array       $where_condition
	 *
	 * @return integer $id
	 *
	 * @since   2.0.0.
	 */
	public function updateInDatabase($table, array $data, $where_condition)
	{
		$credentials    = $this->_getDbCredentials();
		$criteria       = array();
		$values         = array();

		foreach ($data as $key => $value)
		{
			$values[]   = $key . " = '" . $value . "'";
		}

		foreach ($where_condition as $key => $value)
		{
			$criteria[]   = "WHERE " . $key . " = '" . $value . "'";
		}

		DbHelper::updateTable($table, $values, $criteria, $credentials);
	}

	/**
	 * Method to get ID of an extension
	 *
	 * @param   string      $extension      the extension to set the option for
	 *
	 * @return  integer     $id             ID of the extension
	 *
	 * @since   2.0.0
	 */
	private function _getExtensionId($extension = 'com_bwpostman')
	{
		$credentials    = $this->_getDbCredentials();

		$id = DbHelper::getExtensionIdFromDatabase($extension, $credentials);

		return $id;
	}

	/**
	 * Method to set component id of the extension for a specified menu entry
	 *
	 * @param   string      $title         the title of the menu entry
	 *
	 * @since   2.0.0
	 */
	public function setComponentIdInMenu($title)
	{
		$id     = $this->_getExtensionId('com_bwpostman');

		$data   = array('component_id' => $id);
		$where  = array('title' => $title);
		$this->updateInDatabase('menu', $data, $where);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $top_val            value of first list entry
	 * @param string            $top_val_field      identifier for first list entry
	 * @param string            $last_val           value of last list entry
	 * @param string            $last_val_field     identifier for last list entry
	 *
	 *
	 * @since version
	 */
	private function _browsePages(\AcceptanceTester $I, $top_val, $top_val_field, $last_val, $last_val_field)
	{
		$I->scrollTo(Generals::$table_header);
		$I->see($top_val, $top_val_field);
		$I->scrollTo(Generals::$pagination_bar);
		$I->see($last_val, $last_val_field);
	}

}

