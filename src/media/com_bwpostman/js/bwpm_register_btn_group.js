//
// BwPostman Newsletter Component
//
// BwPostman Javascript to customize the layout of btn-groups for register frontend view.
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
(function() {

	function bwpBtnGroup() {
		const form = document.getElementById("bwp_com_form");
		const matches = form.querySelectorAll(".radio.btn-group label");
		for (let i = 0; i < matches.length; ++i) {
			matches[i].classList.add('btn');
		}
		const activlabels = form.querySelectorAll(".btn-group label:not(.active)");
		for (const activlabel of activlabels) {
			activlabel.addEventListener('click', function(event)
			{
				const label = this;
				const input = document.getElementById(label.getAttribute('for'));

				if (input.checked === false)
				{
					const matches = form.querySelectorAll('.radio.btn-group label');
					for (let i = 0; i < matches.length; ++i) {
						matches[i].classList.remove('active');
					}
					if (input.value === '')
					{
						label.classList.add('active');
					}
					else if (input.value === 0)
					{
						label.classList.add('active');
					}
					else
					{
						label.classList.add('active');
					}
					input.checked = true;
				}
			})
		}
		const elems = form.querySelectorAll(".btn-group input[checked=checked]");
		Array.prototype.forEach.call(elems, function(el, i)
		{
			if (el.value === '')
			{
				form.querySelector("label[for=" + el.getAttribute('id') + "]").classList.add("active");
			}
			else if (el.value === 0)
			{
				form.querySelector("label[for=" + el.getAttribute('id') + "]").classList.add("active");
			}
			else
			{
				form.querySelector("label[for=" + el.getAttribute('id') + "]").classList.add("active");
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		bwpBtnGroup();
	});

})();
