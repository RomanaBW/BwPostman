<?php
namespace Page;

/**
 * Class MenuEntriesManagerPage
 *
 * Class to hold needed properties and methods for testing menu entries of BwPostman
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
class MenuEntriesManagerPage
{
    // include url of current page
	public static $main_menu_url    = "/administrator/index.php?option=com_menus&view=items&menutype=mainmenu";

	public static $main_menu_label  = "html/body/header/div[2]/h1";
	public static $main_menu_txt    = "Menus: Items (Main Menu)";

	// menu select list
	public static $main_menu_select             = ".//*[@id='menutype_chzn']/a";
	public static $main_menu_select_mainmenu    = ".//*[@id='menutype_chzn']/div/ul/li[text()='Main Menu']";

	// filter bar
	public static $filter_search_button     = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[2]/button";
	public static $filterbar_button         = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[3]/button";
	public static $clear_button             = ".//*[@id='j-main-container']/div[1]/div[1]/div[1]/div[4]/button";
	public static $filter_status            = ".//*[@id='filter_published_chzn']/a";
	public static $filter_status_trashed    = ".//*[@id='filter_published_chzn']/div/ul/li[text()='Trashed']";

	public static $filter_empty_msg         = "No Matching Results";
	public static $filter_empty_field       = ".//*[@id='j-main-container']/div[2]";

	public static $remove_title_col = ".//*[@id='itemList']/tbody/*/td[4]";

	public static $new_entry_button     = ".//*[@id='toolbar-new']/button";
	public static $menu_save            = ".//*[@id='toolbar-save']/button";
	public static $menu_apply           = ".//*[@id='toolbar-apply']/button";
	public static $trash_button         = ".//*[@id='toolbar-trash']/button";
	public static $trash_empty_button   = ".//*[@id='toolbar-delete']/button";

	public static $published                = '#jform_published';
	public static $published_list           = ".//*[@id='jform_published_chzn']/a";
	public static $published_list_text      = ".//*[@id='jform_published_chzn']/a/span";
	public static $published_unpublished    = ".//*[@id='jform_published_chzn']/div/ul/li[text()='Unpublished']";
	public static $published_published      = ".//*[@id='jform_published_chzn']/div/ul/li[text()='Published']";
	public static $published_trashed        = ".//*[@id='jform_published_chzn']/div/ul/li[text()='Trashed']";


	public static $iframe_type      = "menuTypeModal";
	public static $iframe_nls       = "nlsFrame";

	public static $menu_entry_title = ".//*[@id='jform_title']";

	public static $menu_entry_type                  = ".//*[@id='details']/div/div[1]/div[1]/div[2]/span/a";
	public static $menu_entry_type_field            = ".//*[@id='jform_type']";
	public static $menu_entry_type_txt_register     = "Newsletter Registration";
	public static $menu_entry_type_txt_edit         = "Edit newsletter subscription";
	public static $menu_entry_type_txt_nl_list      = "Published newsletters (overview)";
	public static $menu_entry_type_txt_nl_single    = "Published newsletters (single view)";

	public static $menu_entry_link_field            = ".//*[@id='jform_link']";
	public static $menu_entry_link_txt_edit         = "index.php?option=com_bwpostman&view=edit";
	public static $menu_entry_link_txt_register     = "index.php?option=com_bwpostman&view=register";
	public static $menu_entry_link_txt_nl_list      = "index.php?option=com_bwpostman&view=newsletters";
	public static $menu_entry_link_txt_nl_single    = "index.php?option=com_bwpostman&view=newsletter";

	public static $menu_entry_select_nl             = ".//*[@id='details']/div/div[1]/div[2]/div[2]/span/a";
	public static $menu_entry_select_nl_field       = ".//*[@id='a_name']";
	public static $selected_nl                      = ".//*[@id='adminForm']/table[2]/tbody/tr[2]/td[2]/span/a";
	public static $selected_nl_no_title             = "Select newsletter";
	public static $selected_nl_title                = "Test Newsletter 10.4.2015 21:2:53";

	public static $menu_type_bwpm       = ".//*[@id='collapseTypes']/div[2]/div[1]/strong/a";
	public static $menu_type_edit       = ".//*[@id='collapse1']/*/a[text()='Edit newsletter subscription']";
	public static $menu_type_register   = ".//*[@id='collapse1']/*/a[text()='Newsletter registration']";
	public static $menu_type_nl_list    = ".//*[@id='collapse1']/*/a[text()='Published newsletters (overview)']";
	public static $menu_type_nl_single  = ".//*[@id='collapse1']/*/a[text()='Published newsletters (single view)']";

	public static $fill_title_edit       = "Edit Subscription";
	public static $fill_title_register   = "Register for Newsletters";
	public static $fill_title_nl_list    = "Show Newsletters List";
	public static $fill_title_nl_single  = "Show Single Newsletter";

	// menu options for newsletter list
	public static $list_tab_details         = ".//*[@id='myTabTabs']/*/a[text()='Details']";
	public static $list_tab_options         = ".//*[@id='myTabTabs']/*/a[text()='Options']";
	public static $list_tab_recipients      = ".//*[@id='myTabTabs']/*/a[text()='Recipient selection']";
	public static $list_tab_campaigns       = ".//*[@id='myTabTabs']/*/a[text()='Campaign selection']";
	public static $list_tab_link_type       = ".//*[@id='myTabTabs']/*/a[text()='Link Type']";
	public static $list_tab_page_display    = ".//*[@id='myTabTabs']/*/a[text()='Page Display']";
	public static $list_tab_metadata        = ".//*[@id='myTabTabs']/*/a[text()='Metadata']";
	public static $list_tab_mod_assign      = ".//*[@id='myTabTabs']/*/a[text()='Module Assignment']";

	public static $select_ml_all      = ".//*[@id='jform_params_ml_selected_all']/label[1]";
	public static $select_ml_selected = ".//*[@id='jform_params_ml_selected_all']/label[2]";

	// FE values
	public static $home     = "/index.php";

	public static $main_menu_register   = ".//*/a[text()='Register for Newsletters']";
	public static $main_menu_edit       = ".//*/a[text()='Edit Subscription']";
	public static $main_menu_nl_list    = ".//*/a[text()='Show Newsletters List']";
	public static $main_menu_nl_single  = ".//*/a[text()='Show Single Newsletter']";

	public static $menu_entry_msg_register          = "If you want to edit your newsletter subscription, please click here.";
	public static $menu_entry_identifier_register   = ".//*[@id='bwp_com_form']/div[1]";
	public static $menu_entry_msg_edit              = "If you want to change your newsletter profile, please enter your email address";
	public static $menu_entry_identifier_edit       = ".//*[@id='bwp_com_form']/div";
	public static $menu_entry_msg_nl_list           = "Test Newsletter 10.4.2015 21:41:1";
	public static $menu_entry_identifier_nl_list    = "//*[@id=\"bwp_newsletters_table\"]/tbody/tr[2]/td[2]";
	public static $menu_entry_msg_nl_single         = "Test Newsletter 10.4.2015 21:2:53";
	public static $menu_entry_identifier_nl_single  = "";

}
