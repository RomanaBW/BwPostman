/**
 * BwPostman Newsletter Component
 *
 * BwPostman Javascript for newsletter editing.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt, Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
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
function checkSelectedContent(text_confirm_content, text_confirm_template, text_confirm_text_template, no_html_template, no_text_template) {
	// Get the selected content from the database and split the string into an array but only if there is not the content ''

	var selected_content_new = document.adminForm['jform_selected_content'];
	var selected_content_old = document.getElementById('selected_content_old');
	var content_exists = document.getElementById('content_exists');
	var template_ids = document.getElementsByName('jform[template_id]');
	var text_template_ids = document.getElementsByName('jform[text_template_id]');
	var template_id_old = document.getElementById('template_id_old');
	var text_template_id_old = document.getElementById('text_template_id_old');

	function checkContent() {
		if (selected_content_new.options.length === 0) {
			// item changed but no content selected
			document.adminForm.add_content.value = -1;
		}
		else {
			document.adminForm.add_content.value = 1;
		}
	}

	var selected_content_oldArray = [];
	if (selected_content_old.value !== '') {
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
    var length = template_ids.length;
	var template_id = '';
	for (i = 0; i < length; i++) {
        if (template_ids[i].checked) {
	        template_id = template_ids[i].value;
        }
      }

	if (template_id === '')
	{
		alert(no_html_template);
		return false;
	}

	// Get text_template_id
    length = text_template_ids.length;
	var text_template_id = '';
      for (i = 0; i < length; i++)
      {
        if (text_template_ids[i].checked) {
          text_template_id = text_template_ids[i].value;
        }
      }

	if (text_template_id === '')
	{
		alert(no_text_template);
		return false;
	}

	// Check the selected content from the database and the selected content from the form only if there is already a html- or text-version of the newsletter
	if (content_exists.value === '1') {

		// Check the number of entries and compare them
		if (selected_content_newArray.length !== selected_content_oldArray.length) { // The lengths of the arrays are not equal
			var confirmAddContent = confirm(text_confirm_content);
			if (confirmAddContent === true) {
				checkContent();
				return true;
			}
			else {
				document.adminForm.add_content.value = 0;
				return false;
			}
		}
		else { // The lengths of the arrays are equal

			// Method to check if template_id changed
			if (template_id !== template_id_old.value) { // The values are not equal
				var confirmTemplateId = confirm(text_confirm_template);
				if (confirmTemplateId === true) {
					checkContent();
					return true;
				}
				else {
					document.adminForm.add_content.value = 0;
					return false;
				}
			}
			// Method to check if text_template_id changed
			if (text_template_id !== text_template_id_old.value) { // The values are not equal
				var confirmTexttemplateId = confirm(text_confirm_text_template);
				if (confirmTexttemplateId === true) {
					checkContent();
					return true;
				}
				else {
					document.adminForm.add_content.value = 0;
					return false;
				}
			}

			// Compare the entries of the arrays
			for (var j=0; j<selected_content_newArray.length; j++) {
				if (selected_content_newArray[j] !== selected_content_oldArray[j]) { // The values are not equal
					confirmAddContent = confirm(text_confirm_content);
					if (confirmAddContent === true) {
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
			if (selected_content_new.options.length === 0) {
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


function checkSelectedRecipients (message) { // Method to check if some recipients are selected

	var count_selected = 0;
	var campaign_id = document.getElementById('jform_campaign_id').value;
	var ml_available = document.getElementsByName('jform[ml_available][]');
	var ml_unavailable = document.getElementsByName('jform[ml_unavailable][]');
	var ml_intern = document.getElementsByName('jform[ml_intern][]');
	var usergroups = document.getElementsByName('jform[usergroups][]');

	for (var i=0; i<ml_available.length; i++) {
		if (ml_available[i].checked === true) {
			count_selected++;
		}
	}

	for (i=0; i<ml_unavailable.length; i++) {
		if (ml_unavailable[i].checked === true) {
			count_selected++;
		}
	}

	for (i=0; i<ml_intern.length; i++) {
		if (ml_intern[i].checked === true) {
			count_selected++;
		}
	}

	for (i=0; i<usergroups.length; i++) {
		if (usergroups[i].checked === true) {
			count_selected++;
		}
	}

	if (count_selected === 0) {
		alert (message);
		return false;
	}
	return true;
}


//insert placeholder
function buttonClick(text, editor) {
	var x = document.getElementById("jform_html_version");
	if (window.getComputedStyle(x).display === "none") {
		Joomla.editors.instances[editor].replaceSelection(text);
	}
	else
	{
        InsertAtCaret(text);
	}
}

//-------------------------------------------------------------------
//http://www.mattkruse.com/javascript/selectbox/source.html
//-------------------------------------------------------------------
function moveSelectedOptions(from,to) { // Moves elements from one select box to another one

	// Count selected content
	var selcnt = null;

	// Move them over
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
		  to.options[to.options.length] = new Option(o.text, o.value, false, false);
          selcnt = true;
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

	if (selcnt === null) {
		alert(Joomla.Text._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
	}
}

function moveArticle() {
	// Move article id from available content helper to selected content
	var select = document.getElementById('jform_selected_content');
	var fieldId = document.getElementById('jform_ac_id_id');
	var availableContent = document.getElementById('jform_available_content').options;

	var fieldTitle = document.getElementById('jform_ac_id_name');

	// J5 creates title other way
	if (typeof(fieldTitle) == 'undefined' || fieldTitle == null) {
		fieldTitle = document.getElementById('jform_ac_id');
	}

	if(fieldId.value && fieldTitle.value) {
		var contentFound = 0;

		// Search for selected article at (remaining) available list,
		for (let i = 0; i < availableContent.length; i++) {
			if (parseInt(availableContent[i].value) === parseInt(fieldId.value))
			{
				contentFound = i;
			}
		}

		// If selected article is found at available list, this article is not selected so far and can be used.
		if (contentFound > 0)
		{
			// Add to selected list
			var option = document.createElement("option");
			option.value = fieldId.value;
			option.text = availableContent[contentFound].text;
			select.appendChild(option);

			// Remove from available list
			availableContent[contentFound] = null;
		}

		// Empty input field
		document.querySelector('.js-modal-content-select-field .btn-secondary').click()
		window.processModalParent('jform_ac_id');
	}
	else
	{
		alert(Joomla.Text._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
	}
}

function sortSelectedOptions(updown) {
	// Sort selected content up / down
	var select = document.getElementById('jform_selected_content');
	var option = select.options[select.selectedIndex];

	if(option) {
		if (updown === 'up') {
			select.options.add(option, select.selectedIndex - 1);
		}
		else
		{
			var ind = select.selectedIndex + 2;
			if (ind > select.options.length) ind = 0;
			select.options.add(option, ind);
		}
	}
	else
	{
		alert(Joomla.Text._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
	}
}

function hasCampaign() {
	var selectedCampaign = document.getElementById("jform_campaign_id");
	var selectedCampaignValue = selectedCampaign.options[selectedCampaign.selectedIndex].value;
	var hasCampaign = false;
	if (selectedCampaignValue !== '-1') {
		hasCampaign = true;
	}

	return hasCampaign;
}

function changeTab(newTab, currentTab, text_confirm_content, text_confirm_template, text_confirm_text_template, no_html_template, no_text_template, checkRecipientMessage) {
	if (newTab !== currentTab) {
		if (currentTab === 'edit_basic') {
			var selectedContentOkay = checkSelectedContent(text_confirm_content, text_confirm_template, text_confirm_text_template, no_html_template, no_text_template);

			if (selectedContentOkay === false) {
				return false;
			}

			var hasCampaign = window.hasCampaign();

			if (!hasCampaign) {
				var campaignRecipientsOkay = checkSelectedRecipients(checkRecipientMessage);
			}

			if (campaignRecipientsOkay === false) {
				return false;
			}
		}
		document.adminForm.tab.setAttribute('value', newTab);
		document.adminForm.task.setAttribute('value', 'newsletter.changeTab');
	}
}

function switchRecipients() {
	var selectedCampaign = document.getElementById("jform_campaign_id");
	var selectedCampaignValue = selectedCampaign.options[selectedCampaign.selectedIndex].value;
	var recipients = document.getElementById('recipients');

	if (selectedCampaignValue !== '-1') {
		recipients.style.display = "none";
	} else {
		recipients.style.display = "block";
	}
}

function InsertAtCaret(myValue) {
	var ele = document.getElementsByClassName("insertatcaretactive");
	for (var i = 0; i<ele.length; i++) {
		if (document.selection) {
			//For browsers like Internet Explorer
			ele[i].focus();
			var sel = document.selection.createRange();
			sel.text = myValue;
			ele[i].focus();
		}
		else if (ele[i].selectionStart || ele[i].selectionStart === 0) {
			//For browsers like Firefox and Webkit based
			var startPos = ele[i].selectionStart;
			var endPos = ele[i].selectionEnd;
			var scrollTop = ele[i].scrollTop;
			ele[i].value = ele[i].value.substring(0, startPos) + myValue + ele[i].value.substring(endPos, ele[i].value.length);
			ele[i].focus();
			ele[i].selectionStart = startPos + myValue.length;
			ele[i].selectionEnd = startPos + myValue.length;
			ele[i].scrollTop = scrollTop;
		}
		else {
			ele[i].value += myValue;
			ele[i].focus();
		}
	}
}

function addEventHandler(elem, eventType, handler) {
	if (elem.addEventListener)
		elem.addEventListener (eventType, handler, false);
	else if (elem.attachEvent)
		elem.attachEvent ('on' + eventType, handler);
}

window.onload = function() {
	var Joomla = window.Joomla || {};

	if (document.getElementById('currentTab') !== null && document.getElementById('currentTab').value === 'edit_basic') {
		var selectedCampaign = document.getElementById("jform_campaign_id");
		var selectedCampaignValue = selectedCampaign.options[selectedCampaign.selectedIndex].value;
	}

	Joomla.submitbutton = function (pressbutton) {

		var form = document.adminForm;

		if (form.task.value === 'newsletter.changeTab') {
			Joomla.submitform(pressbutton, form);
		}

		if (pressbutton === 'newsletter.cancel') {
			Joomla.submitform(pressbutton, form);
			return;
		}

		if (pressbutton === 'newsletter.back') {
			form.task.value = 'back';
			Joomla.submitform(pressbutton, form);
			return;
		}

		if (pressbutton === 'newsletter.publish_save') {
			form.task.setAttribute('value', 'newsletter.publish_save');
			Joomla.submitform(pressbutton, form);
		}

		if (pressbutton === 'newsletter.publish_apply') {
			form.task.setAttribute('value', 'newsletter.publish_apply');
			Joomla.submitform(pressbutton, form);
		}

		var confirmSendNl = '';
		if (pressbutton === 'newsletter.sendmail') {
			confirmSendNl = confirm(document.getElementById('confirmSend').value);
			if (confirmSendNl === true) {
				form.task.setAttribute('value', 'newsletter.sendmail');
				Joomla.submitform(pressbutton, form);
			}
		}

		if (pressbutton === 'newsletter.sendmailandpublish') {
			confirmSendNl = confirm(document.getElementById('confirmSendPublish').value);
			if (confirmSendNl === true) {
				form.task.setAttribute('value', 'newsletter.sendmail');
				Joomla.submitform(pressbutton, form);
			}
		}

		if (pressbutton === 'newsletter.sendtestmail') {
			confirmSendNl = confirm(document.getElementById('confirmSend').value);
			if (confirmSendNl === true) {
				form.task.setAttribute('value', 'newsletter.sendmail');
				Joomla.submitform(pressbutton, form);
			}
		}

		if (pressbutton === 'newsletter.save' || pressbutton === 'newsletter.apply' || pressbutton === 'newsletter.save2new' || pressbutton === 'newsletter.save2copy')
		{
			form.task.setAttribute('value', pressbutton);

			if (document.getElementById('currentTab') !== null && document.getElementById('currentTab').value === 'edit_basic')
			{
				var Args1 = document.getElementById('checkContentArgs1').value;
				var Args2 = document.getElementById('checkContentArgs2').value;
				var Args3 = document.getElementById('checkContentArgs3').value;
				var Args4 = document.getElementById('checkContentArgs4').value;
				var Args5 = document.getElementById('checkContentArgs5').value;
				var selectedCampaign = document.getElementById("jform_campaign_id");
				var selectedCampaignValue = selectedCampaign.options[selectedCampaign.selectedIndex].value;

				var res = checkSelectedContent(Args1, Args2, Args3, Args4, Args5)
				if (res === false)
				{
					return false;
				}

				if (selectedCampaignValue === '-1')
				{
					res = checkSelectedRecipients(document.getElementById('checkRecipientArgs').value);
					if (res === false)
					{
						return false;
					}
				}
			}
			Joomla.submitform(pressbutton, form);
		}
	};

	if (document.getElementById('currentTab') !== null && document.getElementById('currentTab').value === 'edit_basic') {
		var recipients = document.getElementById('recipients');
		if (selectedCampaignValue !== '-1') {
			recipients.style.display = "none";
		} else {
			recipients.style.display = "block";
		}
	}

	if (document.getElementById('substitute') !== null && document.getElementById('substitute').value === true) {
		var substitute = document.getElementsByName("jform[substitute_links]");
		for (var i = 0; i < substitute.length; i++) {
			substitute[i].onclick = function () {
				document.getElementById("add_content").value = "1";
				document.getElementById("template_id_old").value = "";
			};
		}
	}
};

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
	// enable InsertAtCaret
	var elms = document.querySelectorAll("#jform_intro_text_text,#jform_intro_text_headline,#jform_text_version,#jform_intro_headline,#jform_intro_text,#jform_html_version");
	for(var i = 0; i < elms.length; i++) {
		addEventHandler(elms[i], 'focus', function() {
			var actives = document.getElementsByClassName('insertatcaretactive');
			for (var z = 0; z < actives.length; z++) {
				actives[z].classList.remove('insertatcaretactive');
			}
			this.classList.add('insertatcaretactive');
		});
	}

	var jform_campaign_id = document.getElementById('jform_campaign_id');
	if (jform_campaign_id) {
		addEventHandler(jform_campaign_id, 'change', function() {
			var recipients = document.getElementById('recipients');
			if (this.value !== '-1') {
				recipients.style.display = 'none';
			} else {
				recipients.style.display = '';
			}
		});
	}
});

