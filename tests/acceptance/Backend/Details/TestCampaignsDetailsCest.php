<?php
use Page\Generals as Generals;
use Page\CampaignEditPage as CamEdit;
use Page\CampaignManagerPage as CamManage;
use Page\MainviewPage as MainView;

// @ToDo: Check for required fields by server (JS switched off)
// @ToDo: Assign newsletters to campaign, perhaps send some of the newsletters, check for appearance in campaign details,
// clear out with and without newsletters

/**
 * Class TestCampaignsDetailsCest
 *
 * This class contains all methods to test manipulation of a single campaign at back end

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
class TestCampaignsDetailsCest
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
	 * Test method to create a single campaign from main view and cancel creation
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
	public function CreateOneCampaignCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign and cancel from main view");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addCampaignButton);
		$I->waitForText('Campaign details', 30);

		CamEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbar4['Back'], 1);

		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from main view, save it and go back to main view
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
	public function CreateOneCampaignCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign complete from main view");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addCampaignButton);
		$I->waitForText('Campaign details', 30);

		$this->fillFormExtended($I);
		$I->click(Generals::$toolbar4['Save & Close']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from list view and cancel creation
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
	public function CreateOneCampaignCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign cancel list view");
		$I->amOnPage(CamManage::$url);
		$I->click(Generals::$toolbar['New']);

		$this->fillFormExtended($I);

		$I->click(Generals::$toolbar4['Cancel']);
		$I->see("Campaigns", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from list view, save it and go back to list view
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
	public function CreateOneCampaignCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign list view");
		$I->amOnPage(CamManage::$url);
		$I->click(Generals::$toolbar['New']);

		CamEdit::fillFormSimple($I);
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 2);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->see(CamEdit::$field_title, CamEdit::$title_col);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from main view and click Save & New
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
	public function CreateOneCampaignSaveNewMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign, save and get new record from main view");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addCampaignButton);
		$I->waitForText('Campaign details', 30);

		CamEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save & New'], 1);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->see('', CamEdit::$title);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$toolbar['Cancel']);
		$I->see("Campaigns", Generals::$pageTitle);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from main view, modify and save as copy
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
	public function CreateOneCampaignSaveCopyMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign, save, modify and save as copy");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addCampaignButton);
		$I->waitForText('Campaign details', 30);

		CamEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->seeInField(CamEdit::$title, CamEdit::$field_title);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// Grab ID of first campaign
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_campaigns', 'id', array('title' => CamEdit::$field_title));

		$I->fillField(CamEdit::$title, CamEdit::$field_title2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->seeInField(CamEdit::$title, CamEdit::$field_title2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// Grab ID of second campaign
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_campaigns', 'id', array('title' => CamEdit::$field_title2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar['Cancel']);
		$I->see("Campaigns", Generals::$pageTitle);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single campaign from list view, save it and go back to list view
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
	public function CreateOneCampaignListViewRestore(AcceptanceTester $I)
	{
		$I->wantTo("Create one campaign, archive, restore, archive and delete");
		$I->amOnPage(CamManage::$url);
		$I->click(Generals::$toolbar['New']);

		CamEdit::fillFormSimple($I);
		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see(CamEdit::$success_save, Generals::$alert_success);
		$I->see(CamEdit::$field_title, CamEdit::$title_col);

		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArchiveItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array);

		$I->switchToArchive($I, CamEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array);

		$I->amOnPage(CamManage::$url);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to create same single campaign twice from main view
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
	public function CreateCampaignTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create campaign twice list view");
		$I->amOnPage(CamManage::$url);
		$I->click(Generals::$toolbar['New']);

		CamEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(CamEdit::$success_save, Generals::$alert_success);

		$I->see('Campaigns', Generals::$pageTitle);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->click(Generals::$toolbar['New']);

		CamEdit::fillFormSimple($I);
		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see("danger", Generals::$alert_heading);
		$I->see(CamEdit::$error_save, Generals::$alert_error);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->click(Generals::$toolbar4['Cancel']);
		$I->see("Campaigns", Generals::$pageTitle);

		$I->HelperArcDelItems($I, CamManage::$arc_del_array, CamEdit::$arc_del_array, true);
		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
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
		// fill title, omit recipients
		$I->fillField(CamEdit::$title, CamEdit::$field_title);
		$I->click(Generals::$toolbar4['Save & Close']);

		// check for recipients selected
		$I->seeInPopup(CamEdit::$popup_no_recipients);
		$I->acceptPopup();

		// fill recipients, omit title
		$I->click(sprintf(Generals::$mls_accessible, 2));
		$I->fillField(CamEdit::$title, "");
		$I->click(Generals::$toolbar4['Save & Close']);

		// check for title
		$I->seeInPopup(CamEdit::$popup_title);
		$I->acceptPopup();

		// fill title and description
		$I->fillField(CamEdit::$title, CamEdit::$field_title);
		$I->fillField(CamEdit::$description, CamEdit::$field_description);
	}
}
