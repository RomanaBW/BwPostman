<?php
namespace Page;

/**
 * Class MainviewPage
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
class MainviewPage
{
    // include url of current page
    public static $url = '/administrator/index.php?option=com_bwpostman';

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $newslettersButton        = './/*[@id=\'cpanel\']/div[1]/div/a';
    public static $addNewsletterButton      = './/*[@id=\'cpanel\']/div[2]/div/a';
	public static $subscribersButton        = './/*[@id=\'cpanel\']/div[3]/div/a';
	public static $addSubscriberButton      = './/*[@id=\'cpanel\']/div[4]/div/a';
	public static $addTestRecipientButton   = './/*[@id=\'cpanel\']/div[5]/div/a';
	public static $campaignsButton          = './/*[@id=\'cpanel\']/div[6]/div/a';
	public static $addCampaingButton        = './/*[@id=\'cpanel\']/div[7]/div/a';
	public static $mailinglistsButton       = './/*[@id=\'cpanel\']/div[8]/div/a';
	public static $addMailinglistButton     = './/*[@id=\'cpanel\']/div[9]/div/a';
	public static $templatesButton          = './/*[@id=\'cpanel\']/div[10]/div/a';
	public static $addHtmlTemplateButton    = './/*[@id=\'cpanel\']/div[11]/div/a';
	public static $addTextTemplateButton    = './/*[@id=\'cpanel\']/div[12]/div/a';
	public static $archiveButton            = './/*[@id=\'cpanel\']/div[12]/div/a';
	public static $settingsButton           = './/*[@id=\'cpanel\']/div[14]/div/a';
	public static $maintenanceButton        = './/*[@id=\'cpanel\']/div[15]/div/a';
	public static $forumButton              = './/*[@id=\'cpanel\']/div[16]/div/a';

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
