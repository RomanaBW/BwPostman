<?php


/**
 * Class TestMailinglistsListsCest
 *
 * This class contains all methods to test list view of mailing lists at back end
 */
class TestMailinglistsListsCest
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
	 * Test method to create a single mailing list from main view, save it and go back to list view
	 *
	 * @param   \Step\Acceptance\Admin          $I
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
	public function CreateOneMailinglistListView(\Step\Acceptance\Admin $I, \Page\MailinglistManagerPage $MlManage, \Page\MailinglistEditPage $MlEdit, \Page\Generals $Generals)
	{
		$I->wantTo("Create_one_mailinglist_list_view");
		$I->amOnPage($MlManage::$url);
		$I->click($MlManage::$toolbar['New']);
		$I->fillField($MlEdit::$title, "general mailing list");
		$I->click($MlEdit::$toolbar['Save']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->acceptPopup('You have to enter a description for the mailinglist.');
		$I->fillField($MlEdit::$title, "");
		$I->fillField($MlEdit::$description, "A pretty description would be nice.");
		$I->click($MlEdit::$toolbar['Save']);
		$I->executeJS("window.confirm = function(msg){return true;};");
//		$I->acceptPopup('You have to enter a title for the mailinglist.');
		$I->fillField($MlEdit::$title, "general mailing list");
		$I->click($MlEdit::$toolbar['Save & Close']);
		$I->see("Message", $Generals::$alert_header);
		$I->see("Mailinglist saved successfully!", $Generals::$alert_msg);
		$I->see('Mailinglists', $Generals::$pageTitle);
		$MlManage::HelperArcDelOneMl($I, $MlManage, $Generals);
		$I->see('Mailinglists', $Generals::$pageTitle);
	}

	/**
	 * Test method to create multiple mailing lists
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function CreateMultipleMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Create_multiple_mailinglists");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman");
		$I->click("img[alt=\"Mailinglists\"]");

			// while loop
			$I->click("//button[@onclick=\"Joomla.submitbutton('mailinglist.add')\"]");
	        $I->fillField("#jform_title", "javascript{ml_arr_title[storedVars.i];}");
	        $I->fillField("#jform_description", "javascript{ml_arr_description[storedVars.i];}");
	        $I->selectOption("#jform_access", "undefined");
	        $I->selectOption("#jform_published", "undefined");
	        $I->click("button.btn.btn-small");
	        $I->see("Message", "h4.alert-heading");
	        $I->see("Mailinglist saved successfully!", "p.alert-message");
}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function EditMailinglistWithoutChangesCancelListView(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Edit_mailinglist_without_changes_cancel_list_view");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman");
		$I->click("img[alt=\"Mailinglists\"]");
		$I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.1|title|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.2|description|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.3|published|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.4|access|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.5|subscribers|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.6|id|
        $I->click("01 Mailingliste 3 A");
		$I->click("#toolbar-cancel > button.btn.btn-small");
		$I->see("Mailinglists", "h1.page-title");
		$I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[2]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[3]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[4]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[5]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[6]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[7]|undefined|

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function EditMailinglistWithChangesCancelListView(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Edit_mailinglist_with_changes_cancel_list_view");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman");
		$I->click("img[alt=\"Mailinglists\"]");
		$I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.1|title|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.2|description|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.3|published|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.4|access|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.5|subscribers|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.6|id|
        $I->click("01 Mailingliste 3 A");
		$I->fillField("#jform_title", "01 Mailingliste 3 AX");
		$I->fillField("#jform_description", "01 Mailingliste 3 weiterer Lauf A X");
		$I->selectOption("#jform_access", "5");
		$I->selectOption("#jform_published", "0");
		$I->click("#toolbar-cancel > button.btn.btn-small");
		$I->see("Mailinglists", "h1.page-title");
		$I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[2]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[3]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[4]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[5]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[6]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[7]|undefined|

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function EditMailinglistWithChangesSaveAndCloseListView(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Edit_mailinglist_with_changes_save_and_close_list_view");
		$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman");
        $I->click("img[alt=\"Mailinglists\"]");
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.1|title|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.2|description|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.3|published|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.4|access|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.5|subscribers|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.6|id|
        $I->click("01 Mailingliste 3 A");
        $I->fillField("#jform_title", "01 Mailingliste 3 A X");
        $I->fillField("#jform_description", "01 Mailingliste 3 weiterer Lauf A X");
        $I->selectOption("#jform_access", "5");
        $I->selectOption("#jform_published", "0");
        $I->click("//button[@onclick=\"Joomla.submitbutton('mailinglist.save')\"]");
        $I->see("Mailinglists", "h1.page-title");
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[2]|01 Mailingliste 3 A X|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[3]|01 Mailingliste 3 weiterer Lauf A X|
        $I->verifyText ====css =#j-main-container table tbody tr:nth-child(3) td:nth-child(4) a span.icon-unpublish||
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[5]|Guest|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[6]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[7]|undefined|

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function EditMailinglistWithChangesSaveOnlyThenVloseListView(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Edit_mailinglist_with_changes_save_only_then_close_list_view");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman");
		$I->click("img[alt=\"Mailinglists\"]");
		$I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.1|title|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.2|description|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.3|published|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.4|access|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.5|subscribers|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.3.6|id|
        $I->click("01 Mailingliste 3 A X");
		$I->storeValue ====id = jform_modified_time | mod_time_1 |
			$I->fillField("#jform_title", "01 Mailingliste 3 A");
		$I->fillField("#jform_description", "01 Mailingliste 3 weiterer Lauf A");
		$I->selectOption("#jform_access", "2");
		$I->selectOption("#jform_published", "1");
		$I->click("//button[@onclick=\"Joomla.submitbutton('mailinglist.apply')\"]");
        $I->see("Mailinglist details: [ Edit ]", "h1.page-title");
        $I->see("Message", "h4.alert-heading");
        $I->see("Mailinglist saved successfully!", "p.alert-message");
        $I->seeInField("#jform_title", "01 Mailingliste 3 A");
        $I->seeInField("#jform_description", "01 Mailingliste 3 weiterer Lauf A");
        $I->see("Registered", "a.chzn-single > span");
        $I->see("pulished", "a.chzn-single.chzn-color-state > span");
        $I->storeValue ====id = jform_modified_time | mod_time_2 |
        $I->echo ====undefined ||
        $I->echo ====undefined ||
        $I->assertEval ====storedVars['mod_time_1'] != storedVars['mod_time_2'] | true |
	        $I->click("//button[@onclick=\"Joomla.submitbutton('mailinglist.cancel')\"]");
        $I->see("Mailinglists", "h1.page-title");
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[2]|01 Mailingliste 3 A|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[3]|01 Mailingliste 3 weiterer Lauf A|
        $I->verifyText ====css =#j-main-container table tbody tr:nth-child(3) td:nth-child(4) a span.icon-publish||
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[5]|Registered|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[6]|undefined|
        $I->verifyText ====//div[@id='j-main-container']/div[2]/table/tbody/tr[3]/td[7]|undefined|

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function SortMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Sort_mailinglists_V2");
		$I->store ====javascript{
		ml_arr_title . length;
	}|length |
	$I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I        $I->click("Description");
        $I        $I->getEval ====delete storedVars['ml_arr_subs'] ||
	$I->getEval ====delete storedVars['ml_arr_ids'] ||
	$I->store ====0 | i |
	$I->store ====1 | k |
	$I->while ====storedVars . i < storedVars . length ||
	$I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.5|cur_val_subs|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.6|cur_val_id|
        $I->push ====undefined | ml_arr_subs |
        $I->push ====undefined | ml_arr_ids |
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->store ====javascript{
		storedVars . k++;
	}||
        $I->endWhile ====||
        $I        $I->runScript ====javascript{
		ml_arr_subs_asc  = sortArr(storedVars . ml_arr_subs, 'asc');
		ml_arr_ids_asc   = sortArr(storedVars . ml_arr_ids, 'asc');
		ml_arr_subs_desc = sortArr(storedVars . ml_arr_subs, 'desc');
		ml_arr_ids_desc  = sortArr(storedVars . ml_arr_ids, 'desc');
	}||
        $I        $I->click("a.js-stools-column-order.hasTooltip");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_title_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[2]");
        $I->see("Title ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[2]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->click("a.js-stools-column-order.hasTooltip");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_title_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[2]");
        $I->see("Title descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[2]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->click("Description");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_description_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[3]");
        $I->see("Description ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[3]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->click("Description");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_description_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[3]");
        $I->see("Description descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[3]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->click("published");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_pub_asc[storedVars . i];
	}|cur_pub |
	$I->echo ====undefined ||
		$I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.undefined')"]");
        $I->see("Status ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[4]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->click("published");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_pub_desc[storedVars . i];
	}|cur_pub |
	$I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.undefined')"]");
        $I->see("Status descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[4]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->click("Access Level");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_acc_asc[storedVars . i];
	}|cur_acc |
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[5]");
        $I->see("Access ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[5]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->click("Access Level");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_acc_desc[storedVars . i];
	}|cur_acc |
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[5]");
        $I->see("Access descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[5]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->click("# subscribers");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_subs_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[6]");
        $I->see("# subscribed mailing lists ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[6]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->click("# subscribers");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_subs_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[6]");
        $I->see("# subscribed mailing lists descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[6]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->click("ID");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_ids_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[7]");
        $I->see("ID ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[7]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->click("ID");
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_ids_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[7]");
        $I->see("ID descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[7]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->selectAndWait ====id = list_fullordering | value = a . title ASC |
	$I->pause ====500 ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . length ||
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_title_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[2]");
        $I->see("Title ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[2]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->selectAndWait ====id = list_fullordering | value = a . title DESC |
	$I->pause ====500 ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . length ||
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_title_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[2]");
        $I->see("Title descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[2]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->selectAndWait ====id = list_fullordering | value = a . description ASC |
	$I->pause ====500 ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . length ||
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_description_asc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[3]");
        $I->see("Description ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[3]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.description DESC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("javascript{ml_arr_description_desc[storedVars.i-1];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[3]");
        $I->see("Description descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[3]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.published ASC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_pub_asc[storedVars . i];
	}|cur_pub |
	$I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.undefined')"]");
        $I->see("Status ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[4]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.published DESC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_pub_desc[storedVars . i];
	}|cur_pub |
	$I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.undefined')"]");
        $I->see("Status descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[4]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.access ASC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_acc_asc[storedVars . i];
	}|cur_acc |
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[5]");
        $I->see("Access ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[5]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.access DESC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->store ====javascript{
		ml_arr_acc_desc[storedVars . i];
	}|cur_acc |
	$I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[5]");
        $I->see("Access descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[5]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "subscribers ASC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->storeEval ====storedVars . ml_arr_subs[undefined] | cur_subs |
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[6]");
        $I->see("# subscribed mailing lists ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[6]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "subscribers DESC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->storeEval ====storedVars . ml_arr_subs[storedVars . length - (storedVars . i + 1)] | cur_subs |
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[6]");
        $I->see("# subscribed mailing lists descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[6]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.id ASC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->storeEval ====storedVars . ml_arr_ids[undefined] | cur_id |
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[7]");
        $I->see("ID ascending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[7]/a/span||
        $I->see("", "span.icon-arrow-up-3");
        $I->endWhile ====||
        $I        $I->selectOption("#list_fullordering", "a.id DESC");
        $I->pause ====500 ||
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . length ||
        $I->storeEval ====storedVars . ml_arr_ids[storedVars . length - (storedVars . i + 1)] | cur_id |
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->see("undefined", "//div[@id='j-main-container']/div[2]/table/tbody/tr[undefined]/td[7]");
        $I->see("ID descending", "a.chzn-single > span");
        $I->assertElementPresent ====//th[7]/a/span||
        $I->assertElementPresent ====css = span . icon - arrow - down - 3 ||
        $I->endWhile ====||
        $I->click("a.js-stools-column-order.hasTooltip");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function FilterMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Filter_mailinglists");
		$I        $I->store ====There are no data available | no_data |
	$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I->click("(//button[@type='button'])[2]");
        $I        $I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_unpublished |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.unpublish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_unpublished ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "0");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_public |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.publish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->click("(//button[@type='button'])[2]");
        $I        $I->label ====test_public ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_registered |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Public", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_registered ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "2");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_special |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Registered", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_special ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "3");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_guest |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Special", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_guest ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "5");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_Super_User |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->echo ====Anzahl Reihen = undefined ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . rows ||
		$I->see("Guest", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_Super_User ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "6");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | finish |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Super Users", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->label ====finish ||
	        $I->click("(//button[@type='button'])[2]");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function PaginationMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Pagination_mailinglists");
		$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I        $I->selectOption("#list_limit", "20");
        $I->click("Description");
        $I->click("a.js-stools-column-order.hasTooltip");
        $I->pause ====500 ||
	        $I        $I->selectOption("#list_limit", "5");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[0];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[4];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("2");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[5];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[9];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("3");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[10];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[14];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->click("4");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[15];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->click("span.icon-first");
        $I->pause ====500 ||
	        $I->see("01 Mailingliste 2 A");
        $I->see("02 Mailingliste 6 A");
        $I->click("span.icon-next");
        $I->pause ====500 ||
	        $I->see("02 Mailingliste 6 B");
        $I->see("02 Mailingliste 8 B");
        $I->click("span.icon-last");
        $I->pause ====500 ||
	        $I->see("04 Mailingliste 15 B");
        $I->click("2");
        $I->pause ====500 ||
	        $I->see("02 Mailingliste 6 B");
        $I->see("02 Mailingliste 8 B");
        $I        $I->click("span.icon-first");
        $I->pause ====500 ||
	        $I->see("javascript{ml_arr_title_asc[0];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[2]");
        $I->see("javascript{ml_arr_title_asc[4];}", "//div[@id='j-main-container']/div[2]/table/tbody/tr[5]/td[2]");
        $I->selectOption("#list_limit", "20");
        $I->pause ====500 ||
	        $I->click("Description");
        $I->pause ====500 ||
	        $I->click("a.js-stools-column-order.hasTooltip");
        $I->pause ====1000 ||

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function SearchMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Search_mailinglists");
		$I        $I->store ====There are no data available | no_data |
	$I        $I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "title");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====02 Mail | search_val |
	$I->selectOption("#filter_search_filter", "title");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.1|found_val|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->assertNotEval ====undefined | -1 |
	$I->endWhile ====||
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====weiterer Lauf B | search_val |
	$I->selectOption("#filter_search_filter", "description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.2|found_val|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->assertNotEval ====undefined | -1 |
	$I->endWhile ====||
        $I        $I->store ====xx | search_val |
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_search_filter", "title_description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->assertTable ====//div[@id='j-main-container']/div[2]/table.1.0|There are no data available|
        $I->click("(//button[@type='button'])[2]");
        $I        $I->store ====weiterer Lauf A | search_val |
	$I->selectOption("#filter_search_filter", "title_description");
        $I->pause ====500 ||
	        $I->fillField("#filter_search", "undefined");
        $I->click("button.btn.hasTooltip");
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.1|found_val|
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.undefined.2|found_val_2|
        $I->storeEval ====javascript{
		storedVars . found_val . indexOf(storedVars . search_val);
	}|result |
	$I->storeEval ====javascript{
		storedVars . found_val_2 . indexOf(storedVars . search_val);
	}|result_2 |
	$I->assertNotEval ====undefined + undefined | -1 |
	$I->endWhile ====||
        $I        $I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_unpublished |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.unpublish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_unpublished ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_published", "0");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_public |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("", "//a[@onclick="return listItemTask('cbundefined', 'mailinglists.publish')"]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_public ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "1");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_registered |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Public", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_registered ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "2");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_special |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Registered", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_special ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "3");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_guest |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Special", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_guest ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "5");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | test_Super_User |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->echo ====Anzahl Reihen = undefined ||
	$I->store ====0 | i |
	$I->while ====storedVars . i < storedVars . rows ||
		$I->see("Guest", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I        $I->label ====test_Super_User ||
		$I->click("//button[@type='button']");
        $I->selectOption("#filter_access", "6");
        $I->pause ====500 ||
        $I->storeTable ====//div[@id='j-main-container']/div[2]/table.1.0|row1|
        $I->gotoIf ====storedVars . row1 == storedVars . no_data | finish |
        $I->storeXpathCount ====//div[@id='j-main-container']/div[2]/table/tbody/tr|rows|
        $I->store ====0 | i |
        $I->while ====storedVars . i < storedVars . rows ||
	        $I->see("Super Users", "//div[@id='j-main-container']/div[2]/table/tbody/tr/td[5]");
        $I->store ====javascript{
		storedVars . i++;
	}||
        $I->endWhile ====||
        $I->label ====finish ||
	        $I->click("(//button[@type='button'])[2]");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function LockedMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{

		$I->wantTo("Locked_mailinglists");
		$I        $I        $I->amOnPage("/administrator/index.php?option=com_bwpostman");
        $I->runScript ====javascript{
		confirm("For this test it is neccessary to log in as BE User 1 into backend by another browser. Then edit the first entry in the list of mailinglists. Let stand this as is and click okay here. Wait for the next message….");
	}||
        $I->click("//div[@id='cpanel']/div[8]/div/a/span");
        $I->click("01 Mailingliste 2 A");
        $I->see("Check-out failed with the following error: The user checking out does not match the user who checked out the item.", "p.alert-message");
        $I->assertElementPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->click("//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span");
        $I->assertElementNotPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->see("One mailing list successfully checked in!", "p.alert-message");
        $I->click("#submenu > li > a");
        $I->runScript ====javascript{
		confirm("Now cancel editing in the other browser and call edit again. Let stand this as is and click okay here anew. Wait for the next message….");
	}||
        $I->click("//div[@id='cpanel']/div[8]/div/a");
        $I->assertElementPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->click("#cb0");
        $I->click("#toolbar-checkin > button.btn.btn-small");
        $I->pause ====1000 ||
        $I->assertElementNotPresent ====//div[@id='j-main-container']/div[2]/table/tbody/tr/td[2]/a/span||
        $I->see("One mailing list successfully checked in!", "p.alert-message");
        $I->runScript ====javascript{
		confirm("Test finished. Cancel editing at the other browser. Logout at the other browser now is possible. ");
	}||
        $I->click("#submenu > li > a");

}
*/
	/**
	 * Test method to create a single mailing list from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
/*	public function ArchiveAndDeleteAllMailinglists(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$I->wantTo("Archive_and_delete_all_mailinglists");
		$I->amOnPage("/administrator/index.php?option=com_bwpostman&view=mailinglists");
		$I->click("input[name=checkall-toggle]");
		$I->click("#toolbar-archive > button.btn.btn-small");
		$I->see("Mailinglists", "h1.page-title");
		$I->canSee('Message', 'h4.alert-heading');
		$I->canSee('The selected mailing lists have been archived.', 'p . alert - message');
		$I->click("(//a[contains(text(),'Archive')])[2]");
		$I->click("//button[@onclick=\"layout.setAttribute('value', 'mailinglists');this.form.submit();\"]");
		$I->click("input[name=checkall-toggle]");
		$I->click("#toolbar-delete > button.btn.btn-small");
		$I->acceptPopup('Do you wish to remove the selected mailinglist(s)?');
		$I->see("Archive", "h1.page-title");
		$I->canSee('Message', 'h4.alert-heading');
		$I->canSee('The selected mailinglists have been removed.', 'p . alert - message');
		$I->canSee('There are no data available', 'td > strong');
		$I->click("(//a[contains(text(),'Mailinglists')])[2]");
		$I->seeInTitle('Bw - Test - Administration - Mailinglists');
		$I->canSee('There are no data available', 'td > strong');
		$I->click("#submenu > li > a");
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
	public function _logout(\Step\Acceptance\Admin $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromAdmin();
	}

}
