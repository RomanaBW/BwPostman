//
// BwPostman Newsletter Component
//
// BwPostman Javascript for template editing.
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

// insert placeholder
jQuery(function ($) {
	$.fn.EnableInsertAtCaret = function () {
		$(this).on("focus", function () {
			$(".insertatcaretactive").removeClass("insertatcaretactive");
			$(this).addClass("insertatcaretactive");
		});
	};
	$("#jform_intro_intro_text,#jform_intro_intro_headline,#jform_tpl_html").EnableInsertAtCaret();
});

function InsertAtCaret(myValue) {
	return jQuery(".insertatcaretactive").each(function (i) {
		if (document.selection) {
			//For browsers like Internet Explorer
			this.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			this.focus();
		} else if (this.selectionStart || this.selectionStart === '0') {
			//For browsers like Firefox and Webkit based
			var startPos = this.selectionStart;
			var endPos = this.selectionEnd;
			var scrollTop = this.scrollTop;
			this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
			this.focus();
			this.selectionStart = startPos + myValue.length;
			this.selectionEnd = startPos + myValue.length;
			this.scrollTop = scrollTop;
		} else {
			this.value += myValue;
			this.focus();
		}
	})
}
