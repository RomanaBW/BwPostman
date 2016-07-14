<?php
namespace Page;

use Codeception\Module\WebDriver;

class MailinglistManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 */
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
	 * Array of sorting criteria values for this page
	 * This array meets table headings
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_criteria = array(
		'description' => 'Description',
		'published' => 'published',
		'access' => 'Access level',
		'subscribers' => '# subscribers',
		'id' => 'ID',
		'title' => 'Title'
	);

	/**
	 * Array of sorting criteria values for this page
	 * This array select list values
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_criteria_select = array(
		'description' => 'Description',
		'published' => 'Status',
		'access' => 'Access',
		'subscribers' => '# subscribed mailing lists',
		'id' => 'ID',
		'title' => 'Title'
	);

	/**
	 * Array of criteria to select from database
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $select_criteria = array(
		'description' => 'a.description',
		'published' => 'a.published',
		'access' => 'a.access',
		'subscribers' => 'subscribers',
		'id' => 'a.id',
		'title' => 'a.title'
	);

	/**
	 * Array of criteria to select from database
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_arrows = array(
		'up' => 'icon-arrow-up-3',
		'down' => 'icon-arrow-down-3'
	);

	/**
	 * Location of selected value in sort select list
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $select_list_selected_location = './/*[@id=\'list_fullordering_chzn\']/a/span';

	/**
	 * Location of table column
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $table_headcol_link_location = ".//*[@id='j-main-container']/div[2]/table/thead/tr/th[%s]/a";

	/**
	 * Location of table column arrow
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $table_headcol_arrow_location = ".//*[@id='j-main-container']/div[2]/table/thead/tr/th[%s]/a/span";

	/**
     * Array of filter id values for this page
     *
     * @var array
     *
     * @since 2.0.0
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
     * Helper method archive and delete a single mailing list, without testing, that is done before
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
    public static function HelperArcDelOneMl(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\Generals $Generals)
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
