//
// BwPostman Newsletter Component
//
// BwPostman base Javascript for template editing.
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
	var Joomla = window.Joomla || {};

	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;

		if (pressbutton === 'template.save') {
			writeStore("inputs", 0);
			writeStore("jpanetabs_template_tabs", 0);
			writeStore("jpanetabs_buttons", 0);
		}

		if (pressbutton === 'template.apply') {
			writeStore("inputs", 0);
		}

		if (pressbutton === 'template.save2copy') {
			writeStore("inputs", 0);
		}

		if (pressbutton === 'template.cancel') {
			// check if form field values has changed
			var inputs_old = readStore("inputs");
			var inputs = checkValues(1);
			if (inputs_old === inputs) {
			} else {
				// confirm if cancel or not
				var confirmCancel = confirm(document.getElementById('cancelText').value);
				if (confirmCancel === false) {
					return;
				}
			}
			writeStore("inputs", 0);
			writeStore("jpanetabs_template_tabs", 0);
			writeStore("jpanetabs_buttons", 0);
			Joomla.submitform(pressbutton, form);
			return;
		}

		// Validate input fields
		if (form.jform_title.value === "") {
			alert(document.getElementById('titleErrorText').value);
		} else if (form.jform_description.value === "") {
			alert(document.getElementById('descriptionErrorText').value);
		} else {
			Joomla.submitform(pressbutton, form);
		}
	};

	// write to storage
	function writeStore(item, value) {
		var test = 'test';
		try {
			localStorage.setItem(test, test);
			localStorage.removeItem(test);
			localStorage[item] = value;
		} catch (e) {
			Cookie.write(item, value);
		}
	}

	// read storage
	function readStore(item) {
		var test = 'test';
		var itemValue = '';
		try {
			localStorage.setItem(test, test);
			localStorage.removeItem(test);
			itemValue = localStorage[item];
		} catch (e) {
			itemValue = Cookie.read(item);
		}
		return itemValue;
	}
	var framefenster = document.getElementById("myIframe");

	if (framefenster.contentWindow.document.body) {
		var framefenster_size = framefenster.contentWindow.document.body.offsetHeight;
		if (document.all && !window.opera) {
			framefenster_size = framefenster.contentWindow.document.body.scrollHeight;
		}
		framefenster.style.height = framefenster_size + 'px';
	}
	// check if store is empty or 0
	var store = readStore("inputs");
	if (store === 0 || store === undefined || store === null) {
		checkValues(0);
	}

};
