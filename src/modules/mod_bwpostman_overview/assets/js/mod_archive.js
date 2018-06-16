/**
 * BwPostman Newsletter Overview Module
 *
 * BwPostman Javascript for overview module.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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
 */
jQuery(document).ready(function()
	{
		menuItemHide(jQuery('#jform_assignment').val());
		jQuery('#jform_assignment').change(function()
		{
			menuItemHide(jQuery(this).val());
		})
	});
	function menuItemHide(val)
	{
		if (val == '')
		{
			jQuery('#newsletterselect-group').hide();
		}
		else
		{
			jQuery('#newsletterselect-group').show();
		}
	}
