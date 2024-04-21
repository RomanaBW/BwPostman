<?php
namespace Page;

/**
 * Class MaintenancePage
 *
 * @package Page
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
class InstallationPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $fe_url          = "/index.php";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $install_url          = "/administrator/index.php?option=com_installer";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $extension_manage_url = "/administrator/index.php?option=com_installer&view=manage";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_manage_url    = "/administrator/index.php?option=com_plugins&view=plugins";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_url    = "/administrator/index.php?option=com_virtuemart&view=config";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_editshop_url    = "/administrator/index.php?option=com_virtuemart&view=user&task=editshop";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_advanced    = "//*/form[@id='adminForm']/div/div/ul[2]/li[1]/div/div/div/div/div[1]/div[3]/div/div[1]/div";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_enable_db_tools    = "//*[@id='dangeroustools']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_setup_url    = "/administrator/index.php?option=com_virtuemart&view=updatesmigration";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_tools_db_tab    = "//*/div[@id='vmuikit-admin-ui-tabs']/div/ul[1]/li[1]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_sample_data_button    = "//*/a[contains(text(), 'fresh install with sample data')]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_sample_data_popup_text    = "This deletes all tables of VirtueMart and makes a demo install (no files). Are you sure?";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_shop_tab    = "//*/form[@id='adminForm']/div/div/ul[1]/li[1]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_template_tab    = "//*/form[@id='adminForm']/div/div/ul[1]/li[4]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_checkout_tab    = "//*/form[@id='adminForm']/div/div/ul[1]/li[6]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_save_path_field    = '//*[@id="forSale_path"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_save_path_value    = '/var/www/vmfiles/';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_checkout_register_field    = "//*[@id='oncheckout_show_register']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_config_checkout_register_option_no    = "//*[@id='oncheckout_show_register1']";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_vendor_tab    = "//*/form[@id='adminForm']/div/div/ul[1]/li[1]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_vendor_field    = '//*[@id="vendor_name"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_vendor_value    = 'BwPostman AutoTests';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_name_field    = '//*[@id="vendor_store_name"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_name_value    = 'BwPostman AutoTests';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_url_field    = '//*[@id="vendor_url"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_url_value    = 'http://172.17.0.3';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_currency_id    = 'vendor_currency_chosen';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_currency_field    = "//*[@id='vendor_currency_chosen']/a";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_currency_value    = '//*[@id="vendor_currency_chosen"]/div/ul/li[text()="Euro"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_accepted_currency_id    = "//*[@id='vendor_accepted_currencies_chosen']/div/ul/li";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_accepted_currency_field    = "//*[@id='vendor_accepted_currencies_chosen']/ul/li/input";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_accepted_currency_value    = "//*[@id='vendor_accepted_currencies_chosen']/div/ul/li[text()='Euro']";

	/**
	 * @var string
	 *
	 * @since 2.3.2
	 */
	public static $virtuemart_shop_accepted_currency_selected    = "//*[@id='vendor_accepted_currencies_chosen']/ul/li/span[text()='Euro']";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_shopper_tab    = "//*/form[@id='adminForm']/div/div/ul[1]/li[3]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shop_shopper_billto    = "//*/h3[contains(text(), 'Bill To Information')]";


	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_firstname_field    = '//*[@id="first_name_field"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_firstname_value    = 'Admin';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_lastname_field    = '//*[@id="last_name_field"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_lastname_value    = 'Tester';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_address_field    = '//*[@id="address_1_field"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_address_value    = 'Nichtswieweg 1';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_zip_field    = '//*[@id="zip_field"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_zip_value    = '0815';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_city_field    = '//*[@id="city_field"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_city_value    = 'Irgendwo';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_country_id    = 'virtuemart_country_id_field_chosen';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_country_field    = '//*[@id="virtuemart_country_id_field_chosen"]/a';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $virtuemart_shopper_country_value    = '//*[@id="virtuemart_country_id_field_chosen"]/div/ul/li[text()="Germany"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_url    = '/administrator/index.php?option=com_menus&view=items&menutype=0main-menu-blog';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_title_field    = '//*[@id="jform_title"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_title_value    = 'Shop';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_item_field    = '//*[@id="jform_type"]';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_button    = "//*[@id='jform_type']/parent::span/button";

	/**
	 * @var string
	 *
	 * @since 4.3.5
	 */
	public static $joomla_topmenu_shop_button_5    = "//*[@id='jform_type']/parent::div/button";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_iframe_name    = 'Menu Item Type';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_iframe_name_5    = '.iframe-content';

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_iframe_virtuemart    = "//*[@id='collapseTypes']/div/h2/button[contains(text(), 'VirtueMart')]";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $joomla_topmenu_shop_iframe_virtuemart_category    = "//*[@id='collapseTypes']/div/div/div/div/a/div[contains(text(), 'Category Layout')]";


	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installField      = "//*[@id='install_package']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installButton     = "//*[@id='installbutton_package']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installedText   = "//*[@id='com_bwp_install_outer']/div/h1";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installFileComponent = "pkg_bwpostman.zip";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installFileU2S       = "plg_bwpostman_bwpm_user2subscriber.zip";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installFileB2S       = "plg_bwpm_buyer2subscriber.zip";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $headingInstall       = "Extensions: Install";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $headingManage        = "Extensions: Manage";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $headingPlugins       = "Plugins";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $pluginSavedSuccess   = "Plugin saved.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $delete_button        = "//*[@id='toolbar-delete']/button";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_no_match      = "There are no extensions installed matching your query.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installSuccessMsg    = "Installation of the package was successful.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installSuccessMsg2    = "Welcome to BwPostman,";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $uninstallSuccessMsg  = "Thank you for using BwPostman. BwPostman is now removed from your system.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installU2SSuccessMsg    = "Installation of the plugin was successful.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installB2SSuccessMsg    = "Installation of the plugin was successful.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installPluginSuccessMsg    = "Installation of the plugin was successful.";

	/**
	 * @var string
	 *
	 * @since 2.3.1
	 */
	public static $installPackageSuccessMsg    = "Installation of the package was successful.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installB2SErrorComMsg   = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman ";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installB2SErrorU2SMsg   = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman Plugin User2Subscriber!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $installB2SErrorVmMsg    = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman Plugin User2Subscriber!";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $optionsSuccessMsg    = "Configuration successfully saved.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $enableSuccessMsg       = "1 extension successfully enabled.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $pluginEnableSuccessMsg = "Plugin enabled.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $icon_published       = "//*[@id='pluginList']/tbody/tr/td[3]/a/span[contains(@class, 'icon-publish')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $sampleDataText       = "Blog Sample Data";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $sampleDataInstallButton       = '//*/button[@data-type="blog"]';

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $sampleDataSuccessStep4       = "//*[@id='system-message-container']/joomla-alert[@type='success']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $sampleDataSuccessText4       = 'Sample data installed.';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $siteModulesUrl  = '/administrator/index.php?option=com_modules&view=modules&client_id=0';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $registrationModuleLine    = "//*[@id='moduleList']/tbody/tr/th/div/a";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $overviewModuleLine    = "//*[@id='moduleList']/tbody/tr/th/div/a";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $positionField    = "//*[@id='general']/div/div[2]/fieldset/div[2]/div[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $positionValue    = "//*[@id='general']/div/div[2]/fieldset/div[2]/div[2]/joomla-field-fancy-select/div/div[2]/div/div[contains(text(), 'Sidebar-right')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $publishedField    = "#jform_published";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $registrationTabs    = array(
		'Module' => "//*/button[@aria-controls='general'][@role='tab']",
		'Menu Assignment' => "//*/button[@aria-controls='assignment'][@role='tab']",
		'Mailing list selection' => "//*/button[@aria-controls='attrib-ml_available'][@role='tab']",
		'Settings for the registration form' => "//*/button[@aria-controls='attrib-reg_settings'][@role='tab']",
		'Advanced' => "//*/button[@aria-controls='attrib-advanced'][@role='tab']",
		'Permissions' => "//*/button[@aria-controls='permissions'][@role='tab']",
	);

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $overviewTabs    = array(
		'Module' => "//*/button[@aria-controls='general'][@role='tab']",
		'Menu Assignment' => "//*/button[@aria-controls='assignment'][@role='tab']",
		'Mailing list selection' => "//*/button[@aria-controls='attrib-ml_available'][@role='tab']",
		'Campaign selection' => "//*/button[@aria-controls='attrib-cam_available'][@role='tab']",
		'Advanced' => "//*/button[@aria-controls='attrib-advanced'][@role='tab']",
		'Permissions' => "//*/button[@aria-controls='permissions'][@role='tab']",
	);

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $menuAssignmentList    = "#jform_assignment";

	/**
	 * @var string
	 *
	 * @since 3.0.2
	 */
	public static $menuAssignmentValue    = "//*[@id='jform_assignment_chosen']/div/ul/li[1]";

	/**
	 * @var string
	 *
	 * @since 3.0.2
	 */
	public static $mlFieldset    = "//*[@id='jform_params_mod_ml_available']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mlSelectRow    = "//*[@id='cb%s']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $RegModPositionFE    = "//*/h3[contains(text(), 'BwPostman Module')]";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $search_button        = "//*[@id='j-main-container']/div[1]/div[2]/div/div/div/button";



	/**
	 * Test method to install BwPostman
	 *
	 * @param   \AcceptanceTester                $I
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
	public static function installation(\AcceptanceTester $I)
	{
		$I->wantTo("Install BwPostman");
		$I->expectTo("see success message and component in menu");
		$I->amOnPage(self::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(self::$headingInstall);

		self::doInstallation($I);

		$heading = $I->grabTextFrom(Generals::$alert_heading4);

		if ($heading == "Warning")
		{
			// @ToDo: Insert workaround for too fast container installation
			self::unInstallation($I);

			$I->amOnPage(self::$install_url);
			$I->waitForElement(Generals::$pageTitle, 30);
			$I->see(self::$headingInstall);

			self::doInstallation($I);
		}

		$I->waitForElement(Generals::$alert_success4, 30);
		$I->see(self::$installSuccessMsg, Generals::$alert_success4);
		$I->see(self::$installSuccessMsg2, self::$installedText);
		$I->dontSee("Error", Generals::$alert_heading4);
		$I->dontSee("Warning", Generals::$alert_heading4);
	}

	/**
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.2.0
	 */
	private static function doInstallation(\AcceptanceTester $I)
	{
		$install_file    = self::$installFileComponent;
		$new_j_installer = true;

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').className = 'visible';");
		}

		$I->attachFile(self::$installField, $install_file);

		if (!$new_j_installer)
		{
			$I->click(self::$installButton);
		}

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').className = 'hidden';");
		}

		$I->waitForElement(Generals::$sys_message_container, 150);

		return;
	}

	/**
	 * Test method to uninstall BwPostman
	 *
	 * @param   \AcceptanceTester                $I
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
	public static function unInstallation(\AcceptanceTester $I)
	{
		$I->wantTo("uninstall BwPostman");
		$I->expectTo("see success message and component not in menu");

		$I->amOnPage(self::$extension_manage_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(self::$headingManage);

		$I->fillField(Generals::$search_field, Generals::$extension);
		$I->click(Generals::$search_button);

		$to_uninstall = $I->elementExists($I, "//*[@id='manageList']");

		if ($to_uninstall)
		{
			$I->checkOption(Generals::$check_all_button);
			$I->click(self::$delete_button);
			$I->acceptPopup();

			$I->waitForElement(Generals::$sys_message_container, 180);
			$I->waitForElement(Generals::$alert_success, 30);
			$I->see(self::$uninstallSuccessMsg, Generals::$alert_success);

			// @ToDo: reset auto increment at usergroups
			$I->resetAutoIncrement('usergroups', 14);
		}
		else
		{
			$I->see(self::$search_no_match);
		}
	}
}
