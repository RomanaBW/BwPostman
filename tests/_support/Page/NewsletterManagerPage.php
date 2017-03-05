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

	public static $tab1             = ".//*[@id='j-main-container']/div[2]/ul/li[1]/button";
	public static $tab2             = ".//*[@id='j-main-container']/div[2]/ul/li[2]/button";
	public static $tab3             = ".//*[@id='j-main-container']/div[2]/ul/li[3]/button";

	/**
	 * Array of sorting criteria values for this page
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
	 * Array of criteria to select from database
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
	public static $filter_authors_list          = ".//*[@id='filter_authors_chzn']/a";
	public static $filter_author_1              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'BwPostmanAdmin')]";
	public static $filter_author_2              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'BwPostmanEditor')]";
	public static $filter_author_3              = ".//*[@id='filter_authors_chzn']/div/ul/li[contains(text(),'AdminTester')]";

	public static $filter_author_1_txt          = 'BwPostmanAdmin';
	public static $filter_author_2_txt          = 'BwPostmanEditor';
	public static $filter_author_3_txt          = 'AdminTester';

	// Filter campaign
	public static $filter_campaign_list          = ".//*[@id='filter_campaign_id_chzn']/a";
	public static $filter_campaign_none          = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[text()='- Select campaign -']";
	public static $filter_campaign_without       = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[text()='- Without campaign -']";
	public static $filter_campaign_cam           = ".//*[@id='filter_campaign_id_chzn']/div/ul/li[3]";

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

	public static $pagination_data_array  = array(
		'pp1_val1'              => "Newsletter for testing 1",
		'pp1_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'pp1_val_last'          => "Newsletter for testing 18",
		'pp1_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'pp2_val1'              => "Newsletter for testing 19",
		'pp2_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'pp2_val_last'          => "Newsletter for testing 4",
		'pp2_field_last'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'pp3_val1'              => "Newsletter for testing 5",
		'pp3_field1'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'pp3_val3'              => "Test Newsletter single 1",
		'pp3_field3'            => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'pp_prev_val1'          => "Test Newsletter single 20",
		'pp_prev_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'pp_prev_val_last'      => "Test Newsletter single 6",
		'pp_prev_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[10]/td[3]",

		'pp_last_val1'          => "Test Newsletter single 7",
		'pp_last_field1'        => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[1]/td[3]",
		'pp_last_val_last'      => "Test Newsletter single 9",
		'pp_last_field_last'    => ".//*[@id='j-main-container']/div[4]/table/tbody/tr[3]/td[3]",
	);

	public static $arc_del_array    = array(
		'section'   => 'newsletter',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=newsletters',
	);
}
