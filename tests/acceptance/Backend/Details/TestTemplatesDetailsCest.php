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

 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

		$this->_fillFormExtendedHtml($I);

		$I->clickAndWait(TplEdit::$toolbar['Back'], 1);

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
	 * @since   2.0.0
	 */
	public function CreateOneHtmlTemplateCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template complete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addHtmlTemplateButton);

		$this->_fillFormSimpleHtml($I);

		// check if save and close is successful
		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 3);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

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
	 * @since   2.0.0
	 */
	public function CreateOneHtmlTemplateCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template cancel list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormExtendedHtml($I);

		$I->click(TplEdit::$toolbar['Cancel']);

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
	 * @since   2.0.0
	 */
	public function CreateOneHtmlTemplateListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one HTML template list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

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
	 * @since   2.0.0
	 */
	public function CreateHtmlTemplateTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create HTML template twice list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);
		$I->see('Template', Generals::$pageTitle);
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->click(TplEdit::$toolbar['Cancel']);

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
	 * @since   2.0.0
	 */
	public function CreateOneTextTemplateCancelMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template and cancel from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addTextTemplateButton);

		$this->_fillFormExtendedText($I);

		$I->clickAndWait(TplEdit::$toolbar['Back'], 1);

		$I->waitForElement(Generals::$pageTitle, 30);
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
	 * @since   2.0.0
	 */
	public function CreateOneTextTemplateCompleteMainView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template complete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addTextTemplateButton);

		TplEdit::_fillFormSimpleText($I);

		// check if save and close is successful
		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

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
	 * @since   2.0.0
	 */
	public function CreateOneTextTemplateCancelListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template cancel list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add Text-Template']);

		$this->_fillFormExtendedText($I);

		$I->click(TplEdit::$toolbar['Cancel']);

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
	 * @since   2.0.0
	 */
	public function CreateOneTextTemplateCompleteListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add Text-Template']);

		TplEdit::_fillFormSimpleText($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

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
	 * @since   2.0.0
	 */
	public function CreateOneTextTemplateListViewRestore(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->click(Generals::$toolbar['Add Text-Template']);

		TplEdit::_fillFormSimpleText($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

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
	 * @since   2.0.0
	 */
	public function CreateTextTemplateTwiceListView(AcceptanceTester $I)
	{
		$I->wantTo("Create Text template twice list view");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);

		$I->click(Generals::$toolbar['Add Text-Template']);

		TplEdit::_fillFormSimpleText($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);
		$I->see('Template', Generals::$pageTitle);

		$I->click(Generals::$toolbar['Add Text-Template']);

		TplEdit::_fillFormSimpleText($I);

		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->click(TplEdit::$toolbar['Cancel']);

		$I->see("Template", Generals::$pageTitle);

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
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, \Page\Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester    $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _failed (AcceptanceTester $I)
	{

	}

	/**
	 * Method to fill form for HTML template with check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _fillFormSimpleHtml(AcceptanceTester $I)
	{
		TplEdit::_fillRequired($I, 'HTML');

		TplEdit::_selectThumbnail($I);

		$this->_fillHtmlContent($I);

		$this->_fillCssContent($I);
	}

	/**
	 * Method to fill form for HTML templates with check of required fields
	 * This method fills in the end all required fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _fillFormExtendedHtml(AcceptanceTester $I)
	{
		$this->_fillRequiredExtended($I, 'HTML');

		// select thumbnail
		TplEdit::_selectThumbnail($I);

		$this->_selectRadiosExtended($I);
	}

	/**
	 * Method to fill form for HTML templates with check of required fields
	 * This method fills in the end all required fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 *
	 * @param AcceptanceTester  $I
	 *
	 * @since   2.0.0
	 */
	private function _fillFormExtendedText(AcceptanceTester $I)
	{
		$this->_fillRequiredExtended($I, 'Text');

		// select thumbnail
		TplEdit::_selectThumbnail($I);

		$this->_selectRadiosExtended($I);
	}

	/**
	 * @param AcceptanceTester  $I
	 * @param string            $type
	 *
	 * @since 2.0.0
	 */
	private function _fillRequiredExtended(AcceptanceTester $I, $type)
	{
		$I->fillField(TplEdit::$title, TplEdit::$field_title);
		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);
		$I->seeInPopup(TplEdit::$popup_description);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, "");
		$I->fillField(TplEdit::$description, sprintf(TplEdit::$field_description, $type));
		$I->clickAndWait(TplEdit::$toolbar['Save & Close'], 1);
		$I->seeInPopup(TplEdit::$popup_title);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, TplEdit::$field_title);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function _selectRadiosExtended(AcceptanceTester $I)
	{
		//show author
		// switch no
		$I->click(TplEdit::$show_author_no);
		$I->seeElement(TplEdit::$show_author_no, ['class' => Generals::$button_red]);
		$I->dontSeeElement(TplEdit::$show_author_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_author_yes, ['class' => Generals::$button_grey]);

		// switch yes
		$I->click(TplEdit::$show_author_yes);
		$I->dontSeeElement(TplEdit::$show_author_no, ['class' => Generals::$button_red]);
		$I->seeElement(TplEdit::$show_author_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_author_no, ['class' => Generals::$button_grey]);

		// show created date
		// switch no
		$I->click(TplEdit::$show_created_no);
		$I->seeElement(TplEdit::$show_created_no, ['class' => Generals::$button_red]);
		$I->dontSeeElement(TplEdit::$show_created_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_created_yes, ['class' => Generals::$button_grey]);

		// switch yes
		$I->click(TplEdit::$show_created_yes);
		$I->dontSeeElement(TplEdit::$show_created_no, ['class' => Generals::$button_red]);
		$I->seeElement(TplEdit::$show_created_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_created_no, ['class' => Generals::$button_grey]);

		// show readon button
		// switch no
		$I->click(TplEdit::$show_readon_no);
		$I->seeElement(TplEdit::$show_readon_no, ['class' => Generals::$button_red]);
		$I->dontSeeElement(TplEdit::$show_readon_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_readon_yes, ['class' => Generals::$button_grey]);

		// switch yes
		$I->click(TplEdit::$show_readon_yes);
		$I->dontSeeElement(TplEdit::$show_readon_no, ['class' => Generals::$button_red]);
		$I->seeElement(TplEdit::$show_readon_yes, ['class' => Generals::$button_green]);
		$I->seeElement(TplEdit::$show_readon_no, ['class' => Generals::$button_grey]);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function _fillHtmlContent(AcceptanceTester $I)
	{
		$I->clickAndWait(TplEdit::$tpl_tab3, 1);
		$I->scrollTo(TplEdit::$button_editor_toggle, 0, -100);
		$I->clickAndWait(TplEdit::$button_editor_toggle, 1);
		$I->fillField(TplEdit::$html_style, TplEdit::$html_style_content);
		$I->scrollTo(TplEdit::$button_editor_toggle, 0, -100);
		$I->click(TplEdit::$button_editor_toggle);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 1);
		$I->clickAndWait(TplEdit::$toolbar['Save'], 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	private function _fillCssContent(AcceptanceTester $I)
	{
		$I->click(TplEdit::$tpl_tab2);
		$I->fillField(TplEdit::$css_style, TplEdit::$css_style_content);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 1);
	}
}
