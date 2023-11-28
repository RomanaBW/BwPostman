<?php
use Page\Generals as Generals;
use Page\NewsletterManagerPage as NlManage;
use Page\NewsletterEditPage as NlEdit;

/**
 * Class TestNewslettersListsCest
 *
 * This class contains all methods to test list view of newsletters at back end
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
class TestNewslettersListsCest
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortNewslettersByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort unsent newsletters by table header");
		$I->amOnPage(NlManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, NlManage::$sort_data_array, 'header', $columns, 'newsletters AS `a`', 0, '', 10, 1);
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
	 * @throws \Exception
	 *
	 */
	public function SortNewslettersBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort unsent newsletters by select list");
		$I->amOnPage(NlManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$query_criteria);
		$I->loopFilterList($I, NlManage::$sort_data_array, '', $columns, 'newsletters AS `a`', 0, '', 10, 1);
	}

	/**
	 * Test method to set newsletter as template by icon
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
	public function SetNewsletterIsTemplate(AcceptanceTester $I)
	{

		$I->wantTo("Set/Unset newsletter is template by icon");
		$I->amOnPage(NlManage::$url);

		// switch status by icon
		$I->clickAndWait(NlManage::$set_template_by_icon['is_template_button'], 2);

		$I->see("One newsletter set as content template!");

		$I->seeElement(NlManage::$set_template_by_icon['is_template_result']);

		$I->clickAndWait(NlManage::$set_template_by_icon['is_not_template_button'], 1);
		$I->see("One newsletter unset as content template!");

		$I->seeElement(NlManage::$set_template_by_icon['is_not_template_result']);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterNewslettersByAuthor(AcceptanceTester $I)
	{
		$I->wantTo("Filter unsent newsletters by author");
		$I->amOnPage(NlManage::$url);

		// Get filter bar
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);

		// select author 1
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(NlManage::$filter_authors_list_id);
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_1);

		$I->see(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 2
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_2);

		$I->see(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 3
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_3);

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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterNewslettersByCampaign(AcceptanceTester $I)
	{
		$I->wantTo("Filter unsent newsletters by campaign");
		$I->amOnPage(NlManage::$url);
		$I->wait(NlManage::$wait_db);

		// Filter single campaign

		// select campaign
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(NlManage::$filter_campaign_list_id);
		$I->selectOption(NlManage::$filter_campaign_list_id, NlManage::$filter_campaign_cam);

		$I->assertFilterResult(NlManage::$filter_cam_result);

		// Filter without campaign
		// select campaign
		$I->selectOption(NlManage::$filter_campaign_list_id, NlManage::$filter_campaign_without);

		$I->assertFilterResult(NlManage::$filter_nocam_result);
	}

	/**
	 * Test method to filter newsletters by content template
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
	public function FilterNewslettersByIsTemplate(AcceptanceTester $I)
	{
		$I->wantTo("Filter unsent newsletters by content template");
		$I->amOnPage(NlManage::$url);
		$I->wait(NlManage::$wait_db);

		// Filter content template yes
		// select yes
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(NlManage::$filter_is_template_list_id);
		$I->selectOption(NlManage::$filter_is_template_list_id, NlManage::$filter_is_template_list_yes);

		$I->assertFilterResult(NlManage::$filter_is_template_yes_result);

		// Filter content template no
		$I->selectOption(NlManage::$filter_is_template_list_id, NlManage::$filter_is_template_list_no);

		$I->assertFilterResult(NlManage::$filter_is_template_no_result);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SearchNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("Search unsent Newsletters");
		$I->amOnPage(NlManage::$url);

		$I->searchLoop($I, NlManage::$search_data_array, false, false);

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
	 * @throws \Exception
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function PaginationNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at unsent newsletters");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);

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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortSentNewslettersByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort sent newsletters by table header");
		$I->amOnPage(NlManage::$url);
		$I->wait(1);
		$I->clickAndWait(NlManage::$tab2, 1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$sent_query_criteria);

		$sort_data      = $this->prepareSortData(NlManage::$sent_sort_data_array);
		$loop_counts    = count($sort_data['select_criteria']) + 1;

		$I->loopFilterList($I, $sort_data, 'header', $columns, 'newsletters AS `a`', 0, '', $loop_counts, 2);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SortSentNewslettersBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort sent newsletters by select list");
		$I->amOnPage(NlManage::$url);
		$I->wait(1);
		$I->clickAndWait(NlManage::$tab2, 1);

		// loop over sorting criterion
		$columns    = implode(', ', NlManage::$sent_query_criteria);

		$sort_data      = $this->prepareSortData(NlManage::$sent_sort_data_array);
		$loop_counts    = count($sort_data['select_criteria']) + 1;

		$I->loopFilterList($I, $sort_data, '', $columns, 'newsletters AS `a`', 0, '', $loop_counts, 2);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterSentNewslettersByAuthor(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by author");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

		// select author 1
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(NlManage::$filter_authors_list_id);
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_1);

		$I->see(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 2
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_2);


		$I->see(NlManage::$filter_author_2_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_1_txt, NlManage::$authors_col);
		$I->dontSee(NlManage::$filter_author_3_txt, NlManage::$authors_col);

		// select author 3
		$I->selectOption(NlManage::$filter_authors_list_id, NlManage::$filter_author_3);

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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterSentNewslettersByCampaign(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by campaign");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

		// Filter single campaign
		// select campaign
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(NlManage::$filter_campaign_list_id);
		$I->selectOption(NlManage::$filter_campaign_list_id, NlManage::$filter_campaign_cam);

		$I->assertFilterResult(NlManage::$filter_sent_cam_result);

		// Filter without campaign
		// select campaign
		$I->selectOption(NlManage::$filter_campaign_list_id, NlManage::$filter_campaign_without);

		$I->see(Generals::$null_msg, Generals::$null_row);
	}

	/**
	 * Test method to filter newsletters by mailinglist
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

		// Get filter bar
		$I->click(Generals::$filterbar_button);
		$I->waitForElementVisible(NlManage::$filter_mailinglist_list, 5);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function FilterSentNewslettersByStatus(AcceptanceTester $I)
	{
		$I->wantTo("Filter sent newsletters by status");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->filterByStatus($I);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function SearchSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("Search sent Newsletters");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->searchLoop($I, NlManage::$search_sent_data_array, false, false);

		$I->click(Generals::$clear_button);
		$I->see(NlManage::$search_sent_clear_val);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function ListlimitSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at sent newsletters");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function PaginationSentNewsletters(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at sent newsletters");
		$I->amOnPage(NlManage::$url);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);

		$I->checkPagination($I, NlManage::$pagination_sent_data_array, 10);

		$I->scrollTo(Generals::$pageTitle);
		$I->wait(1);
	}

    /**
     * Test method to check reset sending trials in queue and send anew
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
    public function ResetSendingTrialsAndSendAnewQueue(AcceptanceTester $I)
    {
        $I->wantTo("reset sending trials at queue");
        $I->amOnPage(NlManage::$url);

        $this->buildQueue($I);

        $I->clickAndWait(NlManage::$tab3, 1);
        $I->clickAndWait(Generals::$systemMessageClose, 1);

        $trial_value    = $I->grabTextFrom(NlManage::$queue_sending_trials_col);
        $I->assertNotEquals(0, intval($trial_value));

        $I->clickAndWait(Generals::$toolbar['Reset sending trials'], 1);

        $trial_value    = $I->grabTextFrom(NlManage::$queue_sending_trials_col);
        $I->assertEquals(0, intval($trial_value));

        $I->setExtensionStatus('bwtestmode', 1);
        $I->setManifestOption('bwtestmode', 'arise_queue_option', '0');
        $I->setManifestOption('bwtestmode', 'suppress_sending', '1');

        $I->clickAndWait(Generals::$toolbar4['Continue sending'], 1);
        $I->setExtensionStatus('bwtestmode', 0);
        $I->setManifestOption('bwtestmode', 'suppress_sending', '0');

        $I->waitForElementVisible(NlManage::$sendLayout, 5);
        $I->waitForElementVisible(NlEdit::$success_send_number_id, 10);
        $I->waitForText(NlEdit::$success_send_ready, 180);
        $I->see(NlEdit::$success_send_ready);

        $I->click(NlManage::$sendLayoutBack);
        $I->waitForElementVisible(Generals::$page_header, 10);
        $I->see("Newsletters", Generals::$pageTitle);

        $I->clickAndWait(NlManage::$tab2, 1);

        $I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function ListlimitQueue(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at queue");
		$I->amOnPage(NlManage::$url);

		$this->buildQueue($I);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(NlManage::$tab3, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->checkListlimit($I);

		$this->cleanupQueue($I);

		$I->see("Newsletters", Generals::$pageTitle);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function PaginationQueue(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at queue");
		$I->amOnPage(NlManage::$url);

		$this->buildQueue($I);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(NlManage::$tab3, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_10);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);

		//Sort table by recipient
		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$ordering_list);
		$I->selectOption(Generals::$ordering_list, "Recipient ascending");
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);

		$I->checkPagination($I, NlManage::$pagination_queue_data_array, 10);

		$I->scrollTo(Generals::$pageTitle);
		$I->wait(1);

		$this->cleanupQueue($I);

		$I->see("Newsletters", Generals::$pageTitle);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
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
	 * @param $sort_data
	 *
	 * @return array $sort_data
	 *
	 * @since 2.0.0
	 */
	private function prepareSortData($sort_data)
	{
//		$bwpm_version = getenv('BW_TEST_BWPM_VERSION');

		/*
		if ($bwpm_version == 132)
		{
			unset($sort_data['sort_criteria']['publish_up']);
			unset($sort_data['sort_criteria']['publish_down']);
			unset($sort_data['sort_criteria_select']['publish_up']);
			unset($sort_data['sort_criteria_select']['publish_down']);
			unset($sort_data['select_criteria']['publish_up']);
			unset($sort_data['select_criteria']['publish_down']);
		}
		*/
		return $sort_data;
	}

	/**
	 * Method to surely build a queue of newsletters not sent
	 *
	 * @param   AcceptanceTester        $I
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function buildQueue(AcceptanceTester $I)
	{
		$I->setExtensionStatus('bwtestmode', 1);
		$I->setManifestOption('bwtestmode', 'arise_queue_option', '1');

		// create newsletter and send (without success)
		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['user']);
		NlEdit::SendNewsletterToRealRecipients($I, false, false, true, 20);

		$I->see(NlManage::$queue_warning_msg);
	}

	/**
	 * Method to surely build a queue of newsletters not sent
	 *
	 * @param   AcceptanceTester        $I
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function cleanupQueue(AcceptanceTester $I)
	{
		$I->setExtensionStatus('bwtestmode', 0);
		$I->setManifestOption('bwtestmode', 'arise_queue_option', '0');

		$I->scrollTo(Generals::$systemMessageClose, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->clickAndWait(Generals::$toolbar['Clear queue'], 1);

		$I->see(NlManage::$queue_cleared_msg);
	}
}
