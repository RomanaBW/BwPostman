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

var $j = jQuery.noConflict();

function extCheck() {
	// get the file name, possibly with path (depends on browser)
	var filename = $j("#importfile").val();
	var format = $j("input[name='fileformat']:checked").val();


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

	var errorTextFileFormat = document.getElementById('importAlertFileFormat');
	switch (extension) {
		case 'xml':
			if (format === 'xml') {
				$j(".button").show();
			} else {
				alert(errorTextFileFormat);
				$j("#importfile").val('');
			}
			break;
		case 'csv':
			if (format === 'csv') {
				$j(".button").show();
				$j(".delimiter").show();
				$j(".enclosure").show();
				$j(".caption").show();
			} else {
				alert(errorTextFileFormat);
				$j("#importfile").val('');
			}
			break;
		default:
			alert(errorTextFileFormat);
			$j("#importfile").val('');
			break;
	}
}

$j(document).ready(function () {
	var format = $j("input[name='fileformat']:checked").val();

	$j(".delimiter").hide();
	$j(".enclosure").hide();
	$j(".caption").hide();
	$j(".button").hide();

	if (typeof (format) == 'undefined') {
		$j(".importfile").hide();
	} else {
		$j(".importfile").show();
		if ($j("#importfile").val() !== '') {
			extCheck();
		}
	}
});

$j("input[name='fileformat']").on("change", function () {
	$j(".importfile").show();
	$j(".delimiter").hide();
	$j(".enclosure").hide();
	$j(".caption").hide();
	$j(".button").hide();
	$j("#importfile").val('');
});

$j("#importfile").on("change", function () {
	if ($j("#importfile").val() !== '') {
		extCheck();
	}
});

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
	for(i = 0; i < element.options.length; i++)
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
	for(i = (element.options.length - 1); i >= 0; i--)
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
		alert (document.getElementById('importAlertFields'));
		return 0;
	}
	return 1;
}
