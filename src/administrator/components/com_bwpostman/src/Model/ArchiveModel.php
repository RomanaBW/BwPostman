<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive model for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use RuntimeException;

/**
 * BwPostman archive model
 * Provides a general view of all archived items
 *
 * @package		BwPostman-Admin
 * @subpackage	Archive
 *
 * @since       0.9.1
 */
class ArchiveModel extends ListModel
{
	/**
	 * Constructor
	 * --> handles the pagination of the single tabs
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		$app    = Factory::getApplication();
		$layout = $app->input->get('layout', 'newsletters');

		if (empty($config['filter_fields']))
		{
			switch ($layout)
			{
				case 'newsletters':
				default:
					$config['filter_fields'] = array(
						'id',
						'a.id',
						'subject',
						'a.subject',
						'mailinglists',
						'a.mailinglists',
						'description',
						'a.description',
						'mailing_date',
						'a.mailing_date',
						'author',
						'a.author',
						'campaigns',
						'a.campaigns',
						'published',
						'a.published',
						'publish_up',
						'a.publish_up',
						'publish_down',
						'a.publish_down',
						'archive_date',
						'a.archive_date',
						'access',
						'a.access',
						'access_level'
					);
					break;
				case 'subscribers':
					$config['filter_fields'] = array(
						'id',
						'a.id',
						'name',
						'a.name',
						'firstname',
						'a.firstname',
						'email',
						'a.email',
						'status',
						'a.status',
						'emailformat',
						'a.emailformat',
						'mailinglists',
						'a.mailinglists',
						'archive_date',
						'a.archive_date',
						'access',
						'a.access',
						'access_level'
					);
					break;
				case 'campaigns':
					$config['filter_fields'] = array(
						'id',
						'a.id',
						'newsletters',
						'a.newsletters',
						'title',
						'a.title',
						'description',
						'a.description',
						'archive_date',
						'a.archive_date',
						'access',
						'a.access',
						'access_level'
					);
					break;
				case 'mailinglists':
					$config['filter_fields'] = array(
						'id',
						'a.id',
						'mailinglists',
						'a.mailinglists',
						'subscribers',
						'a.subscribers',
						'title',
						'a.title',
						'description',
						'a.description',
						'published',
						'a.published',
						'archive_date',
						'a.archive_date',
						'access',
						'a.access',
						'access_level'
					);
					break;
				case 'templates':
					$config['filter_fields'] = array(
						'id',
						'a.id',
						'title',
						'a.title',
						'thumbnail',
						'a.thumbnail',
						'description',
						'a.description',
						'archive_date',
						'a.archive_date',
						'published',
						'a.published',
						'tpl_id',
						'a.tpl_id'
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
	 * @param string $ordering  An optional ordering field.
	 * @param string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Adjust the context to support modal and tabbed layouts.
		$layout = $app->input->get('layout', 'newsletters');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search_nl', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter',
			'filter_search_filter');
		$this->setState('filter.search_filter', $filtersearch);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
		$this->setState('filter.published', $published);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
		$this->setState('filter.status', $status);

		$filter_mailinglist = $this->getUserStateFromRequest($this->context . '.filter.mailinglist',
			'filter_mailinglist');
		$this->setState('filter.mailinglist', $filter_mailinglist);

		$emailformat = $this->getUserStateFromRequest($this->context . '.filter.emailformat', 'filter_emailformat');
		$this->setState('filter.emailformat', $emailformat);

		$tpl_id = $this->getUserStateFromRequest($this->context . '.filter.tpl_id', 'filter_tpl_id');
		$this->setState('filter.tpl_id', $tpl_id);

		switch ($layout)
		{ // Which tab are we in?
			default:
			case "newsletters":
				$orderMainCol = 'a.subject';

				$usergroup = $this->getUserStateFromRequest($this->context . '.filter.usergroups', 'filter_usergroups');
				$this->setState('filter.usergroups', $usergroup);

				$campaign = $this->getUserStateFromRequest($this->context . '.filter.campaigns', 'filter_campaigns');
				$this->setState('filter.campaigns', $campaign);

				$author = $this->getUserStateFromRequest($this->context . '.filter.authors', 'filter_authors', '');
				$this->setState('filter.authors', $author);

				$mailing_date = $this->getUserStateFromRequest($this->context . '.filter.mailing_date',
					'filter_mailing_date', '');
				$this->setState('filter.mailing_date', $mailing_date);
				break;

			case "subscribers":
				$orderMainCol = 'a.name';
				break;

			case "campaigns":
			case "mailinglists":
			case "templates":
				$orderMainCol = 'a.title';
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
	 * @param string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 *
	 * @since    1.0.1
	 */
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.search_filter');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.publish_up');
		$id .= ':' . $this->getState('filter.publish_down');
		$id .= ':' . $this->getState('filter.status');
		$id .= ':' . $this->getState('filter.author');
		$id .= ':' . $this->getState('filter.mailinglists');
		$id .= ':' . $this->getState('filter.usergroups');
		$id .= ':' . $this->getState('filter.campaigns');
		$id .= ':' . $this->getState('filter.mailing_date');
		$id .= ':' . $this->getState('filter.emailformat');
		$id .= ':' . $this->getState('filter.mailinglist');
		$id .= ':' . $this->getState('filter.tpl_id');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @return    false|QueryInterface Query
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function getListQuery()
	{
		// Set some shortcuts
		$db        = $this->_db;
		$pef_tbl_a = $db->quoteName('a');
		$pef_tbl_b = $db->quoteName('b');
		$pef_tbl_c = $db->quoteName('c');
		$pef_tbl_d = $db->quoteName('d');
		$pef_tbl_u = $db->quoteName('u');

		$query      = $db->getQuery(true);
		$sub_query  = $db->getQuery(true);
		$sub_query2 = $db->getQuery(true);
		$jinput     = Factory::getApplication()->input;
		$layout     = $jinput->get('layout', 'newsletters');

		switch ($layout)
		{ // We are in the newsletters_tab
			default:
			case "newsletters":
				$orderMainCol = 'subject';

				$query->select($pef_tbl_a . '.*');
				$query->select($pef_tbl_u . '.' . $db->quoteName('name') . ' AS ' . $db->quoteName('author'));
				$query->select($pef_tbl_c . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('campaigns'));
				$query->select($pef_tbl_c . '.' . $db->quoteName('archive_flag') . ' AS ' . $db->quoteName('campaign_archive_flag'));
				$query->from($db->quoteName('#__bwpostman_newsletters') . ' AS ' . $pef_tbl_a);
				$query->leftJoin(
					$db->quoteName('#__users') . ' AS ' . $pef_tbl_u . ' ON ' .
					$pef_tbl_u . '.' . $db->quoteName('id') . ' =  ' . $pef_tbl_a . '.' . $db->quoteName('created_by')
				);
				$query->leftJoin(
					$db->quoteName('#__bwpostman_campaigns') . ' AS ' . $pef_tbl_c . ' ON ' .
					$pef_tbl_c . '.' . $db->quoteName('id') . ' =  ' . $pef_tbl_a . '.' . $db->quoteName('campaign_id')
				);
				break;

			// We are in the subscribers_tab
			case "subscribers":
				$orderMainCol = 'name';

				// Build sub query which counts all subscribed mailinglists of each subscriber
				$sub_query2->select($pef_tbl_d . '.' . $db->quoteName('id'));
				$sub_query2->from($db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $pef_tbl_d);
				$sub_query2->where($pef_tbl_d . '.' . $db->quoteName('archive_flag') . " = " . 0);

				$sub_query->select(
					'COUNT(' . $pef_tbl_b . '.' . $db->quoteName('mailinglist_id') . ') AS ' . $db->quoteName('mailinglists')
				);
				$sub_query->from($db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS ' . $pef_tbl_b);
				$sub_query->where(
					$pef_tbl_b . '.' . $db->quoteName('subscriber_id') . " = " . $pef_tbl_a . '.' . $db->quoteName('id')
				);
				$sub_query->where($pef_tbl_b . '.' . $db->quoteName('mailinglist_id') . " IN (" . $sub_query2 . ')');

				$query->select($pef_tbl_a . '.' . "*, IF (emailformat = '1','HTML','TEXT')" . ' AS ' . $db->quoteName('emailformat'));
				$query->select('(' . $sub_query . ') AS ' . $db->quoteName('mailinglists'));
				$query->from($db->quoteName('#__bwpostman_subscribers') . ' AS ' . $pef_tbl_a);
				break;

			// We are in the campaigns_tab and we want to show all assigned newsletters
			// because we offer the option to unarchive not only the campaign but also the
			// assigned newsletters
			case "campaigns":
				$orderMainCol = 'title';

				// Build sub query which counts all newsletters of each campaign
				$sub_query->select('COUNT(' . $db->quoteName('n') . '.' . $db->quoteName('id') . ') AS ' . $db->quoteName('newsletters'));
				$sub_query->from($db->quoteName('#__bwpostman_newsletters') . ' AS ' . $db->quoteName('n'));
				$sub_query->where(
					$db->quoteName('n') . '.' . $db->quoteName('campaign_id') . " = " . $pef_tbl_a . '.' . $db->quoteName('id')
				);

				$query->select($pef_tbl_a . '.*');
				$query->select('(' . $sub_query . ') AS ' . $db->quoteName('newsletters'));
				$query->from($db->quoteName('#__bwpostman_campaigns') . ' AS ' . $pef_tbl_a);
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
				$orderMainCol = 'title';

				// Build sub query which counts all subscribers of each mailinglist
				$sub_query2->select($pef_tbl_d . '.' . $db->quoteName('id'));
				$sub_query2->from($db->quoteName('#__bwpostman_subscribers') . ' AS ' . $pef_tbl_d);
				$sub_query2->where($pef_tbl_d . '.' . $db->quoteName('archive_flag') . " = " . 0);

				$sub_query->select(
					'COUNT(' . $pef_tbl_b . '.' . $db->quoteName('subscriber_id') . ') AS ' . $db->quoteName('subscribers')
				);
				$sub_query->from($db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS ' . $pef_tbl_b);
				$sub_query->where(
					$pef_tbl_b . '.' . $db->quoteName('mailinglist_id') . " = " . $pef_tbl_a . '.' . $db->quoteName('id')
				);
				$sub_query->where($pef_tbl_b . '.' . $db->quoteName('subscriber_id') . " IN (" . $sub_query2 . ')');

				$query->select($pef_tbl_a . '.*');
				$query->select('(' . $sub_query . ') AS ' . $db->quoteName('subscribers'));
				$query->from($db->quoteName('#__bwpostman_mailinglists') . ' AS ' . $pef_tbl_a);

				// Join over the asset groups.
				$query->select($db->quoteName('ag') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('access_level'));
				$query->join(
					'LEFT', $db->quoteName('#__viewlevels') . ' AS '  . $db->quoteName('ag') . ' ON '
					. $db->quoteName('ag') . '.'  . $db->quoteName('id')  . '=' . $db->quoteName('a') . '.' . $db->quoteName('access')
				);
				break;

			// We are in the templates_tab
			case "templates":
				$orderMainCol = 'title';

				$query->select($pef_tbl_a . '.' . $db->quoteName('id'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('title'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('description'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('thumbnail'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('published'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('checked_out'));
				$query->select($pef_tbl_a . '.' . $db->quoteName('archive_date'));
				$query->select(
					$pef_tbl_a . '.' . "tpl_id, IF (tpl_id = '998' OR tpl_id > '999','TEXT','HTML')"
					. ' AS ' . $db->quoteName('tpl_id')
				);
				$query->select($pef_tbl_a . '.' . $db->quoteName('created_by'));
				$query->from($db->quoteName('#__bwpostman_templates') . ' AS ' . $pef_tbl_a);
				break;
		}

		// Filter by access level.
		$access = $this->getState('filter.access');

		if ($access)
		{
			$query->where($pef_tbl_a . '.' . $db->quoteName('access') . ' = ' . (int) $access);
		}

		$query->where($pef_tbl_a . '.' . $db->quoteName('archive_flag') . ' = ' . 1);

		// Get the WHERE clause and ORDER-BY clause for the query
		$this->buildQueryWhere($layout, $query);

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}

		if (($orderCol == '') || ($orderCol == 'a.' . $orderMainCol))
		{
			$query->order($pef_tbl_a . '.' . $db->quoteName($orderMainCol) . ' ' . $orderDirn);
		}
		else
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn) . ', ' . $pef_tbl_a . '.' . $db->quoteName($orderMainCol));
		}

		try
		{
			$db->setQuery($query);
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'ArchiveModel BE');

            Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_GET_LIST_QUERY_ERROR'), 'error');
			return false;
		}

		return $query;
	}

	/**
	 * Method to build the WHERE clause
	 *
	 * @access      protected
	 *
	 * @param string     $layout selected layout
	 * @param object    &$query  query to inject the where clause
	 *
	 * @since       0.9.1
	 */
	protected function buildQueryWhere(string $layout, object &$query)
	{
		$db = $this->_db;
		$pef_tbl_a = $db->quoteName('a');
		$pef_tbl_c = $db->quoteName('c');

		// Get the search string
		$filtersearch = $this->getState('filter.search_filter');
		$search       = $db->escape($this->getState('filter.search'));

		// get select list filters
		switch ($layout)
		{ // Which tab are we in?
			case "newsletters":
				// Get the mailinglist
				$filter_mailinglist = $this->getState('filter.mailinglists');

				if ($filter_mailinglist != '')
				{
					$query->where($db->quoteName('nm') . '.' . $db->quoteName('mailinglist_id') . " = " . (int) $filter_mailinglist);
					$query->leftJoin(
						$db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $db->quoteName('nm') . ' ON '
						. $db->quoteName('nm') . '.' . $db->quoteName('newsletter_id') . ' =  ' . $pef_tbl_a . '.' . $db->quoteName('id')
					);
				}

				// Get the usergroup
				$filter_usergroup = $this->getState('filter.usergroups');

				if ($filter_usergroup != '')
				{
					$query->where($db->quoteName('nm') . '.' . $db->quoteName('mailinglist_id') . " = " . (int) $filter_usergroup);
					$query->leftJoin(
						$db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $db->quoteName('nm') . ' ON '
						. $db->quoteName('nm') . '.' . $db->quoteName('newsletter_id') . ' =  ' . $pef_tbl_a . '.' . $db->quoteName('id')
					);
				}

				// Get the campaign
				$filter_campaign = $this->getState('filter.campaigns');

				if ($filter_campaign != '')
				{
					$query->where($pef_tbl_a . '.' . $db->quoteName('campaign_id') . " = " . (int) $filter_campaign);
				}

				// Get the author
				$filter_author = $this->getState('filter.authors');
				if ($filter_author != '')
				{
					$query->where($pef_tbl_a . '.' . $db->quoteName('created_by') . " = " . (int) $filter_author);
				}

				// Filter by published state
				$published = $this->getState('filter.published');

				if (is_numeric($published))
				{
					$query->where('a.published = ' . (int) $published);
				}
				elseif ($published === '')
				{
					$query->where('(a.published = 0 OR a.published = 1)');
				}
				break;

			case "subscribers":
				// Filter by mailinglist
				$mailinglist = $this->getState('filter.mailinglist');

				if ($mailinglist)
				{
					$sub_query2 = $db->getQuery(true);

					$sub_query2->select($pef_tbl_c . '.' . $db->quoteName('subscriber_id'));
					$sub_query2->from($db->quoteName('#__bwpostman_subscribers_mailinglists') . 'AS ' . $pef_tbl_c);
					$sub_query2->where($pef_tbl_c . '.' . $db->quoteName('mailinglist_id') . ' = ' . (int) $mailinglist);

					$query->where('a.id IN (' . $sub_query2 . ')');
				}

				// Filter by emailformat.
				$emailformat = $this->getState('filter.emailformat');

				if ($emailformat != '')
				{
					$query->where('a.emailformat = ' . (int) $emailformat);
				}

				// Get the status
				$filter_status = $this->getState('filter.status');

				if ($filter_status != '')
				{
					$query->where('a.status = ' . (int) $filter_status);
				}
				break;

			case "mailinglists":
				// Get the state
				$filter_published = $this->getState('filter.published');

				if ($filter_published != '')
				{
					$query->where($pef_tbl_a . '.' . $db->quoteName('published') . " = " . (int) $filter_published);
				}

				// Get the access level
				$filter_access = $this->getState('filter.access');

				if ($filter_access != '')
				{
					$query->where($pef_tbl_a . '.' . $db->quoteName('access') . " = " . (int) $filter_access);
				}
				break;

			case "templates":
				// Get the state
				$filter_published = $this->getState('filter.published');

				if ($filter_published != '')
				{
					$query->where($pef_tbl_a . '.' . $db->quoteName('published') . " = " . (int) $filter_published);
				}

				// Filter by format.
				$format = $this->getState('filter.tpl_id');

				if ($format)
				{
					if ($format == '1')
					{
						$query->where('a.tpl_id < 998');
					}

					if ($format == '2')
					{
						$query->where('a.tpl_id > 997');
					}
				}
				break;

			case "campaigns":
			default:
				break;
		}

		if (!empty($search))
		{
			$search = '%' . $search . '%';

			// get select list filters
			switch ($layout)
			{ // Which tab are we in?
				case "newsletters":
					switch ($filtersearch)
					{
						case 'subject':
							$query->where('a.subject LIKE ' . $db->quote($search));
							break;
						case 'description':
							$query->where('a.description LIKE ' . $db->quote($search));
							break;
						case 'subject_description':
							$query->where(
								'(a.subject LIKE ' . $db->quote($search) . 'OR a.description LIKE ' . $db->quote($search,
									false) . ')'
							);
							break;
						case 'html_text_version':
							$query->where(
								'(a.html_version LIKE ' . $db->quote($search, false) . 'OR a.text_version LIKE '
								. $db->quote($search, false) . ')'
							);
							break;
						case 'text_version':
							$query->where('a.text_version LIKE ' . $db->quote($search, false));
							break;
						case 'html_version':
							$query->where('a.html_version LIKE ' . $db->quote($search, false));
							break;
						default:
					}
					break;
				case "subscribers":
					switch ($filtersearch)
					{
						case 'email':
							$query->where('a.email LIKE ' . $db->quote($search, false));
							break;
						case 'name_email':
							$query->where(
								'(a.email LIKE ' . $db->quote($search,
									false) . 'OR a.name LIKE ' . $db->quote($search, false) . ')'
							);
							break;
						case 'fullname':
							$query->where(
								'(a.firstname LIKE ' . $db->quote($search,
									false) . 'OR a.name LIKE ' . $db->quote($search, false) . ')'
							);
							break;
						case 'firstname':
							$query->where('a.firstname LIKE ' . $db->quote($search, false));
							break;
						case 'name':
							$query->where('a.name LIKE ' . $db->quote($search, false));
							break;
						default:
					}
					break;
				case "campaigns":
				case "mailinglists":
					switch ($filtersearch)
					{
						case 'description':
							$query->where('a.description LIKE ' . $db->quote($search, false));
							break;
						case 'title_description':
							$query->where(
								'(a.description LIKE ' . $db->quote($search,
									false) . 'OR a.title LIKE ' . $db->quote($search, false) . ')'
							);
							break;
						case 'title':
							$query->where('a.title LIKE ' . $db->quote($search, false));
							break;
						default:
					}
					break;
				case "templates":
					switch ($filtersearch)
					{
						case 'description':
							$query->where('a.description LIKE ' . $db->quote($search));
							break;
						case 'title_description':
							$query->where('(a.description LIKE ' . $db->quote($search) . 'OR a.title LIKE ' . $db->quote($search) . ')');
							break;
						case 'title':
							$query->where('a.title LIKE ' . $db->quote($search));
							break;
						default:
					}
					break;
			}
		}
	}

	/**
	 * Get the filter form
	 *
	 * @param array   $data     data
	 * @param boolean $loadData load current data
	 * @param string  $layout   the tab we are in
	 *
	 * @return  Form|null  The \JForm object or null if the form can't be found
	 *
	 * @throws Exception
	 *
	 * @since   4.3.0
	 */
	public function getFilterForm($data = [], $loadData = true, $layout = 'newsletters')
	{
		$this->filterFormName = 'filter_archive_' . $layout;

		if (empty($this->filterFormName) || !file_exists(BWPM_ADMINISTRATOR . '/forms/' . $this->filterFormName . '.xml'))
		{
			return null;
		}

		try
		{
			// Get the form.
			return $this->loadForm($this->context . '.filter', $this->filterFormName, ['control' => '', 'load_data' => $loadData]);
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'ArchiveModel BE');
        }

		return null;
	}
}
