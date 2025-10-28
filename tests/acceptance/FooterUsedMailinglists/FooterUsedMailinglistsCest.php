<?php
use Page\Generals as Generals;
use Page\Login as LoginPage;
use Page\FooterUsedMailinglistsPage as FooterPage;
use Page\NewsletterEditPage as NlEdit;
use Page\NewsletterManagerPage as NlManagePage;

/**
 * Class FooterUsedMailinglistsCest
 *
 * This class contains all methods to test adding used mailing lists and user groups to footer of newsletter
 *
 * @package Plugin FooterUsedMailinglists
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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
 * @since 2.3.0
 */
class FooterUsedMailinglistsCest
{
	/**
	 * @var object  $tester AcceptanceTester
	 *
	 * @since 2.3.0
	 */
	public $tester;

	/**
	 * @var array
	 *
	 * @since 2.3.0
	 */
	private $existing_data          = array();

	/**
	 * @var array
	 *
	 * @since 2.3.0
	 */
	private $params                 = array();

	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage                 $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function _login(LoginPage $loginPage)
	{
		$loginPage->logIntoBackend(Generals::$admin);
	}

	/**
	 * Test method to check for no recipients message
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function DontShowAnyRecipients(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with all options set to no");
		$I->expectTo('see no message');

		$I->amOnPage(NlManagePage::$url);

		// Create newsletter
		$recipients = array('available');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->dontSeeElement(FooterPage::$html_preview_add_footer_outer);

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->dontSee(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for available mailinglist
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsAvailableMailinglistOnly(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with available mailinglist");
		$I->expectTo('see available mailinglist at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');

		// Create newsletter
		$recipients = array('available');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for unavailable mailinglist
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsUnavailableMailinglistOnly(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with unavailable mailinglist");
		$I->expectTo('see unavailable mailinglist at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');

		// Create newsletter
		$recipients = array('unavailable');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_unavailable_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for internal mailinglist
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsInternalMailinglistOnly(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with internal mailinglist");
		$I->expectTo('see internal mailinglist at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');

		// Create newsletter
		$recipients = array('internal');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_internal_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for usergroup
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsUsergroupOnly(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with usergroup");
		$I->expectTo('see usergroup at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '1');

		// Create newsletter
		$recipients = array('usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_ug);
		$I->see(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->dontSee(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->see(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for available mailinglist with number of recipients
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsAvailableMailinglistNbr(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with available mailinglist and number recipients");
		$I->expectTo('see one mailinglist  with number of recipients at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');

		// Create newsletter
		$recipients = array('available');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for unavailable mailinglist with number of recipients
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsUnavailableMailinglistNbr(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with unavailable mailinglist and number recipients");
		$I->expectTo('see one mailinglist with number of recipients at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');

		// Create newsletter
		$recipients = array('unavailable');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_unavailable_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for internal mailinglist with number of recipients
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsInternalMailinglistNbr(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with unavailable mailinglist and number of recipients");
		$I->expectTo('see one mailinglist with number of recipients at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');

		// Create newsletter
		$recipients = array('internal');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_internal_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for usergroup with number of recipients
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsUsergroupNbr(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with usergroup and number of recipients");
		$I->expectTo('see usergroup with number of recipients at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups_recipients', '1');

		// Create newsletter
		$recipients = array('usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_ug);
		$I->see(FooterPage::$text_preview_add_footer_ug_selected);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->dontSee(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->see(FooterPage::$text_preview_add_footer_ug);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for three mailinglists, one usergroup, with number of recipients
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsMultipleMailinglistsAndUsergroupsNbr(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with three mailinglists, one usergroup and number of recipients");
		$I->expectTo('see three mailinglists and one usergroup with number of recipients at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups_recipients', '1');

		// Create newsletter
		$recipients = array('available', 'unavailable', 'internal', 'usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr
						+ FooterPage::$text_preview_add_footer_ml_unavailable_nbr
						+ FooterPage::$text_preview_add_footer_ml_internal_nbr
						+ FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_ug);
		$I->see(FooterPage::$text_preview_add_footer_ug_selected);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_all);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->see(FooterPage::$text_preview_add_footer_ug);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for three mailinglists, one usergroup, with number of recipients ans summarizing line
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsMultipleMailinglistsAndUsergroupsNbrSummarized(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with three mailinglists, one usergroup, number of recipients and summarizing line");
		$I->expectTo('see three mailinglists, one usergroup with number of recipients and summarizing line at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups_recipients', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_all_recipients', '1');

		// Create newsletter
		$recipients = array('available', 'unavailable', 'internal', 'usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr
			+ FooterPage::$text_preview_add_footer_ml_unavailable_nbr
			+ FooterPage::$text_preview_add_footer_ml_internal_nbr
			+ FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->seeElement(FooterPage::$html_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_ug);
		$I->see(FooterPage::$text_preview_add_footer_ug_selected);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_all);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->see(FooterPage::$text_preview_add_footer_mls);
		$I->see(FooterPage::$text_preview_add_footer_ml_available);
		$I->see(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->see(FooterPage::$text_preview_add_footer_ml_internal);

		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->see(FooterPage::$text_preview_add_footer_ug);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for no mailinglists, no usergroup, but with number of recipients and summarizing line
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsOnlyNbrAndSummarized(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message with no mailinglists, no usergroup, but number of recipients and summarizing line");
		$I->expectTo('see no mailinglists, no usergroup, no number of recipients but summarizing line at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups_recipients', '1');
		$I->setManifestOption('footerusedmailinglists', 'show_all_recipients', '1');

		// Create newsletter
		$recipients = array('available', 'unavailable', 'internal', 'usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr
			+ FooterPage::$text_preview_add_footer_ml_unavailable_nbr
			+ FooterPage::$text_preview_add_footer_ml_internal_nbr
			+ FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_all);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->dontSee(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * Test method to check recipients message for no mailinglists, no usergroup, but with number of recipients and summarizing line
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function ShowRecipientsOnlySummarized(\AcceptanceTester $I)
	{
		$this->resetOptions($I);

		$I->wantTo("check recipients message only summarizing line");
		$I->expectTo('see only summarizing line at footer');

		$I->amOnPage(NlManagePage::$url);

		// Set option
		$I->setManifestOption('footerusedmailinglists', 'show_all_recipients', '1');

		// Create newsletter
		$recipients = array('available', 'unavailable', 'internal', 'usergroups');
		$I->click(Generals::$toolbar['New']);
		$this->fillFormSimple($I, $recipients);
		$I->clickAndWait(FooterPage::$listViewFirstElement, 1);
		$I->waitForElementVisible(NlEdit::$tab1, 30);

		$nbrRecipients = FooterPage::$text_preview_add_footer_ml_available_nbr
			+ FooterPage::$text_preview_add_footer_ml_unavailable_nbr
			+ FooterPage::$text_preview_add_footer_ml_internal_nbr
			+ FooterPage::$text_preview_add_footer_ug_selected_nbr;

		// Switch to tab 4 (preview)
		$I->clickAndWait(NlEdit::$tab4, 3);

		// Check HTML version
		$I->scrollTo(NlEdit::$tab4_preview_html);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_html_iframe);
		$I->scrollTo(FooterPage::$html_preview_footer_legal, 0, -150); // scroll to before legal
		$I->wait(1);

		$I->waitForElement(FooterPage::$html_preview_footer_legal, 30);
		$I->seeElement(FooterPage::$html_preview_add_footer_outer);

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSeeElement(FooterPage::$html_preview_add_footer_ug);
		$I->dontSee(FooterPage::$text_preview_add_footer_ug_selected);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->seeElement(FooterPage::$html_preview_add_footer_all);
		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();

		// Check text version
		$I->scrollTo(NlEdit::$tab4_preview_text);
		$I->wait(1);
		$I->switchToIFrame(NlEdit::$tab4_preview_text_iframe);

		$I->see(FooterPage::$text_preview_footer_legal);

		$I->dontSee(FooterPage::$text_preview_add_footer_mls);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_available);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_unavailable);
		$I->dontSee(FooterPage::$text_preview_add_footer_ml_internal);

		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_available_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_unavailable_nbr));
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ml_internal_nbr));

		$I->dontSee(FooterPage::$text_preview_add_footer_ug);
		$I->dontSee(sprintf(FooterPage::$text_preview_add_footer_recipients_txt, FooterPage::$text_preview_add_footer_ug_selected_nbr));

		$I->see(sprintf(FooterPage::$text_preview_add_footer_all, $nbrRecipients));

		$I->switchToIFrame();
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbar['Cancel']);

		// Reset option
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');

		// Remove newsletter
		$I->HelperArcDelItems($I, NlManagePage::$arc_del_array, NlEdit::$arc_del_array, true);
	}

	/**
	 * @param   AcceptanceTester    $I
	 *
	 * @since 2.3.0
	 */
	protected function resetOptions($I)
	{
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists', '0');
		$I->setManifestOption('footerusedmailinglists', 'show_mailinglists_recipients', '0');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups', '0');
		$I->setManifestOption('footerusedmailinglists', 'show_usergroups_recipients', '0');
		$I->setManifestOption('footerusedmailinglists', 'show_all_recipients', '0');
	}

