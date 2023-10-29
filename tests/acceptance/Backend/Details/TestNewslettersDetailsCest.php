<?php
namespace Backend\Details;

use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\MainviewPage as MainView;
use Page\NewsletterEditPage as NlEdit;
use Page\NewsletterManagerPage as NlManage;

// @ToDo: Check "entered" values for publish_up/_down, set usable values (diff between both values) and check result in FE

/**
 * Class TestNewslettersDetailsCest
 *
 * This class contains all methods to test manipulation of a single newsletter at back end
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

 * @since   2.0.0
 */
class TestNewslettersDetailsCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage                 $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(LoginPage $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to create a single newsletter from main view and cancel creation
	 *
	 * @param   \AcceptanceTester            $I
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
	public function CreateOneNewsletterCancelMainView(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter and cancel from main view");

		NlEdit::addCustomField($I);
		NlEdit::addContentWithCustomField($I);

		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addNewsletterButton);

		NlEdit::fillFormSimple($I);
		$I->clickAndWait(Generals::$toolbar4['Back'], 1);

		$I->see(Generals::$extension, Generals::$pageTitle);
	}

	/**
	 * Test method to create a single newsletter from main view, save it and go back to main view
	 *
	 * @param   \AcceptanceTester            $I
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
	public function CreateOneNewsletterCompleteMainView(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter, archive and delete from main view");
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addNewsletterButton);

		$this->fillFormSimpleWithCampaign($I);
		$I->click(Generals::$toolbar4['Save']);

		// Check details attachment 1
		$I->scrollTo(NlEdit::$attachment_selected1, 0, -100);
		$I->wait(1);
		$I->seeElement(NlEdit::$attachment_selected1);

		// Check details attachment 2
		$I->scrollTo(NlEdit::$attachment_selected2, 0, -100);
		$I->wait(1);
		$I->seeElement(NlEdit::$attachment_selected2);

		$I->scrollTo(Generals::$toolbar4['Save & Close'],0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		NlEdit::checkSuccess($I, Generals::$admin['author']);

		// Check list view attachment
		$I->seeElement(NlEdit::$attachment_listview_icon);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter from list view and cancel creation
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterCancelListView(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter cancel list view");
		$I->amOnPage(NlManage::$url);
		$I->wait(3);
		$I->click(Generals::$toolbar['New']);
		$I->wait(3);

		$this->fillFormExtended($I);

		$I->click(Generals::$toolbar4['Cancel']);
		$I->see("Newsletters", Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter from list view, save it and go back to list view
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterCompleteListViewDefault(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter, archive and delete list view");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		NlEdit::checkSuccess($I, Generals::$admin['author']);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter from list view with Joomla article with custom field, checks preview
	 *
	 * @param   \AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   4.2.0
	 */
	public function CreateOneNewsletterCompleteListViewCustomfield(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter with Joomla article with custom fields, archive and delete list view");

		$I->amOnPage(NlManage::$url);
		$I->click(Generals::$toolbar['New']);

		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->fillField(NlEdit::$description, NlEdit::$field_description);

		// fill publish and unpublish
		NlEdit::fillPublishedDate($I);

		$I->scrollTo(NlEdit::$legend_templates, 0, -100);
		$I->wait(2);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);
		$I->wait(1);

		NlEdit::selectRecipients($I, false);

		// add content
		$I->scrollTo(NlEdit::$legend_content, 0, -100);
		$I->wait(1);

		// … by double click
		$I->see('blog = ' . NlEdit::$selectedContent_5, sprintf(NlEdit::$available_content, 1));
		$I->doubleClick(sprintf(NlEdit::$available_content, 1));
		$I->dontSee('blog = ' . NlEdit::$selectedContent_5, sprintf(NlEdit::$available_content, 1));

		// Check selected content
		$I->see('blog = ' . NlEdit::$selectedContent_5, sprintf(NlEdit::$selected_content, 1));

		$I->click(Generals::$toolbar4['Save']);

		// Check content success
		// Switch to tab 4 (preview)
		$I->scrollTo(NlEdit::$tab4, 0, -150);
		$I->wait(1);
		$I->clickAndWait(NlEdit::$tab4, 1);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(".//*[@class='article-data']", 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElementVisible(".//*[@class='article-data']");
		$I->seeElement(".//*[@class='article-data']/following-sibling::*[1]");
		$I->see('Test for custom fields', ".//*[@class='article-data']/following-sibling::*[1]");
		$I->seeElement(".//*[@class='article-data']/following-sibling::*[2]");
		$I->see('Author: About the Author: The author loves programming', ".//*[@class='article-data']/following-sibling::*[2]");
		$I->seeElement(".//*[@class='article-data']/following-sibling::*[3]");
		$I->see('Date: Date: 2023-06-18', ".//*[@class='article-data']/following-sibling::*[3]");

		$I->switchToIFrame();

//		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see('Test for custom fields');
		$I->see('Author: About the Author: The author loves programming');
		$I->see('Date: Date: 2023-06-18');

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter as content template from list view, save it and go back to list view
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterCompleteListViewTemplate(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter as content template, archive and delete list view");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);
		$I->selectOption(NlEdit::$is_template, 'Yes');

		$I->click(Generals::$toolbar4['Save & Close']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		NlEdit::checkSuccess($I, Generals::$admin['author']);
		$I->seeElement("//*/table[@id='main-table']/tbody/tr[1]/td[8]/button[contains(@class, 'data-state-1')]");

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter from list view, save it and go back to list view
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterSaveNewListView(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter, save and get new record list view");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->click(Generals::$toolbar4['Save & New']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->wait(1);
		$I->click(Generals::$systemMessageClose);

		$I->see('', NlEdit::$subject);
		$I->click(Generals::$toolbar4['Cancel']);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter from list view, save it, modify and save as copy
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterSaveCopyListViewDefault(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter, save, modify and save as copy");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->seeInField(NlEdit::$subject, NlEdit::$field_subject);

		// Grab ID of first newsletter
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_newsletters', 'id', array('subject' => NlEdit::$field_subject));

		$I->fillField(NlEdit::$subject, NlEdit::$field_subject2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->seeInField(NlEdit::$subject, NlEdit::$field_subject2);

		// Grab ID of second newsletter
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_newsletters', 'id', array('subject' => NlEdit::$field_subject2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a single Newsletter as template from list view, save it, modify and save as copy
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterSaveCopyListViewTemplate(\AcceptanceTester $I)
	{
		$I->wantTo("Create one newsletter as template, save, modify and save as copy");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);
		$I->selectOption(NlEdit::$is_template, 'Yes');

		$I->click(Generals::$toolbar4['Save']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->see("info", Generals::$alert_heading);
		$I->see(NlEdit::$message_template, Generals::$alert_info);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->dontSeeElement(NlEdit::$tab5);
		$I->clickAndWait(NlEdit::$tab2, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->dontSeeElement(NlEdit::$tab5);
		$I->clickAndWait(NlEdit::$tab3, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->dontSeeElement(NlEdit::$tab5);
		$I->clickAndWait(NlEdit::$tab4, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->dontSeeElement(NlEdit::$tab5);
		$I->clickAndWait(NlEdit::$tab1, 1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->dontSeeElement(NlEdit::$tab5);
		$I->seeInField(NlEdit::$subject, NlEdit::$field_subject);

		// Grab ID of first newsletter
		$id1 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_newsletters', 'id', array('subject' => NlEdit::$field_subject));

		$I->fillField(NlEdit::$subject, NlEdit::$field_subject2);

		$I->clickAndWait(Generals::$toolbarSaveActions, 1);
		$I->clickAndWait(Generals::$toolbar4['Save as Copy'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->seeInField(NlEdit::$subject, NlEdit::$field_subject2);

		$I->seeInField(NlEdit::$is_template, 'No');
		$I->seeElement(NlEdit::$tab5);
		$I->dontSee("Notice", Generals::$alert_info . ' ' . Generals::$alert_header );
		$I->dontSee(NlEdit::$message_template, Generals::$alert_info);


		// Grab ID of second newsletter
		$id2 = $I->grabColumnFromDatabase(Generals::$db_prefix . 'bwpostman_newsletters', 'id', array('subject' => NlEdit::$field_subject2));

		$I->assertGreaterThan($id1[0], $id2[0]);

		$I->click(Generals::$toolbar4['Cancel']);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to upload a file while creating a newsletter, cancel creation
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterWithFileUpload(\AcceptanceTester $I)
	{
		$I->setManifestOption('com_media', 'restrict_uploads', 0);

		$I->wantTo("Create one Newsletter and upload a file for attachment");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		// Ensure upload file doesn't exists
		try
		{
			$I->deleteFile(NlEdit::$attachment_upload_path . NlEdit::$attachment_upload_file_raw);
		}
		catch (\Exception $e)
		{
			codecept_debug("No file to delete or not accessible");
		}

		$I->clickAndWait(NlEdit::$attachments_add_button, 1);
		$I->clickAndWait(NlEdit::$attachment_select_button1, 1);
		$I->switchToIFrame(Generals::$media_frame1);
		$I->waitForElementVisible("//*[@id='toolbar']", 5);

		// Show file input field
		$I->executeJS("document.querySelector('input.hidden').className = 'visible';");

		// Upload file
		$I->attachFile("//*/input[@type='file']", NlEdit::$attachment_upload_file_raw);

		// Hide file input field
		$I->executeJS("document.querySelector('input.visible').className = 'hidden';");

		// Check upload success
		$I->waitForElementVisible(Generals::$alert_success, 10);
		$I->see(NlEdit::$attachment_upload_success, Generals::$alert_success);

		// Insert uploaded image
		$I->scrollTo(NlEdit::$attachment_upload_file, 0, -250);
		$I->wait(1);

		$I->clickAndWait(NlEdit::$attachment_upload_select, 1);
		$I->switchToIFrame();
		$I->clickAndWait(NlEdit::$attachment_insert1, 2);

		// See image at attachment at details page/frame
		$I->waitForElementVisible(NlEdit::$attachment, 10);

		// Delete currently uploaded file
		$I->clickAndWait(NlEdit::$attachment_select_button1, 1);
		$I->switchToIFrame(Generals::$media_frame1);
		$I->wait(1);

		$I->scrollTo(NlEdit::$attachment_upload_file, 0, -250);
		$I->wait(1);

		$I->clickAndWait(NlEdit::$attachment_upload_select, 1);
		$I->clickAndWait(NlEdit::$attachment_upload_delete, 1);
		$I->clickAndWait(NlEdit::$attachment_media_delete_confirm, 1);
		$I->switchToIFrame();
		$I->clickAndWait(NlEdit::$attachment_cancel, 1);

		$I->click(NlEdit::$toolbar['Cancel']);
		$I->see("Newsletters", Generals::$pageTitle);

		$I->setManifestOption('com_media', 'restrict_uploads', 1);
	}

	/**
	 * Test method to create a single Newsletter from list view, save it and go back to list view
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateOneNewsletterListViewRestore(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter list view, archive and restore");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);

		NlEdit::checkSuccess($I, Generals::$admin['author']);

		$I->HelperArchiveItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array);

		$I->switchToArchive($I, NlEdit::$arc_del_array['archive_tab']);

		$I->HelperRestoreItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array);

		$I->amOnPage(NlManage::$url);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create multiple newsletters from list view
	 * this is only to create automated some test data!!!!!
	 *
	 * @param   \AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	/*
	public function CreateMultipleNewslettersListView(\AcceptanceTester $I)
	{
		$I->wantTo("Create multiple Newsletters from list view");
		$I->amOnPage(NlManage::$url);

		for ($i = 1; $i <= 25; $i++)
		{
			$I->click(Generals::$toolbar['New']);

			$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
			$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
			$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
			$I->fillField(NlEdit::$subject, 'Newsletter for testing ' . $i);
			$I->fillField(NlEdit::$description, NlEdit::$field_description . ' ' . $i);

			//select attachment
			if (($i % 3) == 0)
			{
				Helper->fillReadonlyInput($I, NlEdit::$attachment_id, NlEdit::$attachment, NlEdit::$field_attachment);
			}

			// fill publish and unpublish
			if (($i % 4) == 0)
			{
				$I->click(NlEdit::$publish_up_button);
				$I->clickAndWait(NlEdit::$today_up, 1);
				$I->click(NlEdit::$publish_down_button);
				$I->clickAndWait(NlEdit::$today_down, 1);
			}

			$I->scrollTo(NlEdit::$legend_templates);
			$I->click(NlEdit::$template_html);
			$I->click(NlEdit::$template_text);

			$I->scrollTo(NlEdit::$legend_recipients);
			$I->click(sprintf(NlEdit::$mls_accessible, 2));
			//		$I->click(sprintf(NlEdit::$mls_nonaccessible, 3));
			//		$I->click(sprintf(NlEdit::$mls_internal, 4));

			$I->scrollTo(NlEdit::$legend_content);
			$I->doubleClick(sprintf(NlEdit::$available_content, ($i % 6) + 1));

		$I->click(NlEdit::$toolbar['Save & Close']);

		$I->waitForElement(Generals::$alert_heading);
		$I->see("Message", Generals::$alert_heading);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);
	}
	}
	*/

	/**
	 * Test method to create same single Newsletter twice from main view
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CreateNewsletterTwiceListView(\AcceptanceTester $I)
	{
		$I->wantTo("Create Newsletter twice list view");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		NlEdit::checkSuccess($I, Generals::$admin['author']);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see(Generals::$alert_warn_txt, Generals::$alert_header);
		$I->see(sprintf(NlEdit::$warn_save, NlEdit::$field_subject), Generals::$alert_warn);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		NlEdit::checkSuccess($I, Generals::$admin['author']);

		$I->see("Newsletters", Generals::$pageTitle);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to copy a newsletter
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CopyNewsletterOnly(\AcceptanceTester $I)
	{
		NlEdit::copyNewsletter($I, Generals::$admin['author']);
	}

	/**
	 * Test method to copy a newsletter
	 *
	 * @param   \AcceptanceTester                $I
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
	public function CopyNewsletterTemplate(\AcceptanceTester $I)
	{
		NlEdit::copyNewsletter($I, Generals::$admin['author'], true, true);
	}

	/**
	 * Test method to create send newsletter to test recipients
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterToTestrecipients(\AcceptanceTester $I)
	{
		$I->wantTo("Send newsletter to test recipients");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		$content_title = NlEdit::fillFormSimple($I);
		$I->wait(2);

		$start  = strpos($content_title, "=") + 2;
		$content_title  = substr($content_title, $start);

		// change to tab 2
		$I->scrollTo(Generals::$nlTabBar, 0, -100);
		$I->wait(1);
		$I->clickAndWait(NlEdit::$tab2, 3);
		$I->executeJS("document.getElementById('" . NlEdit::$tab2_iframe . "').setAttribute('name', '" . NlEdit::$tab2_iframe . "');");
		$I->switchToIFrame(NlEdit::$tab2_iframe);
		$I->waitForElement(NlEdit::$tab2_editor);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$tab2_editor);
		$I->switchToIFrame();

		// change to tab 3
		$I->scrollTo(Generals::$nlTabBar, 0, -100);
		$I->wait(1);
		$I->clickAndWait(NlEdit::$tab3, 3);
		$I->waitForElement(NlEdit::$tab3_editor);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$tab3_editor);

		// change to tab 4
		$I->scrollTo(Generals::$nlTabBar, 0, -100);
		$I->wait(1);
		$I->clickAndWait(NlEdit::$tab4, 5);
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(NlEdit::$tab4_preview_html_divider, 0, 20); // scroll to divider before article
		$I->wait(1);
		$I->waitForElement(NlEdit::$preview_html);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$preview_html);
		$I->switchToIFrame();
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$preview_text);
		$I->switchToIFrame();

		// change to tab 5
		$I->scrollTo(Generals::$nlTabBar, 0, -100);
		$I->wait(1);
		$I->clickAndWait(NlEdit::$tab5, 1);
		$I->clickAndWait(NlEdit::$button_send_test, 1);

		$I->seeInPopup(NlEdit::$popup_send_confirm);
		$I->acceptPopup();
		$I->wait(1);

		$I->waitForElementVisible(NlManage::$sendLayout, 5);
		$I->waitForText(NlEdit::$success_send_ready, 180);
		$I->see(NlEdit::$success_send_ready);

		$I->click(NlManage::$sendLayoutBack);
		$I->waitForElementVisible(Generals::$page_header, 10);
		$I->see("Newsletters", Generals::$pageTitle);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to send newsletter to real recipients, publish option set to no
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterToRealRecipientsPublishOptionNo(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter with two attachments to real recipients with publish option no");
		$I->expectTo("see unpublished sent newsletter");

		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '0');

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author'], false, true);

		NlEdit::SendNewsletterToRealRecipients($I, false, false, false, 10);

		NlEdit::checkStatusOfSentNewsletter($I, NlManage::$first_line_unpublished);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to send newsletter to real recipients, publish option set to yes
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterToRealRecipientsPublishOptionYes(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter to real recipients with publish option yes");
		$I->expectTo("see published sent newsletter");

		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '1');

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEdit::SendNewsletterToRealRecipients($I, false, false, false, 10);

		NlEdit::checkStatusOfSentNewsletter($I, NlManage::$first_line_published);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '0');
	}

	/**
	 * Test method to send and publish newsletter to real recipients, publish option set to no
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendPublishNewsletterToRealRecipientsPublishOptionNo(\AcceptanceTester $I)
	{
		$I->wantTo("Send and publish a newsletter to real recipients with publish option no");
		$I->expectTo("see published sent newsletter");

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEdit::SendNewsletterToRealRecipients($I, false, false, false, 10, true);

		NlEdit::checkStatusOfSentNewsletter($I, NlManage::$first_line_published);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to send and publish newsletter to real recipients, publish option set to yes
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendPublishNewsletterToRealRecipientsPublishOptionYes(\AcceptanceTester $I)
	{
		$I->wantTo("Send and publish a newsletter to real recipients with publish option yes");
		$I->expectTo("see published sent newsletter");

		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '1');

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEdit::SendNewsletterToRealRecipients($I, false, false, false, 10, true);

		NlEdit::checkStatusOfSentNewsletter($I, NlManage::$first_line_published);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '0');
	}

	/**
	 * Test method to create a newsletter and send also to a unconfirmed recipients
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterToUnconfirmed(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter also to a unconfirmed recipients");

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEdit::SendNewsletterToRealRecipients($I, true, false, false, 15);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create a newsletter and send to a real usergroup
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterToRealUsergroup(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter to a real user group");

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author'], true);

		NlEdit::SendNewsletterToRealRecipients($I, false, true, false, 6);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to send newsletter to real recipients, publish option set to no
	 *
	 * @param   \AcceptanceTester                $I
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
	public function SendNewsletterIsTemplate(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter which is template");
		$I->expectTo("see error message");

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);
		$I->clickAndWait(NlEdit::$change_is_template, 2);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->click(NlEdit::$mark_to_send);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar4['Send']);

		$I->waitForElementVisible(Generals::$alert_header, 5);
		$I->see('Newsletters', Generals::$pageTitle);
		$I->see("danger", Generals::$alert_header);
		$I->see(NlEdit::$is_template_error, Generals::$alert_error_1);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to edit a sent newsletter
	 *
	 * @param   \AcceptanceTester                $I
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
	public function EditSentNewsletter(\AcceptanceTester $I)
	{
		$I->wantTo("edit published, publish up and down and change description of a sent newsletter");

		$I->amOnPage(NlManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->clickAndWait(NlManage::$tab2, 1);

		$I->click(NlManage::$first_list_entry_tab2);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar4['Edit']);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see('Newsletter Publishing Details', Generals::$pageTitle);

		// make changes
		$I->selectOption(NlEdit::$published, 'published');

		$I->fillField(NlEdit::$publish_up, NlEdit::$field_edit_publish_up);
		$I->pressKey(NlEdit::$publish_up, \Facebook\WebDriver\WebDriverKeys::TAB);

		$I->fillField(NlEdit::$publish_down, NlEdit::$field_edit_publish_down);
		$I->pressKey(NlEdit::$publish_down, \Facebook\WebDriver\WebDriverKeys::TAB);

		$I->fillField(NlEdit::$description, NlEdit::$field_edit_description);

		$I->click(Generals::$toolbar['Save']);
		Generals::dontSeeAnyWarning($I);

		$I->see(NlEdit::$success_saved, Generals::$alert_success);

		// check changes
		$publish_up = $I->grabValueFrom(NlEdit::$publish_up);
		$I->assertEquals(NlEdit::$field_edit_publish_up, $publish_up);

		$publish_down = $I->grabValueFrom(NlEdit::$publish_down);
		$I->assertEquals(NlEdit::$field_edit_publish_down, $publish_down);

		$I->see(NlEdit::$field_edit_description, NlEdit::$description);

		$I->click(Generals::$toolbar['Cancel']);

		$I->see('Newsletters', Generals::$pageTitle);
		$I->clickAndWait(NlManage::$tab2, 1);

		// check changes in list
		$I->seeElement(NlManage::$publish_by_icon['publish_result']);
		$I->see(NlEdit::$field_edit_publish_up, NlManage::$sent_column_publish_up);
		$I->see(NlEdit::$field_edit_publish_down, NlManage::$sent_column_publish_down);
		$I->see(NlEdit::$field_edit_description, NlManage::$sent_column_description);

		// revert changes
		$I->click(NlManage::$first_list_link);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see('Newsletter Publishing Details', Generals::$pageTitle);

		// make changes
		$I->selectOption(NlEdit::$published, 'unpublished');

		$I->fillField(NlEdit::$publish_up, NlEdit::$field_publish_up);
		$I->pressKey(NlEdit::$publish_up, \Facebook\WebDriver\WebDriverKeys::TAB);

		$I->fillField(NlEdit::$publish_down, NlEdit::$field_publish_down);
		$I->pressKey(NlEdit::$publish_up, \Facebook\WebDriver\WebDriverKeys::TAB);

		$I->fillField(NlEdit::$description, NlEdit::$field_description);

		$I->click(Generals::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$pageTitle, 30);
		Generals::dontSeeAnyWarning($I);

		$I->see(NlEdit::$success_saved, Generals::$alert_success);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->clickAndWait(NlManage::$tab2, 1);

		// check changes in list
		$I->seeElement(NlManage::$publish_by_icon['unpublish_result']);
		$I->see(NlEdit::$field_publish_up, NlManage::$sent_column_publish_up);
		$I->see(NlEdit::$field_publish_down, NlManage::$sent_column_publish_down);
		$I->see(NlEdit::$field_description, NlManage::$sent_column_description);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   LoginPage            $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(\AcceptanceTester $I, LoginPage $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

	/**
	 * Method to fill form with campaign (no other recipients to select) without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 *
	 * @throws \Exception
	 */
	private function fillFormSimpleWithCampaign(\AcceptanceTester $I)
	{
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->fillField(NlEdit::$description, NlEdit::$field_description);

		// select campaign
		$I->selectOption(NlEdit::$campaign, NlEdit::$campaign_selected);
		$I->dontSeeElement(NlEdit::$legend_recipients);

		//select attachment
		NlEdit::selectAttachment($I);

		// fill publish and unpublish
		NlEdit::fillPublishedDate($I);

		$I->scrollTo(NlEdit::$legend_templates, 0, -100);
		$I->wait(1);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);

		$content_1 = $I->grabTextFrom(sprintf(NlEdit::$available_content, 3));
		$I->click(sprintf(NlEdit::$available_content, 3));
		$I->click(NlEdit::$add_content);
		$I->see($content_1, NlEdit::$selected_content_list);
		$I->dontSee($content_1, NlEdit::$available_content_list);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
	}

	/**
	 * Method to fill form with check of required fields
	 * This method fills in the end all fields, but meanwhile all required fields are omitted, one by one,
	 * to check if the related messages appears
	 * This method also checks removing content
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private function fillFormExtended(\AcceptanceTester $I)
	{
		// omit recipients
		$I->click(NlEdit::$toolbar['Save']);
		$I->seeInPopup(NlEdit::$msg_no_recipients);
		$I->acceptPopup();

		// always select recipients, without that other warnings don't appear
		NlEdit::selectRecipients($I);

		// omit from_name
		$I->scrollTo(NlEdit::$legend_general, 0, -100);
		$I->wait(1);
		$I->fillField(NlEdit::$from_name, '');
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(Generals::$toolbar4['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_sender_name, Generals::$alert_warn);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// omit from_email
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general, 0, -100);
		$I->wait(1);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(Generals::$toolbar4['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_sender_email, Generals::$alert_warn);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// omit reply_email
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general, 0, -100);
		$I->wait(1);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$reply_email, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(Generals::$toolbar4['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_replyto_email, Generals::$alert_warn);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		// omit subject
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general, 0, -100);
		$I->wait(1);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(Generals::$toolbar4['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_subject, Generals::$alert_warn);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general, 0, -100);
		$I->wait(1);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->fillField(NlEdit::$description, NlEdit::$field_description);
		$I->clickAndWait(NlEdit::$description, 1);

		//select attachment
		NlEdit::selectAttachment($I);

		// fill publish and unpublish
		NlEdit::fillPublishedDate($I);

		$I->scrollTo(NlEdit::$legend_templates, 0, -100);
		$I->wait(1);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);
		$I->scrollTo(NlEdit::$legend_recipients, 0, -100);
		$I->wait(1);

		// add content
		$I->scrollTo(NlEdit::$legend_content, 0, -100);
		$I->wait(1);

		// …by button
		$I->see(NlEdit::$selectedContent_1, sprintf(NlEdit::$available_content, 4));
		$I->click(sprintf(NlEdit::$available_content, 4));
		$I->click(NlEdit::$add_content);
		$I->dontSee(NlEdit::$selectedContent_1, sprintf(NlEdit::$available_content, 4));

		// … by double click
		$I->see(NlEdit::$selectedContent_2, sprintf(NlEdit::$available_content, 4));
		$I->doubleClick(sprintf(NlEdit::$available_content, 4));
		$I->dontSee(NlEdit::$selectedContent_2, sprintf(NlEdit::$available_content, 4));

		$I->see(NlEdit::$selectedContent_3, sprintf(NlEdit::$available_content, 3));
		$I->doubleClick(sprintf(NlEdit::$available_content, 3));
		$I->dontSee(NlEdit::$selectedContent_3, sprintf(NlEdit::$available_content, 3));


		// Get popup selectors depending on Joomla version
		$jVersion = $I->getJoomlaMainVersion($I);

		$popupSelectorSelect  = NlEdit::$popupSelectorSelect_5;
		$popupSelectorClear   = NlEdit::$popupSelectorClear_5;
		$popupModalIdentifier = NlEdit::$popupModalIdentifier_5;
		$popupIframe          = NlEdit::$popupIframe_5;

		if ($jVersion !== 5)
		{
			$popupSelectorSelect  = NlEdit::$popupSelectorSelect_4;
			$popupSelectorClear   = NlEdit::$popupSelectorClear_4;
			$popupModalIdentifier = NlEdit::$popupModalIdentifier_4;
			$popupIframe          = NlEdit::$popupIframe_4;
		}

		// … by popup selector
		$I->see(NlEdit::$popupSelectorSelectText, $popupSelectorSelect);
		$I->clickAndWait($popupSelectorSelect, 2);
		$I->waitForElementVisible($popupModalIdentifier, 5);
		$I->switchToIFrame($popupIframe);

		// Filter popup list
		$I->clickAndWait(NlEdit::$popupFilterbarIdentifier, 2);
		$I->see(NlEdit::$popupFilteredArticleText, NlEdit::$popupFilteredArticleIdentifier);
		$I->clickAndWait(NlEdit::$popupFilterbarCategoryList, 1);
		$I->clickAndWait(NlEdit::$popupFilterbarCategorySelection, 1);
		$I->dontSee(NlEdit::$popupFilteredArticleText, NlEdit::$popupFilteredArticleIdentifier);

		// Select content
		$I->clickAndWait(NlEdit::$popupFilterbarContentSelection, 1);
		$I->switchToIFrame();
		$I->wait(1);
		$I->see(NlEdit::$popupSelectorClearText, $popupSelectorClear);

		// Add content to selected content
		$I->clickAndWait(NlEdit::$popupSelectorMover, 1);

		if ($jVersion === '4')
		{
			$I->see(NlEdit::$popupSelectorSelectText, $popupSelectorSelect);
		}
		else
		{
			//@ToDo: make JS working (window.processModalParent is not a function at admin-bwpm-nl.js at line 295)
		}

		// Check selected content
		$I->see(NlEdit::$selectedContent_1, sprintf(NlEdit::$selected_content, 1));
		$I->see(NlEdit::$selectedContent_2, sprintf(NlEdit::$selected_content, 2));
		$I->see(NlEdit::$selectedContent_3, sprintf(NlEdit::$selected_content, 3));
		$I->see(NlEdit::$selectedContent_4, sprintf(NlEdit::$selected_content, 4));

		// Sort content by content mover
		$I->clickAndWait(sprintf(NlEdit::$selected_content, 1), 1);
		$I->clickAndWait(NlEdit::$move_down, 1);
		// Workaround because previously selected entry is not deselected
		$I->clickAndWait(sprintf(NlEdit::$selected_content, 2), 1);

		$I->clickAndWait(sprintf(NlEdit::$selected_content, 4), 1);
		$I->clickAndWait(NlEdit::$move_up, 1);
		// Workaround because previously selected entry is not deselected
		$I->clickAndWait(sprintf(NlEdit::$selected_content, 3), 1);

		// Check order of selected content
		$I->see(NlEdit::$selectedContent_2, sprintf(NlEdit::$selected_content, 1));
		$I->see(NlEdit::$selectedContent_1, sprintf(NlEdit::$selected_content, 2));
		$I->see(NlEdit::$selectedContent_4, sprintf(NlEdit::$selected_content, 3));
		$I->see(NlEdit::$selectedContent_3, sprintf(NlEdit::$selected_content, 4));

		// remove content by button
		$I->click(sprintf(NlEdit::$selected_content, 3));
		$I->click(NlEdit::$remove_content);

		// Check selected content
		$I->see(NlEdit::$selectedContent_2, sprintf(NlEdit::$selected_content, 1));
		$I->see(NlEdit::$selectedContent_1, sprintf(NlEdit::$selected_content, 2));
		$I->dontSee(NlEdit::$selectedContent_4, sprintf(NlEdit::$selected_content, 3));
		$I->see(NlEdit::$selectedContent_3, sprintf(NlEdit::$selected_content, 3));

		// remove content by double click
		$I->doubleClick(sprintf(NlEdit::$selected_content, 1));
		$I->wait(1);

		// Check selected content
		$I->dontSee(NlEdit::$selectedContent_2, sprintf(NlEdit::$selected_content, 1));
		$I->see(NlEdit::$selectedContent_1, sprintf(NlEdit::$selected_content, 1));
		$I->dontSee(NlEdit::$selectedContent_4, sprintf(NlEdit::$selected_content, 2));
		$I->see(NlEdit::$selectedContent_3, sprintf(NlEdit::$selected_content, 2));

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
	}
}
