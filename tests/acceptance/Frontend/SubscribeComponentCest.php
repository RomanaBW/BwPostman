<?php
use Page\Generals as Generals;
use Page\SubscriberviewPage as SubsView;

/**
 * Class SubscribeComponentCest
 *
 * This class contains all methods to test subscription at front end
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
class SubscribeComponentCest
{
	/**
	 * Test method to subscribe as guest by component in front end, activate and unsubscribe
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeSimpleActivateAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');

		Generals::presetComponentOptions($I);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElementVisible(SubsView::$registration_complete, 30);
		$I->wait(1);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe as logged-in user by component in front end, activate and unsubscribe
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.1.3
	 */
	public function SubscribeSimpleActivateAndUnsubscribeLoggedIn(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe as logged-in user to mailinglist by component");
		$I->expectTo('get confirmation mail');

		Generals::presetComponentOptions($I);

		SubsView::loginToFrontend($I);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::logoutFromFrontend($I);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end twice, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeTwiceActivateAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component a second time");
		$I->expectTo('see error message');

		Generals::presetComponentOptions($I);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElementVisible(SubsView::$registration_complete, 30);
		$I->wait(1);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);
		$I->wait(5);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElement(SubsView::$err_activation_incomplete, 30);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_activation_incomplete);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end twice, get activation code anew, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeTwiceActivateGetActivationAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');

		Generals::presetComponentOptions($I);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->waitForElement(SubsView::$err_activation_incomplete, 5);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_activation_incomplete);

		$I->scrollTo(SubsView::$button_send_activation);
		$I->wait(1);
		$I->click(SubsView::$button_send_activation);
		$I->waitForElement(SubsView::$success_message, 30);
		$I->see(SubsView::$activation_sent_text, SubsView::$success_message);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end, activate, subscribe a second time, get edit link and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeActivateSubscribeGetEditlinkAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist and unsubscribe by edit link");
		$I->expectTo('unsubscribe with edit link');

		Generals::presetComponentOptions($I);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->scrollTo(SubsView::$registration_complete);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$registration_complete, 30);
		$I->wait(1);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->scrollTo(SubsView::$err_already_subscribed);
		$I->wait(1);
		$I->waitForElement(SubsView::$err_already_subscribed, 30);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_already_subscribed);

		$editlink_code  = $this->gotoEdit($I);
		$I->amOnPage(SubsView::$editlink . $editlink_code);

		$I->waitForElementVisible(SubsView::$view_edit, 2);
		$I->scrollTo(SubsView::$button_unsubscribe);
		$I->wait(2);
		$I->checkOption(SubsView::$button_unsubscribe);
		$I->click(SubsView::$button_submitleave);
		$I->dontSee(SubsView::$mail_fill_1, SubsView::$mail);
	}

	/**
	 * Test method to verify messages for missing input values by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeMissingValuesComponent(AcceptanceTester $I)
	{
		Generals::presetComponentOptions($I);

		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '0');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '0');
		$I->setManifestOption('com_bwpostman', 'show_special', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '1');
		$options = $I->getManifestOptions('com_bwpostman');

		$I->wantTo("Test messages for missing input values by component");
		$I->expectTo('see error messages');
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);

		// Check visibility of obligation marker
		$I->seeElement(SubsView::$firstname_star);
		$I->seeElement(SubsView::$name_star);
		$I->seeElement(SubsView::$special_star);
		$I->seeElement(SubsView::$mailaddress_star);
		$I->seeElement(SubsView::$ml_select_star);
		$I->seeElement(SubsView::$disclaimer_star);

		$I->scrollTo(SubsView::$button_register, 0, -100);
		$I->wait(1);
		$I->click(SubsView::$button_register);
		$I->waitForElementVisible(Generals::$alert_error, 2);
		$I->scrollTo(Generals::$alert_error, 0, -100);
		$I->wait(1);

		$I->see(SubsView::$invalid_field_mailaddress);
		$I->see(SubsView::$invalid_field_firstname);
		$I->see(SubsView::$invalid_field_name);
		$I->see(SubsView::$invalid_select_newsletter_132);
		$I->see(sprintf(SubsView::$popup_enter_special, $options->special_label));
		$I->see(SubsView::$popup_accept_disclaimer);

		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to subscribe by component in front end, activate, make changes and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeSimpleActivateChangeAndUnsubscribe(AcceptanceTester $I)
	{
		Generals::presetComponentOptions($I);

		$options    = $I->getManifestOptions('com_bwpostman');

		// Subscribe
		$I->wantTo("Subscribe to mailinglist by component, change values and unsubscribe");
		$I->expectTo('get confirmation mail');

		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 0);

		SubsView::subscribeByComponent($I);
		$I->click(SubsView::$button_register);

		$I->scrollTo(SubsView::$registration_complete);
		$I->wait(2);
		$I->waitForElement(SubsView::$registration_complete, 5);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		// Activate
		SubsView::activate($I, SubsView::$mail_fill_1);

		// Edit
		$I->scrollTo(SubsView::$button_edit, 0, -150);
		$I->wait(1);
		$I->click(SubsView::$button_edit);

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubsView::$firstname, "Charles");
			$I->scrollTo(SubsView::$button_submit);
			$I->wait(1);
			$I->click(SubsView::$button_submit);
			$I->waitForElementVisible(Generals::$alert_heading, 5);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$firstname, SubsView::$firstname_fill);
			$I->seeInField(SubsView::$firstname, 'Charles');
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubsView::$name, "Crackerbarrel");
			$I->scrollTo(SubsView::$button_submit);
			$I->wait(1);
			$I->click(SubsView::$button_submit);
			$I->waitForElementVisible(Generals::$alert_heading, 5);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$name, SubsView::$lastname_fill);
			$I->seeInField(SubsView::$name, 'Crackerbarrel');
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubsView::$special, "4711");
			$I->scrollTo(SubsView::$button_submit);
			$I->wait(1);
			$I->click(SubsView::$button_submit);
			$I->waitForElementVisible(Generals::$alert_heading, 5);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$special, SubsView::$special_fill);
			$I->seeInField(SubsView::$special, '4711');
		}

		$I->scrollTo(SubsView::$ml2);
		$I->wait(1);
		$I->checkOption(SubsView::$ml2);
		$I->scrollTo(SubsView::$button_submit);
		$I->wait(1);
		$I->click(SubsView::$button_submit);
		$I->waitForElementVisible(Generals::$alert_heading, 5);
		$I->wait(1);
		$I->see(SubsView::$msg_saved_successfully);
		$I->waitForElement(SubsView::$view_edit, 5);
		$I-> seeCheckboxIsChecked(SubsView::$ml2);

		$I->scrollTo(SubsView::$ml1);
		$I->wait(1);
		$I->uncheckOption(SubsView::$ml1);
		$I->scrollTo(SubsView::$button_submit);
		$I->wait(1);
		$I->click(SubsView::$button_submit);
		$I->scrollTo(SubsView::$ml1);
		$I->wait(1);
		$I-> dontSeeCheckboxIsChecked(SubsView::$ml1);

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_2);
		$I->scrollTo(SubsView::$button_submit);
		$I->wait(1);
		$I->click(SubsView::$button_submit);
		$I->waitForElement(Generals::$alert_info, 5);
		$I->wait(1);
		$I->see(SubsView::$msg_changed_mailaddress);

		SubsView::activate($I, SubsView::$mail_fill_2);
		$I->scrollTo(SubsView::$button_edit, 0, -100);
		$I->wait(1);
		$I->click(SubsView::$button_edit);

		SubsView::unsubscribe($I, SubsView::$button_unsubscribe);
	}

	/**
	 * Test method to get error message while activating a non existing subscription
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function SubscribeActivateUnsubscribeAndActivate(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');

		Generals::presetComponentOptions($I);

		SubsView::subscribeByComponent($I);
		$I->clickAndWait(SubsView::$button_register, 2);

		$I->scrollTo(SubsView::$registration_complete);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$registration_complete, 30);
		$I->wait(1);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);

		SubsView::activate($I, SubsView::$mail_fill_1, false);
		$I->waitForElement(SubsView::$err_not_activated, 30);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_invalid_link);
	}

	/**
	 * Test method to get error message for wrong email address to edit
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function GetEditlinkWrongAddress(AcceptanceTester $I)
	{
		$I->wantTo('Get edit link');
		$I->expectTo('see message wrong mail address');
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$register_edit_url, 5);
		$I->click(SubsView::$register_edit_url);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_2);
		$I->scrollTo(SubsView::$send_edit_link, 0, -100);
		$I->wait(1);
		$I->click(SubsView::$send_edit_link);

		$I->scrollTo(SubsView::$err_get_editlink, 0, -100);
		$I->wait(1);
		$I->waitForElement(SubsView::$err_get_editlink, 5);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_no_subscription);
	}

	/**
	 * Test method to get error message for wrong unsubscribe links
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function WrongUnsubscribeLinks(AcceptanceTester $I)
	{
		$I->wantTo('Unsubscribe with faulty edit link');
		$I->expectTo('see message wrong edit link');

		$I->amOnPage(SubsView::$unsubscribe_link_faulty);

		$I->waitForElement(SubsView::$err_get_editlink, 30);
		$I->wait(2);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_wrong_editlink);

		$I->amOnPage(SubsView::$unsubscribe_link_empty);
		$I->waitForElement(SubsView::$err_get_editlink, 30);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_wrong_editlink);

		$I->amOnPage(SubsView::$unsubscribe_link_missing);
		$I->waitForElement(SubsView::$mail, 30);
		$I->see(SubsView::$edit_get_text);
	}

	/**
	 * Test method to subscribe by component in front end with links at text fields
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function SubscribeAbuseFields(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component with links at text fields");
		$I->expectTo('see error messages');

		// Store current field options
		Generals::presetComponentOptions($I);

		$options       = $I->getManifestOptions('com_bwpostman');
		$showName      = $options->show_name_field;
		$showFirstName = $options->show_firstname_field;
		$showSpecial   = $options->show_special;
		$specialLabel  = $options->special_label;

		// Set needed field options
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->wait(1);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);

		// Fill needed fields
		$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubsView::$format_text, 1);
		}

		$I->scrollTo(SubsView::$ml1);
		$I->wait(1);
		$I->checkOption(SubsView::$ml1);

		if ($options->disclaimer)
		{
			$I->checkOption(SubsView::$disclaimer);
		}

		// Fill first name with link
		$I->expectTo('see error message invalid first name');
		$I->fillField(SubsView::$firstname, SubsView::$abuseLink);
		$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		$I->fillField(SubsView::$special, SubsView::$special_fill);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->clickAndWait(SubsView::$button_register, 1);

		// Check error message first name
		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(SubsView::$errorAbuseFirstName, SubsView::$errorContainerContent);

		// Fill last name with link
		$I->expectTo('see error message invalid name');
		$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		$I->fillField(SubsView::$name, SubsView::$abuseLink);
		$I->fillField(SubsView::$special, SubsView::$special_fill);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->clickAndWait(SubsView::$button_register, 1);

		// Check error message last name
		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(SubsView::$errorAbuseLastName, SubsView::$errorContainerContent);

		// Fill special with link
		$I->expectTo('see error message invalid special');
		$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		$I->fillField(SubsView::$special, SubsView::$abuseLink);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->clickAndWait(SubsView::$button_register, 1);

		// Check error message special
		if ($options->special_label === '')
		{
			$options->special_label = 'Additional Field';
		}

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseSpecial, $options->special_label), SubsView::$errorContainerContent);

		// Reset field options
		$I->setManifestOption('com_bwpostman', 'show_name_field', $showName);
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', $showFirstName);
		$I->setManifestOption('com_bwpostman', 'show_special', $showSpecial);
		$I->setManifestOption('com_bwpostman', 'special_label', $specialLabel);
	}

	/**
	 * Test method to subscribe by component in front end with unreachable domain or mailbox
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function SubscribeUnreachableMailAddress(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component with unreachable email address");
		$I->expectTo('see error message');

		// Store current field options
		Generals::presetComponentOptions($I);

		$options = $I->getManifestOptions('com_bwpostman');
		$verify  = $options->verify_mailaddress;

		// Set verification of mail address
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', 1);

		// Fill form
		SubsView::subscribeByComponent($I);

		// Set unreachable domain
		$I->expectTo('see error message invalid email address (domain)');
		$I->fillField(SubsView::$mail, SubsView::$mail_fill_unreachable_domain);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->click(SubsView::$button_register);
		$I->waitForElementVisible(SubsView::$errorContainerHeader, 3);

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseEmail, $options->special_label), SubsView::$errorContainerContent);

		// Set unreachable mailbox
		$I->expectTo('see error message invalid email address (mailbox)');
		$I->fillField(SubsView::$mail, SubsView::$mail_fill_unreachable_mailbox);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->click(SubsView::$button_register);
		$I->waitForElementVisible(SubsView::$errorContainerHeader, 3);

		$I->see('danger', SubsView::$errorContainerHeader);
		$I->see(sprintf(SubsView::$errorAbuseEmail, $options->special_label), SubsView::$errorContainerContent);

		// Reset field options
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', $verify);
	}

	/**
	 * Test method to check visibility of input fields by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function SubscribeShowFieldsComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test visibility of input fields by component");
		$I->expectTo('not to see some fields');

		Generals::presetComponentOptions($I);
		$options = $I->getManifestOptions('com_bwpostman');

		// Set visibility of fields to off
		$I->setManifestOption('com_bwpostman', 'show_gender', '0');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '0');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '0');
		$I->setManifestOption('com_bwpostman', 'show_special', '0');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '0');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'use_captcha', '1');

		// Call page with new options
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);

		// Check visibility of fields switched to off
		$I->dontSeeElement(SubsView::$gender);
		$I->dontSeeElement(SubsView::$firstname);
		$I->dontSeeElement(SubsView::$name);
		$I->dontSeeElement(SubsView::$special);
		$I->dontSeeElement(SubsView::$format_html);
		$I->dontSeeElement(SubsView::$format_text);
		$I->dontSeeElement(SubsView::$disclaimer);

		// Check visibility of question field
		$I->seeElement(SubsView::$question);

		// Check visibility of obligation marker
		$I->dontSeeElement(SubsView::$firstname_star);
		$I->dontSeeElement(SubsView::$name_star);
		$I->dontSeeElement(SubsView::$special_star);
		$I->seeElement(SubsView::$mailaddress_star);
		$I->seeElement(SubsView::$ml_select_star);
		$I->dontSeeElement(SubsView::$disclaimer_star);

		// Set visibility of fields to on
		$I->expectTo('not to see some fields');

		$I->setManifestOption('com_bwpostman', 'show_gender', '1');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '1');
		$I->setManifestOption('com_bwpostman', 'use_captcha', '2');

		// Call page with new options
		$I->reloadPage();
		$I->waitForElementVisible(SubsView::$view_register, 3);

		// Check visibility of fields switched to on
		$I->seeElement(SubsView::$gender);
		$I->seeElement(SubsView::$firstname);
		$I->seeElement(SubsView::$name);
		$I->seeElement(SubsView::$special);
		$I->seeElement(SubsView::$format_html);
		$I->seeElement(SubsView::$format_text);
		$I->seeElement(SubsView::$disclaimer);
		$I->seeElement(SubsView::$math_captcha);

		// Check label of field special
		$I->seeElement(sprintf(SubsView::$special_placeholder, $options->special_label));

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check mailing list description visibility and length by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckMailinglistDescriptionComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test visibility and length of input mailinglist description by component");
		$I->expectTo('to see shortened mailinglist description');

		Generals::presetComponentOptions($I);
		$I->setManifestOption('com_bwpostman', 'show_desc', '1');

		// Call page with description length 50
		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$ml_desc_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$ml_desc_identifier);
		$I->see(SubsView::$ml_desc_long, SubsView::$ml_desc_identifier);

		// Set description length to 18
		$I->setManifestOption('com_bwpostman', 'desc_length', '18');

		// Call page with description length 18
		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$ml_desc_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$ml_desc_identifier);
		$I->see(SubsView::$ml_desc_short, SubsView::$ml_desc_identifier);

		// Set show description to off
		$I->setManifestOption('com_bwpostman', 'show_desc', '0');

		// Call page with description off
		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->dontSeeElement(SubsView::$ml_desc_identifier);

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check intro text by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckIntroTextComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test intro text by component");
		$I->expectTo('to see appropriate intro text');

		Generals::presetComponentOptions($I);

		// Set intro text of component
		$I->setManifestOption('com_bwpostman', 'pretext', SubsView::$intro_text_comp);

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$intro_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$intro_identifier);
		$I->see(SubsView::$intro_text_comp, SubsView::$intro_identifier);

		// Set intro text of component
		$I->setManifestOption('com_bwpostman', 'pretext', SubsView::$intro_text_comp);

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$intro_identifier, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$intro_identifier);
		$I->see(SubsView::$intro_text_comp, SubsView::$intro_identifier);

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check sources at modal window by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentPopupComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by component at modal popup");
		$I->expectTo('to see appropriate disclaimer text at modal popup');

		Generals::presetComponentOptions($I);

		// Set disclaimer to link
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer_link', 'https://www.jahamo-training.de/index.php?option=com_content&view=article&id=15&Itemid=582');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link_modal);
		$I->wait(15);
		$I->waitForElementVisible(SubsView::$disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(SubsView::$disclaimer_url_text);
		$I->switchToIframe();
		$I->click(SubsView::$disclaimer_modal_close);

		// Set disclaimer to article
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '1');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link_modal);
		$I->wait(15);
		$I->waitForElementVisible(SubsView::$disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(SubsView::$disclaimer_article_text);
		$I->switchToIframe();
		$I->click(SubsView::$disclaimer_modal_close);

		// Set disclaimer to menu item
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '2');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link_modal);
		$I->wait(15);
		$I->waitForElementVisible(SubsView::$disclaimer_modal_identifier, 5);
		$I->switchToIframe('BwpFrame');
		$I->see(SubsView::$disclaimer_menuitem_text);
		$I->switchToIframe();
		$I->click(SubsView::$disclaimer_modal_close);

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check sources at new window by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentNewWindowComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by component at new window");
		$I->expectTo('to see appropriate disclaimer text at new window');

		Generals::presetComponentOptions($I);

		// Set disclaimer to link
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('com_bwpostman', 'showinmodal', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer_target', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer_link', 'https://www.jahamo-training.de/index.php?option=com_content&view=article&id=15&Itemid=582');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->switchToNextTab();
        $I->wait(15);
		$I->see(SubsView::$disclaimer_url_text);
		$I->closeTab();

		// Set disclaimer to article
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '1');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->switchToNextTab();
        $I->wait(5);
		$I->see(SubsView::$disclaimer_article_text);
		$I->closeTab();

		// Set disclaimer to menu item
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '2');

		$I->reloadPage();
		$I->seeElement(SubsView::$view_register);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->switchToNextTab();
        $I->wait(5);
		$I->see(SubsView::$disclaimer_menuitem_text);
		$I->closeTab();

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check sources at same window by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckDisclaimerContentSameWindowComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test disclaimer text by component at new window");
		$I->expectTo('to see appropriate disclaimer text at new window');

		Generals::presetComponentOptions($I);

		// Set disclaimer to link
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('com_bwpostman', 'showinmodal', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer_target', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '1');
		$I->setManifestOption('com_bwpostman', 'disclaimer_link', 'https://www.jahamo-training.de/index.php?option=com_content&view=article&id=15&Itemid=582');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->see(SubsView::$disclaimer_url_text);

		// Set disclaimer to article
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '1');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->see(SubsView::$disclaimer_article_text);

		// Set disclaimer to menu item
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '2');

		$I->amOnPage(SubsView::$register_url);
		$I->scrollTo(SubsView::$view_register, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible(SubsView::$view_register, 3);
		$I->scrollTo(SubsView::$disclaimer, 0, -100);
		$I->wait(1);
		$I->seeElement(SubsView::$disclaimer);
		$I->click(SubsView::$disclaimer_link);
		$I->see(SubsView::$disclaimer_menuitem_text);

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to check security question by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function CheckSecurityQuestionComponent(AcceptanceTester $I)
	{
		$I->wantTo("Test security question by component");
		$I->expectTo('to see error message on wrong answer');

		Generals::presetComponentOptions($I);

		// Set use captcha, disable verify mail address
		$I->setManifestOption('com_bwpostman', 'use_captcha', '1');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', '0');

		$I->amOnPage(SubsView::$register_url);
		SubsView::subscribeByComponent($I);

		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->click(SubsView::$button_register);

		$I->waitForElementVisible(Generals::$alert_error_1, 2);
		$I->scrollTo(Generals::$alert_error_1, 0, -100);
		$I->wait(1);
		$I->see(SubsView::$security_question_error);

		$I->fillField(SubsView::$question, '4');
		$I->seeElement(SubsView::$security_star);
		$I->scrollTo(SubsView::$button_register);
		$I->wait(1);
		$I->click(SubsView::$button_register);
		$I->wait(2);

		$I->scrollTo("//*/div/nav/ol");
		$I->wait(1);

		$I->waitForElement(SubsView::$registration_complete, 3);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::activate($I, SubsView::$mail_fill_1);
		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);

		// Reset options
		Generals::presetComponentOptions($I);
	}

	/**
	 * Test method to subscribe by component in front end, but send no activation because of missing sender data
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
//	public function SubscribeActivationNoSenderData(AcceptanceTester $I)
//	{
//		$I->wantTo("Subscribe to mailinglist by component, get error message no activation mail was sent");
//		$I->expectTo('see error message');
//
//		Generals::presetComponentOptions($I);
//		$I->setManifestOption('com_bwpostman', 'default_from_email', 'webmaster');
//
//		SubsView::subscribeByComponent($I);
//		$I->click(SubsView::$button_register);
//
//		$I->scrollTo(SubsView::$err_no_activation, 0, -100);
//		$I->wait(1);
//		$I->waitForElementVisible(SubsView::$err_no_activation, 3);
//		$I->see(SubsView::$msg_err_occurred);
//		$I->see(SubsView::$activation_mail_error);
//
//		SubsView::activate($I, SubsView::$mail_fill_1);
//
//		SubsView::unsubscribe($I, SubsView::$activated_edit_Link);
//
//		$I->setManifestOption('com_bwpostman', 'default_from_email', 'webmaster@boldt-webservice.de');
//	}

	/**
	 * Test method to go to edit newsletter subscription
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @return string                       $editlink_code
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function gotoEdit(\AcceptanceTester $I)
	{
		$I->click(SubsView::$get_edit_Link);
		$I->scrollTo(SubsView::$view_edit_link,0 , -50);
		$I->wait(1);
		$I->waitForElement(SubsView::$view_edit_link, 5);
		$I->see(SubsView::$edit_get_text);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_1);
		$I->click(SubsView::$send_edit_link);
		$I->waitForElement(SubsView::$success_message, 5);
		$I->see(SubsView::$editlink_sent_text);

		$editlink_code = $I->getEditlinkCode(SubsView::$mail_fill_1);
		return $editlink_code;
	}
}
