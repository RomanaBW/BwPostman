<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit view for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;

// Import VIEW object class
jimport('joomla.application.component.view');

//get helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/subscriberhelper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/mailinglist.php');


/**
 * Class BwPostmanViewEdit
 *
 * @since       0.9.1
 */
class BwPostmanViewEdit extends JViewLegacy
{
	/**
	 * The subscriber data
	 *
	 * @var    object
	 *
	 * @since       0.9.1
	 */
	public $subscriber = null;

	/**
	 * several needed lists
	 *
	 * @var    array
	 *
	 * @since       0.9.1
	 */
	public $lists = null;

	/**
	 * The component parameters
	 *
	 * @var    object   Registry object
	 *
	 * @since       0.9.1
	 */
	public $params = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app        = Factory::getApplication();
		$session    = Factory::getSession();
		$params     = ComponentHelper::getParams('com_bwpostman', true);
		$menuParams = new Registry;
		$menu       = $app->getMenu()->getActive();

		if ($menu)
		{
			$menuParams->loadString($menu->getParams());
		}

		$mergedParams = clone $menuParams;
		$params->merge($mergedParams);

		$this->params = $params;

		// If there occurred an error while storing the data load the data from the session
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
		{
			$app->setUserState('com_bwpostman.subscriber.selected_lists', $subscriber->mailinglists);
		}

		// Get the mailinglists which the subscriber is authorized to see
		$model = $this->getModel();
		$mlTable = $model->getTable('Mailinglists');
		$subsTable = $model->getTable('Subscribers');
		$userId  = $subsTable->getUserIdOfSubscriber((int)$subscriber->id);
		$lists['available_mailinglists'] = $mlTable->getAuthorizedMailinglists((int)$userId);

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

		// switch frontend layout
		$layout   = $this->getLayout();
		if ($layout !== "editlink_form")
		{
			$tpl = $this->params->get('fe_layout');
		}
		// Set parent display
		parent::display($tpl);

		return $this;
	}
}
