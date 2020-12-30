//
// BwPostman Newsletter Component
//
// BwPostman Javascript to customize the layout of menu params for newsletters frontend view.
//
// @version %%version_number%%
// @package BwPostman-Site
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

String.prototype.sprintf = function() {
	var counter = 0;
	var args = arguments;

	return this.replace(/%s/g, function() {
		return args[counter++];
	});
};

function updateModal(errStr) {
	var modal = document.getElementById('bwp_mod_Modal');
	var wrapper = document.getElementById('bwp_mod_wrapper');
	var modalcontent = document.getElementById('bwp_mod_modal-content');
	modalcontent.classList.add('bwp-err');
	wrapper.innerHTML = errStr;
	modal.style.display = "block";
}

function checkModRegisterForm()
{

	var form = document.bwp_mod_form;
	var errStr = "";
	var arrCB = document.getElementsByName("mailinglists[]");
	var n =	arrCB.length;
	var check = 0;

	// Validate input fields
	// firstname
	if (document.bwp_mod_form.a_firstname)
	{
		if ((!document.getElementById("a_firstname").value) && (document.getElementById("firstname_field_obligation_mod").value === '1'))
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_FIRSTNAME', true)+"<br />";
		}
	}

	// name
	if (document.bwp_mod_form.a_name)
	{
		if ((!document.getElementById("a_name").value) && (document.getElementById("name_field_obligation_mod").value === '1'))
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_NAME', true)+"<br />";
		}
	}

	// additional field
	if (document.bwp_mod_form.a_special)
	{
		if ((!document.getElementById("a_special").value) && (document.getElementById("special_field_obligation_mod").value === '1'))
		{
			var special_label = document.getElementById("special_label").value;
			if (special_label != '')
			{
				errStr += Joomla.Text._('MOD_BWPOSTMAN_SUB_ERROR_SPECIAL', true).sprintf(special_label)+"<br />";
			}
			else
			{
				errStr += Joomla.Text._('MOD_BWPOSTMAN_SUB_ERROR_SPECIAL', true).sprintf(Joomla.Text._('MOD_BWPOSTMAN_SPECIAL'))+"<br />";
			}
		}
	}

	// email
	var email = document.getElementById("a_email").value;

	if (email === "")
	{
		errStr += Joomla.Text._('MOD_BWPOSTMANERROR_EMAIL', true)+"<br />";
	}
	else
	{
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,14})+$/;
		if (!filter.test(email))
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_EMAIL_INVALID', true)+"<br />";
			email.focus;
		}
	}
	// mailinglist

	if (n > 1)
	{
		for (i = 0; i < n; i++)
		{
			if (arrCB[i].checked === true)
			{
				check++;
			}
		}
	}
	else
	{
		check++;
	}

	if (check === 0)
	{
		errStr += Joomla.Text._('MOD_BWPOSTMANERROR_NL_CHECK')+"<br />";
	}

	// disclaimer
	if (document.bwp_mod_form.agreecheck_mod)
	{
		if (document.bwp_mod_form.agreecheck_mod.checked === false)
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_DISCLAIMER_CHECK')+"<br />";
		}
	}

	// captcha
	if (document.bwp_mod_form.stringCaptcha)
	{
		if (document.bwp_mod_form.stringCaptcha.value === '')
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_CAPTCHA_CHECK')+"<br />";
		}
	}

	// question
	if (document.bwp_mod_form.stringQuestion)
	{
		if (document.bwp_mod_form.stringQuestion.value === '')
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_CAPTCHA_CHECK')+"<br />";
		}
	}

	if ( errStr !== "" )
	{
		updateModal( errStr );
		return false;
	}
	else
	{
		form.submit();
	}
}

jQuery(document).ready(function()
{
	// Turn radios into btn-group
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery(".btn-group label:not(.active)").click(function()
	{
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked'))
		{
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if (input.val() === '')
			{
				label.addClass('active btn-primary');
			}
			else if (input.val() === 0)
			{
				label.addClass('active btn-danger');
			}
			else
			{
				label.addClass('active btn-success');
			}
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function()
	{
		if (jQuery(this).val() === '')
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		}
		else if (jQuery(this).val() === 0)
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		}
		else
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
	function setModModal() {
		// Set the modal height and width 90%
		if (typeof window.innerWidth != 'undefined')
		{
			viewportwidth = window.innerWidth,
				viewportheight = window.innerHeight
		}
		else if (typeof document.documentElement != 'undefined'
			&& typeof document.documentElement.clientWidth !=
			'undefined' && document.documentElement.clientWidth != 0)
		{
			viewportwidth = document.documentElement.clientWidth,
				viewportheight = document.documentElement.clientHeight
		}
		else
		{
			viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
				viewportheight = document.getElementsByTagName('body')[0].clientHeight
		}
		var modalcontent = document.getElementById('bwp_mod_modal-content');
		modalcontent.style.height = viewportheight-(viewportheight*0.10)+'px';
		modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';

		// Get the modal
		var modal = document.getElementById('bwp_mod_Modal');
		var modalhref = document.getElementById('bwp_mod_Modalhref').value;

		// Get the Iframe-Wrapper and set Iframe
		var wrapper = document.getElementById('bwp_mod_wrapper');
		var html = '<iframe id="iFrame" name="iFrame" src="'+modalhref+'" frameborder="0" style="width:100%; height:100%;"></iframe>';

		// Get the button that opens the modal
		var btnopen = document.getElementById("bwp_mod_open");

		// Get the <span> element that closes the modal
		var btnclose = document.getElementsByClassName("bwp_mod_close")[0];

		// When the user clicks the button, open the modal
		if (btnopen !== null) {
			btnopen.onclick = function() {
				wrapper.innerHTML = html;
				// Hack for Beez3 template
				var iframe = document.getElementById('iFrame');
				iframe.onload = function() {
					this.contentWindow.document.head.insertAdjacentHTML("beforeend", `<style>.contentpane #all{max-width:unset;}</style>`);
				}
				modal.style.display = "block";
			}
		}

		// When the user clicks on <span> (x), close the modal
		btnclose.onclick = function() {
			modal.style.display = "none";
			modalcontent.classList.remove('bwp-err');
		}

		// When the user clicks anywhere outside of the modal, close it
		window.addEventListener('click', function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
				modalcontent.classList.remove('bwp-err');
			}
		});
	}
	setModModal();
})