	/**
	 * Method to fill newsletter
	 * This method simply fills all fields, required or not
	 *
	 * @param \AcceptanceTester $I
	 * @param array  $recipients
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 *
	 * @throws \Exception
	 */
	private function fillFormSimple(\AcceptanceTester $I, $recipients)
	{
		$I->fillField(NlEdit::$from_name, NlEdit::$field_from_name);
		$I->fillField(NlEdit::$from_email, NlEdit::$field_from_email);
		$I->fillField(NlEdit::$reply_email, NlEdit::$field_reply_email);
		$I->fillField(NlEdit::$subject, NlEdit::$field_subject);
		$I->fillField(NlEdit::$description, NlEdit::$field_description);

		$I->scrollTo(NlEdit::$legend_templates, 0, -100);
		$I->wait(2);
		$I->click(NlEdit::$template_html);
		$I->click(NlEdit::$template_text);
		$I->wait(1);

		foreach ($recipients as $recipient)
		{
			$this->selectRecipients($I, $recipient);
		}

		// add content
		$I->scrollTo(NlEdit::$legend_content, 0, -100);
		$I->wait(2);
		$I->doubleClick(sprintf(NlEdit::$available_content, 3));
		$I->wait(2);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbar4['Save & Close'], 1);
		$I->waitForElement(Generals::$alert_header, 30);
//		$I->see("Message", Generals::$alert_heading);
		$I->see(NlEdit::$success_saved, Generals::$alert_success);

		return;
	}

	/**
	 * Method to select recipients for newsletter
	 *
	 * @param \AcceptanceTester $I
	 * @param string  $recipients
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	private function selectRecipients(\AcceptanceTester $I, $recipients = 'available')
	{
		switch ($recipients)
		{
			case 'available':
				$I->scrollTo(NlEdit::$legend_recipients, 0, -100);
				$I->wait(1);
				$I->click(sprintf(Generals::$mls_accessible, 2));
				break;
			case 'unavailable':
				$I->scrollTo(NlEdit::$legend_recipients, 0, -100);
				$I->wait(1);
				$I->click(sprintf(Generals::$mls_nonaccessible, 3));
				break;
			case 'internal':
				$I->scrollTo(NlEdit::$legend_recipients, 0, -100);
				$I->wait(1);
				$I->click(sprintf(Generals::$mls_internal, 4));
				break;
			case 'usergroups':
				$I->scrollTo(Generals::$mls_usergroup, 0, -150);
				$I->wait(2);
				$I->click(Generals::$mls_usergroup);
				break;
		}
		$I->wait(2);
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   LoginPage            $loginPage
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(\AcceptanceTester $I, LoginPage $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

}
