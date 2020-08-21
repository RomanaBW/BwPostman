<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman helper class for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Access;
use JHtmlSidebar;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwAccess;

//require_once JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/access/BwAccess.php';


/**
 * Class BwPostmanHelper
 *
 * @since
 */
class BwPostmanHelper
{
	/**
	 * property to hold permissions array
	 *
	 * @var array
	 *
	 * @since 2.0.0
	 */
	private static $permissions = null;

	/**
	 * Configure the Link bar.
	 *
	 * @param    string $vName The name of the task view.
	 *
	 * @return    void
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function addSubmenu($vName)
	{
		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		JHtmlSidebar::addEntry(
			Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY'),
			'index.php?option=com_bwpostman',
			$vName == 'bwpostman'
		);

		if (self::$permissions['view']['newsletter'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_NLS'),
				'index.php?option=com_bwpostman&view=newsletters',
				$vName == 'newsletters'
			);
		}

		if (self::$permissions['view']['subscriber'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_SUBS'),
				'index.php?option=com_bwpostman&view=subscribers',
				$vName == 'subscribers'
			);
		}

		if (self::$permissions['view']['campaign'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_CAMS'),
				'index.php?option=com_bwpostman&view=campaigns',
				$vName == 'campaigns'
			);
		}

		if (self::$permissions['view']['mailinglist'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_MLS'),
				'index.php?option=com_bwpostman&view=mailinglists',
				$vName == 'mailinglists'
			);
		}

		if (self::$permissions['view']['template'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_TPLS'),
				'index.php?option=com_bwpostman&view=templates',
				$vName == 'templates'
			);
		}

		if (self::$permissions['view']['archive'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_ARC'),
				'index.php?option=com_bwpostman&view=archive&layout=newsletters',
				$vName == 'archive'
			);
		}

		if (self::$permissions['view']['maintenance'])
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_MAINTENANCE'),
				'index.php?option=com_bwpostman&view=maintenance',
				$vName == 'maintenance'
			);
		}
	}

	/**
	 * Check if BwPostman is safe to be used.
	 *
	 * If installer is running, it's unsafe to use our framework. Files may be currently replaced with
	 * new ones and the database structure might be inconsistent. Using forum during installation will
	 * likely cause fatal errors and data corruption if you attempt to update objects in the database.
	 *
	 * Always detect BwPostman in your code before you start using the framework:
	 *
	 * <code>
	 *    // Check if BwPostman has been installed and compatible with your code
	 *    if (class_exists('BwPostmanAdmin') && BwPostmanHelper::installed() && BwPostmanHelper::isCompatible('2.0.0-BETA2')) {
	 *        // Initialize the framework (new in 2.0.0-BETA2)
	 *        BwPostmanForum::setup();
	 *        // Start using the framework
	 *    }
	 * </code>
	 *
	 * @see BwPostmanHelper::enabled()
	 * @see BwPostmanHelper::isCompatible()
	 * @see BwPostmanHelper::setup()
	 *
	 * @return boolean True.
	 *
	 * @since
	 */
	public static function installed()
	{
		return true;
	}

	/**
	 * Method to replace the links in a newsletter to provide the correct preview
	 *
	 * @access    public
	 *
	 * @param    string $text HTML-/Text-version
	 *
	 * @return    boolean
	 *
	 * @since
	 */
	public static function replaceLinks(&$text)
	{
		$search_str = '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto|#)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text       = preg_replace($search_str, ' ${1}="' . Uri::root() . '${2}"', $text);

		return true;
	}

	/**
	 * Method to get the version of BwPostman installed
	 *
	 * @return string
	 *
	 * @since   0.9.1
	 *
	 * @throws \Exception
	 */
	static public function getInstalledBwPostmanVersion()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->setQuery($query);

		try
		{
			$manifest = json_decode($db->loadResult(), true);
		}
		catch (\RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return $manifest['version'];
	}

	/**
	 * Method to check, if a given action to a given item is allowed
	 * Breaks and returns false, if the item to check has explicit no permission
	 * Also returns false, if no permission is found
	 *
	 * @param    string     $view       The view to test.
	 * @param    string     $action     The action to check
	 * @param    int        $recordId   The record(s) to test.
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	private static function checkActionPermission($view, $action, $recordId = 0)
	{
		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		// Do not forbid for admin
		if (isset(self::$permissions['com']['admin']) && self::$permissions['com']['admin'])
		{
			return true;
		}

		// Cast recordId
		$recordId = (int) $recordId;

		/*
		 * Real permission checks
		 *
		 * To enable item based deny to someone, who normally has the permission to edit (or vice versa), first check on item level.
		 * If there is found an entry on item level, this one has priority!
		 * If no entry on item level is found, we have check further, until we find an entry.
		 * If we find no entry on all levels, we deny.
		 *
		 * To reach that, we also need a return value of null at authorize method. So we can't use $user->authorize
		 */

		// First: Check for item specific permissions
		if ($recordId)
		{
			// Return result, if we have the permission explicitly named, else go downwards
			$authAction	= 'bwpm.' . $view . '.' . $action;
			$assetName	= 'com_bwpostman.' . $view . '.' . $recordId;

			$actionAllowed = self::authorise($authAction, $assetName, $recordId);

			if ($actionAllowed !== null)
			{
				return $actionAllowed;
			}
		}

		// Second: Check for view specific permissions
		// Return result, if one of the groups is named, else go downwards
		if (isset(self::$permissions[$view][$action]) && (self::$permissions[$view][$action] !== null))
		{
			return self::$permissions[$view][$action];
		}

