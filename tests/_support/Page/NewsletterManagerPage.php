<?php
namespace Page;

/**
 * Class NewsletterManagerPage
 *
 * @package Page
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
//	public static $section  = 'newsletter';
	public static $wait_db  = 1;

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

//	public static $tab1 = ".//*[@id='bwpostman_newsletters_tabs']/dt[2]";
//	public static $tab2 = ".//*[@id='bwpostman_newsletters_tabs']/dt[3]";
//	public static $tab3 = ".//*[@id='bwpostman_newsletters_tabs']/dt[4]";

	public static $tab1 = ".//*[@id='j-main-container']/div[2]/ul/li[1]/button";
	public static $tab2 = ".//*[@id='j-main-container']/div[2]/ul/li[2]/button";
	public static $tab3 = ".//*[@id='j-main-container']/div[2]/ul/li[3]/button";

	public static $first_list_link          = ".//*[@id='main-table']/tbody/tr[1]/td[3]/p[1]/a";
	public static $first_list_entry_tab2    = ".//*[@id='ub0']";
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
			'id'            => 'ID',
		),

		'sort_criteria_select' => array(
			'attachment'    => 'Attachment',
			'subject'       => 'Subject',
			'description'   => 'Description',
			'modified_time' => 'Last modified',
			'authors'       => 'Author',
			'campaign_id'   => 'Campaign',
			'id'            => 'ID',
		),

		'select_criteria' => array(
			'attachment'    => 'a.attachment',
			'subject'       => 'a.subject',
			'description'   => 'a.description',
			'modified_time' => 'a.modified_time',
			'authors'       => 'authors',
			'campaign_id'   => 'campaign_id',
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
		'id'            => 'a.id',
	);

	// Filter authors
	public static $authors_col                  = ".//*[@id='j-main-container']/div[4]/table/tbody/*/td[6]";
	public static $filter_authors_list_id       = "filter_authors_chzn";
	public static $filter_authors_list          = ".//*[@id='filter_authors_chzn']/a";
	public static $filter_author_1              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'BwPostmanAdmin')]";
	public static $filter_author_2              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'BwPostmanEditor')]";
	public static $filter_author_3              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'AdminTester')]";

	public static $filter_author_1_txt          = 'BwPostmanAdmin';
	public static $filter_author_2_txt          = 'BwPostmanEditor';
	public static $filter_author_3_txt          = 'AdminTester';

	// Filter campaign
	public static $filter_campaign_list_id       = "filter_campaign_id_chzn";
	public static $filter_campaign_list          = ".//*[@id='filter_campaign_id_chzn']/a";
	public static $filter_campaign_none          = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[text()='- Select campaign -']";
	public static $filter_campaign_without       = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[text()='- Without campaign -']";
	public static $filter_campaign_cam           = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[3]";

	// Filter mailinglist
	public static $filter_mailinglist_list_id   = "filter_mailinglists_chzn";
	public static $filter_mailinglist_list      = ".//*[@id='filter_mailinglists_chzn']/a";
	public static $filter_mailinglist_1         = ".//*[@id='filter_mailinglists_chzn']/div/ul/li[text()='01 Mailingliste 3 A']";
	public static $filter_mailinglist_2         = ".//*[@id='filter_mailinglists_chzn']/div/ul/li[text()='02 Mailingliste 6 A']";
	public static $filter_mailinglist_3         = ".//*[@id='filter_mailinglists_chzn']/div/ul/li[text()='02 Mailingliste 9 A']";

	public static $filter_mailinglist_1_txt     = '01 Mailingliste 3 A';
	public static $filter_mailinglist_2_txt     = '02 Mailingliste 6 A';
	public static $filter_mailinglist_3_txt     = '02 Mailingliste 9 A';

	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[2]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[3]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[4]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[5]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[6]",
		),
		'search_val'           => array("Test Newsletter single 1", "15", "About your home page"),
		// array of arrays: outer array per search value, inner arrays results per 'search by'
		'search_res'           => array(array(11, 0, 11, 0, 0, 0), array(2, 2, 2, 0, 0, 0), array(0, 0, 0, 5, 5, 5)),
	);

	public static $search_clear_val     = 'Newsletter for testing 1';

	public static $filter_cam_result   = array(
											'Template Gedicht 1',
											'Template Gedicht 3',
											'Test Newsletter single 15',
										);

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

	public static $filter_sent_cam_result   = array(
		"Kopie von 'Kopie von 'Template Gedicht 1''",
		"Kopie von 'Template Gedicht 1'",
		"Test Newsletter 11.4.2015 23:30:35",
		"Test Newsletter 12.4.2015 10:32:6",
	);

	public static $search_sent_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
			".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[2]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[3]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[4]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[5]",
			".//*[@id='filter_search_filter_chzn']/div/ul/li[6]",
		),
		'search_val'           => array("Kopie von", "3", "Die blaue Meise"),
		// array of arrays: outer array per search value, inner arrays results per 'search by'
		'search_res'           => array(array(3, 0, 3, 0, 0, 0), array(15, 6, 19, 20, 28, 31), array(0, 0, 0, 9, 2, 9)),
	);

	public static $search_sent_clear_val     = "Kopie von 'Kopie von 'Template Gedicht 1''";

	public static $publish_by_icon   = array(
		'publish_button'    =>  ".//*[@id='main-table']/tbody/tr[1]/td[8]/a",
		'publish_result'    =>  ".//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  =>  ".//*[@id='main-table']/tbody/tr[1]/td[8]/a",
		'unpublish_result'  =>  ".//*[@id='main-table']/tbody/tr[1]/td[8]/a/span[contains(@class, 'icon-unpublish')]",
	);

	public static $pagination_data_array  = array(
		'p1_val1'              => "Newsletter for testing 1",
		'p1_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p1_val_last'          => "Newsletter for testing 18",
		'p1_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p2_val1'              => "Newsletter for testing 19",
		'p2_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p2_val_last'          => "Newsletter for testing 4",
		'p2_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p3_val1'              => "Newsletter for testing 5",
		'p3_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p3_val3'              => "Test Newsletter single 1",
		'p3_field3'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p_prev_val1'          => "Test Newsletter single 20",
		'p_prev_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p_prev_val_last'      => "Test Newsletter single 6",
		'p_prev_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p_last_val1'          => "Test Newsletter single 7",
		'p_last_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p_last_val_last'      => "Test Newsletter single 9",
		'p_last_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[3]/td[3]",
	);

	public static $pagination_sent_data_array  = array(
		'p1_val1'              => "Kopie von 'Kopie von 'Template Gedicht 1''",
		'p1_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p1_val_last'          => "Test Newsletter 11.4.2015 13:26:59",
		'p1_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p2_val1'              => "Test Newsletter 11.4.2015 18:28:23",
		'p2_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p2_val_last'          => "Test Newsletter 12.4.2015 1:30:13",
		'p2_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p3_val1'              => "Test Newsletter 12.4.2015 2:1:47",
		'p3_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p3_val3'              => " Test Newsletter 9.4.2015 22:52:14",
		'p3_field3'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p_prev_val1'          => "Test Newsletter 12.4.2015 2:1:47",
		'p_prev_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p_prev_val_last'      => "Test Newsletter 9.4.2015 22:52:14",
		'p_prev_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'p_last_val1'          => "Test Newsletter 9.4.2015 23:5:25",
		'p_last_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'p_last_val_last'      => "Test Newsletter 9.4.2015 23:5:25",
		'p_last_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
	);

	public static $arc_del_array    = array(
		'section'   => 'newsletter',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=newsletters',
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

	public static $sent_column_publish_up   = ".//*[@id='main-table']/tbody/tr[1]/td[9]/p[1]";
	public static $sent_column_publish_down = ".//*[@id='main-table']/tbody/tr[1]/td[9]/p[2]";
	public static $sent_column_description  = ".//*[@id='main-table']/tbody/tr[1]/td[4]";
}
