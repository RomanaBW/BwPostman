//
// BwPostman Newsletter Component
//
// BwPostman Javascript for subscribers lists.
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

Joomla = window.Joomla || {};

function changeTab(tab)
{
	if (tab !== document.getElementById('currentTab').value)
	{
		document.adminForm.tab.setAttribute('value',tab);
	}
}

window.onload = function() {
	let Joomla = window.Joomla || {};

	window.OnlyFiltered = function (onlyFiltered) // Get the selected value from modal box
	{
		if (onlyFiltered === '1') {
			document.getElementById('mlToExport').value = document.getElementById('exportMl').value;
		}

		Joomla.submitbutton('subscribers.exportSubscribers', document.adminForm);
	};

	Joomla.submitbutton = function (pressbutton) {
		if (pressbutton === 'subscriber.archive') {
			let ConfirmArchive = confirm(document.getElementById('archiveText').value);
			if (ConfirmArchive === true) {
				Joomla.submitform(pressbutton, document.adminForm);
			}
		} else {
			Joomla.submitform(pressbutton, document.adminForm);
		}
	};
};

