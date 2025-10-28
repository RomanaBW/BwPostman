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

namespace BoldtWebservice\Component\BwPostman\Site\View\Edit;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Utilities\ArrayHelper;

/**
 * Class BwPostmanViewEdit
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
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
	 * Success values
	 *
	 * @var    object   standard object
	 *
	 * @since       4.0.0
	 */
	public $success;

	/**
	 * The current error object
	 *
	 * @var    object
	 *
	 * @since       4.0.0
	 */
	public $error = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  HtmlView
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null): HtmlView
	{
		$app        = Factory::getApplication();
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

		// Get subscriber
		$subscriber   = $this->getSubscriber();

		if (is_array($subscriber->mailinglists))
		{
			$app->setUserState('com_bwpostman.subscriber.selected_lists', $subscriber->mailinglists);
		}

		// Get the mailinglists which the subscriber is authorized to see
		$lists['available_mailinglists'] = $this->getMailinglistsLists($subscriber);

		// Build the email format select list
		$lists['emailformat'] = $this->buildFormatSelectList($subscriber);

		// Build the gender select list
		$lists['gender'] = $this->buildGenderSelectList($subscriber);

		// Save a reference into the view
		$this->lists        = $lists;
		$this->subscriber   = $subscriber;

		// switch frontend layout
		$layout   = $this->getLayout();
		if ($layout !== "editlink_form")
		{
			$tpl = $this->params->get('fe_layout', 'cassiopeia');
		}

		// Set parent display
		parent::display($tpl);

		return $this;
	}

	/**
	 * Get the mailinglists which the subscriber is authorized to see
	 *
	 * @param $subscriber
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private function getMailinglistsLists($subscriber): array
	{
		$model     = $this->getModel();
		$mlTable   = $model->getTable('Mailinglist');
		$subsTable = $model->getTable('Subscriber');
		$userId    = $subsTable->getUserIdOfSubscriber($subscriber->id);

		return $mlTable->getAuthorizedMailinglists((int) $userId);

	}

	/**
	 * Build the email format select list
	 *
	 * @param       $subscriber
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	private function buildFormatSelectList($subscriber): string
	{
		if (!isset($subscriber->emailformat))
		{
			$mailformat_selected = $this->params->get('default_emailformat', '1');
		}
		else
		{
			$mailformat_selected = $subscriber->emailformat;
		}

		return BwPostmanSubscriberHelper::buildMailformatSelectList($mailformat_selected);
	}

	/**
	 * Build the gender select list
	 *
	 * @param       $subscriber
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	private function buildGenderSelectList($subscriber): string
	{
		if (!isset($subscriber->gender))
		{
			$gender_selected = '';
		}
		else
		{
			$gender_selected = $subscriber->gender;
		}

		return BwPostmanSubscriberHelper::buildGenderList($gender_selected);
	}

	/**
	 * Get subscriber
	 *
	 * @return mixed|object
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private function getSubscriber()
	{
		// If there occurred an error while storing the data load the data from the session
		$session         = Factory::getApplication()->getSession();
		$subscriber_data = $session->get('subscriber_data');

		if (isset($subscriber_data) && is_array($subscriber_data))
		{
			$subscriber = ArrayHelper::toObject($subscriber_data, 'stdClass', false);

			$subscriber->id = 0;
			$session->clear('subscriber_data');
		}
		else
		{
			$subscriber = $this->get('Item');
		}

		return $subscriber;
	}
}
