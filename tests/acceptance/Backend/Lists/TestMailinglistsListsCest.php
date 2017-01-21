<?php
use Page\Generals as Generals;
use Page\MailinglistManagerPage as MlManage;


/**
 * Class TestMailinglistsListsCest
 *
 * This class contains all methods to test list view of mailing lists at back end
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
class TestMailinglistsListsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
	 *
	 * @group   component
	 * @group   003_be_lists
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
	 * Test method to publish mailing lists by icon
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function PublishMailinglistsByIcon(AcceptanceTester $I)
	{

		$I->wantTo("Publish/Unpublish Mailinglists by icon");
		$I->amOnPage(MlManage::$url);

		$I->publishByIcon($I, new MlManage(), 'mailing list');
	}

	/**
	 * Test method to publish mailing lists by toolbar button
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function PublishMailinglistsByToolbar(AcceptanceTester $I)
	{

		$I->wantTo("Publish/Unpublish Mailinglists by toolbar buttons");
		$I->amOnPage(MlManage::$url);

		$I->publishByToolbar($I, new MlManage(), 'mailing list');
	}

	/**
	 * Test method sorting mailing lists by click to column in table header
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SortMailinglistsByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort mailinglists by table header");
		$I->amOnPage(MlManage::$url);
		$I->wait(5);

		// loop over sorting criterion
		$columns    = implode(', ', MlManage::$query_criteria);
		$columns    = str_replace('subscribers', $I->getQueryNumberOfSubscribers(), $columns);
		$I->loopFilterList($I, new MlManage(), 'header', $columns, 'mailinglists AS `a`', 0, '', 8);
	}

	/**
	 * Test method sorting mailing lists by selection at select list
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SortMailinglistsBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort mailinglists by select list");
		$I->amOnPage(MlManage::$url);
		$I->wait(5);

		// loop over sorting criterion
		$columns    = implode(', ', MlManage::$query_criteria);
		$columns    = str_replace('subscribers', $I->getQueryNumberOfSubscribers(), $columns);
		$I->loopFilterList($I, new MlManage(), '', $columns, 'mailinglists AS `a`', 0, '', 8);
	}

	/**
	 * Test method to filter mailing lists by status
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function FilterMailinglistsByStatus(AcceptanceTester $I)
	{
		$I->wantTo("Filter mailinglists by status");
		$I->amOnPage(MlManage::$url);

		$I->filterByStatus($I);
	}

	/**
	 * Test method to filter mailing lists by access
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function FilterMailinglistsByAccess(AcceptanceTester $I)
	{
		$I->wantTo("Filter mailinglists by access");
		$I->amOnPage(MlManage::$url);

		$I->filterByAccess($I);
	}

	/**
	 * Test method to search mailing lists
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SearchMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Search Mailinglists");
		$I->amOnPage(MlManage::$url);

		$I->searchLoop($I, new MlManage(), true);

		$I->click(Generals::$clear_button);
		$I->see(MlManage::$search_clear_val);
	}

	/**
	 * Test method to check list limit of mailing lists
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function ListlimitMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at mailinglists");
		$I->amOnPage(MlManage::$url);

		$I->checkListlimit($I);
	}

	/**
	 * Test method to check pagination of mailing lists
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function PaginationMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at mailinglists");
		$I->amOnPage(MlManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10);

		$I->checkPagination($I, new MlManage(), 10);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @group   component
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
