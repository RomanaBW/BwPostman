<?php
namespace Backend\Details;

use Page\Generals as Generals;
use Page\NewsletterEditPage as NlEdit;
use Page\NewsletterManagerPage as NlManage;
use Page\MainviewPage as MainView;
use Page\Login as LoginPage;

// @ToDo: Check "entered" values for publish_up/_down, set usable values (diff between both values) and check result in FE

/**
 * Class TestNewslettersDetailsCest
 *
 * This class contains all methods to test manipulation of a single newsletter at back end
 *
 * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see(Generals::$extension, Generals::$pageTitle);
		$I->click(MainView::$addNewsletterButton);

		NlEdit::fillFormSimple($I);
		$I->clickAndWait(NlEdit::$toolbar['Back'], 1);

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

		$I->click(NlEdit::$toolbar['Save & Close']);
		NlEdit::checkSuccess($I, Generals::$admin['author']);

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

		$I->click(NlEdit::$toolbar['Cancel']);
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
	public function CreateOneNewsletterCompleteListView(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter, archive and delete list view");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(NlEdit::$toolbar['Save & Close']);

		$I->waitForElement(Generals::$alert_header, 30);
		NlEdit::checkSuccess($I, Generals::$admin['author']);

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
	public function CreateOneNewsletterListViewRestore(\AcceptanceTester $I)
	{
		$I->wantTo("Create one Newsletter list view, archive and restore");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(NlEdit::$toolbar['Save & Close']);

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

		$I->waitForElement(Generals::$alert_header);
		$I->see("Message", Generals::$alert_header);
		$I->see(NlEdit::$success_saved, Generals::$alert_msg);
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

		$I->click(NlEdit::$toolbar['Save & Close']);
		NlEdit::checkSuccess($I, Generals::$admin['author']);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->click(Generals::$toolbar['New']);

		NlEdit::fillFormSimple($I);

		$I->click(NlEdit::$toolbar['Save & Close']);
		NlEdit::checkSuccess($I, Generals::$admin['author']);

		$I->see(Generals::$alert_warn_txt, Generals::$alert_header);
		$I->see(sprintf(NlEdit::$warn_save, NlEdit::$field_subject), Generals::$alert);

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
	 * @since   2.0.0
	 */
	public function CopyNewsletter(\AcceptanceTester $I)
	{
		NlEdit::copyNewsletter($I, Generals::$admin['author']);
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
		$I->scrollTo(Generals::$sys_message_container, 0, -100);
		$I->clickAndWait(NlEdit::$tab2, 3);
		$I->switchToIFrame(NlEdit::$tab2_iframe);
		$I->waitForElement(NlEdit::$tab2_editor);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$tab2_editor);
		$I->switchToIFrame();

		// change to tab 3
		$I->clickAndWait(NlEdit::$tab3, 3);
		$I->waitForElement(NlEdit::$tab3_editor);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$tab3_editor);

		// change to tab 4
		$I->clickAndWait(NlEdit::$tab4, 5);
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(NlEdit::$tab4_preview_html_divider, 0, 20); // scroll to divider before article
		$I->waitForElement(NlEdit::$preview_html);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$preview_html);
		$I->switchToIFrame();
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);
		$I->waitForText($content_title, 30);
		$I->see($content_title, NlEdit::$preview_text);
		$I->switchToIFrame();

		// change to tab 5
		$I->scrollTo(Generals::$sys_message_container, 0, -100);
		$I->clickAndWait(NlEdit::$tab5, 1);
		$I->clickAndWait(NlEdit::$button_send_test, 1);

		$I->seeInPopup(NlEdit::$popup_send_confirm);
		$I->acceptPopup();

		$user = getenv('BW_TESTER_USER');

		if (!$user)
		{
			$user = 'root';
		}

		if ($user == 'jenkins')
		{
			$I->wait(1);
		}

		$I->waitForElement(NlEdit::$tab5_send_iframeId, 20);
		$I->switchToIFrame(NlEdit::$tab5_send_iframe);
		$I->waitForText(NlEdit::$success_send, 300);
		$I->see(NlEdit::$success_send);
		$I->switchToIFrame();
		$I->wait(5);

		$I->see("Newsletters", Generals::$pageTitle);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create copy newsletter and send to real recipients
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
	public function SendNewsletterToRealRecipients(\AcceptanceTester $I)
	{
		$I->wantTo("Send a newsletter to real recipients");

		NlEdit::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		NlEdit::SendNewsletterToRealRecipients($I, false, false, false, 20);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create copy newsletter and send to a real usergroup
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

		NlEdit::SendNewsletterToRealRecipients($I, false, true, false, 1);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create copy newsletter and send to a real usergroup
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

		NlEdit::SendNewsletterToRealRecipients($I, true, false, false, 30);

		$I->HelperArcDelItems($I, NlManage::$arc_del_array, NlEdit::$arc_del_array, true);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create copy newsletter and send to real recipients
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
		if (getenv('BWPM_VERSION') != '132')
		{
			$I->wantTo("edit published, publish up and down and change description of a sent newsletter");

			$I->amOnPage(NlManage::$url);
			$I->waitForElement(Generals::$pageTitle, 30);
			$I->clickAndWait(NlManage::$tab2, 1);

			$I->click(NlManage::$first_list_entry_tab2);
			$I->click(Generals::$toolbar['Edit']);
			$I->waitForElement(Generals::$pageTitle, 30);
			$I->see('Newsletter Publishing Details', Generals::$pageTitle);

			// make changes
			$I->clickSelectList(NlEdit::$published_list, NlEdit::$published_published, NlEdit::$published_list_id);

			$I->fillField(NlEdit::$publish_up, NlEdit::$field_edit_publish_up);
			$I->pressKey(NlEdit::$publish_up, \WebDriverKeys::TAB);

			$I->fillField(NlEdit::$publish_down, NlEdit::$field_edit_publish_down);
			$I->pressKey(NlEdit::$publish_down, \WebDriverKeys::TAB);

			$I->fillField(NlEdit::$description, NlEdit::$field_edit_description);

			$I->click(Generals::$toolbar['Save']);
			Generals::dontSeeAnyWarning($I);

			$I->see("Message", Generals::$alert_header);
			$I->see(NlEdit::$success_saved, Generals::$alert_msg);

			// check changes
			$I->see("published", NlEdit::$published_list_text);

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
			$I->clickSelectList(NlEdit::$published_list, NlEdit::$published_unpublished, NlEdit::$published_list_id);

			$I->fillField(NlEdit::$publish_up, NlEdit::$field_publish_up);
			$I->pressKey(NlEdit::$publish_up, \WebDriverKeys::TAB);

			$I->fillField(NlEdit::$publish_down, NlEdit::$field_publish_down);
			$I->pressKey(NlEdit::$publish_up, \WebDriverKeys::TAB);

			$I->fillField(NlEdit::$description, NlEdit::$field_description);

			$I->click(Generals::$toolbar['Save & Close']);
			$I->waitForElement(Generals::$pageTitle, 30);
			Generals::dontSeeAnyWarning($I);

			$I->see("Message", Generals::$alert_header);
			$I->see(NlEdit::$success_saved, Generals::$alert_msg);
			$I->see('Newsletters', Generals::$pageTitle);

			$I->clickAndWait(NlManage::$tab2, 1);

			// check changes in list
			$I->seeElement(NlManage::$publish_by_icon['unpublish_result']);
			$I->see(NlEdit::$field_publish_up, NlManage::$sent_column_publish_up);
			$I->see(NlEdit::$field_publish_down, NlManage::$sent_column_publish_down);
			$I->see(NlEdit::$field_description, NlManage::$sent_column_description);
		}
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   LoginPage            $loginPage
	 *
	 * @return  void
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

		$I->scrollTo(NlEdit::$legend_templates);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);

		$content_1 = $I->grabTextFrom(sprintf(NlEdit::$available_content, 2));
		$I->click(sprintf(NlEdit::$available_content, 2));
		$I->click(NlEdit::$add_content);
		$I->see($content_1, NlEdit::$selected_content_list);
		$I->dontSee($content_1, NlEdit::$available_content_list);
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
		$I->scrollTo(NlEdit::$legend_general);
		$I->fillField(NlEdit::$from_name, '');
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(NlEdit::$toolbar['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_sender_name, Generals::$alert_msg);

		// omit from_email
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(NlEdit::$toolbar['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_sender_email, Generals::$alert_msg);

		// omit reply_email
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$reply_email, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(NlEdit::$toolbar['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_replyto_email, Generals::$alert_msg);

		// omit subject
		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general);
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, '');
		$I->clickAndWait(NlEdit::$description, 1);
		$I->click(NlEdit::$toolbar['Save']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see(Generals::$alert_warn_txt);
		$I->see(NlEdit::$msg_required_subject, Generals::$alert_msg);

		NlEdit::selectRecipients($I);
		$I->scrollTo(NlEdit::$legend_general);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->fillField(NlEdit::$description, NlEdit::$field_description);
		$I->clickAndWait(NlEdit::$description, 1);

		//select attachment
		NlEdit::selectAttachment($I);

		// fill publish and unpublish
		NlEdit::fillPublishedDate($I);

		$I->scrollTo(NlEdit::$legend_templates);
		$I->wait(1);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);
		$I->scrollTo(NlEdit::$legend_recipients);

		// add content
		$I->scrollTo(NlEdit::$legend_content, 0, -100);
		// …by button
		$I->click(sprintf(NlEdit::$available_content, 3));
		$I->click(NlEdit::$add_content);
		// … by double click
		$I->doubleClick(sprintf(NlEdit::$available_content, 3));
		$I->doubleClick(sprintf(NlEdit::$available_content, 2));

		// remove content
		// …by button
		$I->click(sprintf(NlEdit::$selected_content, 3));
		$I->click(NlEdit::$remove_content);
		// …by double click
		$I->doubleClick(sprintf(NlEdit::$selected_content, 1));
		$I->wait(1);
	}
}
