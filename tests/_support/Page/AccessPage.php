<?php
namespace Page;

/**
 * Class AccessPage
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
class AccessPage
{
	/**
	 * set array with all users
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $all_users = array(
		array('user' => 'BwPostmanAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanAdmin', 'half' => 1),
		array('user' => 'BwPostmanManager', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanManager', 'half' => 1),
		array('user' => 'BwPostmanPublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanPublisher', 'half' => 1),
		array('user' => 'BwPostmanEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanEditor', 'half' => 2),
		array('user' => 'BwPostmanCampaignAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanCampaignAdmin', 'half' => 2),
		array('user' => 'BwPostmanCampaignPublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanCampaignPublisher', 'half' => 2),
		array('user' => 'BwPostmanCampaignEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanCampaignEditor', 'half' => 2),
		array('user' => 'BwPostmanMailinglistAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanMailinglistAdmin', 'half' => 2),
		array('user' => 'BwPostmanMailinglistPublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanMailinglistPublisher', 'half' => 2),
		array('user' => 'BwPostmanMailinglistEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanMailinglistEditor', 'half' => 2),
		array('user' => 'BwPostmanNewsletterAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanNewsletterAdmin', 'half' => 3),
		array('user' => 'BwPostmanNewsletterPublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanNewsletterPublisher', 'half' => 3),
		array('user' => 'BwPostmanNewsletterEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanNewsletterEditor', 'half' => 3),
		array('user' => 'BwPostmanSubscriberAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanSubscriberAdmin', 'half' => 3),
		array('user' => 'BwPostmanSubscriberPublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanSubscriberPublisher', 'half' => 3),
		array('user' => 'BwPostmanSubscriberEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanSubscriberEditor', 'half' => 3),
		array('user' => 'BwPostmanTemplateAdmin', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanTemplateAdmin', 'half' => 3),
		array('user' => 'BwPostmanTemplatePublisher', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanTemplatePublisher', 'half' => 3),
		array('user' => 'BwPostmanTemplateEditor', 'password' => 'BwPostmanTest', 'author' => 'BwPostmanTemplateEditor', 'half' => 3),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $main_list_buttons   = array(
		'Newsletters'        => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Newsletters')]",
		'Subscribers'        => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Subscribers')]",
		'Campaigns'          => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Campaigns')]",
		'Mailinglists'       => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Mailinglists')]",
		'Templates'          => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Templates')]",
		'Archive'            => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Archive')]",
		'Basic settings'     => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Basic settings')]",
		'Maintenance'        => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Maintenance')]",
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $main_add_buttons   = array(
		'Newsletter'        => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add newsletter')]",
		'Subscriber'        => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add subscriber')]",
		'Test-Recipient'    => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add Test-Recipient')]",
		'Campaign'          => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add campaign')]",
		'Mailinglist'       => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add mailinglist')]",
		'HTML-Template'     => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add HTML-Template')]",
		'Text-Template'     => "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'Add Text-Template')]",
	);

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $forum_icon       = "//*/div[contains(@class,'bw-icons')]/div/div/div/a/span[contains(text(), 'BwPostman Forum')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $forum_button      = "//*[@id='toolbar-forum']/button";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $options_button   = "//*[@id='toolbar-options']/button";

	/**
	 * @var string
	 *
	 * @since 2.2.1
	 */
	public static $manual_button       = "//*[@id='toolbar-manual']/button";

	// statistics pane
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_statistics_general  = "//*[@id='bwpostman_statistic-pane']/div/a[contains(text(), 'General statistics')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $table_statistics_general = "//*[@id='bwpostman_statistic-pane']/div[1]/div/div/table";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $link_statistics_archive  = "//*/h2[@id='archive-heading']/button[contains(text(), 'Archive statistics')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $table_statistics_archive = "//*[@id='bwpostman_statistic-pane']/div[2]/div/div/table";

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $statistics_general   = array(
		'Newsletters'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Unsent newsletters:')]",
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Sent newsletters:')]",
		),
		'Subscribers'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Subscribers:')]",
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Test-Recipients:')]",
		),
		'Campaigns'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Campaigns:')]",
		),
		'Mailinglists'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Public mailinglists:')]",
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Internal mailinglists:')]",
		),
		'Templates'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# HTML-Templates:')]",
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Text-Templates:')]",
		),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $statistics_archive   = array(
		'Newsletters'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived newsletters:')]",
		),
		'Subscribers'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived subscribers:')]",
		),
		'Campaigns'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived campaigns:')]",
		),
		'Mailinglists'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived mailinglists:')]",
		),
		'Templates'       => array(
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived HTML-Templates:')]",
			"//*[@id='bwpostman_statistic-pane']/div/div/div/table/tbody/tr/td[contains(text(), '# Archived Text-Templates:')]",
		),
	);

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $j_menu_components     = "//*[@id='sidebarmenu']/nav/ul/li/a/span[contains(text(), 'Components')]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $j_menu_tags         = "//*[@id='sidebarmenu']/nav/ul/li/ul/li/a/span[contains(text(), 'Tags')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $j_menu_bwpostman      = "//*[@id='sidebarmenu']/nav/ul/li/ul/li/a/span[contains(text(), 'BwPostman')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $j_menu_bwpostman_link      = "//*[@id='sidebarmenu']/nav/ul/li/ul/li/a[contains(@href,'index.php?option=com_bwpostman')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $j_menu_bwpostman_sub         = "//*[@id='sidebarmenu']/nav/ul/li/ul/li/ul/li/a/span[contains(text(), 'Dashboard')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $j_menu_bwpostman_sub_item    = "//*[@id='sidebarmenu']/nav/ul/li/ul/li/ul/li/a/span[contains(text(), '%s')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $list_view_no_permission      = "No permission for view %s.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkbox_identifier  = "//*[@id='cb%s']";
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkout_icon        = "//*[@id='main-table']/tbody/tr[%s]/td[%s]/a/span[contains(@class, 'icon-checkedout')]";


	// set permission variables
	// set list view permission arrays
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanAdmin_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => true,
		'Campaigns'         => true,
		'Mailinglists'      => true,
		'Templates'         => true,
		'Archive'           => true,
		'Basic settings'    => true,
		'Maintenance'       => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanManager_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => true,
		'Campaigns'         => true,
		'Mailinglists'      => true,
		'Templates'         => true,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanPublisher_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => true,
		'Campaigns'         => true,
		'Mailinglists'      => true,
		'Templates'         => true,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanEditor_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => true,
		'Campaigns'         => true,
		'Mailinglists'      => true,
		'Templates'         => true,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignAdmin_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => true,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignPublisher_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => true,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignEditor_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => true,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistAdmin_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => true,
		'Templates'         => false,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistPublisher_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => true,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistEditor_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => true,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterAdmin_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterPublisher_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterEditor_main_list_permissions = array(
		'Newsletters'       => true,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberAdmin_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => true,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberPublisher_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => true,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberEditor_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => true,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => false,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateAdmin_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => true,
		'Archive'           => true,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplatePublisher_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => true,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateEditor_main_list_permissions = array(
		'Newsletters'       => false,
		'Subscribers'       => false,
		'Campaigns'         => false,
		'Mailinglists'      => false,
		'Templates'         => true,
		'Archive'           => false,
		'Basic settings'    => false,
		'Maintenance'       => false,
	);

	/**
	 * set add permission arrays
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanAdmin_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => true,
		'Mailinglist'       => true,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanManager_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => true,
		'Mailinglist'       => true,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanPublisher_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => true,
		'Mailinglist'       => true,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanEditor_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => true,
		'Mailinglist'       => true,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignAdmin_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => true,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignPublisher_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => true,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignEditor_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => true,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistAdmin_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => true,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistPublisher_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => true,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistEditor_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => true,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterAdmin_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterPublisher_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterEditor_main_add_permissions = array(
		'Newsletter'        => true,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberAdmin_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberPublisher_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberEditor_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => true,
		'Test-Recipient'    => true,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => false,
		'Text-Template'     => false,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateAdmin_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplatePublisher_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateEditor_main_add_permissions = array(
		'Newsletter'        => false,
		'Subscriber'        => false,
		'Test-Recipient'    => false,
		'Campaign'          => false,
		'Mailinglist'       => false,
		'HTML-Template'     => true,
		'Text-Template'     => true,
	);

	/**
	 * set list action permission arrays
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanAdmin_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 1,
						'check content' => "Template Gedicht 1",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
						'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span",
						'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
						'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span",
						'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
					),
				'publish_by_toolbar'   => array(
						'publish_button'    => "//*[@id='cb0']",
						'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
						'unpublish_button'  => "//*[@id='cb0']",
						'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
					),
			),

		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 19,
						'check content' => "Barton",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 1,
						'check content' => "01 Kampagne 2 A",
					),
				'other'   =>
					array(
						'itemid'        => 19,
						'check content' => "04 Kampagne 12 A",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 17,
						'check content' => "05 Mailingliste 18 A",
					),
				'other'   =>
					array(
						'itemid'        => 3,
						'check content' => "01 Mailingliste 4 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 7,
						'check content' => "Standard Deep Blue",
					),
				'other'   =>
					array(
						'itemid'        => 8,
						'check content' => "Standard Soft Blue",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,

		'Maintenance'         =>
			array(
				'permissions'       =>
					array(
						'Admin' => true,
					),
			),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanManager_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 122,
						'check content' => "Test Newsletter single 4",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 62,
						'check content' => "Bernd",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 2,
						'check content' => "01 Kampagne 3 A",
					),
				'other'   =>
					array(
						'itemid'        => 19,
						'check content' => "04 Kampagne 12 A",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'other'   =>
					array(
						'itemid'        => 3,
						'check content' => "01 Mailingliste 4 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 17,
						'check content' => "Z Standard Basic",
					),
				'other'   =>
					array(
						'itemid'        => 2,
						'check content' => "Standard Soft Blue",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,

		'Maintenance'         =>
			array(
				'permissions'       =>
					array(
						'Admin' => false,
					),
			),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanPublisher_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 3,
						'check content' => "Template Gedicht 3",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'other'   =>
					array(
						'itemid'        => 62,
						'check content' => "Bernd",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 20,
						'check content' => "04 Kampagne 12 B",
					),
				'other'   =>
					array(
						'itemid'        => 18,
						'check content' => "03 Kampagne 10 B",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 3,
						'check content' => "01 Mailingliste 4 A",
					),
				'other'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 1,
						'check content' => "Standard Deep Blue",
					),
				'other'   =>
					array(
						'itemid'        => 3,
						'check content' => "Standard Creme",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,

		'Maintenance'         =>
			array(
				'permissions'       =>
					array(
						'Admin' => false,
					),
			),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanEditor_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 120,
						'check content' => "Test Newsletter single 2",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 1093,
						'check content' => "Borst",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 43,
						'check content' => "05 Kampagne 18 A",
					),
				'other'   =>
					array(
						'itemid'        => 18,
						'check content' => "03 Kampagne 10 B",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 6,
						'check content' => "02 Mailingliste 7 A",
					),
				'other'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 3,
						'check content' => "Standard Creme",
					),
				'other'   =>
					array(
						'itemid'        => 17,
						'check content' => "Z Standard Basic",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,

		'Maintenance'         =>
			array(
				'permissions'       =>
					array(
						'Admin' => false,
					),
			),
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignAdmin_item_permissions = array(
		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 45,
						'check content' => "05 Kampagne 19 A Test",
					),
				'other'   =>
					array(
						'itemid'        => 18,
						'check content' => "03 Kampagne 10 B",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignPublisher_item_permissions = array(
		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 47,
						'check content' => "05 Kampagne 20 A Test",
					),
				'other'   =>
					array(
						'itemid'        => 18,
						'check content' => "03 Kampagne 10 B",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanCampaignEditor_item_permissions = array(
		'Campaigns'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 18,
						'check content' => "03 Kampagne 10 B",
					),
				'other'   =>
					array(
						'itemid'        => 47,
						'check content' => "05 Kampagne 20 A Test",
					),
				'check column'  => "Campaign title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistAdmin_item_permissions = array(
		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 8,
						'check content' => "02 Mailingliste 9 A",
					),
				'other'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistPublisher_item_permissions = array(
		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 9,
						'check content' => "03 Mailingliste 10 A",
					),
				'other'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanMailinglistEditor_item_permissions = array(
		'Mailinglists'      =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 11,
						'check content' => "03 Mailingliste 12 A",
					),
				'other'   =>
					array(
						'itemid'        => 14,
						'check content' => "04 Mailingliste 15 A",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterAdmin_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 124,
						'check content' => "Test Newsletter single 6",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterPublisher_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 127,
						'check content' => "Test Newsletter single 9",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanNewsletterEditor_item_permissions = array(
		'Newsletters'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
						'SendNewsletter'    => true,
					),
				'own'   =>
					array(
						'itemid'        => 129,
						'check content' => "Test Newsletter single 11",
					),
				'other'   =>
					array(
						'itemid'        => 169,
						'check content' => "Newsletter for testing 18",
					),
				'check column'  => "Subject",
				'check locator' => "//*[@id='jform_subject']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[8]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberAdmin_item_permissions = array(
		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 1794,
						'check content' => "Cramer",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberPublisher_item_permissions = array(
		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 2310,
						'check content' => "Albers",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanSubscriberEditor_item_permissions = array(
		'Subscribers'       =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 777,
						'check content' => "Acar",
					),
				'other'   =>
					array(
						'itemid'        => 1148,
						'check content' => "Andres",
					),
				'check column'  => "Last name",
				'check locator' => "//*[@id='jform_name']",
				'check link'    => "//*[@id='main-table-bw-confirmed']/tbody/tr/td/a[contains(text(), '%s')]",
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateAdmin_item_permissions = array(
		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => true,
						'Restore'           => true,
						'Delete'            => true,
					),
				'own'   =>
					array(
						'itemid'        => 20,
						'check content' => "Z Standard Soft Blue",
					),
				'other'   =>
					array(
						'itemid'        => 1,
						'check content' => "Standard Deep Blue",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplatePublisher_item_permissions = array(
		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => true,
						'EditOwn'           => true,
						'ModifyState'       => true,
						'ModifyStateOwn'    => true,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 25,
						'check content' => "Z2 Template for Test 01",
					),
				'other'   =>
					array(
						'itemid'        => 1,
						'check content' => "Standard Deep Blue",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $BwPostmanTemplateEditor_item_permissions = array(
		'Templates'         =>
			array(
				'permissions'       =>
					array(
						'Create'            => true,
						'Edit'              => false,
						'EditOwn'           => true,
						'ModifyState'       => false,
						'ModifyStateOwn'    => false,
						'Archive'           => false,
						'Restore'           => false,
						'Delete'            => false,
					),
				'own'   =>
					array(
						'itemid'        => 21,
						'check content' => "Z Standard TEXT Template 1",
					),
				'other'   =>
					array(
						'itemid'        => 1,
						'check content' => "Standard Deep Blue",
					),
				'check column'  => "Title",
				'check locator' => "//*[@id='jform_title']",
				'check link'    => "//*[@id='main-table']/tbody/tr/td/a[contains(text(), '%s')]",
				'publish_by_icon'   => array(
					'publish_button'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
				'publish_by_toolbar'   => array(
					'publish_button'    => "//*[@id='cb0']",
					'publish_result'    => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-publish')]",
					'unpublish_button'  => "//*[@id='cb0']",
					'unpublish_result'  => "//*[@id='main-table']/tbody/tr[1]/td[6]/*/span[contains(@class, 'icon-unpublish')]",
				),
			),

		'Archive'           => true,
	);

	// publish mailinglists

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_icon   = array(
		'publish_button'    => "////*[@id='main-table']/tbody/tr[3]/td[4]/*",
		'publish_result'    => "////*[@id='main-table']/tbody/tr[3]/td[4]/*/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "////*[@id='main-table']/tbody/tr[4]/td[4]/*",
		'unpublish_result'  => "////*[@id='main-table']/tbody/tr[4]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
	);

	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $publish_by_toolbar   = array(
		'publish_button'    => "//*[@id='cb5']",
		'publish_result'    => "////*[@id='main-table']/tbody/tr[6]/td[4]/*/span[contains(@class, 'icon-publish')]",
		'unpublish_button'  => "//*[@id='cb6']",
		'unpublish_result'  => "////*[@id='main-table']/tbody/tr[7]/td[4]/*/span[contains(@class, 'icon-unpublish')]",
	);



	// set array with direct links to parts of BwPostman
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $direct_links = array (
		'direct_link_cam_lists' => '/administrator/index.php?option=com_bwpostman&view=campaigns',
		'direct_link_cam_create' => '/administrator/index.php?option=com_bwpostman&view=campaign&task=add',
		'direct_link_cam_edit_allowed' => '/administrator/index.php?option=com_bwpostman&view=campaign&layout=edit&id=18',
		'direct_link_cam_edit_forbidden' => '/administrator/index.php?option=com_bwpostman&view=campaign&layout=edit&id=1',
		'direct_link_cam_archive' => '/administrator/index.php?option=com_bwpostman&controller=campaigns&tmpl=component&view=campaigns&&layout=default_confirmarchive',
		'direct_link_cam_checkin' => '/administrator/index.php?option=com_bwpostman&view=campaigns&task=campaigns.checkin',

		'direct_link_ml_lists' => '/administrator/index.php?option=com_bwpostman&view=mailinglists',
		'direct_link_ml_create' => '/administrator/index.php?option=com_bwpostman&view=mailinglist&task=add',
		'direct_link_ml_edit_allowed' => '/administrator/index.php?option=com_bwpostman&view=mailinglist&layout=edit&id=11',
		'direct_link_ml_edit_forbidden' => '/administrator/index.php?option=com_bwpostman&view=mailinglist&layout=edit&id=1',
		'direct_link_ml_archive' => '/administrator/index.php?option=com_bwpostman&controller=mailinglist&task=mailinglist.archive',
		'direct_link_ml_checkin' => '/administrator/index.php?option=com_bwpostman&view=mailinglist&',

		'direct_link_nl_lists' => '/administrator/index.php?option=com_bwpostman&view=newsletters',
		'direct_link_nl_create' => '/administrator/index.php?option=com_bwpostman&view=newsletter&task=add&layout=edit_basic',
		'direct_link_nl_edit_allowed' => '/administrator/index.php?option=com_bwpostman&view=newsletter&layout=edit&id=119',
		'direct_link_nl_edit_forbidden' => '/administrator/index.php?option=com_bwpostman&view=newsletter&layout=edit&id=1',
		'direct_link_nl_archive' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',
		'direct_link_nl_checkin' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',
		'direct_link_nl_send' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',
		'direct_link_nl_copy' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',
		'direct_link_nl_publish' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',
		'direct_link_nl_unpublish' => '/administrator/index.php?option=com_bwpostman&view=newsletter&',

		'direct_link_subs_lists' => '/administrator/index.php?option=com_bwpostman&view=subscribers',
		'direct_link_subs_create' => '/administrator/index.php?option=com_bwpostman&view=subscriber&task=add',
		'direct_link_subs_edit_allowed' => '/administrator/index.php?option=com_bwpostman&view=subscriber&layout=edit&id=',
		'direct_link_subs_edit_forbidden' => '/administrator/index.php?option=com_bwpostman&view=subscriber&layout=edit&id=',
		'direct_link_subs_archive' => '/administrator/index.php?option=com_bwpostman&view=subscriber&',
		'direct_link_subs_checkin' => '/administrator/index.php?option=com_bwpostman&view=subscriber&',
		'direct_link_subs_import' => '/administrator/index.php?option=com_bwpostman&view=subscriber&',
		'direct_link_subs_export' => '/administrator/index.php?option=com_bwpostman&view=subscriber&',
		'direct_link_subs_batch' => '/administrator/index.php?option=com_bwpostman&view=subscriber&',

		'direct_link_tpl_lists' => '/administrator/index.php?option=com_bwpostman&view=templates',
		'direct_link_tpl_create_html' => '/administrator/index.php?option=com_bwpostman&view=template&layout=default_html',
		'direct_link_tpl_create_text' => '/administrator/index.php?option=com_bwpostman&view=template&layout=default_text',
		'direct_link_tpl_edit_allowed' => '/administrator/index.php?option=com_bwpostman&view=template&layout=edit&id=',
		'direct_link_tpl_edit_forbidden' => '/administrator/index.php?option=com_bwpostman&view=template&layout=edit&id=',
		'direct_link_tpl_archive' => '/administrator/index.php?option=com_bwpostman&view=template&',
		'direct_link_tpl_checkin' => '/administrator/index.php?option=com_bwpostman&view=template&',
		'direct_link_tpl_set_default' => '/administrator/index.php?option=com_bwpostman&view=template&',
		'direct_link_tpl_install' => '/administrator/index.php?option=com_bwpostman&view=template&',

		'direct_link_maintenance_view' => '/administrator/index.php?option=com_bwpostman&view=maintenance',
		'direct_link_maintenance_save' => '/administrator/index.php?option=com_bwpostman&view=maintenance&task=maintenance.saveTables',
		'direct_link_maintenance_restore' => '/administrator/index.php?option=com_bwpostman&view=maintenance&task=maintenance.restoreTables',
		'direct_link_maintenance_check' => '/administrator/index.php?option=com_bwpostman&view=maintenance&layout=checkTables',
		'direct_link_maintenance_options' => '/administrator/index.php?option=com_config&view=component&component=com_bwpostman&path=',

		'direct_link_options' => '/administrator/index.php?option=com_config&view=component&component=com_bwpostman',
		);

	// set array with button links from main view of BwPostman to parts of BwPostman
	// @ToDo: Fill with values
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $button_links = array (
									'button_link_cam_lists' => '',
									'button_link_cam_create' => '',
									'button_link_cam_edit_allowed' => '',
									'button_link_cam_edit_forbidden' => '',
									'button_link_cam_archive' => '',
									'button_link_cam_checkin' => '',

									'button_link_ml_lists' => '',
									'button_link_ml_create' => '',
									'button_link_ml_edit_allowed' => '',
									'button_link_ml_edit_forbidden' => '',
									'button_link_ml_archive' => '',
									'button_link_ml_checkin' => '',

									'button_link_nl_lists' => '',
									'button_link_nl_create' => '',
									'button_link_nl_edit_allowed' => '',
									'button_link_nl_edit_forbidden' => '',
									'button_link_nl_archive' => '',
									'button_link_nl_checkin' => '',
									'button_link_nl_send' => '',
									'button_link_nl_copy' => '',
									'button_link_nl_publish' => '',
									'button_link_nl_unpublish' => '',

									'button_link_subs_lists' => '',
									'button_link_subs_create' => '',
									'button_link_subs_edit_allowed' => '',
									'button_link_subs_edit_forbidden' => '',
									'button_link_subs_archive' => '',
									'button_link_subs_checkin' => '',
									'button_link_subs_import' => '',
									'button_link_subs_export' => '',
									'button_link_subs_batch' => '',

									'button_link_tpl_lists' => '',
									'button_link_tpl_create' => '',
									'button_link_tpl_edit_allowed' => '',
									'button_link_tpl_edit_forbidden' => '',
									'button_link_tpl_archive' => '',
									'button_link_tpl_checkin' => '',
									'button_link_tpl_set_default' => '',
									'button_link_tpl_install' => '',

									'button_link_maintenance_view' => '',
									'button_link_maintenance_save' => '',
									'button_link_maintenance_restore' => '',
									'button_link_maintenance_check' => '',
								);

	// set arrays possible results of each link
	// @ToDo: Fill with values
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $possible_link_results = array (
												'direct_link_cam_lists' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Campaigns.',
												),
												'direct_link_cam_create' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_cam_edit' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_cam_archive' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_cam_checkin' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_ml_lists' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Mailinglists.',
												),
												'direct_link_ml_create' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_ml_edit' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_ml_archive' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_ml_checkin' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_nl_lists' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Newsletters.',
												),
												'direct_link_nl_create' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_edit' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_archive' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_checkin' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_send' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_copy' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_publish' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_nl_unpublish' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_subs_lists' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Subscribers.',
												),
												'direct_link_subs_create' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_edit' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_archive' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_checkin' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_import' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_export' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_subs_batch' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_tpl_lists' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Templates.',
												),
												'direct_link_tpl_create_html' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_create_text' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_edit' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_archive' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_checkin' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_set_default' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_tpl_install' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_maintenance_view' => array(
													'allowed'   => '',
													'forbidden' => 'No permission for view Maintenance.',
												),
												'direct_link_maintenance_save' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_maintenance_restore' => array(
													'allowed'   => '',
													'forbidden' => '',
												),
												'direct_link_maintenance_check' => array(
													'allowed'   => '',
													'forbidden' => '',
												),

												'direct_link_options' => array(
													'allowed'   => '',
													'forbidden' => 'You are not authorised to view this resource.',
												),
	);

	// set arrays with group permissions to parts of BwPostman
	// @ToDo: Fill with values
	// @ToDo: Reflect how to solve this task for all usergroups
	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $group_permission = array (
	);

	// Messages
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkin_success_text = "One %s successfully checked in";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $checkin_error_text   = "Check-in failed with the following error: The user checking in does not match the user who checked out the item.";

}
