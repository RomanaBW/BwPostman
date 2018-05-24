<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main model for backend.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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
defined('_JEXEC') or die('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.model');

/**
 * BwPostman cover page model
 *
 * @package		BwPostman-Admin
 * @subpackage	CoverPage
 *
 * @since       0.9.1
 */
class BwPostmanModelBwPostman extends JModelLegacy
{
	/**
	 * General statistic data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $general = null;

	/**
	 * Archive statistic data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $archive = null;

	/**
	 * Constructor
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to get general statistic data
	 *
	 * @access 	public
	 *
	 * @return 	array       associative array of General Data
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function getGeneraldata()
	{
		$general	= array();
		$db		= $this->_db;
		$query		= $db->getQuery(true);

		// Get # of all unsent newsletters
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('mailing_date') . ' = ' . $db->quote('0000-00-00 00:00:00'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);

		$db->setQuery($query);
		try
		{
			$general['nl_unsent'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all sent newsletters
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('mailing_date') . ' != ' . $db->quote('0000-00-00 00:00:00'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);

		$db->setQuery($query);
		try
		{
			$general['nl_sent'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all subscribers
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('status') . ' != ' . (int) 9);
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);

		$db->setQuery($query);
		try
		{
			$general['sub'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all test-recipients
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('status') . ' = ' . (int) 9);
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);

		$db->setQuery($query);
		try
		{
			$general['test'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all campaigns
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);

		$db->setQuery($query);
		try
		{
			$general['cam'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all published mailinglists
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('published') . ' = ' . (int) 1);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		$db->setQuery($query);
		try
		{
			$general['ml_published'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all unpublished mailinglists
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('published') . ' = ' . (int) 0);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		$db->setQuery($query);
		try
		{
			$general['ml_unpublished'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all html templates
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));

		$db->setQuery($query);
		try
		{
			$general['html_templates'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all text templates
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));

		$db->setQuery($query);
		try
		{
			$general['text_templates'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get total # of general statistic
		$general[] = array_sum($general);

		return $general;
	}

	/**
	 * Method to get archive statistic data
	 *
	 * @access 	public
	 *
	 * @return 	array       associative array of Archive data
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getArchivedata()
	{
		$archive	= array();
		$db		= $this->_db;
		$query		= $db->getQuery(true);

		// Get # of all archived newsletters
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);

		$db->setQuery($query);
		try
		{
			$archive['arc_nl'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all archived subscribers
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);

		$db->setQuery($query);
		try
		{
			$archive['arc_sub'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all archived campaigns
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);

		$db->setQuery($query);
		try
		{
			$archive['arc_cam'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all archived mailinglists
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);

		$db->setQuery($query);
		try
		{
			$archive['arc_ml'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all html templates
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);
		$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));

		$db->setQuery($query);
		try
		{
			$archive['arc_html_templates'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get # of all text templates
		$query->clear();
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__bwpostman_templates'));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 1);
		$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));

		$db->setQuery($query);
		try
		{
			$archive['arc_text_templates'] = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get total # of general statistic
		$archive[] = array_sum($archive);

		return $archive;
	}

	/**
	 * Method to store the permission values in the asset table.
	 *
	 * This method will get an array with permission key value pairs and transform it
	 * into json and update the asset table in the database.
	 *
	 * This method is derived from Joomla com_config/model/application.php
	 *
	 * @param   string  $permission  Need an array with Permissions (component, rule, value and title)
	 *
	 * @return  array|boolean  A list of result data or false
	 *
	 * @throws Exception
	 *
	 * @since   2.0
	 */
	public function storePermissions($permission = null)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$statePermissions	= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (is_null($permission))
		{
			// Get data from input.
			$permission = array(
				'component' => $app->input->get('comp'),
				'action'    => $app->input->get('action'),
				'rule'      => $app->input->get('rule'),
				'value'     => $app->input->get('value'),
				'title'     => $app->input->get('title', '', 'RAW')
			);
		}

