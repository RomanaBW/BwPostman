<?php
namespace Page;

use Page\NewsletterManagerPage as NlManage;
use Page\Generals as Generals;

/**
 * Class NewsletterEditPage
 *
 * @package Page
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
class NewsletterEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=newsletter&layout=edit_basic';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     *
     * @since   2.0.0
     */

    public static $tab1             = ".//*[@id='adminForm']/div[1]/ul/li[1]/button";
	public static $tab2             = ".//*[@id='adminForm']/div[1]/ul/li[2]/button";
	public static $tab3             = ".//*[@id='adminForm']/div[1]/ul/li[3]/button";
	public static $tab4             = ".//*[@id='adminForm']/div[1]/ul/li[4]/button";
	public static $tab5             = ".//*[@id='adminForm']/div[1]/ul/li[5]/button";

	public static $tab1_legend1      = "General information";
	public static $tab1_legend2      = "Newslettertemplates";
	public static $tab1_legend3      = "Recipients";
	public static $tab1_legend4      = "Website content";

	public static $tab2_legend      = "HTML Version";
	public static $tab2_editor      = ".//*[@id='tinymce']";
	public static $tab2_iframe      = "jform_html_version_ifr";

	public static $tab3_legend      = "Text Version";
	public static $tab3_editor      = ".//*[@id='jform_text_version']";

	public static $tab4_legend1                 = "Email header";
	public static $tab4_preview_html            = ".//*[@id='adminForm']/div[3]/fieldset[2]/div";
	public static $tab4_preview_html_iframe     = "myIframeHtml";
	public static $tab4_preview_html_divider    = "/html/body/table[1]/tbody/tr[5]/td";
	public static $tab4_preview_text            = "Preview of text newsletter";
	public static $tab4_preview_text_iframe     = "myIframeText";

	public static $preview_html      = ".//*/table[2]/tbody/tr[1]/td/h1";
	public static $preview_text      = ".//*[@id='preview_text']/textarea";

	public static $tab5_legend1      = "Send newsletters";
	public static $tab5_legend2      = "Send test newsletters";
	public static $tab5_send_iframe  = "sendFrame";

	public static $checkbox_unconfirmed = ".//*[@id='send_to_unconfirmed']";
	public static $button_send          = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[1]";
	public static $button_send_publish  = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[2]";
	public static $button_send_test     = ".//*[@id='adminForm']/div[3]/fieldset[2]/div/table/tbody/tr[2]/td[2]/input";
	public static $success_send         = 'The newsletters are sent';
	public static $success_send_ready   = 'All newsletters in the queue';
    public static $success_send_number  = '0  of  %s  newsletters need to be sent.';

    public static $nbr_only_confirmed   = 128;
    public static $nbr_unconfirmed      = 83;
    public static $nbr_usergroup        = 4;

	public static $mark_to_send         = ".//*[@id='cb0']";
	public static $duplicate_prefix     = "Copy of '";

	public static $from_name            = ".//*[@id='jform_from_name']";
	public static $from_email           = ".//*[@id='jform_from_email']";
	public static $reply_email          = ".//*[@id='jform_reply_email']";
	public static $subject              = ".//*[@id='jform_subject']";
	public static $campaign             = ".//*[@id='jform_campaign_id']";
	public static $campaign_selected    = "01 Kampagne 5 A";
	public static $description          = ".//*[@id='jform_description']";

	public static $attachment                   = ".//*[@id='jform_attachment']";
	public static $attachment_id                = "jform_attachment";

	public static $attachment_select_button     = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/div/div[1]/ul/li[5]/div/div/a[1]";
	public static $attachment_select            = "html/body/ul/li/a[contains(@href,'joomla_black.png')]";
	public static $attachment_insert            = ".//*[@id='imageForm']/div[2]/div/div[2]/button[1]";
	public static $attachment_media_url_field   = ".//*[@id='f_url']";
	public static $attachment_url               = "images/joomla_black.png";

	public static $publish_up           = ".//*[@id='jform_publish_up']";
	public static $publish_up_button    = ".//*[@id='jform_publish_up_img']";
	public static $publish_down         = ".//*[@id='jform_publish_down']";
	public static $publish_down_button  = ".//*[@id='jform_publish_down_img']";
