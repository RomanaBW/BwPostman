<?php
use Page\Generals as Generals;
use Page\TemplateManagerPage as TplManage;

/**
 * Class TestTemplatesListsCest
 *
 * This class contains all methods to test list view of templates at back end
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
class TestTemplatesListsCest
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
	 * Test method to publish templates by icon
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
	public function PublishTemplatesByIcon(AcceptanceTester $I)
	{
		$I->wantTo("Publish/Unpublish Templates by icon");
		$I->amOnPage(TplManage::$url);

		$I->publishByIcon($I, TplManage::$publish_by_icon, 'template');
	}

	/**
	 * Test method to publish templates by toolbar button
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
	public function PublishTemplatesByToolbar(AcceptanceTester $I)
	{
		$I->wantTo("Publish/Unpublish Templates by toolbar buttons");
		$I->amOnPage(TplManage::$url);

		$I->publishByToolbar($I, TplManage::$publish_by_toolbar, 'template');
	}

	/**
	 * Test method sorting templates by click to column in table header
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
	public function SortTemplatesByTableHeader(AcceptanceTester $I)
	{
		$I->wantTo("Sort templates by table header");
		$I->amOnPage(TplManage::$url);
		$I->wait(1);

		$sidebarToggle = $I->grabAttributeFrom(".//div[@id='j-sidebar-container']", 'class');
		codecept_debug("Sidebar toggle class:");
		codecept_debug($sidebarToggle);
		if (strpos($sidebarToggle, 'j-sidebar-hidden') !== false)
		{
			$I->click(Generals::$submenu_toggle_button);
		}

		// loop over sorting criterion
		$columns    = implode(', ', TplManage::$query_criteria);
		$columns    = str_replace('subscribers', $I->getQueryNumberOfSubscribers(), $columns);
		$I->loopFilterList($I, TplManage::$sort_data_array, 'header', $columns, 'templates AS `a`', 0, '', 9, 1);
	}

	/**
	 * Test method sorting templates by selection at select list
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
	public function SortTemplatesBySelectList(AcceptanceTester $I)
	{
		$I->wantTo("Sort templates by select list");
		$I->amOnPage(TplManage::$url);
		$I->wait(1);

		// loop over sorting criterion     .//*[@id='list_fullordering_chzn']/div/ul/li[4]
		$columns    = implode(', ', TplManage::$query_criteria);
		$columns    = str_replace('subscribers', $I->getQueryNumberOfSubscribers(), $columns);
		$I->loopFilterList($I, TplManage::$sort_data_array, '', $columns, 'templates AS `a`', 0, '', 9, 1);
	}

	/**
	 * Test method to filter templates by status
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
	public function FilterTemplatesByStatus(AcceptanceTester $I)
	{
		$I->wantTo("Filter templates by status");
		$I->amOnPage(TplManage::$url);

		$I->filterByStatus($I);
	}

	/**
	 * Test method to filter templates by access
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
	public function FilterTemplatesByMailformat(AcceptanceTester $I)
	{
		$I->wantTo("Filter templates by email format");
		$I->amOnPage(TplManage::$url);
		$I->wait(1);

		// Get filter bar
		$I->clickAndWait(Generals::$filterbar_button, 1);
		// select published
		$I->clickSelectList(TplManage::$format_list, TplManage::$format_text, TplManage::$format_list_id);

		$I->dontSee(TplManage::$format_text_text, TplManage::$format_text_column);

		// select unpublished
		$I->clickSelectList(TplManage::$format_list, TplManage::$format_html, TplManage::$format_list_id);

		$I->dontSee(TplManage::$format_text_html, TplManage::$format_text_column);
	}

	/**
	 * Test method to search templates
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
	public function SearchTemplates(AcceptanceTester $I)
	{
		$I->wantTo("Search Templates");
		$I->amOnPage(TplManage::$url);

		$I->searchLoop($I, TplManage::$search_data_array, true);

		$I->click(Generals::$clear_button);
		$I->see(TplManage::$search_clear_val);
	}

	/**
	 * Test method to check list limit of templates
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
	public function ListlimitTemplates(AcceptanceTester $I)
	{
		$I->wantTo("test list limit at templates");
		$I->amOnPage(TplManage::$url);

		$I->checkListlimit($I);
	}

	/**
	 * Test method to check pagination of templates
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
	public function PaginationTemplates(AcceptanceTester $I)
	{
		$I->wantTo("test pagination at templates");
		$I->amOnPage(TplManage::$url);

		$I->clickSelectList(Generals::$limit_list, Generals::$limit_5, Generals::$limit_list_id);

		$I->checkPagination($I, TplManage::$pagination_data_array, 5);
	}

	/**
	 * Test method to check pagination of templates
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
	public function SetDefaultTemplates(AcceptanceTester $I)
	{
		$I->wantTo("Switch default template");
		$I->amOnPage(TplManage::$url);

		TplManage::SetDefaultTemplates($I, true);
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
}
