<?php
namespace Page;

use AcceptanceTester;
use Exception;

/**
 * Class ModulesPage
 *
 * @package Page
 * @copyright (C) 2020 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   4.0.0
 */
class RegistrationModulePage
{
	// field identifiers module

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_gender_select_id = ".//*[@id='m_gender']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_gender_female = ".//*[@id='m_gender_chosen']/div/ul/li[contains(text(), 'female')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_gender_male   = ".//*[@id='m_gender_chosen']/div/ul/li[contains(text(), 'male')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_female        = '1';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_male          = '0';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_firstname     = '#a_firstname';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_name          = '#a_name';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_special       = '#a_special';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_mail          = '#a_email';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_format_text   = ".//*[@id='edit_mailformat_m']/label[1]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_format_html   = ".//*[@id='edit_mailformat_m']/label[2]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml0           = '#a_mailinglists0';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml1           = '#a_mailinglists1';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml2           = '#a_mailinglists2';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_math_captcha  = '#a_stringCaptcha';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModalHeaderButton    = "//*/div[@id='registerErrors']/div/div/div[@class='modal-header']/button";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModalBody    = "//*/div[@id='registerErrors']/div/div/div[@class='modal-body']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModalFooterButton    = "//*/div[@id='registerErrors']/div/div/div[@class='modal-footer']/button";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModalCloseButton     = "//*/div[@id='bwp_mod_modal-content']/span[contains(@class,'bwp_mod_close')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModalPopupBody    = "//*/div[@id='bwp_mod_wrapper']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModulBody    = "//*/div[@id='bwp_mod_wrapper']";

	/* @var string
	 *
	 * @since 4.0.0
	 */
	public static $errorModulBodyAlert    = "//*/joomla-alert/div/div[%s]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer    = "//*[@id='agreecheck_mod']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_scroll    = "//*[@id='bwp_mod_form_disclaimer']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_question    = "//*[@id='a_stringQuestion']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_captcha    = "//*[@id='a_stringCaptcha']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_captcha_image    = "//*/p[@class='security_question_lbl']/img";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_button_register      = ".//*[@id='bwp_mod_form']/*/button";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_button_edit          = ".//*[@id='bwp_mod_form_editlink']/button";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_name_mod           = 'Please enter a name!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_firstname_mod      = 'Please enter a first name!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_special_mod        = 'Please enter a value into the %s  field!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_select_newsletter_mod    = 'You have to select one newsletter.';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_select_mailaddress_mod    = 'Please enter an email address!';

	/* @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_name_mod_pop           = 'Please enter a lastname!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_firstname_mod_pop      = 'Please enter a firstname!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_field_special_mod_pop        = 'Please enter a value in field %s!';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_select_newsletter_mod_pop    = 'You have to select at least one newsletter for finishing up your registration.';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $invalid_select_mailaddress_mod_pop    = 'You have to select at least one newsletter for finishing up your registration.';

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_firstname_star    = "//*[@id='bwp_mod_form_firstnamefield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_name_star    = "//*[@id='bwp_mod_form_namefield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_special_star    = "//*[@id='bwp_mod_form_specialfield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_firstname_star_popup    = "//*[@id='bwp_mod_form_firstnamefield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_name_star_popup    = "//*[@id='bwp_mod_form_namefield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_special_star_popup    = "//*[@id='bwp_mod_form_specialfield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_mailaddress_star_popup    = "//*[@id='bwp_mod_form_emailfield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_special_placeholder    = "//*[@id='bwp_mod_form_specialfield']/input[@placeholder='%s']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_mailaddress_star    = "//*[@id='bwp_mod_form_emailfield']/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml_select_star    = "//*[@id='bwp_mod_form_lists']/sup/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_star    = "//*[@id='bwp_mod_form_disclaimer']/span/sup/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_security_star    = "//*/p[contains(@class, 'question')]/span/i[@class='icon-star']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml_desc_long    = "02 Mailingliste 6 weiterer Lauf B";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml_desc_short    = "02 Mailingliste 6 ...";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml_desc_label    = "02 Mailingliste 6 B:";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_ml_desc_identifier    = "//*/div[@class='a_mailinglist_item_2']/label/span";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_intro_identifier    = "//*[@id='bwp_mod_form_pretext']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_intro_text_comp    = "Introtext to registration by component";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_intro_text_mod    = "Introtext to registration by module";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_link_modal    = "//*[@id='bwp_mod_open']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_link    = "//*[@id='bwp_mod_form_disclaimer']/span/a";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_modal_identifier    = "//*/div[@id='bwp_mod_modal-content']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_modal_close    = "//*/div[@id='bwp_mod_Modal']/div/span[contains(@class,'bwp_mod_close')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_modal_popup_close    = "//*/div[@id='bwp_mod_modal-content']/span[contains(@class,'bwp_mod_close')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_url_text    = "Alle Angaben auf dieser Web-Site dienen ausschließlich informativen Zwecke";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_article_text    = "Templates control the look and feel of your website.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_disclaimer_menuitem_text    = "This tells you a bit about this blog and the person who writes it.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_security_question_error    = "You have to enter the result of the spam question.";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_mailinglist_number    = "//*/div[@id='bwp_mod_form_listsfield']/div[@class='a_mailinglist_item_%s']";