		// Third: Check for component permissions
		if (isset(self::$permissions['com'][$action]) && self::$permissions['com'][$action])
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get admin permissions for all sections
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	protected static function getAdminPermissionsForAllSections()
	{
		$permissions = array();

		$sections = array(
			'newsletter',
			'subscriber',
			'campaign',
			'mailinglist',
			'template',
		);

		foreach ($sections as $section)
		{
			$permissions[$section] = self::canAdmin($section);
		}

		return $permissions;
	}

	/**
	 * Method to get view permissions for all views
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 *
	 * @since 2.0.0
	 */
	protected static function getViewPermissionsForAllViews()
	{
		$permissions = array();

		$views = array(
			'newsletter',
			'subscriber',
			'campaign',
			'mailinglist',
			'template',
			'archive',
			'manage',
			'maintenance',
		);

		foreach ($views as $view)
		{
			$permissions[$view] = self::canView($view);
		}

		return $permissions;
	}

	/**
	 * Method to get permissions for all views
	 *
	 * @param string $view		the section to get the rights for
	 *
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	protected static function getPermissionsForSingleViews($view)
	{
		$permissions	= array();

		if ($view !== 'archive' && $view !== 'maintenance')
		{
			$permissions['create']     = self::authorise('bwpm.' . $view . '.create', 'com_bwpostman.' . $view, 0);
			$permissions['edit']       = self::authorise('bwpm.' . $view . '.edit', 'com_bwpostman.' . $view, 0);
			$permissions['edit.own']   = self::authorise('bwpm.' . $view . '.edit.own', 'com_bwpostman.' . $view, 0);
			$permissions['edit.state'] = self::authorise('bwpm.' . $view . '.edit.state', 'com_bwpostman.' . $view, 0);
			$permissions['archive']    = self::authorise('bwpm.' . $view . '.archive', 'com_bwpostman.' . $view, 0);
		}

		if ($view === 'newsletter')
		{
			$permissions['send']  = self::authorise('bwpm.' . $view . '.send', 'com_bwpostman.' . $view, 0);
		}

		$permissions['restore']   = self::authorise('bwpm.' . $view . '.restore', 'com_bwpostman.' . $view, 0);

		if ($view !== 'maintenance')
		{
			$permissions['delete'] = self::authorise('bwpm.' . $view . '.delete', 'com_bwpostman.' . $view, 0);
		}

		if ($view === 'maintenance')
		{
			$permissions['check'] = self::authorise('bwpm.' . $view . '.check', 'com_bwpostman.' . $view, 0);
			$permissions['save']  = self::authorise('bwpm.' . $view . '.save', 'com_bwpostman.' . $view, 0);
		}

		return $permissions;
	}

	/**
	 * @param   string  $view
	 * @param   string  $action            may be edit/edit.own, archive, restore or delete
	 * @param   integer $itemsFromArchive  Do we want items from archive?
	 *
	 * @return bool
	 *
	 * @since version
	 *
	 * @throws \Exception
	 */
	private static function displayButton($view, $action, $itemsFromArchive)
	{
		if (isset(self::$permissions['com'][$action]) && self::$permissions['com'][$action])
		{
			return true;
		}

		if (isset(self::$permissions[$view][$action]) && self::$permissions[$view][$action])
		{
			return true;
		}

		// Enhancement for item specific rights
		$allowedItems = self::getAllowedRecords($view, $action,  $itemsFromArchive);

		foreach ($allowedItems as $allowedItem)
		{
			$editItem = self::checkActionPermission($view, $action, $allowedItem);

			if ($editItem === true)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Method to get selectlist for dates
	 *
	 * @access    public
	 *
	 * @param    string $date      sort of date --> day, hour, minute
	 * @param    int    $length    length of list array
	 * @param    array  $selectval selected values
	 *
	 * @return    array                selectlist
	 *
	 * @since
	 */
	public function getDateList($selectval, $date = 'minute', $length = 10)
	{
		$options    = array();
		$selectlist = array();
		$intval     = 1;
		if ($date == 'minute')
		{
			$intval = ComponentHelper::getParams('Com_bwpostman')->get('autocam_minute_intval');
		}

		switch ($date)
		{
			case 'day':
				for ($i = 0; $i <= 31; $i++)
				{
					$options[] = $i;
				}
				break;

			case 'hour':
				for ($i = 0; $i < 24; $i++)
				{
					$options[] = $i;
				}
				break;

			case 'minute':
				for ($i = 0; $i < 60; $i += $intval)
				{
					$options[] = $i;
				}
				break;
		}

		foreach ($selectval->$date as $key => $value)
		{
			$opt = "automailing_values[" . $date . "][" . $key . "]";
			if ($value != '0')
			{
				$selected = $value;
			}
			else
			{
				$selected = 0;
			}

			$select_html = '<select id="' . $opt . '" name="automailing_values[' . $date . '][]" >';
			foreach ($options as $key2 => $value2)
			{
				$select_html .= '<option value="' . $key2 * $intval . '"';
				if ($selected == $key2 * $intval)
				{
					$select_html .= ' selected="selected"';
				}

				$select_html .= '>' . $value2 . '</option>';
			}

			$select_html .= '</select>';
			$selectlist[] = $select_html;
		}

		return $selectlist;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param    integer $id      The item ID.
	 * @param    string  $section The access section name.
	 *
	 * @return    \JObject
	 *
	 * @since
	 */

	public static function getActions($id = 0, $section = '')
	{
		$path   = JPATH_ADMINISTRATOR . '/components/com_bwpostman/access.xml';
		$result = new \JObject;

		if (($section != '') && $id)
		{
			$assetName = 'com_bwpostman.' . $section . '.' . (int) $id;
		}
		elseif ($section != '')
		{
			$assetName = 'com_bwpostman.' . $section;
		}
		else
		{
			$assetName = 'com_bwpostman';
		}

		$com_actions = Access::getActionsFromFile($path, "/access/section[@name='component']/");

		if ($section != '')
		{
			$sec_actions = Access::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");
			$actions     = array_merge($com_actions, $sec_actions);
		}
		else
		{
			$actions = $com_actions;
		}

		foreach ($actions as $action)
		{
			$result->set($action->name, self::authorise($action->name, $assetName, $id));
		}

		return $result;
	}

	/**
	 * Method to cache permissions to BwPostman in user state
	 *
	 * @return    void
	 *
	 * @throws \Exception
	 *
	 * @since    2.0.0
	 */
	public static function setPermissionsState()
	{
		$app = Factory::getApplication();

		// Debugging variable, normally set to false
		$reload = true;

		if (is_array(self::$permissions) && !$reload)
		{
			return;
		}

		if (!is_null($app->getUserState('com_bwpm.permissions', null)) && !$reload)
		{
			self::$permissions = $app->getUserState('com_bwpm.permissions', null);

			return;
		}

		$user        = Factory::getUser();
		$permissions = array();

		// Set permissions for component
		$permissions['com']['admin']      = $user->authorise('core.admin', 'com_bwpostman');
		$permissions['com']['manage']     = $user->authorise('core.manage', 'com_bwpostman');
		$permissions['com']['create']     = $user->authorise('bwpm.create', 'com_bwpostman');
		$permissions['com']['edit']       = $user->authorise('bwpm.edit', 'com_bwpostman');
		$permissions['com']['edit.own']   = $user->authorise('bwpm.edit.own', 'com_bwpostman');
		$permissions['com']['edit.state'] = $user->authorise('bwpm.edit.state', 'com_bwpostman');
		$permissions['com']['archive']    = $user->authorise('bwpm.archive', 'com_bwpostman');
		$permissions['com']['restore']    = $user->authorise('bwpm.restore', 'com_bwpostman');
		$permissions['com']['delete']     = $user->authorise('bwpm.delete', 'com_bwpostman');
		$permissions['com']['send']       = $user->authorise('bwpm.send', 'com_bwpostman');

		self::$permissions = $permissions;

		$permissions['admin'] = self::getAdminPermissionsForAllSections();
		$permissions['view'] = self::getViewPermissionsForAllViews();

		self::$permissions = $permissions;

		// Set permissions for views
		$views = array(
			'newsletter',
			'subscriber',
			'campaign',
			'mailinglist',
			'template',
			'archive',
			'maintenance',
		);

		foreach ($views as $view)
		{
			$permissions[$view] = self::getPermissionsForSingleViews($view);
		}

		self::$permissions = $permissions;

		$app->setUserState('com_bwpm.permissions', $permissions);
	}

	/**
	 * Method to check if you can administer BwPostman
	 *
	 * @param 	string	$section
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canAdmin($section)
	{
		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		if (self::$permissions['com']['admin'])
		{
			return true;
		}

		// Next check section permission.
		$authAction = 'bwpm.admin.' . $section;
		$assetName  = 'com_bwpostman.' . $section;

		if ($section != 'archive' && $section != 'manage' & $section != 'maintenance')
		{
			if (self::authorise($authAction, $assetName, 0))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Method to check if you can manage BwPostman
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canManage()
	{
		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		return self::$permissions['com']['manage'];
	}

	/**
	 * Method to check if you can view a specific view.
	 *
	 * @param    string     $view       The view to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canView($view = '')
	{
		// Check general component permission first.
		if (self::canAdmin($view))
		{
			return true;
		}

		// Next check view permission.
		$authAction = 'bwpm.view.' . $view;
		$assetName  = 'com_bwpostman.' . $view;

		if ($view == 'archive')
		{
			$assetName = 'com_bwpostman';
		}

		if (self::authorise($authAction, $assetName, 0))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if you can add a record.
	 *
	 * @param    string $view The view to test. Has to be the list mode name.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canAdd($view = '')
	{
		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		$allowed = self::$permissions['com']['create'] || self::$permissions[$view]['create'];

		return $allowed;
	}

	/**
	 * Method to check if you can check in an item
	 *
	 * @param    string	   $section
	 * @param    int       $checkedOut      user id, who checked out this item
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canCheckin($section, $checkedOut = 0)
	{
		// If nothing is checked out, there is nothing to test
		if ($checkedOut == 0)
		{
			return true;
		}

		$user   = Factory::getUser();
		$userId = (int)$user->get('id');

		// If current user checked out, he may check in.
		if ((int)$checkedOut === $userId)
		{
			return true;
		}

		if (!is_array(self::$permissions))
		{
			self::setPermissionsState();
		}

		// If current user can admin or can manage, he may check in.
		if (self::$permissions['com']['admin'] || self::$permissions['com']['manage'])
		{
			return true;
		}

		// If current user can admin this section, he may check in.
		if (self::$permissions['admin'][$section])
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param    string         $view       The view to test. Has to be the single mode name.
	 * @param    array|object   $data       An array of input data.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canEdit($view = '', $data = array())
	{
		/*
		 * To enable item based deny to someone, who normally has the permission to edit (or vice versa), first check on item level.
		 * If there is found an entry on item level, this one has priority!
		 * If no entry on item level is found, we have check further, until we find an entry.
		 * If we find no entry on all levels, we deny.
		 *
		 * To reach that, we also need a return value of null at authorize method. So we can't use $user->authorize
		 */

		// Initialise variables.
		$user      = Factory::getUser();
		$userId    = (int)$user->get('id');
		$recordId  = 0;
		$createdBy = 0;
		$action    = 'edit';

		// Extract needed data
		if (is_object($data))
		{
			$data = ArrayHelper::fromObject($data);
		}

		if (is_array($data))
		{
			if (key_exists('id', $data))
			{
				$recordId = (int) $data['id'];
			}

			if (key_exists('create', $data) || key_exists('created_by', $data))
			{
				$createdBy = (int) $data['created_by'];
			}

			if (key_exists('registered_by', $data))
			{
				$createdBy = (int) $data['registered_by'];
			}
		}

		$itemsFromArchive = 0;
		// This part is needed for displaying the button
		if (!$recordId)
		{
			$display = self::displayButton($view, $action, $itemsFromArchive);

			if ($display)
			{
				return true;
			}

			$display = self::displayButton($view, 'edit.own', $itemsFromArchive);

			return $display;
		}

		// Now lets check for a specific record

		// First check for item specific edit.own permission
		$editOwnItem = self::checkActionPermission($view, 'edit.own', $recordId);

		if ($editOwnItem !== false)
		{
			if ($editOwnItem)
			{
				$ownerId = self::getCreatorId($view, $recordId, $createdBy);

				// Now test the owner is the user. If the owner matches 'me' then allow access.
				if ($ownerId === $userId)
				{
					return true;
				}
			}
		}

		// Second check for item specific edit permission
		$editItem = self::checkActionPermission($view, $action, $recordId);

		if ($editItem !== false)
		{
			return $editItem;
		}

		// Third check for general or view edit.own permission
		$editOwn = self::$permissions['com']['edit.own'] || self::$permissions[$view]['edit.own'];
		if ($editOwn !== null)
		{
			if ($editOwn)
			{
				$ownerId = self::getCreatorId($view, $recordId, $createdBy);

				// Now test the owner is the user. If the owner matches 'me' then allow access.
				if ($ownerId === $userId)
				{
					return true;
				}
			}

			return false;
		}

		// Fourth Check for view edit permission
		if (self::$permissions[$view][$action])
		{
			return true;
		}

		// Last check for general edit permission
		if (self::$permissions['com'][$action])
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if you can edit the state of a record or a set of records.
	 *
	 * @param    string $view      The view to test.
	 * @param    int    $recordId  The record ids to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canEditState($view = '', $recordId = 0)
	{
		$action = 'edit.state';
		$itemsFromArchive = 0;

		// This part is needed for displaying the button
		if ($recordId === 0)
		{
			$allowed = self::displayButton($view, $action, $itemsFromArchive);

			return $allowed;
		}

		// Check permission for submitted record
		$allowed = self::checkActionPermission($view, $action, $recordId);

		if ($allowed !== false)
		{
			return $allowed;
		}

		// Check section permission edit state
		if ($view !== '' && self::$permissions[$view][$action])
		{
			return true;
		}

		// Check section permission edit
		if ($view !== '' && self::$permissions[$view]['edit'])
		{
			return true;
		}

		// Check component permission edit state
		if (self::$permissions['com'][$action])
		{
			return true;
		}

		// Check component permission edit
		if (self::$permissions['com']['edit'])
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if you can send a newsletter.
	 *
	 * @param    int     $recordId   The record to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canSend($recordId = 0)
	{
		$action = 'send';

		if (is_array($recordId))
		{
			$id       = $recordId[0];
			$recordId = $id;
		}

		// Check permission
		$res = self::checkActionPermission('newsletter', $action, $recordId);

		return $res;
	}

	/**
	 * Method to check if you can clear the queue.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    2.0.0
	 */
	public static function canClearQueue()
	{
		$action = 'send';

		// Check permission
		$res = self::checkActionPermission('newsletter', $action);

		return $res;
	}

	/**
	 * Method to check if you can reset the queue.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    2.0.0
	 */
	public static function canResetQueue()
	{
		$action = 'send';

		// Check permission
		$res = self::checkActionPermission('newsletter', $action);

		return $res;
	}

	/**
	 * Method to check if you can retry to send the queue.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    2.0.0
	 */
	public static function canContinueQueue()
	{
		$action = 'send';

		// Check permission
		$res = self::checkActionPermission('newsletter', $action);

		return $res;
	}

	/**
	 * Method to check if you can archive an existing record.
	 *
	 * @param    string  $view             The name of the context.
	 * @param    integer $itemsFromArchive Do we want items from archive?
	 * @param    int     $recordId         The records to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canArchive($view = '', $itemsFromArchive = 0, $recordId = 0)
	{
		// Initialise variables.
		$action = 'archive';

		// This part is needed for displaying the button
		if ($recordId === 0)
		{
			$allowed = self::displayButton($view, $action, $itemsFromArchive);

			return $allowed;
		}

		// Check permission for submitted record
		$allowed = self::checkActionPermission($view, $action, $recordId);

		if ($allowed !== false)
		{
			return $allowed;
		}

		// Check section permission
		if ($view !== '' && self::$permissions[$view][$action])
		{
			return true;
		}

		// Check component permission
		if (self::$permissions['com'][$action])
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to check if you can delete an archived record.
	 *
	 * @param    string $view     The name of the context.
	 * @param    int    $recordId The record to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canDelete($view = '', $recordId = 0)
	{
		// Initialise variables.
		$action           = 'delete';
		$itemsFromArchive = 1;

		// This part is needed for displaying the button
		if ($recordId === 0)
		{
			$allowed = self::displayButton($view, $action, $itemsFromArchive);

			return $allowed;
		}

		// Check permission for submitted record
		$allowed = self::checkActionPermission($view, $action, $recordId);

		if ($allowed !== false)
		{
			return $allowed;
		}

		// Check section permission
		if ($view !== '' && self::$permissions[$view][$action])
		{
			return true;
		}

		// Check component permission
		if (self::$permissions['com'][$action])
		{
			return true;
		}

		Factory::getApplication()->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_DELETE_RIGHTS_MISSING', $view), 'error');

		return false;
	}

	/**
	 * Method to check if you can restore an archived record.
	 *
	 * @param    string    $view     The name of the context.
	 * @param    int       $recordId The record to test.
	 *
	 * @return    boolean
	 *
	 * @throws \Exception
	 *
	 * @since    1.2.0
	 */
	public static function canRestore($view = '', $recordId = 0)
	{
		// Initialise variables.
		$action           = 'restore';
		$itemsFromArchive = 1;

		// This part is needed for displaying the button
		if ($recordId === 0)
		{
			$allowed = self::displayButton($view, $action, $itemsFromArchive);

			return $allowed;
		}

		// Check permission for submitted record
		$allowed = self::checkActionPermission($view, $action, $recordId);

		if ($allowed !== false)
		{
			return $allowed;
		}

		// Check section permission
		if ($view !== '' && self::$permissions[$view][$action])
		{
			return true;
		}

		// Check component permission
		if (self::$permissions['com'][$action])
		{
			return true;
		}

		Factory::getApplication()->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_RESTORE_RIGHTS_MISSING', $view), 'error');

		return false;
	}

	/**
	 * Method to check if there are published mailinglists, If not, display warning message
	 *
	 * @return    bool  true if warning should be displayed
	 *
	 * @throws \Exception
	 *
	 * @since    0.9.8
	 */
	public static function getMailinglistsWarning()
	{
		$_db          = Factory::getDbo();
		$query        = $_db->getQuery(true);
		$ml_published = '';

		// Get # of all published mailinglists
		$query->select('COUNT(*)');
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('published') . ' = ' . 1);
		$query->where($_db->quoteName('archive_flag') . ' = ' . 0);

		$_db->setQuery($query);

		try
		{
			$ml_published = $_db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($ml_published < 1)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_WARNING_NO_PUBLISHED_MAILINGLIST'), 'warning');
			return true;
		}

		unset($ml_published);
		return false;
	}

	/**
	 * Check number of queue entries, which could not be sent
	 *
	 * @return    bool    true if there are unable to send entries in the queue, otherwise false
	 *
	 * @since    1.0.3
	 */
	public static function checkQueueEntries()
	{
		$db   = Factory::getDbo();
		$query = $db->getQuery(true);

		// Get queue entries, which cannot be sent because sending trials have reached limit
		$query->select('DISTINCT ' . $db->quoteName('content_id'));
		$query->from($db->quoteName('#__bwpostman_sendmailqueue'));
		$query->where($db->quoteName('trial') . ' >= 2');
// @ToDo: Hier ist zu überlegen, ob man die Meldung nicht generell anzeigt sobald noch Mails im Queue sind.
// @ToDo: Ist mir erst aufgefallen, als ich den Versand manuell unterbrochen habe, dass nichts angezeigt wird.

		$db->setQuery($query);

		$queueEntriesAtLimit = $db->loadColumn();

		// entries at limit, queue and problem
		if (count($queueEntriesAtLimit))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get a captcha string
	 *
	 * @param int $mode
	 *
	 * @return    string
	 *
	 * @since    0.9.8
	 */
	public static function getCaptcha($mode = 1)
	{
		$zahl    = 1960;
		$no_spam = '';

		if ($mode == 1)
		{
			$no_spam = (date("dmy", time())) * $zahl;
		}

		if ($mode == 2)
		{
			if (date('H', time()) == '00')
			{
				$no_spam = (date("dmy", time() - 86400)) * $zahl;
			}
		}

		return $no_spam;
	}

	/**
	 *    Captcha Bild
	 *
	 *    Systemvoraussetzung:
	 *    Linux, Windows
	 *    PHP 4 >= 4.0.0-RC2 , PHP 5
	 *    GD-Bibliothek ( > gd-1.6 )
	 *    FreeType-Bibliothek
	 *
	 *
	 *    LICENSE: GNU General Public License (GPL)
	 *    This program is free software; you can redistribute it and/or modify
	 *    it under the terms of the GNU General Public License version 2,
	 *    as published by the Free Software Foundation.
	 *
	 * @category      Captcha
	 * @author        Damir Enseleit <info@selfphp.de>
	 * @copyright     2001-2006 SELFPHP
	 * @version       $Id: captcha.php,v 0.10 2006/04/07 13:15:30 des1 Exp $
	 * @link          http://www.selfphp.de
	 */

	/**
	 * Erzeugt die Rechenaufgabe
	 *
	 * @return    void
	 *
	 * @since
	 */

	public static function showCaptcha()
	{
		/**
		 * Method to generate captcha
		 *
		 * @param $im
		 * @param $size
		 * @param $fileTTF
		 *
		 * @return string    $fileName    Gibt die Rechenaufgabe als String für den Dateinamen wieder
		 *
		 * @since
		 */
		function mathCaptcha($im, $size, $fileTTF)
		{
			$math = range(0, 9);
			shuffle($math);

			$mix = range(0, 120);
			shuffle($mix);

			$color = imagecolorallocate($im, $mix[0], $mix[1], $mix[2]);

			$text     = "$math[0] + $math[1]";
			$fileName = $math[0] + $math[1];

			imagettftext($im, $size, 0, 5, 25, $color, $fileTTF, $text);

			return $fileName;
		}

		// TTF-Schrift
		// Sie sollten hier unbedingt den absoluten Pfad angeben, da ansonsten
		// eventuell die TTF-Datei nicht eingebunden werden kann!
		$fileTTF = JPATH_COMPONENT_SITE . '/assets/ttf/style.ttf';

		// Verzeichnis für die Captcha-Bilder (muss Schreibrechte besitzen!)
		// Ausserdem sollten in diesem Ordner nur die Bilder gespeichert werden
		// da das Programm in regelmaessigen Abstaenden dieses leert!
		// Kein abschliessenden Slash benutzen!
		$captchaDir = JPATH_COMPONENT_SITE . '/assets/capimgdir';

		// Schriftgröße Rechenaufgabe
		$sizeMath = 20;

		//Bildgroesse
		$imgWidth  = 80;//200
		$imgHeight = 30;//80

		header("Content-type: image/png");
		$im = @imagecreate($imgWidth, $imgHeight)
		or die("GD! Initialisierung fehlgeschlagen");
		$color = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, $imgWidth, $color);
		$fileName = mathCaptcha($im, $sizeMath, $fileTTF, $imgHeight);

		$codeCaptcha = Factory::getApplication()->input->get('codeCaptcha');

		// Uebermittelten Hash-Wert ueberpruefen
		if (!preg_match('/^[a-f0-9]{32}$/', $codeCaptcha))
		{
			$codeCaptcha = md5(microtime());
		}

		// Image speichern
		imagepng($im, $captchaDir . '/' . $codeCaptcha . '_' . $fileName . '.png');
		imagedestroy($im);
		// Bild ausgeben
//		readfile(Uri::base() . 'components/com_bwpostman/assets/capimgdir/' .$codeCaptcha . '_' . $fileName . '.png');
		readfile($captchaDir . '/' . $codeCaptcha . '_' . $fileName . '.png');
	}

	/**
	 *    Captcha Bild Überprüfung
	 *
	 *    Systemvoraussetzung:
	 *    Linux, Windows
	 *    PHP 4 >= 4.0.0-RC2 , PHP 5
	 *    GD-Bibliothek (> gd-1.6)
	 *    FreeType-Bibliothek
	 *
	 *    Prüft ein Captcha-Bild
	 *
	 *    LICENSE: GNU General Public License (GPL)
	 *    This program is free software; you can redistribute it and/or modify
	 *    it under the terms of the GNU General Public License version 2,
	 *    as published by the Free Software Foundation.
	 *
	 * @category      Captcha
	 * @author        Damir Enseleit <info@selfphp.de>
	 * @copyright     2001-2006 SELFPHP
	 * @version       $Id: captcha_check.php,v 0.10 2006/04/07 13:15:30 des1 Exp $
	 * @link          http://www.selfphp.de
	 *
	 * @param        string  $codeCaptcha   Hash-Wert
	 * @param        string  $stringCaptcha Eingabe durch den User
	 * @param        string  $dir           Das Verzeichnis mit den Captcha-Bilder
	 * @param        integer $delFile       Die Zeit in Minuten, nachdem ein Captcha-Bild gelöscht wird
	 *
	 * @return        bool        TRUE/FALSE
	 *
	 * @since
	 */
	public static function CheckCaptcha($codeCaptcha, $stringCaptcha, $dir, $delFile = 5)
	{
		// Setzt den Check erst einmal auf FALSE
		$captchaTrue = false;

		// Übergebene Hash-Variable überprüfen
		if (!preg_match('/^[a-f0-9]{32}$/', $codeCaptcha))
		{
			return false;
		}

		// Übergebene Captcha-Variable überprüfen
		if (!preg_match('/^[a-zA-Z0-9]{1,6}$/', $stringCaptcha))
		{
			return false;
		}

		$handle = @opendir($dir);

		while (false !== ($file = readdir($handle)))
		{
			if (preg_match("=^\.{1,2}$=", $file))
			{
				continue;
			}

			if (is_dir($dir . $file))
			{
				continue;
			}
			else
			{
				$lastTime = ceil((time() - filemtime($dir . $file)) / 60);

				if ($lastTime > $delFile)
				{
					if ($file != 'index.html')
					{
						unlink($dir . $file);
					}
				}
				else
				{
					if (strtolower($file) == strtolower($codeCaptcha . '_' . $stringCaptcha . '.png'))
					{
						$captchaTrue = true;
					}

					if (preg_match("=^$codeCaptcha=i", $file))
					{
						if ($file != 'index.html')
						{
							unlink($dir . $file);
						}
					}
				}
			}
		}

		@closedir($handle);

		if ($captchaTrue)
		{
			return true;
		}

		return false;
	}

	/**
	 * Load BwPostman language file
	 *
	 * Helper function for external modules and plugins to load the main BwPostman language file(s)
	 *
	 * @param   string $file
	 * @param   string $client
	 *
	 * @return  array
	 *
	 * @since
	 */
//	public static function loadLanguage($file = 'com_bwpostman', $client = 'site')
//	{
//		static $loaded = array();
//
//		if ($client == 'site')
//		{
//			$lookup1 = JPATH_SITE;
//			$lookup2 = JPATH_SITE . 'component/com_bwpostman';
//		}
//		else
//		{
//			$client  = 'admin';
//			$lookup1 = JPATH_ADMINISTRATOR;
//			$lookup2 = JPATH_ADMINISTRATOR . 'component/com_bwpostman';
//		}
//
//		if (empty($loaded["{$client}/{$file}"]))
//		{
//			$lang    = Factory::getLanguage();
//			$english = false;
//			if ($lang->getTag() != 'en-GB' && !JDEBUG && !$lang->getDebug())
//			{
//				$lang->load($file, $lookup2, 'en-GB', true, false);
//				$english = true;
//			}
//
//			$loaded[$file] = $lang->load($file, $lookup1, null, $english, false)
//				|| $lang->load($file, $lookup2, null, $english, false)
//				|| $lang->load($file, $lookup1, $lang->getDefault(), $english, false)
//				|| $lang->load($file, $lookup2, $lang->getDefault(), $english, false);
//		}
//
//		return $loaded[$file];
//	}

	/**
	 * Method to parse language file
	 *
	 * @param $lang
	 * @param $filename
	 *
	 * @return bool
	 *
	 * @since
	 */
//	protected static function parseLanguage($lang, $filename)
//	{
//		if (!file_exists($filename))
//		{
//			return false;
//		}
//
//		$version = phpversion();
//
//		// Capture hidden PHP errors from the parsing.
//		$php_errormsg = null;
//		$track_errors = ini_get('track_errors');
//		ini_set('track_errors', true);
//
//		if ($version >= '5.3.1')
//		{
//			$contents = file_get_contents($filename);
//			$contents = str_replace('_QQ_', '"\""', $contents);
//			$strings  = @parse_ini_string($contents);
//		}
//		else
//		{
//			$strings = @parse_ini_file($filename);
//
//			if ($version == '5.3.0' && is_array($strings))
//			{
//				foreach ($strings as $key => $string)
//				{
//					$strings[$key] = str_replace('_QQ_', '"', $string);
//				}
//			}
//		}
//
//		// Restore error tracking to what it was before.
//		ini_set('track_errors', $track_errors);
//
//		if (!is_array($strings))
//		{
//			$strings = array();
//		}
//
//		$lang->_strings = array_merge($lang->_strings, $strings);
//
//		return !empty($strings);
//	}
//
	/**
	 * Method to get creator id
	 *
	 * @param    string $view       The name of the context.
	 * @param    int    $recordId   The record to test.
	 * @param    int    $createdBy  The user to test against.
	 *
	 * @return  int     $ownerId
	 *
	 * @throws \Exception
	 *
	 * @since
	 */
	private static function getCreatorId($view, $recordId, $createdBy)
	{
		$creatorId = (int)$createdBy;

		$createdPropertyName = 'created_by';

		if ($view == 'subscriber')
		{
			$createdPropertyName = 'registered_by';
		}

		if (!$creatorId)
		{
			$db	= Factory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName($createdPropertyName));
			$query->from($db->quoteName('#__bwpostman_' . $db->escape($view) . 's'));
			$query->where($db->quoteName('id') . ' = ' . (int)$recordId);

			$db->setQuery($query);

			$creatorId = $db->loadResult();
		}

		return (int)$creatorId;
	}

	/**
	 * Method to get the allowed records
	 *
	 * @param   string    $view          The name of the context.
	 * @param   string    $action        The action to check
	 * @param   integer   $fromArchive   Do we want items from archive?
	 *
	 * @return  array|string  $allowed_items or string 'all'
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function getAllowedRecords($view, $action, $fromArchive = 0)
	{
		// check for general permissions
		if (self::canAdmin($view))
		{
			return 'all';
		}

		$asset_records = self::getSectionAssetNames($view);
		$item_records  = self::extractIdFromAssetName($asset_records);

		// Differentiate between archive view and section view
		$reducedItems = self::getItemsSeparatedByArchive($view, $fromArchive, $item_records);

		$allowed_items = array();
		foreach ($reducedItems as $reducedItem)
		{
			$testItem = self::checkActionPermission($view, $action, $reducedItem['id']);

			if ($testItem)
			{
				$allowed_items[] = $reducedItem['id'];
			}
		}

		// check for mailinglist specific permissions
		if ($view != 'mailinglist')
		{
			$mailinglist_items = self::getMailinglistSpecificRecords($view);

			// merge values
			// @ToDo: Is merge correct? Or do I have to intersect?
			if (count($mailinglist_items))
			{
				$allowed_items = array_merge(array_values($allowed_items), array_values($mailinglist_items));
			}
		}

		$allowed_items  = array_unique($allowed_items);

		return $allowed_items;
	}

	/**
	 * Method to get only archived or only not archived records
	 *
	 * @param   string  $view           The name of the context.
	 * @param   integer $fromArchive    Do we come from archive?
	 * @param   array   $itemRecords    items to check for
	 *
	 * @return  array|string  $allowed_items or string 'all'
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	public static function getItemsSeparatedByArchive($view, $fromArchive, $itemRecords)
	{
		$itemsToCheck = array();

		foreach ($itemRecords as $itemRecord)
		{
			$itemsToCheck[] = $itemRecord['id'];
		}

		$db	= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_' . $view . 's'));
		$query->where($db->quoteName('archive_flag') . ' = ' . $db->Quote($fromArchive));
		if (is_array($itemsToCheck) && !empty($itemsToCheck))
		{
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $itemsToCheck) . ')');
		}

		$db->setQuery($query);

		$reducedItems = $db->loadAssocList();

		return $reducedItems;
	}

	/**
	 * Method to get an array of strings of all asset names of the component section
	 * The array items are of the form 'component.section.id', where the part with id may be empty (section-wide permission)
	 *
	 * @param   string  $view           The name of the context.
	 *
	 * @return  array   $asset_records  section names of assets
	 *
	 * @since   2.0.0
	 */
	private static function getSectionAssetNames($view)
	{
		$asset_records  = array();
		$_db            = Factory::getDbo();

		try
		{
			$asset_query = $_db->getQuery(true);

			$asset_query->select($_db->quoteName('name'));
			$asset_query->from($_db->quoteName('#__assets'));
			$asset_query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote('%com_bwpostman.' . $view . '%'));

			$_db->setQuery($asset_query);

			$asset_records = $_db->loadAssocList();
		}
		catch (\Exception $e)
		{
			$asset_records['name']  = 'com_bwpostman.' . $view;
		}

		// If no record is available, set one with general section name (but should not appear on correct installation).
		if (!count($asset_records))
		{
			$asset_records['name']  = 'com_bwpostman.' . $view;
		}

		return $asset_records;
	}

	/**
	 * Method to extract the ID from asset name and inject it in the array
	 *
	 * @param   array    $asset_records
	 *
	 * @return  array    $items
	 *
	 * @since   2.0.0
	 */
	private static function extractIdFromAssetName($asset_records)
	{
		$items = array();

		foreach ($asset_records as $record)
		{
			$item   = array();
			$name = explode('.', $record['name']);
			if (isset($name[2]))
			{
				$item['id'] = (int) $name[2];
			}
			else
			{
				$item['id'] = 0;
			}

			$items[]    = $item;
		}

		return $items;
	}

	/**
	 * Method to check for item specific permission
	 * items without permission will be removed
	 *
	 * @param   string   $view      The name of the context.
	 * @param   string   $action    The action to check
	 * @param   array    $items     The Items to check for
	 *
	 * @return  array           $allowed_ids
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private static function checkRecordsForPermission($view, $action, $items)
	{
		$allowed_ids = array();

		foreach ($items as $item)
		{
			switch ($action)
			{
				case 'edit':
					$allowed = self::canEdit($view, $item);
					if ($allowed)
					{
						$allowed_ids[] = $item['id'];
					}
					break;
			}
		}

		// If no record is permitted, set one ID with zero. A record from database never has an ID of zero.
		if (!count($allowed_ids))
		{
			$allowed_ids[]  = 0;
		}

		return $allowed_ids;
	}

	/**
	 * Method to check for item campaign specific permissions
	 * items without permission will be removed
	 *
	 * @param   string $view    The name of the context.
	 *
	 * @return  array           $allowed_ids
	 *
	 * @throws \Exception
	 *
	 * @since   2.0.0
	 */
	private static function getMailinglistSpecificRecords($view)
	{
		$allowed_ids    = array();
		$result         = array();

		// Get the mailinglists the user may handle
		$asset_records          = self::getSectionAssetNames('mailinglist');
		$item_records           = self::extractIdFromAssetName($asset_records);
		$allowed_mailinglists   = self::checkRecordsForPermission('mailinglist', '', $item_records);

		$general_permission = array_search(0, $allowed_mailinglists);
		if ($general_permission !== false)
		{
			return $allowed_ids;
		}

		$table  = '';
		$field  = '';
		switch ($view)
		{
			case 'campaigns':
					$table  = '#__bwpostman_campaigns_mailinglists';
					$field  = 'campaign_id';
				break;
			case 'newsletter':
					$table  = '#__bwpostman_newsletters_mailinglists';
					$field  = 'newsletter_id';
				break;
			case 'subscriber':
					$table  = '#__bwpostman_subscribers_mailinglists';
					$field  = 'subscriber_id';
				break;
			case 'template':
				// @ToDo: Remove comments, when this cross table is implemented
				//	$table  = '#__bwpostman_template_mailinglists';
				//	$field  = 'template_id';
				break;
			default:
		}

		if ($table != '' && $field != '')
		{
			try
			{
				$_db	= Factory::getDbo();
				$query	= $_db->getQuery(true);

				$query->select($_db->quoteName($field));
				$query->from($_db->quoteName($table));
				if (is_array($allowed_mailinglists) && !empty($allowed_mailinglists))
				{
					$query->where($_db->quoteName('mailinglist_id') . ' IN (' . implode(',', $allowed_mailinglists) . ')');
				}

				$_db->setQuery($query);

				$result = $_db->loadAssocList();
			}
			catch (\RuntimeException $e)
			{
				return $allowed_ids;
			}
		}

		foreach ($result as $item)
		{
			$allowed_ids[]    = (int) $item[$field];
		}

		return $allowed_ids;
	}

	/**
	 * Method to check User object authorisation against an access control
	 * object and optionally an access extension object
	 *
	 * @param   string  $action     The name of the action to check for permission.
	 * @param   string  $assetName  The name of the asset on which to perform the action.
	 * @param   integer $recordId   The id of the record
	 *
	 * @return  boolean|null  True if permission is set, null otherwise
	 *
	 * @since   11.1
	 */
	public static function authorise($action, $assetName = null, $recordId = 0)
	{
		$userId = Factory::getUser()->id;

		return BwAccess::check($userId, $action, $assetName, false, $recordId);
	}

	/**
	 * Method to get query where part for mailinglists
	 *
	 * @param $mls
	 *
	 * @return string
	 *
	 * @since 2.1.1
	 */
	public static function getWhereMlsClause($mls)
	{
		$whereMlsClause = '';

		if (is_array($mls) && !empty($mls))
		{
			$whereMlsClause .= 'm.mailinglist_id IN (' . implode(',', $mls) . ')';
		}

		return $whereMlsClause;
	}

	/**
	 * Method to get query where part for campaigns
	 *
	 * @param $cams
	 *
	 * @return string
	 *
	 * @since 2.1.1
	 */
	public static function getWhereCamsClause($cams)
	{
		$whereCamsClause = '';

		if (is_array($cams) && !empty($cams))
		{
			$whereCamsClause .= 'a.campaign_id IN (' . implode(',', $cams) . ')';
		}

		return $whereCamsClause;
	}

	/**
	 * Method to get query where part for campaigns and mailinglists
	 *
	 * @param $mls
	 * @param $cams
	 *
	 * @return string
	 *
	 * @since 2.1.1
	 */
	public static function getWhereMlsCamsClause($mls, $cams)
	{
		$whereMlsCamsClause = '';
		$whereMlsClause     = self::getWhereMlsClause($mls);
		$whereCamsClause    = self::getWhereCamsClause($cams);


		if ($whereMlsClause != '')
		{
			$whereMlsCamsClause = $whereMlsClause;
		}

		if ($whereCamsClause != '')
		{
			if ($whereMlsClause != '')
			{
				$whereMlsCamsClause = '(' . $whereMlsCamsClause . ' OR ';
			}

			$whereMlsCamsClause .= $whereCamsClause;

			if ($whereMlsClause != '')
			{
				$whereMlsCamsClause .= ')';
			}
		}

		return $whereMlsCamsClause;
	}
}
