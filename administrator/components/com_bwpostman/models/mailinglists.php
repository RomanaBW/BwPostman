<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglists model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modellist');

// Import helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

/**
 * BwPostman mailinglists model
 * Provides a general view of all mailinglists
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Mailinglists
 *
 * @since       0.9.1
 */
class BwPostmanModelMailinglists extends JModelList
{
	/**
	 * Mailinglists data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	var $_data = null;

	/**
	 * Number of all mailinglist
	 *
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 *
	 * @since       0.9.1
	 */
	var $_pagination = null;

	/**
	 * Mailinglists search
	 *
	 * @var string
	 *
	 * @since       0.9.1
	 */
	var $_search = null;

	/**
	 * Mailinglists key
	 * --> we need this as identifier for the different mailinglists filters (e.g. filter_order, state, search ...)
	 * --> value will be "lists"
	 *
	 * @var	string
	 *
	 * @since       0.9.1
	 */
	var $_key = null;

	/**
	 * The query object
	 *
	 * @var	object
	 *
	 * @since       2.0.0
	 */
	private $_query;

	/**
	 * Constructor
	 * --> handles the pagination and set the mailinglists key
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'description', 'a.description',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'subscribers',
				'created_date', 'a.created_date',
				'created_by', 'a.created_by'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter', 'filter_search_filter');
		$this->setState('filter.search_filter', $filtersearch);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('a.title', 'asc');

		$limitstart = $app->input->get->post->get('limitstart');
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 *
	 * @since	1.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.search_filter');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	protected
	 *
	 * @return 	string Query
	 *
	 * @since       0.9.1
	 */
	protected function getListQuery()
	{
		$this->_query	= $this->_db->getQuery(true);
		$sub_query      = $this->_getSubQuery();


		// Select the required fields from the table.
		$this->_query->select(
				$this->getState(
						'list.select',
						'a.id, a.title, a.description, a.checked_out, a.checked_out_time' .
						', a.published, a.access, a.created_date, a.created_by'
				) . ', (' . $sub_query
		);
		$this->_query->from($this->_db->quoteName('#__bwpostman_mailinglists', 'a'));

		$this->_getQueryJoins();
		$this->_getQueryWhere();
		$this->_getQueryOrder();

		$this->_db->setQuery($this->_query);

		return $this->_query;
	}

	/**
	 * Method to get the subquery this query needs
	 * This subquery counts the subscribers of each mailinglists
	 *
	 * @return JDatabaseQuery
	 *
	 * @since   2.0.0
	 */
	private function _getSubQuery()
	{
		$sub_query  = $this->_db->getQuery(true);
		$sub_query2	= $this->_db->getQuery(true);

		$sub_query2->select($this->_db->quoteName('d.id'));
		$sub_query2->from($this->_db->quoteName('#__bwpostman_subscribers','d'));
		$sub_query2->where($this->_db->quoteName('d.archive_flag') . ' = 0');

		$sub_query->select('COUNT(' . $this->_db->quoteName('b.subscriber_id') . ') AS ' . $this->_db->quoteName('subscribers'));
		$sub_query->from($this->_db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS ' . 'b');
		$sub_query->where($this->_db->quoteName('b.mailinglist_id') . ' = ' . $this->_db->quoteName('a.id'));
		$sub_query->where($this->_db->quoteName('b.subscriber_id') . ' IN (' . $sub_query2 . ')) AS subscribers');

		return $sub_query;
	}

	/**
	 * Method to get the joins this query needs
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryJoins()
	{
		// Join over the users for the checked out user.
		$this->_query->select($this->_db->quoteName('uc.name') . ' AS editor');
		$this->_query->join('LEFT', $this->_db->quoteName('#__users', 'uc') . ' ON ' . $this->_db->quoteName('uc.id') . ' = ' . $this->_db->quoteName('a.checked_out'));

		// Join over the asset groups.
		$this->_query->select($this->_db->quoteName('ag.title') . ' AS access_level');
		$this->_query->join('LEFT', $this->_db->quoteName('#__viewlevels', 'ag') . ' ON ' . $this->_db->quoteName('ag.id') . ' = ' . $this->_db->quoteName('a.access'));

		// Join over the users for the author.
		$this->_query->select($this->_db->quoteName('ua.name') , ' AS author_name');
		$this->_query->join('LEFT', $this->_db->quoteName('#__users', 'ua') . ' ON ' . $this->_db->quoteName('ua.id') . ' = ' . $this->_db->quoteName('a.created_by'));
	}

	/**
	 * Method to build the MySQL query 'where' part
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryWhere()
	{
		$this->_getFilterByAccessLevelFilter();
		$this->_getFilterByViewLevel();
		$this->_getFilterByComponentPermissions();
		$this->_getFilterByPublishedState();
		$this->_getFilterByArchiveState();
		$this->_getFilterBySearchword();

	}

	/**
	 * Method to build the MySQL query 'order' part
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryOrder()
	{
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}
		$this->_query->order($this->_db->quoteName($this->_db->escape($orderCol)) . ' ' . $this->_db->escape($orderDirn));
	}

	/**
	 * Method to get the filter by access level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByAccessLevelFilter()
	{
		$access = $this->getState('filter.access');
		if ($access)
		{
			$this->_query->where($this->_db->quoteName('a.access') . ' = ' . (int) $access);
		}
	}

	/**
	 * Method to get the filter by Joomla view level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByViewLevel()
	{
		$user	= JFactory::getUser();

		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$this->_query->where($this->_db->quoteName('a.access') . ' IN ('.$groups.')');
		}
	}

	/**
	 * Method to get the filter by BwPostman permissions
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByComponentPermissions()
	{
		$allowed_ids    = BwPostmanHelper::getAllowedRecords('mailinglist');

		if ($allowed_ids != 'all')
		{
			$this->_query->where($this->_db->quoteName('a.id') . ' IN ('.$allowed_ids.')');
		}
	}

	/**
	 * Method to get the filter by published state
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByPublishedState()
	{
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$this->_query->where($this->_db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$this->_query->where('(' . $this->_db->quoteName('a.published') . ' = 0 OR ' . $this->_db->quoteName('a.published') . ' = 1)');
		}
	}

	/**
	 * Method to get the filter by archived state
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByArchiveState()
	{
		$this->_query->where($this->_db->quoteName('a.archive_flag') . ' = ' . (int) 0);
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterBySearchword()
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search       = '%' . $this->_db->escape($this->getState('filter.search'), true) . '%';

		if (!empty($search))
		{
			switch ($filtersearch)
			{
				case 'description':
					$this->_query->where($this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				case 'title_description':
					$this->_query->where('(' . $this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false) . ' OR ' . $this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false) . ')');
					break;
				case 'title':
					$this->_query->where($this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				default:
			}
		}
	}
}
