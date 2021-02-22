//
// BwPostman Newsletter Component
//
// BwPostman Javascript for maintenance update check save tables.
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

function processUpdateStep(data)
{
	var alert_step_old = document.getElementById('step' + (data.step - 1));
	if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
		alert_step_old.classList.remove('alert-info');
		alert_step_old.classList.add('alert-' + data.aClass);
	}
	document.getElementById('step' + data.step).classList.add('alert', 'alert-info');
	// Do AJAX post
	post = 'step=step' + data.step;
	doAjax(post, function(data){
		if(data.ready !== "1")
		{
			document.getElementById('result').innerHTML = data.result;
			processUpdateStep(data);
		}
		else
		{
			var alert_step_old = document.getElementById('step' + (data.step - 1));
			if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
				alert_step_old.classList.remove('alert-info');
				alert_step_old.classList.add('alert', 'alert-' + data.aClass);
			}

			document.getElementById('loading2').style.display = 'none';
			document.getElementById('result').innerHTML = data.result;
			var toolbar = document.getElementById('toolbar');
			if (toolbar) {
				var buttags = toolbar.getElementsByTagName('button');
				for (var i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				var atags = toolbar.getElementsByTagName('a');
				for (var i = 0; i < atags.length; i++) {
					atags[i].removeAttribute('disabled');
				}
			}
			// Get the modal
			var modal = window.parent.document.getElementById('bwp_Modal');
			var btnclose = window.parent.document.getElementsByClassName('bwp_close')[0];
			btnclose.style.display = 'block';
			btnclose.onclick = function() {
				modal.style.display = 'none';
			};
			window.parent.onclick = function(event) {
				if (event.target === modal) {
					modal.style.display = 'none';
				}
			}
		}
	});
}
var toolbar = document.getElementById('toolbar');
if (toolbar) {
	var buttags = toolbar.getElementsByTagName('button');
	for (var i = 0; i < buttags.length; i++) {
		buttags[i].setAttribute("disabled", "disabled");
	}
	var atags = toolbar.getElementsByTagName('a');
	for (var i = 0; i < atags.length; i++) {
		atags[i].setAttribute("disabled", "disabled");
	}
}
var data = {step: "0"};

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

ready(function() {
	document.querySelector('body.contentpane').style.overflowX = 'hidden';
	processUpdateStep(data);
});
