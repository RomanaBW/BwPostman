//
// BwPostman Newsletter Component
//
// BwPostman Javascript for subscriber import.
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

window.onload = function() {


	function addEventHandler(elem, eventType, handler) {
		if (elem.addEventListener)
			elem.addEventListener (eventType, handler, false);
		else if (elem.attachEvent)
			elem.attachEvent ('on' + eventType, handler);
	}

	function extCheck() {
		// get the file name, possibly with path (depends on browser)
		var filename = document.getElementById('importfile').value;
		var format = document.querySelector('input[name="fileformat"]:checked').value


		// Use a regular expression to trim everything before final dot
		var extension = filename.replace(/^.*\./, '');

		// If there is no dot anywhere in filename, we would have extension == filename,
		// so we account for this possibility now
		if (extension === filename) {
			extension = '';
		} else {
			// if there is an extension, we convert to lower case
			// (N.B. this conversion will not effect the value of the extension
			// on the file upload.)
			extension = extension.toLowerCase();
		}

		var errorTextFileFormat = document.getElementById('importAlertFileFormat').value;
		switch (extension) {
			case 'xml':
				if (format === 'xml') {
					document.getElementById('button_tr').style.display = '';
				} else {
					alert(errorTextFileFormat);
					document.getElementById('importfile').value = '';
				}
				break;
			case 'csv':
				if (format === 'csv') {
					document.getElementById('button_tr').style.display = '';
					document.getElementById('delimiter_tr').style.display = '';
					document.getElementById('enclosure_tr').style.display = '';
					document.getElementById('caption_tr').style.display = '';
				} else {
					alert(errorTextFileFormat);
					document.getElementById('importfile').value = '';
				}
				break;
			default:
				alert(errorTextFileFormat);
				document.getElementById('importfile').value = '';
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
		// avoid error messages, if view is import1
		var importview = document.getElementById('import');
		if (importview === null) return false;

		var fileformat = document.querySelectorAll("input[name=fileformat]");
		var format_checked = fileformat.checked;
		if (format_checked) {
			var format = format_checked.value;
		}

		function style_none() {
			document.getElementById('delimiter_tr').style.display = 'none';
			document.getElementById('enclosure_tr').style.display = 'none';
			document.getElementById('caption_tr').style.display = 'none';
			document.getElementById('button_tr').style.display = 'none';
		}
		style_none();

		if (typeof (format) == 'undefined') {
			document.getElementById('importfile_tr').style.display = 'none';
		} else {
			document.getElementById('importfile_tr').style.display = '';
			if (document.getElementById('importfile').value !== '') {
				extCheck();
			}
		}

		for (var i = 0; i < fileformat.length; i++) {
			addEventHandler(fileformat[i], 'change', function() {
				document.getElementById('importfile_tr').style.display = '';
				document.getElementById('importfile').value = '';
                style_none();
			});
		}

		var importfile = document.getElementById('importfile');
		if (importfile) {
			addEventHandler(importfile, 'change', function() {
				if (document.getElementById('importfile').value !== '') {
					extCheck();
				}
			});
		}
	});

};

//-----------------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-----------------------------------------------------------------------------
function selectAllOptions(obj)
{
	for (var i=0; i<obj.options.length; i++)
	{
		obj.options[i].selected = true;
	}
}


//-----------------------------------------------------------------------------
//referring to: http://www.plus2net.com/javascript_tutorial/list-remove.php
//-----------------------------------------------------------------------------
function removeOptions(selectbox) // Method to get all items in the selectbox when submitting
{
	var i;

	for(i=selectbox.options.length-1;i>=0;i--)
	{
		if(selectbox.options[i].selected){
			if (selectbox.options[i].text === 'email')
			{
				alert (document.getElementById('importAlertEmail'));
			} else
			{
				selectbox.remove(i);
			}
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
				var temp = new Option(element.options[i-1].text,element.options[i-1].value);
				var temp2 = new Option(element.options[i].text,element.options[i].value);
				element.options[i-1] = temp2;
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
				var temp = new Option(element.options[i+1].text,element.options[i+1].value);
				var temp2 = new Option(element.options[i].text,element.options[i].value);
				element.options[i+1] = temp2;
				element.options[i+1].selected = true;
				element.options[i] = temp;
			}
		}
	}
}

function check() // Method to check if the user tries to delete the email item and if the numbers of items in both selected boxes are similar
{
	var count_db_fields = document.getElementById('db_fields').length;

	var count_import_fields = document.getElementById('import_fields').length;

	if (count_db_fields !== count_import_fields) {
		alert (document.getElementById('importAlertFields').value);
		return 0;
	}
	return 1;
}
