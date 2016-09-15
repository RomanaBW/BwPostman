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

 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addHtmlTemplateButton);

		$this->_fillFormExtendedHtml($I);

		$I->click(TplEdit::$toolbar['Back']);

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
		$I->waitForElement(Generals::$pageTitle);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addHtmlTemplateButton);

		$this->_fillFormSimpleHtml($I);

		// check if save and close is successful
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

		// check if preview is visible at template list
		$I->seeElement(sprintf(TplEdit::$thumbnail_list_pos, TplEdit::$thumb_url));

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->click(TplEdit::$toolbar['Save & Close']);

		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);
		$I->see('Template', Generals::$pageTitle);
		$I->click(Generals::$toolbar['Add HTML-Template']);

		$this->_fillFormSimpleHtml($I);

		$I->click(TplEdit::$toolbar['Save & Close']);

		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->click(TplEdit::$toolbar['Cancel']);
		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addTextTemplateButton);

		$this->_fillFormExtendedText($I);

		$I->click(TplEdit::$toolbar['Back']);
//		$I->seeInPopup('Any changes will not be saved. Close without saving?');
//		$I->acceptPopup();
//		$I->executeJS("window.confirm = function(msg){return true;};");

//		$I->click(Generals::$submenu['BwPostman']);
//		$I->waitForElement(Generals::$pageTitle);
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
		$I->waitForElement(Generals::$pageTitle);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->click(MainView::$addTextTemplateButton);

		$this->_fillFormSimpleText($I);

		// check if save and close is successful
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

		// check if preview is visible at template list
		$I->seeElement(sprintf(TplEdit::$thumbnail_list_pos, TplEdit::$thumb_url));

		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
	public function CreateOneTextTemplateListView(AcceptanceTester $I)
	{
		$I->wantTo("Create one Text template list view");
		$I->amOnPage(TplManage::$url);
		$I->click(Generals::$toolbar['Add Text-Template']);

		$this->_fillFormSimpleText($I);

		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
		$I->click(Generals::$toolbar['Add Text-Template']);
		$this->_fillFormSimpleText($I);
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(TplEdit::$success_save, Generals::$alert_msg);
		$I->see('Template', Generals::$pageTitle);
		$I->click(Generals::$toolbar['Add Text-Template']);
		$this->_fillFormSimpleText($I);
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->see("Error", Generals::$alert_header);
		$I->see(TplEdit::$error_save, Generals::$alert_error);
		$I->click(TplEdit::$toolbar['Cancel']);
		$I->see("Template", Generals::$pageTitle);

		$I->HelperArcDelItems($I, new TplManage(), new TplEdit());
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
		$this->_fillRequired($I);

		$this->_selectThumbnail($I);

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
		$this->_fillRequiredExtended($I);

		// select thumbnail
		$this->_selectThumbnail($I);

		$this->_selectRadiosExtended($I);
	}

	/**
	 * Method to fill form for text template with check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _fillFormSimpleText(AcceptanceTester $I)
	{
		$this->_fillRequired($I);

		$this->_selectThumbnail($I);

		$this->_fillTextContent($I);
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
	private function _fillFormExtendedText(AcceptanceTester $I)
	{
		$this->_fillRequiredExtended($I);

		// select thumbnail
		$this->_selectThumbnail($I);

		$this->_selectRadiosExtended($I);
	}

	/**
	 * Method to fill required fields
	 * Usable for both, HTML and Text
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _fillRequired(AcceptanceTester $I)
	{
		$I->fillField(TplEdit::$title, TplEdit::$field_title);
		$I->fillField(TplEdit::$description, TplEdit::$field_description);
	}

	/**
	 * Method to select thumbnail for template
	 *
	 * @param AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	private function _selectThumbnail(AcceptanceTester $I)
	{

		$I->clickAndWait(TplEdit::$thumb_select_button, 1);
		$I->switchToIFrame(Generals::$media_frame);
		$I->switchToIFrame(Generals::$image_frame);
		$I->clickAndWait(TplEdit::$thumb_select, 1);
		$I->switchToIFrame();
		$I->switchToIFrame(Generals::$media_frame);
		$I->clickAndWait(TplEdit::$thumb_insert, 1);
		$I->switchToIFrame();

		// Workaround
//		$I->fillReadonlyInput(TplEdit::$thumbnail_id, TplEdit::$thumbnail, TplEdit::$field_thumbnail);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
	 */
	private function _fillRequiredExtended(AcceptanceTester $I)
	{
		$I->fillField(TplEdit::$title, TplEdit::$field_title);
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->seeInPopup(TplEdit::$popup_description);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, "");
		$I->fillField(TplEdit::$description, TplEdit::$field_description);
		$I->click(TplEdit::$toolbar['Save & Close']);
		$I->seeInPopup(TplEdit::$popup_title);
		$I->acceptPopup();

		$I->fillField(TplEdit::$title, TplEdit::$field_title);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
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
	 *
	 * @since version
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
		$I->click(TplEdit::$toolbar['Save']);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
	 */
	private function _fillCssContent(AcceptanceTester $I)
	{
		$I->click(TplEdit::$tpl_tab2);
		$I->fillField(TplEdit::$css_style, TplEdit::$css_style_content);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 *
	 * @since version
	 */
	private function _fillTextContent(AcceptanceTester $I)
	{
// fill Text
		$I->click(TplEdit::$tpl_tab2);
		$I->fillField(TplEdit::$text_style, TplEdit::$text_style_content);
		$I->scrollTo(TplEdit::$button_refresh_preview, 0, -100);
		$I->clickAndWait(TplEdit::$button_refresh_preview, 5);
		$I->click(TplEdit::$toolbar['Save']);
	}

}
