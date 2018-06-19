<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman form field for rules.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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

defined('JPATH_PLATFORM') or die;

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/access/BwAccess.php');

JFormHelper::loadFieldClass('rules');

/**
 * Form Field class for the Joomla Platform.
 * Field for assigning permissions to groups for a given asset
 *
 * @see    JAccess
 * @since  2.0.0
 */
class JFormFieldBwRules extends JFormFieldRules
{
	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'section':
			case 'component':
			case 'assetField':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to get the field input markup for Access Control Lists.
	 * Optionally can be associated with a specific component and section.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 * @todo:   Add access check.
	 */
	protected function getInput()
	{
		JHtml::_('bootstrap.tooltip');

		// Add Javascript for permission change
		JHtml::_('script', 'system/permissions.js', array('version' => 'auto', 'relative' => true));

		// Load JavaScript message titles
		JText::script('ERROR');
		JText::script('WARNING');
		JText::script('NOTICE');
		JText::script('MESSAGE');

		// Add strings for JavaScript error translations.
		JText::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
		JText::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
		JText::script('JLIB_JS_AJAX_ERROR_OTHER');
		JText::script('JLIB_JS_AJAX_ERROR_PARSE');
		JText::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

		// Initialise some field attributes.
		$section    = $this->section;
		$assetField = $this->assetField;
		$component  = empty($this->component) ? 'root.1' : $this->component;

		// Current view is global config?
		$isGlobalConfig = $component === 'root.1';

		// Get the actions for the asset.
		$actions = BwAccess::getActions($component, $section);

		// Iterate over the children and add to the actions.
		foreach ($this->element->children() as $el)
		{
			if ($el->getName() == 'action')
			{
				$actions[] = (object) array(
					'name' => (string) $el['name'],
					'title' => (string) $el['title'],
					'description' => (string) $el['description'],
				);
			}
		}

		// Get the asset id.
		// Note that for global configuration, com_config injects asset_id = 1 into the form.
		$assetId       = $this->form->getValue($assetField);
		$newItem       = empty($assetId) && $isGlobalConfig === false && $section !== 'component';
		$parentAssetId = null;

		// If the asset id is empty (component or new item).
		if (empty($assetId))
		{
			// Get the section or component asset id as fallback.
			// Workaround because Joomla does not use section
			$parentAssetName = $component;

			if ($section != '')
			{
				$parentAssetName .= '.' . $section;
			}

			$_db = JFactory::getDbo();
			$query = $_db->getQuery(true);
			$query->clear();

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__assets'));
			$query->where($_db->quoteName('name') . ' = ' . $_db->quote($parentAssetName));

			$_db->setQuery($query);

			$assetId = (int) $_db->loadResult();
		}

		// If not in global config we need the parent_id asset to calculate permissions.
		if (!$isGlobalConfig)
		{
			// In this case we need to get the component rules too.
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select($db->quoteName('parent_id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('id') . ' = ' . $assetId);

			$db->setQuery($query);

			$parentAssetId = (int) $db->loadResult();
		}

		// Full width format.

		// Get the rules for just this asset (non-recursive).
		$assetRules = BwAccess::getAssetRules($assetId, false, false);

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Ajax request data.
		$ajaxUri = JRoute::_('index.php?option=com_bwpostman&task=storePermission&format=json&' . JSession::getFormToken() . '=1');

		// Prepare output
		$html = array();

		// Description
		$html[] = '<p class="rule-desc">' . JText::_('JLIB_RULES_SETTINGS_DESC') . '</p>';

		// Begin tabs
		$html[] = '<div class="tabbable tabs-left" data-ajaxuri="' . $ajaxUri . '" id="permissions-sliders">';

		// Building tab nav
		$html[] = '<ul class="nav nav-tabs">';

		foreach ($groups as $group)
		{
			// Initial Active Tab
			$active = (int) $group->value === 1 ? ' class="active"' : '';

			$html[] = '<li' . $active . '>';
			$html[] = '<a href="#permission-' . $group->value . '" data-toggle="tab">';
			$html[] = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $group->level + 1)) . $group->text;
			$html[] = '</a>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		$html[] = '<div class="tab-content">';

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			// Initial Active Pane
			$active = (int) $group->value === 1 ? ' active' : '';

			$html[] = '<div class="tab-pane' . $active . '" id="permission-' . $group->value . '">';
			$html[] = '<table class="table table-striped">';
			$html[] = '<thead>';
			$html[] = '<tr>';

			$html[] = '<th class="actions" id="actions-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_ACTION') . '</span>';
			$html[] = '</th>';

			$html[] = '<th class="settings" id="settings-th' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_SELECT_SETTING') . '</span>';
			$html[] = '</th>';

			$html[] = '<th id="aclactionth' . $group->value . '">';
			$html[] = '<span class="acl-action">' . JText::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
			$html[] = '</th>';

			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody>';

			// Check if this group has super user permissions
			$isSuperUserGroup = BwAccess::checkGroup($group->value, 'core.admin');

			foreach ($actions as $action)
			{
				if (strpos($action->name, 'create') !== false)
				{
					continue;
				}

				$html[] = '<tr>';
				$html[] = '<td headers="actions-th' . $group->value . '">';
				$html[] = '<label for="' . $this->id . '_' . $action->name . '_' . $group->value . '" class="hasTooltip" title="'
					. JHtml::_('tooltipText', $action->title, $action->description) . '">';
				$html[] = JText::_($action->title);
				$html[] = '</label>';
				$html[] = '</td>';

				$html[] = '<td headers="settings-th' . $group->value . '">';

				$html[] = '<select onchange="sendPermissions.call(this, event)" data-chosen="true" class="input-small novalidate"'
					. ' name="' . $this->name . '[' . $action->name . '][' . $group->value . ']"'
					. ' id="' . $this->id . '_' . $action->name	. '_' . $group->value . '"'
					. ' title="' . strip_tags(
						JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text))
					) . '">';

