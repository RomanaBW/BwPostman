<?php
use Page\Generals as Generals;
use Page\MaintenancePage as MaintenancePage;
use Page\MainviewPage as MainView;

/**
 * Class TestMaintenanceCest
 *
 * This class contains all methods to test maintenance functionality at back end
 *
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
class TestMaintenanceCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(\Page\Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to save tables zipped
	 *
	 * @param   AcceptanceTester                $I
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
	public function saveTablesZip(AcceptanceTester $I)
	{
		$I->wantTo("Save tables zipped");
		$I->expectTo("see zip file in download directory");

		$I->setManifestOption('com_bwpostman', 'compress_backup', '1');

		$I->amOnPage(MainView::$url);
		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading);

		$versionToTest = $this->getVersionNumber($I);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$path     = Generals::$downloadFolder[$user];
		$filename = 'BwPostman_' . str_replace('.', '_', $versionToTest) . '_Tables_' . date("Y-m-d_H_i") . '.xml.zip';
		$downloadPath = $path . $filename;
		codecept_debug("Download path: " . $downloadPath);

		$I->clickAndWait(MaintenancePage::$saveTablesButton, 15);

		$I->assertTrue(file_exists($downloadPath));

		$withdraw = getenv('BW_TEST_WITHDRAW');
		codecept_debug ('ENV withdraw: ' . $withdraw);

		if ($withdraw == false)
		{
			codecept_debug ('Unlink downloaded file');
			unlink($downloadPath);
		}
	}

	/**
	 * Test method to save tables unzipped
	 *
	 * @param   AcceptanceTester                $I
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
	public function saveTablesNoZip(AcceptanceTester $I)
	{
		$I->wantTo("Save tables unzipped");
		$I->expectTo("see xml file in download directory");

		$I->setManifestOption('com_bwpostman', 'compress_backup', '0');

		$I->amOnPage(MainView::$url);
		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading);

		$versionToTest = $this->getVersionNumber($I);

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'user2';
		}

		$path     = Generals::$downloadFolder[$user];
		$filename = 'BwPostman_' . str_replace('.', '_', $versionToTest) . '_Tables_' . date("Y-m-d_H_i") . '.xml';
		$downloadPath = $path . $filename;

		$I->clickAndWait(MaintenancePage::$saveTablesButton, 10);

		$I->assertTrue(file_exists($downloadPath));

		$withdraw = getenv('BW_TEST_WITHDRAW');
		codecept_debug ('ENV withdraw: ' . $withdraw);

		if ($withdraw == false)
		{
			codecept_debug ('Unlink downloaded file');
			unlink($downloadPath);
		}

		$I->setManifestOption('com_bwpostman', 'compress_backup', '1');
	}

	/**
	 * Test method to check tables
	 *
	 * @param   AcceptanceTester                $I
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
	public function checkTables(AcceptanceTester $I)
	{
		$I->wantTo("Check tables");
		$I->expectTo("see 'Result check okay'");
		$I->amOnPage(MainView::$url);
		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading);

		$I->click(MaintenancePage::$checkTablesButton);
		$I->waitForElement(MaintenancePage::$step1Field, 30);
		$I->waitForElement(MaintenancePage::$step2Field, 30);
		$I->waitForElement(MaintenancePage::$step3Field, 30);
		$I->wait(10);
		$I->waitForElement(MaintenancePage::$step4Field, 30);
		$I->waitForElement(MaintenancePage::$step5Field, 30);
		$I->waitForElement(MaintenancePage::$step6Field, 30);
		$I->waitForElement(MaintenancePage::$step7Field, 30);
		$I->waitForElement(MaintenancePage::$step7SuccessClass, 30);
		$I->see(MaintenancePage::$step7SuccessMsg, MaintenancePage::$successIdentifierResult);

		$resultsWarn = $I->grabMultiple(MaintenancePage::$warningIdentifier);
		$I->assertEquals(count($resultsWarn), 0);
		$I->see(MaintenancePage::$successTextAllResult, MaintenancePage::$successAllIdentifierResult);

		$I->clickAndWait(MaintenancePage::$checkBackButton, 1);
	}

	/**
	 * Test method to restore tables from unzipped file with errors to see if they are handled correctly
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   3.1.3
	 */
	public function restoreTablesWithErrors(AcceptanceTester $I)
	{
		MaintenancePage::restoreTables($I, false, 'BwPostman_4_x_x_Tables_error_default.xml');
		MaintenancePage::restoreTables($I, false, 'BwPostman_4_x_x_Tables_error_primary.xml');
	}

	/**
	 * Test method to restore tables from unzipped file with modifications to see if they are handled correctly, simple modifications
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   3.1.3
	 */
	public function restoreTablesWithModsSimple(AcceptanceTester $I)
	{
		MaintenancePage::restoreTables($I, false, 'BwPostman_4_x_x_Tables_modified_simple.xml');
	}

	/**
	 * Test method to restore tables from unzipped file with modifications to see if they are handled correctly, higher version as installed
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   3.1.3
	 */
	public function restoreTablesWithModsVersion(AcceptanceTester $I)
	{
		MaintenancePage::restoreTables($I, false, 'BwPostman_4_x_x_Tables_modified_version.xml');
	}

	/**
	 * Test method to restore tables from unzipped file
	 *
	 * @param   AcceptanceTester                $I
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
	public function restoreTablesNoZip(AcceptanceTester $I)
	{
		MaintenancePage::restoreTables($I);
	}

	/**
	 * Test method to restore tables from unzipped file
	 *
	 * @param   AcceptanceTester                $I
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
	public function restoreTablesZip(AcceptanceTester $I)
	{
		MaintenancePage::restoreTables($I, true);
	}

	/**
	 * Test method to check button basic settings
	 *
	 * @param   AcceptanceTester                $I
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
	public function testBasicSettings(AcceptanceTester $I)
	{
		$I->wantTo("test basic settings button");
		$I->expectTo("see configuration page");
		$I->amOnPage(MainView::$url);
		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading);

		$I->clickAndWait(MaintenancePage::$settingsButton, 2);
		$I->see(MaintenancePage::$headingSettings);

		$I->click(MaintenancePage::$cancelSettingsButton);
	}

	/**
	 * Test method to check forum link
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function testForumLink(AcceptanceTester $I)
	{
		if (getenv('BW_TEST_BWPM_VERSION') != '132')
		{
			$I->wantTo("test forum link");
			$I->expectTo("see new page with forum of BwPostman");
			$I->amOnPage(MainView::$url);
			$I->click(MainView::$maintenanceButton);

			$I->waitForElement(Generals::$pageTitle, 30);
			$I->see(MaintenancePage::$heading);

			$I->click(MaintenancePage::$forumButton);
			$I->switchToNextTab();
			$I->wait(2);
			$I->see("In this category you can ask your questions for the Joomla! extension BwPostman.");
		}
	}

	/**
	 * Method to get version of BwPostman from footer
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  string
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function getVersionNumber(AcceptanceTester $I)
	{
		$footerText = $I-> grabTextFrom('.bwpm_copyright');

		$footerTextArray = explode(' ', $footerText);

		return $footerTextArray[2];
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
