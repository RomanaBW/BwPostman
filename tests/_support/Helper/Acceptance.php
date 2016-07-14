<?php
namespace Helper;



// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	public function _beforeSuite()
	{
		$connection = ssh2_connect('universe', 22, array('hostkey'=>'ssh-rsa'));
		ssh2_auth_pubkey_file($connection, 'romana', '/home/romana/.ssh/romana_rsa.pub', '/home/romana/.ssh/romana_rsa');
		ssh2_exec($connection, "mysql -u root -pSusi bwtest < /daten/vhosts/dev/joomla-cms/tests/_data/testdata_bwpostman_complete.sql");
	}

	public function _afterSuite()
	{
		$connection = ssh2_connect('universe', 22, array('hostkey'=>'ssh-rsa'));
		ssh2_auth_pubkey_file($connection, 'romana', '/home/romana/.ssh/romana_rsa.pub', '/home/romana/.ssh/romana_rsa');
		ssh2_exec($connection, "mysql -u root -pSusi bwtest < /daten/vhosts/dev/joomla-cms/tests/_data/testdata_bwpostman_truncate.sql");
	}

	public function changeBrowser($browser) {
		$this->getModule('WebDriver')->_reconfigure(array('browser' => $browser));
	}

	/**
	 * Helper method get table rows in list view
	 *
	 * @param   \AcceptanceTester                $I
	 *
	 * @return  array   $rows
	 *
	 * @since   2.0.0
	 */
	public function GetTableRows(\AcceptanceTester $I)
	{
		$table = $I->grabTextFrom("tbody");
		$rows = explode("\n", $table);

		return $rows;
	}

	/**
	 * Helper method get list length in list view
	 *
	 * @param   \AcceptanceTester                $I
	 *
	 * @return  int
	 *
	 * @since   2.0.0
	 */
	public function GetListLength(\AcceptanceTester $I)
	{
		$table = $I->grabTextFrom("tbody");
		$rows = explode("\n", $table);
		$rcount = count($rows);

		return $rcount;
	}

	/**
	 * Helper method get list data from database
	 *

	 * @param   string      $table_name     name of the table to get values from
	 * @param   string      $columns        select columns
	 * @param   string      $order_col      order column
	 * @param   string      $order_dir      order direction
	 * @param   integer     $limit          number of values to get from database
	 * @param   array       $criteria       special criteria, i.e. WHERE
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public function GetListData($table_name, $columns, $order_col, $order_dir, $limit, $criteria = [])
	{
		$_db        = $this->getModule('Db');
		$table_name = \Page\Generals::$db_prefix . 'bwpostman_' . $table_name;
//		$columns    = implode(', ', $columns);

		$credentials['dsn']         = $_db->_getConfig('dsn');
		$credentials['user']        = $_db->_getConfig('user');
		$credentials['password']    = $_db->_getConfig('password');

		$result     = DbHelper::grabFromDatabaseWithLimit($table_name, $columns, $order_col, $order_dir, $limit, $criteria, $credentials);
//		codecept_debug($result);

		return $result;
	}

	/**
	 * DbHelper method get query for number of subscriubers per mailinglist
	 *
	 * @param   \Page\Generals                  $Generals

	 * @return  string
	 *
	 * @since   2.0.0
	 */
	public static function getQueryNumberOfSubscribers(\Page\Generals $generals)
	{
		// Build sub queries which counts the subscribers of each mailinglists
		$sub_query  = 'SELECT `d`.`id` FROM `' . $generals::$db_prefix . 'bwpostman_subscribers` AS `d` WHERE `d`.`archive_flag` = 0';

		$query  = '(SELECT COUNT(`b`.`subscriber_id`) AS `subscribers`';
		$query  .= ' FROM `' . $generals::$db_prefix . 'bwpostman_subscribers_mailinglists` AS `b`';
		$query  .= ' WHERE `b`.`mailinglist_id` = `a`.`id`';
		$query  .= ' AND `b`.`subscriber_id` IN (' . $sub_query . ')) AS `subscribers`';
//		codecept_debug('Sub-Query:');
//		codecept_debug($query);

		return $query;
	}

	public function clickJQuerySelectedElement(\AcceptanceTester $I, $select_list, $select_text, $sort_order, $form_id) {
		$I->executeJS("document.getElementById('" . $select_list . "').setAttribute('style', 'display: visible');");
		$I->selectOption("#" . $select_list, $select_text . ' ' . $sort_order);
		$I->executeJS('document.getElementById("' . $form_id . '").submit();');
		$I->executeJS("document.getElementById('" . $select_list . "').setAttribute('style', 'display: none');");
	}
}
