<?php
/**
 * BwPostman Overview Module
 *
 * BwPostman helper class for overview module.
 *
 * @version %%version_number%%
 * @package BwPostman-Overview-Module
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

namespace BoldtWebservice\Module\BwPostmanOverview\Site\Helper;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Site\Model\NewslettersModel;
use DateTime;
use Exception;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Log\LogEntry;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use RuntimeException;
use stdClass;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper',
    JPATH_ADMINISTRATOR . '/components/com_bwpostman/Helper');

/**
 * Class ModBwPostmanOverviewHelper
 *
 * @since       1.2.0
 */
class ModBwPostmanOverviewHelper
{
	/**
	 * Retrieve list of newsletters
	 *
	 * @param Registry  $params     module parameters
	 * @param int       $module_id  id of this module
	 *
	 * @return  array     $lists        array of newsletter objects
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	public static function getList(Registry $params, int $module_id = 0): array
	{
		$nlModel = new NewslettersModel();
		$itemid = $nlModel->getMenuItemid();

		$itemPath = '';

		if ($itemid > 0)
		{
			$itemPath = '&Itemid=' . $itemid;
		}

		$i     = 0;
		$lists = array();
		$rows  = self::getItems($params);

		if (count($rows) === 0)
		{
			$lists = array();
		}

		foreach ($rows as $row)
		{
			$date = Factory::getDate($row->mailing_date);

			$sent_month = $date->format('m');
			$sent_year  = $date->format('Y');

			$sent_year_cal  = HtmlHelper::_('date', $row->mailing_date, 'Y');
			$month_name_cal = HtmlHelper::_('date', $row->mailing_date, 'F');

			$lists[$i] = new stdClass;

			$lists[$i]->link = 'index.php?option=com_bwpostman&view=newsletters&mid=' . $module_id . '&year=' . $sent_year
				. '&month=' . $sent_month . $itemPath;
			$lists[$i]->text = Text::sprintf('MOD_BWPOSTMAN_OVERVIEW_DATE', $month_name_cal, $sent_year_cal) . ' (' . $row->count_month . ')';

			$i++;
		}

		return $lists;
	}

	/**
	 * Gets the items depending on Module or Menuitem params
	 *
	 * @param Registry  $params module parameters
	 *
	 * @return  array   $rows   array of newsletter objects
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	private static function getItems(Registry $params): array
	{
		// Get conditions
		$menuItemId = $params->get('menu_item', '');

		if ($menuItemId)
		{
			$menu_params = self::getMenuItemParams($menuItemId);

			$params->set('access-check', $menu_params->get('access-check', '1'));
			$params->set('show_type', $menu_params->get('show_type', 'all'));
			$params->set('ml_selected_all', $menu_params->get('ml_selected_all', 'no'));
			$params->set('ml_available', $menu_params->get('ml_available', ''));
			$params->set('groups_selected_all', $menu_params->get('groups_selected_all', 'no'));
			$params->set('groups_available', $menu_params->get('groups_available', ''));
			$params->set('cam_selected_all', $menu_params->get('cam_selected_all', 'no'));
			$params->set('cam_available', $menu_params->get('cam_available', ''));
		}

		// get accessible mailing lists
		$mls = self::getAccessibleMailinglists($params);

		// get accessible usergroups
		$groups = self::getAccessibleUsergroups($params);

		if (count($groups) > 0)
		{
			// merge mailinglists and usergroups and remove multiple values
			$mls = array_merge($mls, $groups);
			$mls = array_unique($mls);
		}

		// get accessible campaigns
		$cams = self::getAccessibleCampaigns($params);

		// get unique newsletter IDs
		$nls_result = self::getUniqueNlIds($mls, $cams, $params);

		$nls = array_column($nls_result, 'id');

		// get count list
		if (count($nls) > 0)
		{
			return self::getNlCountList($nls);
		}

		return array();
	}

	/**
	 * Method to get the menu item params.
	 *
	 * @param int $id id of menu item
	 *
	 * @return  Registry  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	protected static function getMenuItemParams(int $id = 0): Registry
	{
		$menu = Factory::getApplication()->getMenu();

		return $menu->getParams($id);
	}

	/**
	 * Method to get all published mailing lists which the user is authorized to see and which are selected in menu
	 *
	 * @param Registry  $params module parameters
	 *
	 * @return    array    $mailinglists       ID and title of allowed mailinglists
	 *
	 * @throws Exception
	 *
	 * @since     1.2.0
	 */
	private static function getAccessibleMailinglists(Registry $params): array
	{
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);
		$check = $params->get('access-check', 1);

