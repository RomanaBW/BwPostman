<?php
namespace Page;

//use Codeception\Module\WebDriver;

/**
 * Class MailinglistManagerPage
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
class MailinglistManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $url = '/administrator/index.php?option=com_bwpostman&view=mailinglists';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $section = 'Mailinglists';

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */

	/**
	 * Array of sorting criteria values for this page
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_data_array  = array(
		'sort_criteria' => array(
			'description' => 'Description',
			'published'   => 'published',
			'access'      => 'Access Level',
			'subscribers' => '# subscribers',
			'id'          => 'ID',
			'title'       => 'Title'
		),

		'sort_criteria_select' => array(
			'description' => 'Description',
			'published'   => 'Status',
			'access'      => 'Access',
			'subscribers' => '# subscribed mailing lists',
			'id'          => 'ID',
			'title'       => 'Title'
		),

		'select_criteria' => array(
			'description' => 'a.description',
			'published'   => 'a.published',
			'access'      => 'a.access',
			'subscribers' => 'subscribers',
			'id'          => 'a.id',
			'title'       => 'a.title'
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
		'description' => 'a.description',
		'published'   => 'a.published',
		'access'      => 'a.access',
		'subscribers' => 'subscribers',
		'id'          => 'a.id',
		'title'       => 'a.title'
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_icon   = array(
		'publish_button'    => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a",
		'publish_result'    => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a",
		'unpublish_result'  => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a/span[contains(@class, 'icon-unpublish')]",
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_toolbar   = array(
		'publish_button'    => "//*[@id='cb5']",
		'publish_result'    => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "//*[@id='cb5']",
		'unpublish_result'  => "//*[@id='j-main-container']/table/tbody/tr[6]/td[4]/a/span[contains(@class, 'icon-unpublish')]",
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by' => array(
			"Description",
			"Title & Description",
			"Title",
		),
		'search_val' => array("xx", "liste 2 weit"),
		// array of arrays: outer array per search value, inner arrays per 'search by'
		'search_res' => array(array(0, 0, 0), array(2, 2, 0)),
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_clear_val = '01 Mailingliste 2 weiterer Lauf A';


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $pagination_data_array  = array(
		'p1_val1' => "01 Mailingliste 2 A",
		'p1_field1' => "//*[@id='main-table']/tbody/tr[1]/td[2]",

		'p1_val_last' => "02 Mailingliste 6 B",
		'p1_field_last' => "//*[@id='main-table']/tbody/tr[10]/td[2]",

		'p2_val1' => "02 Mailingliste 6 C",
		'p2_field1' => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p2_val_last' => "03 Mailingliste 12 A",
		'p2_field_last' => "//*[@id='main-table']/tbody/tr[10]/td[2]",

		'p_prev_val1' => "03 Mailingliste 12 B",
		'p_prev_field1' => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_prev_val_last' => "04 Mailingliste 17 A",
		'p_prev_field_last' => "//*[@id='main-table']/tbody/tr[10]/td[2]",

		'p3_val1' => "03 Mailingliste 12 B",
		'p3_field1' => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p3_val3' => "04 Mailingliste 17 A",
		'p3_field3' => "//*[@id='main-table']/tbody/tr[10]/td[2]",

		'p_last_val1' => "04 Mailingliste 17 B",
		'p_last_field1' => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_last_val_last' => "05 Mailingliste 20 B",
		'p_last_field_last' => "//*[@id='main-table']/tbody/tr[7]/td[2]",
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array    = array(
		'section'   => 'mailinglist',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=mailinglists',
	);
}
