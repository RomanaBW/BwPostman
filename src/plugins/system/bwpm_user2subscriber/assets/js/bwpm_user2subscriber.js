/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin javascript file for BwPostman.
 *
 * @version 2.0.2 bwpmpu2s
 * @package			BwPostman User2Subscriber Plugin
 * @author			Romana Boldt
 * @copyright		(C) 2016-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
			var label_span = jQuery(label).parent().find('span');

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
			'#jform_bwpm_user2subscriber_additional_required'
		];
		var field_identifier    = [
			'#jform_bwpm_user2subscriber_name',
			'#jform_bwpm_user2subscriber_firstname',
			'#jform_bwpm_user2subscriber_special'
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
			'#jform_bwpm_user2subscriber_additional_required'
		];
		var field_identifier    = [
			'#jform_bwpm_user2subscriber_name',
			'#jform_bwpm_user2subscriber_firstname',
			'#jform_bwpm_user2subscriber_special'
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
