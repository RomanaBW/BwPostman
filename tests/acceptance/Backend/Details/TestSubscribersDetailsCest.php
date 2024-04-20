<?php
use Page\Generals as Generals;
use Page\SubscriberEditPage as SubEdit;
use Page\SubscriberManagerPage as SubManage;
use Page\MainviewPage as MainView;

// @ToDo: See all fields, that are set

/**
 * Class TestSubscribersDetailsCest
 *
 * This class contains all methods to test manipulation of a single subscriber at back end
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

 * @since   2.0.0
 */
class TestSubscribersDetailsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login                 $loginPage
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
	 * Test method to create a single subscriber from main view and cancel creation
	 *
	 * @param   AcceptanceTester            $I
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
	public function CreateOneSubscriberCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addSubscriberButton);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbar4['Back'], 1);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single subscriber from main view, save it and go back to main view
	 *
	 * @param   AcceptanceTester            $I
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
	public function CreateOneSubscriberCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber, archive and delete from main view");

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');

		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addSubscriberButton);

		SubEdit::fillFormSimple($I, SubManage::$format_text, SubEdit::$male);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '0', '0');

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Reset settings
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to create a single Subscriber from list view and cancel creation
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
	public function CreateOneSubscriberCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber cancel list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		$this->fillFormExtended($I);

		$I->clickAndWait(Generals::$toolbar4['Cancel'], 1);
		$I->see("Subscribers", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Subscriber from list view, save it and go back to list view
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
	public function CreateOneSubscriberCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber complete list view");

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');

		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$female);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '1', '1');

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Reset settings
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to create a single Subscriber from list view, save it and get new and empty edit form
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
	public function CreateOneSubscriberSaveNewListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber complete, save and get new record list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save & New'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->see('', SubEdit::$name);

		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->click(Generals::$toolbar4['Cancel']);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Subscriber from list view and save it as copy
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
	public function CreateOneSubscriberSaveCopyListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber complete, save, save as copy and get new record id");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbar4['Save'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->seeInField(SubEdit::$email, SubEdit::$field_email);

		// Grab ID of first subscriber
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => SubEdit::$field_email));

		$I->fillField(SubEdit::$email, SubEdit::$field_email2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->seeInField(SubEdit::$email, SubEdit::$field_email2);

		// Grab ID of second subscriber
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => SubEdit::$field_email2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Subscriber from list view, save it and go back to list view
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
	public function CreateOneSubscriberListViewRestore(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber list view, archive and restore");

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');

		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$noGender);
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '1', '2');

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArchiveItems($I, SubManage::$arc_del_array, $edit_arc_del_array);

		$I->switchToArchive($I, SubEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, SubManage::$arc_del_array, SubEdit::$arc_del_array);

		$I->amOnPage(SubManage::$url);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Reset settings
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to create a single unactivated subscriber from list view, save it and go back to list view
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
	public function CreateOneSubscriberUnactivatedCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one unactivated subscriber complete list view");

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');

		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$female);

		// Reset confirmation to unconfirmed
		$I->click(SubEdit::$confirm);
		$I->selectOption(SubEdit::$confirm, SubEdit::$unconfirmed);
		$I->wait(1);
		$I->waitForText("unconfirmed", 5);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->clickAndWait(SubManage::$tab_unconfirmed, 2);
		$this->checkSavedValues($I, '1', '1', false);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I, false);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Reset settings
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to create same single Subscriber twice from main view
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
	public function CreateSubscriberTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create Subscriber twice list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Subscribers', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see("danger", Generals::$alert_header);
		$I->see(SubEdit::$error_save, Generals::$alert_error_1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$toolbar4['Cancel']);
		$I->see("Subscribers", Generals::$pageTitle);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Test method to print subscriber details
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
	public function TestSubscriberPrintDataButton(AcceptanceTester $I)
	{
		$I->wantTo("test print data button at subscriber details");
		$I->expectTo("see popup with subscriber data");

		$I->amOnPage(SubManage::$url);
		$I->clickAndWait(SubEdit::$firstSubscriber, 1);

		$I->clickAndWait(sprintf(SubEdit::$printSubsDataButton, SubEdit::$printSubsDataText), 3);

		$I->switchToIFrame(SubEdit::$printSubsIframe);
		$I->see(" l.abbott@tester-net.nil ", SubEdit::$printSubsDataMail);
		$I->switchToIFrame();

		$I->clickAndWait(SubEdit::$printSubsDataClose, 2);
		$I->clickAndWait(Generals::$toolbar4['Cancel'], 1);

		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Subscriber from list view, fill text fields with links
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
	 * @since   3.0.0
	 */
	public function CreateOneSubscriberAbuseListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber with http:// at name field");
		$I->amOnPage(SubManage::$url);

		Generals::presetComponentOptions($I);

		// Set show special
		$I->setManifestOption('com_bwpostman', 'show_special', '1');

		$I->click(Generals::$toolbar['New']);

		$options    = $I->getManifestOptions('com_bwpostman');

		// Fill needed fields
		if ($options->show_gender)
		{
			$I->selectOption(SubEdit::$gender, "0");
		}

		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->selectOption(SubEdit::$mailformat, "0");
		}

		$I->selectOption(SubEdit::$confirm, SubEdit::$confirmed);

		$I->scrollTo(SubEdit::$mls_label, 0, -100);
		$I->wait(1);
		$I->click(sprintf(SubEdit::$mls_accessible, 3));
		$I->click(sprintf(SubEdit::$mls_nonaccessible, 5));
		$I->scrollTo(SubEdit::$mls_internal_label, 0, -100);
		$I->wait(1);
		$I->click(sprintf(SubEdit::$mls_internal, 7));
		$I->scrollTo(Generals::$sys_message_container, 0, -100);
		$I->wait(1);

		// Fill first name with link
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$firstname, SubEdit::$abuseLink);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubEdit::$special, SubEdit::$field_special);
		}

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		// Check error message first name
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("danger", Generals::$alert_header);
		$I->see(SubEdit::$errorAbuseFirstName, Generals::$alert_error_1);

		// Fill last name with link
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$firstname, SubEdit::$field_firstname);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$abuseLink);
		}

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		// Check error message last name
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("danger", Generals::$alert_header);
		$I->see(SubEdit::$errorAbuseLastName, Generals::$alert_error_1);

		// Fill special with link
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubEdit::$special, SubEdit::$abuseLink);
		}

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		// Check error message special
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("danger", Generals::$alert_header);
		$I->see(sprintf(SubEdit::$errorAbuseSpecial, trim(SubEdit::$specialTitle)), Generals::$alert_error_1);

		$I->clickAndWait(SubEdit::$toolbar['Cancel'], 1);

		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to create a test recipient from main view and cancel creation
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.1.3
	 */
	public function CreateOneTesterCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one test recipient and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addTestRecipientButton);

		SubEdit::fillFormSimple($I, 1, 2, true);

		$I->clickAndWait(Generals::$toolbar4['Back'], 1);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a test recipient from list view and cancel creation
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
	public function CreateOneTesterCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one test recipient cancel list view");
		$I->amOnPage(SubManage::$url);

		$I->clickAndWait(SubManage::$tab_testers, 1);

		$I->click(Generals::$toolbar['New']);

		$this->fillFormExtended($I, true);

		$I->clickAndWait(Generals::$toolbar4['Cancel'], 1);
		$I->see("Subscribers", Generals::$pageTitle);
	}

	/**
	 * Test method to test creation of test recipients and also single Subscriber from list view.
	 * One test recipient with html format, one with text format, a second with text format (should give an error) and
	 * one regular subscriber. All three should be possible.
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
	 * @since   4.1.3
	 */
	public function CreateTestersListView(AcceptanceTester $I)
	{
		$I->wantTo("Create a html test recipient complete list view");

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');

		$I->amOnPage(SubManage::$url);

		// Create test recipient with html format
		$I->clickAndWait(SubManage::$tab_testers, 1);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$female, true);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '1', '1');


		// Create test recipient with text format
		$I->clickAndWait(SubManage::$tab_unconfirmed, 1);
		$I->clickAndWait(SubManage::$tab_testers, 1);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_text, SubEdit::$male, true);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '0', '0');

		// Create second test recipient with text format
		$I->clickAndWait(SubManage::$tab_unconfirmed, 1);
		$I->clickAndWait(SubManage::$tab_testers, 1);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_text, SubEdit::$male, true);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);

		$I->see(sprintf(SubEdit::$error_save_tester, SubEdit::$field_email), Generals::$alert_error_1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->clickAndWait(Generals::$toolbar4['Cancel'], 1);

		// Create regular recipient with html format
		$I->clickAndWait(SubManage::$tab_confirmed, 1);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$female);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$this->checkSavedValues($I, '1', '1');

		// Cleanup regular subscriber
		$edit_arc_del_array = SubEdit::prepareDeleteArray($I, true, false, false);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true, false);
		$I->see('Subscribers', Generals::$pageTitle);

		// Cleanup test recipients
		$I->amOnPage(SubManage::$url);

		$I->clickAndWait(SubManage::$tab_testers, 1);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I, true, true, true);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Reset settings
		Generals::presetComponentOptions($I);
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
	 * @param bool             $isTester
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtended(AcceptanceTester $I, $isTester = false)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$I->selectOption(SubEdit::$gender, 'male');
		}

		// omit first name
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
			$I->fillField(SubEdit::$email, SubEdit::$field_email);
			if ($options->firstname_field_obligation)
			{
				$I->clickAndWait(Generals::$toolbar4['Save'], 1);

				if ($options->firstname_field_obligation)
				{
					$I->waitForElementVisible(Generals::$alert_header, 30);
					$I->see("danger", Generals::$alert_heading);
					$I->see(SubEdit::$firstNameMissing, Generals::$alert_error_1);
				}
			}

			$I->fillField(SubEdit::$firstname, SubEdit::$field_firstname);
		}

		// omit last name
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, '');
			$I->fillField(SubEdit::$email, SubEdit::$field_email);
			if ($options->name_field_obligation)
			{
				$I->click(Generals::$toolbar4['Save']);

				if ($options->name_field_obligation)
				{
					$I->waitForElementVisible(Generals::$alert_header, 30);
					$I->see("danger", Generals::$alert_heading);
					$I->see(SubEdit::$lastNameMissing, Generals::$alert_error_1);
				}
			}

			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		// omit additional field
		if ($options->show_special || $options->special_field_obligation)
		{
			if ($options->special_field_obligation)
			{
				$I->fillField(SubEdit::$special, "");
				$I->click(Generals::$toolbar4['Save']);

				$I->waitForElementVisible(Generals::$alert_header, 30);
				$I->see("danger", Generals::$alert_heading);
				$I->see(sprintf(SubEdit::$popup_special, $options->special_label), Generals::$alert_error_1);
			}

			$I->fillField(SubEdit::$special, SubEdit::$field_special);
		}

		// omit email address
		$I->fillField(SubEdit::$email, '');
		$I->click(Generals::$toolbar4['Save & Close']);

		$I->waitForElementVisible(Generals::$alert_header, 30);
		$I->see("danger", Generals::$alert_heading);
		$I->see(Generals::$invalidField . SubEdit::$emailTitle, Generals::$alert_error_1);

		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->click(SubEdit::$mailformat);
			$I->selectOption(SubEdit::$mailformat, SubEdit::$format_text);
			$I->wait(1);
			$I->waitForText("Text", 5);
		}

		if (!$isTester)
		{
			$I->click(SubEdit::$confirm);
			$I->selectOption(SubEdit::$confirm, SubEdit::$confirmed);
			$I->wait(1);
			$I->waitForText("confirmed", 5);

			$I->scrollTo(SubEdit::$mls_label, 0, -100);
			$I->wait(1);
			$I->click(sprintf(SubEdit::$mls_accessible, 3));
			$I->click(sprintf(SubEdit::$mls_nonaccessible, 5));
			$I->scrollTo(SubEdit::$mls_internal_label, 0, -100);
			$I->wait(1);
			$I->click(sprintf(SubEdit::$mls_internal, 7));
		}

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
	}

	/**
	 * Method to check, if entered values are correctly saved at database
	 *
	 * @param AcceptanceTester $I
	 * @param string $format (0 = text, 1 = HTML)
	 * @param string $gender (0 = male, 1 = female, 2 = n.a.)
	 * @param boolean $confirmed
	 *
	 * @since 3.0.2
	 */
	private function checkSavedValues(AcceptanceTester $I, $format, $gender, $confirmed = true)
	{
		$table_subs     = Generals::$db_prefix . 'bwpostman_subscribers';
		$valuesExpected = array(
			'name'          => SubEdit::$field_name,
			'firstname'     => SubEdit::$field_firstname,
			'email'         => SubEdit::$field_email,
			'emailformat'   => $format,
			'gender'        => $gender,
			'special'       => SubEdit::$field_special,
			'registered_by' => '757',
			'confirmed_by'  => '757',
		);

		if (!$confirmed)
		{
			$valuesExpected['confirmed_by']  = '-1';
		}

		$I->seeInDatabase($table_subs, $valuesExpected);
	}
}
