<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\User2SubscriberPage as RegPage;
use Page\SubscriberManagerPage as SubsManagePage;
use Page\InstallationPage as InstallPage;
use Codeception\Extension\BwRunFailed;

/**
 * Class User2SubscriberCest
 *
 * This class contains all methods to test subscription while registration to Joomla at front end
 *
 * @package Register Subscribe Plugin
 * @copyright (C) 2016-2017 Boldt Webservice <forum@boldt-webservice.de>
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
class User2SubscriberCest
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
	 * @var array  $mls_to_subscribe
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

	private $current_mail_address = '';

	private $subscription_only      = false;
	private $name_obligation        = true;
	private $firstname_obligation   = true;
	private $special_obligation     = true;
	private $show_gender            = false;
	private $check_gender           = false;
	private $gender_selected        = 'male';

	private $visitor        = 1;

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
	 * Test method to activate plugin
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
	public function setupUser2Subscriber(AcceptanceTester $I)
	{
		$I->wantTo("activate plugin user2subscriber and setup default options");
		$I->expectTo("see success message and green arrow in extensions list");

		$I->setExtensionStatus('bwpm_user2subscriber', 1);

		$I->amOnPage(InstallPage::$plugin_manage_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingPlugins);

		$I->fillField(Generals::$search_field, Generals::$plugin_u2s);
		$I->click(Generals::$search_button);

		$I->click(".//*[@id='pluginList']/tbody/tr/td[4]/a");
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingPlugins . ": " . Generals::$plugin_u2s);

		// set mailinglist
		$I->click(".//*[@id='myTabTabs']/li[3]/a");
		$I->waitForElement(".//*[@id='jform_params_ml_available']/div", 30);

		$checked    = $I->grabAttributeFrom(".//*[@id='mb6']", "checked");
		if (!$checked)
		{
			$I->click(".//*[@id='mb6']");
		}

		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$pluginSavedSuccess, Generals::$alert_success);
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
	public function User2SubscriberFunctionWithoutSubscription(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and not subscribe to BwPostman");
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$this->initializeTestValues($I);
		$this->subscription_selected    = false;

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to register user with subscription selected first yes but then no
	 * You see Joomla user but no subscriber
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionSwitchSubscriptionWithoutSubscription(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla, switch subscription to yes and back to no and not subscribe to BwPostman");
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$this->initializeTestValues($I);
		$this->subscription_selected    = false;

		$this->selectRegistrationPage($I);

		$this->switchSubscriptionYesAndNo($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithoutActivationExtended(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman without activation extended");
		$I->expectTo('see error messages, see unconfirmed Joomla user and unconfirmed subscriber with HTML format');

		$this->initializeTestValues($I);
		$this->show_gender       = true;
		$I->setManifestOption('com_bwpostman', 'show_gender', '1');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormExtended($I);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithActivationByFrontend(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with activation by frontend");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format');

		$this->initializeTestValues($I);
		$this->name_obligation       = false;
		$this->firstname_obligation  = false;
		$this->special_obligation    = false;
		$this->subscription_only     = true;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to register user witch has already a subscription, plugin mailinglist is same as subscribed one
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithExistingSubscriptionSameList(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman while a subscription to this mailinglist already exists");
		$I->expectTo('see another confirmed Joomla user and existing subscriber with additionally mailinglist');

		$this->initializeTestValues($I);
		$this->name_obligation          = false;
		$this->firstname_obligation     = false;
		$this->special_obligation       = false;
		$this->visitor                  = 2;
		$this->auto_delete              = false;
		$this->current_mail_address     = RegPage::$login_value2_email;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("17"));
		$this->mls_to_subscribe = array(".//*[@id='jform_ml_available_7']");

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to register user witch has already a subscription, plugin mailinglist differs from subscribed ones
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithExistingSubscriptionDifferentList(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman while a subscription to another mailinglist exists");
		$I->expectTo('see another confirmed Joomla user and existing subscriber with additionally mailinglist');

		$this->initializeTestValues($I);
		$this->name_obligation          = false;
		$this->firstname_obligation     = false;
		$this->special_obligation       = false;
		$this->visitor                  = 2;
		$this->auto_delete              = false;
		$this->current_mail_address     = RegPage::$login_value2_email;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);

		$this->_deselectNewMailinglist($I);
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
	public function User2SubscriberFunctionWithActivationByBackend(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with activation by backend");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format');

		$this->initializeTestValues($I);
		$this->name_obligation          = false;
		$this->firstname_obligation     = false;
		$this->special_obligation       = false;
		$this->visitor                  = 1;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activateByBackend($I);
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
	public function User2SubscriberFunctionWithTextFormat(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with text format");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		$this->format   = 'Text';

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->switchSubscriptionToYes($I);
		$I->clickAndWait(RegPage::$subs_identifier_format_text, 1);
		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithoutFormatSelectionHTML(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with predefined format HTML");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithoutFormatSelectionText(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with predefined format text");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		$this->format   = 'Text';
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '0');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->checkBackendSuccessSimple($I);

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
	public function User2SubscriberFunctionWithAnotherMailinglist(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to another mailinglist");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format and another mailinglist');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->mls_to_subscribe = array(RegPage::$mailinglist2_checked);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithTwoMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to two mailinglists");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format and two mailinglists');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("4", "6"));

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->mls_to_subscribe = array(RegPage::$mailinglist1_checked, RegPage::$mailinglist2_checked);

		$this->checkBackendSuccessSimple($I);
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
	public function User2SubscriberFunctionWithoutMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to zero mailinglists");
		$I->expectTo('see confirmed Joomla user and no subscriber');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("0"));

		$this->selectRegistrationPage($I);

		$this->_dontSeePluginInputFields($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->mls_to_subscribe = array("0");

		$admin = $I->haveFriend('Admin10');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->activated = false;
			$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);
			SubsManagePage::gotoSubscribersListTab($I, $this->activated);
			$this->filterForSubscriber($I);

			$I->dontSee(RegPage::$login_value_name, $identifier);

			$this->activated = true;
			$identifier = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);
			SubsManagePage::gotoSubscribersListTab($I, $this->activated);
			$this->filterForSubscriber($I);

			$I->dontSee(RegPage::$login_value_name, $identifier);

			$this->deleteJoomlaUser($I);

			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();
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
	public function User2SubscriberFunctionWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, change mail address with auto update");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		$this->current_mail_address  = $this->subscriber_mail_old;

		$this->checkBackendSuccessWithMailChange($I);
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
	public function User2SubscriberFunctionWithoutActivationWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman without activation, then change mail address");
		$I->expectTo('see unconfirmed Joomla user and unconfirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		$this->current_mail_address  = $this->subscriber_mail_old;

		$this->checkBackendSuccessWithMailChange($I);
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
	public function User2SubscriberFunctionWithMailChangeNo(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, change mail address without auto update");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, see changed mail address for user but not subscriber');

		$this->initializeTestValues($I);

		//set other option settings
		$this->auto_update  = false;
		$I->setManifestOption('bwpm_user2subscriber', 'auto_update_email_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		$this->current_mail_address  = $this->subscriber_mail_old;

		$this->checkBackendSuccessWithMailChange($I);
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
	public function User2SubscriberFunctionWithDeleteNo(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, delete account and not delete subscription");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, delete user, but see subscriber without joomla user id, then subscribe anew and see new user ID');

		$this->initializeTestValues($I);

		//set other option settings
		$this->auto_delete  = false;
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->_activate($I);

		// Delete account
		$admin = $I->haveFriend('Admin2');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->deleteJoomlaUserByDb($I);

			$this->checkForSubscriptionSuccess($I);
			$this->deleteJoomlaUserByDb($I);

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

			return null;
		}
		);
		$admin->leave();

		//reset option settings
		$this->auto_delete  = true;
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '1');

		// register anew
		$this->selectRegistrationPage($I);

		$this->fillJoomlaPartAtRegisterForm($I, 100);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to check for deactivated plugin
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsPluginDeactivated(AcceptanceTester $I)
	{
		$I->wantTo("Deactivate Plugin User2Subscriber");
		$I->expectTo('not see plugin fields at Joomla registration form');

		$I->setExtensionStatus('bwpm_user2subscriber', 0);

		$this->selectRegistrationPage($I);

		$this->_dontSeePluginInputFields($I);

		$I->setExtensionStatus('bwpm_user2subscriber', 1);
	}

	/**
	 * Test method to option message
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsMessage(AcceptanceTester $I)
	{
		$I->wantTo("change newsletter message and change back");
		$I->expectTo('see changed messages as tooltip at Joomla registration form');

		$this->editPluginOptions($I);
		$I->clickAndWait(RegPage::$plugin_tab_options, 1);

		$I->fillField(RegPage::$plugin_message_identifier, RegPage::$plugin_message_new);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->see(RegPage::$plugin_message_new, RegPage::$plugin_message_identifier);

		// look at FE
		$user = $I->haveFriend('User');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);

			$I->see(RegPage::$plugin_message_new, ".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p");
		}
		);
		$user->leave();

		$I->fillField(RegPage::$plugin_message_identifier, RegPage::$plugin_message_old);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->see(RegPage::$plugin_message_old, RegPage::$plugin_message_identifier);

		// look at FE
		$user = $I->haveFriend('User3');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);

			$I->see(RegPage::$plugin_message_old, ".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p");
		}
		);
		$user->leave();

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * Test method to option show newsletter format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsSwitchShowFormat(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'newsletter show format' from yes to no and back");
		$I->expectTo('see, see not, see format selection at Joomla registration form');

		$this->editComponentOptions($I);

		// switch to no
		$I->clickAndWait(".//*[@id='configTabs']/li[2]/a", 1);
		$this->_switchPredefinedNewsletterFormat($I, RegPage::$format_show_button_identifier, 1);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("0", $com_options->show_emailformat);

		// look at FE
		$user = $I->haveFriend('User4');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);
			$I->scrollTo(RegPage::$view_register_subs);
			$this->switchSubscriptionToYes($I);

			$I->dontSeeElement(RegPage::$subs_identifier_format_html);
			$I->dontSeeElement(RegPage::$subs_identifier_format_text);
		}
		);
		$user->leave();

		// switch to yes
		$this->_switchPredefinedNewsletterFormat($I, RegPage::$format_show_button_identifier, 2);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("1", $com_options->show_emailformat);

		// look at FE
		$user = $I->haveFriend('User5');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);
			$I->scrollTo(RegPage::$view_register_subs);
			$this->switchSubscriptionToYes($I);

			$I->seeElement(RegPage::$subs_identifier_format_text);
			$I->seeElement(RegPage::$subs_identifier_format_html);
		}
		);
		$user->leave();

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * Test method to option predefined newsletter format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberPredefinedFormat(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'newsletter format' from yes to no and back");
		$I->expectTo('see Text, see HTML preselected at Joomla registration form');

		$this->editComponentOptions($I);

		// switch to Text
		$I->clickAndWait(".//*[@id='configTabs']/li[2]/a", 1);
		$this->_switchPredefinedNewsletterFormat($I, RegPage::$mailformat_button_identifier, 1);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("0", $com_options->default_emailformat);

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		// look at FE
		$user = $I->haveFriend('User6');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);
			$this->switchSubscriptionToYes($I);

			// @ToDo: FF gets green, Chromium gets red (additional class button-danger)
			$I->seeElement(RegPage::$subs_identifier_format_text, ['class' => RegPage::$button_red]);
			$I->dontSeeElement(RegPage::$subs_identifier_format_html, ['class' => Generals::$button_green]);
		}
		);
		$user->leave();

		$this->selectComponentPage($I);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);

		// switch to html
		$I->clickAndWait(".//*[@id='configTabs']/li[2]/a", 1);
		$this->_switchPredefinedNewsletterFormat($I, RegPage::$mailformat_button_identifier, 2);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("1", $com_options->default_emailformat);

		// look at FE
		$user = $I->haveFriend('User7');
		$user->does(function (AcceptanceTester $I)
		{
			$this->selectRegistrationPage($I);
			$this->switchSubscriptionToYes($I);

			// @ToDo: FF gets green, Chromium gets red (additional class button-danger)
			$I->dontSeeElement(RegPage::$subs_identifier_format_text, ['class' => RegPage::$button_red]);
			$I->seeElement(RegPage::$subs_identifier_format_html, ['class' => Generals::$button_green]);
		}
		);
		$user->leave();

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * Test method to option auto update
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsAutoUpdate(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'auto update email' from yes to no and back");
		$I->expectTo('see No, see Yes at field auto update of plugin options form');

		$this->editPluginOptions($I);
		$I->clickAndWait(RegPage::$plugin_tab_options, 1);

		// switch to Text
		$I->clickAndWait(RegPage::$plugin_auto_update_no, 1);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->seeElement(RegPage::$plugin_auto_update_no, ['class' => Generals::$button_red]);

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("0", $options->auto_update_email_option);

		// switch to yes
		$I->clickAndWait(RegPage::$plugin_auto_update_yes, 1);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->seeElement(RegPage::$plugin_auto_update_yes, ['class' => Generals::$button_green]);

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("1", $options->auto_update_email_option);

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * Test method to option auto delete
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsAutoDelete(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'auto delete' from yes to no and back");
		$I->expectTo('see No, see Yes at auto delete of plugin options form');

		$this->editPluginOptions($I);
		$I->clickAndWait(RegPage::$plugin_tab_options, 1);

		// switch to Text
		$I->clickAndWait(RegPage::$plugin_auto_delete_no, 1);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->seeElement(RegPage::$plugin_auto_delete_no, ['class' => Generals::$button_red]);

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("0", $options->auto_delete_option);

		// switch to yes
		$I->clickAndWait(RegPage::$plugin_auto_delete_yes, 1);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->seeElement(RegPage::$plugin_auto_delete_yes, ['class' => Generals::$button_green]);

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("1", $options->auto_delete_option);

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * Test method to option mailinglists
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("add additional mailinglist to options");
		$I->expectTo('see further selected mailinglist at plugin options form');

		$this->editPluginOptions($I);
		$I->clickAndWait(RegPage::$plugin_tab_mailinglists, 1);

		// click checkbox for further mailinglist
		$I->checkOption(sprintf(RegPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->seeCheckboxIsChecked(sprintf(RegPage::$plugin_checkbox_mailinglist, 6));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("1", $options->ml_available[0]);
		$I->assertEquals("4", $options->ml_available[1]);

		// deselect further mailinglist
		$I->uncheckOption(sprintf(RegPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$plugin_saved_success);
		$I->dontSeeCheckboxIsChecked(sprintf(RegPage::$plugin_checkbox_mailinglist, 5));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		$I->assertEquals("4", $options->ml_available[0]);

		$I->clickAndWait(RegPage::$toolbar_save_button, 1);

		LoginPage::logoutFromBackend($I);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function initializeTestValues($I)
	{
		$this->tester                = $I;
		$this->activated             = false;
		$this->subscription_selected = true;
		$this->subscription_only     = false;
		$this->mls_to_subscribe      = array(RegPage::$mailinglist1_checked);
		$this->current_mail_address  = RegPage::$login_value_email;
		$this->format                = 'HTML';
		$this->auto_update           = true;
		$this->auto_delete           = true;
		$this->name_obligation       = true;
		$this->firstname_obligation  = true;
		$this->special_obligation    = true;
		$this->show_gender           = false;
		$this->check_gender          = false;
		$this->visitor               = 1;

		//reset option settings
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_update_email_option', '1');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '1');
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("4"));
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_gender', '0');
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function selectRegistrationPage(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$register_url);
		$I->waitForElementVisible(RegPage::$view_register);
		$I->seeElement(RegPage::$view_register);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function switchSubscriptionYesAndNo(AcceptanceTester $I)
	{
		$this->switchSubscriptionToYes($I);
		$this->switchSubscriptionToNo($I);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function switchSubscriptionToYes(AcceptanceTester $I)
	{
		$I->scrollTo(".//*[@id='member-registration']");
		$I->click(RegPage::$subs_identifier_subscribe_yes);
		$I->waitForElementVisible(RegPage::$subs_identifier_name);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function switchSubscriptionToNo(AcceptanceTester $I)
	{
		$I->scrollTo(".//*[@id='member-registration']");
		$I->click(RegPage::$subs_identifier_subscribe_no);
		$I->waitForElementNotVisible(RegPage::$subs_identifier_name);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 * @param int              $run
	 *
	 * @since 2.0.0
	 */
	protected function fillJoomlaPartAtRegisterForm(AcceptanceTester $I, $run   = 1)
	{
		$I->scrollTo(".//*[@id='member-registration']");

		if ($this->visitor == 1)
		{
			$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value_name);
			$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value_username);
			$I->fillField(RegPage::$login_identifier_email1, RegPage::$login_value_email);
			$I->fillField(RegPage::$login_identifier_email2, RegPage::$login_value_email);
		}
		elseif ($this->visitor == 2)
		{
			$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value2_name);
			$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value2_username);
			$I->fillField(RegPage::$login_identifier_email1, RegPage::$login_value2_email);
			$I->fillField(RegPage::$login_identifier_email2, RegPage::$login_value2_email);
		}

		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);

		// ensure all is cleaned up
		$admin = $I->haveFriend('Admin' . $run);
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);
			$this->deleteJoomlaUserByDb($I);
			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();
	}

	/**
	 * Method to fill all required BwPostman fields on Joomla registration form
	 * This method fills in the end all fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function fillBwPostmanPartAtRegisterFormExtended(AcceptanceTester $I)
	{
		$com_options    = $I->getManifestOptions('com_bwpostman');

		$I->clickAndWait(RegPage::$subs_identifier_subscribe_yes, 1);

		// omit BwPostman fields
		$I->clickAndWait(RegPage::$login_identifier_register, 1);
		$I->scrollTo(Generals::$alert, 0, -100);
		$I->see(RegPage::$error_message_name);
		$I->see(RegPage::$error_message_firstname);
		$I->see(sprintf(RegPage::$error_message_special, $com_options->special_label));

		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);

		if ($com_options->show_gender)
		{
			$I->clickAndWait(RegPage::$subs_identifier_female, 1);
			$this->check_gender     = true;
			$this->gender_selected  = 'female';
		}

		if ($com_options->show_name_field || $com_options->name_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_name, RegPage::$subs_value_name);
		}

		if ($com_options->show_firstname_field || $com_options->firstname_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_firstname, RegPage::$subs_value_firstname);
		}

		if ($com_options->show_special || $com_options->special_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_special, RegPage::$subs_value_special);
		}
	}

	/**
	 * Method to fill all required BwPostman fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function fillBwPostmanPartAtRegisterFormSimple(AcceptanceTester $I)
	{
		$com_options    = $I->getManifestOptions('com_bwpostman');

		$this->switchSubscriptionToYes($I);
//		$I->clickAndWait(RegPage::$subs_identifier_subscribe_yes, 1);

		if ($com_options->show_gender)
		{
			$I->clickAndWait(RegPage::$subs_identifier_male, 1);
			$this->check_gender = true;
		}

		if ($com_options->show_name_field || $com_options->name_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_name, RegPage::$subs_value_name);
		}

		if ($com_options->show_firstname_field || $com_options->firstname_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_firstname, RegPage::$subs_value_firstname);
		}

		if ($com_options->show_special || $com_options->special_field_obligation)
		{
			$I->fillField(RegPage::$subs_identifier_special, RegPage::$subs_value_special);
		}
	}

	/**
	 * Method to fill all required BwPostman fields on Joomla registration form, with optional fields
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function fillBwPostmanPartAtRegisterFormSimpleOnlySubscription(AcceptanceTester $I)
	{
		$I->clickAndWait(RegPage::$subs_identifier_subscribe_yes, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function registerAndCheckMessage(AcceptanceTester $I)
	{
		$I->click(RegPage::$login_identifier_register);

		$I->waitForElementVisible(RegPage::$success_heading_identifier, 30);

		$I->see(Generals::$alert_msg_txt, RegPage::$success_heading_identifier);
		$I->see(RegPage::$register_success, RegPage::$success_message_identifier);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function checkBackendSuccessSimple(AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin5');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->checkForSubscriptionSuccess($I);
			$this->deleteJoomlaUserByDb($I);
			$this->checkForSubscriptionDeletion($I);

			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();
	}

	/**
	 * Method to check if subscription was successful
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForSubscriptionSuccess(AcceptanceTester $I)
	{
		if ($this->subscription_selected || ($this->auto_delete !== true))
		{
			$result      = array();

			$result['email']    = $this->current_mail_address;
			if ($this->subscription_only)
			{
				$result['name'] = '';
				$result['firstname'] = '';
			}
			else
			{
				if ($this->visitor != 2)
				{
					$format = 1;
					if ($this->format == "Text")
					{
						$format = 0;
					}
					$result['emailformat'] = $format;
				}
				$com_options = $I->getManifestOptions('com_bwpostman');

				if ($com_options->show_gender && $this->check_gender)
				{
					$gender = 0;
					if ($this->gender_selected == "female")
					{
						$gender = 1;
					}

					$result['gender'] = $gender;
				}

				if ($com_options->show_name_field || $com_options->name_field_obligation)
				{
					$result['name'] = RegPage::$subs_value_name;

					if ($this->visitor == 2)
					{
						$result['name'] = RegPage::$subs_value2_name;
					}
				}

				if ($com_options->show_firstname_field || $com_options->firstname_field_obligation)
				{
					$result['firstname'] = RegPage::$subs_value_firstname;

					if ($this->visitor == 2)
					{
						$result['firstname'] = RegPage::$subs_value2_firstname;
					}
				}

				$result['special'] = '';

				if ($com_options->show_special || $com_options->special_field_obligation)
				{
					$result['special'] = RegPage::$subs_value_special;

					if ($this->visitor == 2)
					{
						$result['special'] = '';
					}
				}
			}
			$I->seeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', $result);
		}
		else
		{
			$I->dontSeeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', array('email' => RegPage::$login_value_email));
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function filterForSubscriber(AcceptanceTester $I)
	{
		if ($this->name_obligation)
		{
			$search_value   = RegPage::$subs_value_name;
			$search_field   = 'Name';

			if ($this->visitor == 2)
			{
				$search_value   = RegPage::$subs_value2_name;
			}

		}
		else
		{
			$search_value     = RegPage::$login_value_email;
			$search_field   = 'Email';

			if ($this->visitor == 2)
			{
				$search_value   = RegPage::$login_value2_email;
			}
		}

		$search_for_value = sprintf(SubsManagePage::$search_for_value, $search_field);
		SubsManagePage::filterForSubscriber($I, $search_value, $search_for_value);
	}

	/**
	 * Method to delete Joomla user account
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function deleteJoomlaUserByDb(AcceptanceTester $I)
	{

		$user_id = $I->grabFromDatabase(Generals::$db_prefix . 'users', 'id', array('email' => $this->current_mail_address));
		$subs_id = $I->grabFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => $this->current_mail_address));

		if ($user_id)
		{
			$where_user      = ' WHERE `id` = ' . $user_id;
			$where_usergroup = ' WHERE `user_id` = ' . $user_id;
			$I->deleteRecordFromDatabase('users', $where_user);
			$I->deleteRecordFromDatabase('user_usergroup_map', $where_usergroup);
		}

		if ($subs_id && $this->auto_delete)
		{
			$where_subscriber     = ' WHERE `id` = ' . $subs_id;
			$where_subscriber_map = ' WHERE `subscriber_id` = ' . $subs_id;

			$I->deleteRecordFromDatabase('bwpostman_subscribers', $where_subscriber);
			$I->deleteRecordFromDatabase('bwpostman_subscribers_mailinglists', $where_subscriber_map);
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
			$I->waitForElement(Generals::$alert_header, 30);
			$I->see(Generals::$alert_msg_txt, Generals::$alert_header);
			$I->see(RegPage::$delete_success, Generals::$alert_success);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function gotoUserManagement(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$user_management_url);
		$I->waitForElementVisible(Generals::$pageTitle);
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
		$login_value_name   = RegPage::$login_value_name;

		if ($this->visitor == 2)
		{
			$login_value_name   = RegPage::$login_value2_name;
		}
		$I->fillField(Generals::$search_field, $login_value_name);
		$I->clickAndWait(Generals::$search_button, 1);

		try
		{
			$user_found = $I->grabTextFrom(RegPage::$user_edit_identifier);
		}
		catch (\Codeception\Exception\ElementNotFound $e)
		{
			return false;
		}

		if ($user_found == $login_value_name)
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
		if ($this->auto_delete)
		{
			$I->dontSeeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', array('email' => $this->current_mail_address));
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function checkBackendSuccessWithMailChange(AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin6');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->checkForSubscriptionSuccess($I);

			$this->gotoUserManagement($I);
			$user_found = $this->findUser($I);

			if ($user_found)
			{
				$this->changeMailAddressOfAccount($I);
				$this->current_mail_address  = $this->subscriber_mail_new;

				$this->checkMailChangeOfSubscription($I);

				// delete user
				$this->gotoUserManagement($I);
				$this->deleteJoomlaUserByDb($I);
				$this->checkForSubscriptionDeletion($I);
			}

			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();
	}

	/**
	 * @param AcceptanceTester $I
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
	 * @since 2.0.0
	 */
	protected function checkMailChangeOfSubscription(AcceptanceTester $I)
	{
		$search_values  = array();

		$search_values['name']      = RegPage::$subs_value_name;
		$search_values['status']    = $this->activated;
		$search_values['email']     = $this->current_mail_address;

		if ($this->auto_update)
		{
			$I->seeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', $search_values);
		}
		else
		{
			$I->dontSeeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', $search_values);
		}
	}
	/**
	 * Test method to activate Joomla registration by user himself
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _activate(\AcceptanceTester $I)
	{
		$activation_code = $I->getJoomlaActivationCode($this->current_mail_address);
		$I->amOnPage(RegPage::$user_activation_url . $activation_code);
		$this->activated    = true;
	}


	/**
	 * Test method to activate Joomla registration by backend
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _activateByBackend(\AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin7');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			$this->gotoUserManagement($I);
			$user_found = $this->findUser($I);

			if ($user_found)
			{
				$I->seeElement(".//*[@id='userList']/tbody/tr[1]/td[5]/a/span", ['class' => 'icon-unpublish']);
				$I->clickAndWait('.//*[@id=\'userList\']/tbody/tr[1]/td[5]/a', 1);
				$I->seeElement(".//*[@id='userList']/tbody/tr[1]/td[5]/a/span", ['class' => 'icon-publish']);
				$this->activated    = true;

				$this->checkForSubscriptionSuccess($I);

				// delete user
				$this->gotoUserManagement($I);
				$this->deleteJoomlaUserByDb($I);
				$this->checkForSubscriptionDeletion($I);
			}

			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();

		$this->activated    = true;
	}

	/**
	 * Test method to deselect newly optioned mailinglist at subscription
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _deselectNewMailinglist(\AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin8');
		$admin->does(function (AcceptanceTester $I)
		{
			LoginPage::logIntoBackend(Generals::$admin);

			SubsManagePage::gotoSubscribersListTab($I, $this->activated);
			$this->filterForSubscriber($I);

			// look in details for selected mailinglists
			$edit_identifier        = $this->getTabDependentIdentifier(RegPage::$subscriber_edit_link);
			$I->clickAndWait($edit_identifier, 1);

			$I->scrollTo(RegPage::$mailinglist_fieldset_identifier, 0, -100);

			foreach ($this->mls_to_subscribe as $ml)
			{
				$I->seeCheckboxIsChecked($ml);

				$I->uncheckOption($ml);

				$I->dontSeeCheckboxIsChecked($ml);
			}
			$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

			LoginPage::logoutFromBackend($I);
		}
		);
		$admin->leave();
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
	 * @since 2.0.0
	 */
	protected function editPluginOptions(AcceptanceTester $I)
	{
		$this->tester = $I;
		LoginPage::logIntoBackend(Generals::$admin);

		RegPage::selectPluginPage($I);

		RegPage::filterForPlugin($I, RegPage::$plugin_name);

		$I->clickAndWait(RegPage::$plugin_edit_identifier, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function editComponentOptions(AcceptanceTester $I)
	{
		$this->tester = $I;
		LoginPage::logIntoBackend(Generals::$admin);

		$this->selectComponentPage($I);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function selectComponentPage(AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$url);
		$I->waitForElementVisible(Generals::$pageTitle);
		$I->see(Generals::$extension, Generals::$pageTitle);
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

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
	 */
	private function _dontSeePluginInputFields(AcceptanceTester $I)
	{
		$I->dontSee(RegPage::$subs_identifier_subscribe_yes);
		$I->dontSee(RegPage::$subs_identifier_subscribe_no);
		$I->dontSee(RegPage::$subs_identifier_format_html);
		$I->dontSee(RegPage::$subs_identifier_format_text);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param int              $format
	 *
	 *
	 * @since 2.0.0
	 */
	private function _switchPredefinedNewsletterFormat(AcceptanceTester $I, $button, $format)
	{
		$class  = Generals::$button_red;
		if ($format == 2)
		{
			$class  = Generals::$button_green;
		}
		$I->scrollTo(RegPage::$mailformat_identifier, 0, -100);
		$I->clickAndWait(sprintf($button, $format), 1);
		$I->clickAndWait(RegPage::$toolbar_apply_button, 1);
		$I->see(RegPage::$config_save_success);
		$I->clickAndWait(".//*[@id='configTabs']/li[2]/a", 1);
		$I->scrollTo(RegPage::$mailformat_identifier, 0, -100);
		$I->seeElement(sprintf($button, $format), ['class' => $class]);
	}
}

