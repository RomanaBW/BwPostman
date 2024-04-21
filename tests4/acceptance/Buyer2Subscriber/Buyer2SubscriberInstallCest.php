<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\Buyer2SubscriberPage as BuyerPage;
use Page\MaintenancePage as Restore;
use Page\OptionsPage as Options;
use Page\User2SubscriberPage as UserPage;
use Page\InstallationPage as InstallPage;


/**
 * Class Buyer2SubscriberInstallCest
 *
 * This class contains all methods to test installation and modify options of this plugin
 *
 * @package Buyer Subscribe Plugin
 * @subpackage Installation and Plugin Options
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
class Buyer2SubscriberInstallCest
{
	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	public $tester;

	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(LoginPage $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to install plugin without installed component
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
//	public function installWithoutInstalledComponent(AcceptanceTester $I)
//	{
//		$I->wantTo("Install plugin without installed component");
//		$I->expectTo("see error message and no installed plugin Buyer2Subscriber");
//
//		$this->installPlugin($I);
//
//		$I->waitForElement(Generals::$alert_error, 30);
//		$I->see(InstallPage::$installB2SErrorComMsg, Generals::$alert_error);
//		$I->dontSee("Success", Generals::$alert_heading);
//
//		UserPage::selectPluginPage($I);
//
//		UserPage::filterForPlugin($I, BuyerPage::$plugin_name);
//
//		$I->see(BuyerPage::$plugin_not_installed);
//	}

	/**
	 * Test method to install plugin with installed component
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
	public function installWithPrerequisites(AcceptanceTester $I)
	{
		$I->wantTo("Install plugin after installing package");
		$I->expectTo("see success message and installed plugin");

//		InstallPage::installation($I);

		$this->installPlugin($I);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$installB2SSuccessMsg, Generals::$alert_success);
		$I->dontSee("Error", Generals::$alert_heading);

		UserPage::selectPluginPage($I);

		UserPage::filterForPlugin($I, BuyerPage::$plugin_name);

		$I->see(BuyerPage::$plugin_name);

		$this->checkForPluginFieldsAtVM($I);

//		Options::saveDefaults($I);

//		Restore::restoreTables($I);
	}

	/**
	 * Test method to activate plugin an initialize options
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
	public function activateBuyer2SubscriberAndInitializeOptions(AcceptanceTester $I)
	{
		// @ToDo: Joomla stuff, make method shorter. Some parts of this may be useful for options tests
		// @ToDo: Split into activation and initializing options of plugin
		$I->wantTo("activate plugin Buyer2Subscriber");
		$I->expectTo("see success message and green arrow in extensions list");

		UserPage::selectPluginPage($I);

		// Ensure plugin U2S is activated
		$I->setExtensionStatus('bwpm_user2subscriber', 1);

		// Activate plugin B2S
		UserPage::filterForPlugin($I, BuyerPage::$plugin_name);

		$I->see(BuyerPage::$plugin_name);

		$I->checkOption(Generals::$check_all_button);
		$I->click(Generals::$toolbar['Enable']);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$pluginEnableSuccessMsg, Generals::$alert_success);
		$I->seeElement(InstallPage::$icon_published);

		$I->click(".//*[@id='pluginList']/tbody/tr/td[4]/a");
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingPlugins . ": " . BuyerPage::$plugin_name);

		// set mailinglist
		$I->click(".//*[@id='myTabTabs']/li[4]/a");
		$I->waitForElement(".//*[@id='jform_params_ml_available']/div", 30);

		$checked    = $I->grabAttributeFrom(".//*[@id='mb9']", "checked");
		if (!$checked)
		{
			$I->click(".//*[@id='mb9']");
		}

		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$pluginSavedSuccess, Generals::$alert_success);
	}

	/**
	 * Test method to option message
	 *
	 * @param   AcceptanceTester $I
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
	public function Buyer2SubscriberOptionsMessage(AcceptanceTester $I)
	{
		$I->wantTo("change newsletter message and change back");
		$I->expectTo('see changed messages at top of address edit form');

		$this->editPluginOptions($I);
		$I->clickAndWait(BuyerPage::$plugin_tab_options, 1);

		$this->switchPluginMessage($I, UserPage::$plugin_message_new);

		// look at FE
		$user = $I->haveFriend('User1');
		$that = $this;
		$user->does(
			function (AcceptanceTester $I) use ($that)
			{
				$I->amOnPage('/index.php?option=com_virtuemart&view=cart');
				$I->waitForElementVisible("/html/body/div/div/div/main/div[1]/div/p/img");
				$that->gotoAddressEditPage($I);

				$I->see(UserPage::$plugin_message_new, BuyerPage::$message_identifier);
			}
		);
		$user->leave();

		$this->switchPluginMessage($I, UserPage::$plugin_message_old);

		// look at FE
		$user = $I->haveFriend('User2');
		$user->does(
			function (AcceptanceTester $I) use ($that)
			{
				$I->amOnPage('/index.php?option=com_virtuemart&view=cart');
				$I->waitForElementVisible("/html/body/div/div/div/main/div[1]/div/p/img");
				$that->gotoAddressEditPage($I);

				$I->see(UserPage::$plugin_message_old, BuyerPage::$message_identifier);
			}
		);
		$user->leave();

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

//		$login = new LoginPage($I);
//		$login->logoutFromBackend($I);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $message
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function switchPluginMessage(AcceptanceTester $I, $message)
	{
		$I->fillField(BuyerPage::$plugin_message_identifier, $message);
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->see($message, BuyerPage::$plugin_message_identifier);
	}

	/**
	 * Test method to option mailinglists
	 *
	 * @param   AcceptanceTester $I
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
	public function Buyer2SubscriberOptionsMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("add additional mailinglist to options");
		$I->expectTo('see further selected mailinglist at plugin options form');

		$this->editPluginOptions($I);
		$I->clickAndWait(BuyerPage::$plugin_tab_mailinglists, 1);

		// click checkbox for further mailinglist
		$I->checkOption(sprintf(UserPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->seeCheckboxIsChecked(sprintf(UserPage::$plugin_checkbox_mailinglist, 9));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_buyer2subscriber');
		$I->assertEquals("1", $options->ml_available[0]);
		$I->assertEquals("24", $options->ml_available[1]);

		// deselect further mailinglist
		$I->uncheckOption(sprintf(UserPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->dontSeeCheckboxIsChecked(sprintf(UserPage::$plugin_checkbox_mailinglist, 5));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_buyer2subscriber');
		$I->assertEquals("24", $options->ml_available[0]);

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

//		$login = new LoginPage($I);
//		$login->logoutFromBackend($I);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkForPluginFieldsAtVM(AcceptanceTester $I)
	{
		$this->gotoVMUserfieldsPage($I);

		$I->fillField(BuyerPage::$filter_field, BuyerPage::$filter_search_value);
		$I->click(BuyerPage::$filter_go_button);

		$I->see(BuyerPage::$shopper_field_message, sprintf(BuyerPage::$shopper_field_title, 1));
		$I->see(BuyerPage::$shopper_field_subscription, sprintf(BuyerPage::$shopper_field_title, 2));
		$I->see(BuyerPage::$shopper_field_format, sprintf(BuyerPage::$shopper_field_title, 3));
		$I->see(BuyerPage::$shopper_field_gender, sprintf(BuyerPage::$shopper_field_title, 4));
		$I->see(BuyerPage::$shopper_field_special, sprintf(BuyerPage::$shopper_field_title, 5));

		$I->canSeeNumberOfElements(BuyerPage::$shopper_field_published, 5);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function gotoVMUserfieldsPage(AcceptanceTester $I)
	{
		$I->amOnPage(BuyerPage::$link_to_shopper_fields);
		$I->waitForElement(BuyerPage::$userfield_page_identifier, 30);
		$I->see(BuyerPage::$userfield_page_header_text);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function gotoAddressEditPage(AcceptanceTester $I)
	{
		$I->click(BuyerPage::$button_enter_address);
		$I->scrollTo('//*[@id="userForm"]');
		$I->wait(1);
		$I->waitForElement(BuyerPage::$header_account_details);
		$I->see(BuyerPage::$header_account_details_text);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForPluginFieldsNotVisible($I)
	{
		$I->dontSeeElement(BuyerPage::$message_identifier);
		$I->dontSeeElement(BuyerPage::$subscription_identifier);
		$I->dontSeeElement(BuyerPage::$format_identifier);
		$I->dontSeeElement(BuyerPage::$additional_identifier);
		$I->dontSeeElement(BuyerPage::$gender_identifier);
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
//		$login = new LoginPage($I);
//		$login->logIntoBackend(Generals::$admin);

		$this->selectPluginPage($I);

		$this->filterForPlugin($I);

		$I->clickAndWait(UserPage::$plugin_edit_identifier, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function selectPluginPage(AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$plugin_page);
		$I->wait(1);
		$I->see(Generals::$view_plugin, Generals::$pageTitle);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function filterForPlugin(AcceptanceTester $I)
	{
		$I->fillField(Generals::$search_field, BuyerPage::$plugin_name);
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function installPlugin(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingInstall);

		$new_j_installer = true;

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: visible');");
		}

		$I->attachFile(InstallPage::$installField, InstallPage::$installFileB2S);

		if (!$new_j_installer)
		{
			$I->click(InstallPage::$installButton);
		}

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: none');");
		}

		$I->waitForElement(Generals::$sys_message_container, 30);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   LoginPage             $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, LoginPage $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
