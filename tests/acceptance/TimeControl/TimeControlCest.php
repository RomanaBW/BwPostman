<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\TimeControlPage as TimeControlPage;
use Page\InstallationPage as InstallPage;
use Page\InstallUsersPage as UsersPage;
use Page\NewsletterEditPage as NlEdit;
use Page\NewsletterManagerPage as NlManage;


/**
 * Class TimeControlCest
 *
 * This class contains all methods to test installation and modify options of this plugin
 *
 * @package BwPostman Plugin TimeControl
 * @subpackage Installation and Plugin Options
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   0.9.5
 */
class TimeControlCest
{
	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   0.9.5
	 */
	public $tester;

	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   0.9.5
	 */
	public function _login(LoginPage $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to install plugin with installed component
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
	 * @since   0.9.5
	 */
	public function installWithPrerequisites(AcceptanceTester $I)
	{
		$I->wantTo("Install plugin after installing package");
		$I->expectTo("see success message and installed plugin");

		$this->installPlugin($I);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$installPackageSuccessMsg, Generals::$alert_success);
		$I->dontSee("Error", Generals::$alert_heading);

		// Check for extension plugin exists and is activated
		$this->selectPluginPage($I);
		$this->filterForPlugin($I, TimeControlPage::$pluginExtensionName);

		$I->see(TimeControlPage::$pluginExtensionName);
		$I->seeElement(InstallPage::$icon_published);

		// Check for TC plugin exists
		$this->filterForPlugin($I, TimeControlPage::$pluginName);

		$I->see(TimeControlPage::$pluginName);
	}

	/**
	 * Test method to activate plugin an initialize options
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
	 * @since   0.9.5
	 */
	public function initializeOptionsAndActivateTimeControl(AcceptanceTester $I)
	{
		$I->wantTo("initialize options activate plugin TimeControlPage");
		$I->expectTo("see success message and green arrow in extensions list");

		$this->goToEditPluginParams($I);

		// set options
		$this->setCredentials($I, TimeControlPage::$user1);
		$I->clickSelectList(TimeControlPage::$pluginOptionIntervalField, TimeControlPage::$pluginOptionIntervalValue1, TimeControlPage::$pluginOptionIntervalFieldId);
		$I->fillField(TimeControlPage::$pluginOptionLicenceField, TimeControlPage::$pluginOptionLicence);

		// Save and close
		$this->saveCloseOptions($I);

		// Activate plugin
		$I->checkOption(Generals::$check_all_button);
		$I->click(Generals::$toolbar['Enable']);

		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(InstallPage::$pluginEnableSuccessMsg, Generals::$alert_success);
		$I->seeElement(InstallPage::$icon_published);

	}

