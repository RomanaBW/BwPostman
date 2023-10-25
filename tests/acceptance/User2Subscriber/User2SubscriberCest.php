<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\User2SubscriberPage as RegPage;
use Page\SubscriberManagerPage as SubsManagePage;
use Page\InstallationPage as InstallPage;

//use Codeception\Extension\BwRunFailed;

/**
 * Class User2SubscriberCest
 *
 * This class contains all methods to test subscription while registration to Joomla! at front end
 *
 * @package Register Subscribe Plugin
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
class User2SubscriberCest
{
	/**
	 * @var AcceptanceTester  $tester AcceptanceTester
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
	private static $subscription_selected = true;

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
	private static $format = 'HTML';

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
	private static $auto_delete = true;

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
	 * @var bool  $current_mail_address
	 *
	 * @since   2.0.0
	 */
	private static $current_mail_address = '';

	/**
	 * @var bool  $subscription_only
	 *
	 * @since   2.0.0
	 */
	private static $subscription_only      = false;

	/**
	 * @var bool  $name_obligation
	 *
	 * @since   2.0.0
	 */
	private $name_obligation        = true;

	/**
	 * @var bool  $firstname_obligation
	 *
	 * @since   2.0.0
	 */
	private $firstname_obligation   = true;

	/**
	 * @var bool  $special_obligation
	 *
	 * @since   2.0.0
	 */
	private $special_obligation     = true;

	/**
	 * @var bool  $show_gender
	 *
	 * @since   2.0.0
	 */
	private $show_gender            = false;
	/**
	 * @var bool  $check_gender
	 *
	 * @since   2.0.0
	 */

	private static $check_gender           = false;
	/**
	 * @var bool  $gender_selected
	 *
	 * @since   2.0.0
	 */

	private static $gender_selected        = 'male';

	/**
	 * @var bool  $visitor
	 *
	 * @since   2.0.0
	 */
	private static $visitor        = 1;

	/**
	 * Test method to login into backend
	 *
	 * @param   Page\Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin, $this->tester);
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
	 * @throws Exception
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

		$I->click(RegPage::$plugin_edit_identifier);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$plugin_u2s);

		// set mailinglist
		$I->click(RegPage::$plugin_tab_mailinglists);
		$I->waitForElement("//*[@id='jform_params_ml_available']/div", 30);

		$I->scrollTo(sprintf(RegPage::$plugin_checkbox_mailinglist, 6), 0, -100);
		$I->wait(1);
		$checked    = $I->grabAttributeFrom(sprintf(RegPage::$plugin_checkbox_mailinglist_input, 6), "checked");
		codecept_debug('Checkbox Mailinglist ID6: ' . $checked);
		if (!$checked)
		{
			$I->click(sprintf(RegPage::$plugin_checkbox_mailinglist, 6));
		}

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithoutSubscription(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and not subscribe to BwPostman");
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$this->initializeTestValues($I);
		self::$subscription_selected    = false;

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionSwitchSubscriptionWithoutSubscription(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla, switch subscription to yes and back to no and not subscribe to BwPostman");
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$this->initializeTestValues($I);
		self::$subscription_selected    = false;

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

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
	 * @throws Exception
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

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

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
	 * @throws Exception
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
		self::$subscription_only     = true;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to register user witch has already a subscription, plugin mailinglist is same as subscribed one
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
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
		self::$visitor                  = 2;
		self::$auto_delete              = false;
		self::$current_mail_address     = RegPage::$login_value2_email;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array("17"));
		$this->mls_to_subscribe = array(".//*[@id='jform_ml_available_7']");

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->checkBackendSuccessSimple($I);
	}

	/**
	 * Test method to register user witch has already a subscription, plugin mailinglist differs from subscribed ones
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
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
		self::$visitor                  = 2;
		self::$auto_delete              = false;
		self::$current_mail_address     = RegPage::$login_value2_email;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimpleOnlySubscription($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->checkBackendSuccessSimple($I);

		$this->deselectNewMailinglist($I);
	}

	/**
	 * Test method to register user with subscription selected yes
	 * You see Joomla confirmed user and confirmed subscriber with HTML format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
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
		self::$visitor                  = 1;
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activateByBackend($I);
	}

	/**
	 * Test method to register user with subscription selected yes with activation and selected text format
	 * You see Joomla confirmed user and confirmed subscriber with Text format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithTextFormat(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with text format");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		self::$format   = 'Text';

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$I->scrollTo(RegPage::$subs_identifier_format_text, 0, -100);
		$I->wait(1);
		$I->clickAndWait(RegPage::$subs_identifier_format_text, 1);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

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
	 * @throws Exception
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

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithoutFormatSelectionText(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman with predefined format text");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with Text format');

		$this->initializeTestValues($I);

		//set other option settings
		self::$format   = 'Text';
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '0');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '0');

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

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
	 * @throws Exception
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

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

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
	 * @throws Exception
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

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithoutMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman to zero mailinglists");
		$I->expectTo('see confirmed Joomla user and no subscriber');

		$this->initializeTestValues($I);

		//set other option settings
		$I->setManifestOption('bwpm_user2subscriber', 'ml_available', array());

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$I->dontSee(RegPage::$subs_identifier_subscribe_no);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->mls_to_subscribe = array("0");

		$admin = $I->haveFriend('Admin10');
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				$this->activated = false;
				$identifier = self::getTabDependentIdentifier(RegPage::$subscriber_edit_link);
				SubsManagePage::gotoSubscribersListTab($I, $this->activated);
				self::filterForSubscriber($I);

				$I->dontSee(RegPage::$login_value_name, $identifier);

				$this->activated = true;
				$identifier = self::getTabDependentIdentifier(RegPage::$subscriber_edit_link);
				SubsManagePage::gotoSubscribersListTab($I, $this->activated);
				self::filterForSubscriber($I);

				$I->dontSee(RegPage::$login_value_name, $identifier);

				self::deleteJoomlaUser($I);

				$loginPage->logoutFromBackend($I, false);
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, change mail address with auto update");
		$I->expectTo('see confirmed Joomla user and confirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		self::$current_mail_address  = $this->subscriber_mail_old;

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithoutActivationWithMailChangeYes(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman without activation, then change mail address");
		$I->expectTo('see unconfirmed Joomla user and unconfirmed subscriber with HTML format, see changed mail address for user and subscriber');

		$this->initializeTestValues($I);

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		self::$current_mail_address  = $this->subscriber_mail_old;

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
	 * @throws Exception
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

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		$this->subscriber_mail_old   = RegPage::$login_value_email;
		$this->subscriber_mail_new   = RegPage::$change_value_email;
		self::$current_mail_address  = $this->subscriber_mail_old;

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberFunctionWithDeleteNo(AcceptanceTester $I)
	{
		$I->wantTo("Register at Joomla and subscribe to BwPostman, delete account and not delete subscription");
		$I->expectTo(
			'see confirmed Joomla user and confirmed subscriber with HTML format, delete user, but see subscriber 
			without joomla user id, then subscribe anew and see new user ID'
		);

		$this->initializeTestValues($I);

		//set other option settings
		self::$auto_delete  = false;
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '0');

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->fillJoomlaPartAtRegisterForm($I);

		$this->fillBwPostmanPartAtRegisterFormSimple($I);

		$this->registerAndCheckMessage($I);

		$this->activate($I);

		// Delete account
		$admin = $I->haveFriend('Admin2');
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				self::deleteJoomlaUserByDb($I);

				self::checkForSubscriptionSuccess($I);
				self::deleteJoomlaUserByDb($I);

				// assert subscription is there without Joomla user ID
				try
				{
					$user_id    = $I->grabTextFrom(RegPage::$user_id_identifier);
				}
				catch (Exception $e)
				{
					$loginPage->logoutFromBackend($I, false);
					return false;
				}

				$I->assertEmpty($user_id);

				$loginPage->logoutFromBackend($I, false);

				return null;
			}
		);
		$admin->leave();

		//reset option settings
		self::$auto_delete  = true;
		$I->setManifestOption('bwpm_user2subscriber', 'auto_delete_option', '1');

		// register anew
		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsPluginDeactivated(AcceptanceTester $I)
	{
		$I->wantTo("Deactivate Plugin User2Subscriber");
		$I->expectTo('not see plugin fields at Joomla registration form');

		$I->setExtensionStatus('bwpm_user2subscriber', 0);

		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$this->dontSeePluginInputFields($I);

		$I->setExtensionStatus('bwpm_user2subscriber', 1);
	}

	/**
	 * Test method to option message
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
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
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->see(RegPage::$plugin_message_new, RegPage::$plugin_message_identifier);

		// look at FE
		$user = $I->haveFriend('User');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

				$I->scrollTo(".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p", 0, -100);
				$I->wait(1);
				$I->see(RegPage::$plugin_message_new, ".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p");
			}
		);
		$user->leave();

		$I->fillField(RegPage::$plugin_message_identifier, RegPage::$plugin_message_old);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->see(RegPage::$plugin_message_old, RegPage::$plugin_message_identifier);

		// look at FE
		$user = $I->haveFriend('User3');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

				$I->scrollTo(".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p", 0, -100);
				$I->wait(1);
				$I->see(RegPage::$plugin_message_old, ".//*[@id='member-registration']/fieldset[2]/div[1]/div[2]/p");
			}
		);
		$user->leave();

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$loginPage = new LoginPage($I);
		$loginPage->logoutFromBackend($I, false);
	}

	/**
	 * Test method to option show newsletter format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsSwitchShowFormat(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'newsletter show format' from yes to no and back");
		$I->expectTo('see, see not, see format selection at Joomla registration form');

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$this->editComponentOptions($I);

		// switch to no
		$I->clickAndWait(RegPage::$bwpm_com_options_regTab, 1);
		$this->switchPredefinedNewsletterShow($I, RegPage::$format_show_button_identifier, 0);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("0", $com_options->show_emailformat);

		// look at FE
		$user = $I->haveFriend('User4');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);
				$I->scrollTo(RegPage::$view_register_subs);
				$I->wait(1);
				$this->switchSubscriptionToYes($I);

				$I->dontSeeElement(RegPage::$subs_identifier_format_html);
				$I->dontSeeElement(RegPage::$subs_identifier_format_text);
			}
		);
		$user->leave();

		// switch to yes
		$this->switchPredefinedNewsletterShow($I, RegPage::$format_show_button_identifier, 1);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("1", $com_options->show_emailformat);

		// look at FE
		$user = $I->haveFriend('User5');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);
				$I->scrollTo(RegPage::$view_register_subs);
				$I->wait(1);
				$this->switchSubscriptionToYes($I);

				$I->scrollTo(RegPage::$subs_identifier_format_text, 0, -150);
				$I->wait(1);

				$I->see('HTML', sprintf(RegPage::$subs_identifier_format_html, 1));

			}
		);
		$user->leave();

		$I->scrollTo(Generals::$joomlaHeader,0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$loginPage = new LoginPage($I);
		$loginPage->logoutFromBackend($I, false);
	}

	/**
	 * Test method to option predefined newsletter format
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberPredefinedFormat(AcceptanceTester $I)
	{
		$I->wantTo("switch option 'newsletter format' from yes to no and back");
		$I->expectTo('see Text, see HTML preselected at Joomla registration form');

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$this->editComponentOptions($I);

		// switch to Text
		$I->clickAndWait(RegPage::$bwpm_com_options_regTab, 1);
		$this->switchPredefinedNewsletterFormat($I, RegPage::$mailformat_button_identifier, 0);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("0", $com_options->default_emailformat);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		// look at FE
		$user = $I->haveFriend('User6');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);
				$this->switchSubscriptionToYes($I);

				$I->scrollTo(RegPage::$subs_identifier_format_text, 0, -150);
				$I->wait(1);

				$I->see('TEXT', sprintf(RegPage::$subs_identifier_format_html, 0));
				$I->dontSee('HTML', sprintf(RegPage::$subs_identifier_format_html, 1));
			}
		);
		$user->leave();

		$this->selectComponentPage($I);

		$I->clickAndWait(Generals::$toolbar4['Options'], 1);

		// switch to html
		$I->clickAndWait(RegPage::$bwpm_com_options_regTab, 1);
		$this->switchPredefinedNewsletterFormat($I, RegPage::$mailformat_button_identifier, 1);

		// getManifestOption
		$com_options = $I->getManifestOptions('com_bwpostman');
		$I->assertEquals("1", $com_options->default_emailformat);

		// look at FE
		$user = $I->haveFriend('User7');
		$user->does(
			function (AcceptanceTester $I) {
				$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);
				$this->switchSubscriptionToYes($I);

				$I->scrollTo(RegPage::$subs_identifier_format_text, 0, -150);
				$I->wait(1);

				$I->dontSee('TEXT', sprintf(RegPage::$subs_identifier_format_html, 0));
				$I->see('HTML', sprintf(RegPage::$subs_identifier_format_html, 1));
			}
		);
		$user->leave();

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$loginPage = new LoginPage($I);
		$loginPage->logoutFromBackend($I, false);
	}

	/**
	 * Test method to option mailinglists
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function User2SubscriberOptionsMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("add additional mailinglist to options");
		$I->expectTo('see further selected mailinglist at plugin options form');

		// Preset all fields to be shown and obligatory if possible
		Generals::presetComponentOptions($I);

		$this->editPluginOptions($I);
		$I->clickAndWait(RegPage::$plugin_tab_mailinglists, 1);

		// click checkbox for further mailinglist
		$I->scrollTo(sprintf(RegPage::$plugin_checkbox_mailinglist, 0), 0, -100);
		$I->wait(1);
		$I->click(sprintf(RegPage::$plugin_checkbox_mailinglist, 0));

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->scrollTo(sprintf(RegPage::$plugin_checkbox_mailinglist, 6), 0, -100);
		$I->wait(1);
		$I->seeCheckboxIsChecked(sprintf(RegPage::$plugin_checkbox_mailinglist_input, 6));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		codecept_debug("Options:");
		codecept_debug($options);
		$I->assertEquals("1", $options->ml_available[0]);
		$I->assertEquals("4", $options->ml_available[1]);

		// deselect further mailinglist
		$I->scrollTo(sprintf(RegPage::$plugin_checkbox_mailinglist, 0), 0, -100);
		$I->wait(1);
		$I->click(sprintf(RegPage::$plugin_checkbox_mailinglist, 0));
		$I->dontSeeCheckboxIsChecked(sprintf(RegPage::$plugin_checkbox_mailinglist_input, 0));

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->scrollTo(sprintf(RegPage::$plugin_checkbox_mailinglist, 5), 0, -100);
		$I->wait(1);
		$I->dontSeeCheckboxIsChecked(sprintf(RegPage::$plugin_checkbox_mailinglist_input, 5));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_user2subscriber');
		codecept_debug("Available Mailinglists:");
		codecept_debug($options->ml_available);
		$I->assertEquals("4", $options->ml_available[0]);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$loginPage = new LoginPage($I);
		$loginPage->logoutFromBackend($I, false);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function initializeTestValues($I)
	{
		$this->tester                = $I;
		$this->activated             = false;
		self::$subscription_selected = true;
		self::$subscription_only     = false;
		$this->mls_to_subscribe      = array(RegPage::$mailinglist1_checked);
		self::$current_mail_address  = RegPage::$login_value_email;
		self::$format                = 'HTML';
		$this->auto_update           = true;
		self::$auto_delete           = true;
		$this->name_obligation       = true;
		$this->firstname_obligation  = true;
		$this->special_obligation    = true;
		$this->show_gender           = false;
		self::$check_gender          = false;
		self::$visitor               = 1;

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
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
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
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function switchSubscriptionToYes(AcceptanceTester $I)
	{
		$I->scrollTo(".//*[@id='member-registration']");
		$I->wait(1);
		$I->click(RegPage::$subs_identifier_subscribe_yes);
		$I->waitForElementVisible(RegPage::$subs_identifier_name, 5);
	}

	/**
	 * Method to fill all required Joomla fields on Joomla registration form
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function switchSubscriptionToNo(AcceptanceTester $I)
	{
		$I->scrollTo(".//*[@id='member-registration']");
		$I->wait(1);
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
		$I->scrollTo("//*[@id='member-registration']");
		$I->wait(1);

		if (self::$visitor == 1)
		{
			$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value_name);
			$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value_username);
			$I->fillField(RegPage::$login_identifier_email1, RegPage::$login_value_email);
			try
			{
				$I->fillField(RegPage::$login_identifier_email2, RegPage::$login_value_email);
			}
			catch (\Exception $e)
			{
				codecept_debug('No second mail address field');
			}
		}
		elseif (self::$visitor == 2)
		{
			$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value2_name);
			$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value2_username);
			$I->fillField(RegPage::$login_identifier_email1, RegPage::$login_value2_email);
			try
			{
				$I->fillField(RegPage::$login_identifier_email2, RegPage::$login_value2_email);
			}
			catch (\Exception $e)
			{
				codecept_debug('No second mail address field');
			}
		}

		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);

		// ensure all is cleaned up
		$admin = $I->haveFriend('Admin' . $run);
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);
				self::deleteJoomlaUserByDb($I);
				$loginPage->logoutFromBackend($I, false);
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
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function fillBwPostmanPartAtRegisterFormExtended(AcceptanceTester $I)
	{
		$com_options    = $I->getManifestOptions('com_bwpostman');

		$I->clickAndWait(RegPage::$subs_identifier_subscribe_yes, 1);

		// omit BwPostman fields
		$I->scrollTo(RegPage::$login_identifier_register, 0, -100);
		$I->wait(2);
		$I->clickAndWait(RegPage::$login_identifier_register, 1);
		$I->scrollTo(RegPage::$subs_identifier_subscribe_yes, 0, -100);
		$I->wait(2);

		$I->fillField(RegPage::$subs_identifier_special, '');

		$I->see(RegPage::$login_label_name, RegPage::$login_label_name_identifier);
		$I->see(RegPage::$error_message_missing, RegPage::$login_label_name_missing);

		$I->see(RegPage::$login_label_firstname, RegPage::$login_label_firstname_identifier);
		$I->see(RegPage::$error_message_missing, RegPage::$login_label_firstname_missing);

		$I->fillField(RegPage::$subs_identifier_name, '');

		$I->see($com_options->special_label, RegPage::$login_label_special_identifier);
		$I->see(RegPage::$error_message_missing, RegPage::$login_label_special_missing);

		$I->see(RegPage::$login_label_mailinglists, RegPage::$login_label_mailinglists_identifier);
		$I->see(RegPage::$error_message_mailinglists);

		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);

		$I->scrollTo(RegPage::$subs_identifier_subscribe_no);
		$I->wait(2);

		if ($com_options->show_gender)
		{
			$isPipeline = true;

			try
			{
				$I->grabTextFrom(RegPage::$gender_list . '/a/span');
				$isPipeline = false;
			}
			catch (Exception $e)
			{
				// Do nothing
			}

			if (!$isPipeline)
			{
				$I->scrollTo(RegPage::$gender_list, 0, -100);
				$I->wait(1);

				$I->click(RegPage::$gender_list_id);
				$I->waitForElementVisible(RegPage::$subs_identifier_female, 2);

				// click wanted value
				$I->click(RegPage::$subs_identifier_female);
			}
			else
			{
				$I->click(RegPage::$gender_list_classical);
				$I->waitForElementVisible(RegPage::$subs_identifier_female_classical, 2);

				// click wanted value
				$I->click(RegPage::$subs_identifier_female_classical);

				$I->selectOption(RegPage::$gender_list_classical, RegPage::$subs_option_female);
			}

			self::$check_gender     = true;
			self::$gender_selected  = 'female';
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

		$I->clickAndWait(RegPage::$subs_identifier_mailinglists, 2);
	}

	/**
	 * Method to fill all required BwPostman fields on Joomla registration form
	 *
	 * @param AcceptanceTester	$I
	 * @param boolean			$withMl
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function fillBwPostmanPartAtRegisterFormSimple(AcceptanceTester $I, $withMl = true)
	{
		$com_options    = $I->getManifestOptions('com_bwpostman');

		$this->switchSubscriptionToYes($I);

		if ($com_options->show_gender)
		{
			$I->clickAndWait(RegPage::$subs_identifier_male, 1);
			self::$check_gender = true;
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

		if ($withMl)
		{
			$I->scrollTo(RegPage::$subs_identifier_mailinglists);
			$I->wait(1);
			$I->clickAndWait(RegPage::$subs_identifier_mailinglists, 2);
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

		$I->scrollTo(RegPage::$subs_identifier_mailinglists, 0, -100);
		$I->wait(1);

		$I->clickAndWait(RegPage::$subs_identifier_mailinglists, 2);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function registerAndCheckMessage(AcceptanceTester $I)
	{
		$I->click(RegPage::$login_identifier_register);

		$I->waitForElementVisible(RegPage::$success_heading_identifier, 30);

//		$I->see(Generals::$alert_msg_txt, RegPage::$success_heading_identifier);
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
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				self::checkForSubscriptionSuccess($I);
				self::deleteJoomlaUserByDb($I);
				self::checkForSubscriptionDeletion($I);

				$loginPage->logoutFromBackend($I, false);
			}
		);
		$admin->leave();
	}

	/**
	 * Method to check if subscription was successful
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function checkForSubscriptionSuccess(AcceptanceTester $I)
	{
		if (self::$subscription_selected || (self::$auto_delete !== true))
		{
			$result      = array();

			$result['email']    = self::$current_mail_address;
			if (self::$subscription_only)
			{
				$result['name'] = '';
				$result['firstname'] = '';
			}
			else
			{
				if (self::$visitor != 2)
				{
					$format = 1;
					if (self::$format == "Text")
					{
						$format = 0;
					}

					$result['emailformat'] = $format;
				}

				$com_options = $I->getManifestOptions('com_bwpostman');

				if ($com_options->show_gender && self::$check_gender)
				{
					$gender = 0;
					if (self::$gender_selected == "female")
					{
						$gender = 1;
					}

					$result['gender'] = $gender;
				}

				if ($com_options->show_name_field || $com_options->name_field_obligation)
				{
					$result['name'] = RegPage::$subs_value_name;

					if (self::$visitor == 2)
					{
						$result['name'] = RegPage::$subs_value2_name;
					}
				}

				if ($com_options->show_firstname_field || $com_options->firstname_field_obligation)
				{
					$result['firstname'] = RegPage::$subs_value_firstname;

					if (self::$visitor == 2)
					{
						$result['firstname'] = RegPage::$subs_value2_firstname;
					}
				}

				$result['special'] = '';

				if ($com_options->show_special || $com_options->special_field_obligation)
				{
					$result['special'] = RegPage::$subs_value_special;

					if (self::$visitor == 2)
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
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function filterForSubscriber(AcceptanceTester $I)
	{
		if ($this->name_obligation)
		{
			$search_value   = RegPage::$subs_value_name;
			$search_field   = 'Name';

			if (self::$visitor == 2)
			{
				$search_value   = RegPage::$subs_value2_name;
			}
		}
		else
		{
			$search_value     = RegPage::$login_value_email;
			$search_field   = 'Email';

			if (self::$visitor == 2)
			{
				$search_value   = RegPage::$login_value2_email;
			}
		}

		SubsManagePage::filterForSubscriber($I, $search_value, $search_field);
	}

	/**
	 * Method to delete Joomla user account
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteJoomlaUserByDb(AcceptanceTester $I)
	{

		$user_id = $I->grabFromDatabase(Generals::$db_prefix . 'users', 'id', array('email' => self::$current_mail_address));
		$subs_id = $I->grabFromDatabase(Generals::$db_prefix . 'bwpostman_subscribers', 'id', array('email' => self::$current_mail_address));

		if ($user_id)
		{
			$where_user      = ' WHERE `id` = ' . $user_id;
			$where_usergroup = ' WHERE `user_id` = ' . $user_id;
			$I->deleteRecordFromDatabase('users', $where_user);
			$I->deleteRecordFromDatabase('user_usergroup_map', $where_usergroup);
		}

		if ($subs_id && self::$auto_delete)
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
	 * @throws Exception
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
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->clickAndWait(Generals::$toolbar4['Delete'], 1);

			// process confirmation popup
			$jVersion = $I->getJoomlaMainVersion($I);

			if ($jVersion == 4)
			{
				$I->seeInPopup(RegPage::$delete_confirm);
				$I->acceptPopup();
			}
			else
			{
				$I->see(Generals::$delUserConfirmMessage, Generals::$confirmModalDialog);
				$I->clickAndWait(Generals::$confirmModalYes, 1);
			}

			// see message deleted
			$I->waitForElementVisible(Generals::$alert_success4, 30);
			$I->see(Generals::$alert_success_txt, Generals::$alert_header);
			$I->see(RegPage::$delete_success, Generals::$alert_success4);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function gotoUserManagement(AcceptanceTester $I)
	{
		$I->amOnPage(RegPage::$user_management_url);
		$I->waitForElementVisible(Generals::$pageTitle, 5);
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

		if (self::$visitor == 2)
		{
			$login_value_name   = RegPage::$login_value2_name;
		}

		$I->fillField(Generals::$search_field, $login_value_name);
		$I->clickAndWait(Generals::$search_button, 1);

		try
		{
			$user_found = $I->grabTextFrom(RegPage::$user_edit_identifier);
		}
		catch (\CodeceptionException\ElementNotFound $e)
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
		if (self::$auto_delete)
		{
			$I->dontSeeInDatabase(Generals::$db_prefix . 'bwpostman_subscribers', array('email' => self::$current_mail_address));
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
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				self::checkForSubscriptionSuccess($I);

				self::gotoUserManagement($I);
				$user_found = self::findUser($I);

				if ($user_found)
				{
					self::changeMailAddressOfAccount($I);
					self::$current_mail_address  = $this->subscriber_mail_new;

					self::checkMailChangeOfSubscription($I);

					// delete user
					self::gotoUserManagement($I);
					self::deleteJoomlaUserByDb($I);
					self::checkForSubscriptionDeletion($I);
				}

				$loginPage->logoutFromBackend($I, false);
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
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		// check mail address change of account
		try
		{
			codecept_debug('Check mail change BE with MFA activated');
			$I->see(RegPage::$change_value_email, RegPage::$email_identifier_mfa);
			$I->dontSee(RegPage::$login_value_email, RegPage::$email_identifier_mfa);
		}
		catch (Exception $exception)
		{
			codecept_debug('Check mail change BE with MFA deactivated');
			$I->see(RegPage::$change_value_email, RegPage::$email_identifier);
			$I->dontSee(RegPage::$login_value_email, RegPage::$email_identifier);
		}	}

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
		$search_values['email']     = self::$current_mail_address;

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function activate(\AcceptanceTester $I)
	{
		$activation_code = $I->getJoomlaActivationCode(self::$current_mail_address);
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
	private function activateByBackend(\AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin7');
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				$this->gotoUserManagement($I);
				$user_found = $this->findUser($I);

				if ($user_found)
				{
					$I->seeElement(".//*[@id='userList']/tbody/tr[1]/td[4]/a/span", array('class' => 'icon-unpublish'));
					$I->clickAndWait(".//*[@id='userList']/tbody/tr[1]/td[4]/a", 1);
					$I->seeElement(".//*[@id='userList']/tbody/tr[1]/td[4]/span/span", array('class' => 'icon-publish'));
					$this->activated    = true;

					self::checkForSubscriptionSuccess($I);

					// delete user
					self::gotoUserManagement($I);
					self::deleteJoomlaUserByDb($I);
					self::checkForSubscriptionDeletion($I);
				}

				$loginPage->logoutFromBackend($I, false);
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
	private function deselectNewMailinglist(\AcceptanceTester $I)
	{
		$admin = $I->haveFriend('Admin8');
		$admin->does(
			function (AcceptanceTester $I) {
				$loginPage = new LoginPage($I);
				$loginPage->logIntoBackend(Generals::$admin, $I);

				SubsManagePage::gotoSubscribersListTab($I, $this->activated);
				self::filterForSubscriber($I);

				// look in details for selected mailinglists
				$edit_identifier        = self::getTabDependentIdentifier(RegPage::$subscriber_edit_link);
				$I->clickAndWait($edit_identifier, 1);

				$I->scrollTo(RegPage::$mailinglist_fieldset_identifier, 0, -100);
				$I->wait(1);

				foreach ($this->mls_to_subscribe as $ml)
				{
					$I->seeCheckboxIsChecked($ml);

					$I->uncheckOption($ml);

					$I->dontSeeCheckboxIsChecked($ml);
				}

				$I->scrollTo(Generals::$joomlaHeader, 0, -100);
				$I->wait(1);
				$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

				$loginPage->logoutFromBackend($I, false);
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
			$identifier = sprintf($raw_identifier, "main-table-bw-confirmed");
		}
		else
		{
			$identifier = sprintf($raw_identifier, "main-table-bw-unconfirmed");
		}

		return $identifier;
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function editPluginOptions(AcceptanceTester $I)
	{
		$this->tester = $I;
		$loginPage = new LoginPage($I);
		$loginPage->logIntoBackend(Generals::$admin, $I);

		RegPage::selectPluginPage($I);

		RegPage::filterForPlugin($I, RegPage::$plugin_name);

		$I->clickAndWait(RegPage::$plugin_edit_identifier, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function editComponentOptions(AcceptanceTester $I)
	{
		$this->tester = $I;
		$loginPage = new LoginPage($I);
		$loginPage->logIntoBackend(Generals::$admin, $I);

		$this->selectComponentPage($I);
		$I->waitForElementVisible(Generals::$toolbar4['Options']);

		$I->clickAndWait(Generals::$toolbar4['Options'], 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function selectComponentPage(AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$url);
		$I->waitForElementVisible(Generals::$pageTitle, 5);
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I, false);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
	 */
	private function dontSeePluginInputFields(AcceptanceTester $I)
	{
		$I->dontSee(RegPage::$subs_identifier_subscribe_yes);
		$I->dontSee(RegPage::$subs_identifier_subscribe_no);
		$I->dontSee(RegPage::$subs_identifier_format_html);
		$I->dontSee(RegPage::$subs_identifier_format_text);
		$I->dontSee(RegPage::$subs_identifier_mailinglists);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param int              $format
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function switchPredefinedNewsletterFormat(AcceptanceTester $I, $button, $format)
	{
		$formatText  = "Text";

		if ($format == 1)
		{
			$formatText  = "HTML";
		}

		$I->scrollTo(sprintf($button, 0), 0, -100);
		$I->wait(1);
		$I->clickAndWait(sprintf($button, $format), 1);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);

		$I->waitForElementVisible(Generals::$alert_success4, 30);
		$I->see(Generals::$alert_success_txt, Generals::$alert_header);
		$I->see(RegPage::$config_save_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(RegPage::$bwpm_com_options_regTab, 1);
		$I->scrollTo(sprintf($button, 0), 0, -100);
		$I->wait(1);
		$I->see($formatText, sprintf(RegPage::$mailformat_identifier, $format));
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param int              $format
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private function switchPredefinedNewsletterShow(AcceptanceTester $I, $button, $format)
	{
		$formatShowText  = "No";

		if ($format == 1)
		{
			$formatShowText  = "Yes";
		}

		$I->scrollTo(sprintf($button, 0), 0, -100);
		$I->wait(1);
		$I->clickAndWait(sprintf($button, $format), 1);

		$I->scrollTo(Generals::$joomlaHeader,0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);

		$I->waitForElementVisible(Generals::$alert_success4, 30);
		$I->see(Generals::$alert_success_txt, Generals::$alert_header);
		$I->see(RegPage::$config_save_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(RegPage::$bwpm_com_options_regTab, 1);
		$I->scrollTo(sprintf($button, 0), 0, -100);
		$I->wait(1);
		$I->see($formatShowText, sprintf(RegPage::$format_show_label_identifier, $format));
	}
}
