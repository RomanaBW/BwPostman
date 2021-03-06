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
	public static $register_edit_url    = ".//*[@id='bwp_com_form']/div[1]/p[contains(@class, 'user_edit')]/a";


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
	public static $gender       = "//*[@id='gender_chosen']/a";

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
	 * @since 2.0.0
	 */
	public static $math_captcha  = ".//*[@id='stringCaptcha']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $disclaimer    = ".//*[@id='agreecheck']";

	// field identifier module

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_gender_female = ".//*[@id='genFemaleMod']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_gender_male   = ".//*[@id='genMaleMod']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_female        = '1';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_male          = '0';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_firstname     = '#a_firstname';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_name          = '#a_name';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_special       = '#a_special';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_mail          = '#a_email';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_format_text   = '#formatTextMod';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_format_html   = '#formatHtmlMod';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_ml0           = '#a_mailinglists0';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_ml1           = '#a_mailinglists1';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_ml2           = '#a_mailinglists2';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_math_captcha  = '#a_stringCaptcha';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_disclaimer    = '#agreecheck_mod';


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
	public static $button_register          = ".//*[@id='bwp_com_form']/p[2]/button";

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
	public static $button_submit            = ".//*[@id='bwp_com_form']/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_submitleave       = ".//*[@id='bwp_com_form']/button[2]";

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
	public static $mod_button_register      = ".//*[@id='bwp_mod_form']/*/button[text()='Register']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mod_button_edit          = ".//*[@id='bwp_mod_form_editlink']/button";

	// subscriber mail and base of activation link

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
	public static $errorContainerHeader         = "//*[@id='system-message']/div/h4";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorContainerContent         = "//*[@id='system-message']/div/div/div[contains(@class, 'alert-message')]";


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
	public static $msg_err_occurred            = "An error occurred!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_err_invalid_link        = "You cannot activate your newsletter subscription because the entered activation link is invalid.";

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
	public static $popup_select_newsletter          = 'You have to select at least one newsletter for finishing up your registration.';

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
		$I->click($button);
		$I->waitForElement(self::$view_edit, 30);
		$I->seeElement(self::$view_edit);
		$I->scrollTo(self::$button_submitleave, 0, -150);
		$I->checkOption(self::$button_unsubscribe);
		$I->click(self::$button_submitleave);
		$I->dontSee(self::$mail_fill_1, self::$mail);
	}
}
