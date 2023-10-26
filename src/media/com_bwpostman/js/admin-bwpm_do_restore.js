//
// BwPostman Newsletter Component
//
// BwPostman Javascript for maintenance do restore tables.
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
	function doAjax(data, successCallback) {
		Joomla.request({
			url: document.getElementById('startUrl').value,
			method: 'POST',
			data: data,
			perform: true,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			onSuccess: function onSuccess(response) {
				successCallback(JSON.parse(response));
			},
			onError: function onError(xhr) {
				let errorMessage = xhr.responseText;

				let errorObject;
				if (errorMessage[0] === "{") {
					errorObject = JSON.parse(errorMessage);
					errorMessage = '';
					for (const key in errorObject) {
						errorMessage = errorMessage + key + ': ' + errorObject[key] +'<br />';
					}
				} else {
					errorMessage = 'The XHR response text seems not to be a JSON:<br\>';
				}

				let message = '<p class="text-danger">AJAX Error:' + '<br />' + xhr.statusText + '<br />' + errorMessage + '</p>';
				document.getElementById('loading2').style.display = "none";
				let alert_step = document.getElementById('step' + parseInt(data.match(/\d/g)));
				if(typeof alert_step !== 'undefined' && alert_step !== null) {
					alert_step.querySelector('span.fa').classList.remove('fa-spinner');
					alert_step.classList.remove('alert-info');
					alert_step.classList.add('alert-danger');
				}
				document.getElementById('result').innerHTML = message;
				document.getElementById('resultSet').style.backgroundColor = '#f3d4d4';
				document.getElementById('resultSet').style.borderColor = '#eebfbe';
				let toolbar = document.getElementById('toolbar');
				let buttags = toolbar.getElementsByTagName('button');
				for (let i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				let atags = toolbar.getElementsByTagName('a');
				for (let j = 0; j < atags.length; j++) {
					atags[j].removeAttribute('disabled');
				}
			}
		});
	}

	function processUpdateStep(data) {
		let alert_step_old = document.getElementById('step' + (data.step - 1));
		if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
			alert_step_old.querySelector('span.fa').classList.remove('fa-spinner');
			alert_step_old.classList.remove('alert-info');
			alert_step_old.classList.add('alert-' + data.aClass);
		}

		let pstep = document.getElementById('step' + parseInt(data.step.match(/\d+/g)));

		pstep.classList.remove('alert-secondary');
		pstep.classList.add('alert-info');
		pstep.querySelector('span.fa').classList.add('fa-spinner');
		// Do AJAX post
		let post = 'step=step' + data.step;
		doAjax(post, function (data) {
			if (parseInt(data.ready) !== 1) {
				document.getElementById('result').innerHTML = data.result;
				document.getElementById('error').innerHTML = data.error;
				processUpdateStep(data);
			} else {
				let alert_step_old = document.getElementById('step' + (data.step - 1));
				if(typeof alert_step_old !== 'undefined' && alert_step_old !== null) {
					alert_step_old.querySelector('span.fa').classList.remove('fa-spinner');
					alert_step_old.classList.remove('alert-info');
					alert_step_old.classList.add('alert-' + data.aClass);
				}
				document.getElementById('loading2').style.display = 'none';
				document.getElementById('result').innerHTML = data.result;
				if (data.error !== '') {
					document.getElementById('resultSet').style.backgroundColor = '#f3d4d4';
					document.getElementById('resultSet').style.borderColor = '#eebfbe';
					let alert_step = document.getElementById(data.step);
					if(typeof alert_step !== 'undefined' && alert_step !== null) {
						alert_step.querySelector('span.fa').classList.remove('fa-spinner');
						alert_step.classList.remove('alert-info');
						alert_step.classList.add('alert-danger');
					}
				} else {
					document.getElementById('resultSet').style.backgroundColor = '#e1f5ec';
					document.getElementById('resultSet').style.borderColor = '#0f2f21';
				}
				document.getElementById('error').innerHTML = data.error;
				let toolbar = document.getElementById('toolbar');
				let buttags = toolbar.getElementsByTagName('button');
				for (let i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				let atags = toolbar.getElementsByTagName('a');
				for (let j = 0; j < atags.length; j++) {
					atags[j].removeAttribute('disabled');
				}
			}
		});
	}

	let toolbar = document.getElementById('toolbar');
	let buttags = toolbar.getElementsByTagName('button');
	for (let k = 0; k < buttags.length; k++) {
		buttags[k].setAttribute("disabled", "disabled");
	}
	let atags = toolbar.getElementsByTagName('a');
	for (let l = 0; l < atags.length; l++) {
		atags[l].setAttribute("disabled", "disabled");
	}
	let starturl = document.getElementById('startUrl').value;
	let data = {step: "1"};
	processUpdateStep(data);
});
