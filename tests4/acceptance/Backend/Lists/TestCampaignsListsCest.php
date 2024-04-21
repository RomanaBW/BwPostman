<?php
use Page\Generals as Generals;
use Page\CampaignManagerPage as CamManage;

/**
 * Class TestCampaignsListsCest
 *
 * This class contains all methods to test list view of campaigns at back end
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
class TestCampaignsListsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 *
	 */
	public function _login(\Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method sorting campaigns by click to column in table header
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
	public function SortCampaignsByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort campaigns by table header");
		$I->amOnPage(CamManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', CamManage::$query_criteria);
		$columns    = str_replace('newsletters', $I->getQueryNumberOfNewsletters(), $columns);
		$I->loopFilterList($I, CamManage::$sort_data_array, 'header', $columns, 'campaigns AS `a`', 0, '', 6, 1);
	}

	/**
	 * Test method sorting campaigns by selection at select list
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
	public function SortCampaignsBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort campaigns by select list");
		$I->amOnPage(CamManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', CamManage::$query_criteria);
		$columns    = str_replace('newsletters', $I->getQueryNumberOfNewsletters(), $columns);
		$I->loopFilterList($I, CamManage::$sort_data_array, '', $columns, 'campaigns AS `a`', 0, '', 6, 1);
	}

	/**
	 * Test method to search campaigns
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
	public function SearchCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("Search Campaigns");
		$I->amOnPage(CamManage::$url);

		$I->searchLoop($I, CamManage::$search_data_array, true);

		$I->click(Generals::$clear_button);
		$I->see(CamManage::$search_clear_val);
	}

	/**
	 * Test method to check list limit of campaigns
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
	 *@since   2.0.0
	 *
	 */
	public function ListlimitCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at campaigns");
		$I->amOnPage(CamManage::$url);

		$I->checkListlimit($I);
	}

	/**
	 * Test method to check pagination of campaigns
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
	 *@since   2.0.0
	 *
	 */
	public function PaginationCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at campaigns");
		$I->amOnPage(CamManage::$url);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$limit_list_id);
		$I->selectOption(Generals::$limit_list_id, Generals::$limit_5);
		$I->waitForElementNotVisible(Generals::$filterOptionsPopup, 10);
		$I->assertEquals(5, count($I->GetTableRows($I)));

		$I->checkPagination($I, CamManage::$pagination_data_array, 5);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *@since   2.0.0
	 *
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
