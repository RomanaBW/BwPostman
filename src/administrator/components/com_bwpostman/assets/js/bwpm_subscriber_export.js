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
	for (var i=0; i<obj.options.length; i++)
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
	for(i = 0; i < element.options.length; i++)
	{
		if(element.options[i].selected === true)
		{
			if(i !== 0)
			{
				var temp    = new Option(element.options[i-1].text,element.options[i-1].value);
				var temp2   = new Option(element.options[i].text,element.options[i].value);
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
				var temp    = new Option(element.options[i+1].text,element.options[i+1].value);
				var temp2   = new Option(element.options[i].text,element.options[i].value);
				element.options[i+1] = temp2;
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

var $j = jQuery.noConflict();

function extCheck() {
	var format = $j("input[name='fileformat']:checked").val();

	switch (format) {
		case 'xml':
			$j(".exportgroups").show();
			$j(".exportfields").show();
			break;
		case 'csv':
			$j(".exportgroups").show();
			$j(".exportfields").show();
			$j(".delimiter").show();
			$j(".enclosure").show();
			$j(".caption").show();
			break;
	}
}

$j(document).ready(function () {
	$j(".delimiter").hide();
	$j(".enclosure").hide();
	$j(".caption").hide();
	$j(".exportgroups").hide();
	$j(".exportfields").hide();
	$j(".button").hide();
});

$j("input[name='fileformat']").on("change", function () {
	$j(".delimiter").hide();
	$j(".enclosure").hide();
	extCheck();
});

$j(".state input[type='checkbox']").on("change", function () {
	if ($j(".archive input:checked").length) {
		$j(".button").show();
	}
	if ($j(".state input:checked").length === 0) {
		$j(".button").hide();
	}
});

$j(".archive input[type='checkbox']").on("change", function () {
	if ($j(".state input:checked").length) {
		$j(".button").show();
	}
	if ($j(".archive input:checked").length === 0) {
		$j(".button").hide();
	}
});
