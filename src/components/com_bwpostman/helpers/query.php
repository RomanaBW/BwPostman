<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman query helper class for frontend.
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

defined('_JEXEC') or die;

/**
 * Class BwPostmanHelperQuery
 *
 * @since       1.2.0
 */
class BwPostmanHelperQuery
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 *
	 * @since	1.2.0
	 */
	public static function orderbyPrimary($orderby)
	{
		switch ($orderby)
		{
			case 'alpha' :
				$orderby = 'c.path, ';
				break;

			case 'ralpha' :
				$orderby = 'c.path DESC, ';
				break;

			case 'order' :
				$orderby = 'c.lft, ';
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 * @param	string	$orderDate	The ordering code for the date.
	 *
	 * @return  string  The SQL field(s) to order by.
	 *
	 * @since	1.2.0
	 */
	public static function orderbySecondary($orderby, $orderDate = 'mailing_date')
	{
		$queryDate = self::getQueryDate($orderDate);

		switch ($orderby)
		{
			case 'date' :
				$orderby = $queryDate;
				break;

			case 'rdate' :
				$orderby = $queryDate . ' DESC ';
				break;

			case 'alpha' :
				$orderby = 'a.subject';
				break;

			case 'ralpha' :
				$orderby = 'a.subject DESC';
				break;

			case 'hits' :
				$orderby = 'a.hits DESC';
				break;

			case 'rhits' :
				$orderby = 'a.hits';
				break;

			case 'order' :
				$orderby = 'a.ordering';
				break;

			case 'author' :
				$orderby = 'author';
				break;

			case 'rauthor' :
				$orderby = 'author DESC';
				break;

			case 'front' :
				$orderby = 'a.featured DESC, fp.ordering, ' . $queryDate . ' DESC ';
				break;

			default :
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderDate	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 *
	 * @since	1.2.0
	 */
	public static function getQueryDate($orderDate)
	{
		$db = JFactory::getDbo();

		switch ($orderDate)
		{
			case 'modified' :
				$queryDate = ' CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created_date ELSE a.modified END';
				break;

			// Use created if publish_up is not set
			case 'published' :
				$queryDate = ' CASE WHEN a.publish_up = ' . $db->quote($db->getNullDate()) . ' THEN a.created_date ELSE a.publish_up END ';
				break;

			case 'created_date' :
				$queryDate = ' a.created_date ';
				break;
			case 'mailing_date' :
			default :
				$queryDate = ' a.mailing_date ';
				break;
		}

		return $queryDate;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param	\Joomla\Registry\Registry	$params		An options object for the newsletter.
	 *
	 * @return	array  A named array with "select" and "join" keys.
	 *
	 * @since	1.2.0
	 */
	public static function buildVotingQuery($params = null)
	{
		if (!$params)
		{
			$params = JComponentHelper::getParams('com_content');
		}

		$voting = $params->get('show_vote');

		if ($voting)
		{
			// Calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__content_rating AS v ON a.id = v.content_id';
		}
		else
		{
			$select = '';
			$join = '';
		}

		$results = array ('select' => $select, 'join' => $join);

		return $results;
	}
}
