<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) archive view for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Archive RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Archive
 *
 * @since       0.9.1
 */
class BwPostmanViewArchive extends JViewLegacy
{
	/**
	 * property to hold subscriber object
	 *
	 * @var object  $sub
	 *
	 * @since       0.9.1
	 */
	protected $sub;

	/**
	 * property to hold campaign object
	 *
	 * @var object  $cam
	 *
	 * @since       0.9.1
	 */
	protected $cam;

	/**
	 * property to hold mailinglist object
	 *
	 * @var object  $ml
	 *
	 * @since       0.9.1
	 */
	protected $ml;

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
		$app	= JFactory::getApplication();
		$jinput	= $app->input;

		if (!BwPostmanHelper::canView('archive'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$layout	= $jinput->get('layout');
		$model	= $this->getModel('archive');

		switch ($layout)
		{ // Which tab are we in?
			case "subscriber_modal":
				$sub_id		= $jinput->get('sub_id');
				$this->sub	= $model->getSingleSubscriber((int) $sub_id);
				break;
			case "campaign_modal":
				$cam_id		= $jinput->get('cam_id');
				$this->cam	= $model->getSingleCampaign((int) $cam_id);
				break;
			case "mailinglist_modal":
				$ml_id		= $jinput->get('ml_id');
				$this->ml	= $model->getSingleMailinglist((int) $ml_id);
				break;
		}

		// Call parent display
		parent::display($tpl);
		return $this;
	}
}
