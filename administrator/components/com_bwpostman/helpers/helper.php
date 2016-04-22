<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman helper class for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

//
// Component development:
//
// Newsletter encoding 1=on, 0=off
if (!defined ('BWPOSTMAN_NL_ENCODING')) define ('BWPOSTMAN_NL_ENCODING', 1);
// Newsletter sending 1=on, 0=off
if (!defined ('BWPOSTMAN_NL_SENDING')) define ('BWPOSTMAN_NL_SENDING', 1);


// Component name amd database prefix
if (!defined ('BWPOSTMAN_COMPONENT_NAME')) define ('BWPOSTMAN_COMPONENT_NAME', basename (dirname (__FILE__)));
if (!defined ('BWPOSTMAN_NAME')) define ('BWPOSTMAN_NAME', substr (BWPOSTMAN_COMPONENT_NAME, 4));

// Component location
if (!defined ('BWPOSTMAN_COMPONENT_LOCATION')) define ('BWPOSTMAN_COMPONENT_LOCATION', basename (dirname (dirname (__FILE__))));

// Component paths
if (!defined ('BWPOSTMAN_PATH_COMPONENT_RELATIVE')) define ('BWPOSTMAN_PATH_COMPONENT_RELATIVE', BWPOSTMAN_COMPONENT_LOCATION . '/' . BWPOSTMAN_COMPONENT_NAME);
if (!defined ('BWPOSTMAN_PATH_SITE')) define ('BWPOSTMAN_PATH_SITE', JPATH_ROOT .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
if (!defined ('BWPOSTMAN_PATH_ADMIN')) define ('BWPOSTMAN_PATH_ADMIN', JPATH_ADMINISTRATOR .'/'. BWPOSTMAN_PATH_COMPONENT_RELATIVE);
if (!defined ('BWPOSTMAN_PATH_MEDIA')) define ('BWPOSTMAN_PATH_MEDIA', JPATH_ROOT .'/media/' . BWPOSTMAN_NAME);

/**
 * Class BwPostmanHelper
 */
abstract class BwPostmanHelper {
	/**
	 * property to hold session
	 *
	 * @var array
	 */
	static $session = null;

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the task view.
	 *
	 * @return	void
	 *
	 * @since	1.2.0
	 */
	public static function addSubmenu($vName)
	{
		$canDo	= self::getActions();

		JHtmlSidebar::addEntry
			(
				JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY'),
				'index.php?option=com_bwpostman',
				$vName == 'bwpostman'
			);

		if ($canDo->get('bwpm.view.newsletters')) {
			JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_NLS'),
					'index.php?option=com_bwpostman&view=newsletters',
					$vName == 'newsletters'
				);
		}

		if ($canDo->get('bwpm.view.subscribers')) {
			JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_SUBS'),
					'index.php?option=com_bwpostman&view=subscribers',
					$vName == 'subscribers'
				);
		}

		if ($canDo->get('bwpm.view.campaigns')) {
			JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_CAMS'),
					'index.php?option=com_bwpostman&view=campaigns',
					$vName == 'campaigns'
				);
		}

		if ($canDo->get('bwpm.view.mailinglists')) {
				JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_MLS'),
					'index.php?option=com_bwpostman&view=mailinglists',
					$vName == 'mailinglists'
				);
		}

		if ($canDo->get('bwpm.view.templates')) {
			JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_TPLS'),
					'index.php?option=com_bwpostman&view=templates',
					$vName == 'templates'
				);
		}

		if ($canDo->get('bwpm.archive') || $canDo->get('bwpm.view.archive')) {
			JHtmlSidebar::addEntry
				(
					JText::_('COM_BWPOSTMAN_MENU_MAIN_ENTRY_ARC'),
					'index.php?option=com_bwpostman&view=archive&layout=newsletters',
					$vName == 'archive'
				);
		}

		if ($canDo->get('core.admin') || $canDo->get('bwpm.view.manage')) {
			JHtmlSidebar::addEntry
				(
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
	 *	// Check if BwPostman has been installed and compatible with your code
	 *	if (class_exists('BwPostmanAdmin') && BwPostmanHelper::installed() && BwPostmanHelper::isCompatible('2.0.0-BETA2')) {
	 *		// Initialize the framework (new in 2.0.0-BETA2)
	 *		BwPostmanForum::setup();
	 *		// Start using the framework
	 *	}
	 * </code>
	 *
	 * @see BwPostmanHelper::enabled()
	 * @see BwPostmanHelper::isCompatible()
	 * @see BwPostmanHelper::setup()
	 *
	 * @return boolean True.
	 */
	public static function installed() {
		return true;
	}


	/**
	 * Method to replace the links in a newsletter to provide the correct preview
	 *
	 * @access	public
	 *
	 * @param 	string $text    HTML-/Text-version
	 *
	 * @return 	boolean
	 */
	static public function replaceLinks(&$text)
	{
		$search_str = '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text = preg_replace($search_str,' ${1}="'.JURI::root().'${2}"',$text);
		return true;
	}

	/**
	 * Method to get selectlist for dates
	 *
	 * @access	public
	 *
	 * @param 	string		$date		sort of date --> day, hour, minute
	 * @param 	int			$length		length of listarray
	 * @param 	array   	$selectval  selected values
	 *
	 * @return 	string				selectlist
	 */
	public function getDateList($date = 'minute', $length = 10, $selectval)
	{
		$options	= array();
		$selectlist	= array();
		$intval		= 1;
		if ($date == 'minute') {
			$intval = JComponentHelper::getParams('Com_bwpostman')->getValue('autocam_minute_intval') ;
		}

		switch ($date) {
			case 'day':		for ($i = 0; $i <= 31; $i++) {
								$options[] = $i;
							}
							break;

			case 'hour':	for ($i = 0; $i < 24; $i++) {
								$options[] = $i;
							}
							break;

			case 'minute':	for ($i = 0; $i < 60; $i += $intval) {
								$options[] = $i;
							}
							break;
		}

		foreach ($selectval->$date as $key => $value) {
//			$attribs	= 'class="inputbox" size="1"';
			$opt		= "automailing_values[" . $date . "][".$key."]";
			if ($value != '0') {
				$selected	= $value;
			}
			else {
				$selected	= 0;
			}

			$select_html		= '<select id="' . $opt . '" name="automailing_values['.$date.'][]" >';
			foreach ($options as $key2 => $value2) {

				$select_html		.= '<option value="' . $key2*$intval . '"';
				if ($selected == $key2*$intval) $select_html		.= ' selected="selected"';
				$select_html		.= '>' . $value2 . '</option>';
			}
			$select_html		.= '</select>';
			$selectlist[]	= $select_html;
		}
		return $selectlist;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	integer		$id			The item ID.
	 * @param	string		$section	The access section name.
	 *
	 * @return	JObject
	 */

	public static function getActions($id = 0, $section = '')
	{
		$user	= JFactory::getUser();
		$path	= JPATH_ADMINISTRATOR . '/components/com_bwpostman/access.xml';
		$result	= new JObject;

		if ($section && $id)
		{
			$assetName	= 'com_bwpostman.' . $section . '.' . (int) $id;
		}
		else
		{
			$assetName	= 'com_bwpostman';
		}

		$com_actions	= JAccess::getActionsFromFile($path, "/access/section[@name='component']/");

		if ($section != '') {
			$sec_actions	= JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");
			$actions		= array_merge($com_actions, $sec_actions);
		}
		else {
			$actions	= $com_actions;
		}

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Method to check if you can view a specific view.
	 *
	 * @param	string	$view		The view to test.
	 *
	 * @return	boolean

	 * @since	1.2.0
	 */
	public static function canView($view = '')
	{
		$user	= JFactory::getUser();

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('bwpm.view.' . $view, 'com_bwpostman')) {
			return true;
		}
		return false;
	}

	/**
	 * Method to check if you can add a record.
	 *
	 * @param	string	$view		The view to test. Has to be the list mode name.
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	public static function canAdd($view = '')
	{
		$user	= JFactory::getUser();

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('bwpm.view.' . $view, 'com_bwpostman')) {
			if ($user->authorise('bwpm.add', 'com_bwpostman')) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param	string	$view		The view to test. Has to be the single mode name.
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
/*	public static function canEdit($view = '', $data = array())
	{
		// Initialise variables.
		$recordId	= (int) isset($data['id']) ? $data['id'] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('core.view.' . $view, 'com_bwpostman')) {
			if ($user->authorise('core.edit', 'com_bwpostman')) {
				return true;
			}
			// Fallback on edit.own.
			// First test if the permission is available.
			if ($user->authorise('core.edit.own', 'com_bwpostman') || $user->authorise('core.edit.own', 'com_bwpostman.' . $view . $recordId))
			{
				// Now test the owner is the user.
				$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
				if (empty($ownerId) && $recordId)
				{
					// Need to do a lookup from the model.
					$record = $this->getModel($view)->getItem($recordId);

					if (empty($record))
					{
						return false;
					}
					$ownerId = $record->created_by;
				}

				// If the owner matches 'me' then allow access.
				if ($ownerId == $userId)
				{
					return true;
				}
			}
		}
		return false;
	}
 */
	/**
	 * Method to check if you can edit the state of a record.
	 *
	 * @param	string	$view		The view to test.
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	public static function canEditState($view = '')
	{
		$user	= JFactory::getUser();

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('bwpm.view.' . $view . 's', 'com_bwpostman')) {
			if ($user->authorise('bwpm.edit.state', 'com_bwpostman')) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can send a newsletter.
	 *
	 * @param	string	$recordId		The record to test.
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	public static function canSend($recordId = '')
	{
		$user	= JFactory::getUser();

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('bwpm.view.newsletters', 'com_bwpostman')) {
			if ($user->authorise('bwpm.send', 'com_bwpostman')) {
				return true;
			}
		}

		// Finally check record permission.
		if ($user->authorise('bwpm.view.newsletters', 'com_bwpostman')) {
			if ($user->authorise('bwpm.send.newsletter.' . $recordId, 'com_bwpostman')) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can archive a record.
	 *
	 * @param	string	$view		The view to test. Has to be the single mode name.
	 * @param	array	$data	An array of input data.
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	public static function canArchive($view = '', $data = array())
	{
		$user	= JFactory::getUser();

		// Check general component permission first.
		if ($user->authorise('core.admin', 'com_bwpostman')) {
			return true;
		}

		// Next check view permission.
		if ($user->authorise('bwpm.view.' . $view . 's', 'com_bwpostman')) {
			if ($user->authorise('bwpm.archive', 'com_bwpostman')) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can archive an existing record.
	 *
	 * @param	int		$recordId	The record to test.
	 * @param	int		$ownerId	The user to test against.
	 * @param	string	$context	The name of the context.
	 *
	 * @return	boolean
	 * @since	1.2.0
	 */
	public static function allowArchive($recordId = 0, $ownerId = 0, $context = '')
	{
		// Initialise variables.
		$recordId	= (int) $recordId;
		$ownerId	= (int) $ownerId;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general component archive permission first.
		if ($user->authorise('bwpm.archive', 'com_bwpostman')) {
			return true;
		}

		// Check view archive permission first.
		if ($user->authorise('bwpm.view.archive', 'com_bwpostman.archive')) {
			return true;
		}

		// Then check context archive permission.
		if ($user->authorise('bwpm.archive', 'com_bwpostman.archive')) {
			return true;
		}

		// Next check item archive permission.
		if ($user->authorise('bwpm.archive', 'com_bwpostman.archive' . '.' . $recordId)) {
			return true;
		}

		// Fallback on edit.own (only at context newsletter).
		if ($context = 'newsletter') {
			// First test if the permission is available.
			if ($user->authorise('bwpm.edit.own', 'com_bwpostman.archive' . '.' . $recordId)) {
				// Test if the owner matches 'me'.
				if ($ownerId == $userId) return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can delete an archived record.
	 *
	 * @param	int		$recordId	The record to test.
	 * @param	int		$ownerId	The user to test against.
	 * @param	string	$context	The name of the context.
	 *
	 * @return	boolean
	 * @since	1.2.0
	 */
	public static function allowDelete($recordId = 0, $ownerId = 0, $context = '')
	{
		// Initialise variables.
		$recordId	= (int) $recordId;
		$ownerId	= (int) $ownerId;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general component delete permission first.
		if ($user->authorise('bwpm.delete', 'com_bwpostman')) {
			return true;
		}

		// Then check context delete permission.
		if ($user->authorise('bwpm.delete', 'com_bwpostman.' . $context)) {
			return true;
		}

		// Next check item delete permission.
		if ($user->authorise('bwpm.delete', 'com_bwpostman.' . $context . '.' . $recordId)) {
			return true;
		}

		// Fallback on edit.own (only at context newsletter).
		if ($context = 'newsletter') {
			// First test if the permission is available.
			if ($user->authorise('bwpm.edit.own', 'com_bwpostman.'.$context . '.' . $recordId)) {
				// Test if the owner matches 'me'.
				if ($ownerId == $userId) return true;
			}
		}
		// Fallback on edit.own (only at context subscriber at frontend).
		if ($context = 'subscriber') {
			// First test if the permission is available.
			if ($user->authorise('bwpm.edit.own', 'com_bwpostman.'.$context . '.' . $recordId)) {
				// Test if the owner matches 'me'.
				if ($ownerId == $userId) return true;
			}
		}
		return false;
	}

	/**
	 * Method to check if you can restore an archived record.
	 *
	 * @param	int		$recordId	The record to test.
	 * @param	int		$ownerId	The user to test against.
	 * @param	string	$context	The name of the context.
	 *
	 * @return	boolean
	 * @since	1.2.0
	 */
	public static function allowRestore($recordId = 0, $ownerId = 0, $context = '')
	{
		// Initialise variables.
		$recordId	= (int) $recordId;
		$ownerId	= (int) $ownerId;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general component restore permission first.
		if ($user->authorise('bwpm.restore', 'com_bwpostman')) {
			return true;
		}

		// Then check context restore permission.
		if ($user->authorise('bwpm.restore', 'com_bwpostman.' . $context)) {
			return true;
		}

		// Next check item restore permission.
		if ($user->authorise('bwpm.restore', 'com_bwpostman.' . $context . '.' . $recordId)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('bwpm.edit.own', 'com_bwpostman.'.$context.'.' . $recordId)) {
			// Test if the owner matches 'me'.
			if ($ownerId == $userId) return true;
		}
		return false;
	}

	/**
	 * Method to get all published mailinglists
	 *
	 * @return	string
	 * @since	0.9.8
	 */
	public static function getMailinglistsWarning()
	{
		$_db			= JFactory::getDbo();
		$query			= $_db->getQuery(true);
		$ml_published	='';

		// Get # of all published mailinglists
		$query->select('COUNT(*)');
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		$ml_published = $_db->loadResult();

		if ($ml_published <1){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_WARNING_NO_PUBLISHED_MAILINGLIST'), 'warning');
		}
		unset($ml_published);
	}


	/**
	 * Check number of queue entries
	 *
	 * @return	bool	true if there are entries in the queue, otherwise false
	 *
	 * since	1.0.3
	 */
	static public function checkQueueEntries()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_sendmailqueue'));

		$_db->setQuery($query);

		if ($_db->loadResult() > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Method to get a captcha string
	 *
	 * @param int   $mode
	 * @return	string
	 *
	 * @since	0.9.8
	 */
	public static function getCaptcha($mode = 1)
	{
		$zahl		= 1960;
		$no_spam	= '';
		if ($mode == 1) {
			$no_spam = (date("dmy", time())) * $zahl;
		}
		if ($mode == 2) {
			if (date('H', time())=='00') {
				$no_spam = (date("dmy", time()-86400)) * $zahl;
			}
		}
		return $no_spam;
}

	/**
	 *	Captcha Bild
	 *
	 *	Systemvoraussetzung:
	 *	Linux, Windows
	 * 	PHP 4 >= 4.0.0-RC2 , PHP 5
	 *	GD-Bibliothek ( > gd-1.6 )
	 *	FreeType-Bibliothek
	 *
	 *
	 * 	LICENSE: GNU General Public License (GPL)
	 *	This program is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License version 2,
	 *	as published by the Free Software Foundation.
	 *
	 *	@category	Captcha
	 *	@author		Damir Enseleit <info@selfphp.de>
	 *	@copyright	2001-2006 SELFPHP
	 *	@version	$Id: captcha.php,v 0.10 2006/04/07 13:15:30 des1 Exp $
	 *	@link		http://www.selfphp.de
	 */

	/**
	 * Erzeugt die Rechenaufgabe
	 *
	 * @return	string	$fileName	Gibt die Rechenaufgabe als String für den Dateinamen wieder
	 */

	static public function showCaptcha() {
		/**
		 * Method to generate captcha
		 *
		 * @param $im
		 * @param $size
		 * @param $fileTTF
		 * @param $imgHeight
		 *
		 * @return string	$fileName	Gibt die Rechenaufgabe als String für den Dateinamen wieder
		 */
		function mathCaptcha($im, $size, $fileTTF, $imgHeight)
		{
			$math = range(0,9);
			shuffle($math);

			$mix = range(0,120);
			shuffle($mix);

			$color = imagecolorallocate($im,$mix[0],$mix[1],$mix[2]);

			$text		= "$math[0] + $math[1]";
			$fileName	= $math[0] + $math[1];

			imagettftext($im, $size, 0, 5, 25, $color, $fileTTF,$text);

			return $fileName;
		}

	// TTF-Schrift
	// Sie sollten hier unbedingt den absoluten Pfad angeben, da ansonsten
	// eventuell die TTF-Datei nicht eingebunden werden kann!
	$fileTTF = JPATH_COMPONENT_SITE.'/assets/ttf/style.ttf';

	// Verzeichnis für die Captcha-Bilder (muss Schreibrechte besitzen!)
	// Ausserdem sollten in diesem Ordner nur die Bilder gespeichert werden
	// da das Programm in regelmaessigen Abstaenden dieses leert!
	// Kein abschliessenden Slash benutzen!
	$captchaDir = JPATH_COMPONENT_SITE.'/assets/capimgdir';

	// Schriftgröße Rechenaufgabe
	$sizeMath = 20;

	//Bildgroesse
	$imgWidth = 80;//200
	$imgHeight = 30;//80

	header("Content-type: image/png");
	$im = @imagecreate($imgWidth, $imgHeight)
	 or die("GD! Initialisierung fehlgeschlagen");
	$color = imagecolorallocate($im,255,255,255);
	imagefill($im,0,$imgWidth,$color);
	$fileName = mathCaptcha($im,$sizeMath,$fileTTF,$imgHeight);

	// Uebermittelter Hash-Wert ueberpruefen
	if(!preg_match('/^[a-f0-9]{32}$/',$_GET['codeCaptcha']))
		$_GET['codeCaptcha'] = md5(microtime());

	// Image speichern
	imagepng($im,$captchaDir.'/'.$_GET['codeCaptcha'].'_'.$fileName.'.png');
	imagedestroy($im);
	// Bild ausgeben
	readfile(JURI::base().'components/com_bwpostman/assets/capimgdir/'.$_GET['codeCaptcha'].'_'.$fileName.'.png');
	}


	/**
	 *	Captcha Bild Überprüfung
	 *
	 *	Systemvoraussetzung:
	 *	Linux, Windows
	 * 	PHP 4 >= 4.0.0-RC2 , PHP 5
	 *	GD-Bibliothek (> gd-1.6)
	 *	FreeType-Bibliothek
	 *
	 *	Prüft ein Captcha-Bild
	 *
	 * 	LICENSE: GNU General Public License (GPL)
	 *	This program is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License version 2,
	 *	as published by the Free Software Foundation.
	 *
	 *	@category	Captcha
	 *	@author		Damir Enseleit <info@selfphp.de>
	 *	@copyright	2001-2006 SELFPHP
	 *	@version	$Id: captcha_check.php,v 0.10 2006/04/07 13:15:30 des1 Exp $
	 *	@link		http://www.selfphp.de
	 *
	 * @param		string		$codeCaptcha		Hash-Wert
	 * @param		string		$stringCaptcha		Eingabe durch den User
	 * @param		string		$dir				Das Verzeichnis mit den Captcha-Bilder
	 * @param		integer		$delFile			Die Zeit in Minuten, nachdem ein Captcha-Bild gelöscht wird
	 *
	 * @return		bool		TRUE/FALSE
	 */
	public static function CheckCaptcha($codeCaptcha,$stringCaptcha,$dir,$delFile=5)
	{
		// Setzt den Check erst einmal auf FALSE
		$captchaTrue = FALSE;

		// Übergebene Hash-Variable überprüfen
		if(!preg_match('/^[a-f0-9]{32}$/',$codeCaptcha))
			return FALSE;

		// Übergebene Captcha-Variable überprüfen
		if(!preg_match('/^[a-zA-Z0-9]{1,6}$/',$stringCaptcha))
			return FALSE;

		$handle = @opendir($dir);
		while (false !== ($file = readdir($handle))) {
			if (preg_match("=^\.{1,2}$=", $file)) {
				continue;
			}
			if (is_dir($dir.$file)) {
				continue;
			}
			else {
				$lastTime = ceil((time() - filemtime($dir.$file)) / 60);
				if($lastTime > $delFile) {
					if ($file != 'index.html') unlink($dir.$file);
				}
				else {
					if(strtolower($file) == strtolower($codeCaptcha.'_'.$stringCaptcha.'.png')) {
						$captchaTrue = TRUE;
					}
					if (preg_match("=^$codeCaptcha=i", $file)) {
						if ($file != 'index.html') unlink($dir.$file);
					}
				}
			}
		}

		@closedir($handle);

		if ($captchaTrue)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Load BwPostman language file
	 *
	 * Helper function for external modules and plugins to load the main BwPostman language file(s)
	 *
	 * @param   string  $file
	 * @param   string  $client
	 *
	 */
	public static function loadLanguage($file = 'com_bwpostman', $client = 'site') {
		static $loaded = array();
//		BWPOSTMAN_PROFILER ? BwPostmanProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($client == 'site') {
			$lookup1 = JPATH_SITE;
			$lookup2 = BWPOSTMAN_PATH_SITE;
		}
		else {
			$client = 'admin';
			$lookup1 = JPATH_ADMINISTRATOR;
			$lookup2 = BWPOSTMAN_PATH_ADMIN;
		}
		if (empty($loaded["{$client}/{$file}"])) {
			$lang		= JFactory::getLanguage();
			$english	= false;
			if ($lang->getTag() != 'en-GB' && !JDEBUG && !$lang->getDebug()
				) {
				$lang->load($file, $lookup2, 'en-GB', true, false);
				$english = true;
			}
			$loaded[$file] = $lang->load($file, $lookup1, null, $english, false)
				|| $lang->load($file, $lookup2, null, $english, false)
				|| $lang->load($file, $lookup1, $lang->getDefault(), $english, false)
				|| $lang->load($file, $lookup2, $lang->getDefault(), $english, false);
		}
//		BWPOSTMAN_PROFILER ? BwPostmanProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $loaded[$file];
}

	/**
	 * Method to parse language file
	 *
	 * @param $lang
	 * @param $filename
	 *
	 * @return bool
	 */
	protected static function parseLanguage($lang, $filename) {
		if (!file_exists($filename)) return false;

		$version = phpversion();

		// Capture hidden PHP errors from the parsing.
		$php_errormsg = null;
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);

		if ($version >= '5.3.1') {
			$contents = file_get_contents($filename);
			$contents = str_replace('_QQ_', '"\""', $contents);
			$strings = @parse_ini_string($contents);
		}
		else {
			$strings = @parse_ini_file($filename);

			if ($version == '5.3.0' && is_array($strings)) {
				foreach ($strings as $key => $string) {
					$strings[$key] = str_replace('_QQ_', '"', $string);
				}
			}
		}

		// Restore error tracking to what it was before.
		ini_set('track_errors', $track_errors);

		if (!is_array($strings)) {
			$strings = array();
		}

		$lang->_strings = array_merge($lang->_strings, $strings);
		return !empty($strings);
	}
}
