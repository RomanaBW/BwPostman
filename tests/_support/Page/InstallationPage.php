<?php
namespace Page;

/**
 * Class MaintenancePage
 *
 * @package Page
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
class InstallationPage
{
    // include url of current page
    public static $install_url          = "/administrator/index.php?option=com_installer";
    public static $uninstall_url        = "/administrator/index.php?option=com_installer&view=manage";

    /*
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $installField      = ".//*[@id='install_package']";
	public static $installButton     = ".//*[@id='installbutton_package']";

	public static $headingInstall       = "Extensions: Install";
	public static $headingManage        = "Extensions: Manage";

	public static $delete_button        = ".//*[@id='toolbar-delete']/button";

	public static $installSuccessMsg    = "Installation of the package was successful.";
	public static $uninstallSuccessMsg  = "Thank you for using BwPostman. BwPostman is now removed from your system.";

	public static $optionsSuccessMsg    = "Configuration successfully saved.";
}
