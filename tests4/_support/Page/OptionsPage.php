<?php
namespace Page;

use Page\MainviewPage as MainView;

/**
 * Class MaintenancePage
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
class OptionsPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */

	// Tabs

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_basics       = ".//*[@id='configTabs']/div/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_registration = ".//*[@id='configTabs']/div/button[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_activation   = ".//*[@id='configTabs']/div/button[3]";

	/**
	 * @var string
	 *
	 * @since 2.2.1
	 */
	public static $tab_unsubscription   = ".//*[@id='configTabs']/div/button[4]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_lists_view   = ".//*[@id='configTabs']/div/button[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_single_view  = ".//*[@id='configTabs']/div/button[6]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_permissions  = "//*[@id='configTabs']/div/button[7]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $permissions_fieldset  = "//*[@id='fieldset-permissions']";

	/*
	 * Tab basic settings
	 */

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $config_save_success      = "Configuration saved.";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersName  = ".//*[@id='jform_default_from_name']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersEmail  = ".//*[@id='jform_default_from_email']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $replyEmail  = ".//*[@id='jform_default_reply_email']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $legalInfo  = ".//*[@id='jform_legal_information_text']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $excludedCategories  = ".//*[@id='jform_excluded_categories_chzn']/ul/li/input";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $excludedCategoriesListResult  = ".//*[@id='jform_excluded_categories_chzn']/div/ul/li";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $excludedCategoriesResult  = ".//*[@id='jform_excluded_categories_chzn']/ul/li[1]/span";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $excludedCategoriesEmptyResult  = ".//*[@id='jform_excluded_categories_chzn']/ul/li[1]/a";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $numberNlsToSend  = ".//*[@id='jform_default_mails_per_pageload']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $delayTime  = ".//*[@id='jform_mails_per_pageload_delay']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $delayUnitMinutes  = ".//*[@id='jform_mails_per_pageload_delay_unit']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $delayUnitSeconds  = ".//*[@id='jform_mails_per_pageload_delay_unit']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $publishNlsAtSendingNo  = ".//*[@id='jform_publish_nl_by_default']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $publishNlsAtSendingYes  = ".//*[@id='jform_publish_nl_by_default']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $compressBackupFileNo  = ".//*[@id='jform_compress_backup']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $compressBackupFileYes  = ".//*[@id='jform_compress_backup']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showBwLinkNo  = ".//*[@id='jform_show_boldt_link']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showBwLinkYes  = ".//*[@id='jform_show_boldt_link']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersNameByJoomla  = "Joomla-Test";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersNameByOption  = "Option-Test";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersMailByJoomla  = "webmaster@boldt-webservice.de";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $sendersMailByOption  = "max.mayr@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $replyMailByJoomla  = "webmaster@boldt-webservice.de";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $replyMailByOption  = "max.mayer@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $newslettersPerStep  = 50;

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $newslettersPerStepDefault  = 100;

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $numberOfSeconds  = 5;

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $numberOfSecondsDefault  = 1;


	/*
	* Tab registration
	*/

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $introText  = "";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showGenderNo  = ".//*[@id='jform_show_gender']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showGenderYes  = ".//*[@id='jform_show_gender']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showLastNameNo  = ".//*[@id='jform_show_name_field']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showLastNameYes  = ".//*[@id='jform_show_name_field']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryLastNameNo  = ".//*[@id='jform_name_field_obligation']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryLastNameYes  = ".//*[@id='jform_name_field_obligation']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showFirstNameNo  = ".//*[@id='jform_show_firstname_field']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showFirstNameYes  = ".//*[@id='jform_show_firstname_field']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryFirstNameNo  = ".//*[@id='jform_firstname_field_obligation']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryFirstNameYes  = ".//*[@id='jform_firstname_field_obligation']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showAdditionalFieldNo  = ".//*[@id='jform_show_special']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showAdditionalFieldYes  = ".//*[@id='jform_show_special']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryAdditionalFieldNo  = ".//*[@id='jform_special_field_obligation']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $obligatoryAdditionalFieldYes  = ".//*[@id='jform_special_field_obligation']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $additionalFieldLabel  = ".//*[@id='jform_special_label']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $additionalFieldTooltip  = ".//*[@id='jform_special_desc']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showMailinglistDescriptionNo  = ".//*[@id='jform_show_desc']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showMailinglistDescriptionYes  = ".//*[@id='jform_show_desc']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $MailinglistDescriptionLength  = ".//*[@id='jform_desc_length']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showDisclaimerNo  = ".//*[@id='jform_disclaimer']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $showDisclaimerYes  = ".//*[@id='jform_disclaimer']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerLinkTargetUrl  = ".//*[@id='jform_disclaimer_selection']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerLinkTargetArticle  = ".//*[@id='jform_disclaimer_selection']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerLinkTargetMenuItem  = ".//*[@id='jform_disclaimer_selection']/label[3]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerLink  = ".//*[@id='jform_disclaimer_link']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerArticle  = ".//*[@id='jform_article_id_name']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $DisclaimerMenuItem  = ".//*[@id='jform_disclaimer_menuitem']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $disclaimerCurrentWindowNo  = ".//*[@id='jform_disclaimer_target']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $disclaimerCurrentWindowYes  = ".//*[@id='jform_disclaimer_target']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $disclaimerPopupNo  = ".//*[@id='jform_showinmodal']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $disclaimerPopupYes  = ".//*[@id='jform_showinmodal']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $secureRegistrationNo  = ".//*[@id='jform_use_captcha']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $secureRegistrationQuestion  = ".//*[@id='jform_use_captcha']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $secureRegistrationCaptcha  = ".//*[@id='jform_use_captcha']/label[3]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $SecurityQuestion  = ".//*[@id='jform_security_question']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $SecurityAnswer  = ".//*[@id='jform_security_answer']";


	/*
	 * Tab activation
	 */

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $titleForActivation  = ".//*[@id='jform_activation_salutation_text']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $textForActivation  = ".//*[@id='jform_activation_text']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $textAgreement  = ".//*[@id='jform_permission_text']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $activationToWebmasterNo  = ".//*[@id='jform_activation_to_webmaster']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $activationToWebmasterYes  = ".//*[@id='jform_activation_to_webmaster']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $activationSenderName  = ".//*[@id='jform_activation_from_name']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $activationSenderMail  = ".//*[@id='jform_activation_to_webmaster_email']";

	/*
	 * Tab unsubscription
	 */

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionOneClickNo  = ".//*[@id='jform_del_sub_1_click']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionOneClickYes  = ".//*[@id='jform_del_sub_1_click']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionToWebmasterNo  = ".//*[@id='jform_deactivation_to_webmaster']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionToWebmasterYes  = ".//*[@id='jform_deactivation_to_webmaster']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionSenderName  = ".//*[@id='jform_deactivation_from_name']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionSenderMail  = ".//*[@id='jform_deactivation_to_webmaster_email']";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionSenderNameValue  = "BwPostman Test Unsubscription";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $unsubscriptionSenderMailValue  = "webmaster@tester-net-bwpm.nil]";


	/*
	 * Tab lists view
	 */

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowSearchFieldShow  = ".//*[@id='jform_filter_field']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowSearchFieldHide  = ".//*[@id='jform_filter_field']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowDateFilterShow  = ".//*[@id='jform_date_filter_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowDateFilterHide  = ".//*[@id='jform_date_filter_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowMailinglistFilterShow  = ".//*[@id='jform_ml_filter_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowMailinglistFilterHide  = ".//*[@id='jform_ml_filter_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowCampaignFilterShow  = ".//*[@id='jform_cam_filter_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowCampaignFilterHide  = ".//*[@id='jform_cam_filter_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowUsergroupFilterShow  = ".//*[@id='jform_group_filter_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsShowUsergroupFilterHide  = ".//*[@id='jform_group_filter_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsEnableAttachmentShow  = ".//*[@id='jform_attachment_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsEnableAttachmentHide  = ".//*[@id='jform_attachment_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsCheckAccessYes  = ".//*[@id='jform_access_check']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $listsCheckAccessNo  = ".//*[@id='jform_access_check']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_list_id        = "jform_display_num_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_list           = ".//*[@id='jform_display_num_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_5              = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='5']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_10             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='10']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_15             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='15']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_20             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='20']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_25             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='25']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_30             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='30']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_50             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='05']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_100             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='100']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_all             = ".//*[@id='jform_display_num_chzn']/div/ul/li[text()='all']";



	/*
	 * Tab single view
	 */

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $detailsEnableAttachmentShow  = ".//*[@id='jform_attachment_single_enable']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $detailsEnableAttachmentHide  = ".//*[@id='jform_attachment_single_enable']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $detailsSubjectAsTitleYes  = ".//*[@id='jform_subject_as_title']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $detailsSubjectAsTitleNo  = ".//*[@id='jform_subject_as_title']/label[2]";

	/*
	 * Tab permissions
	 */

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider = "//*/joomla-field-permissions/joomla-tab/div/button[contains(text(), '%s')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanAdmin = "//*/joomla-field-permissions/joomla-tab/div/button[10]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanManager = "//*/joomla-field-permissions/joomla-tab/div/button[11]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanPublisher = "//*/joomla-field-permissions/joomla-tab/div/button[12]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanEditor = "//*/joomla-field-permissions/joomla-tab/div/button[13]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistAdmin
		= "//*/joomla-field-permissions/joomla-tab/div/button[14]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistPublisher
		= "//*/joomla-field-permissions/joomla-tab/div/button[15]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistEditor
		= "//*/joomla-field-permissions/joomla-tab/div/button[16]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberAdmin = "//*/joomla-field-permissions/joomla-tab/div/button[17]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberPublisher
		= "//*/joomla-field-permissions/joomla-tab/div/button[18]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberEditor
		= "//*/joomla-field-permissions/joomla-tab/div/button[19]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterAdmin = "//*/joomla-field-permissions/joomla-tab/div/button[20]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterPublisher
		= "//*/joomla-field-permissions/joomla-tab/div/button[21]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterEditor
		= "//*/joomla-field-permissions/joomla-tab/div/button[22]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignAdmin = "//*/joomla-field-permissions/joomla-tab/div/button[23]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignPublisher
		= "//*/joomla-field-permissions/joomla-tab/div/button[24]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignEditor = "//*/joomla-field-permissions/joomla-tab/div/button[25]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplateAdmin = "//*/joomla-field-permissions/joomla-tab/div/button[26]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplatePublisher
		= "//*/joomla-field-permissions/joomla-tab/div/button[27]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplateEditor = "//*/joomla-field-permissions/joomla-tab/div/button[28]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $select_permission    = ".//*/[@id='jform_rules_core.admin_1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_allowed   = "Allowed";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_denied    = "Denied";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_inherited = "Inherited";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $action       = "//*[@id='permission-%s']/table/tbody/tr[%s]/td[2]/select";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
