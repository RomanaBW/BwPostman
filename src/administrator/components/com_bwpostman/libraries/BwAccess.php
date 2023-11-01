<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman access helper class, based on JAccess.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Libraries;

defined('JPATH_PLATFORM') or die;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Table\Asset;
use Joomla\CMS\Factory;
use Joomla\CMS\Profiler\Profiler;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Log\LogEntry;
use RuntimeException;
use SimpleXMLElement;

//require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

/**
 * Class that handles all access authorisation routines.
 *
 * @since  11.1
 */
class BwAccess
{
	/**
	 * Array of view levels
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $viewLevels = array();

	/**
	 * Array of rules for the asset
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $assetRules = array();

	/**
	 * Array of identities for asset rules
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $assetRulesIdentities = array();

	/**
	 * Array of the permission parent ID mappings
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $assetPermissionsParentIdMapping = array();

	/**
	 * Array of asset types that have been preloaded
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $preloadedAssetTypes = array();

	/**
	 * Array of loaded user identities
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $identities = array();

	/**
	 * Array of user groups.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $userGroups = array();

	/**
	 * Array of user group paths.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $userGroupPaths = array();

	/**
	 * Array of cached groups by user.
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $groupsByUser = array();

	/**
	 * Array of preloaded asset names and ids (key is the asset id).
	 *
	 * @var    array
	 * @since  3.7.0
	 */
	protected static $preloadedAssets = array();

	/**
	 * The root asset id.
	 *
	 * @var    integer
	 * @since  3.7.0
	 */
	protected static $rootAssetId = null;

	/**
	 * The root asset id.
	 *
	 * @var    integer
	 * @since  3.7.0
	 */
	protected static $actionRule = array();

	/**
	 * Method for clearing static caches.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public static function clearStatics()
	{
		self::$viewLevels                      = array();
		self::$assetRules                      = array();
		self::$assetRulesIdentities            = array();
		self::$assetPermissionsParentIdMapping = array();
		self::$preloadedAssetTypes             = array();
		self::$identities                      = array();
		self::$userGroups                      = array();
		self::$userGroupPaths                  = array();
		self::$groupsByUser                    = array();
		self::$preloadedAssets                 = array();
		self::$rootAssetId                     = null;
	}

	/**
	 * Method to check if a user is authorised to perform an action, optionally on an asset.
	 *
	 * @param integer        $userId   Id of the user for which to check authorisation.
	 * @param string         $action   The name of the action to authorise.
	 * @param integer|string $assetKey The asset key (asset id or asset name). null fallback to root asset.
	 * @param boolean        $preload  Indicates whether preloading should be used.
	 *
	 * @return  boolean|null  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	public static function check(int $userId, string $action, $assetKey = null, bool $preload = false): ?bool
	{
		// Sanitise inputs.
		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));

		if (!isset(self::$identities[$userId]))
		{
			// Get all groups against the user is mapped. Other than Joomla I only need the direct groups.
			// @ToDo: Do I really need only direct groups? I also need inherited permissions! What I don't need: Parents up to Manager or root
			self::$identities[$userId] = self::getGroupsByUser($userId, false);
		}

		$identities	= self::$identities[$userId];

		if (self::isRoot($identities))
		{
			return true;
		}

		// Get the JRules object and set data
		$rules	= self::getAssetRules($assetKey, false, true, $preload);

		if (!isset($rules->getData()[$action]))
		{
			return null;
		}

		self::$actionRule	= $rules->getData()[$action]->getData();

		foreach ($identities as $identity)
		{
			if (isset(self::$actionRule[$identity]) && self::$actionRule[$identity])
			{
				return true;
			}
		}

		/*
		 * Workaround:
		 * If identity for this action is not known, inherit it from the parents and set the value here.
		 * This is due to the limit of 5120 characters of the table field for the rules. If I would set all rights
		 * explicitly, that would need about 6000 Characters.
		 */
		self::inheritRules($userId);

