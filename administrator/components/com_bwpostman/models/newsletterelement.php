<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman model for a backend element to select a singlenewsletter for a view in frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
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

// Import MODEL object class
jimport('joomla.application.component.model');

use Joomla\String\StringHelper as JString;

/**
 * BwPostman newsletterelement model
 * Provides a view of single newsletters
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletterelement
 */
class BwPostmanModelNewsletterelement extends JModelLegacy
{

	/**
	 * Newsletters data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Number of all newsletters
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Newsletters search
	 *
	 * @var string
	 */
	var $_search = null;

	/**
	 * Mailinglists key
	 * --> we need this as identifier for the different mailinglists filters (e.g. filter_order, state, search ...)
	 * --> value will be "mailinglists"
	 *
	 * @var	string
	 */
	var $_key = null;

	/**
	 * Constructor
	 * --> handles the pagination and set the mailinglists key
	 */
	public function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();

		$this->_key = $this->getName();

		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest($this->_key.'_limit', 'limit', $app->get('list_limit'), 0);
		$limitstart	= $app->getUserStateFromRequest($this->_key.'_limitstart', 'limitstart', 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Methode to get the mailinglists data
	 *
	 * @access	public
	 * @return 	object Mailinglists-data
	 */
	public function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	/**
	 * Method to get the total number of mailinglists that shall be displayed
	 *
	 * @access 	public
	 * @return 	int Total number
	 */
	public function getTotal()
	{
		// Load the content if it doesn't already exist
		if (!$this->_total)
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the mailinglists view
	 *
	 * @access 	public
	 * @return 	object Pagination
	 */
	public function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), (int) $this->getState('limitstart'), (int) $this->getState('limit'));
		}
		return $this->_pagination;
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	private
	 * @return 	string Query
	 */
	private function _buildQuery()
	{
		$app = JFactory::getApplication();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		// Build the query
		$query->select('a.id, a.subject, a.description,  a.mailing_date, a.published, a.archive_flag');
		$query->from('#__bwpostman_newsletters AS a');

		// Filter by published state
		$query->where('a.published != ' . (int) 0);
		$query->where($_db->quoteName('a.mailing_date') . ' != ' . $_db->quote('0000-00-00 00:00:00'));

		// Get the search string
		$search = $this->getSearch();

		// Get the search filter
		$filter_search = $app->getUserStateFromRequest($this->_key.'_filter_search', 'filter_search', 'subject', 'string');

		if ($search != '')
		{
			$fields = explode(',', $filter_search);

			foreach ($fields as $field)
			{
				$search = $_db->quote('%' . str_replace(' ', '%', $_db->escape(trim($search), true) . '%'));
				$query->where('a.'.$field . " LIKE " . $search);
			}
		}

		// Get the filter order
		$filter_order		= $app->getUserStateFromRequest($this->_key.'_filter_order', '.filter_order', 'a.subject', 'word');
		$filter_order_Dir	= $app->getUserStateFromRequest($this->_key.'_filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'a.subject')
		{
			$query->order('a.subject '.$filter_order_Dir);
		}
		else
		{
			$query->order($_db->escape($filter_order.' '.$filter_order_Dir));
		}

		return $query;
	}

	/**
	 * Method to get the search term
	 *
	 * @access 	public
	 * @return 	string
	 */
	private function getSearch()
	{
		if (!$this->_search)
		{
			$app = JFactory::getApplication();

			$search = $app->getUserStateFromRequest($this->_key.'_search', 'search', '', 'string');
			$this->_search = JString::strtolower($search);
		}
		return $this->_search;
	}
}
