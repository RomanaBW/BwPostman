<?php
namespace Page;

/**
 * Class MainviewPage
 *
 * @package Page
 */
class MainviewPage
{
    // include url of current page
    public static $url = '/administrator/index.php?option=com_bwpostman';

    /**
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

	/**
     * @var object  AcceptanceTester
     */
    protected $tester;

	/**
	 * MainviewPage constructor.
	 *
	 * @param \AcceptanceTester $I
	 */
    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

}
