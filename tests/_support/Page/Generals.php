<?php
namespace Page;

/**
 * Class Generals
 *
 * Class to hold generally needed properties and Methods
 *
 * @package Page
 */
class Generals
{
    // include url of current page
    public static $url = '/administrator/index.php?option=com_bwpostman';

	/**
	 * @var object  $tester AcceptanceTester
	 */
	protected $tester;

	/**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

	public static $pageTitle    = 'h1.page-title';
	public static $alert_header = 'h4.alert-heading';
	public static $alert_msg    = 'div.alert-message';
	public static $alert_warn   = 'div.alert-warning';
	public static $alert_error  = 'div.alert-error';

	public static $header       = '/html/body/div[2]/section/div/div/div[2]/form/div/fieldset/legend';

	/**
	 * Version to test
	 *
	 * @var    string
	 *
	 * @since  2.0.0
	 */
	public static $versionToTest = '2.0.0';

	/**
	 * Array of user groups
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $usergroups = array ('undefined', 'Public', 'Registered', 'Special', 'Guest', 'Super Users');

	/**
	 * Array of states
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $state = array ('unpublish', 'publish');

	/**
	 * Array of submenu xpath values for all pages
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $submenu = array (
		'BwPostman' => './/*[@id=\'submenu\']/li[1]/a',
		'Newsletters' => './/*[@id=\'submenu\']/li[2]/a',
		'Subscribers' => './/*[@id=\'submenu\']/li[3]/a',
		'Campaigns' => './/*[@id=\'submenu\']/li[4]/a',
		'Mailinglists' => './/*[@id=\'submenu\']/li[5]/a',
		'Templates' => './/*[@id=\'submenu\']/li[6]/a',
		'Archive' => './/*[@id=\'submenu\']/li[7]/a',
		'Maintenance' => './/*[@id=\'submenu\']/li[8]/a',
	);

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     *
     * @param   string  $param  page to route to
     *
     * @return  string  new url
     */
    public static function route($param)
    {
        return static::$url.$param;
    }

	/**
	 * Method to get install file name
	 *
	 * @return     string
	 *
	 * @since  2.0.0
	 */
	public static function getInstallFileName () {
		return '/Support/Software/Joomla/BwPostman/' . self::$versionToTest . '/com_bwpostman/com_bwpostman.' . self::$versionToTest . '.zip';
	}


}
