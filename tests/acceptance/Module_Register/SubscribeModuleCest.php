<?php
use Page\Generals as Generals;
use Page\SubscriberviewPage as SubsView;


/**
 * Class SubscribeModuleCest
 *
 * This class contains all methods to test subscription by module
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
class SubscribeModuleCest
{
	/**
	 * Test method to subscribe by module in front end, activate and unsubscribe
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @group   module_subscription
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
	 * Test method to get edit page by click at module in front end
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @group   module_subscription
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
	 * @group   module_subscription
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
	 * Test method to subscribe to newsletter in front end by module
	 *
	 * @param \AcceptanceTester             $I
	 *
	 * @group   module_subscription
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
	 * @group   module_subscription
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
	 * Test method to unsubscribe from all newsletters
	 *
	 * @param \AcceptanceTester             $I
	 * @param string                        $button
	 *
	 * @group   module_subscription
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
