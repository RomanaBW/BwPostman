<?php
namespace Page;

//use Codeception\Module\WebDriver;

/**
 * Class TemplateManagerPage
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
class TemplateManagerPage
{
	/**
	 * url of current page
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
    public static $url      = '/administrator/index.php?option=com_bwpostman&view=templates';
	public static $section  = 'template';

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
			'tpl_id'        => 'Format',
			'published'     => 'published',
			'description'   => 'Description',
			'id'            => 'ID',
			'title'         => 'Title'
		),

		'sort_criteria_select' => array(
			'tpl_id'        => 'Mail format',
			'published'     => 'Status',
			'description'   => 'Description',
			'id'            => 'ID',
			'title'         => 'Title'
		),

		'select_criteria' => array(
			'tpl_id'        => 'a.tpl_id',
			'published'     => 'a.published',
			'description'   => 'a.description',
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
		'tpl_id'        => 'a.tpl_id',
		'published'     => 'a.published',
		'description'   => 'a.description',
		'id'            => 'a.id',
		'title'         => 'a.title'
	);

	// publish by icon
	public static $publish_by_icon   = array(
		'publish_button'    =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a",
		'publish_result'    =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a",
		'unpublish_result'  =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a/span[contains(@class, 'icon-unpublish')]",
	);

	// publish by toolbar
	public static $publish_by_toolbar   = array(
		'publish_button'    =>  ".//*[@id='cb3']",
		'publish_result'    =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  =>  ".//*[@id='cb3']",
		'unpublish_result'  =>  ".//*[@id='main-table']/tbody/tr[4]/td[6]/a/span[contains(@class, 'icon-unpublish')]",
	);

	public static $default_button1      = ".//*[@id='main-table']/tbody/tr[3]/td[5]/a";
	public static $default_result1      = ".//*[@id='main-table']/tbody/tr[3]/td[5]/a/span[contains(@class, 'icon-featured')]";
	public static $no_default_result1   = ".//*[@id='main-table']/tbody/tr[1]/td[5]/a/span[contains(@class, 'icon-featured')]";

	public static $default_button2      = ".//*[@id='main-table']/tbody/tr[1]/td[5]/a";
	public static $default_result2      = ".//*[@id='main-table']/tbody/tr[1]/td[5]/a/span[contains(@class, 'icon-featured')]";
	public static $no_default_result2   = ".//*[@id='main-table']/tbody/tr[3]/td[5]/a/span[contains(@class, 'icon-featured')]";

	// Filter mail format
	public static $format_list_id       = "filter_tpl_id_chzn";
	public static $format_list          = ".//*[@id='filter_tpl_id_chzn']/a";
	public static $format_none          = ".//*[@id='filter_tpl_id_chzn']/div/ul/li[text()='Select email format']";
	public static $format_text          = ".//*/li[text()='Text']";
	public static $format_html          = ".//*/li[text()='HTML']";
	public static $format_text_column   = ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/*/td[4]";
	public static $format_text_text     = 'Text';
	public static $format_text_html     = 'HTML';

	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
												".//*[@id='filter_search_filter_chzn']/div/ul/li[2]",
												".//*[@id='filter_search_filter_chzn']/div/ul/li[3]",
												".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
												),

		'search_val'           => array("Sample for an own", "Blue"),
		// array of arrays: outer array per search value, inner arrays per 'search by'
		'search_res'           => array(array(2, 2, 0), array(0, 4, 4)),
	);
	public static $search_clear_val     = ' Boldt Webservice';

	public static $pagination_data_array  = array(
		'p1_val1'              => "Boldt Webservice",
		'p1_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p1_val_last'          => "Standard Deep Blue",
		'p1_field_last'        => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p2_val1'              => "Standard Soft Blue",
		'p2_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p2_val_last'          => "Template for Test 01",
		'p2_field_last'        => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_prev_val1'          => "Z Standard Soft Blue",
		'p_prev_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_prev_val_last'      => "Z Template for Test 01",
		'p_prev_field_last'    => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p3_val1'              => "Z Boldt Webservice",
		'p3_field1'            => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p3_val3'              => "Z Standard Deep Blue",
		'p3_field3'            => ".//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_last_val1'          => "Z2 Boldt Webservice",
		'p_last_field1'        => ".//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_last_val_last'      => "Z2 Template for Test 01",
		'p_last_field_last'    => ".//*[@id='main-table']/tbody/tr[2]/td[2]",
	);

	public static $archive_confirm    = 'Do you wish to archive the selected template(s)?';
	public static $remove_confirm     = 'Do you wish to remove the selected template(s)?';
	public static $success_remove     = 'The selected template has been removed.';

	public static $arc_del_array    = array(
		'section'   => 'template',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=templates',
	);

	/**
	 * Test method to check pagination of templates
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   boolean             $permission
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function SetDefaultTemplates(\AcceptanceTester $I, $permission)
	{
		$I->click(self::$default_button1);
		if ($permission)
		{
			$I->seeElement(self::$default_result1);
			$I->dontSeeElement(self::$no_default_result1);

			$I->click(self::$default_button2);
			$I->seeElement(self::$default_result2);
			$I->dontSeeElement(self::$no_default_result2);
		}
		else
		{
		    $I->dontSeeElement(Generals::$toolbar['Default']);
			$I->dontSeeElement(self::$default_result1);
			$I->seeElement(self::$no_default_result1);
		}
	}
}