		$logOptions = array();
		$logger     = BwLogger::getInstance($logOptions);

		// fetch only from mailinglists, which are selected, if so
		$all_mls = $params->get('ml_selected_all', 'no');
		$sel_mls = $params->get('ml_available', array());
		$mls     = array();

		if ($all_mls === "yes")
		{
			$query->select('id');
			$query->from($db->quoteName('#__bwpostman_mailinglists'));
			$query->where($db->quoteName('published') . ' = ' . 1);

			try
			{
				$db->setQuery($query);

				$res_mls = $db->loadAssocList();

				if ($res_mls === null)
				{
					$res_mls = array();
				}

				$mls     = array_column($res_mls, 'id');
			}
			catch (RuntimeException $e)
			{
				$message = 'Query 1: ' . $e->getMessage() . ' ' . $query;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}
		}
		else
		{
			$mls = $sel_mls;
		}

		if (!count($mls))
		{
			return array();
		}

		// Check permission, if desired
		if ($all_mls || $check !== 'no')
		{
			// get authorized viewlevels
			$accesslevels = Access::getAuthorisedViewLevels(Factory::getApplication()->getIdentity()->id);

			$query = $db->getQuery(true);

			$query->select('id');
			$query->from($db->quoteName('#__bwpostman_mailinglists'));
			$query->where($db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
			$query->where($db->quoteName('id') . ' IN (' . implode(',', (array)$mls) . ')');
			$query->where($db->quoteName('published') . ' = ' . 1);

			try
			{
				$db->setQuery($query);

				$res_mls = $db->loadAssocList();

				if ($res_mls === null)
				{
					$res_mls = array();
				}

				$mls = array_column($res_mls, 'id');
			}
			catch (RuntimeException $e)
			{
				$message = 'Query 2: ' . $e->getMessage() . ' ' . $query;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}
		}

		return (array)$mls;
	}

	/**
	 * Method to get all campaigns which the user is authorized to see
	 *
	 * @param Registry  $params module parameters
	 *
	 * @return    array    $campaigns     array of ids of allowed campaigns
	 *
	 * @throws Exception
	 *
	 * @since     1.2.0
	 */
	private static function getAccessibleCampaigns(Registry $params): array
	{
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);
		$check = $params->get('access-check', '1');

		$logOptions = array();
		$logger     = BwLogger::getInstance($logOptions);

		// fetch only from campaigns, which are selected, if so
		$all_cams = $params->get('cam_selected_all', 'no');
		$sel_cams = $params->get('cam_available', array());
		$cams     = array();

		if (!is_array($sel_cams) && $all_cams === 'no')
		{
			return array();
		}

		if ($all_cams === 'yes')
		{
			$query->select('c.id');
			$query->from('#__bwpostman_campaigns AS c');

			try
			{
				$db->setQuery($query);

				$res_cams = $db->loadAssocList();

				if ($res_cams === null)
				{
					$res_cams = array();
				}

				$cams = array_column($res_cams, 'id');
			}
			catch (RuntimeException $e)
			{
				$message = 'Query 3: ' . $e->getMessage() . ' ' . $query;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}
		}
		else
		{
			$cams = $sel_cams;
		}

		if (!count($cams))
		{
			return array();
		}

		$acc_mls = array();

