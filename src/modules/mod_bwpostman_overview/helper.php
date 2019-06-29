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

defined('_JEXEC') or die;

/**
 * Class modBwPostmanOverviewHelper
 *
 * @since       1.2.0
 */
class ModBwPostmanOverviewHelper
{
	/**
	 * Retrieve list of newsletters
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 * @param   int     $module_id      id of this module
	 *
	 * @return  array   $lists      array of newsletter objects
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	public static function getList(&$params, $module_id	= 0)
	{
		$item		= $params->get('menu_item');
		$itemid		= (!empty($item)) ? '&Itemid=' . $item : '';

		$i		= 0;
		$lists	= array();
		$rows	= self::getItems($params);

		foreach ($rows as $row)
		{
			$date = JFactory::getDate($row->mailing_date);

			$sent_month	= $date->format('n');
			$sent_year	= $date->format('Y');

			$sent_year_cal	= JHtml::_('date', $row->mailing_date, 'Y');
			$month_name_cal	= JHtml::_('date', $row->mailing_date, 'F');

			$lists[$i]		= new stdClass;

			$lists[$i]->link	= 'index.php?option=com_bwpostman&view=newsletters&mid=' . $module_id . '&year=' . $sent_year
				. '&month=' . $sent_month . $itemid;
			$lists[$i]->text	= JText::sprintf('MOD_BWPOSTMAN_OVERVIEW_DATE', $month_name_cal, $sent_year_cal) . ' (' . $row->count_month . ')';

			$i++;
		}

		return $lists;
	}

	/**
	 * Gets the items depending on Module or Menuitem params
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array   $rows       array of newsletter objects
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	private static function getItems(&$params)
	{
		// Get conditions
		$menuItemId		= $params->get('menu_item');

		if ($menuItemId) {
			$menu_params	= self::getMenuItemParams($menuItemId);

			$params->set('access-check', $menu_params->get('access-check'));
			$params->set('show_type', $menu_params->get('show_type'));
			$params->set('ml_selected_all', $menu_params->get('ml_selected_all'));
			$params->set('ml_available', $menu_params->get('ml_available'));
			$params->set('groups_selected_all', $menu_params->get('groups_selected_all'));
			$params->set('groups_available', $menu_params->get('groups_available'));
			$params->set('cam_selected_all', $menu_params->get('cam_selected_all'));
			$params->set('cam_available', $menu_params->get('cam_available'));
		}

		// Get database
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		// Define null and now dates
		$nullDate	= $_db->quote($_db->getNullDate());
		$nowDate	= $_db->quote(JFactory::getDate()->toSql());

		// get accessible mailing lists
		$mls	= self::getAccessibleMailinglists($params);

		$groups	= self::getAccessibleUsergroups($params);

		if (count($groups) > 0)
		{
			// merge mailinglists and usergroups and remove multiple values
			$mls	= array_merge($mls, $groups);
			$mls	= array_unique($mls);
		}

		// get accessible campaigns
		$cams	= self::getAccessibleCampaigns($params);

		// get unique newsletter IDs
		$query->select(
			'DISTINCT(' . $_db->quoteName('a.id') . '), ' .
			// Use mailing date if publish_up is 0
			'CASE WHEN a.publish_up = ' . $nullDate . ' THEN a.mailing_date ELSE a.publish_up END as publish_up'
		);
		$query->from('#__bwpostman_newsletters AS a');
		$query->where($_db->quoteName('a.published') . ' = 1');
		$query->where($_db->quoteName('a.mailing_date') . ' != ' . $nullDate);

		// Filter by accessible mailing lists, user groups and campaigns
		$query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');

		$whereMlsCamsClause = BwPostmanHelper::getWhereMlsCamsClause($mls, $cams);

		$query->where($whereMlsCamsClause);

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
		$_db->setQuery($query);

		$nls_result	= $_db->loadAssocList();

		$nls	= array();
		foreach ($nls_result as $item)
		{
			$nls[]	= $item['id'];
		}

		if (count($nls) == 0)
		{
			$nls[]	= 0;
		}

		// get count list
		$query	= $_db->getQuery(true);
		$query->select($query->month($_db->quoteName('a.mailing_date')) . ' AS sent_month');
		$query->select($query->year($_db->quoteName('a.mailing_date')) . ' AS sent_year');
		$query->select('COUNT(*) AS count_month');
		$query->select('a.mailing_date');
		$query->from('#__bwpostman_newsletters AS a');

		$query->where($_db->quoteName('a.id') . ' IN (' . implode(',', $nls) . ')');

		$query->order($_db->quoteName('a.mailing_date') . ' DESC');
		$query->group($query->year($_db->quoteName('a.mailing_date')));
		$query->group($query->month($_db->quoteName('a.mailing_date')));

		$_db->setQuery($query);

		$rows = $_db->loadObjectList();

		return $rows;
	}

	/**
	 * Method to get the menu item params.
	 *
	 * @param   int     $id     id of menu item
	 *
	 * @return  Joomla\Registry\Registry  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since   1.2.0
	 */
	protected static function getMenuItemParams($id = 0)
	{
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$params	= $menu->getParams($id);

		return $params;
	}

