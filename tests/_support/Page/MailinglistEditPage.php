<?php
namespace Page;

/**
 * Class MailinglistEditPage
 *
 * @package Page
 */
class MailinglistEditPage
{
    // include url of current page
    public static $url = 'administrator/index.php?option=com_bwpostman&view=mailinglist&layout=edit';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */

	public static $title        = '#jform_title';
	public static $description  = '#jform_description';
	public static $access       = '#jform_access';
	public static $published    = '#jform_published';

	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  1.2.0
	 */
	public static $toolbar = array (
		'Save & Close' => './/*[@id=\'toolbar-save\']/button',
		'Save' => './/*[@id=\'toolbar-apply\']/button',
		'Cancel' => './/*[@id=\'toolbar-cancel\']/button',
		'Help' => './/*[@id=\'toolbar-help\']/button',
	);

}
