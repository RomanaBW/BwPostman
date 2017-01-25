<?php
namespace Page;

//use Codeception\Module\WebDriver;
//use Codeception\PHPUnit\Constraint\Page;

/**
 * Class SubscriberManagerPage
 *
 * @package Page
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
	public static $section  = 'subscriber';
	public static $wait_db  = 1;

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

	/**
	 * Array of sorting criteria values for this page
	 * This array meets table headings
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_criteria = array(
		'firstname'     => 'First name',
		'gender'        => 'Gender',
		'email'         => 'Email',
		'Email format'  => 'Email format',
		'user_id'       => 'Joomla User-ID',
		'mailinglists'  => '# Mailinglists',
		'id'            => 'ID',
		'name'          => 'Last name'
	);

	/**
	 * Array of sorting criteria values for this page
	 * This array select list values
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_criteria_select = array(
		'firstname'     => 'First name',
		'gender'        => 'Gender',
		'email'         => 'Email',
		'Email format'  => 'Mail format',
		'user_id'       => 'Joomla User-ID',
		'mailinglists'  => '# subscribed mailing lists',
		'id'            => 'ID',
		'name'          => 'Name'
	);

	/**
	 * Array of criteria to sort
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $select_criteria = array(
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

	// enter default 'search by' as last array element
	public static $search_by            = array(
		".//*[@id='filter_search_filter_chzn']/div/ul/li[2]",
		".//*[@id='filter_search_filter_chzn']/div/ul/li[3]",
		".//*[@id='filter_search_filter_chzn']/div/ul/li[4]",
		".//*[@id='filter_search_filter_chzn']/div/ul/li[5]",
		".//*[@id='filter_search_filter_chzn']/div/ul/li[6]",
		);

	// Filter mail format
	public static $format_list          = ".//*[@id='filter_emailformat_chzn']/a";
	public static $format_none          = ".//*[@id='filter_emailformat_chzn']/div/ul/li[text()='Select email format']";
	public static $format_text          = ".//*/li[text()='Text']";
	public static $format_html          = ".//*/li[text()='HTML']";
	public static $format_text_column   = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/*/td[5]";
	public static $format_text_text     = 'Text';
	public static $format_text_html     = 'HTML';

	// Filter mailinglist
	public static $ml_list          = ".//*[@id='filter_mailinglist_chzn']/a";
	public static $ml_select        = ".//*/li[text()='04 Mailingliste 14 A']";

	public static $search_val           = array("xx", "Andreas");
	// array of arrays: outer array per search value, inner arrays per 'search by'
	public static $search_res           = array(array(0, 0, 0, 0, 0), array(2, 1, 3, 3, 3));
	public static $search_clear_val     = 'Abbott';

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

	public static $p1_val1              = "Abbott";
	public static $p1_field1            = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[1]/td[2]";
	public static $p1_val_last          = "Alexander";
	public static $p1_field_last        = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[10]/td[2]";

	public static $p2_val1              = "Altmann";
	public static $p2_field1            = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[1]/td[2]";
	public static $p2_val_last          = "Atkins";
	public static $p2_field_last        = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[10]/td[2]";

	public static $p3_val1              = "Auer";
	public static $p3_field1            = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[1]/td[2]";
	public static $p3_val3              = "Barrenbruegge";
	public static $p3_field3            = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[10]/td[2]";

	public static $p_prev_val1          = "Willis";
	public static $p_prev_field1        = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[1]/td[2]";
	public static $p_prev_val_last      = "Zabel";
	public static $p_prev_field_last    = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[10]/td[2]";

	public static $p_last_val1          = "Zauner";
	public static $p_last_field1        = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[1]/td[2]";
	public static $p_last_val_last      = "Zuschuss";
	public static $p_last_field_last    = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/tr[8]/td[2]";

}
