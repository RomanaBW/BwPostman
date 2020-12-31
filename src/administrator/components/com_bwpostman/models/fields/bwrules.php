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

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwAccess;

//require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/access/BwAccess.php');

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
		HtmlHelper::_('bootstrap.tooltip');

		// Add Javascript for permission change
		HTMLHelper::_('form.csrf');
		Factory::getDocument()->getWebAssetManager()
			->useStyle('webcomponent.field-permissions')
			->useScript('webcomponent.field-permissions')
			->useStyle('webcomponent.joomla-tab')
			->useScript('webcomponent.joomla-tab');

		// Load JavaScript message titles
		Text::script('ERROR');
		Text::script('WARNING');
		Text::script('NOTICE');
		Text::script('MESSAGE');

		// Add strings for JavaScript error translations.
		Text::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
		Text::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
		Text::script('JLIB_JS_AJAX_ERROR_OTHER');
		Text::script('JLIB_JS_AJAX_ERROR_PARSE');
		Text::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

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

		// Remove action create, at item level not needed
		for ($i = 0; $i <= count($actions); $i++)
		{
			$action      = $actions[$i];
			$actionParts = explode('.', $action->name);
			if ($actionParts[2] === 'create')
			{
				unset($actions[$i]);
			}
		}

		// Get the asset id.
		// Note that for global configuration, com_config injects asset_id = 1 into the form.
		$assetId       = $this->form->getValue($assetField);
		$newItem       = empty($assetId) && $isGlobalConfig === false && $section !== 'component';
		$parentAssetId = null;

		$assetId = $this->checkAssetId($assetId);

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

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();

			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__assets'));
			$query->where($db->quoteName('name') . ' = ' . $db->quote($parentAssetName));

			try
			{
				$db->setQuery($query);

				$assetId = (int) $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		// If not in global config we need the parent_id asset to calculate permissions.
		if (!$isGlobalConfig)
		{
			// In this case we need to get the section rules too.
			$db = Factory::getDbo();

			$query = $db->getQuery(true)
				->select($db->quoteName('parent_id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('id') . ' = ' . $assetId);

			try
			{
				$db->setQuery($query);

				$parentAssetId = (int) $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		// Full width format.

		// Get the rules for this asset (recursive only for section).
		$assetRules = BwAccess::getAssetRules($assetId, true, false);

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Ajax request data.
		$ajaxUri = Route::_('index.php?option=com_bwpostman&task=storePermission&format=json&' . Session::getFormToken() . '=1');

		// Prepare output
		$html = array();

		// Begin tabs
// Description
		$html[] = '<details>';
		$html[] = '	<summary class="rule-notes">';
		$html[] = Text::_('JLIB_RULES_SETTINGS_DESC');
		$html[] = '	</summary>';
		$html[] = '	<div class="rule-notes">';

		if ($section === 'component' || !$section)
		{
			$html[] = Text::_('JLIB_RULES_SETTING_NOTES');
		}
		else
		{
			$html[] = Text::_('JLIB_RULES_SETTING_NOTES_ITEM');
		}
		$html[] = '	</div>';
		$html[] = '</details>';

		$html = $this->getTabs($ajaxUri, $html, $groups, $actions, $newItem, $assetRules, $isGlobalConfig, $assetId);

		return implode("\n", $html);
	}

	/**
	 * @param           $ajaxUri
	 * @param array     $html
	 * @param array     $groups
	 * @param array     $actions
	 * @param           $newItem
	 * @param Rules     $assetRules
	 * @param           $isGlobalConfig
	 * @param           $assetId
	 *
	 * @return array
	 *
	 * @since 2.4.0
	 */
	protected function getTabs(
		$ajaxUri,
		array $html,
		array $groups,
		array $actions,
		$newItem,
		Rules $assetRules,
		$isGlobalConfig,
		$assetId
	) {
		$html[] = '<joomla-field-permissions class="row mb-2" data-uri="' . $ajaxUri . '">';
		$html[] = '	<joomla-tab orientation="vertical" id="permissions-sliders">';
		// Initial Active Pane
		foreach ($groups as $group) :
			$active = (int) $group->value === 1 ? ' active' : '';
			$html[] = '<section class="tab-pane' . $active . '" name="' . htmlentities(LayoutHelper::render('joomla.html.treeprefix', array('level' => $group->level + 1)), ENT_COMPAT, 'utf-8') . $group->text . '" id="permission-' . $group->value . '">';
			$html[] = '	<table class="table">';
			$html[] = '		<thead>';
			$html[] = '			<tr>';
			$html[] = '				<th class="actions" id="actions-th' . $group->value . '">';
			$html[] = '					<span class="acl-action">' . Text::_('JLIB_RULES_ACTION') . '</span>';
			$html[] = '				</th>';

			$html[] = '				<th class="settings" id="settings-th' . $group->value . '">';
			$html[] = '					<span class="acl-action">' . Text::_('JLIB_RULES_SELECT_SETTING') . '</span>';
			$html[] = '				</th>';

			$html[] = '				<th id="aclaction-th' . $group->value . '">';
			$html[] = '					<span class="acl-action">' . Text::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
			$html[] = '				</th>';
			$html[] = '			</tr>';
			$html[] = '		</thead>';
			$html[] = '		<tbody>';

			// Check if this group has super user permissions
			$isSuperUserGroup = BwAccess::checkGroup($group->value, 'core.admin');

			foreach ($actions as $action) :
				$description = '';
				if (!empty($action->description))
				{
					$description = ' class="hasTooltip" title="' . HTMLHelper::_('tooltipText', $action->title, $action->description) . '"';
				}
				$html[] = '				<tr>';
// Action column
				$html[] = '					<td headers="actions-th' . $group->value . '">';
				$html[] = '						<label for="' . $this->id . '_' . $action->name . '_' . $group->value . '" ' . $description . '>' .
					Text::_($action->title) .
					'			        </label>';
				$html[] = '					</td>';

// Select list column
				$html[] = '					<td headers="settings-th' . $group->value . '">';
				$html[] = '						<div class="d-flex align-items-center">';
				$html[] = '							<select data-onchange-task="permissions.apply"
												class="custom-select novalidate"
												name="' . $this->name . '[' . $action->name . '][' . $group->value . ']"
												id="' . $this->id . '_' . $action->name . '_' . $group->value . '" >';

				/**
				 * Possible values:
				 * null = not set means inherited
				 * false = denied
				 * true = allowed
				 */

				// Get the actual setting for the action for this group.
				$assetRule = $newItem === false ? $assetRules->allow($action->name, $group->value) : null;

				// Build the dropdowns for the permissions sliders
				// The parent group has "Not Set", all children can rightly "Inherit" from that.
				$selected = '';
				if ($assetRule === null)
				{
					$selected = ' selected="selected"';
				}
				$html[] = '								<option value="" ' . $selected . '>';
				$html[] = 								Text::_(empty($group->parent_id) && $isGlobalConfig ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
				$selected = '';

				if ($assetRule === true)
				{
					$selected = ' selected="selected"';
				}
				$html[] = '								<option value="1" ' . $selected . '>';
				$html[] =								Text::_('JLIB_RULES_ALLOWED') . '</option>';
				$selected = '';

				if ($assetRule === false)
				{
					$selected = ' selected="selected"';
				}
				$html[] = '								<option value="0" ' . $selected . '>';
				$html[] =								Text::_('JLIB_RULES_DENIED') . '</option>';

				$html[] = '							</select>&#160;';
				$html[] = '							<span id="icon_' . $this->id . '_' . $action->name . '_' . $group->value . '"></span>';
				$html[] = '						</div>';
				$html[] = '					</td>';

// calculated column
				$html[] = '					<td headers="aclaction-th<?php echo $group->value; ?>">';
				$result = array();
				// Get the group, group parent id, and group global config recursive calculated permission for the chosen action.

				$inheritedGroupRule = BwAccess::checkGroup((int) $group->value, $action->name, $assetId);

				// Current group is a Super User group, so calculated setting is "Allowed (Super User)".
				if ($isSuperUserGroup)
				{
					$result['class'] = 'badge badge-success';
					$result['text']  = '<span class="fa fa-lock icon-white" aria-hidden="true"></span>' . Text::_('JLIB_RULES_ALLOWED_ADMIN');
				}
				else
				{
					// First get the real recursive calculated setting and add (Inherited) to it.

					// If recursive calculated setting is "Denied" or null. Calculated permission is "Not Allowed (Inherited)".
					if ($inheritedGroupRule === null || $inheritedGroupRule === false)
					{
						$result['class'] = 'badge badge-danger';
						$result['text']  = Text::_('JLIB_RULES_NOT_ALLOWED_INHERITED');
					}
					// If recursive calculated setting is "Allowed". Calculated permission is "Allowed (Inherited)".
					else
					{
						$result['class'] = 'badge badge-success';
						$result['text']  = Text::_('JLIB_RULES_ALLOWED_INHERITED');
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
						$result['class'] = 'badge badge-danger';
						$result['text']  = 	Text::_('JLIB_RULES_NOT_ALLOWED');
					}
					// If there is an explicit permission is "Allowed". Calculated permission is "Allowed".
					elseif ($assetRule === true)
					{
						$result['class'] = 'badge badge-success';
						$result['text']  = Text::_('JLIB_RULES_ALLOWED');
					}

					// Third part: Overwrite the calculated permissions labels for special cases.

					// Global configuration with "Not Set" permission. Calculated permission is "Not Allowed (Default)".
					if (empty($group->parent_id) && $isGlobalConfig === true && $assetRule === null)
					{
						$result['class'] = 'badge badge-danger';
						$result['text']  = Text::_('JLIB_RULES_NOT_ALLOWED_DEFAULT');
					}

					/**
					 * Component/Item with explicit "Denied" permission at parent Asset (Category, Component or Global config) configuration.
					 * Or some parent group has an explicit "Denied".
					 * Calculated permission is "Not Allowed (Locked)".
					 */
//										elseif ($inheritedGroupParentAssetRule === false || $inheritedParentGroupRule === false)
//										{
//											$result['class'] = 'badge badge-danger';
//											$result['text']  = '<span class="fa fa-lock icon-white" aria-hidden="true"></span>'. Text::_('JLIB_RULES_NOT_ALLOWED_LOCKED');
//										}
				}

				$html[] = '						<span class="' . $result['class'] . '">' . $result['text'] . '</span>';
				$html[] = '					</td>';
				$html[] = '				</tr>';
			endforeach;
			$html[] = '		</tbody>';
			$html[] = '	</table>';
			$html[] = '</section>';
		endforeach;
		$html[] = '</joomla-tab>';
		$html[] = '</joomla-field-permissions>';

		return $html;
	}

	/**
	 *
	 * Method to check, if current asset id exists in asset table
	 * @param integer          $assetId
	 *
	 * @return integer|null
	 *
	 * @since 2.4.0
	 */
	private function checkAssetId($assetId)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($assetId));

		try
		{
			$db->setQuery($query);

			$res = $db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (is_array($res))
		{
			return (int)$res['id'];
		}

		return null;
	}
}
