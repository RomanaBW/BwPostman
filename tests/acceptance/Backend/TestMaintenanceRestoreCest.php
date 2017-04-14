<?php
use Page\Generals as Generals;
use Page\MaintenancePage as MaintenancePage;
use Page\MainviewPage as MainView;

/**
* Class TestMaintenanceRestoreCest
*
* This class contains all methods to test maintenance functionality at back end
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
class TestMaintenanceRestoreCest
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
	 * Test method to restore tables
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
	public function restoreTables(AcceptanceTester $I)
	{
		$I->wantTo("Restore tables");
		$I->expectTo("see 'Result check okay'");
		$I->amOnPage(MainView::$url);
		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading);

		$I->click(MaintenancePage::$restoreTablesButton);
		$I->waitForElement(MaintenancePage::$headingRestoreFile, 30);

		$I->attachFile(".//*[@id='restorefile']", "BwPostman_2_0_0_Tables.xml");
		$I->click(".//*[@id='adminForm']/fieldset/div[2]/div/table/tbody/tr[2]/td/input");
		$I->dontSeeElement(Generals::$alert_error);

		$I->waitForElementVisible(MaintenancePage::$step1Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step2Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step3Field, 30);
//		$I->wait(20);
		$I->waitForElementVisible(MaintenancePage::$step4Field, 90);
		$I->waitForElementVisible(MaintenancePage::$step5Field, 30);
//		$I->wait(20);
		$I->waitForElementVisible(MaintenancePage::$step6Field, 120);
		$I->waitForElementVisible(MaintenancePage::$step7Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step8Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step9Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step10Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step11Field, 30);
		$I->waitForElementVisible(MaintenancePage::$step11SuccessClass, 30);
		$I->see(MaintenancePage::$step11SuccessMsg, MaintenancePage::$step11SuccessClass);
		$I->click(MaintenancePage::$checkBackButton);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(MaintenancePage::$heading, Generals::$pageTitle);
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

