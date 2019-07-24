<?php
use Page\Generals as Generals;
use Page\InstallationPage as InstallPage;

/**
 * Class InstallRequiredCest
 *
 * This class contains the method to install required extensions for testing BwPostman
 *
 * @copyright (C) 2019 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   2.3.0
 */
class InstallRequiredCest
{
	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $installSuccessMsg1    = "Installation of the";

	/**
	 * @var string
	 *
	 * @since 2.3.0
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
	 * @since   2.3.0
	 */
	public function _login(\Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to install required extensions
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
	 * @since   2.3.0
	 */
	public function installation(AcceptanceTester $I)
	{
		$I->wantTo("Install additional extensions");
		$I->expectTo("see success messages");
		$I->amOnPage(InstallPage::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingInstall);
		$I->waitForText(Generals::$packageInstallerText);

		$envInstallFiles = getenv('ADDITIONAL_EXTENSIONS');
		$installFiles = explode(' ', $envInstallFiles);

		foreach ($installFiles as $installFile)
		{
			self::doInstallation($I, $installFile);

			$heading = $I->grabTextFrom('#system-message-container div h4');

			if ($heading == "Warning")
			{
				continue;
			}

			$I->waitForElementVisible(Generals::$alert_success, 30);
			$I->see(self::$installSuccessMsg1, Generals::$alert_success);
			$I->see(self::$installSuccessMsg2, Generals::$alert_success);
			$I->dontSee("Error", Generals::$alert_heading);
			$I->dontSee("Warning", Generals::$alert_heading);
			$I->waitForElementVisible(Generals::$systemMessageClose, 30);
			$I->click(Generals::$systemMessageClose);
		}
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

		$I->attachFile(InstallPage::$installField, $install_file);

		if (!$new_j_installer)
		{
			$I->click(InstallPage::$installButton);
		}

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: none');");
		}

		$I->waitForElement(Generals::$sys_message_container, 120);

		return;
	}

	/**
	 * Test method to add sample data
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
	 * @since   2.3.0
	 */
	public function addSampleData(AcceptanceTester $I)
	{
		$I->wantTo("add sample data");
		$I->expectTo("see success messages");

		$I->waitForText(InstallPage::$sampleDataText, 30);
		$I->click(InstallPage::$sampleDataInstallButton);
		$I->acceptPopup();
		$I->waitForElementVisible(InstallPage::$sampleDataSuccessStep4, 120);
		$I->waitForText(InstallPage::$sampleDataSuccessText4, 30);
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
	 * @since   2.3.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
