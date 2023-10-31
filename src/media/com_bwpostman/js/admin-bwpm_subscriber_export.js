//
// BwPostman Newsletter Component
//
// BwPostman Javascript for subscriber export.
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

//-----------------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-----------------------------------------------------------------------------
function selectAllOptions(obj)
{
	for (var i = 0; i < obj.options.length; i++)
	{
		obj.options[i].selected = true;
	}
}


//-----------------------------------------------------------------------------
// Like: http://www.plus2net.com/javascript_tutorial/list-remove.php
//-----------------------------------------------------------------------------
function removeOptions(selectbox)
{
	var i;

	for(i=selectbox.options.length-1;i>=0;i--)
	{
		if(selectbox.options[i].selected)
		{
			selectbox.remove(i);
		}
	}
}

//-----------------------------------------------------------------------------
//http://javascript.internet.com/forms/select-box-with-options.html
//-----------------------------------------------------------------------------
function moveUp(element) // Method to move an item up
{
	for(var i = 0; i < element.options.length; i++)
	{
		if(element.options[i].selected === true)
		{
			if(i !== 0)
			{
				var temp    = new Option(element.options[i-1].text,element.options[i-1].value);
				element.options[i-1] = new Option(element.options[i].text, element.options[i].value);
				element.options[i-1].selected = true;
				element.options[i] = temp;
			}
		}
	}
}

function moveDown(element) // Method to move an item down
{
	for(var i = (element.options.length - 1); i >= 0; i--)
	{
		if(element.options[i].selected === true)
		{
			if(i !== (element.options.length - 1))
			{
				var temp    = new Option(element.options[i+1].text,element.options[i+1].value);
				element.options[i+1] = new Option(element.options[i].text, element.options[i].value);
				element.options[i+1].selected = true;
				element.options[i] = temp;
			}
		}
	}
}

function check() // Method to check if the user didn't delete all items in the select box
{
	var count_export_fields = document.getElementById('export_fields').length;

	if (count_export_fields <= 0)
	{
		alert (document.getElementById('exportAlertText').value);
		return 0;
	}
	return 1;
}

function addEventHandler(elem, eventType, handler) {
	if (elem.addEventListener)
		elem.addEventListener (eventType, handler, false);
	else if (elem.attachEvent)
		elem.attachEvent ('on' + eventType, handler);
}

window.onload = function() {

	function extCheck() {
		var format = document.querySelector('input[name="fileformat"]:checked').value

		switch (format) {
			case 'xml':
				document.getElementById('exportgroups_tr').style.display = '';
				document.getElementById('exportfields_tr').style.display = '';
				break;
			case 'csv':
				document.getElementById('exportgroups_tr').style.display = '';
				document.getElementById('exportfields_tr').style.display = '';
				document.getElementById('delimiter_tr').style.display = '';
				document.getElementById('enclosure_tr').style.display = '';
				break;
		}
	}

	function ready(callbackFunc) {
		if (document.readyState !== 'loading') {
			// Document is already ready, call the callback directly
			callbackFunc();
		} else if (document.addEventListener) {
			// All modern browsers to register DOMContentLoaded
			document.addEventListener('DOMContentLoaded', callbackFunc);
		} else {
			// Old IE browsers
			document.attachEvent('onreadystatechange', function() {
				if (document.readyState === 'complete') {
					callbackFunc();
				}
			});
		}
	}

	ready(function() {
		document.getElementById('delimiter_tr').style.display = 'none';
		document.getElementById('enclosure_tr').style.display = 'none';
		document.getElementById('exportgroups_tr').style.display = 'none';
		document.getElementById('exportfields_tr').style.display = 'none';
		document.getElementById('button_tr').style.display = 'none';
	});

	var fileformat = document.querySelectorAll("input[name=fileformat]");
	for (var i = 0; i < fileformat.length; i++) {
		addEventHandler(fileformat[i], 'change', function() {
			document.getElementById('delimiter_tr').style.display = 'none';
			document.getElementById('enclosure_tr').style.display = 'none';
			extCheck();
		});
	}

	var states = document.querySelectorAll(".state input[type='checkbox']");
	for (var i = 0; i < states.length; i++) {
		addEventHandler(states[i], 'change', function() {
			if (document.querySelectorAll(".archive input:checked").length) {
				document.getElementById('button_tr').style.display = '';
			}
			if (document.querySelectorAll(".state input:checked").length === 0) {
				document.getElementById('button_tr').style.display = 'none';
			}
		});
	}

	var archives = document.querySelectorAll(".archive input[type='checkbox']");
	for (var i = 0; i < archives.length; i++) {
		addEventHandler(archives[i], 'change', function() {
			if (document.querySelectorAll(".state input:checked").length) {
				document.getElementById('button_tr').style.display = '';
			}
			if (document.querySelectorAll(".archive input:checked").length === 0) {
				document.getElementById('button_tr').style.display = 'none';
			}
		});
	}

};
