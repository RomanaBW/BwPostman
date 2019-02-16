<?php
namespace Page;

use Page\TemplateManagerPage as TplManage;

/**
 * Class TemplateEditPage
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
class TemplateEditPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url = 'administrator/index.php?option=com_bwpostman&view=template&layout=edit';

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tpl_tab1     = ".//*[@id='template_tabs']/dt[2]/span/h3/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tpl_tab2     = ".//*[@id='template_tabs']/dt[3]/span/h3/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tpl_tab3     = ".//*[@id='template_tabs']/dt[4]/span/h3/a";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $title        = '#jform_title';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $description  = '#jform_description';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumbnail               = ".//*[@id='jform_thumbnail']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumbnail_list_pos      = ".//*[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[3]/a/img[@src='%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumbnail_id            = "jform_thumbnail";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_thumbnail         = 'images/powered_by.png';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_select_button     = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[1]/fieldset[1]/div/ul/li[3]/div/div/div[2]/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_select            = ".//*[@id='browser-list']/ul/li[8]/a";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $thumb_select_user       = ".//*/ul[contains(@class, 'manager')]/li[7]/a/div[1]/div/img[contains(@src,'/powered_by.png')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_insert            = ".//button[@id='insert']";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $thumb_insert_user       = ".//button[contains(@class, 'button-save-selected')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_media_url_field   = ".//*[@id='f_url']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_url               = "/images/powered_by.png";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_author_no   = ".//*[@id='jform_article_show_author']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_author_yes  = ".//*[@id='jform_article_show_author']/label[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_created_no   = ".//*[@id='jform_article_show_createdate']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_created_yes  = ".//*[@id='jform_article_show_createdate']/label[2]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_readon_no   = ".//*[@id='jform_article_show_readon']/label[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_readon_yes  = ".//*[@id='jform_article_show_readon']/label[2]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access       = '#jform_access';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published    = '#jform_published';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_title        = '001 Test Template';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_title2        = '001 Test Template 2';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_description  = 'A pretty description for this %s template would be nice.';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_cancel         = 'Any changes will not be saved. Close without saving?';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $css_style        = ".//*[@id='jform_tpl_css']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $html_style       = ".//*[@id='jform_tpl_html']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $text_style       = ".//*[@id='jform_tpl_html']";

	//buttons

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_editor_toggle     = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/div/div[1]/ul/li[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_refresh_preview   = ".//*[@id='email_preview']/p/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_first_name        = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_last_name         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_full_name         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[3]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_content           = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[4]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_unsubscribe       = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_editlink          = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[6]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_impressum         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[7]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_save       = 'Template saved successfully!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_save         = 'Save failed with the following error:';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_title        = 'You have to enter a title for the template.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_description  = 'You have to enter a description for the template.';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_changes_not_saved  = 'Any changes will not be saved. Close without saving?';


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array     = array(
		'field_title'          => "001 Test Template",
		'archive_tab'          => ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li/button[contains(text(),'Archived templates')]",
		'archive_identifier'   => ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
		'archive_title_col'    => ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]",
		'archive_confirm'      => 'Do you wish to archive the selected template(s)?',
		'archive_success_msg'  => 'The selected template has been archived.',
		'archive_success2_msg' => 'The selected templates have been archived.',

		'delete_button'        => ".//*[@id='toolbar-delete']/button",
		'delete_identifier'    => ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
		'delete_title_col'     => ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/div/table/tbody/*/td[2]",
		'remove_confirm'       => 'Do you wish to remove the selected template(s)?',
		'success_remove'       => 'The selected template has been removed.',
		'success_remove2'      => 'The selected templates have been removed.',
		'success_restore'       => 'The selected template has been restored.',
		'success_restore2'      => 'The selected templates have been restored.',
	);

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close'  => ".//*[@id='toolbar-save']/button",
		'Save'          => ".//*[@id='toolbar-apply']/button",
		'Cancel'        => ".//*[@id='toolbar-cancel']/button",
		'Back'          => ".//*[@id='toolbar-back']/button",
		'Help'          => ".//*[@id='toolbar-help']/button",
	);


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $css_style_content        = '';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $html_style_content       = '';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $text_style_content       = '';

	/**
	 * TemplateEditPage constructor.
	 *
	 * @since       2.0.0
	 */
	public function __construct()
	{
		$data_dir   = 'tests/_data/';
		self::$css_style_content    = $this->getFileContent($data_dir . 'html-newsletter.css');
		self::$html_style_content   = $this->getFileContent($data_dir . 'html-newsletter.txt');
		self::$text_style_content   = $this->getFileContent($data_dir . 'text-newsletter.txt');
	}

	/**
	 * Method to get file content to fill in template fields (CSS, HTML and Text)
	 *
	 * @param $file_name
	 *
	 * @return string
	 *
	 * @since   2.0.0
	 */
	private function getFileContent($file_name)
	{
		$content    = '';

		$content_tmp    = file($file_name);
		for ($i = 0; $i < count($content_tmp); $i++)
		{
			$content .= trim($content_tmp[$i]) . "\n";
		}

		return  $content;
	}

	/**
	 * Test method to create single Template without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 * @param   string              $user
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function CreateTemplateWithoutCleanup(\AcceptanceTester $I, $user)
	{
		$I->wantTo("Create Text template");
		$I->amOnPage(TplManage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);

		$I->click(Generals::$toolbar['Add Text-Template']);

		self::fillFormSimpleText($I, $user);

		$I->clickAndWait(self::$toolbar['Save & Close'], 1);

		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(self::$success_save, Generals::$alert_msg);
		$I->see('Template', Generals::$pageTitle);
	}

	/**
	 * Method to fill form for text template with check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $user
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function fillFormSimpleText(\AcceptanceTester $I, $user = 'AdminTester')
	{
		self::fillRequired($I, 'Text');

		self::selectThumbnail($I, $user);

		self::fillTextContent($I);
	}

	/**
	 * Method to fill required fields
	 * Usable for both, HTML and Text
	 *
	 * @param \AcceptanceTester  $I
	 * @param string            $type
	 *
	 * @since   2.0.0
	 */
	public static function fillRequired(\AcceptanceTester $I, $type)
	{
		$I->fillField(self::$title, self::$field_title);
		$I->fillField(self::$description, sprintf(self::$field_description, $type));
	}

	/**
	 * Method to select thumbnail for template
	 *
	 * @param \AcceptanceTester $I
	 * @param $user
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function selectThumbnail(\AcceptanceTester $I, $user)
	{

		$I->clickAndWait(self::$thumb_select_button, 1);

		$I->switchToIFrame(Generals::$media_frame);

		if ($user === 'AdminTester' || $user === '')
		{
			$I->waitForElement(".//*[@id='browser-list']", 30);
			$I->clickAndWait(self::$thumb_select, 1);

			$I->clickAndWait(self::$thumb_insert, 1);
		}
		else
		{
			$I->switchToIFrame(Generals::$image_frame);

			$I->waitForElementVisible(".//ul[contains(@class, 'manager')]", 30);
			$I->clickAndWait(self::$thumb_select_user, 1);

			$I->switchToIFrame();
			$I->switchToIFrame(Generals::$media_frame);

			$I->clickAndWait(self::$thumb_insert_user, 1);
		}

		$I->switchToIFrame();
	}

	/**
	 * @param \AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	public static function fillTextContent(\AcceptanceTester $I)
	{
		$I->click(self::$tpl_tab2);
		$I->fillField(self::$text_style, self::$text_style_content);
		$I->scrollTo(self::$button_refresh_preview, 0, -100);
		$I->clickAndWait(self::$button_refresh_preview, 2);
	}
}
