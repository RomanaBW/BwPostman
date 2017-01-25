<?php
use Page\Generals as Generals;
use Page\CampaignManagerPage as CamManage;


/**
 * Class TestCampaignsListsCest
 *
 * This class contains all methods to test list view of campaigns at back end
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
class TestCampaignsListsCest
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
	 * Test method sorting campaigns by click to column in table header
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
	public function SortCampaignsByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort campaigns by table header");
		$I->amOnPage(CamManage::$url);
		$I->wait(1);

		// loop over sorting criterion
		$columns    = implode(', ', CamManage::$query_criteria);
		$columns    = str_replace('newsletters', $I->getQueryNumberOfNewsletters(), $columns);
		$I->loopFilterList($I, new CamManage(), 'header', $columns, 'campaigns AS `a`', 0, '', 6);
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
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
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
		$I->loopFilterList($I, new CamManage(), '', $columns, 'campaigns AS `a`', 0, '', 6);
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
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SearchCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("Search Campaigns");
		$I->amOnPage(CamManage::$url);

		$I->searchLoop($I, new CamManage(), true);

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
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
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
	 * @group   component
	 * @group   003_be_lists
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function PaginationCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at campaigns");
		$I->amOnPage(CamManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_5);

		$I->checkPagination($I, new CamManage(), 5);
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
