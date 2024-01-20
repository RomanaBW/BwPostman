//
// BwPostman Newsletter Component
//
// BwPostman Javascript reset and submit filters on newsletters frontend view.
//
// @version %%version_number%%
// @package BwPostman Site
// @author Romana Boldt, Karl Klostermann
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

(function() {

	function clearSelected(w){
		let select = document.getElementById(w);
		if (select) {
			select.selectedIndex = 0;
		}
	}

	document.addEventListener('DOMContentLoaded', function() {

		let filterMls = document.getElementById('filter.mailinglist');
		if (filterMls) {
           	filterMls.addEventListener('change',function(){
				clearSelected('filter.campaign');
				clearSelected('filter.usergroup');
				document.getElementById('adminForm').submit();
			});
		}

		let filterCams = document.getElementById('filter.campaign');
		if (filterCams) {
            filterCams.addEventListener('change',function(){
				clearSelected('filter.mailinglist');
				clearSelected('filter.usergroup');
				document.getElementById('adminForm').submit();
			});
		}

		let filterGroups = document.getElementById('filter.usergroup')
		if (filterGroups) {
			filterGroups.addEventListener('change',function(){
				clearSelected('filter.mailinglist');
				clearSelected('filter.campaign');
				document.getElementById('adminForm').submit();
			});
		}
	});
})();
