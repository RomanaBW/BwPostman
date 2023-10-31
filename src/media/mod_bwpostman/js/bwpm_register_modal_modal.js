//
// BwPostman Newsletter Module
//
// BwPostman Javascript set modal box for register frontend view.
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

	function setRegModal() {
		// Get the modal
		let modal = document.getElementById('bwp_reg_modal');

		// Get the button that opens the modal
		let regopen = document.getElementById("bwp_reg_open");
		let customopen = document.getElementById("bwp_reg_custom_open");

		// Get the <span> element that closes the modal
		let regclose = document.getElementsByClassName("bwp_reg_close")[0];

		// When the user clicks the button, open the modal
		regopen.onclick = function() {
			modal.style.display = "block";
		}
		if (customopen !== null) {
			customopen.onclick = function() {
				modal.style.display = "block";
			}
		}

		// When the user clicks on <span> (x), close the modal
		regclose.onclick = function() {
			modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.addEventListener('click', function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		setRegModal();
	});

})();
