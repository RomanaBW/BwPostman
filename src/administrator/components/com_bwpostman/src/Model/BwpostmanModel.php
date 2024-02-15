<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main model for backend.
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
use Joomla\CMS\Access\Access;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Asset;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Access\Rules;

/**
 * BwPostman cover page model
 *
 * @package		BwPostman-Admin
 * @subpackage	CoverPage
 *
 * @since       0.9.1
 */
class BwpostmanModel extends BaseDatabaseModel
{
	/**
	 * @var MVCFactoryInterface
	 *
	 * @since       4.0.0
	 */
	protected $factory;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$this->factory  = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param string $name    The table type to instantiate
	 * @param string $prefix  A prefix for the table class name. Optional.
	 * @param array  $options Configuration array for model. Optional.
	 *
	 * @return    boolean|Table    A database object
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0
	 */
	public function getTable($name = 'Newsletter', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to get general statistic data
	 *
	 * @return 	array       associative array of general statistics data
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function getGeneraldata(): array
	{
		try
		{
			$general = array();

		// Get # of all unsent newsletters
		$nlTable = $this->getTable();
		$general['nl_unsent'] = $nlTable->getNbrOfNewsletters(false, false);

		// Get # of all sent newsletters
		$general['nl_sent'] = $nlTable->getNbrOfNewsletters(true, false);

		// Get # of all subscribers
		$subsTable = $this->getTable('Subscriber');
		$general['sub'] = $subsTable->getNbrOfSubscribers(false, false);

		// Get # of all test-recipients
		$general['test'] = $subsTable->getNbrOfSubscribers(true, false);

		// Get # of all campaigns
		$camTable = $this->getTable('Campaign');
		$general['cam'] = $camTable->getNbrOfCampaigns(false);

		// Get # of all published mailinglists
		// get available mailinglists to predefine for state
		$mlTable = $this->getTable('Mailinglist');
		$ml_available = $mlTable->getMailinglistsByRestriction(array(), 'available', 0, false);

		// get unavailable mailinglists to predefine for state
		$ml_unavailable = $mlTable->getMailinglistsByRestriction(array(), 'unavailable', 0, false);

		$general['ml_published'] = count($ml_available) + count($ml_unavailable);

		// Get # of all unpublished mailinglists
		// get internal mailinglists to predefine for state
		$ml_intern = $mlTable->getMailinglistsByRestriction(array(), 'internal', 0, false);

		$general['ml_unpublished'] = count($ml_intern);

		// Get # of all html templates
		$tplTable = $this->getTable('Template');
		$general['html_templates'] = $tplTable->getNbrOfTemplates('html', false);

		// Get # of all text templates
		$general['text_templates'] = $tplTable->getNbrOfTemplates('text', false);

			// Get total # of general statistic
			$general[] = array_sum($general);
		}
		catch (Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'BwPostmanModel BE');

            $message = Text::_('COM_BWPOSTMAN_ERROR_GENERAL_STATISTICS_DATA_ERROR');
			Factory::getApplication()->enqueueMessage($message, 'error');
		}

		return $general;
	}

