<?php
use Page\Generals as Generals;
use Helper\Acceptance as Helper;

/**
 * Class TestPlaygroundCest
 *
 * This class is for playing around with various possibilities
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
class TestPlaygroundCest
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
	 * Test method to get component options
	 *
	 * before  _login
	 *
	 * after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function getOptions()
	{
		codecept_debug('Result in Playground');
		codecept_debug(Generals::$com_options->default_from_name);
	}

	/**
	 * Test method to get component options
	 *
	 * @param   AcceptanceTester        $I
	 *
	 * before  _login
	 *
	 * after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function setOption(AcceptanceTester $I)
	{
		// @ToDo: ATTENTION: This method may cause damage to whole column in table at database
		$option = 'default_from_name';
		$value  = 'Changed Bw-Test';

		$I->setManifestOption('com_bwpostman', $option, $value);
codecept_debug('Result in Playground');
codecept_debug(Generals::$com_options->default_from_name);
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
