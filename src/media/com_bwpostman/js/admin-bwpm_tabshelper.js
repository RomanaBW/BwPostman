//
// BwPostman Newsletter Component
//
// BwPostman Javascript for tabs.
//
// @version %%version_number%%
// @package BwPostman-Admin
// @author Romana Boldt, Karl Klostermann
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

document.addEventListener('readystatechange', (event) => {
	var myTabs = document.getElementsByClassName('bwpm-arc-tab');
	// alert('Archive Tabs length: ' + myTabs.length);

	var i;

	for(i = 0, myTabs.length; i < myTabs.length; i++) {
		(function(selectedTab) {
			myTabs[i].onclick = function () {
				var layout = selectedTab.getAttribute("data-layout");

				var layoutElement = document.getElementById('layout');
				layoutElement.value = layout;
				document.forms[0].submit();
			};
		})(myTabs[i]);
	}
});

function ready(callbackFunc) {
	if (document.readyState !== 'loading')
	{
		// Document is already ready, call the callback directly
		callbackFunc();
	}
	else if (document.addEventListener)
	{
		// All modern browsers to register DOMContentLoaded
		document.addEventListener('DOMContentLoaded', callbackFunc);
	}
	else
	{
		// Old IE browsers
		document.attachEvent('onreadystatechange', function() {
			if (document.readyState === 'complete')
			{
				callbackFunc();
			}
		});
	}
}

ready(function() {
	var bwpModal = document.getElementById('bwp-modal');

	bwpModal.addEventListener('show.bs.modal', function (event) {
		// Button that triggered the modal
		var button = event.relatedTarget;
		var windowheight = window.innerHeight - 225;

		// Extract info from data-bs-* attributes
		var title      = button.getAttribute('data-bs-title');
		var contentSrc = button.getAttribute('data-bs-src');
		var frameName  = button.getAttribute('data-bs-frame');

		// Update the modal's content.
		var modalTitle   = bwpModal.querySelector('.modal-title');
		var modalFrame   = bwpModal.querySelector('.modal-frame');

		modalTitle.textContent   = title;

		modalFrame.src    = contentSrc;
		modalFrame.name   = frameName;
		modalFrame.height = windowheight;
	});
});
