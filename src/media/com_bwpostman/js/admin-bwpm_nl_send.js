/**
 * BwPostman Newsletter Component
 *
 * BwPostman Javascript for newsletter sending process.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt, Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
				let message = document.createElement('div');
				message.innerHTML = '<p class="text-danger">AJAX Error: ' + xhr.statusText + '<br />' + xhr.responseText + '</p>';
				document.getElementById('load').style.display = "none";
				document.getElementById('error').setAttribute('class', 'alert alert-danger');
				let resultdiv = document.getElementById('result');
				resultdiv.insertBefore(message, resultdiv.firstChild);
				let toolbar = document.getElementById('toolbar');
				let buttags = toolbar.getElementsByTagName('button');
				for (let i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				let atags = toolbar.getElementsByTagName('a');
				for (let i = 0; i < atags.length; i++) {
					atags[i].removeAttribute('disabled');
				}
			}
		});
	}

	function processUpdateStep(data) {
		let timeout = document.getElementById('delay').value;
		// Do AJAX post
		post = 'mailsDone=' + data.mailsDone;
		doAjax(post, function (data) {
			let res_container = document.getElementById('sendResult');
			if (data.ready !== "1") {
				setStatusDivs(data);
				let alerts = res_container.getElementsByClassName('alert');
                for (let i = 0; i < alerts.length; i++) {
					alerts[i].classList.remove('hidden');
				}
				let alerts_sec = res_container.getElementsByClassName('alert-secondary');
				for (let i = 0; i < alerts_sec.length; i++) {
					alerts_sec[i].classList.add('hidden');
				}
				if (data.delay_msg === "success") {
					setTimeout(function() {
						document.getElementById('sending').setAttribute('class', 'alert alert-success');
						document.getElementById('delay_msg').classList.add('hidden');
						processUpdateStep(data);
					}, timeout);
				} else {
					processUpdateStep(data);
				}
			} else {
				setStatusDivs(data);
				let progress = document.getElementById('nl_bar');
				progress.classList.remove('progress-bar-striped');
				progress.classList.remove('progress-bar-animated');
				let alerts = res_container.getElementsByClassName('alert');
                for (let i = 0; i < alerts.length; i++) {
					alerts[i].classList.remove('hidden');
				}
				let alerts_sec = res_container.getElementsByClassName('alert-secondary');
				for (let i = 0; i < alerts_sec.length; i++) {
					alerts_sec[i].classList.add('hidden');
				}
				document.getElementById('load').style.display = 'none';
				let toolbar = document.getElementById('toolbar');
				let buttags = toolbar.getElementsByTagName('button');
				for (let i = 0; i < buttags.length; i++) {
					buttags[i].removeAttribute('disabled');
				}
				let atags = toolbar.getElementsByTagName('a');
				for (let i = 0; i < atags.length; i++) {
					atags[i].removeAttribute('disabled');
				}
			}
		});
		function setStatusDivs(data) {
			let nl_bar = document.getElementById('nl_bar');
			nl_bar.textContent = data.percent+'%';
			nl_bar.style.width = data.percent+'%';
			nl_bar.setAttribute('aria-valuenow', data.percent);
			document.getElementById('nl_to_send_message').innerHTML = data.nl2sendmsg;
			let result = document.createElement('div');
			result.innerHTML = data.result;
			var resultdiv = document.getElementById('result');
			resultdiv.insertBefore(result, resultdiv.firstChild);
			document.getElementById('sending').setAttribute('class', 'alert alert-'+data.sending);
			document.getElementById('delay_msg').setAttribute('class', 'alert alert-'+data.delay_msg);
			document.getElementById('complete').setAttribute('class', 'alert alert-'+data.complete);
			document.getElementById('published').setAttribute('class', 'alert alert-'+data.published);
			document.getElementById('nopublished').setAttribute('class', 'alert alert-'+data.noPublished);
			document.getElementById('error').setAttribute('class', 'alert alert-'+data.error);
		}
	}

	var res_container = document.getElementById('sendResult');
	var alerts_sec = res_container.getElementsByClassName('alert-secondary');
	for (var i = 0; i < alerts_sec.length; i++) {
		alerts_sec[i].classList.add('hidden');
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
	var data = {mailsDone: "0"};
	processUpdateStep(data);
});
