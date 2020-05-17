//
// BwPostman Newsletter Component
//
// BwPostman Javascript for newsletter sending process.
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

jQuery(document).ready(function() {
	function doAjax(data, successCallback) {
		var structure =
			{
				url: starturl,
				data: data,
				type: 'POST',
				dataType: 'json'
			};

		jQuery.ajax(structure)
			.done(function( data ) {
					// Call the callback function
					successCallback(data);
				})
			.fail(function(req) {
					var message = '<p class="bw_tablecheck_error">AJAX Error: ' + req.statusText + '<br />' + req.responseText + '</p>';
					jQuery('div#load').css({display: 'none'});
					jQuery('div#error').attr('class', 'alert alert-error');
					jQuery('div#result').prepend(message);
					jQuery('div#toolbar').find('button').removeAttr('disabled');
					jQuery('div#toolbar').find('a').removeAttr('disabled');
			});
	}

	function processUpdateStep(data) {
		var timeout = document.getElementById('delay').value;
		// Do AJAX post
		post = {mailsDone: data.mailsDone};
		doAjax(post, function (data) {
			if (data.ready !== "1") {
				setStatusDivs(data);
				jQuery('div.alert').removeClass('hidden');
				jQuery('div.alert-secondary').addClass('hidden');
				if (data.delay_msg === "success") {
					setTimeout(function() {
						jQuery('div#sending').attr('class', 'alert alert-success');
						jQuery('div#delay_msg').addClass('hidden');
						processUpdateStep(data);
					}, timeout);
				} else {
					processUpdateStep(data);
				}
			} else {
				setStatusDivs(data);
				jQuery('div.progress').removeClass('active');
				jQuery('div.alert').removeClass('hidden');
				jQuery('div.alert-secondary').addClass('hidden');
				jQuery('div#loading2').css({display: 'none'});
				jQuery('div#toolbar').find('button').removeAttr('disabled');
				jQuery('div#toolbar').find('a').removeAttr('disabled');
			}
		});
		function setStatusDivs(data) {
			jQuery('div#nl_bar').text(data.percent+'%');
			jQuery('div#nl_bar').css('width', data.percent+'%');
			jQuery('div#nl_bar').attr('aria-valuenow', data.percent);
			jQuery('div#nl_to_send_message').html(data.nl2sendmsg);
			jQuery('div#result').prepend(data.result);
			jQuery('div#sending').attr('class', 'alert alert-'+data.sending);
			jQuery('div#delay_msg').attr('class', 'alert alert-'+data.delay_msg);
			jQuery('div#complete').attr('class', 'alert alert-'+data.complete);
			jQuery('div#published').attr('class', 'alert alert-'+data.published);
			jQuery('div#nopublished').attr('class', 'alert alert-'+data.nopublished);
			jQuery('div#error').attr('class', 'alert alert-'+data.error);
		}
	}

	jQuery('div.alert-secondary').addClass('hidden');
	jQuery('div#toolbar').find('button').attr("disabled", "disabled");
	jQuery('div#toolbar').find('a').attr("disabled", "disabled");
	var starturl = document.getElementById('startUrl').value;
	var data = {};
	processUpdateStep(data);
});


