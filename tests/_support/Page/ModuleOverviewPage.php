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
		// Possible values: all, all_not_arc, not_arc_down, not_arc_but_down, arc, down, arc_and_down, arc_or_down

		// Tab mailinglist selection
		$I->setManifestOption('mod_bwpostman_overview', 'ml_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'ml_available', array("5", "27", "4", "24"));
		$I->setManifestOption('mod_bwpostman_overview', 'groups_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'groups_available', array(""));

		// Tab campaign selection
		$I->setManifestOption('mod_bwpostman_overview', 'cam_selected_all', 'no');
		$I->setManifestOption('mod_bwpostman_overview', 'cam_available', array(""));
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
