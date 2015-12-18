<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main controller for backend.
 *
 * @version 1.3.0 bwpm
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

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

/**
 * BwPostman Component Controller
 *
 * @package 	BwPostman-Admin
 */
class BwPostmanController extends JControllerLegacy
{

	/**
	 * Display the view
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	/**
	 * Method to call the start layout for the add text template
	 *
	 * @access	public
	 * @since	1.1.0
	 */
	public function addtext()
	{
		$jinput	= JFactory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_text');
		$link = JRoute::_('index.php?option=com_bwpostman&view=template&layout=default_text', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the start layout for the add html template
	 *
	 * @access	public
	 * @since	1.1.0
	 */
	public function addhtml()
	{
		$jinput	= JFactory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_add');
		$link = JRoute::_('index.php?option=com_bwpostman&view=template&layout=default_html', false);
		$this->setRedirect($link);
	}
}
