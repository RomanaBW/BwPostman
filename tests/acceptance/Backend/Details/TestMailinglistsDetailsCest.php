<?php
use Page\Generals as Generals;
use Page\MailinglistEditPage as MlEdit;
use Page\MailinglistManagerPage as MlManage;
use Page\MainviewPage as MainView;


/**
 * Class TestMailinglistsDetailsCest
 *
 * This class contains all methods to test manipulation of a single mailing list at back end

 * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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
class TestMailinglistsDetailsCest
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
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateOneMailinglistCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one mailinglist and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->wait(5);

		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addMailinglistButton);

		MlEdit::fillFormSimple($I);

		$I->clickAndWait(MlEdit::$toolbar['Back'], 1);

		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from main view, save it and go back to main view
	 *
	 * @param   AcceptanceTester            $I
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
	public function CreateOneMailinglistCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one mailinglist complete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see('BwPostman', Generals::$pageTitle);
		$I->click(MainView::$addMailinglistButton);

		$this->fillFormExtended($I);

		$I->click(MlEdit::$toolbar['Save & Close']);
		$I->see("Message", Generals::$alert_header);
		$I->see(MlEdit::$success_save, Generals::$alert_msg);

		$I->HelperArcDelItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array, true);
		$I->see('Mailinglists', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from list view and cancel creation
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
	public function CreateOneMailinglistCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one mailinglist cancel list view");
		$I->amOnPage(MlManage::$url);
		$I->click(Generals::$toolbar['New']);

		MlEdit::fillFormSimple($I);

		$I->clickAndWait(MlEdit::$toolbar['Cancel'], 1);
		$I->see("Mailinglists", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from list view, save it and go back to list view
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
	public function CreateOneMailinglistCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one mailinglist list view");
		$I->amOnPage(MlManage::$url);
		$I->click(Generals::$toolbar['New']);

		$this->fillFormExtended($I);

		$I->click(MlEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(MlEdit::$success_save, Generals::$alert_msg);

		$I->HelperArcDelItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array, true);
		$I->see('Mailinglists', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from list view, save it and go back to list view
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
	public function CreateOneMailinglistListViewRestore(AcceptanceTester $I)
	{
		$I->wantTo("Create one mailinglist, archive, restore, archive and delete");
		$I->amOnPage(MlManage::$url);
		$I->click(Generals::$toolbar['New']);

		MlEdit::fillFormSimple($I);
		$I->click(MlEdit::$toolbar['Save & Close']);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(MlEdit::$success_save, Generals::$alert_msg);

		$I->HelperArchiveItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array);

		$I->switchToArchive($I, MlEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array);

		$I->amOnPage(MlManage::$url);

		$I->HelperArcDelItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array, true);
		$I->see('Mailinglists', Generals::$pageTitle);
	}

	/**
	 * Test method to create same single mailing list twice from main view
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
	public function CreateMailinglistTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create mailinglist twice list view");
		$I->amOnPage(MlManage::$url);
		$I->click(Generals::$toolbar['New']);

		MlEdit::fillFormSimple($I);

		$I->click(MlEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(MlEdit::$success_save, Generals::$alert_msg);
		$I->see('Mailinglists', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		MlEdit::fillFormSimple($I);

		$I->click(MlEdit::$toolbar['Save & Close']);
		$I->see("Error", Generals::$alert_header);
		$I->see(MlEdit::$error_save, Generals::$alert_error);
		$I->click(MlEdit::$toolbar['Cancel']);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see("Mailinglists", Generals::$pageTitle);

		$I->HelperArcDelItems($I, MlManage::$arc_del_array, MlEdit::$arc_del_array, true);
		$I->see('Mailinglists', Generals::$pageTitle);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
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
	 * Method to fill form with check of required fields
	 * This method fills in the end all fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtended(AcceptanceTester $I)
	{
		// fill title, omit description
		$I->fillField(MlEdit::$title, MlEdit::$field_title);
		$I->clickAndWait(MlEdit::$toolbar['Save & Close'], 1);

		// check for description filled
		$I->seeInPopup(MlEdit::$popup_description);
		$I->acceptPopup();

		// fill description, omit title
		$I->fillField(MlEdit::$title, "");
		$I->fillField(MlEdit::$description, MlEdit::$field_description);
		$I->click(MlEdit::$toolbar['Save & Close']);

		// check for title filled
		$I->seeInPopup(MlEdit::$popup_title);
		$I->acceptPopup();

		// fill title
		$I->fillField(MlEdit::$title, MlEdit::$field_title);

		// select access
		$I->clickSelectList(MlEdit::$access_list, MlEdit::$access_registered, MlEdit::$access_list_id);
		$I->see("Registered", MlEdit::$access_list_text);

		//select status
		$I->clickSelectList(MlEdit::$published_list, MlEdit::$published_published, MlEdit::$published_list_id);
		$I->see("published", MlEdit::$published_list_text);
	}
}