		// Check permission, if desired
		if ($all_cams === 'yes' || $check === 'yes')
		{
			// get authorized viewlevels
			$accesslevels = Access::getAuthorisedViewLevels(Factory::getApplication()->getIdentity()->id);

			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__bwpostman_mailinglists'));
			$query->where($db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
			$query->where($db->quoteName('published') . ' = ' . 1);

			try
			{
				$db->setQuery($query);

				$res_mls = $db->loadAssocList();

				if ($res_mls === null)
				{
					$res_mls = array();
				}

				$acc_mls = array_column($res_mls, 'id');
			}
			catch (RuntimeException $e)
			{
				$message = 'Query 4: ' . $e->getMessage() . ' ' . $query;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}

			$query	= $db->getQuery(true);

			$query->select('DISTINCT (' . $db->quoteName('campaign_id') . ')');
			$query->from($db->quoteName('#__bwpostman_campaigns_mailinglists'));
			$query->where($db->quoteName('mailinglist_id') . ' IN (' . implode(',', $acc_mls) . ')');
			$query->where($db->quoteName('campaign_id') . ' IN (' . implode(',', (array)$cams) . ')');

			try
			{
				$db->setQuery($query);

				$acc_cams = $db->loadAssocList();

				if ($acc_cams === null)
				{
					$acc_cams = array();
				}

				$cams = array_column($acc_cams, 'campaign_id');

			}
			catch (RuntimeException $e)
			{
				$message = 'Query 5: ' . $e->getMessage() . ' ' . $query;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}
		}

		return (array)$cams;
	}

	/**
	 * Method to get all user groups which the user is authorized to see
	 *
	 * @param Registry  $params module parameters
	 *
	 * @return    array    $groups             array of ids of user groups
	 *
	 * @throws Exception
	 *
	 * @since    1.2.0
	 */
	private static function getAccessibleUsergroups(Registry $params): array
	{
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);
		$check = $params->get('access-check', 1);

		// fetch only from usergroups, which are selected, if so
		$all_groups	= $params->get('groups_selected_all', 'no');
		$sel_groups	= $params->get('groups_available', array());

		if (!is_array($sel_groups) && $all_groups === 'no')
		{
			return array();
		}

		$groups = array();

		if ($all_groups === 'yes')
		{
			$query->select('u.id');
			$query->from('#__usergroups AS u');

			try
			{
				$db->setQuery($query);

				$res_groups = $db->loadAssocList();

				if ($res_groups === null)
				{
					$res_groups = array();
				}

				$groups = array_column($res_groups, 'id');
			}
			catch (RuntimeException $e)
			{
				$logOptions = array();
				$logger     = BwLogger::getInstance($logOptions);
				$message    = 'Query 6: ' . $e->getMessage();

				$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			}

			if (!is_array($groups))
			{
				$groups = array();
			}

			//convert usergroups to match BwPostman's needs
			$c_groups	= array();

			if (count($groups) > 0)
			{
				foreach ($groups as $value)
				{
					$c_groups[]	= '-' . $value;
				}
			}
		}
		else
		{
			$c_groups = $sel_groups;
		}

		// Check permission, if desired
		if ($all_groups === 'yes' || $check === 'yes')
		{
			$user       = Factory::getApplication()->getIdentity();
			$acc_groups = $user->getAuthorisedGroups();

			//convert usergroups to match bwPostman's needs
			$a_groups = array();

			foreach ($acc_groups as $value)
			{
				$a_groups[]	= '-' . $value;
			}

			$sel_groups	= array_intersect($a_groups, (array)$c_groups);
		}

