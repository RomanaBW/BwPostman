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

// insert placeholder
function addEventHandler(elem, eventType, handler) {
	if (elem.addEventListener)
		elem.addEventListener (eventType, handler, false);
	else if (elem.attachEvent)
		elem.attachEvent ('on' + eventType, handler);
}

function ready(callbackFunc) {
	if (document.readyState !== 'loading') {
		// Document is already ready, call the callback directly
		callbackFunc();
	} else if (document.addEventListener) {
		// All modern browsers to register DOMContentLoaded
		document.addEventListener('DOMContentLoaded', callbackFunc);
	} else {
		// Old IE browsers
		document.attachEvent('onreadystatechange', function() {
			if (document.readyState === 'complete') {
				callbackFunc();
			}
		});
	}
}

function InsertAtCaret(myValue) {
	let ele = document.getElementsByClassName("insertatcaretactive");
	for (let i = 0; i<ele.length; i++) {
		if (document.selection) {
			//For browsers like Internet Explorer
			ele[i].focus();
			let sel = document.selection.createRange();
			sel.text = myValue;
			ele[i].focus();
		}
		else if (ele[i].selectionStart || ele[i].selectionStart === 0) {
			//For browsers like Firefox and Webkit based
			let startPos = ele[i].selectionStart;
			let endPos = ele[i].selectionEnd;
			let scrollTop = ele[i].scrollTop;
			ele[i].value = ele[i].value.substring(0, startPos) + myValue + ele[i].value.substring(endPos, ele[i].value.length);
			ele[i].focus();
			ele[i].selectionStart = startPos + myValue.length;
			ele[i].selectionEnd = startPos + myValue.length;
			ele[i].scrollTop = scrollTop;
		}
		else {
			ele[i].value += myValue;
			ele[i].focus();
		}
	}
}

ready(function() {
	// enable InsertAtCaret
	let elms = document.querySelectorAll("#jform_intro_intro_text,#jform_intro_intro_headline,#jform_tpl_html");
	for(let i = 0; i < elms.length; i++) {
		addEventHandler(elms[i], 'focus', function() {
			let actives = document.getElementsByClassName('insertatcaretactive');
			for (let z = 0; z < actives.length; z++) {
				actives[z].classList.remove('insertatcaretactive');
			}
			this.classList.add('insertatcaretactive');
		});
	}
});
