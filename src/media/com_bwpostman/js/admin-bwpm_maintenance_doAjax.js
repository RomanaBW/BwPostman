//
// BwPostman Newsletter Component
//
// BwPostman Javascript for maintenance check doAjax.
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


function doAjax(data, successCallback)
{
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
			document.getElementById('loading2').style.display = "none";
			let alert_step = document.getElementById('step' + parseInt(data.match(/\d/g)));
			if(typeof alert_step !== 'undefined' && alert_step !== null) {
				alert_step.querySelector('span.fa').classList.remove('fa-spinner');
				alert_step.classList.remove('alert-info');
				alert_step.classList.add('alert-danger');
			}
			let resultdiv = document.getElementById('result');
			document.getElementById('resultSet').style.backgroundColor = '#f3d4d4';
			document.getElementById('resultSet').style.borderColor = '#eebfbe';
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
