<?php
namespace Page;

/**
 * Class FooterUsedMailinglists
 *
 * @package Plugin FooterUsedMailinglists
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   2.3.0
 */
class FooterUsedMailinglistsPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	public static $listViewFirstElement = ".//*[@id='main-table']/tbody/tr[1]/td[3]/a";
	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $html_preview_footer_legal    = ".//*[@id='legal']";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $html_preview_add_footer_outer    = ".//*[@class='show-subscribers']";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $html_preview_add_footer_mls    = ".//*[@id='show-mailinglists']";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $html_preview_add_footer_ug    = ".//*[@id='show-usergroups']";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $html_preview_add_footer_all    = ".//*[@id='show-all']";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_footer_legal    = "You receive this newsletter because you subscribed to our newsletter";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_mls    = "This newsletter was sent to following mailinglists:";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_recipients_txt    = "(%s recipients)";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_available    = "02 Mailingliste 6 B";

	/**
	 * @var integer
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_available_nbr    = 128;

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_unavailable    = "01 Mailingliste 4 A";

	/**
	 * @var integer
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_unavailable_nbr    = 253;

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_internal    = "02 Mailingliste 7 B";

	/**
	 * @var integer
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ml_internal_nbr    = 155;

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ug    = "This newsletter was sent to following usergroups:";

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ug_selected    = "Registered";

	/**
	 * @var integer
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_ug_selected_nbr    = 4;

	/**
	 * @var string
	 *
	 * @since   2.3.0
	 */
	public static $text_preview_add_footer_all    = "All in all this newsletter was sent to %s recipients";




	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $plugin_name                      = "BwPostman Plugin User2Subscriber";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $icon_published_identifier        = ".//*[@id='pluginList']/tbody/tr/td[3]/a/span";

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $plugin_edit_identifier           = ".//*[@id='pluginList']/tbody/tr/td[4]/a";

	// plugin edit tab options
	// @ToDo: make more flexible

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $plugin_tab_options               = ".//*[@id='myTabTabs']/li[2]/a";

	//messages

	/**
	 * @var string
	 *
	 * @since 2.3.0
	 */
	public static $config_save_success      = "Configuration saved.";


	/**
	 * @param \AcceptanceTester $I
	 *
	 * @since 2.3.0
	 */
	public static function selectPluginPage(\AcceptanceTester $I)
	{
		$I->amOnPage(Generals::$plugin_page);
		$I->wait(1);
		$I->see(Generals::$view_plugin, Generals::$pageTitle);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $plugin_name
	 *
	 * @since 2.3.0
	 */
	public static function filterForPlugin(\AcceptanceTester $I, $plugin_name)
	{
		$I->fillField(Generals::$search_field, $plugin_name);
		$I->clickAndWait(Generals::$search_button, 1);
	}

}
