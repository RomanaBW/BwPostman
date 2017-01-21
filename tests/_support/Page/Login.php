<?php
namespace Page;


/**
 * Class Login
 *
 * This class contains general helper properties and methods for testing back end
 *
 * @package Page
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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

 * @since   2.0.0
 */
class Login
{
    // include url of current page
    public static $url = '/administrator/index.php';

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     */

	public static $usernameField = 'username';
	public static $passwordField = 'passwd';
	public static $loginButton   = 'button';
	public static $form          = ".//*[@id='form-login']";
	public static $loginArea     = ".//*/button[contains(., 'Log in')]";

	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since       2.0.0
	 */
	protected $tester;

	/**
	 * Login constructor.
	 *
	 * @param \AcceptanceTester $I
	 *
	 * @since       2.0.0
	 */
	public function __construct(\AcceptanceTester $I)
	{
		$this->tester = $I;
	}

	/**
	 * Helper method to login into backend and go to component BwPostman
	 *
	 * @param   array              $user           array of user name and password
	 *
	 * @return	object  $this
	 *
	 * @since  2.0.0
	 */
	public function logIntoBackend($user)
	{
		$I = $this->tester;
//		$I = $this;

		// if snapshot exists - skipping login
//		if ($I->loadSessionSnapshot('login')) {
//			return;
//		}

		// log in
//		$I->wantTo('log in as a backend user');
		$I->amOnPage(self::$url);
		$I->fillField(self::$usernameField, $user['user']);
		$I->fillField(self::$passwordField, $user['password']);
		$I->clickAndWait(self::$loginButton, 1);
		$I->see(Generals::$control_panel, Generals::$pageTitle);

		// saving snapshot
//		$I->saveSessionSnapshot('login');

		return $this;
	}

	/**
	 * Method to logout from backend
	 *
	 * @param \AcceptanceTester $I

	 * @return  object  $this
	 *
	 * @since   2.0.0
	 */
	public function logoutFromBackend(\AcceptanceTester $I)
	{
		$loginArea     = sprintf(".//*/button[contains(., '%s')]", Generals::$login_txt);

		$I->click(Generals::$nav_user_menu);
		$I->click(Generals::$nav_user_menu_logout);

		$I->waitForElement(self::$form);
//		$I->see(Generals::$login_txt, self::$loginArea);
		$I->see(Generals::$login_txt, $loginArea);

		return $this;
	}
}
