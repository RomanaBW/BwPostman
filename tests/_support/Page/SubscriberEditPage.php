<?php
namespace Page;

use Page\SubscriberManagerPage as SubManage;
/**
 * Class SubscriberEditPage
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
class SubscriberEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=subscriber&layout=edit';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     *
     * @since   2.0.0
     */

	public static $firstname    = ".//*[@id='jform_firstname']";
	public static $name         = ".//*[@id='jform_name']";
	public static $email        = ".//*[@id='jform_email']";
	public static $special      = ".//*[@id='jform_special']";
	public static $gender       = ".//*[@id='jform_gender_chzn']/a";
	public static $mailformat   = ".//*[@id='jform_emailformat_chzn']/a";
	public static $confirm      = ".//*[@id='jform_status_chzn']/a";
	public static $unconfirmed  = ".//*[@id='jform_status_chzn']/div/ul/li[2]";
	public static $confirmed    = ".//*[@id='jform_status_chzn']/div/ul/li[1]";

	public static $field_firstname    = "Sam";
	public static $field_name         = "Sample";
	public static $field_email        = "sam.sample@test.nil";
	public static $field_special      = "0815";

	public static $popup_gender     = 'You have to enter a first name for the subscriber.';
	public static $popup_firstname  = 'You have to enter a first name for the subscriber.';
	public static $popup_name       = 'You have to enter a name for the subscriber.';
	public static $popup_email      = 'You have to enter an email address for the subscriber.';
	public static $popup_special    = 'You have to enter a value in field %s.';
	public static $success_saved    = 'Subscriber saved successfully!';
	public static $error_save       = 'Save failed with the following error:';

	public static $field_title          = "sam.sample@test.nil";

	public static $arc_del_array     = array(
		'field_title'          => "sam.sample@test.nil",
		'archive_tab'          => ".//*[@id='main-table']/tbody/tr/td/ul/li/button[contains(text(),'Archived subscribers')]",
		'archive_identifier'   => ".//*[@id='filter_search_filter_chzn']/div/ul/li[5]",
		'archive_title_col'    => ".//*[@id='j-main-container']/div[2]/div/dd[1]/table/tbody/*/td[%s]",
		'archive_success_msg'  => 'The selected subscriber has been archived.',
		'archive_success2_msg' => 'The selected subscribers have been archived.',

		'delete_button'        => ".//*[@id='toolbar-delete']/button",
		'delete_identifier'    => ".//*[@id='filter_search_filter_chzn']/div/ul/li[5]",
		'delete_title_col'     => ".//*[@id='main-table']/tbody/tr/td/div/table/tbody/*/td[4]",
		'remove_confirm'       => 'Do you wish to remove the selected subscriber(s)/test-recipient(s)?',
		'success_remove'       => 'The selected subscriber/test-recipient has been removed.',
		'success_remove2'      => 'The selected subscribers/test-recipients have been removed.',
		'success_restore'       => 'The selected subscriber/test-recipient has been restored.',
		'success_restore2'      => 'The selected subscribers/test-recipients have been restored.',
	);

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close' => ".//*[@id='toolbar-save']/button",
		'Save'         => ".//*[@id='toolbar-apply']/button",
		'Cancel'       => ".//*[@id='toolbar-cancel']/button",
		'Back'         => ".//*[@id='toolbar-back']/button",
		'Help'         => ".//*[@id='toolbar-help']/button",
	);

	public static $female   = ".//*[@id='jform_gender_chzn']/div/ul/li[2]";
	public static $male     = ".//*[@id='jform_gender_chzn']/div/ul/li[3]";
	/**
	 * Variables for selecting mailinglists
	 * Hint: Use with sprintf <nbr> for wanted row
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $mls_accessible       = ".//*[@id='adminForm']/div[1]/div[1]/fieldset/div[1]/div/fieldset/div/p[%s]/label";
	public static $mls_nonaccessible    = ".//*[@id='adminForm']/div[1]/div[1]/fieldset/div[2]/div/fieldset/div/p[%s]/label";
	public static $mls_internal         = ".//*[@id='adminForm']/div[1]/div[1]/fieldset/div[3]/div/fieldset/div/p[%s]/label";

	public static $mls_internal_label   = ".//*[@id='adminForm']/div[1]/div[1]/fieldset/div[3]/div/fieldset/legend";

	/**
	 * Test method to create single Subscriber without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function _CreateSubscriberWithoutCleanup(\AcceptanceTester $I)
	{
		$I->wantTo("Create Subscriber without cleanup");
		$I->amOnPage(SubManage::$url);

		$I->click(Generals::$toolbar['New']);

		self::_fillFormSimple($I);

		$I->click(self::$toolbar['Save & Close']);
		$I->waitForElement(Generals::$alert_header, 30);
		$I->see("Message", Generals::$alert_header);
		$I->see(self::$success_saved, Generals::$alert_msg);
		$I->see('Subscribers', Generals::$pageTitle);
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
		$options    = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$I->clickAndWait(self::$gender, 1);
			$I->clickAndWait(self::$male, 1);
		}

		if ($options->show_firstname_field || $options->firstname_field_obligation)
		{
			$I->fillField(self::$firstname, self::$field_firstname);
		}

		if ($options->show_name_field || $options->name_field_obligation)
		{
			$I->fillField(self::$name, self::$field_name);
		}

		$I->fillField(self::$email, self::$field_email);

		if ($options->show_emailformat)
		{
			$I->clickAndWait(self::$mailformat, 1);
			$I->clickAndWait(SubManage::$format_text, 1);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(self::$special, self::$field_special);
		}

        $I->clickAndWait(self::$confirm, 1);
        $I->clickAndWait(self::$confirmed, 1);

		$I->click(sprintf(self::$mls_accessible, 2));
		$I->click(sprintf(self::$mls_nonaccessible, 3));
		$I->scrollTo(self::$mls_internal_label, 0, -100);
		$I->click(sprintf(self::$mls_internal, 4));
	}

    /**
     * @param \AcceptanceTester $I
     *
     * @return array
     *
     * @since version
     */
    public static function prepareDeleteArray(\AcceptanceTester $I)
    {
        $edit_arc_del_array                      = self::$arc_del_array;
        $edit_arc_del_array['archive_title_col'] = sprintf($edit_arc_del_array['archive_title_col'], 4);

        $options = $I->getManifestOptions('com_bwpostman');

        if ($options->show_gender)
        {
            $edit_arc_del_array['archive_title_col'] = sprintf($edit_arc_del_array['archive_title_col'], 5);
        }

        return $edit_arc_del_array;
    }
}
