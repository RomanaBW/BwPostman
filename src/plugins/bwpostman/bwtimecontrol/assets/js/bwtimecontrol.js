/**
 * BwPostman Newsletter TimeControl Plugin
 *
 * @version %%version_number%%
 * @package BwPostman TimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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
 */

jQuery(document).ready(function()
{
	let toggle_field       = 'a[href*="task=maintenance.startCron"]';

	jQuery(toggle_field).click(function() {
		let alertInfo    = 'div.alert-info';
		let alertHeader  = 'h4.alert-heading';
		let alertMessage = 'div.alert-message';

		jQuery(alertInfo).addClass('alert-warning');
		jQuery(alertInfo).removeClass('alert-info');
		jQuery(alertHeader).remove();
		jQuery(alertMessage).text(message);
	})

});

