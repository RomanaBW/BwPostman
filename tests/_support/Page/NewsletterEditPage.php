<?php
namespace Page;

/**
 * Class NewsletterEditPage
 *
 * @package Page
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

	public static $button_send          = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[1]";
	public static $button_send_publish  = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[2]";
	public static $button_send_test     = ".//*[@id='adminForm']/div[3]/fieldset[2]/div/table/tbody/tr[2]/td[2]/input";
	public static $success_send         = 'The newsletters are sent';

	public static $mark_to_send         = ".//*[@id='cb1']";
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
	public static $attachment_select            = "html/body/ul/li[5]";
	public static $attachment_insert            = ".//*[@id='imageForm']/div[2]/div/div[2]/button[1]";
	public static $attachment_media_url_field   = ".//*[@id='f_url']";
	public static $attachment_url               = "images/joomla_black.png";

	public static $publish_up           = ".//*[@id='jform_publish_up']";
	public static $publish_up_button    = ".//*[@id='jform_publish_up_img']";
	public static $publish_down         = ".//*[@id='jform_publish_down']";
	public static $publish_down_button  = ".//*[@id='jform_publish_down_img']";
	public static $today_up             = "html/body/div[6]/table/thead/tr[2]/td[3]/div";
	public static $today_down           = "html/body/div[7]/table/thead/tr[2]/td[3]/div";

	public static $template_html    = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/div[1]/div/fieldset/div/div/label[2]";
	public static $template_text    = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/div[2]/div/fieldset/div/div/label[4]";

	public static $msg_required_sender_name     = "Field required: Sender's name";
	public static $msg_required_sender_email    = "Field required: Sender's email";
	public static $msg_required_replyto_email   = "Field required: 'Reply to' email";
	public static $msg_required_subject         = "Field required: Subject";
	public static $msg_no_recipients            = "No recipients selected!";

	public static $field_from_name    = "Sam";
	public static $field_from_email   = "sam.sample@test.nil";
	public static $field_reply_email  = "sample@test.nil";
	public static $field_subject      = "1. Simple Single Test Newsletter";
	public static $field_description  = 'Description for the test newsletter';
	public static $field_attachment   = 'images/joomla_black.png';
	public static $field_campaign     = '';
	public static $field_publish_up   = '';
	public static $field_publish_down = '';

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

	public static $archive_button       = ".//*[@id='toolbar-archive']/button";
	public static $archive_tab          = ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li[1]/button";
	public static $archive_identifier   = ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]";
	public static $archive_title_col    = ".//*[@id='j-main-container']/div[4]/table/tbody/*/td[3]";
	public static $archive_success_msg  = 'The selected newsletter has been archived.';
	public static $archive_success2_msg = 'The selected newsletters have been archived.';

	public static $delete_button        = ".//*[@id='toolbar-delete']/button";
	public static $delete_identifier    = ".//*[@id='filter_search_filter_chzn']/div/ul/li[2]";
	public static $delete_title_col     = ".//*[@id='j-main-container']/div[4]/table/tbody/*/td[3]";
	public static $remove_confirm       = 'Do you wish to remove the selected newsletter(s)?';
	public static $success_remove       = 'The selected newsletter has been removed.';
	public static $success_remove2      = 'The selected newsletters have been removed.';

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
}
