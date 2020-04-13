//
// BwPostman Newsletter Component
//
// BwPostman Javascript to customize the layout of menu params for newsletters frontend view.
//
// @version %%version_number%%
// @package BwPostman-Side
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

jQuery(document).ready(function() {
	jQuery('#fieldset-COM_BWPOSTMAN_ML_AVAILABLE .column-count-md-2').attr('class', 'column-count-1');
	jQuery('#fieldset-COM_BWPOSTMAN_CAM_AVAILABLE .column-count-md-2').attr('class', 'column-count-1');
	jQuery('#jform_params_ml_available tbody tr').on('click',function(){
		var box = $(this).find('input:checkbox');
		box.prop("checked", !box.prop("checked"));
	});
	jQuery('#jform_params_groups_available tbody tr').on('click',function(){
		var box = $(this).find('input:checkbox');
		box.prop("checked", !box.prop("checked"));
	});
	jQuery('#jform_params_cam_available tbody tr').on('click',function(){
		var box = $(this).find('input:checkbox');
		box.prop("checked", !box.prop("checked"));
	});
});

