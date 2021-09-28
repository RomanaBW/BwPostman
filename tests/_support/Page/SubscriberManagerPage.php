<?php
namespace Page;

//use Codeception\Module\WebDriver;
//use Codeception\PHPUnit\Constraint\Page;

/**
 * Class SubscriberManagerPage
 *
 * @package Page
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
class SubscriberManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $url      = '/administrator/index.php?option=com_bwpostman&view=subscribers';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $section  = 'subscriber';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $wait_db  = 1;

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_confirmed   = "//*[@id='tab-confirmed']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_unconfirmed = "//*[@id='tab-unconfirmed']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_testers     = "//*[@id='tab-testrecipients']";


	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $confirmedMainTable   = "//table[@id='main-table-bw-confirmed']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $unconfirmedMainTable   = "//table[@id='main-table-bw-unconfirmed']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $testersMainTable   = "//table[@id='main-table-bw-testrecipients']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $first_list_link          = "//*[@id='main-table']/tbody/tr[1]/td[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $first_list_entry_tab2    = "//*[@id='cb0']";

	// search subscriber

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_for_list_id               = "filter_search_filter_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_for_list                  = "//*[@id='filter_search_filter_chzn']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_for_value                 = "//*[@id='filter_search_filter_chzn']/div/ul/li[contains(text(), '%s')]";

	/**
	 * Array of sorting criteria values for this page
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_data_array  = array(
		'sort_criteria' => array(
			'firstname'     => 'First name',
			'gender'        => 'Gender',
			'email'         => 'Email',
			'Email format'  => 'Email format',
			'user_id'       => 'Joomla! User-ID',
			'mailinglists'  => '# Mailinglists',
			'id'            => 'ID',
			'name'          => 'Last name'
		),

		'sort_criteria_select' => array(
			'firstname'     => 'First name',
			'gender'        => 'Gender',
			'email'         => 'Email',
			'Email format'  => 'Mail format',
			'user_id'       => 'Joomla! User-ID',
			'mailinglists'  => '# subscribed mailing lists',
			'id'            => 'ID',
			'name'          => 'Name'
		),

		'select_criteria' => array(
			'name'          => 'a.name',
			'firstname'     => 'a.firstname',
			'gender'        => 'a.gender',
			'email'         => 'a.email',
			'Email format'  => 'a.emailformat',
			'user_id'       => 'a.user_id',
			'mailinglists'  => 'mailinglists',
			'id'            => 'a.id',
		),
	);

	/**
	 * Array of criteria to select from database
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $query_criteria = array(
		'name'          => 'a.name',
		'firstname'     => 'a.firstname',
		'gender'        => 'a.gender',
		'email'         => 'a.email',
		'Email format'  => 'a.emailformat',
		'user_id'       => 'a.user_id',
		'mailinglists'  => 'mailinglists',
		'id'            => 'a.id',
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			"Last name",
			"First name",
			"First name and last name",
			"Email",
			"Name & Email",
		),
		'search_val'           => array("xx", "Andreas"),
		// array of arrays: outer array per search value, inner arrays per 'search by'
		'search_res'           => array(array(0, 0, 0, 0, 0), array(2, 1, 3, 3, 3)),
	);

	/**
	 * @var array
	 *
	 * @since 2.4.0
	 */
	public static $search_data_array_unconfirmed  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			"Last name",
			"First name",
			"First name and last name",
			"Email",
			"Name & Email",
		),
		'search_val'           => array("xx", "Tristan"),
		// array of arrays: outer array per search value, inner arrays per 'search by'
		'search_res'           => array(array(0, 0, 0, 0, 0), array(0, 2, 2, 1, 1)),
	);

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_clear_val     = 'Abbott';

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $search_clear_val_unconfirmed     = 'Atkins';

	// Filter mail format

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list_id       = "//*[@id='filter_emailformat']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list          = "//*[@id='filter_emailformat']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_none          = "Select email format";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text          = "0";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_html          = "1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text_column   = "//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/*/td[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text_text     = 'Text';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text_html     = 'HTML';

	// Filter mailinglist

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml_list_id       = "//*[@id='filter_mailinglist']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml_list          = "//*[@id='filter_mailinglist']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ml_select        = "04 Mailingliste 14 A";

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $filter_subs_result   = array(
											'c.abbott@tester-net.nil',
											'c.breidenbach@tester-net.nil',
											'a.daum@tester-net.nil',
											'erwin.haeberle@tester-net.nil',
											'i.lueck@tester-net.nil',
											's.otte@tester-net.nil',
											'nils.rhodes@tester-net.nil',
											'l.wunderlich@tester-net.nil',
											'lili.zech@tester-net.nil'
										);

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $ml_select_unconfirmed        = "01 Mailingliste 3 A";

	/**
	 * @var array
	 *
	 * @since 2.4.0
	 */
	public static $filter_subs_unconfirmed_result   = array(
		'm.augustin@tester-net.nil',
		'm.bailey@tester-net.nil',
		'katharina.euler@tester-net.nil',
		'n.halle@tester-net.nil',
		'mona.jaschke@tester-net.nil',
		'enrico.noetzel@tester-net.nil',
		'p.ochs@tester-net.nil',
		'jaqueline.roesler@tester-net.nil',
		'maren.tran@tester-net.nil',
		'lewin.underwood@tester-net.nil',
		'rebekka.vasquez@tester-net.nil',
		'v.zabel@tester-net.nil',
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $pagination_data_array  = array(
		'p1_val1'              => "Abbott",
		'p1_field1'            => "//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]",
		'p1_val_last'          => "Alexander",
		'p1_field_last'        => "//*[@id='main-table-bw-confirmed']/tbody/tr[10]/td[2]",

		'p2_val1'              => "Altmann",
		'p2_field1'            => "//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]",
		'p2_val_last'          => "Atkins",
		'p2_field_last'        => "//*[@id='main-table-bw-confirmed']/tbody/tr[10]/td[2]",

		'p3_val1'              => "Auer",
		'p3_field1'            => "//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]",
		'p3_val3'              => "Barrenbruegge",
		'p3_field3'            => "//*[@id='main-table-bw-confirmed']/tbody/tr[10]/td[2]",

		'p_prev_val1'          => "Willis",
		'p_prev_field1'        => "//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]",
		'p_prev_val_last'      => "Zabel",
		'p_prev_field_last'    => "//*[@id='main-table-bw-confirmed']/tbody/tr[10]/td[2]",

		'p_last_val1'          => "Zauner",
		'p_last_field1'        => "//*[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]",
		'p_last_val_last'      => "Zuschuss",
		'p_last_field_last'    => "//*[@id='main-table-bw-confirmed']/tbody/tr[8]/td[2]",
	);

	/**
	 * @var array
	 *
	 * @since 2.4.0
	 */
	public static $pagination_data_array_unconfirmed  = array(
		'p1_val1'              => "Atkins",
		'p1_field1'            => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[1]/td[2]",
		'p1_val_last'          => "Baierl",
		'p1_field_last'        => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[10]/td[2]",

		'p2_val1'              => "Baierl",
		'p2_field1'            => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[1]/td[2]",
		'p2_val_last'          => "Bartl",
		'p2_field_last'        => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[10]/td[2]",

		'p3_val1'              => "Barton",
		'p3_field1'            => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[1]/td[2]",
		'p3_val3'              => "Beier",
		'p3_field3'            => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[10]/td[2]",

		'p_prev_val1'          => "Vasquez",
		'p_prev_field1'        => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[1]/td[2]",
		'p_prev_val_last'      => "Waechter",
		'p_prev_field_last'    => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[10]/td[2]",

		'p_last_val1'          => "Wagener",
		'p_last_field1'        => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[1]/td[2]",
		'p_last_val_last'      => "Zabel",
		'p_last_field_last'    => "//*[@id='main-table-bw-unconfirmed']/tbody/tr[5]/td[2]",
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array    = array(
		'section'   => 'subscriber',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=subscribers',
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_button    = "//*[@id='fileformatcsv']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_button    = "//*[@id='fileformatxml']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_file    = "import_demo.csv";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_file    = "import_demo.xml";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_search_button = "//*[@id='importfile']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_delimiter = "//*[@id='delimiter_chzn']/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_separator = "//*[@id='enclosure_chzn']/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_caption   = "//*[@id='caption']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_button_further = "//*/input[@id='further']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_button_import  = "//*[@id='adminForm']/div/div/div/input[contains(@class, 'btn-success')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_legend_step_2 = "//*[@id='adminForm']/fieldset[2]/legend";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_legend_mls    = "//*[@id='adminForm']/fieldset[2]/div/div[2]/fieldset/div[1]/fieldset";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_legend_format = "//*[@id='adminForm']/fieldset[2]/div/div[3]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_cb_text_format  = "//*[@id='emailformat0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_cb_html_format  = "//*[@id='emailformat1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_cb_confirm_subs = "//*[@id='confirm']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_success_container = "//*[@id='import-success']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_field_0   = "Column_0 (name)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_field_1   = "Column_1 (firstname)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_field_2   = "Column_2 (email)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_field_3   = "Column_3 (emailformat)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_field_4   = "Column_4 (status)";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_field_0   = "Field_0 (name)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_field_1   = "Field_1 (firstname)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_field_2   = "Field_2 (email)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_field_3   = "Field_3 (emailformat)";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_field_4   = "Field_4 (status)";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $import_csv_subscribers   = array(
		array(
			'name' => 'Muster',
			'firstname' => 'Max',
			'email' => 'tester1@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Moritz',
			'email' => 'tester2@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Brunhilde',
			'email' => 'tester3@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Adelgunde',
			'email' => 'tester4@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Erika',
			'email' => 'tester5@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Eugen',
			'email' => 'tester6@boldt-services.de',
			'emailformat' => '0',
			'status' => '1',
		),
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $import_xml_subscribers   = array(
		array(
			'name' => 'Muster',
			'firstname' => 'Maximilian',
			'email' => 'tester7@boldt-services.de',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Emil',
			'email' => 'tester8@boldt-services.nul',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Hanni',
			'email' => 'tester9@boldt-services.nul',
			'emailformat' => '1',
			'status' => '1',
		),
		array(
			'name' => 'Muster',
			'firstname' => 'Nanni',
			'email' => 'tester10@boldt-services.nul',
			'emailformat' => '1',
			'status' => '1',
		),
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_mls_target    = "//*[@id='adminForm']/fieldset[2]/div/div[2]/fieldset/div[1]/fieldset/div[2]/input";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $import_msg_success   = "The import has successfully been completed.";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $exportPath   = "/%s/Downloads/";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_csv_confirmed   = "//*[@id='status1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_csv_unconfirmed = "//*[@id='status0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_csv_testers     = "//*[@id='status9']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_csv_unarchived = "//*[@id='archive0']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_csv_archived   = "//*[@id='archive1']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_legend_fields = "//*[@id='adminForm']/fieldset/div/div/div[5]/label";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subs_footer_div = "//*/div[contains(@class, 'bwpm-footer')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_button_up     = "//*[@id='adminForm']/fieldset/div/table/tbody/tr[5]/td[2]/span[1]/input";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_button_down   = "//*[@id='adminForm']/fieldset/div/table/tbody/tr[5]/td[2]/span[2]/input";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_button_remove = "//*[@id='adminForm']/fieldset/div/table/tbody/tr[5]/td[2]/span[3]/input";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $export_button_export = "//*[@id='adminForm']/fieldset/div/div/div[6]/div/input[contains(@class, 'btn-success')]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $export_popup_yes = "/html/body/form/fieldset/div/p[2]/input[contains(@class, 'btn-success')]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $export_popup_no = "/html/body/form/fieldset/div/p[2]/input[contains(@class, 'btn-secondary')]";

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $subs_c_na_f = '"85";"0";"Otte";"Stephan";"s.otte@tester-net.nil"';

		/**
		 * @var string
		 *
		 * @since 2.2.0
		 */

	public static $subs_c_a_f = '"172";"0";"Zellner";"Janin";"janin.zellner@tester-net.nil"';
		/**
		 * @var string
		 *
		 * @since 2.2.0
		 */
	public static $subs_u_na_f = '"23";"0";"Junker";"Dustin";"d.junker@tester-net.nil"';

		/**
		 * @var string
		 *
		 * @since 2.2.0
		 */
	public static $subs_u_a_f = '"203";"0";"Steinmetz";"Bruno";"bruno.steinmetz@tester-net.nil"';

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $subs_c_na = '"7";"0";"Barth";"Rafael";"r.barth@tester-net.nil"';

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $subs_c_a = '"14";"0";"Yildiz";"Sebastian";"s.yildiz@tester-net.nil"';

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $subs_u_na = '"13";"0";"Oppermann";"Cedric";"cedric.oppermann@tester-net.nil"';

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $subs_u_a = '"119";"0";"Vogt";"Matthies";"m.vogt@tester-net.nil"';

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $exportFieldList = "//*[@id='export_fields']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $exportFieldAssetId = "asset_id";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $exportFieldRemoveButton = "//*[@name='removebutton']";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $tableSelectField = "//*/table[@id='main-table-bw-confirmed']/tbody/tr[%s]/td[1]";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $tableFirstnameField = "//*/table[@id='main-table-bw-confirmed']/tbody/tr[%s]/td[3]";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchModalBody = "//*[@id='collapseModal']/div/div/div[@class='modal-body']";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchModalSelectList = ".//*[@id='batch_mailinglist_id_chzn']";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchModalTask = "//*[@id='batch-task']/div/div[%s]/input";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchMlListId       = "batch_mailinglist_id_chzn";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchMlList          = "//*[@id='batch-mailinglist-id']";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchMlSelectNew        = "1";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchMlSelectOld        = "4";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchMlSelectAlready        = "20";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchProcess        = "//*[@class='modal-footer']/button[2]";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessSubscribe        = "Batch processing „Add subscribers to mailing list with ID 1“ finished. 2 subscribers added.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessSubscribeAlready        = "Batch processing „Add subscribers to mailing list with ID 4“ finished. One subscriber added. There was one subscriber, that already was subscribed to this mailing list. Subscriber skipped.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessUnsubscribe        = "Batch processing „Remove subscribers from mailing list with ID 1“ finished. 2 subscribers removed.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessUnsubscribeOne        = "Batch processing „Remove subscribers from mailing list with ID 4“ finished. One subscriber removed.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessUnsubscribeNo        = "Batch processing „Remove subscribers from mailing list with ID 1“ finished. 2 subscribers removed. There was one subscriber, that was not subscribed to this mailing list. Subscriber skipped.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessMoveForward        = "Batch processing „Add subscribers to mailing list with ID 1“ finished. 2 subscribers added.\nBatch processing „Remove subscribers from mailing list with ID 4“ finished. 2 subscribers removed.";

	/**
	 * @var string
	 *
	 * @since 3.1.2
	 */
	public static $batchSuccessMoveBack        = "Batch processing „Add subscribers to mailing list with ID 4“ finished. 2 subscribers added.\nBatch processing „Remove subscribers from mailing list with ID 1“ finished. 2 subscribers removed.";


	/**
	 * @param \AcceptanceTester $I
	 * @param boolean           $activated
	 *
	 * @since 2.0.0
	 */
	public static function gotoSubscribersListTab(\AcceptanceTester $I, $activated)
	{
		if ($activated)
		{
			$tab = self::$tab_confirmed;
		}
		else
		{
			$tab = self::$tab_unconfirmed;
		}

		$I->amOnPage(self::$url);
		$I->see('Subscribers', Generals::$pageTitle);
		$I->clickAndWait($tab, 1);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $search_value
	 * @param string            $search_for
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	public static function filterForSubscriber(\AcceptanceTester $I, $search_value, $search_for)
	{
		$I->fillField(Generals::$search_field, $search_value);

		$I->click(Generals::$filterOptionsSwitcher);
		$I->click(Generals::$search_list);
		$I->selectOption(Generals::$search_list, $search_for);
	}
}