	/**
	 * Method to get all published mailing lists which the user is authorized to see and which are selected in menu
	 *
	 * @access 	public
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return 	array	$mailinglists       ID and title of allowed mailinglists
	 *
	 * @since	1.2.0
	 */
	private static function getAccessibleMailinglists(&$params)
	{
		$_db		= JFactory::getDbo();
		$query		= $_db->getQuery(true);
		$check		= $params->get('access-check', 1);

		// fetch only from mailinglists, which are selected, if so
		$all_mls	= $params->get('ml_selected_all');
		$sel_mls	= $params->get('ml_available');

		if ($all_mls)
		{
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$res_mls	= $_db->loadAssocList();
			$mls		= array();
			if (count($res_mls) > 0) {
				foreach ($res_mls as $item) {
					$mls[]	= $item['id'];
				}
			}
		}
		else
		{
			$mls	= $sel_mls;
		}

		// if no mls is left, make array
		if ($mls === null || count($mls) == 0)
		{
			$mls[]	= 0;
		}

		// Check permission, if desired
		if ($all_mls || $check != 'no')
		{
			// get authorized viewlevels
			$accesslevels	= JAccess::getAuthorisedViewLevels(JFactory::getUser()->id);
			$acc_levels     = array();
			if (count($accesslevels) > 0)
			{
				foreach ($accesslevels as $key => $value)
				{
					$acc_levels[]	= $key;
				}
			}
			else
			{
				$acc_levels[]	= 0;
			}

			$query	= $_db->getQuery(true);

			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('access') . ' IN (' . implode(',', $acc_levels) . ')');
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$res_mls = $_db->loadAssocList();

			$acc_mls	= array(0);
			foreach ($res_mls as $item)
			{
				$acc_mls[]	= $item['id'];
			}
		}

		if (count($mls) == 0)
		{
			$mls[]	= 0;
		}

		$mailinglists	= $mls;

