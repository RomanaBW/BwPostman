<?php
namespace Page;

/**
 * Class CampaignEditPage
 *
 * @package Page
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
 *
 * @since   2.0.0
 */
class AccessPage
{
    // set array with all users
	public static $all_users = array(   'BwPostmanAdmin',
										'BwPostmanSectionAdmin',
										'BwPostmanManager',
										'BwPostmanPublisher',
										'BwPostmanEditor',
										'BwPostmanCampaignAdmin',
										'BwPostmanCampaignPublisher',
										'BwPostmanCampaignEditor',
										'BwPostmanMailinglistAdmin',
										'BwPostmanMailinglistPublisher',
										'BwPostmanMailinglistEditor',
										'BwPostmanNewsletterAdmin',
										'BwPostmanNewsletterPublisher',
										'BwPostmanNewsletterEditor',
										'BwPostmanSubscriberAdmin',
										'BwPostmanSubscriberPublisher',
										'BwPostmanSubscriberEditor',
										'BwPostmanTemplateAdmin',
										'BwPostmanTemplatePublisher',
										'BwPostmanTemplateEditor',
	);


	/**
	 * Array of toolbar id values for this page
	 *
	 * @var    array
	 *
	 * @since  2.0.0
	 */
	public static $toolbar = array (
		'Save & Close'  => ".//*[@id='toolbar-save']/button",
		'Save'          => ".//*[@id='toolbar-apply']/button",
		'Cancel'        => ".//*[@id='toolbar-cancel']/button",
		'Back'          => ".//*[@id='toolbar-back']/button",
		'Help'          => ".//*[@id='toolbar-help']/button",
	);

}
