<?php
namespace Page;

use AcceptanceTester;
use Exception;

/**
 * Class Generals
 *
 * Class to hold generally needed properties and methods
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
class Generals
{
	// urls of some common pages
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url          = '/administrator/index.php?option=com_bwpostman';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $archive_url  = '/administrator/index.php?option=com_bwpostman&view=archive&layout=newsletters';

	/**
	 * @var string
	 *
	 * @since 3.1.5
	 */
	public static $homeUrlFE = "/index.php";

	/**
	 * @var array
	 *
	 * @since 3.0.0
	 */
	public static $guestGroupId = 13;


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $control_panel        = "Control Panel";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $login_txt            = "Log in";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $nav_user_menu        = ".//*[contains(@class, 'nav-user')]/li/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $nav_user_menu_logout = ".//*[contains(@class, 'nav-user')]/li/ul/li[5]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $logout_txt           = "Log out";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $com_options;

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $sys_message_container    = ".//*[@id='system-message-container']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $media_frame              = "field-media-modal";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $image_frame              = "imageframe";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $back_button  = ".//*[@id='toolbar-back']/button";

	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	protected $tester;

	// backend users
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $admin        = array('user' => 'AdminTester', 'password' => 'BwPostmanTest', 'author' => 'AdminTester');

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $extension            = "BwPostman";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_u2s           = "BwPostman Plugin User2Subscriber";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $first_list_entry     = ".//*[@id='cb0']";

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $pageTitle        = 'h1.page-title';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_header     = 'h4.alert-heading';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_heading    = "//*[@id='system-message-container']/div[1]/h4";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert            = 'div.alert';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_success    = 'div.alert-success';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_msg        = 'div.alert-message';

	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $alert_info        = 'div.alert-info';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_warn       = 'div.alert-warning';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_nothing       = 'div.alert-';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_error      = 'div.alert-error';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_success_txt    = 'Success';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_msg_txt        = 'Message';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_warn_txt       = 'Warning';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $alert_error_txt      = 'Error';

	/**
	 * @var string
	 *
	 * @since 3.1.5
	 */
	public static $warningMessage = "<b>Warning</b>";

	/**
	 * @var string
	 *
	 * @since 3.1.5
	 */
	public static $noticeMessage = "<b>Notice</b>";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $archive_alert_success = 'div.alert-success > div.alert-message';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $archive_txt           = 'Archive';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $header           = '/html/body/div[2]/section/div/div/div[2]/form/div/fieldset/legend';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $check_all_button = ".//*[@name='checkall-toggle']";

	/**
	 * Version to test
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $versionToTest = '2.0.0';

	/**
	 * Version to test
	 *
	 * @var    array
	 *
	 * @since  2.1.0
	 */
	public static $downloadFolder = array(
		'root' => '/tests/tests/_output/',
//		'jenkins' => '/home/jenkins/Downloads/',
		'user1' => '/tests/tests/_output/',
		'user2' => '/repositories/artifacts/bwpostman/downloads/',
		'jenkins' => '/repositories/artifacts/bwpostman/downloads/',
		);

	/**
	 * database prefix
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $db_prefix = 'jos_';

	/**
	 * Array of user groups
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $usergroups = array ('undefined', 'Public', 'Registered', 'Special', 'Guest', 'Super Users');

	/**
	 * Array of states
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $states = array ('unpublish', 'publish');

	/**
	 * Array of sorting order values
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $sort_orders = array ('ascending', 'descending');

	/**
	 * Array of list limit values
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $list_limits = array (5, 10, 20);

	/**
	 * Array of submenu xpath values for all pages
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $submenu = array (
		'BwPostman'     => ".//*[@id='submenu']/li/a[contains(text(), 'BwPostman')]",
		'Newsletters'   => ".//*[@id='submenu']/li/a[contains(text(), 'Newsletters')]",
		'Subscribers'   => ".//*[@id='submenu']/li/a[contains(text(), 'Subscribers')]",
		'Campaigns'     => ".//*[@id='submenu']/li/a[contains(text(), 'Campaigns')]",
		'Mailinglists'  => ".//*[@id='submenu']/li/a[contains(text(), 'Mailinglists')]",
		'Templates'     => ".//*[@id='submenu']/li/a[contains(text(), 'Templates')]",
		'Archive'       => ".//*[@id='submenu']/li/a[contains(text(), 'Archive')]",
		'Maintenance'   => ".//*[@id='submenu']/li/a[contains(text(), 'Maintenance')]",
	);

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $submenu_toggle_button  = ".//*[@id='j-toggle-sidebar-icon']";

	/**
	 * Array of toolbar id values for list page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'New'                  => ".//*[@id='toolbar-new']/button",
		'Edit'                 => ".//*[@id='toolbar-edit']/button",
		'Publish'              => ".//*[@id='toolbar-publish']/button",
		'Unpublish'            => ".//*[@id='toolbar-unpublish']/button",
		'Archive'              => ".//*[@id='toolbar-archive']/button",
		'Help'                 => ".//*[@id='toolbar-help']/button",
		'Duplicate'            => ".//*[@id='toolbar-copy']/button",
		'Send'                 => ".//*[@id='toolbar-envelope']/button",
		'Add HTML-Template'    => ".//*[@id='toolbar-calendar']/button",
		'Add Text-Template'    => ".//*[@id='toolbar-new']/button",
		'Default'              => ".//*[@id='toolbar-default']/button",
		'Check-In'             => ".//*[@id='toolbar-checkin']/button",
		'Install-Template'     => ".//*[@id='toolbar-custom']/a",
		'Options'              => ".//*[@id='toolbar-options']/button",
		'Save'                 => ".//*[@id='toolbar-apply']/button",
		'Save & Close'         => ".//*[@id='toolbar-save']/button",
		'Save & New'           => ".//*[@id='toolbar-save-new']/button",
		'Save as Copy'         => ".//*[@id='toolbar-save-copy']/button",
		'Cancel'               => ".//*[@id='toolbar-cancel']/button",
		'Back'                 => ".//*[@id='toolbar-back']/button",
		'Delete'               => ".//*[@id='toolbar-delete']/button",
		'Restore'              => ".//*[@id='toolbar-unarchive']/button",
		'Enable'               => ".//*[@id='toolbar-publish']/button",
		'Import'               => ".//*[@id='toolbar-download']/button",
		'Export'               => ".//*[@id='toolbar-upload']/button",
		'Export Popup'         => ".//*[@id='toolbar-popup-upload']/button",
		'Batch'                => ".//*[@id='toolbar-batch']/button",
		'Reset sending trials' => ".//*[@id='toolbar-checkin']/button",
		'Continue sending'     => ".//*[@id='toolbar-envelope']/button",
		'Clear queue'          => ".//*[@id='toolbar-trash']/button",
		'Uninstall  '          => ".//*[@id='toolbar-delete']/button",
		'BwPostman Manual'     => ".//*[@id='toolbar-forum']/button",
		'BwPostman Forum'      => ".//*[@id='toolbar-manual']/button",
	);

	/**
	 * Array of arrows to sort
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $sort_arrows = array(
		'up'    => 'icon-arrow-up-3',
		'down'  => 'icon-arrow-down-3'
	);

	/**
	 * Location of selected value in sort select list
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $select_list_selected_location = ".//*[@id='%s']/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $select_list_open              = ".//*[@id='%s']/div";

	/**
	 * Location of table column
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $table_headcol_link_location = ".//*[@id='main-table']/thead/tr/th/a[@data-name = '%s']";

	/**
	 * Location of main table and arrow column
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $main_table                   = ".//*[@id='main-table']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $table_headcol_arrow_location = ".//*/table/thead/tr/th[%s]/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_list_id       = "filter_search_filter_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_field         = ".//*[@id='filter_search']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_list          = ".//*[@id='filter_search_filter_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $search_button        = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[1]/button";

	// Filter bar

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filterbar_button     = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[2]/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $filter_bar_open      = ".//*[@id='j-main-container']/div[1]/div[2]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $clear_button         = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[3]/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $null_row             = ".//*/table/tbody/tr/td";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $null_msg             = "There are no data available";

	// Filter status

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $status_list_id       = "filter_published_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $status_list          = ".//*[@id='filter_published_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $status_none          = ".//*[@id='filter_published_chzn']/div/ul/li[text()='- Select Status -']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $status_unpublished   = ".//*[@id='filter_published_chzn']/div/ul/li[text()='unpublished']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $status_published     = ".//*[@id='filter_published_chzn']/div/ul/li[text()='published']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $icon_unpublished     = ".//*[@id='j-main-container']/*/span[contains(@class, 'icon-unpublish')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $icon_published       = ".//*[@id='j-main-container']/*/span[contains(@class, 'icon-publish')]";

	// filter identifiers

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $publish_row          = ".//*[@id='main-table']/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-publish')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $unpublish_row        = ".//*[@id='main-table']/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-unpublish')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $attachment_row       = ".//*[@id='main-table']/tbody/tr[%s]/td[2]/span[contains(@class, 'icon_attachment')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $template_yes_row          = ".//*[@id='main-table']/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-featured')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $template_no_row        = ".//*[@id='main-table']/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-unfeatured')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $null_date            = '0000-00-00 00:00:00';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $table_header         = ".//*/thead";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $pagination_bar       = '.pagination.pagination-toolbar';

		// Filter access

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_column        = ".//*/td[5]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_list_id       = "filter_access_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_list          = ".//*[@id='filter_access_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_none          = ".//*[@id='filter_access_chzn']/div/ul/li[text()='- Select Access -']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_public        = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Public']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_guest         = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Guest']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_registered    = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Registered']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_special       = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Special']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_super         = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Super Users']";

	// list ordering

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ordering_list        = ".//*[@id='list_fullordering_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ordering_value       = ".//*[@id='list_fullordering_chzn']/div/ul/li[text()='";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $ordering_id          = "list_fullordering_chzn";

	// list limit

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_list_id        = "list_limit_chzn";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_list           = ".//*[@id='list_limit_chzn']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_5              = ".//*[@id='list_limit_chzn']/div/ul/li[text()='5']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_10             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='10']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_15             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='15']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_20             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='20']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_25             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='25']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $limit_30             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='30']";

	// Pagination
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $first_page           = ".//*/a/span[contains(@class, 'icon-first')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $prev_page            = ".//*/a/span[contains(@class, 'icon-previous')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $next_page            = ".//*/a/span[contains(@class, 'icon-next')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $last_page            = ".//*/a/span[contains(@class, 'icon-last')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $page_1               = ".//*/div/nav/ul/li/a[contains(text(), '1')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $page_2               = ".//*/div/nav/ul/li/a[contains(text(), '2')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $page_3               = ".//*/div/nav/ul/li/a[contains(text(), '3')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $last_page_identifier = ".//*/ul[contains(@class, 'pagination-list')]/li[contains(@class, 'active')]/span";


	// buttons
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_red   = 'btn active btn-danger';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_green = 'btn active btn-success';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $button_grey  = 'btn';

	// General error messages
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $msg_edit_no_permission   = "No permission to edit this item!";

	/**
	 * Variables for selecting mailinglists
	 * Hint: Use with sprintf <nbr> for wanted row
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $mls_accessible       = ".//*[@id='jform_ml_available_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_nonaccessible    = ".//*[@id='jform_ml_unavailable_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_internal         = ".//*[@id='jform_ml_intern_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_usergroup        = ".//*[@id='1group_2']";

	/**
	 * General messages
	 * /

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $invalidField        = "Invalid field:  	";


	/**
	 * Plugin related
	 * /

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_disabled_success  = 'Plugin successfully disabled';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_enabled_success   = 'Plugin successfully enabled';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_saved_success     = 'Plugin saved';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $plugin_page                      = "/administrator/index.php?option=com_plugins";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $view_plugin                      = "Plugins";



	/**
	 * Basic route example for your current URL
	 * You can append any additional parameter to URL
	 * and use it in tests like: Page\Edit::route('/123-post');
	 *
	 * @param   string  $param  page to route to
	 *
	 * @return  string  new url
	 *
	 * @since   2.0.0
	 */
	public static function route($param)
	{
		return static::$url . $param;
	}

	/**
	 * Method to get install file name
	 *
	 * @return     string
	 *
	 * @since  2.0.0
	 */
	public static function getInstallFileName()
	{
		return '/Support/Software/Joomla/BwPostman/' . self::$versionToTest . '/com_bwpostman/com_bwpostman.' . self::$versionToTest . '.zip';
	}

	/**
	 * Test method to preset the options of the module
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since   3.1.5
	 */
	public static function presetComponentOptions(AcceptanceTester $I)
	{
		// Basic settings
		$I->setManifestOption('com_bwpostman', 'default_from_name', '');
		$I->setManifestOption('com_bwpostman', 'default_from_email', '');
		$I->setManifestOption('com_bwpostman', 'default_reply_email', '');
		$I->setManifestOption('com_bwpostman', 'legal_information_text', '');
		$I->setManifestOption('com_bwpostman', 'excluded_categories', '');
		$I->setManifestOption('com_bwpostman', 'default_mails_per_pageload', '100');
		$I->setManifestOption('com_bwpostman', 'mails_per_pageload_delay', '1');
		$I->setManifestOption('com_bwpostman', 'mails_per_pageload_delay_unit', '1000');
		$I->setManifestOption('com_bwpostman', 'publish_nl_by_default', '0');
		$I->setManifestOption('com_bwpostman', 'compress_backup', '1');
		$I->setManifestOption('com_bwpostman', 'show_boldt_link', '1');
		$I->setManifestOption('com_bwpostman', 'loglevel', 'ERROR');

		// Registration form
		$I->setManifestOption('com_bwpostman', 'pretext', 'Introtext to registration by component');
		$I->setManifestOption('com_bwpostman', 'show_gender', '0');
		$I->setManifestOption('com_bwpostman', 'show_firstname_field', '1');
		$I->setManifestOption('com_bwpostman', 'firstname_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_name_field', '1');
		$I->setManifestOption('com_bwpostman', 'name_field_obligation', '1');
		$I->setManifestOption('com_bwpostman', 'show_special', '0');
		$I->setManifestOption('com_bwpostman', 'special_field_obligation', '0');
		$I->setManifestOption('com_bwpostman', 'special_label', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'special_desc', 'Mitgliedsnummer');
		$I->setManifestOption('com_bwpostman', 'show_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'default_emailformat', '1');
		$I->setManifestOption('com_bwpostman', 'verify_mailaddress', '0');
		$I->setManifestOption('com_bwpostman', 'show_desc', '1');
		$I->setManifestOption('com_bwpostman', 'desc_length', '150');
		$I->setManifestOption('com_bwpostman', 'disclaimer', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer_selection', '0');
		$I->setManifestOption('com_bwpostman', 'disclaimer_link', 'https://www.disclaimer.de/disclaimer.htm');
		$I->setManifestOption('com_bwpostman', 'article_id', '70');
		$I->setManifestOption('com_bwpostman', 'disclaimer_menuitem', '437');
		$I->setManifestOption('com_bwpostman', 'disclaimer_target', '0');
		$I->setManifestOption('com_bwpostman', 'showinmodal', '1');
		$I->setManifestOption('com_bwpostman', 'use_captcha', '0');
		$I->setManifestOption('com_bwpostman', 'security_question', 'Wieviele Beine hat ein Pferd? (1, 2, ...)');
		$I->setManifestOption('com_bwpostman', 'security_answer', '4');

		// Activation
		$I->setManifestOption('com_bwpostman', 'activation_salutation_text', '');
		$I->setManifestOption('com_bwpostman', 'activation_text', '');
		$I->setManifestOption('com_bwpostman', 'permission_text', '');
		$I->setManifestOption('com_bwpostman', 'activation_to_webmaster', '0');
		$I->setManifestOption('com_bwpostman', 'activation_from_name', '');
		$I->setManifestOption('com_bwpostman', 'activation_to_webmaster_email', '');

		// Unsubscription
		$I->setManifestOption('com_bwpostman', 'del_sub_1_click', '0');
		$I->setManifestOption('com_bwpostman', 'deactivation_to_webmaster', '0');
		$I->setManifestOption('com_bwpostman', 'deactivation_from_name', '');
		$I->setManifestOption('com_bwpostman', 'deactivation_to_webmaster_email', '');

		// Lists view
		$I->setManifestOption('com_bwpostman', 'filter_field', '1');
		$I->setManifestOption('com_bwpostman', 'date_filter_enable', '1');
		$I->setManifestOption('com_bwpostman', 'ml_filter_enable', '1');
		$I->setManifestOption('com_bwpostman', 'cam_filter_enable', '1');
		$I->setManifestOption('com_bwpostman', 'group_filter_enable', '1');
		$I->setManifestOption('com_bwpostman', 'attachment_enable', '1');
		$I->setManifestOption('com_bwpostman', 'access-check', '1');
		$I->setManifestOption('com_bwpostman', 'display_num', '10');

		// Single view
		$I->setManifestOption('com_bwpostman', 'attachment_single_enable', '1');
		$I->setManifestOption('com_bwpostman', 'subject_as_title', '1');
	}

	/**
	 * Method to get all options of component from manifest
	 *
	 * @param       object      $options
	 *
	 * @since  2.0.0
	 */
	public static function setComponentOptions($options)
	{
		self::$com_options = $options;
	}

	/**
	 * @param \AcceptanceTester $I
	 *
	 * @throws \Exception
	 *
	 * @since version
	 */
	public static function dontSeeAnyWarning(\AcceptanceTester $I)
	{
		$I->waitForElement(self::$alert_header, 30);

		$I->dontSee(self::$alert_warn_txt, self::$alert);
		$I->dontSee(self::$alert_error_txt, self::$alert);

		$I->dontSeeElement(self::$alert_warn);
		$I->dontSeeElement(self::$alert_error);
	}
}
