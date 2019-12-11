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
	jQuery('p#step'+(data.step-1)).removeClass('alert-info').addClass('alert-'+data.aClass);
	jQuery('p#step'+data.step).addClass('alert alert-info');
	// Do AJAX post
	post = {step : 'step'+data.step};
	doAjax(post, function(data){
		if(data.ready !== "1")
		{
			processUpdateStep(data);
		}
		else
		{
			jQuery('p#step'+(data.step-1)).removeClass('alert-info').addClass('alert alert-'+data.aClass);
			jQuery('div#loading2').css({display:'none'});
			jQuery('div#result').html(data.result);
			jQuery('div#toolbar').find('button').removeAttr('disabled');
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
jQuery('div#toolbar').find('button').attr("disabled","disabled");
var data = {step: "0"};
processUpdateStep(data);
