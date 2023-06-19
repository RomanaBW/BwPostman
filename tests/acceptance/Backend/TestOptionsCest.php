<?php
use \Page\Generals as Generals;
use \Page\MainviewPage as MainView;
use \Page\OptionsPage as OptionsPage;
use \Page\NewsletterEditPage as NlEditPage;
use \Page\NewsletterManagerPage as NlManagePage;
use \Page\SubscriberviewPage as SubsView;

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
	 * @throws Exception
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function saveDefaults(AcceptanceTester $I)
	{
		OptionsPage::saveDefaults($I);
	}

	/**
	 * Test method to check option sender name
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionSenderName(AcceptanceTester $I)
	{
		$I->wantTo("check default sender name of BwPostman from Joomla settings");
		$I->expectTo("see sender name of Joomla settings at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$from_name, 30);
		$I->seeInField(NlEditPage::$from_name, OptionsPage::$sendersNameByJoomla);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->wantTo("Preset sender name at options of BwPostman");
		$I->expectTo("see self set sender name at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(Generals::$toolbar['Options']);
		$I->waitForElement("#config", 30);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->fillField(OptionsPage::$sendersName, OptionsPage::$sendersNameByOption);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$from_name, 30);
		$I->seeInField(NlEditPage::$from_name, OptionsPage::$sendersNameByOption);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->setManifestOption('com_bwpostman', 'default_from_name', '');
	}

	/**
	 * Test method to check option sender email
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionSenderEmail(AcceptanceTester $I)
	{
		$I->wantTo("check default sender email of BwPostman from Joomla settings");
		$I->expectTo("see sender email of Joomla settings at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$from_email, 30);
		$I->seeInField(NlEditPage::$from_email, OptionsPage::$sendersMailByJoomla);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->wantTo("Preset sender email at options of BwPostman");
		$I->expectTo("see self set sender email at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(Generals::$toolbar['Options']);
		$I->waitForElement("#config", 30);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->fillField(OptionsPage::$sendersEmail, OptionsPage::$sendersMailByOption);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$from_email, 30);
		$I->seeInField(NlEditPage::$from_email, OptionsPage::$sendersMailByOption);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->setManifestOption('com_bwpostman', 'default_from_email', '');
	}

	/**
	 * Test method to check option reply email
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionReplyEmail(AcceptanceTester $I)
	{
		$I->wantTo("check default reply email of BwPostman from Joomla settings");
		$I->expectTo("see sender email of Joomla settings as reply to email at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$reply_email, 30);
		$I->seeInField(NlEditPage::$reply_email, OptionsPage::$replyMailByJoomla);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->wantTo("Preset reply email at options of BwPostman");
		$I->expectTo("see self set reply to email at create newsletters");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->fillField(OptionsPage::$replyEmail, OptionsPage::$replyMailByOption);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->click(MainView::$addNewsletterButton);
		$I->waitForElement(NlEditPage::$reply_email, 30);
		$I->seeInField(NlEditPage::$reply_email, OptionsPage::$replyMailByOption);
		$I->click(Generals::$toolbar['Back']);
		$I->waitForElement(MainView::$dashboard, 30);

		$I->setManifestOption('com_bwpostman', 'default_reply_email', '');
	}

	/**
	 * Test method to check option legal info
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionLegalInfo(AcceptanceTester $I)
	{
		$I->wantTo("check option legal info");
		$I->expectTo("see self set legal info text at newsletter footer");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life, get sent mail
	}

	/**
	 * Test method to check option excluded categories
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionExcludedCategories(AcceptanceTester $I)
	{
		$I->wantTo("check option excluded categories");
		$I->expect("not to see articles of excludes category at create/edit newsletters articles list");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Get first available content
		$I->click(MainView::$addNewsletterButton);
		$I->scrollTo(NlEditPage::$legend_content);
		$I->wait(1);
		$availableContent1 = $I->grabTextFrom(sprintf(NlEditPage::$available_content, 3));

		$I->assertEquals("sample-data-articles/park-site/photo-gallery/animals = Phyllopteryx", $availableContent1);
		$I->clickAndWait(Generals::$toolbar['Back'], 1);

		// Set excluded categories
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$excludedCategories, 0, -120);
		$I->wait(1);
		$I->fillField(OptionsPage::$excludedCategories, " Animals");
		$I->moveMouseOver(OptionsPage::$excludedCategories, 200, 0);
		$I->wait(2);

		$I->click(OptionsPage::$excludedCategoriesListResult);
		$I->click(Generals::$toolbar['Save']);

		$I->scrollTo(OptionsPage::$excludedCategories, 0, -120);
		$I->wait(1);
		$excluded = $I->grabTextFrom(OptionsPage::$excludedCategoriesResult);
		$I->assertEquals("- - - Animals (en-GB)", $excluded);

		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

		// Get second available content
		$I->click(MainView::$addNewsletterButton);
		$I->scrollTo(NlEditPage::$legend_content);
		$I->wait(1);
		$availableContent2 = $I->grabTextFrom(sprintf(NlEditPage::$available_content, 3));

		$I->assertNotEquals("sample-data-articles/park-site/photo-gallery/animals = Phyllopteryx", $availableContent2);
		$I->clickAndWait(Generals::$toolbar['Back'], 1);

		// Reset excluded categories
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);


		$I->scrollTo(OptionsPage::$excludedCategories, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$excludedCategoriesEmptyResult);
		$I->click(Generals::$toolbar['Save']);

		$I->scrollTo(OptionsPage::$excludedCategories, 0, -120);
		$I->wait(1);
		$I->dontSeeInField(OptionsPage::$excludedCategories, "Content Modules");
	}

	/**
	 * Test method to check option newsletters per step
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionNewslettersPerStep(AcceptanceTester $I)
	{
		$I->wantTo("check option newsletters per step");
		$I->expectTo("see defined number of newsletters per step at sending tab and sending popup");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set new numbers per step
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$numberNlsToSend, 0, -120);
		$I->wait(1);
		$I->fillField(OptionsPage::$numberNlsToSend, OptionsPage::$newslettersPerStep);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		NlEditPage::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEditPage::SendNewsletterOptionsCheck($I, 20, OptionsPage::$newslettersPerStep);

		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEditPage::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		// Rest new numbers per step
		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$numberNlsToSend, 0, -120);
		$I->wait(1);
		$I->fillField(OptionsPage::$numberNlsToSend, OptionsPage::$newslettersPerStepDefault);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);
	}

	/**
	 * Test method to check option delay time
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionDelayTime(AcceptanceTester $I)
	{
		$I->wantTo("check option delay time");
		$I->expectTo("see defined delay time at sending popup");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set new numbers of seconds for time delay
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$delayTime, 0, -120);
		$I->wait(1);
		$I->fillField(OptionsPage::$delayTime, OptionsPage::$numberOfSeconds);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		NlEditPage::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEditPage::SendNewsletterOptionsCheck($I, 20, 100, OptionsPage::$numberOfSeconds, 'seconds');

		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEditPage::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		// Reset new numbers od seconds
		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$delayTime, 0, -120);
		$I->wait(1);
		$I->fillField(OptionsPage::$delayTime, OptionsPage::$numberOfSecondsDefault);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);
	}

	/**
	 * Test method to check option delay unit
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionDelayUnit(AcceptanceTester $I)
	{
		$I->wantTo("check option delay unit");
		$I->expectTo("see defined delay time unit at sending popup");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set unit of time delay
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$delayUnitMinutes, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$delayUnitMinutes);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		NlEditPage::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEditPage::SendNewsletterOptionsCheck($I, 20, 100, OptionsPage::$numberOfSecondsDefault, 'minute');
		// Cleanup newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEditPage::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		// Reset new numbers od seconds
		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$delayTime, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$delayUnitSeconds);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

	}

	/**
	 * Test method to check option publish newsletter at sending
	 * basic settings
	 *
	 * Part of this is tested at details tests of newsletters, here only has to be tested, if this setting reaches database
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionPublishNewsletterAtSending(AcceptanceTester $I)
	{
		$I->wantTo("check option publish newsletter at sending yes");
		$I->expectTo("see value 1 in database");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set default publish yes
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$publishNlsAtSendingYes, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$publishNlsAtSendingYes);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$configJson = $I->grabFromDatabase(Generals::$db_prefix . 'extensions', 'params', array('element' => 'com_bwpostman'));
		$configObject = json_decode($configJson);

		$I->assertEquals($configObject->publish_nl_by_default, 1);

		$I->wantTo("check option publish newsletter at sending no");
		$I->expectTo("see value 0 in database");

		// Set default publish yes
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$publishNlsAtSendingNo, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$publishNlsAtSendingNo);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$configJson = $I->grabFromDatabase(Generals::$db_prefix . 'extensions', 'params', array('element' => 'com_bwpostman'));
		$configObject = json_decode($configJson);

		$I->assertEquals($configObject->publish_nl_by_default, 0);
	}

	/**
	 * Test method to check option compress backup
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionCompressBackup(AcceptanceTester $I)
	{
		$I->wantTo("check option compress backup no");
		$I->expectTo("see value 0 in database");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set default publish yes
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$compressBackupFileNo, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$compressBackupFileNo);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$configJson = $I->grabFromDatabase(Generals::$db_prefix . 'extensions', 'params', array('element' => 'com_bwpostman'));
		$configObject = json_decode($configJson);

		$I->assertEquals($configObject->compress_backup, 0);

		$I->wantTo("check option compress backup yes");
		$I->expectTo("see value 1 in database");

		// Set default publish yes
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$compressBackupFileYes, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$compressBackupFileYes);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$configJson = $I->grabFromDatabase(Generals::$db_prefix . 'extensions', 'params', array('element' => 'com_bwpostman'));
		$configObject = json_decode($configJson);

		$I->assertEquals($configObject->compress_backup, 1);
	}

	/**
	 * Test method to check option show Boldt Webservice link
	 * basic settings
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkBasicOptionShowBoldtWebserviceLink(AcceptanceTester $I)
	{
		$I->wantTo("check option show Boldt Webservice link no");
		$I->expect("not to see link to Boldt Webservice at frontend");

		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		// Set show Boldt Webservice link no
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$showBwLinkNo, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$showBwLinkNo);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$FE= $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$I->amOnPage(SubsView::$register_url);
				$I->wait(1);
				$I->seeElement(SubsView::$view_register);

				$I->scrollTo('.button-register');
				$I->wait(1);
				$I->dontSee('BwPostman by Boldt Webservice', '.bwpm_copyright');
			}
		);

		$I->wantTo("check option show Boldt Webservice link yes");
		$I->expectTo("see link to Boldt Webservice at frontend");

		// Set show Boldt Webservice link no
		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_basics, 1);

		$I->scrollTo(OptionsPage::$showBwLinkYes, 0, -120);
		$I->wait(1);
		$I->click(OptionsPage::$showBwLinkYes);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle);

		$FE= $I->haveFriend('Frontend');
		$FE->does(
			function (AcceptanceTester $I)
			{
				$I->amOnPage(SubsView::$register_url);
				$I->wait(1);
				$I->seeElement(SubsView::$view_register);

				$I->scrollTo('.button-register', 0, -120);
				$I->wait(1);
				$I->see('BwPostman by Boldt Webservice', '.bwpm_copyright');
			}
		);
	}

	/**
	 * Test method to check option introduction text
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionIntroText(AcceptanceTester $I)
	{
		// @specialNote: Only testable, if menu entry is created
		$I->wantTo("check option introduction text");
		$I->expectTo("see defined intro test at registration form at frontend");

		$I->amOnPage(MainView::$url);
		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show gender
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowGender(AcceptanceTester $I)
	{
		$I->wantTo("check option show gender no");
		$I->expect("not to see gender selection list at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show gender yes");
		$I->expectTo("see gender selection list at registration form at frontend");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option last name mandatory
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionLastNameMandatory(AcceptanceTester $I)
	{
		$I->wantTo("check option last name mandatory no");
		$I->expect("not to see field for last name at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option last name mandatory yes without entry");
		$I->expectTo("see field for last name at registration form at frontend and not be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option last name mandatory yes with entry");
		$I->expectTo("see field for last name at registration form at frontend, be able to register with filled field and see entered name at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show last name
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowLastName(AcceptanceTester $I)
	{
		$I->wantTo("check option show last name no");
		$I->expect("not to see field for last name at registration form at frontend and be able to register");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show last name yes without entry");
		$I->expectTo("see field for last name at registration form at frontend and be able to register without filled field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show last name yes with entry");
		$I->expectTo("see field for last name at registration form at frontend, be able to register with filled field and see entered name at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option first name mandatory
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionFirstNameMandatory(AcceptanceTester $I)
	{
		$I->wantTo("check option first name mandatory no");
		$I->expect("not to see field for first name at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option first name mandatory yes without entry");
		$I->expectTo("see field for first name at registration form at frontend and not be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option first name mandatory yes with entry");
		$I->expectTo("see field for first name at registration form at frontend, be able to register with filled field and see entered name at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show first name
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowFirstName(AcceptanceTester $I)
	{
		$I->wantTo("check option show first name no");
		$I->expect("not to see field for first name at registration form at frontend and be able to register");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show first name yes without entry");
		$I->expectTo("see field for first name at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show first name yes with entry");
		$I->expectTo("see field for first name at registration form at frontend, be able to register with filled field and see entered name at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show additional field
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowAdditionalField(AcceptanceTester $I)
	{
		$I->wantTo("check option show additional field no");
		$I->expect("not to see field for additional field at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show additional field yes without entry");
		$I->expectTo("see field for first name at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show additional field yes with entry");
		$I->expectTo("see field for first name at registration form at frontend, be able to register with filled field and see entered value at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option additional field mandatory
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionAdditionalFieldMandatory(AcceptanceTester $I)
	{
		$I->wantTo("check option additional field mandatory no");
		$I->expect("not to see field for additional field at registration form at frontend and be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option additional field mandatory yes without entry");
		$I->expectTo("see field for additional field at registration form at frontend and not be able to register with empty field");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option additional field mandatory yes with entry");
		$I->expectTo("see field for additional field at registration form at frontend, be able to register with filled field and see entered value at database");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option additional field label
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionAdditionalFieldLabel(AcceptanceTester $I)
	{
		$I->wantTo("check option additional field label");
		$I->expectTo("see entered value for label for additional field at registration form");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option additional field tooltip
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionAdditionalFieldTooltip(AcceptanceTester $I)
	{
		$I->wantTo("check option additional field tooltip");
		$I->expectTo("see entered value for tooltip for additional field at registration form");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show mail format
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowMailFormat(AcceptanceTester $I)
	{
		$I->wantTo("check option show mail format no");
		$I->expect("not to see mail format selection list at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show mail format yes");
		$I->expectTo("see mail format selection list at registration form at frontend");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option preset mail format
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionPresetMailFormat(AcceptanceTester $I)
	{
		$I->wantTo("check option preset mail format TEXT (without mail format selection)");
		$I->expect("not to see mail format selection at registration, see TEXT as format at database");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option preset mail format TEXT (with mail format selection), no change");
		$I->expectTo("see mail format selection with predefined TEXT at registration form at frontend and database");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option preset mail format TEXT (with mail format selection), with change");
		$I->expectTo("see mail format selection with predefined TEXT at registration form at frontend, HTML at database");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option preset mail format HTML (without mail format selection)");
		$I->expect("not to see mail format selection at registration, see HTML as format at database");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option preset mail format HTML (with mail format selection), no change");
		$I->expectTo("see mail format selection with predefined HTML at registration form at frontend and database");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option preset mail format TEXT (with mail format selection), with change");
		$I->expectTo("see mail format selection with predefined HTML at registration form at frontend, TEXT at database");
		$I->amOnPage(MainView::$url);
		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show mailinglist description
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionShowMailinglistDescription(AcceptanceTester $I)
	{
		$I->wantTo("check option show mailinglist description no");
		$I->expect("not to see mailinglist description at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show mail format yes");
		$I->expectTo("see mailinglist description at registration form at frontend");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option length of description
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionLengthOfDescription(AcceptanceTester $I)
	{
		$I->wantTo("check option length of description shorter than set limit");
		$I->expectTo("see complete mailinglist description at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option length of description longer than set limit");
		$I->expectTo("see shortened mailinglist description at registration form at frontend");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option display disclaimer
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionDisplayDisclaimer(AcceptanceTester $I)
	{
		$I->wantTo("check option display disclaimer no");
		$I->expect("not to see link for disclaimer at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option display disclaimer yes");
		$I->expectTo("see link for disclaimer at registration form at frontend");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option disclaimer link target
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionDisclaimerLinkTarget(AcceptanceTester $I)
	{
		$I->wantTo("check option disclaimer link target URL");
		$I->expectTo("see link for disclaimer at registration form at frontend with preset URL");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option disclaimer link target article");
		$I->expectTo("see link for disclaimer at registration form at frontend with preset article");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option disclaimer link target menu item");
		$I->expectTo("see link for disclaimer at registration form at frontend with preset menu item");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option disclaimer current window
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionDisclaimerCurrentWindow(AcceptanceTester $I)
	{
		$I->wantTo("check option disclaimer current window no");
		$I->expectTo("see clicked disclaimer at registration form at frontend in current window/tab");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option disclaimer current window yes");
		$I->expectTo("see clicked disclaimer at registration form at frontend in new window/tab");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option disclaimer popup
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionDisclaimerPopup(AcceptanceTester $I)
	{
		$I->wantTo("check option disclaimer popup no (current window no)");
		$I->expectTo("see clicked disclaimer at registration form at frontend in new window/tab");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option disclaimer popup yes");
		$I->expectTo("see clicked disclaimer at registration form at frontend in popup");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option secure registration form
	 * registration
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkRegistrationOptionSecureRegistrationForm(AcceptanceTester $I)
	{
		$I->wantTo("check option secure registration form no");
		$I->expect("not to see set question or captcha at registration form at frontend");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option secure registration form with question and wrong answer");
		$I->expectTo("see set question at registration form at frontend and not be able to register");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option secure registration form with question and correct answer");
		$I->expectTo("see set question at registration form at frontend and be able to register");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option secure registration form with captcha and wrong answer");
		$I->expectTo("see set captcha at registration form at frontend and not be able to register");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option secure registration form with captcha and correct answer");
		$I->expectTo("see set captcha at registration form at frontend and be able to register");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option title for activation
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionTitleForActivation(AcceptanceTester $I)
	{
		$I->wantTo("check option title for activation");
		$I->expectTo("see this title at activation mail");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option text for activation
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionTextForActivation(AcceptanceTester $I)
	{
		$I->wantTo("check option text for activation");
		$I->expectTo("see this text at activation mail");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option text agreement
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionTextAgreement(AcceptanceTester $I)
	{
		$I->wantTo("check option text agreement");
		$I->expectTo("see agreement text at activation mail");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option activation also to webmaster?
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionActivationToWebmaster(AcceptanceTester $I)
	{
		$I->wantTo("check option activation also to webmaster no");
		$I->expect("not to get information mail at webmaster");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option activation also to webmaster yes");
		$I->expectTo("get information mail at webmaster");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option activation sender name
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionActivationSenderName(AcceptanceTester $I)
	{
		$I->wantTo("check option activation sender name");
		$I->expectTo("see entered sender name at information mail to webmaster");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option activation sender mail
	 * activation
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkActivationOptionActivationSenderMail(AcceptanceTester $I)
	{
		$I->wantTo("check option activation sender mail");
		$I->expectTo("get information mail to webmaster at entered mail address");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option unsubscription with one click
	 * Unsubscription
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkUnsubscriptionOptionUnsubscriptionWithOneClick(AcceptanceTester $I)
	{
		$I->wantTo("check option unsubscription with one click no, one mailinglist");
		$I->expectTo("see subscriber at archived subscribers");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option unsubscription with one click no, multiple mailinglist");
		$I->expectTo("see subscriber at remaining mailinglists, but not the mailinglist of unsubscription");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option unsubscription with one click yes, multiple mailinglist");
		$I->expect("not to see subscriber at remaining mailinglists, but at archived subscribers");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option unsubscription also to webmaster?
	 * Unsubscription
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkUnsubscriptionOptionUnsubscriptionToWebmaster(AcceptanceTester $I)
	{
		$I->wantTo("check option unsubscription also to webmaster no");
		$I->expect("not to get information mail at webmaster");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life

		$I->wantTo("check option unsubscription also to webmaster yes");
		$I->expectTo("get information mail at webmaster");

		// Set option values for mail at unsubscription
		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_unsubscription, 1);

		$I->clickAndWait(OptionsPage::$unsubscriptionToWebmasterYes, 1);
		$I->fillField(OptionsPage::$unsubscriptionSenderName, OptionsPage::$unsubscriptionSenderNameValue);
		$I->fillField(OptionsPage::$unsubscriptionSenderMail, OptionsPage::$unsubscriptionSenderMailValue);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

		// Subscribe
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		// Activate
		SubsView::activate($I, SubsView::$mail_fill_1);

		// Unsubscribe
		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);

		// Check for mail to webmaster
//		$I->seeInLastMail('Unsubscription of a subscriber');

		// Set option values for mail at unsubscription
		$I->amOnPage(MainView::$url);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);
		$I->clickAndWait(OptionsPage::$tab_unsubscription, 1);

		$I->clickAndWait(OptionsPage::$unsubscriptionToWebmasterNo, 1);
		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(MainView::$dashboard, 30);

	}

	/**
	 * Test method to check option unsubscription sender name
	 * Unsubscription
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkUnsubscriptionOptionUnsubscriptionSenderName(AcceptanceTester $I)
	{
		$I->wantTo("check option unsubscription sender name");
		$I->expectTo("see entered sender name at information mail to webmaster");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option unsubscription sender mail
	 * Unsubscription
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkUnsubscriptionOptionUnsubscriptionSenderMail(AcceptanceTester $I)
	{
		$I->wantTo("check option unsubscription sender mail");
		$I->expectTo("get information mail to webmaster at entered mail address");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option search field
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionSearchField(AcceptanceTester $I)
	{
		$I->wantTo("check option search field no");
		$I->expect("not to see search field at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option search field yes");
		$I->expectTo("see search field at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
		// @ToDo: Check functionality of field, probably at frontend suite
	}

	/**
	 * Test method to check option date filter
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionDateFilter(AcceptanceTester $I)
	{
		$I->wantTo("check option date filter no");
		$I->expect("not to see date filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option date filter yes");
		$I->expectTo("see date filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
		// @ToDo: Check functionality of field, probably at frontend suite
	}

	/**
	 * Test method to check option mailinglists filter
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionMailinglistsFilter(AcceptanceTester $I)
	{
		$I->wantTo("check option mailinglists filter no");
		$I->expect("not to see mailinglists filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option mailinglists filter yes");
		$I->expectTo("see mailinglists filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
		// @ToDo: Check functionality of field, probably at frontend suite
	}

	/**
	 * Test method to check option campaign filter
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionCampaignFilter(AcceptanceTester $I)
	{
		$I->wantTo("check option campaign filter no");
		$I->expect("not to see campaign filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option campaign filter yes");
		$I->expectTo("see campaign filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
		// @ToDo: Check functionality of field, probably at frontend suite
	}

	/**
	 * Test method to check option usergroup filter
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionUsergroupFilter(AcceptanceTester $I)
	{
		$I->wantTo("check option usergroup filter no");
		$I->expect("not to see usergroup filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option usergroup filter yes");
		$I->expectTo("see usergroup filter at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
		// @ToDo: Check functionality of field, probably at frontend suite
	}

	/**
	 * Test method to check option enable attachment
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionEnableAttachment(AcceptanceTester $I)
	{
		$I->wantTo("check option enable attachment lists view no");
		$I->expect("not to see icon for attachment at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option enable attachment lists view yes");
		$I->expectTo("see icon for attachment at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option check access
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionCheckAccess(AcceptanceTester $I)
	{
		$I->wantTo("check option check access no");
		$I->expectTo("see access restricted newsletter at frontend lists view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option check access yes");
		$I->expect("not to see access restricted newsletter at frontend lists view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option # newsletters to list
	 * Lists View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkListsOptionNumberNewslettersToList(AcceptanceTester $I)
	{
		$I->wantTo("check option # newsletters to list, limit 5");
		$I->expectTo("see 5 newsletters at frontend list view and 5 at list limit selection list");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option # newsletters to list, limit 10");
		$I->expectTo("see 10 newsletters at frontend list view and 10 at list limit selection list");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}


	/**
	 * Test method to check option enable attachment
	 * Single View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkSingleOptionEnableAttachment(AcceptanceTester $I)
	{
		$I->wantTo("check option enable attachment single view no");
		$I->expect("not to see icon for attachment at frontend single view");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option enable attachment single view yes");
		$I->expectTo("see icon for attachment at frontend single view");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
	}

	/**
	 * Test method to check option show subject as page title
	 * Single View
	 *
	 * @param AcceptanceTester $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @throws Exception
	 *
	 * @since   2.2.0
	 */
	public function checkSingleOptionShowSubjectAsPageTitle(AcceptanceTester $I)
	{
		$I->wantTo("check option show subject as page title no");
		$I->expect("not to see subject as page title");
		$I->amOnPage(MainView::$url);

		$I->wantTo("check option show subject as page title yes");
		$I->expectTo("see subject as page title");
		$I->amOnPage(MainView::$url);

		// @ToDo: Fill with life
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function setPermissions(AcceptanceTester $I)
	{
		$I->wantTo("Set Permissions BwPostman");
		$I->expectTo("see correct permissions set");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(Generals::$toolbar4['Options']);
		$I->waitForElementVisible(OptionsPage::$tab_permissions, 3);
		$I->click(OptionsPage::$tab_permissions);
		$I->waitForElementVisible(OptionsPage::$permissions_fieldset, 3);

		// get rule names
		$rules  = $I->getRuleNamesByComponentAsset('com_bwpostman');

		foreach(OptionsPage::$bwpm_groups as $groupname => $values)
		{
			$actions  = $values['permissions'];
			$group_id = $I->getGroupIdByName($groupname);

			$slider   = $this->selectPermissionsSliderForUsergroup($I, $groupname);

//			codecept_debug("Groupname: $groupname");
//			codecept_debug("Group ID: $group_id");

			// set permissions
			for ($i = 0; $i < count($rules); $i++)
			{
				$this->setSinglePermission($I, $rules, $i, $groupname, $group_id, $actions);
			}

			// apply
			$I->scrollTo(Generals::$joomlaHeader, 0, -100);
			$I->wait(1);
			$I->click(Generals::$toolbar['Save']);
			$I->waitForElementVisible(Generals::$alert_header, 5);
			$I->click(Generals::$systemMessageClose);
			$I->waitForElementNotVisible(Generals::$systemMessageClose, 3);

			$I->click(OptionsPage::$tab_permissions);
			$I->waitForElementVisible(OptionsPage::$permissions_fieldset, 3);

			// select usergroup
			$I->scrollTo($slider, 0, -100);
			$I->wait(1);

			$I->click($slider);
			$I->waitForElement($slider, 30);

			// check success
			foreach ($rules as $rule)
			{
				$this->checkSetPermissionsSuccess($I, $rule, $group_id, $groupname);
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
	 * @throws Exception
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
	 * @param                  $group_id
	 * @param                  $groupname
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.2.0
	 */
	protected function checkSetPermissionsSuccess(AcceptanceTester $I, $rule, $group_id, $groupname)
	{
		$value      = OptionsPage::$bwpm_group_permissions[$groupname][$rule];
		$rulesToScroll = array(
			'core.admin',
			'bwpm.send',
			'bwpm.admin.newsletter'
		);

		$scrollPos = "//*[@id='jform_rules_" . $rule . "_" . $group_id . "']";
		$identifier = $scrollPos . "/../../../td[3]/output/span";
//		codecept_debug("Identifier: $identifier");
//		codecept_debug("Value: $value");

		if (array_search($rule, $rulesToScroll) !== false)
		{
			$I->scrollTo($scrollPos, 0, -100);
			$I->wait(1);
		}
		$I->waitForElementVisible($scrollPos);

		$I->see($value, $identifier);
	}

	/**
	 * Method to set single permission
	 *
	 * @param AcceptanceTester $I
	 * @param                  $rules
	 * @param                  $i
	 * @param                  $groupname
	 * @param                  $group_id
	 * @param                  $actions
	 *
	 * @since 2.2.0
	 *
	 * @throws Exception
	 */
	protected function setSinglePermission(AcceptanceTester $I, $rules, $i, $groupname, $group_id, $actions)
	{
		$identifier = "//*[@id='jform_rules_" . $rules[$i] . "_" . $group_id . "']";
		$value      = $actions[$rules[$i]];
		$rulesToScroll = array(
			'core.admin',
			'bwpm.send',
			'bwpm.admin.newsletter'
		);

		if (array_search($rules[$i], $rulesToScroll) !== false)
		{
			$I->scrollTo($identifier, 0, -100);
			$I->wait(1);
		}

		$I->waitForElementVisible($identifier, 30);

		$I->click($identifier);
		$I->selectOption($identifier, $value);
	}

	/**
	 * Method to select permissions slider for a given usergroup
	 *
	 * @param AcceptanceTester $I
	 * @param                  $groupname
	 *
	 * @return string
	 *
	 * @since 2.2.0
	 *
	 * @throws Exception
	 */
	protected function selectPermissionsSliderForUsergroup(AcceptanceTester $I, $groupname)
	{
		$slider = sprintf(OptionsPage::$perm_slider, $groupname);
		$I->scrollTo($slider, 0, -100);
		$I->wait(1);

		$I->clickAndWait($slider, 1);
		$I->waitForElementVisible($slider, 30);

		return $slider;
	}
}
