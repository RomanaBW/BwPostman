/**
 * BwPostman Newsletter Component
 *
 * BwPostman Javascript for newsletters lists.
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

function changeTab(newTab, currentTab)
{
	if (newTab !== currentTab)
	{
		document.adminForm.tab.setAttribute('value',newTab);
	}
}

(function() {
	Joomla = window.Joomla || {};

	Joomla.submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton === 'newsletter.archive')
		{
			var ConfirmArchive = confirm(document.getElementById('archiveText').value);
			if (ConfirmArchive === true)
			{
				Joomla.submitform(pressbutton, form);
			}
		}
		else
		{
			Joomla.submitform(pressbutton, form);
		}
	};

})();

jQuery(document).ready(function() {
	function updateModal(selector) {
		var $sel = selector;
		var src = $sel.data('src');
		$('.modal-title').text($sel.data('title'));
		var windowheight = $(window).height()-225;
		$('.modal-text').html('<iframe id="frame" src="'+src+'" width="100%" height="'+windowheight+'" frameborder="0"></iframe>');
		$('.modal-spinner').addClass('hidden');
	}
	$('span.iframe').on('click',function(){
		updateModal($(this));
	});
	$("#bwp-modal").on('hidden.bs.modal', function () {
		$('.modal-title').empty();
		$('.modal-text').empty();
		$('.modal-spinner').removeClass('hidden');
		$('.modal-footer .counter').empty();
	});
});

