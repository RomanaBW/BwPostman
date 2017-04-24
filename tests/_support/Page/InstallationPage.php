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

	public static $installFileComponent = "pkg_bwpostman.zip";
	public static $installFileU2S       = "plg_bwpostman_bwpm_user2subscriber.zip";
	public static $installFileB2S       = "plg_bwpostman_bwpm_buyer2subscriber.zip";

	public static $headingInstall       = "Extensions: Install";
	public static $headingManage        = "Extensions: Manage";
	public static $headingPlugins       = "Plugins";

	public static $pluginSavedSuccess   = "Plugin successfully saved.";

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

		$I->attachFile(self::$installField, self::$installFileComponent);
		$I->click(self::$installButton);
		$I->waitForElement(Generals::$sys_message_container, 120);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(self::$installSuccessMsg, Generals::$alert_success);
		$I->dontSee("Error", Generals::$alert_heading);
	}

}
