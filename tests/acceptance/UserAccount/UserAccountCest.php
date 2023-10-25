<?php
use Page\Generals as Generals;
use Page\InstallUsersPage as UsersPage;
use Page\Login;
use Page\UserAccountPage as UAPage;
use Page\SubscriberEditPage as SubEdit;
use Page\SubscriberManagerPage as SubManage;
use Page\User2SubscriberPage as RegPage;

//use Codeception\Extension\BwRunFailed;

/**
 * Class UserAccountCest
 *
 * This class contains all methods to test subscription while registration to Joomla! at front end
 *
 * @package Register Subscribe Plugin
 * @copyright (C) 2022 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   4.1.0
 */
class UserAccountCest
{
	/**
	 * @var AcceptanceTester  $tester AcceptanceTester
	 *
	 * @since   4.1.0
	 */
	public $tester;

	/**
	 * Test method to login into backend
	 *
	 * @param   Page\Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.1.0
	 */
	public function _login(Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin, $this->tester);
	}

	/**
	 * Test method to check if new or removed Joomla! account reaches subscriber data
	 * New account has to have the new User-ID, removed account has to have empty User-ID
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.1.0
	 */
	public function SyncSubscriberWithAccount(AcceptanceTester $I)
	{
		$I->wantTo("see if new Joomla account reaches subscriber data");
		$I->expectTo('see unconfirmed Joomla user but no subscriber');

		$I->wantTo("Create one Subscriber complete list view");

		// (P)reset component options
		Generals::presetComponentOptions($I);

		// Ensure U2S plugin is deactivated
		$u2SState = (int)$I->getExtensionEnabledState('bwpm_user2subscriber');
		codecept_debug('State of U2S: ' . $u2SState);
		$I->setExtensionStatus('bwpm_user2subscriber', '0');

		// Create subscription
		$this->createSubscription($I);

		// Check that userID is 0
		$this->checkSavedValues($I, '0');

		// Create Joomla account
		$this->createJoomlaAccount($I);

		// Get ID of new account
		$userID = $I->grabFromDatabase(Generals::$db_prefix . 'users', 'id', array('email' => SubEdit::$field_email));

		// Check that userID has reached subscriber data
		$this->checkSavedValues($I, $userID);

		// Remove Joomla account
		$this->deleteJoomlaUser($I, $userID);

		// Check that userID is 0
		$this->checkSavedValues($I, '0');

		// Remove BwPostman subscription
		$I->amOnPage(SubManage::$url);
		$edit_arc_del_array = SubEdit::prepareDeleteArray($I);

		$I->HelperArcDelItems($I, SubManage::$arc_del_array, $edit_arc_del_array, true);
		$I->see('Subscribers', Generals::$pageTitle);

		// Set enabled state of U2S to previous state
		$I->setExtensionStatus('bwpm_user2subscriber', $u2SState);

		// Reset settings
		Generals::presetComponentOptions($I);
	}

	/**
	 * Method to check, if entered values are correctly saved at database
	 *
	 * @param AcceptanceTester $I
	 * @param string $userId
	 *
	 * @since 4.1.0
	 */
	private function checkSavedValues(AcceptanceTester $I, $userId = '0')
	{
		$table_subs     = Generals::$db_prefix . 'bwpostman_subscribers';
		$valuesExpected = array(
			'name'          => SubEdit::$field_name,
			'firstname'     => SubEdit::$field_firstname,
			'email'         => SubEdit::$field_email,
			'user_id'       => $userId,
		);

		$I->seeInDatabase($table_subs, $valuesExpected);
	}/**
 * @param AcceptanceTester $I
 *
 *
 * @throws Exception
 *
 * @since 4.1.0
 */
	private function createSubscription(AcceptanceTester $I): void
	{
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		SubEdit::fillFormSimple($I, SubManage::$format_html, SubEdit::$female);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(SubEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 4.1.0
	 */
	private function createJoomlaAccount(AcceptanceTester $I): void
	{
		$I->selectRegistrationPage($I, RegPage::$register_url, RegPage::$view_register);

		$I->scrollTo("//*[@id='member-registration']");
		$I->wait(1);

		$I->fillField(RegPage::$login_identifier_name, RegPage::$login_value_name);
		$I->fillField(RegPage::$login_identifier_username, RegPage::$login_value_username);
		$I->fillField(RegPage::$login_identifier_email1, SubEdit::$field_email);

		$I->fillField(RegPage::$login_identifier_password1, RegPage::$login_value_password);
		$I->fillField(RegPage::$login_identifier_password2, RegPage::$login_value_password);

		$I->click(RegPage::$login_identifier_register);

		$I->waitForElementVisible(RegPage::$success_heading_identifier, 30);
		$I->wait(2);

		$I->see(RegPage::$register_success, RegPage::$success_message_identifier);
	}

	/**
	 * Method to delete Joomla user account
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
	 */
	protected function deleteJoomlaUser(AcceptanceTester $I, $userId)
	{
		/// Switch to user page
		$I->amOnPage(UsersPage::$user_management_url);

		// Search for user by email
		$I->fillField(Generals::$search_field, SubEdit::$field_email);
		$I->click(UAPage::$filter_go_button);

		// Delete user
		$I->click(Generals::$check_all_button);
		$I->click(Generals::$toolbarActions);
		$I->clickAndWait(UAPage::$delete_button, 1);

		$jVersion = $I->getJoomlaMainVersion($I);

		if ($jVersion == 4)
		{
			$I->acceptPopup();
		}
		else
		{
			$I->see(Generals::$delUserConfirmMessage, Generals::$confirmModalDialog);
			$I->clickAndWait(Generals::$confirmModalYes, 1);
		}

		$I->waitForElementVisible(Generals::$alert_success, 10);
		$I->wait(1);
		$I->see(UAPage::$deleteSuccessMsg, Generals::$alert_success);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   Login             $loginPage
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function _logout(AcceptanceTester $I, Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
