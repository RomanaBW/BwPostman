<?php
namespace Page;

use Mockery\Exception;
use Page\NewsletterManagerPage as NlManage;
use Page\Generals as Generals;

/**
 * Class NewsletterEditPage
 *
 * @package Page
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
class NewsletterEditPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $url = 'administrator/index.php?option=com_bwpostman&view=newsletter&layout=edit_basic';

	/**
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab1             = ".//*[@id='adminForm']/div[1]/ul/li[1]/button";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab2             = ".//*[@id='adminForm']/div[1]/ul/li[2]/button";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab3             = ".//*[@id='adminForm']/div[1]/ul/li[3]/button";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4             = ".//*[@id='adminForm']/div[1]/ul/li[4]/button";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab5             = ".//*[@id='adminForm']/div[1]/ul/li[5]/button";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab1_legend1      = "General information";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab1_legend2      = "Newslettertemplates";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab1_legend3      = "Recipients";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab1_legend4      = "Website content";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab2_legend      = "HTML Version";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab2_editor      = ".//*[@id='tinymce']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab2_iframe      = "jform_html_version_ifr";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab3_legend      = "Text Version";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab3_editor      = ".//*[@id='jform_text_version']";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_legend1                 = "Email header";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_preview_html            = ".//*[@id='adminForm']/div[3]/fieldset[2]/div";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_preview_html_iframe     = "myIframeHtml";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_preview_html_divider    = "/html/body/table[1]/tbody/tr[5]/td";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_preview_text            = ".//*[@id='adminForm']/div[3]/fieldset[3]/div";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab4_preview_text_iframe     = "myIframeText";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $preview_html      = ".//*/table[2]/tbody/tr[1]/td/h1";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $preview_text      = ".//*[@id='preview_text']/textarea";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab5_legend1      = "Send newsletters";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab5_legend2      = "Send test newsletters";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab5_send_iframe  = "sendFrame";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab5_send_iframeId  = "#sendFrame";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */

	public static $checkbox_unconfirmed = ".//*[@id='send_to_unconfirmed']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $button_send          = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[1]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $button_send_publish  = ".//*[@id='adminForm']/div[3]/fieldset[1]/div/table/tbody/tr[5]/td[2]/input[2]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $button_send_test     = ".//*[@id='adminForm']/div[3]/fieldset[2]/div/table/tbody/tr[2]/td[2]/input";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_send         = 'The newsletters are sent';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_send_ready   = 'All newsletters in the queue';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_send_number  = '%s of %s newsletters need to be sent.';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_send_number_id  = "//*[@id='nl_to_send_message']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $delay_message_id  = "//*[@id='nl_delay_message']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $delay_message_text  = 'The entered time delay of %s %s counts down…';

	/**
	 * @var integer
	 *
	 * @since   2.0.0
	 */
	public static $nbr_only_confirmed   = 128;

	/**
	 * @var integer
	 *
	 * @since   2.0.0
	 */
	public static $nbr_unconfirmed      = 83;

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $nbr_usergroup        = 4;


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $mark_to_send         = ".//*[@id='cb0']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $duplicate_prefix     = "Copy of '";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $from_name            = ".//*[@id='jform_from_name']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $from_email           = ".//*[@id='jform_from_email']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $reply_email          = ".//*[@id='jform_reply_email']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $subject              = ".//*[@id='jform_subject']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $campaign             = ".//*[@id='jform_campaign_id']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $campaign_selected    = "01 Kampagne 5 A";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $description          = ".//*[@id='jform_description']";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment                   = ".//*[@id='jform_attachment__attachment0__single_attachment']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_id                = "#jform_attachment";


	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $attachments_add_button     = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/div/div[1]/ul/li[5]/div/div/div/div/div/div/a";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $attachment_new_button      = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/div/div[1]/ul/li[5]/div/div/div/div/div[2]/div[1]/div/a[1]";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $attachment_select_button1     = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/div/div[1]/ul/li[5]/div/div/div/div/div[2]/div[2]/div[2]/div/div[2]/button[1]";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $attachment_select_button2     = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/div/div[1]/ul/li[5]/div/div/div/div/div[3]/div[2]/div[2]/div/div[2]/button[1]";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $attachment_upload_path     = "../www_path/images/";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $attachment_upload_file_raw     = "boldt-webservice.png";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $attachment_upload_success     = "Upload Complete: /";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_select1            = "html/body/ul/li/a[contains(@href,'joomla_black.png')]";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $attachment_select2            = "html/body/ul/li/a[contains(@href,'powered_by.png')]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_upload_file       = "html/body/ul/li/a[contains(@href,'boldt-webservice.png')]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_insert            = ".//*[@id='imageForm']/div[2]/div/div[2]/div/button[1]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_cancel            = ".//*[@id='imageForm']/div[2]/div/div[2]/div/button[2]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_media_url_field   = ".//*[@id='f_url']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $attachment_url               = "images/joomla_black.png";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $publish_up           = ".//*[@id='jform_publish_up']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $publish_up_button    = ".//*[@id='jform_publish_up_img']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $publish_down         = ".//*[@id='jform_publish_down']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $publish_down_button  = ".//*[@id='jform_publish_down_img']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $today_up             = ".//*[@class='calendar']/table/thead/tr[2]/td[3]/div[contains(text(), 'Today')]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $today_down           = "html/body/div[11]/table/thead/tr[2]/td[3]/div";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $template_html    = "//*/span[starts-with(normalize-space(text()),'Standard Basic')]/parent::span/parent::label/input[1]";// Template Standard Basic [3]

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $template_text		= "//*/span[starts-with(normalize-space(text()),'Standard TEXT Template 3')]/parent::span/parent::label/input[1]";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $msg_required_sender_name     = "Field required: Sender's name";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $msg_required_sender_email    = "Field required: Sender's email";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $msg_required_replyto_email   = "Field required: 'Reply to' email";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $msg_required_subject         = "Field required: Subject";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $msg_no_recipients            = "No recipients selected!";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_from_name    = "Sam";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_from_email   = "sam.sample@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_reply_email  = "sample@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_subject      = "1. Simple Single Test Newsletter";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_subject2      = "1. Simple Single Test Newsletter 2";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_description  = 'Description for the test newsletter';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_attachment   = 'images/joomla_black.png';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_campaign     = '';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_publish_up   = '';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_publish_down = '';


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_edit_publish_up    = '2017-03-14 17:00:00';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_edit_publish_down  = '2017-03-25 17:00:00';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_edit_description   = 'Changed description for the test newsletter';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $legend_general       = ".//*[@id='adminForm']/div[3]/div[1]/fieldset/legend";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $legend_templates     = ".//*[@id='adminForm']/div[3]/fieldset[1]/legend";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $legend_recipients    = ".//*[@id='recipients']/div/div[1]/div/fieldset/legend";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $usergroup_recipients = ".//*[@id='recipients']/div/div[2]/div/fieldset/div[24]/div/label";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $legend_content       = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/legend";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $required_from_name   = "Field required: Sender's name";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $required_from_email  = "Field required: Sender's email";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $required_reply_email = "Field required: 'Reply to' email";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $required_subject     = 'Field required: Subject';


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $popup_recipients     = 'No recipients selected!';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_saved        = 'Newsletter saved successfully!';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $message_template     = 'This is a Content Template. If You want to send this newsletter, please work with a copy of this newsletter content template or unset Content Template.';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $warn_save            = "A newsletter with subject '%s' already exists. Do You have an appropriate description?";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $error_save           = 'Save failed with the following error:';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $popup_send_confirm   = 'Do you wish to send the newsletter?';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $popup_send_publish_confirm   = 'Do you wish to send and publish the newsletter on front end?';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_inList_subject   = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_inList_desc      = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[4]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $success_inList_author    = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[6]";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_title          = "1. Simple Single Test Newsletter";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published                = '#jform_published';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published_list_id        = "jform_published_chzn";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published_list           = ".//*[@id='jform_published_chzn']/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published_list_text      = ".//*[@id='jform_published_chzn']/a/span";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published_unpublished    = ".//*[@id='jform_published_chzn']/div/ul/li[text()='unpublished']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $published_published      = ".//*[@id='jform_published_chzn']/div/ul/li[text()='published']";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $is_template                = '#jform_is_template';

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $is_template_list_id         = '#jform_is_template';

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $is_template_error    = "This is a Content Template. A Content Template cannot be sent. If You want to send this newsletter, please work with a copy of this newsletter content template or unset Content Template.";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $change_is_template  = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[8]/a";

	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $arc_del_array     = array(
		'field_title'          => "1. Simple Single Test Newsletter",
		'archive_tab'          => ".//.//*[@id='main-table']/tbody/tr/td/ul/li/button[contains(text(),'Archived newsletters')]",
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


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $female   = ".//*[@id='jform_gender_chzn']/div/ul/li[2]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $male     = ".//*[@id='jform_gender_chzn']/div/ul/li[3]";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $available_content_list   = ".//*[@id='jform_available_content']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $selected_content_list    = ".//*[@id='jform_selected_content']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $available_content        = ".//*[@id='jform_available_content']/option[%s]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $selected_content         = ".//*[@id='jform_selected_content']/option[%s]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $add_content              = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/div/div[2]/input[1]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $remove_content           = ".//*[@id='adminForm']/div[3]/fieldset[2]/div[2]/div/fieldset/div/div[2]/input[2]";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_sent_description  = 'Description for the test newsletter';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_sent_publish_up   = '';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $field_sent_publish_down = '';

	/**
	 * Test method to copy a newsletter
	 *
	 * @param   \AcceptanceTester $I
	 * @param   string          $username
	 * @param 	boolean			$withCleanup
	 * @param   boolean         $isTemplate
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function CopyNewsletter(\AcceptanceTester $I, $username, $withCleanup = true, $isTemplate = false)
	{
		$I->wantTo("Copy a newsletter");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);
		self::fillFormSimple($I);
		if ($isTemplate)
		{
			$I->selectOption(self::$is_template, 'Yes');
		}

		$I->click(self::$toolbar['Save & Close']);
		self::checkSuccess($I, $username);
		$I->see('Newsletters', Generals::$pageTitle);
		if ($isTemplate)
		{
			$I->seeElement(".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-featured')]");
		}

		$I->click(Generals::$first_list_entry);
		$I->clickAndWait(Generals::$toolbar['Duplicate'], 1);
		$I->waitForText(self::$duplicate_prefix . self::$field_subject . "'", 30);
		$I->see(self::$duplicate_prefix . self::$field_subject . "'");
		if ($isTemplate)
		{
			$I->seeElement(".//*[@id='j-main-container']/div[4]/table/tbody/tr[2]/td[8]/a/span[contains(@class, 'icon-unfeatured')]");
		}

		if ($withCleanup)
		{
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
	}

	/**
	 * Method to check success for filling form
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $username
	 *
	 * @return  void
	 *
	 * @throws \Exception
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
	 * @param boolean           $withAttachment
	 *
	 * @return string   $content_title  title of content
	 *
	 * @since   2.0.0
	 *
	 * @throws \Exception
	 */
	public static function fillFormSimple(\AcceptanceTester $I, $toUsergroup = false, $withAttachment = false)
	{
		$I->fillField(self::$from_name, self::$field_from_name);
		$I->fillField(self::$from_email, self::$field_from_email);
		$I->fillField(self::$reply_email, self::$field_reply_email);
		$I->fillField(self::$subject, self::$field_subject);
		$I->fillField(self::$description, self::$field_description);

		//select attachment if desired
		if ($withAttachment)
		{
			self::selectAttachment($I);
		}

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
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function selectAttachment(\AcceptanceTester $I)
	{
		$I->clickAndWait(self::$attachments_add_button, 1);

		//Select first attachment
		$I->clickAndWait(self::$attachment_select_button1, 1);

		$I->switchToIFrame(Generals::$media_frame);
		$I->waitForElementVisible("iframe#imageframe", 5);

		$I->switchToIFrame(Generals::$image_frame);
		$I->waitForElementVisible("ul.manager", 5);
		$I->scrollTo(self::$attachment_select1, 0, -100);
		$I->clickAndWait(self::$attachment_select1, 1);

		$I->switchToIFrame();
		$I->switchToIFrame(Generals::$media_frame);
		$I->clickAndWait(self::$attachment_insert, 1);
		$I->switchToIFrame();

		//Select second attachment
		$I->clickAndWait(self::$attachment_new_button, 1);
		$I->clickAndWait(self::$attachment_select_button2, 1);

		$I->switchToIFrame(Generals::$media_frame);
		$I->waitForElementVisible("iframe#imageframe", 5);

		$I->switchToIFrame(Generals::$image_frame);
		$I->waitForElementVisible("ul.manager", 5);
		$I->scrollTo(self::$attachment_select2, 0, -100);
		$I->clickAndWait(self::$attachment_select2, 1);

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
		if (!$toUsergroup)
		{
			$I->scrollTo(self::$legend_recipients);
			$I->click(sprintf(Generals::$mls_accessible, 2));
		}
		else
		{
			$I->scrollTo(self::$usergroup_recipients, 0, -100);
			$I->click(Generals::$mls_usergroup);
		}
		$I->wait(2);
	}

	/**
	 * Method to fill published and unpublished fields
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @return void
	 *
	 * @throws \Exception
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
	 * @param   boolean             $withAttachment
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function CreateNewsletterWithoutCleanup(\AcceptanceTester $I, $username, $toUsergroup = false, $withAttachment = false)
	{
		$I->wantTo("Create Newsletter without cleanup");
		$I->amOnPage(NlManage::$url);

		$I->click(Generals::$toolbar['New']);

		self::fillFormSimple($I, $toUsergroup, $withAttachment);

		$I->click(self::$toolbar['Save & Close']);
		self::checkSuccess($I, $username);
		$I->see('Newsletters', Generals::$pageTitle);
	}

	/**
	 * Test method to send newsletter to real recipients
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   boolean             $sentToUnconfirmed
	 * @param   boolean             $toUsergroup
	 * @param   boolean             $buildQueue
	 * @param   integer             $iframeTime         time to wait for the last iframe sendFrame appearance (chromium 66+ specific)
	 * @param   boolean             $publish            button send and publish?
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
	public static function SendNewsletterToRealRecipients(\AcceptanceTester $I, $sentToUnconfirmed = false, $toUsergroup = false, $buildQueue = false, $iframeTime = 20, $publish = false)
	{
		codecept_debug("unconfirmed: $sentToUnconfirmed");
		codecept_debug("usergroup: $toUsergroup");
		codecept_debug("Queue: $buildQueue");
		codecept_debug("iFrame time: $iframeTime");
		codecept_debug("Publish: $publish");

		$I->click(self::$mark_to_send);
		$I->click(Generals::$toolbar['Send']);
		$I->see(self::$tab5_legend1);

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

		$remainsToSend  = '0';
		if ($buildQueue)
		{
			$remainsToSend = $nbrToSend;
		}

		if (!$publish)
		{
			$I->clickAndWait(self::$button_send, 1);
			$I->seeInPopup(self::$popup_send_confirm);
		}
		else
		{
			$I->clickAndWait(self::$button_send_publish, 1);
			$I->seeInPopup(self::$popup_send_publish_confirm);
		}

		$I->acceptPopup();

//		$user = getenv('USER');
//
//		if (!$user)
//		{
//			$user = 'root';
//		}
//
//		if ($user == 'jenkins')
//		{
//			$I->wait($iframeTime);
//		}
//
		$I->waitForElementVisible(NlManage::$sendLayout, 5);
		$I->waitForElementVisible(self::$success_send_number_id, 10);
		$I->waitForText(self::$success_send_ready, 180);
		$I->see(self::$success_send_ready);
		$I->see(sprintf(self::$success_send_number, $remainsToSend, $nbrToSend));

		$I->click(NlManage::$sendLayoutBack);
		$I->waitForElementVisible(Generals::$pageTitle, 10);
		$I->see("Newsletters", Generals::$pageTitle);

		$I->clickAndWait(NlManage::$tab2, 1);
	}

	/**
	 * Test method to send newsletter to real recipients with modified step value
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   integer             $iframeTime         time to wait for the last iframe sendFrame appearance (chromium 66+ specific)
	 * @param   integer             $stepSize           number of newsletters to send per step
	 * @param   integer             $timeDelay          number of time units to wait
	 * @param   string              $timeDelayUnit      time unit (seconds|minutes)
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
	public static function SendNewsletterOptionsCheck(\AcceptanceTester $I, $iframeTime = 20, $stepSize = 100, $timeDelay = 1, $timeDelayUnit = 'second')
	{
		$I->click(self::$mark_to_send);
		$I->click(Generals::$toolbar['Send']);
		$I->see(self::$tab5_legend1);

		$nbrToSend  = self::$nbr_only_confirmed;

		$remainsToSend  = $nbrToSend - $stepSize;

		$I->clickAndWait(self::$button_send, 1);
		$I->seeInPopup(self::$popup_send_confirm);

		$I->acceptPopup();

		$user = getenv('USER');

		if (!$user)
		{
			$user = 'root';
		}

		if ($user == 'jenkins')
		{
			$I->wait($iframeTime);
		}

		$I->waitForElement(self::$tab5_send_iframeId, 20);
		$I->switchToIFrame(self::$tab5_send_iframe);

		$I->waitForElementVisible(self::$success_send_number_id, 60);
		$I->waitForText(sprintf(self::$success_send_number, $remainsToSend, $nbrToSend), 60);

		$I->see(sprintf(self::$success_send_number, $remainsToSend, $nbrToSend));

		$I->waitForElementVisible(self::$delay_message_id, 60);
		$I->see(sprintf(self::$delay_message_text, $timeDelay, $timeDelayUnit), self::$delay_message_id);

		while ($remainsToSend > $stepSize)
		{
			$remainsToSend  -= $stepSize;

			$I->waitForElementVisible(self::$success_send_number_id, 180);
			$I->waitForText(sprintf(self::$success_send_number, $remainsToSend, $nbrToSend), 180);

			$I->see(sprintf(self::$success_send_number, $remainsToSend, $nbrToSend));
		}

		$I->waitForElementVisible(self::$success_send_number_id, 180);
		$I->waitForText(self::$success_send_ready, 180);

		$I->see(sprintf(self::$success_send_number, 0, $nbrToSend));

		$I->switchToIFrame();
		$I->wait(8);

		$I->see("Newsletters", Generals::$pageTitle);
		$I->clickAndWait(NlManage::$tab2, 1);
	}
}
