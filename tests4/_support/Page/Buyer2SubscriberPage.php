<?php
namespace Page;

/**
 * Class Buyer2SubscribePage
 *
 * @package Buyer Subscribe Plugin
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
class Buyer2SubscriberPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	// Frontend stuff
	// used urls and links

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_product          = "/index.php/shop/headpiece/cap-baseball-detail";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_cart             = "/index.php/shop/cart";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_editAddress      = "/index.php/shop/user/editaddresscartBT";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_add_to_cart       = "/html/body/div[1]/div/div/main/div[3]/div[5]/div[2]/div/div[4]/form/div[2]/span[3]/input";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_tos               = "//*[@id='tos']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_check_out_now     = "//*[@id='checkoutFormSubmit']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_enter_address     = "//*[@id='checkoutForm']/div[1]/div[1]/a";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_text_show_cart        = "Show Cart";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_text_checkout         = "Check Out Now";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_text_purchase         = "Confirm Purchase";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thank_you_page               = "Thank you for your order!";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $header_cart_text             = "Cart";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $product_page_header_text     = 'Cap "Baseball"';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $sku_text                     = "PRCB";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $header_account_details_text  = "Your account details";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_popup_missing_additional   = "Required field is missing";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_alert_missing_additional   = "Invalid field:  Additional Field";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_error_div  = "//*[@id='system-message-container']/div/div";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_in_popup_show_cart   = "//*[@id='fancybox-content']/div/a[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $header_cart_identifier    = "//*[@id='content']/div[3]/div[1]/h1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $sku_identifier            = "//*[@id='checkoutForm']/fieldset[1]/table/tbody/tr[2]/td[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $header_account_details    = "//*[@id='content']/h1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $product_page_identifier   = "//*[@id='content']/div[3]/h1";

	// billto field identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_email           = "//*[@id='email_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_firstname       = "//*[@id='first_name_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_lastname        = "//*[@id='last_name_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_street          = "//*[@id='address_1_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_zip_code        = "//*[@id='zip_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_city            = "//*[@id='city_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_country_id      = "//*[@id='virtuemart_country_id_field_chzn']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_country_select  = "//*[@id='virtuemart_country_id_field_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_country_value   = "//*[@id='virtuemart_country_id_field_chzn_o_81']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $billto_identifier_save            = "//*[@id='userForm']/div/button[2]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $message_identifier       = "//*[@id='userForm']/fieldset/fieldset/legend[contains(.,'newsletter message')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscription_identifier  = "bw_newsletter_subscription_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscription_list        = "//*[@id='bw_newsletter_subscription_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscription_no          = "//*[@id='bw_newsletter_subscription_chzn_o_1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscription_yes         = "//*[@id='bw_newsletter_subscription_chzn_o_2']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_identifier        = "bw_newsletter_format_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list              = "//*[@id='bw_newsletter_format_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list_value        = "//*[@id='bw_newsletter_format_chzn_o_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $additional_identifier    = "//*[@id='bw_newsletter_additional_field']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender_identifier        = "bw_gender_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender_list              = "//*[@id='bw_gender_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender_value             = "//*[@id='bw_gender_chzn_o_%s']";

	// buyer field values

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_null_value         = "NO_NAME_AVAILABLE";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_email        = "dummy-1@tester-net-vm.nil";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_firstname    = "Sam";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_lastname     = "Sample";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_street       = "Cutandrun 1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_zip_code     = "12345";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_city         = "Elsewhere";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_country      = "Germany";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $buyer_value_special      = "0815";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $order_number_field       = "//*[@id='content']/div[3]/div[2]";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $entry_data_missing_additional       = array(
		array(
			'data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '1',
				'show_emailformat' => '0',
				'default_emailformat' => '0',
			),
		),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $entry_data_subs_missing_additional  = array(
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '1',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '1',
				'show_emailformat' => '0',
				'default_emailformat' => '0',
			),
		),
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $entry_data_no_existing_subs  = array(
		array(
			'data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '0',
				'show_special' => '0',
				'special_field_obligation' => '0',
				'show_emailformat' => '0',
				'default_emailformat' => '1',
			),
		),
		array(
			'data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '1',
				'show_emailformat' => '0',
				'default_emailformat' => '0',
			),
		),
		array(
			'data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '2',
				'special' => '',
				'emailformat' => '1',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '1',
			),
		),
		array(
			'data'  => array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
			),
			array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '1',
			),
		),
		array(
			'data'  => array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '2',
			'special' => '0815',
			'emailformat' => '1',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'data'  => array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '2',
			'special' => '0815',
			'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $result_data_no_existing_subs = array(
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '',
			'special' => '',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
		),
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $entry_data_existing_subs     = array(
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '1',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '0',
				'show_special' => '0',
				'special_field_obligation' => '0',
				'show_emailformat' => '0',
				'default_emailformat' => '1',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => '',
				'name' => '',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '1',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0816',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '0',
				'show_special' => '1',
				'special_field_obligation' => '1',
				'show_emailformat' => '0',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '0',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '0',
				'default_emailformat' => '1',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '2',
				'special' => '0815',
				'emailformat' => '',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '0',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '1',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0816',
				'emailformat' => '1',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '1',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '1',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '1',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '2',
				'special' => '0815',
				'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '0',
				'special' => '0815',
				'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '1',
				'special' => '0815',
				'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
		array(
			'existing_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '',
				'special' => '0815',
				'emailformat' => '0',
			),
			'entry_data'  => array(
				'email' => 'dummy-1@tester-net-vm.nil',
				'firstname' => 'Sam',
				'name' => 'Sample',
				'street' => 'Cutandrun 1',
				'zip_code' => '12345',
				'city' => 'Elsewhere',
				'country' => 'Germany',
				'gender' => '2',
				'special' => '0815',
				'emailformat' => '0',
			),
			'params'  => array(
				'show_gender' => '1',
				'show_special' => '1',
				'special_field_obligation' => '0',
				'show_emailformat' => '1',
				'default_emailformat' => '0',
			),
		),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $result_data_existing_subs    = array(
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0816',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0816',
			'emailformat' => '1',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '0',
			'special' => '0815',
			'emailformat' => '0',
		),
		array(
			'email' => 'dummy-1@tester-net-vm.nil',
			'firstname' => 'Sam',
			'name' => 'Sample',
			'street' => 'Cutandrun 1',
			'zip_code' => '12345',
			'city' => 'Elsewhere',
			'country' => 'Germany',
			'gender' => '1',
			'special' => '0815',
			'emailformat' => '0',
		),

	);



	// Backend stuff
	// used urls and links

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_shopper_fields   = "/administrator/index.php?option=com_virtuemart&view=userfields";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_orders           = "/administrator/index.php?option=com_virtuemart&view=orders";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_to_users            = "/administrator/index.php?option=com_virtuemart&view=user";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_name                  = "BwPostman Plugin Buyer2Subscriber";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $userfield_page_identifier    = "html/body/header/div[2]/h1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $userfield_page_header_text   = "Shopper Field List";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $userfield_published_col  = "//*[@id='editcell']/table/tbody/tr/td[6]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filter_field             = "//*[@id='search']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filter_go_button         = "//*[@id='filterbox']/table/tbody/tr/td/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filter_reset_button      = "//*[@id='filterbox']/table/tbody/tr/td/button[2]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filter_search_value      = "bw_";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_title          = "//*[@id='editcell']/table/tbody/tr[%s]/td[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_published      = "//*/td[6]/a/span[contains(@class, 'icon-publish')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_message        = "bw_newsletter_message";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_subscription   = "bw_newsletter_subscription";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_format         = "bw_newsletter_format";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_gender         = "bw_gender";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $shopper_field_special        = "bw_newsletter_additional";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_not_installed         = "There are no plugins installed matching your query.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $vm_order_table       = "virtuemart_orders";

	// check order

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $order_identifier = "nonreg_SamSampledummy-1@tester-n";

	// plugin edit tab mailinglists

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_tab_options               = "//*[@id='myTabTabs']/li/a[text()='Options']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_tab_mailinglists          = "//*[@id='myTabTabs']/li/a[text()='Mailinglists']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_message_identifier        = "//*[@id='jform_params_bw_register_message_option']";
}