//	public static $result_row   = "//*[@id='permission-%s']/../../../td[3]/output/span";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_groups    = array( 'BwPostmanAdmin'
	                                       => array (
	                                       	    'permissions'
	                                            => array(
	                                            	'core.admin' => 'Allowed',
		                                            'core.login.admin' => 'Allowed',
		                                            'core.manage' => 'Allowed',
		                                            'bwpm.create' => 'Allowed',
													'bwpm.edit' => 'Allowed',
													'bwpm.edit.own' => 'Allowed',
													'bwpm.edit.state' => 'Allowed',
													'bwpm.archive' => 'Allowed',
													'bwpm.restore' => 'Allowed',
													'bwpm.delete' => 'Allowed',
													'bwpm.send' => 'Allowed',
													'bwpm.view.newsletter' => 'Allowed',
													'bwpm.view.subscriber' => 'Allowed',
													'bwpm.view.campaign' => 'Allowed',
													'bwpm.view.mailinglist' => 'Allowed',
													'bwpm.view.template' => 'Allowed',
													'bwpm.view.archive' => 'Allowed',
													'bwpm.admin.newsletter' => 'Allowed',
													'bwpm.admin.subscriber' => 'Allowed',
													'bwpm.admin.campaign' => 'Allowed',
													'bwpm.admin.mailinglist' => 'Allowed',
													'bwpm.admin.template' => 'Allowed',
													'bwpm.view.maintenance' => 'Allowed',
												),
												'parent'    => 'Manager',
											),

											'BwPostmanManager'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanPublisher'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanManager',
											),

											'BwPostmanEditor'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanPublisher',
											),

											'BwPostmanCampaignAdmin'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanCampaignPublisher'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanCampaignAdmin',
											),

											'BwPostmanCampaignEditor'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanCampaignPublisher',
											),

											'BwPostmanMailinglistAdmin'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanMailinglistPublisher'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanMailinglistAdmin',
											),

											'BwPostmanMailinglistEditor'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanMailinglistPublisher',
											),

											'BwPostmanNewsletterAdmin'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanNewsletterPublisher'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanNewsletterAdmin',
											),

											'BwPostmanNewsletterEditor'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanNewsletterPublisher',
											),

											'BwPostmanSubscriberAdmin'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanSubscriberPublisher'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanSubscriberAdmin',
											),

											'BwPostmanSubscriberEditor'
	                                        => array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanSubscriberPublisher',
											),

											'BwPostmanTemplateAdmin'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Denied',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Denied',
													'bwpm.admin.subscriber' => 'Denied',
													'bwpm.admin.campaign' => 'Denied',
													'bwpm.admin.mailinglist' => 'Denied',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanTemplatePublisher'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Denied',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanTemplateAdmin',
											),

											'BwPostmanTemplateEditor'
											=> array (
												'permissions'
												=> array(
													'core.admin' => 'Inherited',
													'core.login.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.admin.newsletter' => 'Inherited',
													'bwpm.admin.subscriber' => 'Inherited',
													'bwpm.admin.campaign' => 'Inherited',
													'bwpm.admin.mailinglist' => 'Inherited',
													'bwpm.admin.template' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanTemplatePublisher',
											),
										);

	// buttons

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_allowed               = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Allowed')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_not_allowed           = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Not Allowed')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_inherited_allowed     = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Allowed (Inherited)')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_inherited_not_allowed = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Not Allowed (Locked)')]";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $noticeToClose = array(
//		'BwPostmanCampaignAdmin' => 'core.admin',
		'BwPostmanMailinglistAdmin' => 'core.admin',
		'BwPostmanNewsletterAdmin' => 'core.admin',
		'BwPostmanSubscriberAdmin' => 'core.admin',
		'BwPostmanTemplateAdmin' => 'core.admin',

