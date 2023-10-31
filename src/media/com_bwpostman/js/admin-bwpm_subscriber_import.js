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

function getRadioChecked(element) {
	let radios = document.getElementsByName(element);

	for (let i = 0, length = radios.length; i < length; i++) {
		if (radios[i].checked) {
			// do whatever you want with the checked radio
			// only one radio can be logically checked, don't check the rest
			return radios[i].value;
		}
	}
}

function extCheck() {
	// get the file name, possibly with path (depends on browser)
	let filename = document.getElementById("importfile").value;
	let format = getRadioChecked('fileformat');

	// Use a regular expression to trim everything before final dot
	let extension = filename.replace(/^.*\./, '');
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

	let errorTextFileFormat = document.getElementById('importAlertFileFormat').value;

	switch (extension) {
		case 'xml':
			if (format === 'xml') {
				document.getElementById("further").parentNode.parentNode.style.display = "flex";
			} else {
				alert(errorTextFileFormat);
				document.getElementById("importfile").value = '';
				document.getElementById("further").parentNode.parentNode.style.display = "none";
			}
			break;
		case 'csv':
			if (format === 'csv') {
				document.getElementById("further").parentNode.parentNode.style.display = "flex";
				document.getElementById("delimiter").parentNode.parentNode.style.display = "flex";
				document.getElementById("enclosure").parentNode.parentNode.style.display = "flex";
				document.getElementById("caption").parentNode.parentNode.parentNode.style.display = "flex";
			} else {
				alert(errorTextFileFormat);
				document.getElementById("importfile").value = '';
				document.getElementById("further").parentNode.parentNode.style.display = "none";
			}
			break;
		default:
			alert(errorTextFileFormat);
			document.getElementById("importfile").value = '';
			document.getElementById("further").parentNode.parentNode.style.display = "none";
			break;
	}
}

function getExtensionOfFilename(filename) {
	// Use a regular expression to trim everything before final dot
	let extension = filename.replace(/^.*\./, '');
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

	return extension;
}

function switchCsvFieldsVisibility(visibility) {
	document.getElementById("delimiter").parentNode.parentNode.style.display = visibility;
	document.getElementById("enclosure").parentNode.parentNode.style.display = visibility;
	document.getElementById("caption").parentNode.parentNode.parentNode.style.display = visibility;
}




window.onload = function() {
	function extCheck() {
		// get the file name, possibly with path (depends on browser)
		let filename = document.getElementById("importfile").value;
		let format = getRadioChecked('fileformat');

		let extension = getExtensionOfFilename(filename);

		let errorTextFileFormat = document.getElementById('importAlertFileFormat').value;

		switch (extension) {
			case 'xml':
				if (format === 'xml') {
					document.getElementById("further").parentNode.parentNode.style.display = "flex";
				} else {
					alert(errorTextFileFormat);
					document.getElementById("importfile").value = '';
					document.getElementById("further").parentNode.parentNode.style.display = "none";
				}
				break;
			case 'csv':
				if (format === 'csv') {
					document.getElementById("further").parentNode.parentNode.style.display = "flex";
					document.getElementById("delimiter").parentNode.parentNode.style.display = "flex";
					document.getElementById("enclosure").parentNode.parentNode.style.display = "flex";
					document.getElementById("caption").parentNode.parentNode.parentNode.style.display = "flex";
				} else {
					alert(errorTextFileFormat);
					document.getElementById("importfile").value = '';
					document.getElementById("further").parentNode.parentNode.style.display = "none";
				}
				break;
			default:
				alert(errorTextFileFormat);
				document.getElementById("importfile").value = '';
				document.getElementById("further").parentNode.parentNode.style.display = "none";
				break;
		}
	}

	let fileformat = document.getElementsByName('fileformat');
	let i = 0;
	let len = fileformat.length

	for(i = 0, len; i < len; i++) {
		fileformat[i].onclick = function () {
			let format = getRadioChecked('fileformat');

			let importfile = document.getElementById("importfile");
			let extension = getExtensionOfFilename(importfile.value);

			importfile.parentNode.parentNode.style.display = "flex";


			if (importfile.value !== '') {
				importfile.value = '';
				switchCsvFieldsVisibility('none');
				document.getElementById("further").parentNode.parentNode.style.display = "none";

				if (format === 'csv') {
					switchCsvFieldsVisibility('flex');
					document.getElementById("further").parentNode.parentNode.style.display = "flex";
				}

				if (format === 'xml') {
					switchCsvFieldsVisibility('none');
					document.getElementById("further").parentNode.parentNode.style.display = "flex";
				}
			}

			importfile.onchange = () => {
				if (importfile.value !== '') {
					extCheck();
				}
				else {
					document.getElementById("further").parentNode.parentNode.style.display = "none";
				}
			};
		}
	}
};

document.addEventListener("DOMContentLoaded", function() {
	let formatExists = document.body.contains(document.getElementsByName('fileformat')[0]);

	document.addEventListener('readystatechange', (event) => {

		if (formatExists) {
			let format = getRadioChecked('fileformat');
			let importfile = document.getElementById("importfile");

			switchCsvFieldsVisibility('none');
			document.getElementById("further").parentNode.parentNode.style.display = "none";

			if (typeof (format) === 'undefined') {
				importfile.parentNode.parentNode.style.display = "none";
			} else {
				importfile.parentNode.parentNode.style.display = "flex";

				if (importfile.value !== '') {
					extCheck();
				}
			}
		}
	});

	if (formatExists) {
		document.getElementById("importfile").addEventListener('change', extCheck);
	}
});


//-----------------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-----------------------------------------------------------------------------
function selectAllOptions(obj)
{
	for (let i=0; i<obj.options.length; i++)
	{
		obj.options[i].selected = true;
	}
}


//-----------------------------------------------------------------------------
//referring to: http://www.plus2net.com/javascript_tutorial/list-remove.php
//-----------------------------------------------------------------------------
function removeOptions(selectbox) // Method to get all items in the selectbox when submitting
{
	let i;

	for(i=selectbox.options.length-1;i>=0;i--)
	{
		if(selectbox.options[i].selected){
			if (selectbox.options[i].text === 'email')
			{
				alert (document.getElementById('importAlertEmail').value);
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
	for(let i = 0; i < element.options.length; i++)
	{
		if(element.options[i].selected === true)
		{
			if(i !== 0)
			{
				let temp = new Option(element.options[i-1].text,element.options[i-1].value);
				element.options[i-1] = new Option(element.options[i].text, element.options[i].value);
				element.options[i-1].selected = true;
				element.options[i] = temp;
			}
		}
	}
}

function moveDown(element) // Method to move an item down
{
	for(let i = (element.options.length - 1); i >= 0; i--)
	{
		if(element.options[i].selected === true)
		{
			if(i !== (element.options.length - 1))
			{
				let temp = new Option(element.options[i+1].text,element.options[i+1].value);
				element.options[i+1] = new Option(element.options[i].text, element.options[i].value);
				element.options[i+1].selected = true;
				element.options[i] = temp;
			}
		}
	}
}

function check() // Method to check if the user tries to delete the email item and if the numbers of items in both selected boxes are similar
{
	let count_db_fields = document.getElementById('db_fields').length;

	let count_import_fields = document.getElementById('import_fields').length;

	if (count_db_fields !== count_import_fields) {
		alert (document.getElementById('importAlertFields').value);
		return 0;
	}
	return 1;
}
