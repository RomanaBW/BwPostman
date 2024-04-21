<?php
namespace Page;

use Page\MailinglistManagerPage as MlManage;

/**
 * Class MailinglistEditPage
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
class MailinglistEditPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url = 'administrator/index.php?option=com_bwpostman&view=mailinglist&layout=edit';

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $title        = '#jform_title';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $description  = '#jform_description';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access               = '#jform_access';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_list_id       = "//*[@id='jform_access']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_list          = "//*[@id='jform_access']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_list_text     = "//*[@id='jform_access']/option[@selected='selected']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_public        = "Public";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_guest         = "Guest";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_registered    = "Registered";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_special       = "Special";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $access_super         = "Super Users";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published                = '#jform_published';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published_list_id        = "//*[@id='jform_published']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published_list           = "//*[@id='jform_published']/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published_list_text      = "//*[@id='jform_published']/a/span";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published_unpublished    = "unpublished";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $published_published      = "published";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_title        = '001 General mailing list';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_title2        = '001 General mailing list 2';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_description  = 'A pretty description would be nice.';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_save       = 'Mailinglist saved successfully!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_save         = 'Save failed with the following error:';


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_title        = 'You have to enter a title for the mailinglist.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_description  = 'You have to enter a description for the mailinglist.';


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array     = array(
		'field_title'          => "001 General mailing list",
		'archive_tab'          => "//*/ul[contains(@class, 'bwp-tabs')]/li/a[contains(text(),'Archived mailinglists')]",
		'archive_identifier'   => "Title",
		'archive_confirm'      => 'Do you wish to archive the selected mailinglist(s)?',
		'archive_title_col'    => "//*[@id='main-table']/tbody/*/td[2]",
		'archive_success_msg'  => 'The selected mailing list has been archived.',
		'archive_success2_msg' => 'The selected mailing lists have been archived.',

		'delete_button'        => "//*[@id='toolbar-delete']/button",
		'delete_identifier'    => "Title",
		'delete_title_col'     => "//*[@id='main-table']/tbody/*/td[2]",
		'remove_confirm'       => 'Do you wish to remove the selected mailinglist(s)?',
		'success_remove'       => 'The selected mailinglist has been removed.',
		'success_remove2'      => 'The selected mailinglists have been removed.',
		'success_restore'       => 'The selected mailinglist has been restored.',
		'success_restore2'      => 'The selected mailinglists have been restored.',
	);

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close'  => "//*[@id='toolbar-save']/button",
		'Save'          => "//*[@id='toolbar-apply']/button",
		'Cancel'        => "//*[@id='toolbar-cancel']/button",
		'Back'          => "//*[@id='toolbar-back']/button",
		'Help'          => "//*[@id='toolbar-help']/button",
	);

	/**
	 * Method to fill form without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public static function fillFormSimple(\AcceptanceTester $I)
	{
		$I->fillField(self::$title, self::$field_title);
		$I->fillField(self::$description, self::$field_description);
	}

	/**
	 * Test method to create single Mailinglist without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function CreateMailinglistWithoutCleanup(\AcceptanceTester $I)
	{
		$I->wantTo("Create mailinglist without cleanup");
		$I->amOnPage(MlManage::$url);

		$I->scrollTo(Generals::$joomlaHeader, 0, 100);
		$I->wait(1);
		$I->click(Generals::$toolbar['New']);

		self::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 30);
//		$I->see("Message", Generals::$alert_heading);
		$I->see(self::$success_save, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Mailinglists', Generals::$pageTitle);
	}
}
