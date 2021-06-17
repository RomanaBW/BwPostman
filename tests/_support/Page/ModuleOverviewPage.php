<?php
namespace Page;

use AcceptanceTester;
use Exception;

/**
 * Class ModuleOverviewPage
 *
 * @package Page
 * @copyright (C) 2020 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   4.0.0
 */
class ModuleOverviewPage
{
	// field identifiers module

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_title_position = "//*/h3[contains(text(), 'BwPostman Overview Module')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_content_position = "//*/div[@id='mod_bwpostman_overview']";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_count_n = "//*/div[@id='mod_bwpostman_overview']/ul/li[%s]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_count_li = "//*/div[@id='mod_bwpostman_overview']/ul/li";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $mod_count_0_message = "No newsletters in the selection";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $warningMessage = "<b>Warning</b>";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $noticeMessage = "<b>Notice</b>";

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $selectedMls = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59");

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $selectedUgs = array("-1", "-2", "-3", "-4", "-5", "-6", "-7", "-8", "-9", "-10", "-11", "-12", "-13", "-14", "-15", "-16", "-17", "-18", "-19", "-20", "-21", "-22", "-23", "-24", "-25", "-26", "-27", "-28");

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $selectedCams = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48");

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $someSelectedMls = array("5", "27", "4", "24");

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $someSelectedUgs = array("-1", "-2","-8");

	/**
	 * @var array
	 *
	 * @since 4.0.0
	 */
	public static $someSelectedCams = array("1", "4", "16", "18");




	/**
	 * Test method to preset the options of the module
	 * Used options:
	 * count:        # of months used for search, from now on backwards
	 * menu_item:    ID od menu item to override module specific options
	 * access-check: Whether to use access restriction of mailing list or not, Only works if specific recipients/campaigns are selected
	 * show_type:    Show newsletters filtered by:
	 *               all:              All newsletters
	 *               all_not_arc:      All not archived newsletters
	 *               not_arc_down:     All not archived and not expired newsletters
	 *               not_arc_but_down: All not archived but expired newsletters
	 *               arc:              All archived newsletters
	 *               down:             All expired newsletters
	 *               arc_and_down:     All archived *and* expired newsletters
	 *               arc_or_down:      All archived *or* expired newsletters
	 *
	 * @param AcceptanceTester $I
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public static function presetModuleOptions(AcceptanceTester $I)
	{
		// Tab module
		$I->setManifestOption('mod_bwpostman_overview', 'count', "12");
		$I->setManifestOption('mod_bwpostman_overview', 'menu_item', "");
		$I->setManifestOption('mod_bwpostman_overview', 'access-check', "yes");
		$I->setManifestOption('mod_bwpostman_overview', 'show_type', "all");

		// Tab mailinglist selection
		$I->setManifestOption('mod_bwpostman_overview', 'ml_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', self::$someSelectedMls);
		$I->setManifestOption('mod_bwpostman_overview', 'groups_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', array(""));

		// Tab campaign selection
		$I->setManifestOption('mod_bwpostman_overview', 'cam_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', array(""));

		// Set guest user group
		$guestGroupId = $I->getGroupIdByName('Guest');
		$I->setManifestOption('com_users', 'guest_usergroup', $guestGroupId);
	}


	/**
	 * Test method placeholder
	 *
	 * @param   AcceptanceTester   $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public static function placeholder(AcceptanceTester $I)
	{

	}
}
