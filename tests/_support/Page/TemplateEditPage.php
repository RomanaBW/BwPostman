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
	public static $tpl_tab1     = "//*[@id='template_tabs']/div/button[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tpl_tab2     = "//*[@id='template_tabs']/div/button[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tpl_tab3     = "//*[@id='template_tabs']/div/button[3]";


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
	public static $thumbnail               = "//*[@id='jform_thumbnail']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumbnail_list_pos      = "//*/table[@id='main-table']/tbody/tr[1]/td[3]/a/img[@src='%s']";

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
	public static $thumb_select_button     = "//*/joomla-field-media/div[@class='input-group']/button[contains(@class, 'button-select')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_select            = "//*[@id='browser-list']/ul/li[contains(@id, '/powered_by.png')]/a";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $thumb_select_user       = "//*/ul[contains(@class, 'manager')]/li/a/div/div/img[contains(@alt, 'powered_by.png')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_insert            = "//button[@id='insert']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $thumb_cancel            = "//button[@id='cancel']";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $thumb_insert_user       = "//button[contains(@class, 'button-save-selected')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_media_url_field   = "//*[@id='f_url']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_url               = "/images/powered_by.png#joomlaImage://local-images/powered_by.png?width=294&height=44";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $thumb_url_short         = "/images/powered_by.png";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $show_title_no   = "//*[@id='jform_article_show_title0']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $show_title_no_active   = "//*/input[@id='jform_article_show_title0'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $show_title_yes  = "//*[@id='jform_article_show_title1']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $show_title_yes_active   = "//*/input[@id='jform_article_show_title1'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_author_no   = "//*[@id='jform_article_show_author0']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_author_no_active   = "//*/input[@id='jform_article_show_author0'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_author_yes  = "//*[@id='jform_article_show_author1']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_author_yes_active   = "//*/input[@id='jform_article_show_author1'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_created_no   = "//*[@id='jform_article_show_createdate0']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_created_no_active   = "//*/input[@id='jform_article_show_createdate0'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_created_yes  = "//*[@id='jform_article_show_createdate1']";


	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_created_yes_active   = "//*/input[@id='jform_article_show_createdate1'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_readon_no   = "//*[@id='jform_article_show_readon0']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_readon_no_active   = "//*/input[@id='jform_article_show_readon0'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $show_readon_yes  = "//*[@id='jform_article_show_readon1']";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $show_readon_yes_active   = "//*/input[@id='jform_article_show_readon1'][contains(@class, 'active')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlIframe   = "myIframeHtml";

	/**
	 * @var string
	 *
	 * @since 4.2.1
	 */
	public static $TemplatesListTitle   = "Templates";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlHeaderIdentifier   = "//*[@class='article-title']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlArticleIdentifier   = "//*[@class='article-content']/div/h2";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlAuthorIdentifier   = "//*[@class='created_by']/small";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlDateIdentifier   = "//*[@class='createdate']/small";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $htmlReadonIdentifier   = "//*/a[@class='readon']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textIframe   = "myIframeHtml";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textHeaderIdentifier   = "//*/body";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textArticleIdentifier   = "//*/body";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textAuthorIdentifier   = "//*/body";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textDateIdentifier   = "//*/body";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $textReadonIdentifier   = "//*/body";


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
	public static $css_style        = "//*[@id='jform_tpl_css']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $html_style       = "//*[@id='jform_tpl_html']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $text_style       = "//*[@id='jform_tpl_html']";

	//buttons

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_editor_toggle     = "//*/button[contains(@class, 'js-tiny-toggler-button')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_refresh_preview   = "//*[@id='email_preview']/p/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_first_name        = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[1]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_last_name         = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_full_name         = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[3]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_content           = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[4]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_unsubscribe       = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_editlink          = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[6]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_impressum         = "//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[7]";


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
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close'  => "//*[@id='toolbar-save']/button",
		'Save'          => "//*[@id='toolbar-apply']/button",
		'Cancel'        => "//*[@id='toolbar-cancel']/button",
		'Back'          => "//*[@id='toolbar-back']/button",
		'Help'          => "//*[@id='toolbar-help']/button",
	);

	/**
	 * Method to get file content to fill in template fields (CSS, HTML and Text)
	 *
	 * @param $file_name
	 *
	 * @return string
	 *
	 * @since   2.0.0
	 */
	public static function getFileContent($file_name)
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

		$I->scrollTo(Generals::$joomlaHeader, 0, 100);
		$I->wait(1);

		$I->waitForElement(Generals::$pageTitle, 30);

		$I->click(Generals::$toolbar['Add Text-Template']);

		self::fillFormSimpleText($I, $user);

		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);

		$I->waitForElementVisible(Generals::$alert_header, 30);
//		$I->see("Message", Generals::$alert_heading);
		$I->see(self::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
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

		if ($user === 'AdminTester' || $user === "user1")
		{
			self::selectThumbnail($I, $user);
		}

		$I->wait(1);
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
		$I->clickAndWait(self::$tpl_tab1, 1);

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

		$I->setIframeName(Generals::$media_frame1);
		$I->switchToIFrame(Generals::$media_frame1);
		$I->wait(1);

		$I->waitForElementVisible(".//*[@id='browser-list']", 5);
		$I-> waitForElement(self::$thumb_select, 5);
		$I->scrollTo(self::$thumb_select, 0, -100);
		$I->wait(1);
		$I->clickAndWait(self::$thumb_select, 1);

		$I->clickAndWait(self::$thumb_insert, 1);

		$I->switchToIFrame();
	}

	/**
	 * @param \AcceptanceTester $I
	 *
	 * @since 2.0.0
	 */
	public static function fillTextContent(\AcceptanceTester $I)
	{
		$text_style_content   = self::getFileContent('tests/_data/text-newsletter.txt');

		$I->wait(1);
		$I->click(self::$tpl_tab2);
		$I->fillField(self::$text_style, $text_style_content);
		$I->scrollTo(self::$button_refresh_preview, 0, -100);
		$I->wait(1);
		$I->clickAndWait(self::$button_refresh_preview, 2);

		$I->switchToIFrame(self::$textIframe);
		$I->see('Intro-Headline', self::$textHeaderIdentifier);
		$I->switchToIFrame();
	}
}
