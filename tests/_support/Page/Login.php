<?php
namespace Page;

/**
 * Class Login
 *
 * This class contains general helper properties and methods for testing back end
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

 * @since   2.0.0
 */
class Login
{
	/**
	 * @var string  $url
	 *
	 * @since       2.0.0
	 */
	public static $url = '/administrator/index.php';

	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */
	/**
	 * @var string  $usernameField
	 *
	 * @since       2.0.0
	 */

	public static $usernameField = 'username';
	/**
	 * @var string  $passwordField
	 *
	 * @since       2.0.0
	 */
	public static $passwordField = 'passwd';

	/**
	 * @var string  $loginButton
	 *
	 * @since       2.0.0
	 */
	public static $loginButton   = "//*[@id='btn-login-submit']";

	/**
	 * @var string  $form
	 *
	 * @since       2.0.0
	 */
	public static $form          = ".//*[@id='form-login']";

	/**
	 * @var string  $loginArea
	 *
	 * @since       2.0.0
	 */
	public static $loginArea     = "//*/button[contains(., 'Log in')]";

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
	 * @param	\AcceptanceTester 			   $I
	 *
	 * @return	void
	 *
	 * @throws \Exception
	 *
	 * @since  2.0.0
	 */
	public function logIntoBackend($user, \AcceptanceTester $I = null)
	{
		if (is_null($I))
		{
			$I = $this->tester;
		}

		$I->amOnPage(self::$url);
		$I->fillField(self::$usernameField, $user['user']);
		$I->fillField(self::$passwordField, $user['password']);
		$I->click(self::$loginButton);
		$I->waitForElementVisible(Generals::$pageTitle, 30);
		$I->see(Generals::$control_panel, Generals::$pageTitle);

		return;
	}

	/**
	 * Method to logout from backend
	 *
	 * @param \AcceptanceTester $I
	 * @param $truncateSession
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function logoutFromBackend(\AcceptanceTester $I, $truncateSession = true)
	{
		$loginArea     = sprintf(".//*/button[contains(., '%s')]", Generals::$login_txt);

		$I->scrollTo(Generals::$pageTop, 0, -100);
		$I->wait(1);
		$I->click(Generals::$nav_user_menu);
		$I->click(Generals::$nav_user_menu_logout);

		$I->waitForElementVisible($loginArea, 30);
		$I->see(Generals::$login_txt, $loginArea);

		if ($truncateSession)
		{
			$I->truncateSession();
		}

		return;
	}
}
