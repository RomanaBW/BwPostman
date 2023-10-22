<?php
use Page\Generals as Generals;
use Page\InstallationPage as InstallPage;

/**
 * Class InstallVirtuemartCest
 *
 * This class contains the method to install required extensions for testing BwPostman
 *
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   2.3.1
 */
class InstallVirtuemartCest
{
	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $installSuccessMsg1    = "Installation of the";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $installSuccessMsg2    = "was successful.";

	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.3.1
	 */
	public function _login(\Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to install BwPostman
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
	 * @since   2.3.1
	 */
	public function installation(AcceptanceTester $I)
	{
		$I->wantTo("Install additional extensions");
		$I->expectTo("see success messages");
		$I->amOnPage(InstallPage::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingInstall);

		$envInstallFiles = getenv('ADDITIONAL_EXTENSIONS');
		$installFiles = explode(' ', $envInstallFiles);
		codecept_debug('Install files');
		codecept_debug($installFiles);

		foreach ($installFiles as $installFile)
		{
			self::doInstallation($I, $installFile);

			$heading = $I->grabTextFrom(Generals::$alert_success);

			if ($heading == "Warning")
			{
				continue;
			}

			$I->waitForElement(Generals::$alert_success, 30);
			$I->see(self::$installSuccessMsg1, Generals::$alert_success);
			$I->see(self::$installSuccessMsg2, Generals::$alert_success);
			$I->dontSee("Error", Generals::$alert_heading);
			$I->dontSee("Warning", Generals::$alert_heading);
		}

		$this->configureVirtuemart($I);
	}

	/**
	 * @param AcceptanceTester  $I
	 * @param string            $install_file
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.2.0
	 */
	private static function doInstallation(AcceptanceTester $I, $install_file)
	{
		$new_j_installer = true;

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: visible');");
		}

		codecept_debug("Install file " . $install_file);
		$I->attachFile(InstallPage::$installField, $install_file);

		if (!$new_j_installer)
		{
			$I->click(InstallPage::$installButton);
		}

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: none');");
		}

