<?php


/**
 * Class TestMailinglistsDetailsCest
 *
 * This class contains all methods to test manipulation of a single mailing list at back end
 */
class TestMailinglistsDetailsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function _login(\Page\Login $loginPage, \Page\Generals $Generals)
	{
		$loginPage->loginAsAdmin('Webmemsahib', 'BESU#PW§1', $Generals);
	}

	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester            $I
	 * @param   \Page\MainviewPage          $mainView
	 * @param   \Page\MailinglistEditPage   $MlEdit
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function CreateOneMailinglistCancelMainView(AcceptanceTester $I, \Page\MainviewPage $mainView, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create one mailinglist and cancel from main view");
		$I->amOnPage($mainView::$url);
		$I->wait(5);
		$I->see('BwPostman', $Generals::$pageTitle);
		$I->click($mainView::$addMailinglistButton);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Cancel']);
		$I->click($Generals::$submenu['BwPostman']);
		$I->waitForElement($Generals::$pageTitle);
		$I->see("BwPostman", $Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from main view, save it and go back to main view
	 *
	 * @param   AcceptanceTester            $I
	 * @param   \Page\MainviewPage          $mainView
	 * @param   \Page\MailinglistEditPage   $MlEdit
	 * @param   \Page\Generals              $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function CreateOneMailinglistCompleteMainView(AcceptanceTester $I, \Page\MainviewPage $mainView, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create one mailinglist complete from main view");
		$I->amOnPage($mainView::$url);
		$I->waitForElement($Generals::$pageTitle);
		$I->see('BwPostman', $Generals::$pageTitle);
		$I->click($mainView::$addMailinglistButton);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->seeInPopup('You have to enter a description for the mailinglist.');
//		$I->acceptPopup();
		$I->fillField($MlEdit::$title, "");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->seeInPopup('You have to enter a title for the mailinglist.');
//		$I->acceptPopup();
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save & Close']);
//		$I->waitForElement($Generals::$alert_header);
		$I->see("Message", $Generals::$alert_header);
		$I->see("Mailinglist saved successfully!", $Generals::$alert_msg);
	}

	/**
	 * Test method archive and delete a single mailing list
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
	 * @since   1.2.0
	 */
	public function ArchiveAndDeleteOneMailinglist(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\Generals $Generals)
	{
		$I->wantTo("Archive and delete one Mailinglist");
		$I->amOnPage($MlManage::$url);
//		$I->waitForElement($Generals::$pageTitle);
		$I->see('Mailinglists', $Generals::$pageTitle);
		$I->checkOption('#cb0');
		$I->click($MlManage::$toolbar['Archive']);
//		$I->waitForElement($Generals::$alert_header);
		$I->see("Message", $Generals::$alert_header);
		$I->see('The selected mailing list has been archived.', $Generals::$alert_msg);
		$I->wantTo("Delete one Mailinglist");
//		$I->click($Generals::$submenu['Archive']);
		$I->amOnPage('/administrator/index.php?option=com_bwpostman&view=archive&layout=newsletters');
		$I->see("Archive", $Generals::$pageTitle);
		$I->click('.//*[@id=\'j-main-container\']/div[2]/table/tbody/tr/td/ul/li[4]/button');
		$I->see('# subscribers');
		$I->see('001 General mailing list');
		$I->waitForElement('#cb0');
		$I->checkOption("#cb0");
		$I->executeJS("Joomla.submitbutton('archive.delete');");
//		$I->click('#toolbar-delete>button');
		$I->executeJS("window.confirm = function(){return true;};");
//		$I->seeInPopup('Do you wish to remove the selected mailinglist(s)?');
//		$I->acceptPopup();
		$I->waitForElement($Generals::$alert_header);
		$I->see('Message', $Generals::$alert_header);
		$I->see('The selected mailinglist has been removed.', $Generals::$alert_msg);
		$I->seeElement('.//*[@id=\'j-main-container\']/div[2]/table/tbody/tr/td/div/table/thead/tr/th[6]/a');
		$I->see("There are no data available", ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/div/table/tbody/tr/td/strong");
//		$I->click($Generals::$submenu['Mailinglists']);
		$I->amOnPage('/administrator/index.php?option=com_bwpostman&view=mailinglists');
		$I->waitForElement($Generals::$pageTitle);
		$I->see('Mailinglists', $Generals::$pageTitle);
//		$I->see('There are no data available', ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/strong");
	}

	/**
	 * Test method to create a single mailing list from list view and cancel creation
	 *
	 * @param   AcceptanceTester                $I
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\MailinglistEditPage       $MlEdit
	 * @param   \Page\Generals                  $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function CreateOneMailinglistCancelListView(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create one mailinglist cancel list view");
		$I->amOnPage($MlManage::$url);
		$I->click($MlManage::$toolbar['New']);
        $I->fillField($MlEdit::$title, "001 General mailing list");
        $I->fillField($MlEdit::$description, "A pretty description would be nice.");
        $I->click($MlEdit::$toolbar['Cancel']);
        $I->see("Mailinglists", $Generals::$pageTitle);
	}

	/**
	 * Test method to create a single mailing list from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester                $I
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\MailinglistEditPage       $MlEdit
	 * @param   \Page\Generals                  $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function CreateOneMailinglistListView(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create_one_mailinglist_list_view");
		$I->amOnPage($MlManage::$url);
		$I->click($MlManage::$toolbar['New']);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->acceptPopup('You have to enter a description for the mailinglist.');
		$I->fillField($MlEdit::$title, "");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->acceptPopup('You have to enter a title for the mailinglist.');
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->see("Message", $Generals::$alert_header);
		$I->see("Mailinglist saved successfully!", $Generals::$alert_msg);
		$I->see('Mailinglists', $Generals::$pageTitle);
		$MlManage::HelperArcDelOneMl($I, $MlManage, $Generals);
		$I->see('Mailinglists', $Generals::$pageTitle);
	}

	/**
	 * Test method to create same single mailing list twice from main view
	 *
	 * @param   AcceptanceTester          $I
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\MailinglistEditPage       $MlEdit
	 * @param   \Page\Generals                  $Generals
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function CreateMailinglistTwiceListView(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create mailinglist twice list view");
		$I->amOnPage($MlManage::$url);
		$I->click($MlManage::$toolbar['New']);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->see("Message", $Generals::$alert_header);
		$I->see("Mailinglist saved successfully!", $Generals::$alert_msg);
		$I->see('Mailinglists', $Generals::$pageTitle);
		$I->click($MlManage::$toolbar['New']);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->see("Error", $Generals::$alert_header);
		$I->see('Save failed with the following error:', $Generals::$alert_error);
		$I->click($MlEdit::$toolbar['Cancel']);
		$I->see("Mailinglists", $Generals::$pageTitle);
		$MlManage::HelperArcDelOneMl($I, $MlManage, $Generals);
		$I->see('Mailinglists', $Generals::$pageTitle);
	}

	/**
	 * Test method for Javascript while creating a single mailing list from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester                $I
	 * @param   \Page\MailinglistManagerPage    $MlManage
	 * @param   \Page\MailinglistEditPage       $MlEdit
	 * @param   \Page\Generals                  $Generals
	 *
	 * env     firefox
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function CreateOneMailinglistListViewJSTest(AcceptanceTester $I, \Page\MailinglistManagerPage $MlManage, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->changeBrowser('firefox');

		$I->wantTo("Create_one_mailinglist_list_view");
		$I->amOnPage($MlManage::$url);
		$I->click($MlManage::$toolbar['New']);
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save']);
//		$I->executeJS("window.confirm = function(msg){return true;};");
		$I->acceptPopup('You have to enter a description for the mailinglist.');
		$I->fillField($MlEdit::$title, "");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save']);
//		$I->executeJS("window.confirm = function(msg){return true;};");
		$I->acceptPopup('You have to enter a title for the mailinglist.');
		$I->fillField($MlEdit::$title, "001 General mailing list");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->see("Message", $Generals::$alert_header);
		$I->see("Mailinglist saved successfully!", $Generals::$alert_msg);
		$I->see('Mailinglists', $Generals::$pageTitle);
		$MlManage::HelperArcDelOneMl($I, $MlManage, $Generals);
		$I->see('Mailinglists', $Generals::$pageTitle);
	}
*/
	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromAdmin();
	}

	public function _failed (AcceptanceTester $I){

	}


}
