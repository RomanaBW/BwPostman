<?php
use Page\Generals as Generals;
use Page\MainviewPage as MainView;
use Page\OptionsPage as OptionsPage;

/**
* Class TestOptionsCest
*
* This class contains all methods to test options of BwPostman
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
class TestOptionsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login     $loginPage
	 *
	 * @group   component
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
	 * Test method to save defaults once of BwPostman
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @group   component
	 * @group   001_installation
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function saveDefaults(AcceptanceTester $I)
	{
		$I->wantTo("Save Default Options BwPostman");
		$I->expectTo("see success message and component in menu");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$I->see("Message", Generals::$alert_header);
		$I->see(Generals::$alert_msg_txt, Generals::$alert_success);
	}

	/**
	 * Test method to set permissions of BwPostman
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @since   2.0.0
	 */
	public function setPermissions(AcceptanceTester $I)
	{
		$I->wantTo("Set Permissions BwPostman");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);

		$I->clickAndWait(OptionsPage::$tab_permissions, 1);

		// get rule names
		$rules  = $I->getRuleNamesByComponentAsset('com_bwpostman');

		foreach(OptionsPage::$bwpm_groups as $groupname => $actions)
		{
			// get ID of usergroup
			$group_id   = $I->getGroupIdByName($groupname);

			// select usergroup
			$slider = sprintf(OptionsPage::$perm_slider, $group_id);
			$I->scrollTo($slider, 0, -100);

			$I->clickAndWait($slider, 1);

			// set permissions
			for ($i = 0; $i < count($rules); $i++)
			{
				$identifier = ".//*[@id='jform_rules_" . $rules[$i] . '_' . $group_id . "']";
				$value      = $actions[$rules[$i]];

				$I->selectOption($identifier, $value);
			}

			// apply
			$I->clickAndWait(Generals::$toolbar['Save'], 1);

			// check success
			foreach ($rules as $rule)
			{
				$key_pos    = array_search($rule, $rules) + 1;
				$identifier = sprintf(OptionsPage::$result_row, $group_id, $key_pos);
				$value      = OptionsPage::$bwpm_group_permissions[$groupname][$rule];

				$I->see($value, $identifier);
			}
		}
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   \Page\Login             $loginPage
	 *
	 * @group   component
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

