<?php
use Page\Generals as Generals;
use Page\InstallationPage as InstallPage;
use Page\Login;

/**
 * Class TestInstallationCest
 *
 * This class contains the method to test installation of BwPostman
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
class TestInstallationCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(Login $loginPage)
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
	 * @since   2.0.0
	 */
	public function installation(AcceptanceTester $I)
	{
		InstallPage::installation($I);
	}

	/**
	 * Test method to check the enabled state of the parts of BwPostman after installation ordered by of their installation order
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function checkExtensionsEnabledState(AcceptanceTester $I)
	{
		//Plugin  BwLibregister has to be enabled
		$state = $I->getExtensionEnabledState('bw_libregister');
		$I->assertEquals(true, $state, 'Plugin BwLibregister enabled');

		// Component BwPostman has to be enabled
		$state = $I->getExtensionEnabledState('com_bwpostman');
		$I->assertEquals(true, $state, 'Component BwPostman enabled');

		// Module Register has to be enabled
		$state = $I->getExtensionEnabledState('mod_bwpostman');
		$I->assertEquals(true, $state, 'Module Register enabled');

		// Module Overview has to be enabled
		$state = $I->getExtensionEnabledState('mod_bwpostman_overview');
		$I->assertEquals(true, $state, 'Module Overview enabled');

		// Plugin Personalize has to be enabled
		$state = $I->getExtensionEnabledState('personalize');
		$I->assertEquals(true, $state, 'Plugin Personalize enabled');

		// Plugin U2S has to be disabled
		$state = $I->getExtensionEnabledState('bwpm_user2subscriber');
		$I->assertEquals(false, $state, 'Plugin U2S disabled');

		// Plugin FUM has to be enabled
		$state = $I->getExtensionEnabledState('footerusedmailinglists');
		$I->assertEquals(true, $state, 'Plugin FUM enabled');

		// Package BwPostman has to be enabled
		$state = $I->getExtensionEnabledState('pkg_bwpostman');
		$I->assertEquals(true, $state, 'Package BwPostman enabled');
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   Login             $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
