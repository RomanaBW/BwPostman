<?php
namespace Page;

/**
 * Class UserAccountPage
 *
 * @package Register Subscribe Plugin
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
 * @since   4.1.0
 */
class UserAccountPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	public static $filter_field             = "//*[@id='search']";

	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	public static $filter_go_button         = "//*/button[@type='submit'][contains(@class, 'btn-primary')]";

	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	public static $delete_button      = "//*/button[contains(@class, 'button-delete')]";

	/**
	 * @var string
	 *
	 * @since 4.1.0
	 */
	public static $deleteSuccessMsg = "User deleted.";
}

