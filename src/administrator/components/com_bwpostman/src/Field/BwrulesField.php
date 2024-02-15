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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

defined('JPATH_PLATFORM') or die;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\RulesField;
use Joomla\CMS\HTML\HTMLHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwAccess;
use Joomla\Database\DatabaseInterface;
use RuntimeException;

/**
 * Form Field class for the Joomla Platform.
 * Field for assigning permissions to groups for a given asset
 *
 * @see    JAccess
 * @since  2.0.0
 */
class BwrulesField extends RulesField
{
	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $layout = 'form.field.rules';

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the value.
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
	 * @throws Exception
	 *
	 * @todo    :   Add access check.
	 * @since   11.1
	 */
	protected function getInput(): string
	{
		HtmlHelper::_('bootstrap.tooltip');

		// Initialise some field attributes.
		$section    = $this->section;
		$assetField = $this->assetField;
		$component  = empty($this->component) ? 'root.1' : $this->component;

		// Current view is global config?
		$isGlobalConfig = $component === 'root.1';

		// Get the actions for the asset.
		$actions = BwAccess::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml',
			"/access/section[@name='" . $section . "']/");

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

		$this->actions = $actions;

		// Get the asset id.
		// Note that for global configuration, com_config injects asset_id = 1 into the form.
		$assetId       = $this->form->getValue($assetField);

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

			$db    = Factory::getContainer()->get(DatabaseInterface::class);
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
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'BwRulesField BE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}
		}

		// If not in global config we need the parent_id asset to calculate permissions.
		if (!$isGlobalConfig)
		{
			// In this case we need to get the section rules too.
			$db = Factory::getContainer()->get(DatabaseInterface::class);

			$query = $db->getQuery(true)
				->select($db->quoteName('parent_id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('id') . ' = ' . $assetId);

			try
			{
				$db->setQuery($query);
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'BwRulesField BE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}
		}

		// Full width format.

		// Get the rules for this asset (recursive only for section).
		$this->assetRules = BwAccess::getAssetRules($assetId, true, false);

		// Get the available user groups.
		$this->groups = $this->getUserGroups();

		// Trim the trailing line in the layout file
		return trim($this->getRenderer($this->layout)->render($this->getLayoutData()));
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	protected function getLayoutData(): array
	{
		$newItem = true;

		$data = parent::getLayoutData();

		$id = $this->form->getValue('id');

		if ($id > 0)
		{
			$newItem = false;
		}

		$extraData = array(
			'groups'         => $this->groups,
			'section'        => $this->section,
			'actions'        => $this->actions,
			'assetId'        => $this->assetId,
			'newItem'        => $newItem,
			'assetRules'     => $this->assetRules,
			'isGlobalConfig' => $this->isGlobalConfig,
			'parentAssetId'  => $this->parentAssetId,
			'component'      => $this->component,
		);

		return array_merge($data, $extraData);
	}

	/**
	 *
	 * Method to check, if current asset id exists in asset table
	 *
	 * @param integer|null $assetId
	 *
	 * @return integer|null
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	private function checkAssetId(int $assetId = null): ?int
	{
		$res   = null;
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($assetId));

		try
		{
			$db->setQuery($query);

			$res = $db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'BwRulesField BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if (is_array($res))
		{
			return (int)$res['id'];
		}

		return null;
	}
}
