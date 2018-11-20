<?php
use Page\Generals as Generals;
use Page\MainviewPage as MainView;
use Page\OptionsPage as OptionsPage;

/**
 * Class TestOptionsCest
 *
 * This class contains all methods to test options of BwPostman
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
class TestOptionsCest
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
	 * Test method to save defaults once of BwPostman
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
	public function saveDefaults(AcceptanceTester $I)
	{
		OptionsPage::saveDefaults($I);
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
	 * @throws \Exception
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

		foreach(OptionsPage::$bwpm_groups as $groupname => $values)
		{
			$actions  = $values['permissions'];
			$group_id = $I->getGroupIdByName($groupname);
			$slider   = $this->selectPermissionsSliderForUsergroup($I, $group_id);

			// set permissions
			for ($i = 0; $i < count($rules); $i++)
			{
				$this->setSinglePermission($I, $rules, $i, $group_id, $actions);
			}

			// apply
			$I->clickAndWait(Generals::$toolbar['Save'], 1);

			$I->clickAndWait(OptionsPage::$tab_permissions, 1);

			// select usergroup
			$I->scrollTo($slider, 0, -100);

			$I->click($slider);
			$I->waitForElement($slider, 30);

			// check success
			foreach ($rules as $rule)
			{
				$this->checkSetPermissionsSuccess($I, $rule, $rules, $group_id, $groupname);
			}
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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

	/**
	 * Method to check success of setting permissions
	 *
	 * @param AcceptanceTester $I
	 * @param                  $rule
	 * @param                  $rules
	 * @param                  $group_id
	 * @param                  $groupname
	 *
	 * @return void
	 *
	 * @since 2.2.0
	 */
	protected function checkSetPermissionsSuccess(AcceptanceTester $I, $rule, $rules, $group_id, $groupname)
	{
		$key_pos    = array_search($rule, $rules) + 1;
		$identifier = sprintf(OptionsPage::$result_row, $group_id, $key_pos);
		$value      = OptionsPage::$bwpm_group_permissions[$groupname][$rule];

		$scrollPos = './/*[@id="jform_rules_' . $rule . '_' . $group_id . '"]';
		$I->scrollTo($scrollPos, 0, -150);

		$I->see($value, $identifier);
	}

	/**
	 * Method to set single permission
	 *
	 * @param AcceptanceTester $I
	 * @param                  $rules
	 * @param                  $i
	 * @param                  $group_id
	 * @param                  $actions
	 *
	 * @since 2.2.0
	 *
	 * @throws Exception
	 */
	protected function setSinglePermission(AcceptanceTester $I, $rules, $i, $group_id, $actions)
	{
		$identifier = './/*[@id="jform_rules_' . $rules[$i] . '_' . $group_id . '"]';
		$value      = $actions[$rules[$i]];

		$I->scrollTo($identifier, 0, -150);
		$I->waitForElementVisible($identifier, 30);

		$selector = '#jform_rules_' . $rules[$i] . '_' . $group_id;
		$I->removeSelectedAttribute($selector);

		$I->click($identifier);
		$I->selectOption($identifier, $value);
		$I->wait(1);
	}

	/**
	 * Method to select permissions slider for a given usergroup
	 *
	 * @param AcceptanceTester $I
	 * @param                  $group_id
	 *
	 * @return string
	 *
	 * @since 2.2.0
	 *
	 * @throws Exception
	 */
	protected function selectPermissionsSliderForUsergroup(AcceptanceTester $I, $group_id)
	{
		$slider = sprintf(OptionsPage::$perm_slider, $group_id);
		$I->scrollTo($slider, 0, -100);

		$I->click($slider);
		$I->waitForElement($slider, 30);

		return $slider;
	}
}
