<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletterslists model for backend.
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

// Import MODEL object class
jimport('joomla.application.component.modellist');

/**
 * BwPostman newsletters model
 * Provides a general view of all unsent and sent newsletters
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanModelNewsletters extends JModelList
{
	/**
	 * Constructor
	 * --> handles the pagination of the single tabs
	 */
	public function __construct()
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'subject', 'a.subject',
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
		if ($layout = $app->input->get('tab', 'unsent'))
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
	 * @return 	int count Queue-data
	 */
	public function getCountQueue()
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		$query->select('COUNT(*)');
		$query->from($_db->quoteName('#__bwpostman_sendmailqueue'));

		$_db->setQuery($query);
		$count_queue = $_db->loadResult();

		return $count_queue;
	}
	
	/**
	 * Method to build the MySQL query
	 *
	 * @access 	private
	 * @return 	string Query
	 */
	protected function getListQuery()
	{
		$_db	= $this->_db;
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$query	= $_db->getQuery(true);
		
		// Define null and now dates, get params
		$nullDate	= $_db->quote($_db->getNullDate());
		$nowDate	= $_db->quote(JFactory::getDate()->toSql());
		
		//Get the tab in which we are for correct query
		$tab	= $jinput->get('tab', 'unsent');

		switch ($tab) {
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
		

		switch ($tab) {
			case ("unsent"):
			case ("sent"):
			default:
					$query->select(
						$this->getState(
							'list.select',
							'a.id, a.subject, a.description, a.checked_out, a.checked_out_time' .
							', a.published, a.publish_up, a.publish_down, a.created_date, a.created_by, a.modified_time'
						)
					);
					$query->select('a.mailing_date');
					$query->select('a.description');
					$query->select('c.title AS campaign_id');
					$query->from('#__bwpostman_newsletters AS a');
					$query->leftJoin('#__bwpostman_campaigns AS c ON c.id = a.campaign_id');
					$query->where('a.archive_flag = 0');
					
					// Join over the users for the checked out user.
					$query->select('uc.name AS editor');
					$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
					
					// Join over the users for the authors.
					$query->select('ua.name AS authors');
					$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
					
					// Filter by campaign
					if ($campaign = $this->getState('filter.campaign_id')) {
						$query->where('a.campaign_id = ' . (int) $campaign);
					}
					
					// Filter by mailinglist
					if ($mailinglist = $this->getState('filter.mailinglists')) {
						$query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
						$query->where('m.mailinglist_id = ' . (int) $mailinglist);
					}
					
					// Filter by usergroup
					if ($usergroup = $this->getState('filter.usergroups')) {
						$query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
						$query->where('m.mailinglist_id = ' . -(int) $usergroup);
					}
					
					// Filter by authors
					if ($authors = $this->getState('filter.authors')) {
						$query->where('a.created_by = ' . (int) $authors);
					}
					
					// Filter by published state
					$published = $this->getState('filter.published');
					if (is_numeric($published)) {
						switch ($published) {
							case 0:
							case 1:
							default:
									$query->where('a.published = ' . (int) $published);
								break;
							case 2:
									$query->where('a.publish_down <> ' . $nullDate);
									$query->where('a.publish_down <= ' . $nowDate);
								break;
							case 3:
									$query->where('(publish_down >= ' . $nowDate . ' OR publish_down = ' . $nullDate . ')');
								break;
							case 4:
									$query->where('a.publish_up <= ' . $nowDate);
									$query->where('a.publish_down <> ' . $nullDate);
									$query->where('a.publish_down > ' . $nowDate);
								break;
							case 5:
									$query->where('a.publish_up > ' . $nowDate);
								break;
						}
						
					}
					elseif ($published === '') {
						$query->where('(a.published = 0 OR a.published = 1)');
					}
					
					// Filter by mailing date
					$query->where('a.mailing_date' . $tab_int . "'0000-00-00 00:00:00'");
					
					// Filter by archive state
					$query->where('a.archive_flag = ' . (int) 0);
					
					// Filter by search word.
					$filtersearch	= $this->getState('filter.search_filter');
					$search			= $_db->escape($this->getState('filter.search'), true);
					
					if (!empty($search)) {
						$search			= '%' . $search . '%';
						switch ($filtersearch) {
							case 'subject':
									$query->where('a.subject LIKE ' . $_db->Quote($search));
								break;
							case 'description':
									$query->where('a.description LIKE ' . $_db->Quote($search));
								break;
							case 'subject_description':
									$query->where('(a.subject LIKE ' . $_db->Quote($search). 'OR a.description LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'html_text_version':
									$query->where('(a.html_version LIKE ' . $_db->Quote($search, false) . 'OR a.text_version LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'text_version':	
									$query->where('a.text_version LIKE ' . $_db->Quote($search. false));
								break;
							case 'html_version':	
									$query->where('a.html_version LIKE ' . $_db->Quote($search, false));
								break;
							default:	
						}
					}
					
					// Add the list ordering clause.
					$orderCol	= $this->state->get('list.ordering', 'a.subject');
					$orderDirn	= $this->state->get('list.direction', 'asc');
					
					//sqlsrv change
					if($orderCol == 'modified_time')
						$orderCol = 'a.modified_time';
					
					$query->order($_db->escape($orderCol.' '.$orderDirn));
					
					$_db->setQuery($query);
				break;

			case ("queue"):
					$query->select('DISTINCT(' . $_db->quoteName('c')  . '.' . $_db->quoteName('nl_id') . ')');
					$query->select($_db->quoteName('c')  . '.' . $_db->quoteName('subject') . ' AS subject');
					$query->select($_db->quoteName('q')  . '.*');
					$query->select($_db->quoteName('n')  . '.' . $_db->quoteName('description'));
					$query->select($_db->quoteName('ua') . '.' . $_db->quoteName('name') . ' AS authors');
					$query->from($_db->quoteName('#__bwpostman_sendmailcontent')  . ' AS ' . $_db->quoteName('c'));
					$query->rightJoin('#__bwpostman_sendmailqueue AS q ON q.content_id = c.id');
					$query->leftJoin('#__bwpostman_newsletters AS n ON n.id = c.nl_id');
					$query->leftJoin('#__users AS ua ON ua.id = n.created_by');
					
					// Filter by campaign
					if ($campaign = $this->getState('filter.campaign_id')) {
						$query->where('n.campaign_id = ' . (int) $campaign);
					}
					
					// Filter by authors
					if ($authors = $this->getState('filter.authors')) {
						$query->where('n.created_by = ' . (int) $authors);
					}
					
					// Filter by search word.
					$filtersearch	= $this->getState('filter.search_filter');
					$search			= $_db->escape($this->getState('filter.search'), true);
					
					if (!empty($search)) {
						$search			= '%' . $search . '%';
						switch ($filtersearch) {
							case 'subject':
								$query->where('n.subject LIKE ' . $_db->Quote($search));
								break;
							case 'description':
								$query->where('n.description LIKE ' . $_db->Quote($search));
								break;
							case 'subject_description':
								$query->where('(n.subject LIKE ' . $_db->Quote($search). 'OR n.description LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'html_text_version':
								$query->where('(n.html_version LIKE ' . $_db->Quote($search, false) . 'OR q.text_version LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'text_version':
								$query->where('n.text_version LIKE ' . $_db->Quote($search. false));
								break;
							case 'html_version':
								$query->where('n.html_version LIKE ' . $_db->Quote($search, false));
								break;
							default:
						}
					}
					
					$query->order($_db->quoteName('q')  . '.' . $_db->quoteName('id'));
					
					$_db->setQuery($query);
				break;
		}	
		return $query;
	}
}