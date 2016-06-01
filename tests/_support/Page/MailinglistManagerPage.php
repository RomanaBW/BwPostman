<?php
namespace Page;

class MailinglistManagerPage
{
    // include url of current page
    public static $url = '/administrator/index.php?option=com_bwpostman&view=mailinglists';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Array of toolbar id values for this page
     *
     * @var    array
     *
     * @since  2.0.0
     */
    public static $toolbar = array (
        'New' => './/*[@id=\'toolbar-new\']/button',
        'Edit' => './/*[@id=\'toolbar-edit\']/button',
        'Publish' => './/*[@id=\'toolbar-publish\']/button',
        'Unpublish' => './/*[@id=\'toolbar-unpublish\']/button',
        'Archive' => './/*[@id=\'toolbar-archive\']/button',
        'Help' => './/*[@id=\'toolbar-help\']/button',
    );

    /**
     * Array of filter id values for this page
     *
     * @var array
     * @since 3.2
     */
    public static $filters = array(
        'Sort Table By:' => 'list_fullordering',
        '5' => 'list_limit',
        '10' => 'list_limit',
        '20' => 'list_limit',
        'Select Status' => 'filter_state',
        'Select Client' => 'filter_client_id',
        'Select Category' => 'filter_category_id',
        'Select Language' => 'filter_language'
    );

    /**
     * Hepler method archive and delete a single mailing list, without testing, that is done before
     *
     * @param   AcceptanceTester                $I
     * @param   \Page\MailinglistManagerPage    $MlManage
     * @param   \Page\Generals                  $Generals
     *
     * @before  _login
     *
     * @depends CreateOneMailinglistCompleteMainView
     *
     * @after   _logout
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public static function HelperArcDelOneMl(\AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\Generals $Generals)
		{
			$I->wantTo("Archive and delete one Mailinglist, helper method");
			$I->amOnPage($MlManage::$url);
			$I->waitForElement($Generals::$pageTitle);
			$I->checkOption('#cb0');
			$I->click($MlManage::$toolbar['Archive']);
			$I->waitForElement($Generals::$alert_header);
			$I->amOnPage('/administrator/index.php?option=com_bwpostman&view=archive&layout=newsletters');
			$I->see("Archive", $Generals::$pageTitle);
			$I->click('.//*[@id=\'j-main-container\']/div[2]/table/tbody/tr/td/ul/li[4]/button');
			$I->waitForElement('#cb0');
			$I->checkOption("#cb0");
			$I->executeJS("Joomla.submitbutton('archive.delete');");
//			$I->makeScreenshot('archive_page');
	//		$I->click('#toolbar-delete>button');
			$I->executeJS("window.confirm = function(){return true;};");
	//		$I->seeInPopup('Do you wish to remove the selected mailinglist(s)?');
	//		$I->acceptPopup();
			$I->waitForElement($Generals::$alert_header);
	//		$I->click($Generals::$submenu['Mailinglists']);
			$I->amOnPage('/administrator/index.php?option=com_bwpostman&view=mailinglists');
			$I->waitForElement($Generals::$pageTitle);
			$I->amOnPage($MlManage::$url);
	}
}
