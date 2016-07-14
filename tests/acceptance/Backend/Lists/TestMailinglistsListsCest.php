<?php


/**
 * Class TestMailinglistsListsCest
 *
 * This class contains all methods to test list view of mailing lists at back end
 */
class TestMailinglistsListsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
	 * @param   \Page\Generals      $Generals
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _login(\Page\Login $loginPage, \Page\Generals $Generals)
	{
		$loginPage->loginAsAdmin('Webmemsahib', 'BESU#PW§1', $Generals);
	}

	/**
	 * Test method sorting mailing lists by click to column in table header
	 *
	 * @param   AcceptanceTester                $I
	 * @param   \Page\MainviewPage              $mainView
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\Generals                  $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function SortMailinglistsByTableHeader(AcceptanceTester $I, \Page\MainviewPage $mainView, \Page\MailinglistManagerPage $MlManage, \Page\Generals $Generals, \Helper\Acceptance $helper)
	{

		$I->wantTo("Sort mailinglists");
		$I->amOnPage($MlManage::$url);
		$I->wait(5);

		// Get list length
		$list_length = $helper->getListLength($I);

		// loop over sorting criterion
		$i          = 2;
		$columns    = implode(', ', $MlManage::$select_criteria);
		$columns    = str_replace('subscribers', $helper->getQueryNumberOfSubscribers($Generals), $columns);
		foreach ($MlManage::$sort_criteria as $key => $criterion) {
			$i++;
			if ($i == 8) $i = 2;
			// sort ascending
			$row_values_nominal = $helper->getListData('mailinglists AS `a`', $columns, $MlManage::$select_criteria[$key], 'ASC', $list_length);
			$I->click(sprintf($MlManage::$table_headcol_link_location, $i));
			$I->expectTo('see arrow up at ' . $criterion);
			$I->seeElement(sprintf($MlManage::$table_headcol_arrow_location, $i), array('class' => $MlManage::$sort_arrows['up']));
			$I->expectTo('see text ' . $MlManage::$sort_criteria_select[$key] . ' ascending');
			$I->see($MlManage::$sort_criteria_select[$key] . ' ascending', $MlManage::$select_list_selected_location);

			// loop over row values
			$row_values_actual  = $helper->GetTableRows($I);
			for ($k = 0; $k < count($list_length); $k++) {
				$I->assertContains($row_values_nominal[$k][$key], $row_values_actual[$k]);
			}

			// sort descending
			$row_values_nominal = $helper->getListData('mailinglists AS `a`', $columns, $MlManage::$select_criteria[$key], 'DESC', $list_length);
			$I->click(sprintf($MlManage::$table_headcol_link_location, $i));
			$I->expectTo('see arrow up at ' . $criterion);
			$I->seeElement(sprintf($MlManage::$table_headcol_arrow_location, $i), array('class' => $MlManage::$sort_arrows['down']));
			$I->expectTo('see text ' . $MlManage::$sort_criteria_select[$key] . ' descending');
			$I->see($MlManage::$sort_criteria_select[$key] . ' descending', $MlManage::$select_list_selected_location);

			// loop over row values
			$row_values_actual  = $helper->GetTableRows($I);
			for ($k = 0; $k < count($list_length); $k++) {
				$I->assertContains($row_values_nominal[$k][$key], $row_values_actual[$k]);
			}
		}
	}
*/
	/**
	 * Test method sorting mailing lists by selection at select list
	 *
	 * @param   AcceptanceTester                $I
	 * @param   \Page\MainviewPage              $mainView
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\Generals                  $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SortMailinglistsBySelectList(AcceptanceTester $I, \Page\MainviewPage $mainView, \Page\MailinglistManagerPage $MlManage, \Page\Generals $Generals, \Helper\Acceptance $helper)
	{

		$I->wantTo("Sort mailinglists");
		$I->amOnPage($MlManage::$url);
		$I->wait(5);

		// Get list length
		$list_length = $helper->getListLength($I);

		// loop over sorting criterion
		$i          = 2;
		$columns    = implode(', ', $MlManage::$select_criteria);
		$columns    = str_replace('subscribers', $helper->getQueryNumberOfSubscribers($Generals), $columns);
		foreach ($MlManage::$sort_criteria as $key => $criterion) {
			$i++;
			if ($i == 8) $i = 2;
			// sort ascending
			$row_values_nominal = $helper->getListData('mailinglists AS `a`', $columns, $MlManage::$select_criteria[$key], 'ASC', $list_length);
			$helper->clickJQuerySelectedElement($I, 'list_fullordering', $MlManage::$sort_criteria_select[$key], 'ascending', 'adminForm');
			$I->wait(1);
			$I->expectTo('see arrow up at ' . $criterion);
			$I->seeElement(sprintf($MlManage::$table_headcol_arrow_location, $i), array('class' => $MlManage::$sort_arrows['up']));
			$I->expectTo('see text ' . $MlManage::$sort_criteria_select[$key] . ' ascending');
			$I->see($MlManage::$sort_criteria_select[$key] . ' ascending', $MlManage::$select_list_selected_location);

			// loop over row values
			$row_values_actual  = $helper->GetTableRows($I);
			for ($k = 0; $k < count($list_length); $k++) {
				$I->assertContains($row_values_nominal[$k][$key], $row_values_actual[$k]);
			}

			// sort descending
			$row_values_nominal = $helper->getListData('mailinglists AS `a`', $columns, $MlManage::$select_criteria[$key], 'DESC', $list_length);
			$helper->clickJQuerySelectedElement($I, 'list_fullordering', $MlManage::$sort_criteria_select[$key], 'descending', 'adminForm');
			$I->wait(1);
			$I->expectTo('see arrow down at ' . $criterion);
			$I->seeElement(sprintf($MlManage::$table_headcol_arrow_location, $i), array('class' => $MlManage::$sort_arrows['down']));
			$I->expectTo('see text ' . $MlManage::$sort_criteria_select[$key] . ' descending');
			$I->see($MlManage::$sort_criteria_select[$key] . ' descending', $MlManage::$select_list_selected_location);

			// loop over row values
			$row_values_actual  = $helper->GetTableRows($I);
			for ($k = 0; $k < count($list_length); $k++) {
				$I->assertContains($row_values_nominal[$k][$key], $row_values_actual[$k]);
			}
		}
	}

	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function FilterMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Filter_mailinglists");
		$I        $I->store ====There are no data available | no_data |
	$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I->click("(//button[@type='button'])[2]");
        $I        $I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_unpublished |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.unpublish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_unpublished ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "0");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_public |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.publish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->click("(//button[@type='button'])[2]");
        $I        $I->label ====test_public ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_registered |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Public", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_registered ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "2");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_special |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Registered", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_special ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "3");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_guest |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Special", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_guest ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "5");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_Super_User |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->echo ====Anzahl Reihen = undefined ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . rows ||
		$I->see("Guest", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_Super_User ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "6");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | finish |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Super Users", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->label ====finish ||
	        $I->click("(//button[@type='button'])[2]");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function PaginationMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Pagination_mailinglists");
		$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I        $I->selectOption("#list_limit", "20");
        $I->click("Description");
        $I->click("a.js-stools-column-order.hasTooltip");
        $I->pause ====500 ||
	        $I        $I->selectOption("#list_limit", "5");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[0];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[4];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("2");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[5];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[9];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("3");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[10];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[14];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("4");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[15];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->click("span.icon-first");
        $I->pause ====500 ||
	        $I->see("01 Mailingliste 2 A");
        $I->see("02 Mailingliste 6 A");
        $I->click("span.icon-next");
        $I->pause ====500 ||
	        $I->see("02 Mailingliste 6 B");
        $I->see("02 Mailingliste 8 B");
        $I->click("span.icon-last");
        $I->pause ====500 ||
	        $I->see("04 Mailingliste 15 B");
        $I->click("2");
        $I->pause ====500 ||
	        $I->see("02 Mailingliste 6 B");
        $I->see("02 Mailingliste 8 B");
        $I        $I->click("span.icon-first");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[0];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[4];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->selectOption("#list_limit", "20");
        $I->pause ====500 ||
	        $I->click("Description");
        $I->pause ====500 ||
	        $I->click("a.js-stools-column-order.hasTooltip");
        $I->pause ====1000 ||

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function SearchMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Search_mailinglists");
		$I        $I->store ====There are no data available | no_data |
	$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "title");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====02 Mail | search_val |
	$I->selectOption("#filter_search_filter", "title");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.1|found_val|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->assertNotEval ====undefined | -1 |
	$I->endWhile ====||
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====weiterer Lauf B | search_val |
	$I->selectOption("#filter_search_filter", "description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.2|found_val|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->assertNotEval ====undefined | -1 |
	$I->endWhile ====||
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "title_description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====weiterer Lauf A | search_val |
	$I->selectOption("#filter_search_filter", "title_description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.1|found_val|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.2|found_val_2|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->storeEval ====javascript{
		storedVars . found_val_2 . indexOf(storedVars . search_val);
	}|result_2 |
	$I->assertNotEval ====undefined + undefined | -1 |
	$I->endWhile ====||
        $I        $I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_unpublished |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.unpublish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_unpublished ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "0");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_public |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.publish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_public ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_registered |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Public", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_registered ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "2");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_special |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Registered", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_special ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "3");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_guest |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Special", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_guest ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "5");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_Super_User |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->echo ====Anzahl Reihen = undefined ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . rows ||
		$I->see("Guest", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_Super_User ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "6");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | finish |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Super Users", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->label ====finish ||
	        $I->click("(//button[@type='button'])[2]");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function LockedMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Locked_mailinglists");
		$I        $I        $I->amOnPage("/administrator/index.php?option=com_bwpostman");
        $I->runScript ====javascript{
		confirm("For this test it is neccessary to log in as BE User 1 into backend by another browser. Then edit the first entry in the list of mailinglists. Let stand this as is and click okay here. Wait for the next message….");
	}||
        $I->click("//div[@id='cpanel']/div[8]/div/a/span");
        $I->click("01 Mailingliste 2 A");
        $I->see("Check-out failed with the following error: The user checking out does not match the user who checked out the item.", "p.alert-message");
        $I->assertElementPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->click("//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span");
        $I->assertElementNotPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->see("One mailing list successfully checked in!", "p.alert-message");
        $I->click("#submenu > li > a");
        $I->runScript ====javascript{
		confirm("Now cancel editing in the other browser and call edit again. Let stand this as is and click okay here anew. Wait for the next message….");
	}||
        $I->click("//div[@id='cpanel']/div[8]/div/a");
        $I->assertElementPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->click("#cb0");
        $I->click("#toolbar-checkin > button.btn.btn-small");
        $I->pause ====1000 ||
        $I->assertElementNotPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->see("One mailing list successfully checked in!", "p.alert-message");
        $I->runScript ====javascript{
		confirm("Test finished. Cancel editing at the other browser. Logout at the other browser now is possible. ");
	}||
        $I->click("#submenu > li > a");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
/*	public function ArchiveAndDeleteAllMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$I->wantTo("Archive_and_delete_all_mailinglists");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
		$I->click("input[name=checkall-toggle]");
		$I->click("#toolbar-archive > button.btn.btn-small");
		$I->see("Mailinglists", "h1.page-title");
		$I->canSee('Message', 'h4.alert-heading');
		$I->canSee('The selected mailing lists have been archived.', 'p . alert - message');
		$I->click("(//a[contains(text(),'Archive')])[2]");
		$I->click("//button[@onclick=\"layout.setAttribute('value', 'mailinglists');this.form.submit();\"]");
		$I->click("input[name=checkall-toggle]");
		$I->click("#toolbar-delete > button.btn.btn-small");
		$I->acceptPopup('Do you wish to remove the selected mailinglist(s)?');
		$I->see("Archive", "h1.page-title");
		$I->canSee('Message', 'h4.alert-heading');
		$I->canSee('The selected mailinglists have been removed.', 'p . alert - message');
		$I->canSee('There are no data available', 'td > strong');
		$I->click("(//a[contains(text(),'Mailinglists')])[2]");
		$I->seeInTitle('Bw - Test - Administration - Mailinglists');
		$I->canSee('There are no data available', 'td > strong');
		$I->click("#submenu > li > a");
	}
*/
	/**
	 * Test method to logout from backend
	 *
	 * @param   \Page\Login             $loginPage
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _logout(\Page\Login $loginPage)
	{
		$loginPage->logoutFromAdmin();
	}

}
