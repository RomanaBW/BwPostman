<?php
namespace Page;

/**
 * Class MailinglistEditPage
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
class MailinglistEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=mailinglist&layout=edit';

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     */

	public static $title        = '#jform_title';
	public static $description  = '#jform_description';

	public static $access               = '#jform_access';
	public static $access_list          = ".//*[@id='jform_access_chzn']/a";
	public static $access_list_text     = ".//*[@id='jform_access_chzn']/a/span";
	public static $access_public        = ".//*[@id='jform_access_chzn']/div/ul/li[text()='Public']";
	public static $access_guest         = ".//*[@id='jform_access_chzn']/div/ul/li[text()='Guest']";
	public static $access_registered    = ".//*[@id='jform_access_chzn']/div/ul/li[text()='Registered']";
	public static $access_special       = ".//*[@id='jform_access_chzn']/div/ul/li[text()='Special']";
	public static $access_super         = ".//*[@id='jform_access_chzn']/div/ul/li[text()='Super Users']";

	public static $published                = '#jform_published';
	public static $published_list           = ".//*[@id='jform_published_chzn']/a";
	public static $published_list_text      = ".//*[@id='jform_published_chzn']/a/span";
	public static $published_unpublished    = ".//*[@id='jform_published_chzn']/div/ul/li[text()='unpublished']";
	public static $published_published      = ".//*[@id='jform_published_chzn']/div/ul/li[text()='published']";

	public static $field_title        = '001 General mailing list';
	public static $field_description  = 'A pretty description would be nice.';

	public static $success_save       = 'Mailinglist saved successfully!';
	public static $error_save         = 'Save failed with the following error:';

	public static $popup_title        = 'You have to enter a title for the mailinglist.';
	public static $popup_description  = 'You have to enter a description for the mailinglist.';

	public static $archive_button       = ".//*[@id='toolbar-archive']/button";
	public static $archive_tab          = ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li[4]/button";
	public static $archive_identifier   = ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]";
	public static $archive_title_col    = ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]";
	public static $archive_success_msg  = 'The selected mailing list has been archived.';
	public static $archive_success2_msg = 'The selected mailing lists have been archived.';

	public static $delete_button        = ".//*[@id='toolbar-delete']/button";
	public static $delete_identifier    = ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]";
	public static $delete_title_col     = ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]";
	public static $remove_confirm       = 'Do you wish to remove the selected mailinglist(s)?';
	public static $success_remove       = 'The selected mailinglist has been removed.';
	public static $success_remove2      = 'The selected mailinglists have been removed.';

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

}
