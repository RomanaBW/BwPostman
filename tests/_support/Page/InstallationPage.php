<?php
namespace Page;

/**
 * Class MaintenancePage
 *
 * @package Page
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
class InstallationPage
{
    // include url of current page
    public static $install_url          = "/administrator/index.php?option=com_installer";
    public static $extension_manage_url = "/administrator/index.php?option=com_installer&view=manage";
	public static $plugin_manage_url    = "/administrator/index.php?option=com_plugins&view=plugins";

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $installField      = ".//*[@id='install_package']";
	public static $installButton     = ".//*[@id='installbutton_package']";
	public static $installButton37   = ".//*[@id='select-file-button']";

	public static $installFileComponent = "pkg_bwpostman.zip";
	public static $installFileU2S       = "plg_bwpostman_bwpm_user2subscriber.zip";
	public static $installFileB2S       = "plg_bwpostman_bwpm_buyer2subscriber.zip";

	public static $installFileComponent_132 = "com_bwpostman.1.3.2.zip";

	public static $headingInstall       = "Extensions: Install";
	public static $headingManage        = "Extensions: Manage";
	public static $headingPlugins       = "Plugins";

	public static $pluginSavedSuccess   = "Plugin saved.";

	public static $delete_button        = ".//*[@id='toolbar-delete']/button";

	public static $search_no_match      = "There are no extensions installed matching your query.";

	public static $installSuccessMsg    = "Installation of the package was successful.";
	public static $uninstallSuccessMsg  = "Thank you for using BwPostman. BwPostman is now removed from your system.";

	public static $installU2SSuccessMsg    = "Installation of the plugin was successful.";

	public static $installB2SSuccessMsg    = "Installation of the plugin was successful.";
	public static $installB2SErrorComMsg   = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman ";
	public static $installB2SErrorU2SMsg   = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman Plugin User2Subscriber!";
	public static $installB2SErrorVmMsg    = "BwPostman Plugin Buyer2Subscriber requires an installed BwPostman Plugin User2Subscriber!";

	public static $optionsSuccessMsg    = "Configuration successfully saved.";

	public static $enableSuccessMsg       = "1 extension successfully enabled.";
	public static $pluginEnableSuccessMsg = "Plugin successfully enabled.";

	public static $icon_published       = ".//*[@id='pluginList']/tbody/tr/td[3]/a/span[contains(@class, 'icon-publish')]";

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
	 * @since   2.0.0
	 */
	public static function installation(\AcceptanceTester $I)
	{
		$I->wantTo("Install BwPostman");
		$I->expectTo("see success message and component in menu");
		$I->amOnPage(self::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(self::$headingInstall);

		$install_file   = self::$installFileComponent;

		if (getenv('BW_TEST_BWPM_VERSION') == 132)
		{
			$install_file   = self::$installFileComponent_132;
		}
codecept_debug('JS-String:' . (string)"jQuery('input[type=file]#install_package').val('$install_file');");
		if ((int)getenv('BW_TEST_JOOMLA_VERSION') < 370)
		{
			// until Joomla 3.6.5
			$I->attachFile(self::$installField, $install_file);
			$I->click(self::$installButton);
		}
		else
		{
			// Since Joomla 3.7.0
			// @ToDo: Use ULR oder folder upload
//			$I->executeJS('jQuery("#legacy-uploader").css("display", "visible !important");');
			$I->executeJS("jQuery('input[type=file]#install_package').val('$install_file');");
//			$I->fillField(".//*[@id='install_package']", $install_file);
//			$I->executeJS('jQuery("#legacy-uploader").css("display", "none");');

		}

		$I->waitForElement(Generals::$sys_message_container, 120);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(self::$installSuccessMsg, Generals::$alert_success);
		$I->dontSee("Error", Generals::$alert_heading);
	}

}
