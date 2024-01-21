<?php
use Page\Generals as Generals;
use Page\Login;
use Page\RegistrationModulePage as Helper;
use Page\SubscriberviewPage as SubsView;
use Page\InstallationPage;

/**
 * Class SubscribeModuleCest
 *
 * This class contains all methods to test subscription by module
 *
 * !!!!Requirements: 3 mailinglists available in frontend at minimum!!!!
 *
 *  * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
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
class SubscribeModuleCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   Login     $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function _login(Login $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to ensure registration module is set up and activated
	 *
	 * @param   AcceptanceTester                $I
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
	public function setupRegistrationModule(AcceptanceTester $I)
	{
		$I->wantTo("Activate and setup registration module");
		$I->expectTo('get module active at frontend');

		// Open Module configuration page
		$I->amOnPage(InstallationPage::$siteModulesUrl);
		$I->fillField(Generals::$search_field, 'BwPostman');
		$I->clickAndWait(InstallationPage::$search_button, 1);

		$I->click(InstallationPage::$registrationModuleLine);
		$I->waitForElement(InstallationPage::$positionField, 5);

		// Fill module tab
		$I->click(InstallationPage::$registrationTabs['Module']);
		$I->waitForElement(InstallationPage::$publishedField);

		$I->clickAndWait(InstallationPage::$positionField, 1);
		$I->clickAndWait(InstallationPage::$positionValue, 1);

		$I->selectOption(InstallationPage::$publishedField, "Published");

		// Fill menu assignment tab
		$I->click(InstallationPage::$registrationTabs['Menu Assignment']);
		$I->waitForElement(InstallationPage::$menuAssignmentList);

		$I->selectOption(InstallationPage::$menuAssignmentList, "On all pages");

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElement(Generals::$alert_success4, 5);

		// Preset module options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to subscribe by module in front end with component options, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeModuleSimpleActivateAndUnsubscribeCO(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module using component options");
		$I->expectTo('get confirmation mail');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman', 'com_params', '1');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$this->subscribeByModule($I);

		$I->click(Helper::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->activate($I, SubsView::$mail_fill_1);

		$this->unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by module in front end with module options, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeModuleSimpleActivateAndUnsubscribeMO(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module using module options");
		$I->expectTo('get confirmation mail');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$this->subscribeByModule($I);

		$I->click(Helper::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->activate($I, SubsView::$mail_fill_1);

		$this->unsubscribe($I, SubsView::$activated_edit_Link);
		$I->setManifestOption('mod_bwpostman', 'com_params', '1');
	}

	/**
	 * Test method to subscribe by module in front end with module options and popup at module position,
	 * activate and unsubscribe, modal layout
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeModuleSimpleActivateAndUnsubscribePopupMO(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module using module options at popup");
		$I->expectTo('get confirmation mail');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:modal-default');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$this->subscribeByModule($I, 'small');

		$I->click(Helper::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->activate($I, SubsView::$mail_fill_1);

		$this->unsubscribe($I, SubsView::$activated_edit_Link);
		$I->setManifestOption('mod_bwpostman', 'com_params', '1');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:default');
	}

	/**
	 * Test method to subscribe by module in front end with module options and popup at module position,
	 * activate and unsubscribe, big modal layout
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeModuleSimpleActivateAndUnsubscribeBigPopupMO(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module using module options at popup");
		$I->expectTo('get confirmation mail');

		Helper::presetModuleOptions($I);
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:modal-cassiopeia');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$this->subscribeByModule($I, 'big');

		$I->click(Helper::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->activate($I, SubsView::$mail_fill_1);

		$this->unsubscribe($I, SubsView::$activated_edit_Link);
		$I->setManifestOption('mod_bwpostman', 'com_params', '1');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:default');
	}

	/**
	 * Test method to subscribe by module in front end with module options and popup at module position, check error popup,
	 * disclaimer popup and close button
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeModulePopupOverPopup(AcceptanceTester $I)
	{
		$I->wantTo("check registration popup over popup and close popup by button");
		$I->expectTo('see registration popup, error popup, disclaimer popup and close popup');

		Helper::presetModuleOptions($I);
		$options = $I->getManifestOptions('mod_bwpostman');
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:modal-default');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_module);

		$I->scrollTo(Helper::$module_position, 0, -100);
		$I->wait(1);
		$I->click(Helper::$module_button_module);
		$I->waitForElementVisible(Helper::$module_modal_content);

		// Check visibility of obligation marker
		$I->seeElement(Helper::$mod_firstname_star_popup);
		$I->seeElement(Helper::$mod_name_star_popup);
		$I->seeElement(Helper::$mod_special_star_popup);
		$I->seeElement(Helper::$mod_mailaddress_star_popup);
		$I->seeElement(Helper::$mod_ml_select_star);
		$I->seeElement(Helper::$mod_disclaimer_star);

		// Register without filled fields
		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(Helper::$errorModalCloseButton, 2);

		// Check error messages
		$I->see(SubsView::$popup_valid_mailaddress, Helper::$errorModalPopupBody);
		$I->see(Helper::$invalid_select_newsletter_mod_pop, Helper::$errorModalPopupBody);
		$I->see(Helper::$invalid_field_firstname_mod_pop, Helper::$errorModalPopupBody);
		$I->see(Helper::$invalid_field_name_mod_pop, Helper::$errorModalPopupBody);
		$I->see(sprintf(Helper::$invalid_field_special_mod_pop, $options->special_label), Helper::$errorModalPopupBody);
		$I->see(SubsView::$popup_accept_disclaimer, Helper::$errorModalPopupBody);

		$I->wait(1);
		$I->click(Helper::$errorModalCloseButton);
		$I->waitForElementNotVisible(Helper::$errorModalCloseButton, 5);

		// Set disclaimer to article
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '1');

		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link_modal);
		$I->wait(2);
		$I->switchToIframe('iFrame');
		$I->see(Helper::$mod_disclaimer_article_text);
		$I->switchToIframe();
		$I->click(Helper::$mod_disclaimer_modal_popup_close);

		// Close modal window of registration
		$I->click(Helper::$mod_register_close);
		$I->waitForElementNotVisible(Helper::$module_modal_content);
		$I->dontSeeElement(Helper::$module_modal_content);

		$I->setManifestOption('mod_bwpostman', 'com_params', '1');
		$I->setManifestOption('mod_bwpostman', 'layout', '_:default');
	}

	/**
	 * Test method to get edit page by click at module in front end
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function EditSubscriptionByModule(AcceptanceTester $I)
	{
		$I->wantTo("Edit subscription by module");
		$I->expectTo('see get edit link page');
		$I->amOnPage(SubsView::$register_url);

		$I->scrollTo(Helper::$mod_button_edit,0, -150);
		$I->wait(2);
		$I->click(Helper::$mod_button_edit);
		$I->waitForElement(SubsView::$mail, 3);
		$I->see(SubsView::$edit_get_text);
	}

	/**
	 * Test method to verify messages for missing input values by module
	 * Set 'show' fields to off but set obligation to on
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeMissingValuesModule(AcceptanceTester $I)
	{
		$I->wantTo("Test messages for missing input values by module");
		$I->expectTo('see error popup');

		Helper::presetModuleOptions($I);
		$options = $I->getManifestOptions('mod_bwpostman');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', '0');
		$I->setManifestOption('mod_bwpostman', 'show_name_field', '0');
		$I->setManifestOption('mod_bwpostman', 'show_special', '0');
		$I->setManifestOption('mod_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('mod_bwpostman', 'disclaimer', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);

		// Check visibility of obligation marker
		$I->seeElement(Helper::$mod_firstname_star);
		$I->seeElement(Helper::$mod_name_star);
		$I->seeElement(Helper::$mod_special_star);
		$I->seeElement(Helper::$mod_mailaddress_star);
		$I->seeElement(Helper::$mod_ml_select_star);
		$I->seeElement(Helper::$mod_disclaimer_star);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(SubsView::$errorContainerContentModal, 2);

		$I->see(SubsView::$popup_valid_mailaddress, Helper::$errorModulBody);
		$I->see(Helper::$invalid_select_newsletter_mod_pop, Helper::$errorModulBody);
		$I->see(Helper::$invalid_field_firstname_mod_pop, Helper::$errorModulBody);
		$I->see(Helper::$invalid_field_name_mod_pop, Helper::$errorModulBody);
		$I->see(sprintf(Helper::$invalid_field_special_mod_pop, $options->special_label), Helper::$errorModulBody);
		$I->see(SubsView::$popup_accept_disclaimer, Helper::$errorModulBody);

//		$I->wait(1);
//		$I->click(Helper::$errorModalFooterButton);
//		$I->waitForElementNotVisible(Helper::$errorModalFooterButton, 5);

		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check visibility of input fields by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeShowFieldsModule(AcceptanceTester $I)
	{
		$I->wantTo("Test visibility of input fields by module");
		$I->expectTo('not to see some fields');

		Helper::presetModuleOptions($I);
		$options = $I->getManifestOptions('mod_bwpostman');

		// Set visibility of fields to off
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'show_gender', '0');
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', '0');
		$I->setManifestOption('mod_bwpostman', 'show_name_field', '0');
		$I->setManifestOption('mod_bwpostman', 'show_special', '0');
		$I->setManifestOption('mod_bwpostman', 'show_emailformat', '0');
		$I->setManifestOption('mod_bwpostman', 'disclaimer', '0');
		$I->setManifestOption('mod_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('mod_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('mod_bwpostman', 'special_field_obligation', '0');

		// Call page with new options
		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);

		// Check visibility of fields switched to off
		$I->dontSeeElement(Helper::$mod_gender_select_id);
		$I->dontSeeElement(Helper::$mod_firstname);
		$I->dontSeeElement(Helper::$mod_name);
		$I->dontSeeElement(Helper::$mod_special);
		$I->dontSeeElement(Helper::$mod_format_html);
		$I->dontSeeElement(Helper::$mod_format_text);
		$I->dontSeeElement(Helper::$mod_disclaimer);

		// Check visibility of obligation marker
		$I->dontSeeElement(Helper::$mod_firstname_star);
		$I->dontSeeElement(Helper::$mod_name_star);
		$I->dontSeeElement(Helper::$mod_special_star);
		$I->seeElement(Helper::$mod_mailaddress_star);
		$I->seeElement(Helper::$mod_ml_select_star);
		$I->dontSeeElement(Helper::$mod_disclaimer_star);

		// Set visibility of fields to on
		$I->expectTo('not to see some fields');

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'show_gender', '1');
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('mod_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('mod_bwpostman', 'show_special', '1');
		$I->setManifestOption('mod_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('mod_bwpostman', 'disclaimer', '1');

		// Call page with new options
		$I->reloadPage();
		$I->waitForElementVisible(SubsView::$view_register, 3);

		// Check visibility of fields switched to on
		$I->seeElement(Helper::$mod_gender_select_id);
		$I->seeElement(Helper::$mod_firstname);
		$I->seeElement(Helper::$mod_name);
		$I->seeElement(Helper::$mod_special);
		$I->seeElement(Helper::$mod_format_html);
		$I->seeElement(Helper::$mod_format_text);
		$I->seeElement(Helper::$mod_disclaimer);

		// Check label of field special
		$I->seeElement(sprintf(Helper::$mod_special_placeholder, $options->special_label));

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check mailing list description visibility and length by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckMailinglistDescriptionModule(AcceptanceTester $I)
	{
		$I->wantTo("Test visibility and length of input mailinglist description by module");
		$I->expectTo('to see shortened mailinglist description');

		Helper::presetModuleOptions($I);

		// Set usage of module parameters
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		// Call page with description length 50
		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_ml_desc_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_ml_desc_identifier);
		$I->see(Helper::$mod_ml_desc_long, Helper::$mod_ml_desc_identifier);

		// Set description length to 18
		$I->setManifestOption('mod_bwpostman', 'desc_length', '18');

		// Call page with description length 18
		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_ml_desc_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_ml_desc_identifier);
		$I->see(Helper::$mod_ml_desc_short, Helper::$mod_ml_desc_identifier);

		// Set show description to off
		$I->setManifestOption('mod_bwpostman', 'show_desc', '0');

		// Call page with description off
		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->dontSeeElement(Helper::$mod_ml_desc_identifier);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check intro text by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckIntroTextModule(AcceptanceTester $I)
	{
		$I->wantTo("Test intro text by module");
		$I->expectTo('to see appropriate intro text');

		Helper::presetModuleOptions($I);

		// Set intro text of component
		$I->setManifestOption('com_bwpostman', 'pretext', Helper::$mod_intro_text_comp);

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_intro_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_intro_identifier);
		$I->see(Helper::$mod_intro_text_comp, Helper::$mod_intro_identifier);

		// Set intro text of module
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('mod_bwpostman', 'pretext', Helper::$mod_intro_text_mod);

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_intro_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_intro_identifier);
		$I->see(Helper::$mod_intro_text_mod, Helper::$mod_intro_identifier);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check sources at modal window by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentPopupModule(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by module at modal popup");
		$I->expectTo('to see appropriate disclaimer text at modal popup');

		Helper::presetModuleOptions($I);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		// Set disclaimer to link
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '0');

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link_modal);
		$I->wait(15);
		$I->waitForElementVisible(Helper::$mod_disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(Helper::$mod_disclaimer_url_text);
		$I->switchToIframe();
		$I->click(Helper::$mod_disclaimer_modal_close);

		// Set disclaimer to article
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '1');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link_modal);
		$I->wait(2);
		$I->waitForElementVisible(Helper::$mod_disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(Helper::$mod_disclaimer_article_text);
		$I->switchToIframe();
		$I->click(Helper::$mod_disclaimer_modal_close);

		// Set disclaimer to menu item
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '2');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link_modal);
		$I->wait(2);
		$I->waitForElementVisible(Helper::$mod_disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(Helper::$mod_disclaimer_menuitem_text);
		$I->switchToIframe();
		$I->click(Helper::$mod_disclaimer_modal_close);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check sources at new window by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentNewWindowModule(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by module at new window");
		$I->expectTo('to see appropriate disclaimer text at new window');

		Helper::presetModuleOptions($I);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		// Set disclaimer to link
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('mod_bwpostman', 'showinmodal', '0');
		$I->setManifestOption('mod_bwpostman', 'disclaimer_target', '0');

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
		$I->switchToNextTab();
        $I->wait(15);
		$I->see(Helper::$mod_disclaimer_url_text);
		$I->closeTab();

		// Set disclaimer to article
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '1');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
		$I->switchToNextTab();
        $I->wait(3);
		$I->see(Helper::$mod_disclaimer_article_text);
		$I->closeTab();

		// Set disclaimer to menu item
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '2');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
		$I->switchToNextTab();
        $I->wait(3);
		$I->see(Helper::$mod_disclaimer_menuitem_text);
		$I->closeTab();

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check sources at same window by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentSameWindowModule(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by module at new window");
		$I->expectTo('to see appropriate disclaimer text at new window');

		Helper::presetModuleOptions($I);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		// Set disclaimer to link
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('mod_bwpostman', 'showinmodal', '0');
		$I->setManifestOption('mod_bwpostman', 'disclaimer_target', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
        $I->wait(15);
		$I->see(Helper::$mod_disclaimer_url_text);

		// Set disclaimer to article
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
		$I->see(Helper::$mod_disclaimer_article_text);

		// Set disclaimer to menu item
		$I->setManifestOption('mod_bwpostman', 'disclaimer_selection', '2');

		$I->amOnPage(SubsView::$register_url);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(Helper::$mod_disclaimer);
		$I->click(Helper::$mod_disclaimer_link);
		$I->see(Helper::$mod_disclaimer_menuitem_text);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check security question by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckSecurityQuestionModule(AcceptanceTester $I)
	{
		$I->wantTo("Test security question by module");
		$I->expectTo('to see error message on wrong answer');

		Helper::presetModuleOptions($I);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		// Set use security captcha
		$I->setManifestOption('mod_bwpostman', 'use_captcha', '2');

		// Check visibility of security captcha
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_button_register, 0, -200);
		$I->wait(1);
		$I->seeElement(SubsView::$math_captcha_mod);

		// Set use security question
		$I->setManifestOption('mod_bwpostman', 'use_captcha', '1');

		$I->amOnPage(SubsView::$register_url);
		$this->subscribeByModule($I);

		$I->click(Helper::$mod_button_register);

		$I->waitForElementVisible(sprintf(Helper::$errorModulBody, 1), 2);
		$I->see(Helper::$mod_security_question_error, sprintf(Helper::$errorModulBody, 1));

		$this->subscribeByModule($I);
		$I->fillField(Helper::$mod_question, '4');
		$I->seeElement(Helper::$mod_security_star);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->activate($I, SubsView::$mail_fill_1);
		$this->unsubscribe($I, SubsView::$activated_edit_Link);

		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to check number of selectable mailing lists by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckSelectableMailinglistsModule(AcceptanceTester $I)
	{
		$I->wantTo("Test the number of selectable mailing lists by module");
		$I->expectTo('to see correct numbers of mailing lists');

		Helper::presetModuleOptions($I);

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(sprintf(Helper::$mod_mailinglist_number, '3'));

		$I->setManifestOption('mod_bwpostman', 'mod_ml_available', array(''));
		$I->reloadPage();
		$I->scrollTo(Helper::$mod_disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(sprintf(Helper::$mod_mailinglist_number, '9'));

		$I->reloadPage();
		// Reset options
		Helper::presetModuleOptions($I);
	}

	/**
	 * Test method to subscribe by module in front end with links at text fields
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeAbuseFieldsModule(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module with links at text fields");
		$I->expectTo('see error messages');

		// Store current field options
		$options       = $I->getManifestOptions('mod_bwpostman');
		$showName      = $options->show_name_field;
		$showFirstName = $options->show_firstname_field;
		$showSpecial   = $options->show_special;
		$specialLabel  = $options->special_label;

		$I->setManifestOption('mod_bwpostman', 'com_params', '0');

		// Set needed field options
		$I->setManifestOption('mod_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('mod_bwpostman', 'show_special', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->wait(1);
		$I->seeElement(Helper::$mod_button_register);

		// Fill needed fields
		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_1);

		$I->scrollTo(Helper::$mod_format_text, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Helper::$mod_format_text, 1);
		$I->checkOption(Helper::$mod_ml1);
		$I->checkOption(Helper::$mod_disclaimer);

		// Fill first name with link
		$I->expectTo('see error message invalid first name');
		$I->fillField(Helper::$mod_firstname, SubsView::$abuseLink);
		$I->fillField(Helper::$mod_name, SubsView::$lastname_fill);
		$I->fillField(Helper::$mod_special, SubsView::$special_fill);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(sprintf(Helper::$errorModulBodyAlert, 1), 2);


		// Check error message first name
		$I->see(SubsView::$errorAbuseFirstName, sprintf(Helper::$errorModulBodyAlert, 1));

		// Fill needed fields
		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_1);
		$I->clickAndWait(Helper::$mod_format_text, 1);
		$I->scrollTo(Helper::$mod_format_text, 0, -100);
		$I->wait(1);
		$I->checkOption(Helper::$mod_ml1);
		$I->checkOption(Helper::$mod_disclaimer);

		// Fill last name with link
		$I->expectTo('see error message invalid name');
		$I->fillField(Helper::$mod_firstname, SubsView::$firstname_fill);
		$I->fillField(Helper::$mod_name, SubsView::$abuseLink);
		$I->fillField(Helper::$mod_special, SubsView::$special_fill);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(sprintf(Helper::$errorModulBodyAlert, 1), 2);

		// Check error message last name
		$I->see(SubsView::$errorAbuseLastName, sprintf(Helper::$errorModulBodyAlert, 1));

		// Fill needed fields
		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_1);
		$I->clickAndWait(Helper::$mod_format_text, 1);
		$I->scrollTo(Helper::$mod_format_text, 0, -100);
		$I->wait(1);
		$I->checkOption(Helper::$mod_ml1);
		$I->checkOption(Helper::$mod_disclaimer);

		// Fill special with link
		$I->expectTo('see error message invalid special');
		$I->fillField(Helper::$mod_firstname, SubsView::$firstname_fill);
		$I->fillField(Helper::$mod_name, SubsView::$lastname_fill);
		$I->fillField(Helper::$mod_special, SubsView::$abuseLink);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(sprintf(Helper::$errorModulBodyAlert, 1), 2);

		// Check error message special
		if ($options->special_label === '')
		{
			$options->special_label = 'Additional Field';
		}

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseSpecial, $options->special_label), sprintf(Helper::$errorModulBodyAlert, 1));

		// Reset field options
		$I->setManifestOption('mod_bwpostman', 'show_name_field', $showName);
		$I->setManifestOption('mod_bwpostman', 'show_firstname_field', $showFirstName);
		$I->setManifestOption('mod_bwpostman', 'show_special', $showSpecial);
		$I->setManifestOption('mod_bwpostman', 'special_label', $specialLabel);
	}

	/**
	 * Test method to subscribe by module in front end with unreachable domain or mailbox
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeUnreachableMailAddressModule(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module with unreachable email address");
		$I->expectTo('see error message');

		// Store current field options
		$options = $I->getManifestOptions('com_bwpostman');
		$verify  = $options->verify_mailaddress;

		// Set verification of mail address
		$I->setManifestOption('mod_bwpostman', 'com_params', '0');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 1);

		// Fill form
		$this->subscribeByModule($I);

		// Set unreachable domain
		$I->expectTo('see error message invalid email address (domain)');
		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_unreachable_domain);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(sprintf(Helper::$errorModulBodyAlert, 1), 2);

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseEmail, $options->special_label), sprintf(Helper::$errorModulBodyAlert, 1));

		// Set unreachable mailbox
		$I->expectTo('see error message invalid email address (mailbox)');
		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_unreachable_mailbox);

		$I->scrollTo(Helper::$mod_button_register, 0, -100);
		$I->wait(1);
		$I->click(Helper::$mod_button_register);
		$I->waitForElementVisible(sprintf(Helper::$errorModulBodyAlert, 1), 2);

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseEmail, $options->special_label), sprintf(Helper::$errorModulBodyAlert, 1));

		// Reset field options
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', $verify);
	}

	/**
	 * Test method to subscribe to newsletter in front end by module
	 *
	 * @param AcceptanceTester $I
	 * @param boolean          $modal
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function subscribeByModule(AcceptanceTester $I, $modal = 'none')
	{
		$options  = $I->getManifestOptions('mod_bwpostman');
		$itemText = '';

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_module);

		if ($modal !== 'none')
		{
			$I->scrollTo(Helper::$module_position, 0, -100);
			$I->wait(1);
			$I->click(Helper::$module_button_module);
			$I->waitForElementVisible(Helper::$module_modal_content, 3);
			$I->waitForElementVisible(Helper::$module_item_identifier, 3);

			if ($modal ==='small')
			{
				$grabField = Helper::$module_item_text_identifier . "/label/span";
				$itemText = $I->grabTextFrom($grabField);
			}
			elseif ($modal === 'big')
			{
				$grabField = Helper::$module_item_text_identifier . "/label/span";
				$itemText = $I->grabTextFrom($grabField);
			}

			$I->assertEquals("01 Mailingliste 5 weiterer Lauf A", $itemText);
		}

		if ($options->show_gender)
		{
			$I->scrollTo(Helper::$mod_gender_select_id, 0, -100);
			$I->wait(1);
			$I->selectOption(Helper::$mod_gender_select_id, '1');
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(Helper::$mod_firstname, SubsView::$firstname_fill);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(Helper::$mod_name, SubsView::$lastname_fill);
		}

		$I->fillField(Helper::$mod_mail, SubsView::$mail_fill_1);

		$I->scrollTo(Helper::$mod_format_text, 0, -100);
		$I->wait(1);
		if ($options->show_emailformat)
		{
			$I->clickAndWait(Helper::$mod_format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(Helper::$mod_special, SubsView::$special_fill);
		}

		$I->checkOption(Helper::$mod_ml2);

		$I->scrollTo(Helper::$mod_button_register, 0, -450);
		$I->wait(2);

		if ($options->disclaimer)
		{
			$I->checkOption(Helper::$mod_disclaimer);
		}

		$I->scrollTo(Helper::$mod_button_register, 0, -200);
		$I->wait(1);
	}

	/**
	 * Test method to activate newsletter subscription
	 *
	 * @param AcceptanceTester $I
	 * @param string           $mailaddress
	 * @param bool             $good
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function activate(AcceptanceTester $I, $mailaddress, $good = true)
	{
		$activation_code = $I->getActivationCode($mailaddress);
		$I->amOnPage(SubsView::$activation_link . $activation_code);
		if ($good)
		{
			$I->see(SubsView::$activation_completed_text, SubsView::$activation_complete);
		}
	}

	/**
	 * Test method to unsubscribe from all newsletters
	 *
	 * @param AcceptanceTester $I
	 * @param string           $button
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function unsubscribe(AcceptanceTester $I, $button)
	{
		$I->click($button);
		$I->waitForElement(SubsView::$view_edit, 5);
		$I->seeElement(SubsView::$view_edit);

		$I->scrollTo(SubsView::$button_unsubscribe, 0, -100);
		$I->wait(1);
		$I->checkOption(SubsView::$button_unsubscribe);
		$I->click(SubsView::$button_submitleave);
		$I->dontSee(SubsView::$mail_fill_1, SubsView::$mail);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester $I
	 * @param Login              $loginPage
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function _logout(AcceptanceTester $I, Login $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}
}