		return (array)$sel_groups;
	}

	/**
	 * Method to get the list of newsletters of the provided mailinglists and campaigns and filtered by provided show
	 * type
	 *
	 * @param array    $mls
	 * @param array    $cams
	 * @param Registry $params
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	private static function getUniqueNlIds(array $mls, array $cams, Registry $params): array
	{
		// Get database
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);

		// Define null and now dates
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(Factory::getDate()->toSql());

		$sinceDateString = ' != ' . $nullDate;
		$count = (int)$params->get('count', '12');

		if ($count > 0)
		{
			$backCountString = 'first day of -' . ($count - 1) . ' month';
			$firstOfMonthObject  = new DateTime($backCountString);
			$sinceDate = $firstOfMonthObject->format('Y-m-d') . ' 0000:00:00';

			$sinceDateString = ' >= ' . $db->quote($sinceDate);
		}

		$query->select(
			'DISTINCT(' . $db->quoteName('a.id') . '), ' .
			// Use mailing date if publish_up is 0
			'CASE WHEN a.publish_up = ' . $nullDate . ' THEN a.mailing_date ELSE a.publish_up END as publish_up'
		);
		$query->from('#__bwpostman_newsletters AS a');
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->where($db->quoteName('a.mailing_date') . $sinceDateString);

		// Filter by accessible mailing lists, user groups and campaigns
		$query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');

        $whereMlsCamsClause = BwPostmanHelper::getWhereMlsCamsClause($mls, $cams);

		if ($whereMlsCamsClause !== '')
		{
			$query->where($whereMlsCamsClause);
		}

		// Filter by show type
		switch ($params->get('show_type', 'arc'))
		{
			case 'all':
			default:
				break;
			case 'all_not_arc':
				$query->where('a.archive_flag = 0');
				break;
			case 'not_arc_down':
				$query->where('a.archive_flag = 0');
				$query->where('a.publish_up <= ' . $nowDate);
				$query->where('(a.publish_down >= ' . $nowDate . ' OR a.publish_down = ' . $nullDate . ')');
				break;
			case 'not_arc_but_down':
				$query->where('a.archive_flag = 0');
				$query->where('a.publish_up <= ' . $nowDate);
				$query->where('a.publish_down <> ' . $nullDate);
				$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc':
				$query->where('a.archive_flag = 1');
				break;
			case 'down':
				$query->where('a.publish_up <= ' . $nowDate);
				$query->where('a.publish_down <> ' . $nullDate);
				$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc_and_down':
				$query->where('a.archive_flag = 1');
				$query->where('a.publish_up <= ' . $nowDate);
				$query->where('a.publish_down <> ' . $nullDate);
				$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc_or_down':
				$query->where(
					'(a.archive_flag = 1
							OR (
									a.publish_down <> ' . $nullDate . '
								AND a.publish_down <= ' . $nowDate . '
								AND a.publish_up <= ' . $nowDate . '
							))'
				);
				break;
		}

		$query->group('a.id');

		try
		{
			$db->setQuery($query);

			$result = $db->loadAssocList();

			if ($result === null)
			{
				$result = array();
			}

			return $result;
		}
		catch (RuntimeException $e)
		{
			$logOptions   = array();
			$logger  = BwLogger::getInstance($logOptions);
			$message = 'Query 7: ' . $e->getMessage() . ' ' . $query;

			$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			return array();
		}
	}

	/**
	 * Method to get the list of newsletters per month and year
	 *
	 * @param array $nls
	 *
	 * @return array
	 *
	 * @since 4.0.0
	 */
	private static function getNlCountList(array $nls): array
	{
		// Get database
		$db = Factory::getContainer()->get('db');

		$query = $db->getQuery(true);
		$query->select($query->month($db->quoteName('a.mailing_date')) . ' AS sent_month');
		$query->select($query->year($db->quoteName('a.mailing_date')) . ' AS sent_year');
		$query->select('COUNT(*) AS count_month');
		$query->select('a.mailing_date');
		$query->from('#__bwpostman_newsletters AS a');

		$query->where($db->quoteName('a.id') . ' IN (' . implode(',', $nls) . ')');

		$query->order($db->quoteName('a.mailing_date') . ' DESC');
		$query->group($query->year($db->quoteName('a.mailing_date')));
		$query->group($query->month($db->quoteName('a.mailing_date')));

		try
		{
			$db->setQuery($query);

			$result = $db->loadObjectList();

			if ($result === null)
			{
				$result = array();
			}

			return $result;
		}
		catch (RuntimeException $e)
		{
			$logOptions   = array();
			$logger  = BwLogger::getInstance($logOptions);
			$message = 'Query 8: ' . $e->getMessage() . ' ' . $query;

			$logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mod_overview'));
			return array();
		}
	}
}