	/**
	 * Method to get archive statistic data
	 *
	 * @return 	array       associative array of archive statistics data
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getArchivedata(): array
	{
		try
		{
			$archive	= array();

		// Get # of all archived newsletters
		$nlTable = $this->getTable();
		$archive['arc_nl'] = $nlTable->getNbrOfNewsletters(false, true);

		// Get # of all archived subscribers
		$subsTable = $this->getTable('Subscriber');
		$archive['arc_sub'] = $subsTable->getNbrOfSubscribers(false, true);

		// Get # of all archived campaigns
		$camTable = $this->getTable('Campaign');
		$archive['arc_cam'] = $camTable->getNbrOfCampaigns(true);

		// Get # of all archived mailinglists
		// get available mailinglists to predefine for state
		$mlTable = $this->getTable('Mailinglist');
		$ml_archived = $mlTable->getMailinglistsByRestriction(array(), 'available', 1, false);

		$archive['arc_ml'] = count($ml_archived);

		// Get # of all html templates
		$tplTable = $this->getTable('Template');
		$archive['arc_html_templates'] = $tplTable->getNbrOfTemplates('html', true);

		// Get # of all text templates
		$archive['arc_text_templates'] = $tplTable->getNbrOfTemplates('text', true);

			// Get total # of general statistic
			$archive[] = array_sum($archive);
		}
		catch (Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'BwPostmanModel BE');

            $message = Text::_('COM_BWPOSTMAN_ERROR_ARCHIVE_STATISTICS_DATA_ERROR');
			Factory::getApplication()->enqueueMessage($message, 'error');
		}

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
	 * @param string|null $permission Need an array with Permissions (component, rule, value and title)
	 *
	 * @return  array|boolean  A list of result data or false
	 *
	 * @throws Exception
	 *
	 * @since   2.0
	 */
	public function storePermissions(string $permission = null)
	{
		$app  = Factory::getApplication();
		$db   = $this->_db;
		$user = $app->getIdentity();

		$statePermissions = $app->getUserState('com_bwpm.permissions', []);

		if (is_null($permission))
		{
			// Get data from input.
			$input = $app->input->Json;

			$permission = array(
				'component' => $input->get('comp'),
				'action'    => $input->get('action'),
				'rule'      => $input->get('rule'),
				'value'     => $input->get('value'),
				'title'     => $input->get('title', '', 'RAW')
			);
		}

		// We are creating a new item, so we don't have an item id so don't allow.
		if (substr($permission['component'], -6) === '.false')
		{
			$app->enqueueMessage(Text::_('JLIB_RULES_SAVE_BEFORE_CHANGE_PERMISSIONS'), 'error');

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
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

			return false;
		}

		$permission['component'] = empty($permission['component']) ? 'root.1' : $permission['component'];

		// Current view is global config?
		$isGlobalConfig = $permission['component'] === 'root.1';

		// Check if changed group has Super User permissions.
		$isSuperUserGroupBefore = Access::checkGroup($permission['rule'], 'core.admin');

		// Check if current user belongs to changed group.
		$currentUserBelongsToGroup = in_array((int) $permission['rule'], $user->groups);

		// Get current user groups tree.
		$currentUserGroupsTree = Access::getGroupsByUser($user->id, true);

		// Check if current user belongs to changed group.
		$currentUserSuperUser = $user->authorise('core.admin');

		// If user is not Super User cannot change the permissions of a group it belongs to.
		if (!$currentUserSuperUser && $currentUserBelongsToGroup)
		{
			$app->enqueueMessage(Text::_('JLIB_USER_ERROR_CANNOT_CHANGE_OWN_GROUPS'), 'error');

			return false;
		}

		// If user is not Super User cannot change the permissions of a group it belongs to.
		if (!$currentUserSuperUser && in_array((int) $permission['rule'], $currentUserGroupsTree))
		{
			$app->enqueueMessage(Text::_('JLIB_USER_ERROR_CANNOT_CHANGE_OWN_PARENT_GROUPS'), 'error');

			return false;
		}

		// If user is not Super User cannot change the permissions of a Super User Group.
		if (!$currentUserSuperUser && $isSuperUserGroupBefore && !$currentUserBelongsToGroup)
		{
			$app->enqueueMessage(Text::_('JLIB_USER_ERROR_CANNOT_CHANGE_SUPER_USER'), 'error');

			return false;
		}

		// If user is not Super User cannot change the Super User permissions in any group it belongs to.
		if ($isSuperUserGroupBefore && $currentUserBelongsToGroup && $permission['action'] === 'core.admin')
		{
			$app->enqueueMessage(Text::_('JLIB_USER_ERROR_CANNOT_DEMOTE_SELF'), 'error');

			return false;
		}

		try
		{
			$asset = new Asset($db);
			$result = $asset->loadByName($permission['component']);

			if ($result === false)
			{
				// @ToDo: Check this path
				$data = array($permission['action'] => array($permission['rule'] => $permission['value']));

				$rules        = new Rules($data);
				$asset->rules = (string) $rules;
				$asset->name  = (string) $permission['component'];
				$asset->title = (string) $permission['title'];

				// Get the parent asset id, so we have a correct tree.
				$parentAsset  = new Asset($db);

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
				 * When changing a permission of an item that doesn't have a row in the asset table a new row is created.
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
				$app->enqueueMessage(Text::_('JLIB_UNKNOWN'), 'error');

				return false;
			}
		}
		catch (Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'BwPostmanModel BE');

            $app->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		// All checks done.
		$result = array(
			'text'   => '',
			'class'  => '',
			'result' => true,
		);

		// Show the current effective calculated permission considering current group, path and cascade.

		try
		{
			// Get the asset id by the name of the component.
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('name') . ' = ' . $db->quote($permission['component']));

			$db->setQuery($query);

			$assetId = (int) $db->loadResult();

			// Fetch the parent asset id.
			$parentAssetId = null;

			/**
			 * @to do: incorrect info
			 * When creating a new item (not saving) it uses the calculated permissions from the component (item <-> component <-> global config).
			 * But if we have a section too (item <-> section(s) <-> component <-> global config) this is not correct.
			 * Also, currently it uses the component permission, but should use the calculated permissions for a child of the component/section.
			 */

			// Get the group parent id of the current group.
			$query->clear()
				->select($db->quoteName('parent_id'))
				->from($db->quoteName('#__usergroups'))
				->where($db->quoteName('id') . ' = ' . (int) $permission['rule']);

			$db->setQuery($query);

			$parentGroupId = (int) $db->loadResult();

			// Count the number of child groups of the current group.
			$query->clear()
				->select('COUNT(' . $db->quoteName('id') . ')')
				->from($db->quoteName('#__usergroups'))
				->where($db->quoteName('parent_id') . ' = ' . (int) $permission['rule']);

			$db->setQuery($query);

			$totalChildGroups = (int) $db->loadResult();
		}
		catch (Exception $exception)
		{
            BwPostmanHelper::logException($exception, 'BwPostmanModel BE');

            $app->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		// Clear access statistics.
		Access::clearStatics();

		// After current group permission is changed we need to check again if the group has Super User permissions.
		$isSuperUserGroupAfter = Access::checkGroup($permission['rule'], 'core.admin');

		// Get the rule for just this asset (non-recursive) and get the actual setting for the action for this group.
		$assetRule = Access::getAssetRules($assetId, false, false)->allow($permission['action'], $permission['rule']);

		// Get the group, group parent id, and group global config recursive calculated permission for the chosen action.
		$inheritedGroupRule = Access::checkGroup($permission['rule'], $permission['action'], $assetId);

		// Current group is a Super User group, so calculated setting is "Allowed (Super User)".
		if ($isSuperUserGroupAfter)
		{
			$result['class'] = 'badge bg-success';
			$result['text'] = '<span class="icon-lock icon-white" aria-hidden="true"></span>' . Text::_('JLIB_RULES_ALLOWED_ADMIN');
		}
		// Not superuser.
		else
		{
			// First get the real recursive calculated setting and add (Inherited) to it.

			// If recursive calculated setting is "Denied" or null. Calculated permission is "Not Allowed (Inherited)".
			if ($inheritedGroupRule === null || $inheritedGroupRule === false)
			{
				$result['class'] = 'badge bg-danger';
				$result['text']  = Text::_('JLIB_RULES_NOT_ALLOWED_INHERITED');
			}
			// If recursive calculated setting is "Allowed". Calculated permission is "Allowed (Inherited)".
			else
			{
				$result['class'] = 'badge bg-success';
				$result['text']  = Text::_('JLIB_RULES_ALLOWED_INHERITED');
			}

			// Second part: Overwrite the calculated permissions labels if there is an explicit permission in the current group.

			/**
			 * @to do: incorrect info
			 * If a component has a permission that doesn't exist in global config (ex: frontend editing in com_modules) by default
			 * we get "Not Allowed (Inherited)" when we should get "Not Allowed (Default)".
			 */

			// If there is an explicit permission "Not Allowed". Calculated permission is "Not Allowed".
			if ($assetRule === false)
			{
				$result['class'] = 'badge bg-danger';
				$result['text']  = Text::_('JLIB_RULES_NOT_ALLOWED');
			}
			// If there is an explicit permission is "Allowed". Calculated permission is "Allowed".
			elseif ($assetRule === true)
			{
				$result['class'] = 'badge bg-success';
				$result['text']  = Text::_('JLIB_RULES_ALLOWED');
			}

			// Third part: Overwrite the calculated permissions labels for special cases.

			// Global configuration with "Not Set" permission. Calculated permission is "Not Allowed (Default)".
			if (empty($parentGroupId) && $isGlobalConfig === true && $assetRule === null)
			{
				$result['class'] = 'badge bg-danger';
				$result['text']  = Text::_('JLIB_RULES_NOT_ALLOWED_DEFAULT');
			}
		}

		// If removed or added superuser from group, we need to refresh the page to recalculate all settings.
		if ($isSuperUserGroupBefore != $isSuperUserGroupAfter)
		{
			$app->enqueueMessage(Text::_('JLIB_RULES_NOTICE_RECALCULATE_GROUP_PERMISSIONS'), 'notice');
		}

		// If this group has child groups, we need to refresh the page to recalculate the child settings.
		if ($totalChildGroups > 0)
		{
			$app->enqueueMessage(Text::_('JLIB_RULES_NOTICE_RECALCULATE_GROUP_CHILDS_PERMISSIONS'), 'notice');
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
	private function getSectionNameFromPermissions($permission): string
	{
		$parts = explode('.', $permission['component']);

		if (isset($parts[1]))
		{
			return $parts[1];
		}

		return '';
	}
}
