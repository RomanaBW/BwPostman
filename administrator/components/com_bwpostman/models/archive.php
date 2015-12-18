<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive model for backend.
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

// Import MODEL object class
jimport('joomla.application.component.modellist');

/**
 * BwPostman archive model
 * Provides a general view of all archived items
 *
 * @package		BwPostman-Admin
 * @subpackage	Archive
 */
class BwPostmanModelArchive extends JModelList
{
	/**
	 * Constructor
	 * --> handles the pagination of the single tabs
	 */
	public function __construct()
	{
		$app	= JFactory::getApplication();
		$layout	= $app->input->get('layout','newsletters');

		if (empty($config['filter_fields'])) {
			switch ($layout) {
				case 'newsletters':
				default:
						$config['filter_fields'] = array(
							'id', 'a.id',
							'subject', 'a.subject',
							'mailinglists', 'a.mailinglists',
							'description', 'a.description',
							'mailing_date', 'a.mailing_date',
							'author', 'a.author',
							'campaigns', 'a.campaigns',
							'published', 'a.published',
							'publish_up', 'a.publish_up',
							'publish_down', 'a.publish_down',
							'archive_date', 'a.archive_date',
							'access', 'a.access', 'access_level'
						);
					break;
				case 'subscribers':
						$config['filter_fields'] = array(
							'id', 'a.id',
							'name', 'a.name',
							'firstname', 'a.firstname',
							'email', 'a.email',
							'status', 'a.status',
							'emailformat', 'a.emailformat',
							'mailinglists', 'a.mailinglists',
							'archive_date', 'a.archive_date',
							'access', 'a.access', 'access_level'
						);
					break;
				case 'campaigns':
						$config['filter_fields'] = array(
							'id', 'a.id',
							'newsletters', 'a.newsletters',
							'title', 'a.title',
							'description', 'a.description',
							'archive_date', 'a.archive_date',
							'access', 'a.access', 'access_level'
						);
					break;
				case 'mailinglists':
						$config['filter_fields'] = array(
							'id', 'a.id',
							'mailinglists', 'a.mailinglists',
							'subscribers', 'a.subscribers',
							'title', 'a.title',
							'description', 'a.description',
							'published', 'a.published',
							'archive_date', 'a.archive_date',
							'access', 'a.access', 'access_level'
						);
					break;
				case 'templates':
						$config['filter_fields'] = array(
							'id', 'a.id',
							'title', 'a.title',
							'thumbnail', 'a.thumbnail',
							'description', 'a.description',
							'archive_date', 'a.archive_date',
							'published', 'a.published',
							'tpl_id', 'a.tpl_id'
						);
					break;
			}
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
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$orderMainCol	= '';

		// Adjust the context to support modal and tabbed layouts.
		if ($layout = $app->input->get('layout','newsletters'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search_nl', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter', 'filter_search_filter');
		$this->setState('filter.search_filter', $filtersearch);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
		$this->setState('filter.status', $status);

		$filter_mailinglist = $this->getUserStateFromRequest($this->context . '.filter.mailinglist', 'filter_mailinglist');
		$this->setState('filter.mailinglist', $filter_mailinglist);


		$emailformat = $this->getUserStateFromRequest($this->context . '.filter.emailformat', 'filter_emailformat');
		$this->setState('filter.emailformat', $emailformat);

		$tpl_id = $this->getUserStateFromRequest($this->context . '.filter.tpl_id', 'filter_tpl_id');
		$this->setState('filter.tpl_id', $tpl_id);

		switch ($layout) { // Which tab are we in?
			default:
			case "newsletters":
					$orderMainCol	= 'a.subject';

					$usergroup = $this->getUserStateFromRequest($this->context . '.filter.usergroups', 'filter_usergroups');
					$this->setState('filter.usergroups', $usergroup);

					$campaign = $this->getUserStateFromRequest($this->context . '.filter.campaigns', 'filter_campaigns');
					$this->setState('filter.campaigns', $campaign);

					$author = $this->getUserStateFromRequest($this->context . '.filter.authors', 'filter_authors', '');
					$this->setState('filter.authors', $author);

					$mailing_date = $this->getUserStateFromRequest($this->context . '.filter.mailing_date', 'filter_mailing_date', '');
					$this->setState('filter.mailing_date', $mailing_date);
				break;

			case "subscribers":
					$orderMainCol	= 'a.name';
				break;

			case "campaigns":
					$orderMainCol	= 'a.title';
				break;

			case "mailinglists":
					$orderMainCol	= 'a.title';
				break;

			case "templates":
					$orderMainCol	= 'a.title';
				break;
		}

		// List state information.
		parent::populateState($orderMainCol, 'ASC');

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
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.search_filter');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.publish_up');
		$id	.= ':'.$this->getState('filter.publish_down');
		$id	.= ':'.$this->getState('filter.status');
		$id	.= ':'.$this->getState('filter.author');
		$id	.= ':'.$this->getState('filter.mailinglists');
		$id	.= ':'.$this->getState('filter.usergroups');
		$id	.= ':'.$this->getState('filter.campaigns');
		$id	.= ':'.$this->getState('filter.mailing_date');
		$id	.= ':'.$this->getState('filter.emailformat');
		$id	.= ':'.$this->getState('filter.mailinglist');
		$id	.= ':'.$this->getState('filter.tpl_id');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	protected
	 *
	 * @return 	string Query
	 */
	protected function getListQuery()
	{
		$_db		= $this->_db;
		$query1		= $_db->getQuery(true);
		$query		= $_db->getQuery(true);
		$sub_query	= $_db->getQuery(true);
		$sub_query2	= $_db->getQuery(true);
		$user		= JFactory::getUser();
		$jinput		= JFactory::getApplication()->input;
		$layout		= $jinput->get('layout','newsletters');

		switch ($layout) {
			// We are in the newsletters_tab
			default:
			case "newsletters":
					$orderMainCol	= 'subject';

					$query->select($_db->quoteName('a') . '.' . '*');
					$query->select($_db->quoteName('u') . '.' . $_db->quoteName('name') . ' AS ' . $_db->quoteName('author'));
					$query->select($_db->quoteName('c') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('campaigns'));
					$query->select($_db->quoteName('c') . '.' . $_db->quoteName('archive_flag') . ' AS ' . $_db->quoteName('campaign_archive_flag'));
					$query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS ' . $_db->quoteName('a'));
					$query->leftJoin($_db->quoteName('#__users') .' AS ' . $_db->quoteName('u') . ' ON ' . $_db->quoteName('u')  . '.' . $_db->quoteName('id') . ' =  ' . $_db->quoteName('a')  . '.' . $_db->quoteName('created_by'));
					$query->leftJoin($_db->quoteName('#__bwpostman_campaigns') .' AS ' . $_db->quoteName('c') . ' ON ' . $_db->quoteName('c') . '.' . $_db->quoteName('id') . ' =  ' . $_db->quoteName('a') . '.' . $_db->quoteName('campaign_id'));
					break;

			// We are in the subscribers_tab
			case "subscribers":
					$orderMainCol	= 'name';

					// Build sub query which counts all subscribed mailinglists of each subscriber
					$sub_query2->select($_db->quoteName('d') . '.' . $_db->quoteName('id'));
					$sub_query2->from($_db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $_db->quoteName('d'));
					$sub_query2->where($_db->quoteName('d') . '.' . $_db->quoteName('archive_flag') . " = " . (int) 0);

					$sub_query->select('COUNT(' . $_db->quoteName('b')  . '.' . $_db->quoteName('mailinglist_id') . ') AS ' . $_db->quoteName('mailinglists'));
					$sub_query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS ' . $_db->quoteName('b'));
					$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('subscriber_id') . " = " . $_db->quoteName('a')  . '.' . $_db->quoteName('id'));
					$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('mailinglist_id') . " IN (" . $sub_query2 . ')');

					$query->select($_db->quoteName('a') . '.' . "*, IF (emailformat = '1','HTML','TEXT')" . ' AS ' . $_db->quoteName('emailformat'));
					$query->select('(' . $sub_query . ') AS ' . $_db->quoteName('mailinglists'));
					$query->from($_db->quoteName('#__bwpostman_subscribers') . ' AS ' . $_db->quoteName('a'));
				break;

			// We are in the campaigns_tab and we want to show all assigned newsletters
			// because we offer the option to unarchive not only the campaign but also the
			// assigned newsletters
			case "campaigns":
					$orderMainCol	= 'title';

					// Build sub query which counts all newsletters of each campaign
					$sub_query->select('COUNT(' . $_db->quoteName('n') . '.' . $_db->quoteName('id') . ') AS ' . $_db->quoteName('newsletters'));
					$sub_query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS ' . $_db->quoteName('n'));
					$sub_query->where($_db->quoteName('n') . '.' . $_db->quoteName('campaign_id') . " = " . $_db->quoteName('a')  . '.' . $_db->quoteName('id'));

					$query->select($_db->quoteName('a') . '.' . '*');
					$query->select('(' . $sub_query . ') AS ' . $_db->quoteName('newsletters'));
					$query->from($_db->quoteName('#__bwpostman_campaigns') . ' AS ' . $_db->quoteName('a'));
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
					$orderMainCol	= 'title';

					// Build sub query which counts all subscribers of each mailinglist
					$sub_query2->select($_db->quoteName('d') . '.' . $_db->quoteName('id'));
					$sub_query2->from($_db->quoteName('#__bwpostman_subscribers') . ' AS ' . $_db->quoteName('d'));
					$sub_query2->where($_db->quoteName('d') . '.' . $_db->quoteName('archive_flag') . " = " . (int) 0);

					$sub_query->select('COUNT(' . $_db->quoteName('b')  . '.' . $_db->quoteName('subscriber_id') . ') AS ' . $_db->quoteName('subscribers'));
					$sub_query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS ' . $_db->quoteName('b'));
					$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('mailinglist_id') . " = " . $_db->quoteName('a')  . '.' . $_db->quoteName('id'));
					$sub_query->where($_db->quoteName('b') . '.' . $_db->quoteName('subscriber_id') . " IN (" . $sub_query2 . ')');

					$query->select($_db->quoteName('a') . '.' . '*');
					$query->select('(' . $sub_query . ') AS ' . $_db->quoteName('subscribers'));
					$query->from($_db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $_db->quoteName('a'));

					// Join over the asset groups.
					$query->select('ag.title AS access_level');
					$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
				break;

			// We are in the templates_tab
			case "templates":
					$orderMainCol	= 'title';

					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('id'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('title'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('description'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('thumbnail'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('published'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('checked_out'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('archive_date'));
					$query->select($_db->quoteName('a') . '.' . "tpl_id, IF (tpl_id = '998' OR tpl_id > '999','TEXT','HTML')" . ' AS ' . $_db->quoteName('tpl_id'));
					$query->select($_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
					$query->from($_db->quoteName('#__bwpostman_templates') . ' AS ' . $_db->quoteName('a'));
				break;
		}
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where($_db->quoteName('a') . '.' . $_db->quoteName('access') . ' = ' . (int) $access);
		}

		$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('archive_flag') . ' = ' . (int) 1);

		// Get the WHERE clause and ORDER-BY clause for the query
		$this->_buildQueryWhere($layout, $query);

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if($orderCol == 'access_level') $orderCol = 'ag.title';

		if (($orderCol == '') || ($orderCol == 'a.' . $orderMainCol)) {
			$query->order($_db->quoteName('a')  . '.' . $_db->quoteName($orderMainCol) . ' ' . $orderDirn);
		}
		else {
			$query->order($_db->escape($orderCol . ' ' . $orderDirn) . ', ' . $_db->quoteName('a')  . '.' . $_db->quoteName($orderMainCol));
		}
		$_db->setQuery($query);

		return $query;
	}

	/**
	 * Method to build the WHERE clause
	 *
	 * @access 	protected
	 *
	 * @return 	string Query
	 */
	protected function _buildQueryWhere($layout, &$query)
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;

		// Get the search string
		$where = '';

		$filtersearch	= $this->getState('filter.search_filter');
		$search			= $_db->escape($this->getState('filter.search'));

		// get select list filters
		switch ($layout) { // Which tab are we in?
			case "newsletters":
					// Get the mailinglist
					$filter_mailinglist = $this->getState('filter.mailinglists');

					if ($filter_mailinglist != '') {
						$query->where($_db->quoteName('nm')  . '.' . $_db->quoteName('mailinglist_id') . " = " . (int)$filter_mailinglist);
						$query->leftJoin($_db->quoteName('#__bwpostman_newsletters_mailinglists') .' AS ' . $_db->quoteName('nm') . ' ON ' . $_db->quoteName('nm') . '.' . $_db->quoteName('newsletter_id') . ' =  ' . $_db->quoteName('a') . '.' . $_db->quoteName('id'));
					}

					// Get the usergroup
					$filter_usergroup = $this->getState('filter.usergroups');

					if ($filter_usergroup != '') {
						$query->where($_db->quoteName('nm')  . '.' . $_db->quoteName('mailinglist_id') . " = " . (int)$filter_usergroup);
						$query->leftJoin($_db->quoteName('#__bwpostman_newsletters_mailinglists') .' AS ' . $_db->quoteName('nm') . ' ON ' . $_db->quoteName('nm') . '.' . $_db->quoteName('newsletter_id') . ' =  ' . $_db->quoteName('a') . '.' . $_db->quoteName('id'));
					}

					// Get the campaign
					$filter_campaign = $this->getState('filter.campaigns');

					if ($filter_campaign != '') {
						$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('campaign_id') . " = " . (int)$filter_campaign);
					}

					// Get the author
					$filter_author = $this->getState('filter.authors');
					if ($filter_author != '') {
						$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('created_by') . " = " . (int)$filter_author);
					}

					// Filter by published state
					$published = $this->getState('filter.published');
					if (is_numeric($published)) {
						$query->where('a.published = ' . (int) $published);
					}
					elseif ($published === '') {
						$query->where('(a.published = 0 OR a.published = 1)');
					}

					// Filter by mailing date
//					$query->where('a.mailing_date' . $tab_int . "'0000-00-00 00:00:00'");
				break;

			case "subscribers":
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

					// Get the status
					$filter_status = $this->getState('filter.status');
					if ($filter_status != '') {
						$query->where('a.status = ' . (int)$filter_status);
					}
				break;

			case "campaigns":
				break;

			case "mailinglists":
					// Get the state
					$filter_published = $this->getState('filter.published');

					if ($filter_published != '') {
						$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('published') . " = " . (int) $filter_published);
					}

					// Get the access level
					$filter_access = $this->getState('filter.access');

					if ($filter_access != '') {
						$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('access') . " = " . (int)$filter_access);
					}
				break;

