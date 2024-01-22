<?php
use Page\Generals as Generals;
use Page\SubscriberManagerPage as SubsManage;
use Page\SubscriberEditPage as SubsEdit;

/**
 * Class TestSubscribersListsCest
 *
 * This class contains all methods to test list view of subscribers at back end
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
class TestSubscribersListsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(\Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method sorting subscribers by click to column in table header
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortSubscribersByTableHeader(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized; only testing problem
		$I->wantTo("Sort confirmed subscribers by table header");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$sort_array     = $this->prepareSortArray($I);
		$loop_counts    = 10;

		$options    = $I->getManifestOptions('com_bwpostman');

		if (!$options->show_gender)
		{
			$loop_counts    = 9;
		}

		// loop over sorting criterion
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, $sort_array, 'header', $columns, 'subscribers AS `a`', 0, '1', $loop_counts, 1);
	}

	/**
	 * Test method sorting subscribers by selection at select list
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortSubscribersBySelectList(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized
		$I->wantTo("Sort confirmed subscribers by select list");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$sort_array = $this->prepareSortArray($I);
		$loop_counts    = 10;

		$options    = $I->getManifestOptions('com_bwpostman');

		if (!$options->show_gender)
		{
			$loop_counts    = 9;
		}

		// loop over sorting criterion
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, $sort_array, '', $columns, 'subscribers AS `a`', 0, '1', $loop_counts, 1);
	}

	/**
	 * Test method to filter subscribers by mail format
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterSubscribersByMailformat(AcceptanceTester $I)
	{
		$I->wantTo("Filter confirmed subscribers by email format");
		$I->amOnPage(SubsManage::$url);

		// select Text
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(SubsManage::$format_list_id);
		$I->selectOption(SubsManage::$format_list_id, SubsManage::$format_text);

		$I->dontSee(SubsManage::$format_text_html, SubsManage::$format_text_column);

		// select HTML
		$I->selectOption(SubsManage::$format_list_id, SubsManage::$format_html);

		$I->dontSee(SubsManage::$format_text_text, SubsManage::$format_text_column);
	}

	/**
	 * Test method to filter subscribers by mailing lists
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterSubscribersByMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Filter confirmed subscribers by mailing list");
		$I->amOnPage(SubsManage::$url);
		$I->wait(SubsManage::$wait_db);

		// Get filter bar
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(SubsManage::$format_list_id);
		$I->selectOption(SubsManage::$ml_list_id, SubsManage::$ml_select);

		$I->assertFilterResult(SubsManage::$filter_subs_result, SubsManage::$confirmedMainTable);
	}

	/**
	 * Test method to search subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SearchSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("Search confirmed Subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->searchLoop($I, SubsManage::$search_data_array, true, false, SubsManage::$confirmedMainTable);

		$I->click(Generals::$clear_button);
		$I->see(SubsManage::$search_clear_val);
	}

	/**
	 * Test method to check list limit of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ListlimitSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at confirmed subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->checkListlimit($I, SubsManage::$confirmedMainTable);
	}

	/**
	 * Test method to check pagination of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function PaginationSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at confirmed subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(10, count($I->GetTableRows($I, SubsManage::$confirmedMainTable)));

		$I->checkPagination($I, SubsManage::$pagination_data_array, 10, SubsManage::$confirmedMainTable);
	}

	/**
	 * Test method sorting subscribers by click to column in table header
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortUnconfirmedSubscribersByTableHeader(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized; only testing problem
		$I->wantTo("Sort unconfirmed subscribers by table header");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		$sort_array     = $this->prepareSortArray($I);
		$loop_counts    = 10;

		$options    = $I->getManifestOptions('com_bwpostman');

		if (!$options->show_gender)
		{
			$loop_counts    = 9;
		}


		// loop over sorting criterion
		// @ToDo: Codeception catches first appearance of element, but that is at confirmed subscribers and not visible!
		// Conclusion: Needs an specific identifier for tables!
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, $sort_array, 'header', $columns, 'subscribers AS `a`', 0, '0', $loop_counts, 2);
	}

	/**
	 * Test method sorting subscribers by selection at select list
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortUnconfirmedSubscribersBySelectList(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized
		$I->wantTo("Sort unconfirmed subscribers by select list");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		$sort_array = $this->prepareSortArray($I);
		$loop_counts    = 10;

		$options    = $I->getManifestOptions('com_bwpostman');

		if (!$options->show_gender)
		{
			$loop_counts    = 9;
		}

		// loop over sorting criterion
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, $sort_array, '', $columns, 'subscribers AS `a`', 0, '0', $loop_counts, 2);
	}

	/**
	 * Test method to filter unconfirmed subscribers by mail format
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterUnconfirmedSubscribersByMailformat(AcceptanceTester $I)
	{
		$I->wantTo("Filter unconfirmed subscribers by email format");
		$I->amOnPage(SubsManage::$url);
		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		// select Text
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(SubsManage::$format_list_id);
		$I->selectOption(SubsManage::$format_list_id, SubsManage::$format_text);

		$I->dontSee(SubsManage::$format_text_html, SubsManage::$format_text_column);

		// select HTML
		$I->selectOption(SubsManage::$format_list_id, SubsManage::$format_html);

		$I->dontSee(SubsManage::$format_text_text, SubsManage::$format_text_column);

	}

	/**
	 * Test method to filter unconfirmed subscribers by mailing list
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterUnconfirmedSubscribersByMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Filter unconfirmed subscribers by mailing list");
		$I->amOnPage(SubsManage::$url);
		$I->wait(SubsManage::$wait_db);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select 01 Mailingliste 3 A
		$I->clickSelectList(SubsManage::$ml_list, SubsManage::$ml_select_unconfirmed, SubsManage::$ml_list_id);

		$I->assertFilterResult(SubsManage::$filter_subs_unconfirmed_result, SubsManage::$unconfirmedMainTable);
	}

	/**
	 * Test method to search subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SearchUnconfirmedSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("Search unconfirmed Subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		$I->searchLoop($I, SubsManage::$search_data_array_unconfirmed, true, false, SubsManage::$unconfirmedMainTable);

		$I->click(Generals::$clear_button);
		$I->see(SubsManage::$search_clear_val_unconfirmed);
	}

	/**
	 * Test method to check list limit of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ListlimitUnconfirmedSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at unconfirmed subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		$I->checkListlimit($I, SubsManage::$unconfirmedMainTable);
	}

	/**
	 * Test method to check pagination of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function PaginationUnconfirmedSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at unconfirmed subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->clickAndWait(SubsManage::$tab_unconfirmed, 1);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(10, count($I->GetTableRows($I, SubsManage::$unconfirmedMainTable)));

		$I->checkPagination($I, SubsManage::$pagination_data_array_unconfirmed, 10, SubsManage::$unconfirmedMainTable);
	}

	/**
	 * Test method sorting subscribers by click to column in table header
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function SortTestRecipientsByTableHeader(AcceptanceTester $I)
//	{
//		// @Todo: ensure UTF-8 characters are recognized; only testing problem
//		$I->wantTo("Sort test recipients by table header");
//		SubsManage::$wait_db;
//		$I->amOnPage(SubsManage::$url);
//		$I->wait(1);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
////		$I->click(Generals::$submenu_toggle_button);
//
//		$sort_array     = $this->prepareSortArray($I);
//		$loop_counts    = 10;
//
//		$options    = $I->getManifestOptions('com_bwpostman');
//
//		if (!$options->show_gender)
//		{
//			$loop_counts    = 9;
//		}
//
//
//		// loop over sorting criterion
//		$columns    = implode(', ', SubsManage::$query_criteria);
//		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
//		$I->loopFilterList($I, $sort_array, 'header', $columns, 'subscribers AS `a`', 0, '1', $loop_counts, 1);
//
////		$I->click(Generals::$submenu_toggle_button);
//	}

	/**
	 * Test method sorting subscribers by selection at select list
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function SortTestRecipientsBySelectList(AcceptanceTester $I)
//	{
//		// @Todo: ensure UTF-8 characters are recognized
//		$I->wantTo("Sort test recipients by select list");
//		SubsManage::$wait_db;
//		$I->amOnPage(SubsManage::$url);
//		$I->wait(1);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		$sort_array = $this->prepareSortArray($I);
//		$loop_counts    = 10;
//
//		$options    = $I->getManifestOptions('com_bwpostman');
//
//		if (!$options->show_gender)
//		{
//			$loop_counts    = 9;
//		}
//
//		// loop over sorting criterion
//		$columns    = implode(', ', SubsManage::$query_criteria);
//		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
//		$I->loopFilterList($I, $sort_array, '', $columns, 'subscribers AS `a`', 0, '1', $loop_counts, 1);
//	}

	/**
	 * Test method to filter subscribers by status
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function FilterTestRecipientsByMailformat(AcceptanceTester $I)
//	{
//		$I->wantTo("Filter test recipients by email format");
//		SubsManage::$wait_db;
//		$I->amOnPage(SubsManage::$url);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		// Get filter bar
//		$I->clickAndWait(Generals::$filterbar_button, 1);
//		// select published
//		$I->clickSelectList(SubsManage::$format_list, SubsManage::$format_text, SubsManage::$format_list_id);
//
//		$I->dontSee(SubsManage::$format_text_html, SubsManage::$format_text_column);
//
//		// select unpublished
//		$I->clickSelectList(SubsManage::$format_list, SubsManage::$format_html, SubsManage::$format_list_id);
//
//		$I->dontSee(SubsManage::$format_text_text, SubsManage::$format_text_column);
//	}

	/**
	 * Test method to filter subscribers by access
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function FilterTestRecipientsByMailinglist(AcceptanceTester $I)
//	{
//		$I->wantTo("Filter test recipients by mailing list");
//		$I->amOnPage(SubsManage::$url);
//		$I->wait(SubsManage::$wait_db);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		// Get filter bar
//		$I->clickAndWait(Generals::$filterbar_button, 1);
//		// select 04 Mailingliste 14 A
//		$I->clickSelectList(SubsManage::$ml_list, SubsManage::$ml_select, SubsManage::$ml_list_id);
//
//		$I->assertFilterResult(SubsManage::$filter_subs_result, SubsManage::$unconfirmedMainTable);
//	}

	/**
	 * Test method to search subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function SearchTestRecipients(AcceptanceTester $I)
//	{
//		$I->wantTo("Search test recipients");
//		SubsManage::$wait_db;
//		$I->amOnPage(SubsManage::$url);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		$I->searchLoop($I, SubsManage::$search_data_array, true, true);
//
//		$I->click(Generals::$clear_button);
//		$I->see(SubsManage::$search_clear_val);
//	}

	/**
	 * Test method to check list limit of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
//	public function ListlimitTestRecipients(AcceptanceTester $I)
//	{
//		$I->wantTo("test list limit at test recipients");
//		$I->amOnPage(SubsManage::$url);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		$I->checkListlimit($I, SubsManage::$testersMainTable);
//	}

	/**
	 * Test method to check pagination of subscribers
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
//	public function PaginationTestRecipients(AcceptanceTester $I)
//	{
//		$I->wantTo("test pagination at test recipients");
//		$I->amOnPage(SubsManage::$url);
//		$I->clickAndWait(SubsManage::$tab_testers, 1);
//
//		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10, Generals::$limit_list_id);
//
//		$I->checkPagination($I, SubsManage::$pagination_data_array, 10, SubsManage::$testersMainTable);
//	}

	/**
	 * Test method to import subscribers by CSV file
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function ImportSubscribersByCSV(AcceptanceTester $I)
	{
		$I->wantTo("import subscribers by CSV file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Import']);
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->wait(2);
		$I->dontSeeElement(SubsManage::$import_search_button);

		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$import_search_button);
		$I->attachFile(SubsManage::$import_search_button, SubsManage::$import_csv_file);

		$I->click(SubsManage::$import_csv_caption);

		$I->click(SubsManage::$import_button_further);

		$I->see(SubsManage::$import_csv_file);
		$I->see('Yes');

		$I->scrollTo(SubsManage::$import_legend_step_2);
		$I->wait(1);
		$I->see(SubsManage::$import_csv_field_0);
		$I->see(SubsManage::$import_csv_field_1);
		$I->see(SubsManage::$import_csv_field_2);
		$I->see(SubsManage::$import_csv_field_3);
		$I->see(SubsManage::$import_csv_field_4);

		$I->scrollTo(SubsManage::$import_legend_mls, 0, -100);
		$I->wait(1);
		$I->click(SubsManage::$import_mls_target);

		$I->scrollTo(SubsManage::$import_legend_format, 0, -100);
		$I->wait(1);
		$I->click(SubsManage::$import_cb_confirm_subs);

		$I->click(SubsManage::$import_button_import);

		$I->waitForElement(SubsManage::$import_success_container, 60);
		$I->see(SubsManage::$import_msg_success, SubsManage::$import_success_container);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		$this->cleanupImportedSubscribers($I, SubsManage::$import_csv_subscribers);
	}

	/**
	 * Test method to import subscribers by XML file
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function ImportSubscribersByXML(AcceptanceTester $I)
	{
		$I->wantTo("import subscribers by XML file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Import']);
		$I->waitForElementVisible(SubsManage::$import_xml_button, 5);
		$I->wait(2);
		$I->dontSeeElement(SubsManage::$import_search_button);

		$I->click(SubsManage::$import_xml_button);
		$I->seeElement(SubsManage::$import_search_button);
		$I->attachFile(SubsManage::$import_search_button, SubsManage::$import_xml_file);

		$I->clickAndWait(SubsManage::$import_button_further, 1);

		$I->see(SubsManage::$import_xml_file);

		$I->scrollTo(SubsManage::$import_legend_step_2);
		$I->wait(1);
		$I->see(SubsManage::$import_xml_field_0);
		$I->see(SubsManage::$import_xml_field_1);
		$I->see(SubsManage::$import_xml_field_2);
		$I->see(SubsManage::$import_xml_field_3);
		$I->see(SubsManage::$import_xml_field_4);

		$I->scrollTo(SubsManage::$import_legend_mls, 0, -100);
		$I->wait(1);
		$I->click(SubsManage::$import_mls_target);

		$I->scrollTo(SubsManage::$import_legend_format, 0, -100);
		$I->wait(1);
		$I->click(SubsManage::$import_cb_confirm_subs);

		$I->click(SubsManage::$import_button_import);

		$I->waitForElement(SubsManage::$import_success_container, 60);
		$I->see(SubsManage::$import_msg_success, SubsManage::$import_success_container);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
		$this->cleanupImportedSubscribers($I, SubsManage::$import_xml_subscribers);
	}

	/**
	 * Test method to export subscribers to CSV file, which are confirmed and not archived
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ExportSubscribersToCSVCA(AcceptanceTester $I)
	{
		$I->wantTo("export confirmed, unarchived subscribers to CSV file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Export']);
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->dontSeeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_confirmed);
		$I->click(SubsManage::$export_csv_unarchived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(2);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.csv';
		$downloadPath   = $exportPath . $filename;

		$I->clickAndWait(SubsManage::$export_button_export, 15);

		$I->seeFileFound($filename, $exportPath);

		// Check exported datasets
		$I->openFile($downloadPath);
		$I->seeInThisFile(SubsManage::$subs_c_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a_f);

		$I->seeInThisFile(SubsManage::$subs_c_na);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to export subscribers to CSV file, which are unconfirmed and archived
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ExportSubscribersToCSVUA(AcceptanceTester $I)
	{
		$I->wantTo("export unconfirmed archived subscribers to CSV file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Export']);
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->dontSeeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_unconfirmed);
		$I->click(SubsManage::$export_csv_archived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(1);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.csv';
		$downloadPath   = $exportPath . $filename;

		$I->clickAndWait(SubsManage::$export_button_export, 15);

		$I->seeFileFound($filename, $exportPath);

		// Check exported datasets
		$I->openFile($downloadPath);
		$I->dontSeeInThisFile(SubsManage::$subs_c_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a_f);
		$I->seeInThisFile(SubsManage::$subs_u_a_f);

		$I->dontSeeInThisFile(SubsManage::$subs_c_na);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na);
		$I->seeInThisFile(SubsManage::$subs_u_a);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to export subscribers to CSV file, which are unconfirmed and archived
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ExportSubscribersToCSVAll(AcceptanceTester $I)
	{
		$I->wantTo("export all archived and unarchived subscribers to CSV file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Export']);
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->dontSeeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_confirmed);
		$I->click(SubsManage::$export_csv_unconfirmed);
		$I->click(SubsManage::$export_csv_archived);
		$I->click(SubsManage::$export_csv_unarchived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(1);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.csv';
		$downloadPath   = $exportPath . $filename;

		$I->clickAndWait(SubsManage::$export_button_export, 15);

		$I->seeFileFound($filename, $exportPath);

		// Check exported datasets
		$I->openFile($downloadPath);
		$I->seeInThisFile(SubsManage::$subs_c_na_f);
		$I->seeInThisFile(SubsManage::$subs_u_na_f);
		$I->seeInThisFile(SubsManage::$subs_c_a_f);
		$I->seeInThisFile(SubsManage::$subs_u_a_f);

		$I->seeInThisFile(SubsManage::$subs_c_na);
		$I->seeInThisFile(SubsManage::$subs_c_a);
		$I->seeInThisFile(SubsManage::$subs_u_na);
		$I->seeInThisFile(SubsManage::$subs_u_a);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to export subscribers to CSV file with filtered by mailinglist, export only filtered
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function ExportSubscribersToCSVFilteredYes(AcceptanceTester $I)
	{
		$I->wantTo("export only filtered subscribers to CSV file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(SubsManage::$ml_list_id);
		$I->selectOption(SubsManage::$ml_list_id, SubsManage::$ml_select);

		$I->assertFilterResult(SubsManage::$filter_subs_result, SubsManage::$confirmedMainTable);

		// Select yes in modal box
		$I->click(Generals::$toolbar['Export']);
		$I->setIframeName('popup_export_iframe');
		$I->switchToIFrame('popup_export_iframe');
		$I->waitForText("Shall only the subscribers of the mailing list be exported, for which currently is filtered?");
		$I->see("Shall only the subscribers of the mailing list be exported, for which currently is filtered?");
		$I->clickAndWait(SubsManage::$export_popup_yes, 3);

		// Come back to main window, proceed with export
		$I->switchToIFrame();
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_confirmed);
		$I->click(SubsManage::$export_csv_unarchived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(1);

		// Determine download path depending on user, which process the tests
		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.csv';
		$downloadPath   = $exportPath . $filename;

		// Download export file, check if it is there
		$I->clickAndWait(SubsManage::$export_button_export, 15);
		$I->seeFileFound($filename, $exportPath);

		// Check exported datasets
		$I->openFile($downloadPath);
		$I->seeInThisFile(SubsManage::$subs_c_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a_f);

		$I->dontSeeInThisFile(SubsManage::$subs_c_na);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to export subscribers to CSV file with filtered by mailinglist, export all
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function ExportSubscribersToCSVFilteredNo(AcceptanceTester $I)
	{
		$I->wantTo("export all subscribers to CSV file when filtered by mailinglist");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(SubsManage::$ml_list_id);
		$I->selectOption(SubsManage::$ml_list_id, SubsManage::$ml_select);

		$I->assertFilterResult(SubsManage::$filter_subs_result, SubsManage::$confirmedMainTable);

		// Select yes in modal box
		$I->click(Generals::$toolbar['Export']);
		$I->setIframeName('popup_export_iframe');
		$I->switchToIFrame('popup_export_iframe');
		$I->waitForText("Shall only the subscribers of the mailing list be exported, for which currently is filtered?");
		$I->see("Shall only the subscribers of the mailing list be exported, for which currently is filtered?");
		$I->clickAndWait(SubsManage::$export_popup_no, 3);

		// Come back to main window, proceed with export
		$I->switchToIFrame();
		$I->waitForElementVisible(SubsManage::$import_csv_button, 5);
		$I->click(SubsManage::$import_csv_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_confirmed);
		$I->click(SubsManage::$export_csv_unarchived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(1);

		// Determine download path depending on user, which process the tests
		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.csv';
		$downloadPath   = $exportPath . $filename;

		// Download export file, check if it is there
		$I->clickAndWait(SubsManage::$export_button_export, 15);
		$I->seeFileFound($filename, $exportPath);

		// Check exported datasets
		$I->openFile($downloadPath);
		$I->seeInThisFile(SubsManage::$subs_c_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na_f);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a_f);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a_f);

		$I->seeInThisFile(SubsManage::$subs_c_na);
		$I->dontSeeInThisFile(SubsManage::$subs_c_a);
		$I->dontSeeInThisFile(SubsManage::$subs_u_na);
		$I->dontSeeInThisFile(SubsManage::$subs_u_a);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to export subscribers to XML file
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws Exception
	 */
	public function ExportSubscribersToXML(AcceptanceTester $I)
	{
		$I->wantTo("export subscribers to XML file");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		$I->click(Generals::$toolbar['Export']);
		$I->dontSeeElement(SubsManage::$import_search_button);

		$I->click(SubsManage::$import_xml_button);
		$I->seeElement(SubsManage::$export_csv_confirmed);

		$I->click(SubsManage::$export_csv_confirmed);
		$I->click(SubsManage::$export_csv_unarchived);

		$this->removeAssetIdFromFields($I);

		$I->scrollTo(SubsManage::$subs_footer_div);
		$I->wait(1);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$exportPath     = Generals::$downloadFolder[$user];
		$filename       = 'BackupList_BwPostman_from_' . date("Y-m-d") . '.xml';
		$downloadPath   = $exportPath . $filename;

		$I->clickAndWait(SubsManage::$export_button_export, 15);

		$I->seeFileFound($filename, $exportPath);
		$I->deleteFile($downloadPath);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);
	}

	/**
	 * Test method to test batch processing subscribe/unsubscribe all okay
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.1.2
	 */
	public function BatchSubscribeUnsubscribeOkay(AcceptanceTester $I)
	{
		// Subscribe to new mailinglist
		$I->wantTo("subscribe by batch all okay");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '3'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '3'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to subscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectNew);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '1'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessSubscribe, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);


		// Unsubscribe from new mailinglist
		$I->wantTo("unsubscribe by batch all okay");

		// Select subscribers
		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '3'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '3'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to unsubscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectNew);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '2'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessUnsubscribe, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);
	}

	/**
	 * Test method to test batch processing subscribe/unsubscribe one already subscribed
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.1.2
	 */
	public function BatchSubscribeUnsubscribeAlready(AcceptanceTester $I)
	{
		// Subscribe to new mailinglist
		$I->wantTo("subscribe by batch one already subscribed");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Chiara', sprintf(SubsManage::$tableFirstnameField, '2'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '2'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to subscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectOld);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '1'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessSubscribeAlready, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);


		// Unsubscribe from new mailinglist
		$I->wantTo("unsubscribe by batch");

		// Select subscribers
		// Check if needed subscribers exists
		$I->see('Chiara', sprintf(SubsManage::$tableFirstnameField, '2'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '2'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to unsubscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectOld);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '2'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessUnsubscribeOne, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);
	}

	/**
	 * Test method to test batch processing subscribe/unsubscribe all okay
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.1.2
	 */
	public function BatchSubscribeUnsubscribeNo(AcceptanceTester $I)
	{
		// Subscribe to new mailinglist
		$I->wantTo("subscribe by batch");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '3'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '3'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to subscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectNew);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '1'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessSubscribe, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);


		// Unsubscribe from new mailinglist
		$I->wantTo("unsubscribe by batch");

		// Select subscribers
		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Chiara', sprintf(SubsManage::$tableFirstnameField, '2'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '3'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '2'));
		$I->click(sprintf(SubsManage::$tableSelectField, '3'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to unsubscribe
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectNew);

		// Check subscribe button
		$I->click(sprintf(SubsManage::$batchModalTask, '2'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessUnsubscribe, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);
	}

	/**
	 * Test method to test batch processing subscribe/unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.1.2
	 */
	public function BatchMove(AcceptanceTester $I)
	{
		// Move to other mailinglist
		$I->wantTo("subscribe by batch");
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// Filter for current mailinglist
		$I->clickAndWait(Generals::$filterbar_button, 1);
		$I->clickSelectList(SubsManage::$ml_list, SubsManage::$batchMlSelectOld, SubsManage::$ml_list_id);

		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '2'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '2'));


		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);


		// Select mailinglist to move to
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectNew);

		// Check move button
		$I->click(sprintf(SubsManage::$batchModalTask, '3'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessMoveForward, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);

		// Clear filter
		$I->clickAndWait(Generals::$clear_button, 1);


		// Move to first mailinglist (needed to get initial state)
		// Filter for new mailinglist
		$I->clickAndWait(Generals::$filterbar_button, 1);
		$I->clickSelectList(SubsManage::$ml_list, SubsManage::$batchMlSelectNew, SubsManage::$ml_list_id);

		// Check if needed subscribers exists
		$I->see('Lars', sprintf(SubsManage::$tableFirstnameField, '1'));
		$I->see('Keno', sprintf(SubsManage::$tableFirstnameField, '2'));

		// Select subscribers
		$I->click(sprintf(SubsManage::$tableSelectField, '1'));
		$I->click(sprintf(SubsManage::$tableSelectField, '2'));

		// Click batch
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar['Batch']);
		$I->waitForElementVisible(SubsManage::$batchModalBody, 5);
		$I->wait(3);

		// Select mailinglist to move to
		$I->selectOption(SubsManage::$batchMlList, SubsManage::$batchMlSelectOld);

		// Check move button
		$I->click(sprintf(SubsManage::$batchModalTask, '3'));

		// Click Process
		$I->click(SubsManage::$batchProcess);
		$I->waitForElementVisible(Generals::$alert_success, 5);

		// Check success message
		$I->see(SubsManage::$batchSuccessMoveBack, Generals::$alert_success4);

		// Check no error or warning appears
		$I->dontSee(Generals::$alert_error);
		$I->dontSee(Generals::$alert_warn);
		$I->dontSee(Generals::$alert_info);
	}


	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function prepareSortArray(AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');
		$sort_array = SubsManage::$sort_data_array;

		if (!$options->show_gender)
		{
			unset($sort_array['sort_criteria']['gender']);
			unset($sort_array['sort_criteria_select']['gender']);
			unset($sort_array['select_criteria']['gender']);
		}

		return $sort_array;
	}

	/**
	 * @param AcceptanceTester $I
	 * @param array            $subscribers
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function cleanupImportedSubscribers(AcceptanceTester $I, $subscribers)
	{
		// @ToDo: Check for mailinglist of imported subscribers

		$mailCol = 5;
		$formatCol = 6;
		$options = $I->getManifestOptions('com_bwpostman');

		if (!$options->show_gender)
		{
			$mailCol = 4;
			$formatCol = 5;
		}

		// Check for imported subscribers
		foreach ($subscribers as $subscriber)
		{
			$search_data_array = array(
				'search_by'  => array(
					"Email",
				),
				'search_val' => array($subscriber['email']),
				// array of arrays: outer array per search value, inner arrays per 'search by'
				'search_res' => array(array(1), array(1)),
			);

			$I->click(SubsManage::$tab_confirmed);

			if ($subscriber['status'] == '0')
			{
				$I->click(SubsManage::$tab_unconfirmed);
			}

			$I->searchLoop($I, $search_data_array, true, false, SubsManage::$confirmedMainTable);
			$table_identifier = ".//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td";

			$I->see($subscriber['name'], $table_identifier . '[2]');
			$I->see($subscriber['firstname'], $table_identifier . '[3]');
			$I->see($subscriber['email'], $table_identifier . '[' . $mailCol . ']');

			$format = 'HTML';

			if ($subscriber['emailformat'] == '0')
			{
				$format = 'Text';
			}

			$I->see($format, $table_identifier . '[' . $formatCol . ']');

			// Delete imported subscribers
			$edit_arc_del_array                = SubsEdit::prepareDeleteArray($I);
			$edit_arc_del_array['field_title'] = $subscriber['email'];

			$I->HelperArcDelItems($I, SubsManage::$arc_del_array, $edit_arc_del_array, true);
			$I->see('Subscribers', Generals::$pageTitle);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.4.0
	 */
	private function removeAssetIdFromFields(AcceptanceTester $I)
	{
		$I->selectOption(SubsManage::$exportFieldList, SubsManage::$exportFieldAssetId);
		$I->scrollTo(SubsManage::$exportFieldRemoveButton, 0, -100);
		$I->wait(1);
		$I->clickAndWait(SubsManage::$exportFieldRemoveButton, 1);
	}
}

