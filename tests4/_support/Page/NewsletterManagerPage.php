<?php
namespace Page;

/**
 * Class NewsletterManagerPage
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
class NewsletterManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $url      = '/administrator/index.php?option=com_bwpostman&view=newsletters';

	/**
	 * url of sending page
	 *
	 * @var string
	 *
	 * @since   2.4.0
	 */
	public static $sendUrl      = '/administrator/index.php?option=com_bwpostman&view=newsletters&task=startsending&layout=nl_send';

	/**
	 * @var integer
	 *
	 * @since   2.0.0
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
	 * @since   2.0.0
	 */
	public static $tab1 = "//*[@id='j-main-container']/div[2]/ul/li[1]/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab2 = "//*[@id='j-main-container']/div[2]/ul/li[2]/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $tab3 = "//*[@id='j-main-container']/div[2]/ul/li[3]/a";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $first_list_link          = "//*[@id='main-table']/tbody/tr[1]/td[3]/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $first_line_unpublished   = "//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-unpublish')]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $first_line_published     = "//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-publish')]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $first_list_entry_tab2    = "//*[@id='cb0']";

	/**
	 * Array of sorting criteria values for unsent newsletters
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_data_array  = array(
		'sort_criteria' => array(
			'attachment'    => 'Attachment',
			'subject'       => 'Subject',
			'description'   => 'Description',
			'modified_time' => 'Last modification date',
			'authors'       => 'Author',
			'campaign_id'   => 'Campaign',
			'is_template'   => 'Content Template',
			'id'            => 'ID',
		),

		'sort_criteria_select' => array(
			'attachment'    => 'Attachment',
			'subject'       => 'Subject',
			'description'   => 'Description',
			'modified_time' => 'Last modified',
			'authors'       => 'Author',
			'campaign_id'   => 'Campaign',
			'is_template'   => 'Content template',
			'id'            => 'ID',
		),

		'select_criteria' => array(
			'attachment'    => 'a.attachment',
			'subject'       => 'a.subject',
			'description'   => 'a.description',
			'modified_time' => 'a.modified_time',
			'authors'       => 'authors',
			'campaign_id'   => 'campaign_id',
			'is_template'   => 'a.is_template',
			'id'            => 'a.id',
		),
	);

	/**
	 * Array of criteria to select from database for unsent newsletters
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $query_criteria = array(
		'attachment'    => 'a.attachment',
		'subject'       => 'a.subject',
		'description'   => 'a.description',
		'modified_time' => 'a.modified_time',
		'is_template'   => 'a.is_template',
		'id'            => 'a.id',
	);

	// Filter authors

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $authors_col                  = "//*[@id='main-table']/tbody/*/td[6]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_authors_list_id       = "//*[@id='filter_authors']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_authors_list          = ".//*[@id='filter_authors']/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_1              = "BwPostmanAdmin";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_2              = "BwPostmanEditor";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_3              = "AdminTester";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_1_txt          = 'BwPostmanAdmin';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_2_txt          = 'BwPostmanEditor';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_author_3_txt          = 'AdminTester';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */

	// Filter campaign

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_campaign_list_id       = "//*[@id='filter_campaign_id']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_campaign_list          = ".//*[@id='filter_campaign_id_chzn']/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_campaign_none          = "- Select campaign -";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_campaign_without       = "- Without campaign -";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_campaign_cam           = "01 Kampagne 2 A";

	// Filter mailinglist

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_list_id   = "//*[@id='filter_mailinglists']";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_list      = ".//*[@id='filter_mailinglists_chzn']/a";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_1         = "01 Mailingliste 3 A";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_2         = "02 Mailingliste 6 A";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_3         = "02 Mailingliste 9 A";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_1_txt     = '01 Mailingliste 3 A';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_2_txt     = '02 Mailingliste 6 A';

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $filter_mailinglist_3_txt     = '02 Mailingliste 9 A';


	// Filter content template

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_list_id       = "//*[@id='filter_is_template']";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_list               = ".//*[@id='filter_is_template_chzn']/a";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_list_none          = "- Content Template -";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_list_yes           = "Yes";

	/**
	 * @var string
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_list_no           = "No";

	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */

	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			"Subject",
			"Description",
			"Subject & Description",
			"Search phrase in HTML version",
			"Search phrase in text version",
			"Search phrase in HTML version & text version",
		),
		'search_val'           => array("Test Newsletter single 1", "15", "About your home page"),
		// array of arrays: outer array per search value, inner arrays results per 'search by'
		'search_res'           => array(array(11, 0, 11, 0, 0, 0), array(2, 2, 2, 0, 0, 0), array(0, 0, 0, 5, 5, 5)),
	);


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $search_clear_val     = 'Newsletter for testing 1';


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $filter_cam_result   = array(
											'Template Gedicht 1',
											'Template Gedicht 3',
											'Test Newsletter single 15',
										);


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $filter_nocam_result = array(
											'Newsletter for testing 1',
											'Newsletter for testing 10',
											'Newsletter for testing 11',
											'Newsletter for testing 12',
											'Newsletter for testing 13',
											'Newsletter for testing 14',
											'Newsletter for testing 15',
											'Newsletter for testing 16',
											'Newsletter for testing 17',
											'Newsletter for testing 18',
											'Newsletter for testing 19',
											'Newsletter for testing 2',
											'Newsletter for testing 20',
											'Newsletter for testing 21',
											'Newsletter for testing 22',
											'Newsletter for testing 23',
											'Newsletter for testing 24',
											'Newsletter for testing 25',
											'Newsletter for testing 3',
											'Newsletter for testing 4',
										);


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $filter_sent_cam_result   = array(
		"Kopie von 'Kopie von 'Template Gedicht 1''",
		"Kopie von 'Template Gedicht 1'",
		"Test Newsletter 11.4.2015 23:30:35",
		"Test Newsletter 12.4.2015 10:32:6",
	);


	/**
	 * @var array
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_yes_result   = array(
		'Newsletter for testing 10',
		'Newsletter for testing 3',
		'Template Gedicht 3',
	);


	/**
	 * @var array
	 *
	 * @since   2.2.0
	 */
	public static $filter_is_template_no_result = array(
		'Newsletter for testing 1',
		'Newsletter for testing 11',
		'Newsletter for testing 12',
		'Newsletter for testing 13',
		'Newsletter for testing 14',
		'Newsletter for testing 15',
		'Newsletter for testing 16',
		'Newsletter for testing 17',
		'Newsletter for testing 18',
		'Newsletter for testing 19',
		'Newsletter for testing 2',
		'Newsletter for testing 20',
		'Newsletter for testing 21',
		'Newsletter for testing 22',
		'Newsletter for testing 23',
		'Newsletter for testing 24',
		'Newsletter for testing 25',
		'Newsletter for testing 4',
		'Newsletter for testing 5',
		'Newsletter for testing 6',
	);


	/**
	 * @var array
	 *
	 * @since 2.2.0
	 */
	public static $set_template_by_icon   = array(
		'is_template_button'     => "//*/table[@id='main-table']/tbody/tr[4]/td[8]/button",
		'is_template_result'     => "//*/table[@id='main-table']/tbody/tr[4]/td[8]/button[contains(@class, 'data-state-1')]",
		'is_not_template_button' => "//*/table[@id='main-table']/tbody/tr[4]/td[8]/button",
		'is_not_template_result' => "//*/table[@id='main-table']/tbody/tr[4]/td[8]/button[contains(@class, 'data-state-0')]",
	);

	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $search_sent_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			"Subject",
			"Description",
			"Subject & Description",
			"Search phrase in HTML version",
			"Search phrase in text version",
			"Search phrase in HTML version & text version",
		),
		'search_val'           => array("Kopie von", "3", "Die blaue Meise"),
		// array of arrays: outer array per search value, inner arrays results per 'search by'
		'search_res'           => array(array(3, 0, 3, 0, 0, 0), array(15, 6, 19, 20, 28, 31), array(0, 0, 0, 9, 2, 9)),
	);

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $search_sent_clear_val     = "Kopie von 'Kopie von 'Template Gedicht 1''";


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $publish_by_icon   = array(
		'publish_button'    => ".//*[@id='main-table']/tbody/tr[1]/td[8]/a",
		'publish_result'    => ".//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => ".//*[@id='main-table']/tbody/tr[1]/td[8]/a",
		'unpublish_result'  => ".//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-unpublish')]",
	);


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $pagination_data_array  = array(
		'p1_val1'              => "Newsletter for testing 1",
		'p1_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p1_val_last'          => "Newsletter for testing 18",
		'p1_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p2_val1'              => "Newsletter for testing 19",
		'p2_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p2_val_last'          => "Newsletter for testing 4",
		'p2_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p3_val1'              => "Newsletter for testing 5",
		'p3_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p3_val3'              => "Test Newsletter single 1",
		'p3_field3'            => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p_prev_val1'          => "Test Newsletter single 20",
		'p_prev_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p_prev_val_last'      => "Test Newsletter single 6",
		'p_prev_field_last'    => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p_last_val1'          => "Test Newsletter single 7",
		'p_last_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p_last_val_last'      => "Test Newsletter single 9",
		'p_last_field_last'    => ".//*[@id='main-table']/tbody/tr[3]/td[3]",
	);


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $pagination_sent_data_array  = array(
		'p1_val1'              => "Kopie von 'Kopie von 'Template Gedicht 1''",
		'p1_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p1_val_last'          => "Test Newsletter 11.4.2015 13:26:59",
		'p1_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p2_val1'              => "Test Newsletter 11.4.2015 18:28:23",
		'p2_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p2_val_last'          => "Test Newsletter 12.4.2015 10:32:6",
		'p2_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p3_val1'              => "Test Newsletter 12.4.2015 2:1:47",
		'p3_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p3_val3'              => " Test Newsletter 9.4.2015 22:52:14",
		'p3_field3'            => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p_prev_val1'          => "Test Newsletter 12.4.2015 2:1:47",
		'p_prev_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p_prev_val_last'      => "Test Newsletter 9.4.2015 22:52:14",
		'p_prev_field_last'    => ".//*[@id='main-table']/tbody/tr[10]/td[3]",

		'p_last_val1'          => "Test Newsletter 9.4.2015 23:5:25",
		'p_last_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
		'p_last_val_last'      => " Test Newsletter 9.4.2015 23:5:25",
		'p_last_field_last'    => ".//*[@id='main-table']/tbody/tr[1]/td[3]",
	);


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $arc_del_array    = array(
		'section'   => 'newsletter',
		'url'   => "/administrator/index.php?option=com_bwpostman&view=newsletters",
	);

	/**
	 * Array of sorting criteria values for sent newsletters
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sent_sort_data_array  = array(
		'sort_criteria' => array(
			'attachment'     => 'Attachment',
			'subject'        => 'Subject',
			'description'    => 'Description',
			'mailing_date'   => 'Mailing date',
			'authors'        => 'Author',
			'campaign_id'    => 'Campaign',
			'published'      => 'published',
			'publish_up'     => 'Published starts at',
			'publish_down'   => 'Published ends at',
			'id'             => 'ID',
		),

		'sort_criteria_select' => array(
			'attachment'    => 'Attachment',
			'subject'       => 'Subject',
			'description'   => 'Description',
			'mailing_date'  => 'Sending date',
			'authors'       => 'Author',
			'campaign_id'   => 'Campaign',
			'published'     => 'Status',
			'publish_up'    => 'Start published',
			'publish_down'  => 'End published',
			'id'            => 'ID',
		),

		'select_criteria' => array(
			'attachment'    => 'a.attachment',
			'subject'       => 'a.subject',
			'description'   => 'a.description',
			'mailing_date'  => 'a.mailing_date',
			'authors'       => 'authors',
			'campaign_id'   => 'campaign_id',
			'published'     => 'a.published',
			'publish_up'    => 'a.publish_up',
			'publish_down'  => 'a.publish_down',
			'id'            => 'a.id',
		),
	);

	/**
	 * Array of criteria to select from database for sent newsletters
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sent_query_criteria = array(
		'attachment'    => 'a.attachment',
		'subject'       => 'a.subject',
		'description'   => 'a.description',
		'mailing_date'  => 'a.mailing_date',
		'published'     => 'a.published',
		'publish_up'    => 'a.publish_up',
		'publish_down'  => 'a.publish_down',
		'id'            => 'a.id',
	);


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $sent_column_publish_up   = ".//*[@id='main-table']/tbody/tr[1]/td[9]/p[1]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $sent_column_publish_down = ".//*[@id='main-table']/tbody/tr[1]/td[9]/p[2]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $sent_column_description  = ".//*[@id='main-table']/tbody/tr[1]/td[4]";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $queue_warning_msg    = "An error occurred while sending the newsletters, please go Newsletters → Queue and revise the entries!";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $queue_cleared_msg    = "The queue has been cleared.";


	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $queue_sending_trials_col = ".//*[@id='main-table']/tbody/tr/td[5]";

	/**
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $queue_list_id            = ".//*[@id='main-table']/thead/tr/th[6]/a";


	/**
	 * @var array
	 *
	 * @since   2.0.0
	 */
	public static $pagination_queue_data_array  = array(
		'p1_val1'              => "a.kellner@tester-net.nil",
		'p1_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[4]",
		'p1_val_last'          => "cedrik.christensen@tester-net.nil",
		'p1_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[4]",

		'p2_val1'              => "claus.kuntz@tester-net.nil",
		'p2_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[4]",
		'p2_val_last'          => "helen.feil@tester-net.nil",
		'p2_field_last'        => ".//*[@id='main-table']/tbody/tr[10]/td[4]",

		'p3_val1'              => "hildegard.roesler@tester-net.nil",
		'p3_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[4]",
		'p3_val3'              => " j.ziegler@tester-net.nil",
		'p3_field3'            => ".//*[@id='main-table']/tbody/tr[10]/td[4]",

		'p_prev_val1'          => "maya.eich@tester-net.nil",
		'p_prev_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[4]",
		'p_prev_val_last'      => "riccardo.fritzsche@tester-net.nil",
		'p_prev_field_last'    => ".//*[@id='main-table']/tbody/tr[10]/td[4]",

		'p_last_val1'          => "ruth.higgins@tester-net.nil",
		'p_last_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[4]",
		'p_last_val_last'      => "v.steffen@tester-net.nil",
		'p_last_field_last'    => ".//*[@id='main-table']/tbody/tr[8]/td[4]",
	);

	/**
	 * @var string
	 *
	 * @since   2.4.0
	 */
	public static $sendLayout = "//*[@id='sendResult']";

	/**
	 * @var string
	 *
	 * @since   2.4.0
	 */
	public static $sendLayoutBack = "//*[@id='toolbar-back']";

}