			case "templates":
					// Get the state
					$filter_published = $this->getState('filter.published');

					if ($filter_published != '') {
						$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('published') . " = " . (int) $filter_published);
					}

					// Filter by format.
					if ($format = $this->getState('filter.tpl_id')) {
						if ($format == '1') {
							$query->where('a.tpl_id < 998');
						}
						if ($format == '2') {
							$query->where('a.tpl_id > 997');
						}
					}
				break;

			default:
				break;
		}

		if (!empty($search)) {
			$search	= '%' . $search . '%';

			// get select list filters
			switch ($layout) { // Which tab are we in?
				case "newsletters":
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
					break;
				case "subscribers":
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
					break;
				case "campaigns":
						switch ($filtersearch) {
							case 'description':
									$query->where('a.description LIKE ' . $_db->Quote($search, false));
								break;
							case 'title_description':
									$query->where('(a.description LIKE ' . $_db->Quote($search, false) . 'OR a.title LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'title':
									$query->where('a.title LIKE ' . $_db->Quote($search, false));
								break;
							default:
						}
					break;
				case "mailinglists":
						switch ($filtersearch) {
							case 'description':
									$query->where('a.description LIKE ' . $_db->Quote($search, false));
								break;
							case 'title_description':
									$query->where('(a.description LIKE ' . $_db->Quote($search, false) . 'OR a.title LIKE ' . $_db->Quote($search, false) . ')');
								break;
							case 'title':
									$query->where('a.title LIKE ' . $_db->Quote($search, false));
								break;
							default:
						}
					break;
				case "templates":
						switch ($filtersearch) {
							case 'description':
									$query->where('a.description LIKE ' . $_db->Quote($search));
								break;
							case 'title_description':
									$query->where('(a.description LIKE ' . $_db->Quote($search) . 'OR a.title LIKE ' . $_db->Quote($search) . ')');
								break;
							case 'title':
									$query->where('a.title LIKE ' . $_db->Quote($search));
								break;
							default:
						}
					break;
			}
			$where_array = array();
		}
		return;
	}

	/**
	 * Method to get the data of a single subscriber for raw view
	 *
	 * @access	public
	 *
	 * @param 	int Subscriber ID
	 *
	 * @return 	object Subscriber
	 */
	public function getSingleSubscriber ($sub_id = null)
	{
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$subQuery1	= $_db->getQuery(true);
		$subQuery2	= $_db->getQuery(true);
		$subQuery3	= $_db->getQuery(true);

		$subQuery1->select($_db->quoteName('u') . '.' . $_db->quoteName('name'));
		$subQuery1->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
		$subQuery1->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('s') . '.' . $_db->quoteName('confirmed_by'));

		$subQuery2->select($_db->quoteName('u') . '.' . $_db->quoteName('name'));
		$subQuery2->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
		$subQuery2->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('s') . '.' . $_db->quoteName('registered_by'));

		$subQuery3->select($_db->quoteName('u') . '.' . $_db->quoteName('name'));
		$subQuery3->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
		$subQuery3->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('s') . '.' . $_db->quoteName('archived_by'));

		$query->select($_db->quoteName('s') . '.' . '*');
		$query->select(' IF(' . $_db->quoteName('s') . '.' . $_db->quoteName('confirmed_by') . ' = ' . (int) 0 . ', "User", (' . $subQuery1 . ' )) AS ' . $_db->quoteName('confirmed_by'));
		$query->select(' IF(' . $_db->quoteName('s') . '.' . $_db->quoteName('registered_by') . ' = ' . (int) 0 . ', "User", (' . $subQuery2 . ' )) AS ' . $_db->quoteName('registered_by'));
		$query->select('(' . $subQuery3 . ') AS ' . $_db->quoteName('archived_by'));
		$query->select(' IF( ' . $_db->quoteName('s') . '.' . $_db->quoteName('emailformat') . ' = ' . (int) 0 . ', "Text", "HTML" ) AS ' . $_db->quoteName('emailformat'));
		$query->from($_db->quoteName('#__bwpostman_subscribers') . ' AS ' . $_db->quoteName('s'));
		$query->where( $_db->quoteName('s') . '.' . $_db->quoteName('id') . ' = ' . (int) $sub_id);

		$_db->setQuery($query);
		$subscriber = $_db->loadObject();

		$query->clear();
		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $sub_id);

		$_db->setQuery($query);

		$mailinglist_id_values = $_db->loadColumn();

		if (!empty($mailinglist_id_values)) {
			$mailinglist_ids = implode (',', $mailinglist_id_values);
		} else {
			$mailinglist_ids = 0;
		}

		$query->clear();
		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('title'));
		$query->select($_db->quoteName('description'));
		$query->select($_db->quoteName('archive_flag'));
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('id') . ' IN  (' . $mailinglist_ids . ')');
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);
		$subscriber->lists = $_db->loadObjectList();

		return $subscriber;
	}

	/**
	 * Method to get the data of a single campaign for raw view
	 *
	 * @access	public
	 *
	 * @param 	int Campaign ID
	 *
	 * @return 	object Campaign
	 */
	public function getSingleCampaign ($cam_id = null)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select('*');
		$query->from($_db->quoteName('#__bwpostman_campaigns'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $cam_id);
		$_db->setQuery($query);

		$campaign = $_db->loadObject();

		// Get all assigned newsletters
		// --> we offer to unarchive not only the campaign but also the assigned newsletters,
		// that's why we have to show also the archived newsletters
		$query->clear();
		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('subject'));
		$query->select($_db->quoteName('campaign_id'));
		$query->select($_db->quoteName('archive_flag'));
		$query->from($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);

		$_db->setQuery($query);
		$campaign->newsletters = $_db->loadObjectList();

		return $campaign;
	}

	/**
	 * Method to get the data of a single Mailinglist for raw view
	 *
	 * @access	public
	 *
	 * @param 	int Mailinglist ID
	 *
	 * @return 	object Mailinglist
	 */
	public function getSingleMailinglist ($ml_id = null)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('a')  . '.' . '*');
		$query->from($_db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $_db->quoteName('a'));
		$query->where($_db->quoteName('a')  . '.' . $_db->quoteName('id') . ' = ' . (int) $ml_id);
		// Join over the asset groups.
		$query->select($_db->quoteName('ag') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('access_level'));
		$query->join('LEFT', $_db->quoteName('#__viewlevels') . ' AS ' . $_db->quoteName('ag') . ' ON ' . $_db->quoteName('ag') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('access'));

		$_db->setQuery($query);
		$mailinglist = $_db->loadObject();

		return $mailinglist;
	}
}
