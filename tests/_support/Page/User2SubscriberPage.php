<?php
namespace Page;

/**
 * Class RegisterSubscribePage
 *
 * @package Register Subscribe Plugin
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
class User2SubscriberPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	// Frontend stuff
	// used urls and links

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $register_url         = '/index.php?option=com_users&view=registration';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $user_activation_url  = '/index.php?option=com_users&task=registration.activate&token=';

	//view identifier

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_register        = "//*[@id='member-registration']/fieldset[1]/legend";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_register_subs   = "//*[@id='member-registration']/fieldset[2]/legend";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_red   = 'btn active btn-success btn-danger'; // needed for chromium

	// login field identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_name            = "//*[@id='jform_name']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_username        = "//*[@id='jform_username']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_password1       = "//*[@id='jform_password1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_password2       = "//*[@id='jform_password2']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_email1          = "//*[@id='jform_email1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_email2          = "//*[@id='jform_email2']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_identifier_register        = "//*[@id='member-registration']/div/div/button";


	// login field values user 1

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value_name         = "Sam Sample";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value_username     = "Sam";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value_password     = "!08Sam15####";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value_email        = "dummy-1@tester-net.nil";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $change_value_email       = "dummy-2@tester-net.nil";

	// login field values user 2

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value2_name         = "Chiara Abbott";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value2_username     = "Chiara";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value2_password     = "!08Sam15####";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_value2_email        = "c.abbott@tester-net.nil";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_field     = "";

	// subscriber field identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_subscribe_no     = "//*[@id='jform_bwpm_user2subscriber_bwpm_user2subscriber0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_subscribe_yes    = "//*[@id='jform_bwpm_user2subscriber_bwpm_user2subscriber1']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $gender_list           = "//*[@id='jform_bwpm_user2subscriber_gender_chosen']";

	/** @var string
	 *
	 * @since 4.0.0
	 */
	public static $gender_list_classical    = "//*[@id='jform_bwpm_user2subscriber_gender']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $gender_list_id           = "//*[@id='jform_bwpm_user2subscriber_gender_chosen']/a";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $subs_identifier_no_gender           = '//*[@id="jform_bwpm_user2subscriber_gender_chosen"]/div/ul/li[1]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $subs_identifier_female           = "//*[@id='jform_bwpm_user2subscriber_gender_chosen']/div/ul/li[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $subs_identifier_female_classical  = "//*[@id='jform_bwpm_user2subscriber_gender']/option[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $subs_option_female           = "female";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $subs_identifier_male             = '//*[@id="jform_bwpm_user2subscriber_gender_chosen"]/div/ul/li[3]';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_name             = "//*[@id='jform_bwpm_user2subscriber_bwpm_name']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_name            = "Last Name";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_name_identifier            = "//*[@id='jform_bwpm_user2subscriber_bwpm_name-lbl'][contains(@class, 'invalid')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_name_missing            = "//*[@id='jform_bwpm_user2subscriber_bwpm_name-lbl'][contains(@class, 'invalid')]/span[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_firstname        = "//*[@id='jform_bwpm_user2subscriber_firstname']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_firstname            = "First Name";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_firstname_identifier            = "//*[@id='jform_bwpm_user2subscriber_firstname-lbl'][contains(@class, 'invalid')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $login_label_firstname_missing            = "//*[@id='jform_bwpm_user2subscriber_firstname-lbl'][contains(@class, 'invalid')]/span[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $login_label_special_identifier            = "//*[@id='jform_bwpm_user2subscriber_special-lbl'][contains(@class, 'invalid')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $login_label_special_missing            = "//*[@id='jform_bwpm_user2subscriber_special-lbl'][contains(@class, 'invalid')]/span[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_special          = "//*[@id='jform_bwpm_user2subscriber_special']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_format_text      = "//*[@id='jform_bwpm_user2subscriber_emailformat0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
