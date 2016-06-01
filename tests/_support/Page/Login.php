<?php
namespace Page;

/**
 * Class Login
 *
 * This class contains general helper properties and methods for testing back end
 *
 * @package Page
 */
class Login
{
    // include url of current page
    public static $url = '/administrator/index.php';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

	public static $usernameField = 'username';
	public static $passwordField = 'passwd';
	public static $loginButton = 'button';

	/**
	 * @var object  $tester AcceptanceTester
	 */
	protected $tester;

	/**
	 * Login constructor.
	 *
	 * @param \AcceptanceTester $I
	 */
	public function __construct(\AcceptanceTester $I)
	{
		$this->tester = $I;
	}

	/**
	 * Helper method to login into backend and go to component BwPostman
	 *
	 * @param   string  $name       user name
	 * @param   string  $password   password
	 *
	 * @return	object  $this
	 *
	 * @since  2.0.0
	 */
	public function loginAsAdmin($name, $password, \Page\Generals $Generals)
	{
		$I = $this->tester;


		$I->wantTo('log in as a backend user');
		$I->amOnPage(self::$url);
		$I->fillField(self::$usernameField, $name);
		$I->fillField(self::$passwordField, $password);
		$I->click(self::$loginButton);
		$I->see("Control Panel", $Generals::$pageTitle);

		return $this;
	}

	/**
	 * Method to logout from backend
	 *
	 * @return  object  $this
	 *
	 * @since   2.0.0
	 */
	public function logoutFromAdmin()
	{
		$I = $this->tester;

		$I->wantTo('log out from backend');
		$I->click("Log out");
		$I->see("Log in", "//form[@id='form-login']/fieldset/div[4]/div/div/button");

		return $this;
	}

}