		// We are creating a new item so we don't have an item id so don't allow.
		if (substr($permission['component'], -6) === '.false')
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_SAVE_BEFORE_CHANGE_PERMISSIONS'), 'error');

			return false;
		}

		$section = $this->getSectionNameFromPermissions($permission);

		$sectionPermission = false;
		if ($section !== '')
		{
			$sectionPermission = $statePermissions['admin'][$section];
		}

		// Check if the user is authorized to do this.
		if (!$statePermissions['com']['admin'] && !$sectionPermission)
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

			return false;
		}

		$permission['component'] = empty($permission['component']) ? 'root.1' : $permission['component'];

		// Current view is global config?
		$isGlobalConfig = $permission['component'] === 'root.1';

		// Check if changed group has Super User permissions.
		$isSuperUserGroupBefore = JAccess::checkGroup($permission['rule'], 'core.admin');

		// Check if current user belongs to changed group.
		$currentUserBelongsToGroup = in_array((int) $permission['rule'], $user->groups) ? true : false;

		// Get current user groups tree.
		$currentUserGroupsTree = JAccess::getGroupsByUser($user->id, true);

		// Check if current user belongs to changed group.
		$currentUserSuperUser = $user->authorise('core.admin');

		// If user is not Super User cannot change the permissions of a group it belongs to.
		if (!$currentUserSuperUser && $currentUserBelongsToGroup)
		{
			$app->enqueueMessage(JText::_('JLIB_USER_ERROR_CANNOT_CHANGE_OWN_GROUPS'), 'error');

			return false;
		}

		// If user is not Super User cannot change the permissions of a group it belongs to.
		if (!$currentUserSuperUser && in_array((int) $permission['rule'], $currentUserGroupsTree))
		{
			$app->enqueueMessage(JText::_('JLIB_USER_ERROR_CANNOT_CHANGE_OWN_PARENT_GROUPS'), 'error');

			return false;
		}

		// If user is not Super User cannot change the permissions of a Super User Group.
		if (!$currentUserSuperUser && $isSuperUserGroupBefore && !$currentUserBelongsToGroup)
		{
			$app->enqueueMessage(JText::_('JLIB_USER_ERROR_CANNOT_CHANGE_SUPER_USER'), 'error');

			return false;
		}

		// If user is not Super User cannot change the Super User permissions in any group it belongs to.
		if ($isSuperUserGroupBefore && $currentUserBelongsToGroup && $permission['action'] === 'core.admin')
		{
			$app->enqueueMessage(JText::_('JLIB_USER_ERROR_CANNOT_DEMOTE_SELF'), 'error');

			return false;
		}

		try
		{
			$asset  = JTable::getInstance('asset');
			$result = $asset->loadByName($permission['component']);

			if ($result === false)
			{
				// @ToDo: Check this path
				$data = array($permission['action'] => array($permission['rule'] => $permission['value']));

				$rules        = new JAccessRules($data);
				$asset->rules = (string) $rules;
				$asset->name  = (string) $permission['component'];
				$asset->title = (string) $permission['title'];

				// Get the parent asset id so we have a correct tree.
				$parentAsset = JTable::getInstance('Asset');

				if (strpos($asset->name, '.') !== false)
				{
					$assetParts = explode('.', $asset->name);
					$parentAsset->loadByName($assetParts[0]);
					$parentAssetId = $parentAsset->id;
				}
				else
				{
					$parentAssetId = $parentAsset->getRootId();
				}

				/**
				 * @to do: incorrect ACL stored
				 * When changing a permission of an item that doesn't have a row in the asset table the row a new row is created.
				 * This works fine for item <-> component <-> global config scenario and component <-> global config scenario.
				 * But doesn't work properly for item <-> section(s) <-> component <-> global config scenario,
				 * because a wrong parent asset id (the component) is stored.
				 * Happens when there is no row in the asset table (ex: deleted or not created on update).
				 */

				$asset->setLocation($parentAssetId, 'last-child');
			}
			else
			{
				// Decode the rule settings.
				$temp = json_decode($asset->rules, true);

				// Check if a new value is to be set.
				if (isset($permission['value']))
				{
					// Check if we already have an action entry.
					if (!isset($temp[$permission['action']]))
					{
						$temp[$permission['action']] = array();
					}

					// Check if we already have a rule entry.
					if (!isset($temp[$permission['action']][$permission['rule']]))
					{
						$temp[$permission['action']][$permission['rule']] = array();
					}

					// Set the new permission.
					$temp[$permission['action']][$permission['rule']] = (int) $permission['value'];

					// Check if we have an inherited setting.
					if ($permission['value'] === '')
					{
						unset($temp[$permission['action']][$permission['rule']]);
					}

					// Check if we have any rules.
					if (!$temp[$permission['action']])
					{
						unset($temp[$permission['action']]);
					}
				}
				else
				{
					// There is no value so remove the action as it's not needed.
					unset($temp[$permission['action']]);
				}

				$asset->rules = json_encode($temp, JSON_FORCE_OBJECT);
			}

			if (!$asset->check() || !$asset->store())
			{
				$app->enqueueMessage(JText::_('JLIB_UNKNOWN'), 'error');

				return false;
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// All checks done.
		$result = array(
			'text'    => '',
			'class'   => '',
			'result'  => true,
		);

		// Show the current effective calculated permission considering current group, path and cascade.

		try
		{
			// Get the asset id by the name of the component.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('id'))
				->from($this->_db->quoteName('#__assets'))
				->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote($permission['component']));

			$this->_db->setQuery($query);

			$assetId = (int) $this->_db->loadResult();

			// Fetch the parent asset id.
			$parentAssetId = null;

			/**
			 * @to do: incorrect info
			 * When creating a new item (not saving) it uses the calculated permissions from the component (item <-> component <-> global config).
			 * But if we have a section too (item <-> section(s) <-> component <-> global config) this is not correct.
			 * Also, currently it uses the component permission, but should use the calculated permissions for achild of the component/section.
			 */

			// If not in global config we need the parent_id asset to calculate permissions.
			if (!$isGlobalConfig)
			{
				// In this case we need to get the component rules too.
				$query->clear()
					->select($this->_db->quoteName('parent_id'))
					->from($this->_db->quoteName('#__assets'))
					->where($this->_db->quoteName('id') . ' = ' . $assetId);

				$this->_db->setQuery($query);

				$parentAssetId = (int) $this->_db->loadResult();
			}

			// Get the group parent id of the current group.
			$query->clear()
				->select($this->_db->quoteName('parent_id'))
				->from($this->_db->quoteName('#__usergroups'))
				->where($this->_db->quoteName('id') . ' = ' . (int) $permission['rule']);

			$this->_db->setQuery($query);

			$parentGroupId = (int) $this->_db->loadResult();

			// Count the number of child groups of the current group.
			$query->clear()
				->select('COUNT(' . $this->_db->quoteName('id') . ')')
				->from($this->_db->quoteName('#__usergroups'))
				->where($this->_db->quoteName('parent_id') . ' = ' . (int) $permission['rule']);

			$this->_db->setQuery($query);

			$totalChildGroups = (int) $this->_db->loadResult();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Clear access statistics.
		JAccess::clearStatics();

		// After current group permission is changed we need to check again if the group has Super User permissions.
		$isSuperUserGroupAfter = JAccess::checkGroup($permission['rule'], 'core.admin');

		// Get the rule for just this asset (non-recursive) and get the actual setting for the action for this group.
		$assetRule = JAccess::getAssetRules($assetId, false, false)->allow($permission['action'], $permission['rule']);

		// Get the group, group parent id, and group global config recursive calculated permission for the chosen action.
		$inheritedGroupRule = JAccess::checkGroup($permission['rule'], $permission['action'], $assetId);

		if (!empty($parentAssetId))
		{
			$inheritedGroupParentAssetRule = JAccess::checkGroup($permission['rule'], $permission['action'], $parentAssetId);
		}
		else
		{
			$inheritedGroupParentAssetRule = null;
		}

		$inheritedParentGroupRule = !empty($parentGroupId) ? JAccess::checkGroup($parentGroupId, $permission['action'], $assetId) : null;

		// Current group is a Super User group, so calculated setting is "Allowed (Super User)".
		if ($isSuperUserGroupAfter)
		{
			$result['class'] = 'label label-success';
			$result['text'] = '<span class="icon-lock icon-white" aria-hidden="true"></span>' . JText::_('JLIB_RULES_ALLOWED_ADMIN');
		}
		// Not super user.
		else
		{
			// First get the real recursive calculated setting and add (Inherited) to it.

			// If recursive calculated setting is "Denied" or null. Calculated permission is "Not Allowed (Inherited)".
			if ($inheritedGroupRule === null || $inheritedGroupRule === false)
			{
				$result['class'] = 'label label-important';
				$result['text']  = JText::_('JLIB_RULES_NOT_ALLOWED_INHERITED');
			}
			// If recursive calculated setting is "Allowed". Calculated permission is "Allowed (Inherited)".
			else
			{
				$result['class'] = 'label label-success';
				$result['text']  = JText::_('JLIB_RULES_ALLOWED_INHERITED');
			}

			// Second part: Overwrite the calculated permissions labels if there is an explicity permission in the current group.

			/**
			 * @to do: incorrect info
			 * If a component has a permission that doesn't exists in global config (ex: frontend editing in com_modules) by default
			 * we get "Not Allowed (Inherited)" when we should get "Not Allowed (Default)".
			 */

			// If there is an explicitly permission "Not Allowed". Calculated permission is "Not Allowed".
			if ($assetRule === false)
			{
				$result['class'] = 'label label-important';
				$result['text']  = JText::_('JLIB_RULES_NOT_ALLOWED');
			}
			// If there is an explicitly permission is "Allowed". Calculated permission is "Allowed".
			elseif ($assetRule === true)
			{
				$result['class'] = 'label label-success';
				$result['text']  = JText::_('JLIB_RULES_ALLOWED');
			}

			// Third part: Overwrite the calculated permissions labels for special cases.

			// Global configuration with "Not Set" permission. Calculated permission is "Not Allowed (Default)".
			if (empty($parentGroupId) && $isGlobalConfig === true && $assetRule === null)
			{
				$result['class'] = 'label label-important';
				$result['text']  = JText::_('JLIB_RULES_NOT_ALLOWED_DEFAULT');
			}

			/**
			 * Component/Item with explicit "Denied" permission at parent Asset (Category, Component or Global config) configuration.
			 * Or some parent group has an explicit "Denied".
			 * Calculated permission is "Not Allowed (Locked)".
			 */
/*			elseif ($inheritedGroupParentAssetRule === false || $inheritedParentGroupRule === false)
			{
				$result['class'] = 'label label-important';
				$result['text']  = '<span class="icon-lock icon-white" aria-hidden="true"></span>' . JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED');
			}
*/		}

		// If removed or added super user from group, we need to refresh the page to recalculate all settings.
		if ($isSuperUserGroupBefore != $isSuperUserGroupAfter)
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOTICE_RECALCULATE_GROUP_PERMISSIONS'), 'notice');
		}

		// If this group has child groups, we need to refresh the page to recalculate the child settings.
		if ($totalChildGroups > 0)
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOTICE_RECALCULATE_GROUP_CHILDS_PERMISSIONS'), 'notice');
		}

		return $result;
	}

	/**
	 * @param $permission
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	private function getSectionNameFromPermissions($permission)
	{
		$parts = explode('.', $permission['component']);

		if (isset($parts[1]))
		{
			return $parts[1];
		}

		return '';
	}
}
