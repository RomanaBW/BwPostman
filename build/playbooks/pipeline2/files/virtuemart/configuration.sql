SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `J4_test`
--

-- --------------------------------------------------------

--
-- Daten für Tabelle `jos_virtuemart_configs`
--

REPLACE INTO `jos_virtuemart_configs` (`virtuemart_config_id`, `config`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 'useSSL=\"0\"|dangeroustools=\"0\"|debug_enable=\"none\"|vmdev=\"none\"|google_jquery=\"0\"|multix=\"none\"|usefancy=\"1\"|jchosen=\"1\"|enableEnglish=\"1\"|shop_is_offline=\"0\"|offline_message=\"Our Shop is currently down for maintenance. Please check back again soon.\"|use_as_catalog=\"0\"|currency_converter_module=\"convertECB.php\"|order_mail_html=\"1\"|useVendorEmail=\"0\"|pdf_button_enable=\"1\"|show_emailfriend=\"0\"|show_printicon=\"1\"|show_out_of_stock_products=\"1\"|ask_captcha=\"1\"|coupons_enable=\"1\"|show_uncat_products=\"0\"|show_uncat_child_products=\"0\"|show_unpub_cat_products=\"1\"|coupons_default_expire=\"1,M\"|weight_unit_default=\"KG\"|lwh_unit_default=\"M\"|list_limit=\"30\"|showReviewFor=\"all\"|reviewMode=\"bought\"|showRatingFor=\"all\"|ratingMode=\"bought\"|reviews_autopublish=\"1\"|reviews_minimum_comment_length=\"0\"|reviews_maximum_comment_length=\"2000\"|product_navigation=\"1\"|display_stock=\"1\"|vmtemplate=\"\"|category_template=\"0\"|showcategory=\"1\"|categorylayout=\"default\"|categories_per_row=\"3\"|productlayout=\"default\"|products_per_row=\"3\"|llimit_init_FE=\"24\"|vmlayout=\"default\"|show_store_desc=\"1\"|show_categories=\"1\"|featured_rows=\"1\"|topten=\"1\"|topten_rows=\"1\"|recent=\"1\"|recent_rows=\"1\"|latest=\"1\"|latest_rows=\"1\"|legacylayouts=\"0\"|assets_general_path=\"components\\/com_virtuemart\\/assets\\/\"|media_category_path=\"images\\/virtuemart\\/category\\/\"|media_product_path=\"images\\/virtuemart\\/product\\/\"|media_manufacturer_path=\"images\\/virtuemart\\/manufacturer\\/\"|media_vendor_path=\"images\\/virtuemart\\/vendor\\/\"|forSale_path_thumb=\"images\\/virtuemart\\/forSale\\/resized\\/\"|img_resize_enable=\"1\"|img_width=\"0\"|img_height=\"90\"|no_image_set=\"noimage_new.gif\"|no_image_found=\"warning.png\"|browse_orderby_field=\"pc.ordering,product_name\"|browse_cat_orderby_field=\"c.ordering,category_name\"|browse_orderby_fields=[\"product_name\",\"`p`.product_sku\",\"mf_name\",\"pc.ordering\"]|browse_search_fields=[\"product_name\",\"`p`.product_sku\",\"product_s_desc\",\"mf_name\"]|askprice=\"1\"|roundindig=\"1\"|show_prices=\"1\"|price_show_packaging_pricelabel=\"0\"|show_tax=\"1\"|basePrice=\"0\"|basePriceText=\"1\"|basePriceRounding=\"-1\"|variantModification=\"0\"|variantModificationText=\"1\"|variantModificationRounding=\"-1\"|basePriceVariant=\"1\"|basePriceVariantText=\"1\"|basePriceVariantRounding=\"-1\"|basePriceWithTax=\"0\"|basePriceWithTaxText=\"1\"|basePriceWithTaxRounding=\"-1\"|discountedPriceWithoutTax=\"1\"|discountedPriceWithoutTaxText=\"1\"|discountedPriceWithoutTaxRounding=\"-1\"|salesPriceWithDiscount=\"0\"|salesPriceWithDiscountText=\"1\"|salesPriceWithDiscountRounding=\"-1\"|salesPrice=\"1\"|salesPriceText=\"1\"|salesPriceRounding=\"-1\"|priceWithoutTax=\"1\"|priceWithoutTaxText=\"1\"|priceWithoutTaxRounding=\"-1\"|discountAmount=\"1\"|discountAmountText=\"1\"|discountAmountRounding=\"-1\"|taxAmount=\"1\"|taxAmountText=\"1\"|taxAmountRounding=\"-1\"|unitPrice=\"1\"|unitPriceText=\"1\"|unitPriceRounding=\"-1\"|addtocart_popup=\"1\"|check_stock=\"0\"|automatic_payment=\"0\"|automatic_shipment=\"0\"|oncheckout_opc=\"1\"|oncheckout_ajax=\"1\"|oncheckout_show_legal_info=\"1\"|oncheckout_show_register=\"0\"|oncheckout_show_steps=\"0\"|oncheckout_show_register_text=\"COM_VIRTUEMART_ONCHECKOUT_DEFAULT_TEXT_REGISTER\"|oncheckout_show_images=\"1\"|inv_os=[\"C\"]|email_os_s=[\"U\",\"C\",\"X\",\"R\",\"S\"]|email_os_v=[\"U\",\"C\",\"X\",\"R\"]|seo_disabled=\"0\"|seo_translate=\"0\"|seo_use_id=\"0\"|enable_content_plugin=\"0\"|reg_captcha=\"0\"|handle_404=\"1\"|member_access_number=\"\"|vmDefLang=\"\"|prodOnlyWLang=\"0\"|vm_lfbs=\"\"|ReInjectJLanguage=\"0\"|backendTemplate=\"0\"|debug_enable_methods=\"0\"|debug_enable_router=\"0\"|debug_Sql=\"0\"|revproxvar=\"\"|multixcart=\"0\"|optimisedProductSql=\"1\"|optimisedCalcSql=\"1\"|optimisedCatSql=\"1\"|invoiceInUserLang=\"0\"|debug_mail=\"0\"|addVendorEmail=\"\"|email_sf_s=[\"email\"]|attach=\"\"|attach_os=[\"U\",\"C\",\"X\",\"R\"]|norm_units=\"KG,100G,M,SM,CUBM,L,100ML,P\"|pdf_icon=\"0\"|recommend_unauth=\"0\"|ask_question=\"0\"|asks_minimum_comment_length=\"50\"|asks_maximum_comment_length=\"2000\"|cp_rm=[\"C\"]|show_pcustoms=\"1\"|show_subcat_products=\"0\"|show_uncat_parent_products=\"0\"|cat_productdetails=\"0\"|latest_products_days=\"7\"|latest_products_orderBy=\"created_on\"|lstockmail=\"0\"|stockhandle_products=\"0\"|stockhandle=\"none\"|rised_availability=\"\"|image=\"\"|reviews_languagefilter=\"0\"|vm_num_ratings_show=\"3\"|rr_os=[\"C\"]|showcategory_desc=\"1\"|showsearch=\"1\"|ProductGroupsSequence=\"featured, discontinued, latest, topten, recent\"|showproducts=\"1\"|omitLoaded=\"1\"|show_manufacturers=\"1\"|manufacturer_per_row=\"3\"|featured=\"1\"|omitLoaded_featured=\"1\"|discontinued=\"1\"|discontinued_rows=\"1\"|omitLoaded_discontinued=\"1\"|omitLoaded_topten=\"1\"|omitLoaded_recent=\"1\"|omitLoaded_latest=\"1\"|bootstrap=\"\"|categorytemplate=\"\"|cartlayout=\"default\"|productsublayout=\"\"|lazyLoad=\"1\"|useLayoutOverrides=\"1\"|css=\"1\"|jquery_framework=\"1\"|jquery=\"1\"|jprice=\"1\"|jsite=\"1\"|jdynupdate=\"1\"|ajax_category=\"0\"|homepage_categories_per_row=\"3\"|homepage_products_per_row=\"3\"|add_img_main=\"0\"|img_width_full=\"\"|img_height_full=\"\"|forSale_path=\"\\/var\\/www\\/vmfiles\\/\"|mediaLimit=\"20\"|llimit_init_BE=\"30\"|pagseq=\"\"|pagseq_1=\"\"|pagseq_2=\"\"|pagseq_3=\"\"|pagseq_4=\"\"|pagseq_5=\"\"|vm_prices_info_tax=\"0\"|vm_prices_info_delivery=\"0\"|rappenrundung=\"0\"|cVarswT=\"1\"|pricesbyCurrency=\"0\"|price_orderby=\"DESC\"|discountedPriceWithoutTaxTt=\"0\"|discountedPriceWithoutTaxTtText=\"0\"|discountedPriceWithoutTaxTtRounding=\"-1\"|priceWithoutTaxTt=\"0\"|priceWithoutTaxTtText=\"0\"|priceWithoutTaxTtRounding=\"-1\"|taxAmountTt=\"0\"|taxAmountTtText=\"0\"|taxAmountTtRounding=\"-1\"|salesPriceTt=\"0\"|salesPriceTtText=\"0\"|salesPriceTtRounding=\"-1\"|discountAmountTt=\"0\"|discountAmountTtText=\"0\"|discountAmountTtRounding=\"-1\"|popup_rel=\"1\"|set_automatic_shipment=\"2\"|set_automatic_payment=\"1\"|radicalShipPaymentVat=\"1\"|agree_to_tos_onorder=\"1\"|oncheckout_change_shopper=\"1\"|del_date_type=\"m\"|oncheckout_only_registered=\"0\"|ordertracking=\"guests\"|prd_brws_orderby_dir=\"ASC\"|cat_brws_orderby_dir=\"ASC\"|feed_latest_published=\"0\"|feed_latest_nb=\"5\"|feed_topten_published=\"0\"|feed_topten_nb=\"5\"|feed_featured_published=\"0\"|feed_featured_nb=\"5\"|feed_home_show_images=\"1\"|feed_home_show_prices=\"1\"|feed_home_show_description=\"0\"|feed_home_description_type=\"product_s_desc\"|feed_home_max_text_length=\"500\"|feed_cat_published=\"0\"|feed_cat_show_images=\"0\"|feed_cat_show_prices=\"0\"|feed_cat_show_description=\"0\"|feed_cat_description_type=\"product_s_desc\"|feed_cat_max_text_length=\"500\"|use_seo_suffix=\"1\"|seo_sufix=\"-detail\"|transliterateSlugs=\"0\"|seo_full=\"1\"|router_by_menu=\"0\"|sef_for_cart_links=\"1\"|UseCachegetChildCategoryList=\"1\"|useCacheVmGetCategoryRoute=\"1\"|UseCachegetOrderByList=\"1\"|task=\"apply\"|option=\"com_virtuemart\"|view=\"config\"|150d7ec1d098dd1000131ed9ee080642=\"1\"|active_languages=[\"en-GB\"]', NULL, 0, '2021-12-10 05:39:47', 15, NULL, 0);

