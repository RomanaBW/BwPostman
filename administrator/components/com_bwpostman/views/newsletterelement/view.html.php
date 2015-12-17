<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element view to select a singlenewsletter for a view in frontend.
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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class BwPostmanViewNewsletterelement extends JViewLegacy
{

	/**
	 * Display
	 *
	 * @access	public
	 * @param	string Template
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$uri		= JFactory::getURI();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		// Build the key for the userState
		$key = $this->getName();

		// Load the ordering, the search and the filters
		$filter_order 		= $app->getUserStateFromRequest($key.'_filter_order', 'filter_order', 'a.subject', 'cmd');
		$filter_order_Dir 	= $app->getUserStateFromRequest($key.'_filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_state 		= $app->getUserStateFromRequest($key.'_filter_state', 'filter_state', '', 'word');
		$filter_search		= $app->getUserStateFromRequest($key.'_filter_search', 'filter_search', 'subject', 'string');
		$search				= $app->getUserStateFromRequest($key.'_search', 'search', '', 'string');
		$search				= JString::strtolower($search);

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_( 'COM_BWPOSTMAN_SELECTNEWSLETTER' ));
		$document->addStyleSheet(JURI::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get data from the model
		$items 		= $this->get('data');
		$pagination = $this->get('pagination');
		$total 		= $this->get('total');

		// Table ordering
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;

		// Search filter
		$lists['search'] = $search;

		// Save a reference into view
		$this->assignRef('items', $items);
		$this->assignRef('lists', $lists);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('request_url',	$uri_string);
		$this->assignRef('user', $user);

		// Call parent display
		parent::display($tpl);
	}
}//end class
?>
