	jQuery(document).ready(function()
	{
		menuItemHide(jQuery('#jform_assignment').val());
		jQuery('#jform_assignment').change(function()
		{
			menuItemHide(jQuery(this).val());
		})
	});
	function menuItemHide(val)
	{
		if (val == '')
		{
			jQuery('#newsletterselect-group').hide();
		}
		else
		{
			jQuery('#newsletterselect-group').show();
		}
	}
