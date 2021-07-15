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
function buttonClick(text, editor) {
	jInsertEditorText(text, editor);
}

//insert placeholder Joomla 4
function buttonClick4(text, editor) {
	// jInsertEditorText(text, editor);
	if (jQuery('#'+editor+':visible').length === 0){
		var content = window.Joomla.editors.instances[editor].getValue();
		// Romana - geht sonst bei leerem Editorfeld nicht
		Joomla.editors.instances[editor].replaceSelection(text);
	}
	else
	{
		// if editor is disabled
		InsertAtCaret(text);
	}
	return true;
}
