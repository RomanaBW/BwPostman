//
// BwPostman Newsletter Component
//
// BwPostman Javascript for template import.
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
	function doAjax(data, successCallback) {
		var	url = starturl,
			data = data,
			type = 'POST';
		var request = new XMLHttpRequest();
		request.onreadystatechange = function()
		{
			if (this.readyState === 4) {
				if (this.status >= 200 && this.status < 300)
				{
					successCallback(parse(this.responseText));
				}
				else
				{
					var message = '<p class="bw_tablecheck_error">AJAX Error: ' + this.statusText + '<br />' + this.responseText + '</p>';
					var alert_step = document.getElementById('step' + parseInt(data.match(/\d/g)));
					if(typeof alert_step !== 'undefined' && alert_step !== null) {
						alert_step.classList.remove('alert-info');
						alert_step.classList.add('alert-error');
					}
					document.getElementById('result').innerHTML = message;
					document.getElementById('resultSet').style.backgroundColor = '#f2dede';
					document.getElementById('resultSet').style.borderColor = '#eed3d7';
					var toolbar = document.getElementById('toolbar');
					var buttags = toolbar.getElementsByTagName('button');
					for (var i = 0; i < buttags.length; i++) {
						buttags[i].removeAttribute('disabled');
					}
					var atags = toolbar.getElementsByTagName('a');
					for (var i = 0; i < atags.length; i++) {
						atags[i].removeAttribute('disabled');
					}
				}
			}
		};
		request.open(type, url, true);
		request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		request.send(data);
	}

	function parse(text){
		try {
			return JSON.parse(text);
		} catch(e){
			return text;
		}
	}

	function processUpdateStep(data) {
		var alert_step_old = document.getElementById('step' + (data.step - 1));
		if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
			alert_step_old.classList.remove('alert-info');
			alert_step_old.classList.add('alert-' + data.aClass);
		}
		document.getElementById('step' + data.step).classList.add('alert', 'alert-info');
		// Do AJAX post
		post = 'step=step' + data.step;
		doAjax(post, function (data) {
			if (data.ready !== "1") {
				var resultdiv = document.createElement('div');
				resultdiv.innerHTML = data.result;
				document.getElementById('result').appendChild(resultdiv);
				processUpdateStep(data);
			} else {
				var alert_step_old = document.getElementById('step' + (data.step - 1));
				if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
					alert_step_old.classList.remove('alert-info');
					alert_step_old.classList.add('alert', 'alert-' + data.aClass);
				}
				var resultdiv = document.createElement('div');
				resultdiv.innerHTML = data.result;
				document.getElementById('result').appendChild(resultdiv);
				var resultSet = document.getElementById('resultSet');
				if (data.aClass !== 'error') {
					resultSet.style.backgroundColor = '#dff0d8';
					resultSet.style.borderColor = '#d6e9c6';
				} else {
					resultSet.style.backgroundColor = '#f2dede';
					resultSet.style.borderColor = '#eed3d7';
				}
				var toolbar = document.getElementById('toolbar');
				var buttags = toolbar.getElementsByTagName('button');
				for (var i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				var atags = toolbar.getElementsByTagName('a');
				for (var i = 0; i < atags.length; i++) {
					atags[i].removeAttribute('disabled');
				}
			}
		});
	}

	var toolbar = document.getElementById('toolbar');
	var buttags = toolbar.getElementsByTagName('button');
	for (var i = 0; i < buttags.length; i++) {
		buttags[i].setAttribute("disabled", "disabled");
	}
	var atags = toolbar.getElementsByTagName('a');
	for (var i = 0; i < atags.length; i++) {
		atags[i].setAttribute("disabled", "disabled");
	}
	var starturl = document.getElementById('startUrl').value;
	var data = {step: "1"};
	processUpdateStep(data);
};
