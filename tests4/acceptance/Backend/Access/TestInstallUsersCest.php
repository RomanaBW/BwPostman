<?php
namespace Backend\Access;

use Codeception\Codecept;
use mysql_xdevapi\Exception;
use Page\Generals as Generals;
use Page\Login as LoginPage;

use Page\AccessPage as AccessPage;
use Page\InstallUsersPage as UsersPage;


/**
 * Class TestInstallUsersCest
 *
 * This class contains the installation and configuration of all users which are needed to test access at backend of BwPostman
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
 * @since   2.1.0
 */
class TestInstallUsersCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage            $loginPage
	 * @param   array                  $user
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(LoginPage $loginPage, array $user)
	{
		$loginPage->logIntoBackend($user);
	}

	/**
	 * Test method to check for allowed/forbidden of list links at main view of BwPostman
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function installNeededUsers(\AcceptanceTester $I)
	{
		$I->wantTo("install needed users");
		$I->expectTo("have needed users in database");

		$loginPage = new LoginPage($I);
		$this->_login($loginPage, Generals::$admin);

		foreach (AccessPage::$all_users as $user)
		{
			# Switch to user page
			$I->amOnPage(UsersPage::$user_management_url);
			codecept_debug('User:');
			codecept_debug($user);

			$userName = $user['user'];

			$groupId = $I->grabColumnFromDatabase(Generals::$db_prefix . 'usergroups', 'id', array('title' => $userName));

			if (empty($groupId))
			{
				$groupId = 0;
			}

			if (array_key_exists(0, $groupId))
			{
				$groupId = (int)$groupId[0];
			}

			codecept_debug('Show resulting group ID: ');
			codecept_debug($groupId);

			# Check if user exists and set user ID variable
			codecept_debug('Check if user exists and set user ID variable');
			$userId = $I->grabColumnFromDatabase(Generals::$db_prefix . 'users', 'id', array('name' => $userName));
			codecept_debug('User ID from table:');
			codecept_debug($userId);

			if (empty($userId))
			{
				$userId = 0;
			}

			if (is_array($userId) && array_key_exists(0, $userId))
			{
				$userId = (int)$userId[0];
			}

			// User doesn't exist, so create it
			if ($userId === 0)
			{
				codecept_debug('User does not exist, so create it');

				$I->click(Generals::$toolbar['New']);
				$I->waitForElement(UsersPage::$registerName);
				$I->click(UsersPage::$accountDetailsTab);

				# Add user
				$I->fillField(UsersPage::$registerName, $user['user']);
				$I->fillField(UsersPage::$registerLoginName, $user['user']);
				$I->fillField(UsersPage::$registerPassword1, $user['password']);
				$I->fillField(UsersPage::$registerPassword2, $user['password']);
				$I->fillField(UsersPage::$registerEmail, $user['user'] . "@tester-net.nil");

				$I->click(Generals::$toolbar['Save & Close']);
				$I->waitForElement(Generals::$alert_success, 10);
				$I->see(UsersPage::$createSuccessMsg, Generals::$alert_success);
			}

			codecept_debug('Show resulting user ID: ');
			codecept_debug($userId);

			// If user exists and appropriate group exists
			if ($userId !== 0 && $groupId !== 0)
			{
				// Check, if user is mapped to appropriate group
				codecept_debug('Check, if existing user is mapped to appropriate group');

				// @ToDo: Check if checkbox for appropriate usergroup is checked. If so, continue, else check checkbox.
				$groupMap = $I->grabFromDatabase(Generals::$db_prefix . 'user_usergroup_map', 'group_id', array('user_id' => $userId));

				codecept_debug('Show group map from table: ');
				codecept_debug($groupMap);

				// Is user is not mapped, insert it
				if (!$groupMap)
				{
					codecept_debug('User is not mapped to appropriate group');

					$I->insertRecordToTable('user_usergroup_map', "$userId, $groupId");
				}
			}


		}
		$this->_logout($I, $loginPage);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   \AcceptanceTester     $I
	 * @param   LoginPage             $loginPage
	 * @param   boolean               $truncateSession
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(\AcceptanceTester $I, LoginPage $loginPage, $truncateSession = false)
	{
		$loginPage->logoutFromBackend($I, $truncateSession);
	}

}
