<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit view for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
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
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

//get helper class
require_once (JPATH_COMPONENT . '/helpers/subscriberhelper.php');


/**
 * Class BwPostmanViewEdit
 */
class BwPostmanViewEdit extends JViewLegacy
{
	/**
	 * The subscriber data
	 *
	 * @var    object
	 */
	public $subscriber = null;

	/**
	 * several needed lists
	 *
	 * @var    array
	 */
	public $lists = null;

	/**
	 * The component parameters
	 *
	 * @var    object   Registry object
	 */
	public $params = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$app		    = JFactory::getApplication();
		$session 	    = JFactory::getSession();
		$this->params	= JComponentHelper::getParams('com_bwpostman', true);

		// If there occured an error while storing the data load the data from the session
		$subscriber_data = $session->get('subscriber_data');

		if(isset($subscriber_data) && is_array($subscriber_data))
		{
			$subscriber	= new stdClass();
			foreach ($subscriber_data AS $key => $value)
			{
				$subscriber->$key = $value;
			}
			$subscriber->id	= 0;
			$session->clear('subscriber_data');
		}
		else
		{
			$subscriber	= $this->get('Item');
			if(!is_object($subscriber))
			{
				$subscriber = BwPostmanSubscriberHelper::fillVoidSubscriber();
			}
		}
		if (is_array($subscriber->mailinglists))
			$app->setUserState('com_bwpostman.subscriber.selected_lists', $subscriber->mailinglists);

		// Get the mailinglists which the subscriber is authorized to see
		$lists['available_mailinglists'] = BwPostmanSubscriberHelper::getAuthorizedMailinglists($subscriber->id);

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename))
			$document->addStyleSheet(JUri::root(true) . $css_filename);

		// Build the email format select list
		if (!isset($subscriber->emailformat))
		{
			$mailformat_selected = $this->params->get('default_emailformat');
		}
		else
		{
			$mailformat_selected = $subscriber->emailformat;
		}
		$lists['emailformat']  = BwPostmanSubscriberHelper::buildMailformatSelectList($mailformat_selected);

		// Build the gender select list
		if (!isset($subscriber->gender))
		{
			$gender_selected = '';
		}
		else
		{
			$gender_selected = $subscriber->gender;
		}
		$lists['gender']    = BwPostmanSubscriberHelper::buildGenderList($gender_selected);

		// Save a reference into the view
		$this->lists        = $lists;
		$this->subscriber   = $subscriber;

		// Set parent display
		parent::display($tpl);
	}
}