				/**
				 * Possible values:
				 * null = not set means inherited
				 * false = denied
				 * true = allowed
				 */

				// Get the actual setting for the action for this group.
				$assetRule = $newItem === false ? $assetRules->allow($action->name, $group->value) : null;

				// Build the dropdown for the permissions sliders

				// The parent group has "Not Set", all children can rightly "Inherit" from that.
				$html[] = '<option value="" ' . ($assetRule === null ? ' selected="selected"' : '') . '>'
					. JText::_(empty($group->parent_id) && $isGlobalConfig ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
				$html[] = '<option value="1" ' . ($assetRule === true ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_ALLOWED')
					. '</option>';
				$html[] = '<option value="0" ' . ($assetRule === false ? ' selected="selected"' : '') . '>' . JText::_('JLIB_RULES_DENIED')
					. '</option>';

				$html[] = '</select>&#160; ';

				$html[] = '<span id="icon_' . $this->id . '_' . $action->name . '_' . $group->value . '"></span>';
				$html[] = '</td>';

				// Build the Calculated Settings column.
				$html[] = '<td headers="aclactionth' . $group->value . '">';

				$result = array();

				// Get the group, group parent id, and group global config recursive calculated permission for the chosen action.
				$inheritedGroupRule            = BwAccess::checkGroup((int) $group->value, $action->name, $assetId);
				$inheritedGroupParentAssetRule = !empty($parentAssetId) ? BwAccess::checkGroup($group->value, $action->name, $parentAssetId) : null;
				$inheritedParentGroupRule      = !empty($group->parent_id) ? BwAccess::checkGroup($group->parent_id, $action->name, $assetId) : null;

				// Current group is a Super User group, so calculated setting is "Allowed (Super User)".
				if ($isSuperUserGroup)
				{
					$result['class'] = 'label label-success';
					$result['text'] = '<span class="icon-lock icon-white"></span>' . JText::_('JLIB_RULES_ALLOWED_ADMIN');
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

					// Second part: Overwrite the calculated permissions labels if there is an explicit permission in the current group.

					/**
					 * @to do: incorrect info
					 * If a component has a permission that doesn't exists in global config (ex: frontend editing in com_modules) by default
					 * we get "Not Allowed (Inherited)" when we should get "Not Allowed (Default)".
					 */

					// If there is an explicit permission "Not Allowed". Calculated permission is "Not Allowed".
					if ($assetRule === false)
					{
						$result['class'] = 'label label-important';
						$result['text']  = JText::_('JLIB_RULES_NOT_ALLOWED');
					}
					// If there is an explicit permission is "Allowed". Calculated permission is "Allowed".
					elseif ($assetRule === true)
					{
						$result['class'] = 'label label-success';
						$result['text']  = JText::_('JLIB_RULES_ALLOWED');
					}

					// Third part: Overwrite the calculated permissions labels for special cases.

					// Global configuration with "Not Set" permission. Calculated permission is "Not Allowed (Default)".
					elseif (empty($group->parent_id) && $isGlobalConfig === true && $assetRule === null
						|| $inheritedGroupParentAssetRule === false || $inheritedParentGroupRule === false)
					{
						$result['class'] = 'label label-important';
						$result['text']  = JText::_('JLIB_RULES_NOT_ALLOWED_DEFAULT');
					}

					/**
					 * Component/Item with explicit "Denied" permission at parent Asset (Category, Component or Global config) configuration.
					 * Or some parent group has an explicit "Denied".
					 * Calculated permission is "Not Allowed (Locked)".
					 */
/*					elseif ($inheritedGroupParentAssetRule === false || $inheritedParentGroupRule === false)
					{
						$result['class'] = 'label label-important';
						$result['text']  = '<span class="icon-lock icon-white"></span>' . JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED');
					}
*/				}

				$html[] = '<span class="' . $result['class'] . '">' . $result['text'] . '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}

			$html[] = '</tbody>';
			$html[] = '</table></div>';
		}

		$html[] = '</div></div>';
		$html[] = '<div class="clr"></div>';
		$html[] = '<div class="alert">';

		if ($section === 'component' || !$section)
		{
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES');
		}
		else
		{
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES_ITEM');
		}

		$html[] = '</div>';

		return implode("\n", $html);
	}
}
