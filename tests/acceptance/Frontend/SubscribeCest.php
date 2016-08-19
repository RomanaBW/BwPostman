<?php
use Page\Generals as Generals;
use Page\SubscriberviewPage as SubsView;


/**
 * Class SubscribeCest
 *
 * This class contains all methods to test subscription at front end
 *
 * !!!!Requirements: 3 mailinglists available in frontend at minimum!!!!
 *
 *  * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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
class SubscribeCest
{
	/**
	 * Test method to subscribe by component in front end, activate and unsubscribe
	 *
	 * @param   AcceptanceTester         $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeSimpleActivateAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end twice, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeTwiceActivateAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component a second time");
		$I->expectTo('see error message');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);
		$I->wait(5);

		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$err_activation_incomplete);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_activation_incomplete);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end twice, get activation code anew, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeTwiceActivateGetActivationAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$err_activation_incomplete);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_activation_incomplete);

		$I->click(SubsView::$button_send_activation);
		$I->waitForElement(SubsView::$success_message);
		$I->see(SubsView::$activation_sent_text, SubsView::$success_message);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to subscribe by component in front end, activate, subscribe a second time, get edit link and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeActivateSubscribeGetEditlinkAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist and unsubscribe by edit link");
		$I->expectTo('unsubscribe with edit link');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$err_already_subscribed);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_already_subscribed);

		$editlink_code  = $this->_gotoEdit($I);
		$I->amOnPage(SubsView::$editlink . $editlink_code);

		$I->seeElement(SubsView::$view_edit);
		$I->checkOption(SubsView::$button_unsubscribe);
		$I->click(SubsView::$button_submitleave);
		$I->dontSee(SubsView::$mail_fill_1, SubsView::$mail);
	}

	/**
	 * Test method to subscribe by module in front end, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeModuleSimpleActivateAndUnsubscribe(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by module");
		$I->expectTo('get confirmation mail');
		$this->_subscribeByModule($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_unsubscribe($I, SubsView::$activated_edit_Link);
	}

	/**
	 * Test method to verify messages for missing input values by component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeMissingValuesComponent(AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		$I->wantTo("Test messages for missing input values by component");
		$I->expectTo('see error messages');
		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);

		// omit mail address
		$I->click(SubsView::$button_register);
		$I->seeElement(Generals::$alert_error);
		$I->see(SubsView::$invalid_field_mailaddress);

		// omit first name
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->click(SubsView::$button_register);
			$I->seeElement(Generals::$alert_error);
			$I->see(SubsView::$invalid_field_firstname);
			$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		}

		// omit last name
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->click(SubsView::$button_register);
			$I->seeElement(Generals::$alert_error);
			$I->see(SubsView::$invalid_field_name);
			$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		}

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);

		//omit mailinglist selection
		$I->clickAndWait(SubsView::$button_register, 1);
		$I->seeInPopup(SubsView::$popup_select_newsletter);
		$I->acceptPopup();

		$I->checkOption(SubsView::$ml1);

		// omit additional field
		if ($options->show_special || $options->special_field_obligation)
		{
			$I->click(SubsView::$button_register);
			$I->seeInPopup(sprintf(SubsView::$popup_enter_special, $options->special_label));
			$I->acceptPopup();
			$I->fillField(SubsView::$special, SubsView::$special_fill);
		}

		// omit disclaimer
		if ($options->disclaimer)
		{
			$I->click(SubsView::$button_register);
			$I->seeInPopup(SubsView::$popup_accept_disclaimer);
			$I->acceptPopup();
			$I->checkOption(SubsView::$disclaimer);
		}
	}

	/**
	 * Test method to get edit page by click at module in front end
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function EditSubscriptionByModule(AcceptanceTester $I)
	{
		$I->wantTo("Edit subscription by module");
		$I->expectTo('see get edit link page');
		$I->amOnPage(SubsView::$register_url);
		$I->click(SubsView::$mod_button_edit);
		$I->waitForElement(SubsView::$mail);
		$I->see(SubsView::$edit_get_text);
	}

	/**
	 * Test method to verify messages for missing input values by module
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeMissingValuesModule(AcceptanceTester $I)
	{
		//Chromium fails to remember entered values, so some fillField are practically superfluous, but Chromium needs them
		$options    = $I->getManifestOptions('mod_bwpostman');

		$I->wantTo("Test messages for missing input values by module");
		$I->expectTo('see error popup');
				$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);

		// omit mail address
		$I->click(SubsView::$mod_button_register);
		$I->seeInPopup(SubsView::$popup_valid_mailaddress);
		$I->acceptPopup();

		$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);

		//omit mailinglist selection
		$I->clickAndWait(SubsView::$mod_button_register, 1);
		$I->seeInPopup(SubsView::$popup_select_newsletter);
		$I->acceptPopup();
		$I->wait(1);

		$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);
		$I->checkOption(SubsView::$mod_ml1);
		$I->checkOption(SubsView::$mod_disclaimer);

		// omit first name
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->click(SubsView::$mod_button_register);
			$I->seeElement(Generals::$alert_error);
			$I->see(SubsView::$invalid_field_firstname_mod);
			$I->fillField(SubsView::$mod_firstname, SubsView::$firstname_fill);
			$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);
			$I->checkOption(SubsView::$mod_ml1);
			$I->checkOption(SubsView::$mod_disclaimer);
		}

		// omit last name
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->click(SubsView::$mod_button_register);
			$I->seeElement(Generals::$alert_error);
			$I->see(SubsView::$invalid_field_name_mod);
			$I->fillField(SubsView::$mod_firstname, SubsView::$firstname_fill);
			$I->fillField(SubsView::$mod_name, SubsView::$lastname_fill);
			$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);
			$I->checkOption(SubsView::$mod_ml1);
			$I->checkOption(SubsView::$mod_disclaimer);
		}

		// omit additional field
		if ($options->show_special || $options->special_field_obligation)
		{
			$I->click(SubsView::$mod_button_register);
			$I->seeElement(Generals::$alert_error);
			$I->see(sprintf(SubsView::$invalid_field_special_mod, $options->special_label));
			$I->fillField(SubsView::$mod_special, SubsView::$special_fill);
			$I->fillField(SubsView::$mod_firstname, SubsView::$firstname_fill);
			$I->fillField(SubsView::$mod_name, SubsView::$lastname_fill);
			$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);
			$I->checkOption(SubsView::$mod_ml1);
		}

		// omit disclaimer
		if ($options->disclaimer)
		{
			$I->click(SubsView::$mod_button_register);
			$I->seeInPopup(SubsView::$popup_accept_disclaimer);
			$I->acceptPopup();
			$I->checkOption(SubsView::$mod_disclaimer);
		}


	}

	/**
	 * Test method to subscribe by component in front end, activate, make changes and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeSimpleActivateChangeAndUnsubscribe(AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		$I->wantTo("Subscribe to mailinglist by component, change values and unsubscribe");
		$I->expectTo('get confirmation mail');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_activate($I, SubsView::$mail_fill_1);
		$I->click(SubsView::$button_edit);

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubsView::$firstname, "Charles");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$firstname, SubsView::$firstname_fill);
			$I->seeInField(SubsView::$firstname, 'Charles');
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubsView::$name, "Crackerbarrel");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$name, SubsView::$lastname_fill);
			$I->seeInField(SubsView::$name, 'Crackerbarrel');
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubsView::$special, "4711");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$special, SubsView::$special_fill);
			$I->seeInField(SubsView::$special, '4711');
		}

		$I->checkOption(SubsView::$ml2);
		$I->click(SubsView::$button_submit);
		$I->waitForElement(SubsView::$edit_saved_successfully);
		$I->see(SubsView::$msg_saved_successfully);
		$I->waitForElement(SubsView::$view_edit);
		$I-> seeCheckboxIsChecked(SubsView::$ml2);
		$I->uncheckOption(SubsView::$ml1);
		$I->click(SubsView::$button_submit);
		$I-> dontSeeCheckboxIsChecked(SubsView::$ml1);

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_2);
		$I->click(SubsView::$button_submit);
		$I->waitForElement(SubsView::$register_success);
		$I->see(SubsView::$msg_saved_changes);

		$this->_activate($I, SubsView::$mail_fill_2);
		$I->click(SubsView::$button_edit);

		$this->_unsubscribe($I, SubsView::$button_unsubscribe);
	}

	/**
	 * Test method to get error message while activating a non existing subscription
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function SubscribeActivateUnsubscribeAndActivate(AcceptanceTester $I)
	{
		$I->wantTo("Subscribe to mailinglist by component");
		$I->expectTo('get confirmation mail');
		$this->_subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		$this->_activate($I, SubsView::$mail_fill_1);

		$this->_unsubscribe($I, SubsView::$activated_edit_Link);

		$this->_activate($I, SubsView::$mail_fill_1, false);
		$I->waitForElement(SubsView::$err_not_activated);
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
	 * @since   2.0.0
	 */
	public function GetEditlinkWrongAddress(AcceptanceTester $I)
	{
		$I->wantTo('Get edit link');
		$I->expectTo('see message wrong mail address');
		$I->amOnPage(SubsView::$register_url);
		$I->click(SubsView::$register_edit_url);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_2);
		$I->click(SubsView::$send_edit_link);
		$I->waitForElement(SubsView::$err_get_editlink);
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
	 * @since   2.0.0
	 */
	public function WrongUnsubscribeLinks(AcceptanceTester $I)
	{
		$I->wantTo('Unsubscribe with faulty edit link');
		$I->expectTo('see message wrong edit link');
		$I->amOnPage(SubsView::$unsubscribe_link_faulty);
		$I->waitForElement(SubsView::$err_get_editlink);
		$I->wait(2);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_wrong_editlink);

		$I->amOnPage(SubsView::$unsubscribe_link_empty);
		$I->waitForElement(SubsView::$err_get_editlink);
		$I->see(SubsView::$msg_err_occurred);
		$I->see(SubsView::$msg_err_wrong_editlink);

		$I->amOnPage(SubsView::$unsubscribe_link_missing);
		$I->waitForElement(SubsView::$mail);
		$I->see(SubsView::$edit_get_text);
	}

	/**
	 * Test method to subscribe to newsletter in front end by component
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @since   2.0.0
	 */
	private function _subscribeByComponent(\AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		$I->amOnPage(SubsView::$register_url);
		$I->wait(1);
		$I->seeElement(SubsView::$view_register);

		if ($options->show_gender)
		{
			$I->click(SubsView::$gender_female);
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		}

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubsView::$format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubsView::$special, SubsView::$special_fill);
		}

		$I->checkOption(SubsView::$ml1);

		if ($options->disclaimer)
		{
			$I->checkOption(SubsView::$disclaimer);
		}
		$I->click(SubsView::$button_register);
	}

	/**
	 * Test method to subscribe to newsletter in front end by module
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @since   2.0.0
	 */
	private function _subscribeByModule(\AcceptanceTester $I)
	{
		$options    = $I->getManifestOptions('mod_bwpostman');

		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_module);

		if ($options->show_gender)
		{
			$I->click(SubsView::$gender_female);
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubsView::$mod_firstname, SubsView::$firstname_fill);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubsView::$mod_name, SubsView::$lastname_fill);
		}

		$I->fillField(SubsView::$mod_mail, SubsView::$mail_fill_1);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(SubsView::$format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubsView::$mod_special, SubsView::$special_fill);
		}

		$I->checkOption(SubsView::$mod_ml2);
		$I->scrollTo(SubsView::$mod_button_register);

		if ($options->disclaimer)
		{
			$I->checkOption(SubsView::$mod_disclaimer);
		}

		$I->click(SubsView::$mod_button_register);
		$I->waitForElement(SubsView::$registration_complete);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);
	}

	/**
	 * Test method to activate newsletter subscription
	 *
	 * @param \AcceptanceTester             $I
	 * @param string                        $mailaddress
	 * @param bool                          $good
	 *
	 * @since   2.0.0
	 */
	private function _activate(\AcceptanceTester $I, $mailaddress, $good = true)
	{
		$activation_code = $I->getActivationCode($mailaddress);
		$I->amOnPage(SubsView::$activation_link . $activation_code);
		if ($good)
		{
			$I->see(SubsView::$activation_completed_text, SubsView::$activation_complete);
		}
	}

	/**
	 * Test method to go to edit newsletter subscription
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @return string                       $editlink_code
	 *
	 * @since   2.0.0
	 */
	private function _gotoEdit(\AcceptanceTester $I)
	{
		$I->click(SubsView::$get_edit_Link);
		$I->waitForElement(SubsView::$view_edit_link);
		$I->see(SubsView::$edit_get_text);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_1);
		$I->click(SubsView::$send_edit_link);
		$I->waitForElement(SubsView::$success_message);
		$I->see(SubsView::$editlink_sent_text);

		$editlink_code = $I->getEditlinkCode(SubsView::$mail_fill_1);
		return $editlink_code;
	}

	/**
	 * Test method to unsubscribe from all newsletters
	 *
	 * @param \AcceptanceTester             $I
	 * @param string                        $button
	 *
	 * @since   2.0.0
	 */
	private function _unsubscribe(\AcceptanceTester $I, $button)
	{
		$I->click($button);
		$I->waitForElement(SubsView::$view_edit);
		$I->seeElement(SubsView::$view_edit);
		$I->checkOption(SubsView::$button_unsubscribe);
		$I->click(SubsView::$button_submitleave);
		$I->dontSee(SubsView::$mail_fill_1, SubsView::$mail);
	}

}