		// Check for permission
		return self::ruleAllow($identities);
	}

	/**
	 * @param string $assetName
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected static function getSectionAsset(string $assetName): ?string
	{
		$sectionRules = null;

		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		$query->select($db->quoteName('rules'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . ' = ' . $db->quote($assetName));

		try
		{
			$db->setQuery($query);

			$sectionRules = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $sectionRules;
	}

	/**
	 * @param array $identities
	 *
	 * @return boolean
	 *
	 * @since 2.0.0
	 */
	protected static function isRoot(array $identities): bool
	{
		$isRoot = array_keys($identities, 8);

		if (count($isRoot))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param integer $userId
	 *
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected static function inheritRules(int $userId)
	{
		$parentIdentities = self::getParentIdentities(self::$identities[$userId]);

		foreach (self::$identities[$userId] as $identity)
		{
			// Check if the identity is unknown.
			if (!isset(self::$actionRule[$identity]))
			{
				// Loop over parent identities, backwards up to root, if needed
				for ($i = 0; $i < count($parentIdentities[$identity]); $i++)
				{
					$parentId = $parentIdentities[$identity][$i]['parent_id'];
					// Check if the parent identity is known
					if (isset(self::$actionRule[$parentId]))
					{
						// if so, set rule to inherited value…
						self::$actionRule[$identity] = (boolean) self::$actionRule[$parentId];

						// …and break loop
						$i = count($parentIdentities[$identity]);
					}
				}
			}
		}
	}

	/**
	 * Get the parent identities for an identity.
	 *
	 * The identity is an integer where +ve represents a user group,
	 * and -ve represents a user.
	 *
	 * @param array|integer $identities An integer representing the identity, or an array of identities
	 *
	 * @return  array   parent identities, oldest last
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	public static function getParentIdentities($identities): array
	{
		$parentIdentities = array();

		foreach ($identities as $identity)
		{
			$db    = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true);

			$query->select('p.parent_id');
			$query->from($db->quoteName('#__usergroups') . ' AS n, ' . $db->quoteName('#__usergroups') . ' AS p');
			$query->where('n.lft BETWEEN p.lft AND p.rgt');
			$query->where('n.id = ' . (int) $identity);
			$query->order('p.lft DESC');

			try
			{
				$db->setQuery($query);

				$parentIdentities[$identity] = $db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $parentIdentities;
	}

	/**
	 * Checks that this action can be performed by an identity.
	 *
	 * The identity is an integer where +ve represents a user group,
	 * and -ve represents a user.
	 *
	 * @param   array|integer  $identities  An integer or array of integers representing the identities to check.
	 *
	 * @return  bool|null  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   11.1
	 */
	public static function ruleAllow($identities): ?bool
	{
		// Implicit null by default.
		$result = null;

		// Check that the inputs are valid.
		if (!empty($identities))
		{
			if (!is_array($identities))
			{
				$identities = array($identities);
			}

			foreach ($identities as $identity)
			{
				// Technically the identity just needs to be unique.
				$identity = (int) $identity;

				// Check if the identity is known.
				if (isset(self::$actionRule[$identity]))
				{
					$result = (boolean) self::$actionRule[$identity];

					// An explicit allow wins.
					if ($result === true)
					{
						break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Method to preload the Rules object for the given asset type.
	 *
	 * @param integer|string|array $assetTypes     The type or name of the asset (e.g. 'com_content.article',
	 *                                             'com_menus.menu.2'). Also accepts the asset id. An array of asset
	 *                                             type or a special
	 *                                             'components' string to load all component assets.
	 * @param boolean              $reload         Set to true to reload from database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   1.6
	 *
	 * @note    This method will return void in 4.0.
	 */
	public static function preload($assetTypes = 'components', bool $reload = false): bool
	{
		// If sent an asset id, we first get the asset type for that asset id.
		if (is_numeric($assetTypes))
		{
			$assetTypes = self::getAssetType($assetTypes);
		}

		// Check for default case:
		$isDefault = is_string($assetTypes) && in_array($assetTypes, array('components', 'component'));

		// Preload the rules for all of the components.
		if ($isDefault)
		{
			self::preloadComponents();

			return true;
		}

		// If we get to this point, this is a regular asset type and we'll proceed with the preloading process.
		if (!is_array($assetTypes))
		{
			$assetTypes = (array) $assetTypes;
		}

		foreach ($assetTypes as $assetType)
		{
			self::preloadPermissions($assetType, $reload);
		}

		return true;
	}

	/**
	 * Method to recursively retrieve the list of parent Asset IDs
	 * for a particular Asset.
	 *
	 * @param string  $assetType     The asset type, or the asset name, or the extension of the asset
	 *                               (e.g. 'com_content.article', 'com_menus.menu.2', 'com_contact').
	 * @param integer $assetId       The numeric asset id.
	 *
	 * @return  array  List of ancestor ids (includes original $assetId).
	 *
	 * @since   1.6
	 */
	protected static function getAssetAncestors(string $assetType, int $assetId): array
	{
		// Get the extension name from the $assetType provided
		$extensionName = self::getExtensionNameFromAsset($assetType);

		// Holds the list of ancestors for the Asset ID:
		$ancestors = array();

		// Add in our starting Asset ID:
		$ancestors[] = $assetId;

		// Initialize the variable we'll use in the loop:
		$id = $assetId;

		while ($id !== 0)
		{
			if (isset(self::$assetPermissionsParentIdMapping[$extensionName][$id]))
			{
				$id = (int) self::$assetPermissionsParentIdMapping[$extensionName][$id]->parent_id;

				if ($id !== 0)
				{
					$ancestors[] = $id;
				}
			}
			else
			{
				// Add additional case to break out of the while loop automatically in
				// the case that the ID is non-existent in our mapping variable above.
				break;
			}
		}

		return $ancestors;
	}

	/**
	 * Method to retrieve the list of Asset IDs and their Parent Asset IDs
	 * and store them for later usage in getAssetRules().
	 *
	 * @param   string  $assetType  The asset type, or the asset name, or the extension of the asset
	 *                              (e.g. 'com_content.article', 'com_menus.menu.2', 'com_contact').
	 *
	 * @return  array  List of asset ids (includes parent asset id information).
	 *
	 * @since   1.6
	 * @deprecated  3.7.0  No replacement. Will be removed in 4.0.
	 */
//	protected static function &preloadPermissionsParentIdMapping($assetType)
//	{
//		// Get the extension name from the $assetType provided
//		$extensionName = self::getExtensionNameFromAsset($assetType);
//
//		if (!isset(self::$assetPermissionsParentIdMapping[$extensionName]))
//		{
//			// Get the database connection object.
//			$db = Factory::getContainer()->get(DatabaseInterface::class);
//
//			// Get a fresh query object:
//			$query    = $db->getQuery(true);
//
//			// Build the database query:
//			$query->select('a.id, a.parent_id');
//			$query->from('#__assets AS a');
//			$query->where('(a.name LIKE ' . $db->quote($extensionName . '.%') . ' OR a.name = ' . $db->quote($extensionName) . ' OR a.id = 1)');
//
//			// Get the Name Permission Map List
//			$db->setQuery($query);
//			$parentIdMapping = $db->loadObjectList('id');
//
//			self::$assetPermissionsParentIdMapping[$extensionName] = &$parentIdMapping;
//		}
//
//		return self::$assetPermissionsParentIdMapping[$extensionName];
//	}

	/**
	 * Method to retrieve the Asset Rule strings for this particular
	 * Asset Type and stores them for later usage in getAssetRules().
	 * Stores 2 arrays: one where the list has the Asset ID as the key
	 * and a second one where the Asset Name is the key.
	 *
	 * @param string  $assetType     The asset type, or the asset name, or the extension of the asset
	 *                               (e.g. 'com_content.article', 'com_menus.menu.2', 'com_contact').
	 * @param boolean $reload        Reload the preloaded assets.
	 *
	 * @return  bool  True
	 *
	 * @throws Exception
	 *
	 * @since   1.6
	 *
	 * @note    This function will return void in 4.0.
	 */
	protected static function preloadPermissions(string $assetType, bool $reload = false): bool
	{
		// Get the extension name from the $assetType provided
		$extensionName = self::getExtensionNameFromAsset($assetType);

		// If asset is a component, make sure that all the component assets are preloaded.
		if ((isset(self::$preloadedAssetTypes[$extensionName]) || isset(self::$preloadedAssetTypes[$assetType])) && !$reload)
		{
			return true;
		}

		!JDEBUG ?: Profiler::getInstance('Application')->mark('Before Access::preloadPermissions (' . $extensionName . ')');

		// Get the database connection object.
		$assets     = null;
		$db         = Factory::getContainer()->get(DatabaseInterface::class);
		$extraQuery = $db->qn('name') . ' = ' . $db->q($extensionName) . ' OR ' . $db->qn('parent_id') . ' = 0';

		// Get a fresh query object.
		$query = $db->getQuery(true)
			->select($db->qn(array('id', 'name', 'rules', 'parent_id')))
			->from($db->qn('#__assets'))
			->where($db->qn('name') . ' LIKE ' . $db->q($extensionName . '.%') . ' OR ' . $extraQuery);

		// Get the permission map for all assets in the asset extension.
		try
		{
			$assets = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		self::$assetPermissionsParentIdMapping[$extensionName] = array();

		foreach ($assets as $asset)
		{
			self::$assetPermissionsParentIdMapping[$extensionName][$asset->id] = $asset;
			self::$preloadedAssets[$asset->id]                                 = $asset->name;
		}

		// Mark asset type and it's extension name as preloaded.
		self::$preloadedAssetTypes[$assetType]     = true;
		self::$preloadedAssetTypes[$extensionName] = true;

		!JDEBUG ?: Profiler::getInstance('Application')->mark('After Access::preloadPermissions (' . $extensionName . ')');

		return true;
	}

	/**
	 * Method to preload the Rules objects for all components.
	 *
	 * Note: This will only get the base permissions for the component.
	 * e.g. it will get 'com_content', but not 'com_content.article.1' or
	 * any more specific asset type rules.
	 *
	 * @return   array  Array of component names that were preloaded.
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected static function preloadComponents(): array
	{
		// If the components already been preloaded do nothing.
		if (isset(self::$preloadedAssetTypes['components']))
		{
			return array();
		}

		!JDEBUG ?: Profiler::getInstance('Application')->mark('Before Access::preloadComponents (all components)');

		// Add root to asset names list.
		$components = array('root.1');

		// Add enabled components to asset names list.
		foreach (ComponentHelper::getComponents() as $component)
		{
			if ($component->enabled)
			{
				$components[] = $component->option;
			}
		}

		// Get the database connection object.
		$assets = null;
		$db     = Factory::getContainer()->get(DatabaseInterface::class);

		// Get the asset info for all assets in asset names list.
		$query = $db->getQuery(true)
			->select($db->qn(array('id', 'name', 'rules', 'parent_id')))
			->from($db->qn('#__assets'))
			->where($db->qn('name') . ' IN (' . implode(',', $db->quote($components)) . ')');

		// Get the Name Permission Map List
		try
		{
			$assets = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$rootAsset = null;

		// First add the root asset and save it to preload memory and mark it as preloaded.
		foreach ($assets as &$asset)
		{
			if ((int) $asset->parent_id === 0)
			{
				$rootAsset                                                       = $asset;
				self::$rootAssetId                                               = $asset->id;
				self::$preloadedAssetTypes[$asset->name]                         = true;
				self::$preloadedAssets[$asset->id]                               = $asset->name;
				self::$assetPermissionsParentIdMapping[$asset->name][$asset->id] = $asset;

				unset($asset);
				break;
			}
		}

		// Now create save the components asset tree to preload memory.
		foreach ($assets as $asset)
		{
			if (!isset(self::$assetPermissionsParentIdMapping[$asset->name]))
			{
				self::$assetPermissionsParentIdMapping[$asset->name] = array($rootAsset->id => $rootAsset, $asset->id => $asset);
				self::$preloadedAssets[$asset->id]                   = $asset->name;
			}
		}

		// Mark all components asset type as preloaded.
		self::$preloadedAssetTypes['components'] = true;

		!JDEBUG ?: Profiler::getInstance('Application')->mark('After Access::preloadComponents (all components)');

		return $components;
	}

	/**
	 * Method to check if a group is authorised to perform an action, optionally on an asset.
	 *
	 * @param integer        $groupId  The path to the group for which to check authorisation.
	 * @param string         $action   The name of the action to authorise.
	 * @param integer|string $assetKey The asset key (asset id or asset name). null fallback to root asset.
	 * @param boolean        $preload  Indicates whether preloading should be used.
	 *
	 * @return  boolean|null  True if authorised, null otherwise
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	public static function checkGroup(int $groupId, string $action, $assetKey = null, bool $preload = true): ?bool
	{
		// Sanitize input.
		$action  = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));

		return self::getAssetRules($assetKey, true, true, $preload)->allow($action, self::getGroupPath($groupId));
	}

	/**
	 * Gets the parent groups that a leaf group belongs to in its branch back to the root of the tree
	 * (including the leaf group id).
	 *
	 * @param   array|integer  $groupId  An integer or array of integers representing the identities to check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   11.1
	 */
	protected static function getGroupPath($groupId)
	{
		// Load all the groups to improve performance on intensive groups checks
		$groups = UserGroupsHelper::getInstance()->getAll();

//		@ToDo: Correct return type?
		if (!isset($groups[$groupId]))
		{
			return array();
		}

		return $groups[$groupId]->path;
	}

	/**
	 * Method to return the Rules object for an asset. The returned object can optionally hold
	 * only the rules explicitly set for the asset or the summation of all inherited rules from
	 * parent assets and explicit rules.
	 *
	 * @param integer|string $assetKey             The asset key (asset id or asset name). null fallback to root asset.
	 * @param boolean        $recursive            True to return the rules object with inherited rules.
	 * @param boolean        $recursiveParentAsset True to calculate the rule also based on inherited
	 *                                             component/extension rules.
	 * @param boolean        $preload              Indicates whether preloading should be used.
	 *
	 * @return  Rules  Rules object for the asset.
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 *
	 * @note    The non preloading code will be removed in 4.0. All asset rules should use asset preloading.
	 */
	public static function getAssetRules($assetKey, bool $recursive = false, bool $recursiveParentAsset = false, bool $preload = true): Rules
	{
		$logOptions = array();
		$logger     = BwLogger::getInstance($logOptions);

		// Auto preload the components assets and root asset (if chosen).
		if ($preload)
		{
			self::preload();
		}

		// When asset key is null fallback to root asset.
		$assetKey = self::cleanAssetKey($assetKey);

		// Auto preload assets for the asset type (if chosen).
		if ($preload)
		{
			self::preload(self::getAssetType($assetKey));
		}

		// Get the asset id and name.
		$assetId = self::getAssetId($assetKey);

		// If asset rules already cached em memory return it (only in full recursive mode).
		if ($recursive && $recursiveParentAsset && $assetId && isset(self::$assetRules[$assetId]))
		{
			return self::$assetRules[$assetId];
		}

		// Get the asset name and the extension name.
		$assetName     = self::getAssetName($assetKey);
		$extensionName = self::getExtensionNameFromAsset($assetName);

		// If asset id does not exist fallback to extension asset, then root asset.
		if (!$assetId)
		{
			if ($extensionName && $assetName !== $extensionName)
			{
				$message = "'No asset found for ' . $assetName . ', falling back to ' . $extensionName";
				$logger->addEntry(new LogEntry($message, BwLogger::BW_WARNING, 'assets'));

				return self::getAssetRules($extensionName, $recursive, $recursiveParentAsset, $preload);
			}

			if (self::$rootAssetId !== null && $assetName !== self::$preloadedAssets[self::$rootAssetId])
			{
				$message = "No asset found for . $assetName . , falling back to" . self::$preloadedAssets[self::$rootAssetId];
				$logger->addEntry(new LogEntry($message, BwLogger::BW_WARNING, 'assets'));

				return self::getAssetRules(self::$preloadedAssets[self::$rootAssetId], $recursive, $recursiveParentAsset, $preload);
			}
		}

		// Almost all calls can take advantage of preloading.
		if ($assetId && isset(self::$preloadedAssets[$assetId]))
		{
			!JDEBUG ?: Profiler::getInstance('Application')->mark('Before BwAccess::getAssetRules (id:' . $assetId . ' name:' . $assetName . ')');

			// Collects permissions for each asset
			$collected = array();

			// If not in any recursive mode. We only want the asset rules of item.
			if (!$recursive && !$recursiveParentAsset)
			{
				$collected = array(self::$assetPermissionsParentIdMapping[$extensionName][$assetId]->rules);
			}
			// If not in parent recursive mode. We want the asset rules of item and section.
			elseif ($recursive && !$recursiveParentAsset)
			{
				$sectionName   = '';

				$assetNameParts = explode('.', $assetName);
				$numberOfDots   = substr_count($assetName, '.');

				$extensionName = $assetNameParts[0];

				if ($numberOfDots === 2 || count($assetNameParts) === 2)
				{
					$sectionName   = $assetNameParts[0] . '.' . $assetNameParts[1];
				}


				$collectedItem = json_decode(self::$assetPermissionsParentIdMapping[$extensionName][$assetId]->rules, true);
				$collectedSection = json_decode(self::getSectionAsset($sectionName), true);
				$collectedMerge = array_merge($collectedSection, $collectedItem);
				$collected = array(json_encode($collectedMerge));
			}
			// If there is parent type of recursive mode.
			else
			{
				$ancestors = array_reverse(self::getAssetAncestors($extensionName, $assetId));

				foreach ($ancestors as $id)
				{
					// If full recursive mode, but not recursive parent mode, do not add the extension asset rules.
					if ($recursive && !$recursiveParentAsset && self::$assetPermissionsParentIdMapping[$extensionName][$id]->name === $extensionName)
					{
						continue;
					}

					// If not full recursive mode, but recursive parent mode, do not add other recursion rules.
					if (!$recursive && $recursiveParentAsset && self::$assetPermissionsParentIdMapping[$extensionName][$id]->name !== $extensionName
						&& self::$assetPermissionsParentIdMapping[$extensionName][$id]->id !== $assetId)
					{
						continue;
					}

					// If empty asset to not add to rules.
					if (self::$assetPermissionsParentIdMapping[$extensionName][$id]->rules === '{}')
					{
						continue;
					}

					$collected[] = self::$assetPermissionsParentIdMapping[$extensionName][$id]->rules;
				}
			}

			/**
			* Hashing the collected rules allows us to store
			* only one instance of the Rules object for
			* Assets that have the same exact permissions...
			* it's a great way to save some memory.
			*/
			$hash = md5(implode(',', $collected));

			if (!isset(self::$assetRulesIdentities[$hash]))
			{
				$rules = new Rules;
				$rules->mergeCollection($collected);

				self::$assetRulesIdentities[$hash] = $rules;
			}

			// Save asset rules to memory cache(only in full recursive mode).
			if ($recursive && $recursiveParentAsset)
			{
				self::$assetRules[$assetId] = self::$assetRulesIdentities[$hash];
			}

			!JDEBUG ?: Profiler::getInstance('Application')->mark('After Access::getAssetRules (id:' . $assetId . ' name:' . $assetName . ')');

			return self::$assetRulesIdentities[$hash];
		}

		// Non preloading code. Use old slower method, slower. Only used in rare cases (if any) or without preloading chosen.
		$message = 'Asset ' . $assetKey . ' permissions fetch without preloading (slower method).';
		$logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'assets'));

		!JDEBUG ?: Profiler::getInstance('Application')->mark('Before Access::getAssetRules (assetKey:' . $assetKey . ')');

		// There's no need to process it with the recursive method for the Root Asset ID.
		if ((int) $assetKey === 1)
		{
			$recursive = false;
		}

		// Get the database connection object.
		$db = Factory::getContainer()->get(DatabaseInterface::class);

		// Build the database query to get the rules for the asset.
		$query = $db->getQuery(true)
			->select($db->qn(($recursive ? 'b.rules' : 'a.rules'), 'rules'))
			->select($db->qn(($recursive ? array('b.id', 'b.name', 'b.parent_id') : array('a.id', 'a.name', 'a.parent_id'))))
			->from($db->qn('#__assets', 'a'));

		// If the asset identifier is numeric assume it is a primary key, else lookup by name.
		$assetString     = is_numeric($assetKey) ? $db->qn('a.id') . ' = ' . $assetKey : $db->qn('a.name') . ' = ' . $db->q($assetKey);
		$extensionString = '';

		if ($recursiveParentAsset && ($extensionName !== $assetKey || is_numeric($assetKey)))
		{
			$extensionString = ' OR ' . $db->qn('a.name') . ' = ' . $db->q($extensionName);
		}

		$recursiveString = $recursive ? ' OR ' . $db->qn('a.parent_id') . ' = 0' : '';

		$query->where('(' . $assetString . $extensionString . $recursiveString . ')');

		// If we want the rules cascading up to the global asset node we need a self-join.
		if ($recursive)
		{
			$query->join('LEFT', $db->qn('#__assets', 'b') . ' ON b.lft <= a.lft AND b.rgt >= a.rgt')
				->order($db->qn('b.lft'));
		}

		// Execute the query and load the rules from the result.
		$result = null;

		try
		{
			$result = $db->setQuery($query)->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get the root even if the asset is not found and in recursive mode
		if (empty($result))
		{
			$assets = new Asset($db);

			$query->clear()
				->select($db->qn(array('id', 'name', 'parent_id', 'rules')))
				->from($db->qn('#__assets'))
				->where($db->qn('id') . ' = ' . $db->q($assets->getRootId()));

			try
			{
				$result = $db->setQuery($query)->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		$collected = array();

		foreach ($result as $asset)
		{
			$collected[] = $asset->rules;
		}

		// Instantiate and return the Rules object for the asset rules.
		$rules = new Rules;
		$rules->mergeCollection($collected);

		!JDEBUG ?: Profiler::getInstance('Application')->mark('Before Access::getAssetRules <strong>Slower</strong> (assetKey:' . $assetKey . ')');

		return $rules;
	}

	/**
	 * Method to clean the asset key to make sure we always have something.
	 *
	 * @param   integer|string  $assetKey  The asset key (asset id or asset name). null fallback to root asset.
	 *
	 * @return  integer|string  Asset id or asset name.
	 *
	 * @since   3.7.0
	 */
	protected static function cleanAssetKey($assetKey = null)
	{
		// If it's a valid asset key, clean it and return it.
		if ($assetKey)
		{
			return strtolower(preg_replace('#[\s\-]+#', '.', trim($assetKey)));
		}

		// Return root asset id if already preloaded.
		if (self::$rootAssetId !== null)
		{
			return self::$rootAssetId;
		}

		// No preload. Return root asset id from Assets.
		$assets = new Asset(Factory::getContainer()->get(DatabaseInterface::class));

		return $assets->getRootId();
	}

	/**
	 * Method to get the asset id from the asset key.
	 *
	 * @param   integer|string  $assetKey  The asset key (asset id or asset name).
	 *
	 * @return  integer  The asset id.
	 *
	 * @since   3.7.0
	 */
	protected static function getAssetId($assetKey): int
	{
		static $loaded = array();

		// If the asset is already an id return it.
		if (is_numeric($assetKey))
		{
			return (int) $assetKey;
		}

		if (!isset($loaded[$assetKey]))
		{
			// It's the root asset.
			if (self::$rootAssetId !== null && $assetKey === self::$preloadedAssets[self::$rootAssetId])
			{
				$loaded[$assetKey] = self::$rootAssetId;
			}
			else
			{
				$preloadedAssetsByName = array_flip(self::$preloadedAssets);

				// If we already have the asset name stored in preloading, example, a component, no need to fetch it from table.
				if (isset($preloadedAssetsByName[$assetKey]))
				{
					$loaded[$assetKey] = $preloadedAssetsByName[$assetKey];
				}
				// Else we have to do an extra db query to fetch it from the table fetch it from table.
				else
				{
					$table = new Asset(Factory::getContainer()->get(DatabaseInterface::class));
					$table->load(array('name' => $assetKey));
					$loaded[$assetKey] = $table->id;
				}
			}
		}

		return (int) $loaded[$assetKey];
	}

	/**
	 * Method to get the asset name from the asset key.
	 *
	 * @param   integer|string  $assetKey  The asset key (asset id or asset name).
	 *
	 * @return  string  The asset name (ex: com_content.article.8).
	 *
	 * @since   3.7.0
	 */
	protected static function getAssetName($assetKey): string
	{
		static $loaded = array();

		// If the asset is already a string return it.
		if (!is_numeric($assetKey))
		{
			return $assetKey;
		}

		if (!isset($loaded[$assetKey]))
		{
			// It's the root asset.
			if (self::$rootAssetId !== null && $assetKey === self::$rootAssetId)
			{
				$loaded[$assetKey] = self::$preloadedAssets[self::$rootAssetId];
			}
			// If we already have the asset name stored in preloading, example, a component, no need to fetch it from table.
			elseif (isset(self::$preloadedAssets[$assetKey]))
			{
				$loaded[$assetKey] = self::$preloadedAssets[$assetKey];
			}
			// Else we have to do an extra db query to fetch it from the table fetch it from table.
			else
			{
				$table = new Asset(Factory::getContainer()->get(DatabaseInterface::class));
				$table->load($assetKey);
				$loaded[$assetKey] = $table->name;
			}
		}

		return $loaded[$assetKey];
	}

	/**
	 * Method to get the extension name from the asset name.
	 *
	 * @param   integer|string  $assetKey  The asset key (asset id or asset name).
	 *
	 * @return  string  The extension name (ex: com_content).
	 *
	 * @since    1.6
	 */
	public static function getExtensionNameFromAsset($assetKey): string
	{
		static $loaded = array();

		if (!isset($loaded[$assetKey]))
		{
			$assetName = self::getAssetName($assetKey);
			$firstDot  = strpos($assetName, '.');

			if ($assetName !== 'root.1' && $firstDot !== false)
			{
				$assetName = substr($assetName, 0, $firstDot);
			}

			$loaded[$assetKey] = $assetName;
		}

		return $loaded[$assetKey];
	}

	/**
	 * Method to get the asset type from the asset name.
	 *
	 * For top level components this returns "components":
	 * 'com_content' returns 'components'
	 *
	 * For other types:
	 * 'com_content.article.1' returns 'com_content.article'
	 * 'com_content.category.1' returns 'com_content.category'
	 *
	 * @param   integer|string  $assetKey  The asset key (asset id or asset name).
	 *
	 * @return  string  The asset type (ex: com_content.article).
	 *
	 * @since    1.6
	 */
	public static function getAssetType($assetKey): string
	{
		// If the asset is already a string return it.
		$assetName = self::getAssetName($assetKey);
		$lastDot   = strrpos($assetName, '.');

		if ($assetName !== 'root.1' && $lastDot !== false)
		{
			return substr($assetName, 0, $lastDot);
		}

		return 'components';
	}

	/**
	 * Method to return the title of a user group
	 *
	 * @param   integer  $groupId  Id of the group for which to get the title of.
	 *
	 * @return  string  The title of the group
	 *
	 * @since   3.5
	 */
//	public static function getGroupTitle($groupId)
//	{
//		// Fetch the group title from the database
//		$db    = Factory::getContainer()->get(DatabaseInterface::class);
//		$query = $db->getQuery(true);
//		$query->select('title')
//			->from('#__usergroups')
//			->where('id = ' . $db->quote($groupId));
//		$db->setQuery($query);
//
//		return $db->loadResult();
//	}

	/**
	 * Method to return a list of user groups mapped to a user. The returned list can optionally hold
	 * only the groups explicitly mapped to the user or all groups both explicitly mapped and inherited
	 * by the user.
	 *
	 * @param integer $userId    Id of the user for which to get the list of groups.
	 * @param boolean $recursive True to include inherited user groups.
	 *
	 * @return  array    List of user group ids to which the user is mapped.
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	public static function getGroupsByUser(int $userId, bool $recursive = true): array
	{
		// Creates a simple unique string for each parameter combination:
		$storeId = $userId . ':' . (int) $recursive;

		if (!isset(self::$groupsByUser[$storeId]))
		{
			// TODO: Uncouple this from ComponentHelper and allow for a configuration setting or value injection.
			if (class_exists('ComponentHelper'))
			{
				$guestUsergroup = ComponentHelper::getParams('com_users')->get('guest_usergroup', 1);
			}
			else
			{
				$guestUsergroup = 1;
			}

			// Guest user (if only the actually assigned group is requested)
			if (empty($userId) && !$recursive)
			{
				$result = array($guestUsergroup);
			}
			// Registered user and guest if all groups are requested
			else
			{
				$db = Factory::getContainer()->get(DatabaseInterface::class);

				// Build the database query to get the rules for the asset.
				$query = $db->getQuery(true)
					->select($recursive ? 'b.id' : 'a.id');

				if (empty($userId))
				{
					$query->from('#__usergroups AS a')
						->where('a.id = ' . (int) $guestUsergroup);
				}
				else
				{
					$query->from('#__user_usergroup_map AS map')
						->where('map.user_id = ' . $userId)
						->join('LEFT', '#__usergroups AS a ON a.id = map.group_id');
				}

				// If we want the rules cascading up to the global asset node we need a self-join.
				if ($recursive)
				{
					$query->join('LEFT', '#__usergroups AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
				}

				// Execute the query and load the rules from the result.
				$result = null;

				try
				{
					$db->setQuery($query);

					$result = $db->loadColumn();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				// Clean up any NULL or duplicate values, just in case
				$result = ArrayHelper::toInteger($result);

				if (empty($result))
				{
					$result = array('1');
				}
				else
				{
					$result = array_unique($result);
				}
			}

			self::$groupsByUser[$storeId] = $result;
		}

		return self::$groupsByUser[$storeId];
	}

	/**
	 * Method to return a list of user Ids contained in a Group
	 *
	 * @param integer $groupId   The group Id
	 * @param boolean $recursive Recursively include all child groups (optional)
	 *
	 * @return  array
	 *
	 * @throws Exception
	 *
	 * @todo    This method should move somewhere else
	 *
	 * @since   11.1
	 */
//	public static function getUsersByGroup(int $groupId, $recursive = false): array
//	{
//		// Get a database object.
//		$db = Factory::getContainer()->get(DatabaseInterface::class);
//
//		$test = $recursive ? '>=' : '=';
//
//		// First find the users contained in the group
//		$query = $db->getQuery(true)
//			->select('DISTINCT(user_id)')
//			->from('#__usergroups as ug1')
//			->join('INNER', '#__usergroups AS ug2 ON ug2.lft' . $test . 'ug1.lft AND ug1.rgt' . $test . 'ug2.rgt')
//			->join('INNER', '#__user_usergroup_map AS m ON ug2.id=m.group_id')
//			->where('ug1.id=' . $db->quote($groupId));
//
//		$result = null;
//
//		try
//		{
//			$db->setQuery($query);
//
//			$result = $db->loadColumn();
//		}
//		catch (RuntimeException $e)
//		{
//			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
//		}
//
//		// Clean up any NULL values, just in case
//		$result = ArrayHelper::toInteger($result);
//
//		return $result;
//	}

	/**
	 * Method to return a list of view levels for which the user is authorised.
	 *
	 * @param integer $userId Id of the user for which to get the list of authorised view levels.
	 *
	 * @return  array    List of view levels for which the user is authorised.
	 *
	 * @throws Exception
	 *
	 * @since   11.1
	 */
	public static function getAuthorisedViewLevels(int $userId): array
	{
		// Only load the view levels once.
		if (empty(self::$viewLevels))
		{
			// Get a database object.
			$db = Factory::getContainer()->get(DatabaseInterface::class);

			// Build the base query.
			$query = $db->getQuery(true)
				->select('id, rules')
				->from($db->quoteName('#__viewlevels'));

			// Set the query for execution.
			try
			{
				$db->setQuery($query);
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// Build the view levels array.
			foreach ($db->loadAssocList() as $level)
			{
				self::$viewLevels[$level['id']] = (array) json_decode($level['rules']);
			}
		}

		// Initialise the authorised array.
		$authorised = array(1);

		// Check for the recovery mode setting and return early.
		$user      = Factory::getUser($userId);
		$root_user = Factory::getApplication()->getConfig()->get('root_user');

		if (($user->username && $user->username == $root_user) || (is_numeric($root_user) && $user->id > 0 && $user->id == $root_user))
		{
			// Find the super user levels.
			foreach (self::$viewLevels as $level => $rule)
			{
				foreach ($rule as $id)
				{
					if ($id > 0 && self::checkGroup($id, 'core.admin'))
					{
						$authorised[] = $level;
						break;
					}
				}
			}

			return $authorised;
		}

		// Get all groups that the user is mapped to recursively.
		$groups = self::getGroupsByUser($userId);

		// Find the authorised levels.
		foreach (self::$viewLevels as $level => $rule)
		{
			foreach ($rule as $id)
			{
				if (($id < 0) && (($id * -1) == $userId))
				{
					$authorised[] = $level;
					break;
				}
				// Check to see if the group is mapped to the level.
				elseif (($id >= 0) && in_array($id, $groups))
				{
					$authorised[] = $level;
					break;
				}
			}
		}

		return $authorised;
	}

	/**
	 * Method to return a list of actions from a file for which permissions can be set.
	 *
	 * @param string $file  The path to the XML file.
	 * @param string $xpath An optional xpath to search for the fields.
	 *
	 * @return  boolean|array   False if case of error or the list of actions available.
	 *
	 * @since   12.1
	 */
	public static function getActionsFromFile(string $file, string $xpath = "/access/section[@name='component']/")
	{
//		JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml', "/access/section[@name='" . $section . "']/"
		if (!is_file($file) || !is_readable($file))
		{
			// If unable to find the file return false.
			return false;
		}
		else
		{
			// Else return the actions from the xml.
			$xml = simplexml_load_file($file);

			return self::getActionsFromData($xml, $xpath);
		}
	}

	/**
	 * Method to return a list of actions from a string or from an xml for which permissions can be set.
	 *
	 * @param   string|SimpleXMLElement $data  The XML string or an XML element.
	 * @param string                    $xpath An optional xpath to search for the fields.
	 *
	 * @return  boolean|array   False if case of error or the list of actions available.
	 *
	 * @since   12.1
	 */
	public static function getActionsFromData($data, string $xpath = "/access/section[@name='component']/")
	{
		// If the data to load isn't already an XML element or string return false.
		if ((!($data instanceof SimpleXMLElement)) && (!is_string($data)))
		{
			return false;
		}

		// Attempt to load the XML if a string.
		if (is_string($data))
		{
			try
			{
				$data = new SimpleXMLElement($data);
			}
			catch (Exception $e)
			{
				return false;
			}

			// Make sure the XML loaded correctly.
			if (!$data)
			{
				return false;
			}
		}

		// Initialise the actions array
		$actions = array();

		// Get the elements from the xpath
		$elements = $data->xpath($xpath . 'action[@name][@title][@description]');

		// If there some elements, analyse them
		if (!empty($elements))
		{
			foreach ($elements as $action)
			{
				// Add the action to the actions array
				$actions[] = (object) array(
					'name' => (string) $action['name'],
					'title' => (string) $action['title'],
					'description' => (string) $action['description'],
				);
			}
		}

		// Finally return the actions array
		return $actions;
	}
}
