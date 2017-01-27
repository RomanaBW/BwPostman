<?php
namespace Page;

/**
 * Class RegisterviewPage
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
class SubscriberviewPage
{
    // used urls and links
    public static $register_url         = '/index.php?option=com_bwpostman&view=register';
	public static $edit_url             = '/index.php?option=com_bwpostman&view=edit';
	public static $register_edit_url    = ".//*[@id='bwp_com_form']/div[1]/p[1]/a";

	public static $unsubscribe_link_faulty  = "/index.php?option=com_bwpostman&view=edit&editlink=8d87c32337a283";
	public static $unsubscribe_link_empty   = "/index.php?option=com_bwpostman&view=edit&editlink=";
	public static $unsubscribe_link_missing = "/index.php?option=com_bwpostman&view=edit";

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     */

	//view identifier
	public static $view_register        = ".//*[@id='bwp_com_register']";
	public static $view_edit            = ".//*[@id='bwp_com_edit_subscription']";
	public static $view_edit_link       = ".//*[@id='bwp_com_form']/div/p[1]";
	public static $view_module          = ".//*[@id='mod_bwpostman']";

	// field identifier component
	public static $gender       = ".//*[@id='jform_gender_chzn']/a";
	public static $gender_female = ".//*[@id='edit_gender']/label[2]";
	public static $gender_male   = ".//*[@id='edit_gender']/label[1]";
	public static $female        = '1';
	public static $male          = '0';
	public static $firstname     = ".//*[@id='firstname']";
	public static $name          = ".//*[@id='name']";
	public static $special       = ".//*[@id='special']";
	public static $mail          = ".//*[@id='email']";
	public static $format_text   = ".//*[@id='edit_mailformat']/label[1]";
	public static $format_html   = ".//*[@id='edit_mailformat']/label[2]";
	public static $ml0           = ".//*[@id='mailinglists0']";
	public static $ml1           = ".//*[@id='mailinglists1']";
	public static $ml2           = ".//*[@id='mailinglists2']";
	public static $math_captcha  = ".//*[@id='stringCaptcha']";
	public static $disclaimer    = ".//*[@id='agreecheck']";

	// field identifier module
	public static $mod_gender_female = ".//*[@id='genFemaleMod']";
	public static $mod_gender_male   = ".//*[@id='genMaleMod']";
	public static $mod_female        = '1';
	public static $mod_male          = '0';
	public static $mod_firstname     = '#a_firstname';
	public static $mod_name          = '#a_name';
	public static $mod_special       = '#a_special';
	public static $mod_mail          = '#a_email';
	public static $mod_format_text   = '#formatTextMod';
	public static $mod_format_html   = '#formatHtmlMod';
	public static $mod_ml0           = '#a_mailinglists0';
	public static $mod_ml1           = '#a_mailinglists1';
	public static $mod_ml2           = '#a_mailinglists2';
	public static $mod_math_captcha  = '#a_stringCaptcha';
	public static $mod_disclaimer    = '#agreecheck_mod';

	public static $edit_mail     = ".//*[@id='email']";

	//field fill values
	public static $firstname_fill   = "Sam";
	public static $lastname_fill    = "Sample";
	public static $special_fill     = "0815";
	public static $mail_fill_1      = "dummy-1@tester-net.nil";
	public static $mail_fill_2      = "dummy-2@tester-net.nil";

	// button identifier
	public static $button_register          = ".//*[@id='bwp_com_form']/p[2]/button";
	public static $button_send_activation   = ".//*[@id='bwp_com_form']/button";
	public static $send_edit_link           = ".//*[@id='bwp_com_form']/button";
	public static $button_unsubscribe       = ".//*[@id='unsubscribe']";
	public static $button_submit            = ".//*[@id='bwp_com_form']/button[1]";
	public static $button_submitleave       = ".//*[@id='bwp_com_form']/button[2]";
	public static $button_edit              = "//*[@id='bwp_com_register_success']/div/div/p[3]/a";

	public static $mandatory_msg           = ".//*[@id='bwp_mod_form_required']";

	public static $mod_button_register      = ".//*[@id='bwp_mod_form']/*/button[text()='Register']";
	public static $mod_button_edit          = ".//*[@id='bwp_mod_form_editlink']/button";

	// subscriber mail and base of activation link
	public static $activation_link  = "/index.php?option=com_bwpostman&view=register&task=activate&subscriber=";
	public static $editlink         = "/index.php?option=com_bwpostman&view=edit&editlink=";

	// success message identifier
	public static $success_message          = ".//*[@id='bwp_com_register_success']/div/div";
	public static $registration_complete    = ".//*[@id='bwp_com_register_success']/div/div/p[1]/strong";
	public static $activation_complete      = ".//*[@id='bwp_com_register_success']/div/div/p[1]/strong";
	public static $activated_edit_Link      = ".//*[@id='bwp_com_register_success']/div/div/p[3]/a";
	public static $edit_saved_successfully  = ".//*[@id='system-message']/div";
	public static $register_success         = ".//*[@id='bwp_com_register_success']";


	//messages
	public static $registration_completed_text  = "Registration completed!";
	public static $error_occurred_text          = "An error occurred!";
	public static $activation_sent_text         = "The activation code has been sent to the given email address";
	public static $activation_completed_text    = "Activation completed!";
	public static $edit_get_text                = "If you want to change your newsletter profile, please enter your email";
	public static $editlink_sent_text           = "The edit link has been sent to the given email address.";
	public static $msg_saved_successfully       = "Changes successfully saved";
	public static $msg_saved_changes            = "Data changed!";

	// error message identifier
	public static $err_activation_incomplete    = ".//*[@id='bwp_com_error_account_notactivated']/p[1]/strong";
	public static $err_already_subscribed       = ".//*[@id='bwp_com_error_account_general']/p[1]/strong";
	public static $err_not_activated            = ".//*[@id='bwp_com_error_account_notactivated']";
	public static $get_edit_Link                = ".//*[@id='bwp_com_error_account_general']/p[2]/a";
	public static $err_get_editlink             = ".//*[@id='bwp_com_error_geteditlink']";

	public static $msg_err_occurred            = "An error occurred!";
	public static $msg_err_invalid_link        = "You cannot activate your newsletter subscription because the entered activation link is invalid.";
	public static $msg_err_no_subscription     = "There is not yet a newsletter subscription for the entered email address";
	public static $msg_err_wrong_editlink      = "You cannot edit your newsletter subscription because the entered edit link is invalid.";


	// invalid fields
	public static $invalid_field_name               = 'Invalid field:  Your name:';
	public static $invalid_field_name_mod           = 'Please enter a name!';
	public static $invalid_field_firstname          = 'Invalid field:  Your first name:';
	public static $invalid_field_firstname_mod      = 'Please enter a first name!';
	public static $invalid_field_mailaddress        = 'Invalid field:  Your email address:';
	public static $invalid_field_special_mod        = 'Please enter a value into the %s field!';
	public static $popup_valid_mailaddress          = 'Please enter a valid mailaddress!';
	public static $popup_select_newsletter          = 'You have to select at least one newsletter for finishing up your registration.';
	public static $invalid_select_newsletter_mod    = 'You have to select one newsletter.';
	public static $popup_enter_special              = 'Please enter a value into the %s field!';
	public static $popup_accept_disclaimer          = 'You have to accept the Disclaimer for finishing up your registration.';

}
