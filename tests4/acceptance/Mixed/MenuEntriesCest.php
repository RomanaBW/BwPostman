<?php
use Page\Generals as Generals;
use Page\MenuEntriesManagerPage as MEManage;

/**
 * Class MenuEntriesCest
 *
 * This class contains all methods to test creating menu entries
 *
 *  * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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
class MenuEntriesCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login     $loginPage
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
	 * Test method to create a registration menu entry
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
	public function createRegistrationEntry(AcceptanceTester $I)
	{
		$I->wantTo("Create a registration menu entry in main menu");
		$I->expectTo("see new menu entry for registration in main menu");

		$I->amOnPage(MEManage::$main_menu_url);
		$I->see(MEManage::$main_menu_txt, MEManage::$main_menu_label);

		// create menu entry in BE
		$this->createMenuEntry($I, MEManage::$fill_title_register, MEManage::$menu_entry_type_txt_register, MEManage::$menu_entry_link_txt_register);

		// check menu entry in FE
		$FE = $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$this->checkMenuEntryInFE(
					$I,
					MEManage::$fill_title_register,
					MEManage::$main_menu_register,
					MEManage::$menu_entry_msg_register,
					MEManage::$menu_entry_identifier_register
				);
			}
		);

		// delete menu entry by subroutine
		$this->deleteMenuItem($I, MEManage::$fill_title_register);
	}

	/**
	 * Test method to create an edit subscription menu entry
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
	public function createEditSubscriptionEntry(AcceptanceTester $I)
	{
		$I->wantTo("Create an edit subscription menu entry in main menu");
		$I->expectTo("see new menu entry to edit subscription in main menu");

		$I->amOnPage(MEManage::$main_menu_url);
		$I->see(MEManage::$main_menu_txt, MEManage::$main_menu_label);

		// create menu entry in BE
		$this->createMenuEntry($I, MEManage::$fill_title_edit, MEManage::$menu_entry_type_txt_edit, MEManage::$menu_entry_link_txt_edit);

		// check menu entry in FE
		$FE = $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$this->checkMenuEntryInFE(
					$I,
					MEManage::$fill_title_edit,
					MEManage::$main_menu_edit,
					MEManage::$menu_entry_msg_edit,
					MEManage::$menu_entry_identifier_edit
				);
			}
		);

		// delete menu entry by subroutine
		$this->deleteMenuItem($I, MEManage::$fill_title_edit);
	}

	/**
	 * Test method to create a menu entry to show newsletter list in FE
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
	public function createNewsletterListEntry(AcceptanceTester $I)
	{
		$I->wantTo("Create an newsletter list menu entry in main menu");
		$I->expectTo("see new menu entry for newsletter list in main menu");

		$I->amOnPage(MEManage::$main_menu_url);
		$I->see(MEManage::$main_menu_txt, MEManage::$main_menu_label);

		// create menu entry in BE
		$this->createMenuEntry($I, MEManage::$fill_title_nl_list, MEManage::$menu_entry_type_txt_nl_list, MEManage::$menu_entry_link_txt_nl_list);

		// @ToDo: Select Mailinglists, without this I get empty list
		$I->clickAndWait(MEManage::$fill_title_nl_list, 1);
		$I->seeInField(MEManage::$menu_entry_title, MEManage::$fill_title_nl_list);
		$I->clickAndWait(MEManage::$list_tab_recipients, 1);
		$I->clickAndWait(MEManage::$select_ml_all, 1);
		$I->clickAndWait(MEManage::$menu_save, 1);

		// check menu entry in FE
		$FE = $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$this->checkMenuEntryInFE(
					$I,
					MEManage::$fill_title_nl_list,
					MEManage::$main_menu_nl_list,
					MEManage::$menu_entry_msg_nl_list,
					MEManage::$menu_entry_identifier_nl_list
				);
			}
		);

		// delete menu entry by subroutine
		$this->deleteMenuItem($I, MEManage::$fill_title_nl_list);
	}

	/**
	 * Test method to create a menu entry to show a single newsletter in FE
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
	public function createNewsletterSingleEntry(AcceptanceTester $I)
	{
		$I->wantTo("Create an single newsletter menu entry in main menu");
		$I->expectTo("see new menu entry for a single newsletter in main menu");

		$I->amOnPage(MEManage::$main_menu_url);
		$I->see(MEManage::$main_menu_txt, MEManage::$main_menu_label);

		// create menu entry in BE
		$this->createMenuEntry(
			$I,
			MEManage::$fill_title_nl_single,
			MEManage::$menu_entry_type_txt_nl_single,
			MEManage::$menu_entry_link_txt_nl_single
		);

		// check menu entry in FE
		$FE = $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$this->checkMenuEntryInFE(
					$I,
					MEManage::$fill_title_nl_single,
					MEManage::$main_menu_nl_single,
					MEManage::$menu_entry_msg_nl_single,
					MEManage::$menu_entry_identifier_nl_single
				);
			}
		);

		// delete menu entry by subroutine
		$this->deleteMenuItem($I, MEManage::$fill_title_nl_single);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   string                  $item   the item to delete
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function deleteMenuItem(AcceptanceTester $I, $item)
	{
		// reduce view to main menu
		$I->clickSelectList(MEManage::$main_menu_select, MEManage::$main_menu_select_mainmenu, MEManage::$main_menu_select_id);

		// filter for item to remove
		$I->fillField(Generals::$search_field, $item);
		$I->clickAndWait(MEManage::$filter_search_button, 1);
		$I->see($item, MEManage::$remove_title_col);

		// put item into trash
		$I->clickAndWait(Generals::$check_all_button, 1);
		$I->click(MEManage::$trash_button);
		$I->see(MEManage::$filter_empty_msg, MEManage::$filter_empty_field);

		// switch to trash
		$I->clickAndWait(MEManage::$filterbar_button, 1);
		$I->clickSelectList(MEManage::$filter_status, MEManage::$filter_status_trashed, MEManage::$filter_status_id);

		// remove item from trash
		$I->clickAndWait(Generals::$check_all_button, 1);
		$I->click(MEManage::$trash_empty_button);
		$I->acceptPopup();
		$I->see(MEManage::$filter_empty_msg, MEManage::$filter_empty_field);

		// clear filter
		$I->clickAndWait(MEManage::$clear_button, 1);
	}

	/**
	 * Method to create a menu entry
	 *
	 * @param AcceptanceTester  $I
	 * @param string            $title
	 * @param string            $type
	 * @param string            $link
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function createMenuEntry(AcceptanceTester $I, $title, $type, $link)
	{
		$I->clickAndWait(MEManage::$new_entry_button, 1);

		$I->fillField(MEManage::$menu_entry_title, $title);
		/*
		// attach a name so that we can switch to the iframe later
		$I->executeJS('jQuery(".iframe").attr("name", "blah")');
		$I->switchToIFrame("blah");
		// This does not work because iframe is not named and has no ID
		$I->clickAndWait(MEManage::$menu_entry_type, 3);

		// $iframe = $I->haveFriend("Menus: New Item - Bw-Test - Administration");

		$I->switchToIFrame(MEManage::$iframe_type);
		$I->clickAndWait(MEManage::$menu_type_bwpm, 1);
		$I->clickAndWait(MEManage::$menu_type_register, 2);
		*/

		// Workaround for missing iframe name, part 1
		$I->fillReadonlyInput("jform_type", MEManage::$menu_entry_type_field, $type);
		$I->fillReadonlyInput("jform_link", MEManage::$menu_entry_link_field, $link);

		if ($type == MEManage::$menu_entry_type_txt_nl_single)
		{
			$I->clickAndWait(MEManage::$menu_apply, 3);
			$I->see(MEManage::$selected_nl_no_title, MEManage::$selected_nl);
			$I->clickAndWait(MEManage::$menu_entry_select_nl, 10);
			$I->switchToIFrame(MEManage::$iframe_nls);
			$I->clickAndWait(MEManage::$selected_nl, 2);
			$I->see(MEManage::$selected_nl_title, MEManage::$selected_nl);
		}

		$I->clickAndWait(MEManage::$menu_save, 1);

		// Workaround for missing iframe name, part 2
		// inject extension ID in database table #__menu for just created menu entry
		$I->setComponentIdInMenu($title);
		// reload page
		$I->amOnPage(MEManage::$main_menu_url);
	}

	/**
	 * @param AcceptanceTester  $I
	 * @param string            $title
	 * @param string            $entry
	 * @param string            $control_msg
	 * @param string            $control_identifier
	 *
	 * @since 2.0.0
	 */
	private function checkMenuEntryInFE(AcceptanceTester $I, $title, $entry, $control_msg, $control_identifier)
	{
		$I->amOnPage(MEManage::$home);

		$I->see($title, $entry);

		$I->clickAndWait($title, 1);
		$I->see($control_msg, $control_identifier);
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
