//
// BwPostman Newsletter Component
//
// BwPostman Javascript for subscriber editing.
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

		if (pressbutton === 'subscriber.cancel') {
			Joomla.submitform(pressbutton, form);
		} else {
			let isValid = true;
			let action = pressbutton.split('.');

			if (action[1] !== 'cancel' && action[1] !== 'close') {
				let forms = document.querySelectorAll('form.form-validate');
				for (let i = 0; i < forms.length; i++) {
					if (!document.formvalidator.isValid(forms[i])) {
						isValid = false;
						break;
					}
				}
			}

			if (isValid) {
				Joomla.submitform(pressbutton, form);
				return true;
			}
		}
	};

	// This function changes the layout-value if the checkbox 'confirm' exists and if it is not checked
	function checkConfirmBox() {

		let form = document.adminForm;

		let cb = document.getElementById('confirm');

		// Does the checkbox 'confirm' exist?
		if (cb == null) {
			return;
		}

		if (form.jform_confirm.checked === false) {
			form.layout.value = 'unconfirmed';
		}
	}
};

