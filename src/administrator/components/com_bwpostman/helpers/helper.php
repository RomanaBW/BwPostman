<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman helper class for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/access/BwAccess.php';

//
// Component development:
//
// Newsletter sending 1=on, 0=off
//if (!defined ('BWPOSTMAN_NL_SENDING')) define ('BWPOSTMAN_NL_SENDING', 1);


// Component name amd database prefix
//if (!defined ('BWPOSTMAN_COMPONENT_NAME')) define ('BWPOSTMAN_COMPONENT_NAME', basename (dirname (__FILE__)));
//if (!defined ('BWPOSTMAN_NAME')) define ('BWPOSTMAN_NAME', substr (BWPOSTMAN_COMPONENT_NAME, 4));

// Component location
//if (!defined ('BWPOSTMAN_COMPONENT_LOCATION')) define ('BWPOSTMAN_COMPONENT_LOCATION', basename (dirname (dirname (__FILE__))));

// Component paths
//if (!defined ('BWPOSTMAN_PATH_COMPONENT_RELATIVE')) define ('BWPOSTMAN_PATH_COMPONENT_RELATIVE', BWPOSTMAN_COMPONENT_LOCATION . '/' . BWPOSTMAN_COMPONENT_NAME);
//if (!defined ('BWPOSTMAN_PATH_SITE')) define ('BWPOSTMAN_PATH_SITE', JPATH_ROOT .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
//if (!defined ('BWPOSTMAN_PATH_ADMIN')) define ('BWPOSTMAN_PATH_ADMIN', JPATH_ADMINISTRATOR .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
//if (!defined ('BWPOSTMAN_PATH_MEDIA')) define ('BWPOSTMAN_PATH_MEDIA', JPATH_ROOT .'/media/' . BWPOSTMAN_NAME);

/**
 * Class BwPostmanHelper
 *
 * @since
 */
abstract class BwPostmanHelper
{
	/**
	 * property to hold session
	 *
	 * @var array
	 *
	 * @since
	 */
	static $session = null;

