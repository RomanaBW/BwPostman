<?php
namespace Page;

/**
 * Class Generals
 *
 * Class to hold generally needed properties and methods
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
class Generals
{
    // include url of current page
    public static $url          = '/administrator/index.php?option=com_bwpostman';
	public static $archive_url  = '/administrator/index.php?option=com_bwpostman&view=archive&layout=newsletters';

	public static $control_panel        = "Control Panel";
	public static $login_txt            = "Log in";
	public static $nav_user_menu        = "html/body/nav/div/div/div/ul[3]/li/a";
	public static $nav_user_menu_logout = "html/body/nav/div/div/div/ul[3]/li/ul/li[5]/a";
	public static $logout_txt           = "Log out";

	public static $com_options;

	public static $sys_message_container    = ".//*[@id='system-message-container']";
	public static $media_frame              = "mediaFrame";
	public static $image_frame              = "imageframe";

	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	protected $tester;

	// backend users
	public static $admin        = array('user' =>'AdminTester', 'password' =>'BwPostmanTest', 'author' => 'AdminTester');

	/* ssh and database connection
	 *
	 * These tests are designed to run on a remote machine by a local machine,
	 * i.e. a local test machine runs the test on the remote server ( may be in
	 * the same local network). For this there is used a ssh connection to initialize
	 * the database, because Codeceptions database skills are very modest and slow.
	 *
	 * Therefore I use straight console commands to initialize an reset the database.
	 *
	*/
	public static $ssh_server       = 'universe';
	public static $ssh_port         = 22;
	public static $ssh_user         = 'romana';
	public static $ssh_options      = array('hostkey' => 'ssh-rsa');
	public static $ssh_key_pub      = '/home/romana/.ssh/romana_rsa.pub';
	public static $ssh_key_rsa      = '/home/romana/.ssh/romana_rsa';

	public static $db_data_path     = '/daten/vhosts/dev/joomla-cms/tests/_data/';
	public static $db_data_start    = 'testdata_bwpostman_complete.sql';
	public static $db_data_end      = 'testdata_bwpostman_reset.sql';

	public static $extension            = "BwPostman";
	public static $first_list_entry     = ".//*[@id='cb0']";

	/*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

	public static $pageTitle        = 'h1.page-title';
	public static $alert_header     = 'h4.alert-heading';
	public static $alert_heading    = "//*[@id='system-message-container']/div[1]/h4";
	public static $alert            = 'div.alert';
	public static $alert_success    = 'div.alert-success';
	public static $alert_msg        = 'div.alert-message';
	public static $alert_warn       = 'div.alert-warning';
	public static $alert_error      = 'div.alert-error';

	public static $alert_success_txt    = 'Success';
	public static $alert_msg_txt        = 'Message';
	public static $alert_warn_txt       = 'Warning';
	public static $alert_error_txt      = 'Error';

	public static $archive_alert_success = 'div.alert-success > div.alert-message';
	public static $archive_txt           = 'Archive';

	public static $header           = '/html/body/div[2]/section/div/div/div[2]/form/div/fieldset/legend';
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
		'BwPostman'     => ".//*[@id='submenu']/li[1]/a",
		'Newsletters'   => ".//*[@id='submenu']/li[2]/a",
		'Subscribers'   => ".//*[@id='submenu']/li[3]/a",
		'Campaigns'     => ".//*[@id='submenu']/li[4]/a",
		'Mailinglists'  => ".//*[@id='submenu']/li[5]/a",
		'Templates'     => ".//*[@id='submenu']/li[6]/a",
		'Archive'       => ".//*[@id='submenu']/li[7]/a",
		'Maintenance'   => ".//*[@id='submenu']/li[8]/a",
	);

	/**
	 * Array of toolbar id values for list page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'New'               => ".//*[@id='toolbar-new']/button",
		'Edit'              => ".//*[@id='toolbar-edit']/button",
		'Publish'           => ".//*[@id='toolbar-publish']/button",
		'Unpublish'         => ".//*[@id='toolbar-unpublish']/button",
		'Archive'           => ".//*[@id='toolbar-archive']/button",
		'Help'              => ".//*[@id='toolbar-help']/button",
		'Duplicate'         => ".//*[@id='toolbar-copy']/button",
		'Send'              => ".//*[@id='toolbar-envelope']/button",
		'Add HTML-Template' => ".//*[@id='toolbar-calendar']/button",
		'Add Text-Template' => ".//*[@id='toolbar-new']/button",
		'Default'           => ".//*[@id='toolbar-default']/button",
		'Check-In'          => ".//*[@id='toolbar-checkin']/button",
		'Install-Template'  => ".//*[@id='toolbar-custom']/a",
		'Options'           => ".//*[@id='toolbar-options']/button",
		'Save'              => ".//*[@id='toolbar-apply']/button",
		'Save & Close'      => ".//*[@id='toolbar-save']/button",
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
	public static $select_list_selected_location = ".//*[@id='list_fullordering_chzn']/a/span";

	/**
	 * Location of table column
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $table_headcol_link_location = ".//*/table/thead/tr/th[%s]/a";

	/**
	 * Location of table column arrow
	 *
	 * @var string
	 *
	 * @since   2.0.0
	 */
	public static $table_headcol_arrow_location = ".//*/table/thead/tr/th[%s]/a/span";

	public static $search_field         = ".//*[@id='filter_search']";
	public static $search_list          = ".//*[@id='filter_search_filter_chzn']/a";
	public static $search_button        = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[1]/button";


	// Filter bar
	public static $filterbar_button     = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[2]/button";
	public static $clear_button         = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[3]/button";
	public static $null_row             = ".//*/table/tbody/tr/td";
	public static $null_msg             = "There are no data available";

	// Filter status
	public static $status_list          = ".//*[@id='filter_published_chzn']/a";
	public static $status_none          = ".//*[@id='filter_published_chzn']/div/ul/li[text()='- Select Status -']";
	public static $status_unpublished   = ".//*[@id='filter_published_chzn']/div/ul/li[text()='unpublished']";
	public static $status_published     = ".//*[@id='filter_published_chzn']/div/ul/li[text()='published']";
	public static $icon_unpublished     = ".//*[@id='j-main-container']/*/span[contains(@class, 'icon-unpublish')]";
	public static $icon_published       = ".//*[@id='j-main-container']/*/span[contains(@class, 'icon-publish')]";

	// filter identifiers
	public static $publish_row          = ".//*[@id='j-main-container']/div[2]/table/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-publish')]";
	public static $unpublish_row        = ".//*[@id='j-main-container']/div[2]/table/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-unpublish')]";
	public static $attachment_row       = ".//*[@id='j-main-container']/div[4]/table/tbody/tr[%s]/td[2]/span[contains(@class, 'icon_attachment')]";
	public static $null_date            = '0000-00-00 00:00:00';
	public static $table_header         = ".//*/thead";
	public static $pagination_bar       = '.pagination.pagination-toolbar';

		// Filter access
	public static $access_column        = ".//*/td[5]";
	public static $access_list          = ".//*[@id='filter_access_chzn']/a";
	public static $access_none          = ".//*[@id='filter_access_chzn']/div/ul/li[text()='- Select Access -']";
	public static $access_public        = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Public']";
	public static $access_guest         = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Guest']";
	public static $access_registered    = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Registered']";
	public static $access_special       = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Special']";
	public static $access_super         = ".//*[@id='filter_access_chzn']/div/ul/li[text()='Super Users']";

	// list ordering
	public static $ordering_list        = ".//*[@id='list_fullordering_chzn']/a";
	public static $ordering_value       = ".//*[@id='list_fullordering_chzn']/div/ul/li[text()='";

	// list limit
	public static $limit_list           = ".//*[@id='list_limit_chzn']/a";
	public static $limit_5              = ".//*[@id='list_limit_chzn']/div/ul/li[text()='5']";
	public static $limit_10             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='10']";
	public static $limit_15             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='15']";
	public static $limit_20             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='20']";
	public static $limit_25             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='25']";
	public static $limit_30             = ".//*[@id='list_limit_chzn']/div/ul/li[text()='30']";

	// Pagination
	public static $first_page           = ".//*/a/span[contains(@class, 'icon-first')]";
	public static $prev_page            = ".//*/a/span[contains(@class, 'icon-previous')]";
	public static $next_page            = ".//*/a/span[contains(@class, 'icon-next')]";
	public static $last_page            = ".//*/a/span[contains(@class, 'icon-last')]";
	public static $page_1               = ".//*/a[text()='1']";
	public static $page_2               = ".//*/a[text()='2']";
	public static $page_3               = ".//*/a[text()='3']";


	// buttons
	public static $button_red   = 'btn active btn-danger';
	public static $button_green = 'btn active btn-success';
	public static $button_grey  = 'btn';

	/**
	 * Variables for selecting mailinglists
	 * Hint: Use with sprintf <nbr> for wanted row
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $mls_accessible       = ".//*[@id='jform_ml_available_%s']";
	public static $mls_nonaccessible    = ".//*[@id='jform_ml_unavailable_%s']";
	public static $mls_internal         = ".//*[@id='jform_ml_intern_%s']";


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
        return static::$url.$param;
    }

	/**
	 * Method to get install file name
	 *
	 * @return     string
	 *
	 * @since  2.0.0
	 */
	public static function getInstallFileName ()
	{
		return '/Support/Software/Joomla/BwPostman/' . self::$versionToTest . '/com_bwpostman/com_bwpostman.' . self::$versionToTest . '.zip';
	}

	/**
	 * Method to get all options of component from manifest
	 *
	 * @param       object      $options
	 *
	 * @since  2.0.0
	 */
	public static function setComponentOptions ($options)
	{
//		$Helper = new Helper();
		self::$com_options = $options;
	}

}
