<?php
namespace Page;

//use Codeception\Module\WebDriver;

/**
 * Class TemplateManagerPage
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

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
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

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_icon   = array(
		'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/a",
		'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/a",
		'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/a/span[contains(@class, 'icon-unpublish')]",
	);

	// publish by toolbar

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_toolbar   = array(
		'publish_button'    => "//*[@id='cb0']",
		'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/a/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "//*[@id='cb0']",
		'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/a/span[contains(@class, 'icon-unpublish')]",
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $default_button1      = "//*[@id='main-table']/tbody/tr[3]/td[5]/*/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $default_result1      = "//*[@id='main-table']/tbody/tr[3]/td[5]/*/span[contains(@class, '-featured')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $no_default_result1   = "//*[@id='main-table']/tbody/tr[4]/td[5]/*/span[contains(@class, '-featured')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $no_default_result_import   = "//*[@id='main-table']/tbody/tr[1]/td[5]/*/span[contains(@class, '-featured')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $default_button2      = "//*[@id='main-table']/tbody/tr[4]/td[5]/*/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $default_result2      = "//*[@id='main-table']/tbody/tr[4]/td[5]/*/span[contains(@class, '-featured')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $no_default_result2   = "//*[@id='main-table']/tbody/tr[3]/td[5]/*/span[contains(@class, '-featured')]";

	// Filter mail format

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list_id       = "//*[@id='filter_tpl_id']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_list          = "//*[@id='filter_tpl_id_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_none          = "//*[@id='filter_tpl_id_chzn']/div/ul/li[text()='Select email format']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text          = "Text";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_html          = "HTML";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text_column   = "//*[@id='main-table']/tbody/*/td[4]";

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


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $search_data_array  = array(
		// enter default 'search by' as last array element
		'search_by'            => array(
				"Description",
				"Title & Description",
				"Title",
				),

		'search_val'           => array("Sample for an own", "Blue"),
		// array of arrays: outer array per search value, inner arrays per 'search by'
		'search_res'           => array(array(2, 2, 0), array(0, 4, 4)),
	);

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_clear_val     = ' Boldt Webservice';

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $pagination_data_array  = array(
		'p1_val1'              => "Boldt Webservice",
		'p1_field1'            => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p1_val_last'          => "Standard Deep Blue",
		'p1_field_last'        => "//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p2_val1'              => "Standard Soft Blue",
		'p2_field1'            => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p2_val_last'          => "Template for Test 01",
		'p2_field_last'        => "//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_prev_val1'          => "Z Standard Soft Blue",
		'p_prev_field1'        => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_prev_val_last'      => "Z Template for Test 01",
		'p_prev_field_last'    => "//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p3_val1'              => "Z Boldt Webservice",
		'p3_field1'            => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p3_val3'              => "Z Standard Deep Blue",
		'p3_field3'            => "//*[@id='main-table']/tbody/tr[5]/td[2]",

		'p_last_val1'          => "Z2 Boldt Webservice",
		'p_last_field1'        => "//*[@id='main-table']/tbody/tr[1]/td[2]",
		'p_last_val_last'      => "Z2 Template for Test 01",
		'p_last_field_last'    => "//*[@id='main-table']/tbody/tr[2]/td[2]",
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $archive_confirm    = 'Do you wish to archive the selected template(s)?';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $remove_confirm     = 'Do you wish to remove the selected template(s)?';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_remove     = 'The selected template has been removed.';


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array    = array(
		'section'   => 'template',
		'url'   => '/administrator/index.php?option=com_bwpostman&view=templates',
	);

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $import_button      = "//*/a[@id='toolbar-install-template']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importPageTitleField      = "//*/section[@id='content']/div/div/main/form/fieldset/legend";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importPageTitle      = "Select archive file";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importField      = "//*/div[contains(@class, 'uploadtpl')]/div[1]/div/input";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $startImport_button      = "//*/div[contains(@class, 'uploadtpl')]/div[2]/div/input";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $step1Field      = "//*/p[@id='step1' and contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $step2Field      = "//*/p[@id='step2' and contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $step3Field      = "//*/p[@id='step3' and contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $step4Field      = "//*/p[@id='step4' and contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $step5Field      = "//*/p[@id='step5' and contains(@class, 'alert-success')]";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importSuccessField      = "//*/h3[@class='text-success']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importSuccessText      = "The template has been successfully installed";


	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importBackButton      = "//*/a[@id='toolbar-back']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importFile      = "standard_basic_import.zip";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $tableHeaderId      = "//*[@id='main-table']/thead/tr/th[8]/a";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $tableHeaderIdArrow      = "//*[@id='main-table']/thead/tr/th[8]/a/span[2]";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $firstTableTitle      = "//*[@id='main-table']/tbody/tr[1]/td[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $importTemplateName      = "Standard Basic Import";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $firstTableCheckbox      = "//*[@id='main-table']/tbody/tr[1]/td[1]/input";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $firstTableId      = "//*[@id='main-table']/tbody/tr[1]/td[8]";


	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $tableRowForDefault      = "//*[@id='main-table']/tbody/tr[4]/td[2]/a";


	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $export_button      = "//*/button[contains(@class, 'button-download')]";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $exportPackSuccess = "The template has been packed and is ready to download!";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $exportPackSuccessField = "html/body/div[2]/section/div/div/div[1]/div/div";


	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $exportDownloadButton    = "//*[@id='system-message-container']/joomla-alert/div[2]/div/a";

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array_import     = array(
		'field_title'          => "Standard Basic Import",
		'archive_tab'          => "//*/ul[contains(@class, 'bwp-tabs')]/li/a[contains(text(),'Archived templates')]",
		'archive_identifier'   => "Title",
		'archive_title_col'    => "//*[@id='main-table']/tbody/*/td[2]",
		'archive_confirm'      => 'Do you wish to archive the selected template(s)?',
		'archive_success_msg'  => 'The selected template has been archived.',
		'archive_success2_msg' => 'The selected templates have been archived.',

		'delete_button'        => "//*[@id='toolbar-delete']/button",
		'delete_identifier'    => "Title",
		'delete_title_col'     => "//*[@id='main-table']/tbody/tr/td/div/table/tbody/*/td[2]",
		'remove_confirm'       => 'Do you wish to remove the selected template(s)?',
		'success_remove'       => 'The selected template has been removed.',
		'success_remove2'      => 'The selected templates have been removed.',
		'success_restore'       => 'The selected template has been restored.',
		'success_restore2'      => 'The selected templates have been restored.',
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
		$I->clickAndWait(self::$default_button1, 1);
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
