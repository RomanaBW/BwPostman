//
// BwPostman Newsletter Component
//
// BwPostman Javascript to customize the layout of menu params for newsletters frontend view.
//
// @version %%version_number%%
// @package BwPostman Site
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
	let counter = 0;
	let args = arguments;

	return this.replace(/%s/g, function() {
		return args[counter++];
	});
};

function updateModal(errStr) {
	jQuery('#registerErrors .modal-body').html(errStr);
	jQuery('#registerErrors').modal('show');
}
jQuery("#registerErrors").on('hidden.bs.modal', function () {
	jQuery('.modal-body').empty();
});

function checkModRegisterForm()
{

	let form = document.bwp_mod_form;
	let errStr = "";
	let arrCB = document.getElementsByName("mailinglists[]");
	let n =	arrCB.length;
	let check = 0;

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
			let special_label = document.getElementById("special_label").value;
			if (special_label !== '')
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
	let email = document.getElementById("a_email").value;

	if (email === "")
	{
		errStr += Joomla.Text._('MOD_BWPOSTMANERROR_EMAIL', true)+"<br />";
	}
	else
	{
		let filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,14})+$/;
		if (!filter.test(email))
		{
			errStr += Joomla.Text._('MOD_BWPOSTMANERROR_EMAIL_INVALID', true)+"<br />";
			email.focus;
		}
	}
	// mailinglist

	if (n > 1)
	{
		for (let i = 0; i < n; i++)
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