	/**
	 * Test method to option message
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
	 * @since   0.9.5
	 */
	public function addCronUser(AcceptanceTester $I)
	{
		$I->wantTo("add cron user and assign to needed user group");
		$I->expectTo('see added user in database');

		# Switch to user page
		$I->amOnPage(UsersPage::$user_management_url);

		# Check for usergroup. If not exists, throw exception
		$user = TimeControlPage::$user1;
		$userName = $user['name'];
		$groupId  = array();
		try
		{
			$groupId = $I->grabColumnFromDatabase(Generals::$db_prefix . 'usergroups', 'id', array('title' => "BwPostmanNewsletterPublisher"));

			if (!$groupId[0])
			{
				$e = new Exception();
				throwException($e);
			}
		}
		catch (RuntimeException $e)
		{
			codecept_debug('Error while grabbing group ID!');
			codecept_debug('Group ID Catch: ');
		}

		# Check for user. If exists, ensure checkbox is checked
		try
		{
			$userId = $I->grabColumnFromDatabase(Generals::$db_prefix . 'users', 'id', array('name' => $userName));

			if ($userId[0])
			{
				$groupMap = $I->grabFromDatabase(Generals::$db_prefix . 'user_usergroup_map', 'group_id', array('user_id' => $userId[0]));

				if (!$groupMap)
				{
					$I->insertRecordToTable('user_usergroup_map', "$userId[0], $groupId[0]");
				}
			}
		}
		catch (RuntimeException $e)
		{
			// Create user, if not exists
			$I->click(Generals::$toolbar['New']);
			$I->waitForElement(UsersPage::$registerName);
			$I->click(UsersPage::$accountDetailsTab);

			# Add user
			$I->fillField(UsersPage::$registerName, $user['name']);
			$I->fillField(UsersPage::$registerLoginName, $user['name']);
			$I->fillField(UsersPage::$registerPassword1, $user['password']);
			$I->fillField(UsersPage::$registerPassword2, $user['password']);
			$I->fillField(UsersPage::$registerEmail, $user['name'] . "@tester-net.nil");

			// Set usergroup
			$I->click(UsersPage::$usergroupTab);
			$I->waitForElement(UsersPage::$publicGroup);

			$checkbox = sprintf(UsersPage::$usergroupCheckbox, $groupId[0]);
			$I->scrollTo($checkbox, 0, -100);
			$I->wait(1);
			$I->click($checkbox);

			// Set timezone
			$I->scrollTo('/html/body/div[2]/section/div/div/form/fieldset/div/div[2]/div[3]/div/label', 0, -300);
			$I->wait(1);
			$I->click(TimeControlPage::$userTimezoneTab);
			$I->waitForElement(TimeControlPage::$pluginUserTimezoneField);
			$I->clickSelectList(TimeControlPage::$pluginUserTimezoneField, TimeControlPage::$pluginUserTimezoneValue, TimeControlPage::$pluginUserTimezoneFieldId);

			$I->click(Generals::$toolbar['Save & Close']);
			$I->waitForElement(Generals::$alert_success, 10);
			$I->see(UsersPage::$createSuccessMsg, Generals::$alert_success);
		}
	}

	/**
	 * Test method to start cron server with correct credentials
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function startCronServerWithCorrectCredentials(AcceptanceTester $I)
	{
		$I->wantTo("start cron server");
		$I->expectTo('see message cron server started');

		$this->doStartCronServer($I, true);
	}

	/**
	 * Test method to stop cron server
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
	 * @since   0.9.5
	 */
	public function stopCronServer(AcceptanceTester $I)
	{
		$I->wantTo("stop cron server");
		$I->expectTo('see message cron server stopped');

		$this->doStopCronServer($I);
	}

