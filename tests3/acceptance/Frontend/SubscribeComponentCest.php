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
	 * Test method to subscribe by component in front end, activate and unsubscribe
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
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

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
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);
		$I->wait(5);

		SubsView::subscribeByComponent($I);
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
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$err_activation_incomplete, 30);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_activation_incomplete);

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
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		SubsView::activate($I, SubsView::$mail_fill_1);

		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$err_already_subscribed, 30);
		$I->see(SubsView::$error_occurred_text, SubsView::$err_already_subscribed);

		$editlink_code  = $this->gotoEdit($I);
		$I->amOnPage(SubsView::$editlink . $editlink_code);

		$I->seeElement(SubsView::$view_edit);
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
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', 1);
		$I->setManifestOption('com_bwpostman', 'disclaimer', 1);
		$options = $I->getManifestOptions('com_bwpostman');
		$bwpm_version   = getenv('BW_TEST_BWPM_VERSION');

		$I->wantTo("Test messages for missing input values by component");
		$I->expectTo('see error messages');
		$I->amOnPage(SubsView::$register_url);
		$I->seeElement(SubsView::$view_register);

		// omit mail address
		$I->click(SubsView::$button_register);
		$I->seeElement(Generals::$alert_error);
		if ($bwpm_version == '132')
		{
			$I->see(SubsView::$invalid_field_mailaddress_132);
		}
		else
		{
			$I->see(SubsView::$invalid_field_mailaddress);
		}

		// omit first name
		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->click(SubsView::$button_register);
			$I->seeElement(Generals::$alert_error);
			if ($bwpm_version == '132')
			{
				$I->see(SubsView::$invalid_field_firstname_132);
			}
			else
			{
				$I->see(SubsView::$invalid_field_firstname);
			}

			$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		}

		// omit last name
		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->click(SubsView::$button_register);
			$I->seeElement(Generals::$alert_error);
			if ($bwpm_version == '132')
			{
				$I->see(SubsView::$invalid_field_name_132);
			}
			else
			{
				$I->see(SubsView::$invalid_field_name);
			}

			$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		}

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);

		//omit mailinglist selection
		$I->clickAndWait(SubsView::$button_register, 1);

		$I->see(SubsView::$invalid_select_newsletter_132);

		$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		$I->fillField(SubsView::$name, SubsView::$lastname_fill);

		$I->checkOption(SubsView::$ml1);

		$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
		$I->fillField(SubsView::$name, SubsView::$lastname_fill);
		$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);

		// omit additional field
		if ($options->show_special || $options->special_field_obligation)
		{
			if ($options->special_label === '')
			{
				$options->special_label = 'Additional Field';
			}
			$I->click(SubsView::$button_register);
			if ($options->special_field_obligation)
			{
				$I->see(sprintf(SubsView::$popup_enter_special, $options->special_label));
			}
			$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
			$I->fillField(SubsView::$name, SubsView::$lastname_fill);
			$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);
			$I->fillField(SubsView::$special, SubsView::$special_fill);
		}

		// omit disclaimer
		if ($options->disclaimer)
		{
			$I->click(SubsView::$button_register);
			$I->see(SubsView::$popup_accept_disclaimer);
			$I->fillField(SubsView::$firstname, SubsView::$firstname_fill);
			$I->fillField(SubsView::$name, SubsView::$lastname_fill);
			$I->fillField(SubsView::$mail, SubsView::$mail_fill_1);
			$I->fillField(SubsView::$special, SubsView::$special_fill);
			$I->checkOption(SubsView::$disclaimer);
		}

		$I->setManifestOption('com_bwpostman', 'special_field_obligation', 0);
		$I->setManifestOption('com_bwpostman', 'disclaimer', 0);
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
		$options    = $I->getManifestOptions('com_bwpostman');

		// Subscribe
		$I->wantTo("Subscribe to mailinglist by component, change values and unsubscribe");
		$I->expectTo('get confirmation mail');
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
		$I->see(SubsView::$registration_completed_text, SubsView::$registration_complete);

		// Activate
		SubsView::activate($I, SubsView::$mail_fill_1);

		// Edit
		$I->click(SubsView::$button_edit);

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(SubsView::$firstname, "Charles");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully, 30);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$firstname, SubsView::$firstname_fill);
			$I->seeInField(SubsView::$firstname, 'Charles');
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(SubsView::$name, "Crackerbarrel");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully, 30);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$name, SubsView::$lastname_fill);
			$I->seeInField(SubsView::$name, 'Crackerbarrel');
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(SubsView::$special, "4711");
			$I->click(SubsView::$button_submit);
			$I->waitForElement(SubsView::$edit_saved_successfully, 30);
			$I->see(SubsView::$msg_saved_successfully);
			$I->dontSeeInField(SubsView::$special, SubsView::$special_fill);
			$I->seeInField(SubsView::$special, '4711');
		}

		$I->checkOption(SubsView::$ml2);
		$I->click(SubsView::$button_submit);
		$I->waitForElement(SubsView::$edit_saved_successfully, 30);
		$I->see(SubsView::$msg_saved_successfully);
		$I->waitForElement(SubsView::$view_edit, 30);
		$I-> seeCheckboxIsChecked(SubsView::$ml2);
		$I->uncheckOption(SubsView::$ml1);
		$I->click(SubsView::$button_submit);
		$I-> dontSeeCheckboxIsChecked(SubsView::$ml1);

		$I->fillField(SubsView::$mail, SubsView::$mail_fill_2);
		$I->click(SubsView::$button_submit);
		$I->waitForElement(Generals::$alert_success, 30);
		$I->see(SubsView::$msg_changed_mailaddress);

		SubsView::activate($I, SubsView::$mail_fill_2);
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
		SubsView::subscribeByComponent($I);
		$I->waitForElement(SubsView::$registration_complete, 30);
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
		$I->click(SubsView::$register_edit_url);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_2);
		$I->click(SubsView::$send_edit_link);
		$I->waitForElement(SubsView::$err_get_editlink, 30);
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

		if (getenv('BWPM_VERSION') == 132)
		{
			$I->seeElement(SubsView::$firstname);
		}
		else
		{
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
	}

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
		$I->waitForElement(SubsView::$view_edit_link, 30);
		$I->see(SubsView::$edit_get_text);
		$I->fillField(SubsView::$edit_mail, SubsView::$mail_fill_1);
		$I->click(SubsView::$send_edit_link);
		$I->waitForElement(SubsView::$success_message, 30);
		$I->see(SubsView::$editlink_sent_text);

		$editlink_code = $I->getEditlinkCode(SubsView::$mail_fill_1);
		return $editlink_code;
	}
}
