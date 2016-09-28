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
	 * @var bool  $auto_update
	 *
	 * @since   2.0.0
	 */
	private $auto_update = true;

	/**
	 * @var bool  $auto_delete
	 *
	 * @since   2.0.0
	 */
	private $auto_delete = true;

	/**
	 * @var bool  $subscriber_mail_old
	 *
	 * @since   2.0.0
	 */
	private $subscriber_mail_old = '';

	/**
	 * @var bool  $subscriber_mail_new
	 *
	 * @since   2.0.0
	 */
	private $subscriber_mail_new = '';


	/**
	 * Test method to register user with subscription selected no
	 * You see Joomla user but no subscriber
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
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$this->initializeTestValues($I);
		$this->subscription_selected    = false;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_no, 1);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes
	 * You see Joomla unconfirmed user and unconfirmed subscriber
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
		$I->expectTo('see unconfirmed Joomla user and unconfirmed subscriber with HTML format');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes
	 * You see Joomla confirmed user and confirmed subscriber with HTML format
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes with activation and selected text format
	 * You see Joomla confirmed user and confirmed subscriber with Text format
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		$this->format   = 'Text';

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);
		$I->clickAndWait(RegPage::$login_identifier_format_text, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, with predefined format html
	 * You see Joomla confirmed user and confirmed subscriber with HTML format
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, with predefined format text
	 * You see Joomla confirmed user and confirmed subscriber with Text format
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		$this->format   = 'Text';
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '0');
		$I->setManifestOption('registersubscribe', 'predefined_mailformat_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);

	}

	/**
	 * Test method to register user with subscription selected yes, with activation and another mailinglist
	 * You see Joomla confirmed user and confirmed subscriber with HTML format and another mailinglist
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithAnotherMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to another mailinglist");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format and another mailinglist');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe = array(RegPage::$mailinglist2_checked);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation and two mailinglists
	 * You see Joomla confirmed user and confirmed subscriber with HTML format and two mailinglists
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format and two mailinglists');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("4", "6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe = array(RegPage::$mailinglist1_checked, RegPage::$mailinglist2_checked);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation and two mailinglists
	 * You see Joomla confirmed user and confirmed subscriber with HTML format and two mailinglists
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to zero mailinglists");
		$I->expectTo('see confirmed Joomla user and no subscriber');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('registersubscribe', 'ml_available', array("0"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->mls_to_subscribe = array("0");

		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->activated = false;
			$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);
			$this->gotoSubscribersListTab($I);
			$this->filterForSubscriber($I);

			$I->dontSee(RegPage::$login_value_name, $identifier);

			$this->activated = true;
			$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);
			$this->gotoSubscribersListTab($I);
			$this->filterForSubscriber($I);
$I->wait(5);
			$I->dontSee(RegPage::$login_value_name, $identifier);

			$this->deleteJoomlaUser($I);

			LoginPage::logoutFromBackend($I);
		}
		);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, change mail address at auto update
	 * You see Joomla confirmed user and confirmed subscriber, see changed mail address for user and subscriber
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;

		$this->checkBackendSuccessWithMailChange($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, change mail address at auto update
	 * You see Joomla unconfirmed user and unconfirmed subscriber, see changed mail address for user and subscriber
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeFunctionWithoutActivationWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman without activation, then change mail address");
		$I->expectTo('see unconfirmed Joomla user and unconfirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;

		$this->checkBackendSuccessWithMailChange($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, change mail address at auto update
	 * You see Joomla confirmed user and confirmed subscriber, see changed mail address for user but not for subscriber
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, see changed mail address for user but not subscriber');

		$this->initializeTestValues($I);

		//set other option settings
		$this->auto_update  = false;
		$I->setManifestOption('registersubscribe', 'auto_update_email_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;

		$this->checkBackendSuccessWithMailChange($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected yes, with activation, delete user but not subscriber, register user anew
	 * You see Joomla confirmed user and confirmed subscriber, delete user, not see user, see subscriber without
	 * Joomla user ID, register anew, see subscriber with new Joomla user ID
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
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, delete user, but see subscriber without joomla user id, then subscribe anew and see new user ID');

		$this->initializeTestValues($I);

		//set other option settings
		$this->auto_delete  = false;
		$I->setManifestOption('registersubscribe', 'auto_delete_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->_activate($I, RegPage::$login_value_email);

		// Delete account
		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->deleteJoomlaUser($I);

			$this->checkForRegistrationSuccess($I);
			$this->deleteJoomlaUser($I);

			// assert subscription is there without Joomla user ID
			try
			{
				$user_id    = $I->grabTextFrom(RegPage::$user_id_identifier);
			}
			catch (\Codeception\Exception\ElementNotFound $e)
			{
				LoginPage::logoutFromBackend($I);
				return false;
			}
			$I->assertEmpty($user_id);

			LoginPage::logoutFromBackend($I);
		}
		);

		//reset option settings
		$this->auto_delete  = true;
		$I->setManifestOption('registersubscribe', 'auto_delete_option', '1');

		// register anew
		$this->selectRegistrationPage($I);

		$this->fillJoomlaRegisterForm($I);

		$I->clickAndWait(RegPage::$login_identifier_subscribe_yes, 1);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);

		$this->initializeTestValues($I);
	}

	/**
	 * Test method to register user with subscription selected no
	 * You see Joomla user but no subscriber
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function RegisterSubscribeOptionsPluginDeactivated(AcceptanceTester $I)
	{
		$I->wantTo("Deactivate Plugin RegisterSubscribe");
		$I->expectTo('not see plugin fields at Joomla registration form');

		$this->tester   = $I;
		LoginPage::logIntoBackend(Generals::$admin);

		$this->selectPluginPage($I);

		$this->filterForPlugin($I);

		$this->disablePlugin($I);

		// @ ToDo: check frontend
		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);

			$I->dontSee(RegPage::$login_identifier_subscribe_yes);
			$I->dontSee(RegPage::$login_identifier_subscribe_no);
			$I->dontSee(RegPage::$login_identifier_format_html);
			$I->dontSee(RegPage::$login_identifier_format_text);
		}
		);

		$this->enablePlugin($I);

		LoginPage::logoutFromBackend($I);
	}

		/**
	 * @param   AcceptanceTester    $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function initializeTestValues($I)
	{
		$this->tester                   = $I;
		$this->activated                = false;
		$this->subscription_selected    = true;
		$this->mls_to_subscribe         = array(RegPage::$mailinglist1_checked);
		$this->format                   = 'HTML';
		$this->auto_update              = true;
		$this->auto_delete              = true;

		//reset option settings
		$I->setManifestOption('registersubscribe', 'show_format_selection_option', '1');
		$I->setManifestOption('registersubscribe', 'predefined_mailformat_option', '1');
		$I->setManifestOption('registersubscribe', 'auto_update_email_option', '1');
		$I->setManifestOption('registersubscribe', 'auto_delete_option', '1');
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

		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);
			$this->deleteJoomlaUser($I);
			LoginPage::logoutFromBackend($I);
		}
		);
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
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function checkBackendSuccessSimple(AcceptanceTester $I)
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
		$this->gotoSubscribersListTab($I);
		$this->filterForSubscriber($I);

		$format_col = $this->getTabDependentIdentifier(RegPage::$subscriber_format_col_identifier);
		$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);

		if ($this->subscription_selected || ($this->auto_delete !== true))
		{
			$I->see(RegPage::$login_value_name, $identifier);
			$I->canSee($this->format, $format_col);

			// look in details for selected mailinglists
			$I->clickAndWait($identifier, 1);
			$I->scrollTo(RegPage::$mailinglist_fieldset_identifier, 0, -100);

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
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function gotoSubscribersListTab(AcceptanceTester $I)
	{
		if ($this->activated)
		{
			$tab = RegPage::$tab_confirmed;
		}
		else
		{
			$tab = RegPage::$tab_unconfirmed;
		}

		$I->amOnPage(RegPage::$subscribers_url);
		$I->see('Subscribers', Generals::$pageTitle);
		$I->clickAndWait($tab, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function filterForSubscriber(AcceptanceTester $I)
	{
		$I->fillField(Generals::$search_field, RegPage::$login_value_name);
		$I->clickAndWait(RegPage::$search_tool_button, 1);
		$I->clickSelectList(RegPage::$search_for_list, RegPage::$search_for_value);
		$I->clickAndWait(Generals::$search_button, 1);
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
		$this->gotoUserManagement($I);
		$user_found = $this->findUser($I);

		if ($user_found)
		{
			// delete user
			$I->checkOption(Generals::$check_all_button);
			$I->clickAndWait(RegPage::$toolbar_delete_button, 1);

			// process confirmation popup
			$I->seeInPopup(RegPage::$delete_confirm);
			$I->acceptPopup();

			// see message deleted
			$I->waitForElement(Generals::$alert_header);
			$I->see(Generals::$alert_msg_txt, Generals::$alert_header);
			$I->see(RegPage::$delete_success, Generals::$alert_success);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function gotoUserManagement(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$user_management_url);
		$I->wait(1);
		$I->see('Users', Generals::$pageTitle);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @return  bool    true on success
	 *
	 * @since 2.0.0
	 */
	protected function findUser(AcceptanceTester $I)
	{
		$I->fillField(Generals::$search_field, RegPage::$login_value_name);
		$I->clickAndWait(Generals::$search_button, 1);

		try
		{
			$user_found = $I->grabTextFrom(RegPage::$user_edit_identifier);
		}
		catch (\Codeception\Exception\ElementNotFound $e)
		{
			return false;
		}

		if ($user_found == RegPage::$login_value_name)
		{
			return true;
		}
		return false;
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
		$this->gotoSubscribersListTab($I);
		$this->filterForSubscriber($I);

		$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_filter_col_identifier);

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
	protected function checkBackendSuccessWithMailChange(AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->checkForRegistrationSuccess($I);

			$this->gotoUserManagement($I);
			$user_found = $this->findUser($I);

			if ($user_found)
			{
				$this->changeMailAddressOfAccount($I);

				$this->checkMailChangeOfSubscription($I);

				// delete user
				$this->gotoUserManagement($I);
				$this->deleteJoomlaUser($I);
				$this->checkForSubscriptionDeletion($I);
			}

			LoginPage::logoutFromBackend($I);
		}
		);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function changeMailAddressOfAccount(AcceptanceTester $I)
	{
		$I->clickAndWait(RegPage::$user_edit_identifier, 1);
		$I->fillField(RegPage::$mail_field_identifier, RegPage::$change_value_email);
		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		// check mail address change of account
		$I->see(RegPage::$change_value_email, RegPage::$email_identifier);
		$I->dontSee(RegPage::$login_value_email, RegPage::$email_identifier);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function checkMailChangeOfSubscription(AcceptanceTester $I)
	{
		$this->gotoSubscribersListTab($I);
		$this->filterForSubscriber($I);
		$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_email_col_identifier);

		if ($this->auto_update)
		{
			$I->see($this->subscriber_mail_new, $identifier);
			$I->dontSee($this->subscriber_mail_old, $identifier);
		}
		else
		{
			$I->see($this->subscriber_mail_old, $identifier);
			$I->dontSee($this->subscriber_mail_new, $identifier);
		}
	}
	/**
	 * Test method to activate Joomla registration
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $mailaddress
	 *
	 * @since   2.0.0
	 */
	private function _activate(\AcceptanceTester $I, $mailaddress)
	{
		$activation_code = $I->getJoomlaActivationCode($mailaddress);
		$I->amOnPage(RegPage::$user_activation_url . $activation_code);
		$this->activated    = true;
	}

	/**
	 * @param   string  $raw_identifier
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	protected function getTabDependentIdentifier($raw_identifier)
	{
		if ($this->activated)
		{
			$identifier = sprintf($raw_identifier, 1);
		}
		else
		{
			$identifier = sprintf($raw_identifier, 2);
		}
		return $identifier;
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function selectPluginPage(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$plugin_page);
		$I->wait(1);
		$I->see('Plugins', Generals::$pageTitle);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function filterForPlugin(AcceptanceTester $I)
	{
		$I->fillField(Generals::$search_field, RegPage::$plugin_name);
		$I->clickAndWait(RegPage::$search_tool_button, 1);
//		$I->clickSelectList(RegPage::$search_for_list, RegPage::$search_for_value);
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function disablePlugin(AcceptanceTester $I)
	{
		$published_icon_class = $I->grabAttributeFrom(RegPage::$icon_published_identifier, 'class');

		if ($published_icon_class == 'icon-publish')
		{
			$I->clickAndWait(RegPage::$icon_published_identifier, 2);
			$I->see(RegPage::$plugin_disabled_success, Generals::$alert_success);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since 2.0.0
	 */
	protected function enablePlugin(AcceptanceTester $I)
	{
		$published_icon_class = $I->grabAttributeFrom(RegPage::$icon_published_identifier, 'class');

		if ($published_icon_class == 'icon-unpublish')
		{
			$I->clickAndWait(RegPage::$icon_published_identifier, 2);
			$I->see(RegPage::$plugin_enabled_success, Generals::$alert_success);
		}
	}

}

