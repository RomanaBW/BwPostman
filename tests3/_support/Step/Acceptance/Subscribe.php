<?php
namespace Step\Acceptance;

use Helper\Acceptance;
use Page\SubscriberviewPage;

/**
 * Class Subscribe
 *
 * This class contains helper methods for testing back end
 *
 * @package Step\Acceptance
 *
 * @since   2.0.0
 */
class Subscribe extends \AcceptanceTester
{
	/**
	 * Test method to subscribe to newsletter in front end by component
	 *
	 * @param SubscriberviewPage  $subscriberView
	 * @param string                    $mailaddress
	 *
	 * @since   2.0.0
	 */
	public function subscribeByComponent(SubscriberviewPage $subscriberView, $mailaddress)
	{
		$I = $this;

		$I->amOnPage($subscriberView::$register_url);
		$I->seeElement($subscriberView::$view_register);

		$I->fillField($subscriberView::$firstname, $subscriberView::$firstname_fill);
		$I->fillField($subscriberView::$name, $subscriberView::$lastname_fill);
		$I->fillField($subscriberView::$special, $subscriberView::$special_fill);
		$I->fillField($subscriberView::$mail, $mailaddress);
		$I->checkOption($subscriberView::$ml1);
		$I->checkOption($subscriberView::$disclaimer);
		$I->click($subscriberView::$button_register);
	}

	/**
	 * Test method to subscribe to newsletter in front end by module
	 *
	 * @param SubscriberviewPage $subscriberView
	 * @param string             $mailaddress
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function subscribeByModule(SubscriberviewPage $subscriberView, $mailaddress)
	{
		$I = $this;

		$I->wantTo("Subscribe to mailinglist by module");
		$I->expectTo('get confirmation mail');
		$I->amOnPage($subscriberView::$register_url);
		$I->seeElement($subscriberView::$view_module);
		$I->fillField($subscriberView::$mod_firstname, $subscriberView::$firstname_fill);
		$I->fillField($subscriberView::$mod_name, $subscriberView::$lastname_fill);
		$I->fillField($subscriberView::$mod_special, $subscriberView::$special_fill);
		$I->fillField($subscriberView::$mod_mail, $mailaddress);
		$I->checkOption($subscriberView::$mod_ml2);
		$I->checkOption($subscriberView::$mod_disclaimer);
		$I->click($subscriberView::$mod_button_register);
		$I->waitForElement($subscriberView::$registration_complete, 30);
		$I->see($subscriberView::$registration_completed_text, $subscriberView::$registration_complete);
	}

	/**
	 * Test method to activate newsletter subscription
	 *
	 * @param SubscriberviewPage    $subscriberView
	 * @param Acceptance            $helper
	 * @param string                $mailaddress
	 *
	 * @since   2.0.0
	 */
	public function activate(SubscriberviewPage $subscriberView, Acceptance $helper, $mailaddress, $good = true)
	{
		$I = $this;

		$I->wantTo("Activate mailinglist subscription");
		$I->expectTo('see activation message');
		$activation_code = $I->getActivationCode($mailaddress);
		$I->amOnPage($subscriberView::$activation_link . $activation_code);
		if ($good)
		{
			$I->see($subscriberView::$activation_completed_text, $subscriberView::$activation_complete);
		}
	}

	/**
	 * Test method to go to edit newsletter subscription
	 *
	 * @param SubscriberviewPage   $subscriberView
	 * @param Acceptance           $helper
	 * @param string               $mailaddress
	 *
	 * @return string              $editlink_code
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function gotoEdit(SubscriberviewPage $subscriberView, Acceptance $helper, $mailaddress)
	{
		$I = $this;

		$I->wantTo('Get edit link');
		$I->expectTo('see message edit link sent');
		$I->click($subscriberView::$get_edit_Link);
		$I->waitForElement($subscriberView::$view_edit_link, 30);
		$I->see($subscriberView::$edit_get_text);
		$I->fillField($subscriberView::$edit_mail, $mailaddress);
		$I->click($subscriberView::$send_edit_link);
		$I->waitForElement($subscriberView::$success_message, 30);
		$I->see($subscriberView::$editlink_sent_text);

		$editlink_code = $I->getEditlinkCode($mailaddress);
		return $editlink_code;
	}

	/**
	 * Test method to unsubscribe from all newsletters
	 *
	 * @param SubscriberviewPage  $subscriberView
	 * @param string              $button
	 * @param string              $mailaddress
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function unsubscribe(SubscriberviewPage $subscriberView, $button, $mailaddress)
	{
		$I = $this;

		$I->wantTo("Unsubscribe from mailinglist");
		$I->expectTo('see unsubscribe message');
		$I->click($button);
		$I->waitForElement($subscriberView::$view_edit, 30);
		$I->seeElement($subscriberView::$view_edit);
		$I->checkOption($subscriberView::$button_unsubscribe);
		$I->click($subscriberView::$button_submitleave);
		$I->dontSee($mailaddress, $subscriberView::$mail);
	}
}
