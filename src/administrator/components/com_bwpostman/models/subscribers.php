<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers lists model for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

// Import MODEL object class
jimport('joomla.application.component.modellist');

/**
 * BwPostman subscribers model
 * Provides a general view of all confirmed and unconfirmed subscribers as well as test-recipients
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class BwPostmanModelSubscribers extends JModelList
{
	/**
	 * The query object
	 *
	 * @var	object
	 *
	 * @since       2.0.0
	 */
	protected $query;

	/**
	 * Constructor
	 * --> handles the pagination of the single tabs
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'firstname', 'a.firstname',
				'gender', 'a.gender',
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
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app	= JFactory::getApplication();
		$jinput	= $app->input;

		// Adjust the context to support modal layouts.
		$layout = $jinput->get('layout');
		if ($layout)
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
	 *
	 * @since	1.0.1
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.search_filter');
		$id	.= ':' . $this->getState('filter.emailformat');
		$id	.= ':' . $this->getState('filter.mailinglist');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	private
	 *
	 * @return 	string Query
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function getListQuery()
	{
		$this->query = $this->_db->getQuery(true);

		$sub_query  = $this->getSubQuery();

		// Select the required fields from the table.
		$this->query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.firstname, a.gender, a.email, a.checked_out, a.checked_out_time' .
				', a.emailformat, a.user_id, a.status, a.registered_by'
			) . ', (' . $sub_query . ') AS mailinglists'
		);
		$this->query->from($this->_db->quoteName('#__bwpostman_subscribers', 'a'));

		$this->getQueryJoins();
		$this->getQueryWhere();
		$this->getQueryOrder();

		$this->_db->setQuery($this->query);

		return $this->query;
	}

	/**
	 * Method to get the subquery this query needs
	 * This subquery counts the mailinglists of each subscriber
	 *
	 * @return JDatabaseQuery
	 *
	 * @since   2.0.0
	 */
	private function getSubQuery()
	{
		$sub_query  = $this->_db->getQuery(true);

		$sub_query->select('COUNT(' . $this->_db->quoteName('b.mailinglist_id') . ') AS ' . $this->_db->quoteName('mailinglists'));
		$sub_query->from($this->_db->quoteName('#__bwpostman_subscribers_mailinglists', 'b'));
		$sub_query->where($this->_db->quoteName('b.subscriber_id') . ' = ' . $this->_db->quoteName('a.id'));

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
	private function getQueryJoins()
	{
		// Join over the users for the checked out user.
		$this->query->select($this->_db->quoteName('uc.name') . ' AS editor');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__users', 'uc') . ' ON ' . $this->_db->quoteName('uc.id') . ' = ' . $this->_db->quoteName('a.checked_out')
		);
	}

	/**
	 * Method to build the MySQL query 'where' part
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getQueryWhere()
	{
		$this->getFilterByAccessLevelFilter();
		$this->getFilterByViewLevel();
//		$this->getFilterByComponentPermissions();
		$this->getFilterBySubscriberState();
		$this->getFilterByMailinglist();
		$this->getFilterByMailformat();
		$this->getFilterByArchiveState();
		$this->getFilterBySearchword();
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
	private function getQueryOrder()
	{
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}

		$this->query->order($this->_db->quoteName($this->_db->escape($orderCol)) . ' ' . $this->_db->escape($orderDirn));
	}

	/**
	 * Method to get the filter by access level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByAccessLevelFilter()
	{
		if (JFactory::getApplication()->isSite())
		{
			$access = $this->getState('filter.access');
			if ($access)
			{
				$this->query->where($this->_db->quoteName('a.access') . ' = ' . (int) $access);
			}
		}
	}

	/**
	 * Method to get the filter by Joomla view level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByViewLevel()
	{
		if (JFactory::getApplication()->isSite())
		{
			$user = JFactory::getUser();

			if (!$user->authorise('core.admin'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$this->query->where($this->_db->quoteName('a.access') . ' IN (' . $groups . ')');
			}
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
	private function getFilterByComponentPermissions()
	{
		$allowed_items  = BwPostmanHelper::getAllowedRecords('subscriber');

		if ($allowed_items != 'all')
		{
			$allowed_ids    = implode(',', $allowed_items);
			$this->query->where($this->_db->quoteName('a.id') . ' IN (' . $allowed_ids . ')');
		}
	}

	/**
	 * Method to get the filter by subscriber state (confirmed, unconfirmed, testrecipient)
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterBySubscriberState()
	{
		$tab	    = JFactory::getApplication()->getUserState('com_bwpostman.subscribers.tab', 'confirmed');
		$tab_int    = 1;

		switch ($tab)
		{
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

		$this->query->where('a.status = ' . (int) $tab_int);
	}

	/**
	 * Method to get the filter by selected mailinglist
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByMailinglist()
	{
		$mailinglist = $this->getState('filter.mailinglist');

		if ($mailinglist)
		{
			$query	= $this->_db->getQuery(true);

			$query->select($this->_db->quoteName('c.subscriber_id'));
			$query->from($this->_db->quoteName('#__bwpostman_subscribers_mailinglists', 'c'));
			$query->where($this->_db->quoteName('c.mailinglist_id') . ' = ' . (int) $mailinglist);

			$this->query->where('a.id IN (' . $query . ')');
		}
	}

	/**
	 * Method to get the filter by selected email format
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByMailformat()
	{
		$emailformat = $this->getState('filter.emailformat');
		if ($emailformat != '')
		{
			$this->query->where($this->_db->quoteName('a.emailformat') . ' = ' . (int) $emailformat);
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
	private function getFilterByArchiveState()
	{
		$this->query->where($this->_db->quoteName('a.archive_flag') . ' = ' . (int) 0);
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
	private function getFilterBySearchword()
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search			= $this->_db->escape($this->getState('filter.search'), true);

		if (!empty($search))
		{
			$search	= '%' . $search . '%';

			switch ($filtersearch)
			{
				case 'email':
					$this->query->where($this->_db->quoteName('a.email') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				case 'name_email':
					$this->query->where(
						'(' . $this->_db->quoteName('a.email') . ' LIKE ' . $this->_db->quote($search, false) .
						' OR ' . $this->_db->quoteName('a.name') . ' LIKE ' . $this->_db->quote($search, false) . ')'
					);
					break;
				case 'fullname':
					$this->query->where(
						'(' . $this->_db->quoteName('a.firstname') . ' LIKE ' . $this->_db->quote($search, false) .
						' OR ' . $this->_db->quoteName('a.name') . ' LIKE ' . $this->_db->quote($search, false) . ')'
					);
					break;
				case 'firstname':
					$this->query->where($this->_db->quoteName('a.firstname') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				case 'name':
					$this->query->where($this->_db->quoteName('a.name') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				default:
			}
		}
	}
	/**
	 * Method to get all mailinglists
	 *
	 * @access 	public
	 *
	 * @return 	array mailinglists
	 *
	 * @throws Exception
	 *
	 * @since	1.0.8
	 */
	public function getMailinglists()
	{
		$result = array();
		$query	= $this->_db->getQuery(true);

		$query->select($this->_db->quoteName('id') . ' AS value');
		$query->select($this->_db->quoteName('title') . ' AS text');
		$query->from($this->_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($this->_db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->order('title ASC');
		$this->_db->setQuery($query);

		try
		{
			$result = $this->_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$mailinglists 	= array ();
		$mailinglists[]	= JHtml::_('select.option',  '', '- ' . JText::_('COM_BWPOSTMAN_SUB_FILTER_MAILINGLISTS') . ' -');
		$mailinglists 	= array_merge($mailinglists, $result);

		return $mailinglists;
	}
}
