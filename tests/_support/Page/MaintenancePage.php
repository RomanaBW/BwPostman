<?php
namespace Page;

use Exception;
use Page\MainviewPage as MainView;

/**
 * Class MaintenancePage
 *
 * @package Page
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
class MaintenancePage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url          = "/administrator/index.php?option=com_bwpostman&view=maintenance";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $forum_url    = "https://www.boldt-webservice.de/de/forum/bwpostman.html";

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkTablesButton    = '//*/div[contains(@class,"bw-icons")]/div/div[1]';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $saveTablesButton     = '//*/div[contains(@class,"bw-icons")]/div/div[2]';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $restoreTablesButton  = '//*/div[contains(@class,"bw-icons")]/div/div[3]';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $settingsButton       = '//*/div[contains(@class,"bw-icons")]/div/div[4]';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $forumButton          = "//*/div/a/span[contains(text(),'BwPostman Forum')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkBackButton      = "//*/a[@id='toolbar-back']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $cancelSettingsButton = "//*[@id='toolbar-cancel']/button";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $heading              = "Maintenance";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkHeading         = "Check tables";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $headingRestoreFile   = "//*[@id='adminForm']/fieldset/div[@class='h2']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $headingSettings      = "BwPostman Configuration";


	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $buttonGetFile        = "//*[@id='restorefile']";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $buttonStartRestore   = "//*/input[@name='submitbutton']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step1Field           = "//*[@id='step1'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step2Field           = "//*[@id='step2'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step3Field           = "//*[@id='step3'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step4Field           = "//*[@id='step4'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step5Field           = "//*[@id='step5'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step6Field           = "//*[@id='step6'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step7Field           = "//*[@id='step7'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step8Field           = "//*[@id='step8'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step9Field           = "//*[@id='step9'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step10Field          = "//*[@id='step10'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step11Field          = "//*[@id='step11'][contains(@class, 'alert-success')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step7SuccessClass    = "//*[@id='step6'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step11SuccessClass   = "//*[@id='step11'][contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step7SuccessMsg      = "User IDs of subscribers are alright";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $step11SuccessMsg     = "Check: Check asset ids and user ids...";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $statisticsUnsentNewsletters     = "//*[@id='generals']/div/table/tbody/tr[1]/td[2]/b/a";

	/*
	 * Success messages
	 */

	/**
	 * @var array
	 *
	 * @since 3.1.3
	 */
	public static $successTableArray     = array(
		'#__bwpostman_campaigns',
		'#__bwpostman_campaigns_mailinglists',
		'#__bwpostman_mailinglists',
		'#__bwpostman_newsletters',
		'#__bwpostman_newsletters_mailinglists',
		'#__bwpostman_sendmailcontent',
		'#__bwpostman_sendmailqueue',
		'#__bwpostman_subscribers',
		'#__bwpostman_subscribers_mailinglists',
//		'#__bwpostman_tc_schedule',
//		'#__bwpostman_tc_settings',
		'#__bwpostman_templates',
		'#__bwpostman_templates_tags',
		'#__bwpostman_templates_tpl',
	);

	/**
	 * @var array
	 *
	 * @since 3.1.3
	 */
	public static $assetTableArray     = array(
		'#__bwpostman_campaigns',
		'#__bwpostman_mailinglists',
		'#__bwpostman_newsletters',
		'#__bwpostman_subscribers',
		'#__bwpostman_templates',
	);

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successAllIdentifierResult     = "//*[@id='result']/p[contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $resultIdentifierId     = "//*[@id='result']";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successIdentifierResult     = "//*[@id='result']/p[@class='text-success']";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextAllResult     = "The check encountered no errors.";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextReadBackup     = "Backup data read successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextRevUsergroups     = "Revision of user groups successful";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextDelAssets     = "Deleting of assets successful";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextRepairAssets     = "Repairing of assets successful";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextDeleteTables     = "table `%s` deleted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextCreateTables     = "table `%s` created successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextRestoreTables     = "table `%s` restored successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextColumns     = "The column names of table `%s` are in order";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextAttributes     = "The attributes of the columns of table `%s` are in order";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $successTextAssets     = "Asset IDs of table `%s` are alright";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextEngine     = "ENGINE of table `%s` adjusted successfully";


	/*
	 * Error and warning handling identifiers
	 */

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $step7FieldError           = "//*[@id='step7'][contains(@class, 'alert-danger')]";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $errorIdentifierSetBack     = "//*[@id='error']/p[contains(@class, 'alert-danger')]";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $errorIdentifierResult     = "//*[@id='result']/p[contains(@class, 'text-danger')]";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningIdentifier     = "//*[@id='result']/p[contains(@class, 'text-warning')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $versionWarningIdentifier     = "//*[@id='result']/p[contains(@class, 'alert-warning')]";

	/*
	 * Error and warning handling messages
	 */

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $errorTextSetBack     = "Error while restoring tables! Tables are set back to initial condition!";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $errorTextDefault     = "Creating table `#__bwpostman_campaigns` failed with error message Invalid default value for 'asset_id'";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $errorTextPrimary     = "Creating table `#__bwpostman_campaigns` failed with error message Key column 'id' doesn't exist in table";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextIncrement     = "The auto increment value of table `#__bwpostman_campaigns` is missing or wrong. Trying to adjust the correct auto increment value...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextIncrement     = "Auto increment value of table `#__bwpostman_campaigns` created successfully.";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextCollation     = "The attribute(s) 'collation' of column 'title' of table `#__bwpostman_mailinglists` is/are not as expected. Trying to adjust defective attribute(s)...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextCollation     = "Attribute 'collation' of column 'title' of table `#__bwpostman_mailinglists` adjusted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextColumn     = "The attribute(s) 'Field' of column 'published' of table `#__bwpostman_campaigns` is/are not as expected. Trying to adjust defective attribute(s)...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextColumn     = "Attribute 'Field' of column 'published' of table `#__bwpostman_campaigns` adjusted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextType     = "The attribute(s) 'Type,Default' of column 'title' of table `#__bwpostman_campaigns` is/are not as expected. Trying to adjust defective attribute(s)...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextType     = "Attribute 'Type' of column 'title' of table `#__bwpostman_campaigns` adjusted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextNull     = "The attribute(s) 'Null' of column 'asset_id' of table `#__bwpostman_campaigns` is/are not as expected. Trying to adjust defective attribute(s)...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextNull     = "Attribute 'Null' of column 'asset_id' of table `#__bwpostman_campaigns` adjusted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextPublishe     = "The column 'publishe' of table `#__bwpostman_campaigns` is installed but not needed. Trying to delete obsolete column...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextPublishe     = "Column 'publishe' of table `#__bwpostman_campaigns` deleted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextPublished     = "Column 'published' of table `#__bwpostman_campaigns` is not installed, but is needed urgently. Trying to install missing column...";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $repairTextPublished     = "Column 'published' of table `#__bwpostman_campaigns` created successfully";

	/**
	 * @var string
	 *
	 * @since 4.0.4
	 */
	public static $warningTextDefault     = "The attribute(s) 'Default,Null' of column 'created_date' of table `#__bwpostman_templates` is/are not as expected. Trying to adjust defective attribute(s)...";

	/**
	 * @var string
	 *
	 * @since 4.0.4
	 */
	public static $repairTextDefault     = "Attribute 'Default' of column 'created_date' of table `#__bwpostman_templates` adjusted successfully";

	/**
	 * @var string
	 *
	 * @since 3.1.3
	 */
	public static $warningTextVersion     = "Installed version of BwPostman is lower than version of backup. Column check is not performed to prevent data loss. Please Update to backed up version of BwPostman.";


	/**
	 * Test method to restore tables
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   boolean             $compressed   Is the backup compressed?
	 * @param   string              $filename     regular backup or having modifications? Last case needs file name transmitted.
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function restoreTables(\AcceptanceTester $I, $compressed = false, $filename = '')
	{
		$I->wantTo("Restore tables");
		$I->expectTo("see 'Result check okay'");
		$I->amOnPage(MainView::$url);

		$I->click(MainView::$maintenanceButton);

		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(self::$heading);

		$I->click(self::$restoreTablesButton);
		$I->waitForElement(self::$headingRestoreFile, 30);

		if($filename === '')
		{
			$filename = self::calculateFilename($compressed);
		}

		codecept_debug('Filename for restore: ' . $filename);

		$I->attachFile(self::$buttonGetFile, $filename);

		$I->click(self::$buttonStartRestore);
		$I->dontSeeElement(Generals::$alert_error);

		// Check result of regular backup
		if(strpos($filename, 'error') === false && strpos($filename, 'modified') === false)
		{
			self::checkRegularResult($I);
		}

		// Check result of modified backup, which can be repaired
		if(strpos($filename, 'modified_simple') !== false)
		{
			self::checkModifiedSimpleResult($I);
		}

		// Check result of modified backup with version greater than installed version
		if(strpos($filename, 'modified_version') !== false)
		{
			self::checkModifiedVersionResult($I);
		}

		// Check result of modified backup, which cannot be repaired
		if(strpos($filename, 'error') !== false)
		{
			self::checkErrorResult($I, $filename);
		}

		$I->click(self::$checkBackButton);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(self::$heading, Generals::$pageTitle);

		$I->amOnPage(MainView::$url);

		$I->waitForElementVisible(self::$statisticsUnsentNewsletters, 5);
		$I->see("53", self::$statisticsUnsentNewsletters);
	}

	/**
	 * Method to calculate file name for regular backup file
	 *
	 * @param bool   $compressed
	 *
	 * @return string
	 *
	 * @since 3.1.3
	 */
	private static function calculateFilename(bool $compressed): string
	{
		$bwpm_version = getenv('BWPM_VERSION_TO_TEST');

		if (substr_count($bwpm_version, ".") == 0)
		{
			$bwpm_version_underline = $bwpm_version[0] . '_' . $bwpm_version[1] . '_' . $bwpm_version[2];
		}
		else
		{
			$bwpm_version_underline = str_replace('.', '_', $bwpm_version);
		}

		$filename = 'BwPostman_' . $bwpm_version_underline . '_Tables.xml';

		if ($compressed)
		{
			$filename .= ".zip";
		}

		return $filename;
	}

