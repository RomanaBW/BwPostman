/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin javascript file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENSE.txt
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

(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {

		let toggle_fields   = '.bwpm-u2s-fields-toggle';

		let U2S_toggleRequired = function(field_identifier, status)
		{

			let label = document.getElementById(field_identifier + '-lbl');

			let label_span = document.querySelectorAll('#' + field_identifier + '-lbl span');

			if (label_span.length === 0)
			{
				let span = document.createElement("span");
				label.appendChild(span);
				label_span =  document.querySelectorAll('#' + field_identifier + '-lbl span');
			}

			if (status === 1)
			{
				document.getElementById(field_identifier).setAttribute('required', 'required');
				document.getElementById(field_identifier).classList.add('required');
				label_span[0].classList.add('star');
				label_span[0].classList.remove('optional');
				label_span[0].innerHTML = ' *';
			}
			if (status === 0)
			{
				document.getElementById(field_identifier).removeAttribute('required');
				document.getElementById(field_identifier).classList.remove('required');
				label_span[0].classList.add('optional');
				label_span[0].classList.remove('star');
				label_span[0].innerHTML = ' (optional)';
			}
		}

		// Hide plugin fields while subscription is not selected
		if (document.getElementById('jform_bwpm_user2subscriber_bwpm_user2subscriber0').checked === true) {
			document.querySelectorAll(toggle_fields).forEach(function(el) {
				el.closest('.control-group').style.display = 'none';
			});
		}
		else
		{
			ShowHide('show');
		}

		// switch fields on (subscription yes) and off (subscription no)
		document.getElementById('jform_bwpm_user2subscriber_bwpm_user2subscriber1').addEventListener('click', function ()
		{
			ShowHide('show');
		});

		document.getElementById('jform_bwpm_user2subscriber_bwpm_user2subscriber0').addEventListener('click', function()
		{
			ShowHide('hide');
		});

		function ShowHide(ShowOrHide) {
			let field_required    = [
				'jform_bwpm_user2subscriber_firstname_required',
				'jform_bwpm_user2subscriber_name_required',
				'jform_bwpm_user2subscriber_additional_required',
				'jform_bwpm_user2subscriber_mailinglists_required',
				'jform_bwpm_user2subscriber_bwpdisclaimer_required'
			];
			let field_identifier    = [
				'jform_bwpm_user2subscriber_firstname',
				'jform_bwpm_user2subscriber_bwpm_name',
				'jform_bwpm_user2subscriber_special',
				'jform_bwpm_user2subscriber_mailinglists',
				'jform_bwpm_user2subscriber_bwpdisclaimer'
			];
			let len = field_required.length;
			let setRequired = 1;
			if (ShowOrHide === 'show')
			{
				document.querySelectorAll(toggle_fields).forEach(function(el) {
				   el.closest('.control-group').style.display = 'block';
				});

				let show_format = document.getElementById('jform_bwpm_user2subscriber_emailformat_show').value;

				if (show_format === '0')
				{
					document.getElementById('jform_bwpm_user2subscriber_emailformat0').closest('.control-group').style.display = 'none';
				}

				setRequired = 1;
			}
			else
			{
				document.querySelectorAll(toggle_fields).forEach(function(el) {
				   el.closest('.control-group').style.display = 'none';
				});
				setRequired = 0;
			}

			for(let i = 0; i < len; i++) {
				if (document.getElementById(field_required[i]).value === '1') {
					U2S_toggleRequired(field_identifier[i], setRequired);
				}
			}
		}

		function setPlgModal() {

			// Set the modal height and width 90%
			let viewportwidth = 0;
			let viewportheight = 0;
			if (typeof window.innerWidth != 'undefined')
			{
				viewportwidth = window.innerWidth,
					viewportheight = window.innerHeight
			}
			else if (typeof document.documentElement != 'undefined'
				&& typeof document.documentElement.clientWidth !=
				'undefined' && document.documentElement.clientWidth !== 0)
			{
				viewportwidth = document.documentElement.clientWidth,
					viewportheight = document.documentElement.clientHeight
			}
			else
			{
				viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
					viewportheight = document.getElementsByTagName('body')[0].clientHeight
			}
			let modalcontent = document.getElementById('bwp_plg_modal-content');

			if (modalcontent !== null) {
				modalcontent.style.height = viewportheight - (viewportheight * 0.10) + 'px';
				modalcontent.style.width = viewportwidth - (viewportwidth * 0.10) + 'px';

				// Get the modal
				let modal = document.getElementById('bwp_plg_Modal');

				// Get the Iframe-Wrapper and set Iframe
				let wrapper = document.getElementById('bwp_plg_wrapper');
				let html = '<iframe id="iFrame" name="iFrame" src="' + dc_src + '" frameborder="0" style="width:100%; height:100%;"></iframe>';

				// Get the button that opens the modal
				let btnopen = document.getElementById("bwp_plg_open");

				// Get the <span> element that closes the modal
				let btnclose = document.getElementsByClassName("bwp_plg_close")[0];

				// When the user clicks the button, open the modal
				btnopen.onclick = function () {
					wrapper.innerHTML = html;
					modal.style.display = "block";
				};

				// When the user clicks on <span> (x), close the modal
				btnclose.onclick = function () {
					modal.style.display = "none";
				};

				// When the user clicks anywhere outside of the modal, close it
				window.addEventListener('click', function(event) {
					if (event.target === modal) {
						modal.style.display = "none";
					}
				});
			}
		}

		// Initialize modal if disclaimer link exists
		if (typeof dc_src !== 'undefined') {
		    setPlgModal();
		}

// Only submit, if mailinglist field is required and a checkbox is selected
		let userForm = document.getElementById('member-registration');
		userForm.addEventListener("submit", function(evt) {
			if (document.getElementById('jform_bwpm_user2subscriber_mailinglists').classList.contains('required'))
			{
				evt.preventDefault();
				let checkbox = document.getElementsByName('jform[mailinglists][]');
				for(let i=0; i< checkbox.length; i++) {
					if (checkbox[i].checked)
					{
						userForm.submit();
					}
				}
				return false;
			}
		});

	});

}());