		return $mailinglists;
	}

	/**
	 * Method to get all campaigns which the user is authorized to see
	 *
	 * @access 	public
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return 	array	$campaigns          array of ids of allowed campaigns
	 *
	 * @since	1.2.0
	 */
	private static function getAccessibleCampaigns(&$params)
	{
		$_db		= JFactory::getDbo();
		$query		= $_db->getQuery(true);
		$check		= $params->get('access-check');

		// fetch only from campaigns, which are selected, if so
		$all_cams	= $params->get('cam_selected_all');
		$sel_cams	= $params->get('cam_available');

		if ($all_cams)
		{
			$query->select('c.id');
			$query->from('#__bwpostman_campaigns AS c');
			$_db->setQuery($query);

			$res_cams	= $_db->loadAssocList();
			$cams		= array();
			if (count($res_cams) > 0)
			{
				foreach ($res_cams as $item)
				{
					$cams[]	= $item['id'];
				}
			}
		}
		else
		{
			$cams	= $sel_cams;
		}

		// if no cam is left, make (empty) array
		if (count($cams) == 0)
		{
			$cams[]	= 0;
		}

		// Check permission, if desired
		if ($all_cams != 'no' || $check != 'no')
		{
			// get authorized viewlevels
			$accesslevels	= JAccess::getAuthorisedViewLevels(JFactory::getUser()->id);
			$acc_levels     = array();
			if (count($accesslevels) > 0)
			{
				foreach ($accesslevels as $key => $value)
				{
					$acc_levels[]	= $key;
				}
			}
			else
			{
				$acc_levels[]	= 0;
			}

			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('access') . ' IN (' . implode(',', $acc_levels) . ')');
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$res_mls = $_db->loadAssocList();

			$acc_mls	= array(0);
			foreach ($res_mls as $item)
			{
				$acc_mls[]	= $item['id'];
			}

			$query	= $_db->getQuery(true);

			$query->select('DISTINCT (' . $_db->quoteName('campaign_id') . ')');
			$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
			$query->where($_db->quoteName('mailinglist_id') . ' IN (' . implode(',', $acc_mls) . ')');
			$query->where($_db->quoteName('campaign_id') . ' IN (' . implode(',', $cams) . ')');

			$_db->setQuery($query);

			$acc_cams	= $_db->loadAssocList();
			if (count($acc_cams) > 0)
			{
				$cams		= array();
				foreach ($acc_cams as $item)
				{
					$cams[]	= $item['campaign_id'];
				}
			}
		}

		// if no cam is left, make array to return
		if (count($cams) == 0)
		{
			$cams[]	= 0;
		}

		$campaigns	= $cams;

		return $campaigns;
	}

	/**
	 * Method to get all user groups which the user is authorized to see
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return 	array	$groups             array of ids of user groups
	 *
	 * @since	1.2.0
	 */
	private static function getAccessibleUsergroups(&$params)
	{
		$_db		= JFactory::getDbo();
		$query		= $_db->getQuery(true);
		$check		= $params->get('access-check', 1);

		// fetch only from usergroups, which are selected, if so
		$all_groups	= $params->get('groups_selected_all');
		$sel_groups	= $params->get('groups_available');

		if ($all_groups)
		{
			$query->select('u.id');
			$query->from('#__usergroups AS u');
			$_db->setQuery($query);

			$res_groups	= $_db->loadAssocList();
			$groups		= array();
			if (count($res_groups) > 0)
			{
				foreach ($res_groups as $item)
				{
					$groups[]	= $item['id'];
				}
			}
			else
			{
				$groups[]	= 0;
			}

			//convert usergroups to match bwPostman's needs
			$c_groups	= array();
			if (count($groups) > 0)
			{
				foreach ($groups as $value)
				{
					$c_groups[]	= '-' . $value;
				}
			}
			else
			{
				$c_groups[]	= 0;
			}
		}
		else
		{
			$c_groups	= $sel_groups;
		}

		if ($c_groups === null || count($c_groups) == 0)
		{
			$c_groups[]	= 0;
		}

		// Check permission, if desired
		if ($all_groups || $check != 'no')
		{
			$user		= JFactory::getUser();
			$acc_groups	= $user->getAuthorisedGroups();

			//convert usergroups to match bwPostman's needs
			$a_groups	= array();
			if (count($acc_groups) > 0)
			{
				foreach ($acc_groups as $value)
				{
					$a_groups[]	= '-' . $value;
				}
			}
			else
			{
				$a_groups[]	= 0;
			}

			$sel_groups	= array_intersect($a_groups, $c_groups);
		}

		if (count($sel_groups) == 0)
		{
			$sel_groups[]	= 0;
		}

		$groups	= $sel_groups;

		return $groups;
	}
}
