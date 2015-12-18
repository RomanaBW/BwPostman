<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriberslists model for backend.
 *
 * @version 1.3.0 bwpm
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
jimport('joomla.application.component.modellist');

/**
 * BwPostman subscribers model
 * Provides a general view of all confirmed and unconfirmed subscribers as well as test-recipients
 *
 * @package		BwPostman-Admin
 * @subpackage	Subscribers
 */
class BwPostmanModelSubscribers extends JModelList
{
	/**
	 * Subscribers/Test-recipients data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Number of all Subscribers/Test-recipients
	 *
	 * @var int
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Subscribers/Test-recipients Search
	 *
	 * @var string
	 */
	var $_search = null;

	/**
	 * filter_mailinglist for subscribers/Test-recipients
	 *
	 * @var int
	 * @since	1.0.8
	 */
	var $_filter_mailinglist = null;

	/**
	 * filter_emailformat for subscribers/Test-recipients
	 * --> empty = nothing is selected, 0 = Text, 1 = HTML
	 *
	 * @var int
	 */
	var $_filter_emailformat = null;

	/**
	 * filter_search for subscribers/Test-recipients
	 * --> to specify the search
	 *
	 * @var string
	 */
	var $_filter_search = null;

	/**
	 * Constructor
	 * --> handles the pagination of the single tabs
	 */
	public function __construct()
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'firstname', 'a.firstname',
				'email', 'a.email',
				'emailformat', 'a.emailformat',
				'user_id', 'a.user_id',
				'mailinglists', 'a.mailinglists',
				'a.mailinglist', 'mailinglist',
				'checked_out_time', 'a.checked_out_time',
				'status', 'a.status', 'registered_by', 'a.registered_by'
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
		$app	= JFactory::getApplication();
		$jinput	= $app->input;

		// Adjust the context to support modal layouts.
		if ($layout = $jinput->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter', 'filter_search_filter', '');
		$this->setState('filter.search_filter', $filtersearch);

		$filter_mailinglist = $this->getUserStateFromRequest($this->context . '.filter.mailinglist', 'filter_mailinglist', '');
		$this->setState('filter.mailinglist', $filter_mailinglist);
		$app->setUserState('com_bwpostman.subscriber.batch_filter_mailinglist', $filter_mailinglist);

		$emailformat = $this->getUserStateFromRequest($this->context . '.filter.emailformat', 'filter_emailformat', '');
		$this->setState('filter.emailformat', $emailformat);

		// List state information.
		parent::populateState('a.name', 'asc');

		$limitstart = $jinput->get->post->get('limitstart');
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
	 * @since	1.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.search_filter');
		$id	.= ':'.$this->getState('filter.emailformat');
		$id	.= ':'.$this->getState('filter.mailinglist');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	private
	 * @return 	string Query
	 */
	protected function getListQuery()
	{
		$jinput		= JFactory::getApplication()->input;
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$sub_query	= $_db->getQuery(true);
		$user		= JFactory::getUser();

		//Get the tab in which we are for subquery
		$tab	= JFactory::getApplication()->getUserState('com_bwpostman.subscribers.tab', 'confirmed');

		switch ($tab) {
			case ("confirmed"):
				$tab_int	= 1;
				break;
			case ("unconfirmed"):
				$tab_int	= 0;
				break;
			case ("testrecipients"):
				$tab_int	= 9;
				break;
		}

		// Build sub query which counts the mailinglists of each subscriber
		$sub_query->select('COUNT(' . $_db->quoteName('b') . '.' . $_db->quoteName('mailinglist_id') . ') AS ' . $_db->quoteName('mailinglists'));
		$sub_query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists') . 'AS ' . $_db->quoteName('b'));
		$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('subscriber_id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('id'));

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'a.id, a.name, a.firstname, a.email, a.checked_out, a.checked_out_time' .
						', a.emailformat, a.user_id, a.status, a.registered_by'
				) . ', (' . $sub_query . ') AS mailinglists'
		);
		$query->from('#__bwpostman_subscribers AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Filter by mailinglist
		$mailinglist = $this->getState('filter.mailinglist');

		if ($mailinglist) {
			$sub_query2	= $_db->getQuery(true);

			$sub_query2->select($_db->quoteName('c') . '.' . $_db->quoteName('subscriber_id'));
			$sub_query2->from($_db->quoteName('#__bwpostman_subscribers_mailinglists') . 'AS ' . $_db->quoteName('c'));
			$sub_query2->where($_db->quoteName('c') . '.' . $_db->quoteName('mailinglist_id') . ' = ' . (int) $mailinglist);

			$query->where('a.id IN (' . $sub_query2 . ')');
		}

		// Filter by emailformat.
		$emailformat = $this->getState('filter.emailformat');
		if ($emailformat != '') {
			$query->where('a.emailformat = ' . (int) $emailformat);
		}

		// Filter by tab (confirmed, unconfirmed, testrecipients)
		$query->where('a.status = ' . (int) $tab_int);

		// Filter by archive state
		$query->where('a.archive_flag = ' . (int) 0);

		// Filter by search word.
		$filtersearch	= $this->getState('filter.search_filter');
		$search			= $_db->escape($this->getState('filter.search'), true);

		if (!empty($search)) {
			$search	= '%' . $search . '%';

			switch ($filtersearch) {
				case 'email':
					$query->where('a.email LIKE ' . $_db->Quote($search, false));
					break;
				case 'name_email':
					$query->where('(a.email LIKE ' . $_db->Quote($search, false) . 'OR a.name LIKE ' . $_db->Quote($search, false) . ')');
					break;
				case 'fullname':
					$query->where('(a.firstname LIKE ' . $_db->Quote($search, false) . 'OR a.name LIKE ' . $_db->Quote($search, false) . ')');
					break;
				case 'firstname':
					$query->where('a.firstname LIKE ' . $_db->Quote($search, false));
					break;
				case 'name':
					$query->where('a.name LIKE ' . $_db->Quote($search, false));
					break;
				default:
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if($orderCol == 'access_level')
			$orderCol = 'ag.title';
		$query->order($_db->escape($orderCol.' '.$orderDirn));

		$_db->setQuery($query);

		return $query;
	}

	/**
	 * Method to get all mailinglists
	 *
	 * @access 	public
	 * @return 	object Mailinglists
	 * @since	1.0.8
	 */
	public function getMailinglists()
	{
		$app		= JFactory::getApplication();
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		$query->select($_db->quoteName('id') . ' AS value');
		$query->select($_db->quoteName('title') . ' AS text');
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->order('title ASC');
		$_db->setQuery ($query);

		$result = $_db->loadObjectList();

		$mailinglists 	= array ();
		$mailinglists[]	= JHTML::_('select.option',  '', '- '. JText::_('COM_BWPOSTMAN_SUB_FILTER_MAILINGLISTS') .' -');
		$mailinglists 	= array_merge($mailinglists, $result);

		return $mailinglists;
	}
}