	// modal registration identifiers module

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_position = "//*[@id='mod_bwpostman']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_modal_button_lbl = "//*[@id='jform_params_modal_btn_label']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_button_module = "//*[@id='bwp_reg_open']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_modal_content = "//*[@id='bwp_reg_modal-content']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_item_identifier = "//*[contains(@class,'a_mailinglist_item_0')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_item_text_identifier = "//*[contains(@class,'a_mailinglist_item_0')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $module_item_br = "//*[contains(@class,'a_mailinglist_item_0')]/span/br";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_register_close = "//*[@id='bwp_reg_modal-content']/div/span[@class='bwp_reg_close']";


	// subscriber mail and base of activation link


	/**
	 * Test method to preset the options of the module
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public static function presetModuleOptions(AcceptanceTester $I)
	{
        $menuItemId = $I->grabFromDatabase('jos_menu', 'id', array('title' => 'Help'));

		// Mailing list selection
		$I->setManifestOption('mod_bwpostman', 'ml_available', array("5", "27", "4", "24"));
		$I->setManifestOption('mod_bwpostman', 'show_desc', '1');
		$I->setManifestOption('mod_bwpostman', 'desc_length', '50');

		// Settings for the registration form
		$I->setManifestOption('mod_bwpostman', 'com_params', '1');

		$I->setManifestOption('mod_bwpostman', 'fe_layout', '');
		$I->setManifestOption('mod_bwpostman', 'pretext', 'Introtext to registration by module');
		$I->setManifestOption('mod_bwpostman', 'show_gender', '1');
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('mod_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('mod_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'show_special', '1');
		$I->setManifestOption('mod_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('mod_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('mod_bwpostman', 'default_emailformat', '1');
		$I->setManifestOption('mod_bwpostman', 'disclaimer', '1');
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '1');
		$I->setManifestOption('mod_bwpostman', 'disclaimer_link', 'https://www.jahamo-training.de/index.php?option=com_content&view=article&id=15&Itemid=582');
		$I->setManifestOption('mod_bwpostman', 'article_id', '6');
		$I->setManifestOption('mod_bwpostman', 'disclaimer_menuitem', $menuItemId);
		$I->setManifestOption('mod_bwpostman', 'disclaimer_target', '0');
		$I->setManifestOption('mod_bwpostman', 'showinmodal', '1');
		$I->setManifestOption('mod_bwpostman', 'use_captcha', '0');
		$I->setManifestOption('mod_bwpostman', 'security_question', 'Wieviele Beine hat ein Pferd? (1, 2, ...)');
		$I->setManifestOption('mod_bwpostman', 'security_answer', '4');

		// Settings for the layout
		$I->setManifestOption('mod_bwpostman', 'layout', '_:default');
		$I->setManifestOption('mod_bwpostman', 'modal_btn_label', 'Newsletter Registration');
	}


	/**
	 * Test method placeholder
	 *
	 * @param   AcceptanceTester   $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public static function placeholder(AcceptanceTester $I)
	{

	}
}
