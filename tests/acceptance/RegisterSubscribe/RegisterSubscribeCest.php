<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\RegisterSubscriberPage as RegPage;


/**
 * Class RegisterSubscribeCest
 *
 * This class contains all methods to test subscription while registration to Joomla at front end
 *
 * @package Register Subscribe Plugin
 * @copyright (C) 2016 Boldt Webservice <forum@boldt-webservice.de>
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
class RegisterSubscribeCest
{
	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	public $tester;

	/**
	 * @var bool  $activated
	 *
	 * @since   2.0.0
	 */
	private $activated = false;

	/**
	 * @var bool  $subscription_selected
	 *
	 * @since   2.0.0
	 */
	private $subscription_selected = true;

	/**
	 * @var bool  $mls_to_subscribe
	 *
	 * @since   2.0.0
	 */
	private $mls_to_subscribe = array();

	/**
	 * @var bool  $format
	 *
	 * @since   2.0.0
	 */
	private $format = 'HTML';

	/**
	 * @var bool  $auto_delete
	 *
	 * @since   2.0.0
	 */
	private $auto_delete = true;

	/**
	 * Test method to register user subscription selected no
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutSubscription(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and not subscribe to BwPostman");
		$I->expectTo('get activation mail by Joomla');

		$this->tester                   = $I;
		$this->subscription_selected    = false;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_no, 1);

		$this->registerAndCheckMessage($I);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);
		$this->subscription_selected    = true;
	}

	/**
	 * Test method to register user subscription selected yes without activation
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutActivation(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman without activation");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);
	}

	/**
	 * Test method to register user subscription selected yes with activation
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithActivation(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with activation");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);
	}

	/**
	 * Test method to register user subscription selected yes with activation and text format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithTextFormat(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with text format");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;
		$this->format   = 'Text';

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);
		$I->clickAndWait(RegPage::$login_identifier_format_text, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);

		// reset newsletter format
		$this->format   = 'HTML';
	}

	/**
	 * Test method to register user subscription selected yes with activation, with predefined format html
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutFormatSelectionHTML(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with predefined format HTML");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		//set other option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '1');
	}

	/**
	 * Test method to register user subscription selected yes with activation, with predefined format text
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutFormatSelectionText(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with predefined format text");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;
		$this->format   = 'Text';

		//set other option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '0');
		$I->setManifestOption('registersubscribe', 'predefined_mailformat_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '1');
		$I->setManifestOption('registersubscribe', 'predefined_mailformat_option', '1');

		// reset newsletter format
		$this->format   = 'HTML';

	}

	/**
	 * Test method to register user subscription selected yes with activation and another mailinglist
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithAnotherMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman top another mailinglist");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		//set other option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe = array(RegPage::$mailinglist2_checked);
		$this->checkBackendSuccess($I);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("4"));
		$this->mls_to_subscribe = array(RegPage::$mailinglist1_checked);
	}

	/**
	 * Test method to register user subscription selected yes with activation and two mailinglists
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithTwoMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to two mailinglists");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		//set other option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("4", "6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe = array(RegPage::$mailinglist1_checked, RegPage::$mailinglist2_checked);
		$this->checkBackendSuccess($I);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("4"));
		$this->mls_to_subscribe = array(RegPage::$mailinglist1_checked);
	}

	/**
	 * Test method to register user subscription with auto change mail address
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, change mail address with auto update");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);
	}

	/**
	 * Test method to register user subscription without auto change mail address
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithMailChangeNo(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, change mail address without auto update");
		$I->expectTo('get activation mail by Joomla');

		$this->tester   = $I;

		//set other option settings
		$I->setManifestOption('registersubscribe', 'auto_update_email_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'auto_update_email_option', '1');
	}

	/**
	 * Test method to register user subscription with no auto deletion of subscription
	 * and register anew (subscription exists)
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithDeleteNo(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, delete account and not delete subscription");
		$I->expectTo('get activation mail by Joomla');

		$this->tester           = $I;

		//set other option settings
		$I->setManifestOption('registersubscribe', 'auto_delete_option', '0');
		$this->auto_delete  = false;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
//		$this->checkBackendSuccess($I);

		// Delete account
		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->deleteJoomlaUser($I);

			// @ToDo: Subscriber hast to be present but with empty user_id
			$this->checkForRegistrationSuccess($I);
			$user_id    = $I->grabTextFrom(".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr/td[7]");
			$I->assertEmpty($user_id);

			LoginPage::logoutFromBackend($I);
		}
		);

		//reset option settings
		$I->setManifestOption('registersubscribe', 'auto_delete_option', '1');
		$this->auto_delete  = true;

		// register anew
		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->checkBackendSuccess($I);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function fillJoomlaRegisterForm(AcceptanceTester $I)
	{
		$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value_name);
		$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value_username);
		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_email1, RegPage::$login_value_email);
		$I->fillField(RegPage::$login_identifier_email2, RegPage::$login_value_email);
	}

	/**
	 * Method to check if subscription was successful
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForRegistrationSuccess(AcceptanceTester $I)
	{
		$this->goToSubscribersListsAndFilterUser($I);

		if ($this->activated)
		{
			$identifier    = sprintf(RegPage::$subscriber_filter_col_identifier, 1);
			$format_col    = sprintf(RegPage::$subscriber_format_col_identifier, 1);
		}
		else
		{
			$identifier    = sprintf(RegPage::$subscriber_filter_col_identifier, 2);
			$format_col    = sprintf(RegPage::$subscriber_format_col_identifier, 2);
		}
		if ($this->subscription_selected || ($this->auto_delete !== true))
		{
			$I->see(RegPage::$login_value_name, $identifier);
			// @ToDo: Look for newsletter format
			$I->canSee($this->format, $format_col);

			// look in details for selected mailinglists
			$I->clickAndWait($identifier . '/a', 1);
			$I->scrollTo("//*[@id='adminForm']/div[1]/div[1]/fieldset/div[1]/div/fieldset/legend/span[2]", 0, -100);
			foreach ($this->mls_to_subscribe as $ml)
			{
				$I->seeCheckboxIsChecked($ml);
			}
			$I->clickAndWait(RegPage::$subscriber_details_close, 1);
		}
		else
		{
			$I->dontSee(RegPage::$login_value_name, $identifier);
		}
	}

	/**
	 * Method to delete Joomla user account
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function deleteJoomlaUser(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$user_management_url);
		$I->wait(1);
		$I->see('Users', Generals::$pageTitle);

		// select user to delete
		$I->fillField(Generals::$search_field, RegPage::$login_value_name);
		$I->clickAndWait(Generals::$search_button, 1);
		$I->see(RegPage::$login_value_name, RegPage::$user_filter_col_identifier);

		// delete user
		$I->checkOption(Generals::$check_all_button);
		$I->clickAndWait(RegPage::$delete_button, 1);

		// process confirmation popup
		$I->seeInPopup(RegPage::$delete_confirm);
		$I->acceptPopup();

		// see message deleted
		$I->waitForElement(Generals::$alert_header);
		$I->see(Generals::$alert_msg_txt, Generals::$alert_header);
		$I->see(RegPage::$delete_success, Generals::$alert_success);
	}

	/**
	 * Method to check if subscription deletion was successful
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForSubscriptionDeletion(AcceptanceTester $I)
	{
		// look in subscribers list for name
		$this->goToSubscribersListsAndFilterUser($I);

		if ($this->activated)
		{
			$identifier    = sprintf(RegPage::$subscriber_filter_col_identifier, 1);
		}
		else
		{
			$identifier    = sprintf(RegPage::$subscriber_filter_col_identifier, 2);
		}

		if ($this->auto_delete)
		{
			$I->dontSee(RegPage::$login_value_name, $identifier);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function goToSubscribersListsAndFilterUser(AcceptanceTester $I)
	{
		if ($this->activated)
		{
			$tab    = RegPage::$tab_confirmed;
		}
		else
		{
			$tab    = RegPage::$tab_unconfirmed;
		}

		// look in subscribers list for name
		$I->amOnPage(RegPage::$subscribers_url);
		$I->see('Subscribers', Generals::$pageTitle);
		$I->clickAndWait($tab, 1);

		// select user to check
		$I->fillField(Generals::$search_field, RegPage::$login_value_name);
		$I->clickAndWait(".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[2]/button", 1);
		$I->clickSelectList(".//*[@id='filter_search_filter_chzn']", ".//*[@id='filter_search_filter_chzn']/div/ul/li[contains(text(), 'Name')]");
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * Test method to activate Joomla registration
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $mailaddress
	 * @param bool              $good
	 *
	 * @since   2.0.0
	 */
	private function _activate(\AcceptanceTester $I, $mailaddress, $good = true)
	{
		$activation_code = $I->getJoomlaActivationCode($mailaddress);
		$I->amOnPage(RegPage::$user_activation_url . $activation_code);
		$this->activated    = true;
		if ($good)
		{
//			$I->see(RegPage::$activation_completed_text, RegPage::$activation_complete);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function selectRegistrationPage(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$register_url);
		$I->wait(7);
		$I->seeElement(RegPage::$view_register);

		$this->mls_to_subscribe     = array(RegPage::$mailinglist1_checked);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function registerAndCheckMessage(AcceptanceTester $I)
	{
		$I->clickAndWait(RegPage::$login_identifier_register, 1);

		$I->see(Generals::$alert_msg_txt, RegPage::$success_heading_identifier);
		$I->see(RegPage::$register_success, RegPage::$success_message_identifier);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function checkBackendSuccess(AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->checkForRegistrationSuccess($I);

			$this->deleteJoomlaUser($I);
			$this->checkForSubscriptionDeletion($I);

			LoginPage::logoutFromBackend($I);
		}
		);
//		$admin->leave();
	}
}

