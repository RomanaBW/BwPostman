<?php
namespace Page;

/**
 * Class MainviewPage
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
class MainviewPage
{
	// include url of current page

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $url = '/administrator/index.php?option=com_bwpostman&view=bwpostman';

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */


	/**
	 * @var string
	 *
	 * @since 2.2.0
	 */
	public static $dashboard    = ".//*[@id='view_bwpostman']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $newslettersButton        = '//*/div[contains(@class,"bw-icons")]/div[1]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addNewsletterButton      = '//*/div[contains(@class,"bw-icons")]/div[2]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $subscribersButton        = '//*/div[contains(@class,"bw-icons")]/div[3]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addSubscriberButton      = '//*/div[contains(@class,"bw-icons")]/div[4]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addTestRecipientButton   = '//*/div[contains(@class,"bw-icons")]/div[5]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $campaignsButton          = '//*/div[contains(@class,"bw-icons")]/div[6]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addCampaignButton        = '//*/div[contains(@class,"bw-icons")]/div[7]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $mailinglistsButton       = '//*/div[contains(@class,"bw-icons")]/div[8]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addMailinglistButton     = '//*/div[contains(@class,"bw-icons")]/div[9]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $templatesButton          = '//*/div[contains(@class,"bw-icons")]/div[10]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addHtmlTemplateButton    = "//*/div[contains(@class,'bw-icons')]/div[11]/div/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $addTextTemplateButton    = '//*/div[contains(@class,"bw-icons")]/div[12]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $archiveButton            = '//*/div[contains(@class,"bw-icons")]/div[12]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $settingsButton           = '//*/div[contains(@class,"bw-icons")]/div[14]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $maintenanceButton        = '//*/div[contains(@class,"bw-icons")]/div[15]/div/a';

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $forumButton              = '//*/div[contains(@class,"bw-icons")]/div[16]/div/a';

	/**
	 * @var object  AcceptanceTester
	 *
	 * @since   2.0.0
	 */
	protected $tester;

	/**
	 * MainviewPage constructor.
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since   2.0.0
	 */
	public function __construct(\AcceptanceTester $I)
	{
		$this->tester = $I;
	}
}
