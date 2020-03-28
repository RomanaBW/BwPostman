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
	var starturl = document.getElementById('startUrl').value;
	var structure =
		{
			success: function(data)
			{
				// Call the callback function
				successCallback(data);
			},
			error: function(req)
			{
				var message = '<p class="bw_tablecheck_error">AJAX Loading Error: '+req.statusText+'</p>';
				jQuery('div#loading2').css({display:'none'});
				jQuery('p#'+data.step).removeClass('alert-info').addClass('alert-error');
				jQuery('div#result').html(message);
				jQuery('div#toolbar').find('button').removeAttr('disabled');
				jQuery('div#toolbar').find('a').removeAttr('disabled');
			}
		};

	structure.url = starturl;
	structure.data = data;
	structure.type = 'POST';
	structure.dataType = 'json';
	jQuery.ajax(structure);
}
