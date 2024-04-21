<?php
namespace Page;

use AcceptanceTester;
use Page\SubscriberManagerPage as SubManage;

/**
 * Class SubscriberEditPage
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
class SubscriberEditPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url = 'administrator/index.php?option=com_bwpostman&view=subscriber&layout=edit';

	/**
	 * Declare UI map for this page here. CSS or XPath allowed.
	 *
	 * @since   2.0.0
	 */


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $edit_form	= "//*[@id='adminForm']/div[1]/fieldset/legend";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $firstname    = "//*[@id='jform_firstname']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $name         = "//*[@id='jform_name']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $email        = "//*[@id='jform_email']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $special      = "//*[@id='jform_special']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $gender       = "//*[@id='jform_gender']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailformat   = "//*[@id='jform_emailformat']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_text          = "Text";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $format_html          = "HTML";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $confirm      = "//*[@id='jform_status']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $unconfirmed  = "0";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $confirmed    = "1";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_firstname    = "Sam";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_name         = "Sample";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_email        = "sam.sample@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_email2        = "sam.sample2@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_special      = "0815";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_gender     = 'You have to enter a first name for the subscriber.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_firstname  = 'You have to enter a first name for the subscriber.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_name       = 'You have to enter a name for the subscriber.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_email      = 'You have to enter an email address for the subscriber.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $popup_special    = 'You have to enter a value in field %s.';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $success_saved    = 'Subscriber saved successfully!';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_save       = 'Save failed with the following error:';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $error_save_tester       = "The entered email address '%s' is already registered in the database (ID: ";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $field_title          = "sam.sample@tester-net.nil";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $lastNameTitle          = "Last name ";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $firstNameTitle          = "First name ";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $firstNameMissing          = "You have to enter a first name for the subscriber.";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $lastNameMissing          = "You have to enter a name for the subscriber.";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $emailTitle          = "Email ";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $specialTitle          = "Mitgliedsnummer ";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $abuseLink     = "http://www.abuse.nil/";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseFirstName    = "Invalid input at 'First name'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseLastName    = "Invalid input at 'Last name'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseSpecial    = "Invalid input at '%s'";

	/**
	 * @var string
	 *
	 * @since 3.0.0
	 */
	public static $errorAbuseEmail    = "Invalid input at 'Your email address'";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $arc_del_array     = array(
		'mainTableId'          => "//*[@id='main-table-bw-confirmed']",
		'field_title'          => "sam.sample",
		'archive_tab'          => "//*/ul[contains(@class, 'bwp-tabs')]/li/a[contains(text(),'Archived subscribers')]",
		'archive_identifier'   => "Name & Email",
		'archive_title_col'    => "//*[@id='main-table-bw-confirmed']/tbody/*/td[%s]",
		'archive_title_col_unconfirmed' => "//*[@id='main-table-bw-unconfirmed']/tbody/*/td[%s]",
		'archive_title_col_testrecipient' => "//*[@id='main-table-bw-testrecipients']/tbody/*/td[%s]",
		'archive_confirm'      => 'Do you wish to archive the selected subscriber(s)?',
		'archive_success_msg'  => 'The selected subscriber has been archived.',
		'archive_success2_msg' => 'The selected subscribers have been archived.',
		'archive_success3_msg'  => 'The selected test-recipient has been archived.',
		'archive_success4_msg'  => 'The selected test-recipients have been archived',

		'delete_button'        => "//*[@id='toolbar-delete']/button",
		'delete_identifier'    => "Name & Email",
		'delete_title_col'     => "//*[@id='main-table']/tbody/tr/td/div/table/tbody/*/td[4]",
		'remove_confirm'       => 'Do you wish to remove the selected subscriber(s)/test-recipient(s)?',
		'success_remove'       => 'The selected subscriber/test-recipient has been removed.',
		'success_remove2'      => 'The selected subscribers/test-recipients have been removed.',
		'success_restore'      => 'The selected subscriber/test-recipient has been restored.',
		'success_restore2'     => 'The selected subscribers/test-recipients have been restored.',
	);

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close' => "//*[@id='toolbar-save']/button",
		'Save'         => "//*[@id='toolbar-apply']/button",
		'Cancel'       => "//*[@id='toolbar-cancel']/button",
		'Back'         => "//*[@id='toolbar-back']/button",
		'Help'         => "//*[@id='toolbar-help']/button",
	);

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $printSubsDataText   = "Print subscriber data";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $printSubsDataButton   = "//*/a[contains(text(), '%s')]";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $printSubsIframe   = "myIframeSub";

	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $printSubsDataMail   = "//*[@id='preview_html']/table[1]/tbody/tr[4]/td[2]";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $printSubsDataClose   = "//*/div[@id='bwp-modal']/div/div/div/button[@class='btn-close']";


	/**
	 * @var string
	 *
	 * @since 4.0.0
	 */
	public static $noGender   = "2";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $female   = "1";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $male     = "0";

	/**
	 * Variables for selecting mailinglists
	 * Hint: Use with sprintf <nbr> for wanted row
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $firstSubscriber       = "//*/table[@id='main-table-bw-confirmed']/tbody/tr[1]/td[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.4.0
	 */
	public static $mls_label            = "//*[@id='subs_mailinglists']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_accessible       = "//*[@id='jform_ml_available_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_nonaccessible    = "//*[@id='jform_ml_unavailable_%s']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_internal         = "//*[@id='jform_ml_intern_%s']";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mls_internal_label   = "//*[@id='ml_intern']";

	/**
	 * Test method to create single Subscriber without cleanup for testing restore permission
	 *
	 * @param   \AcceptanceTester   $I
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function CreateSubscriberWithoutCleanup(\AcceptanceTester $I)
	{
		$I->wantTo("Create Subscriber without cleanup");
		$I->amOnPage(SubManage::$url);

		$I->scrollTo(Generals::$joomlaHeader, 0, 100);
		$I->wait(1);
		$I->click(Generals::$toolbar['New']);

		self::fillFormSimple($I);

		$I->click(Generals::$toolbar4['Save & Close']);
		$I->waitForElementVisible(Generals::$alert_header, 30);
//		$I->see("Message", Generals::$alert_heading);
		$I->see(self::$success_saved, Generals::$alert_success);
		$I->clickAndWait(Generals::$systemMessageClose, 1);
		$I->see('Subscribers', Generals::$pageTitle);
	}

	/**
	 * Method to fill form without check of required fields
	 * This method simply fills all fields, required or not
	 *
	 * @param AcceptanceTester $I
	 * @param string           $format (0 = text, 1 = HTML)
	 * @param string           $gender (0 = male, 1 = female, 2 = n.a.)
	 * @param bool             $isTester
	 *
	 * @since   2.0.0
	 */
	public static function fillFormSimple(AcceptanceTester $I, $format = "0", $gender = "0", $isTester = false)
	{
		$options    = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$I->selectOption(self::$gender, $gender);
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
			$I->click(self::$mailformat);
			$I->selectOption(self::$mailformat, $format);
			$I->wait(1);
			$I->waitForText("Text", 5);
		}

		if ($options->show_special || $options->special_field_obligation)
		{
			$I->fillField(self::$special, self::$field_special);
		}

		if (!$isTester)
		{
			$I->click(self::$confirm);
			$I->selectOption(self::$confirm, self::$confirmed);
			$I->wait(1);
			$I->waitForText("confirmed", 5);

			$I->scrollTo(self::$mls_label, 0, -100);
			$I->wait(1);
			$I->click(sprintf(self::$mls_accessible, 3));
			$I->click(sprintf(self::$mls_nonaccessible, 5));
			$I->scrollTo(self::$mls_internal_label, 0, -100);
			$I->wait(1);
			$I->click(sprintf(self::$mls_internal, 7));
		}

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param boolean           $confirmed
	 * @param bool              $isTester
	 * @param bool              $multiple
	 *
	 * @return array
	 *
	 * @since version
	 */
	public static function prepareDeleteArray(\AcceptanceTester $I, bool $confirmed = true, bool $isTester = false, bool $multiple = false)
	{
		$edit_arc_del_array = self::$arc_del_array;
		$title_col = 4;

		$options = $I->getManifestOptions('com_bwpostman');

		if ($options->show_gender)
		{
			$title_col = 5;
		}
		$edit_arc_del_array['archive_title_col'] = sprintf($edit_arc_del_array['archive_title_col'], $title_col);

		if (!$confirmed)
		{
			$edit_arc_del_array['mainTableId'] = "//*[@id='main-table-bw-unconfirmed']";
			$edit_arc_del_array['archive_title_col'] = sprintf($edit_arc_del_array['archive_title_col_unconfirmed'], $title_col);
		}
		elseif ($isTester)
		{
			$edit_arc_del_array['mainTableId'] = "//*[@id='main-table-bw-testrecipients']";
			$edit_arc_del_array['archive_title_col'] = sprintf($edit_arc_del_array['archive_title_col_testrecipient'], $title_col);
			$edit_arc_del_array['archive_success_msg'] = $edit_arc_del_array['archive_success3_msg'];

			if ($multiple)
			{
				$edit_arc_del_array['archive_success_msg'] = $edit_arc_del_array['archive_success4_msg'];
			}
		}

		return $edit_arc_del_array;
	}
}
