<?php
use Step\Acceptance\Installation as InstallationTester;
use Page\Generals as Generals;
use Page\InstallationPage as InstallPage;

/**
* Class TestDeinstallationCest
*
* This class contains all methods to test installation, update and deinstallation of BwPostman
 *
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
class TestDeinstallationCest
{
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
	 * Test method to uninstall BwPostman
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
	public function uninstall(AcceptanceTester $I)
	{
		$I->wantTo("uninstall BwPostman");
		$I->expectTo("see success message and component not in menu");
		$I->amOnPage(InstallPage::$extension_manage_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingManage);

		$I->fillField(Generals::$search_field, Generals::$extension);
		$I->click(Generals::$search_button);

		$to_uninstall   = $I->elementExists($I, ".//*[@id='manageList']");

		if ($to_uninstall)
		{
			$I->checkOption(Generals::$check_all_button);
			$I->click(InstallPage::$delete_button);
			$I->acceptPopup();

			$I->waitForElement(Generals::$sys_message_container, 180);
			$I->waitForElement(Generals::$alert_success, 30);
			$I->see(InstallPage::$uninstallSuccessMsg, Generals::$alert_success);

			// @ToDo: reset auto increment at usergroups
			$I->resetAutoIncrement('usergroups', 14);
		}
		else
		{
			$I->see(InstallPage::$search_no_match);
		}
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
}