//		'BwPostmanPublisher' => 'core.manage',
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_group_permissions = array(  'BwPostmanAdmin'
	                                                => array(
	                                                	'core.admin' => 'Allowed',
		                                                'core.login.admin' => 'Allowed',
		                                                'core.manage' => 'Allowed',
		                                                'bwpm.create' => 'Allowed',
		                                                'bwpm.edit' => 'Allowed',
		                                                'bwpm.edit.own' => 'Allowed',
		                                                'bwpm.edit.state' => 'Allowed',
		                                                'bwpm.archive' => 'Allowed',
		                                                'bwpm.restore' => 'Allowed',
		                                                'bwpm.delete' => 'Allowed',
		                                                'bwpm.send' => 'Allowed',
		                                                'bwpm.view.newsletter' => 'Allowed',
		                                                'bwpm.view.subscriber' => 'Allowed',
		                                                'bwpm.view.campaign' => 'Allowed',
		                                                'bwpm.view.mailinglist' => 'Allowed',
		                                                'bwpm.view.template' => 'Allowed',
		                                                'bwpm.view.archive' => 'Allowed',
		                                                'bwpm.admin.newsletter' => 'Allowed',
		                                                'bwpm.admin.subscriber' => 'Allowed',
		                                                'bwpm.admin.campaign' => 'Allowed',
		                                                'bwpm.admin.mailinglist' => 'Allowed',
		                                                'bwpm.admin.template' => 'Allowed',
		                                                'bwpm.view.maintenance' => 'Allowed',
														),
	                                                'BwPostmanManager'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Allowed (Inherited)',
		                                                'bwpm.restore' => 'Allowed (Inherited)',
		                                                'bwpm.delete' => 'Allowed (Inherited)',
		                                                'bwpm.send' => 'Allowed (Inherited)',
		                                                'bwpm.view.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.view.subscriber' => 'Allowed (Inherited)',
		                                                'bwpm.view.campaign' => 'Allowed (Inherited)',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Allowed (Inherited)',
		                                                'bwpm.view.archive' => 'Allowed (Inherited)',
		                                                'bwpm.admin.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.admin.subscriber' => 'Allowed (Inherited)',
		                                                'bwpm.admin.campaign' => 'Allowed (Inherited)',
		                                                'bwpm.admin.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.admin.template' => 'Allowed (Inherited)',
		                                                'bwpm.view.maintenance' => 'Not Allowed',
		                                                ),
											'BwPostmanPublisher'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed (Locked)',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Not Allowed',
		                                                'bwpm.restore' => 'Not Allowed',
		                                                'bwpm.delete' => 'Not Allowed',
		                                                'bwpm.send' => 'Allowed (Inherited)',
		                                                'bwpm.view.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.view.subscriber' => 'Allowed (Inherited)',
		                                                'bwpm.view.campaign' => 'Allowed (Inherited)',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Allowed (Inherited)',
		                                                'bwpm.view.archive' => 'Not Allowed',
		                                                'bwpm.admin.newsletter' => 'Not Allowed',
		                                                'bwpm.admin.subscriber' => 'Not Allowed',
		                                                'bwpm.admin.campaign' => 'Not Allowed',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed',
		                                                'bwpm.admin.template' => 'Not Allowed',
		                                                'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												),
											        'BwPostmanEditor'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed (Locked)',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Not Allowed',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Not Allowed',
		                                                'bwpm.archive' => 'Not Allowed (Locked)',
		                                                'bwpm.restore' => 'Not Allowed (Locked)',
		                                                'bwpm.delete' => 'Not Allowed (Locked)',
		                                                'bwpm.send' => 'Not Allowed',
		                                                'bwpm.view.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.view.subscriber' => 'Allowed (Inherited)',
		                                                'bwpm.view.campaign' => 'Allowed (Inherited)',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Allowed (Inherited)',
		                                                'bwpm.view.archive' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												        ),
											        'BwPostmanCampaignAdmin'
	                                                => array(
												        'core.admin' => 'Not Allowed',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Allowed (Inherited)',
												        'bwpm.restore' => 'Allowed (Inherited)',
												        'bwpm.delete' => 'Allowed (Inherited)',
												        'bwpm.send' => 'Not Allowed',
												        'bwpm.view.newsletter' => 'Not Allowed',
												        'bwpm.view.subscriber' => 'Not Allowed',
												        'bwpm.view.campaign' => 'Allowed (Inherited)',
												        'bwpm.view.mailinglist' => 'Not Allowed',
												        'bwpm.view.template' => 'Not Allowed',
												        'bwpm.view.archive' => 'Allowed (Inherited)',
												        'bwpm.admin.newsletter' => 'Not Allowed',
												        'bwpm.admin.subscriber' => 'Not Allowed',
												        'bwpm.admin.campaign' => 'Allowed (Inherited)',
												        'bwpm.admin.mailinglist' => 'Not Allowed',
												        'bwpm.admin.template' => 'Not Allowed',
												        'bwpm.view.maintenance' => 'Not Allowed',
											        ),
											        'BwPostmanCampaignPublisher'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Not Allowed',
												        'bwpm.restore' => 'Not Allowed',
												        'bwpm.delete' => 'Not Allowed',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.view.campaign' => 'Allowed (Inherited)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Not Allowed (Locked)',
												        'bwpm.view.archive' => 'Not Allowed',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
											        'BwPostmanCampaignEditor'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Not Allowed',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Not Allowed',
												        'bwpm.archive' => 'Not Allowed (Locked)',
												        'bwpm.restore' => 'Not Allowed (Locked)',
												        'bwpm.delete' => 'Not Allowed (Locked)',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.view.campaign' => 'Allowed (Inherited)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Not Allowed (Locked)',
												        'bwpm.view.archive' => 'Not Allowed (Locked)',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												        ),
											        'BwPostmanMailinglistAdmin'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Allowed (Inherited)',
		                                                'bwpm.restore' => 'Allowed (Inherited)',
		                                                'bwpm.delete' => 'Allowed (Inherited)',
		                                                'bwpm.send' => 'Not Allowed',
		                                                'bwpm.view.newsletter' => 'Not Allowed',
		                                                'bwpm.view.subscriber' => 'Not Allowed',
		                                                'bwpm.view.campaign' => 'Not Allowed',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Not Allowed',
		                                                'bwpm.view.archive' => 'Allowed (Inherited)',
		                                                'bwpm.admin.newsletter' => 'Not Allowed',
		                                                'bwpm.admin.subscriber' => 'Not Allowed',
		                                                'bwpm.admin.campaign' => 'Not Allowed',
		                                                'bwpm.admin.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.admin.template' => 'Not Allowed',
		                                                'bwpm.view.maintenance' => 'Not Allowed',
												        ),
											        'BwPostmanMailinglistPublisher'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed (Locked)',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Not Allowed',
		                                                'bwpm.restore' => 'Not Allowed',
		                                                'bwpm.delete' => 'Not Allowed',
		                                                'bwpm.send' => 'Not Allowed (Locked)',
		                                                'bwpm.view.newsletter' => 'Not Allowed (Locked)',
		                                                'bwpm.view.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.view.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.archive' => 'Not Allowed',
		                                                'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed',
		                                                'bwpm.admin.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												        ),
											        'BwPostmanMailinglistEditor'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed (Locked)',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Not Allowed',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Not Allowed',
		                                                'bwpm.archive' => 'Not Allowed (Locked)',
		                                                'bwpm.restore' => 'Not Allowed (Locked)',
		                                                'bwpm.delete' => 'Not Allowed (Locked)',
		                                                'bwpm.send' => 'Not Allowed (Locked)',
		                                                'bwpm.view.newsletter' => 'Not Allowed (Locked)',
		                                                'bwpm.view.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.view.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.view.mailinglist' => 'Allowed (Inherited)',
		                                                'bwpm.view.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.archive' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												        ),
											        'BwPostmanNewsletterAdmin'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Allowed (Inherited)',
		                                                'bwpm.restore' => 'Allowed (Inherited)',
		                                                'bwpm.delete' => 'Allowed (Inherited)',
		                                                'bwpm.send' => 'Allowed (Inherited)',
		                                                'bwpm.view.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.view.subscriber' => 'Not Allowed',
		                                                'bwpm.view.campaign' => 'Not Allowed',
		                                                'bwpm.view.mailinglist' => 'Not Allowed',
		                                                'bwpm.view.template' => 'Not Allowed',
		                                                'bwpm.view.archive' => 'Allowed (Inherited)',
		                                                'bwpm.admin.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.admin.subscriber' => 'Not Allowed',
		                                                'bwpm.admin.campaign' => 'Not Allowed',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed',
		                                                'bwpm.admin.template' => 'Not Allowed',
		                                                'bwpm.view.maintenance' => 'Not Allowed',
												        ),
											        'BwPostmanNewsletterPublisher'
	                                                => array(
	                                                	'core.admin' => 'Not Allowed (Locked)',
		                                                'core.login.admin' => 'Allowed (Inherited)',
		                                                'core.manage' => 'Allowed (Inherited)',
		                                                'bwpm.create' => 'Allowed (Inherited)',
		                                                'bwpm.edit' => 'Allowed (Inherited)',
		                                                'bwpm.edit.own' => 'Allowed (Inherited)',
		                                                'bwpm.edit.state' => 'Allowed (Inherited)',
		                                                'bwpm.archive' => 'Not Allowed',
		                                                'bwpm.restore' => 'Not Allowed',
		                                                'bwpm.delete' => 'Not Allowed',
		                                                'bwpm.send' => 'Allowed (Inherited)',
		                                                'bwpm.view.newsletter' => 'Allowed (Inherited)',
		                                                'bwpm.view.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.view.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
		                                                'bwpm.view.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.archive' => 'Not Allowed',
		                                                'bwpm.admin.newsletter' => 'Not Allowed',
		                                                'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.campaign' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
		                                                'bwpm.admin.template' => 'Not Allowed (Locked)',
		                                                'bwpm.view.maintenance' => 'Not Allowed (Locked)',
												        ),
											        'BwPostmanNewsletterEditor'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Not Allowed',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Not Allowed',
												        'bwpm.archive' => 'Not Allowed (Locked)',
												        'bwpm.restore' => 'Not Allowed (Locked)',
												        'bwpm.delete' => 'Not Allowed (Locked)',
												        'bwpm.send' => 'Not Allowed',
												        'bwpm.view.newsletter' => 'Allowed (Inherited)',
												        'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.view.campaign' => 'Not Allowed (Locked)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Not Allowed (Locked)',
												        'bwpm.view.archive' => 'Not Allowed (Locked)',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
											        'BwPostmanSubscriberAdmin'
	                                                => array(
												        'core.admin' => 'Not Allowed',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Allowed (Inherited)',
												        'bwpm.restore' => 'Allowed (Inherited)',
												        'bwpm.delete' => 'Allowed (Inherited)',
												        'bwpm.send' => 'Not Allowed',
												        'bwpm.view.newsletter' => 'Not Allowed',
												        'bwpm.view.subscriber' => 'Allowed (Inherited)',
												        'bwpm.view.campaign' => 'Not Allowed',
												        'bwpm.view.mailinglist' => 'Not Allowed',
												        'bwpm.view.template' => 'Not Allowed',
												        'bwpm.view.archive' => 'Allowed (Inherited)',
												        'bwpm.admin.newsletter' => 'Not Allowed',
												        'bwpm.admin.subscriber' => 'Allowed (Inherited)',
												        'bwpm.admin.campaign' => 'Not Allowed',
												        'bwpm.admin.mailinglist' => 'Not Allowed',
												        'bwpm.admin.template' => 'Not Allowed',
												        'bwpm.view.maintenance' => 'Not Allowed',
											        ),
											        'BwPostmanSubscriberPublisher'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Not Allowed',
												        'bwpm.restore' => 'Not Allowed',
												        'bwpm.delete' => 'Not Allowed',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Allowed (Inherited)',
												        'bwpm.view.campaign' => 'Not Allowed (Locked)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Not Allowed (Locked)',
												        'bwpm.view.archive' => 'Not Allowed',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
											        'BwPostmanSubscriberEditor'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Not Allowed',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Not Allowed',
												        'bwpm.archive' => 'Not Allowed (Locked)',
												        'bwpm.restore' => 'Not Allowed (Locked)',
												        'bwpm.delete' => 'Not Allowed (Locked)',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Allowed (Inherited)',
												        'bwpm.view.campaign' => 'Not Allowed (Locked)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Not Allowed (Locked)',
												        'bwpm.view.archive' => 'Not Allowed (Locked)',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
											        'BwPostmanTemplateAdmin'
	                                                => array(
												        'core.admin' => 'Not Allowed',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Allowed (Inherited)',
												        'bwpm.restore' => 'Allowed (Inherited)',
												        'bwpm.delete' => 'Allowed (Inherited)',
												        'bwpm.send' => 'Not Allowed',
												        'bwpm.view.newsletter' => 'Not Allowed',
												        'bwpm.view.subscriber' => 'Not Allowed',
												        'bwpm.view.campaign' => 'Not Allowed',
												        'bwpm.view.mailinglist' => 'Not Allowed',
												        'bwpm.view.template' => 'Allowed (Inherited)',
												        'bwpm.view.archive' => 'Allowed (Inherited)',
												        'bwpm.admin.newsletter' => 'Not Allowed',
												        'bwpm.admin.subscriber' => 'Not Allowed',
												        'bwpm.admin.campaign' => 'Not Allowed',
												        'bwpm.admin.mailinglist' => 'Not Allowed',
												        'bwpm.admin.template' => 'Allowed (Inherited)',
												        'bwpm.view.maintenance' => 'Not Allowed',
											        ),
											        'BwPostmanTemplatePublisher'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Allowed (Inherited)',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Allowed (Inherited)',
												        'bwpm.archive' => 'Not Allowed',
												        'bwpm.restore' => 'Not Allowed',
												        'bwpm.delete' => 'Not Allowed',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.view.campaign' => 'Not Allowed (Locked)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Allowed (Inherited)',
												        'bwpm.view.archive' => 'Not Allowed',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
											        'BwPostmanTemplateEditor'
	                                                => array(
												        'core.admin' => 'Not Allowed (Locked)',
												        'core.login.admin' => 'Allowed (Inherited)',
												        'core.manage' => 'Allowed (Inherited)',
												        'bwpm.create' => 'Allowed (Inherited)',
												        'bwpm.edit' => 'Not Allowed',
												        'bwpm.edit.own' => 'Allowed (Inherited)',
												        'bwpm.edit.state' => 'Not Allowed',
												        'bwpm.archive' => 'Not Allowed (Locked)',
												        'bwpm.restore' => 'Not Allowed (Locked)',
												        'bwpm.delete' => 'Not Allowed (Locked)',
												        'bwpm.send' => 'Not Allowed (Locked)',
												        'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.view.campaign' => 'Not Allowed (Locked)',
												        'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.view.template' => 'Allowed (Inherited)',
												        'bwpm.view.archive' => 'Not Allowed (Locked)',
												        'bwpm.admin.newsletter' => 'Not Allowed (Locked)',
												        'bwpm.admin.subscriber' => 'Not Allowed (Locked)',
												        'bwpm.admin.campaign' => 'Not Allowed (Locked)',
												        'bwpm.admin.mailinglist' => 'Not Allowed (Locked)',
												        'bwpm.admin.template' => 'Not Allowed (Locked)',
												        'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											        ),
		);

	/*
	 * Messages
	 */

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $optionsSuccessMsg    = "Configuration successfully saved.";

	/**
	 * Test method to save defaults once of BwPostman
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
	public static function saveDefaults(\AcceptanceTester $I)
	{
		$I->wantTo("Save Default Options BwPostman");
		$I->expectTo("see success message and component in menu");
		$I->amOnPage(MainView::$url);

		$I->waitForElementVisible(Generals::$pageTitle, 3);
		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar4['Options'], 1);

		$I->setManifestOption('com_bwpostman', 'disclaimer', '0');
		$I->setManifestOption('com_bwpostman', 'use_captcha', '0');
		$I->setManifestOption('com_bwpostman', 'fe_layout', '');
		$I->setManifestOption('com_bwpostman', 'fe_layout_list', '');
		$I->setManifestOption('com_bwpostman', 'fe_layout_detail', '');

		$I->click(Generals::$toolbar['Save']);

		$I->waitForElementVisible(Generals::$alert_success4, 15);
		$I->see(self::$config_save_success, Generals::$alert_success4);
		$I->clickAndWait(Generals::$systemMessageClose, 1);

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		// Set guest user group
		$guestGroupId = $I->getGroupIdByName('Guest');
		$I->setManifestOption('com_users', 'guest_usergroup', $guestGroupId);
	}
}
