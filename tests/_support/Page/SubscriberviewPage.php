<?php
namespace Page;

/**
 * Class RegisterviewPage
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
class SubscriberviewPage
{
	// used urls and links

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $register_url         = '/index.php?option=com_bwpostman&view=register';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $edit_url             = '/index.php?option=com_bwpostman&view=edit';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $register_edit_url    = "//*[@id='bwp_com_register']/div/p[contains(@class, 'user_edit')]/a";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $unsubscribe_link_faulty  = "/index.php?option=com_bwpostman&view=edit&editlink=8d87c32337a283";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $unsubscribe_link_empty   = "/index.php?option=com_bwpostman&view=edit&editlink=";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $unsubscribe_link_missing = "/index.php?option=com_bwpostman&view=edit";

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	//view identifier

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_register        = ".//*[@id='bwp_com_register']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_edit            = ".//*[@id='bwp_com_edit_subscription']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_edit_link       = ".//*[@id='bwp_com_form']/div/p[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_module          = ".//*[@id='mod_bwpostman']";

	// field identifier component

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender       = "//*[@id='gender']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender_female = "//*[@id='gender_chosen']/div/ul/li[3]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender_male   = "//*[@id='gender_chosen']/div/ul/li[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $female        = '1';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $male          = '0';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $firstname     = ".//*[@id='firstname']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $name          = ".//*[@id='name']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $special       = ".//*[@id='special']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mail          = ".//*[@id='email']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text   = ".//*[@id='edit_mailformat']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_html   = ".//*[@id='edit_mailformat']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml0           = ".//*[@id='mailinglists0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml1           = ".//*[@id='mailinglists1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml2           = ".//*[@id='mailinglists2']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml5           = ".//*[@id='mailinglists5']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $math_captcha  = ".//*[@id='stringCaptcha']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $math_captcha_mod  = ".//*[@id='a_stringCaptcha']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $disclaimer    = ".//*[@id='agreecheck']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $question    = "//*[@id='stringQuestion']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $edit_mail     = ".//*[@id='email']";

	//field fill values

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $firstname_fill   = "Sam";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $lastname_fill    = "Sample";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $special_fill     = "0815";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $abuseLink     = "http://www.abuse.nil/";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mail_fill_1      = "dummy-1@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mail_fill_2      = "dummy-2@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $mail_fill_unreachable_domain      = "dummy@unreachable.nil";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $mail_fill_unreachable_mailbox      = "dummy@boldt-webservice.de";

	// button identifier

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_register          = "//*[@id='bwp_com_form']/div/div[contains(@class, 'button-register')]/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_send_activation   = ".//*[@id='bwp_com_form']/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $send_edit_link           = ".//*[@id='bwp_com_form']/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_unsubscribe       = ".//*[@id='unsubscribe']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_submit            = ".//*[@id='bwp_com_form']/div/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_submitleave       = ".//*[@id='bwp_com_form']/div/button[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_edit              = "//*[@id='bwp_com_register_success']/div/div/p[3]/a";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mandatory_msg           = ".//*[@id='bwp_mod_form_required']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_link  = "/index.php?option=com_bwpostman&view=register&task=activate&subscriber=";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $editlink         = "/index.php?option=com_bwpostman&view=edit&editlink=";

	// success message identifier

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_message          = ".//*[@id='bwp_com_register_success']/div/div";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $registration_complete    = ".//*[@id='bwp_com_register_success']/div/div/p[1]/strong";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_complete      = ".//*[@id='bwp_com_register_success']/div/div/p[1]/strong";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activated_edit_Link      = ".//*[@id='bwp_com_register_success']/div/div/p[3]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $edit_saved_successfully  = ".//*[@id='system-message']/div";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $register_success         = ".//*[@id='bwp_com_register_success']";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorContainerHeader         = "//*[@id='system-message-container']/joomla-alert/div[1][contains(@class, 'alert-heading')]";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorContainerContent         = "//*[@id='system-message-container']/joomla-alert/div[2]/div[contains(@class, 'alert-message')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorContainerContentModal         = "//*[@id='bwp_mod_modal-content'][contains(@class, 'bwp-err')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $firstname_star    = "//*/div[contains(@class, 'user_firstname')]/div/div/span/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $name_star    = "//*/div[contains(@class, 'user_name')]/div/div/span/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $special_star    = "//*/div[contains(@class, 'edit_special')]/div/div/span/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $special_placeholder    = "//*/div[contains(@class, 'edit_special')]/label[contains(text(), '%s')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mailaddress_star    = "//*/div[contains(@class, 'user_email')]/div/div/span/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml_select_star    = "//*/div[contains(@class, 'mail_available')]/sup/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_star    = "//*/div[contains(@class, 'agree_check')]/label/sup/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $security_star    = "//*/div[contains(@class, 'question-result')]/div/div/span/i[contains(@class, 'fa-star')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml_desc_long    = "02 Mailingliste 6 weiterer Lauf B";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml_desc_short    = "02 Mailingliste 6 ...";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml_desc_label    = "02 Mailingliste 6 B:";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $ml_desc_identifier    = "//*/div[contains(@class, 'mailinglists2')]/label/span[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $intro_identifier    = "//*/div[contains(@class, 'pre_text')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $intro_text_comp    = "Introtext to registration by component";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_link_modal    = "//*[@id='bwp_com_open']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_link    = "//*/div[contains(@class, 'agree_check')]/label/a";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_modal_identifier    = "//*/div[@id='bwp_com_Modal']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_modal_close    = "//*/div[@id='bwp_com_Modal']/div/span[contains(@class,'bwp_com_close')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_url_text    = "Alle Angaben auf dieser Web-Site dienen ausschließlich informativen Zwecken";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_article_text    = "Templates control the look and feel of your website.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $disclaimer_menuitem_text    = "This tells you a bit about this blog and the person who writes it.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $security_question_error    = "Spam question: You entered the wrong result!";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $activation_mail_error    = "has successfully been processed but an email with the activation code for your subscription could not be sent.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mailinglist_number    = "//*/div[@id='bwp_mod_form_listsfield']/div[@class='a_mailinglist_item_%s']";


	//messages

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $registration_completed_text  = "Registration completed!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_occurred_text          = "An error occurred!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_sent_text         = "The activation code has been sent to the given email address";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $activation_completed_text    = "Activation completed!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $edit_get_text                = "If you want to change your newsletter profile, please enter your email";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $editlink_sent_text           = "The edit link has been sent to the given email address.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_saved_successfully       = "Changes successfully saved";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_saved_changes            = "Data changed!";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $msg_changed_mailaddress  = "To ensure the change of the mail address";

	// error message identifier

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $err_activation_incomplete    = ".//*[@id='bwp_com_error_account_notactivated']/p[1]/strong";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $err_already_subscribed       = ".//*[@id='bwp_com_error_account_general']/p[1]/strong";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $err_not_activated            = ".//*[@id='bwp_com_error_account_notactivated']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $get_edit_Link                = ".//*[@id='bwp_com_error_account_general']/p[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $err_get_editlink             = ".//*[@id='bwp_com_error_geteditlink']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $err_no_activation             = ".//*[@id='bwp_com_error_email']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_err_occurred            = "An error occurred!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_err_invalid_link        = "You cannot activate your newsletter subscription because the entered activation link is invalid";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_err_no_subscription     = "There is not yet a newsletter subscription for the entered email address";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_err_wrong_editlink      = "You cannot edit your newsletter subscription because the entered edit link is invalid.";


	// invalid fields

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_name               = 'Please enter a name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_name_mod           = 'Please enter a name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_firstname          = 'Please enter a first name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_firstname_mod      = 'Please enter a first name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_mailaddress        = 'Please enter an email address!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_special_mod        = 'Please enter a value into the %s field!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_valid_mailaddress          = 'Please enter a valid mailaddress!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_select_newsletter          = 'You have to select at least one mailinglist for finishing up your registration.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_select_newsletter_mod    = 'You have to select one newsletter.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_enter_special              = 'Please enter a value into the %s field!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_accept_disclaimer          = 'You have to accept the Disclaimer for finishing up your registration.';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_name_132           = 'Please enter a name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_firstname_132      = 'Please enter a first name!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_field_mailaddress_132    = 'Please enter an email address!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalid_select_newsletter_132    = 'You have to select one newsletter.';

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseFirstName    = "Invalid input at 'Your first name'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseLastName    = "Invalid input at 'Your name'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseSpecial    = "Invalid input at '%s'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseEmail    = "Invalid input at 'Your email address'";

    /**
     * @var string
     *
     * @since 4.1.3
     */
    public static $loginPage    = "/index.php?option=com_users&view=login";

    /**
     * @var string
     *
     * @since 4.3.1
     */
    public static $profilePage    = "/index.php?option=com_users&view=profile";

    /**
     * @var string
     *
     * @since 4.1.3
     */
    public static $loginUsernameField    = "//*[@id='username']";

    /**
     * @var string
     *
     * @since 4.1.3
     */
    public static $loginPasswordField    = "//*[@id='password']";

    /**
     * @var string
     *
     * @since 4.1.3
     */
	public static $loginButton    = "//*[@id='com-users-login__form']/fieldset/div/div/button[@type='submit']";

    /**
     * @var string
     *
     * @since 4.1.3
     */
	public static $logoutButton    = "//*[@class='com-users-logout__form form-horizontal well']/div/div/button[@type='submit']";

    /**
     * @var string
     *
     * @since 4.1.3
     */
    public static $profileNameField    = "//*[@id='users-profile-core']/dl/dd[1]";

    /**
     * @var string
     *
     * @since 4.1.3
     */
    public static $itemTitle    = "//*/div[@class='blog-featured']/div[1]/div/h2/a";

    /**
	 * Test method to subscribe to newsletter in front end by component
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @throws \Exception
	 *
	 * @since   2.2.1
	 */
	public static function subscribeByComponent(\AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		$I->amOnPage(self::$register_url);
		$I->wait(1);
		$I->scrollTo(self::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(self::$view_register);

		if ($options->show_gender)
		{
			$I->clickAndWait(self::$gender, 1);
			$I->click(self::$gender_female);
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(self::$firstname, self::$firstname_fill);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(self::$name, self::$lastname_fill);
		}

		$I->fillField(self::$mail, self::$mail_fill_1);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(self::$format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(self::$special, self::$special_fill);
		}

		$I->scrollTo(self::$ml1, 0, -100);
		$I->wait(1);
		$I->checkOption(self::$ml1);

		if ($options->disclaimer)
		{
			$I->checkOption(self::$disclaimer);
		}
	}

	/**
	 * Test method to activate newsletter subscription
	 *
	 * @param \AcceptanceTester             $I
	 * @param string                        $mailaddress
	 * @param bool                          $good
	 *
	 * @throws \Exception
	 *
	 * @since   2.2.1
	 */
	public static function activate(\AcceptanceTester $I, $mailaddress, $good = true)
	{
		$activation_code = $I->getActivationCode($mailaddress);
		$I->amOnPage(self::$activation_link . $activation_code);
		if ($good)
		{
			$I->see(self::$activation_completed_text, self::$activation_complete);
		}
	}

	/**
	 * Test method to unsubscribe from all newsletters
	 *
	 * @param \AcceptanceTester             $I
	 * @param string                        $button
	 *
	 * @throws \Exception
	 *
	 * @since   2.2.1
	 */
	public static function unsubscribe(\AcceptanceTester $I, $button)
	{
		$I->scrollTo($button, 0, -150);
		$I->wait(1);
		$I->click($button);
		$I->waitForElement(self::$view_edit, 30);
		$I->seeElement(self::$view_edit);
		$I->scrollTo(self::$button_submitleave, 0, -150);
		$I->wait(1);
		$I->checkOption(self::$button_unsubscribe);
		$I->click(self::$button_submitleave);
		$I->dontSee(self::$mail_fill_1, self::$mail);
	}

    /**
     * Test method to login at frontend
     *
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     *
     * @since   4.1.3
     */
    public static function loginToFrontend(\AcceptanceTester $I)
    {
//        Go to login page
        $I->amOnPage(self::$loginPage);
        $I->wait(1);
        $I->seeElement(self::$loginUsernameField);

//        Fill login data
        $I->fillField(self::$loginUsernameField, 'BwPostmanPublisher');
        $I->fillField(self::$loginPasswordField, 'BwPostmanTest');

//        Submit login
		$I->scrollTo(self::$loginButton, 0, -100);
		$I->wait(1);
        $I->clickAndWait(self::$loginButton, 1);

//        Check success of login
        $I->amOnPage(self::$profilePage);
        $I->wait(1);
        $I->see('BwPostmanPublisher', self::$profileNameField);
    }

    /**
     * Test method to logout from frontend
     *
     * @param \AcceptanceTester $I
     *
     * @throws \Exception
     *
     * @since   4.1.3
     */
    public static function logoutFromFrontend(\AcceptanceTester $I)
    {
//        Go to login page
        $I->amOnPage(self::$loginPage);
        $I->wait(1);
        $I->seeElement(self::$logoutButton);

//        Submit logout
        $I->clickAndWait(self::$logoutButton, 1);

//        Check success of logout
        $I->see('Joomla!', );

    }
}
