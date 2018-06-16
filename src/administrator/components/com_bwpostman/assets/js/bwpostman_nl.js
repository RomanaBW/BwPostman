/**
 * BwPostman Newsletter Component
 *
 * BwPostman Javascript for newsletter editing.
 *
 * @version %%version_number%% build %%build_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt, Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//Method to check and compare the selected content from the database and the selected content from the form
function checkSelectedContent(selected_content_new, selected_content_old, content_exists, template_id, text_template_id, template_id_old, text_template_id_old, text_confirm_content, text_confirm_template, text_confirm_text_template) {
	// Get the selected content from the database and split the string into an array but only if there is not the content ''
	var selected_content_oldArray = [];
	if (selected_content_old.value != '') {
		selected_content_oldArray = selected_content_old.value.split(",");
	}

	// Get the selected content from the form and store it into an array
	var selected_content_newArray = [];
	for (var i=0; i<selected_content_new.options.length; i++) {

		var o = selected_content_new.options[i];
		o.selected = true;
		selected_content_newArray[i] = o.value;
	}

    // Get template_id
    var template_ids = template_id;
    var length = template_ids.length;
      for (i = 0; i < length; i++)
      {
        if (template_ids[i].checked) {
          template_id = template_ids[i].value;
        }
      }

    // Get text_template_id
    var text_template_ids = text_template_id;
    length = text_template_ids.length;
      for (i = 0; i < length; i++)
      {
        if (text_template_ids[i].checked) {
          text_template_id = text_template_ids[i].value;
        }
      }

	// Check the selected content from the database and the selected content from the form only if there is already a html- or text-version of the newsletter
	if (content_exists.value == 1) {

		// Check the number of entries and compare them
		if (selected_content_newArray.length != selected_content_oldArray.length) { // The lengths of the arrays are not equal
			var confirmAddContent = confirm(text_confirm_content);
			if (confirmAddContent == true) {
				if (selected_content_new.options.length == 0) {
					// content changed but no content selected
					document.adminForm.add_content.value = -1;
				}
				else {
					document.adminForm.add_content.value = 1;
				}
				return true;
			}
			else {
				document.adminForm.add_content.value = 0;
				return false;
			}
		}
		else { // The lengths of the arrays are equal

			// Method to check if template_id changed
			if (template_id != template_id_old.value) { // The values are not equal
				var confirmTemplateId = confirm(text_confirm_template);
				if (confirmTemplateId == true) {
					if (selected_content_new.options.length == 0) {
						// template changed but no content selected
						document.adminForm.add_content.value = -1;
					}
					else {
						document.adminForm.add_content.value = 1;
					}
					return true;
				}
				else {
					document.adminForm.add_content.value = 0;
					return false;
				}
			}
			// Method to check if text_template_id changed
			if (text_template_id != text_template_id_old.value) { // The values are not equal
				var confirmTexttemplateId = confirm(text_confirm_text_template);
				if (confirmTexttemplateId == true) {
					if (selected_content_new.options.length == 0) {
						// template changed but no content selected
						document.adminForm.add_content.value = -1;
					}
					else {
						document.adminForm.add_content.value = 1;
					}
					return true;
				}
				else {
					document.adminForm.add_content.value = 0;
					return false;
				}
			}

			// Compare the entries of the arrays
			for (var j=0; j<selected_content_newArray.length; j++) {

				if (selected_content_newArray[j] != selected_content_oldArray[j]) { // The values are not equal
					confirmAddContent = confirm(text_confirm_content);
					if (confirmAddContent == true) {
						document.adminForm.add_content.value = 1;
						return true;
					}
					else {
						document.adminForm.add_content.value = 0;
						return false;
					}
				}
			}
			// The values of both arrays are equal, so we doesn't have to do anything
			if (selected_content_new.options.length == 0) {
				// content exists but no content selected
				document.adminForm.add_content.value = -1;
			}
			else {
				document.adminForm.add_content.value = 0;
			}
			return true;
		}
	}
	else { // There is no selected content and no old html- or text-version, but may be possibly new entered data in html- or text-editor, so let's save, model will check for content
		// no content selected
		document.adminForm.add_content.value = -1;
		return true;
	}
}


function checkSelectedRecipients (ml_available, ml_unavailable, ml_intern, usergroups, message) { // Method to check if some recipients are selected

	var count_selected = 0;

	for (var i=0; i<ml_available.length; i++) {
		if (ml_available[i].checked == true) {
			count_selected++;
		}
	}

	for (i=0; i<ml_unavailable.length; i++) {
		if (ml_unavailable[i].checked == true) {
			count_selected++;
		}
	}

	for (i=0; i<ml_intern.length; i++) {
		if (ml_intern[i].checked == true) {
			count_selected++;
		}
	}

	for (i=0; i<usergroups.length; i++) {
		if (usergroups[i].checked == true) {
			count_selected++;
		}
	}

	if (count_selected == 0) {
		alert (message);
		return false;
	}
	return true;
}


function addContentTag(type, field){

	var tempval=eval("document.adminForm."+field+'_'+type);
	var tempfield=eval("document.adminForm."+field);
	var text = '';

  	if(tempval.options[tempval.selectedIndex].value != 0) {
    	if(type == 'content') {
    		text = '[CONTENT id="' + tempval.options[tempval.selectedIndex].value + '"]';
    	}
    	if(type == 'bookmark') {
    		text = '[BOOKMARK id="' + tempval.options[tempval.selectedIndex].value
    					+ '" title="' + tempval.options[tempval.selectedIndex].text + '"]';
    	}

    	if(field == "html_message") {
    		tinyMCE.execCommand('mceInsertContent', false, text);
    	}
    	if(field == 'message' || field == 'pdf_message' || field == 'nl_content'  ) {
    		insertAtCursor(tempfield, text);
    	}
  	}
}

function addAttachmentTag(){
  var form = document.adminForm;
  var tempval=eval("document.adminForm.nl_attachments");
  if(form.nl_attachments.options[form.nl_attachments.selectedIndex].value !== 0) {
   var text = '[ATTACHMENT filename="' + form.nl_attachments.options[form.nl_attachments.selectedIndex].value + '"]';
    alert('text');
    insertAtCursor(form.nl_content, text);

  }
}


function insertAtCursor(Field, myValue) {
	var myField = document.getElementById(Field);

  //IE support
  if (document.selection) {
    myField.focus();
    var sel = document.selection.createRange();
    sel.text = myValue;
  }
  //MOZILLA/NETSCAPE support
  else if (myField.selectionStart || myField.selectionStart == '0') {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos)
    + myValue
    + myField.value.substring(endPos, myField.value.length);
  } else {
    myField.value += myValue;
  }
}

//insert placeholder
function buttonClick(text, editor) {
	jInsertEditorText(text, editor);
}

// insert placeholder at cursor position
jQuery(function($){
	$.fn.EnableInsertAtCaret = function() {
		$(this).on("focus", function() {
			$(".insertatcaretactive").removeClass("insertatcaretactive");
			$(this).addClass("insertatcaretactive");
		});
	};
	$("#jform_intro_text_text,#jform_intro_text_headline,#jform_text_version,#jform_html_version").EnableInsertAtCaret();
});

function InsertAtCaret(myValue) {
	 jQuery(".insertatcaretactive").each(function(i) {
		if (document.selection) {
			//For browsers like Internet Explorer
			this.focus();
			var sel = document.selection.createRange();
			sel.text = myValue;
			this.focus();
		}
		else if (this.selectionStart || this.selectionStart == '0') {
			//For browsers like Firefox and Webkit based
			var startPos = this.selectionStart;
			var endPos = this.selectionEnd;
			var scrollTop = this.scrollTop;
			this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
			this.focus();
			this.selectionStart = startPos + myValue.length;
			this.selectionEnd = startPos + myValue.length;
			this.scrollTop = scrollTop;
		}
		else {
			this.value += myValue;
			this.focus();
		}
	})
}


function deselectAll(element) { // Method to deselect all selected options

	for (var i=0; i<element.options.length; i++) {
		var o = element.options[i];
		o.selected = false;
	}
}


function checkSelectedOption (selectbox) { // Method to check if "All subscribers" is selected --> if yes, deselect all other options

	var count_selected = 0;
	for (var i=0; i<selectbox.options.length; i++) {
		var o = selectbox.options[i];
		if (o.selected == true) {
			count_selected++;
		}
	}

	if ((selectbox.value == -1) && (count_selected > 1)) {
		alert ("<?php echo JText::_('BWP_NL_ALL_SELECTED' , true); ?>");
		for (i=0; i<selectbox.options.length; i++) {
			o = selectbox.options[i];
			if (o.value != -1) {
				o.selected = false;
			}
		}
	}
}


//-------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-------------------------------------------------------------------
function moveSelectedOptions(from,to) { // Moves elements from one select box to another one
	// Move them over
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
		  to.options[to.options.length] = new Option(o.text, o.value, false, false);
		}
	}

	// Delete them from original
	for (i=(from.options.length-1); i>=0; i--) {
		o = from.options[i];
		if (o.selected) {
		  from.options[i] = null;
		}
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}
