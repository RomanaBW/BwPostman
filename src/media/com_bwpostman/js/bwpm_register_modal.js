//
// BwPostman Newsletter Component
//
// BwPostman Javascript set modal box for register frontend view.
//
// @version %%version_number%%
// @package BwPostman-Side
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

function setComModal() {
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
	var modalcontent = document.getElementById('bwp_com_modal-content');
	modalcontent.style.height = viewportheight-(viewportheight*0.10)+'px';
	modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';

	// Get the modal
	var commodal = document.getElementById('bwp_com_Modal');
	var commodalhref = document.getElementById('bwp_com_Modalhref').value;

	// Get the Iframe-Wrapper and set Iframe
	var wrapper = document.getElementById('bwp_com_wrapper');
	var html = '<iframe id="BwpFrame" name="BwpFrame" src="'+commodalhref+'" frameborder="0" style="width:100%; height:100%;"></iframe>';

	// Get the button that opens the modal
	var btnopen = document.getElementById("bwp_com_open");

	// Get the <span> element that closes the modal
	var btnclose = document.getElementsByClassName("bwp_com_close")[0];

	// When the user clicks the button, open the modal
	btnopen.onclick = function() {
		wrapper.innerHTML = html;
		// Hack for Beez3 template
		var iframe = document.getElementById('BwpFrame');
		iframe.onload = function() {
			this.contentWindow.document.head.insertAdjacentHTML("beforeend", `<style>.contentpane #all{max-width:unset;}</style>`)
		}
		commodal.style.display = "block";
	}

	// When the user clicks on <span> (x), close the modal
	btnclose.onclick = function() {
		commodal.style.display = "none";
	}

	// When the user clicks anywhere outside of the modal, close it
	window.addEventListener('click', function(event) {
		if (event.target == commodal) {
			commodal.style.display = "none";
		}
	});
}

window.onload = setComModal;