		$I->waitForElementVisible(Generals::$sys_message_container, 120);
		$I->wait(1);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function configureVirtuemart(AcceptanceTester $I)
	{
		$this->addShopMenuItem($I);
		$this->addSampleData($I);
		$this->configureSafePath($I);
		$this->configureRegisterOnCheckout($I);
		$this->configureShopper($I);
		$this->configureVendor($I);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function configureSafePath(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$virtuemart_config_url);
		$I->clickAndWait(InstallPage::$virtuemart_config_template_tab, 1);
		$I->fillField(InstallPage::$virtuemart_config_save_path_field, InstallPage::$virtuemart_config_save_path_value);
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function configureRegisterOnCheckout(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$virtuemart_config_url);
		$I->clickAndWait(InstallPage::$virtuemart_config_checkout_tab, 1);
		$I->scrollTo(InstallPage::$virtuemart_config_checkout_register_option_no, 0, -50);
		$I->wait(1);
		$I->clickAndWait(InstallPage::$virtuemart_config_checkout_register_option_no, 1);
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function configureVendor(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$virtuemart_editshop_url);
		$I->clickAndWait(InstallPage::$virtuemart_shop_vendor_tab, 1);
		$I->fillField(InstallPage::$virtuemart_shop_vendor_field, InstallPage::$virtuemart_shop_vendor_value);
		$I->fillField(InstallPage::$virtuemart_shop_name_field, InstallPage::$virtuemart_shop_name_value);
		$I->fillField(InstallPage::$virtuemart_shop_url_field, InstallPage::$virtuemart_shop_url_value);

		$I->clickSelectList(InstallPage::$virtuemart_shop_currency_field, InstallPage::$virtuemart_shop_currency_value, InstallPage::$virtuemart_shop_currency_id);
//		$I->clickSelectList(InstallPage::$virtuemart_shop_accepted_currency_field, InstallPage::$virtuemart_shop_accepted_currency_value, InstallPage::$virtuemart_shop_accepted_currency_id);
		$I->clickAndWait(InstallPage::$virtuemart_shop_accepted_currency_field, 1);
		$I->waitForElementVisible(InstallPage::$virtuemart_shop_accepted_currency_id);
//		$I->scrollTo(InstallPage::$virtuemart_shop_accepted_currency_value, 0);
//		$I->clickAndWait(InstallPage::$virtuemart_shop_accepted_currency_value, 1);
		$I->seeElement(InstallPage::$virtuemart_shop_accepted_currency_selected);

		$I->clickAndWait(Generals::$toolbar['Save'], 1);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function configureShopper(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$virtuemart_editshop_url);
		$I->clickAndWait(InstallPage::$virtuemart_shop_shopper_tab, 1);
		$I->scrollTo(InstallPage::$virtuemart_shop_shopper_billto, 0, -50);
		$I->wait(1);

		$I->fillField(InstallPage::$virtuemart_shopper_firstname_field, InstallPage::$virtuemart_shopper_firstname_value);
		$I->fillField(InstallPage::$virtuemart_shopper_lastname_field, InstallPage::$virtuemart_shopper_lastname_value);
		$I->fillField(InstallPage::$virtuemart_shopper_address_field, InstallPage::$virtuemart_shopper_address_value);
		$I->fillField(InstallPage::$virtuemart_shopper_zip_field, InstallPage::$virtuemart_shopper_zip_value);
		$I->fillField(InstallPage::$virtuemart_shopper_city_field, InstallPage::$virtuemart_shopper_city_value);

		$I->clickSelectList(InstallPage::$virtuemart_shopper_country_field, InstallPage::$virtuemart_shopper_country_value, InstallPage::$virtuemart_shopper_country_id);
		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function addSampleData(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$virtuemart_config_url);
		$I->clickAndWait(InstallPage::$virtuemart_config_shop_tab, 1);
		$I->scrollTo(InstallPage::$virtuemart_config_advanced, 0, -50);
		$I->wait(1);
		$I->click(InstallPage::$virtuemart_config_enable_db_tools);
		$I->click(Generals::$toolbar['Save']);

		$I->amOnPage(InstallPage::$virtuemart_setup_url);
		$I->clickAndWait(InstallPage::$virtuemart_tools_db_tab, 1);
		$I->scrollTo(InstallPage::$virtuemart_sample_data_button, 0, -50);
		$I->wait(1);
		$I->click(InstallPage::$virtuemart_sample_data_button);
		$I->seeInPopup(InstallPage::$virtuemart_sample_data_popup_text);
		$I->acceptPopup();
		$I->waitForElementVisible(Generals::$alert_info, 120);
	}

	/**
	 * @param AcceptanceTester  $I
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.1
	 */
	private function addShopMenuItem(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$joomla_topmenu_url);
		$I->clickAndWait(Generals::$toolbar['New'], 1);

		$I->fillField(InstallPage::$joomla_topmenu_shop_title_field, InstallPage::$joomla_topmenu_shop_title_value);

		$jVersion = getenv('JOOMLA_VERSION');
		codecept_debug("Joomla version: " . $jVersion);

		$menuSelectIdentifier = InstallPage::$joomla_topmenu_shop_button;

		if ($jVersion === '5')
		{
			$I->clickAndWait(InstallPage::$joomla_topmenu_shop_button_5, 1);
			$I->switchToIFrame(InstallPage::$joomla_topmenu_shop_iframe_name_5);
		}
		else
		{
			$I->clickAndWait(InstallPage::$joomla_topmenu_shop_button, 1);
			$I->switchToIFrame(InstallPage::$joomla_topmenu_shop_iframe_name);
		}


		$I->scrollTo(InstallPage::$joomla_topmenu_shop_iframe_virtuemart, 0, -50);
		$I->wait(1);
		$I->click(InstallPage::$joomla_topmenu_shop_iframe_virtuemart);

		$I->scrollTo(InstallPage::$joomla_topmenu_shop_iframe_virtuemart_category, 0, -50);
		$I->wait(1);
		$I->clickAndWait(InstallPage::$joomla_topmenu_shop_iframe_virtuemart_category, 1);
		$I->switchToIFrame();

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElementVisible(Generals::$alert_success);
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
	 * @since   2.3.1
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
