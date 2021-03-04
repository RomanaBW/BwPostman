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
	var	url = document.getElementById('startUrl').value,
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
				var message = document.createElement('div');
				message.innerHTML = '<p class="bw_tablecheck_error">AJAX Error: ' + this.statusText + '<br />' + this.responseText + '</p>';
				document.getElementById('loading2').style.display = "none";
				var alert_step = document.getElementById('step' + parseInt(data.match(/\d/g)));
				if(typeof alert_step !== 'undefined' && alert_step !== null) {
					alert_step.classList.remove('alert-info');
					alert_step.classList.add('alert-error');
				}
				var resultdiv = document.getElementById('result');
				resultdiv.insertBefore(message, resultdiv.firstChild);
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
