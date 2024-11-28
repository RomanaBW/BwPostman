<?php
namespace Backend\Access;

use AcceptanceTester;
use Exception;
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
use Page\MaintenancePage as MaintenancePage;

use Page\OptionsPage as OptionsPage;
use RuntimeException;

/**
 * Class TestInstallationCest
 *
 * This class contains all methods to test access at backend of BwPostman
 *
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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _login(LoginPage $loginPage, array $user): void
	{
		$loginPage->logIntoBackend($user);
	}

	/**
	 * Test method to check for allowed/forbidden of list links at main view of BwPostman
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsForListViewButtonsFromMainView(AcceptanceTester $I): void
	{
		$I->wantTo("check permissions for main view list buttons");
		$I->expectTo("see appropriate messages");

		$loginPage = new LoginPage($I);

		foreach (AccessPage::$all_users as $user)
		{
			$this->_login($loginPage, $user);

			$I->click(AccessPage::$j_menu_components);
			$I->wait(1);
			$I->waitForElementVisible(AccessPage::$j_menu_tags, 3);
			$I->see('BwPostman', AccessPage::$j_menu_bwpostman);

			$I->click(AccessPage::$j_menu_bwpostman_link);
			$I->wait(1);
			$I->waitForElementVisible(sprintf(AccessPage::$j_menu_bwpostman_sub_item, 'Maintenance'), 3);

			foreach (AccessPage::$main_list_buttons as $button => $link)
			{
				$permission_array   = '_main_list_permissions';
				$allowed            = $this->getAllowedByUser($user, $button, $permission_array);
				$archive_allowed    = $this->getAllowedByUser($user, 'Archive', $permission_array);

				codecept_debug('User: ' . $user['user']);
				codecept_debug('Button: ' . $button);
				codecept_debug('Allowed: ' . $allowed);

				$this->checkAccessByJoomlaMenu($I, $button, $allowed);

				$I->amOnPage(MainView::$url);
				$I->waitForElementVisible(Generals::$pageTitle, 30);
				$I->see('BwPostman');

//				$I->see('BwPostman', Generals::$submenu['BwPostman']);
				$I->see('BwPostman Forum', AccessPage::$forum_icon);
				$I->see('BwPostman Forum', AccessPage::$forum_button);
				$I->see('BwPostman Manual', AccessPage::$manual_button);

				if (!$allowed)
				{
					$I->dontSeeElement($link);

					$this->checkVisibilityOfGeneralStatistics($I, $button, false);

					$this->checkVisibilityOfArchiveStatistics($I, $button, $archive_allowed, false);
					$I->wait(1);

					if ($button == 'Basic settings')
					{
						$I->dontSee('Options', Generals::$toolbar4['Options']);
					}
				}
				else
				{
					if ($button == 'Basic settings')
					{
						$I->see('Options', Generals::$toolbar4['Options']);
					}

					$this->checkVisibilityOfGeneralStatistics($I, $button, true);

					$this->checkVisibilityOfArchiveStatistics($I, $button, $archive_allowed, true);
					$I->wait(1);
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
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	private function getAllowedByUser($user, $button, $permission_array): bool
	{
		$permission_array = $user['user'] . $permission_array;

		return AccessPage::${$permission_array}[$button];
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $allowed
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkAccessByJoomlaMenu(AcceptanceTester $I, string $button, string $allowed): void
	{
		if ($button != 'Basic settings')
		{
			$I->see($button, sprintf(AccessPage::$j_menu_bwpostman_sub_item, $button));

			$I->click(sprintf(AccessPage::$j_menu_bwpostman_sub_item, $button));

			if ($allowed)
			{
				$I->waitForElementVisible(Generals::$pageTitle, 5);
				$I->see($button, Generals::$pageTitle);
			}
			else
			{
				$I->waitForElementVisible(Generals::$alert_error_1, 5);
				$I->see(sprintf(AccessPage::$list_view_no_permission, $button));
				$I->see(Generals::$extension, Generals::$pageTitle);
			}
		}
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $visible
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkVisibilityOfGeneralStatistics(AcceptanceTester $I, string $button, string $visible): void
	{
		if ($button != 'Archive' && $button != 'Basic settings' && $button != 'Maintenance')
		{
			$I->waitForElementVisible(AccessPage::$table_statistics_general, 5);
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
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $archive_allowed
	 * @param string           $visible
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkVisibilityOfArchiveStatistics(AcceptanceTester $I, string $button, string $archive_allowed, string $visible): void
	{
		if ($button != 'Archive' && $button != 'Basic settings' && $button != 'Maintenance')
		{
			if ($archive_allowed)
			{
				$I->click(AccessPage::$link_statistics_archive);
				$I->waitForElementVisible(AccessPage::$table_statistics_archive, 5);
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
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function TestAccessRightsForAddButtonsFromMainView(AcceptanceTester $I): void
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
				$allowed          = $this->getAllowedByUser($user, $button, $permission_array);

				$I->amOnPage(MainView::$url);
				$I->waitForElementVisible(Generals::$pageTitle, 30);
				$I->see('BwPostman');

				// click to icon

				if ($allowed)
				{
					$text_to_see    = $button . ' details:';

					if ($button == 'HTML-Template' ||$button == 'Text-Template')
					{
						$text_to_see    = 'Template details:';
					}

					$I->click($link);
					$I->waitForElementVisible(Generals::$pageTitle, 3);
					$I->see($text_to_see, Generals::$pageTitle);
					$I->click(Generals::$toolbar4['Back']);
					$I->wait(1);
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
	 * loop over all list views, loop over first half of user groups
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.4.0
	 */
	public function TestAccessRightsForActionsInListsByButtonsPart1(AcceptanceTester $I): void
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$partiallyUsers = array();

		foreach (AccessPage::$all_users as $user)
		{
			if ($user['half'] === 1)
			{
				$partiallyUsers[] = $user;
			}
		}

		$this->TestAccessRightsForActionsInListsByButtons($I, $partiallyUsers);
	}

	/**
	 * Test method to check for allowed/forbidden of a single list view by buttons in this list views,
	 * loop over all list views, loop over second half of user groups
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.4.0
	 */
	public function TestAccessRightsForActionsInListsByButtonsPart2(AcceptanceTester $I): void
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$partiallyUsers = array();

		foreach (AccessPage::$all_users as $user)
		{
			if ($user['half'] === 2)
			{
				$partiallyUsers[] = $user;
			}
		}

		$this->TestAccessRightsForActionsInListsByButtons($I, $partiallyUsers);
	}

	/**
	 * Test method to check for allowed/forbidden of a single list view by buttons in this list views,
	 * loop over all list views, loop over second half of user groups
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.2.5
	 */
	public function TestAccessRightsForActionsInListsByButtonsPart3(AcceptanceTester $I): void
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$partiallyUsers = array();

		foreach (AccessPage::$all_users as $user)
		{
			if ($user['half'] === 3)
			{
				$partiallyUsers[] = $user;
			}
		}

		$this->TestAccessRightsForActionsInListsByButtons($I, $partiallyUsers);
	}

	/**
	 * Test method to check for allowed/forbidden of a single list view by buttons in this list views,
	 * loop over all list views, loop over second half of user groups
	 *
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   4.2.5
	 */
	public function TestAccessRightsForActionsInListsByButtonsPart4(AcceptanceTester $I): void
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$partiallyUsers = array();

		foreach (AccessPage::$all_users as $user)
		{
			if ($user['half'] === 4)
			{
				$partiallyUsers[] = $user;
			}
		}

		$this->TestAccessRightsForActionsInListsByButtons($I, $partiallyUsers);
	}

	/**
	 * Test method to check for allowed/forbidden of a single list view by buttons in this list views,
	 * loop over all list views
	 *
	 * @param   AcceptanceTester $I
	 * @param array              $users
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	protected function TestAccessRightsForActionsInListsByButtons(AcceptanceTester $I, array $users): void
	{
		$I->wantTo("check permissions for single list by buttons");
		$I->expectTo("see appropriate messages");

		$loginPage = new LoginPage($I);

		// Loop over all users
		for ($i = 0; $i < count($users); $i++)
		{
			// Shortcut for user variable
			$user   = $users[$i];

			//@SpecialNote: This is a workaround to debug tests. Comment out usergroups/users which are not wanted
			$wanted_users = array(
				'BwPostmanAdmin',
				'BwPostmanManager',
				'BwPostmanPublisher',
				'BwPostmanEditor',
				'BwPostmanCampaignAdmin',
				'BwPostmanCampaignPublisher',
				'BwPostmanCampaignEditor',
				'BwPostmanMailinglistAdmin',
				'BwPostmanMailinglistPublisher',
				'BwPostmanMailinglistEditor',
				'BwPostmanNewsletterAdmin',
				'BwPostmanNewsletterPublisher',
				'BwPostmanNewsletterEditor',
				'BwPostmanSubscriberAdmin',
				'BwPostmanSubscriberPublisher',
				'BwPostmanSubscriberEditor',
				'BwPostmanTemplateAdmin',
				'BwPostmanTemplatePublisher',
				'BwPostmanTemplateEditor',
				);

			if (!in_array($user['user'], $wanted_users))
			{
				continue;
			}

			$this->_login($loginPage, $user);

			// Loop over main view list buttons
			foreach (AccessPage::$main_list_buttons as $button => $link)
			{
				// @SpecialNote: This is a workaround to debug tests. Comment tests which are wanted
				$unwanted_section    = array(
//					'Newsletters',
//					'Subscribers',
//					'Campaigns',
//					'Mailinglists',
//					'Templates',
//					'Archive',
//					'Basic settings',
//					'Maintenance',
					);

				if (in_array($button, $unwanted_section))
				{
					continue;
				}

				$list_permission_array  = '_main_list_permissions';
				$allowed                = $this->getAllowedByUser($user, $button, $list_permission_array);

				codecept_debug('User: ' . $user['user']);
				codecept_debug('Button: ' . $button);
				codecept_debug('Allowed: ' . $allowed);

				$I->amOnPage(MainView::$url);
				$I->waitForElement(Generals::$pageTitle, 30);
				$I->see('BwPostman');
				$I->seeElement(Generals::$toolbar4['BwPostman Forum']);
				$I->seeElement(Generals::$toolbar4['BwPostman Manual']);

				if ($allowed)
				{
					$I->click($link);
					$I->waitForElement(Generals::$pageTitle, 30);

					if ($button != 'Archive' && $button != 'Basic settings' && $button != 'Maintenance')
					{
						$I->see($button, Generals::$pageTitle);

						$item_permission_array = AccessPage::${$user['user'] . '_item_permissions'};

						codecept_debug('Create new item ' . $button);
						$this->createNewItem($I, $button, $item_permission_array);

						codecept_debug('Edit own item ' . $button);
						$this->editItem($I, $button, 'own', $item_permission_array); // own item

						codecept_debug('Edit other item ' . $button);
						$this->editItem($I, $button, 'other', $item_permission_array); // other item

						codecept_debug('Change state of item ' . $button);
						$this->changeStateItem($I, $button, $item_permission_array); //own item

						codecept_debug('Checkin item ' . $button);
						$this->checkinOwnItem($I, $button, $link, $item_permission_array);
						// @ToDo: Use other user to lock. Question: How to determine other user?
						// Workaround: If BwPostmanAdmin, then other user BwPostmanPublisher, else other user BwPostmanAdmin
						// $this->_checkinOtherItem($I, $i, $button, $link);

						codecept_debug('Restore item ' . $button);
						$this->restoreArchivedItem($I, $button, $user, $item_permission_array); // own item
						codecept_debug('Delete item ' . $button);
						$this->deleteArchivedItem($I, $button, $user, $item_permission_array); // own item

						if ($button == 'Newsletters')
						{
							codecept_debug('Duplicate newsletter');
							$this->duplicateNewsletter($I, $user, $item_permission_array);

							codecept_debug('Send newsletter');
							$this->sendNewsletter($I, $user, $item_permission_array);

							// @ToDo: set publish/unpublish date
							// @ToDo: handle queue
							// @ToDo: set/unset content template
						}
						elseif ($button == 'Subscribers')
						{
							// @ToDo: import subscribers
							// @ToDo: export subscribers
							// @ToDo: batch
							codecept_debug('Subscribers nothing to do');
						}
						elseif ($button == 'Templates')
						{
							codecept_debug('Set default template');
							$this->setDefaultTemplate($I, $item_permission_array);

							// @ToDo: import template
						}
					}
					elseif ($button == 'Archive')
					{
						// @ToDo: restore other item?
						// @ToDo: delete other item?
						codecept_debug('Archive nothing to do');
					}
					elseif ($button == 'Basic settings')
					{
						$I->see('BwPostman Configuration', Generals::$pageTitle);
						$I->waitForElementVisible(Generals::$toolbar['Save & Close'], 5);
						$I->click(Generals::$toolbar['Save & Close']);
						$I->waitForElement(Generals::$pageTitle, 10);
						$I->see('BwPostman');

						$I->seeElement(Generals::$toolbar4['Options']);
					}
					elseif ($button == 'Maintenance')
					{
						$item_permission_array = AccessPage::${$user['user'] . '_item_permissions'};
						$admin_allowed         = $item_permission_array['Maintenance']['permissions']['Admin'];

						$I->see($button, Generals::$pageTitle);

						if ($admin_allowed)
						{
							$I->seeElement(MaintenancePage::$checkTablesButton);
							$I->seeElement(MaintenancePage::$saveTablesButton);
							$I->seeElement(MaintenancePage::$restoreTablesButton);
							$I->seeElement(MaintenancePage::$settingsButton);
						}
						else
						{
							$I->dontSeeElement(MaintenancePage::$checkTablesButton);
							$I->dontSeeElement(MaintenancePage::$saveTablesButton);
							$I->dontSeeElement(MaintenancePage::$restoreTablesButton);
							$I->dontSeeElement(MaintenancePage::$settingsButton);
						}

						$I->seeElement(MaintenancePage::$forumButton);
					}
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
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function createNewItem(AcceptanceTester $I, string $button, array $permission_array): void
	{
		$allowed    = $permission_array[$button]['permissions']['Create'];

		$I->click(Generals::$toolbar['New']);
		$I->waitForElement(Generals::$pageTitle, 5);

		if ($allowed)
		{
			$title_to_see = $this->getTitleToSee($button, '');

			$I->see($title_to_see, Generals::$pageTitle);

			$I->click(Generals::$toolbar4['Cancel']);
			if ($button === 'Templates')
			{
				try
				{
					$I->seeInPopup('Any changes will not be saved. Close without saving?');
					$I->acceptPopup();
				}
				catch (Exception $e)
				{
					codecept_debug('Popup Templates not found ' . $e->getMessage());
				}
			}

			$I->waitForElement(Generals::$pageTitle, 5);
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
	 * @param string $button
	 * @param string $add_text
	 * @param string $check_content
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function getTitleToSee(string $button, string $add_text, string $check_content = ''): string
	{
		$title_to_see = substr($button, 0, -1) . ' details: ';

		if ($button == 'HTML-Template' || $button == 'Text-Template' || $button == 'Templates')
		{
			$title_to_see = 'Template details:';

			$add_text = ' ' . $check_content . ' ' . $add_text;
		}

		$title_to_see .= $add_text;

		return substr($title_to_see, 0, 42);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $action
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function editItem(AcceptanceTester $I, string $button, string $action, array $permission_array): void
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
		$tableId = 'main-table';
		if ($button == 'Subscribers')
		{
			$tableId = 'main-table-bw-confirmed';
		}

		$I->filterForItemToEdit($check_content, $tableId);

		// by link
		if ($allowed)
		{
			$I->seeLink($check_content);
			$I->click(sprintf($check_link, $check_content));
			$I->waitForElement(Generals::$pageTitle, 30);

			$this->checkForEditResult($I, $button, $check_content, $check_locator, $allowed);
		}
		else
		{
			$I->dontSeeLink($check_content);
		}

		// by checkbox
		$checkbox       = $this->getCheckbox($I, $check_content, $tableId);

		$I->click($checkbox);
		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->click(Generals::$toolbarActions);
		$I->waitForElementVisible(Generals::$toolbar4['Edit'], 3);
		$I->click(Generals::$toolbar4['Edit']);

		$this->checkForEditResult($I, $button, $check_content, $check_locator, $allowed);

		$I->clickAndWait(Generals::$clear_button, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $check_content
	 * @param string           $check_locator
	 * @param boolean          $allowed
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkForEditResult(AcceptanceTester $I, string $button, string $check_content, string $check_locator, bool $allowed): void
	{
		if ($allowed)
		{
			try
			{
				// Click off message that a content template wants to be edited
				$I->click(Generals::$systemMessageClose);
				$I->waitForElementNotVisible(Generals::$systemMessageClose, 3);

			}
			catch(RuntimeException $e)
			{
				// Do nothing, if there is no content template
				codecept_debug('No content template ' . $e->getMessage());
			}

			$addText = '[ Edit ]';

			$title_to_see = $this->getTitleToSee($button, $addText, $check_content);

			$I->see($title_to_see, Generals::$pageTitle);
			$I->seeInField($check_locator, $check_content);

			$I->click(Generals::$toolbar4['Cancel']);
		}
		else
		{
			$I->see($button, Generals::$pageTitle);
			// for button tests I may only get here at edit other owners items!
			$I->see('No permission to edit this item!', Generals::$alert_error_1);
			$I->click(Generals::$systemMessageClose);
			$I->waitForElementNotVisible(Generals::$systemMessageClose, 3);
		}

		if ($button === "Templates")
		{
			try
			{
				$I->acceptPopup();
			}
			catch (Exception $e)
			{
				codecept_debug('Popup Templates not found ' . $e->getMessage());
			}
		}

		$I->waitForElement(Generals::$pageTitle, 30);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $title_content
	 * @param string           $tableId
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	private function getCheckbox(AcceptanceTester $I, string $title_content, string $tableId = 'main-table'): string
	{
		$checkbox_nbr  = $I->getTableRowIdBySearchValue($title_content, $tableId);

		return sprintf(AccessPage::$checkbox_identifier, $checkbox_nbr - 1);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function changeStateItem(AcceptanceTester $I, string $button, array $permission_array): void
	{
		$has_state_to_change    = array('Newsletters', 'Mailinglists', 'Templates');
		$allowed                = $permission_array[$button]['permissions']['ModifyState'];
		$extraClick             = '';

		if (in_array($button, $has_state_to_change))
		{
			if ($button == 'Newsletters')
			{
				$I->click(NewsletterManagerPage::$tab2);// switch to tab sent newsletters first
				$I->waitForElementVisible(".//*[@id='main-table']/thead/tr/th[5]/a", 20);
				$extraClick = NewsletterManagerPage::$tab2;
			}

			$item_text  = strtolower(substr($button, 0, -1));

			if ($item_text == 'mailinglist')
			{
				$item_text = 'mailing list';
			}

			codecept_debug("Publish by Icon");
			$I->publishByIcon($I, $permission_array[$button]['publish_by_icon'], $item_text, $extraClick, $allowed);
			codecept_debug("Publish by Toolbar");
			$I->publishByToolbar($I, $permission_array[$button]['publish_by_toolbar'], $item_text, $extraClick, $allowed);

			if ($button == 'Newsletters')
			{
				$I->scrollTo(NewsletterManagerPage::$tab1, 0, -100);
				$I->wait(1);
				$I->click(NewsletterManagerPage::$tab1);// switch to tab unsent newsletters to finish
				$I->waitForElement(".//*[@id='main-table']/thead/tr/th[5]/a", 20);
			}

			$I->clickAndWait(Generals::$clear_button, 1);
		}
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $user
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function restoreArchivedItem(AcceptanceTester $I, string $button, array $user, array $permission_array): void
	{
		$archive_allowed    = $permission_array[$button]['permissions']['Archive'];
		$restore_allowed    = $permission_array[$button]['permissions']['Restore'];

		if (!$archive_allowed)
		{
			$I->scrollTo(Generals::$joomlaHeader, 0, -100);
			$I->wait(1);
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->dontSeeElement(Generals::$toolbar4['Archive']);
			return;
		}

		$ui_data = $this->getUiData($button);

		$edit_data   = $ui_data['edit_data'];
		$manage_data = $ui_data['manage_data'];

		if (!$restore_allowed)
		{
			// @ToDo: Check for visibility of tabs
			$I->scrollTo(Generals::$joomlaHeader, 0, -100);
			$I->wait(1);
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->switchToArchive($I, $edit_data['archive_tab']);
			$I->dontSeeElement(Generals::$toolbar4['Restore']);
			return;
		}

		// create item to play with
		$edit_data = $this->createItemForRestoreAndDelete($I, $button, $user, $permission_array, $edit_data);

		// archive item
		$I->HelperArchiveItems($I, $manage_data, $edit_data);

		// restore item
		$I->HelperRestoreItems($I, $manage_data, $edit_data);

		// delete item to cleanup
		$this->deleteItem($I, $button, $user, $permission_array, $manage_data, $edit_data);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $user
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function deleteArchivedItem(AcceptanceTester $I, string $button, array $user, array $permission_array): void
	{
		$archive_allowed    = $permission_array[$button]['permissions']['Archive'];

		if (!$archive_allowed)
		{
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->dontSeeElement(Generals::$toolbar4['Archive']);
			return;
		}

		$ui_data = $this->getUiData($button);

		$edit_data   = $ui_data['edit_data'];
		$manage_data = $ui_data['manage_data'];

		// create item to play with
		$edit_data = $this->createItemForRestoreAndDelete($I, $button, $user, $permission_array, $edit_data);

		// delete item to cleanup
		$this->deleteItem($I, $button, $user, $permission_array, $manage_data, $edit_data);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $link
	 * @param array            $permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkinOwnItem(AcceptanceTester $I, string $button, string $link, array $permission_array): void
	{
		$check_content  = $permission_array[$button]['own']['check content'];
		$check_link     = $permission_array[$button]['check link'];
		$item_link      = sprintf($check_link, $check_content);
		$col_nbr        = 2;
		$tableId        = 'main-table';

		if ($button == 'Subscribers')
		{
			$tableId = 'main-table-bw-confirmed';
		}

		if ($button == 'Newsletters')
		{
			$col_nbr++;
		}

		$this->openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link, $tableId);

		$row_nbr    = $I->getTableRowIdBySearchValue($check_content, $tableId);
		$lock_icon  = sprintf(AccessPage::$checkout_icon, $row_nbr, $col_nbr);

		if ($button == 'Subscribers')
		{
			$lock_icon = str_replace('main-table', 'main-table-bw-confirmed', $lock_icon);
		}

		// by icon
		$I->seeElement($lock_icon);
		$I->click($lock_icon);
		$this->checkCheckinResult($I, $check_content, $lock_icon, $button, $tableId);

		$this->openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link, $tableId);

		// see lock icon
		$I->seeElement($lock_icon);

		// by toolbar
		$checkbox       = $this->getCheckbox($I, $check_content, $tableId);
		$I->click($checkbox);

		$I->scrollTo(Generals::$joomlaHeader, 0, -100);
		$I->wait(1);
		$I->clickAndWait(Generals::$toolbarActions, 1);
		$I->click(Generals::$toolbar4['Check-In']);
		$this->checkCheckinResult($I, $check_content, $lock_icon, $button, $tableId);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param string           $link
	 * @param string           $check_content
	 * @param string           $item_link
	 * @param string           $tableId
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function openItemAndGoBackToListView(AcceptanceTester $I, string $button, string $link, string $check_content, string $item_link, string $tableId): void
	{
		$I->filterForItemToEdit($check_content, $tableId);

		$I->click($item_link);
		$I->waitForElementVisible(Generals::$pageTitle, 30);
        $I->wait(1);

		// go to main view
		$I->amOnPage(MainView::$url);
		$I->waitForElementVisible(Generals::$pageTitle, 30);
		$I->see('BwPostman');

		// goto list view
		$I->click($link);
		$I->waitForElementVisible(Generals::$pageTitle, 30);
		$I->see($button, Generals::$pageTitle);

		$I->filterForItemToEdit($check_content, $tableId);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $check_content
	 * @param string           $lock_icon
	 * @param string           $button
	 * @param string           $tableId
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkCheckinResult(AcceptanceTester $I, string $check_content, string $lock_icon, string $button, string $tableId): void
	{
		$I->scrollTo(Generals::$sys_message_container, 0, 100);
		$I->wait(1);

		$item = substr(strtolower($button), 0, -1);

		$item = str_replace('subscriber', 'recipient', $item);
		$item = str_replace('mailinglist', 'mailing list', $item);

		$I->see(sprintf(AccessPage::$checkin_success_text, $item), Generals::$alert_success);

		$item_found = $I->filterForItemToEdit($check_content, $tableId);
		$I->assertEquals(true, $item_found);

		$I->dontSeeElement($lock_icon);

		$I->clickAndWait(Generals::$clear_button, 1);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param int              $i
	 * @param string           $button
	 * @param string           $link
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function checkinOtherItem(AcceptanceTester $I, int $i, string $button, string $link): void
	{
		// Other user: next one from array. If current user is last one, take previous for other user
		$current_user   = AccessPage::$all_users[$i];
		$next_user      = $this->getNextUser($i);

		$next_permission_array      = AccessPage::${$next_user['user'] . '_item_permissions'};

		$this->switchLoggedInUser($I, $next_user);

		// open item
		$check_content  = $next_permission_array[$button]['own']['check content'];
		$check_link     = $next_permission_array[$button]['check link'];
		$item_link      = sprintf($check_link, $check_content);
		$col_nbr        = 2;
		$tableId        = 'main-table';

		if ($button === 'Subscribers')
		{
			$tableId = 'main-table-bw-confirmed';
		}


		if ($button == 'Newsletters')
		{
			$col_nbr++;
		}

		$this->openItemAndGoBackToListView($I, $button, $link, $check_content, $item_link, $tableId);

		$this->switchLoggedInUser($I, $current_user);

		$item_found = $I->filterForItemToEdit($check_content, $tableId);

		if ($item_found !== true)
		{
			// logout current user
			$this->switchLoggedInUser($I, $next_user);

			$this->checkinOwnItem($I, $button, $link, $next_permission_array);

			$this->switchLoggedInUser($I, $current_user);
		}
		else
		{
			$row_nbr    = $I->getTableRowIdBySearchValue($check_content, $tableId);
			$lock_icon  = sprintf(AccessPage::$checkout_icon, $row_nbr, $col_nbr);

			$I->seeElement($lock_icon);
			$I->click($lock_icon);

			if ($current_user['name'] == 'BwPostmanAdmin')
			{
				$this->checkCheckinResult($I, $check_content, $lock_icon, $button, $tableId);
			}
			else
			{
				$I->scrollTo(Generals::$sys_message_container, 0, 100);
				$I->wait(1);
				$I->see(AccessPage::$checkin_error_text, Generals::$alert_error);

				// logout current user
				$this->switchLoggedInUser($I, $next_user);

				$this->checkinOwnItem($I, $button, $link, $next_permission_array);

				$this->switchLoggedInUser($I, $current_user);
			}
		}
	}


	/**
	 * @param int $i
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getNextUser(int $i): array
	{
		$next_user_id = $i + 1;
		if ($next_user_id > count(AccessPage::$all_users))
		{
			$next_user_id = 1;
		}

		return AccessPage::$all_users[$next_user_id];
	}

	/**
	 * @param AcceptanceTester $I
	 * @param array            $user_to_login
	 *
	 * @throws Exception
	 *@since 2.0.0
	 *
	 */
	private function switchLoggedInUser(AcceptanceTester $I, array $user_to_login): void
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
	 * @param   AcceptanceTester            $I
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
//	public function TestAccessRightsByDirectLinks(\AcceptanceTester $I)
//	{
//		$loginPage  = new LoginPage($I);
//		foreach (AccessPage::$all_users as $user)
//		{
//			$this->_login($loginPage, $user);
//
//			// Loop over array with direct links
//			foreach (AccessPage::$direct_links as $link)
//			{
//				$this->testResultForLink($I, $link, $user);
//			}
//
//			// Loop over array with button links
//			foreach (AccessPage::$button_links as $link)
//			{
//				$this->testResultForLink($I, $link, $user);
//			}
//
//			$this->_logout($I, $loginPage);
//		}
//	}

	/**
	 * method to handle single link
	 *
	 * @param   AcceptanceTester $I
	 * @param string             $link
	 * @param string             $user
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	protected function testResultForLink(AcceptanceTester $I, string $link, string $user): string
	{
		// click link a wait for page loaded
		$I->amOnPage($link);
		$I->waitForElement('.//*[@id=\'isisJsData\']/div/div', 30);

		// Check for allowed/forbidden
		return $this->getExpectedResultByUser($I, $user, $link);
	}

	/**
	 * method to get result that is expected for this user at this link
	 *
	 * @param   AcceptanceTester $I
	 * @param string             $link
	 * @param string             $user
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getExpectedResultByUser(AcceptanceTester $I, string $user, string $link): string
	{
		$action = $this->getActionForLink($I, $link);

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
	 * @param   AcceptanceTester $I
	 * @param string             $link
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getActionForLink(AcceptanceTester $I, string $link): string
	{
		$action = '';

		// @SpecialNote: following is dummy to eliminate IDE warning while coding is not ready
		$I->waitForElement($link);

		return $action;
	}

	/**
	 * Test method to logout from backend
	 *
	 * @param   AcceptanceTester $I
	 * @param   LoginPage        $loginPage
	 * @param boolean            $truncateSession
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function _logout(AcceptanceTester $I, LoginPage $loginPage, bool $truncateSession = false): void
	{
		$loginPage->logoutFromBackend($I, $truncateSession);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param array            $user
	 * @param array            $item_permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function duplicateNewsletter(AcceptanceTester $I, array $user, array $item_permission_array): void
	{
		$create_allowed    = $item_permission_array['Newsletters']['permissions']['Create'];

		if (!$create_allowed)
		{
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->dontSeeElement(Generals::$toolbar4['Duplicate']);
			return;
		}

		// duplicate, also cleans up
		NewsletterEditPage::CopyNewsletter($I, $user['user'], false);

		// cleanup
		$this->switchLoggedInUser($I, Generals::$admin);
		$I->switchToSection($I, NewsletterManagerPage::$arc_del_array);

		$I->HelperArcDelItems($I, NewsletterManagerPage::$arc_del_array, NewsletterEditPage::$arc_del_array, true);
		$this->switchLoggedInUser($I, $user);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param array            $user
	 * @param array            $item_permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function sendNewsletter(AcceptanceTester $I, array $user, array $item_permission_array): void
	{
		$I->wantTo("Send a newsletter to real recipients, checked by permissions");

		$send_allowed    = $item_permission_array['Newsletters']['permissions']['SendNewsletter'];

		if (!$send_allowed)
		{
			$I->clickAndWait(Generals::$toolbarActions, 1);
			$I->dontSeeElement(Generals::$toolbar4['Send']);
			return;
		}

		$this->switchLoggedInUser($I, Generals::$admin);

		NewsletterEditPage::CreateNewsletterWithoutCleanup($I, Generals::$admin['author']);

		$this->switchLoggedInUser($I, $user);
		$I->switchToSection($I, NewsletterManagerPage::$arc_del_array);

		NewsletterEditPage::SendNewsletterToRealRecipients($I, false, false, false, 10);

		$this->switchLoggedInUser($I, Generals::$admin);

		$I->amOnPage(NewsletterManagerPage::$url);
		$I->clickAndWait(NewsletterManagerPage::$tab2, 1);

		$I->HelperArcDelItems($I, NewsletterManagerPage::$arc_del_array, NewsletterEditPage::$arc_del_array, true);
		$this->switchLoggedInUser($I, $user);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param array            $item_permission_array
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function setDefaultTemplate(AcceptanceTester $I, array $item_permission_array): void
	{
		$I->amOnPage(TemplateManagerPage::$url);
		$I->waitForElement(Generals::$pageTitle, 30);
		$I->wantTo("check setting default template by permissions");

		$set_default_allowed = $item_permission_array['Templates']['permissions']['ModifyState'];

		$I->scrollTo(Generals::$filter_toolbar, 0, -100);
		$I->wait(1);
		$I->waitForElementVisible(Generals::$clear_button, 2);
		$I->clickAndWait(Generals::$clear_button, 2);

		codecept_debug('Scroll to pagination');

		try
		{
			$I->scrollTo(Generals::$pagination_bar);
			$I->wait(1);

			$linkToFirstPage    = count($I->grabMultiple(Generals::$first_page));

			if ($linkToFirstPage === 1)
			{
				$I->click(Generals::$first_page);
			}
		}
		catch (Exception $e)
		{
			codecept_debug('No pagination available ' . $e->getMessage());
		}

		$I->scrollTo(TemplateManagerPage::$default_button1, 0, -250);
		$I->wait(1);
		TemplateManagerPage::setDefaultTemplates($I, $set_default_allowed);
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $user
	 * @param array            $permission_array
	 * @param array            $edit_data
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function createItemForRestoreAndDelete(AcceptanceTester $I, string $button, array $user, array $permission_array, array $edit_data): array
	{
		$create_allowed = $permission_array[$button]['permissions']['Create'];

		if (!$create_allowed)
		{
			$adminUser = AccessPage::$all_users[0];
			$this->switchLoggedInUser($I, $adminUser);
		}

		switch ($button)
		{
			case 'Newsletters':
				NewsletterEditPage::CreateNewsletterWithoutCleanup($I, $user['user']);
				break;
			case 'Subscribers':
				$this->switchLoggedInUser($I, $user);

				SubscriberEditPage::CreateSubscriberWithoutCleanup($I);
				$edit_data = SubscriberEditPage::prepareDeleteArray($I);

				break;
			case 'Campaigns':
				CampaignEditPage::createCampaignWithoutCleanup($I);
				break;
			case 'Mailinglists':
				MailinglistEditPage::createMailinglistWithoutCleanup($I);
				break;
			case 'Templates':
				TemplateEditPage::createTemplateWithoutCleanup($I, $user['user']);
				break;
		}

		if (!$create_allowed)
		{
			$this->switchLoggedInUser($I, $user['user']);
		}

		return $edit_data;
	}

	/**
	 * @param string $button
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	private function getUiData(string $button): array
	{
		$ui_data     = array();

		switch ($button)
		{
			case 'Newsletters':
				$ui_data['edit_data']   = NewsletterEditPage::$arc_del_array;
				$ui_data['manage_data'] = NewsletterManagerPage::$arc_del_array;
				break;
			case 'Subscribers':
				$ui_data['edit_data']   = SubscriberEditPage::$arc_del_array;
				$ui_data['manage_data'] = SubscriberManagerPage::$arc_del_array;
				break;
			case 'Campaigns':
				$ui_data['edit_data']   = CampaignEditPage::$arc_del_array;
				$ui_data['manage_data'] = CampaignManagerPage::$arc_del_array;
				break;
			case 'Mailinglists':
				$ui_data['edit_data']   = MailinglistEditPage::$arc_del_array;
				$ui_data['manage_data'] = MailinglistManagerPage::$arc_del_array;
				break;
			case 'Templates':
				$ui_data['edit_data']   = TemplateEditPage::$arc_del_array;
				$ui_data['manage_data'] = TemplateManagerPage::$arc_del_array;
				break;
		}

		return $ui_data;
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $button
	 * @param array            $user
	 * @param array            $permission_array
	 * @param array            $manage_data
	 * @param array            $edit_data
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	private function deleteItem(AcceptanceTester $I, string $button, array $user, array $permission_array, array $manage_data, array $edit_data): void
	{
		$delete_allowed = $permission_array[$button]['permissions']['Delete'];

		// HelperArcDelItems
		if (!$delete_allowed)
		{
			$I->dontSeeElement(Generals::$toolbar4['Delete']);

			$adminUser = AccessPage::$all_users[0];
			$this->switchLoggedInUser($I, $adminUser);

			$I->HelperArcDelItems($I, $manage_data, $edit_data, true);
			$this->switchLoggedInUser($I, $user['user']);
		}
		else
		{
			$I->HelperArcDelItems($I, $manage_data, $edit_data, $delete_allowed);
		}
	}
}