/**
 * Method to check result of regular backup, no modifications expected while restore
 *
 * @param \AcceptanceTester $I
 *
 * @throws \Exception
 *
 * @since 3.1.3
 */
	private static function checkRegularResult(\AcceptanceTester $I)
	{
		$I->waitForElementVisible(self::$step1Field, 30);
		$I->waitForElementVisible(self::$step2Field, 30);
		$I->waitForElementVisible(self::$step3Field, 30);
		$I->waitForElementVisible(self::$step4Field, 300);
		$I->waitForElementVisible(self::$step5Field, 30);
		$I->waitForElementVisible(self::$step6Field, 300);
		$I->waitForElementVisible(self::$step7Field, 30);
		$I->waitForElementVisible(self::$step8Field, 30);
		$I->waitForElementVisible(self::$step9Field, 30);
		$I->waitForElementVisible(self::$step10Field, 30);
		$I->waitForElementVisible(self::$step11Field, 180);
		$I->waitForElementVisible(self::$step11SuccessClass, 30);
		$I->see(self::$step11SuccessMsg, self::$step11SuccessClass);
		$I->wait(10);

		$resultsOkay = $I->grabMultiple(self::$successIdentifierResult);
		$resultsWarn = $I->grabMultiple(self::$warningIdentifier);

//		codecept_debug($resultsWarn);
		$I->assertEquals(count($resultsWarn), 0);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextDelAssets, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextRepairAssets, $resultsOkay);
		$I->assertEquals($found, true);

		foreach (self::$successTableArray as $table)
		{
//			codecept_debug('Delete table assets: ' . $table);
			$found = in_array(sprintf(self::$successTextDelAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);

//			codecept_debug('Create table: ' . $table);
			$found = in_array(sprintf(self::$successTextCreateTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

//			codecept_debug('Restore table: ' . $table);
			$found = in_array(sprintf(self::$successTextRestoreTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

//			codecept_debug('Column check table: ' . $table);
			$found = in_array(sprintf(self::$successTextColumns, $table), $resultsOkay);
			$I->assertEquals($found, true);

//			codecept_debug('Attributes check table: ' . $table);
			$found = in_array(sprintf(self::$successTextAttributes, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}

		foreach (self::$assetTableArray as $table)
		{
//			codecept_debug('Create table assets: ' . $table);
			$found = in_array(sprintf(self::$successTextAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}

		$I->see(MaintenancePage::$successTextAllResult, MaintenancePage::$successAllIdentifierResult);
	}

	/**
	 * Method to check result of backup with modifications, which can be repaired
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since 3.1.3
	 */
	private static function checkModifiedSimpleResult(\AcceptanceTester $I)
	{
		$I->waitForElementVisible(self::$step1Field, 30);
		$I->waitForElementVisible(self::$step2Field, 30);
		$I->waitForElementVisible(self::$step3Field, 30);
		$I->waitForElementVisible(self::$step4Field, 300);
		$I->waitForElementVisible(self::$step5Field, 30);
		$I->waitForElementVisible(self::$step6Field, 300);
		$I->waitForElementVisible(self::$step7Field, 30);
		$I->waitForElementVisible(self::$step8Field, 30);
		$I->waitForElementVisible(self::$step9Field, 60);
		$I->waitForElementVisible(self::$step10Field, 60);
		$I->waitForElementVisible(self::$step11Field, 180);
		$I->waitForElementVisible(self::$step11SuccessClass, 30);
		$I->see(self::$step11SuccessMsg, self::$step11SuccessClass);
		$I->wait(10);

		$resultsOkay = $I->grabMultiple(self::$successIdentifierResult);
		$resultsWarn = $I->grabMultiple(self::$warningIdentifier);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextDelAssets, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextRepairAssets, $resultsOkay);
		$I->assertEquals($found, true);

		foreach (self::$successTableArray as $table)
		{
			$found = in_array(sprintf(self::$successTextDelAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextCreateTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextRestoreTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$repairTextEngine, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextColumns, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextAttributes, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}

		codecept_debug('Check for increment warnings');
		$found = in_array(self::$warningTextIncrement, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for increment okay');
		$found = in_array(self::$repairTextIncrement, $resultsOkay);
		$I->assertEquals($found, true);

		// @ToDo: Assertion should be true, but since new pipeline database version this probably is repaired by the database themselves
//		codecept_debug('Check for collation warnings');
//		$found = in_array(self::$warningTextCollation, $resultsWarn);
//		$I->assertEquals($found, true);

//		codecept_debug('Check for collation okay');
//		$found = in_array(self::$repairTextCollation, $resultsOkay);
//		$I->assertEquals($found, true);

		codecept_debug('Check for Text warnings');
		$found = in_array(self::$warningTextColumn, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for text okay');
		$found = in_array(self::$repairTextColumn, $resultsOkay);
		$I->assertEquals($found, true);

		codecept_debug('Check for type warnings');
		$found = in_array(self::$warningTextType, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for type okay');
		$found = in_array(self::$repairTextType, $resultsOkay);
		$I->assertEquals($found, true);

		codecept_debug('Check for null warnings');
		$found = in_array(self::$warningTextNull, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for null okay');
		$found = in_array(self::$repairTextNull, $resultsOkay);
		$I->assertEquals($found, true);

		codecept_debug('Check for published warnings');
		$found = in_array(self::$warningTextPublishe, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for published okay');
		$found = in_array(self::$repairTextPublishe, $resultsOkay);
		$I->assertEquals($found, true);

		codecept_debug('Check for text published warnings');
		$found = in_array(self::$warningTextPublished, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for text published okay');
		$found = in_array(self::$repairTextPublished, $resultsOkay);
		$I->assertEquals($found, true);

		codecept_debug('Check for text default warnings');
		$found = in_array(self::$warningTextDefault, $resultsWarn);
		$I->assertEquals($found, true);

		codecept_debug('Check for text default okay');
		$found = in_array(self::$repairTextDefault, $resultsOkay);
		$I->assertEquals($found, true);

		foreach (self::$assetTableArray as $table)
		{
			codecept_debug('Check for table success okay');
			$found = in_array(sprintf(self::$successTextAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}

		$I->see(MaintenancePage::$successTextAllResult, MaintenancePage::$successAllIdentifierResult);
	}

	/**
	 * Method to check result of backup with modifications, which can be repaired
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since 3.1.3
	 */
	private static function checkModifiedVersionResult(\AcceptanceTester $I)
	{
		$I->waitForElementVisible(self::$step1Field, 30);
		$I->waitForElementVisible(self::$step2Field, 30);
		$I->waitForElementVisible(self::$step3Field, 30);
		$I->waitForElementVisible(self::$step4Field, 300);
		$I->waitForElementVisible(self::$step5Field, 30);
		$I->waitForElementVisible(self::$step6Field, 300);
		$I->waitForElementVisible(self::$step7Field, 30);
		$I->waitForElementVisible(self::$step8Field, 30);
		$I->waitForElementVisible(self::$step9Field, 30);
		$I->waitForElementVisible(self::$step10Field, 30);
		$I->waitForElementVisible(self::$step11Field, 180);
		$I->waitForElementVisible(self::$step11SuccessClass, 30);
		$I->see(self::$step11SuccessMsg, self::$step11SuccessClass);
		$I->wait(10);

		$resultsOkay = $I->grabMultiple(self::$successIdentifierResult);
		$resultsWarn = $I->grabMultiple(self::$warningIdentifier);
		$resultsWarnVersion = $I->grabMultiple(self::$versionWarningIdentifier);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextReadBackup, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextDelAssets, $resultsOkay);
		$I->assertEquals($found, true);

		$found = in_array(self::$successTextRepairAssets, $resultsOkay);
		$I->assertEquals($found, true);

		foreach (self::$successTableArray as $table)
		{
			$found = in_array(sprintf(self::$successTextDelAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextCreateTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$successTextRestoreTables, $table), $resultsOkay);
			$I->assertEquals($found, true);

			$found = in_array(sprintf(self::$repairTextEngine, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}
//codecept_debug('TP');

		$found = in_array(self::$warningTextVersion, $resultsWarnVersion);
		$I->assertEquals($found, true);

		$found = in_array(self::$warningTextIncrement, $resultsWarn);
		$I->assertEquals($found, true);

		$found = in_array(self::$repairTextIncrement, $resultsOkay);
		$I->assertEquals($found, true);

		foreach (self::$assetTableArray as $table)
		{
			$found = in_array(sprintf(self::$successTextAssets, $table), $resultsOkay);
			$I->assertEquals($found, true);
		}

		$I->see(MaintenancePage::$successTextAllResult, MaintenancePage::$successAllIdentifierResult);
	}

	/**
	 *
	 * Method to check result of backup with modifications, which cannot be repaired
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $filename
	 *
	 * @throws \Exception
	 * @since 3.1.3
	 */
	private static function checkErrorResult(\AcceptanceTester $I, string $filename)
	{
		$I->waitForElementVisible(self::$step1Field, 30);
		$I->waitForElementVisible(self::$step2Field, 30);
		$I->waitForElementVisible(self::$step3Field, 30);
		$I->waitForElementVisible(self::$step4Field, 300);
		$I->waitForElementVisible(self::$step5Field, 60);
		$I->waitForElementVisible(self::$step6Field, 300);

		$I->waitForElementVisible(self::$step7FieldError, 30);
		$I->dontSeeElement(self::$step7Field);
		$I->dontSeeElement(self::$step8Field);
		$I->dontSeeElement(self::$step9Field);
		$I->dontSeeElement(self::$step10Field);
		$I->dontSeeElement(self::$step11Field);

		$I->see(self::$successTextReadBackup, self::$successIdentifierResult);
		$I->see(self::$successTextRevUsergroups, self::$successIdentifierResult);
		$I->see(self::$successTextDelAssets, self::$successIdentifierResult);
		$I->see(self::$successTextRepairAssets, self::$successIdentifierResult);

		$I->see(self::$errorTextSetBack, self::$errorIdentifierSetBack);

		if(strpos($filename, 'default') !== false)
		{
			$I->see(self::$errorTextDefault, self::$errorIdentifierResult);
		}

		if(strpos($filename, 'primary') !== false)
		{
			$I->see(self::$errorTextPrimary, self::$errorIdentifierResult);
		}

		$I->dontSee(MaintenancePage::$successTextAllResult, MaintenancePage::$successAllIdentifierResult);
	}
}
