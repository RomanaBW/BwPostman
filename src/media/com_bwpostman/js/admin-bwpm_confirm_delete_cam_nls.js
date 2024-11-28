//
// BwPostman Newsletter Component
//
// BwPostman Javascript for validating delete newsletters assigned to campaign to delete.
//
// @version 4.3.1
// @package BwPostman-Admin
// @author Romana Boldt
// @copyright (C) 2024 Boldt Webservice <forum@boldt-webservice.de>
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

function confirmDelete(delete_value)  // Get the selected value from modal box
{
	document.adminForm.remove_nl.value = delete_value;
	Joomla.submitbutton('archive.delete');
}
