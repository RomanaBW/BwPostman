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

/*
 * Modified version of
 * FormChanges(string FormID | DOMelement FormNode)
 * Returns true if any form element changed.
 * And false no changes have been made.
 * NULL indicates that the form does not exist.
 *
 * seen on http://blogs.sitepointstatic.com/examples/tech/formchanges/index.html
 */
function FormChanges(form) {

	// get form
	if (typeof form == "string") form = document.getElementById(form);
	if (!form || !form.nodeName || form.nodeName.toLowerCase() !== "form") return null;

	// find changed elements
	let changed = [], n, c, def, o, ol, opt;
	for (let e = 0, el = form.elements.length; e < el; e++) {
		n = form.elements[e];
		c = false;

		switch (n.nodeName.toLowerCase()) {

			// select boxes
			case "select":
				def = 0;
				for (o = 0, ol = n.options.length; o < ol; o++) {
					opt = n.options[o];
					c = c || (opt.selected !== opt.defaultSelected);
					if (opt.defaultSelected) def = o;
				}
				if (c && !n.multiple) c = (def !== n.selectedIndex);
				break;

			// input / textarea
			case "textarea":
			case "input":

				switch (n.type.toLowerCase()) {
					case "checkbox":
					case "radio":
						// checkbox / radio
						c = (n.checked !== n.defaultChecked);
						break;
					default:
						// standard values
						c = (n.value !== n.defaultValue);
						break;
				}
				break;
		}

		if (c) return true;
	}

	return false;

}

window.onload = function() {
	let Joomla = window.Joomla || {};

	Joomla.submitbutton = function (pressbutton) {
		let form = document.adminForm;

		if (pressbutton === 'template.cancel') {
			if (FormChanges(form) === true) {
				// confirm if cancel or not
				let confirmCancel = confirm(document.getElementById('cancelText').value);
				if (confirmCancel === false) {
					return;
				}
			}
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

	let framefenster = document.getElementById("myIframe");

	if (framefenster.contentWindow.document.body) {
		let framefenster_size = framefenster.contentWindow.document.body.offsetHeight+20;
		if (document.all && !window.opera) {
			framefenster_size = framefenster.contentWindow.document.body.scrollHeight+20;
		}
		framefenster.style.height = framefenster_size + 'px';
	}
};
