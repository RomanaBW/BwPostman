<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists model for backend.
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
 * BwPostman newsletters model
 * Provides a general view of all unsent and sent newsletters
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanModelNewsletters extends JModelList
{
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
	 * --> handles the pagination of the single tabs
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'attachment', 'a.attachment',
				'subject', 'a.subject', 'sc.subject',
				'description', 'a.description',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'campaign_id', 'a.campaign_id',
				'created_date', 'a.created_date',
				'modified_time', 'a.modified_time',
				'editor', 'a.editor',
				'authors', 'a.authors',
				'mailing_date', 'a.mailing_date',
				'mailinglists', 'a.mailinglists',
				'created_by', 'a.created_by',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'ordering', 'a.ordering',
				'n.description',
				'q.recipient',
				'q.trial',
				'q.id',
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
		$layout = $app->input->get('tab', 'unsent');
		if ($layout)
		{
			$this->context .= '.' . $layout;
			$this->setState('tab', $layout);
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter', 'filter_search_filter');
		$this->setState('filter.search_filter', $filtersearch);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$publish_up = $this->getUserStateFromRequest($this->context . '.filter.publish_up', 'filter_publish_up', '');
		$this->setState('filter.publish_up', $publish_up);

		$publish_down = $this->getUserStateFromRequest($this->context . '.filter.publish_down', 'filter_publish_down', '');
		$this->setState('filter.publish_down', $publish_down);

		$authors = $this->getUserStateFromRequest($this->context . '.filter.authors', 'filter_authors', '');
		$this->setState('filter.authors', $authors);

		$campaign_id = $this->getUserStateFromRequest($this->context . '.filter.campaign_id', 'filter_campaign_id', '');
		$this->setState('filter.campaign_id', $campaign_id);

		$mailinglist_id = $this->getUserStateFromRequest($this->context . '.filter.mailinglists', 'filter_mailinglists', '');
		$this->setState('filter.mailinglists', $mailinglist_id);

		$usergroup_id = $this->getUserStateFromRequest($this->context . '.filter.usergroups', 'filter_usergroups', '');
		$this->setState('filter.usergroups', $usergroup_id);

		$mailing_date = $this->getUserStateFromRequest($this->context . '.filter.mailing_date', 'filter_mailing_date', '');
		$this->setState('filter.mailing_date', $mailing_date);

		// List state information.
		parent::populateState('a.subject', 'asc');

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
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.publish_up');
		$id	.= ':'.$this->getState('filter.publish_down');
		$id	.= ':'.$this->getState('filter.authors');
		$id	.= ':'.$this->getState('filter.campaign_id');
		$id	.= ':'.$this->getState('filter.mailinglists');
		$id	.= ':'.$this->getState('filter.usergroups');
		$id	.= ':'.$this->getState('filter.description');

		return parent::getStoreId($id);
	}

	/**
	/**
	 * Method to count the queue data
	 *
	 * @access	public
	 *
	 * @return 	int count Queue-data
	 *
	 * @since       0.9.1
	 */
	public function getCountQueue()
	{
		$count_queue    = 0;

		$this->_query = $this->_db->getQuery(true);

		$this->_query->select('COUNT(*)');
		$this->_query->from($this->_db->quoteName('#__bwpostman_sendmailqueue'));

		$this->_db->setQuery($this->_query);
		try
		{
			$count_queue = $this->_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $count_queue;
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	private
	 *
	 * @return 	string Query
	 *
	 * @since       0.9.1
	 */
	protected function getListQuery()
	{
		$jinput	        = JFactory::getApplication()->input;
		$this->_query	= $this->_db->getQuery(true);

		//Get the tab in which we are for correct query
		$tab	= $jinput->get('tab', 'unsent');

		switch ($tab)
		{
			case ("unsent"):
			case ("sent"):
			default:
					$this->_query->select(
						$this->getState(
							'list.select',
							'a.id, a.subject, a.attachment, a.description, a.checked_out, a.checked_out_time' .
							', a.published, a.publish_up, a.publish_down, a.created_date, a.created_by, a.modified_time'
						)
					);
					$this->_query->select($this->_db->quoteName('a.mailing_date'));
					$this->_query->select($this->_db->quoteName('a.description'));
					$this->_query->select($this->_db->quoteName('c.title') . ' AS ' . $this->_db->quoteName('campaign_id'));

					$this->_query->from($this->_db->quoteName('#__bwpostman_newsletters') . 'AS a');
				break;

			case ("queue"):
					$this->_query->select('DISTINCT(' . $this->_db->quoteName('sc.nl_id') . ')');
					$this->_query->select($this->_db->quoteName('sc.subject') . ' AS subject');
					$this->_query->select($this->_db->quoteName('q.id'));
					$this->_query->select($this->_db->quoteName('q.recipient'));
					$this->_query->select($this->_db->quoteName('q.trial'));
					$this->_query->select($this->_db->quoteName('n.description'));
					$this->_query->select($this->_db->quoteName('ua.name') . ' AS authors');
					$this->_query->select($this->_db->quoteName('c.title') . ' AS ' . $this->_db->quoteName('campaign_id'));

					$this->_query->from($this->_db->quoteName('#__bwpostman_sendmailcontent', 'sc'));
				break;
		}
		$this->_getQueryJoins($tab);
		$this->_getQueryWhere($tab);
		$this->_getQueryOrder($tab);

		$this->_db->setQuery($this->_query);

		return $this->_query;
	}

	/**
	 * Method to get the joins this query needs
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryJoins($tab)
	{
		if ($tab == 'sent' || $tab == 'unsent')
		{
			// join over campaigns
			$this->_query->leftJoin($this->_db->quoteName('#__bwpostman_campaigns', 'c') . ' ON ' . $this->_db->quoteName('c.id') . ' = ' . $this->_db->quoteName('a.campaign_id'));

			// Join over the users for the checked out user.
			$this->_query->select($this->_db->quoteName('uc.name') . ' AS editor');
			$this->_query->join('LEFT', $this->_db->quoteName('#__users', 'uc') . ' ON ' . $this->_db->quoteName('uc.id') . ' = ' . $this->_db->quoteName('a.checked_out'));

			// Join over the users for the author.
			$this->_query->select($this->_db->quoteName('ua.name') . ' AS authors');
			$this->_query->join('LEFT', $this->_db->quoteName('#__users', 'ua') . ' ON ' . $this->_db->quoteName('ua.id') . ' = ' . $this->_db->quoteName('a.created_by'));
		}
		elseif ($tab == 'queue')
		{
			$this->_query->rightJoin($this->_db->quoteName('#__bwpostman_sendmailqueue', 'q') . ' ON ' . $this->_db->quoteName('q.content_id') . ' = ' . $this->_db->quoteName('sc.id'));
			$this->_query->leftJoin($this->_db->quoteName('#__bwpostman_newsletters', 'n') . ' ON ' . $this->_db->quoteName('n.id') . ' = ' . $this->_db->quoteName('sc.nl_id'));
			$this->_query->leftJoin($this->_db->quoteName('#__users', 'ua') . ' ON ' . $this->_db->quoteName('ua.id') . ' = ' . $this->_db->quoteName('n.created_by'));
			$this->_query->leftJoin($this->_db->quoteName('#__bwpostman_campaigns', 'c') . ' ON ' . $this->_db->quoteName('c.id') . ' = ' . $this->_db->quoteName('n.campaign_id'));
		}
	}

	/**
	 * Method to build the MySQL query 'where' part
	 *
	 * @access 	private
	 *
	 * @param   string     $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryWhere($tab)
	{
		$this->_getFilterByAccessLevelFilter();
		$this->_getFilterByViewLevel();
		$this->_getFilterByComponentPermissions();
		$this->_getFilterByCampaign($tab);
		$this->_getFilterByAuthor($tab);
		$this->_getFilterBySearchword($tab);

		if ($tab == 'sent' || $tab == 'unsent')
		{
			$this->_getFilterByPublishedState();
			$this->_getFilterByArchiveState();
			$this->_getFilterByMailinglist();
			$this->_getFilterByUsergroup();
			$this->_getFilterByMailingDate($tab);
		}
	}

	/**
	 * Method to build the MySQL query 'order' part
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getQueryOrder($tab)
	{
		$orderCol  = $this->state->get('list.ordering', 'a.subject');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($tab == 'sent' || $tab == 'unsent')
		{
			//sqlsrv change
			if ($orderCol == 'modified_time')
			{
				$orderCol = 'a.modified_time';
			}
			if ($orderCol == 'sc.subject')
			{
				$orderCol = 'a.subject';
			}
		}
		elseif ($tab == 'queue')
		{
			if ($orderCol == 'a.subject')
			{
				$orderCol = 'sc.subject';
			}
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
		$allowed_ids    = BwPostmanHelper::getAllowedRecords('newsletter');

		if ($allowed_ids != 'all')
		{
			$this->_query->where($this->_db->quoteName('a.id') . ' IN ('.$allowed_ids.')');
		}
	}

	/**
	 * Method to get the filter by selected campaign
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByCampaign($tab)
	{
		$campaign = $this->getState('filter.campaign_id');

		if ($campaign)
		{
			if ($tab == 'queue')
			{
				$this->_query->where('n.campaign_id = ' . (int) $campaign);
			}
			else
			{
				$this->_query->where('a.campaign_id = ' . (int) $campaign);
			}
		}
	}

	/**
	 * Method to get the filter by selected author
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByAuthor($tab)
	{
		$authors = $this->getState('filter.authors');
		if ($authors)
		{
			if ($tab == 'queue')
			{
				$this->_query->where('n.created_by = ' . (int) $authors);
			}
			else
			{
				$this->_query->where('a.created_by = ' . (int) $authors);
			}
		}
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterBySearchword($tab)
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search			= $this->_db->escape($this->getState('filter.search'), true);

		if (!empty($search))
		{
			$search	= '%' . $search . '%';

			switch ($filtersearch)
			{
				case 'subject':
					if($tab == 'queue')
					{
					$this->_query->where($this->_db->quoteName('c.subject') . ' LIKE ' . $this->_db->quote($search));
					}
					else
					{
						$this->_query->where($this->_db->quoteName('a.subject') . ' LIKE ' . $this->_db->quote($search));
					}
					break;
				case 'description':
					if($tab == 'queue')
					{
						$this->_query->where($this->_db->quoteName('n.description') . ' LIKE ' . $this->_db->quote($search));
					}
					else
					{
						$this->_query->where($this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search));
					}
					break;
				case 'subject_description':
					if($tab == 'queue')
					{
						$this->_query->where('(' . $this->_db->quoteName('c.subject') . ' LIKE ' . $this->_db->quote($search) . ' OR ' . $this->_db->quoteName('n.description') . ' LIKE ' . $this->_db->quote($search, false) . ')');
					}
					else
					{
						$this->_query->where('(' . $this->_db->quoteName('a.subject') . ' LIKE ' . $this->_db->quote($search) . ' OR ' . $this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false) . ')');
					}
					break;
				case 'html_text_version':
					if ($tab == 'unsent' || $tab == 'sent')
					{
						$this->_query->where('(' . $this->_db->quoteName('a.html_version') . ' LIKE ' . $this->_db->quote($search, false) . ' OR ' . $this->_db->quoteName('a.text_version') . ' LIKE ' . $this->_db->quote($search, false) . ')');
					}
					elseif ($tab == 'queue')
					{
						$this->_query->where('(' . $this->_db->quoteName('a.html_version') . ' LIKE ' . $this->_db->quote($search, false) . 'OR ' . $this->_db->quoteName('q.text_version') . ' LIKE ' . $this->_db->quote($search, false) . ')');
					}
					break;
				case 'text_version':
					$this->_query->where($this->_db->quoteName('a.text_version') . ' LIKE ' . $this->_db->quote($search. false));
					break;
				case 'html_version':
					$this->_query->where($this->_db->quoteName('a.html_version') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				default:
			}
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
		// Define null and now dates, get params
		$nullDate	= $this->_db->quote($this->_db->getNullDate());
		$nowDate	= $this->_db->quote(JFactory::getDate()->toSql());

		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			switch ($published)
			{
				case 0:
				case 1:
				default:
					$this->_query->where($this->_db->quoteName('a.published') . ' = ' . (int) $published);
					break;
				case 2:
					$this->_query->where($this->_db->quoteName('a.publish_down') . ' <> ' . $nullDate);
					$this->_query->where($this->_db->quoteName('a.publish_down') . ' <= ' . $nowDate);
					break;
				case 3:
					$this->_query->where($this->_db->quoteName('publish_down') . ' >= ' . $nowDate . ' OR publish_down = ' . $nullDate . ')');
					break;
				case 4:
					$this->_query->where($this->_db->quoteName('a.publish_up') . ' <= ' . $nowDate);
					$this->_query->where($this->_db->quoteName('a.publish_down') . ' <> ' . $nullDate);
					$this->_query->where($this->_db->quoteName('a.publish_down') . ' > ' . $nowDate);
					break;
				case 5:
					$this->_query->where($this->_db->quoteName('a.publish_up') . ' > ' . $nowDate);
					break;
			}
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
	 * Method to get the filter by selected mailinglist
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByMailinglist()
	{
		$mailinglist = $this->getState('filter.mailinglists');
		if ($mailinglist)
		{
			$this->_query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
			$this->_query->where('m.mailinglist_id = ' . (int) $mailinglist);
		}
	}

	/**
	 * Method to get the filter by selected usergroup
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByUsergroup()
	{
		$usergroup = $this->getState('filter.usergroups');
		if ($usergroup)
		{
			$this->_query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
			$this->_query->where('m.mailinglist_id = ' . -(int) $usergroup);
		}
	}

	/**
	 * Method to get the filter by mailingdate
	 *
	 * @access 	private
	 *
	 * @param   string  $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function _getFilterByMailingDate($tab)
	{
		switch ($tab)
		{
			case ("unsent"):
			default:
				$tab_int	= ' = ';
				break;
			case ("sent"):
				$tab_int	= ' <> ';
				break;
			case ("queue"):
				$tab_int	= ' = ';
				break;
		}

		$this->_query->where('a.mailing_date' . $tab_int . "'0000-00-00 00:00:00'");
	}

    /**
     * Method to get a JPagination object for the data set.
     *
     * @return  JPagination  A JPagination object for the data set.
     *
     * @since   1.6
     */
    public function getQueuePagination()
    {
        // Get a storage key.
        $store = $this->getStoreId('getPaginationQueue');

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');

        // Create the pagination object and add the object to the internal cache.
        $this->cache[$store] = new JPagination($this->getCountQueue(), $this->getStart(), $limit);

        return $this->cache[$store];
    }

}
