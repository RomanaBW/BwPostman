<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element view to select a single newsletter for a view in frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

jimport('joomla.application.component.view');

use Joomla\String\StringHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

/**
 * Class BwPostmanViewNewsletterelement
 *
 * @since       1.0.1
 */
class BwPostmanViewNewsletterelement extends JViewLegacy
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 *
	 * @since       1.0.1
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       1.0.1
	 */
	protected $pagination;

	/**
	 * property to hold mailing lists
	 *
	 * @var array   $lists
	 *
	 * @since       1.0.1
	 */
	protected $lists;

	/**
	 * property to hold user object
	 *
	 * @var object  $user
	 *
	 * @since       1.0.1
	 */
	protected $user;

	/**
	 * property to hold request url
	 *
	 * @var string   $request_url
	 *
	 * @since       1.0.1
	 */
	protected $request_url;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       1.0.1
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		$user		= Factory::getUser();
		$uri		= Uri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		// Build the key for the userState
		$key = $this->getName();

		// Load the ordering, the search and the filters
		$filter_order 		= $app->getUserStateFromRequest($key . '_filter_order', 'filter_order', 'a.subject', 'cmd');
		$filter_order_Dir 	= $app->getUserStateFromRequest($key . '_filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search				= $app->getUserStateFromRequest($key . '_search', 'search', '', 'string');
		$search				= StringHelper::strtolower($search);

		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_SELECTNEWSLETTER'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get data from the model
		$items 		= $this->get('data');
		$pagination = $this->get('pagination');

		// Table ordering
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;

		// Search filter
		$lists['search'] = $search;

		// Save a reference into view
		$this->items        = $items;
		$this->lists        = $lists;
		$this->pagination   = $pagination;
		$this->request_url  = $uri_string;
		$this->user         = $user;

		// Call parent display
		parent::display($tpl);
		return $this;
	}
}//end class
