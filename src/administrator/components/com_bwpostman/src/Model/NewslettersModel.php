<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists model for backend.
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\Database\QueryInterface;
use RuntimeException;

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
class NewslettersModel extends ListModel
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
	 * @throws Exception
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
				'is_template', 'a.is_template',
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
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

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

		$is_template= $this->getUserStateFromRequest($this->context . '.filter.is_template', 'filter_is_template', '');
		$this->setState('filter.is_template', $is_template);

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
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.search_filter');
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.publish_up');
		$id	.= ':' . $this->getState('filter.publish_down');
		$id	.= ':' . $this->getState('filter.authors');
		$id	.= ':' . $this->getState('filter.campaign_id');
		$id	.= ':' . $this->getState('filter.mailinglists');
		$id	.= ':' . $this->getState('filter.usergroups');
		$id	.= ':' . $this->getState('filter.description');
		$id	.= ':' . $this->getState('filter.is_template');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @return    false|QueryInterface|object Query
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function getListQuery()
	{
		$db          = $this->getDatabase();
		$jinput      = Factory::getApplication()->input;
		$this->query = $db->getQuery(true);

		//Get the tab in which we are for correct query
		$tab = $jinput->get('tab', 'unsent');

		switch ($tab)
		{
			case ("unsent"):
			case ("sent"):
			default:
					$this->query->select(
						$this->getState(
							'list.select',
							'a.id, a.subject, a.attachment, a.description, a.checked_out, a.checked_out_time' .
							', a.published, a.publish_up, a.publish_down, a.created_date, a.created_by, a.modified_time' .
							', a.is_template'
						)
					);
					$this->query->select($db->quoteName('a') . '.' . $db->quoteName('mailing_date'));
					$this->query->select($db->quoteName('a') . '.' . $db->quoteName('description'));
					$this->query->select($db->quoteName('c') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('campaign_id'));

					$this->query->from($db->quoteName('#__bwpostman_newsletters') . 'AS a');
				break;

			case ("queue"):
					$this->query->select('DISTINCT(' . $db->quoteName('sc') . '.' . $db->quoteName('nl_id') . ')');
					$this->query->select($db->quoteName('sc') . '.' . $db->quoteName('subject') . ' AS subject');
					$this->query->select($db->quoteName('q') . '.' . $db->quoteName('id'));
					$this->query->select($db->quoteName('q') . '.' . $db->quoteName('recipient'));
					$this->query->select($db->quoteName('q') . '.' . $db->quoteName('trial'));
					$this->query->select($db->quoteName('n') . '.' . $db->quoteName('description'));
					$this->query->select($db->quoteName('ua') . '.' . $db->quoteName('name') . ' AS ' . $db->quoteName('authors'));
					$this->query->select($db->quoteName('c') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('campaign_id'));

					$this->query->from($db->quoteName('#__bwpostman_sendmailcontent','sc'));
				break;
		}

		$this->getQueryJoins($tab);
		$this->getQueryWhere($tab);
		$this->getQueryOrder($tab);

		try
		{
			$db->setQuery($this->query);
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NewslettersModel BE');

			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_GET_LIST_QUERY_ERROR'), 'error');
			return false;
		}

		return $this->query;
	}

	/**
	 * Method to get the joins this query needs
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getQueryJoins(string $tab)
	{
		$db = $this->getDatabase();

		if ($tab == 'sent' || $tab == 'unsent')
		{
			// join over campaigns
			$this->query->leftJoin(
				$db->quoteName('#__bwpostman_campaigns', 'c') .
				' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.campaign_id')
			);

			// Join over the users for the checked out user.
			$this->query->select($db->quoteName('uc.name') . ' AS editor');
			$this->query->join(
				'LEFT',
				$db->quoteName('#__users', 'uc') . ' ON ' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out')
			);

			// Join over the users for the author.
			$this->query->select($db->quoteName('ua.name') . ' AS authors');
			$this->query->join(
				'LEFT',
				$db->quoteName('#__users', 'ua') . ' ON ' . $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by')
			);
		}
		elseif ($tab == 'queue')
		{
			$this->query->rightJoin(
				$db->quoteName('#__bwpostman_sendmailqueue', 'q') .
				' ON ' . $db->quoteName('q.content_id') . ' = ' . $db->quoteName('sc.id')
			);
			$this->query->leftJoin(
				$db->quoteName('#__bwpostman_newsletters', 'n') .
				' ON ' . $db->quoteName('n.id') . ' = ' . $db->quoteName('sc.nl_id')
			);
			$this->query->leftJoin(
				$db->quoteName('#__users', 'ua') .
				' ON ' . $db->quoteName('ua.id') . ' = ' . $db->quoteName('n.created_by')
			);
			$this->query->leftJoin(
				$db->quoteName('#__bwpostman_campaigns', 'c') .
				' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('n.campaign_id')
			);
		}
	}

	/**
	 * Method to build the MySQL query 'where' part
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getQueryWhere(string $tab)
	{
		$this->getFilterByAccessLevelFilter();
		$this->getFilterByViewLevel();
//		$this->getFilterByComponentPermissions();
		$this->getFilterByCampaign($tab);
		$this->getFilterByAuthor($tab);
		$this->getFilterByIsTemplate($tab);
		$this->getFilterBySearchword($tab);

		if ($tab == 'sent' || $tab == 'unsent')
		{
			$this->getFilterByPublishedState();
			$this->getFilterByArchiveState();
			$this->getFilterByMailinglist();
			$this->getFilterByUsergroup();
			$this->getFilterByMailingDate($tab);
		}
	}

	/**
	 * Method to build the MySQL query 'order' part
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getQueryOrder(string $tab)
	{
		$db        = $this->getDatabase();
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

			if ($orderCol == 'is_template')
			{
				$orderCol = 'a.is_template';
			}
		}
		elseif ($tab == 'queue')
		{
			$orderCol = str_replace('a.', 'sc.', $orderCol);
		}

		$this->query->order($db->quoteName($db->escape($orderCol)) . ' ' . $db->escape($orderDirn));
	}

	/**
	 * Method to get the filter by access level
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByAccessLevelFilter()
	{
		$db = $this->getDatabase();

		if (Factory::getApplication()->isClient('site'))
		{
			$access = $this->getState('filter.access');

			if ($access)
			{
				$this->query->where($db->quoteName('a.access') . ' = ' . (int) $access);
			}
		}
	}

	/**
	 * Method to get the filter by Joomla view level
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByViewLevel()
	{
		$db = $this->getDatabase();

		if (Factory::getApplication()->isClient('site'))
		{
			$user = Factory::getApplication()->getIdentity();

			if (!$user->authorise('core.admin'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$this->query->where($db->quoteName('a.access') . ' IN (' . $groups . ')');
			}
		}
	}

	/**
	 * Method to get the filter by BwPostman permissions
	 *
	 * @return    void
	 *
	 * @throws Exception
	 *
	 * @since     2.0.0
	 */