//	public static $subs_identifier_format_html      = "//*[@id='jform_bwpm_user2subscriber_emailformat1']";
	public static $subs_identifier_format_html      = "//*/label[@for='jform_bwpm_user2subscriber_emailformat%s']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $login_label_mailinglists            = "Mailing Lists";

	/* @var string
	 *
	 * @since 4.0.0
	 */
	public static $login_label_mailinglists_identifier            = "//*[@id='jform_bwpm_user2subscriber_mailinglists-lbl'][contains(@class, 'invalid')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_identifier_mailinglists    = "//*[@id='jform_bwpm_user2subscriber_mailinglists']/div/table/tbody/tr/td/input[@id='mb0']";


	// subscriber field values

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_null_value         = "NO_NAME_AVAILABLE";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_name         = "Sample";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_firstname    = "Sam";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_special      = "0815";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_name2        = "Sedlmeier";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_firstname2   = "Andre";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value_special2     = "0816";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value2_name        = "Abbott";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_value2_firstname   = "Chiara";

	// success message identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_heading_identifier   = "//*[@id='system-message-container']/joomla-alert/div[@class='alert-heading']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_message_identifier   = "//*[@id='system-message-container']/joomla-alert[@type='success']/div/div[@class='alert-message']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_completed_text
		= "Your Account has been successfully activated. You can now log in using the username and password you chose during the registration.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_complete          = "//*[@id='system-message-container']/div/div/div";

	// error message identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_message_missing               = "Please fill in this field";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_message_name               = "Invalid field:  Last Name";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_message_firstname          = "Invalid field:  First Name";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_message_special            = "Invalid field:  %s";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_message_mailinglists      = "One of the options must be selected";

	// backend stuff

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $user_management_url              = 'administrator/index.php?option=com_users&view=users';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $user_edit_identifier             = "//*[@id='userList']/tbody/*/th/div[1]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $toolbar_apply_button             = "//*[@id='toolbar-apply']/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $toolbar_save_button              = "//*[@id='toolbar-save']/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $toolbar_delete_button            = "//*[@id='toolbar-delete']/button";

	// com_bwpostman related

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_email_col_identifier  = "//*/table[@id='%s']/tbody/*/td[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_format_col_identifier = "//*/table[@id='%s']/tbody/*/td[6]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_email_col_ident_no_gender  = "//*/table[@id='%s']/tbody/*/td[4]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_format_col_ident_no_gender = "//*/table[@id='%s']/tbody/*/td[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_filter_col_identifier = "//*/table[@id='%s']/tbody/*/td[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_edit_link             = "//*/table[@id='%s']/tbody/*/td[2]/a";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subslist_identifier_name         = "//*/table[@id='%s']/tbody/*/td[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subslist_identifier_firstname    = "//*/table[@id='%s']/tbody/*/td[3]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subslist_identifier_gender       = "//*/table[@id='%s']/tbody/*/td[4]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subslist_identifier_special      = "//*[@id='jform_special']";

	// mailinglist check

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailinglist1_checked             = "//*[@id='jform_ml_available_1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailinglist2_checked             = "//*[@id='jform_ml_available_4']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailinglist_fieldset_identifier  = "//*[@id='subs_mailinglists']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscriber_details_close         = "//*[@id='toolbar-cancel']/button";

	// check for selected options

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $user_id_identifier               = "//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr/td[7]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $email_identifier                 = "//*[@id='userList']/tbody/tr[1]/td[6]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $email_identifier_mfa             = "//*[@id='userList']/tbody/tr[1]/td[7]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mail_field_identifier            = "//*[@id='jform_email']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailformat_identifier            = "//*/label[@for='jform_default_emailformat%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailformat_button_identifier    = "//*/input[@id='jform_default_emailformat%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_show_label_identifier           = "//*/label[@for='jform_show_emailformat%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_show_button_identifier    = "//*/input[@id='jform_show_emailformat%s']";

	// com_plugin related

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_name                      = "BwPostman Plugin User2Subscriber";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $icon_published_identifier        = "//*[@id='pluginList']/tbody/tr/td[3]/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_edit_identifier           = "//*[@id='pluginList']/tbody/tr/th/a";

	// plugin edit tab options
	// @ToDo: make more flexible

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_tab_options               = "//*/button[@aria-controls='attrib-option'][@role='tab']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_message_identifier        = "//*[@id='jform_params_register_message_option']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_show_format_yes           = "//*[@id='jform_params_show_format_selection_option']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_show_format_no            = "//*[@id='jform_params_show_format_selection_option']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_format_html               = "//*/label[@for='jform_params_predefined_mailformat_option1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_format_text               = "//*/label[@for[@id='jform_params_predefined_mailformat_option0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_auto_update_yes           = "//*/label[@for='jform_params_auto_update_email_option1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_auto_update_no            = "//*/label[@for='jform_params_auto_update_email_option0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_auto_delete_yes           = "//*/label[@for='jform_params_register_subscribe_option1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_auto_delete_no            = "//*/label[@for='jform_params_register_subscribe_option0']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_message_old               = 'Test text for newsletter message text';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_message_new               = 'New newsletter message text';

	// plugin edit tab mailinglists
	// @ToDo: make more flexible

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_tab_mailinglists          = "//*/button[@aria-controls='attrib-mailinglists'][@role='tab']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_checkbox_mailinglist      = "//*[@id='mb%s']/parent::td/parent::tr";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $plugin_checkbox_mailinglist_input      = "//*[@id='mb%s']";

	//messages

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $delete_confirm           = "Are you sure you want to delete? Confirming will permanently delete the selected item(s)!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $delete_success           = "User deleted.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $register_success         = "Your account has been created and a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $config_save_success      = "Configuration saved.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $username_used            = 'The username you entered is not available. Please pick another username.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailaddress_used         = 'The email address you entered is already in use or invalid. Please enter another email address.';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_subs_table      = "bwpostman_subscribers";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_subs_mls_table  = "bwpostman_subscribers_mailinglists";

	/**
	 * @var string
	 *
	 * @since 2..0
	 */
	public static $bwpm_com_options_regTab  = "//*[@id='configTabs']/div/button[2]";


	/**
	 * @param \AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	public static function selectPluginPage(\AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$plugin_page);
		$I->wait(1);
		$I->see(Generals::$view_plugin, Generals::$pageTitle);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $plugin_name
	 *
	 * @since 2.0.0
	 */
	public static function filterForPlugin(\AcceptanceTester $I, $plugin_name)
	{
		$I->fillField(Generals::$search_field, $plugin_name);
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param array $mls_to_subscribe
	 *
	 * @since 2.0.0
	 */
	public static function checkSelectedMailinglists(\AcceptanceTester $I, $mls_to_subscribe)
	{
		$table_subs = Generals::$db_prefix . self::$bwpm_subs_table;
		$table_mls  = Generals::$db_prefix . self::$bwpm_subs_mls_table;
		$subs_id   = $I->grabFromDatabase($table_subs, 'id', array('email' => self::$login_value_email));
		foreach ($mls_to_subscribe as $mls)
		{
			$I->seeInDatabase($table_mls, array('subscriber_id' => $subs_id, 'mailinglist_id' => $mls));
		}
	}
}
