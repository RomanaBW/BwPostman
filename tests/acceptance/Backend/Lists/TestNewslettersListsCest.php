<?php
use Page\Generals as Generals;
use Page\NewsletterManagerPage as NlManage;


/**
 * Class TestNewslettersListsCest
 *
 * This class contains all methods to test list view of newsletters at back end
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
class TestNewslettersListsCest
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
	 * Test method sorting newsletters by click to column in table header
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
	public function SortNewslettersByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort newsletters by table header");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->wait(3);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, new NlManage(), 'header', $columns, 'newsletters AS `a`', 0, '', 9);
	}

	/**
	 * Test method sorting newsletters by selection at select list
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
	public function SortNewslettersBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort newsletters by select list");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->wait(3);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, new NlManage(), '', $columns, 'newsletters AS `a`', 0, '', 9);
	}

	/**
	 * Test method to filter newsletters by author
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
	public function FilterNewslettersByAuthor(AcceptanceTester $I)
	{
		$I->wantTo("Filter newsletters by author");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);

		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select author 1
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_1);

		$I->see(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 2
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_2);

		$I->see(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 3
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_3);

		$I->see(NlManage::$filter_author_3_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
	}

	/**
	 * Test method to filter newsletters by campaign
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
	public function FilterNewslettersByCampaign(AcceptanceTester $I)
	{
		$I->wantTo("Filter newsletters by campaign");
		$I->amOnPage(NlManage::$url);
		$I->wait(NlManage::$wait_db);

		// Filter single campaign
		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_cam);

		$I->assertFilterResult(NlManage::$filter_cam_result);

		// Filter without campaign
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_without);

		$I->assertFilterResult(NlManage::$filter_nocam_result);
	}

	/**
	 * Test method to search newsletters
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
	public function SearchNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("Search Newsletters");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);

		$I->searchLoop($I, new NlManage(), false);

		$I->click(Generals::$clear_button);
		$I->see(NlManage::$search_clear_val);
	}

	/**
	 * Test method to check list limit of newsletters
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
	public function ListlimitNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at newsletters");
		$I->amOnPage(NlManage::$url);

		$I->checkListlimit($I);
	}

	/**
	 * Test method to check pagination of newsletters
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
	public function PaginationNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at newsletters");
		$I->amOnPage(NlManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10);

		$I->checkPagination($I, new NlManage(), 10);
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
