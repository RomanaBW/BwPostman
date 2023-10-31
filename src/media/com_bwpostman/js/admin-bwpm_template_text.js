//
// BwPostman Newsletter Component
//
// BwPostman Javascript for template editing.
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

// check form field values
function checkValues(turn) {
	let inputs = '';
	let elements = document.adminForm.elements;
	let fieldValue = '';
	for (let i = 0; i < elements.length; i++) {
		if (elements[i].getAttribute('id') !== 'jform_tpl_html') {
			fieldValue = elements[i].value;
		} else {
			fieldValue = elements[i].value.length;
		}
		if (elements[i].getAttribute('checked') !== false) {
			let fieldChecked = elements[i].getAttribute('checked');
		}
		inputs += fieldValue + fieldChecked;
	}
	if (turn === 0) {
		writeStore("inputs", inputs);
	} else {
		return inputs;
	}
}

