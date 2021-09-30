/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin javascript file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman User2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENSE.txt
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

jQuery(document).ready(function()
{
	var toggle_fields   = '.bwpm-u2s-fields-toggle';

	// Hide plugin fields while subscription is not selected
	jQuery(toggle_fields).parent().parent().hide();

	var show_format = jQuery('#jform_bwpm_user2subscriber_emailformat_show').val();

	if (show_format === '0')
	{
		jQuery('#jform_bwpm_user2subscriber_emailformat0').parent().parent().parent().hide();
	}

	(function( jQuery )
	{
		jQuery.fn.toggleRequired = function(field_identifier, status)
		{
			var label = jQuery(field_identifier + '-lbl');
			var parentOfLabel = jQuery(label).parent();
			var label_span = jQuery(parentOfLabel).find('span');

			if (label_span.length === 0)
			{
				jQuery(label).after('<span></span>');
				label_span = parentOfLabel.find('span');
			}

			if (status === 1)
			{
				jQuery(field_identifier).attr('required', 'required');
				jQuery(field_identifier).addClass('required');
				jQuery(label_span).appendTo(label);
				jQuery(label_span).addClass('star');
				jQuery(label_span).removeClass('optional');
				jQuery(label_span).html(' *');
			}
			if (status === 0)
			{
				jQuery(field_identifier).removeAttr('required');
				jQuery(field_identifier).removeClass('required');
				jQuery(label_span).appendTo(label.parent());
				jQuery(label_span).addClass('optional');
				jQuery(label_span).removeClass('star');
				jQuery(label_span).html('(optional)');
			}
		}
	})( jQuery );

	// switch fields on (subscription yes) and off (subscription no)
	jQuery('#jform_bwpm_user2subscriber_bwpm_user2subscriber1').click(function () {

		var field_required    = [
			'#jform_bwpm_user2subscriber_name_required',
			'#jform_bwpm_user2subscriber_firstname_required',
			'#jform_bwpm_user2subscriber_additional_required',
			'#jform_bwpm_user2subscriber_mailinglists_required',
			'#jform_bwpm_user2subscriber_bwpdisclaimer_required'
		];
		var field_identifier    = [
			'#jform_bwpm_user2subscriber_bwpm_name',
			'#jform_bwpm_user2subscriber_firstname',
			'#jform_bwpm_user2subscriber_special',
			'#jform_bwpm_user2subscriber_mailinglists',
			'#jform_bwpm_user2subscriber_bwpdisclaimer'
		];
		var len = field_required.length;

		jQuery('.bwpm-u2s-fields-toggle').parent().parent().show();

		var show_format = jQuery('#jform_bwpm_user2subscriber_emailformat_show').val();

		if (show_format === '0')
		{
			jQuery('#jform_bwpm_user2subscriber_emailformat0').parent().parent().parent().hide();
		}

		for(i = 0; i < len; i++) {
			if (jQuery(field_required[i]).val() === '1') {
				jQuery().toggleRequired(field_identifier[i], 1);
			}
		}
	});

	jQuery('#jform_bwpm_user2subscriber_bwpm_user2subscriber0').click(function()
	{
		var field_required    = [
			'#jform_bwpm_user2subscriber_name_required',
			'#jform_bwpm_user2subscriber_firstname_required',
			'#jform_bwpm_user2subscriber_additional_required',
			'#jform_bwpm_user2subscriber_mailinglists_required',
			'#jform_bwpm_user2subscriber_bwpdisclaimer_required'
		];
		var field_identifier    = [
			'#jform_bwpm_user2subscriber_bwpm_name',
			'#jform_bwpm_user2subscriber_firstname',
			'#jform_bwpm_user2subscriber_special',
			'#jform_bwpm_user2subscriber_mailinglists',
			'#jform_bwpm_user2subscriber_bwpdisclaimer'
		];
		var len = field_required.length;

		jQuery('.bwpm-u2s-fields-toggle').parent().parent().hide();

		for(i = 0; i < len; i++) {
			if (jQuery(field_required[i]).val() === '1') {
				jQuery().toggleRequired(field_identifier[i], 0);
			}
		}
	});

	// Turn radios into btn-group
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery('.btn-group label:not(.active)').click(function()
	{
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked'))
		{
			label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');

			if (input.val() === '')
			{
				label.addClass('active btn-primary');
			}
			else if (input.val() === 0)
			{
				label.addClass('active btn-danger');
			}
			else
			{
				label.addClass('active btn-success');
			}
			input.prop('checked', true);
		}
	});

	jQuery('.btn-group input[checked=checked]').each(function()
	{
		if (jQuery(this).val() === '')
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-primary');
		}
		else if (jQuery(this).val() === 0)
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-danger');
		}
		else
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-success');
		}
	});
});

function setPlgModal() {
	// Set the modal height and width 90%
	if (typeof window.innerWidth != 'undefined')
	{
		viewportwidth = window.innerWidth,
			viewportheight = window.innerHeight
	}
	else if (typeof document.documentElement != 'undefined'
		&& typeof document.documentElement.clientWidth !=
		'undefined' && document.documentElement.clientWidth !== 0)
	{
		viewportwidth = document.documentElement.clientWidth,
			viewportheight = document.documentElement.clientHeight
	}
	else
	{
		viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
			viewportheight = document.getElementsByTagName('body')[0].clientHeight
	}
	var modalcontent = document.getElementById('bwp_plg_modal-content');

	if (modalcontent !== null) {
		modalcontent.style.height = viewportheight - (viewportheight * 0.10) + 'px';
		modalcontent.style.width = viewportwidth - (viewportwidth * 0.10) + 'px';

		// Get the modal
		var modal = document.getElementById('bwp_plg_Modal');

		// Get the Iframe-Wrapper and set Iframe
		var wrapper = document.getElementById('bwp_plg_wrapper');
		var html = '<iframe id="iFrame" name="iFrame" src="' + dc_src + '" frameborder="0" style="width:100%; height:100%;"></iframe>';

		// Get the button that opens the modal
		var btnopen = document.getElementById("bwp_plg_open");

		// Get the <span> element that closes the modal
		var btnclose = document.getElementsByClassName("bwp_plg_close")[0];

		// When the user clicks the button, open the modal
		btnopen.onclick = function () {
			wrapper.innerHTML = html;
			modal.style.display = "block";
		};

		// When the user clicks on <span> (x), close the modal
		btnclose.onclick = function () {
			modal.style.display = "none";
		};

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function (event) {
			if (event.target === modal) {
				modal.style.display = "none";
			}
		}
	}
}

