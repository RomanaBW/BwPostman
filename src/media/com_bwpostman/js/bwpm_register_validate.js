//
// BwPostman Newsletter Component
//
// BwPostman Javascript validate form of register and edit frontend view.
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

	function submitbutton(pressbutton)
	{
		const form = document.bwp_com_form;
		let fault = false;

		form.edit.value = pressbutton;

		// Validate input fields only, if unsubscribe is not selected
		if (form.unsubscribe.checked === false)
		{
			if (document.bwp_com_form.name)
			{
				if (form.name_field_obligation.value === '1')
				{
					if (form.name.value === "")
					{
						alert(Joomla.Text._('COM_BWPOSTMAN_ERROR_NAME'));
						fault = true;
					}
				}
			}

			if (document.bwp_com_form.firstname)
			{
				if (form.firstname_field_obligation.value === '1')
				{
					if (form.firstname.value === "")
					{
						alert(Joomla.Text._('COM_BWPOSTMAN_ERROR_FIRSTNAME'));
						fault = true;
					}
				}
			}
			if (document.bwp_com_form.special)
			{
				if (form.special_field_obligation.value === '1')
				{
					if (form.special.value === "")
					{
						alert(Joomla.Text._("COM_BWPOSTMAN_SUB_ERROR_SPECIAL").replace("%s", form.special_label.value));
						fault = true;
					}
				}
			}
			if (form.email.value === "")
			{
				alert(Joomla.Text._('COM_BWPOSTMAN_ERROR_EMAIL'));
				fault	= true;
			}
			if (checkNlBoxes() === false)
			{
				alert (Joomla.Text._('COM_BWPOSTMAN_ERROR_NL_CHECK'));
				fault	= true;
			}
		}
		if (fault === false)
		{
			form.submit();
		}
		function checkNlBoxes()
		{
			const arrCB = form.elements['mailinglists[]'];
			const n = arrCB.length;
			let check = 0;
			let i = 0;
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
				return false;
			}
		}
	}

