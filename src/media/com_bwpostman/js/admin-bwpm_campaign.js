//
// BwPostman Newsletter Component
//
// BwPostman Javascript for campaign validation.
//
// @version %%version_number%%
// @package BwPostman-Admin
// @author Romana Boldt
// @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
// @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
// @license GNU/GPL v3, see LICENSE.txt
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

window.onload = function() {
	let Joomla = window.Joomla || {};

	Joomla.submitbutton = function (pressbutton) {
		let form = document.adminForm;
		if (pressbutton === 'campaign.cancel') {
			Joomla.submitform(pressbutton, form);
			return;
		}

		if ((pressbutton === 'campaign.apply') || (pressbutton === 'campaign.save') || (pressbutton === 'campaign.save2new') || (pressbutton === 'campaign.save2copy')) {
			let errors = 0;
			let title = form.jform_title.value;
			let inputs = document.getElementsByTagName('input');
			let recipients = 0;

			for (let i = 0; i < inputs.length; i++) {
				if (inputs[i].type.toLowerCase() === 'checkbox' && inputs[i].checked === true) {
					recipients++;
				}
			}

			if (title === '') {
				alert(document.getElementById('alertTitle').value);
				errors++;
			}

			if (!recipients) {
				alert(document.getElementById('alertRecipients').value);
				errors++;
			}

			if (errors > 0) {
				return false;
			}
			Joomla.submitform(pressbutton, form);
		}
	}
};
