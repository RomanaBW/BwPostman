<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\Buyer2SubscriberPage as BuyerPage;
use Page\User2SubscriberPage as UserPage;

/**
 * Class Buyer2SubscriberCest
 *
 * This class contains all methods to test subscription while buying an item by virtuemart at front end
 *
 * @package Buyer Subscribe Plugin
 * @copyright (C) 2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
class Buyer2SubscriberCest
{
	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	public $tester;

	/**
	 * @var array  $mls_to_subscribe
	 *
	 * @since   2.0.0
	 */
	private $mls_to_subscribe = array();


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	private $subs_selected          = true;


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private $existing_data          = array();

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private $entry_data             = array();

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private $params                 = array();

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public $result_data             = array();


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public $order_number            = '';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public $omitted                 = false;

	/**
	 * Test method to order without activated Plugin B2S
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithB2SPluginDeactivated($I)
	{
		$I->wantTo("order without activated Plugin B2S");
		$I->expectTo('see no additional form fields, see order and no subscriber');

		$this->initializeTestValues($I);
		$this->subs_selected    = false;

		$this->entry_data       = BuyerPage::$entry_data_no_existing_subs[0]['data'];
		$this->params           = BuyerPage::$entry_data_no_existing_subs[0]['params'];
		$this->result_data      = BuyerPage::$result_data_no_existing_subs[0];

		$I->setExtensionStatus('bwpm_buyer2subscriber', 0);
		$this->doOrderUntilAddressEditPage($I);
		$this->checkForPluginFieldsNotVisible($I);
		$this->fillAddressAndSubmitOrder($I);
		$this->checkForSubscriptionProcessed($I);
		$I->setExtensionStatus('bwpm_buyer2subscriber', 1);
		$this->cleanup($I);
	}

	/**
	 * Test method to order with activated Plugin B2S, deactivated Plugin U2S
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithU2SPluginDeactivated($I)
	{
		$I->wantTo("order with activated Plugin B2S but no activated Plugin U2S");
		$I->expectTo('see no additional form fields, see order and no subscriber');

		$this->initializeTestValues($I);
		$this->subs_selected    = false;

		$this->entry_data       = BuyerPage::$entry_data_no_existing_subs[0]['data'];
		$this->params           = BuyerPage::$entry_data_no_existing_subs[0]['params'];
		$this->result_data      = BuyerPage::$result_data_no_existing_subs[0];

		$I->setExtensionStatus('bwpm_user2subscriber', 0);
		$this->doOrderUntilAddressEditPage($I);
		$this->checkForPluginFieldsNotVisible($I);
		$this->checkForSubscriptionProcessed($I);
		$I->setExtensionStatus('bwpm_user2subscriber', 1);
		$this->cleanup($I);
	}

	/**
	 * Test method to order with activated Plugin2 B2S and U2S, deactivated component
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithComponentDeactivated($I)
	{
		$I->wantTo("order activated Plugins B2S and U2S but no activated component");
		$I->expectTo('see no additional form fields, see order and no subscriber');

		$this->initializeTestValues($I);
		$this->subs_selected    = false;

		$this->entry_data       = BuyerPage::$entry_data_no_existing_subs[0]['data'];
		$this->params           = BuyerPage::$entry_data_no_existing_subs[0]['params'];
		$this->result_data      = BuyerPage::$result_data_no_existing_subs[0];

		$I->setExtensionStatus('com_bwpostman', 0);
		$this->doOrderUntilAddressEditPage($I);
		$this->checkForPluginFieldsNotVisible($I);
		$this->checkForSubscriptionProcessed($I);
		$I->setExtensionStatus('com_bwpostman', 1);
		$this->cleanup($I);
	}

	/**
	 * Test method to order without subscription, no existing subscription
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithoutSubscriptionNoExistingSubscription($I)
	{
		$I->wantTo("order without subscription and no existing subscription");
		$I->expectTo('see additional form values, see order and no subscriber');

		$this->initializeTestValues($I);
		$this->subs_selected    = false;

		$this->entry_data       = BuyerPage::$entry_data_no_existing_subs[0]['data'];
		$this->params           = BuyerPage::$entry_data_no_existing_subs[0]['params'];
		$this->result_data      = BuyerPage::$result_data_no_existing_subs[0];

		$this->doOrderUntilAddressEditPage($I);
		$this->fillAddressAndSubmitOrder($I);
		$this->checkForOrderReceived($I);
		$this->checkForSubscriptionProcessed($I);
		$this->cleanup($I);
	}

	/**
	 * Test method to order without subscription, existing subscription
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithoutSubscriptionExistingSubscription($I)
	{
		$I->wantTo("order without subscription and existing subscription");
		$I->expectTo('see additional form values, see order and existing subscriber without changes');

		$this->initializeTestValues($I);
		$this->subs_selected    = false;

		$this->entry_data       = BuyerPage::$entry_data_existing_subs[0]['entry_data'];
		$this->existing_data    = BuyerPage::$entry_data_existing_subs[0]['existing_data'];
		$this->params           = BuyerPage::$entry_data_existing_subs[0]['params'];
		$this->result_data      = BuyerPage::$result_data_existing_subs[0];

		$this->makeExistingSubscription($I, 4);

		$this->doOrderUntilAddressEditPage($I);
		$this->fillAddressAndSubmitOrder($I);
		$this->checkForOrderReceived($I);
		$this->checkForSubscriptionProcessed($I);
		$this->cleanup($I);
	}

	/**
	 * Test method to order with subscription, but omit required additional field
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithSubscriptionWithoutRequiredField($I)
	{
		$I->wantTo("order with subscription and omit filling required additional field");
		$I->expectTo('see additional form values, see error message about missing data in required field');

		$this->initializeTestValues($I);
		$this->subs_selected    = true;
		$this->omitted          = true;

		$this->entry_data       = BuyerPage::$entry_data_missing_additional[0]['data'];
		$this->params           = BuyerPage::$entry_data_missing_additional[0]['params'];
		$this->result_data      = BuyerPage::$entry_data_subs_missing_additional[0];

		$this->setManifestOptions($I);
		$I->setExtensionStatus('bwpm_buyer2subscriber', 1);

		$this->doOrderUntilAddressEditPage($I);
		$this->fillAddressAndSubmitOrder($I);
		$this->checkForMissingRequired($I);
	}

	/**
	 * Test method to order with subscription, no existing subscription
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithSubscriptionNoExistingSubscription($I)
	{
		$I->wantTo("order with subscription and no existing subscription");
		$I->expectTo('see additional form values, see order and new confirmed subscriber');

		$entry_data  = BuyerPage::$entry_data_no_existing_subs;
		$result_data = BuyerPage::$result_data_no_existing_subs;

		for ($k = 0; $k < count($entry_data); $k++)
		{
			try
			{
				$this->initializeTestValues($I);

				$this->entry_data   = $entry_data[$k]['data'];
				$this->params       = $entry_data[$k]['params'];
				$this->result_data  = $result_data[$k];

				$this->setManifestOptions($I);

				$this->doOrderUntilAddressEditPage($I);
				$this->fillAddressAndSubmitOrder($I);
				$this->checkForOrderReceived($I);

				$this->existing_data    = $this->entry_data;

				$this->checkForSubscriptionProcessed($I);

				$this->cleanup($I);
			}
			catch (RuntimeException $e)
			{
				$this->handleException($I, $e);
			}
		}
	}

	/**
	 * Test method to order with subscription, existing subscription with same mailinglist
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithSubscriptionExistingSubscriptionSameML($I)
	{
		$I->wantTo("order with subscription and existing subscription to same mailinglist");
		$I->expectTo('see additional form values, see order and existing subscriber with and without changes');

		$entry_data  = BuyerPage::$entry_data_existing_subs;
		$result_data = BuyerPage::$result_data_existing_subs;

		for ($k = 0; $k < count($entry_data); $k++)
		{
			try
			{
				$this->initializeTestValues($I);

				$this->existing_data    = $entry_data[$k]['existing_data'];
				$this->params           = $entry_data[$k]['params'];
				$this->entry_data       = $entry_data[$k]['entry_data'];
				$this->result_data      = $result_data[$k];

				$this->setManifestOptions($I);

				$this->makeExistingSubscription($I, 4);

				$this->doOrderUntilAddressEditPage($I);
				$this->fillAddressAndSubmitOrder($I);
				$this->checkForOrderReceived($I);
				$this->checkForSubscriptionProcessed($I);
				$this->cleanup($I);
			}
			catch (RuntimeException $e)
			{
				$this->handleException($I, $e);
			}
		}
	}

	/**
	 * Test method to order with subscription, existing subscription with different mailinglist
	 *
	 * @param   AcceptanceTester                $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function orderWithSubscriptionExistingSubscriptionDifferentML($I)
	{
		$I->wantTo("order with subscription and existing subscription to different mailinglist");
		$I->expectTo('see additional form values, see order and existing subscriber with new additional mailinglist');

		$this->initializeTestValues($I);

		$this->existing_data    = BuyerPage::$entry_data_existing_subs[1]['existing_data'];
		$this->entry_data       = BuyerPage::$entry_data_existing_subs[1]['entry_data'];
		$this->params           = BuyerPage::$entry_data_existing_subs[1]['params'];
		$this->result_data      = BuyerPage::$result_data_existing_subs[1];

		$this->makeExistingSubscription($I, 6);

		$this->doOrderUntilAddressEditPage($I);
		$this->fillAddressAndSubmitOrder($I);

		$this->checkForOrderReceived($I);

		$this->checkForSubscriptionProcessed($I);

		$this->cleanup($I);
	}

	/**
	 * Test method to option message
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function Buyer2SubscriberOptionsMessage(AcceptanceTester $I)
	{
		$I->wantTo("change newsletter message and change back");
		$I->expectTo('see changed messages at top of address edit form');

		$this->editPluginOptions($I);
		$I->clickAndWait(BuyerPage::$plugin_tab_options, 1);

		$this->switchPluginMessage($I, UserPage::$plugin_message_new);

		// look at FE
		$user = $I->haveFriend('User1');
		$that = $this;
		$user->does(
			function (AcceptanceTester $I) use ($that)
			{
				$that->doOrderUntilAddressEditPage($I);

				$I->see(UserPage::$plugin_message_new, BuyerPage::$message_identifier);
			}
		);
		$user->leave();

		$this->switchPluginMessage($I, UserPage::$plugin_message_old);

		// look at FE
		$user = $I->haveFriend('User2');
		$user->does(
			function (AcceptanceTester $I) use ($that)
			{
				$that->doOrderUntilAddressEditPage($I);

				$I->see(UserPage::$plugin_message_old, BuyerPage::$message_identifier);
			}
		);
		$user->leave();

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$login = new LoginPage($I);

		$login->logoutFromBackend($I);
	}

	/**
	 * Test method to option mailinglists
	 *
	 * @param   AcceptanceTester $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function Buyer2SubscriberOptionsMailinglists(AcceptanceTester $I)
	{
		$I->wantTo("add additional mailinglist to options");
		$I->expectTo('see further selected mailinglist at plugin options form');

		$this->editPluginOptions($I);
		$I->clickAndWait(BuyerPage::$plugin_tab_mailinglists, 1);

		// click checkbox for further mailinglist
		$I->checkOption(sprintf(UserPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->seeCheckboxIsChecked(sprintf(UserPage::$plugin_checkbox_mailinglist, 6));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_buyer2subscriber');
		$I->assertEquals("1", $options->ml_available[0]);
		$I->assertEquals("4", $options->ml_available[1]);

		// deselect further mailinglist
		$I->uncheckOption(sprintf(UserPage::$plugin_checkbox_mailinglist, 0));
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->dontSeeCheckboxIsChecked(sprintf(UserPage::$plugin_checkbox_mailinglist, 5));

		// getManifestOption
		$options = $I->getManifestOptions('bwpm_buyer2subscriber');
		$I->assertEquals("4", $options->ml_available[0]);

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$login = new LoginPage($I);

		$login->logoutFromBackend($I);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function initializeTestValues($I)
	{
		$this->tester               = $I;
		$this->subs_selected        = true;
		$this->omitted              = false;
		$this->mls_to_subscribe     = array(UserPage::$mailinglist1_checked);

		//reset option settings
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');
		$I->setManifestOption('bwpm_buyer2Subscriber', 'ml_available', array("4"));
		$I->setManifestOption('com_bwpostman', 'show_special', '1');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_gender', '0');
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $message
	 *
	 *
	 * @since 2.0.0
	 */
	private function switchPluginMessage(AcceptanceTester $I, $message)
	{
		$I->fillField(BuyerPage::$plugin_message_identifier, $message);
		$I->clickAndWait(Generals::$toolbar['Save'], 1);
		$I->see(Generals::$plugin_saved_success);
		$I->see($message, BuyerPage::$plugin_message_identifier);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function doOrderUntilAddressEditPage(AcceptanceTester $I)
	{
		$this->gotoProductPage($I);
		$this->addItemAndGotoCart($I);
		$this->verifyCartReached($I);
		$this->gotoAddressEditPage($I);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function gotoProductPage(AcceptanceTester $I)
	{
		$I->amOnPage(BuyerPage::$link_to_product);
		$I->waitForElement(BuyerPage::$product_page_identifier, 30);
		$I->see(BuyerPage::$product_page_header_text);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function addItemAndGotoCart(AcceptanceTester $I)
	{
		$I->click(BuyerPage::$button_add_to_cart);
		$I->waitForElementVisible(BuyerPage::$link_in_popup_show_cart, 30);
		$I->see(BuyerPage::$button_text_show_cart);
		$I->click(BuyerPage::$link_in_popup_show_cart);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function verifyCartReached(AcceptanceTester $I)
	{
		$I->waitForElement(BuyerPage::$header_cart_identifier, 30);
		$I->see(BuyerPage::$header_cart_text);
		$I->see(BuyerPage::$sku_text, BuyerPage::$sku_identifier);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function gotoAddressEditPage(AcceptanceTester $I)
	{
		$I->click(BuyerPage::$button_enter_address);
		$I->waitForElement(BuyerPage::$header_account_details);
		$I->see(BuyerPage::$header_account_details_text);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForPluginFieldsNotVisible($I)
	{
		$I->dontSeeElement(BuyerPage::$message_identifier);
		$I->dontSeeElement(BuyerPage::$subscription_identifier);
		$I->dontSeeElement(BuyerPage::$format_identifier);
		$I->dontSeeElement(BuyerPage::$additional_identifier);
		$I->dontSeeElement(BuyerPage::$gender_identifier);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function checkForMissingRequired($I)
	{
		$I->seeInPopup(BuyerPage::$error_popup_missing_additional);
		$I->acceptPopup();

		$I->scrollTo(Generals::$sys_message_container, 0, 100);
		$I->wait(1);
		$I->see(BuyerPage::$error_alert_missing_additional, BuyerPage::$alert_error_div);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForSubscriptionProcessed($I)
	{
		$table_subs  = Generals::$db_prefix . UserPage::$bwpm_subs_table;
		$table_mls   = Generals::$db_prefix . UserPage::$bwpm_subs_mls_table;

		if ($this->subs_selected)
		{
			$subs_id     = $I->grabFromDatabase($table_subs, 'id', array('email' => $this->entry_data['email']));
			$data_subs   = $this->prepareSubsData($this->result_data);

			$data_mls    = array('subscriber_id' => $subs_id, 'mailinglist_id'   => 4);

			$I->seeInDatabase($table_subs, $data_subs);
			$I->seeInDatabase($table_mls, $data_mls);
		}
		elseif (array_key_exists('email', $this->existing_data))
		{
			$subs_id     = $I->grabFromDatabase($table_subs, 'id', array('email' => $this->existing_data['email']));
			$data_subs   = $this->prepareSubsData($this->result_data);
			$data_mls    = array('subscriber_id' => $subs_id, 'mailinglist_id'   => 4);

			$I->seeInDatabase($table_subs, $data_subs);
			$I->seeInDatabase($table_mls, $data_mls);
		}
		else
		{
			$data   = array('email' => $this->entry_data['email']);

			$I->dontSeeInDatabase($table_subs, $data);
		}
	}

	/**
	 *
	 * @param array $data
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function prepareSubsData($data)
	{
		$data['status'] = 1;

		unset($data['street']);
		unset($data['zip_code']);
		unset($data['city']);
		unset($data['country']);

		return $data;
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function cleanup($I)
	{
		$this->deleteOrder($I);

		if ($this->subs_selected)
		{
			$this->deleteSubscription($I);
		}
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteOrder($I)
	{
		$table  = BuyerPage::$vm_order_table;
		// @ToDo: rewrite following method to build WHERE clause there, feeded by an array submitted like a line before, see next method
		$data   = " WHERE `order_number` = '$this->order_number'";

		$I->deleteRecordFromDatabase($table, $data);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteSubscription($I)
	{
		$table_subs  = UserPage::$bwpm_subs_table;
		$data_subs   = $this->prepareSubsData($this->result_data);

		$subs_id     = $I->grabFromDatabase(Generals::$db_prefix . $table_subs, 'id', array('email' => $this->existing_data['email']));

		$I->deleteRecordFromDatabase($table_subs, $data_subs);

		$table_mls  = UserPage::$bwpm_subs_mls_table;
		$data_mls   = array('subscriber_id' => $subs_id);

		$I->deleteRecordFromDatabase($table_mls, $data_mls);
	}

	/**
	 * Helper method to create a subscription for tests which needs an exiting subscriber
	 * Before the subscription can be processed, a (new) test class intern subscriber object has to be created and
	 *
	 * @param   AcceptanceTester    $I
	 * @param   int                 $ml_id
	 *
	 * @since 2.0.0
	 */
	protected function makeExistingSubscription($I, $ml_id)
	{
		$table_subs     = Generals::$db_prefix . UserPage::$bwpm_subs_table;
		$existing_subs  = $this->prepareSubsData($this->existing_data);

		$I->haveInDatabase($table_subs, $existing_subs);

		$subs_id   = $I->grabFromDatabase($table_subs, 'id', array('email' => $this->existing_data['email']));

		$table_mls      = Generals::$db_prefix . UserPage::$bwpm_subs_mls_table;
		$existing_mls   = array('subscriber_id' => $subs_id, 'mailinglist_id'   => $ml_id);

		$I->haveInDatabase($table_mls, $existing_mls);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function fillAddressAndSubmitOrder($I)
	{
		$entries    = $this->entry_data;
		$params    = $this->params;

		$I->fillField(BuyerPage::$billto_identifier_email, $entries['email']);
		if ($this->subs_selected)
		{
			$I->clickSelectList(BuyerPage::$subscription_list, BuyerPage::$subscription_yes, BuyerPage::$subscription_identifier);
			if ($params['show_emailformat'])
			{
				$I->clickSelectList(
					BuyerPage::$format_list,
					sprintf(BuyerPage::$format_list_value, (int) $entries['selected_format'] + 1),
					BuyerPage::$subscription_identifier
				);
			}

			if ($params['show_gender'])
			{
				$I->clickSelectList(
					BuyerPage::$gender_list,
					sprintf(BuyerPage::$gender_value, (int) $entries['gender'] + 1),
					BuyerPage::$gender_identifier
				);
			}

			if ($params['show_special'])
			{
				$I->fillField(BuyerPage::$additional_identifier, $entries['special']);
			}
		}

		$I->fillField(BuyerPage::$billto_identifier_firstname, $entries['firstname']);
		$I->fillField(BuyerPage::$billto_identifier_lastname, $entries['name']);
		$I->fillField(BuyerPage::$billto_identifier_street, $entries['street']);
		$I->fillField(BuyerPage::$billto_identifier_zip_code, $entries['zip_code']);
		$I->fillField(BuyerPage::$billto_identifier_city, $entries['city']);

		$I->click(BuyerPage::$billto_identifier_save);

		if (!$this->omitted)
		{
			$I->waitForElement(BuyerPage::$header_cart_identifier, 30);

			$this->checkoutCart($I);

			$this->grabOrderNumber($I);
		}
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkoutCart($I)
	{
		$I->scrollTo(BuyerPage::$button_check_out_now, 0, -100);
		$I->wait(1);
		$I->clickAndWait(BuyerPage::$button_tos, 1);
		$I->clickAndWait(BuyerPage::$button_check_out_now, 1);
		$I->waitForText(BuyerPage::$thank_you_page, 30);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function grabOrderNumber($I)
	{
		$order_string       = $I->grabTextFrom(BuyerPage::$order_number_field);
		$order_texts        = explode(' ', $order_string);
		$this->order_number = $order_texts[2];
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.0.0
	 */
	protected function checkForOrderReceived($I)
	{
		$table  = Generals::$db_prefix . BuyerPage::$vm_order_table;
		$data   = array('order_number' => $this->order_number);

		$I->seeInDatabase($table, $data);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function editPluginOptions(AcceptanceTester $I)
	{
		$this->tester = $I;
		$login = new LoginPage($I);

		$login->logIntoBackend(Generals::$admin);

		$this->selectPluginPage($I);

		$this->filterForPlugin($I);

		$I->clickAndWait(UserPage::$plugin_edit_identifier, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function selectPluginPage(AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$plugin_page);
		$I->wait(1);
		$I->see(Generals::$view_plugin, Generals::$pageTitle);
	}

	/**
	 * @param AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	protected function filterForPlugin(AcceptanceTester $I)
	{
		$I->fillField(Generals::$search_field, BuyerPage::$plugin_name);
		$I->clickAndWait(Generals::$search_button, 1);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function setManifestOptions($I)
	{
		foreach ($this->params as $param => $value)
		{
			$I->setManifestOption('com_bwpostman', $param, $value);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 * @param RuntimeException $e
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function handleException($I, $e)
	{
		sprintf('Runtime-Exception: %s', $e->getMessage());
		$this->cleanup($I);
	}
}