	/**
	 * Configure the Link bar.
	 *
	 * @param    string $vName The name of the task view.
	 *
	 * @return    void
	 *
	 * @since    1.2.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY'),
			'index.php?option=com_bwpostman',
			$vName == 'bwpostman'
		);

		if (self::canView('newsletter'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_NLS'),
				'index.php?option=com_bwpostman&view=newsletters',
				$vName == 'newsletters'
			);
		}

		if (self::canView('subscriber'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_SUBS'),
				'index.php?option=com_bwpostman&view=subscribers',
				$vName == 'subscribers'
			);
		}

		if (self::canView('campaign'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_CAMS'),
				'index.php?option=com_bwpostman&view=campaigns',
				$vName == 'campaigns'
			);
		}

		if (self::canView('mailinglist'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_MLS'),
				'index.php?option=com_bwpostman&view=mailinglists',
				$vName == 'mailinglists'
			);
		}

		if (self::canView('template'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_TPLS'),
				'index.php?option=com_bwpostman&view=templates',
				$vName == 'templates'
			);
		}

		if (self::canView('archive'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_ARC'),
				'index.php?option=com_bwpostman&view=archive&layout=newsletters',
				$vName == 'archive'
			);
		}

		if (self::canView('maintenance'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_MAINTENANCE'),
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
		$search_str = '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text       = preg_replace($search_str, ' ${1}="' . JUri::root() . '${2}"', $text);

		return true;
	}

	/**
	 * Method to check, if a given action is allowed
	 * Breaks and returns false, if one of the items to check has no permission
	 *
	 * @param    string     $view       The view to test.
	 * @param    string     $action     The action to check
	 * @param    array      $recordIds   The record to test.
	 *
	 * @return bool
	 *
	 * @since version
	 */
	private static function checkActionPermission($view, $action, $recordIds = array())
	{
		$user = JFactory::getUser();
		$res  = false;

		// Check view permission.
		if (!self::canView($view))
		{
			return false;
		}

		// Check general component permission
		if ($user->authorise('bwpm.' . $action, 'com_bwpostman'))
		{
			return true;
		}

		// Check specific view permission
		if ($user->authorise('bwpm.' . $view . '.' . $action, 'com_bwpostman.' . $view))
		{
			return true;
		}

		// Check record specific permission
		foreach ($recordIds as $recordId)
		{
			if ((int) $recordId === 0) // new record
			{
				$res = false;
			}
			elseif ($user->authorise('bwpm.' . $view . '.' . $action, 'com_bwpostman.' . $view . (int) $recordId))
			{
				$res = true;
			}
			else
			{
				$res = false;
				break;
			}
		}

		return $res;
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
			$intval = JComponentHelper::getParams('Com_bwpostman')->get('autocam_minute_intval');
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
	 * @return    JObject
	 *
	 * @since
	 */

	public static function getActions($id = 0, $section = '')
	{
		$user   = JFactory::getUser();
		$path   = JPATH_ADMINISTRATOR . '/components/com_bwpostman/access.xml';
		$result = new JObject;

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

		$com_actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");

		if ($section != '')
		{
			$sec_actions = JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");
			$actions     = array_merge($com_actions, $sec_actions);
		}
		else
		{
			$actions = $com_actions;
		}

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Method to check if you can administer BwPostman
	 *
	 * @return    boolean
	 * @since    1.2.0
	 */
	public static function canAdmin()
	{
		$user = JFactory::getUser();
		$res  = false;

		if ($user->authorise('core.admin', 'com_bwpostman'))
		{
			$res = true;
		}

		return $res;
	}

	/**
	 * Method to check if you can manage BwPostman
	 *
	 * @return    boolean
	 * @since    1.2.0
	 */
	public static function canManage()
	{
		$user = JFactory::getUser();
		$res  = false;

		if ($user->authorise('core.admin', 'com_bwpostman') || $user->authorise('core.manage', 'com_bwpostman'))
		{
			$res = true;
		}

		return $res;
	}

	/**
	 * Method to check if you can check in an item
	 *
	 * @param    string    $view            The view to test.
	 * @param    int       $recordId        The record to test.
	 * @param    int       $checkedOut      user id, who checked out this item
	 *
	 * @return    boolean
	 * @since    1.2.0
	 */
	public static function canCheckin($view, $recordId, $checkedOut = 0)
	{
		$user    = JFactory::getUser();
		$userId  = $user->get('id');
		$allowed = false;

		if (self::canManage() || $checkedOut == $userId || $checkedOut == 0)
		{
			$allowed = true;
		}

		if (!$allowed)
		{
			$allowed = self::canEditState($view, $recordId);
		}

		if (!$allowed)
		{
			$allowed = self::canEdit($view, array($recordId));
		}

		return $allowed;
	}

	/**
	 * Method to check if you can view a specific view.
	 *
	 * @param    string $view The view to test.
	 *
	 * @return    boolean
	 * @since    1.2.0
	 */
	public static function canView($view = '')
	{
		$user = JFactory::getUser();
		$res  = false;

		// Check general component permission first.
		if (self::canAdmin())
		{
			$res = true;
		}

		// Next check view permission.
		if (self::authorise($user->id, 'bwpm.view.' . $view, 'com_bwpostman.' . $view))
		{
			$res = true;
		}

		return $res;
	}

	/**
	 * Method to check if you can add a record.
	 *
	 * @param    string $view The view to test. Has to be the list mode name.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canAdd($view = '')
	{
		$action = 'create';

		// Check permission
		$res      = self::checkActionPermission($view, $action, array(0));

		return $res;
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param    string         $view       The view to test. Has to be the single mode name.
	 * @param    array|object   $data       An array of input data.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canEdit($view = '', $data = array())
	{
		// Initialise variables.
		$user       = JFactory::getUser();
		$userId     = $user->get('id');
		$action     = 'edit';
		$recordId   = 0;
		$createdBy  = 0;

		if (is_object($data))
		{
			if (property_exists($data, 'id'))
			{
				$recordId = (int) $data->id;
			}

			if (property_exists($data, 'created_by'))
			{
				$createdBy = (int) $data->created_by;
			}

			if (property_exists($data, 'registered_by'))
			{
				$createdBy = (int) $data->registered_by;
			}
		}
		elseif (is_array($data))
		{
			if (key_exists('id', $data))
			{
				$recordId = (int) $data['id'];
			}

			if (key_exists('create', $data))
			{
				$createdBy = (int) $data['created_by'];
			}

			if (key_exists('registered_by', $data))
			{
				$createdBy = (int) $data['registered_by'];
			}
		}

		// Check permission
		$res      = self::checkActionPermission($view, $action, array($recordId));
		// Fallback on edit own.
		if (!$res)
		{
			// Then test if the permission is available.
			if ($user->authorise('bwpm.edit.own', 'com_bwpostman')
				|| $user->authorise('bwpm.' . $view . '.edit.own', 'com_bwpostman.' . $view)
				|| $user->authorise('bwpm.' . $view . '.edit.own', 'com_bwpostman.' . $view . '.' . $recordId)
			)
			{
				// Check for general 'edit own' permission, used for displaying button
				if (!$recordId)
				{
					$res = true;
				}
				else
				{
					$ownerId = self::getOwnerId($view, $recordId, $createdBy);
					// Now test the owner is the user. If the owner matches 'me' then allow access.
					if ($ownerId == $userId)
					{
						$res = true;
					}
				}
			}
		}

		return $res;
	}

	/**
	 * Method to check if you can edit the state of a record.
	 *
	 * @param    string     $view       The view to test.
	 * @param    int        $recordId   The record to test.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canEditState($view = '', $recordId = 0)
	{
		$action = 'edit.state';

		// Check permission
		$res      = self::checkActionPermission($view, $action, array($recordId));

		return $res;
	}

	/**
	 * Method to check if you can send a newsletter.
	 *
	 * @param    int     $recordId   The record to test.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canSend($recordId = 0)
	{
		$action = 'send';

		// Check permission
		$res      = self::checkActionPermission('newsletter', $action, array($recordId));

		return $res;
	}

	/**
	 * Method to check if you can clear the queue.
	 *
	 * @return    boolean
	 *
	 * @since    2.0.0
	 */
	public static function canClearQueue()
	{
		$action = 'send';

		// Check permission
		$res      = self::checkActionPermission('newsletter', $action, array());

		return $res;
	}

	/**
	 * Method to check if you can reset the queue.
	 *
	 * @return    boolean
	 *
	 * @since    2.0.0
	 */
	public static function canResetQueue()
	{
		$action = 'send';

		// Check permission
		$res      = self::checkActionPermission('newsletter', $action, array());

		return $res;
	}

	/**
	 * Method to check if you can retry to send the queue.
	 *
	 * @return    boolean
	 *
	 * @since    2.0.0
	 */
	public static function canContinueQueue()
	{
		$action = 'send';

		// Check permission
		$res      = self::checkActionPermission('newsletter', $action, array());

		return $res;
	}

	/**
	 * Method to check if you can archive an existing record.
	 *
	 * @param    string     $view       The name of the context.
	 * @param    array      $recordIds  The record to test.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canArchive($view = '', $recordIds = array())
	{
		// Initialise variables.
		$action   = 'archive';

		// Check permission
		$res      = self::checkActionPermission($view, $action, $recordIds);

		return $res;
	}

	/**
	 * Method to check if you can delete an archived record.
	 *
	 * @param    string $view       The name of the context.
	 * @param    array  $recordIds   The record to test.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canDelete($view = '', $recordIds = array())
	{
		// Initialise variables.
		$action   = 'delete';

		// Check permission
		$res      = self::checkActionPermission($view, $action, $recordIds);

		return $res;
	}

	/**
	 * Method to check if you can restore an archived record.
	 *
	 * @param    string     $view       The name of the context.
	 * @param    array      $recordIds   The record to test.
	 *
	 * @return    boolean
	 *
	 * @since    1.2.0
	 */
	public static function canRestore($view = '', $recordIds = array())
	{
		// Initialise variables.
		$action   = 'restore';

		// Check permission
		$res      = self::checkActionPermission($view, $action, $recordIds);

		return $res;
	}

	/**
	 * Method to check if there are published mailinglists, If not, display warning message
	 *
	 * @return    bool  true if warning should be displayed
	 *
	 * @throws Exception
	 *
	 * @since    0.9.8
	 */
	public static function getMailinglistsWarning()
	{
		$_db          = JFactory::getDbo();
		$query        = $_db->getQuery(true);
		$ml_published = '';

		// Get # of all published mailinglists
		$query->select('COUNT(*)');
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		try
		{
			$ml_published = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($ml_published < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_WARNING_NO_PUBLISHED_MAILINGLIST'), 'warning');
			return true;
		}

		unset($ml_published);
		return false;
	}

	/**
	 * Check number of queue entries
	 *
	 * @return    bool    true if there are entries in the queue, otherwise false
	 *
	 * @since    1.0.3
	 */
	public static function checkQueueEntries()
	{
		$_db   = JFactory::getDbo();
		$query = $_db->getQuery(true);

		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_sendmailqueue'));

		$_db->setQuery($query);

		if ($_db->loadResult() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
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
	 * @return    string    $fileName    Gibt die Rechenaufgabe als String für den Dateinamen wieder
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

		// Uebermittelter Hash-Wert ueberpruefen
		if (!preg_match('/^[a-f0-9]{32}$/', $_GET['codeCaptcha']))
		{
			$_GET['codeCaptcha'] = md5(microtime());
		}

		// Image speichern
		imagepng($im, $captchaDir . '/' . $_GET['codeCaptcha'] . '_' . $fileName . '.png');
		imagedestroy($im);
		// Bild ausgeben
		readfile(JUri::base() . 'components/com_bwpostman/assets/capimgdir/' . $_GET['codeCaptcha'] . '_' . $fileName . '.png');
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
		else
		{
			return false;
		}
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
	public static function loadLanguage($file = 'com_bwpostman', $client = 'site')
	{
		static $loaded = array();

		if ($client == 'site')
		{
			$lookup1 = JPATH_SITE;
			$lookup2 = BWPOSTMAN_PATH_SITE;
		}
		else
		{
			$client  = 'admin';
			$lookup1 = JPATH_ADMINISTRATOR;
			$lookup2 = BWPOSTMAN_PATH_ADMIN;
		}

		if (empty($loaded["{$client}/{$file}"]))
		{
			$lang    = JFactory::getLanguage();
			$english = false;
			if ($lang->getTag() != 'en-GB' && !JDEBUG && !$lang->getDebug())
			{
				$lang->load($file, $lookup2, 'en-GB', true, false);
				$english = true;
			}

			$loaded[$file] = $lang->load($file, $lookup1, null, $english, false)
				|| $lang->load($file, $lookup2, null, $english, false)
				|| $lang->load($file, $lookup1, $lang->getDefault(), $english, false)
				|| $lang->load($file, $lookup2, $lang->getDefault(), $english, false);
		}

		return $loaded[$file];
	}

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
	protected static function parseLanguage($lang, $filename)
	{
		if (!file_exists($filename))
		{
			return false;
		}

		$version = phpversion();

		// Capture hidden PHP errors from the parsing.
		$php_errormsg = null;
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);

		if ($version >= '5.3.1')
		{
			$contents = file_get_contents($filename);
			$contents = str_replace('_QQ_', '"\""', $contents);
			$strings  = @parse_ini_string($contents);
		}
		else
		{
			$strings = @parse_ini_file($filename);

			if ($version == '5.3.0' && is_array($strings))
			{
				foreach ($strings as $key => $string)
				{
					$strings[$key] = str_replace('_QQ_', '"', $string);
				}
			}
		}

		// Restore error tracking to what it was before.
		ini_set('track_errors', $track_errors);

		if (!is_array($strings))
		{
			$strings = array();
		}

		$lang->_strings = array_merge($lang->_strings, $strings);

		return !empty($strings);
	}

	/**
	 * Method to parse language file
	 *
	 * @param    string $view       The name of the context.
	 * @param    int    $recordId   The record to test.
	 * @param    int    $createdBy  The user to test against.
	 *
	 * @return  int     $ownerId
	 *
	 * @since
	 */
	private static function getOwnerId($view, $recordId, $createdBy)
	{
		$ownerId = $createdBy;

		$createdPropertyName    = 'created_by';

		if ($view == 'subscriber')
		{
			$createdPropertyName    = 'registered_by';
		}

		if (!$ownerId)
		{
			// Need to do a lookup from the model.
			// get the model for user groups
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models');
			$model  = JModelLegacy::getInstance(ucfirst($view), 'BwPostmanModel');
			$record = $model->getItem($recordId);

			if (!is_object($record) || !property_exists($record, $createdPropertyName))
			{
				return false;
			}

			$ownerId = $record->{$createdPropertyName};
		}

		return $ownerId;
	}

	/**
	 * Method to get the allowed records as comma separated list
	 *
	 * @param   string  $view       The name of the context.
	 *
	 * @return  string  $allowed_ids
	 *
	 * @since   2.0.0
	 */
	public static function getAllowedRecords($view)
	{
		// check for general permissions
		if (self::canAdmin())
		{
			return 'all';
		}

		$asset_records = self::getSectionAssetNames($view);
		$item_records  = self::extractIdFromAssetName($asset_records);
		$allowed_items = self::checkRecordsForPermission($view, $item_records);

		$general_permission = array_search(0, $allowed_items);
		if ($general_permission !== false)
		{
			return 'all';
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
		$allowed_ids    = implode(',', $allowed_items);

		return $allowed_ids;
	}

	/**
	 * Method to get an array of strings of all asset names of the component section
	 * The array items are of the form 'component.section.id', where th part with id may be empty (section-wide permission)
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
		$_db            = JFactory::getDbo();

		try
		{
			$asset_query = $_db->getQuery(true);

			$asset_query->select($_db->quoteName('name'));
			$asset_query->from($_db->quoteName('#__assets'));
			$asset_query->where($_db->quoteName('name') . ' LIKE ' . $_db->quote('%com_bwpostman.' . $view . '%'));

			$_db->setQuery($asset_query);

			$asset_records = $_db->loadAssocList();
		}
		catch (Exception $e)
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
	 * @param   string $view    The name of the context.
	 * @param   array           $items
	 *
	 * @return  array           $allowed_ids
	 *
	 * @since   2.0.0
	 */
	private static function checkRecordsForPermission($view, $items)
	{
		$allowed_ids = array();

		foreach ($items as $item)
		{
			$allowed = self::canEdit($view, $item);
			if ($allowed)
			{
				$allowed_ids[] = $item['id'];
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
	 * @since   2.0.0
	 */
	private static function getMailinglistSpecificRecords($view)
	{
		$allowed_ids    = array();
		$result         = array();

		// Get the mailinglists the user may handle
		$asset_records          = self::getSectionAssetNames('mailinglist');
		$item_records           = self::extractIdFromAssetName($asset_records);
		$allowed_mailinglists   = self::checkRecordsForPermission('mailinglist', $item_records);

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
				$_db	= JFactory::getDbo();
				$query	= $_db->getQuery(true);

				$query->select($_db->quoteName($field));
				$query->from($_db->quoteName($table));
				$query->where($_db->quoteName('mailinglist_id') . ' IN (' . implode(',', $allowed_mailinglists) . ')');

				$_db->setQuery($query);

				$result = $_db->loadAssocList();
			}
			catch (RuntimeException $e)
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
	 * @param   string  $assetname  The name of the asset on which to perform the action.
	 *
	 * @return  boolean  True if authorised
	 *
	 * @since   11.1
	 */
	public static function authorise($userId, $action, $assetname = null)
	{
		return (bool) BwAccess::check($userId, $action, $assetname, false);
	}
}
