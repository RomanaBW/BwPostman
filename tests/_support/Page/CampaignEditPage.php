<?php
namespace Page;

use Page\CampaignManagerPage as CamManage;

/**
 * Class CampaignEditPage
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
class CampaignEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=campaign&layout=edit';

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     */

	public static $title        = '#jform_title';
	public static $description  = '#jform_description';

	public static $field_title        = '001 new campaign for tests';
	public static $field_description  = 'A pretty description would be nice.';

	public static $success_save       = 'Campaign saved successfully!';
	public static $error_save         = 'Save failed with the following error:';

	public static $popup_title        = 'You have to enter a title for the campaign.';
	public static $popup_description  = 'You have to enter a description for the campaign.';

	public static $popup_no_recipients  = 'No recipients selected!';
	public static $warning_no_title     = 'Field required: Campaign title';

	public static $title_col            = ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]";

	public static $arc_del_array     = array(
		'field_title'          => "001 new campaign for tests",
		'archive_button'       => ".//*[@id='toolbar-popup-archive']/button",
		'archive_tab'          => ".//*[@id='j-main-container']/div[2]/table/tbody/tr/td/ul/li/button[contains(text(),'Archived campaigns')]",
		'archive_identifier'   => ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
		'archive_title_col'    => ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]",
		'archive_success_msg'  => 'The selected campaign has been archived.',
		'archive_success2_msg' => 'The selected campaigns have been archived.',

		'delete_button'        => ".//*[@id='toolbar-popup-delete']/button",
		'delete_identifier'    => ".//*[@id='filter_search_filter_chzn']/div/ul/li[1]",
		'delete_title_col'     => ".//*[@id='j-main-container']/div[2]/table/tbody/*/td[2]",
		'remove_confirm'       => 'Do you wish to remove the selected newsletter(s)?',
		'success_remove'       => 'The selected campaign has been removed.',
		'success_remove2'      => 'The selected campaigns have been removed.',
		'restore_button'       => ".//*[@id='toolbar-popup-unarchive']/button",
		'restore_confirm'       => 'Do you wish to restore the selected newsletter(s)?',
		'success_restore'       => 'The selected campaign has been restored.',
		'success_restore2'      => 'The selected campaigns have been restored.',
	);

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

	/**
	 * Test method to create single Campaign without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function _CreateCampaignWithoutCleanup(\AcceptanceTester $I)
	{
		$I->wantTo("Create campaign without cleanup");
		$I->amOnPage(CamManage::$url);
		$I->click(Generals::$toolbar['New']);

		self::_fillFormSimple($I);

		$I->click(self::$toolbar['Save & Close']);

		$I->see("Message", Generals::$alert_header);
		$I->see(self::$success_save, Generals::$alert_success);

		$I->see('Campaigns', Generals::$pageTitle);
	}

	/**
	 * Method to fill form without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public static function _fillFormSimple(\AcceptanceTester $I)
	{
		$I->fillField(self::$title, self::$field_title);
		$I->fillField(self::$description, self::$field_description);
		$I->click(sprintf(Generals::$mls_accessible, 2));
	}
}
