<?php
namespace Page;

/**
 * Class InstallUsersPage
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
 *
 * @since   2.1.0
 */
class InstallUsersPage
{
	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $user_management_url              = 'administrator/index.php?option=com_users&view=users';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $registerName              = '//*[@id="jform_name"]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $registerLoginName              = '//*[@id="jform_username"]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $registerPassword1            = '//*[@id="jform_password"]';
	//*[@id="jform_password"]

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $registerPassword2         = '//*[@id="jform_password2"]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $registerEmail              = '//*[@id="jform_email"]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $accountDetailsTab              = "//*/a[@id='tab-details']";
	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $usergroupTab              = "//*/a[@id='tab-groups']";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $publicGroup      = "//*/section[@id='groups']/fieldset/div/div[1]/div/label";

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $usergroupCheckbox              = '//*[@id="1group_%s"]';

	/**
	 * @var string
	 *
	 * @since 2.1.0
	 */
	public static $createSuccessMsg              = "User saved.";
}
