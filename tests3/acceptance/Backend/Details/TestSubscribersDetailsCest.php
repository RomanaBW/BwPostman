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

		$this->fillFormExtended($I);

		$I->clickAndWait(SubEdit::$toolbar['Back'], 1);
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
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addSubscriberButton);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
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

		$I->clickAndWait(SubEdit::$toolbar['Cancel'], 1);
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
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
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

		$I->clickAndWait(Generals::$toolbar['Save & New'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);
		$I->see('', SubEdit::$name);

		$I->click(Generals::$toolbar['Cancel']);

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

		$I->clickAndWait(Generals::$toolbar['Save'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);
		$I->seeInField(SubEdit::$email, SubEdit::$field_email);

		// Grab ID of first subscriber
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => SubEdit::$field_email));

		$I->fillField(SubEdit::$email, SubEdit::$field_email2);

		$I->clickAndWait(Generals::$toolbar['Save as Copy'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);
		$I->seeInField(SubEdit::$email, SubEdit::$field_email2);

		// Grab ID of second subscriber
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => SubEdit::$field_email2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar['Cancel']);

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
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);

		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArchiveItems($I, SubManage::$arc_del_array, $edit_arc_del_array);

		$I->switchToArchive($I, SubEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, SubManage::$arc_del_array, SubEdit::$arc_del_array);

		$I->amOnPage(SubManage::$url);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);
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

		$I->click(SubEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);
		$I->see('Subscribers', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);
		$I->see("Error", Generals::$alert_header);
		$I->see(SubEdit::$error_save, Generals::$alert_error);

		$I->click(SubEdit::$toolbar['Cancel']);
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

		$I->clickAndWait(SubEdit::$printSubsDataButton, 3);

		$I->switchToIFrame("subsData");
		$I->see(" l.abbott@tester-net.nil ", "html/body/table[1]/tbody/tr[4]/td[2]");

		$I->switchToIFrame();
		$I->clickAndWait("html/body/div[5]/a", 1);
		$I->clickAndWait(Generals::$toolbar['Cancel'], 1);

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
		$I->wantTo("Create one Subscriber complete list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		$options    = $I->getManifestOptions('com_bwpostman');

		// Fill needed fields
		if ($options->show_gender)
		{
			$I->clickAndWait(SubEdit::$gender, 1);
			$I->clickAndWait(SubEdit::$male, 1);
		}

		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubEdit::$mailformat, 1);
			$I->clickAndWait(SubManage::$format_text, 1);
		}

		$I->clickAndWait(SubEdit::$confirm, 1);
		$I->clickAndWait(SubEdit::$confirmed, 1);

		$I->scrollTo(SubEdit::$mls_label, 0, -100);
		$I->click(sprintf(SubEdit::$mls_accessible, 2));
		$I->click(sprintf(SubEdit::$mls_nonaccessible, 3));
		$I->scrollTo(SubEdit::$mls_internal_label, 0, -100);
		$I->click(sprintf(SubEdit::$mls_internal, 4));
		$I->scrollTo(Generals::$sys_message_container, 0, -100);

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

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		// Check error message first name
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(SubEdit::$errorAbuseFirstName, Generals::$alert_msg);

		// Fill last name with link
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$firstname, SubEdit::$field_firstname);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$abuseLink);
		}

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		// Check error message last name
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(SubEdit::$errorAbuseLastName, Generals::$alert_msg);

		// Fill special with link
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubEdit::$special, SubEdit::$abuseLink);
		}

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		// Check error message special
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(sprintf(SubEdit::$errorAbuseSpecial, trim(SubEdit::$specialTitle)), Generals::$alert_msg);

		$I->clickAndWait(SubEdit::$toolbar['Cancel'], 1);
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtended(AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$I->clickAndWait(SubEdit::$gender, 1);
			$I->clickAndWait(SubEdit::$male, 1);
		}

		// omit first name
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
			if ($options->firstname_field_obligation)
			{
				$I->clickAndWait(SubEdit::$toolbar['Save'], 1);

				if ($options->firstname_field_obligation)
				{
					$I->waitForElement(Generals::$alert_header, 30);
					$I->see("Error", Generals::$alert_header);
					$I->see(Generals::$invalidField . SubEdit::$firstNameTitle, Generals::$alert_error);
				}
			}

			$I->fillField(SubEdit::$firstname, SubEdit::$field_firstname);
		}

		// omit last name
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, '');
			if ($options->name_field_obligation)
			{
				$I->click(SubEdit::$toolbar['Save']);

				if ($options->name_field_obligation)
				{
					$I->waitForElement(Generals::$alert_header, 30);
					$I->see("Error", Generals::$alert_header);
					$I->see(Generals::$invalidField . SubEdit::$lastNameTitle, Generals::$alert_error);
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
				$I->click(SubEdit::$toolbar['Save']);

				$I->waitForElement(Generals::$alert_header, 30);
				$I->see("Error", Generals::$alert_header);
				$I->see(Generals::$invalidField . SubEdit::$specialTitle, Generals::$alert_error);
			}

			$I->fillField(SubEdit::$special, SubEdit::$field_special);
		}

		// omit email address
		$I->fillField(SubEdit::$email, '');
		$I->click(SubEdit::$toolbar['Save & Close']);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(Generals::$invalidField . SubEdit::$emailTitle, Generals::$alert_error);

		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubEdit::$mailformat, 1);
			$I->clickAndWait(SubManage::$format_text, 1);
		}

		$I->clickAndWait(SubEdit::$confirm, 1);
		$I->clickAndWait(SubEdit::$confirmed, 1);

		$I->scrollTo(SubEdit::$mls_label, 0, -100);
		$I->click(sprintf(SubEdit::$mls_accessible, 2));
		$I->click(sprintf(SubEdit::$mls_nonaccessible, 3));
		$I->scrollTo(SubEdit::$mls_internal_label, 0, -100);
		$I->click(sprintf(SubEdit::$mls_internal, 4));
	}
}
