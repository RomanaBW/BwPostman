<?php
use Page\Generals as Generals;
use Page\SubscriberManagerPage as SubsManage;


/**
 * Class TestSubscribersListsCest
 *
 * This class contains all methods to test list view of subscribers at back end
 *
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
	 * @since   2.0.0
	 */
	public function SortSubscribersByTableHeader(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized; only testing problem
		$I->wantTo("Sort subscribers by table header");
		SubsManage::$wait_db;
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, SubsManage::$sort_data_array, 'header', $columns, 'subscribers AS `a`', 0, '1', 10);
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
	 * @since   2.0.0
	 */
	public function SortSubscribersBySelectList(AcceptanceTester $I)
	{
		// @Todo: ensure UTF-8 characters are recognized
		$I->wantTo("Sort subscribers by select list");
		SubsManage::$wait_db;
		$I->amOnPage(SubsManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', SubsManage::$query_criteria);
		$columns    = str_replace('mailinglists', $I->getQueryNumberOfMailinglists(), $columns);
		$I->loopFilterList($I, SubsManage::$sort_data_array, '', $columns, 'subscribers AS `a`', 0, '1', 10);
	}

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
	 * @since   2.0.0
	 */
	public function FilterSubscribersByMailformat(AcceptanceTester $I)
	{
		$I->wantTo("Filter subscribers by email format");
		SubsManage::$wait_db;
		$I->amOnPage(SubsManage::$url);

		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select published
		$I->clickSelectList(SubsManage::$format_list, SubsManage::$format_text);

		$I->dontSee(SubsManage::$format_text_text, SubsManage::$format_text_column);

		// select unpublished
		$I->clickSelectList(SubsManage::$format_list, SubsManage::$format_html);

		$I->dontSee(SubsManage::$format_text_html, SubsManage::$format_text_column);

	}

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
	 * @since   2.0.0
	 */
	public function FilterSubscribersByMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Filter subscribers by mailing list");
		$I->amOnPage(SubsManage::$url);
		$I->wait(SubsManage::$wait_db);

		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select 04 Mailingliste 14 A
		$I->clickSelectList(SubsManage::$ml_list, SubsManage::$ml_select);

		$I->assertFilterResult(SubsManage::$filter_subs_result);
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
	 * @since   2.0.0
	 */
	public function SearchSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("Search Subscribers");
		SubsManage::$wait_db;
		$I->amOnPage(SubsManage::$url);

		$I->searchLoop($I, SubsManage::$search_data_array, true);

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
	 */
	public function ListlimitSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->checkListlimit($I);
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
	 * @since   2.0.0
	 */
	public function PaginationSubscribers(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at subscribers");
		$I->amOnPage(SubsManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10);

		$I->checkPagination($I, SubsManage::$pagination_data_array, 10);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

}
