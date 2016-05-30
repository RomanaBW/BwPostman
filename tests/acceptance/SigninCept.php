<?php
//use Step\Acceptance\Admin as AdminTester;
use Page\Login as LoginPage;

$I = new AcceptanceTester($scenario);

$loginPage  = new LoginPage($I);
$loginPage->loginAsAdmin('Webmemsahib', 'BESU#PW§1');
/*
$I->wantTo('login to site');
$I->amOnPage(LoginPage::$URL);
$I->fillField(LoginPage::$usernameField, 'Webmemsahib');
$I->fillField(LoginPage::$passwordField, 'BESU#PW§1');
$I->click(LoginPage::$loginButton);
$I->see("Control Panel", "h1.page-title");
*/
//$I->loginAsAdmin();

$I->click("Components");
$I->click("BwPostman");
$I->waitForElement('h1.page-title');
$I->see('BwPostman', 'h1.page-title');

$loginPage->logout();

