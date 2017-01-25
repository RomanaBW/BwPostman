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

 * @since   2.0.0
 */
class TestSubscribersDetailsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login                 $loginPage
	 *
	 * @group   component
	 * @group   005_be_details
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
	 * Test method to create a single subscriber from main view and cancel creation
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   005_be_details
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateOneSubscriberCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addSubscriberButton);

		$this->_fillFormExtended($I);

		$I->clickAndWait(SubEdit::$toolbar['Back'], 1);
		$I->waitForElement(Generals::$pageTitle);
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
	 * @group   component
	 * @group   005_be_details
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateOneSubscriberCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber, archive and delete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addSubscriberButton);

		$this->_fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);

		$I->HelperArcDelItems($I, new SubManage(), new SubEdit());
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
	 * @group   component
	 * @group   005_be_details
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateOneSubscriberCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber cancel list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		$this->_fillFormExtended($I);

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
	 * @group   component
	 * @group   005_be_details
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateOneSubscriberListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Subscriber list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		$this->_fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);

		$I->HelperArcDelItems($I, new SubManage(), new SubEdit());
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
	 * @group   component
	 * @group   005_be_details
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function CreateSubscriberTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create Subscriber twice list view");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		$this->_fillFormSimple($I);

		$I->click(SubEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(SubEdit::$success_saved, Generals::$alert_msg);
		$I->see('Subscribers', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		$this->_fillFormSimple($I);

		$I->clickAndWait(SubEdit::$toolbar['Save & Close'], 1);
		$I->see("Error", Generals::$alert_header);
		$I->see(SubEdit::$error_save, Generals::$alert_error);

		$I->click(SubEdit::$toolbar['Cancel']);
		$I->see("Subscribers", Generals::$pageTitle);

		$I->HelperArcDelItems($I, new SubManage(), new SubEdit());
		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
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

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @group   component
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _failed (AcceptanceTester $I){

	}

	/**
	 * Method to fill form without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param AcceptanceTester $I
	 *
	 * @group   component
	 *
	 * @since   2.0.0
	 */
	private function _fillFormSimple(AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$I->clickAndWait(SubEdit::$gender, 1);
			$I->clickAndWait(SubEdit::$male, 1);
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubEdit::$firstname, SubEdit::$field_firstname);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubEdit::$mailformat, 1);
			$I->clickAndWait(SubManage::$format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubEdit::$special, SubEdit::$field_special);
		}
		$I->clickAndWait(SubEdit::$confirm, 1);
		$I->clickAndWait(SubEdit::$confirmed, 1);
		$I->click(sprintf(SubEdit::$mls_accessible, 2));
		$I->click(sprintf(SubEdit::$mls_nonaccessible, 3));
		$I->click(sprintf(SubEdit::$mls_internal, 4));
	}

	/**
	 * Method to fill form with check of required fields
	 * This method fills in the end all fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester $I
	 *
	 * @group   component
	 *
	 * @since   2.0.0
	 */
	private function _fillFormExtended(AcceptanceTester $I)
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
				$I->click(SubEdit::$toolbar['Save']);

				if ($options->firstname_field_obligation)
				{
					$I->seeInPopup(SubEdit::$popup_firstname);
					$I->acceptPopup();
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
					$I->seeInPopup(SubEdit::$popup_name);
					$I->acceptPopup();
				}
			}
			$I->fillField(SubEdit::$name, SubEdit::$field_name);
		}

		// omit additional field
		if ($options->show_special || $options->special_field_obligation)
		{
			if ($options->firstname_field_obligation)
			{
				$I->fillField(SubEdit::$special, "");
				if ($options->special_field_obligation)
				{
					$I->seeInPopup(sprintf(SubEdit::$popup_special, $options->special_label));
					$I->acceptPopup();
				}
			}
			$I->fillField(SubEdit::$special, SubEdit::$field_special);
		}

		// omit email address
		$I->fillField(SubEdit::$email, '');
		$I->click(SubEdit::$toolbar['Save & Close']);

		$I->seeInPopup(SubEdit::$popup_email);
		$I->acceptPopup();
		$I->fillField(SubEdit::$email, SubEdit::$field_email);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubEdit::$mailformat, 1);
			$I->clickAndWait(SubManage::$format_text, 1);
		}
		$I->clickAndWait(SubEdit::$confirm, 1);
		$I->clickAndWait(SubEdit::$confirmed, 1);
		$I->click(sprintf(SubEdit::$mls_accessible, 2));
		$I->click(sprintf(SubEdit::$mls_nonaccessible, 3));
		$I->click(sprintf(SubEdit::$mls_internal, 4));
	}
}
