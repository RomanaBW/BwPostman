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

		if (pressbutton === 'template.cancel') {
			if (jQuery("#adminForm").data("changed")) {
				// confirm if cancel or not
				var confirmCancel = confirm(document.getElementById('cancelText').value);
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

	var framefenster = document.getElementById("myIframe");

	if (framefenster.contentWindow.document.body) {
		var framefenster_size = framefenster.contentWindow.document.body.offsetHeight+20;
		if (document.all && !window.opera) {
			framefenster_size = framefenster.contentWindow.document.body.scrollHeight+20;
		}
		framefenster.style.height = framefenster_size + 'px';
	}
};

jQuery( document ).ready(function() {
	jQuery("#adminForm :input").change(function() {
		jQuery("#adminForm").data("changed",true);
	});
});

