<?php
namespace Page;

/**
 * Class TemplateEditPage
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
class TemplateEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=template&layout=edit';

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     */

    public static $tpl_tab1     = ".//*[@id='template_tabs']/dt[2]/span/h3/a";
	public static $tpl_tab2     = ".//*[@id='template_tabs']/dt[3]/span/h3/a";
	public static $tpl_tab3     = ".//*[@id='template_tabs']/dt[4]/span/h3/a";

	public static $title        = '#jform_title';
	public static $description  = '#jform_description';

	public static $thumbnail               = ".//*[@id='jform_thumbnail']";
	public static $thumbnail_list_pos      = ".//*[@id='j-main-container']/div[2]/table/tbody/tr[1]/td[3]/a/img[@src='%s']";
	public static $thumbnail_id            = "jform_thumbnail";
	public static $field_thumbnail         = 'images/powered_by.png';

	public static $thumb_select_button     = './/*[@id=\'adminForm\']/fieldset/div/div[1]/div[1]/dd[1]/fieldset[1]/div/ul/li[3]/div/div/a[1]';
//	public static $thumb_select            = "html/body/ul/li[6]/a";
	public static $thumb_select            = "html/body/ul/li/a[contains(@href,'powered_by.png')]";
	public static $thumb_insert            = ".//*[@id='imageForm']/div[2]/div/div[2]/button[1]";
	public static $thumb_media_url_field   = ".//*[@id='f_url']";
	public static $thumb_url               = "/images/powered_by.png";

	public static $show_author_no   = ".//*[@id='jform_article_show_author']/label[1]";
	public static $show_author_yes  = ".//*[@id='jform_article_show_author']/label[2]";

	public static $show_created_no   = ".//*[@id='jform_article_show_createdate']/label[1]";
	public static $show_created_yes  = ".//*[@id='jform_article_show_createdate']/label[2]";

	public static $show_readon_no   = ".//*[@id='jform_article_show_readon']/label[1]";
	public static $show_readon_yes  = ".//*[@id='jform_article_show_readon']/label[2]";

	public static $access       = '#jform_access';
	public static $published    = '#jform_published';

	public static $field_title        = '001 Test HTML-Template';
	public static $field_description  = 'A pretty description for this HTML template would be nice.';

	public static $msg_cancel         = 'Any changes will not be saved. Close without saving?';

	public static $css_style        = ".//*[@id='jform_tpl_css']";
	public static $html_style       = ".//*[@id='jform_tpl_html']";
	public static $text_style       = ".//*[@id='jform_tpl_html']";

	//buttons
//	public static $button_editor_toggle     = ".//*[@id='wf_editor_jform_tpl_html_toggle']";
	public static $button_editor_toggle     = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/div/div[1]/ul/li[2]/a";
	public static $button_refresh_preview   = ".//*[@id='email_preview']/p/button";
	public static $button_first_name        = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[1]";
	public static $button_last_name         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[2]";
	public static $button_full_name         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[3]";
	public static $button_content           = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[4]";
	public static $button_unsubscribe       = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[5]";
	public static $button_editlink          = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[6]";
	public static $button_impressum         = ".//*[@id='adminForm']/fieldset/div/div[1]/div[1]/dd[3]/fieldset/ul/li/div[2]/a[7]";

	public static $success_save       = 'Template saved successfully!';
//	public static $success_save       = 'Item successfully saved.';
	public static $error_save         = 'Save failed with the following error:';

	public static $popup_title        = 'You have to enter a title for the template.';
	public static $popup_description  = 'You have to enter a description for the template.';

	public static $popup_changes_not_saved  = 'Any changes will not be saved. Close without saving?';

	public static $archive_button       = ".//*[@id='toolbar-archive']/button";
	public static $archive_tab          = ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li[5]/button";
	public static $archive_identifier   = ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]";
	public static $archive_title_col    = ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]";
	public static $archive_confirm      = 'Do you wish to archive the selected template(s)?';
	public static $archive_success_msg  = 'The selected template has been archived.';
	public static $archive_success2_msg = 'The selected templates have been archived.';

	public static $delete_button        = ".//*[@id='toolbar-delete']/button";
	public static $delete_identifier    = ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]";
	public static $delete_title_col    = ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/div/table/tbody/*/td[2]";
	public static $remove_confirm       = 'Do you wish to remove the selected template(s)?';
	public static $success_remove       = 'The selected template has been removed.';
	public static $success_remove2      = 'The selected templates have been removed.';

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

	public static $css_style_content        = '';
	public static $html_style_content       = '';
	public static $text_style_content       = '';

	/**
	 * TemplateEditPage constructor.
	 *
	 * @since       2.0.0
	 */
	public function __construct()
	{
                $base_dir   = '/vms/dockers/global_data/tests';
                $data_dir   = $base_dir . '/BwPostman/tests/_data/';
                $data_dir   = 'tests/_data/';
		self::$css_style_content    = $this->_getFileContent($data_dir . 'html-newsletter.css');
		self::$html_style_content   = $this->_getFileContent($data_dir . 'html-newsletter.txt');
		self::$text_style_content   = $this->_getFileContent($data_dir . 'text-newsletter.txt');
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
	private function _getFileContent($file_name)
	{
		$content    = '';

		$content_tmp    = file($file_name);
		for ($i = 0; $i < count($content_tmp); $i++)
		{
			$content .= trim($content_tmp[$i]) . "\n";
		}
		return  $content;
	}
}
