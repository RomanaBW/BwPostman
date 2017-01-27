/**
 * BwPostman User2Subscriber Plugin
 *
 * Plugin to automated subscription at Joomla registration
 *
 * BwPostman User2Subscriber Plugin javascript file for BwPostman.
 *
 * @version 2.0.0 bwpmpus
 * @package			BwPostman User2Subscriber Plugin
 * @author			Romana Boldt
 * @copyright		(C) 2016-2017 Boldt Webservice <forum@boldt-webservice.de>
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
	// Turn radios into btn-group
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery('.btn-group label:not(.active)').click(function()
	{
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked'))
		{
			label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');

			if (input.val() == '')
			{
				label.addClass('active btn-primary');
			}
			else if (input.val() == 0)
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
		if (jQuery(this).val() == '')
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-primary');
		}
		else if (jQuery(this).val() == 0)
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-danger');
		}
		else
		{
			jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-success');
		}
	});
})