//	private function getFilterByComponentPermissions()
//	{
//		$db            = $this->getDatabase();
//		$allowed_items = BwPostmanHelper::getAllowedRecords('newsletter');
//
//		if ($allowed_items != 'all')
//		{
//			$allowed_ids = implode(',', $allowed_items);
//			$this->query->where($db->quoteName('a.id') . ' IN (' . $allowed_ids . ')');
//		}
//	}

	/**
	 * Method to get the filter by selected campaign
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByCampaign(string $tab)
	{
		$campaign = $this->getState('filter.campaign_id');

		if ($campaign)
		{
			if ($tab == 'queue')
			{
				$this->query->where('n.campaign_id = ' . (int) $campaign);
			}
			else
			{
				$this->query->where('a.campaign_id = ' . (int) $campaign);
			}
		}
	}

	/**
	 * Method to get the filter by selected campaign
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByIsTemplate(string $tab)
	{
		$isTemplate = $this->getState('filter.is_template');

		if ($isTemplate !== "")
		{
			if ($tab == 'unsent')
			{
				$this->query->where('a.is_template = ' . (int) $isTemplate);
			}
		}
	}

	/**
	 * Method to get the filter by selected author
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByAuthor(string $tab)
	{
		$authors = $this->getState('filter.authors');

		if ($authors)
		{
			if ($tab == 'queue')
			{
				$this->query->where('n.created_by = ' . (int) $authors);
			}
			else
			{
				$this->query->where('a.created_by = ' . (int) $authors);
			}
		}
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterBySearchword(string $tab)
	{
		$db           = $this->getDatabase();
		$filtersearch = $this->getState('filter.search_filter');
		$search       = $db->escape($this->getState('filter.search'), true);

		if (!empty($search))
		{
			$search	= '%' . $search . '%';

			switch ($filtersearch)
			{
				case 'subject':
					if($tab == 'queue')
					{
						$this->query->where($db->quoteName('c.subject') . ' LIKE ' . $db->quote($search));
					}
					else
					{
						$this->query->where($db->quoteName('a.subject') . ' LIKE ' . $db->quote($search));
					}
					break;
				case 'description':
					if($tab == 'queue')
					{
						$this->query->where($db->quoteName('n.description') . ' LIKE ' . $db->quote($search));
					}
					else
					{
						$this->query->where($db->quoteName('a.description') . ' LIKE ' . $db->quote($search));
					}
					break;
				case 'subject_description':
					if($tab == 'queue')
					{
						$this->query->where(
							'(' . $db->quoteName('c.subject') . ' LIKE ' . $db->quote($search) .
							' OR ' . $db->quoteName('n.description') . ' LIKE ' . $db->quote($search, false) . ')'
						);
					}
					else
					{
						$this->query->where(
							'(' . $db->quoteName('a.subject') . ' LIKE ' . $db->quote($search) .
							' OR ' . $db->quoteName('a.description') . ' LIKE ' . $db->quote($search, false) . ')'
						);
					}
					break;
				case 'html_text_version':
					if ($tab == 'unsent' || $tab == 'sent')
					{
						$this->query->where(
							'(' . $db->quoteName('a.html_version') . ' LIKE ' . $db->quote($search, false) .
							' OR ' . $db->quoteName('a.text_version') . ' LIKE ' . $db->quote($search, false) . ')'
						);
					}
					elseif ($tab == 'queue')
					{
						$this->query->where(
							'(' . $db->quoteName('a.html_version') . ' LIKE ' . $db->quote($search, false) .
							'OR ' . $db->quoteName('q.text_version') . ' LIKE ' . $db->quote($search, false) . ')'
						);
					}
					break;
				case 'text_version':
					$this->query->where($db->quoteName('a.text_version') . ' LIKE ' . $db->quote($search, false));
					break;
				case 'html_version':
					$this->query->where($db->quoteName('a.html_version') . ' LIKE ' . $db->quote($search, false));
					break;
				default:
			}
		}
	}

	/**
	 * Method to get the filter by published state
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByPublishedState()
	{
		// Define null and now dates, get params
		$db       = $this->getDatabase();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			switch ($published)
			{
				case 0:
				case 1:
				default:
					$this->query->where($db->quoteName('a.published') . ' = ' . (int) $published);
					break;
				case 2:
					$this->query->where($db->quoteName('a.publish_down') . ' <> ' . $nullDate);
					$this->query->where($db->quoteName('a.publish_down') . ' IS NOT NOLL');
					$this->query->where($db->quoteName('a.publish_down') . ' <= ' . $nowDate);
					break;
				case 3:
					$this->query->where($db->quoteName('publish_down') . ' >= ' . $nowDate
						. ' OR publish_down = ' . $nullDate
						. ' OR publish_down IS NULL)');
					break;
				case 4:
					$this->query->where($db->quoteName('a.publish_up') . ' <= ' . $nowDate);
					$this->query->where($db->quoteName('a.publish_down') . ' <> ' . $nullDate);
					$this->query->where($db->quoteName('a.publish_down') . ' IS NOT NOLL');
					$this->query->where($db->quoteName('a.publish_down') . ' > ' . $nowDate);
					break;
				case 5:
					$this->query->where($db->quoteName('a.publish_up') . ' > ' . $nowDate);
					break;
			}
		}
		elseif ($published === '')
		{
			$this->query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
		}
	}


	/**
	 * Method to get the filter by archived state
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByArchiveState()
	{
		$db = $this->getDatabase();

		$this->query->where($db->quoteName('a.archive_flag') . ' = ' . 0);
	}

	/**
	 * Method to get the filter by selected mailinglist
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByMailinglist()
	{
		$mailinglist = $this->getState('filter.mailinglists');

		if ($mailinglist)
		{
			$this->query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
			$this->query->where('m.mailinglist_id = ' . (int) $mailinglist);
		}
	}

	/**
	 * Method to get the filter by selected usergroup
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByUsergroup()
	{
		$usergroup = $this->getState('filter.usergroups');

		if ($usergroup)
		{
			$this->query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');
			$this->query->where('m.mailinglist_id = ' . -(int) $usergroup);
		}
	}

	/**
	 * Method to get the filter by mailingdate
	 *
	 * @param string $tab
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByMailingDate(string $tab)
	{
		switch ($tab)
		{
			case ("unsent"):
			case ("queue"):
			default:
				$comparator1 = ' = ';
				$comparator2 = ' IS ';
				$glue        = ' OR ';
				break;
			case ("sent"):
				$comparator1 = ' <> ';
				$comparator2 = ' IS NOT ';
				$glue        = ' AND ';
				break;
		}

		$this->query->where('(a.mailing_date' . $comparator1 . $this->getDatabase()->quote($this->getDatabase()->getNullDate())
			. $glue . 'a.mailing_date' . $comparator2 . ' NULL)');
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  Pagination  A Pagination object for the data set.
	 *
	 * @throws Exception
	 *
	 * @since   1.6
	 */
	public function getQueuePagination(): Pagination
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
		$this->cache[$store] = new Pagination($this->getCountQueue(), $this->getStart(), $limit);

		return $this->cache[$store];
	}

	/**
	/**
	 * Method to count the number of queued records
	 *
	 * @return 	int count Queue-data
	 *
	 * @throws Exception
	 *
	 * @since  0.9.1
	 */
	public function getCountQueue(): int
	{
		$count_queue = 0;
		$db          = $this->getDatabase();
		$query       = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_sendmailqueue'));

		try
		{
			$db->setQuery($query);

			$count_queue = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NewslettersModel BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $count_queue;
	}

	/**
	 * Get the filter form
	 *
	 * @param array   $data     data
	 * @param boolean $loadData load current data
	 *
     * returns  The \JForm object or null if the form can't be found
	 *
	 * @throws Exception
	 *
	 * @since   4.3.0
	 */
	public function getFilterForm($data = [], $loadData = true)
	{
		$layout = Factory::getApplication()->input->get('tab', 'unsent');
		$this->filterFormName = 'filter_newsletters_' . $layout;

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
            BwPostmanHelper::logException($exception, 'NewslettersModel BE');
        }

		return null;
	}
}
