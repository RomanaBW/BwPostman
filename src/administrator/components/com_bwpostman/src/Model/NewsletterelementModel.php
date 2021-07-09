<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman model for a backend element to select a single newsletter for a view in frontend.
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

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\String\StringHelper;
use Joomla\CMS\Pagination\Pagination;

/**
 * BwPostman newsletterelement model
 * Provides a view of single newsletters
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletterelement
 *
 * @since
 */
class NewsletterelementModel extends BaseDatabaseModel
{

	/**
	 * Newsletters data
	 *
	 * @var array
	 *
	 * @since
	 */
	private $data = null;

	/**
	 * Number of all newsletters
	 *
	 * @var integer
	 *
	 * @since
	 */
	private $total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 *
	 * @since
	 */
	private $pagination = null;

	/**
	 * Newsletters search
	 *
	 * @var string
	 *
	 * @since
	 */
	private $search = null;

	/**
	 * Mailinglists key
	 * --> we need this as identifier for the different mailinglists filters (e.g. filter_order, state, search ...)
	 * --> value will be "mailinglists"
	 *
	 * @var	string
	 *
	 * @since
	 */
	private $key;

	/**
	 * Constructor
	 * --> handles the pagination and set the mailinglists key
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function __construct()
	{
		parent::__construct();

		$app = Factory::getApplication();

		$this->key = $this->getName();

		// Get the pagination request variables
		$limit      = $app->getUserStateFromRequest($this->key . '_limit', 'limit', $app->get('list_limit'), 0);
		$limitstart = $app->getUserStateFromRequest($this->key . '_limitstart', 'limitstart', 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the mailinglists data
	 *
	 * @return 	array Mailinglists-data
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getData(): array
	{
		if (empty($this->data))
		{
			$query      = $this->buildQuery();
			$this->data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->data;
	}

	/**
	 * Method to get the total number of mailinglists that shall be displayed
	 *
	 * @return 	int Total number
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getTotal(): int
	{
		// Load the content if it doesn't already exist
		if (!$this->total)
		{
			$query       = $this->buildQuery();
			$this->total = $this->_getListCount($query);
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the mailinglists view
	 *
	 * @return 	object Pagination
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->pagination))
		{
			$this->pagination = new Pagination($this->getTotal(), (int) $this->getState('limitstart'), (int) $this->getState('limit'));
		}

		return $this->pagination;
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @return 	string Query
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function buildQuery(): string
	{
		$app   = Factory::getApplication();
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// Build the query
		$query->select('a.id, a.subject, a.description,  a.mailing_date, a.published, a.archive_flag');
		$query->from('#__bwpostman_newsletters AS a');

		// Filter by published state
		$query->where('a.published != ' . 0);
		$query->where($db->quoteName('a.mailing_date') . ' != ' . $db->quote($db->getNullDate()));

		// Get the search string
		$search = $this->getSearch();

		// Get the search filter
		$filter_search = $app->getUserStateFromRequest($this->key . '_filter_search', 'filter_search', 'subject', 'string');

		if ($search != '')
		{
			$fields = explode(',', $filter_search);

			foreach ($fields as $field)
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('a.' . $field . " LIKE " . $search);
			}
		}

		// Get the filter order
		$filter_order     = $app->getUserStateFromRequest($this->key . '_filter_order', '.filter_order', 'a.subject', 'word');
		$filter_order_Dir = $app->getUserStateFromRequest($this->key . '_filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'a.subject')
		{
			$query->order('a.subject ' . $filter_order_Dir);
		}
		else
		{
			$query->order($db->escape($filter_order . ' ' . $filter_order_Dir));
		}

		return $query;
	}

	/**
	 * Method to get the search term
	 *
	 * @return 	string
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function getSearch(): string
	{
		if (!$this->search)
		{
			$app = Factory::getApplication();

			$search       = $app->getUserStateFromRequest($this->key . '_search', 'search', '', 'string');
			$this->search = StringHelper::strtolower($search);
		}

		return $this->search;
	}
}
