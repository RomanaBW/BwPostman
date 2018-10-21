/**
 * BwPostman Buyer2Subscriber Plugin
 *
 * Plugin to automated subscription at VirtueMart registration
 *
 * BwPostman Buyer2Subscriber Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman Buyer2Subscriber Plugin
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
	var toggle_fields       = '.bwpm-u2s-fields-toggle';
	var subs_selected       = jQuery('#bw_newsletter_subscription');
	var subs_select_status  = subs_selected.find(":selected").val();

	// Hide plugin fields while subscription is not selected
	jQuery(toggle_fields).parent().parent().hide();

	var additional_required = jQuery('#bw_newsletter_additional_required_field').val();

	(function( jQuery )
	{
		jQuery.fn.toggleRequired = function(field_identifier, status)
		{
			var label_td = jQuery(field_identifier).parent().prev();
			var label    = jQuery(label_td);
			var label_span = jQuery(label).parent().find('span');

			if (status === '1')
			{
				jQuery(field_identifier).attr('required', 'required');
				jQuery(field_identifier).addClass('required');
				jQuery(label_span).appendTo(label);
				jQuery(label_span).addClass('asterisk');
				jQuery(label_span).html(' *');
			}
			if (status === '0')
			{
				jQuery(field_identifier).removeAttr('required');
				jQuery(field_identifier).removeClass('required');
				jQuery(label_span).removeClass('star');
			}
		}
	})( jQuery );

	if (subs_select_status === '0')
	{
		jQuery('#bw_gender').parent().parent().hide();
		jQuery('#bw_newsletter_format').parent().parent().hide();
		if (additional_required === '1')
		{
			jQuery().toggleRequired('#bw_newsletter_additional_field', subs_select_status);
		}
		jQuery('#bw_newsletter_additional_field').parent().parent().hide();
	}

	// switch fields on (subscription yes) and off (subscription no)
	subs_selected.change(function()
	{
		var optionSelected = jQuery(this).find("option:selected");
		var valueSelected  = optionSelected.val();
		var show_format    = jQuery('#bw_newsletter_show_format_field').val();

		if (valueSelected === '1')
		{
			jQuery('#bw_gender').parent().parent().show();
			jQuery('#bw_newsletter_additional_field').parent().parent().show();
			if (show_format === '1') {
				jQuery('#bw_newsletter_format').parent().parent().show();
			}
			if (additional_required === '1')
			{
				jQuery().toggleRequired('#bw_newsletter_additional_field', valueSelected);
			}
		}
		else
		{
			jQuery('#bw_gender').parent().parent().hide();
			jQuery('#bw_newsletter_format').parent().parent().hide();
			jQuery('#bw_newsletter_additional_field').parent().parent().hide();
			if (additional_required === '1')
			{
				jQuery().toggleRequired('#bw_newsletter_additional_field', subs_select_status);
			}
		}
	});

});

