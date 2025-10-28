<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns lists model for backend.
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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * BwPostman campaigns model
 * Provides a general view of all campaigns
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Campaigns
 *
 * @since       0.9.1
 */
class CampaignsModel extends ListModel
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
	 * --> handles the pagination and set the campaigns key
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
				'title', 'a.title',
				'description', 'a.description',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'newsletters', 'a.newsletters',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
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
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

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
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.search_filter');
		$id	.= ':' . $this->getState('filter.title');
		$id	.= ':' . $this->getState('filter.description');
		$id	.= ':' . $this->getState('filter.newsletters');
		$id	.= ':' . $this->getState('filter.access');
		$id	.= ':' . $this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @return    false|object|QueryInterface      query
	 *
	 * @throws Exception
	 *
	 * @since   0.9.1
	 */
	protected function getListQuery()
	{
		$this->query = $this->_db->getQuery(true);
		$sub_query   = $this->getSubQuery();

		// Select the required fields from the table.
		$this->query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.description, a.checked_out, a.checked_out_time' .
				', a.published, a.access, a.created_date, a.created_by'
			) . ', (' . $sub_query . ') AS newsletters'
		);
		$this->query->from($this->_db->quoteName('#__bwpostman_campaigns', 'a'));

		$this->getQueryJoins();
		$this->getQueryWhere();
		$this->getQueryOrder();

		try
		{
			$this->_db->setQuery($this->query);
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'CampaignsModel BE');

            Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_GET_LIST_QUERY_ERROR'), 'error');
			return false;
		}

		return $this->query;
	}

	/**
	 * Method to get the subquery this query needs
	 * This subquery counts the newsletters of each campaign
	 *
	 * @return QueryInterface
	 *
	 * @since   2.0.0
	 */
	private function getSubQuery(): QueryInterface
	{
		$sub_query = $this->_db->getQuery(true);

		$sub_query->select('COUNT(' . $this->_db->quoteName('b.id') . ') AS ' . $this->_db->quoteName('newsletters'));
		$sub_query->from($this->_db->quoteName('#__bwpostman_newsletters') . 'AS ' . $this->_db->quoteName('b'));
		$sub_query->where($this->_db->quoteName('b.archive_flag') . ' = ' . 0);
		$sub_query->where($this->_db->quoteName('b.campaign_id') . ' = ' . $this->_db->quoteName('a.id'));

		return $sub_query;
	}

	/**
	 * Method to get the joins this query needs
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

		// Join over the asset groups.
		$this->query->select($this->_db->quoteName('ag.title') . ' AS access_level');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__viewlevels', 'ag') . ' ON ' . $this->_db->quoteName('ag.id') . ' = ' . $this->_db->quoteName('a.access')
		);

		// Join over the users for the author.
		$this->query->select($this->_db->quoteName('ua.name'), ' AS author_name');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__users', 'ua') . ' ON ' . $this->_db->quoteName('ua.id') . ' = ' . $this->_db->quoteName('a.created_by')
		);
	}

	/**
	 * Method to build the MySQL query 'where' part
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
		$this->getFilterByPublishedState();
		$this->getFilterByArchiveState();
		$this->getFilterBySearchword();
	}

	/**
	 * Method to build the MySQL query 'order' part
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
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByAccessLevelFilter()
	{
		if (Factory::getApplication()->isClient('site'))
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
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByViewLevel()
	{
		if (Factory::getApplication()->isClient('site'))
		{
			$user = Factory::getApplication()->getIdentity();

			if (!$user->authorise('core.admin'))
			{
				$groups = $user->getAuthorisedViewLevels();
				$groups = implode(',', ArrayHelper::toInteger($groups));
				$this->query->where($this->_db->quoteName('a.access') . ' IN (' . $groups . ')');
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
//		$allowed_items  = BwPostmanHelper::getAllowedRecords('campaign');
//
//		if ($allowed_items != 'all')
//		{
//			$allowed_ids = implode(',', ArrayHelper::toInteger($allowed_items));
//			$this->query->where($this->_db->quoteName('a.id') . ' IN (' . $allowed_ids . ')');
//		}
//	}

	/**
	 * Method to get the filter by published state
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByPublishedState()
	{
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$this->query->where($this->_db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$this->query->where('(' . $this->_db->quoteName('a.published') . ' = 0 OR ' . $this->_db->quoteName('a.published') . ' = 1)');
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
		$this->query->where($this->_db->quoteName('a.archive_flag') . ' = ' . 0);
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterBySearchword()
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search       = '%' . $this->_db->escape($this->getState('filter.search'), true) . '%';

		if (!empty($search))
		{
			switch ($filtersearch)
			{
				case 'description':
					$this->query->where($this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				case 'title_description':
					$this->query->where(
						'(' . $this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false) .
						' OR ' . $this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false) . ')'
					);
					break;
				case 'title':
					$this->query->where($this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				default:
			}
		}
	}
}
