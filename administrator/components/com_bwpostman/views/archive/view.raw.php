<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) archive view for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

/**
 * BwPostman Archive RAW View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 */
class BwPostmanViewArchive extends JViewLegacy
{
	public function display ($tpl = Null)
	{
		$jinput	= JFactory::getApplication()->input;

		$layout	= $jinput->get('layout');
		$model	= $this->getModel('archive');

		switch ($layout) { // Which tab are we in?
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
	}
}
