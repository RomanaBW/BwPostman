<?php
namespace Helper;

use Codeception;
use Exception;
use Page\Generals;

/**
 * here you can define custom actions
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
class Acceptance extends Codeception\Module
{
	/**
	 * Method to change browser
	 *
	 * @param $browser
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function changeBrowser($browser)
	{
		$this->getModule('WebDriver')->_reconfigure(array('browser' => $browser));
	}

	/**
	 * Helper to press escape on browser dialog
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.1.0
	 */
	public function pressEscapeKey()
	{
		$escapeKey = \Facebook\WebDriver\WebDriverKeys::ESCAPE;
		$this->getModule('WebDriver')->webDriver->getKeyboard()->sendKeys([$escapeKey]);
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
	 * @param   string            $tableIdentifier
	 *
	 * @return  array   $rows
	 *
	 * @since   2.0.0
	 */
	public function GetTableRows(\AcceptanceTester $I, $tableIdentifier = "//table[@id='main-table']")
	{
		$rowsIdentifier = $tableIdentifier . '/tbody/tr';

		// get all table rows
		$rows  = $I->grabMultiple($rowsIdentifier);

		// remove empty elements
		$filteredRows = array_filter($rows);

		// remove new lines and line breaks
		$cleanedRows = str_replace(array("\r\n", "\n", "\r"), ' ', $filteredRows);

		// reindexing of table
		$result = array_slice($cleanedRows, 0);

		return $result;
	}

	/**
	 * Helper method get list length in list view
	 *
	 * @param   \AcceptanceTester $I
	 * @param   string            $tableIdentifier
	 *
	 * @return  integer
	 *
	 * @since   2.0.0
	 */
	public function GetListLength(\AcceptanceTester $I, $tableIdentifier = "//table[@id='main-table']")
	{
		$rows = $I->GetTableRows($I, $tableIdentifier);
		$row_count   = count($rows);
		return $row_count;
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
	 * @param   int         $tab            tab of view, not always needed
	 *
	 * @return  array
	 *
	 *
	 * @throws Exception
	 * @since   2.0.0
	 */
	public function GetListData(
		$table_name = 'mailinglists',
		$columns = '*',
		$archive = 0,
		$status = '',
		$order_col = '',
		$order_dir = 'ASC',
		$limit = 20,
		$criteria = array(), $tab = 1
	)
	{
		$credentials    = $this->getDbCredentials();
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

		$result = DbHelper::grabFromDatabaseWithLimit(
			$table_name,
			$columns,
			$archive,
			$status,
			$order_col,
			$order_dir,
			$limit,
			$criteria,
			$credentials,
			$tab
		);
		return $result;
	}

	/**
	 * Helper method substitute access
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function SubstituteAccess($data = array())
	{
		$result     = array();
		$usergroups = $this->getUsergroups();
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
	 * Helper method substitute zero date
	 *
	 * @param   array $data list of table entries
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	private function SubstituteNullDate($data = array())
	{
		$result     = array();

		foreach ($data as $item)
		{
			$dataset = array();
			foreach ($item as $key => $value)
			{
				if ($key == 'publish_up' || $key == 'publish_down')
				{
					if ($value == Generals::$null_date)
					{
						$value  = '-';
					}
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
	private function SubstituteGender($data = array())
	{
		$result     = array();
		$gender     = array('male', 'female', 'not specified');
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
	private function SubstituteMailformat($data = array())
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
	 * @return  array
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getUsergroups()
	{
		$credentials = $this->getDbCredentials();
		$criteria    = array();
		$driver      = new Codeception\Lib\Driver\Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query = 'SELECT `id`, `title` FROM `' . Generals::$db_prefix . 'viewlevels`';

		$sth = $driver->executeQuery($query, $criteria);
		$res = $sth->fetchAll(\PDO::FETCH_ASSOC);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getDbCredentials()
	{
		$_db = $this->getModule('Db');
		$creds = $_db->_getConfig();

		$credentials['dsn']      = $creds['dsn'];
		$credentials['user']     = $creds['user'];
		$credentials['password'] = $creds['password'];

		$dsn_array  = explode(';', $credentials['dsn']);
		$db_name    = explode('=', $dsn_array[1]);

		$credentials['database'] = $db_name[1];

		return $credentials;
	}

	/**
	 * Method to get activation code from database
	 *
	 * @param   string $subscriber_mail mail address of subscriber
	 *
	 * @return  string
	 *
	 *
	 * @throws Exception
	 * @since   2.0.0
	 */

	public function getActivationCode($subscriber_mail)
	{
		$credentials = self::getDbCredentials();

		$result = DbHelper::fetchActivationCode($subscriber_mail, $criteria = array(), $credentials);

		return $result;
	}

	/**
	 * Method to get joomla activation code from database
	 *
	 * @param   string $user_mail mail address of user
	 *
	 * @return  array
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */

	public function getJoomlaActivationCode($user_mail)
	{
		$credentials = self::getDbCredentials();

		$result = DbHelper::fetchJoomlaActivationCode($user_mail, $criteria = array(), $credentials);

		return $result;
	}

	/**
	 * Method to get editlink code from database
	 *
	 * @param   string $subscriber_mail mail address of subscriber
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */

	public function getEditlinkCode($subscriber_mail)
	{
		$credentials    = self::getDbCredentials();
		$result         = DbHelper::fetchEditLink($subscriber_mail, $criteria = array(), $credentials);

		return $result;
	}

	/**
	 * Helper method to loop over filters
	 *
	 * @param \AcceptanceTester     $I              tester object
	 * @param array                 $sort_data_array    manage data (per section)
	 * @param string                $manner         header or select list
	 * @param string                $columns        columns for query
	 * @param string                $table          table of section
	 * @param integer               $archive        archived items or not?
	 * @param string                $status         published or not? Leave empty, if status not given in table
	 * @param integer               $loop_counts    how many sort criteria are given?
	 * @param int                   $tab            tab of view, not always needed
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function loopFilterList(\AcceptanceTester $I, $sort_data_array, $manner, $columns, $table, $archive, $status, $loop_counts  = 0, $tab = 1)
	{
		$section = 'default';

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

		if (strpos($table, 'subscribers') !== false)
		{
			$section = 'subscribers';
		}

		foreach ($sort_data_array['sort_criteria'] as $key => $criterion)
		{
			foreach (Generals::$sort_orders as $order)
			{
				if ($order == 'ascending')
				{
					$i++;
					if ($key == 'publish_down')
					{
						$i--;
					}

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

				$row_values_raw = $I->GetListData(
					$table,
					$columns,
					$archive,
					$status,
					$sort_data_array['select_criteria'][$key],
					$db_order,
					$list_length,
					array(),
					$tab
				);
				if ($key == 'access')
				{
					$row_values = self::SubstituteAccess($row_values_raw);
				}
				elseif ($key == 'gender')
				{
					$row_values = self::SubstituteGender($row_values_raw);
				}
				elseif ($key == 'Email format')
				{
					$row_values = self::SubstituteMailformat($row_values_raw);
				}
				elseif ($key == 'tpl_id')
				{
					$row_values = self::SubstituteTemplateFormat($row_values_raw);
				}
				elseif ($key == 'publish_up' || $key == 'publish_down')
				{
					$row_values = self::SubstituteNullDate($row_values_raw);
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
					$tableHeadcolLinkLocation = sprintf(Generals::$table_headcol_link_location, $criterion);
					if ($section === 'subscribers')
					{
						if ($tab === 1)
						{
							$tableHeadcolLinkLocation = str_replace('main-table', 'main-table-bw-confirmed', $tableHeadcolLinkLocation);
						}
						if ($tab === 2)
						{
							$tableHeadcolLinkLocation = str_replace('main-table', 'main-table-bw-unconfirmed', $tableHeadcolLinkLocation);
						}
					}
					$I->click($tableHeadcolLinkLocation);
					$I->waitForElementVisible($tableHeadcolLinkLocation, 30);
				}
				else
				{
					$I->click(Generals::$filterOptionsSwitcher);
					$I->click(Generals::$ordering_list);
					$I->selectOption(Generals::$ordering_list, $sort_data_array['sort_criteria_select'][$key] . " " . $order);
					$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
				}

				$I->expectTo('see arrow ' . $arrow . ' at ' . $criterion);
				$I->waitForElementVisible(sprintf(Generals::$table_headcol_arrow_location, $i, Generals::$sort_arrows[$arrow]), 5);
				$I->seeElement(sprintf(Generals::$table_headcol_arrow_location, $i, Generals::$sort_arrows[$arrow]));
				$I->expectTo('see text ' . $sort_data_array['sort_criteria_select'][$key] . ' ' . $order);
				$I->click(Generals::$filterOptionsSwitcher);
				$orderingText = $sort_data_array['sort_criteria_select'][$key] . ' ' . $order;
				$I->see(
					$orderingText,
					sprintf(Generals::$select_list_selected_location, Generals::$ordering_id, $orderingText)
				);
				$I->click(Generals::$filterOptionsSwitcher);
				$I->waitForElementNotVisible(Generals::$filterOptionsPopup);

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

							if (strpos($table, 'newsletter') !== false && $tab == 2)
							{
								$col = 8;
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
						case 'attachment':
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
						case 'gender':
						case 'authors':
						case 'campaign_id':
							if (($needle == '') || ($needle == null))
							{
								//do nothing;
							}
							break;
						case 'is_template':
							$col = 8;
							if ($needle == '1')
							{
								$I->seeElement(sprintf(Generals::$template_yes_row, ($k + 1), $col));
							}
							else
							{
								$I->seeElement(sprintf(Generals::$template_no_row, ($k + 1), $col));
							}
							break;
						default:
							if (!($key == 'user_id') || ($needle != '0'))
							{
								$I->assertStringContainsString($needle, $haystack);
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function filterByStatus(\AcceptanceTester $I)
	{
		// select published
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$status_list_id);
		$I->selectOption(Generals::$status_list_id, Generals::$status_published);

		$I->dontSeeElement(Generals::$icon_unpublished);
		$I->wait(1);

		// select unpublished
		$I->selectOption(Generals::$status_list_id, Generals::$status_unpublished);

		$I->dontSeeElement(Generals::$icon_published);
	}

	/**
	 * Helper method to publish by icon
	 *
	 * @param \AcceptanceTester $I
	 * @param array             $publish_by_icon
	 * @param string            $item
	 * @param string            $extra_click
	 * @param boolean           $allowed
	 *
	 * @since   2.0.0
	 */
	public function publishByIcon(\AcceptanceTester $I, $publish_by_icon, $item, $extra_click = '', $allowed = true)
	{
		// switch status by icon
		$I->scrollTo($publish_by_icon['publish_button'], 0, -200);
		$I->wait(1);
		$I->clickAndWait($publish_by_icon['publish_button'], 2);

		if (!$allowed)
		{
			$I->dontSee("One " . $item . " published!");
			return;
		}

		$I->see("One " . $item . " published!");

		// Confirm success message
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		if ($item == 'newsletter')
		{
			$I->clickAndWait($extra_click, 1);
		}

		$I->seeElement($publish_by_icon['publish_result']);

		$I->scrollTo($publish_by_icon['unpublish_button'], 0, -200);
		$I->wait(1);
		$I->clickAndWait($publish_by_icon['unpublish_button'], 1);
		$I->see("One " . $item . " unpublished!");
		if ($item == 'newsletter')
		{
			$I->clickAndWait($extra_click, 1);
		}

		$I->seeElement($publish_by_icon['unpublish_result']);

		// Confirm success message
		if ($extra_click === '')
		{
			$I->click(Generals::$systemMessageClose);
		}
	}

	/**
	 * Helper method to publish by toolbar
	 *
	 * @param \AcceptanceTester $I
	 * @param array             $publish_by_toolbar
	 * @param string            $item
	 * @param string            $extra_click
	 * @param boolean           $allowed
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function publishByToolbar(\AcceptanceTester $I, $publish_by_toolbar, $item, $extra_click = '', $allowed = true)
	{
		// switch status by toolbar
		$I->wait(1);
		if (!$allowed)
		{
			$I->scrollTo(Generals::$first_list_entry, 0, -250);
			$I->wait(1);
			$I->click(Generals::$first_list_entry);

			$I->scrollTo(Generals::$joomlaHeader, 0, -100);
			$I->wait(1);
			$I->clickAndWait(Generals::$toolbarActions, 1);

			$I->scrollTo(Generals::$first_list_entry, 0, -250);
			$I->wait(1);
			$I->dontSeeElement(Generals::$toolbar4['Publish']);
			$I->dontSeeElement(Generals::$toolbar4['Unpublish']);
			return;
		}

		$I->scrollTo($publish_by_toolbar['publish_button'], 0, -250);
		$I->wait(1);
		$I->click($publish_by_toolbar['publish_button']);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Publish'], 1);

		$I->scrollTo($publish_by_toolbar['publish_button'], 0, -250);
		$I->wait(1);
		$I->see("One " . $item . " published!");

		// Confirm success message
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);

		$I->waitForElementVisible(Generals::$systemMessageClose, 5);
		$I->click(Generals::$systemMessageClose);

		if ($item == 'newsletter')
		{
			$I->wait(1);
			$I->clickAndWait($extra_click, 2);
		}

//		$I->waitForElementVisible($publish_by_toolbar['unpublish_button'], 10);
		$I->seeElement($publish_by_toolbar['publish_result']);

		$I->scrollTo($publish_by_toolbar['unpublish_button'], 0, -250);
		$I->wait(1);
		$I->waitForElementVisible($publish_by_toolbar['unpublish_button'], 3);
		$I->wait(1);
		$I->clickAndWait($publish_by_toolbar['unpublish_button'], 1);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Unpublish'], 1);
		$I->see("One " . $item . " unpublished!");

		// Confirm success message
		$I->waitForElementVisible(Generals::$systemMessageClose, 5);
		$I->click(Generals::$systemMessageClose);

		if ($item == 'newsletter')
		{
			$I->wait(1);
			$I->clickAndWait($extra_click, 2);
		}

		$I->seeElement($publish_by_toolbar['unpublish_result']);
	}

	/**
	 * Helper method to check pagination
	 *
	 * @param \AcceptanceTester $I
	 * @param array             $pagination_data_array
	 * @param int               $listlenght
	 * @param string            $tableIdentifier
	 *
	 * @since   2.0.0
	 */
	public function checkPagination(\AcceptanceTester $I, $pagination_data_array, $listlenght, $tableIdentifier = "//table[@id='main-table']")
	{
		if (isset($pagination_data_array['p1_val1']))
		{
			$I->assertEquals($listlenght, count(self::GetTableRows($I, $tableIdentifier)));
			$this->browsePages(
				$I,
				$pagination_data_array['p1_val1'],
				$pagination_data_array['p1_field1'],
				$pagination_data_array['p1_val_last'],
				$pagination_data_array['p1_field_last']
			);
		}

		if (isset($pagination_data_array['p2_val1']))
		{
			$I->clickAndWait(Generals::$next_page, 1);
			$this->browsePages(
				$I,
				$pagination_data_array['p2_val1'],
				$pagination_data_array['p2_field1'],
				$pagination_data_array['p2_val_last'],
				$pagination_data_array['p2_field_last']
			);
		}

		if (isset($pagination_data_array['p_last_val1']))
		{
			$I->clickAndWait(Generals::$last_page, 1);
			$this->browsePages(
				$I,
				$pagination_data_array['p_last_val1'],
				$pagination_data_array['p_last_field1'],
				$pagination_data_array['p_last_val_last'],
				$pagination_data_array['p_last_field_last']
			);
		}

		if (isset($pagination_data_array['p_prev_val1']))
		{
			$I->clickAndWait(Generals::$prev_page, 1);
			$this->browsePages(
				$I,
				$pagination_data_array['p_prev_val1'],
				$pagination_data_array['p_prev_field1'],
				$pagination_data_array['p_prev_val_last'],
				$pagination_data_array['p_prev_field_last']
			);
		}

		if (isset($pagination_data_array['p1_val1']))
		{
			$I->clickAndWait(Generals::$first_page, 1);
			$this->browsePages(
				$I,
				$pagination_data_array['p1_val1'],
				$pagination_data_array['p1_field1'],
				$pagination_data_array['p1_val_last'],
				$pagination_data_array['p1_field_last']
			);
		}

		if (isset($pagination_data_array['p3_val1']))
		{
			$I->clickAndWait(Generals::$page_3, 1);
			$this->browsePages(
				$I,
				$pagination_data_array['p3_val1'],
				$pagination_data_array['p3_field1'],
				$pagination_data_array['p3_val3'],
				$pagination_data_array['p3_field3']
			);
		}
	}

	/**
	 * Helper method to check list limit
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $tableIdentifier
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function checkListlimit(\AcceptanceTester $I, $tableIdentifier = "//table[@id='main-table']")
	{
		$I->assertEquals(20, count($I->GetTableRows($I, $tableIdentifier)));

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_5);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(5, count($I->GetTableRows($I, $tableIdentifier)));

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_15);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(15, count($I->GetTableRows($I, $tableIdentifier)));

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_20);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(20, count($I->GetTableRows($I, $tableIdentifier)));

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(10, count($I->GetTableRows($I, $tableIdentifier)));
	}

	/**
	 * Helper method to check filter by access
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function filterByAccess(\AcceptanceTester $I)
	{
		// select public
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$access_list_id);
		$I->selectOption(Generals::$access_list_id, Generals::$access_public);

		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select guest
		$I->selectOption(Generals::$access_list_id, Generals::$access_guest);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select registered
		$I->selectOption(Generals::$access_list_id, Generals::$access_registered);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select special
		$I->selectOption(Generals::$access_list_id, Generals::$access_special);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Super Users", Generals::$access_column);

		// select super users
		$I->selectOption(Generals::$access_list_id, Generals::$access_super);

		$I->dontSee("Public", Generals::$access_column);
		$I->dontSee("Guest", Generals::$access_column);
		$I->dontSee("Registered", Generals::$access_column);
		$I->dontSee("Special", Generals::$access_column);
	}

	/**
	 * Helper method to loop over search values
	 *
	 * @param \AcceptanceTester $I
	 * @param array             $search_data_array
	 * @param bool              $exact
	 * @param bool              $searchWithSpan
	 * @param string            $mainTable
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function searchLoop(\AcceptanceTester $I, $search_data_array, $exact = true, bool $searchWithSpan = false, $mainTable = "//*[@id='main-table']")
	{
		// loop search value
		for ($j = 0; $j < count($search_data_array['search_val']); $j++)
		{
			// loop search by
			$I->fillField(Generals::$search_field, $search_data_array['search_val'][$j]);
			for ($i = 0; $i < count($search_data_array['search_by']); $i++)
			{
				// open 'search by' list, select 'search by' value
				$I->click(Generals::$filterOptionsSwitcher);
				$I->click(Generals::$search_list);
				$I->selectOption(Generals::$search_list, $search_data_array['search_by'][$i]);
				$I->wait(2);

				// click search button
				$searchButton = Generals::$search_button;

				if ($searchWithSpan)
				{
					$searchButton = Generals::$search_button_span;
				}

				$I->click($searchButton);
				$I->waitForElement($mainTable);
				// check result
				if ((int) $search_data_array['search_res'][$j][$i] == 0)
				{
					$I->see(Generals::$null_msg, Generals::$null_row);
				}
				elseif ($exact)
				{
					$I->assertTableSearchResult($search_data_array['search_val'][$j], (int) $search_data_array['search_res'][$j][$i], $mainTable);
				}
			}
		}
	}

	/**
	 * Helper method archive and delete items, specified by EditData
	 *
	 * @param \AcceptanceTester $I
	 * @param array             $manage_data
	 * @param array             $edit_data
	 * @param boolean           $delete_allowed
	 * @param bool              $isTester
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   2.0.0
	 */
	public function HelperArcDelItems(\AcceptanceTester $I, $manage_data, $edit_data, $delete_allowed, $isTester = false)
	{
		$this->HelperArchiveItems($I, $manage_data, $edit_data, $isTester);

		$this->switchToArchive($I, $edit_data['archive_tab']);

		if ($delete_allowed)
		{
			$this->HelperDeleteItems($I, $manage_data, $edit_data);
		}

		$this->switchToSection($I, $manage_data);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param                   $manage_data
	 * @param                   $edit_data
	 * @param                   $isTester
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	public function HelperArchiveItems(\AcceptanceTester $I, $manage_data, $edit_data, $isTester = false)
	{
		// ensure we are on the section list page
		$I->see($manage_data['section'], Generals::$pageTitle);
		// select items to archive
		$I->fillField(Generals::$search_field, $edit_data['field_title']);
		$I->clickAndWait(Generals::$filterOptionsSwitcher, 1);
		$I->clickAndWait(Generals::$search_list, 1);
		$I->selectOption(Generals::$search_list, $edit_data['archive_identifier']);

		$searchButton = Generals::$search_button;

//		if ($manage_data['section'] == 'archive' || $manage_data['section'] == 'subscriber' || $manage_data['section'] == 'newsletter')
//		{
//			$searchButton = Generals::$search_button_span;
//		}

		$I->click($searchButton);

		$mainTableId = Generals::$main_table;

		if (isset($edit_data['mainTableId']))
		{
			$mainTableId = $edit_data['mainTableId'];
		}
		$I->waitForElement($mainTableId);
		$I->see($edit_data['field_title'], $edit_data['archive_title_col']);

		//count items
		$count = $I->GetListLength($I, $mainTableId);

		// archive items
		$archive_button = Generals::$toolbar4['Archive'];

		$I->checkOption(Generals::$check_all_button);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->clickAndWait($archive_button, 1);

		if ($manage_data['section'] == 'template' || $manage_data['section'] == 'subscriber' || $manage_data['section'] == 'newsletter'|| $manage_data['section'] == 'mailinglist')
		{
			// process confirmation popup
			$I->seeInPopup($edit_data['archive_confirm']);
			$I->acceptPopup();
		}
		elseif ($manage_data['section'] == 'campaigns')
		{
			// process newsletter popup
			$I->setIframeName($manage_data['popup_archive_iframe']);
			$I->switchToIFrame($manage_data['popup_archive_iframe']);
			$I->waitForText($manage_data['popup_archive_newsletters']);
			$I->see($manage_data['popup_archive_newsletters']);
			$I->clickAndWait($manage_data['popup_button_no'], 1);
			$I->switchToIFrame();
		}

		// see message archived
		$I->waitForElementVisible(Generals::$alert_heading4, 30);
		$I->seeElement(Generals::$alert_heading4);

		if ($count == '1')
		{
			$I->see($edit_data['archive_success_msg'], Generals::$alert_success4);
		}
		else
		{
			if (!$isTester)
			{
				$I->see($edit_data['archive_success2_msg'], Generals::$alert_success4);
			}
			else
			{
				$I->see($edit_data['archive_success4_msg'], Generals::$alert_success4);
			}
		}
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param                   $manage_data
	 * @param                   $edit_data
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function HelperDeleteItems(\AcceptanceTester $I, $manage_data, $edit_data)
	{
		// select items to delete
		$I->fillField(Generals::$search_field, $edit_data['field_title']);
		$I->click(Generals::$filterOptionsSwitcher);
		$I->clickAndWait(Generals::$search_list, 1);
		$I->selectOption(Generals::$search_list, $edit_data['delete_identifier']);

		$I->click(Generals::$search_button);

		$I->waitForElement(Generals::$main_table);

		$I->see($edit_data['field_title']);

		//count items
		$count    = $I->GetListLength($I, Generals::$main_table);
		$jVersion = $I->getJoomlaMainVersion($I);
		codecept_debug('Joomla main version: ' . $jVersion);
		codecept_debug('Type of Joomla main version: ' . gettype($jVersion));

		$I->checkOption(Generals::$check_all_button);
		$I->clickAndWait($edit_data['delete_button'], 1);

		if ($manage_data['section'] == 'campaigns')
		{
			$I->switchToIFrame($manage_data['popup_delete_iframe']);
			$I->waitForElementVisible("//*[@id='confirm-delete']", 5);
			$I->waitForText($manage_data['popup_delete_newsletters'], 5, "//*[@id='confirm-delete']/tbody/tr/th");
			$I->see($manage_data['popup_delete_newsletters']);
			$I->clickAndWait($manage_data['popup_button_delete_no'], 1);
			$I->switchToIFrame();
		}
		else
		{
			// process confirmation popup
			if ($jVersion !== 5)
			{
				$I->seeInPopup($edit_data['remove_confirm']);
				$I->acceptPopup();
			}
			else
			{
				$I->see($edit_data['remove_confirm'], Generals::$confirmModalDialog);
				$I->clickAndWait(Generals::$confirmModalYes, 2);
			}
		}

		// see message deleted
		$I->waitForElementVisible(Generals::$alert_heading4, 30);
		$I->seeElement(Generals::$alert_heading4);

		if ($count == '1')
		{
			$I->see($edit_data['success_remove'], Generals::$alert_success4);
		}
		else
		{
			$I->see($edit_data['success_remove2'], Generals::$alert_success4);
		}

		$I->dontSee($edit_data['field_title']);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param                   $manage_data
	 * @param                   $edit_data
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function HelperRestoreItems(\AcceptanceTester $I, $manage_data, $edit_data)
	{
		$I->switchToArchive($I, $edit_data['archive_tab']);

		// select items to restore
		$I->fillField(Generals::$search_field, $edit_data['field_title']);
		$I->clickAndWait(Generals::$filterbar_button, 2);
		$I->clickSelectList(Generals::$search_list, $edit_data['delete_identifier'], Generals::$search_list_id);
		$I->clickAndWait(Generals::$search_button, 1);
		$I->see($edit_data['field_title']);

		//count items
		$count = $I->GetListLength($I, "//table[@id='main-table']");

		$I->checkOption(Generals::$check_all_button);

		$restore_button = Generals::$toolbar4['Restore'];

		if ($manage_data['section'] == 'campaigns')
		{
			$restore_button = $edit_data['restore_button'];
		}

		$I->clickAndWait($restore_button, 1);

		if ($manage_data['section'] == 'campaigns')
		{
			$I->switchToIFrame($manage_data['popup_restore_iframe']);
			$I->waitForText($manage_data['popup_restore_newsletters']);
			$I->see($manage_data['popup_restore_newsletters']);
			$I->clickAndWait($manage_data['popup_button_delete_no'], 1);
			$I->switchToIFrame();
		}

		// see message restored
		$I->waitForElementVisible(Generals::$alert_heading4, 30);
		$I->see(Generals::$alert_info_txt, Generals::$alert_heading4);
		if ($count == '1')
		{
			$I->see($edit_data['success_restore'], Generals::$alert_info);
		}
		else
		{
			$I->see($edit_data['success_restore2'], Generals::$alert_info);
		}

		$I->dontSee($edit_data['field_title']);

		$I->switchToSection($I, $manage_data);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $archive_tab
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function switchToArchive(\AcceptanceTester $I, $archive_tab)
	{
		$I->amOnPage(Generals::$archive_url);
		$I->waitForElement(Generals::$pageTitle, 30);

		$I->see(Generals::$archive_txt, Generals::$pageTitle);
		$I->wait(1);
		$I->click($archive_tab);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param                   $manage_data
	 *
	 *
	 * @since 2.0.0
	 */
	public function switchToSection(\AcceptanceTester $I, $manage_data)
	{
		$I->amOnPage($manage_data['url']);
		$I->see($manage_data['section'], Generals::$pageTitle);
	}

	/**
	 * Method to get options of specified extension
	 *
	 * @param   string      $extension      the extension to get the options for
	 *
	 * @return  object      $options
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function getManifestOptions($extension)
	{
		$credentials    = $this->getDbCredentials();
		$criteria       = array();

		$options = DbHelper::grabManifestOptionsFromDatabase($extension, $credentials, $criteria);

		if ($extension == 'mod_bwpostman' && property_exists($options, 'com_params'))
		{
			if ($options->com_params)
			{
				$extension = 'com_bwpostman';
				$comOptions = DbHelper::grabManifestOptionsFromDatabase($extension, $credentials, $criteria);

				$options = (object)array_merge((array)$options, (array)$comOptions);
			}
		}

		return $options;
	}

	/**
	 * Method to set a single option of specified extension
	 *
	 * @param   string            $extension      the extension to set the option for
	 * @param   string            $option         the option to update
	 * @param   mixed|array       $value          the new value for this option
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setManifestOption($extension = 'com_bwpostman', $option = '', $value = '')
	{
		$credentials    = $this->getDbCredentials();
		$criteria       = array();
		$options        = DbHelper::grabManifestOptionsFromDatabase($extension, $credentials, $criteria);

		$options->$option   = $value;

		$options_string = json_encode($options);

		DbHelper::setManifestOptionsInDatabase($extension, $options_string, $credentials, $criteria);
	}

	/**
	 * Updates an SQL record into a database. This record will **not** be reset after the test.
	 *
	 * @param string  $table the name of table to update
	 * @param   array $data  array of key-value pairs to update
	 * @param array   $where_condition
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0.
	 */
	public function updateInDatabase(string $table, array $data, array $where_condition)
	{
		$credentials    = $this->getDbCredentials();
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
	 * Updates an SQL record into a database. This record will **not** be reset after the test.
	 *
	 * @param   string      $table              the name of table to update
	 * @param   int         $value              value to set autoincrement to
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0.
	 */
	public function resetAutoIncrement($table, $value)
	{
		$credentials    = $this->getDbCredentials();
		$criteria       = array();

		DbHelper::resetAutoIncrement($table, $value, $criteria, $credentials);
	}

	/**
	 * Truncate table session. This record will **not** be reset after the test.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0.
	 */
	public function truncateSession()
	{
		$credentials    = $this->getDbCredentials();

		DbHelper::truncateSession($credentials);
	}

	/**
	 * Method to get ID of an extension
	 *
	 * @param string $extension the extension to set the option for
	 *
	 * @return  integer     $id             ID of the extension
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getExtensionId(string $extension = 'com_bwpostman'): int
	{
		$credentials    = $this->getDbCredentials();

		$id = DbHelper::getExtensionIdFromDatabase($extension, $credentials);

		return $id;
	}

	/**
	 * Method to get ID of an extension
	 *
	 * @param string $extension the extension to check extension state
	 *
	 * @return  boolean     $enabled enabled state of an extension
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function getExtensionEnabledState(string $extension = 'com_bwpostman'): bool
	{
		$credentials    = $this->getDbCredentials();

		$enabled = DbHelper::getExtensionEnabledStateFromDatabase($extension, $credentials);

		return $enabled;
	}

	/**
	 * Method to set component id of the extension for a specified menu entry
	 *
	 * @param   string      $title         the title of the menu entry
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setComponentIdInMenu($title)
	{
		$id     = $this->getExtensionId('com_bwpostman');

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
	 * @since 2.0.0
	 */
	private function browsePages(\AcceptanceTester $I, $top_val, $top_val_field, $last_val, $last_val_field)
	{
		$I->scrollTo(Generals::$table_header);
		$I->wait(1);
		$I->see($top_val, $top_val_field);
		$I->scrollTo(Generals::$pagination_bar, 0, -100);
		$I->wait(1);
		$I->see($last_val, $last_val_field);
	}

	/**
	 * Test method to get group ID by name
	 *
	 * @param   string      $groupname
	 *
	 * @return  int
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function getGroupIdByName($groupname)
	{
		$credentials    = $this->getDbCredentials();
		$criteria       = array();

		$group_id = (int) DbHelper::getGroupIdByName($groupname, $criteria, $credentials);

		return $group_id;
	}

	/**
	 * Test method to get rule names by component asset
	 *
	 * @param   string      $extension
	 *
	 * @return  array
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function getRuleNamesByComponentAsset($extension)
	{
		$rules = array(
			"core.admin",
			"core.login.admin",
			"core.manage",
			"bwpm.create",
			"bwpm.edit",
			"bwpm.edit.own",
			"bwpm.edit.state",
			"bwpm.archive",
			"bwpm.restore",
			"bwpm.delete",
			"bwpm.send",
			"bwpm.view.newsletter",
			"bwpm.view.subscriber",
			"bwpm.view.campaign",
			"bwpm.view.mailinglist",
			"bwpm.view.template",
			"bwpm.view.archive",
			"bwpm.admin.newsletter",
			"bwpm.admin.subscriber",
			"bwpm.admin.campaign",
			"bwpm.admin.mailinglist",
			"bwpm.admin.template",
			"bwpm.view.maintenance",
		);

		return $rules;
	}

	/**
	 * Method to set status of an extension
	 *
	 * @param string    $extension
	 * @param string    $status
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setExtensionStatus($extension, $status)
	{
		$credentials = $this->getDbCredentials();
		$criteria    = array();
		$driver      = new Codeception\Lib\Driver\Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query = 'UPDATE `' . Generals::$db_prefix . 'extensions` SET `enabled` = ' . $status . " WHERE `element` = '" . $extension . "'";

		$sth = $driver->executeQuery($query, $criteria);
	}

	/**
	 * Method to set delete records in specified table
	 *
	 * @param string    $table
	 * @param mixed     $condition
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function deleteRecordFromDatabase($table, $condition)
	{
		$credentials = $this->getDbCredentials();
		$criteria    = array();
		$driver      = new Codeception\Lib\Driver\Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		if (is_array($condition))
		{
			$nbr_values  = count($condition);
			$i           = 1;
			$where_clause  = " WHERE ";
			foreach ($condition as $key => $value)
			{
				$where_clause   .= "`$key` = '$value'";
				if ($i < $nbr_values)
				{
					$where_clause .= " AND ";
					$i++;
				}
			}
		}
		else
		{
			$where_clause   = $condition;
		}

		$query      = "DELETE FROM " . Generals::$db_prefix . $table . $where_clause;

		$sth = $driver->executeQuery($query, $criteria);
		$sth->rowCount();
	}

	/**
	 * Method to insert records in specified table
	 *
	 * @param string    $table
	 * @param string    $values
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function insertRecordToTable($table, $values)
	{
		$credentials = $this->getDbCredentials();
		$criteria    = array();
		$driver      = new Codeception\Lib\Driver\Db($credentials['dsn'], $credentials['user'], $credentials['password']);

		$query      = "INSERT INTO " . Generals::$db_prefix . $table . ' VALUES (' . $values . ')';

		$sth = $driver->executeQuery($query, $criteria);
		$res = $sth->rowCount();
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param $registerUrl
	 * @param $viewRegister
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function selectRegistrationPage(\AcceptanceTester $I, $registerUrl, $viewRegister)
	{
		$I->amOnPage($registerUrl);
		$I->wait(2);
		$I->scrollTo($viewRegister, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible($viewRegister, 5);
		$I->seeElement($viewRegister);
	}


	/**
	 * @param \AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	public function getJoomlaMainVersion(\AcceptanceTester $I)
	{
		return (int)$I->grabTextFrom("//*[@class='header-item']/div[contains(@class, 'joomlaversion')]/div/span[3]")[0];
	}
}
