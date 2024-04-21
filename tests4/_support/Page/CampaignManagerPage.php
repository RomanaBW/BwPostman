<?php
namespace Page;

//use Codeception\Module\WebDriver;

/**
 * Class CampaignManagerPage
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
class CampaignManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $url      = '/administrator/index.php?option=com_bwpostman&view=campaigns';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $section  = 'campaigns';

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
			'description'   => 'Campaign description',
			'newsletters'   => '# Newsletters',
			'id'            => 'ID',
			'title'         => 'Campaign title'
		),
		'sort_criteria_select' => array(
			'description'   => 'Description',
			'newsletters'   => '# Newsletters',
			'id'            => 'ID',
			'title'         => 'Title'
		),
		'select_criteria' => array(
			'description'   => 'a.description',
			'newsletters'   => 'newsletters',
			'id'            => 'a.id',
			'title'         => 'a.title'
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
		'description'   => 'a.description',
		'newsletters'   => 'newsletters',
		'id'            => 'a.id',
		'title'         => 'a.title'
	);

	// enter default 'search by' as last array element

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $search_data_array  = array(
		'search_by'            => array(
											"Description",
											"Title & Description",
											"Title",
											),
	'search_val'           => array("Test", "12 A Test"),
	// array of arrays: outer array per search value, inner arrays per 'search by'
	'search_res'           => array(array(6, 8, 4), array(1, 1, 0)),
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_clear_val     = '01 Kampagne 2 A';


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $pagination_data_array  = array(
		'p1_val1'              => "01 Kampagne 2 A",
		'p1_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p1_val_last'          => "01 Kampagne 4 A",
		'p1_field_last'        => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p2_val1'              => "01 Kampagne 4 B",
		'p2_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p2_val_last'          => "02 Kampagne 8 A",
		'p2_field_last'        => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_prev_val1'          => "04 Kampagne 12 A",
		'p_prev_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_prev_val_last'      => "05 Kampagne 19 A Test",
		'p_prev_field_last'    => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p3_val1'              => "02 Kampagne 8 B",
		'p3_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p3_val3'              => "03 Kampagne 10 B",
		'p3_field3'            => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_last_val1'          => "05 Kampagne 19 B Test",
		'p_last_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_last_val_last'      => "05 Kampagne 20 B Test",
		'p_last_field_last'    => ".//*[@id='main-table']/tbody/tr[3]/td[2]",
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array    = array(
		'section'                   => 'campaigns',
		'url'                       => '/administrator/index.php?option=com_bwpostman&view=campaigns',
		'popup_archive_iframe'      => 'Archive',
		'popup_archive_newsletters' => 'Do you wish to archive the newsletters which are attached to the campaign, too?',
		'popup_button_yes'          => "/html/body/form/fieldset/div/p[2]/input[contains(@class, 'btn-success')]",
		'popup_button_no'           => "/html/body/form/fieldset/div/p[2]/input[contains(@class, 'btn-secondary')]",
		'popup_button_delete_yes'   => "/html/body/form/fieldset/table/tbody/tr[2]/td/input[1]",
		'popup_button_delete_no'    => "/html/body/form/fieldset/table/tbody/tr[2]/td/input[2]",
		'popup_delete_iframe'       => 'Delete',
		'popup_delete_newsletters'  => 'Do you wish to remove the according newsletters, too?',
		'popup_restore_iframe'      => 'Restore',
		'popup_restore_newsletters' => 'Do you wish to restore the according newsletters, too?',
	);
}
