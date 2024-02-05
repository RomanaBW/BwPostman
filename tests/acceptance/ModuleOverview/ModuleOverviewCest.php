<?php
use Page\Generals as Generals;
use Page\Login;
use Page\ModuleOverviewPage as Helper;
use Page\SubscriberviewPage as SubsView;
use Page\InstallationPage;

/**
 * Class ModuleOverviewCest
 *
 * This class contains all methods to test subscription by module
 *
 * !!!!Requirements: 3 mailinglists available in frontend at minimum!!!!
 *
 *  * @copyright (C) 2020 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   4.0.0
 */
class ModuleOverviewCest
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
	 * @since   4.0.0
	 */
	public function _login(Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to ensure overview module is set up and activated
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
	 * @since   4.0.0
	 */
	public function setupOverviewModule(AcceptanceTester $I)
	{
		$I->wantTo("Activate and setup overview module");
		$I->expectTo('get module active at frontend');

		// Open Module configuration page
		$I->amOnPage(InstallationPage::$siteModulesUrl);
		$I->fillField(Generals::$search_field, 'Newsletter Overview');
		$I->clickAndWait(InstallationPage::$search_button, 1);

		$I->click(InstallationPage::$overviewModuleLine);
		$I->waitForElement(InstallationPage::$positionField, 5);

		// Fill module tab
		$I->click(InstallationPage::$overviewTabs['Module']);
		$I->waitForElement(InstallationPage::$publishedField);

		$I->clickAndWait(InstallationPage::$positionField, 1);
		$I->clickAndWait(InstallationPage::$positionValue, 1);

		$I->selectOption(InstallationPage::$publishedField, "Published");

		// Fill menu assignment tab
		$I->click(InstallationPage::$overviewTabs['Menu Assignment']);
		$I->waitForElement(InstallationPage::$menuAssignmentList);

		$I->selectOption(InstallationPage::$menuAssignmentList, "On all pages");

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElement(Generals::$alert_success4, 5);

		// Preset module options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed without access check
	 * 12: one year
	 * 4:  four months
	 * 1:  last months, which doesn't have sent newsletters
	 * 0:  display all, no limit of months
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsAll(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE without access check");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");

		// Call page with 12 months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 11));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 12));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(20, $nbrNls);

		// Call page with 4 months
		$I->setManifestOption('mod_bwpostman_overview', 'count', '4');

		$I->reloadPage();
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 3));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 4));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(4, $nbrNls);

		// Call page with 1 month
		$I->setManifestOption('mod_bwpostman_overview', 'count', '1');

		$I->reloadPage();
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->see(Helper::$mod_count_0_message, Helper::$mod_content_position);

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(0, $nbrNls);

		// Call page with all months
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');

		$I->reloadPage();
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 20));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 21));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(29, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed with access check on all newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsRestricted(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE with access check");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "yes");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 19));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 20));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(28, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed without access check without archived newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyNotArchived(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE without archived");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'all_not_arc');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 16));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 17));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(22, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed without access check without archived and without expired newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsNotArchivedNotExpired(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE without archived, without expired");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'not_arc_down');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 14));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 15));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(19, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed without access check without expired newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsNotArchivedButExpired(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE without archived but with expired");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'not_arc_but_down');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 2));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 3));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(3, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only archived newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyArchived(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE only archived");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'arc');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 6));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 7));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(7, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only expired newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyExpired(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE only expired");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'down');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 3));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 4));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(4, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only archived *and* expired newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsArchivedAndExpired(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE archived and expired");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'arc_and_down');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 1));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 2));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(1, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only archived *or* expired newsletters
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsArchivedOrExpired(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE archived or expired");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'arc_or_down');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 8));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 9));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(10, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only mailinglists
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyAllMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE only mailinglists");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$selectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'all');

		// Call page with all mailinglists selected
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 17));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 18));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(26, $nbrNls);

		// Call page with some mailinglists selected
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', Helper::$someSelectedMls);
		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 14));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 15));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(18, $nbrNls);

		// Call page with option all
		$I->setManifestOption('mod_bwpostman_overview', 'ml_selected_all', 'yes');

		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 17));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 18));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(26, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only usergroups
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyAllUsergroups(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE only usergroups");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$selectedUgs);
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'all');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 7));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 8));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(10, $nbrNls);


		// Call page with some user groups selected
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', Helper::$someSelectedUgs);
		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 7));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 8));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(9, $nbrNls);

		// Call page with option all
		$I->setManifestOption('mod_bwpostman_overview', 'groups_selected_all', 'yes');

		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 5));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 6));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(6, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check the number of months displayed only campaigns
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModuleCheckNumberOfMonthsOnlyAllCampaigns(AcceptanceTester $I)
	{
		$I->wantTo("check the number of months displayed at FE only usergroups");
		$I->expectTo('see the appropriate number of months and count of newsletters');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', array());
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$selectedCams);
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "no");
		$I->setManifestOption('mod_bwpostman_overview', 'count', '0');
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', 'all');

		// Call page with all months
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_title_position, 0, -100);
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 20));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 21));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(29, $nbrNls);

		// Call page with some campaigns selected
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', Helper::$someSelectedCams);
		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 7));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 8));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(8, $nbrNls);

		// Call page with option all
		$I->setManifestOption('mod_bwpostman_overview', 'cam_selected_all', 'yes');

		$I->reloadPage();
		$I->wait(1);
		$I->dontSeeInSource(Generals::$warningMessage);
		$I->dontSeeInSource(Generals::$noticeMessage);
		$I->seeElement(sprintf(Helper::$mod_count_n, 3));
		$I->dontSeeElement(sprintf(Helper::$mod_count_n, 4));

		// Check summarized number of newsletters
		$nbrNls = $this->countNewsletters($I);
		$I->assertEquals(3, $nbrNls);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModule12(AcceptanceTester $I)
	{
		$I->wantTo("");
		$I->expectTo('');

		Helper::presetModuleOptions($I);

	}

	/**
	 * Test method to
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function OverviewModule13(AcceptanceTester $I)
	{
		$I->wantTo("");
		$I->expectTo('');

		/*
		 * *** Frage: Kann show_type für nachfolgende Tests weg gelassen werden?    ***
		 * Erste schnelle Antwort: Ja, vermutlich, weil ich die Prüfung auf den Typ ja schon habe und der am Ende stattfindet.
		 * Dort habe ich die Newsletter, die über die Bereiche definiert sind, alle zusammen als erstes Ergebnis, in der
		 * Query kommt dann eben noch das Datum ab und der Typ dazu.
		 *
		 * *** Frage: Was passiert, wenn bei der Anzahl der Monate nichts eingetragen ist? ***
		 *
		 * Tests mit
		 * - ml_selected_all
		 * - groups_selected_all
		 * - cam_selected_all
		 * da wird immer auf Access geprüft. Ergibt also andere Ergebnisse als mit XX_available und alles ausgewählt.
		 *
		 * Es sollten auch Tests gemacht werden, die nur Mailinglisten, nur Gruppen und nur Kampagnen ausgeählt haben.
		 * Eventuell auch Tests mit Kombinationen aus 2 der 3 Bereiche.
		 *
		 * Obige Tests haben immer alle MLs, Gruppen und Cams ausgewählt, da sollte vielleicht auch mal auf weniger getestet werden.
		 *
		 * Menu item:
		 * eim paar Auswahlen im Modul einstellen und prüfen, dann andere Auswahlen in Menu item, darauf umstellen und auch prüfen.
		 *
		 */

	}

	/**
	 * Method to get the lines of months, extract number of newsletters and summarize them
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  integer
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function countNewsletters(AcceptanceTester $I)
	{
		$nbrNls = 0;

		$lines = $I->grabMultiple(Helper::$mod_count_li);

		foreach ($lines as $line)
		{
			$start = strpos($line, '(') + 1;
			$end = strpos($line, ')');

			if ($start !== false && $end !== false)
			{
				$count = (int) substr($line, $start, $end);

				$nbrNls += $count;
codecept_debug("Sum: " . $nbrNls);
			}
		}

		return $nbrNls;
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester $I
	 * @param Login              $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function _logout(AcceptanceTester $I, Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
