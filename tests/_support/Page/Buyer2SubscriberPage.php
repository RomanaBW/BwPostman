<?php
namespace Page;

/**
 * Class Buyer2SubscribePage
 *
 * @package Buyer Subscribe Plugin
 * @copyright (C) 2016-2017 Boldt Webservice <forum@boldt-webservice.de>
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
class Buyer2SubscriberPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	// Frontend stuff
	// used urls and links
	public static $link_to_product      = "/index.php/en/shop/headpiece/cap-baseball-detail";
	public static $link_to_cart         = "/index.php/en/shop/cart";
	public static $link_to_editAddress  = "/index.php/en/shop/user/editaddresscartBT";

	public static $button_add_to_cart       = ".//*[@id='content']/div[3]/div[5]/div[2]/div/div[4]/form/div[2]/span[3]/input";
	public static $button_tos               = ".//*[@id='tos']";
	public static $button_check_out_now     = ".//*[@id='checkoutFormSubmit']";

	public static $button_text_checkout     = "Check Out Now";
	public static $button_text_purchase     = "Confirm Purchase";
	public static $thank_you_page           = "Thank you for your order!";

	public static $link_in_popup_show_cart   = ".//*[@id='fancybox-content']/div/a[2]";

	// billto field identifiers
	public static $billto_identifier_email           = ".//*[@id='email_field']";
	public static $billto_identifier_firstname       = ".//*[@id='first_name_field']";
	public static $billto_identifier_lastname        = ".//*[@id='last_name_field']";
	public static $billto_identifier_street          = ".//*[@id='address_1_field']";
	public static $billto_identifier_zip_code        = ".//*[@id='zip_field']";
	public static $billto_identifier_city            = ".//*[@id='city_field']";
	public static $billto_identifier_country_select  = ".//*[@id='virtuemart_country_id_field_chzn']/a";
	public static $billto_identifier_country_value   = ".//*[@id='virtuemart_country_id_field_chzn_o_81']";
	public static $billto_identifier_save            = ".//*[@id='userForm']/div/button[1]";

	// subscriber field identifiers
	public static $buyer_identifier_subs_select      = ".//*[@id='bw_newsletter_subscription_chzn']/a";

	public static $buyer_identifier_format_select    = ".//*[@id='bw_newsletter_format_chzn']/a";

	public static $buyer_identifier_gender_select    = ".//*[@id='bw_gender_chzn']/a";

	public static $buyer_identifier_special          = ".//*[@id='bw_newsletter_additional_field']";

	// buyer field values
	public static $buyer_null_value         = "NO_NAME_AVAILABLE";

	public static $buyer_value_email        = "dummy-1@tester-net-vm.nil";
	public static $buyer_value_firstname    = "Sam";
	public static $buyer_value_lastname     = "Sample";
	public static $buyer_value_street       = "Nichtswieweg 1";
	public static $buyer_value_zip_code     = "12345";
	public static $buyer_value_city         = "Elsewhere";
	public static $buyer_value_country      = "Germany";
	public static $buyer_value_special      = "0815";

	public static $buyer_value_name2        = "Sedlmeier";
	public static $buyer_value_firstname2   = "Andre";
	public static $buyer_value_special2     = "0816";

	public static $buyer_value2_name        = "Abbott";
	public static $buyer_value2_firstname   = "Chiara";


	// Backend stuff
	// used urls and links
	public static $link_to_shopperfields    = "/administrator/index.php?option=com_virtuemart&view=userfields";
	public static $link_to_orders           = "/administrator/index.php?option=com_virtuemart&view=orders";
	public static $link_to_users            = "/administrator/index.php?option=com_virtuemart&view=user";

	public static $filter_field             = ".//*[@id='search']";
	public static $filter_go_button         = ".//*[@id='filterbox']/table/tbody/tr/td/button[1]";
	public static $filter_reset_button      = ".//*[@id='filterbox']/table/tbody/tr/td/button[2]";

	public static $filter_search_value      = "bw_";

	public static $shopper_field_message        = "bw_newsletter_message";
	public static $shopper_field_subscription   = "bw_newsletter_subscription";
	public static $shopper_field_format         = "bw_newsletter_format";
	public static $shopper_field_gender         = "bw_gender";
	public static $shopper_field_special        = "bw_newsletter_additional";

	public static $shopper_field_published      = ".//*[@id='editcell']/table/tbody/tr[%s]/td[6]/a/span[contains(@class, 'icon-publish')]";
}
