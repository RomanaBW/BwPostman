<?php
namespace Step\Acceptance;

/**
 * Class Admin
 *
 * This class contains helper methods for testing back end
 *
 * @package Step\Acceptance
 */
class AdminX extends \AcceptanceTester
{
	/**
	 * Helper method to login into backend and go to component BwPostman
	 *
	 * @return	void
	 *
	 * @since  1.2.0
	 */
    public function loginAsAdmin()
    {
        $I = $this;

	    $I->amOnPage('/administrator/index.php');
	    $I->wantTo('log in as a backend user');
	    $I->amOnPage('/administrator/index.php');
	    $I->fillField('username', 'Webmemsahib');
	    $I->fillField('passwd', 'BESU#PW§1');
	    $I->click('button');
	    $I->see("Control Panel", "h1.page-title");
    }

	/**
	 *
	 */
	public function logoutFromAdmin()
	{
		$I = $this;

		$I->wantTo('log out from backend');
		$I->click("Log out");
		$I->see("Log in", "//form[@id='form-login']/fieldset/div[4]/div/div/button");
	}
}