	/**
	 * Test method to start cron server with wrong password
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function startCronServerWithWrongPassword(AcceptanceTester $I)
	{
		$I->wantTo("start cron server with faulty password");
		$I->expectTo('see message cron server could not start');

		$this->goToEditPluginParams($I);
		$this->setCredentials($I, TimeControlPage::$user2);
		$this->saveCloseOptions($I);

		$this->doStartCronServer($I, false);

		// Reset user at plugin options
		$this->goToEditPluginParams($I);
		$this->setCredentials($I, TimeControlPage::$user1);
		$this->saveCloseOptions($I);
	}

	/**
	 * Test method to start cron server with completely false credentials
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function startCronServerWithCompletelyFalseCredentials(AcceptanceTester $I)
	{
		$I->wantTo("start cron server with completely wrong credentials");
		$I->expectTo('see message cron server could not start');

		$this->goToEditPluginParams($I);
		$this->setCredentials($I, TimeControlPage::$user4);
		$this->saveCloseOptions($I);

		$this->doStartCronServer($I, false);

		// Reset user at plugin options
		$this->goToEditPluginParams($I);
		$this->setCredentials($I, TimeControlPage::$user1);
		$this->saveCloseOptions($I);
	}

	/**
	 * Test method to start cron server with wrong user name
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
//	public function startCronServerWithWrongUserName(AcceptanceTester $I)
//	{
//		$I->wantTo("start cron server with faulty user name");
//		$I->expectTo('see message cron server could not start');
//
//		$this->goToEditPluginParams($I);
//		$this->setCredentials($I, TimeControlPage::$user3);
//		$this->saveCloseOptions($I);
//
//		$this->doStartCronServer($I, false);
//
//		// Reset user at plugin options
//		$this->goToEditPluginParams($I);
//		$this->setCredentials($I, TimeControlPage::$user1);
//		$this->saveCloseOptions($I);
//	}

	/**
	 * Test method to create timed newsletter, activated, wait for sending time passed, then check if sent
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function sendTimedNewsletterActivated(AcceptanceTester $I)
	{
		$this->doStartCronServer($I, true);

		// create newsletter conventional
		$I->amOnPage(TimeControlPage::$nlListsPage);
		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author'], false, false);

		$this->addSendingTime($I);
		$this->activateSendingTime($I);

		$this->waitUntilSent($I);

		$this->checkForSendSuccess($I);

		// cleanup newsletter
		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		$this->doStopCronServer($I);
	}

	/**
	 * Test method to create timed newsletter, not activated, wait for sending time passed, then activate and check if sent
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function sendTimedNewsletterNotActivated(AcceptanceTester $I)
	{
		$this->doStartCronServer($I, true);

		// create newsletter
		$I->amOnPage(TimeControlPage::$nlListsPage);
		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author'], false, false);
		$this->addSendingTime($I);

		// wait for sending time passed plus some time
		$I->wait('70');

		$this->activateSendingTime($I);

		$this->waitUntilSent($I);

		$this->checkForSendSuccess($I);

		// cleanup newsletter
		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		$this->doStopCronServer($I);
	}

	/**
	 * Test method to create timed newsletter, activated, wait for sending time passed, then check if sent
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	public function sendTimedNewsletterThenStartCronServer(AcceptanceTester $I)
	{
		// create newsletter conventional
		$I->amOnPage(TimeControlPage::$nlListsPage);
		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author'], false, false);

		$this->addSendingTime($I);
		$this->activateSendingTime($I);

		$I->wait('70');

		$this->doStartCronServer($I, true);

		$I->amOnPage(TimeControlPage::$nlListsPage);
		$this->waitUntilSent($I);

		$this->checkForSendSuccess($I);

		// cleanup newsletter
		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		$this->doStopCronServer($I);
	}

	/**
	 * Helper method to mgo to plugin page
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since 0.9.5
	 */
	private function selectPluginPage(AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$plugin_page);
		$I->wait(1);
		$I->see(Generals::$view_plugin, Generals::$pageTitle);
	}

	/**
	 * Helper method to select specific plugin at plugin page
	 *
	 * @param AcceptanceTester  $I
	 * @param string            $pluginName
	 *
	 * @since 0.9.5
	 */
	private function filterForPlugin(AcceptanceTester $I, $pluginName)
	{
		$I->fillField(Generals::$search_field, $pluginName);
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * Helper method to install plugin
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function installPlugin(AcceptanceTester $I)
	{
		$I->amOnPage(InstallPage::$install_url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingInstall);

		$new_j_installer = true;

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: visible');");
		}

		$I->attachFile(InstallPage::$installField, TimeControlPage::$installFileTC);

		if (!$new_j_installer)
		{
			$I->click(InstallPage::$installButton);
		}

		if ($new_j_installer)
		{
			$I->executeJS("document.getElementById('legacy-uploader').setAttribute('style', 'display: none');");
		}

		$I->waitForElement(Generals::$sys_message_container, 30);
	}


	/**
	 * Helper method to set plugin credentials
	 *
	 * @param   AcceptanceTester  $I
	 * @param   array             $user
	 *
	 * @since 0.9.5
	 */
	private function setCredentials(AcceptanceTester $I, $user)
	{
		$I->fillField(TimeControlPage::$pluginOptionUserNameField, $user['name']);
		$I->fillField(TimeControlPage::$pluginOptionPasswordField, $user['password']);
	}

	/**
	 * Helper method to save and close plugin options
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function saveCloseOptions(AcceptanceTester $I)
	{
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_success, 150);
		$I->see(InstallPage::$pluginSavedSuccess, Generals::$alert_success);
	}


	/**
	 * Helper method to start cron server
	 *
	 * @param   AcceptanceTester  $I
	 * @param   boolean           $success
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function doStartCronServer(AcceptanceTester $I, $success)
	{
		$I->amOnPage(TimeControlPage::$maintenanceUrl);
		$I->see(TimeControlPage::$infoCronServerStopped, Generals::$alert_info);

		$I->click(TimeControlPage::$startCronServerButton);

		if ($success)
		{
			$I->waitForElementVisible(Generals::$alert_info, 10);
			$I->see(TimeControlPage::$infoCronServerStarted, Generals::$alert_info);
		}
		else
		{
			$I->waitForElementVisible(Generals::$alert_error, 60);
			$I->see(TimeControlPage::$errorCronServerWrongCredentials, Generals::$alert_error);
			$I->see(TimeControlPage::$errorCronServerNotStarted, Generals::$alert_error);
			$I->see(TimeControlPage::$infoCronServerStopped, Generals::$alert_info);
		}
	}

	/**
	 * Helper method to stop cron server
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function doStopCronServer(AcceptanceTester $I)
	{
		$I->amOnPage(TimeControlPage::$maintenanceUrl);
		$I->see(TimeControlPage::$infoCronServerStarted, Generals::$alert_info);

		$I->click(TimeControlPage::$stopCronServerButton);
		$I->waitForElementVisible(Generals::$alert_nothing, 10);
		$I->see(TimeControlPage::$infoStoppingCronServer, Generals::$alert_nothing);

		$I->waitForElementVisible(Generals::$alert_info, 300);
		$I->see(TimeControlPage::$infoCronServerStopped, Generals::$alert_info);
	}

	/**
	 * Helper method to go to plugin page, filter for plugin and open for edit
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function goToEditPluginParams(AcceptanceTester $I)
	{
		$this->selectPluginPage($I);
		$this->filterForPlugin($I, TimeControlPage::$pluginName);

		$I->see(TimeControlPage::$pluginName);

		$I->click(".//*[@id='pluginList']/tbody/tr/td[4]/a");
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(InstallPage::$headingPlugins . ": " . TimeControlPage::$pluginName);
	}

	/**
	 * Helper method to add sending time to an existing newsletter
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function addSendingTime(AcceptanceTester $I)
	{
		$sendingTime = new DateTime(TimeControlPage::$scheduledDateOffset, new DateTimeZone('Europe/Berlin'));

		$I->clickAndWait(TimeControlPage::$nlListUnsentSubjectField, 1);
		$I->fillField(TimeControlPage::$scheduledDateField, $sendingTime->format('Y-m-j H:i'));
		$I->click(Generals::$toolbar['Save & Close']);
		NlEdit::checkSuccess($I, Generals::$admin['author']);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Helper method to activate sending time of an existing newsletter

	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function activateSendingTime(AcceptanceTester $I)
	{
		$I->clickAndWait(TimeControlPage::$nlListUnsentSubjectField, 1);
		$I->selectOption(TimeControlPage::$scheduledActivatedField, TimeControlPage::$scheduledActivatedFieldTrue);
		$I->click(Generals::$toolbar['Save & Close']);
		NlEdit::checkSuccess($I, Generals::$admin['author']);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Helper method to wait for timed newsletter to be sent
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function waitUntilSent(AcceptanceTester $I)
	{
		$unsent = true;

		while ($unsent)
		{
			try
			{
				$I->wait(5);
				$I->amOnPage(TimeControlPage::$nlListsPage);
				$I->see(NlEdit::$field_subject, TimeControlPage::$nlListUnsentSubjectField);
			}
			catch (Exception $e)
			{
				$unsent = false;
			}
		}
		$I->dontSee(NlEdit::$field_subject, TimeControlPage::$nlListUnsentSubjectField);
	}

	/**
	 * Helper method to check success of sending timed newsletter
	 *
	 * @param   AcceptanceTester  $I
	 *
	 * @throws Exception
	 *
	 * @since 0.9.5
	 */
	private function checkForSendSuccess(AcceptanceTester $I)
	{
		// Check status of sent newsletter
		$I->clickAndWait(NlManage::$tab2, 2);
		$I->see(NlEdit::$field_subject, TimeControlPage::$nlListSentSubjectField);
		$I->clickSelectList(
			Generals::$ordering_list,
			".//*[@id='list_fullordering_chzn']/div/ul/li[text()='ID descending']",
			Generals::$ordering_id
		);
		$I->seeElement(NlManage::$first_line_unpublished);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester        $I
	 * @param   LoginPage             $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   0.9.5
	 */
	public function _logout(AcceptanceTester $I, LoginPage $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
