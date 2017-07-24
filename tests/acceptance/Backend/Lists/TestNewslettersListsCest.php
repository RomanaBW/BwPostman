<?php
use Page\Generals as Generals;
use Page\NewsletterManagerPage as NlManage;


/**
 * Class TestNewslettersListsCest
 *
 * This class contains all methods to test list view of newsletters at back end
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
		$I->wantTo("Sort unsent newsletters by table header");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, NlManage::$sort_data_array, 'header', $columns, 'newsletters AS `a`', 0, '', 9, 1);
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
		$I->wantTo("Sort unsent newsletters by select list");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, NlManage::$sort_data_array, '', $columns, 'newsletters AS `a`', 0, '', 9, 1);
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
		$I->wantTo("Filter unsent newsletters by author");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);

		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_authors_list);
		// select author 1
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_1, NlManage::$filter_authors_list_id);

		$I->see(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 2
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_2, NlManage::$filter_authors_list_id);

		$I->see(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 3
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_3, NlManage::$filter_authors_list_id);

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
		$I->wantTo("Filter unsent newsletters by campaign");
		$I->amOnPage(NlManage::$url);
		$I->wait(NlManage::$wait_db);

		// Filter single campaign
		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_campaign_list);
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_cam, NlManage::$filter_campaign_list_id);

		$I->assertFilterResult(NlManage::$filter_cam_result);

		// Filter without campaign
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_without, NlManage::$filter_campaign_list_id);

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
		$I->wantTo("Search unsent Newsletters");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);

		$I->searchLoop($I, NlManage::$search_data_array, false);

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
		$I->wantTo("test list limit at unsent newsletters");
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
		$I->wantTo("test pagination at unsent newsletters");
		$I->amOnPage(NlManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10, Generals::$limit_list_id);

		$I->checkPagination($I, NlManage::$pagination_data_array, 10);
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
	public function SortSentNewslettersByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort sent newsletters by table header");
		$I->amOnPage(NlManage::$url);
		$I->wait(1);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$sent_query_criteria);

		$sort_data      = $this->_prepareSortData(NlManage::$sent_sort_data_array);
		$loop_counts    = count($sort_data['select_criteria']) + 1;


		$I->loopFilterList($I, $sort_data, 'header', $columns, 'newsletters AS `a`', 0, '', $loop_counts, 2);

		$I->click(Generals::$submenu_toggle_button);
	}

	/**
	 * Test method sorting sent newsletters by selection at select list
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
	public function SortSentNewslettersBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort sent newsletters by select list");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->wait(1);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$sent_query_criteria);

		$sort_data      = $this->_prepareSortData(NlManage::$sent_sort_data_array);
		$loop_counts    = count($sort_data['select_criteria']) + 1;

			$I->loopFilterList($I, $sort_data, '', $columns, 'newsletters AS `a`', 0, '', $loop_counts, 2);

		$I->click(Generals::$submenu_toggle_button);
	}

	/**
	 * Test method to filter sent newsletters by author
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
	public function FilterSentNewslettersByAuthor(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by author");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_authors_list);
		// select author 1
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_1, NlManage::$filter_authors_list_id);
		$I->wait(1);

		$I->see(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 2
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_2, NlManage::$filter_authors_list_id);
		$I->wait(1);

		$I->see(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 3
		$I->clickSelectList(NlManage::$filter_authors_list, NlManage::$filter_author_3, NlManage::$filter_authors_list_id);
		$I->wait(1);

		$I->see(NlManage::$filter_author_3_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);

		$I->click(Generals::$submenu_toggle_button);
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
	public function FilterSentNewslettersByCampaign(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by campaign");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		// Filter single campaign
		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_campaign_list);
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_cam, NlManage::$filter_campaign_list_id);

		$I->assertFilterResult(NlManage::$filter_sent_cam_result);

		// Filter without campaign
		// select campaign
		$I->clickSelectList(NlManage::$filter_campaign_list, NlManage::$filter_campaign_without, NlManage::$filter_campaign_list_id);

		$I->see(Generals::$null_msg, Generals::$null_row);

		$I->click(Generals::$submenu_toggle_button);
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
/*	public function FilterSentNewslettersByMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by mailinglist");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_mailinglist_list);
		// select mailinglist 1
		$I->clickSelectList(NlManage::$filter_mailinglist_list, NlManage::$filter_mailinglist_1, NlManage::$filter_mailinglist_list_id);

		$I->see(NlManage::$filter_mailinglist_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_3_txt, NlManage::$authors_col);

		// select mailinglist 2
		$I->clickSelectList(NlManage::$filter_mailinglist_list, NlManage::$filter_mailinglist_2, NlManage::$filter_mailinglist_list_id);

		$I->see(NlManage::$filter_mailinglist_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_3_txt, NlManage::$authors_col);

		// select mailinglist 3
		$I->clickSelectList(NlManage::$filter_mailinglist_list, NlManage::$filter_mailinglist_3, NlManage::$filter_mailinglist_list_id);

		$I->see(NlManage::$filter_mailinglist_3_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_mailinglist_2_txt, NlManage::$authors_col);

		$I->click(Generals::$submenu_toggle_button);
	}
*/
	/**
	 * Test method to filter sent newsletters by status
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
	public function FilterSentNewslettersByStatus(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by status");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		$I->filterByStatus($I);

		$I->click(Generals::$submenu_toggle_button);
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
	public function SearchSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("Search sent Newsletters");
		NlManage::$wait_db;
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		$I->searchLoop($I, NlManage::$search_sent_data_array, false);

		$I->click(Generals::$clear_button);
		$I->see(NlManage::$search_sent_clear_val);

//		$I->click(Generals::$submenu_toggle_button);
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
	public function ListlimitSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at sent newsletters");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		$I->checkListlimit($I);

		$I->click(Generals::$submenu_toggle_button);
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
	public function PaginationSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at sent newsletters");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);
		$I->click(Generals::$submenu_toggle_button);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_10, Generals::$limit_list_id);

		$I->checkPagination($I, NlManage::$pagination_sent_data_array, 10);

		$I->scrollTo(Generals::$pageTitle);

		$I->click(Generals::$submenu_toggle_button);
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

	/**
	 * @param $sort_data
	 *
	 * @return array $sort_data
	 *
	 * @since 2.0.0
	 */
	private function _prepareSortData($sort_data)
	{
		$bwpm_version = getenv('BW_TEST_BWPM_VERSION');

/*		if ($bwpm_version == 132)
		{
			unset($sort_data['sort_criteria']['publish_up']);
			unset($sort_data['sort_criteria']['publish_down']);
			unset($sort_data['sort_criteria_select']['publish_up']);
			unset($sort_data['sort_criteria_select']['publish_down']);
			unset($sort_data['select_criteria']['publish_up']);
			unset($sort_data['select_criteria']['publish_down']);
		}
*/		return $sort_data;
	}
}