--
-- Daten für Tabelle `jos_virtuemart_userfields`
--

REPLACE INTO `jos_virtuemart_userfields` (`virtuemart_userfield_id`, `virtuemart_vendor_id`, `userfield_jplugin_id`, `name`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `cols`, `rows`, `value`, `default`, `placeholder`, `registration`, `shipment`, `account`, `cart`, `readonly`, `calculated`, `sys`, `userfield_params`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(5, 0, 0, 'email', 'COM_VIRTUEMART_REGISTER_EMAIL', '', 'emailaddress', 100, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, NULL, 4, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(6, 0, 0, 'name', 'COM_VIRTUEMART_USER_DISPLAYED_NAME', '', 'text', 400, 30, 1, 0, 0, '', NULL, NULL, 1, 0, 1, 0, 0, 0, 1, '', 8, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(7, 0, 0, 'username', 'COM_VIRTUEMART_USERNAME', '', 'text', 150, 30, 1, 0, 0, '', NULL, NULL, 1, 0, 1, 0, 0, 0, 1, '', 6, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(8, 0, 0, 'password', 'COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1', '', 'password', 100, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, NULL, 10, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(9, 0, 0, 'password2', 'COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_2', '', 'password', 100, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, NULL, 12, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(15, 0, 0, 'agreed', 'COM_VIRTUEMART_I_AGREE_TO_TOS', '', 'checkbox', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 1, NULL, 13, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(16, 0, 0, 'tos', 'COM_VIRTUEMART_STORE_FORM_TOS', '', 'custom', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 1, 0, 0, 1, NULL, 14, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(17, 0, 0, 'customer_note', 'COM_VIRTUEMART_CNOTES_CART', '', 'textarea', 2500, NULL, 0, 60, 1, NULL, NULL, NULL, 0, 0, 0, 1, 0, 0, 1, NULL, 13, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(20, 0, 0, 'address_type_name', 'COM_VIRTUEMART_USER_FORM_ADDRESS_LABEL', '', 'text', 32, 30, 1, NULL, NULL, NULL, 'COM_VIRTUEMART_USER_FORM_ST_LABEL', NULL, 0, 1, 0, 0, 0, 0, 1, NULL, 16, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(21, 0, 0, 'delimiter_billto', 'COM_VIRTUEMART_USER_FORM_BILLTO_LBL', '', 'delimiter', 25, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, NULL, 18, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(22, 0, 0, 'company', 'COM_VIRTUEMART_SHOPPER_FORM_COMPANY_NAME', '', 'text', 64, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 20, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(23, 0, 0, 'title', 'COM_VIRTUEMART_SHOPPER_FORM_TITLE', '', 'select', 0, 210, 0, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, NULL, 22, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(24, 0, 0, 'first_name', 'COM_VIRTUEMART_SHOPPER_FORM_FIRST_NAME', '', 'text', 32, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 24, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(25, 0, 0, 'middle_name', 'COM_VIRTUEMART_SHOPPER_FORM_MIDDLE_NAME', '', 'text', 32, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 26, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(26, 0, 0, 'last_name', 'COM_VIRTUEMART_SHOPPER_FORM_LAST_NAME', '', 'text', 32, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 28, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(27, 0, 0, 'address_1', 'COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_1', '', 'text', 64, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 30, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(28, 0, 0, 'address_2', 'COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_2', '', 'text', 64, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 32, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(29, 0, 0, 'zip', 'COM_VIRTUEMART_SHOPPER_FORM_ZIP', '', 'text', 32, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 34, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(35, 0, 0, 'city', 'COM_VIRTUEMART_SHOPPER_FORM_CITY', '', 'text', 32, 30, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 36, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(36, 0, 0, 'virtuemart_country_id', 'COM_VIRTUEMART_SHOPPER_FORM_COUNTRY', '', 'select', 0, 210, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 38, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(37, 0, 0, 'virtuemart_state_id', 'COM_VIRTUEMART_SHOPPER_FORM_STATE', '', 'select', 0, 210, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 40, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(38, 0, 0, 'phone_1', 'COM_VIRTUEMART_SHOPPER_FORM_PHONE', '', 'text', 32, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 42, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(39, 0, 0, 'phone_2', 'COM_VIRTUEMART_SHOPPER_FORM_PHONE2', '', 'text', 32, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 44, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(40, 0, 0, 'fax', 'COM_VIRTUEMART_SHOPPER_FORM_FAX', '', 'text', 32, 30, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0, 0, 1, NULL, 46, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(41, 0, 0, 'delimiter_sendregistration', 'COM_VIRTUEMART_BUTTON_SEND_REG', '', 'delimiter', 25, 30, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, NULL, 2, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(42, 0, 0, 'delimiter_userinfo', 'COM_VIRTUEMART_ORDER_PRINT_CUST_INFO_LBL', '', 'delimiter', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 0, NULL, 14, 0, 1, NULL, 0, NULL, 0, NULL, 0),
	(50, 0, 0, 'tax_exemption_number', 'COM_VIRTUEMART_SHOPPER_FORM_TAXEXEMPTION_NBR', 'Vendors can set here a tax exemption number for a shopper. This field is only changeable by administrators.', 'text', 10, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, 0, 0, NULL, 48, 0, 0, NULL, 0, NULL, 0, NULL, 0),
	(51, 0, 0, 'tax_usage_type', 'COM_VIRTUEMART_SHOPPER_FORM_TAX_USAGE', 'Federal, national, educational, public, or similar often get a special tax. This field is only writable by administrators.', 'select', 0, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 1, 1, 0, 0, 0, NULL, 50, 0, 0, NULL, 0, NULL, 0, NULL, 0);

--
-- Daten für Tabelle `jos_virtuemart_userinfos`
--

REPLACE INTO `jos_virtuemart_userinfos` (`virtuemart_userinfo_id`, `virtuemart_user_id`, `address_type`, `address_type_name`, `company`, `title`, `last_name`, `first_name`, `middle_name`, `phone_1`, `phone_2`, `fax`, `address_1`, `address_2`, `city`, `virtuemart_state_id`, `virtuemart_country_id`, `zip`, `agreed`, `tos`, `customer_note`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 15, 'BT', '', 'Boldt Webservice TestShop', 'Mrs', 'Tester', 'Admin', '', '555-555-555', '', '', 'Nichtswieweg 1', '', 'Irgendwo', 0, 81, '73079', 0, 0, '', '2021-12-09 13:01:09', 15, '2021-12-09 13:01:09', 15, NULL, 0);

--
-- Daten für Tabelle `jos_virtuemart_vendors`
--

REPLACE INTO `jos_virtuemart_vendors` (`virtuemart_vendor_id`, `vendor_name`, `vendor_currency`, `vendor_accepted_currencies`, `vendor_params`, `metarobot`, `metaauthor`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 'Boldt Webservice TestShop', 47, '52,26,47,144', 'max_cats_per_product=-1|max_products=-1|max_customers=-1|force_product_pattern=-1|vendor_min_pov=\"0\"|vendor_min_poq=1|vendor_freeshipment=0|vendor_address_format=\"\"|vendor_date_format=\"\"|vendor_letter_format=\"A4\"|vendor_letter_orientation=\"P\"|vendor_letter_margin_top=\"55\"|vendor_letter_margin_left=\"25\"|vendor_letter_margin_right=\"25\"|vendor_letter_margin_bottom=\"25\"|vendor_letter_margin_header=\"20\"|vendor_letter_margin_footer=\"20\"|vendor_letter_font=\"helvetica\"|vendor_letter_font_size=\"8\"|vendor_letter_header_font_size=\"7\"|vendor_letter_footer_font_size=\"6\"|vendor_letter_header=\"1\"|vendor_letter_header_line=\"1\"|vendor_letter_header_line_color=\"#000000\"|vendor_letter_header_image=\"1\"|vendor_letter_header_imagesize=\"60\"|vendor_letter_header_cell_height_ratio=\"1\"|vendor_letter_footer=\"1\"|vendor_letter_footer_line=\"1\"|vendor_letter_footer_line_color=\"#000000\"|vendor_letter_footer_cell_height_ratio=\"1\"|vendor_letter_add_tos=\"0\"|vendor_letter_add_tos_newpage=\"1\"|vendor_letter_for_product_pdf=\"0\"|vendor_mail_width=640|vendor_mail_header=1|vendor_mail_tos=1|vendor_mail_logo=1|vendor_mail_logo_width=200|vendor_mail_font=\"helvetica\"|vendor_mail_header_font_size=11|vendor_mail_font_size=12|vendor_mail_footer_font_size=10|', '', '', NULL, 0, '2021-12-10 05:12:30', 15, NULL, 0);

--
-- Daten für Tabelle `jos_virtuemart_vendors_en_gb`
--

REPLACE INTO `jos_virtuemart_vendors_en_gb` (`virtuemart_vendor_id`, `vendor_store_desc`, `vendor_terms_of_service`, `vendor_legal_info`, `vendor_letter_css`, `vendor_letter_header_html`, `vendor_letter_footer_html`, `vendor_store_name`, `vendor_phone`, `vendor_url`, `metadesc`, `metakey`, `customtitle`, `vendor_invoice_free1`, `vendor_invoice_free2`, `vendor_mail_free1`, `vendor_mail_free2`, `vendor_mail_css`, `slug`) VALUES
	(1, '<p>Welcome to VirtueMart the ecommerce managment system. The sample data give you a good insight of the possibilities with VirtueMart. The product description is directly the manual to configure the demonstrated features.</p>\r\n<p>You see here the store description used to describe your store. Check it out!</p>\r\n<p>We were established in 1869 in a time when getting good clothes was expensive, but the quality was good. Now that only a select few of those authentic clothes survive, we have dedicated this store to bringing the experience alive for collectors and master carrier everywhere.</p>', '<h5>This is a demo store. Your orders will not proceed. You have not configured any terms of service yet. Click <a href=\"administrator/index.php?option=com_virtuemart&amp;view=user&amp;task=editshop\">here</a> to change this text.</h5>', '<p>VAT-ID: XYZ-DEMO<br />Reg.Nr: DEMONUMBER</p>', '.vmdoc-header { }\r\n.vmdoc-footer { }\r\n', '<h1>{vm:vendorname}</h1>\r\n<p>{vm:vendoraddress}</p>', '<p>{vm:vendorlegalinfo}<br />Page {vm:pagenum}/{vm:pagecount}</p>', 'VirtueMart 3 Sample store', '', 'https://www.j4-test.nil/', '', '', '', '', '', '', '', '', 'virtuemart-3-sample-store');


--
-- Daten für Tabelle `jos_virtuemart_shipmentmethods`
--

REPLACE INTO `jos_virtuemart_shipmentmethods` (`virtuemart_shipmentmethod_id`, `virtuemart_vendor_id`, `shipment_jplugin_id`, `shipment_element`, `shipment_params`, `currency_id`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 1056, 'weight_countries', 'display_color=\"\"|', 0, 1, 0, 1, '2021-12-10 23:34:43', 15, '2021-12-10 23:34:43', 15, NULL, 0),
	(2, 1, 1056, 'weight_countries', 'shipment_logos=\"\"|show_on_pdetails=\"0\"|zip_start=\"\"|zip_stop=\"\"|weight_start=\"\"|weight_stop=\"\"|weight_unit=\"KG\"|nbproducts_start=0|nbproducts_stop=0|shipment_cost=0|package_fee=0|tax_id=\"0\"|free_shipment=\"\"|categories=\"\"|blocking_categories=\"\"|countries=\"\"|blocking_countries=\"\"|min_amount=0|max_amount=0|virtuemart_shipmentmethod_ids=\"\"|byCoupon=\"0\"|couponCode=\"\"|display_color=\"#000000\"|', 47, 0, 0, 1, '2021-12-10 23:36:20', 15, '2021-12-10 23:36:30', 15, NULL, 0);

--
-- Daten für Tabelle `jos_virtuemart_shipmentmethods_en_gb`
--

REPLACE INTO `jos_virtuemart_shipmentmethods_en_gb` (`virtuemart_shipmentmethod_id`, `shipment_name`, `shipment_desc`, `slug`) VALUES
	(1, 'Self pick-up', '', 'self-pick-up'),
	(2, 'Default', '', 'default');

--
-- Daten für Tabelle `jos_virtuemart_shipment_plg_weight_countries`
--

REPLACE INTO `jos_virtuemart_shipment_plg_weight_countries` (`id`, `virtuemart_order_id`, `order_number`, `virtuemart_shipmentmethod_id`, `shipment_name`, `order_weight`, `shipment_weight_unit`, `shipment_cost`, `shipment_package_fee`, `tax_id`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
	(1, 1, 'URQY03', 2, '<span class=\"vmshipment_name\">Default</span>', '0.3000', 'KG', '0.00', '0.00', 0, '2021-12-10 23:36:59', 0, '2021-12-10 23:36:59', 0, NULL, 0);

UPDATE `jos_virtuemart_vmusers` SET `virtuemart_vendor_id` = 1, `user_is_vendor` = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