//	public static $today_up             = "html/body/div[10]/table/thead/tr[2]/td[3]/div";
	public static $today_up             = ".//*[@class='calendar']/table/thead/tr[2]/td[3]/div[contains(text(), 'Today')]";
    public static $today_down           = "html/body/div[11]/table/thead/tr[2]/td[3]/div";

	public static $template_html    = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/div[1]/div/fieldset/div/div/label/div/span[contains(text(),'Standard Basic')]";// Template Standard Basic [3]
	public static $template_text    = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/div[2]/div/fieldset/div/div/label/div/span[contains(text(),'Standard TEXT Template 3')]";

	public static $msg_required_sender_name     = "Field required: Sender's name";
	public static $msg_required_sender_email    = "Field required: Sender's email";
	public static $msg_required_replyto_email   = "Field required: 'Reply to' email";
	public static $msg_required_subject         = "Field required: Subject";
	public static $msg_no_recipients            = "No recipients selected!";

	public static $field_from_name    = "Sam";
	public static $field_from_email   = "sam.sample@tester-net.nil";
	public static $field_reply_email  = "sample@tester-net.nil";
	public static $field_subject      = "1. Simple Single Test Newsletter";
	public static $field_description  = 'Description for the test newsletter';
	public static $field_attachment   = 'images/joomla_black.png';
	public static $field_campaign     = '';
	public static $field_publish_up   = '';
	public static $field_publish_down = '';

	public static $field_edit_publish_up    = '2017-03-14 17:00:00';
	public static $field_edit_publish_down  = '2017-03-25 17:00:00';
	public static $field_edit_description   = 'Changed description for the test newsletter';

	public static $legend_general       = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/legend";
	public static $legend_templates     = ".//*[@id='adminForm']/div[3]/fieldset[1]/legend";
	public static $legend_recipients    = ".//*[@id='recipients']/div/div[1]/div/fieldset/legend";
	public static $legend_content       = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/legend";

	public static $required_from_name   = "Field required: Sender's name";
	public static $required_from_email  = "Field required: Sender's email";
	public static $required_reply_email = "Field required: 'Reply to' email";
	public static $required_subject     = 'Field required: Subject';

	public static $popup_recipients     = 'No recipients selected!';
	public static $success_saved        = 'Newsletter saved successfully!';
	public static $warn_save            = "A newsletter with subject '%s' already exists. Do You have an appropriate description?";
	public static $error_save           = 'Save failed with the following error:';
	public static $popup_send_confirm   = 'Do you wish to send the newsletter?';

	public static $success_inList_subject   = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]";
	public static $success_inList_desc      = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[4]";
	public static $success_inList_author    = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[6]";

	public static $field_title          = "1. Simple Single Test Newsletter";

	public static $published                = '#jform_published';
	public static $published_list_id        = "jform_published_chzn";
	public static $published_list           = ".//*[@id='jform_published_chzn']/a";
	public static $published_list_text      = ".//*[@id='jform_published_chzn']/a/span";
	public static $published_unpublished    = ".//*[@id='jform_published_chzn']/div/ul/li[text()='unpublished']";
	public static $published_published      = ".//*[@id='jform_published_chzn']/div/ul/li[text()='published']";

	public static $arc_del_array     = array(
		'field_title'          => "1. Simple Single Test Newsletter",
		'archive_tab'          => ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li/button[contains(text(),'Archived newsletters')]",
		'archive_identifier'   => ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
		'archive_title_col'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'archive_success_msg'  => 'The selected newsletter has been archived.',
		'archive_success2_msg' => 'The selected newsletters have been archived.',

		'delete_button'        => ".//*[@id='toolbar-delete']/button",
		'delete_identifier'    => ".//*[@id='filter_search_filter_chzn']/div/ul/li[2]",
		'delete_title_col'     => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'remove_confirm'       => 'Do you wish to remove the selected newsletter(s)?',
		'success_remove'       => 'The selected newsletter has been removed.',
		'success_remove2'      => 'The selected newsletters have been removed.',
		'success_restore'       => 'The selected newsletter has been restored.',
		'success_restore2'      => 'The selected newsletters have been restored.',
	);

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close' => ".//*[@id='toolbar-save']/button",
		'Save'         => ".//*[@id='toolbar-apply']/button",
		'Cancel'       => ".//*[@id='toolbar-cancel']/button",
		'Help'         => ".//*[@id='toolbar-help']/button",
		'Back'         => ".//*[@id='toolbar-back']/button",
	);

	public static $female   = ".//*[@id='jform_gender_chzn']/div/ul/li[2]";
	public static $male     = ".//*[@id='jform_gender_chzn']/div/ul/li[3]";

	public static $available_content_list   = ".//*[@id='jform_available_content']";
	public static $selected_content_list    = ".//*[@id='jform_selected_content']";
	public static $available_content        = ".//*[@id='jform_available_content']/option[%s]";
	public static $selected_content         = ".//*[@id='jform_selected_content']/option[%s]";
	public static $add_content              = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/div/div[2]/input[1]";
	public static $remove_content           = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/div/div[2]/input[2]";

	public static $field_sent_description  = 'Description for the test newsletter';
	public static $field_sent_publish_up   = '';
	public static $field_sent_publish_down = '';

	/**
	 * Test method to copy a newsletter
	 *
	 * @param   \AcceptanceTester $I
	 * @param   string            $username
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function CopyNewsletter(\AcceptanceTester $I, $username)
	{
		$I->wantTo("Copy a newsletter");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);
		self::fillFormSimple($I);

		$I->click(self::$toolbar['Save & Close']);
		self::checkSuccess($I, $username);
		$I->see('Newsletters', Generals::$pageTitle);

		$I->click(Generals::$first_list_entry);
		$I->clickAndWait(Generals::$toolbar['Duplicate'], 1);
		$I->waitForText(self::$duplicate_prefix . self::$field_subject . "'", 30);
		$I->see(self::$duplicate_prefix . self::$field_subject . "'");

		$archive_allowed    = true;
		$delete_allowed     = true;

		if ($username != Generals::$admin['author'])
		{
			$archive_allowed = AccessPage::${$username . '_item_permissions'}['Newsletters']['permissions']['Archive'];
			$delete_allowed  = AccessPage::${$username . '_item_permissions'}['Newsletters']['permissions']['Delete'];
		}

		if ($archive_allowed)
		{
			$I->HelperArcDelItems($I, NlManage::$arc_del_array, self::$arc_del_array, $delete_allowed);
			$I->see('Newsletters', Generals::$pageTitle);
		}
	}

	/**
	 * Method to check success for filling form
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $username
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function checkSuccess(\AcceptanceTester $I, $username)
	{
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(self::$success_saved, Generals::$alert_msg);

		$I->see(self::$field_subject, self::$success_inList_subject);
		$I->see(self::$field_description, self::$success_inList_desc);
		$I->see($username, self::$success_inList_author);
	}

	/**
	 * Method to fill form without campaign ( that is: selecting other recipients) without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
     * @param boolean           $toUsergroup
	 *
	 * @return string   $content_title  title of content
	 *
	 * @since   2.0.0
	 */
	public static function fillFormSimple(\AcceptanceTester $I, $toUsergroup = false)
	{
		$I->fillField(self::$from_name, self::$field_from_name);
		$I->fillField(self::$from_email, self::$field_from_email);
		$I->fillField(self::$reply_email, self::$field_reply_email);
		$I->fillField(self::$subject, self::$field_subject);
		$I->fillField(self::$description, self::$field_description);

		//select attachment
		self::selectAttachment($I);

		// fill publish and unpublish
		self::fillPublishedDate($I);

		$I->scrollTo(self::$legend_templates);
		$I->click(self::$template_html);
		$I->click(self::$template_text);

		self::selectRecipients($I, $toUsergroup);

		// add content
		$I->scrollTo(self::$legend_content);
		$I->doubleClick(sprintf(self::$available_content, 2));
		$I->wait(2);
		$content_title = $I->grabTextFrom(sprintf(self::$selected_content, 1));
		$I->see($content_title, self::$selected_content_list);

		return $content_title;
	}

	/**
	 * Method to select attachment for newsletter
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	public static function selectAttachment(\AcceptanceTester $I)
	{
		$I->clickAndWait(self::$attachment_select_button, 1);
		$I->switchToIFrame(Generals::$media_frame);
		$I->switchToIFrame(Generals::$image_frame);
		$I->clickAndWait(self::$attachment_select, 1);
		$I->switchToIFrame();
		$I->switchToIFrame(Generals::$media_frame);
		$I->clickAndWait(self::$attachment_insert, 1);
		$I->switchToIFrame();
	}

	/**
	 * Method to select recipients for newsletter
	 *
	 * @param \AcceptanceTester $I
     * @param boolean           $toUsergroup
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	public static function selectRecipients(\AcceptanceTester $I, $toUsergroup = false)
	{
		$I->scrollTo(self::$legend_recipients);
		if (!$toUsergroup)
        {
            $I->click(sprintf(Generals::$mls_accessible, 2));
        }
        else
        {
            $I->click(Generals::$mls_usergroup);
        }
//		$I->click(sprintf(Generals::$mls_nonaccessible, 3));
//		$I->click(sprintf(Generals::$mls_internal, 4));
	}

	/**
	 * Method to fill published and unpublished fields
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	public static function fillPublishedDate(\AcceptanceTester $I)
	{
		$now_up     = new \DateTime('+10 minutes', new \DateTimeZone('Europe/Berlin'));
		$now_down   = new \DateTime('+11 minutes', new \DateTimeZone('Europe/Berlin'));

		$I->fillField(self::$publish_up, $now_up->format('Y-m-j H:i'));
		$I->fillField(self::$publish_down, $now_down->format('Y-m-j H:i'));
	}

	/**
	 * Test method to create single Newsletter without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   string              $username
     * @param   boolean             $toUsergroup
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function _CreateNewsletterWithoutCleanup(\AcceptanceTester $I, $username, $toUsergroup = false)
	{
		$I->wantTo("Create Newsletter without cleanup");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		self::fillFormSimple($I, $toUsergroup);

		$I->click(self::$toolbar['Save & Close']);
		self::checkSuccess($I, $username);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to create copy newsletter and send to real recipients
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   boolean             $sentToUnconfirmed
     * @param   boolean             $toUsergroup
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function SendNewsletterToRealRecipients(\AcceptanceTester $I, $sentToUnconfirmed = false, $toUsergroup = false)
	{
		$I->click(self::$mark_to_send);
		$I->click(Generals::$toolbar['Send']);
		$I->see(self::$tab5_legend1);

		$I->click(self::$tab2);
        $I->click(self::$tab3);
        $I->click(self::$toolbar['Save']);

        $I->click(self::$tab5);

        $nbrToSend  = self::$nbr_only_confirmed;

        if ($sentToUnconfirmed)
        {
            $I->click(self::$checkbox_unconfirmed);
            $nbrToSend += self::$nbr_unconfirmed;
        }

        if ($toUsergroup)
        {
            $nbrToSend = self::$nbr_usergroup;
        }

		$I->clickAndWait(self::$button_send, 1);

		$I->seeInPopup(self::$popup_send_confirm);
		$I->acceptPopup();

		$I->wait(2);
		$I->switchToIFrame(self::$tab5_send_iframe);
		$I->waitForText(self::$success_send_ready, 300);
		$I->see(self::$success_send_ready);

		$I->see(sprintf(self::$success_send_number, $nbrToSend));

		$I->switchToIFrame();
		$I->wait(8);

		$I->see("Newsletters", Generals::$pageTitle);
		$I->clickAndWait(NlManage::$tab2, 1);
	}
}
