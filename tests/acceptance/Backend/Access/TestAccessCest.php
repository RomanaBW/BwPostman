<?php
namespace Backend\Access;

use Page\Generals as Generals;
use Page\MainviewPage as MainView;
use Page\Login as LoginPage;

use Page\AccessPage as AccessPage;

use Page\CampaignEditPage;
use Page\CampaignManagerPage;
use Page\MailinglistEditPage;
use Page\MailinglistManagerPage;
use Page\SubscriberEditPage;
use Page\SubscriberManagerPage;
use Page\TemplateEditPage;
use Page\TemplateManagerPage;
use Page\NewsletterEditPage as NewsletterEditPage;
use Page\NewsletterManagerPage as NewsletterManagerPage;

use Page\OptionsPage as OptionsPage;

/**
* Class TestInstallationCest
*
* This class contains all methods to test access at backend of BwPostman
 *
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
class TestAccessCest
{
	/**
	 * Test method to login into backend
	 *
	 * @param   LoginPage            $loginPage
	 * @param   array                  $user
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _login(LoginPage $loginPage, array $user)
	{
		$loginPage->logIntoBackend($user);
	}

	/**
	 * Test method to check for allowed/forbidden of list links at main view of BwPostman
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsForListViewButtonsFromMainView(\AcceptanceTester $I)
	{
		$I->wantTo("check permissions for main view list buttons");
		$I->expectTo("see appropriate messages");

		$loginPage = new LoginPage($I);

		foreach (AccessPage::$all_users as $user)
		{
			$this->_login($loginPage, $user);

			foreach (AccessPage::$main_list_buttons as $button => $link)
			{
				$permission_array   = '_main_list_permissions';
				$allowed            = $this->_getAllowedByUser($user, $button, $permission_array);
				$archive_allowed    = $this->_getAllowedByUser($user, 'Archive', $permission_array);

				$this->_checkAccessByJoomlaMenu($I, $button, $allowed);

				$I->amOnPage(MainView::$url);
				$I->waitForElement(Generals::$pageTitle, 30);
				$I->see('BwPostman');

				$I->see('BwPostman', Generals::$submenu['BwPostman']);
				$I->see('BwPostman Forum', AccessPage::$forum_icon);
				$I->see('Help', AccessPage::$help_button);

				if (!$allowed)
				{
					$I->dontSeeElement($link);

					$this->_checkVisibilityOfGeneralStatistics($I, $button, false);

					$this->_checkVisibilityOfArchiveStatistics($I, $button, $archive_allowed, false);

					$this->_checkVisibilityOfSubmenuItems($I, $button, $archive_allowed, false);

					if ($button == 'Basic settings')
					{
						$I->dontSee('Options', AccessPage::$options_button);
					}
				}
				else
				{
					$text_to_see    = $button;

					if ($button == 'Basic settings')
					{
						$text_to_see    = 'BwPostman Configuration';
						$I->see('Options', AccessPage::$options_button);
					}

					$this->_checkVisibilityOfGeneralStatistics($I, $button, true);

					$this->_checkVisibilityOfArchiveStatistics($I, $button, $archive_allowed, true);

					$this->_checkVisibilityOfSubmenuItems($I, $button, $archive_allowed, true);

					$I->clickAndWait($link, 1);
					$I->see($text_to_see, Generals::$pageTitle);
				}
			}
			$this->_logout($I, $loginPage);
		}
	}

	/**
	 * @param $user
	 * @param $button
	 * @param $permission_array
	 *
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	private function _getAllowedByUser($user, $button, $permission_array)
	{
		$permission_array = $user['user'] . $permission_array;
		$allowed          = AccessPage::${$permission_array}[$button];

		return $allowed;
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $button
	 * @param string           $allowed
	 *
	 *
	 * @since 2.0.0
	 */
	private function _checkAccessByJoomlaMenu(\AcceptanceTester $I, $button, $allowed)
	{
		if ($button != 'Basic settings')
		{
			$I->clickAndWait( AccessPage::$j_menu_components, 1);
			$I->see('BwPostman', AccessPage::$j_menu_bwpostman);

			$I->moveMouseOver(AccessPage::$j_menu_bwpostman);
			$I->waitForElementVisible(AccessPage::$j_menu_bwpostman_sub, 10);
			$I->see($button, sprintf(AccessPage::$j_menu_bwpostman_sub_item, $button));

			$I->moveMouseOver(sprintf(AccessPage::$j_menu_bwpostman_sub_item, $button));
			$I->clickAndWait(sprintf(AccessPage::$j_menu_bwpostman_sub_item, $button), 1);

			if ($allowed)
			{
				$I->waitForElement(Generals::$pageTitle, 30);
				$I->see($button, Generals::$pageTitle);
			}
			else
			{
				$I->seeElement(Generals::$alert_error);
				$I->see(sprintf(AccessPage::$list_view_no_permission, $button));
				$I->see(Generals::$extension, Generals::$pageTitle);
			}
		}
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $button
	 * @param string           $visible
	 *
	 *
	 * @since 2.0.0
	 */
	private function _checkVisibilityOfGeneralStatistics(\AcceptanceTester $I, $button, $visible)
	{
		if ($button != 'Archive' && $button != 'Basic settings' && $button != 'Maintenance')
		{
			$I->waitForElementVisible(AccessPage::$table_statistics_general, 30);
			$I->wait(1);

			foreach (AccessPage::$statistics_general[$button] as $statistics_general_text)
			{
				if ($visible)
				{
					$I->seeElement($statistics_general_text);
				}
				else
				{
					$I->dontSeeElement($statistics_general_text);
				}
			}
		}
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $button
	 * @param string           $archive_allowed
	 * @param string           $visible
	 *
	 *
	 * @since 2.0.0
	 */
	private function _checkVisibilityOfArchiveStatistics(\AcceptanceTester $I, $button, $archive_allowed, $visible)
	{
		if ($button != 'Basic settings')
		{
			if ($archive_allowed)
			{
				$I->see('Archive', Generals::$submenu['Archive']);

				if ($visible)
				{
					$I->see($button, Generals::$submenu[$button]);
				}
				else
				{
					$I->dontSee($button, Generals::$submenu[$button]);
				}
			}
			else
			{
				$I->dontSee('Archive', Generals::$submenu['Archive']);
			}
		}
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string           $button
	 * @param string           $archive_allowed
	 * @param string           $visible
	 *
	 *
	 * @since 2.0.0
	 */
	private function _checkVisibilityOfSubmenuItems(\AcceptanceTester $I, $button, $archive_allowed, $visible)
	{
		if ($button != 'Archive' && $button != 'Basic settings' && $button != 'Maintenance')
		{
			if ($archive_allowed)
			{
				$I->waitForElementVisible(AccessPage::$link_statistics_archive, 30);
				$I->wait(1);

				$I->click(AccessPage::$link_statistics_archive);
				$I->waitForElementVisible(AccessPage::$table_statistics_archive, 30);
				$I->wait(1);

				foreach (AccessPage::$statistics_archive[$button] as $statistics_archive_text)
				{
					if ($visible)
					{
						$I->seeElement($statistics_archive_text);
					}
					else
					{
						$I->dontSeeElement($statistics_archive_text);
					}
				}
				$I->clickAndWait(AccessPage::$link_statistics_general, 1);
			}
			else
			{
				$I->dontSeeElement(AccessPage::$link_statistics_archive);
			}
		}
	}

	/**
	 * Test method to check for allowed/forbidden of "add" links at main view of BwPostman
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsForAddButtonsFromMainView(\AcceptanceTester $I)
	{
		$I->wantTo("check permissions for main view add buttons");
		$I->expectTo("see appropriate messages");

		$loginPage = new LoginPage($I);

		foreach (AccessPage::$all_users as $user)
		{
			$this->_login($loginPage, $user);

			foreach (AccessPage::$main_add_buttons as $button => $link)
			{
				$permission_array = '_main_add_permissions';
				$allowed          = $this->_getAllowedByUser($user, $button, $permission_array);

				$I->amOnPage(MainView::$url);
				$I->waitForElement(Generals::$pageTitle, 30);
				$I->see('BwPostman');

				// click to icon

				if ($allowed)
				{
					$text_to_see    = $button . ' details:';

					if ($button == 'HTML-Template' ||$button == 'Text-Template')
					{
						$text_to_see    = 'Templatedetails:';
					}

					$I->clickAndWait($link, 1);
					$I->see($text_to_see, Generals::$pageTitle);
					$I->click(Generals::$back_button);
				}
				else
				{
					$I->dontSeeElement($link);
				}
			}
			$this->_logout($I, $loginPage);
		}
	}

	/**
	 * Test method to check for allowed/forbidden of a single list view by buttons in this list views,
     * loop over all list views
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsForActionsInListsByButtons(\AcceptanceTester $I)
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$loginPage = new LoginPage($I);

		// Loop over all users
		for ($i = 0; $i < count(AccessPage::$all_users); $i++)
		{
			// @ToDo: Consider, that some webmaster may set user permissions e.g. to send newsletter but not to create or edit one
            // Simplify user variable
			$user   = AccessPage::$all_users[$i];

			// @ToDo: This is a workaround to first create the tests. In real life this break must be removed!
			if ($user['user'] != 'BwPostmanAdmin')
			{
				break;
			}
			$this->_login($loginPage, $user);

			// Loop over main view list buttons
			foreach (AccessPage::$main_list_buttons as $button => $link)
			{
				$list_permission_array  = '_main_list_permissions';
				$allowed                = $this->_getAllowedByUser($user, $button, $list_permission_array);
codecept_debug('User: ' . $user['user']);
codecept_debug('Button: ' . $button);
codecept_debug('Allowed: ' . $allowed);

				$I->amOnPage(MainView::$url);
				$I->waitForElement(Generals::$pageTitle, 30);
				$I->see('BwPostman');

				if ($allowed)
				{
					$I->click($link);
					$I->waitForElement(Generals::$pageTitle, 30);
					$I->see($button, Generals::$pageTitle);

					$item_permission_array = AccessPage::${$user['user'] . '_item_permissions'};

					if ($button != 'Archive' || $button != 'Basic settings' || $button != 'Maintenance')
					{
						$this->_createNewItem($I, $button, $item_permission_array);

						$this->_editItem($I, $button, 'own', $item_permission_array); // own item
						$this->_editItem($I, $button, 'other', $item_permission_array); // other item

						$this->_changeStateItem($I, $button, $item_permission_array); //own item

						$this->_checkinOwnItem($I, $button, $link, $item_permission_array);
//						$this->_checkinOtherItem($I, $i, $button, $link);

						$this->_restoreArchivedItem($I, $button, $user, $item_permission_array); // own item

						if ($button == 'Newsletters')
						{
							$this->_duplicateNewsletter($I, $user, $item_permission_array);

							$this->_sendNewsletter($I, $user, $item_permission_array);

							// @ToDo: set publish/unpublish date
							// @ToDo: handle queue
						}
						elseif ($button == 'Subscribers')
						{
							// @ToDo: import subscribers
							// @ToDo: export subscribers
							// @ToDo: batch
						}
						elseif ($button == 'Templates')
						{
							$this->_setDefaultTemplate($I, $user, $item_permission_array);

							// @ToDo: import template
						}
					}
					else
					{
						// @ToDo: restore
						// @ToDo: delete
					}
				}
				else
				{
					// @ToDo: don't see button
				}

                // @ToDo: This is a workaround to first create the tests. In real life this break must be removed!
				if ($button == 'Newsletters')
				{
					break;
				}
			}
			$this->_logout($I, $loginPage);
		}
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param array             $permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _createNewItem($I, $button, $permission_array)
	{
		$allowed    = $permission_array[$button]['permissions']['Create'];

		$I->click(Generals::$toolbar['New']);
		$I->waitForElement(Generals::$pageTitle, 30);

		if ($allowed)
		{
			$title_to_see = $this->_getTitleToSee($button, '');

			$I->see($title_to_see, Generals::$pageTitle);

			$I->click(Generals::$toolbar['Cancel']);
			$I->waitForElement(Generals::$pageTitle, 30);
			$I->see('BwPostman');
		}
		else
		{
			$I->see($button, Generals::$pageTitle);
			// for button tests I may not get here!
			$I->see('No permission create an item!', Generals::$alert_error);
		}
	}

	/**
	 * @param $button
	 * @param $add_text
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function _getTitleToSee($button, $add_text)
	{
		$title_to_see = substr($button, 0, -1) . ' details: ';

		if ($button == 'HTML-Template' || $button == 'Text-Template')
		{
			$title_to_see = 'Templatedetails:';
		}
		$title_to_see .= $add_text;

		return $title_to_see;
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param string            $action
	 * @param array             $permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _editItem($I, $button, $action, $permission_array)
	{
		$check_content  = $permission_array[$button][$action]['check content'];
		$check_locator  = $permission_array[$button]['check locator'];
		$check_link     = $permission_array[$button]['check link'];
		$allowed        = $permission_array[$button]['permissions']['Edit'];

		if ($action == 'own')
		{
			$allowed = $permission_array[$button]['permissions']['EditOwn'];
		}

		// find page and row for desired item
		$item_found  = $I->findPageWithItemAndScrollToItem($check_content);

		$I->assertEquals(true, $item_found);

		// by link
		$I->click(sprintf($check_link, $check_content));
		$I->waitForElement(Generals::$pageTitle, 30);

		$this->_checkForEditResult($I, $button, $check_content, $check_locator, $allowed);

		// find page and row for desired item
		$item_found  = $I->findPageWithItemAndScrollToItem($check_content);

		$I->assertEquals(true, $item_found);

		// by checkbox
		$checkbox       = $this->_getCheckbox($I, $check_content);

		$I->click($checkbox);
		$I->click(Generals::$toolbar['Edit']);

		$this->_checkForEditResult($I, $button, $check_content, $check_locator, $allowed);
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param string            $check_content
	 * @param string            $check_locator
	 * @param boolean           $allowed
	 *
	 *
	 * @since 2.0.0
	 */
	private function _checkForEditResult($I, $button, $check_content, $check_locator, $allowed)
	{
		if ($allowed)
		{
			$title_to_see = $this->_getTitleToSee($button, '[ Edit ]');

			$I->see($title_to_see, Generals::$pageTitle);
			$I->seeInField($check_locator, $check_content);
		}
		else
		{
			$I->see($button, Generals::$pageTitle);
			// for button tests I may only get here at edit other owners items!
			$I->see('No permission edit this item!', Generals::$alert_error);
		}

		$I->click(Generals::$toolbar['Cancel']);
		$I->waitForElement(Generals::$pageTitle, 30);
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $title_content
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function _getCheckbox($I, $title_content)
	{
		$checkbox_nbr  = $I->getTableRowIdBySearchValue($title_content);
		$checkbox = sprintf(AccessPage::$checkbox_identifier, $checkbox_nbr - 1);

		return $checkbox;
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param array             $permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _changeStateItem($I, $button, $permission_array)
	{
		$has_state_to_change    = array('Newsletters', 'Mailinglists', 'Templates');

		if (in_array($button, $has_state_to_change))
		{
			if ($button == 'Newsletters')
			{
				$I->click(NewsletterManagerPage::$tab2);// switch to tab sent newsletters first
				$I->waitForElement(".//*[@id='j-main-container']/div[4]/table/thead/tr/th[5]/a", 20);
			}
			$I->publishByIcon($I, $permission_array[$button]['publish_by_icon'], strtolower(substr($button, 0, -1)), NewsletterManagerPage::$tab2);
			$I->publishByToolbar($I, $permission_array[$button]['publish_by_toolbar'], strtolower(substr($button, 0, -1)), NewsletterManagerPage::$tab2);

			if ($button == 'Newsletters')
			{
				$I->click(NewsletterManagerPage::$tab1);// switch to tab unsent newsletters to finish
				$I->waitForElement(".//*[@id='j-main-container']/div[4]/table/thead/tr/th[5]/a", 20);
			}
		}
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param array             $user
	 * @param string            $permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _restoreArchivedItem($I, $button, $user, $permission_array)
	{
		// only thing to test remains in restore item
		$create_allowed     = $permission_array[$button]['permissions']['Create'];

		if (!$create_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar['New']);
			return;
		}

		$archive_allowed    = $permission_array[$button]['permissions']['Archive'];
		$restore_allowed    = $permission_array[$button]['permissions']['Restore'];

		if (!$archive_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar['Archive']);
			return;
		}

		$edit_data      = array();
		$manage_data    = array();

		switch ($button)
		{
			case 'Newsletters':
					$edit_data      = NewsletterEditPage::$arc_del_array;
					$manage_data    = NewsletterManagerPage::$arc_del_array;
				break;
			case 'Subscribers':
					$edit_data      = SubscriberEditPage::$arc_del_array;
					$manage_data    = SubscriberManagerPage::$arc_del_array;
				break;
			case 'Campaigns':
					$edit_data      = CampaignEditPage::$arc_del_array;
					$manage_data    = CampaignManagerPage::$arc_del_array;
				break;
			case 'Mailinglists':
					$edit_data      = MailinglistEditPage::$arc_del_array;
					$manage_data    = MailinglistManagerPage::$arc_del_array;
				break;
			case 'Templates':
					$edit_data      = TemplateEditPage::$arc_del_array;
					$manage_data    = TemplateManagerPage::$arc_del_array;
				break;
		}

		if (!$restore_allowed)
		{
			$I->switchToArchive($I, $edit_data['archive_tab']);
			$I->dontSeeElement(Generals::$toolbar['Restore']);
			$I->switchToSection($I, $manage_data);
			return;
		}

		switch ($button)
		{
			case 'Newsletters':
				NewsletterEditPage::_CreateNewsletterWithoutCleanup($I, $user['user']);
//				\TestSubscribersDetailsCest::_createSubscriberWithoutCleanup($I);
				break;
			case 'Subscribers':
				SubscriberEditPage::_CreateSubscriberWithoutCleanup($I);
				break;
			case 'Campaigns':
				CampaignEditPage::_createCampaignWithoutCleanup($I);
				break;
			case 'Mailinglists':
				MailinglistEditPage::_createMailinglistWithoutCleanup($I);
				break;
			case 'Templates':
				TemplateEditPage::_createTemplateWithoutCleanup($I);
				break;
		}

		$delete_allowed = $permission_array['Newsletters']['permissions']['Delete'];

		$I->HelperArchiveItems($I, $manage_data, $edit_data);

		// restore item
		$I->HelperRestoreItems($I, $manage_data, $edit_data);

		// HelperArcDelItems
		if (!$delete_allowed)
		{
			$this->_switchLoggedInUser($I, Generals::$admin['author']);
			$I->HelperArcDelItems($I, $manage_data, $edit_data, true);
			$this->_switchLoggedInUser($I, $user['user']);
		}
		else
		{
			$I->HelperArcDelItems($I, $manage_data, $edit_data, $delete_allowed);
		}
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param string            $button
	 * @param string            $link
	 * @param string            $permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _checkinOwnItem($I, $button, $link, $permission_array)
	{
		$check_content  = $permission_array[$button]['own']['check content'];
		$check_link     = $permission_array[$button]['check link'];
		$item_link      = sprintf($check_link, $check_content);
		$col_nbr        = 2;

		if ($button == 'Newsletters')
		{
			$col_nbr++;
		}

		$this->_openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link);

		$row_nbr    = $I->getTableRowIdBySearchValue($check_content);
		$lock_icon  = sprintf(AccessPage::$checkout_icon, $row_nbr, $col_nbr);

		// by icon
		$I->seeElement($lock_icon);
		$I->click($lock_icon);
		$this->_checkCheckinResult($I, $check_content, $lock_icon);

		$this->_openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link);

		// see lock icon
		$I->seeElement($lock_icon);

		// by toolbar
		$checkbox       = $this->_getCheckbox($I, $check_content);
		$I->click($checkbox);
		$I->click(Generals::$toolbar['Check-In']);
		$this->_checkCheckinResult($I, $check_content, $lock_icon);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param $button
	 * @param $link
	 * @param $check_content
	 * @param $item_link
	 *
	 *
	 * @since 2.0.0
	 */
	private function _openItemAndGoBackToListView(\AcceptanceTester $I, $button, $link, $check_content, $item_link)
	{
		$item_found = $I->findPageWithItemAndScrollToItem($check_content);

		$I->assertEquals(true, $item_found);

		$I->click($item_link);
		$I->waitForElement(Generals::$pageTitle, 30);

		// go to main view
		$I->amOnPage(MainView::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see('BwPostman');

		// goto list view
		$I->click($link);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->see($button, Generals::$pageTitle);

		$item_found = $I->findPageWithItemAndScrollToItem($check_content);

		$I->assertEquals(true, $item_found);
	}
	/**
	 * @param \AcceptanceTester $I
	 * @param $check_content
	 * @param $lock_icon
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _checkCheckinResult(\AcceptanceTester $I, $check_content, $lock_icon)
	{
		$I->scrollTo(Generals::$sys_message_container, 0, 100);
		$I->see(AccessPage::$checkin_success_text, Generals::$alert_success);

		$item_found = $I->findPageWithItemAndScrollToItem($check_content);

		$I->assertEquals(true, $item_found);

		$I->dontSeeElement($lock_icon);
	}

	/**
	 * @param \AcceptanceTester  $I
	 * @param int               $i
	 * @param string            $button
	 * @param string            $link
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _checkinOtherItem($I, $i, $button, $link)
	{
		// Other user: next one from array. If current user is last one, take previous for other user
		$current_user   = AccessPage::$all_users[$i];
		$next_user      = $this->_getNextUser($i);

		$next_permission_array      = AccessPage::${$next_user['user'] . '_item_permissions'};

		$this->_switchLoggedInUser($I, $next_user);

		// open item
		$check_content  = $next_permission_array[$button]['own']['check content'];
		$check_link     = $next_permission_array[$button]['check link'];
		$item_link      = sprintf($check_link, $check_content);
		$col_nbr        = 2;

		if ($button == 'Newsletters')
		{
			$col_nbr++;
		}

		$this->_openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link);

		$this->_switchLoggedInUser($I, $current_user);

		$item_found = $I->findPageWithItemAndScrollToItem($check_content);

		if ($item_found !== true)
		{
			// logout current user
			$this->_switchLoggedInUser($I, $next_user);

			$this->_checkinOwnItem($I, $button, $link, $next_permission_array);

			$this->_switchLoggedInUser($I, $current_user);
		}
		else
		{
			$row_nbr    = $I->getTableRowIdBySearchValue($check_content);
			$lock_icon  = sprintf(AccessPage::$checkout_icon, $row_nbr, $col_nbr);

			$I->seeElement($lock_icon);
			$I->click($lock_icon);

			if ($current_user['name'] == 'BwPostmanAdmin')
			{
				$this->_checkCheckinResult($I, $check_content, $lock_icon);
			}
			else
			{
				$I->scrollTo(Generals::$sys_message_container, 0, 100);
				$I->see(AccessPage::$checkin_error_text, Generals::$alert_error);

				// logout current user
				$this->_switchLoggedInUser($I, $next_user);

				$this->_checkinOwnItem($I, $button, $link, $next_permission_array);

				$this->_switchLoggedInUser($I, $current_user);
			}
		}
	}


	/**
	 * @param $i
	 *
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	private function _getNextUser($i)
	{
		$next_user_id = $i + 1;
		if ($next_user_id > count(AccessPage::$all_users))
		{
			$next_user_id = 1;
		}
		$next_user = AccessPage::$all_users[$next_user_id];

		return $next_user;
	}
	/**
	 * @param $I
	 * @param $user_to_login
	 *
	 *
	 * @since 2.0.0
	 */
	private function _switchLoggedInUser($I, $user_to_login)
	{
		$loginPage = new LoginPage($I);

		// logout current user
		$this->_logout($I, $loginPage);

		// login as other user
		$this->_login($loginPage, $user_to_login);
	}

	/**
	 * Test method to check for allowed/forbidden to all parts of BwPostman
	 *
	 * @param   \AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsByDirectLinks(\AcceptanceTester $I)
	{
		$loginPage  = new LoginPage($I);
		foreach (AccessPage::$all_users as $user)
		{
			$this->_login($loginPage, $user);

			// Loop over array with direct links
			foreach (AccessPage::$direct_links as $link)
			{
				$this->_testResultForLink($I, $link, $user);
			}

			// Loop over array with direct links
			foreach (AccessPage::$button_links as $link)
			{
				$this->_testResultForLink($I, $link, $user);
			}

			$this->_logout($I, $loginPage);
		}
	}

	/**
	 * method to handle single link
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   string              $link
	 * @param   string              $user
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	private function _testResultForLink(\AcceptanceTester $I, $link, $user)
	{
		// click link an wait for page loaded
		$I->amOnPage($link);
		$I->waitForElement('.//*[@id=\'isisJsData\']/div/div', 30);

		$expected_result    = $this->_getExpectedResultByUser($I, $user, $link);

		// Check for allowed/forbidden
		return $expected_result;
	}

	/**
	 * method to get result that is expected for this user at this link
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   string              $link
	 * @param   string              $user
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	private function _getExpectedResultByUser(\AcceptanceTester $I, $user, $link)
	{
		$action = $this->_getActionForLink($I, $link);

		$permission_for_action = OptionsPage::$bwpm_groups[$user]['permissions'][$action];

		while ($permission_for_action == 'Inherited')
		{
			$user                   = OptionsPage::$bwpm_groups[$user]['parent'];
			$permission_for_action  = OptionsPage::$bwpm_groups[$user]['permissions'][$action];
		}

		return $permission_for_action;
	}

	/**
	 * method to get result that is expected for this user at this link
	 *
	 * @param   \AcceptanceTester    $I
	 * @param   string              $link
	 *
	 * @return  string
	 *
	 * @since   2.0.0
	 */
	private function _getActionForLink(\AcceptanceTester $I, $link)
	{
		$action = '';

		// @ToDo: following is dummy to eliminate IDE warning while coding ist not ready
		$I->waitForElement($link);

		return $action;
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   \AcceptanceTester        $I
	 * @param   LoginPage             $loginPage
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function _logout(\AcceptanceTester $I, LoginPage $loginPage)
	{
		$loginPage->logoutFromBackend($I);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $user
	 * @param string            $item_permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _duplicateNewsletter(\AcceptanceTester $I, $user, $item_permission_array)
	{
		$create_allowed    = $item_permission_array['Newsletters']['permissions']['Create'];

		if (!$create_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar['Duplicate']);
			return;
		}

		// duplicate
		NewsletterEditPage::CopyNewsletter($I, $user['user']);

		$archive_allowed    = $item_permission_array['Newsletters']['permissions']['Archive'];
		$delete_allowed     = $item_permission_array['Newsletters']['permissions']['Delete'];

		if (!$archive_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar['Archive']);

			$this->_switchLoggedInUser($I, Generals::$admin);

			$I->HelperArcDelItems($I, NewsletterManagerPage::$arc_del_array, NewsletterEditPage::$arc_del_array, $delete_allowed);

			$this->_switchLoggedInUser($I, $user);
		}
		else
		{
			if (!$delete_allowed)
			{
				$I->switchToArchive($I, NewsletterEditPage::$arc_del_array['archive_tab']);

				$I->dontSeeElement(Generals::$toolbar['Delete']);

				$this->_switchLoggedInUser($I, Generals::$admin);

				$I->HelperDeleteItems($I, NewsletterManagerPage::$arc_del_array, NewsletterEditPage::$arc_del_array);

				$this->_switchLoggedInUser($I,$user);

				$I->switchToSection($I, NewsletterManagerPage::$arc_del_array);
			}
		}
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $user
	 * @param string            $item_permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _sendNewsletter(\AcceptanceTester $I, $user, $item_permission_array)
	{
		$I->wantTo("Send a newsletter to real recipients, checked by permissions");

		$send_allowed    = $item_permission_array['Newsletters']['permissions']['SendNewsletter'];

		if (!$send_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar['Send']);
			return;
		}

		$this->_switchLoggedInUser($I, Generals::$admin);

		NewsletterEditPage::_CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		$this->_switchLoggedInUser($I, $user);
		$I->switchToSection($I, NewsletterManagerPage::$arc_del_array);

		$I->seeElement(Generals::$toolbar['Send']);
		NewsletterEditPage::SendNewsletterToRealRecipients($I, $user['username']);

		$this->_switchLoggedInUser($I, Generals::$admin);
		$I->HelperArcDelItems($I, NewsletterManagerPage::$arc_del_array, NewsletterEditPage::$arc_del_array, true);
		$this->_switchLoggedInUser($I, $user);
	}

	/**
	 * @param \AcceptanceTester $I
	 * @param string            $user
	 * @param string            $item_permission_array
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _setDefaultTemplate(\AcceptanceTester $I, $user, $item_permission_array)
	{
		$I->wantTo("Send a check setting default template by permissions");

		$set_default_allowed    = $item_permission_array['Templates']['permissions']['ModifyState'];

		TemplateManagerPage::setDefaultTemplates($I, $set_default_allowed);
	}
}

