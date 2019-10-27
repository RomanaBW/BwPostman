<?php

use Page\Generals as Generals;
use Page\TemplateEditPage as TplEdit;
use Page\TemplateManagerPage as TplManage;
use Page\MainviewPage as MainView;

//@ToDo: Editing of provided templates, both directly and copy

/**
 * Class TestTemplatesDetailsCest
 *
 * This class contains all methods to test manipulation of a single template at back end

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
class TestTemplatesDetailsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   \Page\Login         $loginPage
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
	 * Test method to create a single HTML template from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addHtmlTemplateButton);
		$I->waitForElement(TplEdit::$tpl_tab1, 30);

		$this->fillFormExtendedHtml($I);

		$I->clickAndWait(Generals::$toolbar4['Back'], 1);

		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from main view, save it and go back to main view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template complete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addHtmlTemplateButton);

		$this->fillFormSimpleHtml($I);

		// check if save and close is successful
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 3);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// check if preview is visible at template list
		$thumb = sprintf(TplEdit::$thumbnail_list_pos, TplEdit::$thumb_url);
		$I->seeElement($thumb);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template cancel list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormExtendedHtml($I);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$msg_cancel);
		$I->acceptPopup();

		$I->see("Template", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormSimpleHtml($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateSaveNewListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template, save and get new record list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormSimpleHtml($I);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save & New'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->see('Template HTML', TplEdit::$tpl_tab3);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(TplEdit::$tpl_tab1, 1);

		$I->see('', TplEdit::$title);
		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$msg_cancel);
		$I->acceptPopup();

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view, save, modify and save as copy
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneHtmlTemplateSaveCopyListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template, save, modify and save as copy");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormSimpleHtml($I);

		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->see('Template HTML', TplEdit::$tpl_tab3);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(TplEdit::$tpl_tab1, 1);
		$I->seeInField(TplEdit::$title, TplEdit::$field_title);

		// Grab ID of first template
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => TplEdit::$field_title));

		$I->fillField(TplEdit::$title, TplEdit::$field_title2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->seeInField(TplEdit::$title, TplEdit::$field_title2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Template HTML', TplEdit::$tpl_tab3);

		// Grab ID of second template
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => TplEdit::$field_title2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$msg_cancel);
		$I->acceptPopup();

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create same single HTML template twice from main view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateHtmlTemplateTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create HTML template twice list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormSimpleHtml($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->see('Template', Generals::$pageTitle);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$toolbar4['Add HTML-Template']);

		$this->fillFormSimpleHtml($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Text template from main view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addTextTemplateButton);

		$this->fillFormExtendedText($I);

		$I->clickAndWait(Generals::$toolbar4['Back'], 1);

		$I->waitForElement(Generals::$pageTitle, 5);
		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Text template from main view, save it and go back to main view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template complete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addTextTemplateButton);

		TplEdit::fillFormSimpleText($I);

		// check if save and close is successful
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// check if preview is visible at template list
		$I->seeElement(sprintf(TplEdit::$thumbnail_list_pos, TplEdit::$thumb_url));

		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Text template from list view and cancel creation
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template cancel list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add Text-Template']);

		$this->fillFormExtendedText($I);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$msg_cancel);
		$I->acceptPopup();

		$I->see("Template", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Text template from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view, save it and get new record
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateSaveNewListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template, save and get new record list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save & New'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->see('Template TEXT', TplEdit::$tpl_tab2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(TplEdit::$tpl_tab1, 1);

		$I->see('', TplEdit::$title);
		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single HTML template from list view, save, modify and save as copy
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateSaveCopyListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template, save, modify and save as copy");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->see('Template TEXT', TplEdit::$tpl_tab2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(TplEdit::$tpl_tab1, 1);
		$I->seeInField(TplEdit::$title, TplEdit::$field_title);

		// Grab ID of first template
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => TplEdit::$field_title));

		$I->fillField(TplEdit::$title, TplEdit::$field_title2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->seeInField(TplEdit::$title, TplEdit::$field_title2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Template TEXT', TplEdit::$tpl_tab2);

		// Grab ID of second template
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => TplEdit::$field_title2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Text template from list view, save it and go back to list view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateOneTextTemplateRestoreListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template list view, archive and restore");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArchiveItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array);

		$I->switchToArchive($I, TplEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array);

		$I->amOnPage(TplManage::$url);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to create same single Text template twice from main view
	 *
	 * @param   AcceptanceTester    $I
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
	public function CreateTextTemplateTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create Text template twice list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);

		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Template', Generals::$pageTitle);

		$I->click(Generals::$toolbar4['Add Text-Template']);

		TplEdit::fillFormSimpleText($I);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to edit a default template from list view and save it as new
	 *
	 * @param   AcceptanceTester    $I
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
	public function DefaultTemplateSaveNewListView(AcceptanceTester $I)
	{
		$I->wantTo("Edit default template, save and get new record list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(TplManage::$tableRowForDefault);
		$I->waitForElementVisible(TplEdit::$tpl_tab1);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save & New'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Template HTML', TplEdit::$tpl_tab3);

		$I->clickAndWait(TplEdit::$tpl_tab1, 1);

		$I->see('', TplEdit::$title);
		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to edit a default template from list view and save it as copy
	 *
	 * @param   AcceptanceTester    $I
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
	public function DefaultTemplateSaveCopyListView(AcceptanceTester $I)
	{
		$I->wantTo("Edit default template, save as copy");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 5);
		$I->click(TplManage::$tableRowForDefault);
		$I->waitForElementVisible(TplEdit::$tpl_tab1);

		// Grab ID of first template
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => 'Standard Creme'));

		$I->fillField(TplEdit::$title, TplEdit::$field_title);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);
		$I->waitForElement(Generals::$alert_header, 5);
		$I->see("Message", Generals::$alert_heading);
		$I->see(TplEdit::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->see('Header', TplEdit::$tpl_tab2);

		$I->seeInField(TplEdit::$title, TplEdit::$field_title);

		// Grab ID of second template
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_templates', 'id', array('title' => TplEdit::$field_title));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->seeInPopup(TplEdit::$popup_changes_not_saved);
		$I->acceptPopup();

		$I->HelperArcDelItems($I, TplManage::$arc_del_array, TplEdit::$arc_del_array, true);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 * @param   \Page\Login         $loginPage
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
	 * Method to fill form for HTML template with check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormSimpleHtml(AcceptanceTester $I)
	{
		TplEdit::fillRequired($I, 'HTML');

		TplEdit::selectThumbnail($I, 'AdminTester');

		$this->fillHtmlContent($I);

		$this->fillCssContent($I);
	}

	/**
	 * Method to fill form for HTML templates with check of required fields
	 * This method fills in the end all required fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtendedHtml(AcceptanceTester $I)
	{
		$this->fillRequiredExtended($I, 'HTML');

		// select thumbnail
		TplEdit::selectThumbnail($I, 'AdminTester');

		$this->selectRadiosExtended($I);
	}

	/**
	 * Method to fill form for HTML templates with check of required fields
	 * This method fills in the end all required fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester  $I
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtendedText(AcceptanceTester $I)
	{
		$this->fillRequiredExtended($I, 'Text');

		// select thumbnail
		TplEdit::selectThumbnail($I, 'AdminTester');

		$this->selectRadiosExtended($I);
	}

	/**
	 * @param AcceptanceTester  $I
	 * @param string            $type
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private function fillRequiredExtended(AcceptanceTester $I, $type)
	{
		$I->clickAndWait(TplEdit::$tpl_tab1, 1);
		$I->fillField(TplEdit::$title, TplEdit::$field_title);
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->seeInPopup(TplEdit::$popup_description);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, "");
		$I->fillField(TplEdit::$description, sprintf(TplEdit::$field_description, $type));
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->seeInPopup(TplEdit::$popup_title);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, TplEdit::$field_title);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function selectRadiosExtended(AcceptanceTester $I)
	{
		//show author
		// switch no
		$I->click(TplEdit::$show_author_no);
		$I->seeElement(TplEdit::$show_author_no_active);
		$I->dontSeeElement(TplEdit::$show_author_yes_active);

		// switch yes
		$I->click(TplEdit::$show_author_yes);
		$I->dontSeeElement(TplEdit::$show_author_no_active);
		$I->seeElement(TplEdit::$show_author_yes_active);

		// show created date
		// switch no
		$I->click(TplEdit::$show_created_no);
		$I->seeElement(TplEdit::$show_created_no_active);
		$I->dontSeeElement(TplEdit::$show_created_yes_active);

		// switch yes
		$I->click(TplEdit::$show_created_yes);
		$I->dontSeeElement(TplEdit::$show_created_no_active);
		$I->seeElement(TplEdit::$show_created_yes_active);

		// show readon button
		// switch no
		$I->click(TplEdit::$show_readon_no);
		$I->seeElement(TplEdit::$show_readon_no_active);
		$I->dontSeeElement(TplEdit::$show_readon_yes_active);

		// switch yes
		$I->click(TplEdit::$show_readon_yes);
		$I->dontSeeElement(TplEdit::$show_readon_no_active);
		$I->seeElement(TplEdit::$show_readon_yes_active);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function fillHtmlContent(AcceptanceTester $I)
	{
		$I->clickAndWait(TplEdit::$tpl_tab3, 1);
		$I->scrollTo(TplEdit::$button_editor_toggle, 0, -100);
		$I->clickAndWait(TplEdit::$button_editor_toggle, 1);
		$I->fillField(TplEdit::$html_style, TplEdit::$html_style_content);
		$I->scrollTo(TplEdit::$button_editor_toggle, 0, -100);
		$I->click(TplEdit::$button_editor_toggle);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 1);
		$I->clickAndWait(Generals::$toolbar4['Save'], 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function fillCssContent(AcceptanceTester $I)
	{
		$I->click(TplEdit::$tpl_tab2);
		$I->fillField(TplEdit::$css_style, TplEdit::$css_style_content);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 1);
	}
}
